import 'category.dart';

class Order {
  final int id;
  final String orderNumber;
  final int? customerId;
  final int? businessLinkId;
  final int? tableId;
  final double total;
  final String status;
  final String paymentStatus;
  final String? paymentMethod;
  final String? customerName;
  final String? customerPhone;
  final String? customerEmail;
  final String? notes;
  final String? createdAt;
  final String? updatedAt;
  final List<OrderItem>? items;
  final TableInfo? table;
  final BusinessInfo? business;

  Order({
    required this.id,
    required this.orderNumber,
    this.customerId,
    this.businessLinkId,
    this.tableId,
    required this.total,
    required this.status,
    required this.paymentStatus,
    this.paymentMethod,
    this.customerName,
    this.customerPhone,
    this.customerEmail,
    this.notes,
    this.createdAt,
    this.updatedAt,
    this.items,
    this.table,
    this.business,
  });

  factory Order.fromJson(Map<String, dynamic> json) {

    return Order(
      id: json['id'] ?? 0,
      orderNumber: json['order_number'] ?? json['orderNumber'] ?? '',
      customerId: json['customer_id'] ?? json['customerId'],
      businessLinkId: json['business_link_id'] ?? json['businessLinkId'],
      tableId: json['table_id'] ?? json['tableId'],
      total: _parsePrice(json['total']),
      status: json['status'] ?? 'pending',
      paymentStatus:
          json['payment_status'] ?? json['paymentStatus'] ?? 'pending',
      paymentMethod: json['payment_method'] ?? json['paymentMethod'],
      customerName: json['customer_name'] ?? json['customerName'],
      customerPhone: json['customer_phone'] ?? json['customerPhone'],
      customerEmail: json['customer_email'] ?? json['customerEmail'],
      notes: json['notes'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
      items: json['order_items'] != null
          ? (json['order_items'] as List)
              .map((e) => OrderItem.fromJson(e))
              .toList()
          : null,
      table: json['table'] != null ? TableInfo.fromJson(json['table']) : null,
      business: json['business'] != null
          ? BusinessInfo.fromJson(json['business'])
          : null,
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
      'customer_id': customerId,
      'business_link_id': businessLinkId,
      'table_id': tableId,
      'total': total,
      'status': status,
      'payment_status': paymentStatus,
      'payment_method': paymentMethod,
      'customer_name': customerName,
      'customer_phone': customerPhone,
      'customer_email': customerEmail,
      'notes': notes,
      'created_at': createdAt,
      'updated_at': updatedAt,
      'items': items?.map((e) => e.toJson()).toList(),
      'table': table?.toJson(),
      'business': business?.toJson(),
    };
  }
}

class OrderItem {
  final int id;
  final int orderId;
  final int itemId;
  final String itemName;
  final int quantity;
  final double price;
  final double subtotal;
  final String? notes;
  final MenuItem? item;

  OrderItem({
    required this.id,
    required this.orderId,
    required this.itemId,
    required this.itemName,
    required this.quantity,
    required this.price,
    required this.subtotal,
    this.notes,
    this.item,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    // print(json);

    return OrderItem(
      id: json['id'] ?? 0,
      orderId: json['order_id'] ?? json['orderId'] ?? 0,
      itemId: json['item_id'] ?? json['itemId'] ?? 0,
      itemName:
          json['item_name'] ?? json['itemName'] ?? json['item']['title'] ?? '',
      quantity: json['quantity'] ?? 1,
      price: _parsePrice(json['price']),
      subtotal: _parsePrice(json['subtotal']),
      notes: json['notes'],
      item: json['item'] != null ? MenuItem.fromJson(json['item']) : null,
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
      'order_id': orderId,
      'item_id': itemId,
      'item_name': itemName,
      'quantity': quantity,
      'price': price,
      'subtotal': subtotal,
      'notes': notes,
      'item': item?.toJson(),
    };
  }
}

class TableInfo {
  final int id;
  final String tableNumber;
  final String tableName;
  final String? qrCode;

  TableInfo({
    required this.id,
    required this.tableNumber,
    required this.tableName,
    this.qrCode,
  });

  factory TableInfo.fromJson(Map<String, dynamic> json) {
    print('Drame Start');
    print(json);
    print('Drame End');

    return TableInfo(
      id: json['id'] ?? 0,
      tableNumber: json['table_number'] ?? json['tableNumber'] ?? '',
      tableName: json['table_name'] ?? json['tableName'] ?? '',
      qrCode: json['qr_code'] ?? json['qrCode'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'table_number': tableNumber,
      'table_number': tableNumber,
      'qr_code': qrCode,
    };
  }
}

class BusinessInfo {
  final int id;
  final String name;
  final String? businessLink;

  BusinessInfo({
    required this.id,
    required this.name,
    this.businessLink,
  });

  factory BusinessInfo.fromJson(Map<String, dynamic> json) {
    return BusinessInfo(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      businessLink: json['business_link'] ?? json['businessLink'],
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

class OrderStatistics {
  final int total;
  final int pending;
  final int confirmed;
  final int preparing;
  final int completed;
  final int cancelled;
  final double totalRevenue;
  final double todayRevenue;

  OrderStatistics({
    required this.total,
    required this.pending,
    required this.confirmed,
    required this.preparing,
    required this.completed,
    required this.cancelled,
    required this.totalRevenue,
    required this.todayRevenue,
  });

  factory OrderStatistics.fromJson(Map<String, dynamic> json) {
    return OrderStatistics(
      total: json['total'] ?? 0,
      pending: json['pending'] ?? 0,
      confirmed: json['confirmed'] ?? 0,
      preparing: json['preparing'] ?? 0,
      completed: json['completed'] ?? 0,
      cancelled: json['cancelled'] ?? 0,
      totalRevenue: _parsePrice(json['total_revenue'] ?? json['totalRevenue']),
      todayRevenue: _parsePrice(json['today_revenue'] ?? json['todayRevenue']),
    );
  }

  static double _parsePrice(dynamic price) {
    if (price == null) return 0.0;
    if (price is double) return price;
    if (price is int) return price.toDouble();
    if (price is String) return double.tryParse(price) ?? 0.0;
    return 0.0;
  }
}
