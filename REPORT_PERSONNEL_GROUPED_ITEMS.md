# Report Personnel - Get Items Grouped by Office

## New Endpoint: Get Organization Items Grouped by Office

### URL
```
GET http://localhost:8000/api/v1/report/personnels/grouped-by-office?unit_id=1
```

### Headers
```
Authorization: Bearer {your_sanctum_token}
Content-Type: application/json
Accept: application/json
```

---

## Query Parameters (at least one required)

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `unit_id` | integer | Yes (unless other filters) | Filter by unit ID |
| `sub_unit_id` | integer | No | Filter by sub-unit ID |
| `office_id` | integer | No | Filter by office ID |
| `sub_office_id` | integer | No | Filter by sub-office ID |

---

## Example Requests

### 1. Get Items by Unit ID Only
```
GET http://localhost:8000/api/v1/report/personnels/grouped-by-office?unit_id=1
```

### 2. Get Items by Unit + Sub-Unit
```
GET http://localhost:8000/api/v1/report/personnels/grouped-by-office?unit_id=1&sub_unit_id=1
```

### 3. Get Items by Unit + Office
```
GET http://localhost:8000/api/v1/report/personnels/grouped-by-office?unit_id=1&office_id=1
```

### 4. Get Items with All Filters
```
GET http://localhost:8000/api/v1/report/personnels/grouped-by-office?unit_id=1&sub_unit_id=1&office_id=1&sub_office_id=1
```

---

## Response Example (Success - 200)

```json
{
  "status": true,
  "message": "Items grouped by office retrieved successfully",
  "data": [
    {
      "office_id": 1,
      "office_name": "OFFICE OF THE COMMANDER",
      "items": [
        {
          "id": 1,
          "description": "OFFICE OF THE COMMANDER",
          "grade": "",
          "afpos": "",
          "required": "",
          "office": true,
          "officeName": "OFFICE OF THE COMMANDER"
        },
        {
          "id": 2,
          "description": "COMMANDER",
          "grade": "O8",
          "afpos": "SUW",
          "required": "1",
          "office_id": 1
        },
        {
          "id": 3,
          "description": "FLAG SECRETARY",
          "grade": "O5",
          "afpos": "SUW",
          "required": "1",
          "office_id": 1
        },
        {
          "id": 4,
          "description": "FLAG LIEUTENANT",
          "grade": "O2",
          "afpos": "SUW",
          "required": "1",
          "office_id": 1
        },
        {
          "id": 5,
          "description": "Admin Supervisor",
          "grade": "E6",
          "afpos": "YN",
          "required": "1",
          "office_id": 1
        },
        {
          "id": 6,
          "description": "Admin Specialist",
          "grade": "E2",
          "afpos": "YN",
          "required": "1",
          "office_id": 1
        },
        {
          "id": 7,
          "description": "Admin Apprentice",
          "grade": "E1",
          "afpos": "YN",
          "required": "1",
          "office_id": 1
        },
        {
          "id": 8,
          "description": "Mess Management Specialist",
          "grade": "E3",
          "afpos": "CS",
          "required": "1",
          "office_id": 1
        },
        {
          "id": 9,
          "description": "Mess Management Apprentice",
          "grade": "E1",
          "afpos": "CS",
          "required": "2",
          "office_id": 1
        },
        {
          "id": 10,
          "description": "Security Escort & Vehicle Operator",
          "grade": "E3",
          "afpos": "CD",
          "required": "1",
          "office_id": 1
        },
        {
          "id": 11,
          "description": "Security Escort & Vehicle Operator",
          "grade": "E2",
          "afpos": "CD",
          "required": "2",
          "office_id": 1
        },
        {
          "id": 12,
          "description": "Sub - Total",
          "grade": "",
          "afpos": "",
          "required": "12",
          "office_id": 1
        }
      ]
    },
    {
      "office_id": 2,
      "office_name": "OFFICE OF THE CHIEF OF STAFF",
      "items": [
        {
          "id": 13,
          "description": "OFFICE OF THE CHIEF OF STAFF",
          "grade": "",
          "afpos": "",
          "office": true,
          "officeName": "OFFICE OF THE CHIEF OF STAFF"
        },
        {
          "id": 14,
          "description": "CHIEF OF STAFF",
          "grade": "O7",
          "afpos": "SUW",
          "required": "1",
          "office_id": 2
        },
        {
          "id": 15,
          "description": "Administrtive Apprentice",
          "grade": "E2",
          "afpos": "QM",
          "required": "1",
          "office_id": 2
        },
        {
          "id": 16,
          "description": "Security Escort/Vehicle Operator",
          "grade": "E2",
          "afpos": "CD",
          "required": "1",
          "office_id": 2
        }
      ]
    },
    {
      "office_id": 3,
      "office_name": "SECRETARY OF PHILIPPINE FLEET STAFF",
      "items": [
        {
          "id": 17,
          "description": "SECRETARY OF PHILIPPINE FLEET STAFF",
          "grade": "",
          "afpos": "",
          "office": true,
          "officeName": "SECRETARY OF PHILIPPINE FLEET STAFF"
        },
        {
          "id": 18,
          "description": "SECRETARY OF SURFACE WARFARE STAFF",
          "grade": "O5",
          "afpos": "SUW",
          "required": "1",
          "office_id": 3
        }
      ]
    }
  ]
}
```

---

## Response Example (Not Found - 404)

```json
{
  "status": false,
  "message": "No organization data found for the given filters"
}
```

---

## How It Works

1. **Receives filter parameters** (unit_id, sub_unit_id, office_id, sub_office_id)
2. **Looks up organization** data matching those filters
3. **Groups items** by office_id/office_name
4. **Returns grouped structure** with each office containing its items

This is perfect for:
- ✅ Displaying organization structure by office
- ✅ Building hierarchical UI components
- ✅ Calculating office-level statistics
- ✅ Personnel assignment by office
- ✅ Report generation by office

---

## Integration with Frontend

### Example - Display Offices and Personnel:

```javascript
// Get grouped items
const response = await fetch('/api/v1/report/personnels/grouped-by-office?unit_id=1');
const data = await response.json();

// Display each office
data.data.forEach(office => {
  console.log(`\n${office.office_name}`);
  console.log(`Count: ${office.items.length}`);
  office.items.forEach(item => {
    console.log(`  - ${item.description} (${item.grade} ${item.afpos})`);
  });
});
```

Output:
```
OFFICE OF THE COMMANDER
Count: 12
  - OFFICE OF THE COMMANDER
  - COMMANDER (O8 SUW)
  - FLAG SECRETARY (O5 SUW)
  - FLAG LIEUTENANT (O2 SUW)
  - Admin Supervisor (E6 YN)
  - Admin Specialist (E2 YN)
  - Admin Apprentice (E1 YN)
  - Mess Management Specialist (E3 CS)
  - Mess Management Apprentice (E1 CS)
  - Security Escort & Vehicle Operator (E3 CD)
  - Security Escort & Vehicle Operator (E2 CD)
  - Sub - Total

OFFICE OF THE CHIEF OF STAFF
Count: 4
  - OFFICE OF THE CHIEF OF STAFF
  - CHIEF OF STAFF (O7 SUW)
  - Administrtive Apprentice (E2 QM)
  - Security Escort/Vehicle Operator (E2 CD)
```
