# E-Commerce Analytics API Documentation

## Setup Instructions

Before using the analytics endpoints, run the following migration to set up the required database columns:

```bash
php artisan migrate
```

This will add the following columns to your database:
- Products table: stock, status, sales_count, view_count, average_rating, reviews_count
- Reviews table: status column for tracking approved reviews

## Overview
This documentation covers the Analytics API endpoints available to administrators. These endpoints provide comprehensive insights into sales performance, order statistics, product popularity, and customer reviews.

## Base URL
```
http://your-domain.com/api
```

## Authentication
All endpoints require authentication using Bearer token and admin privileges.

**Request Headers:**
```
Authorization: Bearer your_access_token_here
Content-Type: application/json
Accept: application/json
```

## Endpoints

### 1. Sales Analytics
Retrieves detailed sales statistics and trends over time.

**Endpoint:** `GET /api/analytics/sales`

**Query Parameters:**
- timeFrame: string (optional) - Accepts 'week', 'month', or 'year' (default: 'month')

**Response Example:**
```json
{
    "status": "success",
    "data": {
        "sales_trend": [
            {
                "date": "2024-01-20",
                "orders_count": 25,
                "total_sales": 15000.50,
                "average_order_value": 600.02
            }
        ],
        "summary": {
            "total_sales": 45000.75,
            "total_orders": 75,
            "average_order_value": 600.01
        }
    }
}
```

### 2. Order Analytics
Provides comprehensive order statistics and recent order details.

**Endpoint:** `GET /api/analytics/orders`

**Response Example:**
```json
{
    "status": "success",
    "data": {
        "orders_by_status": [
            {
                "status": "completed",
                "count": 150,
                "total_amount": 75000.50
            },
            {
                "status": "pending",
                "count": 30,
                "total_amount": 15000.25
            }
        ],
        "recent_orders": [
            {
                "id": 1,
                "user": {
                    "id": 1,
                    "name": "John Doe"
                },
                "order_items": [
                    {
                        "product": {
                            "id": 1,
                            "name": "Product Name",
                            "price": 299.99
                        },
                        "quantity": 2
                    }
                ],
                "total_amount": 599.98,
                "status": "completed",
                "created_at": "2024-01-20T12:00:00.000000Z"
            }
        ]
    }
}
```

### 3. Product Analytics
Retrieves product popularity metrics and category performance statistics.

**Endpoint:** `GET /api/analytics/products`

**Response Example:**
```json
{
    "status": "success",
    "data": {
        "top_products": [
            {
                "id": 1,
                "name": "Premium Product",
                "order_items_count": 150,
                "reviews_count": 45,
                "reviews_avg_rating": 4.5,
                "price": 299.99
            }
        ],
        "category_performance": [
            {
                "name": "Electronics",
                "products_count": 100,
                "total_sales": 50000.75
            }
        ]
    }
}
```

### 4. Review Analytics
Provides insights into customer reviews and ratings.

**Endpoint:** `GET /api/analytics/reviews`

**Response Example:**
```json
{
    "status": "success",
    "data": {
        "rating_distribution": [
            {
                "rating": 5,
                "count": 150
            },
            {
                "rating": 4,
                "count": 100
            }
        ],
        "recent_reviews": [
            {
                "id": 1,
                "user": {
                    "id": 1,
                    "name": "Jane Smith"
                },
                "product": {
                    "id": 1,
                    "name": "Premium Product"
                },
                "rating": 5,
                "comment": "Excellent product!",
                "created_at": "2024-01-20T12:00:00.000000Z"
            }
        ],
        "average_rating": 4.5
    }
}
```

### 5. Dashboard Overview
Provides a quick overview of key metrics for the dashboard.

**Endpoint:** `GET /api/analytics/dashboard`

**Response Example:**
```json
{
    "status": "success",
    "data": {
        "daily_sales": 5000.50,
        "monthly_sales": 150000.75,
        "pending_orders": 25,
        "total_products": 500,
        "total_reviews": 1500,
        "inventory_status": {
            "total_products": 500,
            "out_of_stock": 10,
            "low_stock": 25,
            "in_stock": 465
        }
    }
}
```

## Error Responses

### 1. Unauthorized Error (401)
```json
{
    "status": "error",
    "message": "Unauthorized"
}
```

### 2. Forbidden Error (403)
```json
{
    "status": "error",
    "message": "Forbidden - Insufficient permissions"
}
```

### 3. Invalid Parameter Error (400)
```json
{
    "status": "error",
    "message": "Invalid timeFrame parameter. Allowed values: week, month, year"
}
```

### 4. Server Error (500)
```json
{
    "status": "error",
    "message": "Internal server error",
    "details": "Error details when available"
}
```

## Implementation Notes

### Authentication
- All endpoints require a valid Bearer token
- Admin role is required for all analytics endpoints
- Token must be included in the Authorization header

### Data Format
- All timestamps are in ISO 8601 format
- Monetary values are in the default store currency
- Decimal values are rounded to 2 decimal places
- Dates are in YYYY-MM-DD format

### Performance Considerations
- Results are paginated where applicable
- Recent data is limited to 10 items by default
- Data is cached for 5 minutes to improve performance
- Heavy queries use database indexes for optimization

### Security
- All endpoints are protected by authentication
- Role-based access control is enforced
- Input validation is performed on all parameters
- Sensitive data is filtered from responses

### Rate Limiting
- API requests are limited to 60 per minute per user
- Rate limit headers are included in responses
- Rate limit exceeded returns 429 Too Many Requests

## Database Requirements
The analytics system requires the following database fields:

### Orders Table
- id (primary key)
- user_id (foreign key)
- total_amount (decimal)
- status (string)
- created_at (timestamp)

### Products Table
- id (primary key)
- name (string)
- price (decimal)
- stock (integer)
- status (enum: active, inactive, draft)
- sales_count (integer)
- view_count (integer)
- category_id (foreign key)
- average_rating (decimal)
- reviews_count (integer)

### Reviews Table
- id (primary key)
- user_id (foreign key)
- product_id (foreign key)
- rating (integer)
- comment (text)
- status (string)
- created_at (timestamp)
