import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Secure token & session persistence
class SecureTokenStorage {
  final FlutterSecureStorage _storage;

  SecureTokenStorage([FlutterSecureStorage? storage])
      : _storage = storage ??
            const FlutterSecureStorage(
              aOptions: AndroidOptions(encryptedSharedPreferences: true),
            );

  static const String _accessTokenKey = 'kpsta_access_token';
  static const String _refreshTokenKey = 'kpsta_refresh_token';
  static const String _userDataKey = 'kpsta_user_data';
  static const String _userRoleKey = 'kpsta_user_role';

  Future<void> saveTokens({
    required String accessToken,
    required String refreshToken,
  }) async {
    await _storage.write(key: _accessTokenKey, value: accessToken);
    await _storage.write(key: _refreshTokenKey, value: refreshToken);
  }

  Future<String?> getAccessToken() async {
    return await _storage.read(key: _accessTokenKey);
  }

  Future<String?> getRefreshToken() async {
    return await _storage.read(key: _refreshTokenKey);
  }

  Future<void> saveUserData({
    required String userJson,
    required String role,
  }) async {
    await _storage.write(key: _userDataKey, value: userJson);
    await _storage.write(key: _userRoleKey, value: role);
  }

  Future<String?> getUserData() async {
    return await _storage.read(key: _userDataKey);
  }

  Future<String?> getUserRole() async {
    return await _storage.read(key: _userRoleKey);
  }

  Future<void> clearAll() async {
    await _storage.delete(key: _accessTokenKey);
    await _storage.delete(key: _refreshTokenKey);
    await _storage.delete(key: _userDataKey);
    await _storage.delete(key: _userRoleKey);
  }
}
