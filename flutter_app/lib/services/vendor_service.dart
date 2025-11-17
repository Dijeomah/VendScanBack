import 'dart:io';
import '../models/business.dart';
import '../models/category.dart';
import '../models/dashboard.dart';
import '../models/notification.dart';
import '../models/order.dart';
import '../models/server.dart';
import '../models/table.dart';
import '../utils/constants.dart';
import 'api_service.dart';

class VendorService {
  final _api = ApiService();

  // Dashboard
  Future<DashboardStatistics?> getDashboardStatistics() async {
    try {
      final response = await _api.get('${ApiEndpoints.vendorDashboard}/statistics');
      if (response.success && response.data != null) {
        return DashboardStatistics.fromJson(response.data);
      }
    } catch (e) {
      print('Error getting dashboard statistics: $e');
    }
    return null;
  }

  // Business Management
  Future<List<Business>> getBusinessLinks() async {
    try {
      final response = await _api.get(ApiEndpoints.vendorBusinessLinks);
      if (response.success && response.data != null) {
        return (response.data as List)
            .map((e) => Business.fromJson(e))
            .toList();
      }
    } catch (e) {
      print('Error getting business links: $e');
    }
    return [];
  }

  Future<ApiResponse> createBusiness({
    required String name,
    required String businessLink,
    String? description,
    String? businessType,
  }) async {
    return await _api.post(
      ApiEndpoints.vendorBusinessInfo,
      data: {
        'name': name,
        'business_link': businessLink,
        if (description != null) 'description': description,
        if (businessType != null) 'business_type': businessType,
      },
    );
  }

  Future<ApiResponse> updateBusiness({
    required int businessId,
    required Map<String, dynamic> data,
  }) async {
    return await _api.put(
      '${ApiEndpoints.vendorBusinessInfo}/$businessId',
      data: data,
    );
  }

  Future<ApiResponse> uploadBusinessMedia({
    required int businessId,
    File? headerImage,
    File? logoImage,
  }) async {
    try {
      final formData = <String, dynamic>{};

      if (headerImage != null) {
        formData['header_image'] = await _api.uploadFile(
          '${ApiEndpoints.vendorBusinessLinks}/$businessId/media',
          file: headerImage,
          fieldName: 'header_image',
        );
      }

      if (logoImage != null) {
        formData['logo_image'] = await _api.uploadFile(
          '${ApiEndpoints.vendorBusinessLinks}/$businessId/media',
          file: logoImage,
          fieldName: 'logo_image',
        );
      }

      return await _api.post(
        '${ApiEndpoints.vendorBusinessLinks}/$businessId/media',
        data: formData,
      );
    } catch (e) {
      return ApiResponse(success: false, message: e.toString());
    }
  }

  // Menu Management
  Future<List<MenuItem>> getMenuItems({int? businessLinkId}) async {
    try {
      final response = await _api.get(
        ApiEndpoints.vendorItems,
        queryParameters: businessLinkId != null
            ? {'business_link_id': businessLinkId}
            : null,
      );
      if (response.success && response.data != null) {
        return (response.data as List)
            .map((e) => MenuItem.fromJson(e))
            .toList();
      }
    } catch (e) {
      print('Error getting menu items: $e');
    }
    return [];
  }

  Future<MenuItem?> getMenuItem(int id) async {
    try {
      final response = await _api.get('${ApiEndpoints.vendorItems}/$id');
      if (response.success && response.data != null) {
        return MenuItem.fromJson(response.data);
      }
    } catch (e) {
      print('Error getting menu item: $e');
    }
    return null;
  }

  Future<ApiResponse> createMenuItem({
    required String name,
    required double price,
    required int businessLinkId,
    String? description,
    int? categoryId,
    int? subCategoryId,
    File? image,
  }) async {
    final data = {
      'name': name,
      'price': price,
      'business_link_id': businessLinkId,
      if (description != null) 'description': description,
      if (categoryId != null) 'category_id': categoryId,
      if (subCategoryId != null) 'sub_category_id': subCategoryId,
    };

    if (image != null) {
      return await _api.uploadFile(
        ApiEndpoints.vendorItems,
        file: image,
        fieldName: 'image',
        data: data,
      );
    } else {
      return await _api.post(ApiEndpoints.vendorItems, data: data);
    }
  }

  Future<ApiResponse> updateMenuItem({
    required int id,
    String? name,
    double? price,
    String? description,
    int? categoryId,
    int? subCategoryId,
    bool? isAvailable,
    File? image,
  }) async {
    final data = <String, dynamic>{};
    if (name != null) data['name'] = name;
    if (price != null) data['price'] = price;
    if (description != null) data['description'] = description;
    if (categoryId != null) data['category_id'] = categoryId;
    if (subCategoryId != null) data['sub_category_id'] = subCategoryId;
    if (isAvailable != null) data['is_available'] = isAvailable;

    if (image != null) {
      return await _api.uploadFile(
        '${ApiEndpoints.vendorItems}/$id',
        file: image,
        fieldName: 'image',
        data: data,
      );
    } else {
      return await _api.put('${ApiEndpoints.vendorItems}/$id', data: data);
    }
  }

  Future<ApiResponse> deleteMenuItem(int id) async {
    return await _api.delete('${ApiEndpoints.vendorItems}/$id');
  }

  // Category Management
  Future<List<Category>> getCategories({int? businessLinkId}) async {
    try {
      final response = await _api.get(
        ApiEndpoints.vendorCategories,
        queryParameters: businessLinkId != null
            ? {'business_link_id': businessLinkId}
            : null,
      );
      if (response.success && response.data != null) {
        return (response.data as List)
            .map((e) => Category.fromJson(e))
            .toList();
      }
    } catch (e) {
      print('Error getting categories: $e');
    }
    return [];
  }

  Future<ApiResponse> createCategory({
    required String name,
    required int businessLinkId,
    String? description,
  }) async {
    return await _api.post(
      ApiEndpoints.vendorCategories,
      data: {
        'name': name,
        'business_link_id': businessLinkId,
        if (description != null) 'description': description,
      },
    );
  }

  // Order Management
  Future<List<Order>> getOrders({
    String? status,
    String? paymentStatus,
    int? businessLinkId,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (status != null) queryParams['status'] = status;
      if (paymentStatus != null) queryParams['payment_status'] = paymentStatus;
      if (businessLinkId != null) {
        queryParams['business_link_id'] = businessLinkId;
      }

      final response = await _api.get(
        ApiEndpoints.vendorOrders,
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );

      if (response.success && response.data != null) {
        return (response.data as List).map((e) => Order.fromJson(e)).toList();
      }
    } catch (e) {
      print('Error getting orders: $e');
    }
    return [];
  }

  Future<Order?> getOrder(int id) async {
    try {
      final response = await _api.get('${ApiEndpoints.vendorOrders}/$id');
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
      '${ApiEndpoints.vendorOrders}/$orderId/status',
      data: {'status': status},
    );
  }

  Future<ApiResponse> updatePaymentStatus({
    required int orderId,
    required String paymentStatus,
  }) async {
    return await _api.patch(
      '${ApiEndpoints.vendorOrders}/$orderId/payment-status',
      data: {'payment_status': paymentStatus},
    );
  }

  Future<OrderStatistics?> getOrderStatistics() async {
    try {
      final response = await _api.get('${ApiEndpoints.vendorOrders}/statistics');
      if (response.success && response.data != null) {
        return OrderStatistics.fromJson(response.data);
      }
    } catch (e) {
      print('Error getting order statistics: $e');
    }
    return null;
  }

  // Table Management
  Future<List<RestaurantTable>> getTables(int businessId) async {
    try {
      final response = await _api.get(
        '${ApiEndpoints.vendorTables}/$businessId/tables',
      );
      if (response.success && response.data != null) {
        return (response.data as List)
            .map((e) => RestaurantTable.fromJson(e))
            .toList();
      }
    } catch (e) {
      print('Error getting tables: $e');
    }
    return [];
  }

  Future<ApiResponse> createTable({
    required int businessId,
    required String tableNumber,
    int? capacity,
    String? location,
  }) async {
    return await _api.post(
      '${ApiEndpoints.vendorTables}/$businessId/tables',
      data: {
        'table_number': tableNumber,
        if (capacity != null) 'capacity': capacity,
        if (location != null) 'location': location,
      },
    );
  }

  Future<ApiResponse> bulkCreateTables({
    required int businessId,
    required int count,
    String? prefix,
  }) async {
    return await _api.post(
      '${ApiEndpoints.vendorTables}/$businessId/tables/bulk-create',
      data: {
        'count': count,
        if (prefix != null) 'prefix': prefix,
      },
    );
  }

  Future<ApiResponse> deleteTable({
    required int businessId,
    required int tableId,
  }) async {
    return await _api.delete(
      '${ApiEndpoints.vendorTables}/$businessId/tables/$tableId',
    );
  }

  // Server Management
  Future<List<Server>> getServers() async {
    try {
      final response = await _api.get(ApiEndpoints.vendorServers);
      if (response.success && response.data != null) {
        return (response.data as List).map((e) => Server.fromJson(e)).toList();
      }
    } catch (e) {
      print('Error getting servers: $e');
    }
    return [];
  }

  Future<ApiResponse> createServer({
    required String name,
    required String email,
    required String password,
    String? phone,
  }) async {
    return await _api.post(
      ApiEndpoints.vendorServers,
      data: {
        'name': name,
        'email': email,
        'password': password,
        if (phone != null) 'phone': phone,
      },
    );
  }

  Future<ApiResponse> assignServerToBusiness({
    required int serverId,
    required int businessId,
  }) async {
    return await _api.post(
      '${ApiEndpoints.vendorServers}/$serverId/assign-business',
      data: {'business_id': businessId},
    );
  }

  Future<ApiResponse> assignServerToTable({
    required int businessId,
    required int serverId,
    required int tableId,
  }) async {
    return await _api.post(
      '${ApiEndpoints.vendorTables}/$businessId/assignments',
      data: {
        'server_id': serverId,
        'table_id': tableId,
      },
    );
  }

  // Notifications
  Future<List<AppNotification>> getNotifications() async {
    try {
      final response = await _api.get(ApiEndpoints.vendorNotifications);
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
          await _api.get('${ApiEndpoints.vendorNotifications}/unread-count');
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
      '${ApiEndpoints.vendorNotifications}/$notificationId/read',
    );
  }

  Future<ApiResponse> markAllNotificationsAsRead() async {
    return await _api.post(
      '${ApiEndpoints.vendorNotifications}/mark-all-read',
    );
  }
}
