import {defineStore} from 'pinia'
import {useApi} from '@/composables/useApi'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: null,
        isAuthenticated: false,
    }),

    getters: {
        isVendor: (state) => state.user?.role === 'vendor',
        isAdmin: (state) => state.user?.role === 'admin',
        isServer: (state) => state.user?.role === 'server',
        userName: (state) => state.user
            ? `${state.user.first_name} ${state.user.last_name}`
            : '',
        userRole: (state) => state.user?.role || null,
    },

    actions: {
        async login(credentials) {
            try {
                const {auth} = useApi()
                const response = await auth.login(credentials)

                console.log('🔐 Login response structure:', response.data)

                const {token: tokenObj, user} = response.data.data

                // Extract the actual access_token from the token object
                const accessToken = tokenObj.access_token

                console.log('🔑 Token object:', tokenObj)
                console.log('🔑 Access token:', accessToken)
                console.log('👤 User:', user)

                this.setAuth(accessToken, user)
                return user
            } catch (error) {
                console.error('Login error:', error)
                throw error
            }
        },

        async register(data) {
            try {
                const {auth} = useApi()
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
                const {auth} = useApi()
                await auth.logout()
            } catch (error) {
                console.error('Logout error:', error)
            } finally {
                this.clearAuth()
            }
        },

        async refreshToken() {
            try {
                const {auth} = useApi()
                const response = await auth.refresh()

                console.log('🔄 Refresh response:', response.data)

                // Refresh endpoint returns data directly (not wrapped in data.data)
                // and also returns token object with access_token inside
                const accessToken = response.data.access_token
                const user = response.data.user

                console.log('🔄 Refresh access token:', accessToken)
                console.log('🔄 Refresh user:', user)

                this.setAuth(accessToken, user)
            } catch (error) {
                console.error('🔄 Refresh error:', error)
                this.clearAuth()
                throw error
            }
        },

        async fetchUser() {
            try {
                const {auth} = useApi()
                const response = await auth.getUser()
                return response.data
            } catch (error) {
                console.error('Error fetching user:', error)
                throw error
            }
        },

        setAuth(token, user) {
            console.log('💾 Setting auth - Token:', token)
            console.log('💾 Setting auth - User:', user)

            this.token = token
            this.user = user
            this.isAuthenticated = true

            localStorage.setItem('token', token)
            localStorage.setItem('user', JSON.stringify(user))

            console.log('✅ Auth set in localStorage')
            console.log('✅ Store token:', this.token)
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
