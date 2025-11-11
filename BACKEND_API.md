# API Documentation

This document provides a detailed description of the API endpoints for the QR-based menu management system.

## Authentication

### Register

*   **URL:** `/api/auth/register`
*   **Method:** `POST`
*   **Body:**

```json
{
    "name": "John Doe",
    "email": "johndoe@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

*   **Response:**

```json
{
    "message": "User successfully registered",
    "user": {
        "name": "John Doe",
        "email": "johndoe@example.com",
        "updated_at": "2025-08-06T12:00:00.000000Z",
        "created_at": "2025-08-06T12:00:00.000000Z",
        "id": 1
    }
}
```

### Login

*   **URL:** `/api/auth/login`
*   **Method:** `POST`
*   **Body:**

```json
{
    "email": "johndoe@example.com",
    "password": "password"
}
```

*   **Response:**

```json
{
    "access_token": "<jwt_token>",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "johndoe@example.com",
        "email_verified_at": null,
        "created_at": "2025-08-06T12:00:00.000000Z",
        "updated_at": "2025-08-06T12:00:00.000000Z"
    }
}
```

## Admin

### Get Dashboard

*   **URL:** `/api/admin/dashboard`
*   **Method:** `GET`
*   **Headers:**

```
Authorization: Bearer <jwt_token>
```

*   **Response:**

```json
{
    "message": "Admin Dashboard"
}
```

## Vendor

### Get Dashboard

*   **URL:** `/api/vendor/dashboard`
*   **Method:** `GET`
*   **Headers:**

```
Authorization: Bearer <jwt_token>
```

*   **Response:**

```json
{
    "message": "Vendor Dashboard"
}
```

## Subdomain

### Show Vendor Page

*   **URL:** `http://<subdomain>.vendscan.app`
*   **Method:** `GET`
*   **Response:**

```html
<!DOCTYPE html>
<html>
<head>
    <title>Vendor Page</title>
</head>
<body>
    <h1>Welcome to [Vendor Name]'s page!</h1>
    <p>This is the subdomain page for [Vendor Name].</p>
</body>
</html>
```
