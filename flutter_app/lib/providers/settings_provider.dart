import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

class SettingsProvider extends ChangeNotifier {
  static const String _notificationSoundKey = 'notification_sound_enabled';
  static const String _vibrationKey = 'vibration_enabled';
  static const String _autoRefreshKey = 'auto_refresh_enabled';
  static const String _refreshIntervalKey = 'refresh_interval_seconds';

  bool _notificationSoundEnabled = true;
  bool _vibrationEnabled = true;
  bool _autoRefreshEnabled = true;
  int _refreshIntervalSeconds = 30;

  bool get notificationSoundEnabled => _notificationSoundEnabled;
  bool get vibrationEnabled => _vibrationEnabled;
  bool get autoRefreshEnabled => _autoRefreshEnabled;
  int get refreshIntervalSeconds => _refreshIntervalSeconds;

  Future<void> loadSettings() async {
    final prefs = await SharedPreferences.getInstance();

    _notificationSoundEnabled = prefs.getBool(_notificationSoundKey) ?? true;
    _vibrationEnabled = prefs.getBool(_vibrationKey) ?? true;
    _autoRefreshEnabled = prefs.getBool(_autoRefreshKey) ?? true;
    _refreshIntervalSeconds = prefs.getInt(_refreshIntervalKey) ?? 30;

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
