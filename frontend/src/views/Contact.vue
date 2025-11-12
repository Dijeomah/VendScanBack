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
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Get in Touch</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Contact Info Cards -->
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
          <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Us</h3>
          <p class="text-gray-600 mb-2">Our team is here to help</p>
          <a href="mailto:support@vendscan.com" class="text-primary-600 hover:text-primary-700 font-medium">
            support@vendscan.com
          </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
          <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Call Us</h3>
          <p class="text-gray-600 mb-2">Mon-Fri from 8am to 5pm</p>
          <a href="tel:+15551234567" class="text-primary-600 hover:text-primary-700 font-medium">
            +1 (555) 123-4567
          </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
          <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Visit Us</h3>
          <p class="text-gray-600 mb-2">Come say hello</p>
          <p class="text-primary-600 font-medium">
            123 Business Ave<br />
            San Francisco, CA 94102
          </p>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="bg-white rounded-lg shadow-sm p-8 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a message</h2>
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                First Name *
              </label>
              <input
                id="first_name"
                v-model="form.first_name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="John"
              />
            </div>
            <div>
              <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                Last Name *
              </label>
              <input
                id="last_name"
                v-model="form.last_name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Doe"
              />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email *
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              placeholder="you@example.com"
            />
          </div>

          <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
              Subject *
            </label>
            <input
              id="subject"
              v-model="form.subject"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              placeholder="How can we help?"
            />
          </div>

          <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
              Message *
            </label>
            <textarea
              id="message"
              v-model="form.message"
              rows="6"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
              placeholder="Tell us more about your inquiry..."
            ></textarea>
          </div>

          <button
            type="submit"
            :disabled="submitting"
            class="w-full px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-semibold"
          >
            {{ submitting ? 'Sending...' : 'Send Message' }}
          </button>
        </form>
      </div>

      <!-- FAQ Section -->
      <div class="mt-16">
        <h2 class="text-3xl font-bold text-gray-900 text-center mb-8">Frequently Asked Questions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">What are your support hours?</h3>
            <p class="text-gray-600">Our support team is available Monday through Friday, 8am to 5pm PST. We typically respond within 24 hours.</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Do you offer phone support?</h3>
            <p class="text-gray-600">Yes! Phone support is available for Pro and Max plan subscribers. Free plan users can reach us via email.</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">How quickly will I hear back?</h3>
            <p class="text-gray-600">We aim to respond to all inquiries within 24 hours during business days. Urgent issues are prioritized.</p>
          </div>
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I schedule a demo?</h3>
            <p class="text-gray-600">Absolutely! Mention "demo request" in your message and we'll set up a personalized walkthrough of VendScan.</p>
          </div>
        </div>
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
import { useToast } from 'vue-toastification'

const toast = useToast()

const submitting = ref(false)
const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  subject: '',
  message: ''
})

const handleSubmit = async () => {
  submitting.value = true

  try {
    // TODO: Implement actual contact form submission
    await new Promise(resolve => setTimeout(resolve, 1500))

    toast.success('Message sent successfully! We\'ll get back to you soon.')

    // Reset form
    form.value = {
      first_name: '',
      last_name: '',
      email: '',
      subject: '',
      message: ''
    }
  } catch (error) {
    console.error('Error sending message:', error)
    toast.error('Failed to send message. Please try again or email us directly.')
  } finally {
    submitting.value = false
  }
}
</script>
