import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

// Configure NProgress
NProgress.configure({
  showSpinner: false,
  trickleSpeed: 200,
  minimum: 0.3,
  easing: 'ease',
  speed: 500
})

const routes = [
  // Auth routes
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/Login.vue'),
    meta: { guest: true, title: 'Login' }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/Register.vue'),
    meta: { guest: true, title: 'Register' }
  },

  // Public menu routes
  {
    path: '/menu/:vendorLink',
    name: 'public-menu',
    component: () => import('@/views/menu/PublicMenu.vue'),
    meta: { title: 'Menu' }
  },

  // Footer pages
  {
    path: '/about',
    name: 'about',
    component: () => import('@/views/About.vue'),
    meta: { title: 'About Us' }
  },
  {
    path: '/contact',
    name: 'contact',
    component: () => import('@/views/Contact.vue'),
    meta: { title: 'Contact Us' }
  },
  {
    path: '/support',
    name: 'support',
    component: () => import('@/views/Support.vue'),
    meta: { title: 'Support Center' }
  },
  {
    path: '/privacy',
    name: 'privacy',
    component: () => import('@/views/Privacy.vue'),
    meta: { title: 'Privacy Policy' }
  },
  {
    path: '/terms',
    name: 'terms',
    component: () => import('@/views/Terms.vue'),
    meta: { title: 'Terms of Service' }
  },

  // Public menu ordering routes
  {
    path: '/m/:businessLink',
    name: 'Menu',
    component: () => import('@/views/public/Menu.vue'),
    meta: { title: 'Menu' }
  },
  {
    path: '/m/:businessLink/checkout',
    name: 'Checkout',
    component: () => import('@/views/public/Checkout.vue'),
    meta: { title: 'Checkout' }
  },
  {
    path: '/order/:orderNumber',
    name: 'OrderConfirmation',
    component: () => import('@/views/public/OrderConfirmation.vue'),
    meta: { title: 'Order Confirmation' }
  },

  // Vendor routes
  {
    path: '/vendor',
    redirect: '/vendor/dashboard',
  },
  {
    path: '/vendor/dashboard',
    name: 'vendor-dashboard',
    component: () => import('@/views/vendor/Dashboard.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Dashboard' }
  },
  {
    path: '/vendor/menu',
    name: 'vendor-menu',
    component: () => import('@/views/vendor/MenuManagement.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Menu Management' }
  },
  {
    path: '/vendor/items/create',
    name: 'vendor-item-create',
    component: () => import('@/views/vendor/ItemCreate.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Create Item' }
  },
  {
    path: '/vendor/items/:id/edit',
    name: 'vendor-item-edit',
    component: () => import('@/views/vendor/ItemEdit.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Edit Item' }
  },
  {
    path: '/vendor/businesses',
    name: 'vendor-businesses',
    component: () => import('@/views/vendor/BusinessManagement.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Business Management' }
  },
  {
    path: '/vendor/settings',
    name: 'vendor-settings',
    component: () => import('@/views/vendor/Settings.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Settings' }
  },
  {
    path: '/vendor/tables',
    name: 'vendor-tables',
    component: () => import('@/views/vendor/TableManagement.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Table Management' }
  },
  {
    path: '/vendor/servers',
    name: 'vendor-servers',
    component: () => import('@/views/vendor/ServerManagement.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Server Management' }
  },
  {
    path: '/vendor/subscription',
    name: 'vendor-subscription',
    component: () => import('@/views/vendor/SubscriptionManagement.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Subscription Management' }
  },
  {
    path: '/vendor/orders',
    name: 'vendor-orders',
    component: () => import('@/views/vendor/Orders.vue'),
    meta: { requiresAuth: true, role: 'vendor', title: 'Orders & Transactions' }
  },

  // Server routes
  {
    path: '/server',
    redirect: '/server/dashboard',
  },
  {
    path: '/server/dashboard',
    name: 'server-dashboard',
    component: () => import('@/views/server/Dashboard.vue'),
    meta: { requiresAuth: true, role: 'server', title: 'Server Dashboard' }
  },
  {
    path: '/server/orders',
    name: 'server-orders',
    component: () => import('@/views/server/Orders.vue'),
    meta: { requiresAuth: true, role: 'server', title: 'Orders' }
  },

  // Admin routes
  {
    path: '/admin',
    redirect: '/admin/dashboard',
  },
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: () => import('@/views/admin/Dashboard.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Admin Dashboard' }
  },
  {
    path: '/admin/vendors',
    name: 'admin-vendors',
    component: () => import('@/views/admin/VendorList.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Vendors' }
  },
  {
    path: '/admin/vendors/create',
    name: 'admin-vendor-create',
    component: () => import('@/views/admin/VendorCreate.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Create Vendor' }
  },
  {
    path: '/admin/vendors/:id/edit',
    name: 'admin-vendor-edit',
    component: () => import('@/views/admin/VendorEdit.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Edit Vendor' }
  },
  {
    path: '/admin/tables',
    name: 'admin-tables',
    component: () => import('@/views/admin/Tables.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Tables Management' }
  },
  {
    path: '/admin/servers',
    name: 'admin-servers',
    component: () => import('@/views/admin/Servers.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Servers Management' }
  },
  {
    path: '/admin/orders',
    name: 'admin-orders',
    component: () => import('@/views/admin/Orders.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Orders & Transactions' }
  },
  {
    path: '/admin/reports',
    name: 'admin-reports',
    component: () => import('@/views/admin/Reports.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Reports & Analytics' }
  },
  {
    path: '/admin/settings',
    name: 'admin-settings',
    component: () => import('@/views/admin/Settings.vue'),
    meta: { requiresAuth: true, role: 'admin', title: 'Settings' }
  },

  // Subdomain routes for ordering system
  {
    path: '/checkout',
    name: 'subdomain-checkout',
    component: () => import('@/views/public/Checkout.vue'),
    meta: { title: 'Checkout' },
    beforeEnter: (to, from, next) => {
      // Only allow on subdomains
      const hostname = window.location.hostname
      const parts = hostname.split('.')
      if (parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www') {
        next()
      } else {
        next('/') // Redirect to home if not on subdomain
      }
    }
  },
  {
    path: '/order/:orderNumber',
    name: 'subdomain-order-confirmation',
    component: () => import('@/views/public/OrderConfirmation.vue'),
    meta: { title: 'Order Confirmation' }
  },

  // Default route - check for subdomain
  {
    path: '/',
    name: 'home',
    beforeEnter: (to, from, next) => {
      // Check if we're on a subdomain
      const hostname = window.location.hostname
      const parts = hostname.split('.')

      // If on subdomain (e.g., airvend3.localhost), show new menu ordering system
      if (parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www') {
        // Load the new Menu component for subdomains
        to.matched[0].components = {
          default: () => import('@/views/public/Menu.vue')
        }
        next()
      } else {
        // Otherwise show landing page
        to.matched[0].components = {
          default: () => import('@/views/LandingPage.vue')
        }
        next()
      }
    },
    component: () => import('@/views/LandingPage.vue'),
    meta: { title: 'VendScan - Digital QR Menus' }
  },
  
  // 404 Not Found
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFound.vue'),
    meta: { title: 'Not Found' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guard
router.beforeEach((to, from, next) => {
  // Start loading bar
  NProgress.start()

  const authStore = useAuthStore()
  authStore.initAuth()

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const isGuest = to.matched.some(record => record.meta.guest)
  const requiredRole = to.meta.role

  // Set page title
  document.title = to.meta.title ? `${to.meta.title} - QR Menu` : 'QR Menu Management System'

  if (requiresAuth && !authStore.isAuthenticated) {
    // Redirect to login if not authenticated
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else if (isGuest && authStore.isAuthenticated) {
    // Redirect authenticated users away from guest pages
    if (authStore.isAdmin) {
      next('/admin/dashboard')
    } else if (authStore.isVendor) {
      next('/vendor/dashboard')
    } else if (authStore.isServer) {
      next('/server/dashboard')
    } else {
      next('/')
    }
  } else if (requiredRole && authStore.user?.role !== requiredRole) {
    // Role-based access control
    console.warn(`Access denied. Required role: ${requiredRole}, User role: ${authStore.user?.role}`)

    // Redirect to appropriate dashboard
    if (authStore.isAdmin) {
      next('/admin/dashboard')
    } else if (authStore.isVendor) {
      next('/vendor/dashboard')
    } else if (authStore.isServer) {
      next('/server/dashboard')
    } else {
      next('/login')
    }
  } else {
    next()
  }
})

// Complete loading bar after navigation
router.afterEach(() => {
  NProgress.done()
})

export default router
