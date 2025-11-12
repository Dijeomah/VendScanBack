<template>
  <VendorLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard</h1>
          <p class="text-gray-600 mt-1">Welcome back, {{ authStore.userName }}!</p>
        </div>
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

        <!-- Subscription Tier Card -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg shadow-sm p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-primary-100 mb-1">Current Plan</p>
              <h3 class="text-2xl font-bold capitalize">{{ authStore.user?.subscription_tier || 'Free' }} Plan</h3>
              <p class="text-sm text-primary-100 mt-2">
                {{ authStore.user?.business_links?.length || 0 }} of {{ getBusinessLimit() }} businesses created
              </p>
            </div>
            <div class="text-right">
              <router-link
                v-if="authStore.user?.subscription_tier !== 'max'"
                to="/vendor/subscription"
                class="inline-flex items-center px-4 py-2 bg-white text-primary-600 rounded-lg hover:bg-primary-50 transition-all font-medium text-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Upgrade Plan
              </router-link>
              <span v-else class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg font-medium text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                Max Plan
              </span>
            </div>
          </div>
          <div class="mt-4 pt-4 border-t border-primary-500">
            <div class="grid grid-cols-3 gap-4 text-center">
              <div>
                <p class="text-2xl font-bold">{{ stats.totalItems }}</p>
                <p class="text-xs text-primary-100">Menu Items</p>
              </div>
              <div>
                <p class="text-2xl font-bold">{{ businessStats.totalTables }}</p>
                <p class="text-xs text-primary-100">Tables</p>
              </div>
              <div>
                <p class="text-2xl font-bold">{{ businessStats.totalServers }}</p>
                <p class="text-xs text-primary-100">Servers</p>
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
                to="/vendor/tables"
                class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all group"
              >
                <div class="p-2 bg-blue-600 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900 group-hover:text-blue-700">Table Management</p>
                  <p class="text-sm text-gray-600">Manage tables and QR codes</p>
                </div>
              </router-link>

              <router-link
                to="/vendor/servers"
                class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-all group"
              >
                <div class="p-2 bg-orange-600 rounded-lg mr-4">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900 group-hover:text-orange-700">Server Management</p>
                  <p class="text-sm text-gray-600">Manage servers and assignments</p>
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
    </div>
  </VendorLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useVendorStore } from '@/stores/vendor'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import VendorLayout from '@/components/layouts/VendorLayout.vue'
const authStore = useAuthStore()
const vendorStore = useVendorStore()
const toast = useToast()

const loading = ref(true)
const businessStats = ref({
  totalTables: 0,
  totalServers: 0
})

const stats = computed(() => ({
  totalItems: vendorStore.totalItems || 0,
  activeItems: vendorStore.activeItems.length || 0,
  totalCategories: vendorStore.totalCategories || 0,
  menuViews: 0 // This would come from analytics
}))

const getBusinessLimit = () => {
  const tier = authStore.user?.subscription_tier || 'free'
  switch (tier) {
    case 'free':
      return 3
    case 'pro':
      return 5
    case 'max':
      return 'Unlimited'
    default:
      return 3
  }
}

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
