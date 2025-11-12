import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

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

  // Default route - check for subdomain
  {
    path: '/',
    name: 'home',
    beforeEnter: (to, from, next) => {
      // Check if we're on a subdomain
      const hostname = window.location.hostname
      const parts = hostname.split('.')

      // If on subdomain (e.g., airvend-res.localhost), show public menu
      if (parts.length > 1 && parts[0] !== 'localhost' && parts[0] !== 'www') {
        // Load the PublicMenu component dynamically for subdomains
        to.matched[0].components = {
          default: () => import('@/views/menu/PublicMenu.vue')
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
    } else {
      next('/login')
    }
  } else {
    next()
  }
})

export default router
