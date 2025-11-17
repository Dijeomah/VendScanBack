# VendScan Flutter App

A comprehensive Flutter mobile application for the VendScan QR Menu Management System.

## Features

### Customer Features
- **QR Code Scanning** - Scan restaurant table QR codes
- **Menu Browsing** - View restaurant menus with categories
- **Order Placement** - Place orders with notes and payment selection
- **Real-time Order Tracking** - Track order status with visual timeline
- **Order History** - View past orders

### Vendor Features
- **Dashboard** - View statistics and analytics
- **Menu Management** - Create, edit, delete menu items
- **Order Management** - View and update order status
- **Table Management** - Create tables and generate QR codes
- **Server Management** - Manage servers and assignments
- **Business Profile** - Update business information and media

## Prerequisites

- Flutter SDK 3.0.0 or higher
- Dart SDK 3.0.0 or higher
- Android Studio / Xcode (for mobile development)
- VendScan Backend API running

## Installation

1. **Clone the repository:**
   ```bash
   cd flutter_app
   ```

2. **Install dependencies:**
   ```bash
   flutter pub get
   ```

3. **Configure API endpoint:**
   Edit `lib/utils/constants.dart` and set your backend URL:
   ```dart
   static const String baseUrl = 'http://your-backend-url/api';
   ```

4. **Run the app:**
   ```bash
   # For Android
   flutter run

   # For iOS
   flutter run -d ios

   # For a specific device
   flutter devices
   flutter run -d <device-id>
   ```

## Project Structure

```
flutter_app/
├── lib/
│   ├── main.dart                 # Entry point
│   ├── models/                   # Data models
│   │   ├── user.dart
│   │   ├── menu.dart
│   │   ├── order.dart
│   │   └── ...
│   ├── services/                 # API & Services
│   │   ├── api_service.dart
│   │   ├── auth_service.dart
│   │   └── storage_service.dart
│   ├── providers/                # State management
│   │   ├── auth_provider.dart
│   │   ├── menu_provider.dart
│   │   └── order_provider.dart
│   ├── screens/                  # UI Screens
│   │   ├── auth/
│   │   ├── customer/
│   │   └── vendor/
│   ├── widgets/                  # Reusable widgets
│   │   ├── common/
│   │   └── custom/
│   └── utils/                    # Utilities
│       ├── constants.dart
│       ├── theme.dart
│       └── helpers.dart
├── assets/
│   ├── images/
│   └── icons/
├── pubspec.yaml
└── README.md
```

## Configuration

### Android Configuration

1. **Update `android/app/build.gradle`:**
   ```gradle
   defaultConfig {
       minSdkVersion 21
       targetSdkVersion 33
   }
   ```

2. **Add permissions in `android/app/src/main/AndroidManifest.xml`:**
   ```xml
   <uses-permission android:name="android.permission.CAMERA" />
   <uses-permission android:name="android.permission.INTERNET" />
   ```

### iOS Configuration

1. **Update `ios/Runner/Info.plist`:**
   ```xml
   <key>NSCameraUsageDescription</key>
   <string>Camera permission is required for QR code scanning</string>
   <key>NSPhotoLibraryUsageDescription</key>
   <string>Photo library access is required for uploading images</string>
   ```

## Building for Production

### Android
```bash
flutter build apk --release
flutter build appbundle --release
```

### iOS
```bash
flutter build ios --release
```

## API Integration

The app communicates with the VendScan Laravel backend API. Ensure the backend is running and accessible.

**Base URL Configuration:**
- Development: `http://localhost:8000/api`
- Production: `https://your-domain.com/api`

## State Management

The app uses Provider for state management with the following providers:
- `AuthProvider` - Authentication state
- `MenuProvider` - Menu data and operations
- `OrderProvider` - Order management
- `VendorProvider` - Vendor operations

## Testing

```bash
# Run tests
flutter test

# Run with coverage
flutter test --coverage
```

## Troubleshooting

### Common Issues

1. **QR Scanner not working:**
   - Ensure camera permissions are granted
   - Check AndroidManifest.xml and Info.plist configurations

2. **API connection issues:**
   - Verify backend URL in constants.dart
   - Check network permissions
   - Ensure backend is running

3. **Build errors:**
   ```bash
   flutter clean
   flutter pub get
   flutter run
   ```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

MIT License

## Support

For issues and questions:
- GitHub Issues: [Report an issue](https://github.com/yourusername/VendScanBack/issues)
- Email: support@vendscan.com

---

**Version:** 1.0.0

**Last Updated:** 2025-11-17
