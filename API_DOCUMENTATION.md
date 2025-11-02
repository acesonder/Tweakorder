# Tweakorder API Documentation

## Overview

The Tweakorder API provides RESTful endpoints for managing products, clients, workers, orders, case management, and more. All API responses are in JSON format.

**Base URL:** `http://your-domain.com/api/`

**Authentication:** Session-based authentication for client portal endpoints. Staff endpoints currently open (add authentication layer as needed).

---

## Table of Contents

1. [Products API](#products-api)
2. [Clients API](#clients-api)
3. [Workers API](#workers-api)
4. [Orders API](#orders-api)
5. [Client Portal APIs](#client-portal-apis)
6. [Case Management APIs](#case-management-apis)
7. [Error Logging API](#error-logging-api)
8. [Location & Schedule APIs](#location--schedule-apis)

---

## Products API

### Get All Products

```http
GET /api/products.php
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "description": "Product description",
      "image": "uploads/image.jpg",
      "inventory": 100,
      "background_color": "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
      "category": "Supplies",
      "sku": "SKU001",
      "created_at": "2024-01-01 12:00:00"
    }
  ]
}
```

### Create Product

```http
POST /api/products.php
Content-Type: multipart/form-data
```

**Parameters:**
- `name` (required) - Product name
- `description` (optional) - Product description
- `image` (optional) - Product image file
- `inventory` (optional) - Stock quantity, default: 100
- `background_color` (optional) - CSS gradient
- `category` (optional) - Product category
- `sku` (optional) - Stock keeping unit

**Response:**
```json
{
  "success": true,
  "message": "Product added successfully",
  "product_id": 1
}
```

### Update Product

```http
PUT /api/products.php
Content-Type: application/json
```

**Body:**
```json
{
  "id": 1,
  "name": "Updated Product Name",
  "inventory": 50
}
```

### Delete Product

```http
DELETE /api/products.php?id=1
```

---

## Clients API

### Get All Clients

```http
GET /api/clients.php
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "phone": "555-0100",
      "email": "john@example.com",
      "address": "123 Main St",
      "username": "JOHNDOE010190",
      "created_at": "2024-01-01 12:00:00"
    }
  ]
}
```

### Create Client

```http
POST /api/clients.php
Content-Type: application/json
```

**Body:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "phone": "555-0100",
  "email": "john@example.com",
  "address": "123 Main St"
}
```

---

## Workers API

### Get All Workers

```http
GET /api/workers.php
```

### Create Worker

```http
POST /api/workers.php
Content-Type: application/json
```

**Body:**
```json
{
  "first_name": "Jane",
  "last_name": "Smith"
}
```

---

## Orders API

### Get All Orders

```http
GET /api/orders.php
```

**Query Parameters:**
- `status` (optional) - Filter by status
- `client_id` (optional) - Filter by client

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "client_name": "John Doe",
      "worker_name": "Jane Smith",
      "status": "Processing",
      "products": "Product 1, Product 2",
      "created_at": "2024-01-01 12:00:00"
    }
  ]
}
```

### Create Order

```http
POST /api/orders.php
Content-Type: application/json
```

**Body:**
```json
{
  "client_id": 1,
  "worker_id": 1,
  "status": "Processing",
  "products": [
    {"product_id": 1, "quantity": 2},
    {"product_id": 2, "quantity": 1}
  ]
}
```

### Update Order Status

```http
PUT /api/orders.php
Content-Type: application/json
```

**Body:**
```json
{
  "id": 1,
  "status": "Fulfilled"
}
```

---

## Client Portal APIs

### Authentication

#### Register

```http
POST /api/auth.php
Content-Type: application/json
```

**Body:**
```json
{
  "action": "register",
  "first_name": "John",
  "last_name": "Doe",
  "dob": "1990-01-01",
  "password": "securePassword123",
  "security_question": "What is your favorite color?",
  "security_answer": "Blue"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Account created successfully",
  "username": "JOHNDOE010190"
}
```

#### Login

```http
POST /api/auth.php
Content-Type: application/json
```

**Body:**
```json
{
  "action": "login",
  "username": "JOHNDOE010190",
  "password": "securePassword123"
}
```

#### Forgot Password

```http
POST /api/auth.php
Content-Type: application/json
```

**Body:**
```json
{
  "action": "forgot",
  "first_name": "John",
  "last_name": "Doe",
  "dob": "1990-01-01"
}
```

### Client Orders

#### Get Client Orders

```http
GET /api/client-orders.php
```

**Requires:** Active client session

**Response:**
```json
{
  "success": true,
  "orders": [
    {
      "id": 1,
      "products": "Product 1, Product 2",
      "status": "Processing",
      "delivery_method": "Pickup",
      "created_at": "2024-01-01 12:00:00"
    }
  ],
  "stats": {
    "total": 10,
    "processing": 2,
    "fulfilled": 8
  }
}
```

#### Create Client Order

```http
POST /api/client-orders.php
Content-Type: application/json
```

**Body:**
```json
{
  "products": [
    {"product_id": 1, "quantity": 2}
  ],
  "delivery_method": "Pickup",
  "location": "Main Location",
  "scheduled_date": "2024-01-15",
  "scheduled_time": "10:00",
  "special_instructions": "Please call when ready"
}
```

---

## Case Management APIs

### Case Templates

#### Get Templates

```http
GET /api/case-templates.php
```

**Query Parameters:**
- `category` (optional) - Filter by category (Addiction, Homelessness, Mental Health, General, Referral)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "category": "Addiction",
      "name": "Initial Substance Use Assessment",
      "template_text": "Template content..."
    }
  ]
}
```

### Case Notes

#### Get Case Notes

```http
GET /api/case-notes.php
```

**Query Parameters:**
- `client_id` (optional) - Filter by client
- `category` (optional) - Filter by category

#### Create Case Note

```http
POST /api/case-notes.php
Content-Type: application/json
```

**Body:**
```json
{
  "client_id": 1,
  "template_id": 5,
  "note_text": "Note content...",
  "follow_up_date": "2024-01-15",
  "is_confidential": 1,
  "category": "Addiction"
}
```

---

## Error Logging API

### Get Error Logs

```http
GET /api/error-logs.php
```

**Query Parameters:**
- `status` (optional) - Filter by status (Unresolved/Resolved)
- `severity` (optional) - Filter by severity (Critical, High, Medium, Low)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "error_type": "Database",
      "severity": "High",
      "error_message": "Connection failed",
      "page_url": "/api/products.php",
      "status": "Unresolved",
      "created_at": "2024-01-01 12:00:00"
    }
  ]
}
```

### Log Error

```http
POST /api/error-logs.php
Content-Type: application/json
```

**Body:**
```json
{
  "error_type": "Validation",
  "severity": "Medium",
  "error_message": "Invalid input",
  "page_url": "/mobile-order.html"
}
```

### Update Error Status

```http
PUT /api/error-logs.php
Content-Type: application/json
```

**Body:**
```json
{
  "id": 1,
  "status": "Resolved"
}
```

---

## Location & Schedule APIs

### Get Locations

```http
GET /api/locations.php
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Main Office",
      "type": "Both",
      "address": "123 Main St",
      "is_active": 1
    }
  ]
}
```

### Get Schedule Availability

```http
GET /api/schedule.php
```

**Query Parameters:**
- `date` (optional) - Filter by date (YYYY-MM-DD)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "date": "2024-01-15",
      "time_slot": "10:00 AM",
      "is_available": 1
    }
  ]
}
```

---

## Error Responses

All endpoints return errors in the following format:

```json
{
  "success": false,
  "error": "Error message description"
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `400` - Bad Request (Invalid parameters)
- `401` - Unauthorized
- `404` - Not Found
- `500` - Internal Server Error

---

## Rate Limiting

Currently no rate limiting is implemented. Consider adding rate limiting for production use.

---

## Security Notes

1. Always use HTTPS in production
2. Implement CORS policies as needed
3. Add API authentication tokens for staff endpoints
4. Sanitize all input data
5. Use prepared statements for all database queries
6. Implement request validation

---

## Changelog

- **v1.0** - Initial API release with core functionality
- **v1.1** - Added Client Portal APIs
- **v1.2** - Added Case Management APIs
- **v1.3** - Added Error Logging API

---

## Support

For API issues or questions, please open an issue on GitHub or contact support.
