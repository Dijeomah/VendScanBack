import 'package:flutter/foundation.dart';
import '../models/dashboard.dart';
import '../models/order.dart';
import '../models/server.dart';
import '../services/server_service.dart';

class ServerProvider with ChangeNotifier {
  final ServerService _serverService = ServerService();

  // Dashboard
  DashboardStatistics? _dashboardStats;
  bool _isLoadingDashboard = false;

  DashboardStatistics? get dashboardStats => _dashboardStats;
  bool get isLoadingDashboard => _isLoadingDashboard;

  // Profile
  Server? _profile;
  bool _isLoadingProfile = false;

  Server? get profile => _profile;
  bool get isLoadingProfile => _isLoadingProfile;

  // Assigned Resources
  List<TableAssignment> _assignedTables = [];
  List<BusinessAssignment> _assignedBusinesses = [];
  bool _isLoadingAssignments = false;

  List<TableAssignment> get assignedTables => _assignedTables;
  List<BusinessAssignment> get assignedBusinesses => _assignedBusinesses;
  bool get isLoadingAssignments => _isLoadingAssignments;

  // Orders
  List<Order> _orders = [];
  bool _isLoadingOrders = false;

  List<Order> get orders => _orders;
  bool get isLoadingOrders => _isLoadingOrders;

  // Dashboard Methods
  Future<void> loadDashboardStatistics() async {
    _isLoadingDashboard = true;
    notifyListeners();

    _dashboardStats = await _serverService.getDashboardStatistics();

    _isLoadingDashboard = false;
    notifyListeners();
  }

  // Profile Methods
  Future<void> loadProfile() async {
    _isLoadingProfile = true;
    notifyListeners();

    _profile = await _serverService.getProfile();

    _isLoadingProfile = false;
    notifyListeners();
  }

  // Assignment Methods
  Future<void> loadAssignments() async {
    _isLoadingAssignments = true;
    notifyListeners();

    _assignedTables = await _serverService.getAssignedTables();
    _assignedBusinesses = await _serverService.getAssignedBusinesses();

    _isLoadingAssignments = false;
    notifyListeners();
  }

  // Order Methods
  Future<void> loadOrders({String? status, int? tableId}) async {
    _isLoadingOrders = true;
    notifyListeners();

    _orders = await _serverService.getOrders(status: status, tableId: tableId);

    _isLoadingOrders = false;
    notifyListeners();
  }

  Future<bool> updateOrderStatus(int orderId, String status) async {
    final response = await _serverService.updateOrderStatus(
      orderId: orderId,
      status: status,
    );

    if (response.success) {
      await loadOrders();
      return true;
    }
    return false;
  }

  Future<void> refresh() async {
    await Future.wait([
      loadDashboardStatistics(),
      loadProfile(),
      loadAssignments(),
      loadOrders(),
    ]);
  }
}
