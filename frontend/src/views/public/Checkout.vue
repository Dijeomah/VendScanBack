<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-6">
        <button
          @click="goBack"
          class="text-primary-600 hover:text-primary-700 flex items-center gap-2 mb-4"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Back to Menu
        </button>
        <div class="flex items-center gap-3">
          <img
            v-if="checkoutData?.business?.business_data?.logo_image"
            :src="checkoutData.business.business_data.logo_image"
            :alt="checkoutData.business.business_data?.business_name || 'Logo'"
            class="w-16 h-16 rounded-full object-cover border-2 border-gray-200"
          />
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="text-gray-600">{{ checkoutData?.business?.business_data?.business_name || checkoutData?.business?.business_link }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Form -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Customer Information -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Customer Information</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name (Optional)</label>
                <input
                  v-model="form.customer_name"
                  type="text"
                  placeholder="Enter your name"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone (Optional)</label>
                <input
                  v-model="form.customer_phone"
                  type="tel"
                  placeholder="Enter your phone number"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions (Optional)</label>
                <textarea
                  v-model="form.notes"
                  rows="3"
                  placeholder="Any special requests?"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Payment Options -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Options</h2>
            <div class="space-y-3">
              <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all" :class="form.payment_method === 'pay_before' ? 'border-primary-600 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                <input
                  type="radio"
                  v-model="form.payment_method"
                  value="pay_before"
                  class="w-5 h-5 text-primary-600"
                />
                <div class="ml-3 flex-1">
                  <div class="font-semibold text-gray-900">Pay Now</div>
                  <div class="text-sm text-gray-600">Complete payment before your meal</div>
                </div>
              </label>

              <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all" :class="form.payment_method === 'pay_after' ? 'border-primary-600 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                <input
                  type="radio"
                  v-model="form.payment_method"
                  value="pay_after"
                  class="w-5 h-5 text-primary-600"
                />
                <div class="ml-3 flex-1">
                  <div class="font-semibold text-gray-900">Pay After Service</div>
                  <div class="text-sm text-gray-600">Pay when you're done</div>
                </div>
              </label>
            </div>
          </div>

          <!-- Payment Method (only show if pay_before) -->
          <div v-if="form.payment_method === 'pay_before'" class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
            <div class="grid grid-cols-2 gap-3">
              <button
                v-for="method in paymentMethods"
                :key="method.value"
                @click="form.payment_type = method.value"
                :class="[
                  'p-4 border-2 rounded-lg text-center transition-all',
                  form.payment_type === method.value
                    ? 'border-primary-600 bg-primary-50'
                    : 'border-gray-200 hover:border-gray-300'
                ]"
              >
                <div class="text-2xl mb-2">{{ method.icon }}</div>
                <div class="font-semibold text-sm">{{ method.label }}</div>
              </button>
            </div>
          </div>

          <!-- Card Payment Form -->
          <div v-if="form.payment_method === 'pay_before' && form.payment_type === 'card'" class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Card Details</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                <input
                  v-model="cardForm.card_number"
                  type="text"
                  placeholder="1234 5678 9012 3456"
                  maxlength="19"
                  @input="formatCardNumber"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                <input
                  v-model="cardForm.card_name"
                  type="text"
                  placeholder="JOHN DOE"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                  <input
                    v-model="cardForm.expiry_date"
                    type="text"
                    placeholder="MM/YY"
                    maxlength="5"
                    @input="formatExpiryDate"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                  <input
                    v-model="cardForm.cvv"
                    type="text"
                    placeholder="123"
                    maxlength="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Flutterwave Payment Info -->
          <div v-if="form.payment_method === 'pay_before' && form.payment_type === 'flutterwave'" class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Flutterwave Payment</h2>
            <div class="space-y-4">
              <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                  <h3 class="font-semibold text-blue-900 mb-1">Secure Payment with Flutterwave</h3>
                  <p class="text-sm text-blue-700">
                    You will be redirected to Flutterwave's secure payment page to complete your transaction.
                    Flutterwave supports card payments, bank transfers, and USSD.
                  </p>
                </div>
              </div>
              <div class="text-sm text-gray-600">
                <p class="mb-2"><strong>Accepted Payment Methods:</strong></p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                  <li>Debit/Credit Cards (Visa, Mastercard, Verve)</li>
                  <li>Bank Transfer</li>
                  <li>USSD</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>

            <!-- Cart Items -->
            <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
              <div
                v-for="(item, index) in checkoutData?.cart"
                :key="index"
                class="flex justify-between text-sm"
              >
                <div class="flex-1">
                  <div class="font-medium text-gray-900">{{ item.item.title }}</div>
                  <div class="text-gray-600">Qty: {{ item.quantity }} × ${{ item.item.price }}</div>
                  <div v-if="item.notes" class="text-xs text-gray-500 italic">{{ item.notes }}</div>
                </div>
                <div class="font-semibold text-gray-900">${{ (item.item.price * item.quantity).toFixed(2) }}</div>
              </div>
            </div>

            <div class="border-t pt-4 space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold">${{ checkoutData?.subtotal?.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax (10%)</span>
                <span class="font-semibold">${{ checkoutData?.tax?.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold border-t pt-2">
                <span>Total</span>
                <span class="text-primary-600">${{ checkoutData?.total?.toFixed(2) }}</span>
              </div>
            </div>

            <button
              @click="placeOrder"
              :disabled="processing"
              class="w-full mt-6 bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition-all font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="processing" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
              </span>
              <span v-else>Place Order</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const { public: publicApi } = useApi()
const toast = useToast()

const checkoutData = ref(null)
const processing = ref(false)
const userLocation = ref({
  latitude: null,
  longitude: null,
  error: null
})

const form = ref({
  customer_name: '',
  customer_phone: '',
  notes: '',
  payment_method: 'pay_after',
  payment_type: 'card'
})

const cardForm = ref({
  card_number: '',
  card_name: '',
  expiry_date: '',
  cvv: ''
})

const paymentMethods = [
  { value: 'card', label: 'Card', icon: '💳' },
  { value: 'flutterwave', label: 'Flutterwave', icon: '💸' }
]

const formatCardNumber = (event) => {
  let value = event.target.value.replace(/\s/g, '')
  let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value
  cardForm.value.card_number = formattedValue
}

const formatExpiryDate = (event) => {
  let value = event.target.value.replace(/\//g, '')
  if (value.length >= 2) {
    value = value.substring(0, 2) + '/' + value.substring(2, 4)
  }
  cardForm.value.expiry_date = value
}

const requestLocation = () => {
  if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userLocation.value.latitude = position.coords.latitude
        userLocation.value.longitude = position.coords.longitude
        userLocation.value.error = null
        console.log('Location captured:', position.coords.latitude, position.coords.longitude)
      },
      (error) => {
        console.warn('Location error:', error.message)
        userLocation.value.error = error.message
        // Don't show error toast yet - only if geofencing is required
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      }
    )
  } else {
    console.warn('Geolocation not supported')
    userLocation.value.error = 'Geolocation not supported by your browser'
  }
}

const goBack = () => {
  // Check if we're on a subdomain
  const hostname = window.location.hostname
  const parts = hostname.split('.')
  const isSubdomain = parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www'

  if (isSubdomain) {
    // Subdomain routing - go to root
    router.push('/')
  } else {
    // Path-based routing - go back
    router.back()
  }
}

const validateCardForm = () => {
  if (!cardForm.value.card_number.replace(/\s/g, '')) {
    toast.error('Please enter card number')
    return false
  }
  if (cardForm.value.card_number.replace(/\s/g, '').length < 13) {
    toast.error('Please enter a valid card number')
    return false
  }
  if (!cardForm.value.card_name) {
    toast.error('Please enter cardholder name')
    return false
  }
  if (!cardForm.value.expiry_date || cardForm.value.expiry_date.length !== 5) {
    toast.error('Please enter a valid expiry date (MM/YY)')
    return false
  }
  if (!cardForm.value.cvv || cardForm.value.cvv.length < 3) {
    toast.error('Please enter a valid CVV')
    return false
  }
  return true
}

const placeOrder = async () => {
  if (!checkoutData.value || !checkoutData.value.cart || checkoutData.value.cart.length === 0) {
    toast.error('Your cart is empty')
    return
  }

  // Validate card form if card payment is selected
  if (form.value.payment_method === 'pay_before' && form.value.payment_type === 'card') {
    if (!validateCardForm()) {
      return
    }
  }

  try {
    processing.value = true

    // Prepare order data
    const orderData = {
      business_link: checkoutData.value.business.business_link,
      table_id: checkoutData.value.table?.id || null,
      customer_name: form.value.customer_name || null,
      customer_phone: form.value.customer_phone || null,
      customer_latitude: userLocation.value.latitude,
      customer_longitude: userLocation.value.longitude,
      notes: form.value.notes || null,
      payment_method: form.value.payment_method,
      items: checkoutData.value.cart.map(item => ({
        item_id: item.item.id,
        quantity: item.quantity,
        notes: item.notes || null
      }))
    }

    // Create order
    const orderResponse = await publicApi.createOrder(orderData)
    const order = orderResponse.data.data || orderResponse.data

    // If pay_before, process payment immediately
    if (form.value.payment_method === 'pay_before') {
      if (form.value.payment_type === 'card') {
        // Process card payment
        const [expiryMonth, expiryYear] = cardForm.value.expiry_date.split('/')
        const paymentData = {
          payment_method: 'card',
          card_number: cardForm.value.card_number.replace(/\s/g, ''),
          card_name: cardForm.value.card_name,
          expiry_month: expiryMonth,
          expiry_year: '20' + expiryYear,
          cvv: cardForm.value.cvv
        }

        try {
          await publicApi.processPayment(order.id, paymentData)
          toast.success('Payment processed successfully!')
        } catch (paymentError) {
          toast.error(paymentError.response?.data?.message || 'Payment failed. Please try again.')
          processing.value = false
          return
        }
      } else if (form.value.payment_type === 'flutterwave') {
        // Initiate Flutterwave payment
        const flutterwaveData = {
          payment_method: 'flutterwave',
          amount: checkoutData.value.total,
          email: form.value.customer_phone ? `${form.value.customer_phone}@vendscan.com` : 'customer@vendscan.com',
          phone: form.value.customer_phone || '',
          name: form.value.customer_name || 'Customer'
        }

        try {
          const paymentResponse = await publicApi.processPayment(order.id, flutterwaveData)

          // Check if there's a redirect URL from Flutterwave
          if (paymentResponse.data?.payment_url) {
            // Save order info before redirecting
            localStorage.setItem('pending_order', JSON.stringify({
              order_number: order.order_number,
              business_link: checkoutData.value.business.business_link
            }))

            // Redirect to Flutterwave payment page
            window.location.href = paymentResponse.data.payment_url
            return
          }
        } catch (paymentError) {
          toast.error(paymentError.response?.data?.message || 'Failed to initiate Flutterwave payment')
          processing.value = false
          return
        }
      }
    }

    // Clear cart
    localStorage.removeItem(`cart_${checkoutData.value.business.business_link}`)
    localStorage.removeItem('checkout_data')

    // Check if we're on a subdomain
    const hostname = window.location.hostname
    const parts = hostname.split('.')
    const isSubdomain = parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www'

    // Redirect to order confirmation
    if (isSubdomain) {
      // Subdomain routing - use path
      router.push(`/order/${order.order_number}`)
    } else {
      // Path-based routing - use named route
      router.push({
        name: 'OrderConfirmation',
        params: { orderNumber: order.order_number }
      })
    }

    toast.success('Order placed successfully!')
  } catch (error) {
    console.error('Error placing order:', error)
    toast.error(error.response?.data?.message || 'Failed to place order')
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  const data = localStorage.getItem('checkout_data')
  if (data) {
    checkoutData.value = JSON.parse(data)
  } else {
    toast.error('No checkout data found')
    router.push({ name: 'Menu', params: { businessLink: route.params.businessLink } })
  }

  // Request user location for geofencing
  requestLocation()
})
</script>
