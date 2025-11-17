import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

import '../../models/category.dart';
import '../../providers/vendor_provider.dart';
import '../../services/vendor_service.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';

class MenuItemFormScreen extends StatefulWidget {
  final MenuItem? item;

  const MenuItemFormScreen({super.key, this.item});

  @override
  State<MenuItemFormScreen> createState() => _MenuItemFormScreenState();
}

class _MenuItemFormScreenState extends State<MenuItemFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _priceController = TextEditingController();

  int? _selectedCategoryId;
  int? _selectedSubCategoryId;
  File? _imageFile;
  bool _isLoading = false;

  bool get isEditing => widget.item != null;

  @override
  void initState() {
    super.initState();
    _loadCategories();

    if (isEditing) {
      _nameController.text = widget.item!.name;
      _descriptionController.text = widget.item!.description ?? '';
      _priceController.text = widget.item!.price.toString();
      _selectedCategoryId = widget.item!.categoryId;
      _selectedSubCategoryId = widget.item!.subCategoryId;
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _descriptionController.dispose();
    _priceController.dispose();
    super.dispose();
  }

  Future<void> _loadCategories() async {
    await context.read<VendorProvider>().loadCategories();
  }

  Future<void> _pickImage() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(
      source: ImageSource.gallery,
      maxWidth: 1024,
      maxHeight: 1024,
      imageQuality: 85,
    );

    if (pickedFile != null) {
      setState(() {
        _imageFile = File(pickedFile.path);
      });
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    final vendorProvider = context.read<VendorProvider>();
    if (vendorProvider.selectedBusiness == null) {
      Helpers.showToast('Please select a business first', isError: true);
      return;
    }

    setState(() => _isLoading = true);

    final vendorService = VendorService();
    final name = _nameController.text.trim();
    final description = _descriptionController.text.trim();
    final price = double.parse(_priceController.text.trim());

    try {
      final response = isEditing
          ? await vendorService.updateMenuItem(
              id: widget.item!.id,
              name: name,
              description: description.isNotEmpty ? description : null,
              price: price,
              categoryId: _selectedCategoryId,
              subCategoryId: _selectedSubCategoryId,
              image: _imageFile,
            )
          : await vendorService.createMenuItem(
              name: name,
              price: price,
              businessLinkId: vendorProvider.selectedBusiness!.id,
              description: description.isNotEmpty ? description : null,
              categoryId: _selectedCategoryId,
              subCategoryId: _selectedSubCategoryId,
              image: _imageFile,
            );

      if (!mounted) return;

      if (response.success) {
        Helpers.showToast(
          isEditing ? 'Item updated successfully' : 'Item created successfully',
        );
        Navigator.pop(context, true);
      } else {
        Helpers.showToast(response.message, isError: true);
      }
    } catch (e) {
      if (!mounted) return;
      Helpers.showToast('An error occurred: $e', isError: true);
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(isEditing ? 'Edit Menu Item' : 'Add Menu Item'),
      ),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            // Image Picker
            GestureDetector(
              onTap: _pickImage,
              child: Container(
                height: 200,
                decoration: BoxDecoration(
                  color: AppTheme.backgroundColor,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.borderColor),
                ),
                child: _buildImagePreview(),
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Tap to select image',
              style: AppTheme.bodySmall,
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),

            // Name Field
            TextFormField(
              controller: _nameController,
              decoration: const InputDecoration(
                labelText: 'Item Name *',
                hintText: 'e.g., Margherita Pizza',
              ),
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'Please enter item name';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),

            // Description Field
            TextFormField(
              controller: _descriptionController,
              decoration: const InputDecoration(
                labelText: 'Description',
                hintText: 'Describe your item',
              ),
              maxLines: 3,
            ),
            const SizedBox(height: 16),

            // Price Field
            TextFormField(
              controller: _priceController,
              decoration: const InputDecoration(
                labelText: 'Price *',
                hintText: '0.00',
                prefixText: '\$ ',
              ),
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'Please enter price';
                }
                if (double.tryParse(value) == null) {
                  return 'Please enter a valid price';
                }
                if (double.parse(value) <= 0) {
                  return 'Price must be greater than 0';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),

            // Category Dropdown
            Consumer<VendorProvider>(
              builder: (context, provider, _) {
                return DropdownButtonFormField<int>(
                  value: _selectedCategoryId,
                  decoration: const InputDecoration(
                    labelText: 'Category',
                    hintText: 'Select a category',
                  ),
                  items: provider.categories.map((category) {
                    return DropdownMenuItem(
                      value: category.id,
                      child: Text(category.name),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() {
                      _selectedCategoryId = value;
                      _selectedSubCategoryId = null; // Reset subcategory
                    });
                  },
                );
              },
            ),
            const SizedBox(height: 16),

            // Subcategory Dropdown (if category selected)
            if (_selectedCategoryId != null)
              Consumer<VendorProvider>(
                builder: (context, provider, _) {
                  final category = provider.categories
                      .firstWhere((c) => c.id == _selectedCategoryId);
                  final subCategories = category.subCategories ?? [];

                  if (subCategories.isEmpty) {
                    return const SizedBox.shrink();
                  }

                  return Column(
                    children: [
                      DropdownButtonFormField<int>(
                        value: _selectedSubCategoryId,
                        decoration: const InputDecoration(
                          labelText: 'Subcategory',
                          hintText: 'Select a subcategory',
                        ),
                        items: subCategories.map((subCategory) {
                          return DropdownMenuItem(
                            value: subCategory.id,
                            child: Text(subCategory.name),
                          );
                        }).toList(),
                        onChanged: (value) {
                          setState(() {
                            _selectedSubCategoryId = value;
                          });
                        },
                      ),
                      const SizedBox(height: 16),
                    ],
                  );
                },
              ),

            const SizedBox(height: 24),

            // Submit Button
            ElevatedButton(
              onPressed: _isLoading ? null : _submit,
              child: _isLoading
                  ? const SizedBox(
                      height: 20,
                      width: 20,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                      ),
                    )
                  : Text(isEditing ? 'Update Item' : 'Create Item'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildImagePreview() {
    if (_imageFile != null) {
      return ClipRRect(
        borderRadius: BorderRadius.circular(12),
        child: Image.file(
          _imageFile!,
          fit: BoxFit.cover,
        ),
      );
    } else if (isEditing && widget.item!.image != null) {
      return ClipRRect(
        borderRadius: BorderRadius.circular(12),
        child: Image.network(
          widget.item!.image!,
          fit: BoxFit.cover,
          errorBuilder: (context, error, stackTrace) {
            return _buildPlaceholder();
          },
        ),
      );
    } else {
      return _buildPlaceholder();
    }
  }

  Widget _buildPlaceholder() {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        const Icon(
          Icons.add_photo_alternate_outlined,
          size: 48,
          color: AppTheme.textSecondaryColor,
        ),
        const SizedBox(height: 8),
        Text(
          'Add Photo',
          style: AppTheme.bodyMedium.copyWith(
            color: AppTheme.textSecondaryColor,
          ),
        ),
      ],
    );
  }
}
