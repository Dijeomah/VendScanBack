class Category {
  final int id;
  final String name;
  final String? description;
  final int? businessLinkId;
  final int? order;
  final bool? isActive;
  final String? createdAt;
  final String? updatedAt;
  final List<SubCategory>? subCategories;
  final List<MenuItem>? items;

  Category({
    required this.id,
    required this.name,
    this.description,
    this.businessLinkId,
    this.order,
    this.isActive,
    this.createdAt,
    this.updatedAt,
    this.subCategories,
    this.items,
  });

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'],
      businessLinkId: json['business_link_id'] ?? json['businessLinkId'],
      order: json['order'],
      isActive: json['is_active'] ?? json['isActive'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
      subCategories: json['sub_categories'] != null
          ? (json['sub_categories'] as List)
              .map((e) => SubCategory.fromJson(e))
              .toList()
          : null,
      items: json['items'] != null
          ? (json['items'] as List).map((e) => MenuItem.fromJson(e)).toList()
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'business_link_id': businessLinkId,
      'order': order,
      'is_active': isActive,
      'created_at': createdAt,
      'updated_at': updatedAt,
      'sub_categories':
          subCategories?.map((e) => e.toJson()).toList(),
      'items': items?.map((e) => e.toJson()).toList(),
    };
  }
}

class SubCategory {
  final int id;
  final String name;
  final String? description;
  final int categoryId;
  final int? order;
  final bool? isActive;
  final String? createdAt;
  final String? updatedAt;

  SubCategory({
    required this.id,
    required this.name,
    this.description,
    required this.categoryId,
    this.order,
    this.isActive,
    this.createdAt,
    this.updatedAt,
  });

  factory SubCategory.fromJson(Map<String, dynamic> json) {
    return SubCategory(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'],
      categoryId: json['category_id'] ?? json['categoryId'] ?? 0,
      order: json['order'],
      isActive: json['is_active'] ?? json['isActive'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'category_id': categoryId,
      'order': order,
      'is_active': isActive,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }
}

class MenuItem {
  final int id;
  final String name;
  final String? description;
  final double price;
  final String? image;
  final int? categoryId;
  final int? subCategoryId;
  final int? businessLinkId;
  final bool? isAvailable;
  final bool? isActive;
  final String? createdAt;
  final String? updatedAt;

  MenuItem({
    required this.id,
    required this.name,
    this.description,
    required this.price,
    this.image,
    this.categoryId,
    this.subCategoryId,
    this.businessLinkId,
    this.isAvailable,
    this.isActive,
    this.createdAt,
    this.updatedAt,
  });

  factory MenuItem.fromJson(Map<String, dynamic> json) {
    return MenuItem(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      description: json['description'],
      price: _parsePrice(json['price']),
      image: json['image'],
      categoryId: json['category_id'] ?? json['categoryId'],
      subCategoryId: json['sub_category_id'] ?? json['subCategoryId'],
      businessLinkId: json['business_link_id'] ?? json['businessLinkId'],
      isAvailable: json['is_available'] ?? json['isAvailable'],
      isActive: json['is_active'] ?? json['isActive'],
      createdAt: json['created_at'] ?? json['createdAt'],
      updatedAt: json['updated_at'] ?? json['updatedAt'],
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
      'name': name,
      'description': description,
      'price': price,
      'image': image,
      'category_id': categoryId,
      'sub_category_id': subCategoryId,
      'business_link_id': businessLinkId,
      'is_available': isAvailable,
      'is_active': isActive,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }
}
