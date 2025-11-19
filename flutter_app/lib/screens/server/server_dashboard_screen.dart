import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../providers/auth_provider.dart';
import '../../providers/server_provider.dart';
import '../../providers/settings_provider.dart';
import '../../services/notification_service.dart';
import '../../utils/constants.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';
import '../../widgets/stat_card.dart';
import '../settings/settings_screen.dart';
import 'server_orders_screen.dart';

class ServerDashboardScreen extends StatefulWidget {
  const ServerDashboardScreen({super.key});

  @override
  State<ServerDashboardScreen> createState() => _ServerDashboardScreenState();
}

class _ServerDashboardScreenState extends State<ServerDashboardScreen> {
  int _selectedIndex = 0;
  Timer? _refreshTimer;
  int _lastOrderCount = 0;

  @override
  void initState() {
    super.initState();
    _loadData();
    _startAutoRefresh();
  }

  @override
  void dispose() {
    _refreshTimer?.cancel();
    super.dispose();
  }

  void _startAutoRefresh() {
    final settings = context.read<SettingsProvider>();
    if (settings.autoRefreshEnabled) {
      _refreshTimer = Timer.periodic(
        Duration(seconds: settings.refreshIntervalSeconds),
        (_) => _checkForNewOrders(),
      );
    }
  }

  Future<void> _checkForNewOrders() async {
    final serverProvider = context.read<ServerProvider>();
    await serverProvider.loadOrders();

    final currentCount = serverProvider.orders.length;
    if (currentCount > _lastOrderCount && _lastOrderCount > 0) {
      // New orders detected - play notification
      NotificationService().notifyNewOrder();
    }
    _lastOrderCount = currentCount;
  }

  Future<void> _loadData() async {
    final serverProvider = context.read<ServerProvider>();
    await serverProvider.loadDashboardStatistics();
    await serverProvider.loadAssignments();

    // Initialize order count for notifications
    await serverProvider.loadOrders();
    _lastOrderCount = serverProvider.orders.length;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Server Dashboard'),
        actions: [
          // Notification Bell with Badge
          Consumer<ServerProvider>(
            builder: (context, provider, _) {
              final pendingCount = provider.orders
                  .where((o) =>
                      o.status == AppConstants.orderStatusPending ||
                      o.status == AppConstants.orderStatusConfirmed)
                  .length;

              return IconButton(
                icon: Badge(
                  isLabelVisible: pendingCount > 0,
                  label: Text(
                    pendingCount > 99 ? '99+' : pendingCount.toString(),
                    style: const TextStyle(fontSize: 10),
                  ),
                  child: const Icon(Icons.notifications_outlined),
                ),
                onPressed: () {
                  // Navigate to Orders tab
                  setState(() {
                    _selectedIndex = 1;
                  });
                },
                tooltip: 'Pending Orders',
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.settings_outlined),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const SettingsScreen(),
                ),
              );
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
            icon: Icon(Icons.receipt_long),
            label: 'Orders',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.table_bar),
            label: 'My Tables',
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
        return const ServerOrdersScreen();
      case 2:
        return _buildTablesView();
      default:
        return _buildDashboard();
    }
  }

  Widget _buildDashboard() {
    return Consumer<ServerProvider>(
      builder: (context, serverProvider, _) {
        if (serverProvider.isLoadingDashboard) {
          return const Center(child: CircularProgressIndicator());
        }

        final stats = serverProvider.dashboardStats;

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
                      'Welcome, ${authProvider.user?.name ?? "Server"}!',
                      style: AppTheme.headlineMedium,
                    );
                  },
                ),
                const SizedBox(height: 8),
                Text(
                  'Here\'s your activity overview',
                  style: AppTheme.bodyMedium.copyWith(
                    color: AppTheme.getTextSecondary(context),
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
                      title: 'Assigned Tables',
                      value: '${serverProvider.assignedTables.length}',
                      icon: Icons.table_bar,
                      color: AppTheme.secondaryColor,
                    ),
                    StatCard(
                      title: 'Businesses',
                      value: '${serverProvider.assignedBusinesses.length}',
                      icon: Icons.business,
                      color: AppTheme.accentColor,
                    ),
                  ],
                ),
                const SizedBox(height: 24),

                // Assigned Tables Section
                if (serverProvider.assignedTables.isNotEmpty) ...[
                  Text(
                    'My Assigned Tables',
                    style: AppTheme.headlineSmall,
                  ),
                  const SizedBox(height: 16),
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: serverProvider.assignedTables.length,
                    itemBuilder: (context, index) {
                      final table = serverProvider.assignedTables[index];
                      return Card(
                        margin: const EdgeInsets.only(bottom: 8),
                        child: ListTile(
                          leading: const CircleAvatar(
                            backgroundColor: AppTheme.primaryColor,
                            child: Icon(
                              Icons.table_bar,
                              color: Colors.white,
                            ),
                          ),
                          title: Text('Table ${table.tableNumber}'),
                          subtitle: Text(table.businessName ?? 'Business'),
                          trailing: const Icon(Icons.arrow_forward_ios,
                              size: 16),
                          onTap: () {
                            // Load orders filtered by this table first
                            context.read<ServerProvider>().loadOrders(
                              tableId: table.id,
                            );
                            // Then navigate to Orders tab
                            setState(() {
                              _selectedIndex = 1;
                            });
                          },
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

  Widget _buildTablesView() {
    return Consumer<ServerProvider>(
      builder: (context, serverProvider, _) {
        if (serverProvider.isLoadingAssignments) {
          return const Center(child: CircularProgressIndicator());
        }

        if (serverProvider.assignedTables.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(
                  Icons.table_bar,
                  size: 64,
                  color: AppTheme.getTextSecondary(context),
                ),
                const SizedBox(height: 16),
                Text(
                  'No Tables Assigned',
                  style: AppTheme.headlineSmall,
                ),
                const SizedBox(height: 8),
                Text(
                  'You don\'t have any assigned tables yet',
                  style: AppTheme.bodyMedium.copyWith(
                    color: AppTheme.getTextSecondary(context),
                  ),
                ),
              ],
            ),
          );
        }

        return RefreshIndicator(
          onRefresh: () => serverProvider.loadAssignments(),
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: serverProvider.assignedTables.length,
            itemBuilder: (context, index) {
              final table = serverProvider.assignedTables[index];
              return Card(
                margin: const EdgeInsets.only(bottom: 12),
                child: ListTile(
                  leading: CircleAvatar(
                    backgroundColor: AppTheme.primaryColor,
                    child: Text(
                      table.tableNumber,
                      style: const TextStyle(color: Colors.white),
                    ),
                  ),
                  title: Text('Table ${table.tableNumber}'),
                  subtitle: Text(table.businessName ?? 'Business'),
                  trailing: ElevatedButton(
                    onPressed: () {
                      // Load orders filtered by this table first
                      context.read<ServerProvider>().loadOrders(
                        tableId: table.id,
                      );
                      // Then navigate to Orders tab
                      setState(() {
                        _selectedIndex = 1;
                      });
                    },
                    child: const Text('View Orders'),
                  ),
                ),
              );
            },
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
