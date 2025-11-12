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
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
      <!-- Header -->
      <div class="mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Terms of Service</h1>
        <p class="text-gray-600">Last updated: January 1, 2025</p>
      </div>

      <!-- Content -->
      <div class="prose prose-lg max-w-none">
        <div class="bg-blue-50 border-l-4 border-blue-600 p-6 mb-8">
          <p class="text-blue-900 mb-0">
            Please read these Terms of Service carefully before using VendScan. By accessing or using our service, you agree to be bound by these terms.
          </p>
        </div>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Acceptance of Terms</h2>
          <p class="text-gray-600">
            By creating an account, accessing, or using VendScan ("Service"), you agree to be bound by these Terms of Service ("Terms"). If you disagree with any part of these terms, you may not access the Service. These Terms apply to all users, including vendors, customers, and visitors.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Description of Service</h2>
          <p class="text-gray-600 mb-4">
            VendScan provides a cloud-based platform for restaurants and food vendors to:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Create and manage digital menus</li>
            <li>Generate QR codes for table access</li>
            <li>Manage multiple business locations</li>
            <li>Track table and server assignments</li>
            <li>Provide analytics and reporting</li>
          </ul>
          <p class="text-gray-600">
            We reserve the right to modify, suspend, or discontinue any part of the Service at any time with or without notice.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Account Registration and Security</h2>
          <h3 class="text-xl font-semibold text-gray-900 mb-3">Account Creation</h3>
          <p class="text-gray-600 mb-4">
            To use VendScan, you must:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Provide accurate, current, and complete information</li>
            <li>Be at least 18 years of age or have parental/guardian consent</li>
            <li>Maintain and update your account information</li>
            <li>Be responsible for all activities under your account</li>
          </ul>

          <h3 class="text-xl font-semibold text-gray-900 mb-3">Account Security</h3>
          <p class="text-gray-600 mb-4">
            You are responsible for:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Maintaining the confidentiality of your password</li>
            <li>All activities that occur under your account</li>
            <li>Notifying us immediately of any unauthorized access</li>
          </ul>
          <p class="text-gray-600">
            We are not liable for any loss or damage from your failure to comply with these security obligations.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Subscription Plans and Billing</h2>
          <h3 class="text-xl font-semibold text-gray-900 mb-3">Plan Types</h3>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li><strong>Free Plan:</strong> Up to 3 businesses with basic features</li>
            <li><strong>Pro Plan:</strong> $29/month for up to 5 businesses with advanced features</li>
            <li><strong>Max Plan:</strong> $99/month for unlimited businesses and premium features</li>
          </ul>

          <h3 class="text-xl font-semibold text-gray-900 mb-3">Billing</h3>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Subscriptions are billed monthly in advance</li>
            <li>Payments are processed securely through third-party payment processors</li>
            <li>You authorize us to charge your payment method automatically</li>
            <li>Failed payments may result in service suspension</li>
            <li>All fees are non-refundable except as required by law or our 14-day money-back guarantee</li>
          </ul>

          <h3 class="text-xl font-semibold text-gray-900 mb-3">Cancellation</h3>
          <p class="text-gray-600">
            You may cancel your subscription at any time. Upon cancellation, you will retain access until the end of your current billing period. No refunds will be provided for partial months.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">5. User Content and Conduct</h2>
          <h3 class="text-xl font-semibold text-gray-900 mb-3">Your Content</h3>
          <p class="text-gray-600 mb-4">
            You retain ownership of all content you upload (menus, images, business information). By uploading content, you grant us a non-exclusive, worldwide, royalty-free license to use, store, and display your content solely for providing the Service.
          </p>

          <h3 class="text-xl font-semibold text-gray-900 mb-3">Prohibited Conduct</h3>
          <p class="text-gray-600 mb-4">
            You agree NOT to:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Upload illegal, offensive, or infringing content</li>
            <li>Violate any laws or regulations</li>
            <li>Attempt to gain unauthorized access to the Service</li>
            <li>Interfere with or disrupt the Service</li>
            <li>Use the Service for any fraudulent or malicious purpose</li>
            <li>Scrape, crawl, or use automated means to access the Service without permission</li>
            <li>Resell or redistribute the Service without authorization</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Intellectual Property</h2>
          <p class="text-gray-600 mb-4">
            The Service and its original content, features, and functionality are owned by VendScan and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.
          </p>
          <p class="text-gray-600">
            You may not copy, modify, distribute, sell, or lease any part of our Service without our express written permission.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Disclaimer of Warranties</h2>
          <p class="text-gray-600 mb-4">
            THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Warranties of merchantability or fitness for a particular purpose</li>
            <li>Warranties of non-infringement</li>
            <li>Warranties that the Service will be uninterrupted, secure, or error-free</li>
            <li>Warranties regarding the accuracy or reliability of any information obtained through the Service</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Limitation of Liability</h2>
          <p class="text-gray-600 mb-4">
            TO THE MAXIMUM EXTENT PERMITTED BY LAW, VENDSCAN SHALL NOT BE LIABLE FOR:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Any indirect, incidental, special, consequential, or punitive damages</li>
            <li>Loss of profits, revenue, data, or use</li>
            <li>Business interruption or loss of goodwill</li>
            <li>Any damages arising from your use or inability to use the Service</li>
          </ul>
          <p class="text-gray-600">
            Our total liability shall not exceed the amount you paid us in the 12 months preceding the claim.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Indemnification</h2>
          <p class="text-gray-600">
            You agree to indemnify, defend, and hold harmless VendScan and its officers, directors, employees, and agents from any claims, damages, losses, liabilities, and expenses (including legal fees) arising from your use of the Service, violation of these Terms, or infringement of any third-party rights.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Termination</h2>
          <p class="text-gray-600 mb-4">
            We may terminate or suspend your account and access to the Service immediately, without prior notice or liability, for any reason, including:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Breach of these Terms</li>
            <li>Non-payment of fees</li>
            <li>Fraudulent or illegal activity</li>
            <li>At our sole discretion</li>
          </ul>
          <p class="text-gray-600">
            Upon termination, your right to use the Service will cease immediately. We may delete your account and content, but we are not obligated to do so.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Governing Law and Dispute Resolution</h2>
          <p class="text-gray-600 mb-4">
            These Terms shall be governed by and construed in accordance with the laws of the State of California, United States, without regard to its conflict of law provisions.
          </p>
          <p class="text-gray-600">
            Any disputes arising from these Terms or the Service shall be resolved through binding arbitration in San Francisco, California, except where prohibited by law.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Changes to Terms</h2>
          <p class="text-gray-600">
            We reserve the right to modify these Terms at any time. We will notify you of material changes by email or through the Service. Your continued use after changes become effective constitutes acceptance of the revised Terms.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Severability</h2>
          <p class="text-gray-600">
            If any provision of these Terms is found to be unenforceable or invalid, that provision will be limited or eliminated to the minimum extent necessary, and the remaining provisions will remain in full force and effect.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">14. Entire Agreement</h2>
          <p class="text-gray-600">
            These Terms, together with our Privacy Policy, constitute the entire agreement between you and VendScan regarding the Service and supersede all prior agreements and understandings.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">15. Contact Information</h2>
          <p class="text-gray-600 mb-4">
            If you have any questions about these Terms, please contact us:
          </p>
          <div class="bg-gray-100 rounded-lg p-6">
            <p class="text-gray-900 font-semibold mb-2">VendScan Legal Team</p>
            <p class="text-gray-600">Email: <a href="mailto:legal@vendscan.com" class="text-primary-600 hover:text-primary-700">legal@vendscan.com</a></p>
            <p class="text-gray-600">Address: 123 Business Ave, San Francisco, CA 94102</p>
            <p class="text-gray-600">Phone: +1 (555) 123-4567</p>
          </div>
        </section>

        <div class="bg-yellow-50 border-l-4 border-yellow-600 p-6">
          <p class="text-yellow-900 mb-0">
            By using VendScan, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.
          </p>
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
// No special logic needed for this page
</script>

<style scoped>
.prose {
  color: #4b5563;
}

.prose h2 {
  color: #111827;
  font-weight: 700;
}

.prose h3 {
  color: #111827;
  font-weight: 600;
}

.prose a {
  text-decoration: none;
}

.prose a:hover {
  text-decoration: underline;
}
</style>
