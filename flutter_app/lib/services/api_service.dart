import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../utils/constants.dart';
import 'storage_service.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  late Dio _dio;
  final _storage = StorageService();

  void init() {
    _dio = Dio(
      BaseOptions(
        baseUrl: AppConstants.baseUrl,
        connectTimeout: const Duration(milliseconds: AppConstants.connectionTimeout),
        receiveTimeout: const Duration(milliseconds: AppConstants.receiveTimeout),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      ),
    );

    // Add interceptors
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Add auth token if exists
          final token = _storage.getToken();
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }

          if (kDebugMode) {
            print('REQUEST[${options.method}] => PATH: ${options.path}');
            print('Headers: ${options.headers}');
            if (options.data != null) {
              print('Body: ${options.data}');
            }
          }

          return handler.next(options);
        },
        onResponse: (response, handler) {
          if (kDebugMode) {
            print(
                'RESPONSE[${response.statusCode}] => PATH: ${response.requestOptions.path}');
            print('Data: ${response.data}');
          }
          return handler.next(response);
        },
        onError: (error, handler) async {
          if (kDebugMode) {
            print(
                'ERROR[${error.response?.statusCode}] => PATH: ${error.requestOptions.path}');
            print('Error: ${error.response?.data}');
          }

          // Handle 401 Unauthorized - token expired
          if (error.response?.statusCode == 401) {
            // Try to refresh token
            final refreshed = await _refreshToken();
            if (refreshed) {
              // Retry the request
              return handler.resolve(await _retry(error.requestOptions));
            } else {
              // Logout user
              await _storage.clearAuth();
            }
          }

          return handler.next(error);
        },
      ),
    );
  }

  Future<bool> _refreshToken() async {
    try {
      final response = await _dio.post(ApiEndpoints.refresh);
      if (response.statusCode == 200 && response.data['success'] == true) {
        final token = response.data['data']['token'];
        await _storage.saveToken(token);
        return true;
      }
    } catch (e) {
      if (kDebugMode) print('Token refresh failed: $e');
    }
    return false;
  }

  Future<Response<dynamic>> _retry(RequestOptions requestOptions) async {
    final options = Options(
      method: requestOptions.method,
      headers: requestOptions.headers,
    );

    return _dio.request<dynamic>(
      requestOptions.path,
      data: requestOptions.data,
      queryParameters: requestOptions.queryParameters,
      options: options,
    );
  }

  // Generic GET request
  Future<ApiResponse> get(
    String endpoint, {
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await _dio.get(
        endpoint,
        queryParameters: queryParameters,
      );
      return _handleResponse(response);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  // Generic POST request
  Future<ApiResponse> post(
    String endpoint, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await _dio.post(
        endpoint,
        data: data,
        queryParameters: queryParameters,
      );
      return _handleResponse(response);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  // Generic PUT request
  Future<ApiResponse> put(
    String endpoint, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await _dio.put(
        endpoint,
        data: data,
        queryParameters: queryParameters,
      );
      return _handleResponse(response);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  // Generic PATCH request
  Future<ApiResponse> patch(
    String endpoint, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await _dio.patch(
        endpoint,
        data: data,
        queryParameters: queryParameters,
      );
      return _handleResponse(response);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  // Generic DELETE request
  Future<ApiResponse> delete(
    String endpoint, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
  }) async {
    try {
      final response = await _dio.delete(
        endpoint,
        data: data,
        queryParameters: queryParameters,
      );
      return _handleResponse(response);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  // Upload file (multipart)
  Future<ApiResponse> uploadFile(
    String endpoint, {
    required File file,
    required String fieldName,
    Map<String, dynamic>? data,
  }) async {
    try {
      final formData = FormData.fromMap({
        fieldName: await MultipartFile.fromFile(file.path),
        ...?data,
      });

      final response = await _dio.post(
        endpoint,
        data: formData,
      );
      return _handleResponse(response);
    } on DioException catch (e) {
      return _handleError(e);
    }
  }

  ApiResponse _handleResponse(Response response) {
    if (response.statusCode! >= 200 && response.statusCode! < 300) {
      return ApiResponse(
        success: response.data['success'] ?? true,
        message: response.data['message'] ?? 'Success',
        data: response.data['data'],
      );
    } else {
      return ApiResponse(
        success: false,
        message: response.data['message'] ?? 'An error occurred',
        errors: response.data['errors'],
      );
    }
  }

  ApiResponse _handleError(DioException error) {
    String message = 'An error occurred';
    Map<String, dynamic>? errors;

    if (error.type == DioExceptionType.connectionTimeout ||
        error.type == DioExceptionType.receiveTimeout) {
      message = 'Connection timeout. Please try again.';
    } else if (error.type == DioExceptionType.badResponse) {
      message = error.response?.data['message'] ?? 'Server error occurred';
      errors = error.response?.data['errors'];
    } else if (error.type == DioExceptionType.cancel) {
      message = 'Request was cancelled';
    } else if (error.error is SocketException) {
      message = 'No internet connection';
    } else {
      message = error.message ?? 'Unknown error occurred';
    }

    return ApiResponse(
      success: false,
      message: message,
      errors: errors,
    );
  }
}

class ApiResponse {
  final bool success;
  final String message;
  final dynamic data;
  final Map<String, dynamic>? errors;

  ApiResponse({
    required this.success,
    required this.message,
    this.data,
    this.errors,
  });

  String? getFieldError(String field) {
    if (errors == null) return null;
    final fieldErrors = errors![field];
    if (fieldErrors is List && fieldErrors.isNotEmpty) {
      return fieldErrors.first.toString();
    } else if (fieldErrors is String) {
      return fieldErrors;
    }
    return null;
  }

  String get firstError {
    if (errors == null || errors!.isEmpty) return message;
    final firstKey = errors!.keys.first;
    return getFieldError(firstKey) ?? message;
  }
}
