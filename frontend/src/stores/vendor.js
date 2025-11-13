import {defineStore} from 'pinia'
import {useApi} from '@/composables/useApi'

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
        inactiveItems: (state) => state.items.filter(item => !item.status),
        itemsByCategory: (state) => (categoryId) =>
            state.items.filter(item => item.category_id === categoryId),
        totalItems: (state) => state.items.length,
        totalCategories: (state) => state.categories.length,
    },

    actions: {
        async fetchFullProfile() {
            this.loading = true
            this.error = null
            try {
                const {vendor} = useApi()
                const response = await vendor.getFullProfile()

                this.vendor = response.data.data || response.data
                this.categories = this.vendor?.categories || []

                // Extract items from business_links
                const businessLinks = this.vendor?.business_links || []
                // Flatten items from all business links instead of just the first one
                this.items = businessLinks.flatMap(link => link?.items || [])

                return this.vendor
            } catch (error) {
                this.error = error.message
                console.error('Error fetching vendor profile:', error)
                throw error
            } finally {
                this.loading = false
            }
        },

        async fetchDashboard() {
            this.loading = true
            try {
                const {vendor} = useApi()
                const response = await vendor.getDashboard()
                return response.data
            } catch (error) {
                this.error = error.message
                throw error
            } finally {
                this.loading = false
            }
        },

        async createItem(data) {
            try {
                const {vendor} = useApi()
                const response = await vendor.createItem(data)
                const newItem = response.data.data || response.data

                this.items.push(newItem)
                return newItem
            } catch (error) {
                console.error('Error creating item:', error)
                throw error
            }
        },

        async updateItem(id, data) {
            try {
                const {vendor} = useApi()
                const response = await vendor.updateItem(id, data)
                const updatedItem = response.data.data || response.data

                const index = this.items.findIndex(item => item.id === id)
                if (index !== -1) {
                    this.items[index] = updatedItem
                }

                return updatedItem
            } catch (error) {
                console.error('Error updating item:', error)
                throw error
            }
        },

        async deleteItem(id) {
            try {
                const {vendor} = useApi()
                await vendor.deleteItem(id)

                this.items = this.items.filter(item => item.id !== id)
            } catch (error) {
                console.error('Error deleting item:', error)
                throw error
            }
        },

        async createCategory(data) {
            try {
                const {vendor} = useApi()
                const response = await vendor.createCategory(data)
                const newCategory = response.data.data || response.data

                this.categories.push(newCategory)
                return newCategory
            } catch (error) {
                console.error('Error creating category:', error)
                throw error
            }
        },

        async updateProfile(data) {
            try {
                const {vendor} = useApi()
                const response = await vendor.updateProfile(data)

                this.vendor = {...this.vendor, ...response.data.data}
                return this.vendor
            } catch (error) {
                console.error('Error updating profile:', error)
                throw error
            }
        },

        async uploadMedia(formData) {
            try {
                const {vendor} = useApi()
                const response = await vendor.uploadMedia(formData)

                // Update vendor with new media URLs
                if (response.data.data) {
                    this.vendor = {...this.vendor, ...response.data.data}
                }

                return response.data
            } catch (error) {
                console.error('Error uploading media:', error)
                throw error
            }
        },

        async generateQR() {
            try {
                const {vendor} = useApi()
                const response = await vendor.generateQR()
                return response.data
            } catch (error) {
                console.error('Error generating QR code:', error)
                throw error
            }
        },

        async setBusinessLink(formData) {
            try {
                const {vendor} = useApi()
                console.log(formData)
                const response = await vendor.setBusinessLink(formData)
                return response.data
            } catch (error) {
                console.error('Error setting business link:', error)
                throw error
            }
        },

        async setBusinessInfo(formData) {
            try {
                const {vendor} = useApi()
                const response = await vendor.setBusinessInfo(formData)
                return response.data
            } catch (error) {
                console.error('Error setting business info:', error)
                throw error
            }
        }
    },
})
