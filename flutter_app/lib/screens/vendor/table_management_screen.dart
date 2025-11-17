import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:qr_flutter/qr_flutter.dart';

import '../../models/table.dart';
import '../../providers/vendor_provider.dart';
import '../../services/vendor_service.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';

class TableManagementScreen extends StatefulWidget {
  const TableManagementScreen({super.key});

  @override
  State<TableManagementScreen> createState() => _TableManagementScreenState();
}

class _TableManagementScreenState extends State<TableManagementScreen> {
  @override
  void initState() {
    super.initState();
    _loadTables();
  }

  Future<void> _loadTables() async {
    final vendorProvider = context.read<VendorProvider>();
    if (vendorProvider.selectedBusiness != null) {
      await vendorProvider.loadTables(vendorProvider.selectedBusiness!.id);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Table Management'),
        actions: [
          IconButton(
            icon: const Icon(Icons.add_box_outlined),
            onPressed: _showBulkCreateDialog,
            tooltip: 'Bulk Create Tables',
          ),
        ],
      ),
      body: Consumer<VendorProvider>(
        builder: (context, provider, _) {
          if (provider.isLoadingTables) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.selectedBusiness == null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.business_outlined,
                    size: 64,
                    color: AppTheme.textSecondaryColor,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'No Business Selected',
                    style: AppTheme.headlineSmall,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Please select a business first',
                    style: AppTheme.bodyMedium.copyWith(
                      color: AppTheme.textSecondaryColor,
                    ),
                  ),
                ],
              ),
            );
          }

          if (provider.tables.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.table_bar,
                    size: 64,
                    color: AppTheme.textSecondaryColor,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'No Tables Yet',
                    style: AppTheme.headlineSmall,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Create your first table',
                    style: AppTheme.bodyMedium.copyWith(
                      color: AppTheme.textSecondaryColor,
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton.icon(
                    onPressed: _showCreateDialog,
                    icon: const Icon(Icons.add),
                    label: const Text('Create Table'),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: _loadTables,
            child: GridView.builder(
              padding: const EdgeInsets.all(16),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
                childAspectRatio: 1,
              ),
              itemCount: provider.tables.length,
              itemBuilder: (context, index) {
                final table = provider.tables[index];
                return _TableCard(
                  table: table,
                  onTap: () => _showTableDetails(table),
                  onDelete: () => _deleteTable(table),
                );
              },
            ),
          );
        },
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showCreateDialog,
        icon: const Icon(Icons.add),
        label: const Text('Add Table'),
      ),
    );
  }

  void _showCreateDialog() {
    final tableNumberController = TextEditingController();
    final capacityController = TextEditingController();
    final locationController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Create Table'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: tableNumberController,
                decoration: const InputDecoration(
                  labelText: 'Table Number *',
                  hintText: 'e.g., 1, A1, VIP-1',
                ),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: capacityController,
                decoration: const InputDecoration(
                  labelText: 'Capacity',
                  hintText: 'Number of seats',
                ),
                keyboardType: TextInputType.number,
              ),
              const SizedBox(height: 16),
              TextField(
                controller: locationController,
                decoration: const InputDecoration(
                  labelText: 'Location',
                  hintText: 'e.g., Ground Floor, Patio',
                ),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              if (tableNumberController.text.trim().isEmpty) {
                Helpers.showToast('Please enter table number', isError: true);
                return;
              }

              Navigator.pop(context);
              await _createTable(
                tableNumberController.text.trim(),
                capacityController.text.trim().isNotEmpty
                    ? int.tryParse(capacityController.text.trim())
                    : null,
                locationController.text.trim().isNotEmpty
                    ? locationController.text.trim()
                    : null,
              );
            },
            child: const Text('Create'),
          ),
        ],
      ),
    );
  }

  void _showBulkCreateDialog() {
    final countController = TextEditingController();
    final prefixController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Bulk Create Tables'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: countController,
              decoration: const InputDecoration(
                labelText: 'Number of Tables *',
                hintText: 'e.g., 10',
              ),
              keyboardType: TextInputType.number,
            ),
            const SizedBox(height: 16),
            TextField(
              controller: prefixController,
              decoration: const InputDecoration(
                labelText: 'Prefix (Optional)',
                hintText: 'e.g., T- (will create T-1, T-2, ...)',
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              if (countController.text.trim().isEmpty) {
                Helpers.showToast('Please enter count', isError: true);
                return;
              }

              final count = int.tryParse(countController.text.trim());
              if (count == null || count <= 0) {
                Helpers.showToast('Please enter a valid count', isError: true);
                return;
              }

              Navigator.pop(context);
              await _bulkCreateTables(
                count,
                prefixController.text.trim().isNotEmpty
                    ? prefixController.text.trim()
                    : null,
              );
            },
            child: const Text('Create'),
          ),
        ],
      ),
    );
  }

  Future<void> _createTable(String tableNumber, int? capacity, String? location) async {
    final provider = context.read<VendorProvider>();
    if (provider.selectedBusiness == null) return;

    final vendorService = VendorService();
    final response = await vendorService.createTable(
      businessId: provider.selectedBusiness!.id,
      tableNumber: tableNumber,
      capacity: capacity,
      location: location,
    );

    if (response.success) {
      Helpers.showToast('Table created successfully');
      await _loadTables();
    } else {
      Helpers.showToast(response.message, isError: true);
    }
  }

  Future<void> _bulkCreateTables(int count, String? prefix) async {
    final provider = context.read<VendorProvider>();
    if (provider.selectedBusiness == null) return;

    final vendorService = VendorService();
    final response = await vendorService.bulkCreateTables(
      businessId: provider.selectedBusiness!.id,
      count: count,
      prefix: prefix,
    );

    if (response.success) {
      Helpers.showToast('$count tables created successfully');
      await _loadTables();
    } else {
      Helpers.showToast(response.message, isError: true);
    }
  }

  Future<void> _deleteTable(RestaurantTable table) async {
    final confirmed = await Helpers.showConfirmDialog(
      context,
      title: 'Delete Table',
      message: 'Are you sure you want to delete Table ${table.tableNumber}?',
      confirmText: 'Delete',
    );

    if (!confirmed) return;

    final provider = context.read<VendorProvider>();
    if (provider.selectedBusiness == null) return;

    final vendorService = VendorService();
    final response = await vendorService.deleteTable(
      businessId: provider.selectedBusiness!.id,
      tableId: table.id,
    );

    if (response.success) {
      Helpers.showToast('Table deleted successfully');
      await _loadTables();
    } else {
      Helpers.showToast(response.message, isError: true);
    }
  }

  void _showTableDetails(RestaurantTable table) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => _TableDetailsSheet(table: table),
    );
  }
}

class _TableCard extends StatelessWidget {
  final RestaurantTable table;
  final VoidCallback onTap;
  final VoidCallback onDelete;

  const _TableCard({
    required this.table,
    required this.onTap,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(
                      'Table ${table.tableNumber}',
                      style: AppTheme.titleLarge,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  IconButton(
                    icon: const Icon(
                      Icons.delete_outline,
                      color: AppTheme.errorColor,
                      size: 20,
                    ),
                    onPressed: onDelete,
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                  ),
                ],
              ),
              if (table.capacity != null) ...[
                Row(
                  children: [
                    const Icon(
                      Icons.people_outline,
                      size: 16,
                      color: AppTheme.textSecondaryColor,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      '${table.capacity} seats',
                      style: AppTheme.bodySmall,
                    ),
                  ],
                ),
              ],
              if (table.location != null) ...[
                const SizedBox(height: 4),
                Row(
                  children: [
                    const Icon(
                      Icons.location_on_outlined,
                      size: 16,
                      color: AppTheme.textSecondaryColor,
                    ),
                    const SizedBox(width: 4),
                    Expanded(
                      child: Text(
                        table.location!,
                        style: AppTheme.bodySmall,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              ],
              const Spacer(),
              ElevatedButton.icon(
                onPressed: onTap,
                icon: const Icon(Icons.qr_code, size: 16),
                label: const Text('View QR'),
                style: ElevatedButton.styleFrom(
                  minimumSize: const Size(double.infinity, 36),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _TableDetailsSheet extends StatelessWidget {
  final RestaurantTable table;

  const _TableDetailsSheet({required this.table});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              // Handle
              Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: AppTheme.borderColor,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              const SizedBox(height: 24),

              // Title
              Text(
                'Table ${table.tableNumber}',
                style: AppTheme.headlineMedium,
              ),
              const SizedBox(height: 24),

              // QR Code
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.borderColor),
                ),
                child: table.qrCode != null
                    ? QrImageView(
                        data: table.qrCode!,
                        version: QrVersions.auto,
                        size: 250,
                      )
                    : const SizedBox(
                        width: 250,
                        height: 250,
                        child: Center(
                          child: Text('No QR Code'),
                        ),
                      ),
              ),
              const SizedBox(height: 24),

              // Table Info
              if (table.capacity != null ||
                  table.location != null) ...[
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        if (table.capacity != null)
                          Row(
                            children: [
                              const Icon(Icons.people_outline),
                              const SizedBox(width: 12),
                              Text(
                                'Capacity: ${table.capacity} seats',
                                style: AppTheme.bodyLarge,
                              ),
                            ],
                          ),
                        if (table.capacity != null && table.location != null)
                          const Divider(height: 24),
                        if (table.location != null)
                          Row(
                            children: [
                              const Icon(Icons.location_on_outlined),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Text(
                                  'Location: ${table.location}',
                                  style: AppTheme.bodyLarge,
                                ),
                              ),
                            ],
                          ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
              ],

              // Actions
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () {
                        // Download QR code logic
                        Helpers.showToast('Download feature coming soon');
                      },
                      icon: const Icon(Icons.download),
                      label: const Text('Download'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: () {
                        // Share QR code logic
                        Helpers.showToast('Share feature coming soon');
                      },
                      icon: const Icon(Icons.share),
                      label: const Text('Share'),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
