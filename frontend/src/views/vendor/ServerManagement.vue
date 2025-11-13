<template>
  <VendorLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Server Management</h1>
              <p class="text-gray-600 mt-1">Manage waiters/servers and their business assignments</p>
            </div>
            <button
              @click="openCreateModal"
              class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Add Server
            </button>
          </div>
        </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 mb-1">Total Servers</p>
              <p class="text-3xl font-bold text-gray-900">{{ servers.length }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 mb-1">Total Assignments</p>
              <p class="text-3xl font-bold text-gray-900">{{ totalAssignments }}</p>
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
              <p class="text-sm text-gray-600 mb-1">Avg Tables/Server</p>
              <p class="text-3xl font-bold text-gray-900">{{ avgTablesPerServer }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600 mb-1">Active Servers</p>
              <p class="text-3xl font-bold text-gray-900">{{ servers.length }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>

      <!-- Servers Grid -->
      <div v-else-if="servers.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="server in servers"
          :key="server.id"
          class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                  <span class="text-lg font-bold text-primary-600">
                    {{ server.first_name[0] }}{{ server.last_name[0] }}
                  </span>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ server.first_name }} {{ server.last_name }}
                  </h3>
                  <p class="text-sm text-gray-500">{{ server.userid }}</p>
                </div>
              </div>
              <div class="flex gap-1">
                <button
                  @click="openEditModal(server)"
                  class="p-2 text-gray-400 hover:text-blue-600 transition-colors"
                  title="Edit Server"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button
                  @click="confirmDelete(server)"
                  class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                  title="Delete Server"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <div class="space-y-2 mb-4">
              <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ server.email }}
              </div>
              <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ server.phone_number }}
              </div>
            </div>

            <div class="border-t pt-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Assigned To:</span>
                <button
                  @click="openAssignModal(server)"
                  class="text-sm text-primary-600 hover:text-primary-700 font-medium"
                >
                  Manage
                </button>
              </div>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="assignment in server.assigned_businesses"
                  :key="assignment.id"
                  class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded"
                >
                  {{ assignment.business?.business_name || 'Business' }}
                </span>
                <span v-if="!server.assigned_businesses || server.assigned_businesses.length === 0" class="text-xs text-gray-400 italic">
                  No assignments yet
                </span>
              </div>
              <div class="mt-2 text-xs text-gray-500">
                Tables: {{ server.assigned_tables?.length || 0 }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Servers Yet</h3>
        <p class="text-gray-600 mb-6">Create your first server/waiter to get started</p>
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Your First Server
        </button>
      </div>
    </div>

    <!-- Create/Edit Server Modal -->
    <div v-if="showServerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold text-gray-900">
            {{ editingServer ? 'Edit Server' : 'Create New Server' }}
          </h2>
        </div>
        <div class="p-6">
          <form @submit.prevent="saveServer" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                <input
                  v-model="serverForm.first_name"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  placeholder="John"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                <input
                  v-model="serverForm.last_name"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                  placeholder="Doe"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
              <input
                v-model="serverForm.email"
                type="email"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="server@example.com"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
              <input
                v-model="serverForm.phone_number"
                type="tel"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="+1234567890"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Password {{ editingServer ? '(leave blank to keep current)' : '*' }}
              </label>
              <input
                v-model="serverForm.password"
                type="password"
                :required="!editingServer"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Enter password"
              />
            </div>

            <div v-if="!editingServer">
              <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Businesses (Optional)</label>
              <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                <label
                  v-for="business in businesses"
                  :key="business.id"
                  class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded"
                >
                  <input
                    type="checkbox"
                    :value="business.id"
                    v-model="serverForm.business_link_ids"
                    class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                  />
                  <span class="text-sm text-gray-700">{{ business.business_name }}</span>
                </label>
                <p v-if="businesses.length === 0" class="text-sm text-gray-500 italic">
                  No businesses available
                </p>
              </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button
                type="button"
                @click="closeServerModal"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
              >
                {{ saving ? 'Saving...' : (editingServer ? 'Update' : 'Create') }} Server
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Assign to Business Modal -->
        <div v-if="showAssignModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-900">
                        Manage Business & Table Assignments
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ selectedServer?.first_name }} {{ selectedServer?.last_name }}
                    </p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div
                            v-for="business in businesses"
                            :key="business.id"
                            class="border border-gray-200 rounded-lg"
                        >
                            <!-- Business Header -->
                            <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ business.business_name }}</p>
                                    <p class="text-sm text-gray-500">{{ business.business_link }}</p>
                                    <p v-if="getBusinessAssignedTablesCount(business.id) > 0" class="text-xs text-primary-600 mt-1">
                                        {{ getBusinessAssignedTablesCount(business.id) }} table(s) assigned
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        v-if="isServerAssignedToBusiness(business.id)"
                                        @click="toggleBusinessTables(business.id)"
                                        class="px-4 py-2 text-sm bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-all"
                                    >
                                        {{ expandedBusinessId === business.id ? 'Hide' : 'Manage' }} Tables
                                    </button>
                                    <button
                                        v-if="isServerAssignedToBusiness(business.id)"
                                        @click="unassignFromBusiness(business.id)"
                                        class="px-4 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all"
                                    >
                                        Remove
                                    </button>
                                    <button
                                        v-else
                                        @click="assignToBusiness(business.id)"
                                        class="px-4 py-2 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
                                    >
                                        Assign
                                    </button>
                                </div>
                            </div>

                            <!-- Tables Section (Expandable) -->
                            <div v-if="expandedBusinessId === business.id && isServerAssignedToBusiness(business.id)"
                                 class="border-t border-gray-200 p-4 bg-gray-50">
                                <div v-if="loadingTables" class="text-center py-4">
                                    <p class="text-sm text-gray-500">Loading tables...</p>
                                </div>
                                <div v-else-if="businessTables.length === 0" class="text-center py-4">
                                    <p class="text-sm text-gray-500">No tables found for this business</p>
                                </div>
                                <div v-else class="space-y-2">
                                    <p class="text-sm font-medium text-gray-700 mb-3">Select tables to assign:</p>
                                    <div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto">
                                        <label
                                            v-for="table in businessTables"
                                            :key="table.id"
                                            class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-white cursor-pointer"
                                            :class="{ 'bg-primary-50 border-primary-300': isTableAssignedToServer(table.id) }"
                                        >
                                            <input
                                                type="checkbox"
                                                :checked="isTableAssignedToServer(table.id)"
                                                @change="toggleTableAssignment(business.id, table.id)"
                                                class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                            />
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ table.table_name }}</p>
                                                <p class="text-xs text-gray-500">{{ table.table_number }}</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <button
                                            @click="saveTableAssignments(business.id)"
                                            :disabled="savingTables"
                                            class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
                                        >
                                            {{ savingTables ? 'Saving...' : 'Save Table Assignments' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-if="businesses.length === 0" class="text-center text-gray-500 py-8">
                            No businesses available
                        </p>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button
                            @click="closeAssignModal"
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all"
                        >
                            Done
                        </button>
                    </div>
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
          <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Delete Server?</h3>
          <p class="text-gray-600 text-center mb-6">
            Are you sure you want to delete <strong>{{ serverToDelete?.first_name }} {{ serverToDelete?.last_name }}</strong>?
            This action cannot be undone.
          </p>
          <div class="flex gap-3">
            <button
              @click="closeDeleteModal"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all"
            >
              Cancel
            </button>
            <button
              @click="deleteServer"
              :disabled="deleting"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all disabled:opacity-50"
            >
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
      </div>
    </div>
  </VendorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import VendorLayout from '@/components/layouts/VendorLayout.vue'

const { vendor } = useApi()
const toast = useToast()

const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const loadingTables = ref(false)
const savingTables = ref(false)
const servers = ref([])
const businesses = ref([])
const businessTables = ref([])
const serverTableAssignments = ref([])
const expandedBusinessId = ref(null)
const selectedTableIds = ref(new Set())

const showServerModal = ref(false)
const showAssignModal = ref(false)
const showDeleteModal = ref(false)
const editingServer = ref(null)
const selectedServer = ref(null)
const serverToDelete = ref(null)

const serverForm = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone_number: '',
  password: '',
  business_link_ids: []
})

// Computed
const totalAssignments = computed(() => {
  return servers.value.reduce((total, server) => {
    return total + (server.assigned_businesses?.length || 0)
  }, 0)
})

const avgTablesPerServer = computed(() => {
  if (servers.value.length === 0) return 0
  const totalTables = servers.value.reduce((total, server) => {
    return total + (server.assigned_tables?.length || 0)
  }, 0)
  return (totalTables / servers.value.length).toFixed(1)
})

// Methods
const loadServers = async () => {
  try {
    loading.value = true
    const response = await vendor.getServers()
    servers.value = response.data?.data || []
  } catch (error) {
    console.error('Error loading servers:', error)
    toast.error('Failed to load servers')
  } finally {
    loading.value = false
  }
}

const loadBusinesses = async () => {
  try {
    const response = await vendor.getBusinessLinks()
    businesses.value = response.data?.data?.businesses || [];
    console.log('Loaded businesses:', businesses.value)
  } catch (error) {
    console.error('Error loading businesses:', error)
  }
}

const openCreateModal = () => {
  editingServer.value = null
  serverForm.value = {
    first_name: '',
    last_name: '',
    email: '',
    phone_number: '',
    password: '',
    business_link_ids: []
  }
  showServerModal.value = true
}

const openEditModal = (server) => {
  editingServer.value = server
  serverForm.value = {
    first_name: server.first_name,
    last_name: server.last_name,
    email: server.email,
    phone_number: server.phone_number,
    password: '',
    business_link_ids: []
  }
  showServerModal.value = true
}

const closeServerModal = () => {
  showServerModal.value = false
  editingServer.value = null
}

const saveServer = async () => {
  try {
    saving.value = true

    const payload = { ...serverForm.value }
    if (editingServer.value && !payload.password) {
      delete payload.password
    }
    if (editingServer.value) {
      delete payload.business_link_ids
    }

    if (editingServer.value) {
      await vendor.updateServer(editingServer.value.id, payload)
      toast.success('Server updated successfully!')
    } else {
      await vendor.createServer(payload)
      toast.success('Server created successfully!')
    }

    closeServerModal()
    await loadServers()
  } catch (error) {
    console.error('Error saving server:', error)
    const message = error.response?.data?.message || 'Failed to save server'
    toast.error(message)
  } finally {
    saving.value = false
  }
}

const confirmDelete = (server) => {
  serverToDelete.value = server
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  serverToDelete.value = null
}

const deleteServer = async () => {
  try {
    deleting.value = true
    await vendor.deleteServer(serverToDelete.value.id)
    toast.success('Server deleted successfully!')
    closeDeleteModal()
    await loadServers()
  } catch (error) {
    console.error('Error deleting server:', error)
    toast.error('Failed to delete server')
  } finally {
    deleting.value = false
  }
}

const openAssignModal = (server) => {
  selectedServer.value = server
  showAssignModal.value = true
}

const closeAssignModal = () => {
  showAssignModal.value = false
  selectedServer.value = null
}

const isServerAssignedToBusiness = (businessId) => {
  if (!selectedServer.value) return false
  return selectedServer.value.assigned_businesses?.some(
    assignment => assignment.business_link_id === businessId
  ) || false
}

const assignToBusiness = async (businessId) => {
  try {
    await vendor.assignServerToBusiness(selectedServer.value.id, {
      business_link_id: businessId
    })
    toast.success('Server assigned to business successfully!')
    await loadServers()
    // Update selectedServer with fresh data
    selectedServer.value = servers.value.find(s => s.id === selectedServer.value.id)
  } catch (error) {
    console.error('Error assigning server:', error)
    const message = error.response?.data?.message || 'Failed to assign server'
    toast.error(message)
  }
}

const unassignFromBusiness = async (businessId) => {
  try {
    await vendor.removeServerFromBusiness(selectedServer.value.id, {
      business_link_id: businessId
    })
    toast.success('Server removed from business successfully!')
    await loadServers()
    // Update selectedServer with fresh data
    selectedServer.value = servers.value.find(s => s.id === selectedServer.value.id)
    // Clear table data if this was the expanded business
    if (expandedBusinessId.value === businessId) {
      expandedBusinessId.value = null
      businessTables.value = []
      serverTableAssignments.value = []
    }
  } catch (error) {
    console.error('Error removing server:', error)
    toast.error('Failed to remove server from business')
  }
}

const toggleBusinessTables = async (businessId) => {
  if (expandedBusinessId.value === businessId) {
    expandedBusinessId.value = null
    businessTables.value = []
    serverTableAssignments.value = []
    selectedTableIds.value = new Set()
  } else {
    expandedBusinessId.value = businessId
    await loadBusinessTables(businessId)
  }
}

const loadBusinessTables = async (businessId) => {
  try {
    loadingTables.value = true

    // Load all tables for this business
    const tablesResponse = await vendor.getTables(businessId)
    businessTables.value = tablesResponse.data?.data || []

    // Load current server's table assignments for this business
    const assignmentsResponse = await vendor.getServerAssignments(businessId, selectedServer.value.id)
    serverTableAssignments.value = assignmentsResponse.data?.data || []

    // Initialize selectedTableIds with currently assigned tables
    selectedTableIds.value = new Set(
      serverTableAssignments.value.map(assignment => assignment.table_id)
    )
  } catch (error) {
    console.error('Error loading tables:', error)
    toast.error('Failed to load tables')
  } finally {
    loadingTables.value = false
  }
}

const isTableAssignedToServer = (tableId) => {
  return selectedTableIds.value.has(tableId)
}

const toggleTableAssignment = (businessId, tableId) => {
  if (selectedTableIds.value.has(tableId)) {
    selectedTableIds.value.delete(tableId)
  } else {
    selectedTableIds.value.add(tableId)
  }
  // Force reactivity
  selectedTableIds.value = new Set(selectedTableIds.value)
}

const saveTableAssignments = async (businessId) => {
  try {
    savingTables.value = true

    const currentlyAssignedIds = new Set(
      serverTableAssignments.value.map(a => a.table_id)
    )

    // Find tables to assign (newly selected)
    const tablesToAssign = [...selectedTableIds.value].filter(
      id => !currentlyAssignedIds.has(id)
    )

    // Find tables to unassign (previously selected but now deselected)
    const tablesToUnassign = [...currentlyAssignedIds].filter(
      id => !selectedTableIds.value.has(id)
    )

    // Assign new tables
    if (tablesToAssign.length > 0) {
      await vendor.bulkAssignServerToTables(businessId, {
        server_id: selectedServer.value.id,
        table_ids: tablesToAssign
      })
    }

    // Unassign removed tables
    for (const tableId of tablesToUnassign) {
      const assignment = serverTableAssignments.value.find(a => a.table_id === tableId)
      if (assignment) {
        await vendor.removeTableAssignment(businessId, assignment.id)
      }
    }

    toast.success('Table assignments saved successfully!')

    // Reload assignments
    await loadBusinessTables(businessId)
    await loadServers()

  } catch (error) {
    console.error('Error saving table assignments:', error)
    toast.error('Failed to save table assignments')
  } finally {
    savingTables.value = false
  }
}

const getBusinessAssignedTablesCount = (businessId) => {
  if (!selectedServer.value || !selectedServer.value.assigned_tables) return 0
  return selectedServer.value.assigned_tables.filter(
    assignment => assignment.business_link_id === businessId
  ).length
}

onMounted(async () => {
  await Promise.all([loadServers(), loadBusinesses()])
})
</script>
