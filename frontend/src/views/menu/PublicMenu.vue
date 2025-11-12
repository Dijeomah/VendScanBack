<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-screen">
      <LoadingSpinner size="lg" text="Loading menu..." />
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center min-h-screen p-4">
      <div class="text-center max-w-md">
        <div class="text-6xl mb-4">😕</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Menu Not Found</h2>
        <p class="text-gray-600 mb-6">{{ error }}</p>
        <button
          @click="loadMenu"
          class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
        >
          Try Again
        </button>
      </div>
    </div>

    <!-- Menu Content -->
    <div v-else-if="vendor">
      <!-- Table Context Banner (shown when accessed via QR code) -->
      <div v-if="tableNumber" class="bg-primary-600 text-white">
        <div class="max-w-4xl mx-auto px-4 py-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <div>
                <p class="font-semibold">Table {{ tableNumber }}</p>
                <p class="text-xs text-primary-100">Viewing menu for this table</p>
              </div>
            </div>
            <button
              @click="tableNumber = null"
              class="p-1 rounded hover:bg-primary-700 transition-colors"
              title="Clear table selection"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Vendor Header -->
      <div class="bg-white shadow-sm">
        <!-- Hero Image -->
        <div v-if="vendor.vendor_media?.hero_image" class="h-48 md:h-64 overflow-hidden">
          <img
            :src="vendor.vendor_media.hero_image"
            :alt="vendor.business_name"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Vendor Info -->
        <div class="max-w-4xl mx-auto px-4 py-6">
          <div class="flex items-center gap-4">
            <!-- Logo -->
            <div v-if="vendor.vendor_media?.logo" class="flex-shrink-0">
              <img
                :src="vendor.vendor_media.logo"
                :alt="vendor.business_name"
                class="w-20 h-20 rounded-full border-4 border-white shadow-lg -mt-10"
              />
            </div>

            <!-- Business Info -->
            <div class="flex-1">
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                {{ vendor.business_name || 'Our Menu' }}
              </h1>
              <div v-if="businessLink" class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                <span v-if="businessLink.business_type" class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  {{ businessLink.business_type }}
                </span>
                <span v-if="businessLink.phone_number" class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  {{ businessLink.phone_number }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="sticky top-0 z-10 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-3">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search menu..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            />
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Menu Items -->
      <div class="max-w-4xl mx-auto px-4 py-6">
        <!-- No Items -->
        <div v-if="filteredItems.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🍽️</div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">
            {{ searchQuery ? 'No items found' : 'No menu items yet' }}
          </h3>
          <p class="text-gray-600">
            {{ searchQuery ? 'Try a different search' : 'Check back soon!' }}
          </p>
        </div>

        <!-- Items Grid -->
        <div v-else class="space-y-4">
          <div
            v-for="item in filteredItems"
            :key="item.id"
            class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer overflow-hidden"
            @click="selectedItem = item"
          >
            <div class="flex gap-4 p-4">
              <!-- Item Image -->
              <div v-if="item.image" class="flex-shrink-0">
                <img
                  :src="item.image"
                  :alt="item.title"
                  class="w-24 h-24 rounded-lg object-cover"
                />
              </div>

              <!-- Item Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ item.title }}
                  </h3>
                  <span class="text-lg font-bold text-primary-600 whitespace-nowrap">
                    ${{ formatPrice(item.price) }}
                  </span>
                </div>

                <p v-if="item.description" class="text-sm text-gray-600 mt-1 line-clamp-2">
                  {{ item.description }}
                </p>

                <div class="flex items-center gap-2 mt-2">
                  <span v-if="item.category" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700">
                    {{ item.category.category_name }}
                  </span>
                  <span v-if="!item.status" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                    Unavailable
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Item Detail Modal -->
      <Transition name="modal">
        <div v-if="selectedItem" class="fixed inset-0 z-50 overflow-y-auto" @click.self="selectedItem = null">
          <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

            <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-xl">
              <!-- Close Button -->
              <button
                @click="selectedItem = null"
                class="absolute top-4 right-4 p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors z-10"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>

              <!-- Item Image -->
              <div v-if="selectedItem.image" class="h-64 overflow-hidden rounded-t-2xl">
                <img
                  :src="selectedItem.image"
                  :alt="selectedItem.title"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Item Details -->
              <div class="p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                  <h2 class="text-2xl font-bold text-gray-900">
                    {{ selectedItem.title }}
                  </h2>
                  <span class="text-2xl font-bold text-primary-600 whitespace-nowrap">
                    ${{ formatPrice(selectedItem.price) }}
                  </span>
                </div>

                <p v-if="selectedItem.description" class="text-gray-600 mb-4">
                  {{ selectedItem.description }}
                </p>

                <div class="flex flex-wrap gap-2">
                  <span v-if="selectedItem.category" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-700">
                    {{ selectedItem.category.category_name }}
                  </span>
                  <span
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                      selectedItem.status
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                    ]"
                  >
                    {{ selectedItem.status ? 'Available' : 'Unavailable' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const route = useRoute()
const { public: publicApi } = useApi()

const loading = ref(true)
const error = ref(null)
const vendor = ref(null)
const businessLink = ref(null)
const items = ref([])
const searchQuery = ref('')
const selectedItem = ref(null)
const tableNumber = ref(null)

const filteredItems = computed(() => {
  if (!searchQuery.value) return items.value

  const query = searchQuery.value.toLowerCase()
  return items.value.filter(item =>
    item.title?.toLowerCase().includes(query) ||
    item.description?.toLowerCase().includes(query) ||
    item.category?.category_name?.toLowerCase().includes(query)
  )
})

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
}

const getSubdomain = () => {
  const hostname = window.location.hostname
  const parts = hostname.split('.')

  // If hostname is like 'airvend-res.localhost' or 'airvend-res.example.com'
  // Return the first part as subdomain
  if (parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www') {
    return parts[0]
  }

  return null
}

const loadMenu = async () => {
  loading.value = true
  error.value = null

  try {
    let response

    // Check if we're on a subdomain
    const subdomain = getSubdomain()

    if (subdomain) {
      console.log('Loading menu for subdomain:', subdomain)
      response = await publicApi.getVendorBySubdomain(subdomain)
    } else {
      // Fall back to path-based routing
      const vendorLink = route.params.vendorLink
      console.log('Loading menu for vendor link:', vendorLink)
      response = await publicApi.getVendorMenuByLink(vendorLink)
    }

    console.log('Menu response:', response)

    vendor.value = response.data || response
    businessLink.value = vendor.value.business_links?.[0]
    items.value = businessLink.value?.items || []

    console.log('Vendor:', vendor.value)
    console.log('Items:', items.value)
  } catch (err) {
    console.error('Error loading menu:', err)
    error.value = err.response?.data?.message || 'Unable to load menu. Please try again.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  // Check for table query parameter (from QR code scan)
  if (route.query.table) {
    tableNumber.value = route.query.table
    console.log('Table context detected:', tableNumber.value)
  }

  loadMenu()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
