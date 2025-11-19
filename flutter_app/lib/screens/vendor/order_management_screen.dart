import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../models/order.dart';
import '../../providers/vendor_provider.dart';
import '../../utils/constants.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';
import 'order_detail_screen.dart';

class OrderManagementScreen extends StatefulWidget {
  const OrderManagementScreen({super.key});

  @override
  State<OrderManagementScreen> createState() => _OrderManagementScreenState();
}

class _OrderManagementScreenState extends State<OrderManagementScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  String? _selectedStatus;

  final List<Map<String, dynamic>> _statusTabs = [
    {'label': 'All', 'status': null},
    {'label': 'Pending', 'status': AppConstants.orderStatusPending},
    {'label': 'Confirmed', 'status': AppConstants.orderStatusConfirmed},
    {'label': 'Preparing', 'status': AppConstants.orderStatusPreparing},
    {'label': 'Completed', 'status': AppConstants.orderStatusCompleted},
  ];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _statusTabs.length, vsync: this);
    _tabController.addListener(_handleTabChange);
    _loadOrders();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  void _handleTabChange() {
    if (_tabController.indexIsChanging) {
      setState(() {
        _selectedStatus = _statusTabs[_tabController.index]['status'];
      });
      _loadOrders();
    }
  }

  Future<void> _loadOrders() async {
    await context.read<VendorProvider>().loadOrders(status: _selectedStatus);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Orders'),
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          tabs: _statusTabs.map((tab) => Tab(text: tab['label'])).toList(),
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: _statusTabs.map((tab) => _buildOrderList()).toList(),
      ),
    );
  }

  Widget _buildOrderList() {
    return Consumer<VendorProvider>(
      builder: (context, provider, _) {
        if (provider.isLoadingOrders) {
          return const Center(child: CircularProgressIndicator());
        }

        if (provider.orders.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(
                  Icons.receipt_long_outlined,
                  size: 64,
                  color: AppTheme.getTextSecondary(context),
                ),
                const SizedBox(height: 16),
                Text(
                  'No orders found',
                  style: AppTheme.headlineSmall,
                ),
                const SizedBox(height: 8),
                Text(
                  'Orders will appear here',
                  style: AppTheme.bodyMedium.copyWith(
                    color: AppTheme.getTextSecondary(context),
                  ),
                ),
              ],
            ),
          );
        }

        return RefreshIndicator(
          onRefresh: _loadOrders,
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: provider.orders.length,
            itemBuilder: (context, index) {
              final order = provider.orders[index];
              return _OrderCard(
                order: order,
                onTap: () => _navigateToDetail(order),
                onStatusUpdate: () => _showStatusDialog(order),
              );
            },
          ),
        );
      },
    );
  }

  void _navigateToDetail(Order order) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => OrderDetailScreen(orderId: order.id),
      ),
    );
  }

  void _showStatusDialog(Order order) {
    showDialog(
      context: context,
      builder: (context) => _UpdateStatusDialog(
        order: order,
        onUpdate: (newStatus) {
          _updateOrderStatus(order, newStatus);
        },
      ),
    );
  }

  Future<void> _updateOrderStatus(Order order, String newStatus) async {
    final provider = context.read<VendorProvider>();
    final success = await provider.updateOrderStatus(order.id, newStatus);

    if (success) {
      Helpers.showToast('Order status updated');
    } else {
      Helpers.showToast('Failed to update status', isError: true);
    }
  }
}

class _OrderCard extends StatelessWidget {
  final Order order;
  final VoidCallback onTap;
  final VoidCallback onStatusUpdate;

  const _OrderCard({
    required this.order,
    required this.onTap,
    required this.onStatusUpdate,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Row
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          order.orderNumber,
                          style: AppTheme.titleLarge.copyWith(
                            color: AppTheme.primaryColor,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          order.customerName ?? 'Guest',
                          style: AppTheme.bodyMedium,
                        ),
                      ],
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        Helpers.formatCurrency(order.total),
                        style: AppTheme.titleLarge.copyWith(
                          color: AppTheme.successColor,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 4,
                        ),
                        decoration: BoxDecoration(
                          color: StatusColors.getOrderStatusColor(order.status)
                              .withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          Helpers.getOrderStatusText(order.status),
                          style: AppTheme.bodySmall.copyWith(
                            color: StatusColors.getOrderStatusColor(order.status),
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Order Info
              Row(
                children: [
                  const Icon(
                    Icons.access_time,
                    size: 16,
                    color: AppTheme.getTextSecondary(context),
                  ),
                  const SizedBox(width: 4),
                  Text(
                    Helpers.formatDateTime(order.createdAt),
                    style: AppTheme.bodySmall,
                  ),
                  const SizedBox(width: 16),
                  if (order.table != null) ...[
                    const Icon(
                      Icons.table_bar,
                      size: 16,
                      color: AppTheme.getTextSecondary(context),
                    ),
                    const SizedBox(width: 4),
                    Text(
                      'Table ${order.table!.tableNumber}',
                      style: AppTheme.bodySmall,
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 12),

              // Items Summary
              if (order.items != null && order.items!.isNotEmpty) ...[
                Text(
                  '${order.items!.length} item(s): ${order.items!.map((e) => e.itemName).join(", ")}',
                  style: AppTheme.bodySmall,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 12),
              ],

              // Actions
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: onStatusUpdate,
                      icon: const Icon(Icons.edit, size: 16),
                      label: const Text('Update Status'),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: onTap,
                      icon: const Icon(Icons.visibility, size: 16),
                      label: const Text('View Details'),
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

class _UpdateStatusDialog extends StatefulWidget {
  final Order order;
  final Function(String) onUpdate;

  const _UpdateStatusDialog({
    required this.order,
    required this.onUpdate,
  });

  @override
  State<_UpdateStatusDialog> createState() => _UpdateStatusDialogState();
}

class _UpdateStatusDialogState extends State<_UpdateStatusDialog> {
  late String _selectedStatus;

  final List<String> _statuses = [
    AppConstants.orderStatusPending,
    AppConstants.orderStatusConfirmed,
    AppConstants.orderStatusPreparing,
    AppConstants.orderStatusReady,
    AppConstants.orderStatusServed,
    AppConstants.orderStatusCompleted,
    AppConstants.orderStatusCancelled,
  ];

  @override
  void initState() {
    super.initState();
    _selectedStatus = widget.order.status;
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: const Text('Update Order Status'),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: _statuses.map((status) {
          return RadioListTile<String>(
            title: Text(Helpers.getOrderStatusText(status)),
            value: status,
            groupValue: _selectedStatus,
            onChanged: (value) {
              setState(() {
                _selectedStatus = value!;
              });
            },
          );
        }).toList(),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Cancel'),
        ),
        ElevatedButton(
          onPressed: () {
            widget.onUpdate(_selectedStatus);
            Navigator.pop(context);
          },
          child: const Text('Update'),
        ),
      ],
    );
  }
}
