import 'dart:io';
import 'package:dio/dio.dart';
import '../constants/api_constants.dart';
import '../storage/secure_token_storage.dart';
import 'api_exceptions.dart';

/// Unified Dio HTTP client with JWT interceptor and token refresh rotation
class ApiClient {
  final Dio dio;
  final SecureTokenStorage tokenStorage;
  bool _isRefreshing = false;

  ApiClient({
    Dio? customDio,
    SecureTokenStorage? customTokenStorage,
  })  : tokenStorage = customTokenStorage ?? SecureTokenStorage(),
        dio = customDio ??
            Dio(
              BaseOptions(
                baseUrl: ApiConstants.baseUrl,
                connectTimeout: const Duration(seconds: 10),
                receiveTimeout: const Duration(seconds: 10),
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                },
              ),
            ) {
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Attach Bearer token if available
          final token = await tokenStorage.getAccessToken();
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException error, handler) async {
          if (error.response?.statusCode == 401 && !_isRefreshing) {
            // Attempt token refresh
            _isRefreshing = true;
            try {
              final refreshToken = await tokenStorage.getRefreshToken();
              if (refreshToken != null && refreshToken.isNotEmpty) {
                final refreshResponse = await dio.post(
                  ApiConstants.tokenRefresh,
                  data: {'refresh_token': refreshToken},
                  options: Options(headers: {'Authorization': ''}),
                );

                if (refreshResponse.statusCode == 200 &&
                    refreshResponse.data['success'] == true) {
                  final newAccessToken =
                      refreshResponse.data['data']['access_token'];
                  final newRefreshToken =
                      refreshResponse.data['data']['refresh_token'];

                  await tokenStorage.saveTokens(
                    accessToken: newAccessToken,
                    refreshToken: newRefreshToken,
                  );

                  _isRefreshing = false;

                  // Retry the original request
                  final retryOptions = error.requestOptions;
                  retryOptions.headers['Authorization'] =
                      'Bearer $newAccessToken';
                  final retryResponse = await dio.fetch(retryOptions);
                  return handler.resolve(retryResponse);
                }
              }
            } catch (_) {
              // Refresh failed, clear session
              await tokenStorage.clearAll();
            } finally {
              _isRefreshing = false;
            }
          }
          return handler.next(error);
        },
      ),
    );
  }

  /// Map Dio exceptions into domain ApiException instances
  ApiException _handleError(dynamic error) {
    if (error is DioException) {
      final response = error.response;
      if (response != null) {
        final statusCode = response.statusCode;
        final data = response.data;
        String message = 'An unexpected error occurred';
        List<dynamic>? errors;

        if (data is Map<String, dynamic>) {
          message = data['message']?.toString() ?? message;
          if (data['errors'] is List) {
            errors = data['errors'];
          }
        }

        switch (statusCode) {
          case 401:
            return UnauthorizedException(message: message);
          case 403:
            return ForbiddenException(message: message);
          case 404:
            return NotFoundException(message: message);
          case 422:
            return ValidationException(message: message, errors: errors);
          default:
            return ApiException(
              message: message,
              statusCode: statusCode,
              errors: errors,
            );
        }
      }
      return NetworkException(
        message: error.message ?? 'Network connection failure',
      );
    }
    return ApiException(message: error.toString());
  }

  // --- HTTP Verbs ---

  Future<dynamic> get(
    String path, {
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await dio.get(path, queryParameters: queryParameters);
      return _unwrap(response.data);
    } catch (e) {
      throw _handleError(e);
    }
  }

  Future<dynamic> post(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await dio.post(
        path,
        data: data,
        queryParameters: queryParameters,
      );
      return _unwrap(response.data);
    } catch (e) {
      throw _handleError(e);
    }
  }

  Future<dynamic> put(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await dio.put(
        path,
        data: data,
        queryParameters: queryParameters,
      );
      return _unwrap(response.data);
    } catch (e) {
      throw _handleError(e);
    }
  }

  Future<dynamic> delete(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await dio.delete(
        path,
        data: data,
        queryParameters: queryParameters,
      );
      return _unwrap(response.data);
    } catch (e) {
      throw _handleError(e);
    }
  }

  Future<dynamic> uploadFile(
    String path, {
    required File file,
    String fieldName = 'file',
    String? category,
  }) async {
    try {
      final fileName = file.path.split(Platform.pathSeparator).last;
      final formData = FormData.fromMap({
        fieldName: await MultipartFile.fromFile(file.path, filename: fileName),
        if (category != null) 'category': category,
      });

      final response = await dio.post(
        path,
        data: formData,
        options: Options(contentType: 'multipart/form-data'),
      );
      return _unwrap(response.data);
    } catch (e) {
      throw _handleError(e);
    }
  }

  /// Unwraps the standard { success, data, meta, message, errors } response envelope
  dynamic _unwrap(dynamic rawData) {
    if (rawData is Map<String, dynamic>) {
      if (rawData.containsKey('success') && rawData['success'] == false) {
        throw ApiException(
          message: rawData['message']?.toString() ?? 'Operation failed',
          errors: rawData['errors'] is List ? rawData['errors'] : null,
        );
      }
      return rawData;
    }
    return rawData;
  }
}
