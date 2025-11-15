<template>
  <VendorLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Table Management</h1>
              <p class="text-gray-600 mt-1">Manage tables and generate QR codes</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
              <button
                @click="showBulkCreateModal = true"
                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Bulk Create
              </button>
              <button
                @click="showCreateModal = true"
                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Table
              </button>
            </div>
          </div>
        </div>
      <!-- Business Selector -->
      <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Business</label>
        <select
          v-model="selectedBusinessId"
          @change="loadTables"
          class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
        >
          <option value="">-- Select a business --</option>
          <option v-for="business in businesses" :key="business.id" :value="business.id">
            {{ business.business_data?.business_name || business.business_link }}
          </option>
        </select>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <LoadingSpinner size="lg" text="Loading tables..." />
      </div>

      <!-- No Business Selected -->
      <div v-else-if="!selectedBusinessId" class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="flex justify-center mb-4">
          <BuildingIcon :size="96" class="text-gray-400" />
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Select a Business</h3>
        <p class="text-gray-600">Please select a business to manage its tables</p>
      </div>

      <!-- Tables List -->
      <div v-else-if="!loading && tables.length > 0">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-600">Total Tables</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ tables.length }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ activeTables }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-600">Occupied</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ occupiedTables }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-600">Total Seats</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalSeats }}</p>
          </div>
        </div>

        <!-- Tables Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="table in tables"
            :key="table.id"
            class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ table.table_number }}</h3>
                <p v-if="table.table_name" class="text-sm text-gray-600">{{ table.table_name }}</p>
              </div>
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  table.status === 'active' ? 'bg-green-100 text-green-700' :
                  table.status === 'occupied' ? 'bg-yellow-100 text-yellow-700' :
                  table.status === 'reserved' ? 'bg-blue-100 text-blue-700' :
                  'bg-gray-100 text-gray-700'
                ]"
              >
                {{ table.status }}
              </span>
            </div>

            <div class="space-y-2 mb-4">
              <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>{{ table.seats }} seats</span>
              </div>
              <div v-if="table.server_assignments && table.server_assignments.length > 0" class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ table.server_assignments.length }} server(s)</span>
              </div>
            </div>

            <div v-if="table.notes" class="mb-4 p-2 bg-gray-50 rounded text-sm text-gray-600">
              {{ table.notes }}
            </div>

            <!-- QR Code Preview -->
            <div v-if="table.table_qr_code" class="mb-4 flex justify-center">
              <img :src="table.table_qr_code" alt="Table QR Code" class="w-32 h-32 border rounded" />
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
              <button
                @click="viewQRCode(table)"
                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-all"
              >
                View QR
              </button>
              <button
                @click="editTable(table)"
                class="flex-1 px-3 py-2 text-sm font-medium text-primary-700 bg-primary-50 rounded hover:bg-primary-100 transition-all"
              >
                Edit
              </button>
              <button
                @click="confirmDelete(table)"
                class="flex-1 px-3 py-2 text-sm font-medium text-red-700 bg-red-50 rounded hover:bg-red-100 transition-all"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!loading && tables.length === 0" class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="text-6xl mb-4">🪑</div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Tables Yet</h3>
        <p class="text-gray-600 mb-6">Create tables for your business to get started</p>
        <button
          @click="showCreateModal = true"
          class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
        >
          Create Your First Table
        </button>
      </div>
      </div>
    </div>

    <!-- Create Table Modal -->
    <Transition name="modal">
      <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showCreateModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
          <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

          <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Add New Table</h3>

            <form @submit.prevent="createTable" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Table Number *</label>
                <input
                  v-model="tableForm.table_number"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="e.g., Table 1, T-05"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Table Name (Optional)</label>
                <input
                  v-model="tableForm.table_name"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="e.g., Window Booth"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Seats</label>
                <input
                  v-model.number="tableForm.seats"
                  type="number"
                  min="1"
                  max="50"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="4"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea
                  v-model="tableForm.notes"
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="Any special notes about this table..."
                ></textarea>
              </div>

              <div class="flex gap-3 pt-4">
                <button
                  type="button"
                  @click="showCreateModal = false"
                  class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="submitting"
                  class="flex-1 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 disabled:opacity-50"
                >
                  {{ submitting ? 'Creating...' : 'Create Table' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Edit Table Modal -->
    <Transition name="modal">
      <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showEditModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
          <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

          <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Edit Table</h3>

            <form @submit.prevent="updateTable" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Table Number *</label>
                <input
                  v-model="tableForm.table_number"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="e.g., Table 1, T-05"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Table Name (Optional)</label>
                <input
                  v-model="tableForm.table_name"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="e.g., Window Booth"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Seats</label>
                <input
                  v-model.number="tableForm.seats"
                  type="number"
                  min="1"
                  max="50"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="4"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea
                  v-model="tableForm.notes"
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="Any special notes about this table..."
                ></textarea>
              </div>

              <div class="flex gap-3 pt-4">
                <button
                  type="button"
                  @click="showEditModal = false"
                  class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="submitting"
                  class="flex-1 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 disabled:opacity-50"
                >
                  {{ submitting ? 'Updating...' : 'Update Table' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Bulk Create Modal -->
    <Transition name="modal">
      <div v-if="showBulkCreateModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showBulkCreateModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
          <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

          <div class="relative bg-white rounded-2xl max-w-lg w-full shadow-xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Bulk Create Tables</h3>

            <form @submit.prevent="bulkCreateTables" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Tables *</label>
                <input
                  v-model.number="bulkForm.count"
                  type="number"
                  min="1"
                  max="100"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="10"
                />
              </div>

<!--              <div>-->
<!--                <label class="block text-sm font-medium text-gray-700 mb-2">Table Prefix</label>-->
<!--                <input-->
<!--                  v-model="bulkForm.prefix"-->
<!--                  type="text"-->
<!--                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"-->
<!--                  placeholder="Table"-->
<!--                />-->
<!--                <p class="text-xs text-gray-500 mt-1">Tables will be created as: {{ bulkForm.prefix }} 1, {{ bulkForm.prefix }} 2, etc.</p>-->
<!--              </div>-->

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seats Per Table</label>
                <input
                  v-model.number="bulkForm.seats_per_table"
                  type="number"
                  min="1"
                  max="50"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="4"
                />
              </div>

              <div class="flex gap-3 pt-4">
                <button
                  type="button"
                  @click="showBulkCreateModal = false"
                  class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="submitting"
                  class="flex-1 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50"
                >
                  {{ submitting ? 'Creating...' : `Create ${bulkForm.count} Tables` }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Transition>

    <!-- View QR Modal -->
    <Transition name="modal">
      <div v-if="showQRModal && selectedTable" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showQRModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
          <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

          <div class="relative bg-white rounded-2xl max-w-md w-full shadow-xl p-6 text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ selectedTable.table_number }}</h3>
            <p class="text-sm text-gray-600 mb-6">{{ selectedTable.qr_code_url }}</p>

            <img :src="selectedTable.table_qr_code" alt="Table QR Code" class="w-64 h-64 mx-auto border rounded-lg mb-6" />

            <button
              @click="downloadQR(selectedTable)"
              class="w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 mb-2"
            >
              Download QR Code
            </button>
            <button
              @click="showQRModal = false"
              class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirmation Modal -->
    <Transition name="modal">
      <div v-if="showDeleteModal && tableToDelete" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showDeleteModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
          <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

          <div class="relative bg-white rounded-2xl max-w-md w-full shadow-xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Table?</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete <strong>{{ tableToDelete.table_number }}</strong>? This action cannot be undone.</p>

            <div class="flex gap-3">
              <button
                @click="showDeleteModal = false"
                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                @click="deleteTable"
                :disabled="submitting"
                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50"
              >
                {{ submitting ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </VendorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import VendorLayout from '@/components/layouts/VendorLayout.vue'
import BuildingIcon from '@/components/icons/BuildingIcon.vue'

const { vendor } = useApi()
const toast = useToast()

const loading = ref(false)
const submitting = ref(false)
const businesses = ref([])
const selectedBusinessId = ref('')
const tables = ref([])

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showBulkCreateModal = ref(false)
const showQRModal = ref(false)
const showDeleteModal = ref(false)
const selectedTable = ref(null)
const tableToEdit = ref(null)
const tableToDelete = ref(null)

const tableForm = ref({
  table_number: '',
  table_name: '',
  seats: 4,
  notes: ''
})

const bulkForm = ref({
  count: 10,
  prefix: null,
  seats_per_table: 4
})

const activeTables = computed(() => tables.value.filter(t => t.status === 'active').length)
const occupiedTables = computed(() => tables.value.filter(t => t.status === 'occupied').length)
const totalSeats = computed(() => tables.value.reduce((sum, t) => sum + (t.seats || 0), 0))

const loadBusinesses = async () => {
  try {
    const response = await vendor.getFullProfile()
    businesses.value = response.data?.data?.business_links || response.data?.business_links || []
  } catch (error) {
    console.error('Error loading businesses:', error)
    toast.error('Failed to load businesses')
  }
}

const loadTables = async () => {
  if (!selectedBusinessId.value) {
    tables.value = []
    return
  }

  loading.value = true
  try {
    const response = await vendor.getTables(selectedBusinessId.value)
    tables.value = response.data?.data || response.data || []
  } catch (error) {
    console.error('Error loading tables:', error)
    toast.error('Failed to load tables')
  } finally {
    loading.value = false
  }
}

const createTable = async () => {
  submitting.value = true
  try {
    await vendor.createTable(selectedBusinessId.value, tableForm.value)
    toast.success('Table created successfully!')
    showCreateModal.value = false
    tableForm.value = { table_number: '', table_name: '', seats: 4, notes: '' }
    await loadTables()
  } catch (error) {
    console.error('Error creating table:', error)
    toast.error(error.response?.data?.message || 'Failed to create table')
  } finally {
    submitting.value = false
  }
}

const bulkCreateTables = async () => {
  submitting.value = true
  try {
    const response = await vendor.bulkCreateTables(selectedBusinessId.value, bulkForm.value)
    const data = response.data?.data || response.data
    const count = bulkForm.value.count
    const estimatedTime = data.estimated_time || Math.ceil(count / 2)

    toast.success(`Creating ${count} tables in the background. This may take ${estimatedTime} seconds. The page will auto-refresh.`)
    showBulkCreateModal.value = false
    bulkForm.value = { count: 10, prefix: null, seats_per_table: 4 }

    // Auto-refresh after estimated time + buffer
    const refreshDelay = (parseInt(estimatedTime) + 3) * 1000 // Add 3 second buffer

    setTimeout(async () => {
      await loadTables()
      toast.info('Tables refreshed! Your new tables should now appear.')
    }, refreshDelay)

  } catch (error) {
    console.error('Error bulk creating tables:', error)
    toast.error(error.response?.data?.message || 'Failed to create tables')
  } finally {
    submitting.value = false
  }
}

const editTable = (table) => {
  tableToEdit.value = table
  tableForm.value = {
    table_number: table.table_number,
    table_name: table.table_name || '',
    seats: table.seats || 4,
    notes: table.notes || ''
  }
  showEditModal.value = true
}

const updateTable = async () => {
  if (!tableToEdit.value) return

  submitting.value = true
  try {
    await vendor.updateTable(selectedBusinessId.value, tableToEdit.value.id, tableForm.value)
    toast.success('Table updated successfully!')
    showEditModal.value = false
    tableToEdit.value = null
    tableForm.value = { table_number: '', table_name: '', seats: 4, notes: '' }
    await loadTables()
  } catch (error) {
    console.error('Error updating table:', error)
    toast.error(error.response?.data?.message || 'Failed to update table')
  } finally {
    submitting.value = false
  }
}

const viewQRCode = (table) => {
  selectedTable.value = table
  showQRModal.value = true
}

const downloadQR = (table) => {
  const link = document.createElement('a')
  link.href = table.table_qr_code
  link.download = `${table.table_number}_QR.svg`
  link.click()
  toast.success('QR code downloaded!')
}

const confirmDelete = (table) => {
  tableToDelete.value = table
  showDeleteModal.value = true
}

const deleteTable = async () => {
  submitting.value = true
  try {
    await vendor.deleteTable(selectedBusinessId.value, tableToDelete.value.id)
    toast.success('Table deleted successfully!')
    showDeleteModal.value = false
    tableToDelete.value = null
    await loadTables()
  } catch (error) {
    console.error('Error deleting table:', error)
    toast.error(error.response?.data?.message || 'Failed to delete table')
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadBusinesses()
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
