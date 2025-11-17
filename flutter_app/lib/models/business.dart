class Business {
  final int id;
  final String name;
  final String businessLink;
  final String? description;
  final String? headerImage;
  final String? logoImage;
  final String? businessType;
  final String? address;
  final String? phone;
  final String? email;
  final bool? isActive;
  final String? createdAt;
  final String? updatedAt;

  Business({
    required this.id,
    required this.name,
    required this.businessLink,
    this.description,
    this.headerImage,
    this.logoImage,
    this.businessType,
    this.address,
    this.phone,
    this.email,
    this.isActive,
    this.createdAt,
    this.updatedAt,
  });

  factory Business.fromJson(Map<String, dynamic> json) {
    return Business(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      businessLink: json['business_link'] ?? json['businessLink'] ?? '',
      description: json['description'],
      headerImage: json['header_image'] ?? json['headerImage'],
      logoImage: json['logo_image'] ?? json['logoImage'],
      businessType: json['business_type'] ?? json['businessType'],
      address: json['address'],
      phone: json['phone'],
      email: json['email'],
      isActive: json['is_active'] ?? json['isActive'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'business_link': businessLink,
      'description': description,
      'header_image': headerImage,
      'logo_image': logoImage,
      'business_type': businessType,
      'address': address,
      'phone': phone,
      'email': email,
      'is_active': isActive,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }
}

class BusinessData {
  final int id;
  final String? description;
  final String? address;
  final String? city;
  final String? state;
  final String? country;
  final String? phone;
  final String? email;
  final String? website;
  final String? headerImage;
  final String? logoImage;
  final Map<String, dynamic>? openingHours;
  final String? createdAt;
  final String? updatedAt;

  BusinessData({
    required this.id,
    this.description,
    this.address,
    this.city,
    this.state,
    this.country,
    this.phone,
    this.email,
    this.website,
    this.headerImage,
    this.logoImage,
    this.openingHours,
    this.createdAt,
    this.updatedAt,
  });

  factory BusinessData.fromJson(Map<String, dynamic> json) {
    return BusinessData(
      id: json['id'] ?? 0,
      description: json['description'],
      address: json['address'],
      city: json['city'],
      state: json['state'],
      country: json['country'],
      phone: json['phone'],
      email: json['email'],
      website: json['website'],
      headerImage: json['header_image'] ?? json['headerImage'],
      logoImage: json['logo_image'] ?? json['logoImage'],
      openingHours: json['opening_hours'] as Map<String, dynamic>?,
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'description': description,
      'address': address,
      'city': city,
      'state': state,
      'country': country,
      'phone': phone,
      'email': email,
      'website': website,
      'header_image': headerImage,
      'logo_image': logoImage,
      'opening_hours': openingHours,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }
}
