<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Menu Management</h1>
            <p class="text-sm text-gray-600 mt-1">Manage your menu items and categories</p>
          </div>
          <div class="flex gap-3">
            <router-link
              to="/vendor/dashboard"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
            >
              Back to Dashboard
            </router-link>
            <router-link
              to="/vendor/items/create"
              class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Add New Item
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <LoadingSpinner size="lg" text="Loading menu items..." />
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search items by name, description, or category..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>

            <!-- Status Filter -->
            <div>
              <select
                v-model="statusFilter"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              >
                <option value="all">All Status</option>
                <option value="active">Active Only</option>
                <option value="inactive">Inactive Only</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Total Items</p>
            <p class="text-2xl font-bold text-gray-900">{{ vendorStore.totalItems }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ vendorStore.activeItems.length }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Inactive</p>
            <p class="text-2xl font-bold text-red-600">{{ vendorStore.inactiveItems.length }}</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-sm text-gray-600">Categories</p>
            <p class="text-2xl font-bold text-purple-600">{{ vendorStore.totalCategories }}</p>
          </div>
        </div>

        <!-- No Items -->
        <div v-if="filteredItems.length === 0" class="bg-white rounded-lg shadow-sm p-12 text-center">
          <div class="text-6xl mb-4">📋</div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">
            {{ searchQuery ? 'No items found' : 'No menu items yet' }}
          </h3>
          <p class="text-gray-600 mb-6">
            {{ searchQuery ? 'Try adjusting your search or filters' : 'Get started by adding your first menu item' }}
          </p>
          <router-link
            v-if="!searchQuery"
            to="/vendor/items/create"
            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Your First Item
          </router-link>
        </div>

        <!-- Items Table -->
        <div v-else class="bg-white rounded-lg shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Item
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Category
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Price
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
                <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gray-50">
                  <!-- Item Info -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div v-if="item.image" class="flex-shrink-0 h-12 w-12">
                        <img :src="item.image" :alt="item.title" class="h-12 w-12 rounded-lg object-cover" />
                      </div>
                      <div :class="item.image ? 'ml-4' : ''">
                        <div class="text-sm font-medium text-gray-900">{{ item.title }}</div>
                        <div v-if="item.description" class="text-sm text-gray-500 truncate max-w-xs">
                          {{ item.description }}
                        </div>
                      </div>
                    </div>
                  </td>

                  <!-- Category -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="item.category" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-800">
                      {{ item.category.category_name }}
                    </span>
                    <span v-else class="text-sm text-gray-400">No category</span>
                  </td>

                  <!-- Price -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">${{ formatPrice(item.price) }}</div>
                  </td>

                  <!-- Status -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                        item.status
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ item.status ? 'Active' : 'Inactive' }}
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                      <router-link
                        :to="`/vendor/items/${item.id}/edit`"
                        class="text-primary-600 hover:text-primary-900 p-2 hover:bg-primary-50 rounded-lg transition-colors"
                        title="Edit"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </router-link>
                      <button
                        @click="confirmDelete(item)"
                        class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded-lg transition-colors"
                        title="Delete"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
    </main>

    <!-- Delete Confirmation Modal -->
    <Transition name="modal">
      <div v-if="itemToDelete" class="fixed inset-0 z-50 overflow-y-auto" @click.self="itemToDelete = null">
        <div class="flex items-center justify-center min-h-screen p-4">
          <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

          <div class="relative bg-white rounded-lg max-w-md w-full shadow-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Delete Menu Item?</h3>
            <p class="text-gray-600 mb-6">
              Are you sure you want to delete "<strong>{{ itemToDelete.title }}</strong>"? This action cannot be undone.
            </p>

            <div class="flex gap-3 justify-end">
              <button
                @click="itemToDelete = null"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
              >
                Cancel
              </button>
              <button
                @click="deleteItem"
                :disabled="deleting"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <LoadingSpinner v-if="deleting" size="sm" />
                <span>{{ deleting ? 'Deleting...' : 'Delete' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useVendorStore } from '@/stores/vendor'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const vendorStore = useVendorStore()
const toast = useToast()

const loading = ref(true)
const searchQuery = ref('')
const statusFilter = ref('all')
const itemToDelete = ref(null)
const deleting = ref(false)

const filteredItems = computed(() => {
  let items = vendorStore.items

  // Filter by status
  if (statusFilter.value === 'active') {
    items = items.filter(item => item.status)
  } else if (statusFilter.value === 'inactive') {
    items = items.filter(item => !item.status)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    items = items.filter(item =>
      item.title?.toLowerCase().includes(query) ||
      item.description?.toLowerCase().includes(query) ||
      item.category?.category_name?.toLowerCase().includes(query)
    )
  }

  return items
})

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
}

const confirmDelete = (item) => {
  itemToDelete.value = item
}

const deleteItem = async () => {
  if (!itemToDelete.value) return

  deleting.value = true

  try {
    await vendorStore.deleteItem(itemToDelete.value.id)
    toast.success('Item deleted successfully!')
    itemToDelete.value = null
  } catch (error) {
    console.error('Error deleting item:', error)
    toast.error('Failed to delete item. Please try again.')
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  try {
    await vendorStore.fetchFullProfile()
  } catch (error) {
    console.error('Error loading menu items:', error)
    toast.error('Failed to load menu items')
  } finally {
    loading.value = false
  }
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
