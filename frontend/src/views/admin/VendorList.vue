<template>
    <AdminLayout>
        <div class="p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Vendors</h1>
                            <p class="text-gray-600 mt-1">Manage all vendor accounts</p>
                        </div>
                        <router-link
                            to="/admin/vendors/create"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Vendor
                        </router-link>
                    </div>
                </div>
                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center items-center h-64">
                    <LoadingSpinner size="lg" text="Loading vendors..."/>
                </div>

                <!-- Content -->
                <div v-else>
                    <!-- Search and Filters -->
                    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Search -->
                            <div class="flex-1">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search vendors by name, email, or business..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                />
                            </div>

                            <!-- Refresh Button -->
                            <button
                                @click="loadVendors"
                                :disabled="refreshing"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all disabled:opacity-50 flex items-center gap-2"
                            >
                                <svg
                                    class="w-5 h-5"
                                    :class="{ 'animate-spin': refreshing }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Vendors</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ vendors.length }}</p>
                                </div>
                                <div class="p-3 bg-primary-100 rounded-lg">
                                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Active Vendors</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ activeVendors }}</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Menu Items</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalItems }}</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-lg">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vendors Table -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <!-- No vendors -->
                        <div v-if="filteredVendors.length === 0" class="text-center py-12">
                            <div class="text-6xl mb-4">👥</div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                {{ searchQuery ? 'No vendors found' : 'No vendors yet' }}
                            </h3>
                            <p class="text-gray-600 mb-6">
                                {{
                                    searchQuery ? 'Try adjusting your search criteria' : 'Create your first vendor to get started'
                                }}
                            </p>
                            <router-link
                                v-if="!searchQuery"
                                to="/admin/vendors/create"
                                class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"/>
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
                                        Vendor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Business
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stats
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="vendor in filteredVendors" :key="vendor.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div v-if="vendor.vendor_media?.logo" class="flex-shrink-0 h-10 w-10">
                                                <img :src="vendor.vendor_media.logo" :alt="vendor.business_links?.[0]?.business_data?.business_name || vendor.first_name"
                                                     class="h-10 w-10 rounded-full object-cover"/>
                                            </div>
                                            <div :class="vendor.vendor_media?.logo ? 'ml-4' : ''">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ vendor.first_name }} {{ vendor.last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ vendor.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ vendor.business_links?.[0]?.business_data?.business_name || 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ vendor.business_links?.[0]?.business_data?.business_type || 'Not set' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ vendor.phone_number || 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ vendor.business_links?.[0]?.business_link || 'No link' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                    <span
                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      {{ vendor.items_count || 0 }} items
                    </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <router-link
                                                :to="`/admin/vendors/${vendor.id}/edit`"
                                                class="text-primary-600 hover:text-primary-900"
                                                title="Edit vendor"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </router-link>
                                            <button
                                                @click="confirmDelete(vendor)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete vendor"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Delete Confirmation Modal -->
            <div v-if="vendorToDelete"
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Vendor</h3>
                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete <strong>{{
                            vendorToDelete.business_links?.[0]?.business_data?.business_name || vendorToDelete.first_name + ' ' + vendorToDelete.last_name
                        }}</strong>?
                        This action cannot be undone.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            @click="vendorToDelete = null"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteVendor"
                            :disabled="deleting"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <LoadingSpinner v-if="deleting" size="sm"/>
                            <span>{{ deleting ? 'Deleting...' : 'Delete' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue'
import {useApi} from '@/composables/useApi'
import {useToast} from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import AdminLayout from '@/components/layouts/AdminLayout.vue'

const {admin} = useApi()
const toast = useToast()

const loading = ref(true)
const refreshing = ref(false)
const deleting = ref(false)
const vendors = ref([])
const searchQuery = ref('')
const vendorToDelete = ref(null)

const filteredVendors = computed(() => {
    if (!Array.isArray(vendors.value.data)) return []
    if (!searchQuery.value) return vendors.value.data

    const query = searchQuery.value.toLowerCase()
    return vendors.value.data.filter(vendor =>
        vendor.first_name?.toLowerCase().includes(query) ||
        vendor.last_name?.toLowerCase().includes(query) ||
        vendor.email?.toLowerCase().includes(query) ||
        vendor.business_links?.[0]?.business_data?.business_name?.toLowerCase().includes(query) ||
        vendor.business_links?.[0]?.business_link?.toLowerCase().includes(query)
    )
})

const activeVendors = computed(() => {
    if (!Array.isArray(vendors.value.data)) return 0
    return vendors.value.data.filter(v => v.business_links?.length > 0).length
})

const totalItems = computed(() => {
    if (!Array.isArray(vendors.value.data)) return 0
    return vendors.value.data.reduce((total, vendor) => total + (vendor.items_count || 0), 0)
})

const loadVendors = async () => {
    refreshing.value = true
    try {
        const response = await admin.getVendors()
        const data = response.data.data || response.data
        vendors.value = data.vendors || data || []
    } catch (error) {
        console.error('Error loading vendors:', error)
        toast.error('Failed to load vendors')
    } finally {
        loading.value = false
        refreshing.value = false
    }
}

const confirmDelete = (vendor) => {
    vendorToDelete.value = vendor
}

const deleteVendor = async () => {
    if (!vendorToDelete.value) return

    deleting.value = true
    try {
        await admin.deleteVendor(vendorToDelete.value.id)
        toast.success('Vendor deleted successfully')
        vendors.value = vendors.value.filter(v => v.id !== vendorToDelete.value.id)
        vendorToDelete.value = null
    } catch (error) {
        console.error('Error deleting vendor:', error)
        toast.error(error.response?.data?.message || 'Failed to delete vendor')
    } finally {
        deleting.value = false
    }
}

onMounted(() => {
    loadVendors()
})
</script>

<style scoped>
/* Custom styles if needed */
</style>
