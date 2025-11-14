<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <!-- Order Details -->
      <div v-else-if="order" class="space-y-6">
        <!-- Success Header -->
        <div class="bg-white rounded-xl shadow-sm p-8 text-center">
          <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
          <p class="text-gray-600 mb-4">Thank you for your order</p>
          <div class="inline-block bg-primary-100 text-primary-700 px-6 py-2 rounded-full font-semibold">
            Order #{{ order.order_number }}
          </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Order Status</h2>
            <div v-if="isActiveOrder" class="flex items-center gap-2 text-sm text-gray-600">
              <div class="animate-pulse w-2 h-2 bg-green-500 rounded-full"></div>
              <span>Live tracking</span>
            </div>
            <div v-else class="text-sm text-gray-500">
              <span>Order {{ order.status === 'completed' ? 'completed' : 'inactive' }}</span>
            </div>
          </div>

          <!-- Status Progress Timeline -->
          <div class="mb-6">
            <div class="flex justify-between items-center">
              <div
                v-for="(statusStep, index) in statusSteps"
                :key="statusStep.key"
                class="flex flex-col items-center flex-1"
                :class="{ 'relative': index < statusSteps.length - 1 }"
              >
                <!-- Step Circle -->
                <div class="relative z-10 flex items-center justify-center">
                  <div
                    :class="[
                      'w-10 h-10 rounded-full flex items-center justify-center transition-all',
                      getStepStatus(statusStep.key) === 'completed'
                        ? 'bg-green-500 text-white'
                        : getStepStatus(statusStep.key) === 'current'
                        ? 'bg-primary-600 text-white ring-4 ring-primary-100'
                        : 'bg-gray-200 text-gray-400'
                    ]"
                  >
                    <svg v-if="getStepStatus(statusStep.key) === 'completed'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span v-else class="text-xs font-bold">{{ index + 1 }}</span>
                  </div>
                </div>

                <!-- Line connecting to next step -->
                <div
                  v-if="index < statusSteps.length - 1"
                  class="absolute left-1/2 top-5 w-full h-0.5"
                  :class="getStepStatus(statusSteps[index + 1].key) !== 'pending' ? 'bg-green-500' : 'bg-gray-200'"
                ></div>

                <!-- Step Label -->
                <span
                  class="text-xs mt-2 text-center"
                  :class="getStepStatus(statusStep.key) !== 'pending' ? 'text-gray-900 font-medium' : 'text-gray-400'"
                >
                  {{ statusStep.label }}
                </span>
              </div>
            </div>
          </div>

          <!-- Current Status Details -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
              <span :class="[
                'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                getStatusClass(order.status)
              ]">
                {{ getStatusText(order.status) }}
              </span>
              <span :class="[
                'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                getPaymentStatusClass(order.payment_status)
              ]">
                {{ getPaymentStatusText(order.payment_status) }}
              </span>
            </div>
            <p class="text-sm text-gray-600">
              {{ getStatusDescription(order.status) }}
            </p>
          </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Details</h2>

          <div class="space-y-3 mb-4">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Business</span>
              <span class="font-medium">{{ order.business_link?.business_data?.business_name || order.business_link?.business_link }}</span>
            </div>
            <div v-if="order.table" class="flex justify-between text-sm">
              <span class="text-gray-600">Table</span>
              <span class="font-medium">{{ order.table.table_name }}</span>
            </div>
            <div v-if="order.server" class="flex justify-between text-sm">
              <span class="text-gray-600">Server</span>
              <span class="font-medium">{{ order.server.first_name }} {{ order.server.last_name }}</span>
            </div>
            <div v-if="order.customer_name" class="flex justify-between text-sm">
              <span class="text-gray-600">Customer</span>
              <span class="font-medium">{{ order.customer_name }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Order Time</span>
              <span class="font-medium">{{ formatDate(order.created_at) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Payment Method</span>
              <span class="font-medium">{{ order.payment_method === 'pay_before' ? 'Paid' : 'Pay After Service' }}</span>
            </div>
          </div>

          <div v-if="order.notes" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <p class="text-sm font-medium text-gray-700 mb-1">Special Instructions:</p>
            <p class="text-sm text-gray-600">{{ order.notes }}</p>
          </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Items Ordered</h2>

          <div class="space-y-3">
            <div
              v-for="item in order.order_items"
              :key="item.id"
              class="flex justify-between items-start pb-3 border-b last:border-0"
            >
              <div class="flex-1">
                <div class="font-medium text-gray-900">{{ item.item?.title }}</div>
                <div class="text-sm text-gray-600">{{ item.item?.category?.category_name }}</div>
                <div class="text-sm text-gray-600">Qty: {{ item.quantity }} × ${{ item.unit_price }}</div>
                <div v-if="item.notes" class="text-xs text-gray-500 italic mt-1">Note: {{ item.notes }}</div>
              </div>
              <div class="font-semibold text-gray-900">${{ item.subtotal }}</div>
            </div>
          </div>

          <div class="border-t pt-4 mt-4 space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Subtotal</span>
              <span class="font-semibold">${{ order.subtotal }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Tax (10%)</span>
              <span class="font-semibold">${{ order.tax }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-2">
              <span>Total</span>
              <span class="text-primary-600">${{ order.total }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Info (if paid) -->
        <div v-if="order.payment && order.payment.payment_status === 'completed'" class="bg-white rounded-xl shadow-sm p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Information</h2>

          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Transaction Reference</span>
              <span class="font-mono text-xs">{{ order.payment.transaction_reference }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Payment Method</span>
              <span class="font-medium capitalize">{{ order.payment.payment_method }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Payment Date</span>
              <span class="font-medium">{{ formatDate(order.payment.payment_at) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Amount Paid</span>
              <span class="font-bold text-green-600">${{ order.payment.amount }}</span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
          <button
            @click="goToMenu"
            class="flex-1 bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition-all font-semibold"
          >
            Back to Menu
          </button>
          <button
            @click="printReceipt"
            class="flex-1 bg-white border-2 border-primary-600 text-primary-600 py-3 rounded-lg hover:bg-primary-50 transition-all font-semibold"
          >
            Print Receipt
          </button>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="bg-white rounded-xl shadow-sm p-8 text-center">
        <div class="text-6xl mb-4">❌</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Order Not Found</h2>
        <p class="text-gray-600 mb-6">We couldn't find your order</p>
        <button
          @click="goToMenu"
          class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-all"
        >
          Go to Menu
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const { public: publicApi } = useApi()
const toast = useToast()

const loading = ref(true)
const order = ref(null)
const pollingInterval = ref(null)
const POLL_INTERVAL = 5000 // Poll every 5 seconds

const orderNumber = route.params.orderNumber

// Status steps for progress timeline
const statusSteps = [
  { key: 'pending', label: 'Received' },
  { key: 'confirmed', label: 'Confirmed' },
  { key: 'preparing', label: 'Preparing' },
  { key: 'served', label: 'Served' },
  { key: 'completed', label: 'Completed' }
]

// Check if order is active (should continue polling)
const isActiveOrder = computed(() => {
  if (!order.value) return false
  return !['completed', 'cancelled'].includes(order.value.status)
})

const loadOrder = async (showLoadingState = true) => {
  try {
    if (showLoadingState) {
      loading.value = true
    }
    const response = await publicApi.getOrder(orderNumber)
    const newOrder = response.data.data || response.data

    // Check if status changed
    if (order.value && newOrder.status !== order.value.status) {
      toast.info(`Order status updated to: ${getStatusText(newOrder.status)}`)
    }

    order.value = newOrder

    // Stop polling if order is completed or cancelled
    if (!isActiveOrder.value && pollingInterval.value) {
      clearInterval(pollingInterval.value)
      pollingInterval.value = null
    }
  } catch (error) {
    console.error('Error loading order:', error)
    if (showLoadingState) {
      toast.error('Failed to load order')
    }
  } finally {
    loading.value = false
  }
}

// Start polling for order updates
const startPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value)
  }

  if (isActiveOrder.value) {
    pollingInterval.value = setInterval(() => {
      loadOrder(false) // Don't show loading state for polls
    }, POLL_INTERVAL)
  }
}

// Determine the status of each step in the timeline
const getStepStatus = (stepKey) => {
  if (!order.value) return 'pending'

  const currentStatusIndex = statusSteps.findIndex(s => s.key === order.value.status)
  const stepIndex = statusSteps.findIndex(s => s.key === stepKey)

  if (stepIndex < currentStatusIndex) return 'completed'
  if (stepIndex === currentStatusIndex) return 'current'
  return 'pending'
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

const getStatusDescription = (status) => {
  const descriptions = {
    pending: 'Your order has been received and is waiting to be confirmed',
    confirmed: 'Your order has been confirmed and will be prepared soon',
    preparing: 'Your order is being prepared',
    served: 'Your order has been served',
    completed: 'Your order is complete. Thank you!',
    cancelled: 'This order has been cancelled'
  }
  return descriptions[status] || ''
}

const getPaymentStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    paid: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getPaymentStatusText = (status) => {
  const texts = {
    pending: 'Payment Pending',
    paid: 'Paid',
    failed: 'Payment Failed'
  }
  return texts[status] || status
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

const goToMenu = () => {
  // Check if we're on a subdomain
  const hostname = window.location.hostname
  const parts = hostname.split('.')
  const isSubdomain = parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www'

  if (isSubdomain) {
    // Subdomain routing - go to root
    router.push('/')
  } else if (order.value?.business_link) {
    // Path-based routing - go to menu with business link
    router.push({
      name: 'Menu',
      params: { businessLink: order.value.business_link.business_link }
    })
  } else {
    router.push({ name: 'Home' })
  }
}

const printReceipt = () => {
  window.print()
}

onMounted(async () => {
  await loadOrder()
  startPolling()
})

onUnmounted(() => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value)
    pollingInterval.value = null
  }
})
</script>

<style scoped>
@media print {
  .print\:hidden {
    display: none !important;
  }
}
</style>
