import 'dart:async';
import 'package:audioplayers/audioplayers.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/services.dart';

class NotificationService {
  static final NotificationService _instance = NotificationService._internal();
  factory NotificationService() => _instance;
  NotificationService._internal();

  final AudioPlayer _audioPlayer = AudioPlayer();
  bool _soundEnabled = true;
  bool _vibrationEnabled = true;

  // Track last known order count to detect new orders
  int _lastOrderCount = 0;
  Timer? _pollingTimer;

  bool get soundEnabled => _soundEnabled;
  bool get vibrationEnabled => _vibrationEnabled;

  void setSoundEnabled(bool enabled) {
    _soundEnabled = enabled;
  }

  void setVibrationEnabled(bool enabled) {
    _vibrationEnabled = enabled;
  }

  /// Play notification sound for new order
  Future<void> playNewOrderSound() async {
    if (!_soundEnabled) return;

    try {
      // Try to play from assets first
      await _audioPlayer.play(
        AssetSource('sounds/new_order.mp3'),
        volume: 1.0,
      );
    } catch (e) {
      if (kDebugMode) {
        print('Error playing custom sound: $e');
      }
      // Fallback: Play system notification sound using platform channel
      await _playSystemNotificationSound();
    }
  }

  /// Play a simple beep sound
  Future<void> playSimpleBeep() async {
    if (!_soundEnabled) return;

    try {
      // Generate a simple tone using a frequency
      await _audioPlayer.play(
        UrlSource('https://www.soundjay.com/buttons/beep-01a.mp3'),
        volume: 0.7,
      );
    } catch (e) {
      if (kDebugMode) {
        print('Error playing beep: $e');
      }
      await _playSystemNotificationSound();
    }
  }

  /// Play system notification sound as fallback
  Future<void> _playSystemNotificationSound() async {
    try {
      // Use system sound feedback
      await SystemSound.play(SystemSoundType.alert);
    } catch (e) {
      if (kDebugMode) {
        print('Error playing system sound: $e');
      }
    }
  }

  /// Vibrate the device
  Future<void> vibrate() async {
    if (!_vibrationEnabled) return;

    try {
      await HapticFeedback.heavyImpact();
    } catch (e) {
      if (kDebugMode) {
        print('Error vibrating: $e');
      }
    }
  }

  /// Notify user of new order with sound and vibration
  Future<void> notifyNewOrder() async {
    await Future.wait([
      playNewOrderSound(),
      vibrate(),
    ]);
  }

  /// Check if there are new orders and notify
  void checkAndNotifyNewOrders(int currentOrderCount) {
    if (currentOrderCount > _lastOrderCount && _lastOrderCount > 0) {
      // New orders detected
      notifyNewOrder();
    }
    _lastOrderCount = currentOrderCount;
  }

  /// Start polling for new orders
  void startPolling({
    required Duration interval,
    required Future<int> Function() getOrderCount,
  }) {
    stopPolling();
    _pollingTimer = Timer.periodic(interval, (timer) async {
      try {
        final count = await getOrderCount();
        checkAndNotifyNewOrders(count);
      } catch (e) {
        if (kDebugMode) {
          print('Error polling for orders: $e');
        }
      }
    });
  }

  /// Stop polling
  void stopPolling() {
    _pollingTimer?.cancel();
    _pollingTimer = null;
  }

  /// Reset the order count tracker
  void resetOrderCount() {
    _lastOrderCount = 0;
  }

  /// Dispose resources
  void dispose() {
    stopPolling();
    _audioPlayer.dispose();
  }
}
