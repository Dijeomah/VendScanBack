<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto p-8">
      <div class="bg-white rounded-lg shadow-sm p-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Public Menu Viewer</h1>
        <p class="text-gray-600 mb-6">Displaying menu for: {{ vendorLink }}</p>
        <p class="text-sm text-gray-500">This page will show the public-facing menu for customers who scan the QR code.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const vendorLink = computed(() => route.params.vendorLink)
</script>
