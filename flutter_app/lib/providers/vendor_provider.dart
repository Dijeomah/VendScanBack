import 'package:flutter/foundation.dart' hide Category;
import '../models/business.dart';
import '../models/category.dart';
import '../models/dashboard.dart';
import '../models/order.dart';
import '../models/server.dart';
import '../models/table.dart';
import '../services/vendor_service.dart';

class VendorProvider with ChangeNotifier {
  final VendorService _vendorService = VendorService();

  // Dashboard
  DashboardStatistics? _dashboardStats;
  bool _isLoadingDashboard = false;

  DashboardStatistics? get dashboardStats => _dashboardStats;
  bool get isLoadingDashboard => _isLoadingDashboard;

  // Business
  List<Business> _businesses = [];
  Business? _selectedBusiness;
  bool _isLoadingBusinesses = false;

  List<Business> get businesses => _businesses;
  Business? get selectedBusiness => _selectedBusiness;
  bool get isLoadingBusinesses => _isLoadingBusinesses;

  // Menu Items
  List<MenuItem> _menuItems = [];
  bool _isLoadingMenuItems = false;

  List<MenuItem> get menuItems => _menuItems;
  bool get isLoadingMenuItems => _isLoadingMenuItems;

  // Categories
  List<Category> _categories = [];
  bool _isLoadingCategories = false;

  List<Category> get categories => _categories;
  bool get isLoadingCategories => _isLoadingCategories;

  // Orders
  List<Order> _orders = [];
  bool _isLoadingOrders = false;

  List<Order> get orders => _orders;
  bool get isLoadingOrders => _isLoadingOrders;

  // Tables
  List<RestaurantTable> _tables = [];
  bool _isLoadingTables = false;

  List<RestaurantTable> get tables => _tables;
  bool get isLoadingTables => _isLoadingTables;

  // Servers
  List<Server> _servers = [];
  bool _isLoadingServers = false;

  List<Server> get servers => _servers;
  bool get isLoadingServers => _isLoadingServers;

  // Dashboard Methods
  Future<void> loadDashboardStatistics() async {
    _isLoadingDashboard = true;
    notifyListeners();

    _dashboardStats = await _vendorService.getDashboardStatistics();

    _isLoadingDashboard = false;
    notifyListeners();
  }

  // Business Methods
  Future<void> loadBusinesses() async {
    _isLoadingBusinesses = true;
    notifyListeners();

    _businesses = await _vendorService.getBusinessLinks();
    if (_businesses.isNotEmpty && _selectedBusiness == null) {
      _selectedBusiness = _businesses.first;
    }

    _isLoadingBusinesses = false;
    notifyListeners();
  }

  void selectBusiness(Business business) {
    _selectedBusiness = business;
    notifyListeners();

    // Reload data for selected business
    loadMenuItems();
    loadCategories();
    loadOrders();
    if (_selectedBusiness != null) {
      loadTables(_selectedBusiness!.id);
    }
  }

  // Menu Methods
  Future<void> loadMenuItems() async {
    _isLoadingMenuItems = true;
    notifyListeners();

    _menuItems = await _vendorService.getMenuItems(
      businessLinkId: _selectedBusiness?.id,
    );

    _isLoadingMenuItems = false;
    notifyListeners();
  }

  Future<bool> createMenuItem(MenuItem item) async {
    // Implementation would call vendor service
    return true;
  }

  Future<bool> updateMenuItem(int id, MenuItem item) async {
    // Implementation would call vendor service
    return true;
  }

  Future<bool> deleteMenuItem(int id) async {
    final response = await _vendorService.deleteMenuItem(id);
    if (response.success) {
      _menuItems.removeWhere((item) => item.id == id);
      notifyListeners();
      return true;
    }
    return false;
  }

  // Category Methods
  Future<void> loadCategories() async {
    _isLoadingCategories = true;
    notifyListeners();

    _categories = await _vendorService.getCategories(
      businessLinkId: _selectedBusiness?.id,
    );

    _isLoadingCategories = false;
    notifyListeners();
  }

  // Order Methods
  Future<void> loadOrders({String? status}) async {
    _isLoadingOrders = true;
    notifyListeners();

    _orders = await _vendorService.getOrders(
      status: status,
      businessLinkId: _selectedBusiness?.id,
    );

    _isLoadingOrders = false;
    notifyListeners();
  }

  Future<bool> updateOrderStatus(int orderId, String status) async {
    final response = await _vendorService.updateOrderStatus(
      orderId: orderId,
      status: status,
    );

    if (response.success) {
      await loadOrders();
      return true;
    }
    return false;
  }

  // Table Methods
  Future<void> loadTables(int businessId) async {
    _isLoadingTables = true;
    notifyListeners();

    _tables = await _vendorService.getTables(businessId);

    _isLoadingTables = false;
    notifyListeners();
  }

  // Server Methods
  Future<void> loadServers() async {
    _isLoadingServers = true;
    notifyListeners();

    _servers = await _vendorService.getServers();

    _isLoadingServers = false;
    notifyListeners();
  }

  Future<void> refresh() async {
    await Future.wait([
      loadDashboardStatistics(),
      loadBusinesses(),
      loadMenuItems(),
      loadCategories(),
      loadOrders(),
      loadServers(),
    ]);
  }
}
