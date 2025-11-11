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
    userRole: (state) => state.user?.role || null,
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
        console.error('Login error:', error)
        throw error
      }
    },

    async register(data) {
      try {
        const { auth } = useApi()
        const response = await auth.register(data)

        if (response.data.user || response.data.success) {
          // Auto-login after registration
          return await this.login({
            email: data.email,
            password: data.password
          })
        }
        return response.data
      } catch (error) {
        console.error('Registration error:', error)
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
          console.error('Error parsing user data:', error)
          this.clearAuth()
        }
      }
    },
  },
})
