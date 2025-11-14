<template>
  <VendorLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Orders & Transactions</h1>
            <p class="text-gray-600 mt-1">Manage your restaurant orders</p>
          </div>
          <button
            @click="refreshOrders"
            :disabled="refreshing"
            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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
                <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">${{ parseFloat(statistics.total_revenue || 0).toFixed(2) }}</p>
              </div>
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Today's Orders</p>
                <p class="text-2xl font-bold text-gray-900">{{ statistics.today_orders || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Today's Revenue</p>
                <p class="text-2xl font-bold text-gray-900">${{ parseFloat(statistics.today_revenue || 0).toFixed(2) }}</p>
              </div>
              <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
              <input
                v-model="filters.from_date"
                type="date"
                @change="loadOrders"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
              <input
                v-model="filters.to_date"
                type="date"
                @change="loadOrders"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <LoadingSpinner size="lg" text="Loading orders..." />
        </div>

        <!-- Orders Table -->
        <div v-else class="bg-white rounded-xl shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Order #
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Table
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Server
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Items
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Payment
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ order.order_number }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ order.customer_name || 'Guest' }}</div>
                    <div v-if="order.customer_phone" class="text-xs text-gray-500">{{ order.customer_phone }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ order.table?.table_name || '-' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ order.server ? `${order.server.first_name} ${order.server.last_name}` : '-' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ order.order_items?.length || 0 }} items</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">${{ order.total }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(order.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                      {{ getStatusText(order.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getPaymentStatusClass(order.payment_status)" class="px-2 py-1 text-xs font-medium rounded-full">
                      {{ order.payment_status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(order.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button
                      @click="viewOrder(order)"
                      class="text-primary-600 hover:text-primary-900 mr-3"
                    >
                      View
                    </button>
                    <button
                      @click="updateOrderStatus(order)"
                      class="text-green-600 hover:text-green-900 mr-3"
                    >
                      Status
                    </button>
                    <button
                      @click="updateOrderPaymentStatus(order)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Payment
                    </button>
                  </td>
                </tr>

                <tr v-if="!orders.data || orders.data.length === 0">
                  <td colspan="10" class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                      <div class="text-4xl mb-2">📋</div>
                      <p>No orders found</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="orders.data && orders.data.length > 0" class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t">
            <div class="text-sm text-gray-700">
              Showing {{ orders.from }} to {{ orders.to }} of {{ orders.total }} orders
            </div>
            <div class="flex gap-2">
              <button
                v-for="link in orders.links"
                :key="link.label"
                @click="changePage(link.url)"
                :disabled="!link.url"
                :class="[
                  'px-3 py-1 text-sm rounded',
                  link.active ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                  !link.url && 'opacity-50 cursor-not-allowed'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <Teleport to="body">
      <div
        v-if="selectedOrder"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        @click="selectedOrder = null"
      >
        <div
          @click.stop
          class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto"
        >
          <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Order Details</h2>
            <button @click="selectedOrder = null" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="p-6 space-y-6">
            <!-- Order Info -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Information</h3>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-600">Order Number</p>
                  <p class="font-medium">{{ selectedOrder.order_number }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Date</p>
                  <p class="font-medium">{{ formatDate(selectedOrder.created_at) }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Customer</p>
                  <p class="font-medium">{{ selectedOrder.customer_name || 'Guest' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Table</p>
                  <p class="font-medium">{{ selectedOrder.table?.table_name || '-' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Server</p>
                  <p class="font-medium">
                    {{ selectedOrder.server ? `${selectedOrder.server.first_name} ${selectedOrder.server.last_name}` : '-' }}
                  </p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Payment Method</p>
                  <p class="font-medium capitalize">{{ selectedOrder.payment_method.replace('_', ' ') }}</p>
                </div>
              </div>
              <div v-if="selectedOrder.notes" class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-3">
                <p class="text-sm font-medium text-gray-700">Special Instructions:</p>
                <p class="text-sm text-gray-600">{{ selectedOrder.notes }}</p>
              </div>
            </div>

            <!-- Order Items -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Items</h3>
              <div class="space-y-2">
                <div
                  v-for="item in selectedOrder.order_items"
                  :key="item.id"
                  class="flex justify-between items-start p-3 bg-gray-50 rounded"
                >
                  <div class="flex-1">
                    <p class="font-medium">{{ item.item?.title }}</p>
                    <p class="text-sm text-gray-600">Qty: {{ item.quantity }} × ${{ item.unit_price }}</p>
                    <p v-if="item.notes" class="text-xs text-gray-500 italic mt-1">{{ item.notes }}</p>
                  </div>
                  <p class="font-semibold">${{ item.subtotal }}</p>
                </div>
              </div>

              <div class="mt-4 space-y-2 border-t pt-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Subtotal</span>
                  <span class="font-semibold">${{ selectedOrder.subtotal }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Tax</span>
                  <span class="font-semibold">${{ selectedOrder.tax }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                  <span>Total</span>
                  <span class="text-primary-600">${{ selectedOrder.total }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Update Status Modal -->
    <Teleport to="body">
      <div
        v-if="statusUpdateOrder"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        @click="statusUpdateOrder = null"
      >
        <div
          @click.stop
          class="bg-white rounded-xl shadow-xl max-w-md w-full p-6"
        >
          <h2 class="text-xl font-bold text-gray-900 mb-4">Update Order Status</h2>

          <div class="space-y-3 mb-6">
            <label
              v-for="status in orderStatuses"
              :key="status.value"
              class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
              :class="newStatus === status.value ? 'border-primary-600 bg-primary-50' : 'border-gray-200 hover:border-gray-300'"
            >
              <input
                type="radio"
                v-model="newStatus"
                :value="status.value"
                class="w-4 h-4 text-primary-600"
              />
              <div class="ml-3">
                <div class="font-medium text-gray-900">{{ status.label }}</div>
                <div class="text-sm text-gray-600">{{ status.description }}</div>
              </div>
            </label>
          </div>

          <div class="flex gap-3">
            <button
              @click="statusUpdateOrder = null"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="confirmStatusUpdate"
              :disabled="updatingStatus"
              class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50"
            >
              <span v-if="updatingStatus">Updating...</span>
              <span v-else>Update</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Update Payment Status Modal -->
    <Teleport to="body">
      <div
        v-if="paymentStatusUpdateOrder"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        @click="paymentStatusUpdateOrder = null"
      >
        <div
          @click.stop
          class="bg-white rounded-xl shadow-xl max-w-md w-full p-6"
        >
          <h2 class="text-xl font-bold text-gray-900 mb-4">Update Payment Status</h2>

          <div class="space-y-3 mb-6">
            <label
              v-for="status in paymentStatuses"
              :key="status.value"
              class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
              :class="newPaymentStatus === status.value ? 'border-primary-600 bg-primary-50' : 'border-gray-200 hover:border-gray-300'"
            >
              <input
                type="radio"
                v-model="newPaymentStatus"
                :value="status.value"
                class="w-4 h-4 text-primary-600"
              />
              <div class="ml-3">
                <div class="font-medium text-gray-900">{{ status.label }}</div>
                <div class="text-sm text-gray-600">{{ status.description }}</div>
              </div>
            </label>
          </div>

          <div class="flex gap-3">
            <button
              @click="paymentStatusUpdateOrder = null"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              @click="confirmPaymentStatusUpdate"
              :disabled="updatingPaymentStatus"
              class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50"
            >
              <span v-if="updatingPaymentStatus">Updating...</span>
              <span v-else>Update</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </VendorLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import VendorLayout from '@/components/layouts/VendorLayout.vue'

const { vendor } = useApi()
const toast = useToast()

const loading = ref(true)
const refreshing = ref(false)
const orders = ref({ data: [], links: [] })
const statistics = ref({})
const selectedOrder = ref(null)
const statusUpdateOrder = ref(null)
const newStatus = ref('')
const updatingStatus = ref(false)
const paymentStatusUpdateOrder = ref(null)
const newPaymentStatus = ref('')
const updatingPaymentStatus = ref(false)

const filters = ref({
  status: 'all',
  payment_status: 'all',
  from_date: '',
  to_date: ''
})

const orderStatuses = [
  { value: 'pending', label: 'Pending', description: 'Order received' },
  { value: 'confirmed', label: 'Confirmed', description: 'Order confirmed' },
  { value: 'preparing', label: 'Preparing', description: 'Food is being prepared' },
  { value: 'served', label: 'Served', description: 'Food has been served' },
  { value: 'completed', label: 'Completed', description: 'Order completed' },
  { value: 'cancelled', label: 'Cancelled', description: 'Order cancelled' }
]

const paymentStatuses = [
  { value: 'pending', label: 'Pending', description: 'Payment not yet received' },
  { value: 'paid', label: 'Paid', description: 'Payment completed' },
  { value: 'failed', label: 'Failed', description: 'Payment failed' }
]

const loadOrders = async () => {
  try {
    loading.value = true
    // Filter out empty values
    const params = Object.fromEntries(
      Object.entries(filters.value).filter(([_, value]) => value !== '' && value !== null && value !== undefined)
    )
    const response = await vendor.getOrders(params)
    orders.value = response.data.data || response.data
  } catch (error) {
    console.error('Error loading orders:', error)
    toast.error('Failed to load orders')
  } finally {
    loading.value = false
  }
}

const loadStatistics = async () => {
  try {
    const response = await vendor.getOrderStatistics()
    statistics.value = response.data.data || response.data
  } catch (error) {
    console.error('Error loading statistics:', error)
    toast.error('Failed to load order statistics')
  }
}

const refreshOrders = async () => {
  refreshing.value = true
  await Promise.all([loadOrders(), loadStatistics()])
  refreshing.value = false
  toast.success('Orders refreshed')
}

const changePage = (url) => {
  if (!url) return
  // Extract page number from URL and load
  loadOrders()
}

const viewOrder = (order) => {
  selectedOrder.value = order
}

const updateOrderStatus = (order) => {
  statusUpdateOrder.value = order
  newStatus.value = order.status
}

const updateOrderPaymentStatus = (order) => {
  paymentStatusUpdateOrder.value = order
  newPaymentStatus.value = order.payment_status
}

const confirmStatusUpdate = async () => {
  try {
    updatingStatus.value = true
    await vendor.updateOrderStatus(statusUpdateOrder.value.id, {
      status: newStatus.value
    })
    toast.success('Order status updated')
    statusUpdateOrder.value = null
    await loadOrders()
  } catch (error) {
    console.error('Error updating status:', error)
    toast.error('Failed to update status')
  } finally {
    updatingStatus.value = false
  }
}

const confirmPaymentStatusUpdate = async () => {
  try {
    updatingPaymentStatus.value = true
    await vendor.updatePaymentStatus(paymentStatusUpdateOrder.value.id, {
      payment_status: newPaymentStatus.value
    })
    toast.success('Payment status updated')
    paymentStatusUpdateOrder.value = null
    await loadOrders()
  } catch (error) {
    console.error('Error updating payment status:', error)
    toast.error('Failed to update payment status')
  } finally {
    updatingPaymentStatus.value = false
  }
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    preparing: 'bg-purple-100 text-purple-800',
    served: 'bg-green-100 text-green-800',
    completed: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Pending',
    confirmed: 'Confirmed',
    preparing: 'Preparing',
    served: 'Served',
    completed: 'Completed',
    cancelled: 'Cancelled'
  }
  return texts[status] || status
}

const getPaymentStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    paid: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

onMounted(() => {
  loadOrders()
  loadStatistics()
})
</script>
