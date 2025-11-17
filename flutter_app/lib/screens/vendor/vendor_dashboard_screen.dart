import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/auth_provider.dart';
import '../../providers/vendor_provider.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';
import '../../widgets/stat_card.dart';
import 'menu_management_screen.dart';
import 'order_management_screen.dart';
import 'table_management_screen.dart';
import 'server_management_screen.dart';

class VendorDashboardScreen extends StatefulWidget {
  const VendorDashboardScreen({super.key});

  @override
  State<VendorDashboardScreen> createState() => _VendorDashboardScreenState();
}

class _VendorDashboardScreenState extends State<VendorDashboardScreen> {
  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final vendorProvider = context.read<VendorProvider>();
    await vendorProvider.loadDashboardStatistics();
    await vendorProvider.loadBusinesses();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Vendor Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_outlined),
            onPressed: () {
              Helpers.showToast('Notifications feature coming soon');
            },
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () => _handleLogout(context),
          ),
        ],
      ),
      body: _buildBody(),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: (index) {
          setState(() {
            _selectedIndex = index;
          });
        },
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.dashboard),
            label: 'Dashboard',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.restaurant_menu),
            label: 'Menu',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.receipt_long),
            label: 'Orders',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.table_bar),
            label: 'Tables',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.people),
            label: 'Servers',
          ),
        ],
      ),
    );
  }

  Widget _buildBody() {
    switch (_selectedIndex) {
      case 0:
        return _buildDashboard();
      case 1:
        return const MenuManagementScreen();
      case 2:
        return const OrderManagementScreen();
      case 3:
        return const TableManagementScreen();
      case 4:
        return const ServerManagementScreen();
      default:
        return _buildDashboard();
    }
  }

  Widget _buildDashboard() {
    return Consumer<VendorProvider>(
      builder: (context, vendorProvider, _) {
        if (vendorProvider.isLoadingDashboard) {
          return const Center(child: CircularProgressIndicator());
        }

        final stats = vendorProvider.dashboardStats;

        return RefreshIndicator(
          onRefresh: _loadData,
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Welcome Section
                Consumer<AuthProvider>(
                  builder: (context, authProvider, _) {
                    return Text(
                      'Welcome, ${authProvider.user?.name ?? "Vendor"}!',
                      style: AppTheme.headlineMedium,
                    );
                  },
                ),
                const SizedBox(height: 8),
                Text(
                  'Here\'s an overview of your business',
                  style: AppTheme.bodyMedium.copyWith(
                    color: AppTheme.textSecondaryColor,
                  ),
                ),
                const SizedBox(height: 24),

                // Statistics Cards
                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisSpacing: 16,
                  mainAxisSpacing: 16,
                  childAspectRatio: 1.5,
                  children: [
                    StatCard(
                      title: 'Total Orders',
                      value: '${stats?.totalOrders ?? 0}',
                      icon: Icons.receipt_long,
                      color: AppTheme.primaryColor,
                    ),
                    StatCard(
                      title: 'Today Orders',
                      value: '${stats?.todayOrders ?? 0}',
                      icon: Icons.today,
                      color: AppTheme.successColor,
                    ),
                    StatCard(
                      title: 'Total Revenue',
                      value: Helpers.formatCurrency(stats?.totalRevenue ?? 0),
                      icon: Icons.attach_money,
                      color: AppTheme.secondaryColor,
                    ),
                    StatCard(
                      title: 'Today Revenue',
                      value: Helpers.formatCurrency(stats?.todayRevenue ?? 0),
                      icon: Icons.trending_up,
                      color: AppTheme.accentColor,
                    ),
                    StatCard(
                      title: 'Menu Items',
                      value: '${stats?.totalItems ?? 0}',
                      icon: Icons.restaurant,
                      color: AppTheme.warningColor,
                    ),
                    StatCard(
                      title: 'Tables',
                      value: '${stats?.totalTables ?? 0}',
                      icon: Icons.table_bar,
                      color: AppTheme.infoColor,
                    ),
                  ],
                ),
                const SizedBox(height: 24),

                // Recent Orders Section
                if (stats?.recentOrders != null &&
                    stats!.recentOrders!.isNotEmpty) ...[
                  Text(
                    'Recent Orders',
                    style: AppTheme.headlineSmall,
                  ),
                  const SizedBox(height: 16),
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: stats.recentOrders!.length,
                    itemBuilder: (context, index) {
                      final order = stats.recentOrders![index];
                      return Card(
                        margin: const EdgeInsets.only(bottom: 8),
                        child: ListTile(
                          leading: CircleAvatar(
                            backgroundColor: StatusColors.getOrderStatusColor(
                              order.status,
                            ),
                            child: const Icon(
                              Icons.receipt,
                              color: Colors.white,
                            ),
                          ),
                          title: Text(order.orderNumber),
                          subtitle: Text(order.customerName),
                          trailing: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Text(
                                Helpers.formatCurrency(order.total),
                                style: AppTheme.titleMedium.copyWith(
                                  color: AppTheme.primaryColor,
                                ),
                              ),
                              Text(
                                Helpers.capitalize(order.status),
                                style: AppTheme.bodySmall.copyWith(
                                  color: StatusColors.getOrderStatusColor(
                                    order.status,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ],
              ],
            ),
          ),
        );
      },
    );
  }


  Future<void> _handleLogout(BuildContext context) async {
    final confirmed = await Helpers.showConfirmDialog(
      context,
      title: 'Logout',
      message: 'Are you sure you want to logout?',
      confirmText: 'Logout',
    );

    if (confirmed && mounted) {
      await context.read<AuthProvider>().logout();
      Helpers.showToast('Logged out successfully');
    }
  }
}
