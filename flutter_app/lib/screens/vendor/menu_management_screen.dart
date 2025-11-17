import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

import '../../models/category.dart';
import '../../providers/vendor_provider.dart';
import '../../services/vendor_service.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';
import '../../widgets/loading_overlay.dart';
import 'menu_item_form_screen.dart';

class MenuManagementScreen extends StatefulWidget {
  const MenuManagementScreen({super.key});

  @override
  State<MenuManagementScreen> createState() => _MenuManagementScreenState();
}

class _MenuManagementScreenState extends State<MenuManagementScreen> {
  final _searchController = TextEditingController();
  String _searchQuery = '';
  int? _selectedCategoryId;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    final vendorProvider = context.read<VendorProvider>();
    await Future.wait([
      vendorProvider.loadMenuItems(),
      vendorProvider.loadCategories(),
    ]);
  }

  List<MenuItem> _getFilteredItems(List<MenuItem> items) {
    var filtered = items;

    // Filter by search query
    if (_searchQuery.isNotEmpty) {
      filtered = filtered.where((item) {
        return item.name.toLowerCase().contains(_searchQuery.toLowerCase()) ||
            (item.description?.toLowerCase().contains(_searchQuery.toLowerCase()) ?? false);
      }).toList();
    }

    // Filter by category
    if (_selectedCategoryId != null) {
      filtered = filtered.where((item) => item.categoryId == _selectedCategoryId).toList();
    }

    return filtered;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Menu Management'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
          ),
        ],
      ),
      body: Column(
        children: [
          // Search Bar
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Search menu items...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchQuery.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          setState(() {
                            _searchController.clear();
                            _searchQuery = '';
                          });
                        },
                      )
                    : null,
              ),
              onChanged: (value) {
                setState(() {
                  _searchQuery = value;
                });
              },
            ),
          ),

          // Category Filter Chips
          Consumer<VendorProvider>(
            builder: (context, provider, _) {
              if (provider.categories.isEmpty) return const SizedBox.shrink();

              return SizedBox(
                height: 50,
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  children: [
                    Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: FilterChip(
                        label: const Text('All'),
                        selected: _selectedCategoryId == null,
                        onSelected: (selected) {
                          setState(() {
                            _selectedCategoryId = null;
                          });
                        },
                      ),
                    ),
                    ...provider.categories.map((category) {
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: FilterChip(
                          label: Text(category.name),
                          selected: _selectedCategoryId == category.id,
                          onSelected: (selected) {
                            setState(() {
                              _selectedCategoryId = selected ? category.id : null;
                            });
                          },
                        ),
                      );
                    }).toList(),
                  ],
                ),
              );
            },
          ),

          // Menu Items Grid
          Expanded(
            child: Consumer<VendorProvider>(
              builder: (context, provider, _) {
                if (provider.isLoadingMenuItems) {
                  return const Center(child: CircularProgressIndicator());
                }

                final filteredItems = _getFilteredItems(provider.menuItems);

                if (filteredItems.isEmpty) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.restaurant_menu,
                          size: 64,
                          color: AppTheme.textSecondaryColor,
                        ),
                        const SizedBox(height: 16),
                        Text(
                          _searchQuery.isNotEmpty || _selectedCategoryId != null
                              ? 'No items found'
                              : 'No menu items yet',
                          style: AppTheme.headlineSmall,
                        ),
                        const SizedBox(height: 8),
                        Text(
                          _searchQuery.isNotEmpty || _selectedCategoryId != null
                              ? 'Try adjusting your filters'
                              : 'Add your first menu item',
                          style: AppTheme.bodyMedium.copyWith(
                            color: AppTheme.textSecondaryColor,
                          ),
                        ),
                      ],
                    ),
                  );
                }

                return RefreshIndicator(
                  onRefresh: _loadData,
                  child: GridView.builder(
                    padding: const EdgeInsets.all(16),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      crossAxisSpacing: 16,
                      mainAxisSpacing: 16,
                      childAspectRatio: 0.75,
                    ),
                    itemCount: filteredItems.length,
                    itemBuilder: (context, index) {
                      final item = filteredItems[index];
                      return _MenuItemCard(
                        item: item,
                        onTap: () => _navigateToEdit(item),
                        onDelete: () => _deleteItem(item),
                        onToggleAvailability: () => _toggleAvailability(item),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _navigateToCreate,
        icon: const Icon(Icons.add),
        label: const Text('Add Item'),
      ),
    );
  }

  void _showFilterDialog() {
    // Additional filter options can be added here
    Helpers.showToast('Filter options');
  }

  void _navigateToCreate() async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => const MenuItemFormScreen(),
      ),
    );

    if (result == true) {
      _loadData();
    }
  }

  void _navigateToEdit(MenuItem item) async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => MenuItemFormScreen(item: item),
      ),
    );

    if (result == true) {
      _loadData();
    }
  }

  Future<void> _toggleAvailability(MenuItem item) async {
    final vendorService = VendorService();
    final newStatus = !(item.isAvailable ?? true);

    final response = await vendorService.updateMenuItem(
      id: item.id,
      isAvailable: newStatus,
    );

    if (response.success) {
      Helpers.showToast('Item ${newStatus ? 'enabled' : 'disabled'}');
      _loadData();
    } else {
      Helpers.showToast(response.message, isError: true);
    }
  }

  Future<void> _deleteItem(MenuItem item) async {
    final confirmed = await Helpers.showConfirmDialog(
      context,
      title: 'Delete Item',
      message: 'Are you sure you want to delete "${item.name}"?',
      confirmText: 'Delete',
    );

    if (!confirmed) return;

    final provider = context.read<VendorProvider>();
    final success = await provider.deleteMenuItem(item.id);

    if (success) {
      Helpers.showToast('Item deleted successfully');
    } else {
      Helpers.showToast('Failed to delete item', isError: true);
    }
  }
}

class _MenuItemCard extends StatelessWidget {
  final MenuItem item;
  final VoidCallback onTap;
  final VoidCallback onDelete;
  final VoidCallback onToggleAvailability;

  const _MenuItemCard({
    required this.item,
    required this.onTap,
    required this.onDelete,
    required this.onToggleAvailability,
  });

  @override
  Widget build(BuildContext context) {
    final isAvailable = item.isAvailable ?? true;

    return Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            Expanded(
              child: Stack(
                fit: StackFit.expand,
                children: [
                  if (item.image != null)
                    Image.network(
                      item.image!,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return _buildPlaceholder();
                      },
                    )
                  else
                    _buildPlaceholder(),

                  // Availability Badge
                  Positioned(
                    top: 8,
                    right: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: isAvailable
                            ? AppTheme.successColor
                            : AppTheme.errorColor,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        isAvailable ? 'Available' : 'Unavailable',
                        style: AppTheme.bodySmall.copyWith(
                          color: Colors.white,
                          fontSize: 10,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Details
            Padding(
              padding: const EdgeInsets.all(8.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    item.name,
                    style: AppTheme.titleMedium,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Text(
                    Helpers.formatCurrency(item.price),
                    style: AppTheme.bodyLarge.copyWith(
                      color: AppTheme.primaryColor,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 8),

                  // Actions
                  Row(
                    children: [
                      Expanded(
                        child: IconButton(
                          icon: Icon(
                            isAvailable
                                ? Icons.visibility_off_outlined
                                : Icons.visibility_outlined,
                            size: 20,
                          ),
                          onPressed: onToggleAvailability,
                          tooltip: isAvailable ? 'Disable' : 'Enable',
                        ),
                      ),
                      Expanded(
                        child: IconButton(
                          icon: const Icon(
                            Icons.edit_outlined,
                            size: 20,
                          ),
                          onPressed: onTap,
                          tooltip: 'Edit',
                        ),
                      ),
                      Expanded(
                        child: IconButton(
                          icon: const Icon(
                            Icons.delete_outline,
                            size: 20,
                            color: AppTheme.errorColor,
                          ),
                          onPressed: onDelete,
                          tooltip: 'Delete',
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPlaceholder() {
    return Container(
      color: AppTheme.backgroundColor,
      child: const Icon(
        Icons.restaurant,
        size: 48,
        color: AppTheme.textSecondaryColor,
      ),
    );
  }
}
