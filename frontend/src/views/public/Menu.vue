<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <!-- Header Section with Logo and Banner -->
    <div class="relative overflow-hidden">
      <!-- Background Header Image (if exists) -->
      <div
        v-if="business?.business_data?.header_image"
        class="absolute inset-0 opacity-20"
      >
        <img
          :src="business.business_data.header_image"
          :alt="business.business_data?.business_name || 'Business header'"
          class="w-full h-full object-cover"
        />
      </div>

      <!-- Menu Header Content -->
      <div class="relative z-10 max-w-7xl mx-auto px-4 py-8 md:py-12">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <!-- Left: Logo and Business Info -->
          <div class="flex flex-col items-center md:items-start gap-4 text-center md:text-left">
            <!-- Logo -->
            <div v-if="business?.business_data?.logo_image" class="relative">
              <div class="absolute inset-0 bg-amber-500 rounded-full blur-xl opacity-30"></div>
              <img
                :src="business.business_data.logo_image"
                :alt="business.business_data?.business_name || 'Logo'"
                class="relative w-24 h-24 md:w-32 md:h-32 rounded-full object-cover border-4 border-amber-500 shadow-2xl"
              />
            </div>

            <!-- Business Name and Title -->
            <div>
              <h1 class="text-5xl md:text-6xl font-bold text-white mb-2 font-serif tracking-wider">Menu</h1>
              <h2 class="text-2xl md:text-3xl text-amber-400 font-semibold">{{ business?.business_data?.business_name || business?.business_link || 'Restaurant' }}</h2>
              <p v-if="table" class="text-sm text-gray-300 mt-2">Table: {{ table.table_name }}</p>
            </div>
          </div>

          <!-- Right: Header Image Decoration -->
          <div v-if="business?.business_data?.header_image" class="hidden md:block">
            <div class="relative">
              <div class="absolute inset-0 bg-amber-500 rounded-full blur-2xl opacity-20"></div>
              <div class="relative w-48 h-48 rounded-full overflow-hidden border-4 border-amber-500 shadow-2xl">
                <img
                  :src="business.business_data.header_image"
                  :alt="business.business_data?.business_name || 'Business header'"
                  class="w-full h-full object-cover"
                />
              </div>
            </div>
          </div>

          <!-- Cart Button (Desktop Only - Mobile has sticky button below) -->
          <button
            @click="showCart = true"
            class="hidden md:flex bg-gradient-to-r from-amber-500 to-amber-600 text-white px-6 py-3 rounded-full hover:from-amber-600 hover:to-amber-700 transition-all items-center gap-2 shadow-2xl"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="font-semibold">Cart</span>
            <span v-if="cartItemsCount > 0" class="bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold">
              {{ cartItemsCount }}
            </span>
          </button>
        </div>

        <!-- Decorative Line -->
        <div class="mt-8 flex items-center justify-center gap-4">
          <div class="h-px bg-gradient-to-r from-transparent via-amber-500 to-transparent flex-1"></div>
          <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <div class="h-px bg-gradient-to-r from-transparent via-amber-500 to-transparent flex-1"></div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-500"></div>
    </div>

    <!-- Menu Content -->
    <div v-else-if="categories.length > 0" class="max-w-7xl mx-auto px-4 py-8 pb-32 md:pb-8">
      <!-- Category Tabs -->
      <div class="mb-8 -mx-4 px-4 overflow-x-auto overflow-y-hidden" style="scrollbar-width: thin; scrollbar-color: #f59e0b #1f2937;">
        <div class="flex md:justify-center gap-3 pb-2 min-w-max md:min-w-0">
          <button
            v-for="category in categories"
            :key="category.id"
            :data-category-id="category.id"
            @click="scrollToCategory(category.id)"
            :class="[
              'px-6 py-3 rounded-full font-semibold whitespace-nowrap transition-all transform hover:scale-105 flex-shrink-0',
              selectedCategory === category.id
                ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/50'
                : 'bg-gray-800 bg-opacity-50 text-gray-300 hover:bg-opacity-70 border border-amber-500/30'
            ]"
          >
            {{ category.category_name }}
          </button>
        </div>
      </div>

      <!-- Items List - Restaurant Menu Style -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div
          v-for="item in filteredItems"
          :key="item.id"
          class="group relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl overflow-hidden border border-amber-500/20 hover:border-amber-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-amber-500/20"
        >
          <!-- Item Content -->
          <div class="relative z-10 p-6">
            <!-- Item Header with Image -->
            <div class="flex gap-4 mb-4">
              <!-- Item Image Thumbnail -->
              <div v-if="item.image" class="flex-shrink-0">
                <div class="w-24 h-24 rounded-xl overflow-hidden border-2 border-amber-500/50 shadow-lg">
                  <img
                    :src="item.image"
                    :alt="item.title"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                    loading="lazy"
                  />
                </div>
              </div>

              <!-- Item Info -->
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start gap-3 mb-2">
                  <h3 class="text-xl font-bold text-white font-serif flex-1">{{ item.title }}</h3>
                  <div class="flex items-baseline gap-1 flex-shrink-0">
                    <span class="text-sm text-amber-400">$</span>
                    <span class="text-2xl font-bold text-amber-400">{{ item.price }}</span>
                  </div>
                </div>

                <p v-if="item.sub_category" class="text-xs text-amber-400/70 mb-2 uppercase tracking-wider">
                  {{ item.sub_category.sub_category_name }}
                </p>

                <p v-if="item.description" class="text-sm text-gray-300 leading-relaxed line-clamp-2">
                  {{ item.description }}
                </p>
              </div>
            </div>

            <!-- Decorative Dots Line -->
            <div class="border-t border-dashed border-amber-500/20 my-4"></div>

            <!-- Add to Cart Button -->
            <button
              @click="addToCart(item)"
              class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              <span>Add to Cart</span>
            </button>
          </div>

          <!-- Decorative Corner Accent -->
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/10 to-transparent rounded-bl-full"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredItems.length === 0" class="text-center py-20">
        <div class="text-6xl mb-4">🍽️</div>
        <h3 class="text-2xl font-semibold text-white mb-2">No items in this category</h3>
        <p class="text-gray-400">Try selecting a different category</p>
      </div>
    </div>

    <!-- Empty Menu State -->
    <div v-else class="text-center py-20">
      <div class="text-6xl mb-4">📋</div>
      <h3 class="text-2xl font-semibold text-white mb-2">Menu not available</h3>
      <p class="text-gray-400">Please check back later</p>
    </div>

    <!-- Mobile Sticky Cart Button -->
    <button
      v-if="!loading"
      @click="showCart = true"
      class="md:hidden fixed bottom-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-amber-500 to-amber-600 text-white px-8 py-4 rounded-full hover:from-amber-600 hover:to-amber-700 transition-all flex items-center gap-3 shadow-2xl z-40 border-2 border-amber-400"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <span class="font-bold text-lg">View Cart</span>
      <span v-if="cartItemsCount > 0" class="bg-red-500 text-white text-sm rounded-full w-7 h-7 flex items-center justify-center font-bold animate-pulse">
        {{ cartItemsCount }}
      </span>
    </button>

    <!-- Cart Sidebar -->
    <Teleport to="body">
      <div
        v-if="showCart"
        class="fixed inset-0 bg-black bg-opacity-70 z-50 backdrop-blur-sm"
        @click="showCart = false"
      >
        <div
          @click.stop
          class="fixed right-0 top-0 h-full w-full max-w-md bg-gradient-to-b from-gray-900 to-gray-800 shadow-2xl flex flex-col border-l border-amber-500/30"
        >
          <!-- Cart Header -->
          <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-6 flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold font-serif">Your Cart</h2>
              <p class="text-sm text-amber-100">Review your order</p>
            </div>
            <button @click="showCart = false" class="text-white hover:text-amber-100 transition-colors">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Cart Items -->
          <div class="flex-1 overflow-y-auto p-4">
            <div v-if="cart.length === 0" class="text-center py-20">
              <div class="text-6xl mb-4">🛒</div>
              <p class="text-gray-400">Your cart is empty</p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="(cartItem, index) in cart"
                :key="index"
                class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-4 border border-amber-500/20 hover:border-amber-500/40 transition-all"
              >
                <div class="flex justify-between items-start mb-3">
                  <div class="flex-1">
                    <h3 class="font-semibold text-white font-serif">{{ cartItem.item.title }}</h3>
                    <p class="text-sm text-gray-400">${{ cartItem.item.price }} each</p>
                  </div>
                  <button
                    @click="removeFromCart(index)"
                    class="text-red-400 hover:text-red-500 transition-colors"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>

                <!-- Quantity Controls -->
                <div class="flex items-center gap-3 mb-3">
                  <button
                    @click="updateQuantity(index, cartItem.quantity - 1)"
                    class="w-8 h-8 bg-gray-700 hover:bg-amber-500 rounded-full flex items-center justify-center transition-colors text-white"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <span class="font-semibold text-white min-w-[2rem] text-center">{{ cartItem.quantity }}</span>
                  <button
                    @click="updateQuantity(index, cartItem.quantity + 1)"
                    class="w-8 h-8 bg-gray-700 hover:bg-amber-500 rounded-full flex items-center justify-center transition-colors text-white"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  </button>
                  <span class="ml-auto font-bold text-amber-400 text-lg">${{ (cartItem.item.price * cartItem.quantity).toFixed(2) }}</span>
                </div>

                <!-- Item Notes -->
                <input
                  v-model="cartItem.notes"
                  type="text"
                  placeholder="Special instructions (optional)"
                  class="w-full text-sm bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                />
              </div>
            </div>
          </div>

          <!-- Cart Footer -->
          <div v-if="cart.length > 0" class="border-t border-amber-500/30 p-6 bg-gradient-to-b from-gray-900 to-black">
            <div class="space-y-3 mb-6">
              <div class="flex justify-between text-sm">
                <span class="text-gray-400">Subtotal</span>
                <span class="font-semibold text-white">${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-400">Tax (10%)</span>
                <span class="font-semibold text-white">${{ tax.toFixed(2) }}</span>
              </div>
              <div class="border-t border-dashed border-amber-500/30 pt-3"></div>
              <div class="flex justify-between text-xl font-bold">
                <span class="text-white">Total</span>
                <span class="text-amber-400">${{ total.toFixed(2) }}</span>
              </div>
            </div>

            <button
              @click="proceedToCheckout"
              class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white py-4 rounded-xl transition-all font-bold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105"
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

// Get business link from subdomain or route param
const getBusinessLink = () => {
  // Check if we have route param (path-based routing)
  if (route.params.businessLink) {
    return route.params.businessLink
  }

  // Otherwise extract from subdomain
  const hostname = window.location.hostname
  const parts = hostname.split('.')

  // Return subdomain if exists (e.g., airvend3.localhost -> airvend3)
  if (parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www') {
    return parts[0]
  }

  return null
}

const businessLink = getBusinessLink()
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
    console.log('Menu loaded table:', tableId)

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

const scrollToCategory = (categoryId) => {
  selectedCategory.value = categoryId

  // Scroll the selected category button into view
  const categoryButtons = document.querySelectorAll('[data-category-id]')
  const selectedButton = Array.from(categoryButtons).find(btn => btn.dataset.categoryId === String(categoryId))

  if (selectedButton) {
    selectedButton.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' })
  }
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

  // Check if we're on a subdomain
  const hostname = window.location.hostname
  const parts = hostname.split('.')
  const isSubdomain = parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www'

  if (isSubdomain) {
    // Subdomain routing - use simple path
    router.push({
      path: '/checkout',
      query: tableId ? { table: tableId } : {}
    })
  } else {
    // Path-based routing - use named route with params
    router.push({
      name: 'Checkout',
      params: { businessLink },
      query: tableId ? { table: tableId } : {}
    })
  }
}

onMounted(() => {
  loadMenu()
})
</script>
