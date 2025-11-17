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
    // Handle both 'name' and 'first_name/last_name' formats
    String name;
    if (json.containsKey('name') && json['name'] != null) {
      name = json['name'];
    } else {
      final firstName = json['first_name'] ?? '';
      final lastName = json['last_name'] ?? '';
      name = '$firstName $lastName'.trim();
    }

    // Handle both 'phone' and 'phone_number' formats
    final phone = json['phone'] ?? json['phone_number'];

    return User(
      id: json['id'] ?? 0,
      name: name.isNotEmpty ? name : 'User',
      email: json['email'] ?? '',
      phone: phone,
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
