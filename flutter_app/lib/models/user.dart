class User {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String role;
  final String? status;
  final String? createdAt;
  final String? updatedAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.role,
    this.status,
    this.createdAt,
    this.updatedAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'],
      role: json['role'] ?? '',
      status: json['status'],
      createdAt: json['created_at'],
      updatedAt: json['updated_at'],
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
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }

  bool get isVendor => role.toLowerCase() == 'vendor';
  bool get isServer => role.toLowerCase() == 'server';
  bool get isAdmin => role.toLowerCase() == 'admin';
}
