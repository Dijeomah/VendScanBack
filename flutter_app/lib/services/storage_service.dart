import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import '../utils/constants.dart';

class StorageService {
  static final StorageService _instance = StorageService._internal();
  factory StorageService() => _instance;
  StorageService._internal();

  SharedPreferences? _prefs;

  Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  // Token management
  Future<void> saveToken(String token) async {
    await _prefs?.setString(AppConstants.tokenKey, token);
  }

  String? getToken() {
    return _prefs?.getString(AppConstants.tokenKey);
  }

  Future<void> removeToken() async {
    await _prefs?.remove(AppConstants.tokenKey);
  }

  bool get hasToken => getToken() != null;

  // User management
  Future<void> saveUser(User user) async {
    final userJson = json.encode(user.toJson());
    await _prefs?.setString(AppConstants.userKey, userJson);
    await _prefs?.setString(AppConstants.roleKey, user.role);
  }

  User? getUser() {
    final userJson = _prefs?.getString(AppConstants.userKey);
    if (userJson == null) return null;
    try {
      final userMap = json.decode(userJson) as Map<String, dynamic>;
      return User.fromJson(userMap);
    } catch (e) {
      return null;
    }
  }

  Future<void> removeUser() async {
    await _prefs?.remove(AppConstants.userKey);
    await _prefs?.remove(AppConstants.roleKey);
  }

  String? getRole() {
    return _prefs?.getString(AppConstants.roleKey);
  }

  bool get isVendor => getRole()?.toLowerCase() == AppConstants.roleVendor;
  bool get isServer => getRole()?.toLowerCase() == AppConstants.roleServer;
  bool get isAdmin => getRole()?.toLowerCase() == AppConstants.roleAdmin;

  // General key-value storage
  Future<void> setString(String key, String value) async {
    await _prefs?.setString(key, value);
  }

  String? getString(String key) {
    return _prefs?.getString(key);
  }

  Future<void> setBool(String key, bool value) async {
    await _prefs?.setBool(key, value);
  }

  bool? getBool(String key) {
    return _prefs?.getBool(key);
  }

  Future<void> setInt(String key, int value) async {
    await _prefs?.setInt(key, value);
  }

  int? getInt(String key) {
    return _prefs?.getInt(key);
  }

  Future<void> remove(String key) async {
    await _prefs?.remove(key);
  }

  // Clear all storage
  Future<void> clearAll() async {
    await _prefs?.clear();
  }

  // Logout - clear auth data
  Future<void> clearAuth() async {
    await removeToken();
    await removeUser();
  }
}
