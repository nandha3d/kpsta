class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final List<dynamic>? errors;

  ApiException({
    required this.message,
    this.statusCode,
    this.errors,
  });

  @override
  String toString() => 'ApiException: $message (code: $statusCode)';
}

class UnauthorizedException extends ApiException {
  UnauthorizedException({String message = 'Session expired or unauthorized'})
      : super(message: message, statusCode: 401);
}

class ForbiddenException extends ApiException {
  ForbiddenException({String message = 'Access denied. Insufficient permissions.'})
      : super(message: message, statusCode: 403);
}

class NotFoundException extends ApiException {
  NotFoundException({String message = 'Resource not found'})
      : super(message: message, statusCode: 404);
}

class ValidationException extends ApiException {
  ValidationException({
    String message = 'Validation failed',
    List<dynamic>? errors,
  }) : super(message: message, statusCode: 422, errors: errors);
}

class NetworkException extends ApiException {
  NetworkException({String message = 'Network connection error. Please check your internet.'})
      : super(message: message, statusCode: null);
}
