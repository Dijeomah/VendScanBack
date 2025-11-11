# Frontend Implementation Guide (Vue.js 3 + Vite)

## Table of Contents
1. [Project Structure](#project-structure)
2. [Setup & Installation](#setup--installation)
3. [API Integration](#api-integration)
4. [State Management](#state-management)
5. [Routing](#routing)
6. [Authentication](#authentication)
7. [Best Practices](#best-practices)

---

## Project Structure

### Recommended Vue 3 + Vite Project Structure

```
qr-menu-frontend/
├── node_modules/             # Dependencies
├── public/
│   ├── favicon.ico
│   ├── logo.svg
│   └── placeholder-food.jpg
├── src/
│   ├── assets/              # Static assets
│   │   ├── css/
│   │   │   ├── main.css     # Global styles
│   │   │   └── tailwind.css # Tailwind imports
│   │   ├── fonts/           # Custom fonts
│   │   └── images/          # Images
│   ├── components/          # Reusable components
│   │   ├── common/          # Shared components
│   │   │   ├── Button.vue
│   │   │   ├── Input.vue
│   │   │   ├── Modal.vue
│   │   │   ├── Icon.vue
│   │   │   └── Card.vue
│   │   ├── menu/            # Menu-specific components
│   │   │   ├── MenuItemCard.vue
│   │   │   ├── CategorySection.vue
│   │   │   ├── ItemDetailModal.vue
│   │   │   └── MenuSearch.vue
│   │   ├── vendor/          # Vendor dashboard components
│   │   │   ├── DashboardStats.vue
│   │   │   ├── ItemForm.vue
│   │   │   ├── CategoryForm.vue
│   │   │   └── QRCodeDisplay.vue
│   │   ├── admin/           # Admin-specific components
│   │   │   ├── VendorTable.vue
│   │   │   ├── VendorForm.vue
│   │   │   └── SystemStats.vue
│   │   └── layout/          # Layout components
│   │       ├── Sidebar.vue
│   │       ├── Topbar.vue
│   │       ├── Footer.vue
│   │       └── UserMenu.vue
│   ├── composables/         # Composition API utilities
│   │   ├── useApi.js        # API composable
│   │   ├── useAuth.js       # Authentication composable
│   │   ├── useMenu.js       # Menu data composable
│   │   ├── useToast.js      # Toast notifications
│   │   └── useVendor.js     # Vendor data composable
│   ├── layouts/             # Page layouts
│   │   ├── DefaultLayout.vue
│   │   ├── DashboardLayout.vue
│   │   ├── PublicMenuLayout.vue
│   │   └── AuthLayout.vue
│   ├── router/              # Vue Router
│   │   ├── index.js         # Router configuration
│   │   └── guards.js        # Route guards
│   ├── stores/              # Pinia stores
│   │   ├── auth.js          # Authentication state
│   │   ├── vendor.js        # Vendor state
│   │   ├── menu.js          # Menu state
│   │   └── ui.js            # UI state (sidebar, modals)
│   ├── types/               # TypeScript types (if using TS)
│   │   ├── api.ts           # API response types
│   │   ├── models.ts        # Data model types
│   │   └── index.ts         # Type exports
│   ├── utils/               # Utilities
│   │   ├── api.js           # API helpers
│   │   ├── format.js        # Formatting utilities
│   │   ├── validation.js    # Validation rules
│   │   └── constants.js     # App constants
│   ├── views/               # Page components (routed)
│   │   ├── Home.vue
│   │   ├── Login.vue
│   │   ├── Register.vue
│   │   ├── vendor/
│   │   │   ├── Dashboard.vue
│   │   │   ├── MenuManagement.vue
│   │   │   ├── ItemCreate.vue
│   │   │   ├── ItemEdit.vue
│   │   │   ├── Settings.vue
│   │   │   └── Analytics.vue
│   │   ├── admin/
│   │   │   ├── Dashboard.vue
│   │   │   ├── VendorList.vue
│   │   │   ├── VendorCreate.vue
│   │   │   └── BusinessManagement.vue
│   │   └── menu/
│   │       └── PublicMenu.vue  # Public menu viewer
│   ├── App.vue              # Root component
│   └── main.js              # Entry point
├── .env                     # Environment variables (local)
├── .env.example             # Environment example
├── .gitignore
├── index.html               # HTML entry point
├── package.json
├── tailwind.config.js       # Tailwind configuration
├── vite.config.js           # Vite configuration
└── README.md
```

---

## Setup & Installation

### 1. Create Vite + Vue 3 Project

```bash
# Create new Vue 3 project with Vite
npm create vite@latest qr-menu-frontend -- --template vue

# Navigate to project
cd qr-menu-frontend

# Install dependencies
npm install
```

### 2. Install Required Packages

```bash
# Core dependencies
npm install vue-router@4 pinia
npm install axios
npm install @vueuse/core

# Form handling & validation
npm install vee-validate yup

# UI utilities
npm install @headlessui/vue
npm install @heroicons/vue

# Tailwind CSS
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# DaisyUI (optional - Tailwind component library)
npm install -D daisyui

# QR Code handling
npm install qrcode.vue

# Date handling
npm install date-fns

# Toast notifications
npm install vue-toastification

# Meta tags for SEO
npm install @vueuse/head

# Development
npm install -D @vitejs/plugin-vue
```

### 3. Configure Vite (`vite.config.js`)

```javascript
// vite.config.js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],

  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },

  server: {
    port: 3000,
    proxy: {
      // Proxy API requests to Laravel backend
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      }
    }
  },

  build: {
    // Output directory
    outDir: 'dist',
    // Generate source maps
    sourcemap: false,
    // Rollup options
    rollupOptions: {
      output: {
        manualChunks: {
          'vendor': ['vue', 'vue-router', 'pinia'],
          'utils': ['axios', '@vueuse/core']
        }
      }
    }
  }
})
```

### 4. Configure Tailwind (`tailwind.config.js`)

```javascript
// tailwind.config.js
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
      },
    },
  },
  plugins: [
    require('daisyui'),
  ],
  daisyui: {
    themes: ["light", "dark"],
  },
}
```

### 5. Environment Configuration (`.env`)

```bash
# .env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_DOMAIN=qr-app.test
VITE_APP_URL=http://localhost:3000
```

### 6. Update `main.js`

```javascript
// src/main.js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createHead } from '@vueuse/head'
import Toast from 'vue-toastification'
import router from './router'
import App from './App.vue'

// Styles
import './assets/css/tailwind.css'
import './assets/css/main.css'
import 'vue-toastification/dist/index.css'

const app = createApp(App)
const pinia = createPinia()
const head = createHead()

app.use(pinia)
app.use(router)
app.use(head)
app.use(Toast, {
  transition: "Vue-Toastification__bounce",
  maxToasts: 3,
  newestOnTop: true
})

app.mount('#app')
```

### 7. Create CSS Files

```css
/* src/assets/css/tailwind.css */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

```css
/* src/assets/css/main.css */
:root {
  /* Your CSS variables from design system */
  --primary-500: #3b82f6;
  --text-primary: #111827;
  --bg-primary: #ffffff;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Utility classes */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}
```

---

## API Integration

### 1. API Composable (`src/composables/useApi.js`)

```javascript
// src/composables/useApi.js
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

// Create axios instance
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
})

// Request interceptor - add auth token
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    const token = authStore.token

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    return config
  },
  (error) => Promise.reject(error)
)

// Response interceptor - handle errors
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config
    const authStore = useAuthStore()

    // Handle 401 - token expired
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true

      try {
        // Try to refresh token
        await authStore.refreshToken()
        return api(originalRequest)
      } catch (refreshError) {
        // Refresh failed, logout
        authStore.logout()
        const router = useRouter()
        router.push('/login')
        return Promise.reject(refreshError)
      }
    }

    return Promise.reject(error)
  }
)

export function useApi() {
  return {
    api,

    // Auth endpoints
    auth: {
      login: (credentials) =>
        api.post('/auth/login', credentials),

      register: (data) =>
        api.post('/auth/register', data),

      logout: () =>
        api.post('/auth/logout'),

      refresh: () =>
        api.post('/auth/refresh'),

      getCountries: () =>
        api.get('/auth/countries'),

      getStates: (countryId) =>
        api.get(`/auth/states/${countryId}`),

      getCities: (stateId) =>
        api.get(`/auth/cities/${stateId}`),
    },

    // Vendor endpoints
    vendor: {
      getDashboard: () =>
        api.get('/vendor/dashboard'),

      getProfile: () =>
        api.get('/vendor/profile'),

      getFullProfile: () =>
        api.get('/vendor/full-profile'),

      updateProfile: (data) =>
        api.put('/vendor/profile/update', data),

      setBusinessInfo: (data) =>
        api.post('/vendor/business-info', data),

      setBusinessLink: (data) =>
        api.post('/vendor/business-links', data),

      uploadMedia: (formData) =>
        api.post('/vendor/media', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        }),

      generateQR: () =>
        api.post('/vendor/generate-qr'),

      // Categories
      getCategories: () =>
        api.get('/vendor/categories'),

      createCategory: (data) =>
        api.post('/vendor/categories', data),

      // Items
      getItems: () =>
        api.get('/vendor/items'),

      getItem: (id) =>
        api.get(`/vendor/items/${id}`),

      createItem: (data) =>
        api.post('/vendor/items', data),

      updateItem: (id, data) =>
        api.put(`/vendor/items/${id}`, data),

      deleteItem: (id) =>
        api.delete(`/vendor/items/${id}`),

      getItemsByCategory: (categoryId) =>
        api.get(`/vendor/items/by-category/${categoryId}`),
    },

    // Admin endpoints
    admin: {
      getDashboard: () =>
        api.get('/admin/dashboard'),

      getVendors: () =>
        api.get('/admin/vendors'),

      getVendor: (id) =>
        api.get(`/admin/vendors/${id}`),

      createVendor: (data) =>
        api.post('/admin/vendors', data),

      updateVendor: (id, data) =>
        api.put(`/admin/vendors/${id}`, data),

      deleteVendor: (id) =>
        api.delete(`/admin/vendors/${id}`),

      generateVendorQR: (id) =>
        api.post(`/admin/vendors/${id}/generate-qr`),
    },

    // Public endpoints
    public: {
      getVendorMenu: async (subdomain) => {
        const response = await axios.get(
          `http://${subdomain}.${import.meta.env.VITE_APP_DOMAIN}`
        )
        return response.data
      }
    }
  }
}
```

---

## State Management

### 1. Auth Store (`src/stores/auth.js`)

```javascript
// src/stores/auth.js
import { defineStore } from 'pinia'
import { useApi } from '@/composables/useApi'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false,
  }),

  getters: {
    isVendor: (state) => state.user?.role === 'vendor',
    isAdmin: (state) => state.user?.role === 'admin',
    userName: (state) => state.user
      ? `${state.user.first_name} ${state.user.last_name}`
      : '',
  },

  actions: {
    async login(credentials) {
      try {
        const { auth } = useApi()
        const response = await auth.login(credentials)
        const { access_token, user } = response.data

        this.setAuth(access_token, user)
        return user
      } catch (error) {
        throw error
      }
    },

    async register(data) {
      try {
        const { auth } = useApi()
        const response = await auth.register(data)

        // Auto-login after registration
        if (response.data.user) {
          return await this.login({
            email: data.email,
            password: data.password
          })
        }
      } catch (error) {
        throw error
      }
    },

    async logout() {
      try {
        const { auth } = useApi()
        await auth.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
      }
    },

    async refreshToken() {
      try {
        const { auth } = useApi()
        const response = await auth.refresh()
        const { access_token, user } = response.data

        this.setAuth(access_token, user)
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    setAuth(token, user) {
      this.token = token
      this.user = user
      this.isAuthenticated = true

      // Persist to localStorage
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))
    },

    clearAuth() {
      this.token = null
      this.user = null
      this.isAuthenticated = false

      localStorage.removeItem('token')
      localStorage.removeItem('user')
    },

    initAuth() {
      const token = localStorage.getItem('token')
      const userStr = localStorage.getItem('user')

      if (token && userStr) {
        try {
          const user = JSON.parse(userStr)
          this.setAuth(token, user)
        } catch (error) {
          this.clearAuth()
        }
      }
    },
  },
})
```

### 2. Vendor Store (`src/stores/vendor.js`)

```javascript
// src/stores/vendor.js
import { defineStore } from 'pinia'
import { useApi } from '@/composables/useApi'

export const useVendorStore = defineStore('vendor', {
  state: () => ({
    vendor: null,
    items: [],
    categories: [],
    loading: false,
    error: null,
  }),

  getters: {
    activeItems: (state) => state.items.filter(item => item.status),

    itemsByCategory: (state) => (categoryId) =>
      state.items.filter(item => item.category_id === categoryId),
  },

  actions: {
    async fetchFullProfile() {
      this.loading = true
      try {
        const { vendor } = useApi()
        const response = await vendor.getFullProfile()
        this.vendor = response.data.data

        // Extract items and categories
        this.categories = this.vendor?.categories || []
        this.items = this.vendor?.business_links?.[0]?.items || []
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async createItem(data) {
      const { vendor } = useApi()
      const response = await vendor.createItem(data)

      // Add to local state
      this.items.push(response.data.data)

      return response.data.data
    },

    async updateItem(id, data) {
      const { vendor } = useApi()
      const response = await vendor.updateItem(id, data)

      // Update in local state
      const index = this.items.findIndex(item => item.id === id)
      if (index !== -1) {
        this.items[index] = response.data.data
      }

      return response.data.data
    },

    async deleteItem(id) {
      const { vendor } = useApi()
      await vendor.deleteItem(id)

      // Remove from local state
      this.items = this.items.filter(item => item.id !== id)
    },
  },
})
```

---

## Routing

### 1. Router Configuration (`src/router/index.js`)

```javascript
// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/Home.vue'),
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/Register.vue'),
    meta: { guest: true },
  },

  // Vendor routes
  {
    path: '/vendor',
    meta: { requiresAuth: true, role: 'vendor' },
    children: [
      {
        path: 'dashboard',
        name: 'VendorDashboard',
        component: () => import('@/views/vendor/Dashboard.vue'),
      },
      {
        path: 'menu',
        name: 'MenuManagement',
        component: () => import('@/views/vendor/MenuManagement.vue'),
      },
      {
        path: 'items/create',
        name: 'ItemCreate',
        component: () => import('@/views/vendor/ItemCreate.vue'),
      },
      {
        path: 'items/:id/edit',
        name: 'ItemEdit',
        component: () => import('@/views/vendor/ItemEdit.vue'),
      },
      {
        path: 'settings',
        name: 'VendorSettings',
        component: () => import('@/views/vendor/Settings.vue'),
      },
    ],
  },

  // Admin routes
  {
    path: '/admin',
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: () => import('@/views/admin/Dashboard.vue'),
      },
      {
        path: 'vendors',
        name: 'VendorList',
        component: () => import('@/views/admin/VendorList.vue'),
      },
      {
        path: 'vendors/create',
        name: 'VendorCreate',
        component: () => import('@/views/admin/VendorCreate.vue'),
      },
    ],
  },

  // Public menu
  {
    path: '/menu/:subdomain',
    name: 'PublicMenu',
    component: () => import('@/views/menu/PublicMenu.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Initialize auth from localStorage
  if (!authStore.isAuthenticated) {
    authStore.initAuth()
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
    return
  }

  // Check if guest-only route
  if (to.meta.guest && authStore.isAuthenticated) {
    // Redirect based on role
    if (authStore.isAdmin) {
      next('/admin/dashboard')
    } else if (authStore.isVendor) {
      next('/vendor/dashboard')
    } else {
      next('/')
    }
    return
  }

  // Check role-based access
  if (to.meta.role && authStore.user?.role !== to.meta.role) {
    next('/unauthorized')
    return
  }

  next()
})

export default router
```

---

## Authentication

### Login Page Example (`src/views/Login.vue`)

```vue
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const form = ref({
  email: '',
  password: '',
})

const loading = ref(false)
const errors = ref({})

async function handleLogin() {
  loading.value = true
  errors.value = {}

  try {
    const user = await authStore.login(form.value)

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
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toast.error(error.response?.data?.message || 'Login failed')
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow">
      <div>
        <h2 class="text-3xl font-bold text-center">
          Sign in to your account
        </h2>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Email address
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            :class="{ 'border-red-500': errors.email }"
          />
          <p v-if="errors.email" class="mt-1 text-sm text-red-500">
            {{ errors.email[0] }}
          </p>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            Password
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            :class="{ 'border-red-500': errors.password }"
          />
          <p v-if="errors.password" class="mt-1 text-sm text-red-500">
            {{ errors.password[0] }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50"
        >
          <span v-if="loading">Signing in...</span>
          <span v-else>Sign in</span>
        </button>
      </form>

      <p class="text-center text-sm text-gray-600">
        Don't have an account?
        <router-link to="/register" class="font-medium text-primary-600 hover:text-primary-500">
          Sign up
        </router-link>
      </p>
    </div>
  </div>
</template>
```

---

## Best Practices

### 1. Composables for Reusable Logic

```javascript
// src/composables/useToast.js
import { useToast as useVueToast } from 'vue-toastification'

export function useToast() {
  const toast = useVueToast()

  return {
    success: (message) => toast.success(message),
    error: (message) => toast.error(message),
    info: (message) => toast.info(message),
    warning: (message) => toast.warning(message),
  }
}
```

### 2. Error Handling

```javascript
// src/composables/useErrorHandler.js
import { useToast } from './useToast'

export function useErrorHandler() {
  const toast = useToast()

  const handleError = (error, context) => {
    console.error(`Error in ${context}:`, error)

    const message = error.response?.data?.message
      || error.message
      || 'An error occurred'

    toast.error(message)
  }

  return { handleError }
}
```

### 3. Loading States

```vue
<script setup>
import { ref } from 'vue'

const loading = ref(false)
const data = ref(null)

async function fetchData() {
  loading.value = true
  try {
    const response = await api.getData()
    data.value = response.data
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div v-if="loading">
    <LoadingSpinner />
  </div>
  <div v-else>
    <!-- Actual content -->
  </div>
</template>
```

---

This implementation guide provides everything you need to start building with Vue.js 3 + Vite!
