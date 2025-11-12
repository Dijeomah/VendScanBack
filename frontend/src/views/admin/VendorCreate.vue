<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Vendor</h1>
            <p class="text-sm text-gray-600 mt-1">Add a new vendor account</p>
          </div>
          <router-link
            to="/admin/vendors"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
          >
            Back to Vendors
          </router-link>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm p-6">
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

          <!-- Account Security -->
          <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Security</h2>
            <div class="grid grid-cols-1 gap-4">
              <!-- Password -->
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                  Password <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  id="password"
                  required
                  minlength="8"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.password }"
                  placeholder="Minimum 8 characters"
                />
                <p v-if="errors.password" class="mt-1 text-sm text-red-500">{{ errors.password }}</p>
                <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
              </div>

              <!-- Confirm Password -->
              <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                  Confirm Password <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.confirm_password"
                  type="password"
                  id="confirm_password"
                  required
                  minlength="8"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  :class="{ 'border-red-500': errors.confirm_password }"
                  placeholder="Re-enter password"
                />
                <p v-if="errors.confirm_password" class="mt-1 text-sm text-red-500">{{ errors.confirm_password }}</p>
              </div>
            </div>
          </div>

          <!-- Info Box -->
          <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex">
              <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              <div class="text-sm text-blue-700">
                <p class="font-medium mb-1">After creating the vendor account:</p>
                <ul class="list-disc list-inside space-y-1">
                  <li>The vendor will receive their login credentials via email</li>
                  <li>They can complete their business information in their settings</li>
                  <li>Business media and QR code can be configured later</li>
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
              <span>{{ submitting ? 'Creating...' : 'Create Vendor' }}</span>
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const router = useRouter()
const { admin } = useApi()
const toast = useToast()

const submitting = ref(false)

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone_number: '',
  password: '',
  confirm_password: ''
})

const errors = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone_number: '',
  password: '',
  confirm_password: ''
})

const clearErrors = () => {
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })
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

  // Validate password
  if (!form.password) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters'
    isValid = false
  }

  // Validate password confirmation
  if (!form.confirm_password) {
    errors.confirm_password = 'Please confirm your password'
    isValid = false
  } else if (form.password !== form.confirm_password) {
    errors.confirm_password = 'Passwords do not match'
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
      phone_number: form.phone_number,
      password: form.password
    }

    const response = await admin.createVendor(payload)

    toast.success('Vendor created successfully!')
    router.push('/admin/vendors')
  } catch (error) {
    console.error('Error creating vendor:', error)

    // Handle validation errors from backend
    if (error.response?.data?.errors) {
      const backendErrors = error.response.data.errors
      Object.keys(backendErrors).forEach(key => {
        if (errors.hasOwnProperty(key)) {
          errors[key] = Array.isArray(backendErrors[key])
            ? backendErrors[key][0]
            : backendErrors[key]
        }
      })
      toast.error('Please fix the validation errors')
    } else {
      toast.error(error.response?.data?.message || 'Failed to create vendor')
    }
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
/* Custom styles if needed */
</style>
