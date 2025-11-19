import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'providers/auth_provider.dart';
import 'providers/vendor_provider.dart';
import 'providers/server_provider.dart';
import 'providers/settings_provider.dart';
import 'screens/auth/login_screen.dart';
import 'screens/vendor/vendor_dashboard_screen.dart';
import 'screens/server/server_dashboard_screen.dart';
import 'services/api_service.dart';
import 'services/notification_service.dart';
import 'services/storage_service.dart';
import 'utils/constants.dart';
import 'utils/theme.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize services
  await StorageService().init();
  ApiService().init();

  runApp(const VendScanApp());
}

class VendScanApp extends StatelessWidget {
  const VendScanApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => VendorProvider()),
        ChangeNotifierProvider(create: (_) => ServerProvider()),
        ChangeNotifierProvider(create: (_) {
          final settingsProvider = SettingsProvider();
          settingsProvider.loadSettings().then((_) {
            // Sync notification service with settings
            NotificationService().setSoundEnabled(settingsProvider.notificationSoundEnabled);
            NotificationService().setVibrationEnabled(settingsProvider.vibrationEnabled);
          });
          return settingsProvider;
        }),
      ],
      child: Consumer<SettingsProvider>(
        builder: (context, settingsProvider, _) {
          return MaterialApp(
            title: AppConstants.appName,
            theme: AppTheme.lightTheme,
            darkTheme: AppTheme.darkTheme,
            themeMode: settingsProvider.themeMode,
            debugShowCheckedModeBanner: false,
            home: const AuthWrapper(),
          );
        },
      ),
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, authProvider, _) {
        if (!authProvider.isAuthenticated) {
          return const LoginScreen();
        }

        // Route based on user role
        if (authProvider.isVendor) {
          return const VendorDashboardScreen();
        } else if (authProvider.isServer) {
          return const ServerDashboardScreen();
        } else {
          // Default or admin
          return const LoginScreen();
        }
      },
    );
  }
}
