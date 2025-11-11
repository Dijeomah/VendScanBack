# Testing and Deployment Guide

Complete guide for testing and deploying the Vue.js 3 + Vite frontend for the QR Menu Management System.

---

## Table of Contents

1. [Testing Strategy](#testing-strategy)
2. [Manual Testing Checklist](#manual-testing-checklist)
3. [Browser Compatibility](#browser-compatibility)
4. [Performance Optimization](#performance-optimization)
5. [Build for Production](#build-for-production)
6. [Deployment Options](#deployment-options)
7. [Environment Configuration](#environment-configuration)
8. [Post-Deployment Checklist](#post-deployment-checklist)

---

## Testing Strategy

### Test Pyramid

```
                    ┌─────────────┐
                    │   E2E Tests │ (Manual)
                    └─────────────┘
                  ┌───────────────────┐
                  │ Integration Tests │ (Manual)
                  └───────────────────┘
              ┌─────────────────────────────┐
              │      Component Tests         │ (Manual)
              └─────────────────────────────┘
          ┌─────────────────────────────────────┐
          │         Unit Tests (Optional)        │
          └─────────────────────────────────────┘
```

### Testing Priorities

1. **Critical Paths (Must Test)**
   - User authentication (login, register, logout)
   - Menu viewing (public menu)
   - Item CRUD operations (vendor)
   - QR code generation
   - Payment flows (if implemented)

2. **Important Features (Should Test)**
   - Search functionality
   - Filtering
   - Image uploads
   - Form validations
   - Error handling

3. **Nice to Have (Can Test)**
   - Responsive design edge cases
   - Loading states
   - Empty states
   - Toast notifications

---

## Manual Testing Checklist

### Authentication Flow

**Login Page** (`/login`)
- [ ] Can navigate to login page
- [ ] Form validation works (empty fields)
- [ ] Invalid credentials show error message
- [ ] Valid credentials redirect to correct dashboard (vendor/admin)
- [ ] "Remember me" checkbox persists login
- [ ] "Forgot password" link works (if implemented)
- [ ] Can navigate to register page

**Register Page** (`/register`)
- [ ] All form fields render correctly
- [ ] Email validation works
- [ ] Password confirmation validation works
- [ ] Country/State/City dropdowns populate correctly
- [ ] State dropdown disabled until country selected
- [ ] City dropdown disabled until state selected
- [ ] Form submission creates account
- [ ] Successful registration redirects to dashboard
- [ ] Error messages display for invalid data
- [ ] Can navigate to login page

**Logout**
- [ ] Logout clears auth token
- [ ] Logout redirects to login page
- [ ] Cannot access protected routes after logout

---

### Public Menu Flow

**Menu Viewer** (`/menu/:vendorLink`)
- [ ] Menu loads for valid vendor link
- [ ] Shows 404/error for invalid vendor link
- [ ] Hero image displays correctly
- [ ] Logo displays correctly
- [ ] Business name and description display
- [ ] Categories render in correct order
- [ ] Items display with images, prices, descriptions
- [ ] Search filters items correctly
- [ ] Clicking item opens detail modal
- [ ] Item detail modal shows complete information
- [ ] Modal closes correctly
- [ ] Category navigation works
- [ ] Unavailable items show proper status
- [ ] Page is mobile responsive
- [ ] Images load properly (or show placeholder)
- [ ] No console errors

---

### Vendor Dashboard Flow

**Dashboard** (`/vendor/dashboard`)
- [ ] Stats display correctly
- [ ] QR code displays if generated
- [ ] Can generate QR code
- [ ] Can download QR code
- [ ] Menu link is correct
- [ ] Copy link button works
- [ ] Quick action buttons navigate correctly
- [ ] Recent items display
- [ ] Can edit items from recent items
- [ ] Can delete items (with confirmation)

**Menu Management** (`/vendor/menu`)
- [ ] All items display in table
- [ ] Search filters items
- [ ] Category filter works
- [ ] Status filter works
- [ ] Can navigate to create item
- [ ] Can edit items
- [ ] Delete confirmation modal appears
- [ ] Can delete items
- [ ] Empty state shows when no items

**Create Item** (`/vendor/items/create`)
- [ ] Form renders correctly
- [ ] Categories dropdown populates
- [ ] All fields accept input
- [ ] Required validation works
- [ ] Price validation works (must be number)
- [ ] Can mark item as available/unavailable
- [ ] Form submits successfully
- [ ] Redirects to menu management after creation
- [ ] Error messages display for invalid data
- [ ] Can cancel and go back

**Edit Item** (`/vendor/items/:id/edit`)
- [ ] Form pre-fills with existing data
- [ ] Can update all fields
- [ ] Validation works same as create
- [ ] Form submits successfully
- [ ] Redirects after save
- [ ] Error handling works

**Settings** (`/vendor/settings`)
- [ ] Profile information pre-fills
- [ ] Can update profile
- [ ] Can upload logo
- [ ] Can upload hero image
- [ ] Can update business information
- [ ] Can update business link
- [ ] Changes save successfully
- [ ] Success/error messages display

---

### Admin Dashboard Flow

**Dashboard** (`/admin/dashboard`)
- [ ] System stats display
- [ ] Can navigate to vendor management
- [ ] Quick actions work

**Vendor Management** (`/admin/vendors`)
- [ ] All vendors display in table
- [ ] Can create new vendor
- [ ] Can edit existing vendor
- [ ] Delete confirmation modal appears
- [ ] Can delete vendor
- [ ] Search/filter works

**Create/Edit Vendor**
- [ ] Form validation works
- [ ] Can set vendor credentials
- [ ] Can upload vendor media
- [ ] Can generate QR for vendor
- [ ] Form submits successfully

---

### Error Handling

**Network Errors**
- [ ] Shows error message when API is down
- [ ] Shows error message for 500 errors
- [ ] Shows error message for 404 errors
- [ ] Shows error message for 401 (unauthorized)
- [ ] Retry logic works where applicable

**Validation Errors**
- [ ] Field-level errors display
- [ ] Form-level errors display
- [ ] Errors clear when fixed

**Loading States**
- [ ] Loading spinners show during API calls
- [ ] Buttons disable during submission
- [ ] Skeleton loaders show where appropriate

---

### Responsive Design Testing

**Test on these breakpoints:**
- [ ] Mobile (375px - iPhone SE)
- [ ] Mobile (390px - iPhone 12/13/14)
- [ ] Tablet (768px - iPad)
- [ ] Laptop (1024px)
- [ ] Desktop (1440px)
- [ ] Large Desktop (1920px)

**Components to test:**
- [ ] Navigation/Sidebar collapses on mobile
- [ ] Tables scroll horizontally on mobile
- [ ] Forms stack vertically on mobile
- [ ] Modals fit mobile screens
- [ ] Images scale appropriately
- [ ] Text is readable on all sizes
- [ ] Touch targets are ≥44px on mobile
- [ ] No horizontal scroll on any size

---

## Browser Compatibility

### Browsers to Test

**Desktop:**
- [ ] Chrome (latest 2 versions)
- [ ] Firefox (latest 2 versions)
- [ ] Safari (latest 2 versions)
- [ ] Edge (latest version)

**Mobile:**
- [ ] iOS Safari (latest)
- [ ] Chrome Android (latest)
- [ ] Samsung Internet (latest)

### Common Issues to Check

- [ ] CSS Grid/Flexbox works in all browsers
- [ ] Date inputs work (fallback for Safari)
- [ ] File uploads work
- [ ] LocalStorage works
- [ ] Fetch/Axios requests work
- [ ] No console errors in any browser

---

## Performance Optimization

### Before Deployment

**Code Splitting**
```javascript
// Already configured in vite.config.js
build: {
  rollupOptions: {
    output: {
      manualChunks: {
        'vendor': ['vue', 'vue-router', 'pinia'],
        'utils': ['axios', '@vueuse/core']
      }
    }
  }
}
```

**Image Optimization**
- [ ] Compress images before upload
- [ ] Use appropriate image formats (WebP with JPG fallback)
- [ ] Implement lazy loading for images
- [ ] Use responsive images (`srcset`)

**Performance Checklist**
- [ ] Remove console.log statements
- [ ] Remove unused dependencies
- [ ] Enable compression (gzip/brotli)
- [ ] Minimize bundle size
- [ ] Lazy load routes
- [ ] Optimize font loading

### Performance Metrics

**Target Metrics:**
- First Contentful Paint (FCP): < 1.5s
- Largest Contentful Paint (LCP): < 2.5s
- Time to Interactive (TTI): < 3.5s
- Cumulative Layout Shift (CLS): < 0.1
- First Input Delay (FID): < 100ms

**Tools to Measure:**
- Lighthouse (Chrome DevTools)
- WebPageTest
- GTmetrix

---

## Build for Production

### Step 1: Pre-Build Checklist

```bash
# 1. Update dependencies
npm update

# 2. Run security audit
npm audit fix

# 3. Remove unused dependencies
npm prune

# 4. Clear node_modules and reinstall (optional but recommended)
rm -rf node_modules package-lock.json
npm install
```

### Step 2: Environment Configuration

Create `.env.production`:
```bash
VITE_API_BASE_URL=https://api.yourproduction.com/api
VITE_APP_DOMAIN=yourproduction.com
VITE_APP_URL=https://yourproduction.com
```

### Step 3: Build

```bash
# Build for production
npm run build

# Output will be in dist/ folder
```

### Step 4: Test Production Build Locally

```bash
# Preview production build
npm run preview

# Visit http://localhost:4173
# Test critical flows
```

### Step 5: Verify Build

**Check these before deploying:**
- [ ] `dist/` folder created
- [ ] `index.html` exists
- [ ] Assets folder contains JS/CSS bundles
- [ ] Bundle sizes are reasonable (< 500KB for main)
- [ ] No errors during build
- [ ] No security vulnerabilities in packages
- [ ] Environment variables are correct

---

## Deployment Options

### Option 1: Vercel (Recommended)

**Pros:**
- Free tier available
- Automatic deployments from Git
- Built-in CI/CD
- Global CDN
- Environment variable support
- Custom domains

**Deployment Steps:**

```bash
# 1. Install Vercel CLI
npm install -g vercel

# 2. Login
vercel login

# 3. Initialize project
vercel

# 4. Deploy
vercel --prod
```

**Or using Git:**
1. Push code to GitHub
2. Connect repository to Vercel
3. Configure build settings:
   - Build Command: `npm run build`
   - Output Directory: `dist`
4. Add environment variables
5. Deploy

---

### Option 2: Netlify

**Pros:**
- Free tier available
- Easy drag-and-drop deployment
- Form handling (if needed)
- Custom domains
- SSL certificates

**Deployment Steps:**

```bash
# 1. Install Netlify CLI
npm install -g netlify-cli

# 2. Login
netlify login

# 3. Initialize
netlify init

# 4. Deploy
netlify deploy --prod
```

**netlify.toml configuration:**
```toml
[build]
  command = "npm run build"
  publish = "dist"

[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

---

### Option 3: AWS S3 + CloudFront

**Pros:**
- Highly scalable
- Very fast (CDN)
- Pay as you go

**Steps:**
1. Create S3 bucket
2. Enable static website hosting
3. Upload `dist/` folder
4. Create CloudFront distribution
5. Configure custom domain (Route 53)

---

### Option 4: Docker + Any VPS

**Dockerfile:**
```dockerfile
# Build stage
FROM node:18-alpine AS build

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# Production stage
FROM nginx:alpine

COPY --from=build /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

**nginx.conf:**
```nginx
server {
    listen 80;
    server_name _;
    root /usr/share/nginx/html;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Deploy:**
```bash
# Build image
docker build -t qr-menu-frontend .

# Run container
docker run -p 80:80 qr-menu-frontend
```

---

## Environment Configuration

### Environment Variables

**Required variables:**
```bash
# API Configuration
VITE_API_BASE_URL=https://api.yourproduction.com/api

# App Configuration
VITE_APP_DOMAIN=yourproduction.com
VITE_APP_URL=https://yourproduction.com
```

**Setting in different platforms:**

**Vercel:**
- Project Settings → Environment Variables
- Add each variable with production/preview scope

**Netlify:**
- Site Settings → Environment variables
- Add each variable

**Docker:**
```bash
docker run -p 80:80 \
  -e VITE_API_BASE_URL=https://api.yourproduction.com/api \
  -e VITE_APP_URL=https://yourproduction.com \
  qr-menu-frontend
```

---

## Post-Deployment Checklist

### Immediate Post-Deploy

- [ ] Site loads without errors
- [ ] All assets load correctly (no 404s)
- [ ] Can login with test account
- [ ] Can register new account
- [ ] Public menu loads
- [ ] Vendor dashboard loads
- [ ] Admin dashboard loads (if applicable)
- [ ] QR code generation works
- [ ] Image uploads work
- [ ] API requests succeed
- [ ] HTTPS is working
- [ ] Custom domain configured (if applicable)

### Performance Check

- [ ] Run Lighthouse audit (target: 90+ score)
- [ ] Check page load times (< 3 seconds)
- [ ] Verify bundle sizes are optimized
- [ ] Check CDN is serving assets
- [ ] Verify caching headers are set

### Security Check

- [ ] HTTPS enforced
- [ ] Security headers configured:
  - Content-Security-Policy
  - X-Frame-Options
  - X-Content-Type-Options
  - Referrer-Policy
- [ ] API endpoints using HTTPS
- [ ] No sensitive data in localStorage
- [ ] CORS configured correctly on backend

### Monitoring Setup

**Recommended Tools:**
- [ ] Google Analytics (optional)
- [ ] Sentry (error tracking)
- [ ] Uptime monitoring (UptimeRobot, etc.)
- [ ] Performance monitoring (Vercel Analytics, etc.)

---

## Continuous Deployment

### Git Workflow

```bash
# Development
git checkout -b feature/new-feature
# Make changes
git commit -m "feat: add new feature"
git push origin feature/new-feature

# Create PR → Review → Merge to main

# Main branch auto-deploys to production
```

### Branch Strategy

- `main` → Production
- `develop` → Staging/Preview
- `feature/*` → Feature branches (deploy previews)

---

## Rollback Strategy

### Vercel/Netlify

**Quick Rollback:**
1. Go to Deployments
2. Find previous working deployment
3. Click "Promote to Production"

### Docker

**Quick Rollback:**
```bash
# Tag your releases
docker tag qr-menu-frontend qr-menu-frontend:v1.0.0

# Rollback to previous version
docker run -p 80:80 qr-menu-frontend:v1.0.0
```

---

## Troubleshooting Common Issues

### Issue: Build fails

**Check:**
- Node version (should be 18+)
- Dependencies installed (`npm install`)
- Environment variables set
- Build logs for specific errors

### Issue: Blank page after deployment

**Check:**
- Browser console for errors
- Network tab for failed requests
- Base URL in router configuration
- Asset paths (should be relative)

### Issue: API requests failing

**Check:**
- CORS configuration on backend
- API base URL is correct
- HTTPS vs HTTP
- Network tab for request details

### Issue: Routes return 404

**Check:**
- Server configured for SPA (all routes → index.html)
- History mode vs hash mode in router
- Redirects/rewrites configured

---

## Final Pre-Launch Checklist

### Code Quality
- [ ] No console errors
- [ ] No console warnings
- [ ] No TypeScript errors (if using TS)
- [ ] Code is formatted consistently
- [ ] Comments removed or updated

### Functionality
- [ ] All features working
- [ ] All forms validated
- [ ] All error states handled
- [ ] All loading states implemented
- [ ] All success messages displayed

### Performance
- [ ] Lighthouse score > 90
- [ ] Bundle size optimized
- [ ] Images optimized
- [ ] Fonts optimized
- [ ] Lazy loading implemented

### SEO (Public Menu)
- [ ] Meta tags set
- [ ] Open Graph tags set
- [ ] Titles descriptive
- [ ] Descriptions unique
- [ ] Structured data (optional)

### Security
- [ ] No API keys in frontend code
- [ ] HTTPS enforced
- [ ] Security headers set
- [ ] XSS protection in place
- [ ] CSRF protection (if needed)

### Documentation
- [ ] README updated
- [ ] Environment variables documented
- [ ] Deployment process documented
- [ ] API documentation current

---

## Maintenance

### Regular Tasks

**Weekly:**
- [ ] Check error logs
- [ ] Review performance metrics
- [ ] Check uptime reports

**Monthly:**
- [ ] Update dependencies
- [ ] Run security audit
- [ ] Review and optimize performance
- [ ] Backup configuration

**Quarterly:**
- [ ] Review and update documentation
- [ ] Conduct security review
- [ ] Test disaster recovery
- [ ] Review analytics

---

**Last Updated:** 2025-11-09
**Status:** Complete deployment guide ready for production
