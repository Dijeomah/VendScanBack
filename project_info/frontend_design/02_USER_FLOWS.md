# User Flows & Journey Maps

## Overview

This document maps out the complete user journeys for all three user types in the QR Menu Management System based on the API endpoints analyzed.

---

## 1. Customer Flow (Public Menu Viewer)

### Primary Journey: Scanning QR Code to Viewing Menu

```
┌─────────────────────────────────────────────────────────────┐
│ CUSTOMER SCANS QR CODE AT RESTAURANT TABLE                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ QR Code redirects to: restaurant-name.yourdomain.com        │
│ API: GET http://{subdomain}.{domain}/                      │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ LOADING STATE (Skeleton UI)                                │
│ - Show vendor logo placeholder                              │
│ - Show category placeholders                                │
│ - Show item placeholders                                    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ MENU HOME PAGE                                              │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ [Vendor Logo] Restaurant Name           │                │
│ │ ⭐⭐⭐⭐⭐ (4.5) • $$ • Italian         │                │
│ ├────────────────────────────────────────┤                │
│ │ [Hero Image]                           │                │
│ ├────────────────────────────────────────┤                │
│ │ 🔍 Search menu...                      │                │
│ ├────────────────────────────────────────┤                │
│ │ Categories:                            │                │
│ │ [🍕 Pizza] [🍝 Pasta] [🥗 Salads]      │                │
│ ├────────────────────────────────────────┤                │
│ │ APPETIZERS                             │                │
│ │ ┌──────────────────────────────┐      │                │
│ │ │ [Image] Spring Rolls    $5.99│      │                │
│ │ │ Crispy vegetable rolls...    │      │                │
│ │ └──────────────────────────────┘      │                │
│ │ ┌──────────────────────────────┐      │                │
│ │ │ [Image] Bruschetta      $6.99│      │                │
│ │ │ Toasted bread with...        │      │                │
│ │ └──────────────────────────────┘      │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ USER INTERACTIONS:                                          │
│                                                             │
│ 1. Browse categories → Smooth scroll to section            │
│ 2. Search items → Filter list in real-time                 │
│ 3. Tap item → View details modal                          │
│ 4. Share menu → Social share buttons                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ ITEM DETAIL MODAL                                           │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ [Full-width Image]                     │                │
│ │                                        │                │
│ │ Margherita Pizza              $12.99   │                │
│ │ ⭐⭐⭐⭐⭐ 4.8 (124 reviews)            │                │
│ │                                        │                │
│ │ Fresh mozzarella, tomato sauce,        │                │
│ │ basil, extra virgin olive oil          │                │
│ │                                        │                │
│ │ 🌱 Vegetarian                          │                │
│ │ 🔥 500 cal                             │                │
│ │ ⏱️ 15 min prep time                    │                │
│ │                                        │                │
│ │ [Close] [Share]                        │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
```

### Customer Actions & States

| Action | API Call | Expected Behavior |
|--------|----------|------------------|
| Scan QR | `GET /{subdomain}` | Load vendor menu with SSR |
| Search item | Client-side filter | Instant filter of loaded menu |
| View item | Client-side modal | Show full item details |
| Share menu | Native share API | Share subdomain URL |

---

## 2. Vendor Flow (Menu Management)

### 2.1 First-Time Vendor Onboarding

```
┌─────────────────────────────────────────────────────────────┐
│ STEP 1: REGISTRATION                                        │
│ URL: /register                                              │
│ API: POST /api/auth/register                               │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ Create Your Account                    │                │
│ │                                        │                │
│ │ First Name:    [______________]        │                │
│ │ Last Name:     [______________]        │                │
│ │ Email:         [______________]        │                │
│ │ Phone:         [______________]        │                │
│ │ Password:      [______________]        │                │
│ │ Confirm:       [______________]        │                │
│ │ Role:          ● Vendor ○ Admin        │                │
│ │                                        │                │
│ │ [Continue →]                           │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 2: EMAIL VERIFICATION (Future)                         │
│ "Check your email for verification link"                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 3: BUSINESS INFORMATION                                │
│ URL: /onboarding/business                                   │
│ API: POST /api/vendor/business-info                        │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ Tell Us About Your Business            │                │
│ │                                        │                │
│ │ Business Name:     [______________]    │                │
│ │ Business Address:  [______________]    │                │
│ │                                        │                │
│ │ Country:   [Select Country ▼]         │                │
│ │ State:     [Select State ▼]           │                │
│ │ City:      [Select City ▼]            │                │
│ │                                        │                │
│ │ [← Back] [Continue →]                 │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 4: BUSINESS LINK (SUBDOMAIN)                           │
│ URL: /onboarding/subdomain                                  │
│ API: POST /api/vendor/business-links                       │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ Choose Your Menu URL                   │                │
│ │                                        │                │
│ │ Your business link:                    │                │
│ │ [italian-restaurant]                   │                │
│ │                                        │                │
│ │ Your menu will be available at:        │                │
│ │ 🔗 italian-restaurant.yourdomain.com   │                │
│ │                                        │                │
│ │ ✓ Available                            │                │
│ │                                        │                │
│ │ [← Back] [Continue →]                 │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ STEP 5: UPLOAD BRANDING                                     │
│ URL: /onboarding/branding                                   │
│ API: POST /api/vendor/media                                │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ Upload Your Branding                   │                │
│ │                                        │                │
│ │ Logo (Square, min 512x512px)           │                │
│ │ ┌──────────────┐                       │                │
│ │ │   [Upload]   │                       │                │
│ │ │   📷         │                       │                │
│ │ └──────────────┘                       │                │
│ │                                        │                │
│ │ Hero Image (16:9, min 1920x1080px)     │                │
│ │ ┌──────────────────────────┐           │                │
│ │ │   [Upload]               │           │                │
│ │ │   🖼️                     │           │                │
│ │ └──────────────────────────┘           │                │
│ │                                        │                │
│ │ [Skip] [← Back] [Finish Setup]        │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ WELCOME TO DASHBOARD                                        │
│ "Your account is set up! Let's create your first menu."    │
│                                                             │
│ [Create First Category]                                     │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Daily Vendor Workflow

```
┌─────────────────────────────────────────────────────────────┐
│ LOGIN                                                        │
│ URL: /login                                                 │
│ API: POST /api/auth/login                                  │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ Email:    [______________]             │                │
│ │ Password: [______________]             │                │
│ │ [x] Remember me                        │                │
│ │                                        │                │
│ │ [Login]           [Forgot Password?]   │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ VENDOR DASHBOARD                                            │
│ URL: /vendor/dashboard                                      │
│ API: GET /api/vendor/dashboard                             │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ ☰  QR Menu Manager      [👤 Profile ▼] │                │
│ ├────────────────────────────────────────┤                │
│ │                                        │                │
│ │ Welcome back, John! 👋                 │                │
│ │                                        │                │
│ │ Quick Stats:                           │                │
│ │ ┌──────┬──────┬──────┬──────┐         │                │
│ │ │ 156  │ 45   │ 12   │ 892  │         │                │
│ │ │ Items│ Active│ Cats │Views │         │                │
│ │ └──────┴──────┴──────┴──────┘         │                │
│ │                                        │                │
│ │ Your QR Code:                          │                │
│ │ ┌──────────┐                           │                │
│ │ │ [QR Code]│  [Download] [Print]       │                │
│ │ └──────────┘                           │                │
│ │                                        │                │
│ │ Menu Link:                             │                │
│ │ 🔗 italian-restaurant.domain.com [📋]  │                │
│ │                                        │                │
│ │ Quick Actions:                         │                │
│ │ [+ New Item] [Edit Menu] [View Live]   │                │
│ │                                        │                │
│ │ Recent Activity:                       │                │
│ │ • "Margherita Pizza" viewed 23 times   │                │
│ │ • "Caesar Salad" out of stock          │                │
│ └────────────────────────────────────────┘                │
│                                                             │
│ SIDEBAR:                                                    │
│ • 📊 Dashboard                                              │
│ • 🍽️  Menu Management                                      │
│ • 📁 Categories                                             │
│ • 🖼️  Media Library                                         │
│ • ⚙️  Settings                                              │
│ • 📈 Analytics                                              │
│ • 🚪 Logout                                                 │
└─────────────────────────────────────────────────────────────┘
```

### 2.3 Menu Management Flow

```
┌─────────────────────────────────────────────────────────────┐
│ MENU MANAGEMENT PAGE                                        │
│ URL: /vendor/menu                                           │
│ API: GET /api/vendor/full-profile                          │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ Menu Management                        │                │
│ │                                        │                │
│ │ [+ New Category] [+ New Item] [Search] │                │
│ │                                        │                │
│ │ ▼ APPETIZERS (8 items)      [Edit][Del]│                │
│ │   ┌──────────────────────────────────┐│                │
│ │   │ [🖼️] Spring Rolls       $5.99    ││                │
│ │   │      Status: ● Active  [Edit][×] ││                │
│ │   └──────────────────────────────────┘│                │
│ │   ┌──────────────────────────────────┐│                │
│ │   │ [🖼️] Bruschetta         $6.99    ││                │
│ │   │      Status: ○ Inactive [Edit][×]││                │
│ │   └──────────────────────────────────┘│                │
│ │   [...more items...]                  │                │
│ │                                        │                │
│ │ ▼ MAIN COURSES (15 items)   [Edit][Del]│                │
│ │   [...items...]                        │                │
│ │                                        │                │
│ │ ▼ DESSERTS (6 items)        [Edit][Del]│                │
│ │   [...items...]                        │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ CREATE/EDIT ITEM MODAL                                      │
│ API: POST /api/vendor/items (create)                       │
│      PUT /api/vendor/items/{id} (update)                   │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ Add New Menu Item                      │                │
│ │                                        │                │
│ │ Item Name*:                            │                │
│ │ [Margherita Pizza___________]          │                │
│ │                                        │                │
│ │ Description:                           │                │
│ │ [Fresh mozzarella, tomato sauce,       │                │
│ │  basil, extra virgin olive oil...]     │                │
│ │                                        │                │
│ │ Price*:                                │                │
│ │ $ [12.99]                              │                │
│ │                                        │                │
│ │ Category*:                             │                │
│ │ [Select Category ▼]                    │                │
│ │                                        │                │
│ │ Upload Image:                          │                │
│ │ ┌──────────────┐                       │                │
│ │ │ [Drag & Drop]│                       │                │
│ │ │ or [Browse]  │                       │                │
│ │ └──────────────┘                       │                │
│ │                                        │                │
│ │ Status:                                │                │
│ │ ● Active    ○ Inactive                 │                │
│ │                                        │                │
│ │ [Cancel] [Save Item]                   │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
```

---

## 3. Admin Flow (System Management)

### 3.1 Admin Dashboard

```
┌─────────────────────────────────────────────────────────────┐
│ ADMIN DASHBOARD                                             │
│ URL: /admin/dashboard                                       │
│ API: GET /api/admin/dashboard                              │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ ☰ Admin Panel      [🔍 Search] [👤▼]   │                │
│ ├────────────────────────────────────────┤                │
│ │                                        │                │
│ │ System Overview                        │                │
│ │                                        │                │
│ │ ┌──────┬──────┬──────┬──────┐         │                │
│ │ │ 1,234│ 567  │ 8,901│ 95%  │         │                │
│ │ │Vendors│Active│ Items│Uptime│         │                │
│ │ └──────┴──────┴──────┴──────┘         │                │
│ │                                        │                │
│ │ Recent Vendors:                        │                │
│ │ ┌────────────────────────────────────┐│                │
│ │ │ Joe's Pizza                         ││                │
│ │ │ joe-pizza.domain.com               ││                │
│ │ │ Joined: 2 days ago                 ││                │
│ │ │ [View] [Edit] [Delete]             ││                │
│ │ └────────────────────────────────────┘│                │
│ │                                        │                │
│ │ System Alerts:                         │                │
│ │ ⚠️ 3 vendors pending approval          │                │
│ │ ⚠️ 12 items flagged for review         │                │
│ └────────────────────────────────────────┘                │
│                                                             │
│ SIDEBAR:                                                    │
│ • 📊 Dashboard                                              │
│ • 👥 Vendors                                                │
│ • 🏢 Businesses                                             │
│ • 🔗 Business Links                                         │
│ • 📁 Categories                                             │
│ • 🍽️  Items                                                 │
│ • ⚙️  Settings                                              │
│ • 📈 Analytics                                              │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 Vendor Management

```
┌─────────────────────────────────────────────────────────────┐
│ VENDOR MANAGEMENT                                           │
│ URL: /admin/vendors                                         │
│ API: GET /api/admin/vendors                                │
│                                                             │
│ ┌────────────────────────────────────────┐                │
│ │ Vendors                                │                │
│ │                                        │                │
│ │ [+ New Vendor] [🔍 Search] [Filter ▼]  │                │
│ │                                        │                │
│ │ ┌────────────────────────────────────┐│                │
│ │ │ Name    │Email    │Business│Actions││                │
│ │ ├────────────────────────────────────┤│                │
│ │ │John Doe │john@... │Joe's   │[View] ││                │
│ │ │         │         │Pizza   │[Edit] ││                │
│ │ │         │         │        │[Del]  ││                │
│ │ │         │         │        │[QR]   ││                │
│ │ ├────────────────────────────────────┤│                │
│ │ │Jane S.  │jane@... │Sushi   │[...]  ││                │
│ │ │         │         │Bar     │       ││                │
│ │ └────────────────────────────────────┘│                │
│ │                                        │                │
│ │ Showing 1-10 of 1,234                  │                │
│ │ [← Prev] [1][2][3]...[124] [Next →]   │                │
│ └────────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────────┘
```

---

## Flow Diagrams Summary

### API Call Mapping

| User Action | Page | API Endpoint | Method | Auth Required |
|------------|------|--------------|--------|---------------|
| **Customer** |
| View menu | `/{subdomain}` | `/{subdomain}` | GET | No |
| Search items | Client-side | N/A | - | No |
| **Vendor** |
| Register | `/register` | `/api/auth/register` | POST | No |
| Login | `/login` | `/api/auth/login` | POST | No |
| Get dashboard | `/vendor/dashboard` | `/api/vendor/dashboard` | GET | Yes |
| Get full profile | `/vendor/menu` | `/api/vendor/full-profile` | GET | Yes |
| Set business info | `/onboarding` | `/api/vendor/business-info` | POST | Yes |
| Set business link | `/onboarding` | `/api/vendor/business-links` | POST | Yes |
| Upload media | `/onboarding` | `/api/vendor/media` | POST | Yes |
| Create category | `/vendor/menu` | `/api/vendor/categories` | POST | Yes |
| Create item | `/vendor/menu` | `/api/vendor/items` | POST | Yes |
| Update item | `/vendor/menu` | `/api/vendor/items/{id}` | PUT | Yes |
| Delete item | `/vendor/menu` | `/api/vendor/items/{id}` | DELETE | Yes |
| Get QR code | `/vendor/dashboard` | `/api/vendor/generate-qr` | POST | Yes |
| **Admin** |
| View dashboard | `/admin/dashboard` | `/api/admin/dashboard` | GET | Yes (admin) |
| List vendors | `/admin/vendors` | `/api/admin/vendors` | GET | Yes (admin) |
| Create vendor | `/admin/vendors` | `/api/admin/vendors` | POST | Yes (admin) |
| Update vendor | `/admin/vendors` | `/api/admin/vendors/{id}` | PUT | Yes (admin) |
| Delete vendor | `/admin/vendors` | `/api/admin/vendors/{id}` | DELETE | Yes (admin) |
| Generate QR | `/admin/vendors` | `/api/admin/vendors/{id}/generate-qr` | POST | Yes (admin) |

---

## Next Steps

These user flows will be implemented in the UI/UX design specifications (next document).
