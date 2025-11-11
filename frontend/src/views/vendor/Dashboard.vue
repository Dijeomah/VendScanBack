<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Welcome back, {{ authStore.userName }}!</p>
          </div>
          <button
            @click="logout"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
          >
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <LoadingSpinner size="lg" text="Loading dashboard..." />
      </div>

      <!-- Dashboard Content -->
      <div v-else class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Items -->
          <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Total Items</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalItems }}</p>
              </div>
              <div class="p-3 bg-primary-100 rounded-lg">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Active Items -->
          <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Active Items</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ stats.activeItems }}</p>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Categories -->
          <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Categories</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalCategories }}</p>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Menu Views -->
          <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Menu Views</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.menuViews }}</p>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- QR Code Section -->
          <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Your QR Code</h2>
            <div v-if="vendorStore.vendor?.qr_code_url" class="flex flex-col items-center">
              <img
                :src="vendorStore.vendor.qr_code_url"
                alt="QR Code"
                class="w-48 h-48 border-2 border-gray-200 rounded-lg"
              />
              <div class="mt-4 flex gap-3">
                <button
                  @click="downloadQR"
                  class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all text-sm font-medium"
                >
                  Download QR Code
                </button>
                <button
                  @click="regenerateQR"
                  class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium"
                >
                  Regenerate
                </button>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <p class="text-gray-600 mb-4">No QR code generated yet</p>
              <button
                @click="regenerateQR"
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
              >
                Generate QR Code
              </button>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="space-y-3">
              <router-link
                to="/vendor/items/create"
                class="flex items-center p-4 bg-primary-50 rounded-lg hover:bg-primary-100 transition-all group"
              >
                <div class="p-2 bg-primary-600 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900 group-hover:text-primary-700">Add New Item</p>
                  <p class="text-sm text-gray-600">Create a new menu item</p>
                </div>
              </router-link>

              <router-link
                to="/vendor/menu"
                class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-all group"
              >
                <div class="p-2 bg-green-600 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900 group-hover:text-green-700">Manage Menu</p>
                  <p class="text-sm text-gray-600">View and edit your menu items</p>
                </div>
              </router-link>

              <router-link
                to="/vendor/settings"
                class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-all group"
              >
                <div class="p-2 bg-purple-600 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900 group-hover:text-purple-700">Settings</p>
                  <p class="text-sm text-gray-600">Update your profile and business info</p>
                </div>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useVendorStore } from '@/stores/vendor'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const router = useRouter()
const authStore = useAuthStore()
const vendorStore = useVendorStore()
const toast = useToast()

const loading = ref(true)

const stats = computed(() => ({
  totalItems: vendorStore.totalItems || 0,
  activeItems: vendorStore.activeItems.length || 0,
  totalCategories: vendorStore.totalCategories || 0,
  menuViews: 0 // This would come from analytics
}))

onMounted(async () => {
  try {
    await vendorStore.fetchFullProfile()
  } catch (error) {
    console.error('Error loading dashboard:', error)
    toast.error('Failed to load dashboard data')
  } finally {
    loading.value = false
  }
})

const logout = async () => {
  try {
    await authStore.logout()
    toast.success('Logged out successfully')
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}

const downloadQR = () => {
  if (vendorStore.vendor?.qr_code_url) {
    window.open(vendorStore.vendor.qr_code_url, '_blank')
  }
}

const regenerateQR = async () => {
  try {
    await vendorStore.generateQR()
    toast.success('QR Code generated successfully!')
    await vendorStore.fetchFullProfile()
  } catch (error) {
    console.error('Error generating QR code:', error)
    toast.error('Failed to generate QR code')
  }
}
</script>
