<template>
  <VendorLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard</h1>
          <p class="text-gray-600 mt-1">Welcome back, {{ authStore.userName }}!</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-64">
          <LoadingSpinner size="lg" text="Loading dashboard..." />
        </div>

        <!-- Dashboard Content -->
        <div v-else class="space-y-6">
          <!-- Stats Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Total Orders -->
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-cyan-100 text-sm font-medium mb-1">Total Orders</p>
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

            <!-- Total Businesses -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-blue-100 text-sm font-medium mb-1">My Businesses</p>
                  <p class="text-3xl font-bold">{{ statistics.businesses?.total || 0 }}</p>
                  <p class="text-blue-100 text-xs mt-2">
                    of {{ getBusinessLimit() }} {{ getBusinessLimit() === 'Unlimited' ? '' : 'allowed' }}
                  </p>
                </div>
                <div class="w-12 h-12 bg-blue-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Total Items -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-green-100 text-sm font-medium mb-1">Total Items</p>
                  <p class="text-3xl font-bold">{{ statistics.items?.total || 0 }}</p>
                  <p class="text-green-100 text-xs mt-2">
                    {{ statistics.items?.active || 0 }} active
                  </p>
                </div>
                <div class="w-12 h-12 bg-green-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Tables -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-purple-100 text-sm font-medium mb-1">Tables</p>
                  <p class="text-3xl font-bold">{{ statistics.tables?.total || 0 }}</p>
                  <p class="text-purple-100 text-xs mt-2">
                    {{ statistics.tables?.occupied || 0 }} occupied
                  </p>
                </div>
                <div class="w-12 h-12 bg-purple-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Servers -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-orange-100 text-sm font-medium mb-1">Servers</p>
                  <p class="text-3xl font-bold">{{ statistics.servers?.total || 0 }}</p>
                  <p class="text-orange-100 text-xs mt-2">
                    {{ statistics.servers?.active || 0 }} active
                  </p>
                </div>
                <div class="w-12 h-12 bg-orange-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                  <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Transaction Charts Row -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenue Trends Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trends (Last 7 Days)</h3>
              <canvas ref="revenueChartRef"></canvas>
            </div>

            <!-- Top Servers Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Servers by Sales</h3>
              <canvas ref="topServersChartRef"></canvas>
            </div>
          </div>

          <!-- Charts Row -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Item Growth Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Item Growth (Last 7 Days)</h3>
              <canvas ref="itemGrowthChartRef"></canvas>
            </div>

            <!-- Items by Category Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Items by Category</h3>
              <canvas ref="itemsCategoryChartRef"></canvas>
            </div>
          </div>

          <!-- Subscription & Quick Actions Row -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Subscription Tier Card -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl shadow-sm p-6 text-white">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <p class="text-sm font-medium text-primary-100 mb-1">Current Plan</p>
                  <h3 class="text-2xl font-bold capitalize">{{ authStore.user?.subscription_tier || 'Free' }} Plan</h3>
                </div>
                <router-link
                  v-if="authStore.user?.subscription_tier !== 'max'"
                  to="/vendor/subscription"
                  class="inline-flex items-center px-4 py-2 bg-white text-primary-600 rounded-lg hover:bg-primary-50 transition-all font-medium text-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                  </svg>
                  Upgrade
                </router-link>
              </div>
              <div class="pt-4 border-t border-primary-500">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                  <div>
                    <p class="text-2xl font-bold">{{ statistics.items?.total || 0 }}</p>
                    <p class="text-xs text-primary-100">Items</p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">{{ statistics.tables?.total || 0 }}</p>
                    <p class="text-xs text-primary-100">Tables</p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">{{ statistics.categories?.total || 0 }}</p>
                    <p class="text-xs text-primary-100">Categories</p>
                  </div>
                  <div>
                    <p class="text-2xl font-bold">{{ statistics.categories?.subcategories || 0 }}</p>
                    <p class="text-xs text-primary-100">Subcategories</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
              <div class="space-y-3">
                <router-link
                  to="/vendor/items/create"
                  class="flex items-center p-3 bg-primary-50 rounded-lg hover:bg-primary-100 transition-all group"
                >
                  <div class="p-2 bg-primary-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 group-hover:text-primary-700 text-sm">Add New Item</p>
                    <p class="text-xs text-gray-600">Create menu item</p>
                  </div>
                </router-link>

                <router-link
                  to="/vendor/businesses"
                  class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all group"
                >
                  <div class="p-2 bg-blue-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 group-hover:text-blue-700 text-sm">Manage Businesses</p>
                    <p class="text-xs text-gray-600">{{ statistics.businesses?.total || 0 }} businesses</p>
                  </div>
                </router-link>

                <router-link
                  to="/vendor/tables"
                  class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-all group"
                >
                  <div class="p-2 bg-purple-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 group-hover:text-purple-700 text-sm">Table Management</p>
                    <p class="text-xs text-gray-600">{{ statistics.tables?.total || 0 }} tables</p>
                  </div>
                </router-link>

                <router-link
                  to="/vendor/servers"
                  class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-all group"
                >
                  <div class="p-2 bg-orange-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 group-hover:text-orange-700 text-sm">Server Management</p>
                    <p class="text-xs text-gray-600">{{ statistics.servers?.total || 0 }} servers</p>
                  </div>
                </router-link>
              </div>
            </div>
          </div>

          <!-- Businesses List -->
          <div v-if="statistics.businesses?.list?.length" class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg font-semibold text-gray-900">Your Businesses</h2>
              <router-link
                to="/vendor/businesses"
                class="text-sm text-primary-600 hover:text-primary-700 font-medium"
              >
                Manage All
              </router-link>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="business in statistics.businesses.list.slice(0, 3)"
                :key="business.id"
                class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-all"
              >
                <h3 class="font-semibold text-gray-900">{{ business.business_name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ business.business_type || 'Restaurant' }}</p>
                <div class="mt-3 text-xs text-gray-600">
                  <span class="inline-block px-2 py-1 bg-gray-100 rounded">{{ business.business_link }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </VendorLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import VendorLayout from '@/components/layouts/VendorLayout.vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const authStore = useAuthStore()
const { vendor } = useApi()
const toast = useToast()

const loading = ref(true)
const statistics = ref({})
const orderStatistics = ref({})

// Chart refs
const itemGrowthChartRef = ref(null)
const itemsCategoryChartRef = ref(null)
const revenueChartRef = ref(null)
const topServersChartRef = ref(null)

// Chart instances
let itemGrowthChart = null
let itemsCategoryChart = null
let revenueChart = null
let topServersChart = null

const getBusinessLimit = () => {
  const tier = authStore.user?.subscription_tier || 'free'
  switch (tier) {
    case 'free':
      return 3
    case 'pro':
      return 5
    case 'max':
      return 'Unlimited'
    default:
      return 3
  }
}

const loadDashboard = async () => {
  try {
    const response = await vendor.getDashboardStatistics()
    const data = response.data.data || response.data
    statistics.value = data

    console.log('Dashboard statistics:', statistics.value)

    // Load order statistics
    try {
      const orderResponse = await vendor.getOrderStatistics()
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
  createRevenueChart()
  createTopServersChart()
  createItemGrowthChart()
  createItemsCategoryChart()
}

const destroyCharts = () => {
  if (revenueChart) revenueChart.destroy()
  if (topServersChart) topServersChart.destroy()
  if (itemGrowthChart) itemGrowthChart.destroy()
  if (itemsCategoryChart) itemsCategoryChart.destroy()
}

const createRevenueChart = () => {
  if (!revenueChartRef.value || !orderStatistics.value.revenue_by_day?.length) return

  const ctx = revenueChartRef.value.getContext('2d')
  const dates = orderStatistics.value.revenue_by_day.map(item => {
    const date = new Date(item.date)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  })

  revenueChart = new Chart(ctx, {
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

const createTopServersChart = () => {
  if (!topServersChartRef.value || !orderStatistics.value.top_servers?.length) return

  const ctx = topServersChartRef.value.getContext('2d')
  const serverData = orderStatistics.value.top_servers.slice(0, 5) // Top 5 servers

  topServersChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: serverData.map(server => {
        const firstName = server.server?.first_name || 'Server'
        const lastName = server.server?.last_name || ''
        return `${firstName} ${lastName}`.trim()
      }),
      datasets: [{
        label: 'Sales ($)',
        data: serverData.map(server => parseFloat(server.total_sales)),
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

const createItemGrowthChart = () => {
  if (!itemGrowthChartRef.value || !statistics.value.growth?.items) return

  const ctx = itemGrowthChartRef.value.getContext('2d')
  const dates = statistics.value.growth.items.map(item => {
    const date = new Date(item.date)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  })

  itemGrowthChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: dates,
      datasets: [{
        label: 'Items Created',
        data: statistics.value.growth.items.map(item => item.count),
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

const createItemsCategoryChart = () => {
  if (!itemsCategoryChartRef.value || !statistics.value.items?.by_category?.length) return

  const ctx = itemsCategoryChartRef.value.getContext('2d')
  const itemsByCategory = statistics.value.items.by_category.slice(0, 5) // Top 5 categories

  itemsCategoryChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: itemsByCategory.map(item => item.category?.category_name || 'Uncategorized'),
      datasets: [{
        data: itemsByCategory.map(item => item.count),
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
          position: 'bottom'
        }
      }
    }
  })
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
