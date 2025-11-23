class RestaurantTable {
  final int id;
  final int businessLinkId;
  final String tableNumber;
  final String? qrCode;
  final String? qrCodePath;
  final int? capacity;
  final String? status;
  final String? location;
  final bool? isActive;
  final String? createdAt;
  final String? updatedAt;
  final List<ServerAssignment>? assignments;

  RestaurantTable({
    required this.id,
    required this.businessLinkId,
    required this.tableNumber,
    this.qrCode,
    this.qrCodePath,
    this.capacity,
    this.status,
    this.location,
    this.isActive,
    this.createdAt,
    this.updatedAt,
    this.assignments,
  });

  factory RestaurantTable.fromJson(Map<String, dynamic> json) {
    return RestaurantTable(
      id: json['id'] ?? 0,
      businessLinkId: json['business_link_id'] ?? json['businessLinkId'] ?? 0,
      tableNumber: json['table_number'] ?? json['tableNumber'] ?? '',
      qrCode: json['qr_code'] ?? json['qrCode'],
      qrCodePath: json['qr_code_path'] ?? json['qrCodePath'],
      capacity: json['capacity'],
      status: json['status'],
      location: json['location'],
      isActive: json['is_active'] ?? json['isActive'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
      assignments: json['assignments'] != null
          ? (json['assignments'] as List)
              .map((e) => ServerAssignment.fromJson(e))
              .toList()
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'business_link_id': businessLinkId,
      'table_number': tableNumber,
      'qr_code': qrCode,
      'qr_code_path': qrCodePath,
      'capacity': capacity,
      'status': status,
      'location': location,
      'is_active': isActive,
      'created_at': createdAt,
      'updated_at': updatedAt,
      'assignments': assignments?.map((e) => e.toJson()).toList(),
    };
  }
}

class ServerAssignment {
  final int id;
  final int serverId;
  final int businessLinkId;
  final int tableId;
  final String? status;
  final String? assignedAt;
  final String? createdAt;
  final String? updatedAt;
  final ServerInfo? server;
  final TableInfo? table;

  ServerAssignment({
    required this.id,
    required this.serverId,
    required this.businessLinkId,
    required this.tableId,
    this.status,
    this.assignedAt,
    this.createdAt,
    this.updatedAt,
    this.server,
    this.table,
  });

  factory ServerAssignment.fromJson(Map<String, dynamic> json) {
    return ServerAssignment(
      id: json['id'] ?? 0,
      serverId: json['server_id'] ?? json['serverId'] ?? 0,
      businessLinkId: json['business_link_id'] ?? json['businessLinkId'] ?? 0,
      tableId: json['table_id'] ?? json['tableId'] ?? 0,
      status: json['status'],
      assignedAt: json['assigned_at'] ?? json['assignedAt'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
      server: json['server'] != null ? ServerInfo.fromJson(json['server']) : null,
      table: json['table'] != null ? TableInfo.fromJson(json['table']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'server_id': serverId,
      'business_link_id': businessLinkId,
      'table_id': tableId,
      'status': status,
      'assigned_at': assignedAt,
      'created_at': createdAt,
      'updated_at': updatedAt,
      'server': server?.toJson(),
      'table': table?.toJson(),
    };
  }
}

class ServerInfo {
  final int id;
  final String name;
  final String? email;
  final String? phone;

  ServerInfo({
    required this.id,
    required this.name,
    this.email,
    this.phone,
  });

  factory ServerInfo.fromJson(Map<String, dynamic> json) {
    return ServerInfo(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'],
      phone: json['phone'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
    };
  }
}

class TableInfo {
  final int id;
  final String tableNumber;

  TableInfo({
    required this.id,
    required this.tableNumber,
  });

  factory TableInfo.fromJson(Map<String, dynamic> json) {
    return TableInfo(
      id: json['id'] ?? 0,
      tableNumber: json['table_number'] ?? json['tableNumber'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'table_number': tableNumber,
    };
  }
}
