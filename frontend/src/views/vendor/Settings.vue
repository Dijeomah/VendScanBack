<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
            <p class="text-sm text-gray-600 mt-1">Manage your profile and business information</p>
          </div>
          <router-link
            to="/vendor/dashboard"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
          >
            Back to Dashboard
          </router-link>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <LoadingSpinner size="lg" text="Loading settings..." />
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
          <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  'flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors',
                  activeTab === tab.id
                    ? 'border-primary-500 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ tab.name }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Profile Settings -->
        <div v-show="activeTab === 'profile'" class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Profile Information</h2>
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- First Name -->
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                  First Name <span class="text-red-500">*</span>
                </label>
                <input
                  id="first_name"
                  v-model="profileForm.first_name"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
              </div>

              <!-- Last Name -->
              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                  Last Name <span class="text-red-500">*</span>
                </label>
                <input
                  id="last_name"
                  v-model="profileForm.last_name"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  Email <span class="text-red-500">*</span>
                </label>
                <input
                  id="email"
                  v-model="profileForm.email"
                  type="email"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
              </div>

              <!-- Phone -->
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                  Phone Number
                </label>
                <input
                  id="phone"
                  v-model="profileForm.phone_number"
                  type="tel"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
              <button
                type="submit"
                :disabled="profileSubmitting"
                class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <LoadingSpinner v-if="profileSubmitting" size="sm" />
                <span>{{ profileSubmitting ? 'Saving...' : 'Save Changes' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Business Settings -->
        <div v-show="activeTab === 'business'" class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Business Information</h2>
          <form @submit.prevent="updateBusiness" class="space-y-6">
            <!-- Business Name -->
            <div>
              <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">
                Business Name <span class="text-red-500">*</span>
              </label>
              <input
                id="business_name"
                v-model="businessForm.business_name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>

            <!-- Business Type -->
            <div>
              <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">
                Business Type
              </label>
              <input
                id="business_type"
                v-model="businessForm.business_type"
                type="text"
                placeholder="e.g., Restaurant, Cafe, Food Truck"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>

            <!-- Phone Number -->
            <div>
              <label for="business_phone" class="block text-sm font-medium text-gray-700 mb-2">
                Business Phone Number
              </label>
              <input
                id="business_phone"
                v-model="businessForm.phone_number"
                type="tel"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>

              <!-- Address -->
              <div>
                  <label for="business_address" class="block text-sm font-medium text-gray-700 mb-2">
                      Business Address
                  </label>
                  <input
                      id="business_address"
                      v-model="businessForm.business_address"
                      type="text"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  />
              </div>

            <!-- Country -->
            <div>
              <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                Country
              </label>
              <select
                id="country"
                v-model="businessForm.country_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              >
                <option value="">Select a country</option>
                <option v-for="country in countries" :key="country.id" :value="country.id">
                  {{ country.name }}
                </option>
              </select>
            </div>

            <!-- State -->
            <div>
              <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                State
              </label>
              <select
                id="state"
                v-model="businessForm.state_id"
                :disabled="!businessForm.country_id || loadingStates"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
              >
                <option value="">{{ loadingStates ? 'Loading states...' : 'Select a state' }}</option>
                <option v-for="state in states" :key="state.id" :value="state.id">
                  {{ state.name }}
                </option>
              </select>
            </div>

            <!-- City -->
            <div>
              <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                City
              </label>
              <select
                id="city"
                v-model="businessForm.city_id"
                :disabled="!businessForm.state_id || loadingCities"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
              >
                <option value="">{{ loadingCities ? 'Loading cities...' : 'Select a city' }}</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name }}
                </option>
              </select>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
              <button
                type="submit"
                :disabled="businessSubmitting"
                class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <LoadingSpinner v-if="businessSubmitting" size="sm" />
                <span>{{ businessSubmitting ? 'Saving...' : 'Save Changes' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Media Settings -->
        <div v-show="activeTab === 'media'" class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Brand Media</h2>
          <div class="space-y-8">
            <!-- Logo Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Logo</label>
              <div class="flex items-center gap-6">
                <div v-if="logoPreview || vendorData?.vendor_media?.logo" class="flex-shrink-0">
                  <img
                    :src="logoPreview || vendorData.vendor_media.logo"
                    alt="Logo"
                    class="w-24 h-24 rounded-full object-cover border-2 border-gray-200"
                  />
                </div>
                <div v-else class="flex-shrink-0 w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center">
                  <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <label
                    for="logo"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-all"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Logo
                  </label>
                  <input
                    id="logo"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleLogoChange"
                  />
                  <p class="text-sm text-gray-500 mt-2">Square image recommended. PNG or JPG up to 5MB.</p>
                </div>
              </div>
            </div>

            <!-- Hero Image Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Hero Image</label>
              <div class="space-y-4">
                <div v-if="heroPreview || vendorData?.vendor_media?.hero_image" class="w-full">
                  <img
                    :src="heroPreview || vendorData.vendor_media.hero_image"
                    alt="Hero"
                    class="w-full h-48 rounded-lg object-cover border-2 border-gray-200"
                  />
                </div>
                <div v-else class="w-full h-48 rounded-lg bg-gray-100 flex items-center justify-center">
                  <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <label
                    for="hero"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-all"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Hero Image
                  </label>
                  <input
                    id="hero"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleHeroChange"
                  />
                  <p class="text-sm text-gray-500 mt-2">Wide landscape image recommended (16:9). PNG or JPG up to 5MB.</p>
                </div>
              </div>
            </div>

            <!-- Save Media Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
              <button
                @click="uploadMedia"
                :disabled="mediaSubmitting || (!logoFile && !heroFile)"
                class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <LoadingSpinner v-if="mediaSubmitting" size="sm" />
                <span>{{ mediaSubmitting ? 'Uploading...' : 'Upload Media' }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- QR Code Settings -->
        <div v-show="activeTab === 'qr'" class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">QR Code</h2>
          <div class="space-y-6">
            <!-- QR Code Display -->
            <div v-if="vendorData?.qr_code" class="flex flex-col items-center">
              <div class="bg-white p-6 rounded-lg border-2 border-gray-200 inline-block mb-4">
                <img :src="vendorData.qr_code" alt="QR Code" class="w-64 h-64" />
              </div>
              <div class="flex gap-3">
                <a
                  :href="vendorData.qr_code"
                  download="qr-code.png"
                  class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all"
                >
                  Download QR Code
                </a>
                <button
                  @click="regenerateQR"
                  :disabled="qrSubmitting"
                  class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                  <LoadingSpinner v-if="qrSubmitting" size="sm" />
                  <span>{{ qrSubmitting ? 'Regenerating...' : 'Regenerate QR' }}</span>
                </button>
              </div>
            </div>
            <div v-else class="text-center py-12">
              <div class="text-6xl mb-4">📱</div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">No QR Code Yet</h3>
              <p class="text-gray-600 mb-6">Generate a QR code for your menu</p>
              <button
                @click="regenerateQR"
                :disabled="qrSubmitting"
                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 mx-auto"
              >
                <LoadingSpinner v-if="qrSubmitting" size="sm" />
                <span>{{ qrSubmitting ? 'Generating...' : 'Generate QR Code' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useVendorStore } from '@/stores/vendor'
import { useAuthStore } from '@/stores/auth'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const vendorStore = useVendorStore()
const authStore = useAuthStore()
const { auth } = useApi()
const toast = useToast()

const loading = ref(true)
const activeTab = ref('profile')
const vendorData = ref(null)

// Profile form
const profileForm = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone_number: ''
})
const profileSubmitting = ref(false)

// Business form
const businessForm = ref({
  business_name: '',
  business_type: '',
  phone_number: '',
  country_id: '',
  state_id: '',
  city_id: ''
})
const businessSubmitting = ref(false)

// Location data
const countries = ref([])
const states = ref([])
const cities = ref([])
const loadingStates = ref(false)
const loadingCities = ref(false)

// Media uploads
const logoFile = ref(null)
const heroFile = ref(null)
const logoPreview = ref(null)
const heroPreview = ref(null)
const mediaSubmitting = ref(false)

// QR Code
const qrSubmitting = ref(false)

const tabs = [
  { id: 'profile', name: 'Profile' },
  { id: 'business', name: 'Business' },
  { id: 'media', name: 'Media' },
  { id: 'qr', name: 'QR Code' }
]

const handleLogoChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) {
    toast.error('Image size must be less than 5MB')
    return
  }

  logoFile.value = file
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const handleHeroChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) {
    toast.error('Image size must be less than 5MB')
    return
  }

  heroFile.value = file
  const reader = new FileReader()
  reader.onload = (e) => {
    heroPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const updateProfile = async () => {
  profileSubmitting.value = true

  try {
    await vendorStore.updateProfile(profileForm.value)

    // Update auth store with new user data
    authStore.user = {
      ...authStore.user,
      first_name: profileForm.value.first_name,
      last_name: profileForm.value.last_name,
      email: profileForm.value.email
    }

    toast.success('Profile updated successfully!')
  } catch (error) {
    console.error('Error updating profile:', error)
    toast.error(error.response?.data?.message || 'Failed to update profile')
  } finally {
    profileSubmitting.value = false
  }
}

const updateBusiness = async () => {
  businessSubmitting.value = true
  try {
    // await vendorStore.setBusinessLink(businessForm.value)
    await vendorStore.setBusinessInfo(businessForm.value)
    toast.success('Business information updated successfully!')
    await loadSettings()
  } catch (error) {
    console.error('Error updating business:', error)
    toast.error(error.response?.data?.message || 'Failed to update business information')
  } finally {
    businessSubmitting.value = false
  }
}

const uploadMedia = async () => {
  if (!logoFile.value && !heroFile.value) {
    toast.error('Please select at least one image to upload')
    return
  }

  mediaSubmitting.value = true

  try {
    const formData = new FormData()
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }
    if (heroFile.value) {
      formData.append('hero_image', heroFile.value)
    }

    await vendorStore.uploadMedia(formData)
    toast.success('Media uploaded successfully!')

    // Clear files and previews
    logoFile.value = null
    heroFile.value = null
    logoPreview.value = null
    heroPreview.value = null

    // Reload settings to get new images
    await loadSettings()
  } catch (error) {
    console.error('Error uploading media:', error)
    toast.error(error.response?.data?.message || 'Failed to upload media')
  } finally {
    mediaSubmitting.value = false
  }
}

const regenerateQR = async () => {
  qrSubmitting.value = true

  try {
    await vendorStore.generateQR()
    toast.success('QR code generated successfully!')
    await loadSettings()
  } catch (error) {
    console.error('Error generating QR:', error)
    toast.error(error.response?.data?.message || 'Failed to generate QR code')
  } finally {
    qrSubmitting.value = false
  }
}

// Fetch countries
const fetchCountries = async () => {
  try {
    const response = await auth.getCountries()
    countries.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Error fetching countries:', error)
    toast.error('Failed to load countries')
  }
}

// Fetch states for selected country
const fetchStates = async (countryId) => {
  if (!countryId) {
    states.value = []
    return
  }

  loadingStates.value = true
  try {
    const response = await auth.getStates(countryId)
    states.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Error fetching states:', error)
    toast.error('Failed to load states')
    states.value = []
  } finally {
    loadingStates.value = false
  }
}

// Fetch cities for selected state
const fetchCities = async (stateId) => {
  if (!stateId) {
    cities.value = []
    return
  }

  loadingCities.value = true
  try {
    const response = await auth.getCities(stateId)
    cities.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Error fetching cities:', error)
    toast.error('Failed to load cities')
    cities.value = []
  } finally {
    loadingCities.value = false
  }
}

// Watch for country changes
watch(() => businessForm.value.country_id, async (newCountryId) => {
  // Clear state and city when country changes
  businessForm.value.state_id = ''
  businessForm.value.city_id = ''
  states.value = []
  cities.value = []

  // Fetch states for new country
  if (newCountryId) {
    await fetchStates(newCountryId)
  }
})

// Watch for state changes
watch(() => businessForm.value.state_id, async (newStateId) => {
  // Clear city when state changes
  businessForm.value.city_id = ''
  cities.value = []

  // Fetch cities for new state
  if (newStateId) {
    await fetchCities(newStateId)
  }
})

const loadSettings = async () => {
  try {
    await vendorStore.fetchFullProfile()
    vendorData.value = vendorStore.profile

    // Populate profile form
    profileForm.value = {
      first_name: authStore.user?.first_name || '',
      last_name: authStore.user?.last_name || '',
      email: authStore.user?.email || '',
      phone_number: authStore.user?.phone_number || ''
    }

    // Populate business form
    const businessLink = vendorData.value?.business_links?.[0]
    if (businessLink) {
      businessForm.value = {
        business_name: vendorData.value.business_name || '',
        business_type: businessLink.business_type || '',
        phone_number: businessLink.phone_number || '',
        country_id: businessLink.country_id || '',
        state_id: businessLink.state_id || '',
        city_id: businessLink.city_id || ''
      }

      // If we have existing location data, fetch dependent dropdowns
      if (businessLink.country_id) {
        await fetchStates(businessLink.country_id)
        if (businessLink.state_id) {
          await fetchCities(businessLink.state_id)
        }
      }
    }
  } catch (error) {
    console.error('Error loading settings:', error)
    toast.error('Failed to load settings')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  // Fetch countries first
  await fetchCountries()
  // Then load settings
  await loadSettings()
})
</script>

<style scoped>
/* Custom styles if needed */
</style>
