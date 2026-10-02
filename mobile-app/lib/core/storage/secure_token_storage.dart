import 'package:flutter/foundation.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

/// Secure token & session persistence with robust in-memory fallback
class SecureTokenStorage {
  final FlutterSecureStorage _storage;
  final Map<String, String> _memoryFallback = {};

  SecureTokenStorage([FlutterSecureStorage? storage])
      : _storage = storage ?? const FlutterSecureStorage();

  static const String _accessTokenKey = 'kpsta_access_token';
  static const String _refreshTokenKey = 'kpsta_refresh_token';
  static const String _userDataKey = 'kpsta_user_data';
  static const String _userRoleKey = 'kpsta_user_role';

  Future<void> saveTokens({
    required String accessToken,
    required String refreshToken,
  }) async {
    _memoryFallback[_accessTokenKey] = accessToken;
    _memoryFallback[_refreshTokenKey] = refreshToken;
    try {
      await _storage.write(key: _accessTokenKey, value: accessToken);
      await _storage.write(key: _refreshTokenKey, value: refreshToken);
    } catch (e) {
      debugPrint('[SecureTokenStorage] Write fallback to memory: $e');
    }
  }

  Future<String?> getAccessToken() async {
    try {
      final val = await _storage.read(key: _accessTokenKey);
      if (val != null) {
        _memoryFallback[_accessTokenKey] = val;
        return val;
      }
    } catch (e) {
      debugPrint('[SecureTokenStorage] Read fallback to memory: $e');
    }
    return _memoryFallback[_accessTokenKey];
  }

  Future<String?> getRefreshToken() async {
    try {
      final val = await _storage.read(key: _refreshTokenKey);
      if (val != null) {
        _memoryFallback[_refreshTokenKey] = val;
        return val;
      }
    } catch (e) {
      debugPrint('[SecureTokenStorage] Read fallback to memory: $e');
    }
    return _memoryFallback[_refreshTokenKey];
  }

  Future<void> saveUserData({
    required String userJson,
    required String role,
  }) async {
    _memoryFallback[_userDataKey] = userJson;
    _memoryFallback[_userRoleKey] = role;
    try {
      await _storage.write(key: _userDataKey, value: userJson);
      await _storage.write(key: _userRoleKey, value: role);
    } catch (e) {
      debugPrint('[SecureTokenStorage] Write fallback to memory: $e');
    }
  }

  Future<String?> getUserData() async {
    try {
      final val = await _storage.read(key: _userDataKey);
      if (val != null) {
        _memoryFallback[_userDataKey] = val;
        return val;
      }
    } catch (e) {
      debugPrint('[SecureTokenStorage] Read fallback to memory: $e');
    }
    return _memoryFallback[_userDataKey];
  }

  Future<String?> getUserRole() async {
    try {
      final val = await _storage.read(key: _userRoleKey);
      if (val != null) {
        _memoryFallback[_userRoleKey] = val;
        return val;
      }
    } catch (e) {
      debugPrint('[SecureTokenStorage] Read fallback to memory: $e');
    }
    return _memoryFallback[_userRoleKey];
  }

  Future<void> clearAll() async {
    _memoryFallback.clear();
    try {
      await _storage.delete(key: _accessTokenKey);
      await _storage.delete(key: _refreshTokenKey);
      await _storage.delete(key: _userDataKey);
      await _storage.delete(key: _userRoleKey);
    } catch (e) {
      debugPrint('[SecureTokenStorage] Delete fallback: $e');
    }
  }
}
