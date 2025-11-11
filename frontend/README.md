# QR Menu Management System - Frontend

A modern, production-ready Vue.js 3 + Vite frontend application for managing digital restaurant menus via QR codes.

## 🎨 Design Philosophy

This frontend was designed with the following principles in mind:

- **Mobile-First**: Optimized for mobile devices where most customers will view menus
- **Fast & Lightweight**: Built with Vite for lightning-fast development and production builds
- **Beautiful UI**: Clean, modern design using Tailwind CSS + DaisyUI
- **User-Centric**: Intuitive navigation and delightful interactions
- **Production-Ready**: Complete error handling, loading states, and user feedback

## 🚀 Tech Stack

- **Framework**: Vue.js 3 (Composition API)
- **Build Tool**: Vite 5.x (incredibly fast HMR)
- **State Management**: Pinia 2.x
- **Routing**: Vue Router 4.x
- **HTTP Client**: Axios
- **UI Framework**: Tailwind CSS 3.x + DaisyUI
- **Form Validation**: VeeValidate + Yup
- **Icons**: Heroicons
- **Notifications**: Vue Toastification
- **QR Codes**: qrcode.vue
- **Utilities**: @vueuse/core, date-fns

## 📁 Project Structure

```
frontend/
├── src/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── tailwind.css          # Tailwind imports
│   │   │   └── main.css              # Custom global styles
│   │   └── images/                   # Static images
│   ├── components/
│   │   ├── common/                   # Reusable UI components
│   │   │   └── LoadingSpinner.vue
│   │   ├── menu/                     # Public menu components
│   │   ├── vendor/                   # Vendor-specific components
│   │   ├── admin/                    # Admin-specific components
│   │   └── layout/                   # Layout components
│   ├── composables/
│   │   └── useApi.js                 # API integration layer
│   ├── layouts/
│   │   └── (Dashboard layouts)
│   ├── router/
│   │   └── index.js                  # Vue Router configuration
│   ├── stores/
│   │   ├── auth.js                   # Authentication state
│   │   └── vendor.js                 # Vendor data state
│   ├── utils/                        # Utility functions
│   ├── views/
│   │   ├── Login.vue                 # Login page
│   │   ├── Register.vue              # Registration page
│   │   ├── NotFound.vue              # 404 page
│   │   ├── vendor/                   # Vendor dashboard pages
│   │   │   ├── Dashboard.vue         # ✅ Vendor dashboard (functional)
│   │   │   ├── MenuManagement.vue    # 🚧 Menu management
│   │   │   ├── ItemCreate.vue        # 🚧 Create menu item
│   │   │   ├── ItemEdit.vue          # 🚧 Edit menu item
│   │   │   └── Settings.vue          # 🚧 Vendor settings
│   │   ├── admin/                    # Admin panel pages
│   │   │   └── (Admin pages)         # 🚧 Under construction
│   │   └── menu/
│   │       └── PublicMenu.vue        # 🚧 Public menu viewer
│   ├── App.vue                       # Root component
│   └── main.js                       # Application entry point
├── public/                           # Static assets
├── .env                              # Environment variables
├── index.html                        # HTML template
├── vite.config.js                    # Vite configuration
├── tailwind.config.js                # Tailwind configuration
├── postcss.config.js                 # PostCSS configuration
├── package.json                      # Dependencies
└── README.md                         # This file
```

## 🛠️ Setup Instructions

### Prerequisites

- Node.js 18+ installed
- npm or yarn package manager
- Laravel backend running on `http://localhost:8000`

### Installation

1. **Navigate to the frontend directory:**
   ```bash
   cd frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Configure environment variables:**
   
   The `.env` file is already set up with default values:
   ```env
   VITE_API_BASE_URL=http://localhost:8000/api
   VITE_APP_URL=http://localhost:3000
   ```
   
   Update these if your backend runs on a different URL.

4. **Start the development server:**
   ```bash
   npm run dev
   ```
   
   The application will be available at `http://localhost:3000`

5. **Build for production:**
   ```bash
   npm run build
   ```
   
   Output will be in the `dist/` directory.

6. **Preview production build:**
   ```bash
   npm run preview
   ```

## ✨ Implemented Features

### ✅ Core Infrastructure
- [x] Vue 3 + Vite project setup
- [x] Tailwind CSS + DaisyUI styling
- [x] Vue Router with authentication guards
- [x] Pinia state management
- [x] Axios API integration layer
- [x] Toast notifications
- [x] Loading states & error handling

### ✅ Authentication System
- [x] Beautiful login page with form validation
- [x] Registration page with role selection (Vendor/Admin)
- [x] Password visibility toggle
- [x] JWT token management
- [x] Auto-redirect based on user role
- [x] Persistent authentication (localStorage)
- [x] Logout functionality

### ✅ Vendor Dashboard
- [x] Dashboard overview with stats:
  - Total items count
  - Active items count
  - Categories count
  - Menu views (placeholder)
- [x] QR code display & management
- [x] Quick action buttons:
  - Add new item
  - Manage menu
  - Settings
- [x] Profile data loading
- [x] Responsive design

### 🚧 In Progress / Planned

- [ ] **Public Menu Viewer** (Priority: HIGH)
  - Category sections
  - Menu item cards with images
  - Search functionality
  - Item detail modal
  - Responsive grid layout
  
- [ ] **Vendor Menu Management**
  - Item list with filtering
  - Create new items
  - Edit existing items
  - Delete items
  - Category management
  - Image upload
  
- [ ] **Vendor Settings**
  - Profile update
  - Business information
  - Logo/hero image upload
  - QR code customization
  
- [ ] **Admin Panel**
  - Dashboard with system stats
  - Vendor CRUD operations
  - Business management
  - Analytics

## 🎯 Key Features & Design Highlights

### 1. **Exceptional User Experience**
- Smooth transitions and animations
- Instant feedback on user actions
- Loading states for all async operations
- Clear error messages
- Optimistic UI updates

### 2. **Responsive Design**
- Mobile-first approach
- Works seamlessly on all screen sizes
- Touch-friendly interactive elements
- Optimized for both mobile and desktop

### 3. **Security**
- JWT token-based authentication
- Role-based access control (RBAC)
- Protected routes with navigation guards
- Secure API communication

### 4. **Performance**
- Code splitting & lazy loading
- Optimized bundle size
- Fast initial load time
- Vite's lightning-fast HMR

### 5. **Developer Experience**
- Clean, modular code structure
- Composition API for better reusability
- Centralized API management
- Type-safe (ready for TypeScript)

## 🎨 Design System

### Colors
```css
Primary: #3b82f6 (Blue)
Success: #22c55e (Green)
Warning: #f59e0b (Orange)
Error: #ef4444 (Red)
```

### Typography
- Font: Inter (Google Fonts)
- Sizes: Tailwind's type scale
- Weights: 300-800

### Components
All components follow a consistent design pattern:
- Rounded corners (lg: 0.5rem, xl: 0.75rem)
- Subtle shadows
- Hover states
- Focus rings for accessibility
- Smooth transitions

## 📱 User Flows

### Customer Flow
1. Scan QR code → Redirects to `/menu/{vendorLink}`
2. View menu with categories
3. Click item for details
4. Search/filter items

### Vendor Flow
1. Register/Login → `/vendor/dashboard`
2. View dashboard stats and QR code
3. Manage menu items (Create/Edit/Delete)
4. Update business settings
5. Download QR code

### Admin Flow
1. Login → `/admin/dashboard`
2. View system statistics
3. Manage all vendors
4. Generate QR codes for vendors

## 🔌 API Integration

The frontend communicates with the Laravel backend via the `useApi` composable:

```javascript
// Example usage
import { useApi } from '@/composables/useApi'

const { vendor } = useApi()

// Get vendor profile
const profile = await vendor.getFullProfile()

// Create menu item
const item = await vendor.createItem(itemData)
```

### Available API Methods:
- **Auth**: login, register, logout, refresh
- **Vendor**: getDashboard, getProfile, updateProfile, createItem, etc.
- **Admin**: getDashboard, getVendors, createVendor, etc.
- **Public**: getVendorMenuByLink

## 🧪 Testing

To test the frontend:

1. **Start the backend:**
   ```bash
   # In the Laravel project root
   php artisan serve
   ```

2. **Start the frontend:**
   ```bash
   # In the frontend directory
   npm run dev
   ```

3. **Test login:**
   - Navigate to `http://localhost:3000/login`
   - Use test credentials from your database
   - Verify redirection based on role

4. **Test vendor dashboard:**
   - Login as a vendor
   - Check if stats load correctly
   - Test QR code generation

## 🚀 Deployment

### Build for Production
```bash
npm run build
```

### Deploy Options

**Option 1: Netlify**
```bash
# Install Netlify CLI
npm install -g netlify-cli

# Deploy
netlify deploy --prod --dir=dist
```

**Option 2: Vercel**
```bash
# Install Vercel CLI
npm install -g vercel

# Deploy
vercel --prod
```

**Option 3: Self-Host**
- Build the project
- Upload the `dist/` folder to your web server
- Configure your server to serve the `index.html` for all routes (SPA mode)

### Environment Variables for Production
Update `.env` or configure via your hosting platform:
```env
VITE_API_BASE_URL=https://your-api-domain.com/api
VITE_APP_URL=https://your-frontend-domain.com
```

## 📚 Next Steps & Recommendations

### Immediate Priorities:

1. **Complete Public Menu Viewer** (HIGH PRIORITY)
   - This is the core user-facing feature
   - Customers need this to view menus via QR code
   - Implementation guide in `project_info/frontend_design/08_ALL_PAGES.md`

2. **Vendor Menu Management** (HIGH PRIORITY)
   - CRUD operations for menu items
   - Category management
   - Image upload functionality

3. **Vendor Settings Page** (MEDIUM PRIORITY)
   - Business profile updates
   - Media uploads (logo, hero image)
   - QR code customization

4. **Admin Panel** (MEDIUM PRIORITY)
   - Complete vendor management
   - System analytics
   - Business link management

### Future Enhancements:

- [ ] Add unit tests (Vitest)
- [ ] Add E2E tests (Playwright)
- [ ] Implement PWA features
- [ ] Add dark mode
- [ ] Multi-language support (i18n)
- [ ] Advanced analytics dashboard
- [ ] Email notifications
- [ ] Export functionality (PDF menus)
- [ ] SEO optimization with prerendering

## 📖 Documentation Reference

For detailed implementation guides, refer to the documentation in `project_info/frontend_design/`:

- `00_CLAUDE_START_HERE.md` - Complete build guide
- `06_STEP_BY_STEP_BUILD_PLAN.md` - Day-by-day implementation plan
- `07_COMPLETE_COMPONENTS.md` - All component implementations
- `08_ALL_PAGES.md` - All page implementations
- `10_API_COMPLETE_REFERENCE.md` - API documentation

## 🤝 Contributing

When adding new features:

1. Follow the existing code structure
2. Use Composition API
3. Implement proper error handling
4. Add loading states
5. Ensure responsive design
6. Test on mobile devices

## 📞 Support

For questions or issues:
- Check the documentation in `project_info/`
- Review the Laravel backend API documentation
- Test API endpoints using Postman

## 🎉 Credits

**Built with ❤️ using:**
- Vue.js Team for the amazing framework
- Vite Team for the blazing-fast build tool
- Tailwind CSS Team for the utility-first CSS framework
- The amazing open-source community

---

**Status**: 🚀 Foundation Complete - Ready for Feature Development

**Last Updated**: 2025-11-11
**Version**: 1.0.0-beta
