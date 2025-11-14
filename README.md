# VendScan - QR Menu Management System

A comprehensive, full-stack QR code-based digital menu management system for restaurants, cafes, and food businesses. Built with Laravel 9 (backend) and Vue.js 3 (frontend).

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![Laravel](https://img.shields.io/badge/Laravel-9.x-red.svg)
![Vue](https://img.shields.io/badge/Vue-3.x-green.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [System Architecture](#-system-architecture)
- [Installation](#-installation)
- [User Roles](#-user-roles)
- [API Documentation](#-api-documentation)
- [Project Structure](#-project-structure)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### 🍽️ Core Features

#### For Customers
- ✅ **QR Code Menu Access** - Scan QR code to view restaurant menu instantly
- ✅ **Real-Time Order Tracking** - Monitor order status from confirmation to completion
  - Visual progress timeline (Received → Confirmed → Preparing → Served → Completed)
  - Auto-polling every 5 seconds for live updates
  - Smart polling that stops when order is completed
  - Status change notifications
- ✅ **Responsive Menu Display** - Beautiful menu interface with categories and images
- ✅ **Order Placement** - Easy ordering with notes and payment method selection
- ✅ **Order Confirmation** - Detailed order summary with receipt

#### For Vendors/Restaurants
- ✅ **Dashboard** - Comprehensive overview with statistics
  - Total orders, revenue, today's stats
  - Active orders tracking
  - Quick action buttons
- ✅ **Menu Management** - Complete CRUD operations for menu items
  - Create, read, update, delete menu items
  - Menu item images upload
  - Category and subcategory organization
  - Active/inactive status toggle
  - Price management
- ✅ **Business Profile Management**
  - Business information setup
  - Header/banner image upload
  - Logo upload
  - Business type configuration
- ✅ **Table Management** - QR code generation for each table
  - Create and manage tables
  - Generate unique QR codes
  - Download QR codes for printing
  - Table status tracking
- ✅ **Server Management** - Assign servers to tables
  - Create server accounts
  - Assign servers to specific tables
  - Track server performance
- ✅ **Order Management** - View and manage customer orders
  - Filter by status and payment status
  - Update order status
  - View order details and history
  - Order timeline tracking
- ✅ **Category Management** - Organize menu items
  - Create categories and subcategories
  - Reorder categories
  - Category-based filtering
- ✅ **Notifications** - Real-time notifications for new orders
  - Order status updates
  - Mark as read/unread
  - Notification count badge

#### For Servers
- ✅ **Server Dashboard** - Dedicated dashboard for servers
  - View assigned tables and businesses
  - Statistics for orders served
  - Quick access to active orders
- ✅ **Order Management** - Manage orders for assigned tables
  - View orders by table
  - Update order status (confirmed, preparing, served)
  - Filter orders by status
  - Order details view
- ✅ **Notifications** - Get notified when assigned tables place orders
  - Real-time order notifications
  - Table-specific alerts
  - Quick action links

#### For Admins
- ✅ **Admin Dashboard** - Platform-wide overview
  - Total vendors, businesses, orders, revenue
  - Growth trends (7-day charts)
  - Top vendors by revenue
  - Platform revenue over time
  - Business type distribution
  - Table and server statistics
- ✅ **Vendor Management** - Complete vendor CRUD
  - Create, view, edit vendors
  - View vendor statistics
  - Monitor vendor performance
- ✅ **Table Management** - Oversee all tables
  - View all tables across all businesses
  - Table status overview
  - QR code management
- ✅ **Server Management** - Manage all servers
  - View all servers across platform
  - Server assignments
  - Server performance metrics
- ✅ **Order Management** - Platform-wide order tracking
  - View all orders
  - Filter by vendor, status, date
  - Order statistics and analytics
- ✅ **Reports & Analytics** - Comprehensive reporting
  - Revenue over time charts
  - Orders by status
  - Top performing items
  - Vendor performance metrics
  - Date range filtering
  - Export functionality (planned)
- ✅ **Settings** - Platform configuration
  - General settings (platform name, contact info)
  - Payment settings (currency, tax rate, commission)
  - Notification preferences
  - Security settings (password policy, session timeout)

### 🔐 Authentication & Authorization
- ✅ JWT-based authentication
- ✅ Role-based access control (Admin, Vendor, Server)
- ✅ Protected routes with navigation guards
- ✅ Token refresh mechanism
- ✅ Session management

### 🎨 User Experience
- ✅ **Responsive Design** - Works on all devices (mobile, tablet, desktop)
- ✅ **Loading States** - Smooth loading indicators
- ✅ **Error Handling** - User-friendly error messages
- ✅ **Toast Notifications** - Real-time feedback
- ✅ **Form Validation** - Client-side and server-side validation
- ✅ **Dark Mode Ready** - Prepared for dark mode implementation

### 🔔 Notification System
- ✅ Real-time notifications for vendors
- ✅ Real-time notifications for servers
- ✅ Order status change notifications
- ✅ New order alerts
- ✅ Mark as read/unread functionality
- ✅ Notification count badges

### 📊 Analytics & Reporting
- ✅ Revenue tracking and charts
- ✅ Order statistics
- ✅ Top performing items
- ✅ Vendor performance metrics
- ✅ Growth trend analysis
- ✅ Date range filtering

### 🖼️ Media Management
- ✅ **Cloudinary Integration** - Cloud-based image storage
- ✅ Business header/banner image upload
- ✅ Business logo upload
- ✅ Menu item image upload
- ✅ Image preview functionality
- ✅ Automatic image optimization

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 9.x
- **Language**: PHP 8.1+
- **Database**: MySQL 8.0
- **Authentication**: JWT (tymon/jwt-auth)
- **API**: RESTful API architecture
- **QR Code**: SimpleSoftwareIO QR Code
- **Image Storage**: Cloudinary
- **Testing**: PHPUnit

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **Build Tool**: Vite 5.x
- **State Management**: Pinia 2.x
- **Routing**: Vue Router 4.x
- **HTTP Client**: Axios
- **UI Framework**: Tailwind CSS 3.x
- **Charts**: Chart.js 4.x
- **Notifications**: Vue Toastification
- **Icons**: Heroicons
- **QR Display**: qrcode.vue

### DevOps & Tools
- **Version Control**: Git
- **Package Managers**: Composer (PHP), npm (JavaScript)
- **Development**: Docker (optional)

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Client Layer                         │
│  (Vue.js 3 SPA - Mobile & Desktop Responsive)              │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ HTTP/REST API (Axios)
                     │
┌────────────────────▼────────────────────────────────────────┐
│                    API Layer (Laravel)                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Controllers (Auth, Vendor, Admin, Server, Public)   │  │
│  └─────────────────────┬────────────────────────────────┘  │
│  ┌─────────────────────▼────────────────────────────────┐  │
│  │  Middleware (Auth, CORS, Role Check)                 │  │
│  └─────────────────────┬────────────────────────────────┘  │
│  ┌─────────────────────▼────────────────────────────────┐  │
│  │  Business Logic (Models, Services)                   │  │
│  └─────────────────────┬────────────────────────────────┘  │
└────────────────────────┼────────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                │                │
┌───────▼────────┐ ┌────▼─────┐ ┌────────▼────────┐
│  MySQL Database │ │ Cloudinary│ │  File Storage   │
│  (Relational DB)│ │  (Images) │ │  (QR Codes)     │
└────────────────┘ └───────────┘ └─────────────────┘
```

### Database Schema Overview

```
Users (Admin, Vendor, Server)
  ├─ BusinessLinks (Vendor's businesses)
  │    ├─ BusinessData (Business details, media)
  │    ├─ Categories
  │    │    └─ SubCategories
  │    │         └─ Items (Menu items with images)
  │    ├─ TableLinkQrData (Tables with QR codes)
  │    └─ Servers (Server assignments)
  │
  └─ Orders
       ├─ OrderItems
       ├─ Payments
       └─ Notifications
```

---

## 🚀 Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+
- Git

### Backend Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/VendScanBack.git
   cd VendScanBack
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

4. **Configure database in `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=vendscan
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Configure Cloudinary in `.env`:**
   ```env
   CLOUDINARY_CLOUD_NAME=your_cloud_name
   CLOUDINARY_API_KEY=your_api_key
   CLOUDINARY_API_SECRET=your_api_secret
   ```

6. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

7. **Generate JWT secret:**
   ```bash
   php artisan jwt:secret
   ```

8. **Run migrations:**
   ```bash
   php artisan migrate
   ```

9. **Seed database (optional):**
   ```bash
   php artisan db:seed
   ```

10. **Start the development server:**
    ```bash
    php artisan serve
    ```
    Backend will run on `http://localhost:8000`

### Frontend Setup

1. **Navigate to frontend directory:**
   ```bash
   cd frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Configure environment (`.env`):**
   ```env
   VITE_API_BASE_URL=http://localhost:8000/api
   VITE_APP_URL=http://localhost:3000
   ```

4. **Start development server:**
   ```bash
   npm run dev
   ```
   Frontend will run on `http://localhost:3000`

5. **Build for production:**
   ```bash
   npm run build
   ```

---

## 👥 User Roles

### 1. Customer (Public Access)
- No authentication required
- Can scan QR codes
- View menus
- Place orders
- Track orders in real-time

### 2. Server
**Credentials**: Created by vendor
**Access**: Server Dashboard (`/server/dashboard`)

**Permissions**:
- View assigned tables and businesses
- View and update orders for assigned tables
- Receive real-time notifications for new orders
- Update order status (confirmed, preparing, served)

**Key Routes**:
- `/server/dashboard` - Server dashboard
- `/server/orders` - Manage orders

### 3. Vendor (Restaurant Owner)
**Credentials**: Register at `/register` with role `vendor`
**Access**: Vendor Dashboard (`/vendor/dashboard`)

**Permissions**:
- Manage business profile and media
- Create and manage menu items
- Manage categories
- Create and manage tables with QR codes
- Create and manage servers
- View and manage orders
- Receive real-time notifications

**Key Routes**:
- `/vendor/dashboard` - Overview and statistics
- `/vendor/menu` - Menu management
- `/vendor/items/create` - Create menu item
- `/vendor/items/:id/edit` - Edit menu item
- `/vendor/categories` - Category management
- `/vendor/tables` - Table management
- `/vendor/servers` - Server management
- `/vendor/orders` - Order management
- `/vendor/business` - Business profile

### 4. Admin (Platform Administrator)
**Credentials**: Created via database seeder or direct DB insert
**Access**: Admin Dashboard (`/admin/dashboard`)

**Permissions**:
- Full system access
- Manage all vendors
- View all businesses, tables, servers
- Platform-wide analytics
- System settings
- View all orders and transactions

**Key Routes**:
- `/admin/dashboard` - Platform overview
- `/admin/vendors` - Vendor management
- `/admin/tables` - Table management
- `/admin/servers` - Server management
- `/admin/orders` - Order management
- `/admin/reports` - Reports & analytics
- `/admin/settings` - System settings

---

## 📡 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication
All authenticated endpoints require a JWT token in the Authorization header:
```
Authorization: Bearer {your-jwt-token}
```

### Endpoint Categories

#### Auth Endpoints
```
POST   /auth/login              - User login
POST   /auth/register           - User registration
POST   /auth/logout             - Logout
POST   /auth/refresh            - Refresh token
GET    /auth/user              - Get authenticated user
```

#### Vendor Endpoints
```
GET    /vendor/dashboard                    - Dashboard statistics
GET    /vendor/profile                      - Full profile
POST   /vendor/business                     - Update business info
POST   /vendor/business/media              - Upload business images
GET    /vendor/items                        - List menu items
POST   /vendor/items                        - Create menu item
GET    /vendor/items/{id}                  - Get menu item
PUT    /vendor/items/{id}                  - Update menu item
DELETE /vendor/items/{id}                  - Delete menu item
GET    /vendor/categories                   - List categories
POST   /vendor/categories                   - Create category
GET    /vendor/tables                       - List tables
POST   /vendor/tables                       - Create table
GET    /vendor/servers                      - List servers
POST   /vendor/servers                      - Create server
GET    /vendor/orders                       - List orders
GET    /vendor/orders/{id}                 - Get order details
PATCH  /vendor/orders/{id}/status          - Update order status
GET    /vendor/notifications               - List notifications
PATCH  /vendor/notifications/{id}/read     - Mark as read
```

#### Server Endpoints
```
GET    /server/dashboard/statistics        - Dashboard statistics
GET    /server/assigned-tables             - Get assigned tables
GET    /server/assigned-businesses         - Get assigned businesses
GET    /server/orders                      - List orders
GET    /server/orders/{id}                - Get order details
PATCH  /server/orders/{id}/status         - Update order status
GET    /server/notifications              - List notifications
```

#### Admin Endpoints
```
GET    /admin/dashboard                    - Platform statistics
GET    /admin/vendors                      - List all vendors
POST   /admin/vendors                      - Create vendor
GET    /admin/vendors/{id}                - Get vendor
PUT    /admin/vendors/{id}                - Update vendor
DELETE /admin/vendors/{id}                - Delete vendor
GET    /admin/orders                       - List all orders
GET    /admin/orders/statistics           - Order statistics
GET    /admin/tables                       - List all tables
GET    /admin/servers                      - List all servers
```

#### Public Endpoints
```
GET    /public/menu/{businessLink}        - Get menu by business link
POST   /public/orders                      - Place order
GET    /public/orders/{orderNumber}       - Get order status
```

### Response Format

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors (if any)
  }
}
```

---

## 📁 Project Structure

```
VendScanBack/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── Base/
│   │   │   │   │   └── AdminController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   └── VendorController.php
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php
│   │   │   ├── Server/
│   │   │   │   └── ServerController.php
│   │   │   ├── Vendor/
│   │   │   │   ├── VendorController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── ItemController.php
│   │   │   │   ├── TableController.php
│   │   │   │   ├── ServerManagementController.php
│   │   │   │   └── OrderController.php
│   │   │   └── Public/
│   │   │       └── PublicController.php
│   │   └── Middleware/
│   │       ├── Authenticate.php
│   │       └── CheckRole.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── BusinessLink.php
│   │   ├── BusinessData.php
│   │   ├── Category.php
│   │   ├── SubCategory.php
│   │   ├── Item.php
│   │   ├── TableLinkQrData.php
│   │   ├── Server.php
│   │   ├── ServerTableAssignment.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Payment.php
│   │   └── Notification.php
│   └── Helpers/
│       └── helpers.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   │   ├── common/
│   │   │   ├── layouts/
│   │   │   └── NotificationDropdown.vue
│   │   ├── composables/
│   │   │   └── useApi.js
│   │   ├── router/
│   │   │   └── index.js
│   │   ├── stores/
│   │   │   ├── auth.js
│   │   │   └── vendor.js
│   │   ├── views/
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   ├── admin/
│   │   │   │   ├── Dashboard.vue
│   │   │   │   ├── VendorList.vue
│   │   │   │   ├── Tables.vue
│   │   │   │   ├── Servers.vue
│   │   │   │   ├── Orders.vue
│   │   │   │   ├── Reports.vue
│   │   │   │   └── Settings.vue
│   │   │   ├── vendor/
│   │   │   │   ├── Dashboard.vue
│   │   │   │   ├── MenuManagement.vue
│   │   │   │   ├── ItemCreate.vue
│   │   │   │   ├── ItemEdit.vue
│   │   │   │   ├── CategoryManagement.vue
│   │   │   │   ├── TableManagement.vue
│   │   │   │   ├── ServerManagement.vue
│   │   │   │   ├── Orders.vue
│   │   │   │   └── BusinessManagement.vue
│   │   │   ├── server/
│   │   │   │   ├── Dashboard.vue
│   │   │   │   └── Orders.vue
│   │   │   └── public/
│   │   │       ├── Menu.vue
│   │   │       ├── Checkout.vue
│   │   │       └── OrderConfirmation.vue
│   │   ├── App.vue
│   │   └── main.js
│   ├── package.json
│   ├── vite.config.js
│   └── README.md
├── .env.example
├── composer.json
├── package.json
└── README.md (this file)
```

---

## 📸 Screenshots

### Customer Experience
- **QR Code Menu** - Scan and view menu instantly
- **Order Tracking** - Real-time order status with visual timeline
- **Order Confirmation** - Complete order details and receipt

### Vendor Dashboard
- **Dashboard Overview** - Statistics and quick actions
- **Menu Management** - Grid view with item images
- **Business Profile** - Header image, logo, and business info
- **Table Management** - QR code generation and management
- **Server Management** - Create and assign servers
- **Order Management** - Filter and update orders

### Server Dashboard
- **Server Dashboard** - View assigned tables and stats
- **Order Management** - Manage orders for assigned tables
- **Real-time Notifications** - Get notified of new orders

### Admin Dashboard
- **Platform Overview** - Comprehensive statistics and charts
- **Vendor Management** - Complete vendor CRUD
- **Reports & Analytics** - Revenue charts and performance metrics
- **Settings** - Platform configuration

---

## 🎯 Key Features in Detail

### Real-Time Order Tracking
The order confirmation page features a sophisticated real-time tracking system:

```javascript
// Auto-polling every 5 seconds
pollingInterval = setInterval(() => {
  loadOrder(false) // Updates without showing loading state
}, 5000)

// Smart polling that stops when order is completed
if (order.status === 'completed' || order.status === 'cancelled') {
  clearInterval(pollingInterval)
}
```

**Visual Progress Timeline:**
- 5 distinct stages with visual indicators
- Completed steps show with green checkmarks
- Current step highlighted with pulsing ring
- Pending steps shown in gray

### Business Media Management
Vendors can upload:
- **Header/Banner Image** - Full-width hero image (1920x400px recommended)
- **Logo Image** - Square logo (512x512px recommended)

All images are:
- Uploaded to Cloudinary for optimal delivery
- Automatically optimized
- Cached for fast loading

### Server Assignment System
Vendors can:
1. Create server accounts
2. Assign servers to specific tables
3. Servers only see orders for their assigned tables
4. Servers receive real-time notifications

### Notification System
Real-time notifications for:
- New orders (for vendors and servers)
- Order status changes (for customers)
- Table assignments (for servers)

Features:
- Notification badges with count
- Dropdown menu with recent notifications
- Mark as read/unread
- Direct links to relevant pages

---

## 🚧 Roadmap

### Phase 1: Current (Completed ✅)
- [x] User authentication and authorization
- [x] Vendor dashboard and menu management
- [x] Server dashboard and order management
- [x] Admin dashboard with analytics
- [x] QR code generation
- [x] Real-time order tracking
- [x] Business media upload
- [x] Notification system

### Phase 2: Enhancements (Planned)
- [ ] Payment gateway integration (Stripe/PayPal)
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Customer loyalty program
- [ ] Multi-language support
- [ ] Dark mode
- [ ] PWA features (offline mode)

### Phase 3: Advanced Features
- [ ] Inventory management
- [ ] Staff attendance tracking
- [ ] Advanced analytics (predictive insights)
- [ ] Mobile apps (iOS & Android)
- [ ] Kitchen display system
- [ ] Table reservation system

---

## 🧪 Testing

### Backend Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage
```

### Frontend Testing
```bash
# Run unit tests (when implemented)
npm run test

# Run E2E tests (when implemented)
npm run test:e2e
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 for PHP code
- Use Vue 3 Composition API
- Follow Tailwind CSS conventions
- Write meaningful commit messages
- Add tests for new features

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Authors

- **Your Name** - *Initial work* - [YourGitHub](https://github.com/yourusername)

---

## 🙏 Acknowledgments

- Laravel community for the amazing framework
- Vue.js team for the progressive framework
- Tailwind CSS for the utility-first CSS framework
- All open-source contributors

---

## 📞 Support

For support, email support@vendscan.com or open an issue in the repository.

---

## 🔗 Links

- [Frontend Documentation](./frontend/README.md)
- [API Documentation](#-api-documentation)
- [Project Issues](https://github.com/yourusername/VendScanBack/issues)
- [Project Review](./frontend/issues/PROJECT_REVIEW.md)

---

**Status**: 🚀 Production Ready

**Last Updated**: 2025-11-14

**Version**: 1.0.0

---

Made with ❤️ for restaurants and food businesses
