# Hierarchical Filter API Documentation

## Overview

The Hierarchical Filter API provides RESTful endpoints for managing and filtering organizational hierarchies. The structure follows this hierarchy:

```
Category → Units → Sub-units → Offices → Sub-offices
```

All endpoints are prefixed with `/api/v1/`

---

## Base URL

```
http://localhost:8000/api/v1/
```

---

## Response Format

### Success Response

```json
{
  "status": "success",
  "message": "Resource retrieved successfully",
  "data": {
    // Response data here
  }
}
```

### Error Response

```json
{
  "status": "error",
  "message": "Error description",
  "error": {
    // Error details here
  }
}
```

---

## Categories API

### Get All Categories

**Endpoint:** `GET /categories`

**Query Parameters:**
- `per_page` (optional, default: 15) - Number of records per page

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/categories?per_page=25"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Categories retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Armed Forces",
        "abreviation": "AF",
        "description": "Armed Forces Category",
        "icon": "icon-af.png",
        "created_at": "2024-06-12T10:00:00Z",
        "updated_at": "2024-06-12T10:00:00Z"
      }
    ],
    "total": 10,
    "per_page": 25,
    "current_page": 1
  }
}
```

---

### Get Category by ID

**Endpoint:** `GET /categories/{id}`

**Path Parameters:**
- `id` (required) - Category ID

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/categories/1"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Category retrieved successfully",
  "data": {
    "id": 1,
    "name": "Armed Forces",
    "abreviation": "AF",
    "description": "Armed Forces Category",
    "icon": "icon-af.png",
    "created_at": "2024-06-12T10:00:00Z",
    "updated_at": "2024-06-12T10:00:00Z"
  }
}
```

---

### Create Category

**Endpoint:** `POST /categories`

**Request Body:**
```json
{
  "name": "New Category",
  "abreviation": "NC",
  "description": "Category description",
  "icon": "icon.png"
}
```

**Validation Rules:**
- `name` (required, string, max: 255)
- `abreviation` (optional, string, max: 50)
- `description` (optional, string)
- `icon` (optional, string)

**Example Response (201 Created):**
```json
{
  "status": "success",
  "message": "Category created successfully",
  "data": {
    "id": 11,
    "name": "New Category",
    "abreviation": "NC",
    "description": "Category description",
    "icon": "icon.png",
    "created_at": "2024-06-12T15:30:00Z",
    "updated_at": "2024-06-12T15:30:00Z"
  }
}
```

---

### Update Category

**Endpoint:** `PUT/PATCH /categories/{id}`

**Path Parameters:**
- `id` (required) - Category ID

**Request Body:**
```json
{
  "name": "Updated Name",
  "description": "Updated description"
}
```

**Example Response (200 OK):**
```json
{
  "status": "success",
  "message": "Category updated successfully",
  "data": null
}
```

---

### Delete Category

**Endpoint:** `DELETE /categories/{id}`

**Path Parameters:**
- `id` (required) - Category ID

**Example Response (200 OK):**
```json
{
  "status": "success",
  "message": "Category deleted successfully",
  "data": null
}
```

---

## Units API

### Get All Units (with optional category filter)

**Endpoint:** `GET /units`

**Query Parameters:**
- `category_id` (optional) - Filter units by category ID
- `per_page` (optional, default: 15) - Number of records per page

**Example Requests:**

Get all units:
```bash
curl -X GET "http://localhost:8000/api/v1/units"
```

Get units for a specific category:
```bash
curl -X GET "http://localhost:8000/api/v1/units?category_id=1&per_page=20"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Units retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "category_id": 1,
        "name": "Army",
        "abreviation": "AR",
        "description": "Army Unit",
        "icon": "icon-army.png",
        "created_at": "2024-06-12T10:00:00Z",
        "updated_at": "2024-06-12T10:00:00Z"
      },
      {
        "id": 2,
        "category_id": 1,
        "name": "Navy",
        "abreviation": "NV",
        "description": "Navy Unit",
        "icon": "icon-navy.png",
        "created_at": "2024-06-12T10:00:00Z",
        "updated_at": "2024-06-12T10:00:00Z"
      }
    ],
    "total": 5,
    "per_page": 15,
    "current_page": 1
  }
}
```

---

### Get Unit by ID

**Endpoint:** `GET /units/{id}`

**Path Parameters:**
- `id` (required) - Unit ID

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/units/1"
```

---

### Create Unit

**Endpoint:** `POST /units`

**Request Body:**
```json
{
  "category_id": 1,
  "name": "Air Force",
  "abreviation": "AF",
  "description": "Air Force Unit",
  "icon": "icon-airforce.png"
}
```

**Validation Rules:**
- `category_id` (required, integer, must exist in pn_categories)
- `name` (required, string, max: 255)
- `abreviation` (optional, string, max: 50)
- `description` (optional, string)
- `icon` (optional, string)

---

### Update Unit

**Endpoint:** `PUT/PATCH /units/{id}`

**Path Parameters:**
- `id` (required) - Unit ID

**Request Body:**
```json
{
  "name": "Updated Unit Name",
  "category_id": 1
}
```

---

### Delete Unit

**Endpoint:** `DELETE /units/{id}`

**Path Parameters:**
- `id` (required) - Unit ID

---

## Sub-units API

### Get All Sub-units (with optional unit filter)

**Endpoint:** `GET /sub-units`

**Query Parameters:**
- `unit_id` (optional) - Filter sub-units by unit ID
- `per_page` (optional, default: 15) - Number of records per page

**Example Requests:**

Get all sub-units:
```bash
curl -X GET "http://localhost:8000/api/v1/sub-units"
```

Get sub-units for a specific unit:
```bash
curl -X GET "http://localhost:8000/api/v1/sub-units?unit_id=1&per_page=20"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Sub-units retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "category_id": 1,
        "unit_id": 1,
        "name": "Infantry",
        "abreviation": "INF",
        "description": "Infantry Sub-unit",
        "icon": "icon-infantry.png",
        "created_at": "2024-06-12T10:00:00Z",
        "updated_at": "2024-06-12T10:00:00Z"
      }
    ],
    "total": 3,
    "per_page": 15,
    "current_page": 1
  }
}
```

---

### Get Sub-unit by ID

**Endpoint:** `GET /sub-units/{id}`

**Path Parameters:**
- `id` (required) - Sub-unit ID

---

### Create Sub-unit

**Endpoint:** `POST /sub-units`

**Request Body:**
```json
{
  "category_id": 1,
  "unit_id": 1,
  "name": "Cavalry",
  "abreviation": "CAV",
  "description": "Cavalry Sub-unit",
  "icon": "icon-cavalry.png"
}
```

**Validation Rules:**
- `category_id` (required, integer, must exist in pn_categories)
- `unit_id` (required, integer, must exist in pn_units)
- `name` (required, string, max: 255)
- `abreviation` (optional, string, max: 50)
- `description` (optional, string)
- `icon` (optional, string)

---

### Update Sub-unit

**Endpoint:** `PUT/PATCH /sub-units/{id}`

**Path Parameters:**
- `id` (required) - Sub-unit ID

---

### Delete Sub-unit

**Endpoint:** `DELETE /sub-units/{id}`

**Path Parameters:**
- `id` (required) - Sub-unit ID

---

## Offices API

### Get All Offices (with optional sub-unit filter)

**Endpoint:** `GET /offices`

**Query Parameters:**
- `sub_unit_id` (optional) - Filter offices by sub-unit ID
- `per_page` (optional, default: 15) - Number of records per page

**Example Requests:**

Get all offices:
```bash
curl -X GET "http://localhost:8000/api/v1/offices"
```

Get offices for a specific sub-unit:
```bash
curl -X GET "http://localhost:8000/api/v1/offices?sub_unit_id=1&per_page=20"
```

---

### Get Office by ID

**Endpoint:** `GET /offices/{id}`

**Path Parameters:**
- `id` (required) - Office ID

---

### Create Office

**Endpoint:** `POST /offices`

**Request Body:**
```json
{
  "category_id": 1,
  "unit_id": 1,
  "sub_unit_id": 1,
  "name": "Headquarters",
  "abreviation": "HQ",
  "description": "Main Office",
  "icon": "icon-hq.png"
}
```

**Validation Rules:**
- `category_id` (required, integer, must exist in pn_categories)
- `unit_id` (required, integer, must exist in pn_units)
- `sub_unit_id` (optional, integer, must exist in pn_sub_units)
- `name` (required, string, max: 255)
- `abreviation` (optional, string, max: 50)
- `description` (optional, string)
- `icon` (optional, string)

---

### Update Office

**Endpoint:** `PUT/PATCH /offices/{id}`

**Path Parameters:**
- `id` (required) - Office ID

---

### Delete Office

**Endpoint:** `DELETE /offices/{id}`

**Path Parameters:**
- `id` (required) - Office ID

---

## Sub-offices API

### Get All Sub-offices (with optional office filter)

**Endpoint:** `GET /sub-offices`

**Query Parameters:**
- `office_id` (optional) - Filter sub-offices by office ID
- `per_page` (optional, default: 15) - Number of records per page

**Example Requests:**

Get all sub-offices:
```bash
curl -X GET "http://localhost:8000/api/v1/sub-offices"
```

Get sub-offices for a specific office:
```bash
curl -X GET "http://localhost:8000/api/v1/sub-offices?office_id=1&per_page=20"
```

---

### Get Sub-office by ID

**Endpoint:** `GET /sub-offices/{id}`

**Path Parameters:**
- `id` (required) - Sub-office ID

---

### Create Sub-office

**Endpoint:** `POST /sub-offices`

**Request Body:**
```json
{
  "category_id": 1,
  "unit_id": 1,
  "sub_unit_id": 1,
  "office_id": 1,
  "name": "Personnel Department",
  "abreviation": "PD",
  "description": "Personnel Sub-office",
  "icon": "icon-personnel.png"
}
```

**Validation Rules:**
- `category_id` (required, integer, must exist in pn_categories)
- `unit_id` (required, integer, must exist in pn_units)
- `sub_unit_id` (optional, integer, must exist in pn_sub_units)
- `office_id` (optional, integer, must exist in pn_offices)
- `name` (required, string, max: 255)
- `abreviation` (optional, string, max: 50)
- `description` (optional, string)
- `icon` (optional, string)

---

### Update Sub-office

**Endpoint:** `PUT/PATCH /sub-offices/{id}`

**Path Parameters:**
- `id` (required) - Sub-office ID

---

### Delete Sub-office

**Endpoint:** `DELETE /sub-offices/{id}`

**Path Parameters:**
- `id` (required) - Sub-office ID

---

## Error Handling

### Common Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 400 | Bad Request | Invalid request format |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Internal Server Error | Server error |

### Validation Error Response (422)

```json
{
  "status": "error",
  "message": "Validation failed",
  "error": {
    "name": [
      "The name field is required."
    ],
    "category_id": [
      "The category_id must be an integer."
    ]
  }
}
```

---

## Hierarchical Filtering Examples

### Example 1: Get all units in a category and their sub-units

```bash
# Step 1: Get units for category 1
curl -X GET "http://localhost:8000/api/v1/units?category_id=1"

# Step 2: Use unit ID (e.g., 1) to get sub-units
curl -X GET "http://localhost:8000/api/v1/sub-units?unit_id=1"

# Step 3: Use sub-unit ID (e.g., 1) to get offices
curl -X GET "http://localhost:8000/api/v1/offices?sub_unit_id=1"

# Step 4: Use office ID (e.g., 1) to get sub-offices
curl -X GET "http://localhost:8000/api/v1/sub-offices?office_id=1"
```

### Example 2: JavaScript/Frontend Implementation

```javascript
async function getHierarchicalData(categoryId) {
  try {
    // Get units for category
    const unitsRes = await fetch(`/api/v1/units?category_id=${categoryId}`);
    const unitsData = await unitsRes.json();
    
    // Get sub-units for first unit
    const firstUnitId = unitsData.data.data[0].id;
    const subUnitsRes = await fetch(`/api/v1/sub-units?unit_id=${firstUnitId}`);
    const subUnitsData = await subUnitsRes.json();
    
    // Get offices for first sub-unit
    const firstSubUnitId = subUnitsData.data.data[0].id;
    const officesRes = await fetch(`/api/v1/offices?sub_unit_id=${firstSubUnitId}`);
    const officesData = await officesRes.json();
    
    console.log('Units:', unitsData.data);
    console.log('Sub-Units:', subUnitsData.data);
    console.log('Offices:', officesData.data);
  } catch (error) {
    console.error('Error fetching hierarchical data:', error);
  }
}
```

---

## HTTP Methods

| Method | Purpose |
|--------|---------|
| GET | Retrieve resource(s) |
| POST | Create a new resource |
| PUT | Replace entire resource |
| PATCH | Partial update of resource |
| DELETE | Delete resource |

---

## Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 400 | Bad Request - Invalid request |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Internal Server Error |

---

## Pagination

All list endpoints support pagination:

```bash
curl -X GET "http://localhost:8000/api/v1/categories?per_page=25&page=2"
```

**Pagination Response:**
```json
{
  "data": [...],
  "total": 100,
  "per_page": 25,
  "current_page": 2,
  "last_page": 4,
  "from": 26,
  "to": 50
}
```

---

## Authentication

Currently, these endpoints are public. Add authentication middleware if needed:

```php
Route::middleware('auth:sanctum')->group(function () {
    // Protected routes
});
```

---

## Users API

### Get All Users (with optional filtering)

**Endpoint:** `GET /users`

**Query Parameters:**
- `search` (optional) - Search by name or username
- `category_id` (optional) - Filter users by category
- `unit_id` (optional) - Filter users by unit
- `sub_unit_id` (optional) - Filter users by sub-unit
- `office_id` (optional) - Filter users by office
- `sub_office_id` (optional) - Filter users by sub-office
- `per_page` (optional, default: 15) - Number of records per page

**Example Requests:**

Get all users:
```bash
curl -X GET "http://localhost:8000/api/v1/users"
```

Search users:
```bash
curl -X GET "http://localhost:8000/api/v1/users?search=john"
```

Get users for a specific category:
```bash
curl -X GET "http://localhost:8000/api/v1/users?category_id=1"
```

Get users for a specific unit:
```bash
curl -X GET "http://localhost:8000/api/v1/users?unit_id=1"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Users retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "category_id": 1,
        "unit_id": 1,
        "sub_unit_id": 1,
        "office_id": 1,
        "sub_office_id": 1,
        "rank": "Captain",
        "name": "John Doe",
        "username": "john_doe",
        "created_at": "2024-06-12T10:00:00Z",
        "updated_at": "2024-06-12T10:00:00Z"
      }
    ],
    "total": 50,
    "per_page": 15,
    "current_page": 1
  }
}
```

---

### Get User by ID

**Endpoint:** `GET /users/{id}`

**Path Parameters:**
- `id` (required) - User ID

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/users/1"
```

---

### Create User

**Endpoint:** `POST /users`

**Request Body:**
```json
{
  "category_id": 1,
  "unit_id": 1,
  "sub_unit_id": 1,
  "office_id": 1,
  "sub_office_id": 1,
  "rank": "Captain",
  "name": "John Doe",
  "username": "john_doe",
  "password": "securepassword123"
}
```

**Validation Rules:**
- `category_id` (required, integer, must exist in pn_categories)
- `unit_id` (required, integer, must exist in pn_units)
- `sub_unit_id` (optional, integer, must exist in pn_sub_units)
- `office_id` (optional, integer, must exist in pn_offices)
- `sub_office_id` (optional, integer, must exist in pn_sub_offices)
- `rank` (optional, string, max: 255)
- `name` (required, string, max: 255)
- `username` (required, string, max: 255, unique)
- `password` (required, string, min: 8)

**Example Response (201 Created):**
```json
{
  "status": "success",
  "message": "User created successfully",
  "data": {
    "id": 50,
    "category_id": 1,
    "unit_id": 1,
    "sub_unit_id": 1,
    "office_id": 1,
    "sub_office_id": 1,
    "rank": "Captain",
    "name": "John Doe",
    "username": "john_doe",
    "created_at": "2024-06-12T15:30:00Z",
    "updated_at": "2024-06-12T15:30:00Z"
  }
}
```

---

### Update User

**Endpoint:** `PUT/PATCH /users/{id}`

**Path Parameters:**
- `id` (required) - User ID

**Request Body:**
```json
{
  "name": "Jane Doe",
  "rank": "Major",
  "password": "newpassword123"
}
```

**Note:** Password will be automatically hashed before storage.

**Example Response (200 OK):**
```json
{
  "status": "success",
  "message": "User updated successfully",
  "data": null
}
```

---

### Delete User

**Endpoint:** `DELETE /users/{id}`

**Path Parameters:**
- `id` (required) - User ID

**Example Response (200 OK):**
```json
{
  "status": "success",
  "message": "User deleted successfully",
  "data": null
}
```

---

### Get Users by Rank

**Endpoint:** `GET /users/rank/{rank}`

**Path Parameters:**
- `rank` (required) - User rank (e.g., Captain, Major, etc.)

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/users/rank/Captain"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Users with rank 'Captain' retrieved successfully",
  "data": [
    {
      "id": 1,
      "rank": "Captain",
      "name": "John Doe",
      "username": "john_doe"
    }
  ]
}
```

---

### Search Users

**Endpoint:** `GET /users/search`

**Query Parameters:**
- `q` (required) - Search query (searches in name and username)
- `per_page` (optional, default: 15) - Number of records per page

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/v1/users/search?q=john&per_page=20"
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Search results retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "username": "john_doe",
        "rank": "Captain"
      },
      {
        "id": 5,
        "name": "Johnny Smith",
        "username": "johnny_smith",
        "rank": "Lieutenant"
      }
    ],
    "total": 2,
    "per_page": 20,
    "current_page": 1
  }
}
```

---

## Rate Limiting

Consider implementing rate limiting for production:

```php
Route::middleware('throttle:60,1')->group(function () {
    // Rate-limited routes
});
```

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2024-06-12 | Initial release with all endpoints |

---

## Support

For issues or questions, contact the development team.
