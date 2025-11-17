class Server {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String role;
  final String? status;
  final int? createdBy;
  final String? createdAt;
  final String? updatedAt;
  final List<BusinessAssignment>? businesses;
  final List<TableAssignment>? tables;
  final ServerStatistics? statistics;

  Server({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.role,
    this.status,
    this.createdBy,
    this.createdAt,
    this.updatedAt,
    this.businesses,
    this.tables,
    this.statistics,
  });

  factory Server.fromJson(Map<String, dynamic> json) {
    return Server(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'],
      role: json['role'] ?? 'server',
      status: json['status'],
      createdBy: json['created_by'] ?? json['createdBy'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
      businesses: json['businesses'] != null
          ? (json['businesses'] as List)
              .map((e) => BusinessAssignment.fromJson(e))
              .toList()
          : null,
      tables: json['tables'] != null
          ? (json['tables'] as List)
              .map((e) => TableAssignment.fromJson(e))
              .toList()
          : null,
      statistics: json['statistics'] != null
          ? ServerStatistics.fromJson(json['statistics'])
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'role': role,
      'status': status,
      'created_by': createdBy,
      'created_at': createdAt,
      'updated_at': updatedAt,
      'businesses': businesses?.map((e) => e.toJson()).toList(),
      'tables': tables?.map((e) => e.toJson()).toList(),
      'statistics': statistics?.toJson(),
    };
  }
}

class BusinessAssignment {
  final int id;
  final String name;
  final String businessLink;

  BusinessAssignment({
    required this.id,
    required this.name,
    required this.businessLink,
  });

  factory BusinessAssignment.fromJson(Map<String, dynamic> json) {
    return BusinessAssignment(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      businessLink: json['business_link'] ?? json['businessLink'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'business_link': businessLink,
    };
  }
}

class TableAssignment {
  final int id;
  final String tableNumber;
  final int businessLinkId;
  final String? businessName;

  TableAssignment({
    required this.id,
    required this.tableNumber,
    required this.businessLinkId,
    this.businessName,
  });

  factory TableAssignment.fromJson(Map<String, dynamic> json) {
    return TableAssignment(
      id: json['id'] ?? 0,
      tableNumber: json['table_number'] ?? json['tableNumber'] ?? '',
      businessLinkId: json['business_link_id'] ?? json['businessLinkId'] ?? 0,
      businessName: json['business_name'] ?? json['businessName'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'table_number': tableNumber,
      'business_link_id': businessLinkId,
      'business_name': businessName,
    };
  }
}

class ServerStatistics {
  final int totalOrders;
  final int todayOrders;
  final int assignedTables;
  final int assignedBusinesses;

  ServerStatistics({
    required this.totalOrders,
    required this.todayOrders,
    required this.assignedTables,
    required this.assignedBusinesses,
  });

  factory ServerStatistics.fromJson(Map<String, dynamic> json) {
    return ServerStatistics(
      totalOrders: json['total_orders'] ?? json['totalOrders'] ?? 0,
      todayOrders: json['today_orders'] ?? json['todayOrders'] ?? 0,
      assignedTables: json['assigned_tables'] ?? json['assignedTables'] ?? 0,
      assignedBusinesses:
          json['assigned_businesses'] ?? json['assignedBusinesses'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'total_orders': totalOrders,
      'today_orders': todayOrders,
      'assigned_tables': assignedTables,
      'assigned_businesses': assignedBusinesses,
    };
  }
}
