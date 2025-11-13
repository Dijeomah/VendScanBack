<template>
  <ServerLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Server Dashboard</h1>
        <p class="text-gray-600 mt-1">Manage your assigned tables and orders</p>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 mb-1">Total Orders</p>
              <p class="text-2xl font-bold text-gray-900">{{ statistics.total_orders || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 mb-1">Today's Orders</p>
              <p class="text-2xl font-bold text-gray-900">{{ statistics.today_orders || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 mb-1">Pending Orders</p>
              <p class="text-2xl font-bold text-gray-900">{{ statistics.pending_orders || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 mb-1">Assigned Tables</p>
              <p class="text-2xl font-bold text-gray-900">{{ statistics.assigned_tables?.length || 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Assigned Tables & Businesses -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assigned Tables -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">My Tables</h2>
          <div v-if="statistics.assigned_tables?.length > 0" class="space-y-3">
            <div
              v-for="assignment in statistics.assigned_tables"
              :key="assignment.id"
              class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-semibold text-gray-900">{{ assignment.table?.table_name }}</p>
                  <p class="text-sm text-gray-600">{{ assignment.business?.business_data?.business_name || assignment.business?.business_link }}</p>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                  Active
                </span>
              </div>
            </div>
          </div>
          <p v-else class="text-center text-gray-500 py-8">No tables assigned yet</p>
        </div>

        <!-- Assigned Businesses -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">My Businesses</h2>
          <div v-if="statistics.assigned_businesses?.length > 0" class="space-y-3">
            <div
              v-for="assignment in statistics.assigned_businesses"
              :key="assignment.id"
              class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-semibold text-gray-900">{{ assignment.business?.business_data?.business_name || assignment.business?.business_link }}</p>
                  <p class="text-sm text-gray-600">{{ assignment.business?.business_link }}</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                  Active
                </span>
              </div>
            </div>
          </div>
          <p v-else class="text-center text-gray-500 py-8">No businesses assigned yet</p>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900">Recent Orders</h2>
          <router-link
            to="/server/orders"
            class="text-sm text-primary-600 hover:text-primary-700 font-medium"
          >
            View All →
          </router-link>
        </div>

        <div v-if="loading" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        </div>

        <div v-else-if="statistics.recent_orders?.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order #</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Table</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="order in statistics.recent_orders"
                :key="order.id"
                class="hover:bg-gray-50 cursor-pointer"
                @click="viewOrder(order.id)"
              >
                <td class="px-4 py-3 text-sm font-medium text-gray-900">#{{ order.order_number }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ order.table?.table_name || 'N/A' }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ order.order_items?.length || 0 }} items</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${{ parseFloat(order.total).toFixed(2) }}</td>
                <td class="px-4 py-3">
                  <span
                    :class="getStatusClass(order.status)"
                    class="px-2 py-1 rounded-full text-xs font-medium"
                  >
                    {{ formatStatus(order.status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ formatTime(order.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <p v-else class="text-center text-gray-500 py-12">No recent orders</p>
      </div>
    </div>
  </ServerLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import ServerLayout from '@/components/layouts/ServerLayout.vue'
import { formatDistanceToNow } from 'date-fns'

const router = useRouter()
const toast = useToast()
const { server } = useApi()

const loading = ref(true)
const statistics = ref({})

const loadDashboardStatistics = async () => {
  try {
    loading.value = true
    const response = await server.getDashboardStatistics()
    statistics.value = response.data?.data || response.data
  } catch (error) {
    console.error('Error loading dashboard statistics:', error)
    toast.error('Failed to load dashboard data')
  } finally {
    loading.value = false
  }
}

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-blue-100 text-blue-700',
    preparing: 'bg-purple-100 text-purple-700',
    served: 'bg-green-100 text-green-700',
    completed: 'bg-gray-100 text-gray-700',
    cancelled: 'bg-red-100 text-red-700',
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

const formatTime = (date) => {
  if (!date) return 'N/A'
  try {
    return formatDistanceToNow(new Date(date), { addSuffix: true })
  } catch {
    return 'N/A'
  }
}

const viewOrder = (orderId) => {
  router.push({ name: 'server-orders', query: { orderId } })
}

onMounted(() => {
  loadDashboardStatistics()
})
</script>
