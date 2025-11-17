# Flutter App Implementation Guide

## Overview

This document provides a comprehensive guide for the VendScan Flutter mobile application, focusing on Vendor and Server features.

## Project Structure

```
flutter_app/
├── lib/
│   ├── main.dart                     # App entry point
│   ├── models/                       # Data models
│   │   ├── user.dart
│   │   ├── business.dart
│   │   ├── category.dart
│   │   ├── order.dart
│   │   ├── table.dart
│   │   ├── server.dart
│   │   ├── notification.dart
│   │   └── dashboard.dart
│   ├── services/                     # API & Business logic
│   │   ├── api_service.dart         # HTTP client
│   │   ├── auth_service.dart        # Authentication
│   │   ├── storage_service.dart     # Local storage
│   │   ├── vendor_service.dart      # Vendor operations
│   │   └── server_service.dart      # Server operations
│   ├── providers/                    # State management
│   │   ├── auth_provider.dart
│   │   ├── vendor_provider.dart
│   │   └── server_provider.dart
│   ├── screens/                      # UI Screens
│   │   ├── auth/
│   │   │   └── login_screen.dart
│   │   ├── vendor/
│   │   │   └── vendor_dashboard_screen.dart
│   │   └── server/
│   │       └── server_dashboard_screen.dart
│   ├── widgets/                      # Reusable widgets
│   │   └── stat_card.dart
│   └── utils/                        # Utilities
│       ├── constants.dart
│       ├── theme.dart
│       └── helpers.dart
├── pubspec.yaml
└── README.md
```

## Features Implemented

### Core Infrastructure
- ✅ Complete data models for all entities
- ✅ API service with Dio HTTP client
- ✅ JWT authentication with token refresh
- ✅ Local storage with SharedPreferences
- ✅ Provider state management
- ✅ Custom theme and styling
- ✅ Helper utilities

### Authentication
- ✅ Login screen
- ✅ JWT token management
- ✅ Auto token refresh
- ✅ Role-based routing

### Vendor Features
- ✅ Dashboard with statistics
- ✅ Business management (ready for implementation)
- ✅ Menu management (ready for implementation)
- ✅ Order management (ready for implementation)
- ✅ Table management (ready for implementation)
- ✅ Server management (ready for implementation)
- ✅ Notification system (ready for implementation)

### Server Features
- ✅ Dashboard with statistics
- ✅ Assigned tables view
- ✅ Order management (ready for implementation)
- ✅ Notification system (ready for implementation)

## Setup Instructions

### 1. Prerequisites
- Flutter SDK 3.0.0+
- Dart SDK 3.0.0+
- Android Studio / Xcode
- VendScan Backend API

### 2. Installation

```bash
cd flutter_app
flutter pub get
```

### 3. Configuration

Edit `lib/utils/constants.dart`:
```dart
static const String baseUrl = 'http://YOUR_BACKEND_URL/api';
```

For local development:
- Android Emulator: `http://10.0.2.2:8000/api`
- iOS Simulator: `http://localhost:8000/api`
- Physical Device: `http://YOUR_IP:8000/api`

### 4. Running the App

```bash
# Check available devices
flutter devices

# Run on specific device
flutter run -d <device-id>

# Run in debug mode
flutter run

# Run in release mode
flutter run --release
```

## API Integration

The app communicates with the Laravel backend API using these endpoints:

### Authentication
- `POST /auth/login` - User login
- `POST /auth/logout` - Logout
- `POST /auth/refresh` - Refresh token

### Vendor
- `GET /vendor/dashboard/statistics` - Dashboard stats
- `GET /vendor/business-links` - Get businesses
- `GET /vendor/items` - Get menu items
- `GET /vendor/categories` - Get categories
- `GET /vendor/orders` - Get orders
- `GET /vendor/businesses/{id}/tables` - Get tables
- `GET /vendor/servers` - Get servers
- `PATCH /vendor/orders/{id}/status` - Update order status

### Server
- `GET /server/dashboard/statistics` - Dashboard stats
- `GET /server/assigned-tables` - Get assigned tables
- `GET /server/assigned-businesses` - Get assigned businesses
- `GET /server/orders` - Get orders
- `PATCH /server/orders/{id}/status` - Update order status

## Development Roadmap

### Phase 1: Core Features (Current)
- [x] Authentication
- [x] Dashboard
- [x] Basic navigation
- [ ] Menu management screens
- [ ] Order management screens
- [ ] Table management screens
- [ ] Server management screens

### Phase 2: Enhanced Features
- [ ] Real-time notifications
- [ ] Image upload for menu items
- [ ] QR code generation and display
- [ ] Bulk operations
- [ ] Search and filters
- [ ] Sort functionality

### Phase 3: Advanced Features
- [ ] Offline support
- [ ] Push notifications
- [ ] Analytics and reporting
- [ ] Export functionality
- [ ] Multi-language support
- [ ] Dark mode

## Key Components to Implement

### 1. Menu Management Screen

Create `lib/screens/vendor/menu_management_screen.dart`:
- List all menu items
- Add new menu item with image upload
- Edit existing menu item
- Delete menu item
- Filter by category
- Search functionality

### 2. Order Management Screen

Create `lib/screens/vendor/order_management_screen.dart`:
- List orders with filters (status, date)
- View order details
- Update order status
- Real-time order updates

### 3. Table Management Screen

Create `lib/screens/vendor/table_management_screen.dart`:
- List all tables
- Create new table
- Bulk create tables
- View and download QR codes
- Assign servers to tables

### 4. Server Management Screen

Create `lib/screens/vendor/server_management_screen.dart`:
- List all servers
- Create new server
- Assign servers to businesses
- Assign servers to tables
- View server statistics

### 5. Server Order Screen

Create `lib/screens/server/server_orders_screen.dart`:
- List orders for assigned tables
- Filter by table and status
- Update order status
- View order details

## Code Examples

### Creating a New Screen

```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class MenuManagementScreen extends StatefulWidget {
  const MenuManagementScreen({super.key});

  @override
  State<MenuManagementScreen> createState() => _MenuManagementScreenState();
}

class _MenuManagementScreenState extends State<MenuManagementScreen> {
  @override
  void initState() {
    super.initState();
    context.read<VendorProvider>().loadMenuItems();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Menu Management')),
      body: Consumer<VendorProvider>(
        builder: (context, provider, _) {
          if (provider.isLoadingMenuItems) {
            return const Center(child: CircularProgressIndicator());
          }

          return ListView.builder(
            itemCount: provider.menuItems.length,
            itemBuilder: (context, index) {
              final item = provider.menuItems[index];
              return ListTile(
                title: Text(item.name),
                subtitle: Text('\$${item.price}'),
              );
            },
          );
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          // Navigate to add item screen
        },
        child: const Icon(Icons.add),
      ),
    );
  }
}
```

### Making API Calls

```dart
// In a provider or service
Future<void> createMenuItem(MenuItem item) async {
  final response = await _vendorService.createMenuItem(
    name: item.name,
    price: item.price,
    businessLinkId: selectedBusiness!.id,
    description: item.description,
    categoryId: item.categoryId,
  );

  if (response.success) {
    await loadMenuItems(); // Refresh list
    Helpers.showToast('Menu item created successfully');
  } else {
    Helpers.showToast(response.message, isError: true);
  }
}
```

## Testing

```bash
# Run tests
flutter test

# Run with coverage
flutter test --coverage

# Run integration tests
flutter drive --target=test_driver/app.dart
```

## Building for Production

### Android

```bash
# Build APK
flutter build apk --release

# Build App Bundle
flutter build appbundle --release
```

### iOS

```bash
# Build for iOS
flutter build ios --release
```

## Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Check backend URL in constants.dart
   - Ensure backend is running
   - Check network permissions

2. **Dependencies Error**
   ```bash
   flutter clean
   flutter pub get
   flutter run
   ```

3. **Build Errors**
   - Update Flutter: `flutter upgrade`
   - Clear cache: `flutter clean`
   - Reinstall dependencies

## Contributing

When adding new features:
1. Create feature branch
2. Follow existing code structure
3. Update models if API changes
4. Add error handling
5. Test thoroughly
6. Update this documentation

## Resources

- [Flutter Documentation](https://flutter.dev/docs)
- [Provider Documentation](https://pub.dev/packages/provider)
- [Dio Documentation](https://pub.dev/packages/dio)
- [VendScan API Documentation](../README.md)

## Support

For issues or questions:
- GitHub Issues
- Email: support@vendscan.com

---

**Version:** 1.0.0
**Last Updated:** 2025-11-17
**Status:** Development
