# User API - Postman Sample Payloads

## Base URL
```
http://localhost:8000/api/v1
```

## Password Hashing
✅ **Already Implemented** - Passwords are automatically hashed using `Hash::make()` in the UserService during user creation and updates.

---

## 1. GET - Retrieve All Users (with Pagination)

### Request
```
GET /users
```

### Query Parameters (Optional)
```
?per_page=15
?category_id=1
?unit_id=1
?sub_unit_id=1
?office_id=1
?sub_office_id=1
?search=john
```

### Example with Search
```
GET /users?search=john&per_page=10
```

### Response (Success - 200)
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "category_id": 1,
                "unit_id": 1,
                "sub_unit_id": 1,
                "office_id": 1,
                "sub_office_id": 1,
                "rank": "Officer",
                "name": "John Doe",
                "username": "johndoe",
                "created_at": "2026-06-13T10:30:00.000000Z",
                "updated_at": "2026-06-13T10:30:00.000000Z",
                "category": { "id": 1, "name": "Category 1" },
                "unit": { "id": 1, "name": "Unit 1" },
                "subUnit": { "id": 1, "name": "Sub Unit 1" },
                "office": { "id": 1, "name": "Office 1" },
                "subOffice": { "id": 1, "name": "Sub Office 1" }
            }
        ],
        "last_page": 1,
        "per_page": 15,
        "total": 1
    },
    "message": "Users retrieved successfully"
}
```

---

## 2. GET - Retrieve Single User by ID

### Request
```
GET /users/{id}
```

### Example
```
GET /users/1
```

### Response (Success - 200)
```json
{
    "success": true,
    "data": {
        "id": 1,
        "category_id": 1,
        "unit_id": 1,
        "sub_unit_id": 1,
        "office_id": 1,
        "sub_office_id": 1,
        "rank": "Officer",
        "name": "John Doe",
        "username": "johndoe",
        "created_at": "2026-06-13T10:30:00.000000Z",
        "updated_at": "2026-06-13T10:30:00.000000Z",
        "category": { "id": 1, "name": "Category 1" },
        "unit": { "id": 1, "name": "Unit 1" },
        "subUnit": { "id": 1, "name": "Sub Unit 1" },
        "office": { "id": 1, "name": "Office 1" },
        "subOffice": { "id": 1, "name": "Sub Office 1" }
    },
    "message": "User retrieved successfully"
}
```

### Response (Not Found - 404)
```json
{
    "success": false,
    "message": "User not found"
}
```

---

## 3. POST - Create New User

### Request
```
POST /users
Content-Type: application/json
```

### Request Body (Minimal Required)
```json
{
    "category_id": 1,
    "unit_id": 1,
    "name": "Jane Smith",
    "username": "janesmith",
    "password": "SecurePassword123"
}
```

### Request Body (Complete with All Fields)
```json
{
    "category_id": 1,
    "unit_id": 1,
    "sub_unit_id": 1,
    "office_id": 1,
    "sub_office_id": 1,
    "rank": "Senior Officer",
    "name": "Jane Smith",
    "username": "janesmith",
    "password": "SecurePassword123"
}
```

### Validation Rules
- `category_id` - required, must exist in pn_categories table
- `unit_id` - required, must exist in pn_units table
- `sub_unit_id` - optional, must exist in pn_sub_units table
- `office_id` - optional, must exist in pn_offices table
- `sub_office_id` - optional, must exist in pn_sub_offices table
- `rank` - optional, max 255 characters
- `name` - required, max 255 characters
- `username` - required, unique, max 255 characters
- `password` - required, minimum 8 characters (will be automatically hashed)

### Response (Success - 201)
```json
{
    "success": true,
    "data": {
        "id": 2,
        "category_id": 1,
        "unit_id": 1,
        "sub_unit_id": 1,
        "office_id": 1,
        "sub_office_id": 1,
        "rank": "Senior Officer",
        "name": "Jane Smith",
        "username": "janesmith",
        "created_at": "2026-06-13T11:00:00.000000Z",
        "updated_at": "2026-06-13T11:00:00.000000Z",
        "category": { "id": 1, "name": "Category 1" },
        "unit": { "id": 1, "name": "Unit 1" },
        "subUnit": { "id": 1, "name": "Sub Unit 1" },
        "office": { "id": 1, "name": "Office 1" },
        "subOffice": { "id": 1, "name": "Sub Office 1" }
    },
    "message": "User created successfully"
}
```

### Response (Validation Error - 422)
```json
{
    "success": false,
    "message": {
        "username": [
            "The username has already been taken."
        ],
        "password": [
            "The password must be at least 8 characters."
        ]
    }
}
```

---

## 4. PUT - Update User

### Request
```
PUT /users/{id}
Content-Type: application/json
```

### Example URL
```
PUT /users/1
```

### Request Body (Update Password Only)
```json
{
    "password": "NewSecurePassword456"
}
```

### Request Body (Update Multiple Fields)
```json
{
    "rank": "Chief Officer",
    "name": "John Doe Updated",
    "username": "johndoe_updated",
    "office_id": 2,
    "sub_office_id": 2
}
```

### Request Body (Update Password and Other Fields)
```json
{
    "name": "Jane Smith Updated",
    "rank": "Senior Officer",
    "category_id": 2,
    "unit_id": 2,
    "password": "NewSecurePassword789"
}
```

### Validation Rules (All fields are optional for updates)
- `category_id` - must exist in pn_categories table
- `unit_id` - must exist in pn_units table
- `sub_unit_id` - must exist in pn_sub_units table
- `office_id` - must exist in pn_offices table
- `sub_office_id` - must exist in pn_sub_offices table
- `rank` - max 255 characters
- `name` - max 255 characters
- `username` - must be unique (excluding current user), max 255 characters
- `password` - minimum 8 characters (will be automatically hashed)

### Response (Success - 200)
```json
{
    "success": true,
    "data": null,
    "message": "User updated successfully"
}
```

### Response (User Not Found - 404)
```json
{
    "success": false,
    "message": "User not found"
}
```

### Response (Validation Error - 422)
```json
{
    "success": false,
    "message": {
        "username": [
            "The username must be a string."
        ]
    }
}
```

---

## 5. DELETE - Remove User

### Request
```
DELETE /users/{id}
```

### Example
```
DELETE /users/1
```

### Response (Success - 200)
```json
{
    "success": true,
    "data": null,
    "message": "User deleted successfully"
}
```

### Response (User Not Found - 404)
```json
{
    "success": false,
    "message": "User not found"
}
```

---

## Password Hashing Implementation Details

### In UserService
```php
public function createUser(array $data)
{
    // Hash password before storing
    if (isset($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    }
    return $this->repository->create($data);
}

public function updateUser(int $id, array $data): bool
{
    // Hash password if it's being updated
    if (isset($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    }
    return $this->repository->update($id, $data);
}
```

### In User Model
```php
protected function casts(): array
{
    return [
        'password' => 'hashed',
    ];
}
```

**Note:** Passwords are never returned in API responses (they're hidden in the User model's `$hidden` array).

---

## Additional Endpoints

### Get Users by Rank
```
GET /users/rank/{rank}?per_page=10
```

### Search Users
```
GET /users/search?q=john&per_page=10
```

---

## Tips for Postman Testing

1. **Set Base URL as Postman Variable:**
   - Click "Environments" → "Create new environment"
   - Add variable: `base_url` = `http://localhost:8000/api/v1`
   - Use `{{base_url}}/users` in requests

2. **Common Postman Tests:**
   ```javascript
   // Check status code
   pm.test("Status code is 200", function () {
       pm.response.to.have.status(200);
   });

   // Check response structure
   pm.test("Response has required fields", function () {
       var jsonData = pm.response.json();
       pm.expect(jsonData).to.have.property('success');
       pm.expect(jsonData).to.have.property('data');
       pm.expect(jsonData).to.have.property('message');
   });
   ```

3. **Save Response Values:**
   ```javascript
   var jsonData = pm.response.json();
   pm.environment.set("user_id", jsonData.data.id);
   // Use {{user_id}} in next requests
   ```
