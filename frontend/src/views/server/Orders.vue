<template>
  <ServerLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
        <p class="text-gray-600 mt-1">Manage orders for your assigned tables</p>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="filters.status"
              @change="loadOrders"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
              <option value="all">All Status</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="preparing">Preparing</option>
              <option value="served">Served</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
            <select
              v-model="filters.payment_status"
              @change="loadOrders"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
              <option value="all">All</option>
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
            </select>
          </div>

          <div class="flex items-end">
            <button
              @click="refreshOrders"
              :disabled="refreshing"
              class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 flex items-center justify-center gap-2"
            >
              <svg v-if="!refreshing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <div v-else class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
              Refresh
            </button>
          </div>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div v-if="loading" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        </div>

        <div v-else-if="orders.length > 0" class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order #</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Table</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="order in orders"
                :key="order.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ order.order_number }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ order.table?.table_name || 'N/A' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ order.customer_name || 'Guest' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ order.order_items?.length || 0 }} items</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ parseFloat(order.total).toFixed(2) }}</td>
                <td class="px-6 py-4">
                  <span
                    :class="getPaymentStatusClass(order.payment_status)"
                    class="px-2 py-1 rounded-full text-xs font-medium"
                  >
                    {{ formatStatus(order.payment_status) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="getStatusClass(order.status)"
                    class="px-2 py-1 rounded-full text-xs font-medium"
                  >
                    {{ formatStatus(order.status) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <button
                      @click="viewOrderDetails(order)"
                      class="text-primary-600 hover:text-primary-700"
                      title="View Details"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <button
                      @click="openStatusModal(order)"
                      class="text-blue-600 hover:text-blue-700"
                      title="Update Status"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <p v-else class="text-center text-gray-500 py-12">No orders found</p>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">Order Details</h2>
          <p class="text-sm text-gray-600 mt-1">#{{ selectedOrder?.order_number }}</p>
        </div>
        <div class="p-6 space-y-6">
          <!-- Order Info -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-600">Table</p>
              <p class="font-medium text-gray-900">{{ selectedOrder?.table?.table_name || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Customer</p>
              <p class="font-medium text-gray-900">{{ selectedOrder?.customer_name || 'Guest' }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Status</p>
              <span
                :class="getStatusClass(selectedOrder?.status)"
                class="inline-block px-2 py-1 rounded-full text-xs font-medium mt-1"
              >
                {{ formatStatus(selectedOrder?.status) }}
              </span>
            </div>
            <div>
              <p class="text-sm text-gray-600">Payment Status</p>
              <span
                :class="getPaymentStatusClass(selectedOrder?.payment_status)"
                class="inline-block px-2 py-1 rounded-full text-xs font-medium mt-1"
              >
                {{ formatStatus(selectedOrder?.payment_status) }}
              </span>
            </div>
          </div>

          <!-- Order Items -->
          <div>
            <h3 class="font-semibold text-gray-900 mb-3">Order Items</h3>
            <div class="space-y-2">
              <div
                v-for="item in selectedOrder?.order_items"
                :key="item.id"
                class="flex justify-between items-center p-3 bg-gray-50 rounded-lg"
              >
                <div>
                  <p class="font-medium text-gray-900">{{ item.item?.title }}</p>
                  <p class="text-sm text-gray-600">Quantity: {{ item.quantity }}</p>
                </div>
                <p class="font-medium text-gray-900">${{ parseFloat(item.subtotal).toFixed(2) }}</p>
              </div>
            </div>
          </div>

          <!-- Order Total -->
          <div class="border-t border-gray-200 pt-4 space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Subtotal</span>
              <span class="text-gray-900">${{ parseFloat(selectedOrder?.subtotal || 0).toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Tax</span>
              <span class="text-gray-900">${{ parseFloat(selectedOrder?.tax || 0).toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
              <span>Total</span>
              <span>${{ parseFloat(selectedOrder?.total || 0).toFixed(2) }}</span>
            </div>
          </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
          <button
            @click="closeDetailsModal"
            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Status Update Modal -->
    <div v-if="showStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">Update Order Status</h2>
          <p class="text-sm text-gray-600 mt-1">#{{ selectedOrder?.order_number }}</p>
        </div>
        <div class="p-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
          <select
            v-model="newStatus"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="preparing">Preparing</option>
            <option value="served">Served</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
          <button
            @click="closeStatusModal"
            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all"
          >
            Cancel
          </button>
          <button
            @click="updateOrderStatus"
            :disabled="updating"
            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
          >
            {{ updating ? 'Updating...' : 'Update Status' }}
          </button>
        </div>
      </div>
    </div>
  </ServerLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import ServerLayout from '@/components/layouts/ServerLayout.vue'

const toast = useToast()
const { server } = useApi()

const loading = ref(true)
const refreshing = ref(false)
const updating = ref(false)
const orders = ref([])
const showDetailsModal = ref(false)
const showStatusModal = ref(false)
const selectedOrder = ref(null)
const newStatus = ref('')

const filters = ref({
  status: 'all',
  payment_status: 'all',
})

const loadOrders = async () => {
  try {
    loading.value = true
    const params = Object.fromEntries(
      Object.entries(filters.value).filter(([_, value]) => value !== '' && value !== null && value !== undefined && value !== 'all')
    )
    const response = await server.getOrders(params)
    const data = response.data?.data || response.data
    orders.value = data.data || data
  } catch (error) {
    console.error('Error loading orders:', error)
    toast.error('Failed to load orders')
  } finally {
    loading.value = false
  }
}

const refreshOrders = async () => {
  refreshing.value = true
  await loadOrders()
  refreshing.value = false
  toast.success('Orders refreshed')
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order
  showDetailsModal.value = true
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedOrder.value = null
}

const openStatusModal = (order) => {
  selectedOrder.value = order
  newStatus.value = order.status
  showStatusModal.value = true
}

const closeStatusModal = () => {
  showStatusModal.value = false
  selectedOrder.value = null
  newStatus.value = ''
}

const updateOrderStatus = async () => {
  try {
    updating.value = true
    await server.updateOrderStatus(selectedOrder.value.id, { status: newStatus.value })
    toast.success('Order status updated successfully')
    closeStatusModal()
    await loadOrders()
  } catch (error) {
    console.error('Error updating order status:', error)
    toast.error('Failed to update order status')
  } finally {
    updating.value = false
  }
}

const formatStatus = (status) => {
  return status?.charAt(0).toUpperCase() + status?.slice(1) || 'N/A'
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

const getPaymentStatusClass = (status) => {
  return status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
}

onMounted(() => {
  loadOrders()
})
</script>
