<template>
  <AdminLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Table Management</h1>
          <p class="text-gray-600 mt-1">View and manage all tables across businesses</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Total Tables</p>
                <p class="text-3xl font-bold text-gray-900">{{ statistics.total_tables || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Active Tables</p>
                <p class="text-3xl font-bold text-green-600">{{ statistics.active_tables || 0 }}</p>
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
                <p class="text-sm text-gray-600 mb-1">Occupied</p>
                <p class="text-3xl font-bold text-orange-600">{{ statistics.occupied_tables || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-1">Businesses</p>
                <p class="text-3xl font-bold text-purple-600">{{ statistics.tables_by_business?.length || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <LoadingSpinner size="lg" text="Loading tables..." />
        </div>

        <!-- Tables List -->
        <div v-else class="bg-white rounded-lg shadow-sm">
          <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">All Tables</h2>

            <div v-if="tables.length === 0" class="text-center py-12">
              <div class="text-6xl mb-4">📋</div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">No tables yet</h3>
              <p class="text-gray-600">Tables will appear here once vendors create them</p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Table
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Business
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Seats
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="table in tables" :key="table.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">Table {{ table.table_number }}</div>
                      <div v-if="table.table_name" class="text-sm text-gray-500">{{ table.table_name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ table.business_link?.business_data?.business_name || table.business_link?.business_link || 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ table.seats || 'N/A' }} seats
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': table.status === 'active',
                          'bg-orange-100 text-orange-800': table.status === 'occupied',
                          'bg-gray-100 text-gray-800': table.status === 'inactive'
                        }"
                      >
                        {{ table.status || 'active' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        @click="confirmDelete(table)"
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
                  Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} tables
                </div>
                <div class="flex gap-2">
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="loadTables(page)"
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
            <h3 class="text-lg font-bold text-gray-900 mb-4">Delete Table?</h3>
            <p class="text-gray-600 mb-6">
              Are you sure you want to delete <strong>Table {{ tableToDelete?.table_number }}</strong>?
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
                @click="deleteTable"
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
const tables = ref([])
const statistics = ref({})
const showDeleteModal = ref(false)
const tableToDelete = ref(null)
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

const loadTables = async (page = 1) => {
  try {
    loading.value = true
    const [tablesRes, statsRes] = await Promise.all([
      admin.getTables(),
      admin.getTableStatistics()
    ])

    const tablesData = tablesRes.data.data || tablesRes.data
    tables.value = tablesData.data || tablesData || []

    if (tablesData.meta || tablesData.pagination) {
      const meta = tablesData.meta || tablesData.pagination
      pagination.value = {
        current_page: meta.current_page || page,
        per_page: meta.per_page || 50,
        total: meta.total || tables.value.length,
        from: meta.from || 1,
        to: meta.to || tables.value.length
      }
    }

    const statsData = statsRes.data.data || statsRes.data
    statistics.value = statsData
  } catch (error) {
    console.error('Error loading tables:', error)
    toast.error('Failed to load tables')
  } finally {
    loading.value = false
  }
}

const confirmDelete = (table) => {
  tableToDelete.value = table
  showDeleteModal.value = true
}

const deleteTable = async () => {
  try {
    deleting.value = true
    await admin.deleteTable(tableToDelete.value.id)
    toast.success('Table deleted successfully!')
    showDeleteModal.value = false
    tableToDelete.value = null
    await loadTables()
  } catch (error) {
    console.error('Error deleting table:', error)
    toast.error('Failed to delete table')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  loadTables()
})
</script>
