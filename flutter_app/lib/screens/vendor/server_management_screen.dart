import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../models/server.dart';
import '../../providers/vendor_provider.dart';
import '../../services/vendor_service.dart';
import '../../utils/helpers.dart';
import '../../utils/theme.dart';

class ServerManagementScreen extends StatefulWidget {
  const ServerManagementScreen({super.key});

  @override
  State<ServerManagementScreen> createState() => _ServerManagementScreenState();
}

class _ServerManagementScreenState extends State<ServerManagementScreen> {
  @override
  void initState() {
    super.initState();
    _loadServers();
  }

  Future<void> _loadServers() async {
    await context.read<VendorProvider>().loadServers();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Server Management'),
      ),
      body: Consumer<VendorProvider>(
        builder: (context, provider, _) {
          if (provider.isLoadingServers) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.servers.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.people_outline,
                    size: 64,
                    color: AppTheme.textSecondaryColor,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'No Servers Yet',
                    style: AppTheme.headlineSmall,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Add your first server',
                    style: AppTheme.bodyMedium.copyWith(
                      color: AppTheme.textSecondaryColor,
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton.icon(
                    onPressed: _showCreateDialog,
                    icon: const Icon(Icons.add),
                    label: const Text('Add Server'),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: _loadServers,
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: provider.servers.length,
              itemBuilder: (context, index) {
                final server = provider.servers[index];
                return _ServerCard(
                  server: server,
                  onTap: () => _showServerDetails(server),
                  onAssign: () => _showAssignDialog(server),
                );
              },
            ),
          );
        },
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showCreateDialog,
        icon: const Icon(Icons.add),
        label: const Text('Add Server'),
      ),
    );
  }

  void _showCreateDialog() {
    final nameController = TextEditingController();
    final emailController = TextEditingController();
    final phoneController = TextEditingController();
    final passwordController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Create Server'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: nameController,
                decoration: const InputDecoration(
                  labelText: 'Name *',
                  hintText: 'Server name',
                ),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: emailController,
                decoration: const InputDecoration(
                  labelText: 'Email *',
                  hintText: 'server@example.com',
                ),
                keyboardType: TextInputType.emailAddress,
              ),
              const SizedBox(height: 16),
              TextField(
                controller: phoneController,
                decoration: const InputDecoration(
                  labelText: 'Phone',
                  hintText: '+1234567890',
                ),
                keyboardType: TextInputType.phone,
              ),
              const SizedBox(height: 16),
              TextField(
                controller: passwordController,
                decoration: const InputDecoration(
                  labelText: 'Password *',
                  hintText: 'Minimum 8 characters',
                ),
                obscureText: true,
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
              if (nameController.text.trim().isEmpty ||
                  emailController.text.trim().isEmpty ||
                  passwordController.text.isEmpty) {
                Helpers.showToast('Please fill all required fields', isError: true);
                return;
              }

              Navigator.pop(context);
              await _createServer(
                nameController.text.trim(),
                emailController.text.trim(),
                passwordController.text,
                phoneController.text.trim().isNotEmpty
                    ? phoneController.text.trim()
                    : null,
              );
            },
            child: const Text('Create'),
          ),
        ],
      ),
    );
  }

  Future<void> _createServer(
    String name,
    String email,
    String password,
    String? phone,
  ) async {
    final vendorService = VendorService();
    final response = await vendorService.createServer(
      name: name,
      email: email,
      password: password,
      phone: phone,
    );

    if (response.success) {
      Helpers.showToast('Server created successfully');
      await _loadServers();
    } else {
      Helpers.showToast(response.message, isError: true);
    }
  }

  void _showServerDetails(Server server) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => _ServerDetailsSheet(server: server),
    );
  }

  void _showAssignDialog(Server server) {
    final provider = context.read<VendorProvider>();

    if (provider.selectedBusiness == null) {
      Helpers.showToast('Please select a business first', isError: true);
      return;
    }

    if (provider.tables.isEmpty) {
      Helpers.showToast('No tables available', isError: true);
      return;
    }

    showDialog(
      context: context,
      builder: (context) => _AssignTableDialog(
        server: server,
        onAssign: (tableId) => _assignServerToTable(server, tableId),
      ),
    );
  }

  Future<void> _assignServerToTable(Server server, int tableId) async {
    final provider = context.read<VendorProvider>();
    if (provider.selectedBusiness == null) return;

    final vendorService = VendorService();
    final response = await vendorService.assignServerToTable(
      businessId: provider.selectedBusiness!.id,
      serverId: server.id,
      tableId: tableId,
    );

    if (response.success) {
      Helpers.showToast('Server assigned to table successfully');
    } else {
      Helpers.showToast(response.message, isError: true);
    }
  }
}

class _ServerCard extends StatelessWidget {
  final Server server;
  final VoidCallback onTap;
  final VoidCallback onAssign;

  const _ServerCard({
    required this.server,
    required this.onTap,
    required this.onAssign,
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
              Row(
                children: [
                  CircleAvatar(
                    backgroundColor: AppTheme.primaryColor,
                    child: Text(
                      server.name[0].toUpperCase(),
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          server.name,
                          style: AppTheme.titleLarge,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          server.email,
                          style: AppTheme.bodyMedium.copyWith(
                            color: AppTheme.textSecondaryColor,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              if (server.phone != null) ...[
                const SizedBox(height: 12),
                Row(
                  children: [
                    const Icon(
                      Icons.phone_outlined,
                      size: 16,
                      color: AppTheme.textSecondaryColor,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      server.phone!,
                      style: AppTheme.bodySmall,
                    ),
                  ],
                ),
              ],
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: onAssign,
                      icon: const Icon(Icons.assignment_ind, size: 16),
                      label: const Text('Assign Table'),
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

class _ServerDetailsSheet extends StatelessWidget {
  final Server server;

  const _ServerDetailsSheet({required this.server});

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
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Handle
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: AppTheme.borderColor,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Server Info
              Row(
                children: [
                  CircleAvatar(
                    radius: 32,
                    backgroundColor: AppTheme.primaryColor,
                    child: Text(
                      server.name[0].toUpperCase(),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          server.name,
                          style: AppTheme.headlineSmall,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          server.email,
                          style: AppTheme.bodyMedium.copyWith(
                            color: AppTheme.textSecondaryColor,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              // Stats Card
              if (server.statistics != null) ...[
                Card(
                  color: AppTheme.backgroundColor,
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _buildStatItem(
                          'Total Orders',
                          '${server.statistics!.totalOrders}',
                        ),
                        _buildStatItem(
                          'Today',
                          '${server.statistics!.todayOrders}',
                        ),
                        _buildStatItem(
                          'Tables',
                          '${server.statistics!.assignedTables}',
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
              ],

              // Assigned Tables
              if (server.tables != null && server.tables!.isNotEmpty) ...[
                Text(
                  'Assigned Tables',
                  style: AppTheme.titleLarge,
                ),
                const SizedBox(height: 12),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: server.tables!.map((table) {
                    return Chip(
                      avatar: const Icon(Icons.table_bar, size: 16),
                      label: Text('Table ${table.tableNumber}'),
                    );
                  }).toList(),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatItem(String label, String value) {
    return Column(
      children: [
        Text(
          value,
          style: AppTheme.headlineMedium.copyWith(
            color: AppTheme.primaryColor,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          label,
          style: AppTheme.bodySmall,
        ),
      ],
    );
  }
}

class _AssignTableDialog extends StatefulWidget {
  final Server server;
  final Function(int) onAssign;

  const _AssignTableDialog({
    required this.server,
    required this.onAssign,
  });

  @override
  State<_AssignTableDialog> createState() => _AssignTableDialogState();
}

class _AssignTableDialogState extends State<_AssignTableDialog> {
  int? _selectedTableId;

  @override
  Widget build(BuildContext context) {
    return Consumer<VendorProvider>(
      builder: (context, provider, _) {
        return AlertDialog(
          title: Text('Assign ${widget.server.name} to Table'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: provider.tables.map((table) {
                return RadioListTile<int>(
                  title: Text('Table ${table.tableNumber}'),
                  subtitle: table.location != null
                      ? Text(table.location!)
                      : null,
                  value: table.id,
                  groupValue: _selectedTableId,
                  onChanged: (value) {
                    setState(() {
                      _selectedTableId = value;
                    });
                  },
                );
              }).toList(),
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: _selectedTableId == null
                  ? null
                  : () {
                      widget.onAssign(_selectedTableId!);
                      Navigator.pop(context);
                    },
              child: const Text('Assign'),
            ),
          ],
        );
      },
    );
  }
}
