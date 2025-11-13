<template>
  <VendorLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Business Management</h1>
              <p class="text-gray-600 mt-1">Manage your businesses and their settings</p>
            </div>
            <button
              @click="openCreateModal"
              :disabled="!canCreateMoreBusinesses"
              class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Add Business
            </button>
          </div>

          <!-- Limit Warning -->
          <div v-if="!canCreateMoreBusinesses" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start">
              <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div class="flex-1">
                <h3 class="text-sm font-medium text-yellow-800">Business Limit Reached</h3>
                <p class="text-sm text-yellow-700 mt-1">
                  You've reached the limit of {{ businessLimit }} businesses for your {{ authStore.user?.subscription_tier || 'free' }} plan.
                  <router-link to="/vendor/subscription" class="font-semibold underline hover:text-yellow-900">
                    Upgrade your plan
                  </router-link> to create more businesses.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Total Businesses</p>
                <p class="text-3xl font-bold text-gray-900">{{ businesses.length }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Remaining Slots</p>
                <p class="text-3xl font-bold text-green-600">{{ remainingSlots }}</p>
              </div>
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Current Plan</p>
                <p class="text-2xl font-bold text-gray-900 capitalize">{{ authStore.user?.subscription_tier || 'Free' }}</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Total Items</p>
                <p class="text-3xl font-bold text-gray-900">{{ totalItems }}</p>
              </div>
              <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>

        <!-- Businesses Grid -->
        <div v-else-if="businesses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="business in businesses"
            :key="business.id"
            class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all"
          >
            <div class="p-6">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1 min-w-0">
                  <h3 class="text-lg font-semibold text-gray-900 truncate">
                    {{ business.business_data?.business_name || business.business_link }}
                  </h3>
                  <p class="text-sm text-gray-500 truncate">{{ business.business_link }}</p>
                </div>
                <div class="flex gap-1 ml-2">
                  <button
                    @click="openEditModal(business)"
                    class="p-2 text-gray-400 hover:text-blue-600 transition-colors"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="confirmDelete(business)"
                    class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                    title="Delete"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="space-y-2 mb-4">
                <div v-if="business.business_data?.business_type" class="flex items-center text-sm text-gray-600">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                  {{ business.business_data.business_type }}
                </div>
                <div v-if="business.business_data?.phone_number" class="flex items-center text-sm text-gray-600">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  {{ business.business_data.phone_number }}
                </div>
                <div v-if="business.business_data?.address" class="flex items-start text-sm text-gray-600">
                  <svg class="w-4 h-4 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span class="flex-1">{{ business.business_data.address }}</span>
                </div>
              </div>

              <div class="border-t pt-4">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-600">Menu Items:</span>
                  <span class="font-semibold text-gray-900">{{ business.items?.length || 0 }}</span>
                </div>
                <a
                  :href="`http://${business.business_link}.localhost:3000`"
                  target="_blank"
                  class="mt-3 block w-full text-center px-4 py-2 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all font-medium"
                >
                  View Menu
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">No Businesses Yet</h3>
          <p class="text-gray-600 mb-6">Create your first business to get started with digital menus</p>
          <button
            @click="openCreateModal"
            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Your First Business
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingBusiness ? 'Edit Business' : 'Create New Business' }}
          </h2>
        </div>
        <div class="p-6">
          <form @submit.prevent="saveBusiness" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
              <input
                v-model="businessForm.business_name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="My Restaurant"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Business Link (Subdomain) *
              </label>
              <div class="flex items-center">
                <input
                  v-model="businessForm.business_link"
                  type="text"
                  required
                  :disabled="editingBusiness"
                  pattern="[a-z0-9-]+"
                  class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                  placeholder="my-restaurant"
                  @input="validateBusinessLink"
                />
                <span class="px-4 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-sm text-gray-600">
                  .localhost:3000
                </span>
              </div>
              <p class="text-xs text-gray-500 mt-1">
                Only lowercase letters, numbers, and hyphens. Cannot be changed after creation.
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Business Type</label>
              <select
                v-model="businessForm.business_type"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">Select type</option>
                <option value="Restaurant">Restaurant</option>
                <option value="Cafe">Cafe</option>
                <option value="Bar">Bar</option>
                <option value="Food Truck">Food Truck</option>
                <option value="Bakery">Bakery</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
              <input
                v-model="businessForm.phone_number"
                type="tel"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="+1234567890"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
              <textarea
                v-model="businessForm.address"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="123 Main St, City, State 12345"
              ></textarea>
            </div>

            <!-- Geofencing Settings -->
            <div class="border-t pt-4">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Geofencing</h3>
                  <p class="text-xs text-gray-500 mt-1">Restrict orders to customers within a specific area</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    v-model="businessForm.geofence_enabled"
                    class="sr-only peer"
                  />
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                </label>
              </div>

              <div v-if="businessForm.geofence_enabled" class="space-y-4 pl-4 border-l-2 border-gray-200">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
                    <input
                      v-model.number="businessForm.latitude"
                      type="number"
                      step="0.000001"
                      min="-90"
                      max="90"
                      :required="businessForm.geofence_enabled"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      placeholder="40.7128"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
                    <input
                      v-model.number="businessForm.longitude"
                      type="number"
                      step="0.000001"
                      min="-180"
                      max="180"
                      :required="businessForm.geofence_enabled"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      placeholder="-74.0060"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Geofence Radius: {{ businessForm.geofence_radius }}m
                  </label>
                  <select
                    v-model.number="businessForm.geofence_radius"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  >
                    <option :value="50">50 meters (~164 feet)</option>
                    <option :value="100">100 meters (~328 feet)</option>
                    <option :value="200">200 meters (~656 feet)</option>
                    <option :value="500">500 meters (~0.3 miles)</option>
                    <option :value="1000">1 kilometer (~0.6 miles)</option>
                    <option :value="2000">2 kilometers (~1.2 miles)</option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">
                    Only customers within this radius can place orders
                  </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 text-xs text-blue-800">
                      <strong>Tip:</strong> You can use Google Maps to find your business coordinates.
                      Right-click on your location and select "What's here?" to see the coordinates.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
              >
                {{ saving ? 'Saving...' : (editingBusiness ? 'Update' : 'Create') }} Business
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6">
          <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Delete Business?</h3>
          <p class="text-gray-600 text-center mb-6">
            Are you sure you want to delete <strong>{{ businessToDelete?.business_name }}</strong>?
            All associated data including menu items, tables, and servers will be permanently deleted. This action cannot be undone.
          </p>
          <div class="flex gap-3">
            <button
              @click="closeDeleteModal"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all"
            >
              Cancel
            </button>
            <button
              @click="deleteBusiness"
              :disabled="deleting"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all disabled:opacity-50"
            >
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </VendorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useVendorStore } from '@/stores/vendor'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import VendorLayout from '@/components/layouts/VendorLayout.vue'

const authStore = useAuthStore()
const vendorStore = useVendorStore()
const { vendor } = useApi()
const toast = useToast()

const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const businesses = ref([])
const showModal = ref(false)
const showDeleteModal = ref(false)
const editingBusiness = ref(null)
const businessToDelete = ref(null)

const businessForm = ref({
  business_name: '',
  business_link: '',
  business_type: '',
  phone_number: '',
  address: '',
  geofence_enabled: false,
  latitude: null,
  longitude: null,
  geofence_radius: 100
})

const businessLimit = computed(() => {
  const tier = authStore.user?.subscription_tier || 'free'
  switch (tier) {
    case 'free': return 3
    case 'pro': return 5
    case 'max': return Infinity
    default: return 3
  }
})

const canCreateMoreBusinesses = computed(() => {
  return businesses.value.length < businessLimit.value
})

const remainingSlots = computed(() => {
  const remaining = businessLimit.value - businesses.value.length
  return businessLimit.value === Infinity ? '∞' : Math.max(0, remaining)
})

const totalItems = computed(() => {
  return businesses.value.reduce((total, business) => {
    return total + (business.items?.length || 0)
  }, 0)
})

const loadBusinesses = async () => {
  try {
    loading.value = true
    const response = await vendor.getBusinessLinks()
    const responseData = response.data?.data || response.data || {}

    // Extract businesses array from the response
    businesses.value = responseData.businesses || []

    console.log('Loaded businesses:', businesses.value)
  } catch (error) {
    console.error('Error loading businesses:', error)
    toast.error('Failed to load businesses')
  } finally {
    loading.value = false
  }
}

const validateBusinessLink = (event) => {
  // Only allow lowercase letters, numbers, and hyphens
  event.target.value = event.target.value.toLowerCase().replace(/[^a-z0-9-]/g, '')
  businessForm.value.business_link = event.target.value
}

const openCreateModal = () => {
  if (!canCreateMoreBusinesses.value) {
    toast.warning('Please upgrade your plan to create more businesses')
    return
  }
  editingBusiness.value = null
  businessForm.value = {
    business_name: '',
    business_link: '',
    business_type: '',
    phone_number: '',
    address: '',
    geofence_enabled: false,
    latitude: null,
    longitude: null,
    geofence_radius: 100
  }
  showModal.value = true
}

const openEditModal = (business) => {
  editingBusiness.value = business
  businessForm.value = {
    business_name: business.business_data?.business_name || business.business_link,
    business_link: business.business_link,
    business_type: business.business_data?.business_type || '',
    phone_number: business.business_data?.phone_number || '',
    address: business.business_data?.address || '',
    geofence_enabled: business.business_data?.geofence_enabled || false,
    latitude: business.business_data?.latitude || null,
    longitude: business.business_data?.longitude || null,
    geofence_radius: business.business_data?.geofence_radius || 100
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingBusiness.value = null
}

const saveBusiness = async () => {
  try {
    saving.value = true

    if (editingBusiness.value) {
      // Update existing business
      await vendor.updateBusinessInfo(editingBusiness.value.id, businessForm.value)
      toast.success('Business updated successfully!')
    } else {
      // Create new business
      await vendor.setBusinessInfo(businessForm.value)
      toast.success('Business created successfully!')
    }

    closeModal()
    await loadBusinesses()
  } catch (error) {
    console.error('Error saving business:', error)
    const message = error.response?.data?.message || 'Failed to save business'
    toast.error(message)
  } finally {
    saving.value = false
  }
}

const confirmDelete = (business) => {
  businessToDelete.value = business
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  businessToDelete.value = null
}

const deleteBusiness = async () => {
  try {
    deleting.value = true
    await vendor.deleteBusinessLink(businessToDelete.value.id)
    toast.success('Business deleted successfully!')
    closeDeleteModal()
    await loadBusinesses()
  } catch (error) {
    console.error('Error deleting business:', error)
    toast.error('Failed to delete business')
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  await loadBusinesses()
})
</script>
