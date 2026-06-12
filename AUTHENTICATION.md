# Authentication API Documentation

## Overview
The API now includes Sanctum-based authentication with daily login/logout logging to text files.

## Endpoints

### 1. Login (Public)
**Endpoint:** `POST /api/v1/login`

**Request Body:**
```json
{
    "username": "string",
    "password": "string"
}
```

**Success Response (200):**
```json
{
    "status": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "username": "john_doe",
            "rank": "Officer"
        },
        "token": "1|abc123...xyz789"
    }
}
```

**Error Response (422):**
```json
{
    "status": false,
    "message": "Validation failed",
    "errors": {
        "username": ["The provided credentials are incorrect."]
    }
}
```

**Example cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "john_doe",
    "password": "password123"
  }'
```

---

### 2. Logout (Protected)
**Endpoint:** `POST /api/v1/logout`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Success Response (200):**
```json
{
    "status": true,
    "message": "Logout successful",
    "data": {}
}
```

**Error Response (401):**
```json
{
    "status": false,
    "message": "Logout failed",
    "errors": {
        "error": "User not authenticated"
    }
}
```

**Example cURL:**
```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

---

### 3. Get Current User (Protected)
**Endpoint:** `GET /api/v1/me`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Success Response (200):**
```json
{
    "status": true,
    "message": "User details retrieved",
    "data": {
        "id": 1,
        "name": "John Doe",
        "username": "john_doe",
        "rank": "Officer",
        "category": {...},
        "unit": {...},
        "subUnit": {...},
        "office": {...},
        "subOffice": {...}
    }
}
```

**Example cURL:**
```bash
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

---

## Authentication Flow

### Step 1: Login
```
POST /api/v1/login
{
    "username": "john_doe",
    "password": "password123"
}
```

Save the returned `token` from the response.

### Step 2: Use Token for Protected Endpoints
Include the token in the `Authorization` header for all protected endpoints:
```
Authorization: Bearer {token}
```

### Step 3: Logout
```
POST /api/v1/logout
Authorization: Bearer {token}
```

---

## Daily Logging

All login and logout activities are logged to daily text files in:
```
storage/logs/auth/auth_YYYY-MM-DD.txt
```

### Log Entry Format
```
[YYYY-MM-DD HH:MM:SS] ACTION: LOGIN | USERNAME: john_doe | STATUS: SUCCESS | MESSAGE: Login successful | IP: 192.168.1.1 | USER-AGENT: Mozilla/5.0...
```

### What Gets Logged
- **Timestamp:** Exact date and time of login/logout
- **Action:** LOGIN or LOGOUT
- **Username:** Who performed the action
- **Status:** SUCCESS or FAILED
- **Message:** Reason or result
- **IP Address:** Client IP address
- **User-Agent:** Browser/client information

### Log File Examples
- `storage/logs/auth/auth_2026-06-12.txt` - Today's logs
- `storage/logs/auth/auth_2026-06-11.txt` - Yesterday's logs

---

## Protected Routes

The following routes now require authentication:

```
POST   /api/v1/logout
GET    /api/v1/me
GET    /api/v1/categories
POST   /api/v1/categories
GET    /api/v1/categories/{id}
PUT    /api/v1/categories/{id}
DELETE /api/v1/categories/{id}

GET    /api/v1/units
POST   /api/v1/units
GET    /api/v1/units/{id}
PUT    /api/v1/units/{id}
DELETE /api/v1/units/{id}

GET    /api/v1/sub-units
POST   /api/v1/sub-units
GET    /api/v1/sub-units/{id}
PUT    /api/v1/sub-units/{id}
DELETE /api/v1/sub-units/{id}

GET    /api/v1/offices
POST   /api/v1/offices
GET    /api/v1/offices/{id}
PUT    /api/v1/offices/{id}
DELETE /api/v1/offices/{id}

GET    /api/v1/sub-offices
POST   /api/v1/sub-offices
GET    /api/v1/sub-offices/{id}
PUT    /api/v1/sub-offices/{id}
DELETE /api/v1/sub-offices/{id}

GET    /api/v1/users
POST   /api/v1/users
GET    /api/v1/users/{id}
PUT    /api/v1/users/{id}
DELETE /api/v1/users/{id}
GET    /api/v1/users/rank/{rank}
GET    /api/v1/users/search
```

---

## Frontend Implementation (JavaScript)

### Login
```javascript
async function login(username, password) {
    const response = await fetch('http://localhost:8000/api/v1/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: username,
            password: password
        })
    });

    const data = await response.json();
    
    if (data.status) {
        // Save token to localStorage
        localStorage.setItem('auth_token', data.data.token);
        return data.data.user;
    } else {
        throw new Error(data.message);
    }
}
```

### Protected Request
```javascript
async function getAuthenticatedData(endpoint) {
    const token = localStorage.getItem('auth_token');
    
    const response = await fetch(endpoint, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });
    
    return await response.json();
}
```

### Logout
```javascript
async function logout() {
    const token = localStorage.getItem('auth_token');
    
    const response = await fetch('http://localhost:8000/api/v1/logout', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });

    const data = await response.json();
    
    if (data.status) {
        // Clear token from localStorage
        localStorage.removeItem('auth_token');
        // Redirect to login page
        window.location.href = '/login';
    }
}
```

---

## HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 401 | Unauthorized - Missing or invalid token |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Internal Server Error - Server error |

---

## Notes

1. **Token Storage:** Keep tokens secure. Use httpOnly cookies or secure localStorage.
2. **Token Expiration:** Tokens can be configured to expire. Check Laravel Sanctum documentation.
3. **Daily Logs:** Logs are automatically organized by date for easy access and archiving.
4. **IP Tracking:** All login/logout attempts are logged with IP address for security auditing.
