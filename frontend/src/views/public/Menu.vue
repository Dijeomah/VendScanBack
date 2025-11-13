<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header with Business Info -->
    <div class="bg-white shadow-sm sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ business?.business_name || 'Menu' }}</h1>
            <p v-if="table" class="text-sm text-gray-600">Table: {{ table.table_name }}</p>
          </div>
          <button
            @click="showCart = true"
            class="relative bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-all flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Cart</span>
            <span v-if="cartItemsCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold">
              {{ cartItemsCount }}
            </span>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
    </div>

    <!-- Menu Content -->
    <div v-else-if="categories.length > 0" class="max-w-7xl mx-auto px-4 py-6">
      <!-- Category Tabs -->
      <div class="mb-6 overflow-x-auto">
        <div class="flex gap-2 pb-2">
          <button
            v-for="category in categories"
            :key="category.id"
            @click="selectedCategory = category.id"
            :class="[
              'px-4 py-2 rounded-lg font-medium whitespace-nowrap transition-all',
              selectedCategory === category.id
                ? 'bg-primary-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            ]"
          >
            {{ category.category_name }}
          </button>
        </div>
      </div>

      <!-- Items Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="item in filteredItems"
          :key="item.id"
          class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all"
        >
          <div class="p-4">
            <div class="flex justify-between items-start mb-2">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ item.title }}</h3>
                <p v-if="item.sub_category" class="text-xs text-gray-500">{{ item.sub_category.sub_category_name }}</p>
              </div>
              <span class="text-lg font-bold text-primary-600">${{ item.price }}</span>
            </div>
            <p class="text-sm text-gray-600 mb-4">{{ item.description }}</p>

            <!-- Add to Cart Button -->
            <button
              @click="addToCart(item)"
              class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Add to Cart
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredItems.length === 0" class="text-center py-20">
        <div class="text-6xl mb-4">🍽️</div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No items in this category</h3>
        <p class="text-gray-600">Try selecting a different category</p>
      </div>
    </div>

    <!-- Empty Menu State -->
    <div v-else class="text-center py-20">
      <div class="text-6xl mb-4">📋</div>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">Menu not available</h3>
      <p class="text-gray-600">Please check back later</p>
    </div>

    <!-- Cart Sidebar -->
    <Teleport to="body">
      <div
        v-if="showCart"
        class="fixed inset-0 bg-black bg-opacity-50 z-50"
        @click="showCart = false"
      >
        <div
          @click.stop
          class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-xl flex flex-col"
        >
          <!-- Cart Header -->
          <div class="bg-primary-600 text-white p-4 flex items-center justify-between">
            <h2 class="text-xl font-bold">Your Cart</h2>
            <button @click="showCart = false" class="text-white hover:text-gray-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Cart Items -->
          <div class="flex-1 overflow-y-auto p-4">
            <div v-if="cart.length === 0" class="text-center py-20">
              <div class="text-6xl mb-4">🛒</div>
              <p class="text-gray-600">Your cart is empty</p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="(cartItem, index) in cart"
                :key="index"
                class="bg-gray-50 rounded-lg p-4"
              >
                <div class="flex justify-between items-start mb-2">
                  <div class="flex-1">
                    <h3 class="font-semibold text-gray-900">{{ cartItem.item.title }}</h3>
                    <p class="text-sm text-gray-600">${{ cartItem.item.price }} each</p>
                  </div>
                  <button
                    @click="removeFromCart(index)"
                    class="text-red-600 hover:text-red-700"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>

                <!-- Quantity Controls -->
                <div class="flex items-center gap-3 mb-2">
                  <button
                    @click="updateQuantity(index, cartItem.quantity - 1)"
                    class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <span class="font-semibold">{{ cartItem.quantity }}</span>
                  <button
                    @click="updateQuantity(index, cartItem.quantity + 1)"
                    class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  </button>
                  <span class="ml-auto font-bold text-primary-600">${{ (cartItem.item.price * cartItem.quantity).toFixed(2) }}</span>
                </div>

                <!-- Item Notes -->
                <input
                  v-model="cartItem.notes"
                  type="text"
                  placeholder="Special instructions (optional)"
                  class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </div>
            </div>
          </div>

          <!-- Cart Footer -->
          <div v-if="cart.length > 0" class="border-t p-4 bg-white">
            <div class="space-y-2 mb-4">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold">${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax (10%)</span>
                <span class="font-semibold">${{ tax.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold border-t pt-2">
                <span>Total</span>
                <span class="text-primary-600">${{ total.toFixed(2) }}</span>
              </div>
            </div>

            <button
              @click="proceedToCheckout"
              class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition-all font-semibold"
            >
              Proceed to Checkout
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const { public: publicApi } = useApi()
const toast = useToast()

const loading = ref(true)
const business = ref(null)
const table = ref(null)
const categories = ref([])
const items = ref([])
const selectedCategory = ref(null)
const cart = ref([])
const showCart = ref(false)

const businessLink = route.params.businessLink
const tableId = route.query.table

// Computed
const filteredItems = computed(() => {
  if (!selectedCategory.value) return items.value
  return items.value.filter(item => item.category_id === selectedCategory.value)
})

const cartItemsCount = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.quantity, 0)
})

const subtotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + (item.item.price * item.quantity), 0)
})

const tax = computed(() => subtotal.value * 0.1)

const total = computed(() => subtotal.value + tax.value)

// Methods
const loadMenu = async () => {
  try {
    loading.value = true
    const response = await publicApi.getMenu(businessLink)
    const data = response.data.data || response.data

    business.value = data.business
    categories.value = data.categories || []

    // Flatten items from grouped structure
    if (data.items) {
      items.value = Object.values(data.items).flat()
    }

    // Select first category by default
    if (categories.value.length > 0) {
      selectedCategory.value = categories.value[0].id
    }

    // Load table info if tableId is provided
    if (tableId) {
      const tableResponse = await publicApi.getTableInfo(tableId)
      table.value = tableResponse.data.data || tableResponse.data
    }

    // Load cart from localStorage
    const savedCart = localStorage.getItem(`cart_${businessLink}`)
    if (savedCart) {
      cart.value = JSON.parse(savedCart)
    }
  } catch (error) {
    console.error('Error loading menu:', error)
    toast.error('Failed to load menu')
  } finally {
    loading.value = false
  }
}

const addToCart = (item) => {
  const existingIndex = cart.value.findIndex(c => c.item.id === item.id)

  if (existingIndex >= 0) {
    cart.value[existingIndex].quantity++
  } else {
    cart.value.push({
      item: item,
      quantity: 1,
      notes: ''
    })
  }

  saveCart()
  toast.success(`${item.title} added to cart`)
  showCart.value = true
}

const removeFromCart = (index) => {
  const item = cart.value[index]
  cart.value.splice(index, 1)
  saveCart()
  toast.info(`${item.item.title} removed from cart`)
}

const updateQuantity = (index, newQuantity) => {
  if (newQuantity < 1) {
    removeFromCart(index)
    return
  }
  cart.value[index].quantity = newQuantity
  saveCart()
}

const saveCart = () => {
  localStorage.setItem(`cart_${businessLink}`, JSON.stringify(cart.value))
}

const proceedToCheckout = () => {
  if (cart.value.length === 0) {
    toast.warning('Your cart is empty')
    return
  }

  // Store cart data for checkout page
  localStorage.setItem('checkout_data', JSON.stringify({
    business: business.value,
    table: table.value,
    cart: cart.value,
    subtotal: subtotal.value,
    tax: tax.value,
    total: total.value
  }))

  router.push({
    name: 'Checkout',
    params: { businessLink },
    query: tableId ? { table: tableId } : {}
  })
}

onMounted(() => {
  loadMenu()
})
</script>
