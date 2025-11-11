<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">Manage vendors and monitor system activity</p>
          </div>
          <button
            @click="handleLogout"
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

      <!-- Content -->
      <div v-else>
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Vendors</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalVendors }}</p>
              </div>
              <div class="p-3 bg-primary-100 rounded-lg">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Items</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalItems }}</p>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Total Categories</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalCategories }}</p>
              </div>
              <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600">Business Links</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalBusinessLinks }}</p>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <router-link
              to="/admin/vendors/create"
              class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow"
            >
              <div class="flex items-center gap-4">
                <div class="p-3 bg-primary-100 rounded-lg">
                  <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">Add Vendor</h3>
                  <p class="text-sm text-gray-600">Create a new vendor account</p>
                </div>
              </div>
            </router-link>

            <router-link
              to="/admin/vendors"
              class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow"
            >
              <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 rounded-lg">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">Manage Vendors</h3>
                  <p class="text-sm text-gray-600">View and edit vendor accounts</p>
                </div>
              </div>
            </router-link>

            <button
              @click="refreshDashboard"
              :disabled="refreshing"
              class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow text-left"
            >
              <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 rounded-lg">
                  <svg
                    class="w-6 h-6 text-blue-600"
                    :class="{ 'animate-spin': refreshing }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">Refresh Data</h3>
                  <p class="text-sm text-gray-600">Update dashboard statistics</p>
                </div>
              </div>
            </button>
          </div>
        </div>

        <!-- Recent Vendors -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Recent Vendors</h2>
            <router-link
              to="/admin/vendors"
              class="text-sm text-primary-600 hover:text-primary-700 font-medium"
            >
              View All
            </router-link>
          </div>

          <!-- No vendors -->
          <div v-if="recentVendors.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">👥</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No vendors yet</h3>
            <p class="text-gray-600 mb-6">Create your first vendor to get started</p>
            <router-link
              to="/admin/vendors/create"
              class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Add First Vendor
            </router-link>
          </div>

          <!-- Vendors Table -->
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Business
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Contact
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Items
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="vendor in recentVendors" :key="vendor.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div v-if="vendor.vendor_media?.logo" class="flex-shrink-0 h-10 w-10">
                        <img :src="vendor.vendor_media.logo" :alt="vendor.business_name" class="h-10 w-10 rounded-full object-cover" />
                      </div>
                      <div :class="vendor.vendor_media?.logo ? 'ml-4' : ''">
                        <div class="text-sm font-medium text-gray-900">{{ vendor.business_name || 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ vendor.user?.email || 'N/A' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ vendor.user?.first_name }} {{ vendor.user?.last_name }}</div>
                    <div class="text-sm text-gray-500">{{ vendor.user?.phone_number || 'N/A' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      {{ vendor.items_count || 0 }} items
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <router-link
                      :to="`/admin/vendors/${vendor.id}/edit`"
                      class="text-primary-600 hover:text-primary-900 mr-4"
                    >
                      Edit
                    </router-link>
                    <router-link
                      :to="`/admin/vendors/${vendor.id}`"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      View
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const router = useRouter()
const { admin } = useApi()
const authStore = useAuthStore()
const toast = useToast()

const loading = ref(true)
const refreshing = ref(false)
const stats = ref({
  totalVendors: 0,
  totalItems: 0,
  totalCategories: 0,
  totalBusinessLinks: 0
})
const recentVendors = ref([])

const loadDashboard = async () => {
  try {
    const [dashboardRes, vendorsRes] = await Promise.all([
      admin.getDashboard(),
      admin.getVendors()
    ])

    const dashboardData = dashboardRes.data.data || dashboardRes.data
    stats.value = {
      totalVendors: dashboardData.total_vendors || 0,
      totalItems: dashboardData.total_items || 0,
      totalCategories: dashboardData.total_categories || 0,
      totalBusinessLinks: dashboardData.total_business_links || 0
    }

    // Get recent vendors (last 5)
    const vendorsData = vendorsRes.data.data || vendorsRes.data
    recentVendors.value = (vendorsData.vendors || vendorsData || []).slice(0, 5)
  } catch (error) {
    console.error('Error loading dashboard:', error)
    toast.error('Failed to load dashboard data')
  } finally {
    loading.value = false
  }
}

const refreshDashboard = async () => {
  refreshing.value = true
  await loadDashboard()
  refreshing.value = false
  toast.success('Dashboard refreshed!')
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(() => {
  loadDashboard()
})
</script>

<style scoped>
/* Custom styles if needed */
</style>
