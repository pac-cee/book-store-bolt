# API Documentation

## Overview

BookHaven's API provides programmatic access to book catalog, user management, and order processing functionalities. This document details the available endpoints, request/response formats, and authentication requirements.

## Authentication

All API requests require authentication using Bearer tokens:

```http
Authorization: Bearer <token>
```

## Endpoints

### Books

#### List Books
```http
GET /api/books
```

Query Parameters:
- `page` (integer): Page number
- `limit` (integer): Items per page
- `category` (string): Filter by category
- `search` (string): Search term

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Book Title",
      "author": "Author Name",
      "price": 29.99,
      "category": "Fiction"
    }
  ],
  "meta": {
    "total": 100,
    "page": 1,
    "pages": 10
  }
}
```

#### Get Book Details
```http
GET /api/books/{id}
```

Response:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Book Title",
    "author": "Author Name",
    "description": "Book description",
    "price": 29.99,
    "category": "Fiction",
    "isbn": "978-3-16-148410-0",
    "publisher": "Publisher Name",
    "published_date": "2023-01-01",
    "pages": 300,
    "language": "English",
    "reviews": []
  }
}
```

### Orders

#### Create Order
```http
POST /api/orders
```

Request:
```json
{
  "items": [
    {
      "book_id": 1,
      "quantity": 2
    }
  ],
  "shipping_address_id": 1,
  "payment_method_id": 1
}
```

Response:
```json
{
  "success": true,
  "data": {
    "order_id": "ORD123456",
    "total": 59.98,
    "status": "pending"
  }
}
```

## Error Handling

All errors follow this format:

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error description"
  }
}
```

Common Error Codes:
- `UNAUTHORIZED`: Authentication required
- `FORBIDDEN`: Insufficient permissions
- `NOT_FOUND`: Resource not found
- `VALIDATION_ERROR`: Invalid input data