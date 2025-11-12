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
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
        <p class="text-gray-600">Last updated: January 1, 2025</p>
      </div>

      <!-- Content -->
      <div class="prose prose-lg max-w-none">
        <div class="bg-blue-50 border-l-4 border-blue-600 p-6 mb-8">
          <p class="text-blue-900 mb-0">
            At VendScan, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our service.
          </p>
        </div>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Information We Collect</h2>

          <h3 class="text-xl font-semibold text-gray-900 mb-3">Personal Information</h3>
          <p class="text-gray-600 mb-4">
            We collect information that you provide directly to us when you:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Create an account (name, email address, phone number)</li>
            <li>Set up your business profile (business name, address, type)</li>
            <li>Upload menu items and images</li>
            <li>Contact our support team</li>
            <li>Subscribe to our paid plans (payment information via secure third-party processors)</li>
          </ul>

          <h3 class="text-xl font-semibold text-gray-900 mb-3">Automatically Collected Information</h3>
          <p class="text-gray-600 mb-4">
            When you access our service, we automatically collect:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Device information (IP address, browser type, operating system)</li>
            <li>Usage data (pages viewed, time spent, features used)</li>
            <li>QR code scan statistics (for analytics purposes)</li>
            <li>Cookies and similar tracking technologies</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">2. How We Use Your Information</h2>
          <p class="text-gray-600 mb-4">
            We use the collected information to:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Provide, maintain, and improve our services</li>
            <li>Process your transactions and manage subscriptions</li>
            <li>Send you technical notices, updates, and support messages</li>
            <li>Respond to your comments and questions</li>
            <li>Analyze usage patterns and optimize user experience</li>
            <li>Detect, prevent, and address technical issues or fraud</li>
            <li>Send marketing communications (with your consent)</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Information Sharing and Disclosure</h2>
          <p class="text-gray-600 mb-4">
            We do not sell your personal information. We may share your information only in the following circumstances:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li><strong>Service Providers:</strong> Third-party vendors who perform services on our behalf (payment processing, hosting, analytics)</li>
            <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
            <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
            <li><strong>With Your Consent:</strong> When you explicitly authorize us to share your information</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Data Security</h2>
          <p class="text-gray-600 mb-4">
            We implement appropriate technical and organizational measures to protect your personal information, including:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Encryption of data in transit and at rest</li>
            <li>Regular security assessments and updates</li>
            <li>Access controls and authentication mechanisms</li>
            <li>Secure third-party payment processing (we do not store full payment card details)</li>
          </ul>
          <p class="text-gray-600">
            However, no method of transmission over the Internet is 100% secure. While we strive to protect your information, we cannot guarantee absolute security.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Your Rights and Choices</h2>
          <p class="text-gray-600 mb-4">
            You have the following rights regarding your personal information:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li><strong>Access:</strong> Request a copy of the personal information we hold about you</li>
            <li><strong>Correction:</strong> Update or correct inaccurate information</li>
            <li><strong>Deletion:</strong> Request deletion of your personal information (subject to legal obligations)</li>
            <li><strong>Opt-out:</strong> Unsubscribe from marketing communications at any time</li>
            <li><strong>Data Portability:</strong> Receive your data in a structured, machine-readable format</li>
          </ul>
          <p class="text-gray-600">
            To exercise these rights, please contact us at <a href="mailto:privacy@vendscan.com" class="text-primary-600 hover:text-primary-700">privacy@vendscan.com</a>.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Cookies and Tracking Technologies</h2>
          <p class="text-gray-600 mb-4">
            We use cookies and similar technologies to:
          </p>
          <ul class="list-disc pl-6 text-gray-600 mb-6 space-y-2">
            <li>Remember your preferences and settings</li>
            <li>Understand how you use our service</li>
            <li>Improve and personalize your experience</li>
            <li>Analyze traffic and usage patterns</li>
          </ul>
          <p class="text-gray-600">
            You can control cookies through your browser settings. However, disabling cookies may limit certain features of our service.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Children's Privacy</h2>
          <p class="text-gray-600">
            Our service is not directed to children under 13 years of age. We do not knowingly collect personal information from children under 13. If you become aware that a child has provided us with personal information, please contact us immediately.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">8. International Data Transfers</h2>
          <p class="text-gray-600">
            Your information may be transferred to and processed in countries other than your country of residence. These countries may have data protection laws different from those in your country. We ensure appropriate safeguards are in place to protect your information.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Data Retention</h2>
          <p class="text-gray-600">
            We retain your personal information for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law. When we no longer need your information, we will securely delete or anonymize it.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Changes to This Privacy Policy</h2>
          <p class="text-gray-600">
            We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new Privacy Policy on this page and updating the "Last updated" date. Your continued use of the service after changes become effective constitutes acceptance of the revised policy.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Contact Us</h2>
          <p class="text-gray-600 mb-4">
            If you have questions or concerns about this Privacy Policy or our data practices, please contact us:
          </p>
          <div class="bg-gray-100 rounded-lg p-6">
            <p class="text-gray-900 font-semibold mb-2">VendScan Privacy Team</p>
            <p class="text-gray-600">Email: <a href="mailto:privacy@vendscan.com" class="text-primary-600 hover:text-primary-700">privacy@vendscan.com</a></p>
            <p class="text-gray-600">Address: 123 Business Ave, San Francisco, CA 94102</p>
            <p class="text-gray-600">Phone: +1 (555) 123-4567</p>
          </div>
        </section>
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
