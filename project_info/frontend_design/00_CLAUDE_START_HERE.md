# 🤖 CLAUDE: START HERE

## Your Mission
Build a complete, production-ready Vue.js 3 + Vite frontend for the QR Menu Management System.

Everything you need is in this folder. Follow the documents **in the exact order** specified below.

---

## ✅ COMPLETE EXECUTION CHECKLIST

### Pre-Flight Check
- [ ] Read this entire document first
- [ ] Verify Laravel backend is running on `http://localhost:8000`
- [ ] Verify you have Node.js 18+ installed
- [ ] Verify you have npm or yarn installed

### Build Order (Follow EXACTLY)

#### 📄 STEP 1: Understand the Project (30 minutes)
**Read these documents in order:**
1. [ ] `01_TECHNOLOGY_RECOMMENDATION.md` - Understand WHY Vue.js + Vite
2. [ ] `02_USER_FLOWS.md` - Understand user journeys & API mappings
3. [ ] `03_UI_UX_SPECIFICATIONS.md` - Review design system

**What you should know after this:**
- ✅ Technology stack (Vue 3, Vite, Pinia, Tailwind)
- ✅ All three user types (Customer, Vendor, Admin)
- ✅ Complete user workflows
- ✅ Color system and typography
- ✅ Component specifications

---

#### 🛠️ STEP 2: Set Up Project (Day 1 - 2 hours)
**Follow:** `06_STEP_BY_STEP_BUILD_PLAN.md` - DAY 1

**Tasks:**
- [ ] Create Vite + Vue 3 project
- [ ] Install ALL dependencies (complete list provided)
- [ ] Create folder structure
- [ ] Configure Vite (`vite.config.js`)
- [ ] Configure Tailwind (`tailwind.config.js`)
- [ ] Create CSS files
- [ ] Create `.env` file
- [ ] Update `main.js`
- [ ] Test: Run `npm run dev` - should start on port 3000

**Verification:**
```bash
npm run dev
# Should see: "Local: http://localhost:3000"
# Browser should open with Vue welcome page
```

---

#### 🧱 STEP 3: Build Foundation (Day 2-3 - 6 hours)
**Follow:** `07_COMPLETE_COMPONENTS.md` + `04_IMPLEMENTATION_GUIDE.md`

**Tasks:**
- [ ] Create API composable (`src/composables/useApi.js`)
- [ ] Create Auth store (`src/stores/auth.js`)
- [ ] Create Vendor store (`src/stores/vendor.js`)
- [ ] Create Router (`src/router/index.js`)
- [ ] Create all base components:
  - [ ] Button component
  - [ ] Input component
  - [ ] Modal component
  - [ ] LoadingSpinner component
  - [ ] Icon component (using Heroicons)

**Verification:**
```javascript
// Test in a component
import Button from '@/components/common/Button.vue'
// Should import without errors
```

---

#### 🔐 STEP 4: Build Authentication (Day 3 - 4 hours)
**Follow:** `08_ALL_PAGES.md` - Authentication Section

**Tasks:**
- [ ] Create Login page (`src/views/Login.vue`)
- [ ] Create Register page (`src/views/Register.vue`)
- [ ] Create Auth layout (`src/layouts/AuthLayout.vue`)
- [ ] Add routes to router
- [ ] Test login with Laravel backend
- [ ] Test register with Laravel backend

**Verification:**
1. Visit `http://localhost:3000/login`
2. Try logging in with test credentials
3. Should redirect to dashboard based on role
4. Token should be stored in localStorage

---

#### 🍽️ STEP 5: Build Public Menu (Days 4-6 - 8 hours)
**Follow:** `08_ALL_PAGES.md` - Public Menu Section + `07_COMPLETE_COMPONENTS.md`

**Tasks:**
- [ ] Create MenuItemCard component
- [ ] Create CategorySection component
- [ ] Create ItemDetailModal component
- [ ] Create MenuSearch component
- [ ] Create PublicMenuLayout
- [ ] Create PublicMenu page (`src/views/menu/PublicMenu.vue`)
- [ ] Add route to router
- [ ] Test subdomain detection
- [ ] Test menu display
- [ ] Test search functionality
- [ ] Test item modal

**Verification:**
1. Create a test vendor in Laravel with items
2. Visit `http://localhost:3000/menu/test-vendor`
3. Should see vendor menu with categories and items
4. Click item should open modal
5. Search should filter items

---

#### 👨‍💼 STEP 6: Build Vendor Dashboard (Days 7-12 - 16 hours)
**Follow:** `08_ALL_PAGES.md` - Vendor Section + `07_COMPLETE_COMPONENTS.md`

**Tasks:**
- [ ] Create DashboardLayout component
- [ ] Create Sidebar component
- [ ] Create Topbar component
- [ ] Create DashboardStats component
- [ ] Create QRCodeDisplay component
- [ ] Create Vendor Dashboard page
- [ ] Create Menu Management page
- [ ] Create ItemForm component
- [ ] Create CategoryForm component
- [ ] Create Item Create page
- [ ] Create Item Edit page
- [ ] Create Vendor Settings page
- [ ] Add all routes
- [ ] Test dashboard access (vendor role only)
- [ ] Test QR code display
- [ ] Test menu management (CRUD items)
- [ ] Test category management
- [ ] Test settings update

**Verification:**
1. Login as vendor
2. Should redirect to `/vendor/dashboard`
3. See stats, QR code, recent activity
4. Navigate to Menu Management
5. Create/Edit/Delete items
6. Create categories
7. Upload media

---

#### 👑 STEP 7: Build Admin Panel (Days 13-16 - 12 hours)
**Follow:** `08_ALL_PAGES.md` - Admin Section + `07_COMPLETE_COMPONENTS.md`

**Tasks:**
- [ ] Create VendorTable component
- [ ] Create VendorForm component
- [ ] Create SystemStats component
- [ ] Create Admin Dashboard page
- [ ] Create Vendor List page
- [ ] Create Vendor Create page
- [ ] Create Vendor Edit page
- [ ] Create Business Management page
- [ ] Add all routes
- [ ] Test admin access (admin role only)
- [ ] Test vendor CRUD
- [ ] Test vendor media upload
- [ ] Test QR generation for vendors

**Verification:**
1. Login as admin
2. Should redirect to `/admin/dashboard`
3. See system stats
4. Navigate to Vendors
5. Create/Edit/Delete vendors
6. Generate QR codes for vendors

---

#### 🧪 STEP 8: Testing & Polish (Days 17-18 - 6 hours)
**Follow:** `09_TESTING_AND_DEPLOYMENT.md`

**Tasks:**
- [ ] Test all user flows end-to-end
- [ ] Test authentication flows
- [ ] Test form validations
- [ ] Test error handling
- [ ] Test loading states
- [ ] Test responsive design on mobile
- [ ] Fix any bugs found
- [ ] Polish UI/UX
- [ ] Add loading skeletons
- [ ] Optimize images
- [ ] Test in different browsers

**Verification:**
- [ ] All pages load without console errors
- [ ] All forms submit correctly
- [ ] All API calls work
- [ ] Mobile responsive
- [ ] No broken links

---

#### 🚀 STEP 9: Build & Deploy (Day 18 - 2 hours)
**Follow:** `09_TESTING_AND_DEPLOYMENT.md` - Deployment Section

**Tasks:**
- [ ] Run production build
- [ ] Test production build locally
- [ ] Configure deployment (Netlify/Vercel)
- [ ] Deploy to production
- [ ] Test production deployment
- [ ] Configure custom domain (if needed)

**Verification:**
```bash
npm run build
# Should create dist/ folder with no errors

npm run preview
# Should serve production build locally
# Test all functionality
```

---

## 📁 Complete Document Reference

### Core Documents (Read in Order)
1. **00_CLAUDE_START_HERE.md** ← YOU ARE HERE
2. **01_TECHNOLOGY_RECOMMENDATION.md** - Why Vue.js + Vite
3. **02_USER_FLOWS.md** - User journeys & API mapping
4. **03_UI_UX_SPECIFICATIONS.md** - Design system
5. **04_IMPLEMENTATION_GUIDE.md** - Technical setup
6. **05_PAGE_IMPLEMENTATIONS.md** - Example implementations

### Build Documents (Follow Step by Step)
7. **06_STEP_BY_STEP_BUILD_PLAN.md** - Day-by-day plan (DETAILED)
8. **07_COMPLETE_COMPONENTS.md** - ALL components with full code
9. **08_ALL_PAGES.md** - ALL pages with full code
10. **09_TESTING_AND_DEPLOYMENT.md** - Testing & deployment guide
11. **10_API_COMPLETE_REFERENCE.md** - Full API documentation

### Reference Documents
12. **UPDATED_FOR_VUE.md** - Summary of Vue.js updates

---

## 🎯 Success Criteria

### When you're done, the app should:
- ✅ Allow customers to view menus via QR code scan
- ✅ Allow vendors to register, login, and manage menus
- ✅ Allow vendors to create/edit/delete items and categories
- ✅ Allow vendors to upload media (logo, hero image)
- ✅ Allow vendors to view and download QR codes
- ✅ Allow admins to manage all vendors
- ✅ Work on desktop, tablet, and mobile
- ✅ Have no console errors
- ✅ Have proper loading states
- ✅ Have proper error handling
- ✅ Be production-ready

---

## 🆘 If You Get Stuck

### Common Issues & Solutions

**Issue: Cannot find module '@/...'**
```bash
# Solution: Check vite.config.js has alias configured
resolve: {
  alias: {
    '@': path.resolve(__dirname, './src'),
  },
}
```

**Issue: API calls failing**
```bash
# Check:
1. Laravel backend running on http://localhost:8000
2. .env has correct VITE_API_BASE_URL
3. CORS enabled in Laravel
4. Token in Authorization header
```

**Issue: Pinia store not working**
```javascript
// Make sure main.js has:
import { createPinia } from 'pinia'
const pinia = createPinia()
app.use(pinia)
```

**Issue: Router not working**
```javascript
// Make sure main.js has:
import router from './router'
app.use(router)
```

**Issue: Tailwind styles not applying**
```css
// Check main.js imports:
import './assets/css/tailwind.css'

// Check tailwind.config.js content array includes your files
content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"]
```

---

## 📊 Progress Tracking

### Phase 1: Foundation ⬜
- [ ] Day 1: Project Setup
- [ ] Day 2: Base Components
- [ ] Day 3: Authentication

### Phase 2: Public Menu ⬜
- [ ] Day 4: Menu Components
- [ ] Day 5: Menu Page
- [ ] Day 6: Polish

### Phase 3: Vendor Dashboard ⬜
- [ ] Day 7: Dashboard Layout
- [ ] Day 8: Dashboard Page
- [ ] Day 9: Menu Management
- [ ] Day 10: Item CRUD
- [ ] Day 11: Category Management
- [ ] Day 12: Settings

### Phase 4: Admin Panel ⬜
- [ ] Day 13: Admin Dashboard
- [ ] Day 14: Vendor CRUD
- [ ] Day 15: Business Management
- [ ] Day 16: Polish

### Phase 5: Testing & Deploy ⬜
- [ ] Day 17: Testing
- [ ] Day 18: Deployment

---

## 🎓 What You'll Learn

By building this, you'll master:
- ✅ Vue 3 Composition API
- ✅ Vite build tool
- ✅ Pinia state management
- ✅ Vue Router with guards
- ✅ Axios API integration
- ✅ JWT authentication
- ✅ Tailwind CSS
- ✅ Form validation with Vee-Validate
- ✅ Component architecture
- ✅ Production deployment

---

## ⏱️ Estimated Timeline

- **Total Time:** 18 days (144 hours)
- **Working 8 hours/day:** 18 days
- **Working 4 hours/day:** 36 days
- **Part-time (2 hours/day):** 72 days

**With AI assistance (Claude):**
- **Estimated:** 10-12 days full-time
- **Estimated:** 20-24 days part-time

---

## 🚦 Ready to Start?

### Your Next Action:
1. ✅ Read `01_TECHNOLOGY_RECOMMENDATION.md`
2. ✅ Read `02_USER_FLOWS.md`
3. ✅ Read `03_UI_UX_SPECIFICATIONS.md`
4. ✅ Follow `06_STEP_BY_STEP_BUILD_PLAN.md` - DAY 1

---

## 📝 Notes for Claude AI

If you're Claude building this:
- Follow the documents **in exact order**
- Don't skip steps
- Test after each major component
- Ask for clarification if API responses don't match expectations
- All API endpoints are documented in `10_API_COMPLETE_REFERENCE.md`
- All components have full code in `07_COMPLETE_COMPONENTS.md`
- All pages have full code in `08_ALL_PAGES.md`
- When in doubt, refer to `04_IMPLEMENTATION_GUIDE.md`

**You have everything you need. Good luck! 🚀**
