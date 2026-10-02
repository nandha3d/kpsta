import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/network/api_client.dart';
import '../../../core/storage/secure_token_storage.dart';
import '../data/auth_repository.dart';
import '../domain/user_model.dart';

final secureStorageProvider = Provider<SecureTokenStorage>((ref) {
  return SecureTokenStorage();
});

final apiClientProvider = Provider<ApiClient>((ref) {
  final storage = ref.watch(secureStorageProvider);
  return ApiClient(customTokenStorage: storage);
});

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  final client = ref.watch(apiClientProvider);
  final storage = ref.watch(secureStorageProvider);
  return AuthRepository(apiClient: client, tokenStorage: storage);
});

enum AuthStatus { initial, authenticated, unauthenticated, loading, error }

class AuthState {
  final AuthStatus status;
  final UserModel? user;
  final String? errorMessage;
  final bool otpSent;
  final String? pendingPhone;

  const AuthState({
    this.status = AuthStatus.initial,
    this.user,
    this.errorMessage,
    this.otpSent = false,
    this.pendingPhone,
  });

  AuthState copyWith({
    AuthStatus? status,
    UserModel? user,
    String? errorMessage,
    bool? otpSent,
    String? pendingPhone,
  }) {
    return AuthState(
      status: status ?? this.status,
      user: user ?? this.user,
      errorMessage: errorMessage,
      otpSent: otpSent ?? this.otpSent,
      pendingPhone: pendingPhone ?? this.pendingPhone,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepository _repo;

  AuthNotifier(this._repo) : super(const AuthState()) {
    checkSession();
  }

  Future<void> checkSession() async {
    state = state.copyWith(status: AuthStatus.loading);
    try {
      final user = await _repo.getCachedUser();
      if (user != null) {
        state = state.copyWith(status: AuthStatus.authenticated, user: user);
        // Refresh profile in background
        _repo.getCurrentUser().then((fresh) {
          state = state.copyWith(user: fresh);
        }).catchError((_) {});
      } else {
        state = state.copyWith(status: AuthStatus.unauthenticated);
      }
    } catch (_) {
      state = state.copyWith(status: AuthStatus.unauthenticated);
    }
  }

  Future<bool> requestOtp(String phone) async {
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      await _repo.requestOtp(phone);
      state = state.copyWith(
        status: AuthStatus.unauthenticated,
        otpSent: true,
        pendingPhone: phone,
      );
      return true;
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: e.toString().replaceAll('ApiException: ', ''),
      );
      return false;
    }
  }

  Future<bool> verifyOtp(String otp) async {
    if (state.pendingPhone == null) return false;
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      final user = await _repo.verifyOtp(
        phone: state.pendingPhone!,
        otp: otp,
      );
      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: user,
        otpSent: false,
        pendingPhone: null,
      );
      return true;
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: e.toString().replaceAll('ApiException: ', ''),
      );
      return false;
    }
  }

  void resetOtp() {
    state = state.copyWith(otpSent: false, pendingPhone: null, errorMessage: null);
  }

  Future<void> logout() async {
    state = state.copyWith(status: AuthStatus.loading);
    await _repo.logout();
    state = const AuthState(status: AuthStatus.unauthenticated);
  }
}

final authNotifierProvider =
    StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  final repo = ref.watch(authRepositoryProvider);
  return AuthNotifier(repo);
});
