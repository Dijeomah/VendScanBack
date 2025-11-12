import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: false
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    const token = authStore.token

    console.log('📤 Request interceptor')
    console.log('📤 URL:', config.url)
    console.log('📤 Token from store:', token)
    console.log('📤 Token type:', typeof token)

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      console.log('✅ Authorization header set')
    } else {
      console.warn('⚠️ No token available for request')
    }

    return config
  },
  (error) => Promise.reject(error)
)

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config
    const authStore = useAuthStore()

    // Don't try to refresh if this IS the refresh request or login/register
    const isAuthEndpoint = originalRequest.url?.includes('/auth/login') ||
                          originalRequest.url?.includes('/auth/register') ||
                          originalRequest.url?.includes('/auth/refresh')

    if (error.response?.status === 401 && !originalRequest._retry && !isAuthEndpoint) {
      originalRequest._retry = true

      try {
        await authStore.refreshToken()
        // Update the Authorization header with the new token
        originalRequest.headers.Authorization = `Bearer ${authStore.token}`
        return api(originalRequest)
      } catch (refreshError) {
        // Token refresh failed, logout user
        authStore.clearAuth()
        // Redirect to login if we have a router
        if (typeof window !== 'undefined') {
          window.location.href = '/login'
        }
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
      login: (credentials) => api.post('/auth/login', credentials),
      register: (data) => api.post('/auth/register', data),
      logout: () => api.post('/auth/logout'),
      refresh: () => api.post('/auth/refresh'),
      getCountries: () => api.get('/auth/countries'),
      getStates: (countryId) => api.get(`/auth/states/${countryId}`),
      getCities: (stateId) => api.get(`/auth/cities/${stateId}`),
    },

    // Vendor endpoints
    vendor: {
      getDashboard: () => api.get('/vendor/dashboard'),
      getProfile: () => api.get('/vendor/profile'),
      getFullProfile: () => api.get('/vendor/full-profile'),
      updateProfile: (data) => api.put('/vendor/profile/update', data),
      setBusinessInfo: (data) => api.post('/vendor/business-info', data),
      setBusinessLink: (data) => api.post('/vendor/business-links', data),
      uploadMedia: (formData) => api.post('/vendor/media', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      }),
      generateQR: () => api.post('/vendor/generate-qr'),

      // Categories
      getCategories: () => api.get('/vendor/categories'),
      createCategory: (data) => api.post('/vendor/categories', data),

      // Subcategories
      getSubcategories: () => api.get('/vendor/subcategories'),
      createSubcategory: (data) => api.post('/vendor/subcategories', data),

      // Items
      getItems: () => api.get('/vendor/items'),
      getItem: (id) => api.get(`/vendor/items/${id}`),
      createItem: (data) => api.post('/vendor/items', data),
      updateItem: (id, data) => api.put(`/vendor/items/${id}`, data),
      deleteItem: (id) => api.delete(`/vendor/items/${id}`),
      getItemsByCategory: (categoryId) => api.get(`/vendor/items/by-category/${categoryId}`),
      addItemToCategory: (categoryId, data) => api.post(`/vendor/categories/${categoryId}/items`, data),

      // Tables
      getTables: (businessId) => api.get(`/vendor/businesses/${businessId}/tables`),
      getTable: (businessId, tableId) => api.get(`/vendor/businesses/${businessId}/tables/${tableId}`),
      createTable: (businessId, data) => api.post(`/vendor/businesses/${businessId}/tables`, data),
      bulkCreateTables: (businessId, data) => api.post(`/vendor/businesses/${businessId}/tables/bulk-create`, data),
      updateTable: (businessId, tableId, data) => api.put(`/vendor/businesses/${businessId}/tables/${tableId}`, data),
      deleteTable: (businessId, tableId) => api.delete(`/vendor/businesses/${businessId}/tables/${tableId}`),

      // Servers
      getServers: () => api.get('/vendor/servers'),
      getServer: (serverId) => api.get(`/vendor/servers/${serverId}`),
      createServer: (data) => api.post('/vendor/servers', data),
      updateServer: (serverId, data) => api.put(`/vendor/servers/${serverId}`, data),
      deleteServer: (serverId) => api.delete(`/vendor/servers/${serverId}`),
      assignServerToBusiness: (serverId, data) => api.post(`/vendor/servers/${serverId}/assign-business`, data),
      removeServerFromBusiness: (serverId, data) => api.delete(`/vendor/servers/${serverId}/remove-business`, { data }),
      getBusinessServers: (businessId) => api.get(`/vendor/businesses/${businessId}/servers`),

      // Table Assignments
      getTableAssignments: (businessId) => api.get(`/vendor/businesses/${businessId}/assignments`),
      assignServerToTable: (businessId, data) => api.post(`/vendor/businesses/${businessId}/assignments`, data),
      bulkAssignServerToTables: (businessId, data) => api.post(`/vendor/businesses/${businessId}/assignments/bulk`, data),
      removeTableAssignment: (businessId, assignmentId) => api.delete(`/vendor/businesses/${businessId}/assignments/${assignmentId}`),
      updateAssignmentStatus: (businessId, assignmentId, data) => api.patch(`/vendor/businesses/${businessId}/assignments/${assignmentId}/status`, data),
      getServerAssignments: (businessId, serverId) => api.get(`/vendor/businesses/${businessId}/servers/${serverId}/assignments`),
      getTableServerAssignments: (businessId, tableId) => api.get(`/vendor/businesses/${businessId}/tables/${tableId}/assignments`),
    },

    // Admin endpoints
    admin: {
      getDashboard: () => api.get('/admin/dashboard'),
      getVendors: () => api.get('/admin/vendors'),
      getVendor: (id) => api.get(`/admin/vendors/${id}`),
      createVendor: (data) => api.post('/admin/vendors', data),
      updateVendor: (id, data) => api.put(`/admin/vendors/${id}`, data),
      deleteVendor: (id) => api.delete(`/admin/vendors/${id}`),
      setVendorMedia: (id, formData) => api.post(`/admin/vendors/${id}/media`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      }),
      generateVendorQR: (id) => api.post(`/admin/vendors/${id}/generate-qr`),

      // Businesses
      getBusinesses: () => api.get('/admin/businesses'),
      getBusinessLinks: () => api.get('/admin/business-links'),
      getBusinessLink: (id) => api.get(`/admin/business-links/${id}`),
      deleteBusinessLink: (id) => api.delete(`/admin/business-links/${id}`),

      // Items & Categories
      getItems: () => api.get('/admin/items'),
      createItem: (data) => api.post('/admin/items/create', data),
      updateItem: (id, data) => api.put(`/admin/items/update/${id}`, data),
      getCategories: () => api.get('/admin/categories'),
      getCategoriesWithItems: () => api.get('/admin/categories-with-items'),
      getCategory: (id) => api.get(`/admin/categories/${id}`),
      createCategory: (data) => api.post('/admin/categories', data),
      updateCategory: (id, data) => api.put(`/admin/categories/${id}`, data),
      deleteCategory: (id) => api.delete(`/admin/categories/${id}`),
    },

    // Public endpoints
    public: {
      getVendorMenuByLink: async (vendorLink) => {
        const response = await api.get(`/menu/${vendorLink}`)
        return response.data
      },
      getVendorBySubdomain: async (subdomain) => {
        const response = await api.get(`/subdomain/${subdomain}`)
        return response.data
      }
    }
  }
}
