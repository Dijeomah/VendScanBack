import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../models/order.dart';
import '../../providers/server_provider.dart';
import '../../utils/constants.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';
import '../vendor/order_detail_screen.dart';

class ServerOrdersScreen extends StatefulWidget {
  const ServerOrdersScreen({super.key});

  @override
  State<ServerOrdersScreen> createState() => _ServerOrdersScreenState();
}

class _ServerOrdersScreenState extends State<ServerOrdersScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  String? _selectedStatus;
  int? _selectedTableId;

  final List<Map<String, dynamic>> _statusTabs = [
    {'label': 'All', 'status': null},
    {'label': 'Pending', 'status': AppConstants.orderStatusPending},
    {'label': 'Confirmed', 'status': AppConstants.orderStatusConfirmed},
    {'label': 'Preparing', 'status': AppConstants.orderStatusPreparing},
    {'label': 'Ready', 'status': AppConstants.orderStatusReady},
    {'label': 'Served', 'status': AppConstants.orderStatusServed},
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
    await context.read<ServerProvider>().loadOrders(
          status: _selectedStatus,
          tableId: _selectedTableId,
        );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('My Orders'),
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          tabs: _statusTabs.map((tab) => Tab(text: tab['label'])).toList(),
        ),
        actions: [
          // Table Filter
          Consumer<ServerProvider>(
            builder: (context, provider, _) {
              if (provider.assignedTables.isEmpty) return const SizedBox.shrink();

              return PopupMenuButton<int>(
                icon: Badge(
                  isLabelVisible: _selectedTableId != null,
                  label: const Text('1'),
                  child: const Icon(Icons.filter_list),
                ),
                tooltip: 'Filter by Table',
                onSelected: (tableId) {
                  setState(() {
                    _selectedTableId = tableId == -1 ? null : tableId;
                  });
                  _loadOrders();
                },
                itemBuilder: (context) => [
                  const PopupMenuItem<int>(
                    value: -1,
                    child: Text('All Tables'),
                  ),
                  ...provider.assignedTables.map((table) {
                    return PopupMenuItem<int>(
                      value: table.id,
                      child: Text('Table ${table.tableNumber}'),
                    );
                  }),
                ],
              );
            },
          ),
        ],
      ),
      body: TabBarView(
        controller: _tabController,
        children: _statusTabs.map((tab) => _buildOrderList()).toList(),
      ),
    );
  }

  Widget _buildOrderList() {
    return Consumer<ServerProvider>(
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
                  color: AppTheme.textSecondaryColor,
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
                    color: AppTheme.textSecondaryColor,
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
              return _ServerOrderCard(
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
    final provider = context.read<ServerProvider>();
    final success = await provider.updateOrderStatus(order.id, newStatus);

    if (success) {
      Helpers.showToast('Order status updated');
    } else {
      Helpers.showToast('Failed to update status', isError: true);
    }
  }
}

class _ServerOrderCard extends StatelessWidget {
  final Order order;
  final VoidCallback onTap;
  final VoidCallback onStatusUpdate;

  const _ServerOrderCard({
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
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 8,
                                vertical: 4,
                              ),
                              decoration: BoxDecoration(
                                color: AppTheme.primaryColor,
                                borderRadius: BorderRadius.circular(4),
                              ),
                              child: Text(
                                order.table != null
                                    ? 'Table ${order.table!.tableNumber}'
                                    : 'No Table',
                                style: AppTheme.bodySmall.copyWith(
                                  color: Colors.white,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                order.orderNumber,
                                style: AppTheme.titleMedium,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 4),
                        Text(
                          order.customerName ?? 'Guest',
                          style: AppTheme.bodyMedium.copyWith(
                            color: AppTheme.textSecondaryColor,
                          ),
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

              // Time
              Row(
                children: [
                  const Icon(
                    Icons.access_time,
                    size: 16,
                    color: AppTheme.textSecondaryColor,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    Helpers.timeAgo(order.createdAt),
                    style: AppTheme.bodySmall,
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Items Summary
              if (order.items != null && order.items!.isNotEmpty) ...[
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppTheme.backgroundColor,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: order.items!.map((item) {
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 4),
                        child: Row(
                          children: [
                            Text(
                              '${item.quantity}x',
                              style: AppTheme.bodyMedium.copyWith(
                                fontWeight: FontWeight.w600,
                                color: AppTheme.primaryColor,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                item.itemName,
                                style: AppTheme.bodyMedium,
                              ),
                            ),
                          ],
                        ),
                      );
                    }).toList(),
                  ),
                ),
                const SizedBox(height: 12),
              ],

              // Quick Actions
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: onStatusUpdate,
                      icon: const Icon(Icons.edit, size: 16),
                      label: const Text('Update'),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: onTap,
                      icon: const Icon(Icons.visibility, size: 16),
                      label: const Text('Details'),
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

  // Server can only update to these statuses
  final List<String> _serverStatuses = [
    AppConstants.orderStatusConfirmed,
    AppConstants.orderStatusPreparing,
    AppConstants.orderStatusReady,
    AppConstants.orderStatusServed,
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
        children: _serverStatuses.map((status) {
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
