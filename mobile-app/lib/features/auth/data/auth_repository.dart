import 'dart:convert';
import '../../../core/constants/api_constants.dart';
import '../../../core/network/api_client.dart';
import '../../../core/storage/secure_token_storage.dart';
import '../domain/user_model.dart';

/// Authentication repository handling WhatsApp OTP, tokens, and profile retrieval
class AuthRepository {
  final ApiClient apiClient;
  final SecureTokenStorage tokenStorage;

  AuthRepository({
    required this.apiClient,
    required this.tokenStorage,
  });

  /// Request WhatsApp OTP for given phone number
  Future<Map<String, dynamic>> requestOtp(String phone) async {
    final response = await apiClient.post(
      ApiConstants.otpRequest,
      data: {'phone': phone},
    );
    return response['data'] as Map<String, dynamic>;
  }

  /// Verify WhatsApp OTP and persist tokens
  Future<UserModel> verifyOtp({
    required String phone,
    required String otp,
  }) async {
    final response = await apiClient.post(
      ApiConstants.otpVerify,
      data: {
        'phone': phone,
        'otp': otp,
        'device_type': 'mobile_flutter',
      },
    );

    final data = response['data'] as Map<String, dynamic>;
    final accessToken = data['access_token'] as String;
    final refreshToken = data['refresh_token'] as String;
    final userData = data['user'] as Map<String, dynamic>;

    final user = UserModel.fromJson(userData);

    // Save tokens and session
    await tokenStorage.saveTokens(
      accessToken: accessToken,
      refreshToken: refreshToken,
    );
    await tokenStorage.saveUserData(
      userJson: jsonEncode(user.toJson()),
      role: user.role,
    );

    return user;
  }

  /// Authenticate with username or email and password
  Future<UserModel> loginWithPassword({
    required String username,
    required String password,
  }) async {
    final response = await apiClient.post(
      ApiConstants.login,
      data: {
        'username': username,
        'password': password,
        'device_type': 'mobile_flutter',
      },
    );

    final data = response['data'] as Map<String, dynamic>;
    final accessToken = data['access_token'] as String;
    final refreshToken = data['refresh_token'] as String;
    final userData = data['user'] as Map<String, dynamic>;

    final user = UserModel.fromJson(userData);

    // Save tokens and session
    await tokenStorage.saveTokens(
      accessToken: accessToken,
      refreshToken: refreshToken,
    );
    await tokenStorage.saveUserData(
      userJson: jsonEncode(user.toJson()),
      role: user.role,
    );

    return user;
  }

  /// Fetch authenticated user profile from /auth/me
  Future<UserModel> getCurrentUser() async {
    final response = await apiClient.get(ApiConstants.me);
    final data = response['data'] as Map<String, dynamic>;
    final user = UserModel.fromJson(data);

    await tokenStorage.saveUserData(
      userJson: jsonEncode(user.toJson()),
      role: user.role,
    );
    return user;
  }

  /// Check if user has an existing cached session
  Future<UserModel?> getCachedUser() async {
    final token = await tokenStorage.getAccessToken();
    if (token == null || token.isEmpty) return null;

    final userJson = await tokenStorage.getUserData();
    if (userJson != null && userJson.isNotEmpty) {
      try {
        return UserModel.fromJson(jsonDecode(userJson));
      } catch (_) {}
    }
    return null;
  }

  /// Revoke tokens and clear local secure storage
  Future<void> logout() async {
    try {
      final refreshToken = await tokenStorage.getRefreshToken();
      if (refreshToken != null && refreshToken.isNotEmpty) {
        await apiClient.post(
          ApiConstants.logout,
          data: {'refresh_token': refreshToken},
        );
      }
    } catch (_) {
      // Ignore network errors on logout
    } finally {
      await tokenStorage.clearAll();
    }
  }
}
