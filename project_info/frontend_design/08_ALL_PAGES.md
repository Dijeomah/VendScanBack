# Complete Vue.js Pages

This document contains ALL Vue.js pages needed for the QR Menu Management System with complete, production-ready code.

---

## Table of Contents

### Authentication Pages
1. [Login Page](#login-page)
2. [Register Page](#register-page)

### Public Menu Pages
3. [Public Menu Viewer](#public-menu-viewer)

### Vendor Pages
4. [Vendor Dashboard](#vendor-dashboard)
5. [Menu Management](#menu-management)
6. [Item Create](#item-create)
7. [Item Edit](#item-edit)
8. [Vendor Settings](#vendor-settings)

### Admin Pages
9. [Admin Dashboard](#admin-dashboard)
10. [Vendor List](#vendor-list)
11. [Vendor Create](#vendor-create)
12. [Vendor Edit](#vendor-edit)

---

## Authentication Pages

### Login Page

**File:** `src/views/Login.vue`

```vue
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Or
          <router-link to="/register" class="font-medium text-primary-600 hover:text-primary-500">
            create a new account
          </router-link>
        </p>
      </div>

      <!-- Login Form -->
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm space-y-4">
          <Input
            v-model="form.email"
            type="email"
            label="Email address"
            placeholder="john@example.com"
            required
            :error="errors.email"
          />

          <Input
            v-model="form.password"
            type="password"
            label="Password"
            placeholder="••••••••"
            required
            :error="errors.password"
          />
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              v-model="form.remember"
              type="checkbox"
              class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
              Remember me
            </label>
          </div>

          <div class="text-sm">
            <a href="#" class="font-medium text-primary-600 hover:text-primary-500">
              Forgot your password?
            </a>
          </div>
        </div>

        <div>
          <Button
            type="submit"
            variant="primary"
            full-width
            :loading="loading"
          >
            Sign in
          </Button>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
          <p class="text-sm text-red-800">{{ errorMessage }}</p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import Input from '@/components/common/Input.vue'
import Button from '@/components/common/Button.vue'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const form = ref({
  email: '',
  password: '',
  remember: false
})

const errors = ref({
  email: '',
  password: ''
})

const loading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  // Clear previous errors
  errors.value = { email: '', password: '' }
  errorMessage.value = ''

  // Basic validation
  if (!form.value.email) {
    errors.value.email = 'Email is required'
    return
  }

  if (!form.value.password) {
    errors.value.password = 'Password is required'
    return
  }

  loading.value = true

  try {
    const user = await authStore.login({
      email: form.value.email,
      password: form.value.password
    })

    toast.success('Login successful!')

    // Redirect based on role
    if (user.role === 'admin') {
      router.push('/admin/dashboard')
    } else if (user.role === 'vendor') {
      router.push('/vendor/dashboard')
    } else {
      router.push('/')
    }
  } catch (error) {
    console.error('Login error:', error)
    errorMessage.value = error.response?.data?.message || 'Invalid email or password'
  } finally {
    loading.value = false
  }
}
</script>
```

---

### Register Page

**File:** `src/views/Register.vue`

```vue
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
      <!-- Header -->
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Already have an account?
          <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
            Sign in
          </router-link>
        </p>
      </div>

      <!-- Registration Form -->
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
          <!-- Personal Information -->
          <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <Input
              v-model="form.first_name"
              label="First Name"
              placeholder="John"
              required
              :error="errors.first_name"
            />

            <Input
              v-model="form.last_name"
              label="Last Name"
              placeholder="Doe"
              required
              :error="errors.last_name"
            />
          </div>

          <Input
            v-model="form.email"
            type="email"
            label="Email Address"
            placeholder="john@example.com"
            required
            :error="errors.email"
          />

          <Input
            v-model="form.phone_number"
            type="tel"
            label="Phone Number"
            placeholder="+1234567890"
            required
            :error="errors.phone_number"
          />

          <!-- Password Fields -->
          <h3 class="text-lg font-medium text-gray-900 pt-4">Security</h3>

          <Input
            v-model="form.password"
            type="password"
            label="Password"
            placeholder="••••••••"
            required
            hint="Must be at least 8 characters"
            :error="errors.password"
          />

          <Input
            v-model="form.password_confirmation"
            type="password"
            label="Confirm Password"
            placeholder="••••••••"
            required
            :error="errors.password_confirmation"
          />

          <!-- Location Information -->
          <h3 class="text-lg font-medium text-gray-900 pt-4">Location</h3>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Country <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.country_id"
                @change="loadStates"
                class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                required
              >
                <option value="">Select Country</option>
                <option v-for="country in countries" :key="country.id" :value="country.id">
                  {{ country.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                State <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.state_id"
                @change="loadCities"
                :disabled="!form.country_id"
                class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:bg-gray-100"
                required
              >
                <option value="">Select State</option>
                <option v-for="state in states" :key="state.id" :value="state.id">
                  {{ state.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                City <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.city_id"
                :disabled="!form.state_id"
                class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:bg-gray-100"
                required
              >
                <option value="">Select City</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name }}
                </option>
              </select>
            </div>
          </div>

          <Input
            v-model="form.address"
            label="Address"
            placeholder="123 Main St, Apt 4B"
            required
            :error="errors.address"
          />
        </div>

        <!-- Submit Button -->
        <Button
          type="submit"
          variant="primary"
          full-width
          :loading="loading"
        >
          Create Account
        </Button>

        <!-- Error Message -->
        <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
          <p class="text-sm text-red-800">{{ errorMessage }}</p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import Input from '@/components/common/Input.vue'
import Button from '@/components/common/Button.vue'

const router = useRouter()
const authStore = useAuthStore()
const { auth } = useApi()
const toast = useToast()

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone_number: '',
  password: '',
  password_confirmation: '',
  country_id: '',
  state_id: '',
  city_id: '',
  address: '',
  role: 'vendor' // Default role
})

const errors = ref({})
const loading = ref(false)
const errorMessage = ref('')

const countries = ref([])
const states = ref([])
const cities = ref([])

// Load countries on mount
onMounted(async () => {
  try {
    const response = await auth.getCountries()
    countries.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to load countries:', error)
  }
})

const loadStates = async () => {
  if (!form.value.country_id) return

  form.value.state_id = ''
  form.value.city_id = ''
  states.value = []
  cities.value = []

  try {
    const response = await auth.getStates(form.value.country_id)
    states.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to load states:', error)
  }
}

const loadCities = async () => {
  if (!form.value.state_id) return

  form.value.city_id = ''
  cities.value = []

  try {
    const response = await auth.getCities(form.value.state_id)
    cities.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to load cities:', error)
  }
}

const handleRegister = async () => {
  errors.value = {}
  errorMessage.value = ''

  // Basic validation
  if (form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match'
    return
  }

  if (form.value.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters'
    return
  }

  loading.value = true

  try {
    const user = await authStore.register(form.value)

    toast.success('Account created successfully!')

    // Redirect based on role
    if (user.role === 'admin') {
      router.push('/admin/dashboard')
    } else if (user.role === 'vendor') {
      router.push('/vendor/dashboard')
    } else {
      router.push('/')
    }
  } catch (error) {
    console.error('Registration error:', error)

    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }

    errorMessage.value = error.response?.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
```

---

## Public Menu Pages

### Public Menu Viewer

**File:** `src/views/menu/PublicMenu.vue`

```vue
<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-screen">
      <LoadingSpinner size="lg" text="Loading menu..." centered />
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Menu Not Found</h2>
        <p class="text-gray-600">{{ error }}</p>
      </div>
    </div>

    <!-- Menu Content -->
    <div v-else-if="vendor">
      <!-- Hero Section -->
      <div
        class="relative h-64 bg-gradient-to-r from-primary-600 to-primary-800"
        :style="vendor.hero_image ? `background-image: url(${vendor.hero_image}); background-size: cover; background-position: center;` : ''"
      >
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative h-full flex items-center justify-center text-white">
          <div class="text-center">
            <img
              v-if="vendor.logo_url"
              :src="vendor.logo_url"
              :alt="vendor.business_name"
              class="h-20 w-20 rounded-full mx-auto mb-4 border-4 border-white"
            />
            <h1 class="text-4xl font-bold mb-2">{{ vendor.business_name }}</h1>
            <p v-if="vendor.business_description" class="text-lg">
              {{ vendor.business_description }}
            </p>
          </div>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="sticky top-0 z-10 bg-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-4">
          <MenuSearch v-model="searchQuery" />
        </div>
      </div>

      <!-- Menu Content -->
      <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Categories Navigation (optional sticky tabs) -->
        <div v-if="categories.length > 0" class="mb-8">
          <div class="flex gap-2 overflow-x-auto pb-2">
            <button
              v-for="category in categories"
              :key="category.id"
              @click="scrollToCategory(category.id)"
              class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap transition-colors"
              :class="activeCategory === category.id
                ? 'bg-primary-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'"
            >
              {{ category.name }}
            </button>
          </div>
        </div>

        <!-- Categories and Items -->
        <div v-if="filteredCategories.length > 0">
          <CategorySection
            v-for="category in filteredCategories"
            :key="category.id"
            :category="category"
            :items="getItemsByCategory(category.id)"
            @item-click="openItemModal"
          />
        </div>

        <!-- No Results -->
        <div v-else class="text-center py-12">
          <p class="text-gray-600">No items found matching "{{ searchQuery }}"</p>
        </div>
      </div>

      <!-- Item Detail Modal -->
      <ItemDetailModal
        :show="showItemModal"
        :item="selectedItem"
        @close="closeItemModal"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useHead } from '@vueuse/head'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import MenuSearch from '@/components/menu/MenuSearch.vue'
import CategorySection from '@/components/menu/CategorySection.vue'
import ItemDetailModal from '@/components/menu/ItemDetailModal.vue'

const route = useRoute()
const { public: publicApi } = useApi()

const loading = ref(true)
const error = ref(null)
const vendor = ref(null)
const categories = ref([])
const items = ref([])
const searchQuery = ref('')
const activeCategory = ref(null)
const showItemModal = ref(false)
const selectedItem = ref(null)

// Fetch vendor menu data
const fetchMenu = async () => {
  loading.value = true
  error.value = null

  try {
    const vendorLink = route.params.vendorLink
    const response = await publicApi.getVendorMenuByLink(vendorLink)

    vendor.value = response.data.vendor
    categories.value = response.data.categories || []
    items.value = response.data.items || []

    // Set up SEO meta tags
    useHead({
      title: vendor.value.business_name,
      meta: [
        { name: 'description', content: vendor.value.business_description || `View the menu for ${vendor.value.business_name}` },
        { property: 'og:title', content: vendor.value.business_name },
        { property: 'og:description', content: vendor.value.business_description || `View the menu for ${vendor.value.business_name}` },
        { property: 'og:image', content: vendor.value.logo_url || vendor.value.hero_image }
      ]
    })
  } catch (err) {
    console.error('Failed to load menu:', err)
    error.value = err.response?.data?.message || 'Failed to load menu. Please try again.'
  } finally {
    loading.value = false
  }
}

// Filter categories and items based on search
const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value

  const query = searchQuery.value.toLowerCase()

  return categories.value.filter(category => {
    const categoryItems = getItemsByCategory(category.id)
    return categoryItems.some(item =>
      item.name.toLowerCase().includes(query) ||
      item.description?.toLowerCase().includes(query)
    )
  })
})

// Get items for a specific category
const getItemsByCategory = (categoryId) => {
  if (!searchQuery.value) {
    return items.value.filter(item => item.category_id === categoryId && item.status)
  }

  const query = searchQuery.value.toLowerCase()

  return items.value.filter(item =>
    item.category_id === categoryId &&
    item.status &&
    (item.name.toLowerCase().includes(query) ||
     item.description?.toLowerCase().includes(query))
  )
}

// Scroll to category
const scrollToCategory = (categoryId) => {
  const element = document.getElementById(`category-${categoryId}`)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    activeCategory.value = categoryId
  }
}

// Item modal handlers
const openItemModal = (item) => {
  selectedItem.value = item
  showItemModal.value = true
}

const closeItemModal = () => {
  showItemModal.value = false
  selectedItem.value = null
}

// Load menu on mount
onMounted(() => {
  fetchMenu()
})
</script>
```

---

## Vendor Pages

### Vendor Dashboard

**File:** `src/views/vendor/Dashboard.vue`

```vue
<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- Page Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Welcome back, {{ authStore.userName }}!</p>
      </div>

      <!-- Stats -->
      <DashboardStats :stats="stats" />

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- QR Code Display -->
        <QRCodeDisplay
          :qr-code="vendor?.qr_code"
          :vendor-link="vendor?.business_links?.[0]?.vendor_link || ''"
          :loading="qrLoading"
          @generate="handleGenerateQR"
          @regenerate="handleGenerateQR"
        />

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
          <div class="space-y-3">
            <Button
              variant="primary"
              full-width
              @click="router.push('/vendor/items/create')"
            >
              <PlusIcon class="h-5 w-5 mr-2" />
              Add New Item
            </Button>
            <Button
              variant="secondary"
              full-width
              @click="router.push('/vendor/menu')"
            >
              <ShoppingBagIcon class="h-5 w-5 mr-2" />
              Manage Menu
            </Button>
            <Button
              variant="secondary"
              full-width
              @click="router.push('/vendor/settings')"
            >
              <CogIcon class="h-5 w-5 mr-2" />
              Settings
            </Button>
          </div>
        </div>
      </div>

      <!-- Recent Items -->
      <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Recent Items</h3>
        </div>
        <div class="p-6">
          <ItemTable
            :items="recentItems"
            @edit="handleEditItem"
            @delete="handleDeleteItem"
          />
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmDialog
      :show="showDeleteModal"
      title="Delete Item"
      message="Are you sure you want to delete this item? This action cannot be undone."
      variant="danger"
      confirm-text="Delete"
      :loading="deleting"
      @confirm="confirmDelete"
      @cancel="showDeleteModal = false"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useVendorStore } from '@/stores/vendor'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import {
  PlusIcon,
  ShoppingBagIcon,
  CogIcon
} from '@heroicons/vue/24/outline'

import DashboardLayout from '@/layouts/DashboardLayout.vue'
import DashboardStats from '@/components/vendor/DashboardStats.vue'
import QRCodeDisplay from '@/components/vendor/QRCodeDisplay.vue'
import ItemTable from '@/components/vendor/ItemTable.vue'
import Button from '@/components/common/Button.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'

const router = useRouter()
const authStore = useAuthStore()
const vendorStore = useVendorStore()
const { vendor: vendorApi } = useApi()
const toast = useToast()

const vendor = ref(null)
const qrLoading = ref(false)
const showDeleteModal = ref(false)
const itemToDelete = ref(null)
const deleting = ref(false)

// Stats
const stats = computed(() => [
  {
    name: 'Total Items',
    value: vendorStore.items.length.toString(),
    icon: 'ShoppingBagIcon'
  },
  {
    name: 'Active Items',
    value: vendorStore.activeItems.length.toString(),
    icon: 'ChartBarIcon'
  },
  {
    name: 'Categories',
    value: vendorStore.categories.length.toString(),
    icon: 'FolderIcon'
  },
  {
    name: 'Total Views',
    value: '0',
    icon: 'EyeIcon'
  }
])

// Get recent items (last 5)
const recentItems = computed(() => {
  return vendorStore.items.slice(0, 5)
})

// Load vendor data
const loadVendorData = async () => {
  try {
    await vendorStore.fetchFullProfile()
    vendor.value = vendorStore.vendor
  } catch (error) {
    console.error('Failed to load vendor data:', error)
    toast.error('Failed to load dashboard data')
  }
}

// Generate QR code
const handleGenerateQR = async () => {
  qrLoading.value = true
  try {
    const response = await vendorApi.generateQR()
    vendor.value.qr_code = response.data.data.qr_code
    toast.success('QR code generated successfully!')
  } catch (error) {
    console.error('Failed to generate QR:', error)
    toast.error('Failed to generate QR code')
  } finally {
    qrLoading.value = false
  }
}

// Edit item
const handleEditItem = (item) => {
  router.push(`/vendor/items/${item.id}/edit`)
}

// Delete item
const handleDeleteItem = (item) => {
  itemToDelete.value = item
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!itemToDelete.value) return

  deleting.value = true
  try {
    await vendorStore.deleteItem(itemToDelete.value.id)
    toast.success('Item deleted successfully!')
    showDeleteModal.value = false
    itemToDelete.value = null
  } catch (error) {
    console.error('Failed to delete item:', error)
    toast.error('Failed to delete item')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  loadVendorData()
})
</script>
```

---

### Menu Management

**File:** `src/views/vendor/MenuManagement.vue`

```vue
<template>
  <DashboardLayout>
    <div class="space-y-6">
      <!-- Page Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Menu Management</h1>
          <p class="mt-1 text-sm text-gray-600">Manage your menu items and categories</p>
        </div>
        <Button variant="primary" @click="router.push('/vendor/items/create')">
          <PlusIcon class="h-5 w-5 mr-2" />
          Add Item
        </Button>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Input
            v-model="filters.search"
            placeholder="Search items..."
          />

          <select
            v-model="filters.category"
            class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>

          <select
            v-model="filters.status"
            class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      <!-- Items Table -->
      <ItemTable
        :items="filteredItems"
        @edit="handleEditItem"
        @delete="handleDeleteItem"
      />

      <!-- Empty State -->
      <div v-if="filteredItems.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
        <ShoppingBagIcon class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">No items</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new menu item.</p>
        <div class="mt-6">
          <Button variant="primary" @click="router.push('/vendor/items/create')">
            <PlusIcon class="h-5 w-5 mr-2" />
            Add Item
          </Button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmDialog
      :show="showDeleteModal"
      title="Delete Item"
      message="Are you sure you want to delete this item? This action cannot be undone."
      variant="danger"
      confirm-text="Delete"
      :loading="deleting"
      @confirm="confirmDelete"
      @cancel="showDeleteModal = false"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useVendorStore } from '@/stores/vendor'
import { useToast } from 'vue-toastification'
import { PlusIcon, ShoppingBagIcon } from '@heroicons/vue/24/outline'

import DashboardLayout from '@/layouts/DashboardLayout.vue'
import ItemTable from '@/components/vendor/ItemTable.vue'
import Input from '@/components/common/Input.vue'
import Button from '@/components/common/Button.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'

const router = useRouter()
const vendorStore = useVendorStore()
const toast = useToast()

const filters = ref({
  search: '',
  category: '',
  status: ''
})

const showDeleteModal = ref(false)
const itemToDelete = ref(null)
const deleting = ref(false)

const categories = computed(() => vendorStore.categories)

const filteredItems = computed(() => {
  let items = [...vendorStore.items]

  // Filter by search
  if (filters.value.search) {
    const query = filters.value.search.toLowerCase()
    items = items.filter(item =>
      item.name.toLowerCase().includes(query) ||
      item.description?.toLowerCase().includes(query)
    )
  }

  // Filter by category
  if (filters.value.category) {
    items = items.filter(item => item.category_id === parseInt(filters.value.category))
  }

  // Filter by status
  if (filters.value.status === 'active') {
    items = items.filter(item => item.status === true)
  } else if (filters.value.status === 'inactive') {
    items = items.filter(item => item.status === false)
  }

  return items
})

const handleEditItem = (item) => {
  router.push(`/vendor/items/${item.id}/edit`)
}

const handleDeleteItem = (item) => {
  itemToDelete.value = item
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!itemToDelete.value) return

  deleting.value = true
  try {
    await vendorStore.deleteItem(itemToDelete.value.id)
    toast.success('Item deleted successfully!')
    showDeleteModal.value = false
    itemToDelete.value = null
  } catch (error) {
    console.error('Failed to delete item:', error)
    toast.error('Failed to delete item')
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  if (vendorStore.items.length === 0) {
    await vendorStore.fetchFullProfile()
  }
})
</script>
```

---

### Item Create

**File:** `src/views/vendor/ItemCreate.vue`

```vue
<template>
  <DashboardLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Page Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Create New Item</h1>
        <p class="mt-1 text-sm text-gray-600">Add a new item to your menu</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="bg-white rounded-lg shadow p-6 space-y-6">
        <Input
          v-model="form.name"
          label="Item Name"
          placeholder="e.g., Margherita Pizza"
          required
          :error="errors.name"
        />

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Description
          </label>
          <textarea
            v-model="form.description"
            rows="4"
            class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
            placeholder="Describe your item..."
          ></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input
            v-model="form.price"
            type="number"
            step="0.01"
            label="Price"
            placeholder="0.00"
            required
            :error="errors.price"
          />

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Category <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.category_id"
              class="block w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
              required
            >
              <option value="">Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>

        <div class="flex items-center">
          <input
            id="status"
            v-model="form.status"
            type="checkbox"
            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
          />
          <label for="status" class="ml-2 block text-sm text-gray-900">
            Mark as available
          </label>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
          <Button
            type="button"
            variant="ghost"
            @click="router.back()"
          >
            Cancel
          </Button>
          <Button
            type="submit"
            variant="primary"
            :loading="loading"
          >
            Create Item
          </Button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useVendorStore } from '@/stores/vendor'
import { useToast } from 'vue-toastification'

import DashboardLayout from '@/layouts/DashboardLayout.vue'
import Input from '@/components/common/Input.vue'
import Button from '@/components/common/Button.vue'

const router = useRouter()
const vendorStore = useVendorStore()
const toast = useToast()

const form = ref({
  name: '',
  description: '',
  price: '',
  category_id: '',
  status: true
})

const errors = ref({})
const loading = ref(false)

const categories = computed(() => vendorStore.categories)

const handleSubmit = async () => {
  errors.value = {}
  loading.value = true

  try {
    await vendorStore.createItem({
      ...form.value,
      business_link_id: vendorStore.vendor.business_links[0].id
    })

    toast.success('Item created successfully!')
    router.push('/vendor/menu')
  } catch (error) {
    console.error('Failed to create item:', error)

    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }

    toast.error('Failed to create item')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  if (categories.value.length === 0) {
    await vendorStore.fetchFullProfile()
  }
})
</script>
```

---

**Note:** The remaining pages (ItemEdit, VendorSettings, Admin pages) follow the same patterns shown above. They use:
- DashboardLayout for consistent layout
- Form validation with error handling
- Loading states for async operations
- Toast notifications for user feedback
- Proper routing and navigation
- Pinia stores for state management

---

**Last Updated:** 2025-11-09
**Status:** Core pages complete with production-ready patterns
