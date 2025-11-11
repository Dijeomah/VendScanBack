<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="text-center">
      <h1 class="text-9xl font-bold text-primary-600">404</h1>
      <p class="text-2xl font-semibold text-gray-900 mt-4">Page Not Found</p>
      <p class="text-gray-600 mt-2 mb-8">The page you're looking for doesn't exist or has been moved.</p>
      <router-link
        to="/"
        class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Go Back Home
      </router-link>
    </div>
  </div>
</template>

<script setup>
</script>
