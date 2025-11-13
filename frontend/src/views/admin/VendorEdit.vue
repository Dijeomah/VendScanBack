<template>
  <AdminLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Vendor</h1>
          <p class="text-gray-600 mt-1">Update vendor account information</p>
        </div>
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <LoadingSpinner size="lg" text="Loading vendor..." />
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-white rounded-lg shadow-sm p-6">
        <div class="text-center">
          <div class="text-6xl mb-4">⚠️</div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Error Loading Vendor</h3>
          <p class="text-gray-600 mb-6">{{ error }}</p>
          <router-link
            to="/admin/vendors"
            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
          >
            Back to Vendors
          </router-link>
        </div>
      </div>

      <!-- Edit Form -->
      <div v-else class="bg-white rounded-lg shadow-sm p-6">
        <form @submit.prevent="handleSubmit">
          <!-- Personal Information -->
          <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- First Name -->
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                  First Name <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.first_name"
                  type="text"
                  id="first_name"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.first_name }"
                  placeholder="Enter first name"
                />
                <p v-if="errors.first_name" class="mt-1 text-sm text-red-500">{{ errors.first_name }}</p>
              </div>

              <!-- Last Name -->
              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                  Last Name <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.last_name"
                  type="text"
                  id="last_name"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.last_name }"
                  placeholder="Enter last name"
                />
                <p v-if="errors.last_name" class="mt-1 text-sm text-red-500">{{ errors.last_name }}</p>
              </div>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
            <div class="grid grid-cols-1 gap-4">
              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  Email Address <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  id="email"
                  required
                  maxlength="50"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.email }"
                  placeholder="vendor@example.com"
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email }}</p>
              </div>

              <!-- Phone Number -->
              <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                  Phone Number <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.phone_number"
                  type="tel"
                  id="phone_number"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.phone_number }"
                  placeholder="+1 (555) 123-4567"
                />
                <p v-if="errors.phone_number" class="mt-1 text-sm text-red-500">{{ errors.phone_number }}</p>
              </div>
            </div>
          </div>

          <!-- Business Information (Read-only) -->
          <div v-if="vendor.business_links && vendor.business_links.length > 0" class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h2>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-600">Business Name</p>
                  <p class="font-medium text-gray-900">{{ vendor.business_links[0].business_data?.business_name || vendor.business_links[0].business_link || 'Not set' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Business Link</p>
                  <p class="font-medium text-gray-900">{{ vendor.business_links[0].business_link || 'Not set' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Business Type</p>
                  <p class="font-medium text-gray-900">{{ vendor.business_links[0].business_type || 'Not set' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-600">Subdomain</p>
                  <p class="font-medium text-gray-900">{{ vendor.business_links[0].subdomain || 'Not set' }}</p>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-3">
                Business information can be managed by the vendor in their settings page
              </p>
            </div>
          </div>

          <!-- Info Box -->
          <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex">
              <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              <div class="text-sm text-blue-700">
                <p class="font-medium mb-1">Note:</p>
                <ul class="list-disc list-inside space-y-1">
                  <li>Password changes must be handled separately for security</li>
                  <li>Vendor can update their business information in their settings</li>
                  <li>Business media can be managed from the vendor's account</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end gap-3">
            <router-link
              to="/admin/vendors"
              class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="submitting"
              class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <LoadingSpinner v-if="submitting" size="sm" />
              <span>{{ submitting ? 'Updating...' : 'Update Vendor' }}</span>
            </button>
          </div>
        </form>
      </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import AdminLayout from '@/components/layouts/AdminLayout.vue'

const router = useRouter()
const route = useRoute()
const { admin } = useApi()
const toast = useToast()

const loading = ref(true)
const submitting = ref(false)
const error = ref(null)
const vendor = ref({})

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone_number: ''
})

const errors = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone_number: ''
})

const clearErrors = () => {
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })
}

const loadVendor = async () => {
  try {
    const vendorId = route.params.id

    if (!vendorId) {
      error.value = 'Vendor ID is required'
      loading.value = false
      return
    }

    const response = await admin.getVendor(vendorId)
    const data = response.data.data || response.data

    vendor.value = data

    // Pre-populate form
    form.first_name = data.first_name || ''
    form.last_name = data.last_name || ''
    form.email = data.email || ''
    form.phone_number = data.phone_number || ''

  } catch (err) {
    console.error('Error loading vendor:', err)
    error.value = err.response?.data?.message || 'Failed to load vendor information'
  } finally {
    loading.value = false
  }
}

const validateForm = () => {
  clearErrors()
  let isValid = true

  // Validate first name
  if (!form.first_name.trim()) {
    errors.first_name = 'First name is required'
    isValid = false
  }

  // Validate last name
  if (!form.last_name.trim()) {
    errors.last_name = 'Last name is required'
    isValid = false
  }

  // Validate email
  if (!form.email.trim()) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  } else if (form.email.length > 50) {
    errors.email = 'Email must not exceed 50 characters'
    isValid = false
  }

  // Validate phone number
  if (!form.phone_number.trim()) {
    errors.phone_number = 'Phone number is required'
    isValid = false
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) {
    toast.error('Please fix the errors in the form')
    return
  }

  submitting.value = true

  try {
    const payload = {
      first_name: form.first_name,
      last_name: form.last_name,
      email: form.email,
      phone_number: form.phone_number
    }

    const response = await admin.updateVendor(route.params.id, payload)

    toast.success('Vendor updated successfully!')
    router.push('/admin/vendors')
  } catch (err) {
    console.error('Error updating vendor:', err)

    // Handle validation errors from backend
    if (err.response?.data?.errors) {
      const backendErrors = err.response.data.errors
      Object.keys(backendErrors).forEach(key => {
        if (errors.hasOwnProperty(key)) {
          errors[key] = Array.isArray(backendErrors[key])
            ? backendErrors[key][0]
            : backendErrors[key]
        }
      })
      toast.error('Please fix the validation errors')
    } else {
      toast.error(err.response?.data?.message || 'Failed to update vendor')
    }
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadVendor()
})
</script>

<style scoped>
/* Custom styles if needed */
</style>
