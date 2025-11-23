import '../models/dashboard.dart';
import '../models/notification.dart';
import '../models/order.dart';
import '../models/server.dart';
import '../utils/constants.dart';
import 'api_service.dart';

class ServerService {
  final _api = ApiService();

  // Dashboard
  Future<DashboardStatistics?> getDashboardStatistics() async {
    try {
      final response = await _api.get(ApiEndpoints.serverDashboard);
      if (response.success && response.data != null) {
        return DashboardStatistics.fromJson(response.data);
      }
    } catch (e) {
      print('Error getting dashboard statistics: $e');
    }
    return null;
  }

  // Profile
  Future<Server?> getProfile() async {
    try {
      final response = await _api.get(ApiEndpoints.serverProfile);
      if (response.success && response.data != null) {
        return Server.fromJson(response.data);
      }
    } catch (e) {
      print('Error getting profile: $e');
    }
    return null;
  }

  // Assigned Resources
  Future<List<TableAssignment>> getAssignedTables() async {
    try {
      final response = await _api.get(ApiEndpoints.serverAssignedTables);
      if (response.success && response.data != null) {
        return (response.data as List)
            .map((e) => TableAssignment.fromJson(e))
            .toList();
      }
    } catch (e) {
      print('Error getting assigned tables: $e');
    }
    return [];
  }

  Future<List<BusinessAssignment>> getAssignedBusinesses() async {
    try {
      final response = await _api.get(ApiEndpoints.serverAssignedBusinesses);
      if (response.success && response.data != null) {
        return (response.data as List)
            .map((e) => BusinessAssignment.fromJson(e))
            .toList();
      }
    } catch (e) {
      print('Error getting assigned businesses: $e');
    }
    return [];
  }

  // Orders
Future<List<Order>> getOrders({
  String? status,
  int? tableId,
}) async {
  try {
    final queryParams = <String, dynamic>{};
    if (status != null) queryParams['status'] = status;
    if (tableId != null) queryParams['table_id'] = tableId;

    final response = await _api.get(
      ApiEndpoints.serverOrders,
      queryParameters: queryParams.isNotEmpty ? queryParams : null,
    );

    print('Drame Log: $response.success');
    if (response.success && response.data != null) {
      // Extract the actual orders list from the pagination structure
      final data = response.data['data'] as List;
      return data.map((e) => Order.fromJson(e)).toList();
    }
  } catch (e) {
    print('Error getting orders: $e');
  }
  return [];
}


  Future<Order?> getOrder(int id) async {
    try {
      final response = await _api.get('${ApiEndpoints.serverOrders}/$id');
      if (response.success && response.data != null) {
        return Order.fromJson(response.data);
      }
    } catch (e) {
      print('Error getting order: $e');
    }
    return null;
  }

  Future<ApiResponse> updateOrderStatus({
    required int orderId,
    required String status,
  }) async {
    return await _api.patch(
      '${ApiEndpoints.serverOrders}/$orderId/status',
      data: {'status': status},
    );
  }

  Future<ApiResponse> updatePaymentStatus({
    required int orderId,
    required String paymentStatus,
  }) async {
    return await _api.patch(
      '${ApiEndpoints.serverOrders}/$orderId/payment-status',
      data: {'payment_status': paymentStatus},
    );
  }

  // Notifications
  Future<List<AppNotification>> getNotifications() async {
    try {
      final response = await _api.get(ApiEndpoints.serverNotifications);
      if (response.success && response.data != null) {
        return (response.data as List)
            .map((e) => AppNotification.fromJson(e))
            .toList();
      }
    } catch (e) {
      print('Error getting notifications: $e');
    }
    return [];
  }

  Future<int> getUnreadNotificationCount() async {
    try {
      final response =
          await _api.get('${ApiEndpoints.serverNotifications}/unread-count');
      if (response.success && response.data != null) {
        return response.data['count'] ?? 0;
      }
    } catch (e) {
      print('Error getting unread notification count: $e');
    }
    return 0;
  }

  Future<ApiResponse> markNotificationAsRead(String notificationId) async {
    return await _api.post(
      '${ApiEndpoints.serverNotifications}/$notificationId/read',
    );
  }

  Future<ApiResponse> markAllNotificationsAsRead() async {
    return await _api.post(
      '${ApiEndpoints.serverNotifications}/mark-all-read',
    );
  }
}
