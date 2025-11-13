<template>
    <VendorLayout>
        <div class="p-4 md:p-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Add New Menu Item</h1>
                    <p class="text-gray-600 mt-1">Create a new item for your menu</p>
                </div>
                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center items-center h-64">
                    <LoadingSpinner size="lg" text="Loading categories..."/>
                </div>

                <!-- Form -->
                <form v-else @submit.prevent="handleSubmit" class="bg-white rounded-lg shadow-sm p-6">
                    <div class="space-y-6">
                        <!-- Business -->
                        <div>
                            <label for="business" class="block text-sm font-medium text-gray-700 mb-2">
                                Business <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="business"
                                v-model="form.business_link"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.business_link }"
                            >
                                <option value="">Select a Business</option>
                                <option v-for="business in businesses" :key="business.id" :value="business.business_link">
                                    {{ business.business_data.business_name }}
                                </option>
                            </select>
                            <p v-if="errors.business_link" class="mt-1 text-sm text-red-600">
                                {{ errors.business_link }}</p>
                        </div>

                        <!-- Item Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Item Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                required
                                placeholder="e.g., Classic Burger"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.title }"
                            />
                            <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Describe your item..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                                :class="{ 'border-red-500': errors.description }"
                            ></textarea>
                            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                        </div>

                        <!-- Price and Category -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Price <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                    <input
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0.00"
                                        class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        :class="{ 'border-red-500': errors.price }"
                                    />
                                </div>
                                <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price }}</p>
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="category"
                                    v-model="form.category_id"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.category_id }"
                                >
                                    <option value="">Select a category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.category_name }}
                                    </option>
                                </select>
                                <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{
                                        errors.category_id
                                    }}</p>
                            </div>
                        </div>

                        <!-- Subcategory (Optional) -->
                        <div v-if="form.category_id && availableSubcategories.length > 0">
                            <label for="sub_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Subcategory
                            </label>
                            <select
                                id="sub_category_id"
                                v-model="form.sub_category_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="">No subcategory</option>
                                <option v-for="subcategory in availableSubcategories" :key="subcategory.id"
                                        :value="subcategory.id">
                                    {{ subcategory.sub_category_name }}
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select a subcategory to further organize this item</p>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Item Image
                            </label>
                            <div class="flex items-center gap-4">
                                <!-- Image Preview -->
                                <div v-if="imagePreview" class="flex-shrink-0">
                                    <img :src="imagePreview" alt="Preview"
                                         class="w-32 h-32 rounded-lg object-cover border-2 border-gray-200"/>
                                </div>

                                <!-- Upload Button -->
                                <div class="flex-1">
                                    <label
                                        for="image"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition-all"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ imagePreview ? 'Change Image' : 'Upload Image' }}
                                    </label>
                                    <input
                                        id="image"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleImageChange"
                                    />
                                    <p class="mt-2 text-sm text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                </div>

                                <!-- Remove Image -->
                                <button
                                    v-if="imagePreview"
                                    type="button"
                                    @click="removeImage"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Remove image"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                            <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image }}</p>
                        </div>

                        <!-- Status Toggle -->
                        <div>
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input
                                        v-model="form.status"
                                        type="checkbox"
                                        class="sr-only"
                                    />
                                    <div
                                        :class="[
                    'block w-14 h-8 rounded-full transition-colors',
                    form.status ? 'bg-green-500' : 'bg-gray-300'
                  ]"
                                    ></div>
                                    <div
                                        :class="[
                    'absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform',
                    form.status ? 'transform translate-x-6' : ''
                  ]"
                                    ></div>
                                </div>
                                <div class="ml-3">
                <span class="text-sm font-medium text-gray-700">
                  {{ form.status ? 'Active' : 'Inactive' }}
                </span>
                                    <p class="text-sm text-gray-500">
                                        {{
                                            form.status ? 'Item is visible to customers' : 'Item is hidden from customers'
                                        }}
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                            <router-link
                                to="/vendor/menu"
                                class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all"
                            >
                                Cancel
                            </router-link>
                            <button
                                type="submit"
                                :disabled="submitting"
                                class="px-6 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                                <LoadingSpinner v-if="submitting" size="sm"/>
                                <span>{{ submitting ? 'Creating...' : 'Create Item' }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </VendorLayout>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue'
import {useRouter} from 'vue-router'
import {useVendorStore} from '@/stores/vendor'
import {useToast} from 'vue-toastification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import VendorLayout from '@/components/layouts/VendorLayout.vue'

const router = useRouter()
const vendorStore = useVendorStore()
const toast = useToast()

const loading = ref(true)
const submitting = ref(false)
const categories = ref([])
const imagePreview = ref(null)
const imageFile = ref(null)
const businesses = ref([])

const form = ref({
    business_link: '',
    title: '',
    description: '',
    price: '',
    category_id: '',
    sub_category_id: '',
    status: true
})

const errors = ref({})

// Computed property to get subcategories for selected category
const availableSubcategories = computed(() => {
    if (!form.value.category_id) return []

    const selectedCategory = categories.value.find(cat => cat.id === form.value.category_id)
    return selectedCategory?.subcategories || []
})

const handleImageChange = (event) => {
    const file = event.target.files[0]
    if (!file) return

    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        errors.value.image = 'Image size must be less than 5MB'
        return
    }

    // Validate file type
    if (!file.type.startsWith('image/')) {
        errors.value.image = 'Please upload a valid image file'
        return
    }

    errors.value.image = null
    imageFile.value = file

    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
        imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
}

const removeImage = () => {
    imagePreview.value = null
    imageFile.value = null
    const input = document.getElementById('image')
    if (input) input.value = ''
}

const validateForm = () => {
    errors.value = {}

    if (!form.value.business_link) {
        errors.value.business_link = 'Business link is required'
    }

    if (!form.value.title.trim()) {
        errors.value.title = 'Title is required'
    }

    if (!form.value.price || parseFloat(form.value.price) < 0) {
        errors.value.price = 'Valid price is required'
    }

    if (!form.value.category_id) {
        errors.value.category_id = 'Category is required'
    }

    return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
    if (!validateForm()) {
        toast.error('Please fix the errors in the form')
        return
    }

    // Check if vendor has a business
    if (!vendorStore.vendor?.business_links || vendorStore.vendor.business_links.length === 0) {
        toast.error('Please create a business first')
        router.push('/vendor/businesses')
        return
    }

    submitting.value = true

    try {
        const formData = new FormData()
        // formData.append('business_link_id', form.value.business_link)
        formData.append('title', form.value.title)
        formData.append('description', form.value.description || '')
        formData.append('price', form.value.price)
        formData.append('category_id', form.value.category_id)
        formData.append('business_link',form.value.business_link ?? vendorStore.vendor.business_links[0].business_link)
        formData.append('status', form.value.status ? '1' : '0')

        // sub_category_id is optional
        if (form.value.sub_category_id) {
            formData.append('sub_category_id', form.value.sub_category_id)
        }

        if (imageFile.value) {
            formData.append('image', imageFile.value)
        }

        await vendorStore.createItem(formData)
        toast.success('Menu item created successfully!')
        router.push('/vendor/menu')
    } catch (error) {
        console.error('Error creating item:', error)
        toast.error(error.response?.data?.message || 'Failed to create item. Please try again.')

        // Handle validation errors from backend
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors
        }
    } finally {
        submitting.value = false
    }
}

onMounted(async () => {
    try {
        await vendorStore.fetchFullProfile();
        businesses.value = vendorStore.vendor?.business_links || [];
        categories.value = vendorStore.categories;
    } catch (error) {
        console.error('Error loading categories:', error)
        toast.error('Failed to load categories')
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
/* Custom styles if needed */
</style>
