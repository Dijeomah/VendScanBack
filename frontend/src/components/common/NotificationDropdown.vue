<template>
  <div class="relative" v-click-outside="closeDropdown">
    <!-- Notification Bell Button -->
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
      :class="{ 'bg-gray-100': showDropdown }"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <!-- Notification Badge -->
      <span v-if="unreadCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="showDropdown"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-[600px] flex flex-col"
      >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
          <button
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="text-sm text-primary-600 hover:text-primary-700 font-medium"
          >
            Mark all read
          </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="p-8 flex justify-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        </div>

        <!-- Notifications List -->
        <div v-else-if="notifications.length > 0" class="overflow-y-auto flex-1">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            @click="handleNotificationClick(notification)"
            :class="[
              'px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors',
              !notification.read_at ? 'bg-blue-50' : 'bg-white'
            ]"
          >
            <div class="flex items-start gap-3">
              <!-- Icon -->
              <div class="flex-shrink-0 w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">{{ notification.data.message }}</p>
                <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                  <span v-if="notification.data.business_name">{{ notification.data.business_name }}</span>
                  <span v-if="notification.data.business_name && notification.data.table_name">•</span>
                  <span v-if="notification.data.table_name">{{ notification.data.table_name }}</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ formatTime(notification.created_at) }}</p>
              </div>

              <!-- Unread Indicator -->
              <div v-if="!notification.read_at" class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full"></div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="p-8 text-center">
          <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <p class="text-gray-500">No notifications yet</p>
        </div>

        <!-- Footer -->
        <div v-if="notifications.length > 0" class="px-4 py-3 border-t border-gray-200 bg-gray-50">
          <router-link
            :to="{ name: 'Notifications' }"
            class="text-sm text-primary-600 hover:text-primary-700 font-medium block text-center"
            @click="closeDropdown"
          >
            View all notifications
          </router-link>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  userRole: {
    type: String,
    required: true,
    validator: (value) => ['vendor', 'server'].includes(value)
  }
})

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()
const { vendor, server } = useApi()

const showDropdown = ref(false)
const loading = ref(false)
const notifications = ref([])
const unreadCount = ref(0)
let pollInterval = null

// Get the correct API based on user role
const api = props.userRole === 'vendor' ? vendor : server

const fetchUnreadCount = async () => {
  try {
    const response = await api.getUnreadCount()
    const data = response.data?.data || response.data
    unreadCount.value = data.count || 0
  } catch (error) {
    console.error('Error fetching unread count:', error)
  }
}

const fetchNotifications = async () => {
  try {
    loading.value = true
    const response = await api.getUnreadNotifications()
    const data = response.data?.data || response.data
    notifications.value = Array.isArray(data) ? data : []
  } catch (error) {
    console.error('Error fetching notifications:', error)
    toast.error('Failed to load notifications')
  } finally {
    loading.value = false
  }
}

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
  if (showDropdown.value && notifications.value.length === 0) {
    fetchNotifications()
  }
}

const closeDropdown = () => {
  showDropdown.value = false
}

const handleNotificationClick = async (notification) => {
  // Mark as read
  if (!notification.read_at) {
    try {
      await api.markNotificationAsRead(notification.id)
      notification.read_at = new Date().toISOString()
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    } catch (error) {
      console.error('Error marking notification as read:', error)
    }
  }

  // Navigate to order details if order_id exists
  if (notification.data.order_id) {
    closeDropdown()
    router.push({
      name: props.userRole === 'vendor' ? 'VendorOrders' : 'ServerOrders',
      query: { orderId: notification.data.order_id }
    })
  }
}

const markAllAsRead = async () => {
  try {
    await api.markAllNotificationsAsRead()
    notifications.value.forEach(n => n.read_at = new Date().toISOString())
    unreadCount.value = 0
    toast.success('All notifications marked as read')
  } catch (error) {
    console.error('Error marking all as read:', error)
    toast.error('Failed to mark notifications as read')
  }
}

const formatTime = (timestamp) => {
  if (!timestamp) return ''

  const date = new Date(timestamp)
  const now = new Date()
  const diff = Math.floor((now - date) / 1000) // difference in seconds

  if (diff < 60) return 'Just now'
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`
  if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`

  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value()
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}

onMounted(() => {
  // Fetch unread count immediately
  fetchUnreadCount()

  // Poll for new notifications every 30 seconds
  pollInterval = setInterval(() => {
    fetchUnreadCount()
  }, 30000)
})

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval)
  }
})
</script>
