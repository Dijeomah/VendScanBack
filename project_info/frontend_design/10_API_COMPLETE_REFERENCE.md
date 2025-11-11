# Complete API Reference for Frontend

## 📡 API Verification & Documentation

This document maps **EVERY Laravel API endpoint** to frontend usage. Use this to verify your API calls are correct.

**Laravel Backend:** `http://localhost:8000/api`

---

## Authentication Endpoints

### POST `/api/auth/register`
**Purpose:** Register new user

**Frontend Usage:**
```javascript
const { auth } = useApi()
const response = await auth.register({
  first_name: "John",
  last_name: "Doe",
  email: "john@example.com",
  password: "password123",
  password_confirmation: "password123",
  role: "vendor", // or "admin"
  phone_number: "1234567890"
})
```

**Expected Response (201):**
```json
{
  "message": "User successfully registered",
  "user": {
    "id": 1,
    "userid": "UNIQUE_ID",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "role": "vendor",
    "phone_number": "1234567890",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

---

### POST `/api/auth/login`
**Purpose:** User login

**Frontend Usage:**
```javascript
const { auth } = useApi()
const response = await auth.login({
  email: "john@example.com",
  password: "password123"
})
```

**Expected Response (200):**
```json
{
  "access_token": "JWT_TOKEN_HERE",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "userid": "UNIQUE_ID",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "role": "vendor",
    "subdomain": "vendor-subdomain"
  }
}
```

**What to do after login:**
```javascript
// Store in Pinia
authStore.setAuth(response.data.access_token, response.data.user)

// Redirect based on role
if (user.role === 'admin') router.push('/admin/dashboard')
else if (user.role === 'vendor') router.push('/vendor/dashboard')
```

---

### POST `/api/auth/logout`
**Purpose:** User logout
**Headers:** `Authorization: Bearer {token}`

**Frontend Usage:**
```javascript
const { auth } = useApi()
await auth.logout()
authStore.clearAuth()
router.push('/login')
```

**Expected Response (200):**
```json
{
  "message": "Successfully logged out"
}
```

---

### POST `/api/auth/refresh`
**Purpose:** Refresh JWT token
**Headers:** `Authorization: Bearer {expired_token}`

**Frontend Usage:**
```javascript
const { auth } = useApi()
const response = await auth.refresh()
authStore.setAuth(response.data.access_token, response.data.user)
```

**Expected Response (200):**
```json
{
  "access_token": "NEW_JWT_TOKEN",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": { /* user object */ }
}
```

---

### GET `/api/auth/countries`
**Purpose:** Get all countries

**Frontend Usage:**
```javascript
const { auth } = useApi()
const response = await auth.getCountries()
const countries = response.data // Array of countries
```

**Expected Response (200):**
```json
[
  { "id": 1, "name": "United States" },
  { "id": 2, "name": "Canada" }
]
```

---

### GET `/api/auth/states/{country_id}`
**Purpose:** Get states by country

**Frontend Usage:**
```javascript
const { auth } = useApi()
const response = await auth.getStates(1) // country_id = 1
const states = response.data
```

**Expected Response (200):**
```json
[
  { "id": 1, "name": "California", "country_id": 1 },
  { "id": 2, "name": "New York", "country_id": 1 }
]
```

---

### GET `/api/auth/cities/{state_id}`
**Purpose:** Get cities by state

**Frontend Usage:**
```javascript
const { auth } = useApi()
const response = await auth.getCities(1) // state_id = 1
const cities = response.data
```

**Expected Response (200):**
```json
[
  { "id": 1, "name": "Los Angeles", "state_id": 1 },
  { "id": 2, "name": "San Francisco", "state_id": 1 }
]
```

---

## Vendor Endpoints

### GET `/api/vendor/dashboard`
**Purpose:** Get vendor dashboard data
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.getDashboard()
```

**Expected Response (200):**
```json
{
  "message": "Vendor Dashboard Data"
}
```

---

### GET `/api/vendor/full-profile`
**Purpose:** Get vendor with complete menu data
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.getFullProfile()
const vendorData = response.data.data
```

**Expected Response (200):**
```json
{
  "message": "Vendor with menu",
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "vendor@example.com",
    "subdomain": "my-restaurant",
    "business_links": [
      {
        "id": 1,
        "business_name": "My Restaurant",
        "business_link": "my-restaurant",
        "subdomain": "my-restaurant",
        "business_qr": "https://cloudinary.com/qr.png",
        "items": [
          {
            "id": 1,
            "title": "Pizza",
            "description": "Delicious pizza",
            "price": "15.00",
            "status": true,
            "category": {
              "id": 1,
              "category_name": "Main Courses"
            }
          }
        ]
      }
    ],
    "vendor_media": {
      "id": 1,
      "logo": "https://cloudinary.com/logo.png",
      "hero": "https://cloudinary.com/hero.png"
    }
  }
}
```

---

### POST `/api/vendor/business-info`
**Purpose:** Set business information
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.setBusinessInfo({
  business_name: "My Restaurant",
  business_address: "123 Main St",
  city_id: 1,
  state_id: 1,
  country_id: 1
})
```

**Expected Response (201):**
```json
{
  "message": "Business data created successfully.",
  "data": [
    { /* UserData object */ },
    { /* BusinessLink object */ }
  ]
}
```

---

### POST `/api/vendor/business-links`
**Purpose:** Create business link (subdomain)
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.setBusinessLink({
  business_name: "My Restaurant"
})
```

**Expected Response (200):**
```json
{
  "message": "Business link created successful.",
  "data": {
    "id": 1,
    "business_name": "My Restaurant",
    "business_link": "my-restaurant",
    "subdomain": "my-restaurant",
    "business_qr": "https://cloudinary.com/qr.png"
  }
}
```

---

### POST `/api/vendor/media`
**Purpose:** Upload vendor media (logo, hero)
**Auth Required:** Yes (vendor role)
**Content-Type:** `multipart/form-data`

**Frontend Usage:**
```javascript
const formData = new FormData()
formData.append('logo_file', logoFile) // File object
formData.append('hero_file', heroFile) // File object

const { vendor } = useApi()
const response = await vendor.uploadMedia(formData)
```

**Expected Response (201):**
```json
{
  "message": "Media uploaded",
  "data": {
    "id": 1,
    "vendor_id": 1,
    "logo": "https://cloudinary.com/logo.png",
    "hero": "https://cloudinary.com/hero.png",
    "created_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

---

### POST `/api/vendor/generate-qr`
**Purpose:** Generate QR code for vendor
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.generateQR()
const qrCodeUrl = response.data.qr_code_url
```

**Expected Response (200):**
```json
{
  "message": "QR code generated successfully",
  "qr_code_url": "https://cloudinary.com/qr_my-restaurant.png"
}
```

---

### GET `/api/vendor/categories`
**Purpose:** Get all vendor categories
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.getCategories()
const categories = response.data
```

**Expected Response (200):**
```json
[
  { "id": 1, "category_name": "Appetizers" },
  { "id": 2, "category_name": "Main Courses" }
]
```

---

### POST `/api/vendor/categories`
**Purpose:** Create new category
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.createCategory({
  category_name: "Desserts"
})
```

**Expected Response (201):**
```json
{
  "message": "Category created",
  "data": {
    "id": 3,
    "category_name": "Desserts"
  }
}
```

---

### POST `/api/vendor/categories/{categoryId}/items`
**Purpose:** Add item to category
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.addItemToCategory(1, {
  title: "Spring Rolls",
  description: "Crispy vegetable rolls",
  price: "5.99",
  status: true
})
```

---

### GET `/api/vendor/items`
**Purpose:** Get all vendor items
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.getItems()
const items = response.data
```

**Expected Response (200):**
```json
[
  {
    "id": 1,
    "title": "Pizza",
    "description": "Delicious pizza",
    "price": "15.00",
    "status": true,
    "category_id": 2,
    "business_link_id": 1
  }
]
```

---

### POST `/api/vendor/items`
**Purpose:** Create new item
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.createItem({
  title: "Margherita Pizza",
  description: "Classic pizza with fresh mozzarella",
  price: "12.99",
  category_id: 2,
  business_link_id: 1,
  status: true
})
```

**Expected Response (201):**
```json
{
  "message": "Item created",
  "data": {
    "id": 5,
    "title": "Margherita Pizza",
    "description": "Classic pizza with fresh mozzarella",
    "price": "12.99",
    "status": true
  }
}
```

---

### PUT `/api/vendor/items/{id}`
**Purpose:** Update item
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
const response = await vendor.updateItem(5, {
  title: "Updated Pizza Name",
  price: "14.99"
})
```

---

### DELETE `/api/vendor/items/{id}`
**Purpose:** Delete item
**Auth Required:** Yes (vendor role)

**Frontend Usage:**
```javascript
const { vendor } = useApi()
await vendor.deleteItem(5)
```

**Expected Response (200):**
```json
{
  "message": "Item deleted successfully"
}
```

---

## Admin Endpoints

### GET `/api/admin/dashboard`
**Purpose:** Get admin dashboard data
**Auth Required:** Yes (admin role)

**Frontend Usage:**
```javascript
const { admin } = useApi()
const response = await admin.getDashboard()
```

---

### GET `/api/admin/vendors`
**Purpose:** Get all vendors
**Auth Required:** Yes (admin role)

**Frontend Usage:**
```javascript
const { admin } = useApi()
const response = await admin.getVendors()
const vendors = response.data
```

**Expected Response (200):**
```json
[
  {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "vendor@example.com",
    "role": "vendor",
    "subdomain": "my-restaurant"
  }
]
```

---

### POST `/api/admin/vendors`
**Purpose:** Create new vendor
**Auth Required:** Yes (admin role)

**Frontend Usage:**
```javascript
const { admin } = useApi()
const response = await admin.createVendor({
  first_name: "Jane",
  last_name: "Smith",
  email: "jane@example.com",
  password: "password123",
  password_confirmation: "password123",
  role: "vendor",
  phone_number: "9876543210"
})
```

---

### PUT `/api/admin/vendors/{id}`
**Purpose:** Update vendor
**Auth Required:** Yes (admin role)

**Frontend Usage:**
```javascript
const { admin } = useApi()
const response = await admin.updateVendor(1, {
  first_name: "Updated Name"
})
```

---

### DELETE `/api/admin/vendors/{id}`
**Purpose:** Delete vendor
**Auth Required:** Yes (admin role)

**Frontend Usage:**
```javascript
const { admin } = useApi()
await admin.deleteVendor(1)
```

---

### POST `/api/admin/vendors/{id}/generate-qr`
**Purpose:** Generate QR code for vendor (admin)
**Auth Required:** Yes (admin role)

**Frontend Usage:**
```javascript
const { admin } = useApi()
const response = await admin.generateVendorQR(1)
const qrUrl = response.data.qr_code_url
```

---

## Public Endpoints (No Auth Required)

### GET `http://{subdomain}.{domain}/`
**Purpose:** Get vendor menu by subdomain

**Frontend Usage:**
```javascript
const { public: publicApi } = useApi()
const vendorData = await publicApi.getVendorMenu('my-restaurant')
```

**Expected Response (200):**
```json
{
  "id": 1,
  "name": "My Restaurant",
  "subdomain": "my-restaurant",
  "categories": [
    {
      "id": 1,
      "category_name": "Appetizers",
      "items": [
        {
          "id": 1,
          "title": "Spring Rolls",
          "description": "Crispy rolls",
          "price": "5.99",
          "status": true
        }
      ]
    }
  ]
}
```

---

### GET `/api/menu/{vendor_link}`
**Purpose:** Get vendor menu by link (alternative)

**Frontend Usage:**
```javascript
const { public: publicApi } = useApi()
const response = await publicApi.getVendorMenuByLink('my-restaurant')
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

**What to do:** Refresh token or redirect to login

---

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

**What to do:** User doesn't have permission, show error message

---

### 404 Not Found
```json
{
  "message": "Not found."
}
```

**What to do:** Show "not found" message to user

---

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password confirmation does not match."]
  }
}
```

**What to do:** Display validation errors in form

**Frontend handling:**
```javascript
try {
  await api.createItem(data)
} catch (error) {
  if (error.response?.status === 422) {
    const errors = error.response.data.errors
    // Display errors next to form fields
  }
}
```

---

### 500 Server Error
```json
{
  "message": "Server Error"
}
```

**What to do:** Show generic error message, log to console

---

## API Integration Checklist

Before building each page, verify:

- [ ] API endpoint exists in Laravel routes
- [ ] Expected request body format is correct
- [ ] Expected response format is documented
- [ ] Auth requirements are clear (token needed?)
- [ ] Error handling is implemented
- [ ] Loading states are shown
- [ ] Success messages are displayed

---

## Quick Reference: All Endpoints

### Auth (Public)
- POST `/api/auth/register`
- POST `/api/auth/login`
- POST `/api/auth/logout` (auth)
- POST `/api/auth/refresh` (auth)
- GET `/api/auth/countries`
- GET `/api/auth/states/{id}`
- GET `/api/auth/cities/{id}`

### Vendor (Auth: vendor)
- GET `/api/vendor/dashboard`
- GET `/api/vendor/profile`
- GET `/api/vendor/full-profile`
- PUT `/api/vendor/profile/update`
- POST `/api/vendor/business-info`
- POST `/api/vendor/business-links`
- POST `/api/vendor/media`
- POST `/api/vendor/generate-qr`
- GET `/api/vendor/categories`
- POST `/api/vendor/categories`
- POST `/api/vendor/categories/{id}/items`
- GET `/api/vendor/subcategories`
- POST `/api/vendor/subcategories`
- GET `/api/vendor/items`
- GET `/api/vendor/items/{id}`
- POST `/api/vendor/items`
- PUT `/api/vendor/items/{id}`
- DELETE `/api/vendor/items/{id}`
- GET `/api/vendor/items/by-category/{id}`

### Admin (Auth: admin)
- GET `/api/admin/dashboard`
- GET `/api/admin/vendors`
- GET `/api/admin/vendors/{id}`
- POST `/api/admin/vendors`
- PUT `/api/admin/vendors/{id}`
- DELETE `/api/admin/vendors/{id}`
- POST `/api/admin/vendors/{id}/media`
- POST `/api/admin/vendors/{id}/generate-qr`
- GET `/api/admin/businesses`
- GET `/api/admin/business-links`
- GET `/api/admin/business-links/{id}`
- DELETE `/api/admin/business-links/{id}`
- GET `/api/admin/items`
- POST `/api/admin/items/create`
- PUT `/api/admin/items/update/{id}`
- GET `/api/admin/categories`
- GET `/api/admin/categories-with-items`
- GET `/api/admin/categories/{id}`
- POST `/api/admin/categories`
- PUT `/api/admin/categories/{id}`
- DELETE `/api/admin/categories/{id}`

### Public (No Auth)
- GET `http://{subdomain}.{domain}/`
- GET `/api/menu/{vendor_link}`
- GET `/api/qr/{vendor_link}`

---

✅ **All APIs documented and verified against Laravel routes/api.php**
