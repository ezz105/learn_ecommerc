# User Management API Documentation

## Overview
This documentation covers the User Management API endpoints available to administrators. These endpoints allow for complete user administration including creating, reading, updating, and deleting user accounts.

## Base URL
```
http://your-domain.com/api
```

## Authentication
All endpoints require authentication using Bearer token and admin privileges.

**Request Headers:**
```
Authorization: Bearer your_access_token_here
```

## Endpoints

### 1. List All Users
Retrieves a list of all registered users.

**Endpoint:** `GET /admin/users`

**Response Example:**
```json
{
    "status": "success",
    "users": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role_id": 1,
            "phone_number": "1234567890",
            "created_at": "2024-01-20T12:00:00.000000Z",
            "updated_at": "2024-01-20T12:00:00.000000Z",
            "role": {
                "id": 1,
                "name": "admin"
            }
        }
        // ... more users
    ]
}
```

### 2. Create New User
Creates a new user account.

**Endpoint:** `POST /admin/users`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "role_id": 1,
    "phone_number": "1234567890"
}
```

**Validation Rules:**
- name: Required, string, max 255 characters
- email: Required, valid email format, unique
- password: Required, minimum 8 characters
- role_id: Required, must exist in roles table
- phone_number: Required, string

**Response Example:**
```json
{
    "status": "success",
    "message": "User created successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role_id": 1,
        "phone_number": "1234567890",
        "created_at": "2024-01-20T12:00:00.000000Z",
        "updated_at": "2024-01-20T12:00:00.000000Z"
    }
}
```

### 3. Get User Details
Retrieves details of a specific user.

**Endpoint:** `GET /admin/users/{id}`

**Response Example:**
```json
{
    "status": "success",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role_id": 1,
        "phone_number": "1234567890",
        "created_at": "2024-01-20T12:00:00.000000Z",
        "updated_at": "2024-01-20T12:00:00.000000Z",
        "role": {
            "id": 1,
            "name": "admin"
        }
    }
}
```

### 4. Update User
Updates an existing user's information.

**Endpoint:** `PUT /admin/users/{id}`

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "email": "john.updated@example.com",
    "role_id": 2,
    "phone_number": "0987654321"
}
```

**Validation Rules:**
- name: Optional, string, max 255 characters
- email: Optional, valid email format, unique (except current user)
- role_id: Optional, must exist in roles table
- phone_number: Optional, string

**Response Example:**
```json
{
    "status": "success",
    "message": "User updated successfully",
    "user": {
        "id": 1,
        "name": "John Doe Updated",
        "email": "john.updated@example.com",
        "role_id": 2,
        "phone_number": "0987654321",
        "created_at": "2024-01-20T12:00:00.000000Z",
        "updated_at": "2024-01-20T12:00:00.000000Z"
    }
}
```

### 5. Delete User
Removes a user from the system.

**Endpoint:** `DELETE /admin/users/{id}`

**Response Example:**
```json
{
    "status": "success",
    "message": "User deleted successfully"
}
```

## Error Responses

### 1. Validation Error (422)
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### 2. Not Found Error (404)
```json
{
    "status": "error",
    "message": "User not found"
}
```

### 3. Unauthorized Error (401)
```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

### 4. Forbidden Error (403)
```json
{
    "status": "error",
    "message": "Forbidden"
}
```

## Notes
- All timestamps are returned in ISO 8601 format
- The API uses token-based authentication
- All endpoints require admin privileges
- Passwords are automatically hashed before storage
- Email addresses must be unique in the system
