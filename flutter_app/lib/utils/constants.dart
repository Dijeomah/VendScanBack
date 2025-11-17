class AppConstants {
  // API Configuration
  static const String baseUrl = 'http://localhost:8000/api';

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String roleKey = 'user_role';

  // App Info
  static const String appName = 'VendScan';
  static const String appVersion = '1.0.0';

  // Pagination
  static const int defaultPageSize = 20;

  // Timeouts
  static const int connectionTimeout = 30000;
  static const int receiveTimeout = 30000;

  // Order Status
  static const String orderStatusPending = 'pending';
  static const String orderStatusConfirmed = 'confirmed';
  static const String orderStatusPreparing = 'preparing';
  static const String orderStatusReady = 'ready';
  static const String orderStatusServed = 'served';
  static const String orderStatusCompleted = 'completed';
  static const String orderStatusCancelled = 'cancelled';

  // Payment Status
  static const String paymentStatusPending = 'pending';
  static const String paymentStatusPaid = 'paid';
  static const String paymentStatusFailed = 'failed';

  // User Roles
  static const String roleAdmin = 'admin';
  static const String roleVendor = 'vendor';
  static const String roleServer = 'server';

  // Image Settings
  static const int maxImageSize = 5 * 1024 * 1024; // 5MB
  static const List<String> allowedImageTypes = ['jpg', 'jpeg', 'png', 'webp'];

  // Validation
  static const int minPasswordLength = 8;
  static const int maxNameLength = 100;

  // Polling Intervals (milliseconds)
  static const int orderPollingInterval = 5000;
  static const int notificationPollingInterval = 10000;
}

class ApiEndpoints {
  // Auth
  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String logout = '/auth/logout';
  static const String refresh = '/auth/refresh';

  // Vendor
  static const String vendorDashboard = '/vendor/dashboard';
  static const String vendorProfile = '/vendor/profile';
  static const String vendorBusinessLinks = '/vendor/business-links';
  static const String vendorBusinessInfo = '/vendor/business-info';
  static const String vendorItems = '/vendor/items';
  static const String vendorCategories = '/vendor/categories';
  static const String vendorSubcategories = '/vendor/subcategories';
  static const String vendorOrders = '/vendor/orders';
  static const String vendorTables = '/vendor/businesses';
  static const String vendorServers = '/vendor/servers';
  static const String vendorNotifications = '/vendor/notifications';
  static const String vendorMedia = '/vendor/media';
  static const String vendorSubscription = '/vendor/subscription';

  // Server
  static const String serverDashboard = '/server/dashboard/statistics';
  static const String serverProfile = '/server/profile';
  static const String serverAssignedTables = '/server/assigned-tables';
  static const String serverAssignedBusinesses = '/server/assigned-businesses';
  static const String serverOrders = '/server/orders';
  static const String serverNotifications = '/server/notifications';

  // Public
  static const String publicMenu = '/public/menu';
  static const String publicOrders = '/public/orders';
}
