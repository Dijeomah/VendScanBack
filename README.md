# QR-Based Menu Management System

## 1. Project Overview

This project is a QR-based menu management system designed to allow businesses (vendors) to create and manage digital menus accessible via QR codes. It features a robust Laravel API backend and is intended to be complemented by a separate, modern Vue.js frontend.

**Key Features:**
*   **User Authentication:** Secure registration, login, and session management for different user roles (Admin, Vendor).
*   **Vendor Management:** Admins can manage vendor accounts.
*   **Menu Management:** Vendors can create, update, and delete categories, subcategories, and menu items.
*   **QR Code Generation:** Dynamic generation of QR codes linking directly to vendor-specific digital menus.
*   **Subdomain Support:** Each vendor can have a unique subdomain (e.g., `restaurant.yourdomain.com`) serving their menu data as a JSON API.
*   **Media Uploads:** Integration with Cloudinary for image and video storage.

## 2. Backend API Reference

The backend is a Laravel API. All API endpoints are prefixed with `/api` unless otherwise specified (e.g., public menu routes, subdomains).

### Authentication

All authenticated API requests require a JWT token in the `Authorization: Bearer <token>` header.

#### Register a New User
*   **URL:** `/api/auth/register`
*   **Method:** `POST`
*   **Request Body (JSON):**
    ```json
    {
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@example.com",
        "password": "password",
        "password_confirmation": "password",
        "role": "Vendor", // or "Admin", "User"
        "phone_number": "1234567890"
    }
    ```
*   **Success Response (201 Created):**
    ```json
    {
        "message": "User successfully registered",
        "user": {
            "id": 1,
            "userid": "UNIQUE_USER_ID",
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com",
            "role": "Vendor",
            "phone_number": "1234567890",
            "updated_at": "YYYY-MM-DDTHH:MM:SS.000000Z",
            "created_at": "YYYY-MM-DDTHH:MM:SS.000000Z"
        }
    }
    ```
*   **Error Response (422 Unprocessable Entity):**
    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "email": ["The email has already been taken."],
            "password": ["The password confirmation does not match."]
        }
    }
    ```

#### User Login
*   **URL:** `/api/auth/login`
*   **Method:** `POST`
*   **Request Body (JSON):**
    ```json
    {
        "email": "john.doe@example.com",
        "password": "password"
    }
    ```
*   **Success Response (200 OK):**
    ```json
    {
        "access_token": "YOUR_JWT_TOKEN",
        "token_type": "bearer",
        "expires_in": 3600, // Token expiration in seconds
        "user": {
            "id": 1,
            "userid": "UNIQUE_USER_ID",
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com",
            "role": "Vendor",
            "phone_number": "1234567890",
            "email_verified_at": null,
            "created_at": "YYYY-MM-DDTHH:MM:SS.000000Z",
            "updated_at": "YYYY-MM-DDTHH:MM:SS.000000Z"
        }
    }
    ```
*   **Error Response (401 Unauthorized):**
    ```json
    {
        "message": "Unauthorized"
    }
    ```

#### User Logout
*   **URL:** `/api/auth/logout`
*   **Method:** `POST`
*   **Headers:** `Authorization: Bearer <token>`
*   **Success Response (200 OK):**
    ```json
    {
        "message": "Successfully logged out"
    }
    ```

#### Refresh Token
*   **URL:** `/api/auth/refresh`
*   **Method:** `POST`
*   **Headers:** `Authorization: Bearer <expired_token>`
*   **Success Response (200 OK):**
    ```json
    {
        "access_token": "NEW_JWT_TOKEN",
        "token_type": "bearer",
        "expires_in": 3600,
        "user": {
            // User data
        }
    }
    ```

#### Get Countries
*   **URL:** `/api/auth/countries`
*   **Method:** `GET`
*   **Success Response (200 OK):**
    ```json
    [
        {"id": 1, "name": "United States"},
        {"id": 2, "name": "Canada"}
    ]
    ```

#### Get States by Country
*   **URL:** `/api/auth/states/{country_id}`
*   **Method:** `GET`
*   **Success Response (200 OK):**
    ```json
    [
        {"id": 1, "name": "California", "country_id": 1},
        {"id": 2, "name": "New York", "country_id": 1}
    ]
    ```

#### Get Cities by State
*   **URL:** `/api/auth/cities/{state_id}`
*   **Method:** `GET`
*   **Success Response (200 OK):**
    ```json
    [
        {"id": 1, "name": "Los Angeles", "state_id": 1},
        {"id": 2, "name": "San Francisco", "state_id": 1}
    ]
    ```

### Admin Endpoints

All Admin endpoints require `Authorization: Bearer <token>` and the authenticated user must have the `admin` role.

#### Admin Dashboard
*   **URL:** `/api/admin/dashboard`
*   **Method:** `GET`
*   **Success Response (200 OK):**
    ```json
    {
        "message": "Admin Dashboard Data"
        // ... other dashboard specific data
    }
    ```

#### Manage Vendors (CRUD)
*   **List Vendors:** `GET /api/admin/vendors`
*   **Create Vendor:** `POST /api/admin/vendors` (Request body similar to user registration, but role is implicitly 'Vendor')
*   **Show Vendor:** `GET /api/admin/vendors/{id}`
*   **Update Vendor:** `PUT /api/admin/vendors/{id}`
*   **Delete Vendor:** `DELETE /api/admin/vendors/{id}`

#### Manage Business & Links
*   **Get All Businesses:** `GET /api/admin/businesses`
*   **Get All Business Links:** `GET /api/admin/business-links`
*   **Get Single Business Link:** `GET /api/admin/business-links/{id}`
*   **Delete Business Link:** `DELETE /api/admin/business-links/{id}`

#### Manage Items (Admin View)
*   **List All Items:** `GET /api/admin/items`
*   **Add Item:** `POST /api/admin/items/create` (Request body for item creation)
*   **Update Item:** `PUT /api/admin/items/update/{id}` (Request body for item update)

#### Manage Categories (Admin View)
*   **List All Categories:** `GET /api/admin/categories`
*   **List Categories with Items:** `GET /api/admin/categories-with-items`
*   **Add Category:** `POST /api/admin/categories` (Request body for category creation)
*   **Show Category:** `GET /api/admin/categories/{id}`
*   **Update Category:** `PUT /api/admin/categories/{id}`
*   **Delete Category:** `DELETE /api/admin/categories/{id}`

#### Generate QR Code for Vendor
*   **URL:** `/api/admin/vendors/{id}/generate-qr`
*   **Method:** `POST`
*   **Success Response (200 OK):**
    ```json
    {
        "message": "QR code generated successfully",
        "qr_code_url": "https://res.cloudinary.com/your_cloud_name/image/upload/.../qr_your-business-link.png"
    }
    ```

### Vendor Endpoints

All Vendor endpoints require `Authorization: Bearer <token>` and the authenticated user must have the `vendor` role.

#### Vendor Dashboard
*   **URL:** `/api/vendor/dashboard`
*   **Method:** `GET`
*   **Success Response (200 OK):**
    ```json
    {
        "message": "Vendor Dashboard Data"
        // ... other dashboard specific data
    }
    ```

#### Get Full Vendor Profile with Menu
*   **URL:** `/api/vendor/full-profile`
*   **Method:** `GET`
*   **Success Response (200 OK):**
    ```json
    {
        "message": "Vendor with menu",
        "data": {
            "id": 1,
            "name": "Vendor Name",
            "email": "vendor@example.com",
            // ... other vendor details
            "business_links": [
                {
                    "id": 1,
                    "business_name": "My Restaurant",
                    "business_link": "my-restaurant",
                    "subdomain": "my-restaurant",
                    "business_qr": "https://...",
                    "items": [
                        {
                            "id": 1,
                            "title": "Pizza",
                            "description": "Delicious pizza",
                            "price": "15.00",
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
                "logo": "https://...",
                "hero": "https://..."
            }
        }
    }
    ```

#### Manage Vendor Profile
*   **Get Profile:** `GET /api/vendor/profile`
*   **Edit Profile (Get Data for Form):** `GET /api/vendor/profile/edit`
*   **Update Profile:** `PUT /api/vendor/profile/update` (Request body for profile update)

#### Set Business Information
*   **URL:** `/api/vendor/business-info`
*   **Method:** `POST`
*   **Request Body (JSON):**
    ```json
    {
        "business_name": "My Awesome Restaurant",
        "business_address": "123 Main St",
        "city_id": 1,
        "state_id": 1,
        "country_id": 1
    }
    ```
*   **Success Response (201 Created):**
    ```json
    {
        "message": "Business data created successfully.",
        "data": [
            // UserData object
            // BusinessLink object (including subdomain and business_qr)
        ]
    }
    ```

#### Set Business Link (Subdomain)
*   **URL:** `/api/vendor/business-links`
*   **Method:** `POST`
*   **Request Body (JSON):**
    ```json
    {
        "business_name": "My Awesome Restaurant" // This will be slugified for business_link and subdomain
    }
    ```
*   **Success Response (200 OK):**
    ```json
    {
        "message": "Business link created successful.",
        "data": {
            // BusinessLink object (including subdomain and business_qr)
        }
    }
    ```

#### Generate QR Code (Vendor)
*   **URL:** `/api/vendor/generate-qr`
*   **Method:** `POST`
*   **Success Response (200 OK):**
    ```json
    {
        "message": "QR code generated successfully",
        "qr_code_url": "https://res.cloudinary.com/your_cloud_name/image/upload/.../qr_your-business-link.png"
    }
    ```

#### Manage Categories (Vendor View)
*   **List Categories:** `GET /api/vendor/categories`
*   **Create Category:** `POST /api/vendor/categories` (Request body for category creation)
*   **Add Item to Category:** `POST /api/vendor/categories/{categoryId}/items` (Request body for item creation)

#### Manage Subcategories (Vendor View)
*   **View Subcategories:** `GET /api/vendor/subcategories`
*   **Create Subcategory:** `POST /api/vendor/subcategories` (Request body for subcategory creation)

#### Manage Items (Vendor View)
*   **List Items:** `GET /api/vendor/items`
*   **Create Item:** `POST /api/vendor/items`
*   **Show Item:** `GET /api/vendor/items/{id}`
*   **Update Item:** `PUT /api/vendor/items/{id}`
*   **Delete Item:** `DELETE /api/vendor/items/{id}`
*   **List Items by Category:** `GET /api/vendor/items/by-category/{categoryId}`

#### Upload Media
*   **URL:** `/api/vendor/media`
*   **Method:** `POST`
*   **Request Body (multipart/form-data):**
    *   `logo_file`: (Optional) Image file for logo.
    *   `hero_file`: (Optional) Image file for hero banner.
*   **Success Response (201 Created):**
    ```json
    {
        "message": "Media uploaded",
        "data": {
            "id": 1,
            "vendor_id": 1,
            "logo": "https://res.cloudinary.com/your_cloud_name/image/upload/.../logo.png",
            "hero": "https://res.cloudinary.com/your_cloud_name/image/upload/.../hero.png",
            "updated_at": "YYYY-MM-DDTHH:MM:SS.000000Z",
            "created_at": "YYYY-MM-DDTHH:MM:SS.000000Z"
        }
    }
    ```

### Public Endpoints

These endpoints do not require authentication.

#### Vendor Public Menu (JSON API)
*   **URL:** `http://{subdomain}.{APP_DOMAIN}` (e.g., `http://myrestaurant.qr-app.test`)
*   **Method:** `GET`
*   **Success Response (200 OK):**
    ```json
    {
        "id": 1,
        "name": "Vendor Name",
        "email": "vendor@example.com",
        "subdomain": "vendor-subdomain",
        // ... other vendor details
        "categories": [
            {
                "id": 1,
                "category_name": "Appetizers",
                "items": [
                    {
                        "id": 101,
                        "title": "Spring Rolls",
                        "description": "Crispy vegetable spring rolls",
                        "price": "5.99",
                        "status": true
                    },
                    // ... more items
                ]
            },
            {
                "id": 2,
                "category_name": "Main Courses",
                "items": [
                    {
                        "id": 201,
                        "title": "Chicken Curry",
                        "description": "Spicy chicken curry with rice",
                        "price": "12.50",
                        "status": true
                    },
                    // ... more items
                ]
            }
        ]
    }
    ```
*   **Error Response (404 Not Found):**
    ```json
    {
        "message": "Vendor not found."
    }
    ```

## 3. Frontend (Vue.js) Development Guide

This section outlines the approach for building a separate, SaaS-worthy frontend application using Vue.js.

### SaaS-Worthy Features to Implement

To make the frontend truly SaaS-worthy, consider implementing the following:

*   **Responsive Design:** A fluid layout that adapts to various screen sizes (desktop, tablet, mobile).
*   **Intuitive User Interface (UI):** Clean, modern, and easy-to-navigate design.
*   **Role-Based Dashboards:** Separate dashboards for Admin and Vendor roles, displaying relevant information and actions.
*   **Comprehensive Menu Management:**
    *   CRUD operations for Categories, Subcategories, and Items.
    *   Drag-and-drop reordering for menu items/categories.
    *   Rich text editor for item descriptions.
    *   Image/video upload integration for menu items (via Cloudinary).
    *   Item status toggles (e.g., "Available", "Out of Stock").
*   **QR Code Management:**
    *   Display generated QR codes.
    *   Option to download QR codes in various formats (PNG, SVG).
    *   Preview of the public vendor menu.
*   **User Profile Management:** Ability for users to update their personal and business information.
*   **Authentication Flows:**
    *   Secure login and registration forms.
    *   Password reset functionality.
    *   JWT token management (storage, refresh, invalidation).
*   **Form Validation:** Client-side validation to provide immediate feedback to users.
*   **State Management:** Use Vuex or Pinia for centralized state management (e.g., user authentication status, vendor data).
*   **Routing:** Vue Router for single-page application navigation.
*   **Error Handling:** Graceful display of API errors and user-friendly messages.
*   **Loading States:** Indicate when data is being fetched or operations are in progress.
*   **Notifications:** Toast messages or similar for success/error feedback.
*   **Analytics Integration (Optional):** Track public menu views, popular items, etc.
*   **Customization Options (Future):** Allow vendors to customize their public menu's appearance (colors, fonts, themes).

### Project Setup (Vue.js)

You can scaffold a new Vue.js project using Vue CLI or Vite. Vite is generally recommended for faster development.

**Using Vite (Recommended):**

```bash
npm create vue@latest
# Follow the prompts (e.g., project name, TypeScript, Vue Router, Pinia, ESLint)
```

**Using Vue CLI (Alternative):**

```bash
npm install -g @vue/cli
vue create my-qr-app-frontend
# Choose "Manually select features" and select Router, Vuex/Pinia, Linter/Formatter
```

### API Integration Strategy

The Vue.js frontend will communicate with the Laravel backend via HTTP requests.

#### 1. HTTP Client

Use a library like `axios` for making HTTP requests.

```bash
npm install axios
```

#### 2. Authentication (JWT)

*   **Login/Registration:**
    *   Send user credentials to `/api/auth/login` or `/api/auth/register`.
    *   Upon successful response, store the `access_token` (JWT) in `localStorage` or `sessionStorage`.
    *   Store user data (including `role`) in your Vuex/Pinia store.
*   **Authenticated Requests:**
    *   Attach the JWT to every authenticated request in the `Authorization` header:
        ```javascript
        axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('jwt_token')}`;
        ```
    *   Consider using an Axios interceptor to automatically attach the token and handle token refresh.
*   **Token Refresh:**
    *   Implement logic to refresh the token using `/api/auth/refresh` when it's about to expire or when an API call returns a 401 Unauthorized error (indicating an expired token).
    *   Be careful to avoid infinite loops during token refresh.
*   **Logout:**
    *   Call `/api/auth/logout`.
    *   Remove the JWT from `localStorage`/`sessionStorage` and clear user data from the store.

#### 3. API Service Module

Create a dedicated module (e.g., `src/services/api.js`) to encapsulate all API calls. This makes your code cleaner and easier to maintain.

```javascript
// src/services/api.js
import axios from 'axios';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_APP_BACKEND_URL || 'http://localhost:8000/api', // Adjust as needed
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Axios Interceptor for attaching JWT
apiClient.interceptors.request.use(config => {
    const token = localStorage.getItem('jwt_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

// Axios Interceptor for handling token refresh (more complex, requires careful implementation)
// apiClient.interceptors.response.use(response => response, async error => {
//     const originalRequest = error.config;
//     if (error.response.status === 401 && !originalRequest._retry) {
//         originalRequest._retry = true;
//         // Logic to refresh token and retry original request
//     }
//     return Promise.reject(error);
// });

export default {
    auth: {
        login(credentials) {
            return apiClient.post('/auth/login', credentials);
        },
        register(userData) {
            return apiClient.post('/auth/register', userData);
        },
        logout() {
            return apiClient.post('/auth/logout');
        },
        refresh() {
            return apiClient.post('/auth/refresh');
        },
        getCountries() {
            return apiClient.get('/auth/countries');
        },
        getStates(countryId) {
            return apiClient.get(`/auth/states/${countryId}`);
        },
        getCities(stateId) {
            return apiClient.get(`/auth/cities/${stateId}`);
        },
    },
    vendor: {
        getDashboard() {
            return apiClient.get('/vendor/dashboard');
        },
        getProfile() {
            return apiClient.get('/vendor/profile');
        },
        updateProfile(data) {
            return apiClient.put('/vendor/profile/update', data);
        },
        setBusinessInfo(data) {
            return apiClient.post('/vendor/business-info', data);
        },
        setBusinessLink(data) {
            return apiClient.post('/vendor/business-links', data);
        },
        generateQrCode() {
            return apiClient.post('/vendor/generate-qr');
        },
        uploadMedia(formData) { // Use FormData for file uploads
            return apiClient.post('/vendor/media', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
        },
        // ... other vendor specific API calls
    },
    admin: {
        // ... admin specific API calls
    },
    public: {
        getVendorMenu(subdomain) {
            // Note: This requires a different baseURL or direct URL construction
            // For example, if your frontend is on example.com and backend is api.example.com
            // you might need to adjust this. For local development, ensure your
            // Herd/Valet setup correctly routes subdomains.
            return axios.get(`http://${subdomain}.${import.meta.env.VITE_APP_DOMAIN}`);
        }
    }
};
```

#### 4. Environment Variables

Use `.env` files in your Vue.js project to manage environment-specific variables (e.g., backend API URL, base domain for subdomains).

```dotenv
# .env (for development)
VITE_APP_BACKEND_URL=http://localhost:8000/api
VITE_APP_DOMAIN=qr-app.test # Matches your Herd/Valet TLD
```

Access these in your Vue.js code using `import.meta.env.VITE_APP_BACKEND_URL` (Vite) or `process.env.VUE_APP_BACKEND_URL` (Vue CLI).

### UI/UX Recommendations

*   **Component Library:** Use a well-maintained Vue.js component library for a consistent and professional look.
    *   **Vuetify:** Comprehensive Material Design framework.
    *   **Element Plus:** Vue 3 UI library based on Element UI.
    *   **Quasar Framework:** High-performance Vue.js framework for building various types of applications.
    *   **Tailwind CSS + Headless UI:** For maximum flexibility and customizability.
*   **Design System:** Consider establishing a simple design system (colors, typography, spacing) to maintain consistency.
*   **User Flow:** Map out the user journeys for each role (registration, login, dashboard, menu creation, etc.) to ensure a smooth and logical experience.

### Development Workflow (Vue.js Frontend)

1.  **Install Dependencies:**
    ```bash
    npm install
    ```
2.  **Start Development Server:**
    ```bash
    npm run dev # For Vite
    # or
    npm run serve # For Vue CLI
    ```
    This will typically start the frontend on `http://localhost:5173` (Vite) or `http://localhost:8080` (Vue CLI).
3.  **Build for Production:**
    ```bash
    npm run build
    ```
    This will compile your Vue.js application into static assets (HTML, CSS, JS) in a `dist` folder, ready for deployment.

## 4. Deployment Considerations

### Backend (Laravel)

*   **Server:** A PHP-compatible web server (Nginx, Apache).
*   **Database:** MySQL, PostgreSQL, etc.
*   **Environment Variables:** Ensure all `.env` variables are correctly configured on your production server.
*   **Composer Dependencies:** Run `composer install --no-dev` on the server.
*   **Migrations:** Run `php artisan migrate` on the server.
*   **Queue Worker (Optional):** If using queues for background tasks (e.g., QR code generation), set up a queue worker.
*   **Supervisor/PM2:** For keeping queue workers and other long-running processes alive.
*   **SSL/TLS:** Essential for production.
*   **Subdomain Configuration:** Your web server (Nginx/Apache) must be configured to handle wildcard subdomains and route them to your Laravel application.

### Frontend (Vue.js)

*   **Static Hosting:** The built Vue.js application (`dist` folder) can be hosted on any static file server (Nginx, Apache, Netlify, Vercel, AWS S3, Cloudflare Pages).
*   **CDN:** Use a CDN for faster global delivery of static assets.
*   **Environment Variables:** Ensure `VITE_APP_BACKEND_URL` and `VITE_APP_DOMAIN` point to your production backend API and domain.
*   **CORS:** Ensure your Laravel backend has appropriate CORS headers configured to allow requests from your frontend domain.

---

This `README.md` provides a solid foundation for developing your Vue.js frontend. Remember to refer to the specific documentation for Vue.js, Vue Router, Pinia/Vuex, and your chosen component library as you build out the application.
