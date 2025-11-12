<template>
  <div class="min-h-screen bg-white">
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
          <router-link to="/" class="text-gray-600 hover:text-gray-900">
            ← Back to Home
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <h1 class="text-4xl font-bold text-gray-900 mb-8">About VendScan</h1>

      <div class="prose prose-lg max-w-none">
        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
          <p class="text-gray-600 mb-4">
            VendScan is revolutionizing the way restaurants and food businesses connect with their customers. Our mission is to make digital menu management accessible, affordable, and effortless for businesses of all sizes.
          </p>
          <p class="text-gray-600">
            We believe that every restaurant deserves modern, contactless menu technology without the complexity or high costs traditionally associated with digital transformation.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Story</h2>
          <p class="text-gray-600 mb-4">
            Founded in 2025, VendScan emerged from a simple observation: restaurants needed a better way to share their menus with customers. The traditional paper menu was becoming outdated, and existing digital solutions were either too expensive or too complicated.
          </p>
          <p class="text-gray-600 mb-4">
            We set out to create a platform that would combine simplicity, affordability, and powerful features. Today, VendScan serves hundreds of restaurants worldwide, from small cafes to large restaurant chains.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">What We Offer</h2>
          <ul class="space-y-3 text-gray-600">
            <li class="flex items-start">
              <svg class="w-6 h-6 text-primary-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>QR Code Menus:</strong> Generate custom QR codes that customers can scan to view your menu instantly</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-primary-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>Multi-Business Management:</strong> Manage multiple locations or brands from a single dashboard</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-primary-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>Table & Server Management:</strong> Organize your restaurant operations with table assignments and server management</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-primary-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>Real-Time Updates:</strong> Make changes to your menu and see them reflected instantly</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-primary-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span><strong>Flexible Pricing:</strong> Plans that scale with your business, from free to enterprise</span>
            </li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Values</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 bg-gray-50 rounded-lg">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Simplicity</h3>
              <p class="text-gray-600">We believe technology should be intuitive and easy to use, not complicated.</p>
            </div>
            <div class="p-6 bg-gray-50 rounded-lg">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Affordability</h3>
              <p class="text-gray-600">Quality digital solutions shouldn't break the bank. We keep our prices accessible.</p>
            </div>
            <div class="p-6 bg-gray-50 rounded-lg">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Innovation</h3>
              <p class="text-gray-600">We're constantly improving our platform with new features and capabilities.</p>
            </div>
            <div class="p-6 bg-gray-50 rounded-lg">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Support</h3>
              <p class="text-gray-600">Our team is here to help you succeed, every step of the way.</p>
            </div>
          </div>
        </section>

        <section class="bg-primary-50 rounded-lg p-8 text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Join Us Today</h2>
          <p class="text-gray-600 mb-6">
            Ready to modernize your restaurant's menu? Start your free trial today.
          </p>
          <router-link
            to="/register"
            class="inline-block px-8 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all font-semibold"
          >
            Get Started Free
          </router-link>
        </section>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p>&copy; 2025 VendScan. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
// About page
</script>
