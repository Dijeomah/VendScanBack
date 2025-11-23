class DashboardStatistics {
  final int totalOrders;
  final int todayOrders;
  final int activeOrders;
  final int completedOrders;
  final double totalRevenue;
  final double todayRevenue;
  final int totalItems;
  final int totalTables;
  final int totalServers;
  final int totalBusinesses;
  final List<RecentOrder>? recentOrders;
  final Map<String, dynamic>? ordersByStatus;
  final Map<String, dynamic>? revenueData;

  DashboardStatistics({
    required this.totalOrders,
    required this.todayOrders,
    required this.activeOrders,
    required this.completedOrders,
    required this.totalRevenue,
    required this.todayRevenue,
    required this.totalItems,
    required this.totalTables,
    required this.totalServers,
    required this.totalBusinesses,
    this.recentOrders,
    this.ordersByStatus,
    this.revenueData,
  });

  factory DashboardStatistics.fromJson(Map<String, dynamic> json) {
    return DashboardStatistics(
      totalOrders: json['total_orders'] ?? 0,
      todayOrders: json['today_orders'] ?? json['todayOrders'] ?? 0,
      activeOrders: json['active_orders'] ?? json['activeOrders'] ?? 0,
      completedOrders: json['orders_by_status'][2]['status']=='completed'?json['orders_by_status'][2]['count']:0,
      totalRevenue: _parsePrice(json['total_revenue'] ?? 0),
      todayRevenue: _parsePrice(json['today_revenue'] ?? 0),
      totalItems: json['items']['total'] ?? 0,
      totalTables: json['tables']['total'] ?? 2,
      // totalTables: 2,
      totalServers: json['total_servers'] ?? json['totalServers'] ?? 0,
      totalBusinesses:  json['businesses']['total'] ?? json['totalBusinesses'] ?? 0,
      recentOrders: json['recent_orders'] != null
          ? (json['recent_orders'] as List)
              .map((e) => RecentOrder.fromJson(e))
              .toList()
          : null,
      // recentOrders: json['recent_orders'] ?? null,
      ordersByStatus: json['orders_by_status'] != null
          ? { for (var item in json['orders_by_status'] as List) (item as Map<String, dynamic>)['status'].toString() : item['count'] }
          : null,
      // revenueData: json['revenue_data'] as Map<String, dynamic>?,
    );
  }

  static double _parsePrice(dynamic price) {
    if (price == null) return 0.0;
    if (price is double) return price;
    if (price is int) return price.toDouble();
    if (price is String) return double.tryParse(price) ?? 0.0;
    return 0.0;
  }

  Map<String, dynamic> toJson() {
    return {
      'total_orders': totalOrders,
      'today_orders': todayOrders,
      'active_orders': activeOrders,
      'completed_orders': completedOrders,
      'total_revenue': totalRevenue,
      'today_revenue': todayRevenue,
      'total_items': totalItems,
      'total_tables': totalTables,
      'total_servers': totalServers,
      'total_businesses': totalBusinesses,
      'recent_orders': recentOrders?.map((e) => e.toJson()).toList(),
      'orders_by_status': ordersByStatus,
      'revenue_data': revenueData,
    };
  }
}

class RecentOrder {
  final int id;
  final String orderNumber;
  final String customerName;
  final double total;
  final String status;
  final String createdAt;

  RecentOrder({
    required this.id,
    required this.orderNumber,
    required this.customerName,
    required this.total,
    required this.status,
    required this.createdAt,
  });

  factory RecentOrder.fromJson(Map<String, dynamic> json) {
    return RecentOrder(
      id: json['id'] ?? 0,
      orderNumber: json['order_number'] ?? json['orderNumber'] ?? '',
      customerName: json['customer_name'] ?? json['customerName'] ?? 'Guest',
      total: _parsePrice(json['total']),
      status: json['status'] ?? 'pending',
      createdAt: json['created_at'] ?? json['createdAt'] ?? '',
    );
  }

  static double _parsePrice(dynamic price) {
    if (price == null) return 0.0;
    if (price is double) return price;
    if (price is int) return price.toDouble();
    if (price is String) return double.tryParse(price) ?? 0.0;
    return 0.0;
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'order_number': orderNumber,
      'customer_name': customerName,
      'total': total,
      'status': status,
      'created_at': createdAt,
    };
  }
}
