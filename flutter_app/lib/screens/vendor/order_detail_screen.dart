import 'package:flutter/material.dart';

import '../../models/order.dart';
import '../../services/server_service.dart';
import '../../services/vendor_service.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';

class OrderDetailScreen extends StatefulWidget {
  final int orderId;

  const OrderDetailScreen({super.key, required this.orderId});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  final _vendorService = VendorService();
  final _serverService = ServerService();
  Order? _order;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadOrder();
  }

  Future<void> _loadOrder() async {
    setState(() => _isLoading = true);
    _order = await _vendorService.getOrder(widget.orderId);
    setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Order Details'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _order == null
              ? _buildErrorState()
              : _buildContent(),
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(
            Icons.error_outline,
            size: 64,
            color: AppTheme.errorColor,
          ),
          const SizedBox(height: 16),
          Text(
            'Failed to load order',
            style: AppTheme.headlineSmall,
          ),
          const SizedBox(height: 16),
          ElevatedButton(
            onPressed: _loadOrder,
            child: const Text('Retry'),
          ),
        ],
      ),
    );
  }

  Widget _buildContent() {
    return RefreshIndicator(
      onRefresh: _loadOrder,
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Order Header Card
            _buildHeaderCard(),
            const SizedBox(height: 16),

            // Customer Info Card
            _buildCustomerCard(),
            const SizedBox(height: 16),

            // Order Items Card
            _buildItemsCard(),
            const SizedBox(height: 16),

            // Order Summary Card
            _buildSummaryCard(),
            const SizedBox(height: 16),

            // Status Timeline
            _buildStatusTimeline(),
          ],
        ),
      ),
    );
  }

  Widget _buildHeaderCard() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '#${_order!.orderNumber}',
                  style: AppTheme.headlineSmall.copyWith(
                    color: AppTheme.primaryColor,
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    color: StatusColors.getOrderStatusColor(_order!.status)
                        .withOpacity(0.1),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Text(
                    Helpers.getOrderStatusText(_order!.status),
                    style: AppTheme.titleMedium.copyWith(
                      color: StatusColors.getOrderStatusColor(_order!.status),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Icon(
                  Icons.access_time,
                  size: 16,
                  color: AppTheme.getTextSecondary(context),
                ),
                const SizedBox(width: 4),
                Text(
                  Helpers.formatDateTime(_order!.createdAt),
                  style: AppTheme.bodyMedium,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCustomerCard() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Customer Information',
              style: AppTheme.titleLarge,
            ),
            const SizedBox(height: 12),
            _buildInfoRow(
              Icons.person_outline,
              'Name',
              _order!.customerName ?? 'N/A',
            ),
            if (_order!.customerPhone != null) ...[
              const SizedBox(height: 8),
              _buildInfoRow(
                Icons.phone_outlined,
                'Phone',
                _order!.customerPhone!,
              ),
            ],
            if (_order!.customerEmail != null) ...[
              const SizedBox(height: 8),
              _buildInfoRow(
                Icons.email_outlined,
                'Email',
                _order!.customerEmail!,
              ),
            ],
            if (_order!.table != null) ...[
              const SizedBox(height: 8),
              _buildInfoRow(
                Icons.table_bar,
                'Table',
                'Table ${_order!.table!.tableNumber}',
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildItemsCard() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Order Items',
              style: AppTheme.titleLarge,
            ),
            const SizedBox(height: 12),
            ...(_order!.items ?? []).map((item) {
              return Padding(
                padding: const EdgeInsets.only(bottom: 12),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: AppTheme.primaryColor.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        '${item.quantity}x',
                        style: AppTheme.titleMedium.copyWith(
                          color: AppTheme.primaryColor,
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            item.itemName,
                            style: AppTheme.titleMedium,
                          ),
                          if (item.notes != null) ...[
                            const SizedBox(height: 4),
                            Text(
                              'Note: ${item.notes}',
                              style: AppTheme.bodySmall.copyWith(
                                fontStyle: FontStyle.italic,
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                    Text(
                      Helpers.formatCurrency(item.subtotal),
                      style: AppTheme.titleMedium.copyWith(
                        color: AppTheme.primaryColor,
                      ),
                    ),
                  ],
                ),
              );
            }),
          ],
        ),
      ),
    );
  }

  Widget _buildSummaryCard() {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Payment Method',
                  style: AppTheme.bodyLarge,
                ),
                Text(
                  Helpers.capitalize(_order!.paymentMethod ?? 'N/A'),
                  style: AppTheme.bodyLarge,
                ),
              ],
            ),
            const Divider(height: 24),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Payment Status',
                  style: AppTheme.bodyLarge,
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: StatusColors.getPaymentStatusColor(
                      _order!.paymentStatus,
                    ).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    Helpers.getPaymentStatusText(_order!.paymentStatus),
                    style: AppTheme.bodyMedium.copyWith(
                      color: StatusColors.getPaymentStatusColor(
                        _order!.paymentStatus,
                      ),
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
            const Divider(height: 24),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Total',
                  style: AppTheme.titleLarge,
                ),
                Text(
                  Helpers.formatCurrency(_order!.total),
                  style: AppTheme.titleLarge.copyWith(
                    color: AppTheme.successColor,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusTimeline() {
    final statuses = [
      'pending',
      'confirmed',
      'preparing',
      'ready',
      'served',
      'completed',
    ];

    final currentIndex = statuses.indexOf(_order!.status.toLowerCase());

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Order Progress',
              style: AppTheme.titleLarge,
            ),
            const SizedBox(height: 16),
            ...List.generate(statuses.length, (index) {
              final status = statuses[index];
              final isCompleted = index <= currentIndex;
              final isCurrent = index == currentIndex;

              return Row(
                children: [
                  Column(
                    children: [
                      Container(
                        width: 32,
                        height: 32,
                        decoration: BoxDecoration(
                          color: isCompleted
                              ? AppTheme.primaryColor
                              : AppTheme.borderColor,
                          shape: BoxShape.circle,
                        ),
                        child: Icon(
                          isCompleted ? Icons.check : Icons.circle,
                          color: Colors.white,
                          size: 16,
                        ),
                      ),
                      if (index < statuses.length - 1)
                        Container(
                          width: 2,
                          height: 32,
                          color: isCompleted
                              ? AppTheme.primaryColor
                              : AppTheme.borderColor,
                        ),
                    ],
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(bottom: 32),
                      child: Text(
                        Helpers.capitalize(status),
                        style: AppTheme.titleMedium.copyWith(
                          color: isCurrent
                              ? AppTheme.primaryColor
                              : isCompleted
                                  ? AppTheme.textPrimaryColor
                                  : AppTheme.getTextSecondary(context),
                          fontWeight:
                              isCurrent ? FontWeight.bold : FontWeight.normal,
                        ),
                      ),
                    ),
                  ),
                ],
              );
            }),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(
          icon,
          size: 20,
          color: AppTheme.getTextSecondary(context),
        ),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: AppTheme.bodyMedium.copyWith(
            color: AppTheme.getTextSecondary(context),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: AppTheme.bodyMedium,
          ),
        ),
      ],
    );
  }
}
