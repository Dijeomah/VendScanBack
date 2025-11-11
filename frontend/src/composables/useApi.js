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

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
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

    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true

      try {
        await authStore.refreshToken()
        return api(originalRequest)
      } catch (refreshError) {
        authStore.logout()
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
      refresh: (credentials) => api.post('/auth/refresh', credentials),
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
      }
    }
  }
}
