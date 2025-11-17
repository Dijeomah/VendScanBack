import '../models/user.dart';
import '../utils/constants.dart';
import 'api_service.dart';
import 'storage_service.dart';

class AuthService {
  final _api = ApiService();
  final _storage = StorageService();

  // Login
  Future<AuthResult> login({
    required String email,
    required String password,
  }) async {
    try {
      final response = await _api.post(
        ApiEndpoints.login,
        data: {
          'email': email,
          'password': password,
        },
      );

      if (response.success && response.data != null) {
        final token = response.data['token'];
        final userData = response.data['user'];

        await _storage.saveToken(token);
        final user = User.fromJson(userData);
        await _storage.saveUser(user);

        return AuthResult(
          success: true,
          message: response.message,
          user: user,
        );
      } else {
        return AuthResult(
          success: false,
          message: response.message,
        );
      }
    } catch (e) {
      return AuthResult(
        success: false,
        message: 'Login failed: ${e.toString()}',
      );
    }
  }

  // Register
  Future<AuthResult> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    required String role,
    String? phone,
  }) async {
    try {
      final response = await _api.post(
        ApiEndpoints.register,
        data: {
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
          'role': role,
          if (phone != null) 'phone': phone,
        },
      );

      if (response.success && response.data != null) {
        final token = response.data['token'];
        final userData = response.data['user'];

        await _storage.saveToken(token);
        final user = User.fromJson(userData);
        await _storage.saveUser(user);

        return AuthResult(
          success: true,
          message: response.message,
          user: user,
        );
      } else {
        return AuthResult(
          success: false,
          message: response.message,
        );
      }
    } catch (e) {
      return AuthResult(
        success: false,
        message: 'Registration failed: ${e.toString()}',
      );
    }
  }

  // Logout
  Future<bool> logout() async {
    try {
      await _api.post(ApiEndpoints.logout);
      await _storage.clearAuth();
      return true;
    } catch (e) {
      // Clear local storage even if API call fails
      await _storage.clearAuth();
      return false;
    }
  }

  // Get current user
  User? getCurrentUser() {
    return _storage.getUser();
  }

  // Check if user is authenticated
  bool get isAuthenticated => _storage.hasToken;

  // Get user role
  String? get userRole => _storage.getRole();

  // Check user roles
  bool get isVendor => _storage.isVendor;
  bool get isServer => _storage.isServer;
  bool get isAdmin => _storage.isAdmin;

  // Refresh token
  Future<bool> refreshToken() async {
    try {
      final response = await _api.post(ApiEndpoints.refresh);
      if (response.success && response.data != null) {
        final token = response.data['token'];
        await _storage.saveToken(token);
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
}

class AuthResult {
  final bool success;
  final String message;
  final User? user;

  AuthResult({
    required this.success,
    required this.message,
    this.user,
  });
}
