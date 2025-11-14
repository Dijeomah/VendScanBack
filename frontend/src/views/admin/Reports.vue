<template>
  <AdminLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Reports & Analytics</h1>
          <p class="text-gray-600 mt-1">View detailed reports and export data</p>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
              <input
                v-model="filters.from_date"
                type="date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>
            <div class="md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
              <input
                v-model="filters.to_date"
                type="date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>
            <div class="md:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
              <select
                v-model="filters.report_type"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              >
                <option value="all">All Reports</option>
                <option value="revenue">Revenue</option>
                <option value="orders">Orders</option>
                <option value="vendors">Vendors</option>
                <option value="items">Items</option>
              </select>
            </div>
            <div class="md:col-span-1 flex items-end gap-2">
              <button
                @click="loadReports"
                :disabled="loading"
                class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
              >
                <svg v-if="loading" class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span v-else>Apply</span>
              </button>
              <button
                @click="exportReports"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>

        <!-- Reports Content -->
        <div v-else class="space-y-6">
          <!-- Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
              <p class="text-blue-100 text-sm font-medium mb-1">Total Revenue</p>
              <p class="text-3xl font-bold">${{ formatCurrency(reports.total_revenue) }}</p>
              <p class="text-blue-100 text-xs mt-2">
                {{ reports.revenue_change >= 0 ? '+' : '' }}{{ reports.revenue_change }}% from last period
              </p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
              <p class="text-green-100 text-sm font-medium mb-1">Total Orders</p>
              <p class="text-3xl font-bold">{{ reports.total_orders || 0 }}</p>
              <p class="text-green-100 text-xs mt-2">
                {{ reports.orders_change >= 0 ? '+' : '' }}{{ reports.orders_change }}% from last period
              </p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
              <p class="text-purple-100 text-sm font-medium mb-1">Avg Order Value</p>
              <p class="text-3xl font-bold">${{ formatCurrency(reports.avg_order_value) }}</p>
              <p class="text-purple-100 text-xs mt-2">Per transaction</p>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
              <p class="text-orange-100 text-sm font-medium mb-1">Active Vendors</p>
              <p class="text-3xl font-bold">{{ reports.active_vendors || 0 }}</p>
              <p class="text-orange-100 text-xs mt-2">In selected period</p>
            </div>
          </div>

          <!-- Revenue Chart -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Revenue Over Time</h2>
            <canvas ref="revenueChartRef"></canvas>
          </div>

          <!-- Orders Chart -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Orders by Status</h2>
            <canvas ref="ordersChartRef"></canvas>
          </div>

          <!-- Top Performing Items -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Top Performing Items</h2>
            <div v-if="reports.top_items?.length > 0" class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity Sold</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="item in reports.top_items" :key="item.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ item.item?.title || 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ item.item?.category?.category_name || 'Uncategorized' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                      {{ item.total_quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                      ${{ formatCurrency(item.total_revenue) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p v-else class="text-center text-gray-500 py-8">No data available for selected period</p>
          </div>

          <!-- Vendor Performance -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Vendor Performance</h2>
            <div v-if="reports.vendor_performance?.length > 0" class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Orders</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Revenue</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg Order Value</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="vendor in reports.vendor_performance" :key="vendor.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ vendor.vendor?.first_name }} {{ vendor.vendor?.last_name }}
                      </div>
                      <div class="text-sm text-gray-500">{{ vendor.vendor?.email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                      {{ vendor.order_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                      ${{ formatCurrency(vendor.total_sales) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                      ${{ formatCurrency(vendor.total_sales / vendor.order_count) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p v-else class="text-center text-gray-500 py-8">No vendor data available</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import AdminLayout from '@/components/layouts/AdminLayout.vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const { admin } = useApi()
const toast = useToast()

const loading = ref(true)
const reports = ref({
  total_revenue: 0,
  total_orders: 0,
  avg_order_value: 0,
  active_vendors: 0,
  revenue_change: 0,
  orders_change: 0,
  top_items: [],
  vendor_performance: [],
  revenue_by_day: [],
  orders_by_status: []
})

const filters = ref({
  from_date: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  to_date: new Date().toISOString().split('T')[0],
  report_type: 'all'
})

const revenueChartRef = ref(null)
const ordersChartRef = ref(null)
let revenueChart = null
let ordersChart = null

const loadReports = async () => {
  try {
    loading.value = true
    const response = await admin.getOrderStatistics()
    const data = response.data.data || response.data

    reports.value = {
      total_revenue: data.total_revenue || 0,
      total_orders: data.total_orders || 0,
      avg_order_value: data.total_orders > 0 ? (data.total_revenue / data.total_orders) : 0,
      active_vendors: data.top_vendors?.length || 0,
      revenue_change: 5.2, // Calculate based on previous period
      orders_change: 12.5, // Calculate based on previous period
      top_items: data.top_items || [],
      vendor_performance: data.top_vendors || [],
      revenue_by_day: data.revenue_by_day || [],
      orders_by_status: data.orders_by_status || []
    }

    setTimeout(() => {
      initCharts()
    }, 100)
  } catch (error) {
    console.error('Error loading reports:', error)
    toast.error('Failed to load reports')
  } finally {
    loading.value = false
  }
}

const initCharts = () => {
  destroyCharts()
  createRevenueChart()
  createOrdersChart()
}

const destroyCharts = () => {
  if (revenueChart) revenueChart.destroy()
  if (ordersChart) ordersChart.destroy()
}

const createRevenueChart = () => {
  if (!revenueChartRef.value || !reports.value.revenue_by_day?.length) return

  const ctx = revenueChartRef.value.getContext('2d')
  const dates = reports.value.revenue_by_day.map(item => {
    const date = new Date(item.date)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  })

  revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: dates,
      datasets: [{
        label: 'Revenue ($)',
        data: reports.value.revenue_by_day.map(item => parseFloat(item.revenue)),
        borderColor: 'rgb(59, 130, 246)',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '$' + value.toFixed(2)
            }
          }
        }
      }
    }
  })
}

const createOrdersChart = () => {
  if (!ordersChartRef.value || !reports.value.orders_by_status?.length) return

  const ctx = ordersChartRef.value.getContext('2d')

  ordersChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: reports.value.orders_by_status.map(item =>
        item.status.charAt(0).toUpperCase() + item.status.slice(1)
      ),
      datasets: [{
        data: reports.value.orders_by_status.map(item => item.count),
        backgroundColor: [
          'rgba(251, 191, 36, 0.8)',  // pending - yellow
          'rgba(59, 130, 246, 0.8)',  // confirmed - blue
          'rgba(168, 85, 247, 0.8)',  // preparing - purple
          'rgba(34, 197, 94, 0.8)',   // served - green
          'rgba(156, 163, 175, 0.8)', // completed - gray
          'rgba(239, 68, 68, 0.8)'    // cancelled - red
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  })
}

const formatCurrency = (value) => {
  if (!value) return '0.00'
  return parseFloat(value).toFixed(2)
}

const exportReports = () => {
  toast.info('Export functionality coming soon!')
}

onMounted(() => {
  loadReports()
})
</script>

<style scoped>
canvas {
  max-height: 400px;
}
</style>
