import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class SettingsProvider extends ChangeNotifier {
  static const String _notificationSoundKey = 'notification_sound_enabled';
  static const String _vibrationKey = 'vibration_enabled';
  static const String _autoRefreshKey = 'auto_refresh_enabled';
  static const String _refreshIntervalKey = 'refresh_interval_seconds';
  static const String _themeModeKey = 'theme_mode';

  bool _notificationSoundEnabled = true;
  bool _vibrationEnabled = true;
  bool _autoRefreshEnabled = true;
  int _refreshIntervalSeconds = 30;
  ThemeMode _themeMode = ThemeMode.system;

  bool get notificationSoundEnabled => _notificationSoundEnabled;
  bool get vibrationEnabled => _vibrationEnabled;
  bool get autoRefreshEnabled => _autoRefreshEnabled;
  int get refreshIntervalSeconds => _refreshIntervalSeconds;
  ThemeMode get themeMode => _themeMode;

  Future<void> loadSettings() async {
    final prefs = await SharedPreferences.getInstance();

    _notificationSoundEnabled = prefs.getBool(_notificationSoundKey) ?? true;
    _vibrationEnabled = prefs.getBool(_vibrationKey) ?? true;
    _autoRefreshEnabled = prefs.getBool(_autoRefreshKey) ?? true;
    _refreshIntervalSeconds = prefs.getInt(_refreshIntervalKey) ?? 30;

    // Load theme mode
    final themeModeString = prefs.getString(_themeModeKey) ?? 'system';
    _themeMode = _themeModeFromString(themeModeString);

    notifyListeners();
  }

  ThemeMode _themeModeFromString(String value) {
    switch (value) {
      case 'light':
        return ThemeMode.light;
      case 'dark':
        return ThemeMode.dark;
      default:
        return ThemeMode.system;
    }
  }

  String _themeModeToString(ThemeMode mode) {
    switch (mode) {
      case ThemeMode.light:
        return 'light';
      case ThemeMode.dark:
        return 'dark';
      default:
        return 'system';
    }
  }

  Future<void> setThemeMode(ThemeMode mode) async {
    _themeMode = mode;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_themeModeKey, _themeModeToString(mode));
    notifyListeners();
  }

  Future<void> setNotificationSoundEnabled(bool value) async {
    _notificationSoundEnabled = value;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_notificationSoundKey, value);
    notifyListeners();
  }

  Future<void> setVibrationEnabled(bool value) async {
    _vibrationEnabled = value;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_vibrationKey, value);
    notifyListeners();
  }

  Future<void> setAutoRefreshEnabled(bool value) async {
    _autoRefreshEnabled = value;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_autoRefreshKey, value);
    notifyListeners();
  }

  Future<void> setRefreshIntervalSeconds(int value) async {
    _refreshIntervalSeconds = value;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt(_refreshIntervalKey, value);
    notifyListeners();
  }
}
