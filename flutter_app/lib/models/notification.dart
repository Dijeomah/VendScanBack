class AppNotification {
  final String id;
  final String type;
  final String? notifiableType;
  final int? notifiableId;
  final Map<String, dynamic> data;
  final String? readAt;
  final String? createdAt;
  final String? updatedAt;

  AppNotification({
    required this.id,
    required this.type,
    this.notifiableType,
    this.notifiableId,
    required this.data,
    this.readAt,
    this.createdAt,
    this.updatedAt,
  });

  factory AppNotification.fromJson(Map<String, dynamic> json) {
    return AppNotification(
      id: json['id'] ?? '',
      type: json['type'] ?? '',
      notifiableType: json['notifiable_type'] ?? json['notifiableType'],
      notifiableId: json['notifiable_id'] ?? json['notifiableId'],
      data: json['data'] ?? {},
      readAt: json['read_at'] ?? json['readAt'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'type': type,
      'notifiable_type': notifiableType,
      'notifiable_id': notifiableId,
      'data': data,
      'read_at': readAt,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  bool get isRead => readAt != null;

  String get title => data['title'] ?? 'Notification';
  String get message => data['message'] ?? '';
  String? get actionUrl => data['action_url'] ?? data['actionUrl'];
  int? get orderId => data['order_id'] ?? data['orderId'];
  String? get orderNumber => data['order_number'] ?? data['orderNumber'];
}

class NotificationCount {
  final int total;
  final int unread;

  NotificationCount({
    required this.total,
    required this.unread,
  });

  factory NotificationCount.fromJson(Map<String, dynamic> json) {
    return NotificationCount(
      total: json['total'] ?? 0,
      unread: json['unread'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'total': total,
      'unread': unread,
    };
  }
}
