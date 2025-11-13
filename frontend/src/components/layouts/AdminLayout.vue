<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 to-gray-800 border-r border-gray-700 transform transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center justify-between h-16 px-6 border-b border-gray-700">
        <router-link to="/admin/dashboard" class="flex items-center">
          <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
          <span class="ml-2 text-xl font-bold text-white">Admin Panel</span>
        </router-link>
      </div>

      <!-- User Info -->
      <div class="px-6 py-4 border-b border-gray-700">
        <div class="flex items-center">
          <div class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center">
            <span class="text-sm font-bold text-gray-900">
              {{ userInitials }}
            </span>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-white">{{ userName }}</p>
            <p class="text-xs text-gray-400">Administrator</p>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="px-3 py-4 flex-1 overflow-y-auto">
        <div class="space-y-1">
          <router-link
            v-for="item in navItems"
            :key="item.path"
            :to="item.path"
            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
            :class="[
              isActiveRoute(item.path)
                ? 'bg-yellow-500 text-gray-900'
                : 'text-gray-300 hover:bg-gray-700 hover:text-white'
            ]"
          >
            <component :is="item.icon" class="w-5 h-5 mr-3" />
            {{ item.name }}
          </router-link>
        </div>
      </nav>

      <!-- Logout -->
      <div class="px-3 py-4 border-t border-gray-700">
        <button
          @click="handleLogout"
          class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Logout
        </button>
      </div>
    </aside>

    <!-- Mobile Overlay -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
    ></div>

    <!-- Main Content -->
    <div class="lg:pl-64">
      <!-- Mobile Header -->
      <div class="lg:hidden sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <span class="text-lg font-bold text-gray-900">Admin Panel</span>
        <div class="w-10"></div>
      </div>

      <!-- Page Content -->
      <main class="min-h-screen">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, h } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const sidebarOpen = ref(false)

// User info
const userName = computed(() => {
  const user = authStore.user
  if (user?.first_name && user?.last_name) {
    return `${user.first_name} ${user.last_name}`
  }
  return user?.email || 'Admin User'
})

const userInitials = computed(() => {
  const user = authStore.user
  if (user?.first_name && user?.last_name) {
    return `${user.first_name[0]}${user.last_name[0]}`.toUpperCase()
  }
  return 'AD'
})

// SVG Icon Components
const DashboardIcon = () => h('svg', {
  class: 'w-5 h-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
  })
])

const VendorsIcon = () => h('svg', {
  class: 'w-5 h-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
  })
])

const UsersIcon = () => h('svg', {
  class: 'w-5 h-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
  })
])

const SettingsIcon = () => h('svg', {
  class: 'w-5 h-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
  }),
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z'
  })
])

const ReportsIcon = () => h('svg', {
  class: 'w-5 h-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
  })
])

const TablesIcon = () => h('svg', {
  class: 'w-5 h-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'
  })
])

const ServersIcon = () => h('svg', {
  class: 'w-5 h-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'
  })
])

const OrdersIcon = () => h('svg', {
  class: 'w-5 h-5',
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24'
}, [
  h('path', {
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'stroke-width': '2',
    d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'
  })
])

// Navigation items
const navItems = [
  { name: 'Dashboard', path: '/admin/dashboard', icon: DashboardIcon },
  { name: 'Vendors', path: '/admin/vendors', icon: VendorsIcon },
  { name: 'Tables', path: '/admin/tables', icon: TablesIcon },
  { name: 'Servers', path: '/admin/servers', icon: ServersIcon },
  { name: 'Orders', path: '/admin/orders', icon: OrdersIcon },
  { name: 'Reports', path: '/admin/reports', icon: ReportsIcon },
  { name: 'Settings', path: '/admin/settings', icon: SettingsIcon }
]

// Check if route is active
const isActiveRoute = (path) => {
  return route.path === path || route.path.startsWith(path + '/')
}

// Logout handler
const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
/* Ensure smooth transitions */
aside {
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
}

/* Custom scrollbar for sidebar */
nav::-webkit-scrollbar {
  width: 6px;
}

nav::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
</style>
