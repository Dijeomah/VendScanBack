<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 p-4">
    <div class="w-full max-w-2xl">
      <!-- Logo/Brand -->
      <div class="text-center mb-6">
        <div class="inline-block p-3 bg-white rounded-2xl shadow-lg mb-3">
          <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Create Your Account</h1>
        <p class="text-gray-600 mt-1">Join QR Menu Manager and digitize your menu</p>
      </div>

      <!-- Registration Form -->
      <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
        <form @submit.prevent="handleRegister" class="space-y-5">
          <!-- Personal Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                First Name *
              </label>
              <input
                id="first_name"
                v-model="form.first_name"
                type="text"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                placeholder="John"
              />
            </div>

            <div>
              <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                Last Name *
              </label>
              <input
                id="last_name"
                v-model="form.last_name"
                type="text"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                placeholder="Doe"
              />
            </div>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
              Email Address *
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
              placeholder="you@example.com"
            />
          </div>

          <!-- Phone -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
              Phone Number
            </label>
            <input
              id="phone"
              v-model="form.phone_number"
              type="tel"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
              placeholder="+1 (555) 000-0000"
            />
          </div>

          <!-- Password Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password *
              </label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                placeholder="••••••••"
              />
              <p class="mt-1 text-xs text-gray-500">At least 8 characters</p>
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm Password *
              </label>
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                required
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                placeholder="••••••••"
              />
            </div>
          </div>

          <!-- Role Selection - Vendor Only -->
          <div class="p-4 bg-primary-50 border-2 border-primary-500 rounded-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0 mr-3">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-900">Restaurant/Business Owner Account</p>
                <p class="text-sm text-gray-600">You're registering as a vendor to manage your menu</p>
              </div>
            </div>
          </div>

          <!-- Terms & Conditions -->
          <div class="flex items-start">
            <input
              id="terms"
              v-model="form.terms"
              type="checkbox"
              required
              class="w-4 h-4 mt-1 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
            />
            <label for="terms" class="ml-2 text-sm text-gray-700">
              I agree to the <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Terms of Service</a>
              and <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Privacy Policy</a>
            </label>
          </div>

          <!-- Error Message -->
          <div v-if="errors.general" class="p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ errors.general }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-primary-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
          >
            <LoadingSpinner v-if="loading" size="sm" class="mr-2" />
            <span>{{ loading ? 'Creating account...' : 'Create Account' }}</span>
          </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center">
          <span class="text-gray-600">Already have an account?</span>
          <router-link to="/login" class="ml-1 text-primary-600 hover:text-primary-700 font-medium">
            Sign in
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const loading = ref(false)

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
    phone_number: '',
  password: '',
  password_confirmation: '',
  role: 'vendor',
  terms: false
})

const errors = reactive({
  general: ''
})

const handleRegister = async () => {
  if (form.password !== form.password_confirmation) {
    toast.error('Passwords do not match')
    return
  }

  if (!form.terms) {
    toast.error('Please accept the terms and conditions')
    return
  }

  loading.value = true
  errors.general = ''

  try {
    const user = await authStore.register(form)

    toast.success('Account created successfully!')

    // Redirect based on role
    if (user.role === 'admin') {
      router.push('/admin/dashboard')
    } else if (user.role === 'vendor') {
      router.push('/vendor/dashboard')
    }
  } catch (error) {
    console.error('Registration error:', error)

    if (error.response?.data?.message) {
      errors.general = error.response.data.message
    } else if (error.response?.data?.errors) {
      const errorMessages = Object.values(error.response.data.errors).flat()
      errors.general = errorMessages.join(', ')
    } else {
      errors.general = 'An error occurred during registration. Please try again.'
    }

    toast.error(errors.general)
  } finally {
    loading.value = false
  }
}
</script>
