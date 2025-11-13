<template>
  <AdminLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Server Management</h1>
          <p class="text-gray-600 mt-1">View and manage all servers (waiters/staff)</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Total Servers</p>
                <p class="text-3xl font-bold text-gray-900">{{ statistics.total_servers || 0 }}</p>
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
                <p class="text-sm text-gray-600 mb-1">Active Servers</p>
                <p class="text-3xl font-bold text-green-600">{{ statistics.active_servers || 0 }}</p>
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
                <p class="text-sm text-gray-600 mb-1">Inactive</p>
                <p class="text-3xl font-bold text-gray-600">{{ statistics.inactive_servers || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Assigned</p>
                <p class="text-3xl font-bold text-purple-600">{{ statistics.assigned_servers || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <LoadingSpinner size="lg" text="Loading servers..." />
        </div>

        <!-- Servers List -->
        <div v-else class="bg-white rounded-lg shadow-sm">
          <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">All Servers</h2>

            <div v-if="servers.length === 0" class="text-center py-12">
              <div class="text-6xl mb-4">👥</div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">No servers yet</h3>
              <p class="text-gray-600">Servers will appear here once vendors create them</p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Server
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Contact
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Businesses
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="server in servers" :key="server.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                          <span class="text-sm font-medium text-gray-600">
                            {{ server.first_name?.[0] }}{{ server.last_name?.[0] }}
                          </span>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ server.first_name }} {{ server.last_name }}
                          </div>
                          <div class="text-sm text-gray-500">ID: {{ server.id }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ server.email || 'N/A' }}</div>
                      <div class="text-sm text-gray-500">{{ server.phone_number || 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': server.status === 'active',
                          'bg-gray-100 text-gray-800': server.status === 'inactive'
                        }"
                      >
                        {{ server.status || 'active' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ server.business_servers?.length || 0 }} businesses
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        @click="confirmDelete(server)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Pagination -->
              <div v-if="pagination.total > pagination.per_page" class="px-6 py-4 flex items-center justify-between border-t">
                <div class="text-sm text-gray-700">
                  Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} servers
                </div>
                <div class="flex gap-2">
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="loadServers(page)"
                    :disabled="page === pagination.current_page"
                    class="px-3 py-1 rounded"
                    :class="page === pagination.current_page ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                  >
                    {{ page }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Delete Server?</h3>
            <p class="text-gray-600 mb-6">
              Are you sure you want to delete <strong>{{ serverToDelete?.first_name }} {{ serverToDelete?.last_name }}</strong>?
              This action cannot be undone.
            </p>
            <div class="flex gap-3 justify-end">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                @click="deleteServer"
                :disabled="deleting"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
              >
                {{ deleting ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import AdminLayout from '@/components/layouts/AdminLayout.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const { admin } = useApi()
const toast = useToast()

const loading = ref(true)
const deleting = ref(false)
const servers = ref([])
const statistics = ref({})
const showDeleteModal = ref(false)
const serverToDelete = ref(null)
const pagination = ref({
  current_page: 1,
  per_page: 50,
  total: 0,
  from: 0,
  to: 0
})

const visiblePages = computed(() => {
  const pages = []
  const total = Math.ceil(pagination.value.total / pagination.value.per_page)
  for (let i = 1; i <= total; i++) {
    pages.push(i)
  }
  return pages
})

const loadServers = async (page = 1) => {
  try {
    loading.value = true
    const [serversRes, statsRes] = await Promise.all([
      admin.getServers(),
      admin.getServerStatistics()
    ])

    const serversData = serversRes.data.data || serversRes.data
    servers.value = serversData.data || serversData || []

    if (serversData.meta || serversData.pagination) {
      const meta = serversData.meta || serversData.pagination
      pagination.value = {
        current_page: meta.current_page || page,
        per_page: meta.per_page || 50,
        total: meta.total || servers.value.length,
        from: meta.from || 1,
        to: meta.to || servers.value.length
      }
    }

    const statsData = statsRes.data.data || statsRes.data
    statistics.value = statsData
  } catch (error) {
    console.error('Error loading servers:', error)
    toast.error('Failed to load servers')
  } finally {
    loading.value = false
  }
}

const confirmDelete = (server) => {
  serverToDelete.value = server
  showDeleteModal.value = true
}

const deleteServer = async () => {
  try {
    deleting.value = true
    await admin.deleteServer(serverToDelete.value.id)
    toast.success('Server deleted successfully!')
    showDeleteModal.value = false
    serverToDelete.value = null
    await loadServers()
  } catch (error) {
    console.error('Error deleting server:', error)
    toast.error('Failed to delete server')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  loadServers()
})
</script>
