<template>
  <AdminLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600 mt-1">Monitor system activity and manage platform</p>
          </div>
          <button
            @click="refreshDashboard"
            :disabled="refreshing"
            class="inline-flex items-center px-4 py-2 bg-yellow-500 text-gray-900 rounded-lg hover:bg-yellow-600 transition-all disabled:opacity-50 font-medium"
          >
            <svg
              class="w-5 h-5 mr-2"
              :class="{ 'animate-spin': refreshing }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-64">
          <LoadingSpinner size="lg" text="Loading dashboard..." />
        </div>

        <!-- Dashboard Content -->
        <div v-else>
          <!-- Stats Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Orders Card -->
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-cyan-100 text-sm font-medium mb-1">Platform Orders</p>
                  <p class="text-3xl font-bold">{{ orderStatistics?.total_orders || 0 }}</p>
                  <p class="text-cyan-100 text-xs mt-2">
                      ${{ parseFloat(orderStatistics?.total_revenue || 0).toFixed(2) }} revenue
                  </p>
                </div>
                <div class="w-12 h-12 bg-cyan-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Vendors Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-blue-100 text-sm font-medium mb-1">Total Vendors</p>
                  <p class="text-3xl font-bold">{{ statistics.vendors?.total || 0 }}</p>
                  <p class="text-blue-100 text-xs mt-2">
                    {{ statistics.vendors?.active_last_30_days || 0 }} active this month
                  </p>
                </div>
                <div class="w-12 h-12 bg-blue-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Businesses Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-green-100 text-sm font-medium mb-1">Total Businesses</p>
                  <p class="text-3xl font-bold">{{ statistics.businesses?.total || 0 }}</p>
                  <p class="text-green-100 text-xs mt-2">
                    {{ statistics.businesses?.by_type?.length || 0 }} business types
                  </p>
                </div>
                <div class="w-12 h-12 bg-green-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Menu Items Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-purple-100 text-sm font-medium mb-1">Menu Items</p>
                  <p class="text-3xl font-bold">{{ statistics.items?.total || 0 }}</p>
                  <p class="text-purple-100 text-xs mt-2">
                    {{ statistics.items?.active || 0 }} active items
                  </p>
                </div>
                <div class="w-12 h-12 bg-purple-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Tables & Servers Card -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-orange-100 text-sm font-medium mb-1">Tables & Servers</p>
                  <p class="text-3xl font-bold">{{ (statistics.tables?.total || 0) + (statistics.servers?.total || 0) }}</p>
                  <p class="text-orange-100 text-xs mt-2">
                    {{ statistics.tables?.total || 0 }} tables, {{ statistics.servers?.total || 0 }} servers
                  </p>
                </div>
                <div class="w-12 h-12 bg-orange-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Categories Summary -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600 mb-1">Total Categories</p>
                  <p class="text-3xl font-bold text-gray-900">{{ statistics.categories?.total || 0 }}</p>
                  <p class="text-xs text-gray-500 mt-2">Used across all businesses</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600 mb-1">Total Subcategories</p>
                  <p class="text-3xl font-bold text-gray-900">{{ statistics.categories?.subcategories || 0 }}</p>
                  <p class="text-xs text-gray-500 mt-2">Nested categorization</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Transaction Charts Row -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Platform Revenue Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Platform Revenue (Last 7 Days)</h3>
              <canvas ref="platformRevenueChartRef"></canvas>
            </div>

            <!-- Top Vendors Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Vendors by Revenue</h3>
              <canvas ref="topVendorsChartRef"></canvas>
            </div>
          </div>

          <!-- Charts Row -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Growth Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Growth Trends (Last 7 Days)</h3>
              <canvas ref="growthChartRef"></canvas>
            </div>

            <!-- Business Types Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Types Distribution</h3>
              <canvas ref="businessTypesChartRef"></canvas>
            </div>
          </div>

          <!-- More Stats Row -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Items by Category -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Items by Category</h3>
              <canvas ref="itemsCategoryChartRef"></canvas>
            </div>

            <!-- Table Status -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Table Status</h3>
              <canvas ref="tableStatusChartRef"></canvas>
            </div>

            <!-- Server Status -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Server Status</h3>
              <canvas ref="serverStatusChartRef"></canvas>
            </div>
          </div>

          <!-- Recent Vendors Table -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-lg font-semibold text-gray-900">Recent Vendors</h2>
              <router-link
                to="/admin/vendors"
                class="text-sm text-yellow-600 hover:text-yellow-700 font-medium"
              >
                View All
              </router-link>
            </div>

            <!-- No vendors -->
            <div v-if="!statistics.vendors?.recent?.length" class="text-center py-12">
              <div class="text-6xl mb-4">👥</div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">No vendors yet</h3>
              <p class="text-gray-600">Create your first vendor to get started</p>
            </div>

            <!-- Vendors Table -->
            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Vendor
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Businesses
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Joined
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="vendor in statistics.vendors.recent" :key="vendor.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ vendor.first_name }} {{ vendor.last_name }}</div>
                      <div class="text-sm text-gray-500">{{ vendor.email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        {{ vendor.business_links?.length || 0 }} businesses
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ new Date(vendor.created_at).toLocaleDateString() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <router-link
                        :to="`/admin/vendors/${vendor.id}`"
                        class="text-yellow-600 hover:text-yellow-900 mr-4"
                      >
                        View
                      </router-link>
                      <router-link
                        :to="`/admin/vendors/${vendor.id}/edit`"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        Edit
                      </router-link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import AdminLayout from '@/components/layouts/AdminLayout.vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const { admin } = useApi()
const toast = useToast()

const loading = ref(true)
const refreshing = ref(false)
const statistics = ref({})
const orderStatistics = ref({})

// Chart refs
const platformRevenueChartRef = ref(null)
const topVendorsChartRef = ref(null)
const growthChartRef = ref(null)
const businessTypesChartRef = ref(null)
const itemsCategoryChartRef = ref(null)
const tableStatusChartRef = ref(null)
const serverStatusChartRef = ref(null)

// Chart instances
let platformRevenueChart = null
let topVendorsChart = null
let growthChart = null
let businessTypesChart = null
let itemsCategoryChart = null
let tableStatusChart = null
let serverStatusChart = null

const loadDashboard = async () => {
  try {
    const dashboardRes = await admin.getDashboard()
    const dashboardData = dashboardRes.data.data || dashboardRes.data
    statistics.value = dashboardData.statistics || {}

    console.log('Dashboard statistics:', statistics.value)

    // Load order statistics
    try {
      const orderResponse = await admin.getOrderStatistics()
      orderStatistics.value = orderResponse.data.data || orderResponse.data
      console.log('Order statistics:', orderStatistics.value)
    } catch (orderError) {
      console.error('Error loading order statistics:', orderError)
      // Don't fail the whole dashboard if orders fail
    }

    // Wait for next tick to ensure DOM is updated
    await nextTick()
    initCharts()
  } catch (error) {
    console.error('Error loading dashboard:', error)
    toast.error('Failed to load dashboard data')
  } finally {
    loading.value = false
  }
}

const initCharts = () => {
  destroyCharts()
  createPlatformRevenueChart()
  createTopVendorsChart()
  createGrowthChart()
  createBusinessTypesChart()
  createItemsCategoryChart()
  createTableStatusChart()
  createServerStatusChart()
}

const destroyCharts = () => {
  if (platformRevenueChart) platformRevenueChart.destroy()
  if (topVendorsChart) topVendorsChart.destroy()
  if (growthChart) growthChart.destroy()
  if (businessTypesChart) businessTypesChart.destroy()
  if (itemsCategoryChart) itemsCategoryChart.destroy()
  if (tableStatusChart) tableStatusChart.destroy()
  if (serverStatusChart) serverStatusChart.destroy()
}

const createPlatformRevenueChart = () => {
  if (!platformRevenueChartRef.value || !orderStatistics.value.revenue_by_day?.length) return

  const ctx = platformRevenueChartRef.value.getContext('2d')
  const dates = orderStatistics.value.revenue_by_day.map(item => {
    const date = new Date(item.date)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  })

  platformRevenueChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: dates,
      datasets: [{
        label: 'Revenue ($)',
        data: orderStatistics.value.revenue_by_day.map(item => parseFloat(item.revenue)),
        borderColor: 'rgb(6, 182, 212)',
        backgroundColor: 'rgba(6, 182, 212, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
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

const createTopVendorsChart = () => {
  if (!topVendorsChartRef.value || !orderStatistics.value.top_vendors?.length) return

  const ctx = topVendorsChartRef.value.getContext('2d')
  const vendorData = orderStatistics.value.top_vendors.slice(0, 5) // Top 5 vendors

  topVendorsChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: vendorData.map(vendor => {
        const firstName = vendor.vendor?.first_name || 'Vendor'
        const lastName = vendor.vendor?.last_name || ''
        return `${firstName} ${lastName}`.trim()
      }),
      datasets: [{
        label: 'Revenue ($)',
        data: vendorData.map(vendor => parseFloat(vendor.total_revenue)),
        backgroundColor: [
          'rgba(59, 130, 246, 0.8)',
          'rgba(34, 197, 94, 0.8)',
          'rgba(168, 85, 247, 0.8)',
          'rgba(251, 146, 60, 0.8)',
          'rgba(236, 72, 153, 0.8)'
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
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

const createGrowthChart = () => {
  if (!growthChartRef.value || !statistics.value.growth) return

  const ctx = growthChartRef.value.getContext('2d')
  const dates = statistics.value.growth.vendors?.map(item => {
    const date = new Date(item.date)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  }) || []

  growthChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: dates,
      datasets: [
        {
          label: 'Vendors',
          data: statistics.value.growth.vendors?.map(item => item.count) || [],
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4
        },
        {
          label: 'Businesses',
          data: statistics.value.growth.businesses?.map(item => item.count) || [],
          borderColor: 'rgb(34, 197, 94)',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          tension: 0.4
        },
        {
          label: 'Items',
          data: statistics.value.growth.items?.map(item => item.count) || [],
          borderColor: 'rgb(168, 85, 247)',
          backgroundColor: 'rgba(168, 85, 247, 0.1)',
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  })
}

const createBusinessTypesChart = () => {
  if (!businessTypesChartRef.value || !statistics.value.businesses?.by_type?.length) return

  const ctx = businessTypesChartRef.value.getContext('2d')
  const businessTypes = statistics.value.businesses.by_type

  businessTypesChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: businessTypes.map(item => item.business_type || 'Other'),
      datasets: [{
        data: businessTypes.map(item => item.count),
        backgroundColor: [
          'rgba(59, 130, 246, 0.8)',
          'rgba(34, 197, 94, 0.8)',
          'rgba(168, 85, 247, 0.8)',
          'rgba(251, 146, 60, 0.8)',
          'rgba(236, 72, 153, 0.8)',
          'rgba(20, 184, 166, 0.8)'
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

const createItemsCategoryChart = () => {
  if (!itemsCategoryChartRef.value || !statistics.value.items?.by_category?.length) return

  const ctx = itemsCategoryChartRef.value.getContext('2d')
  const itemsByCategory = statistics.value.items.by_category.slice(0, 5) // Top 5 categories

  itemsCategoryChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: itemsByCategory.map(item => item.category?.category_name || 'Uncategorized'),
      datasets: [{
        label: 'Items',
        data: itemsByCategory.map(item => item.count),
        backgroundColor: 'rgba(168, 85, 247, 0.8)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  })
}

const createTableStatusChart = () => {
  if (!tableStatusChartRef.value || !statistics.value.tables) return

  const ctx = tableStatusChartRef.value.getContext('2d')

  tableStatusChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Available', 'Occupied'],
      datasets: [{
        data: [
          statistics.value.tables.available || 0,
          statistics.value.tables.occupied || 0
        ],
        backgroundColor: [
          'rgba(34, 197, 94, 0.8)',
          'rgba(239, 68, 68, 0.8)'
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

const createServerStatusChart = () => {
  if (!serverStatusChartRef.value || !statistics.value.servers) return

  const ctx = serverStatusChartRef.value.getContext('2d')

  serverStatusChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Active', 'Inactive'],
      datasets: [{
        data: [
          statistics.value.servers.active || 0,
          statistics.value.servers.inactive || 0
        ],
        backgroundColor: [
          'rgba(34, 197, 94, 0.8)',
          'rgba(156, 163, 175, 0.8)'
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

const refreshDashboard = async () => {
  refreshing.value = true
  await loadDashboard()
  refreshing.value = false
  toast.success('Dashboard refreshed!')
}

onMounted(() => {
  loadDashboard()
})
</script>

<style scoped>
canvas {
  max-height: 300px;
}
</style>
