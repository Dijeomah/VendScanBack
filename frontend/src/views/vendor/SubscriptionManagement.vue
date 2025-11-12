<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <router-link
          to="/vendor/dashboard"
          class="inline-flex items-center text-primary-600 hover:text-primary-700 mb-4"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Dashboard
        </router-link>
        <h1 class="text-3xl font-bold text-gray-900">Subscription Plans</h1>
        <p class="text-gray-600 mt-2">Choose the plan that fits your business needs</p>
      </div>

      <!-- Current Plan Banner -->
      <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg shadow-sm p-6 text-white mb-8">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-primary-100 mb-1">Your Current Plan</p>
            <h2 class="text-3xl font-bold capitalize">{{ currentTier }} Plan</h2>
            <p class="text-primary-100 mt-2">
              {{ businessCount }} of {{ getBusinessLimit(currentTier) }} businesses used
            </p>
          </div>
          <div class="text-right">
            <div class="bg-white/20 rounded-lg px-4 py-2">
              <p class="text-sm text-primary-100">Next billing date</p>
              <p class="text-lg font-semibold">{{ nextBillingDate }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing Plans -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <!-- Free Plan -->
        <div
          :class="[
            'bg-white rounded-xl shadow-sm border-2 transition-all',
            currentTier === 'free' ? 'border-primary-600 ring-2 ring-primary-100' : 'border-gray-200 hover:border-primary-300'
          ]"
        >
          <div class="p-8">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-2xl font-bold text-gray-900">Free</h3>
              <span v-if="currentTier === 'free'" class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">
                Current
              </span>
            </div>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900">$0</span>
              <span class="text-gray-600">/month</span>
            </div>
            <ul class="space-y-3 mb-8">
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Up to <strong>3 businesses</strong></span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Unlimited menu items</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Unlimited tables & servers</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">QR code generation</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Basic support</span>
              </li>
            </ul>
            <button
              v-if="currentTier !== 'free'"
              @click="confirmDowngrade('free')"
              class="w-full px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-semibold"
            >
              Downgrade to Free
            </button>
            <button
              v-else
              disabled
              class="w-full px-6 py-3 bg-gray-100 text-gray-400 rounded-lg font-semibold cursor-not-allowed"
            >
              Current Plan
            </button>
          </div>
        </div>

        <!-- Pro Plan -->
        <div
          :class="[
            'bg-white rounded-xl shadow-lg border-2 transition-all transform md:scale-105',
            currentTier === 'pro' ? 'border-primary-600 ring-2 ring-primary-100' : 'border-primary-500'
          ]"
        >
          <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white text-center py-2 rounded-t-xl">
            <span class="text-sm font-semibold">MOST POPULAR</span>
          </div>
          <div class="p-8">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-2xl font-bold text-gray-900">Pro</h3>
              <span v-if="currentTier === 'pro'" class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">
                Current
              </span>
            </div>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900">$29</span>
              <span class="text-gray-600">/month</span>
            </div>
            <ul class="space-y-3 mb-8">
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Up to <strong>5 businesses</strong></span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Everything in Free</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Advanced analytics</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Priority support</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Custom branding</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Online ordering (coming soon)</span>
              </li>
            </ul>
            <button
              v-if="currentTier === 'free'"
              @click="confirmUpgrade('pro')"
              class="w-full px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-lg hover:from-primary-700 hover:to-primary-800 transition-all font-semibold shadow-lg"
            >
              Upgrade to Pro
            </button>
            <button
              v-else-if="currentTier === 'pro'"
              disabled
              class="w-full px-6 py-3 bg-gray-100 text-gray-400 rounded-lg font-semibold cursor-not-allowed"
            >
              Current Plan
            </button>
            <button
              v-else
              @click="confirmDowngrade('pro')"
              class="w-full px-6 py-3 border-2 border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition-all font-semibold"
            >
              Downgrade to Pro
            </button>
          </div>
        </div>

        <!-- Max Plan -->
        <div
          :class="[
            'bg-white rounded-xl shadow-sm border-2 transition-all',
            currentTier === 'max' ? 'border-primary-600 ring-2 ring-primary-100' : 'border-gray-200 hover:border-primary-300'
          ]"
        >
          <div class="p-8">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-2xl font-bold text-gray-900">Max</h3>
              <span v-if="currentTier === 'max'" class="px-3 py-1 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">
                Current
              </span>
            </div>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900">$99</span>
              <span class="text-gray-600">/month</span>
            </div>
            <ul class="space-y-3 mb-8">
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700"><strong>Unlimited businesses</strong></span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Everything in Pro</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">White-label solution</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">Dedicated account manager</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">API access</span>
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-gray-700">24/7 premium support</span>
              </li>
            </ul>
            <button
              v-if="currentTier !== 'max'"
              @click="confirmUpgrade('max')"
              class="w-full px-6 py-3 bg-gradient-to-r from-gray-800 to-gray-900 text-white rounded-lg hover:from-gray-900 hover:to-black transition-all font-semibold"
            >
              Upgrade to Max
            </button>
            <button
              v-else
              disabled
              class="w-full px-6 py-3 bg-gray-100 text-gray-400 rounded-lg font-semibold cursor-not-allowed"
            >
              Current Plan
            </button>
          </div>
        </div>
      </div>

      <!-- FAQ Section -->
      <div class="bg-white rounded-lg shadow-sm p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
        <div class="space-y-4">
          <div class="border-b border-gray-200 pb-4">
            <h3 class="font-semibold text-gray-900 mb-2">Can I cancel my subscription anytime?</h3>
            <p class="text-gray-600">Yes, you can cancel your subscription at any time. You'll continue to have access until the end of your billing period.</p>
          </div>
          <div class="border-b border-gray-200 pb-4">
            <h3 class="font-semibold text-gray-900 mb-2">What happens if I exceed my business limit?</h3>
            <p class="text-gray-600">You'll need to upgrade to a higher tier to create more businesses. Your existing businesses will continue to work normally.</p>
          </div>
          <div class="border-b border-gray-200 pb-4">
            <h3 class="font-semibold text-gray-900 mb-2">Do you offer refunds?</h3>
            <p class="text-gray-600">We offer a 14-day money-back guarantee on all paid plans. No questions asked.</p>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 mb-2">How do I upgrade or downgrade my plan?</h3>
            <p class="text-gray-600">Simply click the upgrade or downgrade button on the plan you want. Changes take effect immediately.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="showConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">
          {{ confirmAction === 'upgrade' ? 'Upgrade Plan' : 'Change Plan' }}
        </h3>
        <p class="text-gray-600 mb-6">
          Are you sure you want to {{ confirmAction }} to the <strong class="capitalize">{{ targetTier }}</strong> plan?
          <span v-if="confirmAction === 'upgrade'">You'll be charged immediately.</span>
          <span v-else>This will take effect at the end of your current billing period.</span>
        </p>
        <div class="flex gap-3">
          <button
            @click="showConfirmModal = false"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all"
          >
            Cancel
          </button>
          <button
            @click="processPlanChange"
            :disabled="processing"
            class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50"
          >
            {{ processing ? 'Processing...' : 'Confirm' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

const authStore = useAuthStore()
const toast = useToast()

const showConfirmModal = ref(false)
const confirmAction = ref('')
const targetTier = ref('')
const processing = ref(false)

const currentTier = computed(() => authStore.user?.subscription_tier || 'free')
const businessCount = computed(() => authStore.user?.business_links?.length || 0)

const nextBillingDate = computed(() => {
  // This would come from actual subscription data
  const date = new Date()
  date.setMonth(date.getMonth() + 1)
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
})

const getBusinessLimit = (tier) => {
  switch (tier) {
    case 'free':
      return 3
    case 'pro':
      return 5
    case 'max':
      return 'Unlimited'
    default:
      return 3
  }
}

const confirmUpgrade = (tier) => {
  confirmAction.value = 'upgrade'
  targetTier.value = tier
  showConfirmModal.value = true
}

const confirmDowngrade = (tier) => {
  confirmAction.value = 'downgrade'
  targetTier.value = tier
  showConfirmModal.value = true
}

const processPlanChange = async () => {
  try {
    processing.value = true

    // TODO: Call API to change subscription
    // For now, just simulate the change
    await new Promise(resolve => setTimeout(resolve, 1500))

    toast.success(`Successfully ${confirmAction.value}d to ${targetTier.value} plan!`)
    showConfirmModal.value = false

    // Refresh user data
    await authStore.fetchUser()
  } catch (error) {
    console.error('Error changing plan:', error)
    toast.error('Failed to change plan. Please try again.')
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  // Load current subscription details
})
</script>
