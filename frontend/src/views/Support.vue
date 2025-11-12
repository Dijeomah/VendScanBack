<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <router-link to="/" class="flex items-center">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="ml-2 text-xl font-bold text-gray-900">VendScan</span>
          </router-link>
          <div class="flex items-center gap-4">
            <router-link to="/login" class="text-gray-700 hover:text-primary-600 font-medium">Login</router-link>
            <router-link to="/vendor/signup" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all">
              Get Started
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Help Center</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Find answers to common questions and learn how to get the most out of VendScan
        </p>
      </div>

      <!-- Search Bar -->
      <div class="max-w-2xl mx-auto mb-12">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search for help..."
            class="w-full px-6 py-4 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-lg"
          />
          <svg class="absolute left-4 top-4 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow cursor-pointer">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Getting Started</h3>
          <p class="text-gray-600 text-sm">Learn the basics of setting up your VendScan account</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow cursor-pointer">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Menu Management</h3>
          <p class="text-gray-600 text-sm">How to create and manage your digital menu</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow cursor-pointer">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">QR Codes</h3>
          <p class="text-gray-600 text-sm">Generate and manage QR codes for your tables</p>
        </div>
      </div>

      <!-- FAQ Sections -->
      <div class="max-w-4xl mx-auto space-y-12">
        <!-- Getting Started -->
        <div>
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Getting Started</h2>
          <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(0)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">How do I create an account?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 0 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 0" class="px-6 pb-6">
                <p class="text-gray-600">
                  Click the "Get Started" button on the homepage, fill in your business details, and verify your email address. You'll be up and running in minutes!
                </p>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(1)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">What subscription plan should I choose?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 1 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 1" class="px-6 pb-6">
                <p class="text-gray-600">
                  Start with the Free plan (up to 3 businesses). Upgrade to Pro (5 businesses) or Max (unlimited) as your needs grow. All plans include unlimited menu items and QR codes.
                </p>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(2)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">Can I try VendScan before paying?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 2 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 2" class="px-6 pb-6">
                <p class="text-gray-600">
                  Yes! Our Free plan lets you manage up to 3 businesses with full access to all core features. No credit card required.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Menu Management -->
        <div>
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Menu Management</h2>
          <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(3)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">How do I add menu items?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 3 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 3" class="px-6 pb-6">
                <p class="text-gray-600">
                  Navigate to Menu Management, click "Add New Item", fill in the details (name, price, description, image), select a category, and save. You can also bulk import items from a CSV file.
                </p>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(4)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">Can I organize items into categories?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 4 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 4" class="px-6 pb-6">
                <p class="text-gray-600">
                  Absolutely! Create custom categories like "Appetizers," "Main Courses," "Desserts," etc. Each menu item can be assigned to a category for better organization.
                </p>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(5)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">How do I add photos to menu items?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 5 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 5" class="px-6 pb-6">
                <p class="text-gray-600">
                  When creating or editing an item, click "Upload Image" and select a photo from your device. We support JPG, PNG, and GIF files up to 5MB. Square images work best!
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- QR Codes & Tables -->
        <div>
          <h2 class="text-2xl font-bold text-gray-900 mb-6">QR Codes & Table Management</h2>
          <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(6)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">How do I generate QR codes?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 6 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 6" class="px-6 pb-6">
                <p class="text-gray-600">
                  Go to Table Management, create tables for your restaurant, and QR codes are automatically generated for each table. Download and print them to place on your tables.
                </p>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(7)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">Can customers scan the QR code from any phone?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 7 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 7" class="px-6 pb-6">
                <p class="text-gray-600">
                  Yes! Our QR codes work on all smartphones (iOS and Android). Customers simply open their camera app and scan - no app download required.
                </p>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(8)"
                class="w-full flex items-center justify-between p-6 text-left"
              >
                <span class="text-lg font-semibold text-gray-900">How do I assign servers to tables?</span>
                <svg
                  :class="['w-5 h-5 text-gray-500 transition-transform', openFaq === 8 ? 'transform rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="openFaq === 8" class="px-6 pb-6">
                <p class="text-gray-600">
                  In Server Management, create server profiles and then assign them to specific tables or sections. This helps track which server is responsible for each table.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Still Need Help -->
      <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg p-12 text-center mt-16">
        <h2 class="text-3xl font-bold text-white mb-4">Still need help?</h2>
        <p class="text-primary-100 mb-8 max-w-2xl mx-auto">
          Can't find the answer you're looking for? Our support team is here to help.
        </p>
        <router-link
          to="/contact"
          class="inline-flex items-center px-8 py-3 bg-white text-primary-600 rounded-lg hover:bg-gray-100 transition-all font-semibold"
        >
          Contact Support
          <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </router-link>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="col-span-1">
            <div class="flex items-center mb-4">
              <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <span class="ml-2 text-xl font-bold text-gray-900">VendScan</span>
            </div>
            <p class="text-gray-600 text-sm">Modern QR menu management for restaurants and food vendors.</p>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 mb-3">Company</h3>
            <ul class="space-y-2 text-sm">
              <li><router-link to="/about" class="text-gray-600 hover:text-primary-600">About</router-link></li>
              <li><router-link to="/contact" class="text-gray-600 hover:text-primary-600">Contact</router-link></li>
            </ul>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 mb-3">Support</h3>
            <ul class="space-y-2 text-sm">
              <li><router-link to="/support" class="text-gray-600 hover:text-primary-600">Help Center</router-link></li>
            </ul>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 mb-3">Legal</h3>
            <ul class="space-y-2 text-sm">
              <li><router-link to="/privacy" class="text-gray-600 hover:text-primary-600">Privacy</router-link></li>
              <li><router-link to="/terms" class="text-gray-600 hover:text-primary-600">Terms</router-link></li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-600">
          <p>&copy; 2025 VendScan. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const searchQuery = ref('')
const openFaq = ref(null)

const toggleFaq = (index) => {
  openFaq.value = openFaq.value === index ? null : index
}
</script>
