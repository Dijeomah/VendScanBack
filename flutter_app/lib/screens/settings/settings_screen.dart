import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/auth_provider.dart';
import '../../providers/settings_provider.dart';
import '../../services/notification_service.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Settings'),
      ),
      body: Consumer<SettingsProvider>(
        builder: (context, settings, _) {
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              // Notifications Section
              _buildSectionHeader('Notifications'),
              Card(
                child: Column(
                  children: [
                    SwitchListTile(
                      title: const Text('Notification Sound'),
                      subtitle: const Text(
                        'Play a sound when new orders arrive',
                      ),
                      value: settings.notificationSoundEnabled,
                      onChanged: (value) {
                        settings.setNotificationSoundEnabled(value);
                        NotificationService().setSoundEnabled(value);
                        if (value) {
                          // Play a test sound
                          NotificationService().playSimpleBeep();
                        }
                      },
                      secondary: Icon(
                        settings.notificationSoundEnabled
                            ? Icons.volume_up
                            : Icons.volume_off,
                        color: settings.notificationSoundEnabled
                            ? AppTheme.primaryColor
                            : AppTheme.getTextSecondary(context),
                      ),
                    ),
                    const Divider(height: 1),
                    SwitchListTile(
                      title: const Text('Vibration'),
                      subtitle: const Text(
                        'Vibrate when new orders arrive',
                      ),
                      value: settings.vibrationEnabled,
                      onChanged: (value) {
                        settings.setVibrationEnabled(value);
                        NotificationService().setVibrationEnabled(value);
                        if (value) {
                          // Test vibration
                          NotificationService().vibrate();
                        }
                      },
                      secondary: Icon(
                        settings.vibrationEnabled
                            ? Icons.vibration
                            : Icons.smartphone,
                        color: settings.vibrationEnabled
                            ? AppTheme.primaryColor
                            : AppTheme.getTextSecondary(context),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // Auto Refresh Section
              _buildSectionHeader('Auto Refresh'),
              Card(
                child: Column(
                  children: [
                    SwitchListTile(
                      title: const Text('Auto Refresh Orders'),
                      subtitle: const Text(
                        'Automatically check for new orders',
                      ),
                      value: settings.autoRefreshEnabled,
                      onChanged: (value) {
                        settings.setAutoRefreshEnabled(value);
                      },
                      secondary: Icon(
                        Icons.refresh,
                        color: settings.autoRefreshEnabled
                            ? AppTheme.primaryColor
                            : AppTheme.getTextSecondary(context),
                      ),
                    ),
                    if (settings.autoRefreshEnabled) ...[
                      const Divider(height: 1),
                      ListTile(
                        title: const Text('Refresh Interval'),
                        subtitle: Text(
                          '${settings.refreshIntervalSeconds} seconds',
                        ),
                        leading: const Icon(Icons.timer),
                        trailing: const Icon(Icons.chevron_right),
                        onTap: () => _showRefreshIntervalDialog(context, settings),
                      ),
                    ],
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // Appearance Section
              _buildSectionHeader('Appearance'),
              Card(
                child: Column(
                  children: [
                    ListTile(
                      title: const Text('Theme'),
                      subtitle: Text(_getThemeModeLabel(settings.themeMode)),
                      leading: Icon(
                        _getThemeModeIcon(settings.themeMode),
                        color: Theme.of(context).colorScheme.primary,
                      ),
                      trailing: const Icon(Icons.chevron_right),
                      onTap: () => _showThemeModeDialog(context, settings),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // Test Section
              _buildSectionHeader('Test Notifications'),
              Card(
                child: ListTile(
                  title: const Text('Test Notification Sound'),
                  subtitle: const Text('Play the notification sound'),
                  leading: const Icon(Icons.play_circle_outline),
                  trailing: ElevatedButton(
                    onPressed: () {
                      NotificationService().notifyNewOrder();
                      Helpers.showToast('Notification played!');
                    },
                    child: const Text('Test'),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Account Section
              _buildSectionHeader('Account'),
              Card(
                child: Column(
                  children: [
                    Consumer<AuthProvider>(
                      builder: (context, auth, _) {
                        final user = auth.currentUser;
                        return ListTile(
                          title: Text(user?.name ?? 'User'),
                          subtitle: Text(user?.email ?? ''),
                          leading: CircleAvatar(
                            backgroundColor: AppTheme.primaryColor,
                            child: Text(
                              (user?.name.isNotEmpty ?? false)
                                  ? user!.name[0].toUpperCase()
                                  : 'U',
                              style: const TextStyle(color: Colors.white),
                            ),
                          ),
                        );
                      },
                    ),
                    const Divider(height: 1),
                    ListTile(
                      title: const Text(
                        'Logout',
                        style: TextStyle(color: AppTheme.errorColor),
                      ),
                      leading: const Icon(
                        Icons.logout,
                        color: AppTheme.errorColor,
                      ),
                      onTap: () => _showLogoutDialog(context),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // App Info
              _buildSectionHeader('About'),
              Card(
                child: Column(
                  children: [
                    const ListTile(
                      title: Text('App Version'),
                      subtitle: Text('1.0.0'),
                      leading: Icon(Icons.info_outline),
                    ),
                    const Divider(height: 1),
                    ListTile(
                      title: const Text('VendScan'),
                      subtitle: const Text('QR Menu Management System'),
                      leading: const Icon(
                        Icons.qr_code_scanner,
                        color: AppTheme.primaryColor,
                      ),
                      onTap: () {
                        // Show about dialog
                        showAboutDialog(
                          context: context,
                          applicationName: 'VendScan',
                          applicationVersion: '1.0.0',
                          applicationIcon: const Icon(
                            Icons.qr_code_scanner,
                            size: 48,
                            color: AppTheme.primaryColor,
                          ),
                          children: [
                            const Text(
                              'QR Menu Management System for Restaurants',
                            ),
                          ],
                        );
                      },
                    ),
                  ],
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 8),
      child: Text(
        title,
        style: AppTheme.titleMedium.copyWith(
          color: AppTheme.getTextSecondary(context),
        ),
      ),
    );
  }

  void _showRefreshIntervalDialog(
    BuildContext context,
    SettingsProvider settings,
  ) {
    final intervals = [15, 30, 60, 120, 300];
    final labels = ['15 sec', '30 sec', '1 min', '2 min', '5 min'];

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Refresh Interval'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: List.generate(intervals.length, (index) {
            return RadioListTile<int>(
              title: Text(labels[index]),
              value: intervals[index],
              groupValue: settings.refreshIntervalSeconds,
              onChanged: (value) {
                if (value != null) {
                  settings.setRefreshIntervalSeconds(value);
                  Navigator.pop(context);
                }
              },
            );
          }),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel'),
          ),
        ],
      ),
    );
  }

  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Logout'),
        content: const Text('Are you sure you want to logout?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(context);
              await context.read<AuthProvider>().logout();
              Helpers.showToast('Logged out successfully');
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.errorColor,
            ),
            child: const Text('Logout'),
          ),
        ],
      ),
    );
  }

  String _getThemeModeLabel(ThemeMode mode) {
    switch (mode) {
      case ThemeMode.light:
        return 'Light';
      case ThemeMode.dark:
        return 'Dark';
      case ThemeMode.system:
        return 'System Default';
    }
  }

  IconData _getThemeModeIcon(ThemeMode mode) {
    switch (mode) {
      case ThemeMode.light:
        return Icons.light_mode;
      case ThemeMode.dark:
        return Icons.dark_mode;
      case ThemeMode.system:
        return Icons.brightness_auto;
    }
  }

  void _showThemeModeDialog(
    BuildContext context,
    SettingsProvider settings,
  ) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Choose Theme'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            _buildThemeOption(
              context,
              settings,
              ThemeMode.system,
              'System Default',
              'Follow device settings',
              Icons.brightness_auto,
            ),
            _buildThemeOption(
              context,
              settings,
              ThemeMode.light,
              'Light',
              'Always use light theme',
              Icons.light_mode,
            ),
            _buildThemeOption(
              context,
              settings,
              ThemeMode.dark,
              'Dark',
              'Always use dark theme',
              Icons.dark_mode,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel'),
          ),
        ],
      ),
    );
  }

  Widget _buildThemeOption(
    BuildContext context,
    SettingsProvider settings,
    ThemeMode mode,
    String title,
    String subtitle,
    IconData icon,
  ) {
    final isSelected = settings.themeMode == mode;
    return ListTile(
      leading: Icon(
        icon,
        color: isSelected ? Theme.of(context).colorScheme.primary : null,
      ),
      title: Text(
        title,
        style: TextStyle(
          fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
          color: isSelected ? Theme.of(context).colorScheme.primary : null,
        ),
      ),
      subtitle: Text(subtitle),
      trailing: isSelected
          ? Icon(
              Icons.check_circle,
              color: Theme.of(context).colorScheme.primary,
            )
          : null,
      onTap: () {
        settings.setThemeMode(mode);
        Navigator.pop(context);
      },
    );
  }
}
