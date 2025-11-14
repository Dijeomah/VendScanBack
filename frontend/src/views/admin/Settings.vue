<template>
  <AdminLayout>
    <div class="p-4 md:p-8">
      <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Settings</h1>
          <p class="text-gray-600 mt-1">Manage your platform settings and preferences</p>
        </div>

        <!-- Settings Tabs -->
        <div class="bg-white rounded-xl shadow-sm mb-6">
          <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                  activeTab === tab.id
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ tab.name }}
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="p-6">
            <!-- General Settings -->
            <div v-if="activeTab === 'general'" class="space-y-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">General Settings</h3>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Platform Name</label>
                    <input
                      v-model="settings.platform_name"
                      type="text"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Support Email</label>
                    <input
                      v-model="settings.support_email"
                      type="email"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input
                      v-model="settings.contact_phone"
                      type="tel"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="flex items-center">
                      <input
                        v-model="settings.allow_registration"
                        type="checkbox"
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                      />
                      <span class="ml-2 text-sm text-gray-700">Allow new vendor registrations</span>
                    </label>
                  </div>

                  <div>
                    <label class="flex items-center">
                      <input
                        v-model="settings.maintenance_mode"
                        type="checkbox"
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                      />
                      <span class="ml-2 text-sm text-gray-700">Enable maintenance mode</span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  @click="saveSettings"
                  :disabled="saving"
                  class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
                >
                  {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>

            <!-- Payment Settings -->
            <div v-if="activeTab === 'payment'" class="space-y-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Settings</h3>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default Currency</label>
                    <select
                      v-model="settings.currency"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                      <option value="USD">USD - US Dollar</option>
                      <option value="EUR">EUR - Euro</option>
                      <option value="GBP">GBP - British Pound</option>
                      <option value="NGN">NGN - Nigerian Naira</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                    <input
                      v-model.number="settings.tax_rate"
                      type="number"
                      step="0.01"
                      min="0"
                      max="100"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Commission Rate (%)</label>
                    <input
                      v-model.number="settings.commission_rate"
                      type="number"
                      step="0.01"
                      min="0"
                      max="100"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                    <p class="text-xs text-gray-500 mt-1">Platform commission on each transaction</p>
                  </div>

                  <div>
                    <label class="flex items-center">
                      <input
                        v-model="settings.enable_cash_payment"
                        type="checkbox"
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                      />
                      <span class="ml-2 text-sm text-gray-700">Enable cash payment option</span>
                    </label>
                  </div>

                  <div>
                    <label class="flex items-center">
                      <input
                        v-model="settings.enable_card_payment"
                        type="checkbox"
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                      />
                      <span class="ml-2 text-sm text-gray-700">Enable card payment option</span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  @click="saveSettings"
                  :disabled="saving"
                  class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
                >
                  {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>

            <!-- Notification Settings -->
            <div v-if="activeTab === 'notifications'" class="space-y-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Notification Settings</h3>

                <div class="space-y-4">
                  <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Email Notifications</h4>
                    <div class="space-y-2">
                      <label class="flex items-center">
                        <input
                          v-model="settings.notify_new_vendor"
                          type="checkbox"
                          class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">New vendor registration</span>
                      </label>
                      <label class="flex items-center">
                        <input
                          v-model="settings.notify_new_order"
                          type="checkbox"
                          class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">New order placed</span>
                      </label>
                      <label class="flex items-center">
                        <input
                          v-model="settings.notify_payment"
                          type="checkbox"
                          class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">Payment received</span>
                      </label>
                    </div>
                  </div>

                  <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">System Notifications</h4>
                    <div class="space-y-2">
                      <label class="flex items-center">
                        <input
                          v-model="settings.notify_low_stock"
                          type="checkbox"
                          class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">Low stock alerts</span>
                      </label>
                      <label class="flex items-center">
                        <input
                          v-model="settings.notify_errors"
                          type="checkbox"
                          class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">System errors</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  @click="saveSettings"
                  :disabled="saving"
                  class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
                >
                  {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>

            <!-- Security Settings -->
            <div v-if="activeTab === 'security'" class="space-y-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Settings</h3>

                <div class="space-y-6">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Password Policy</h4>
                    <div class="space-y-2">
                      <label class="flex items-center">
                        <input
                          v-model="settings.require_strong_password"
                          type="checkbox"
                          class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                        />
                        <span class="ml-2 text-sm text-gray-700">Require strong passwords</span>
                      </label>
                      <div class="ml-6 text-xs text-gray-500">
                        Must contain uppercase, lowercase, numbers, and special characters
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Timeout (minutes)</label>
                    <input
                      v-model.number="settings.session_timeout"
                      type="number"
                      min="5"
                      max="1440"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>

                  <div>
                    <label class="flex items-center">
                      <input
                        v-model="settings.enable_two_factor"
                        type="checkbox"
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                      />
                      <span class="ml-2 text-sm text-gray-700">Enable two-factor authentication</span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  @click="saveSettings"
                  :disabled="saving"
                  class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
                >
                  {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- System Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Platform Version:</span>
              <span class="font-medium">v1.0.0</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Database Version:</span>
              <span class="font-medium">MySQL 8.0</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Last Backup:</span>
              <span class="font-medium">{{ new Date().toLocaleDateString() }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Server Status:</span>
              <span class="flex items-center font-medium text-green-600">
                <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                Online
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import AdminLayout from '@/components/layouts/AdminLayout.vue'

const toast = useToast()

const activeTab = ref('general')
const saving = ref(false)

const tabs = [
  { id: 'general', name: 'General' },
  { id: 'payment', name: 'Payment' },
  { id: 'notifications', name: 'Notifications' },
  { id: 'security', name: 'Security' }
]

const settings = ref({
  // General
  platform_name: 'VendScan',
  support_email: 'support@vendscan.com',
  contact_phone: '+1234567890',
  allow_registration: true,
  maintenance_mode: false,

  // Payment
  currency: 'USD',
  tax_rate: 10,
  commission_rate: 5,
  enable_cash_payment: true,
  enable_card_payment: true,

  // Notifications
  notify_new_vendor: true,
  notify_new_order: true,
  notify_payment: true,
  notify_low_stock: true,
  notify_errors: true,

  // Security
  require_strong_password: true,
  session_timeout: 120,
  enable_two_factor: false
})

const saveSettings = async () => {
  try {
    saving.value = true
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    toast.success('Settings saved successfully!')
  } catch (error) {
    console.error('Error saving settings:', error)
    toast.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  // Load settings from API
})
</script>
