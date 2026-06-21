# Report Personnel - Office Filtering Guide

## Overview
The Report Personnel API now supports filtering organization items by **specific office**. Instead of saving all items from an organization structure, you can now save only items belonging to a specific office.

---

## How It Works

### Before (All Items Saved)
- Request creates report with unit_id and sub_unit_id
- **ALL items** from the organization are saved (including multiple offices)
- `required` count = total items in organization

### After (Filtered by Office)
- Request includes `officeName` parameter specifying which office to save
- **ONLY items for that office** are saved
- `required` count = items in selected office only

---

## Usage Examples

### Example 1: Create Report for "OFFICE OF THE COMMANDER"

**Endpoint:** `POST /api/v1/report/personnels`

**Request Payload:**
```json
{
  "category_id": 1,
  "unit_id": 2,
  "sub_unit_id": 7,
  "officeName": "OFFICE OF THE COMMANDER",
  "report_month": "2024-06"
}
```

**What Gets Saved:**
- Only items with `officeName: "OFFICE OF THE COMMANDER"`
- Items IDs 1-12 (Office header + 11 personnel positions)
- `required` = 12 (sum of required from these items)
- `actual` = 0 (to be filled during assessment)

**Response:**
```json
{
  "status": "success",
  "message": "Report created successfully",
  "data": {
    "id": 1,
    "category_id": 1,
    "unit_id": 2,
    "sub_unit_id": 7,
    "report_month": "2024-06",
    "items": [
      {
        "id": 1,
        "description": "OFFICE OF THE COMMANDER",
        "office": true,
        "officeName": "OFFICE OF THE COMMANDER",
        "required": ""
      },
      {
        "id": 2,
        "description": "COMMANDER",
        "grade": "O8",
        "afpos": "SUW",
        "required": "1",
        "office": false,
        "officeName": "OFFICE OF THE COMMANDER"
      },
      ...
      {
        "id": 12,
        "description": "Sub - Total",
        "required": "12",
        "office": false,
        "officeName": "OFFICE OF THE COMMANDER"
      }
    ],
    "required": 12,
    "actual": 0,
    "grade_points": 0,
    "afpos_points": 0,
    "created_at": "2024-06-21T10:30:00Z"
  }
}
```

---

### Example 2: Create Report for "OFFICE OF THE DEPUTY COMMANDER"

**Endpoint:** `POST /api/v1/report/personnels`

**Request Payload:**
```json
{
  "category_id": 1,
  "unit_id": 2,
  "sub_unit_id": 7,
  "officeName": "OFFICE OF THE DEPUTY COMMANDER",
  "report_month": "2024-06"
}
```

**What Gets Saved:**
- Only items with `officeName: "OFFICE OF THE DEPUTY COMMANDER"`
- All items under Deputy Commander office (much larger)
- `required` = sum of all required positions (likely 200+)
- Separate report from "OFFICE OF THE COMMANDER"

---

### Example 3: Auto-Select First Office (No officeName Specified)

**Endpoint:** `POST /api/v1/report/personnels`

**Request Payload:**
```json
{
  "category_id": 1,
  "unit_id": 2,
  "sub_unit_id": 7,
  "report_month": "2024-06"
}
```

**What Gets Saved:**
- If `officeName` is NOT provided, uses **first office** in organization
- First office = "OFFICE OF THE COMMANDER"
- Same as Example 1

---

## Valid Office Names

These office names exist in the organization item structure:

1. **OFFICE OF THE COMMANDER**
   - Personnel items: 11 positions
   - Sub-total required: 12

2. **OFFICE OF THE DEPUTY COMMANDER**
   - Personnel items: 200+ positions
   - Includes multiple branches and sections

---

## Request Parameters

| Parameter | Type | Required | Description |
|---|---|---|---|
| `category_id` | integer | No | Organization category ID |
| `unit_id` | integer | **Yes** | Unit ID (must exist) |
| `sub_unit_id` | integer | No | Sub-unit ID (must exist if provided) |
| `office_id` | integer | No | Office ID for hierarchical filtering |
| `sub_office_id` | integer | No | Sub-office ID for hierarchical filtering |
| `officeName` | string | No | **NEW** - Office name to filter items by. If not provided, uses first office |
| `report_month` | string | **Yes** | Report month (e.g., "2024-06") |

---

## Filtering Logic

### How officeName Filtering Works

1. **Retrieve organization** by unit_id + sub_unit_id filters
2. **Parse items** JSON from organization
3. **Find office header** where `office === true` and `officeName === requested`
4. **Collect items** starting from header until next office header or end of array
5. **Sum required** from all collected items
6. **Save filtered items** to report

### Example Algorithm (Pseudocode)

```php
function filterItemsByOffice(items, officeName):
    filtered = []
    capture = false
    
    for each item in items:
        // Check for our target office header
        if item.office === true AND item.officeName === officeName:
            capture = true
            filtered.append(item)
            continue
        
        // Stop if we hit another office header
        if capture AND item.office === true AND item.officeName !== officeName:
            break
        
        // Add items if we're in capture mode
        if capture:
            filtered.append(item)
    
    return filtered
```

---

## Response Structure

```json
{
  "status": "success|error",
  "message": "Description of the operation",
  "data": {
    "id": 1,
    "category_id": 1,
    "unit_id": 2,
    "sub_unit_id": 7,
    "office_id": null,
    "sub_office_id": null,
    "report_month": "2024-06",
    "items": [ /* filtered items array */ ],
    "result": null,
    "required": 12,         // Sum of required from filtered items
    "actual": 0,            // To be updated during assessment
    "grade_points": 0,      // Calculated during grading
    "afpos_points": 0,      // Calculated during grading
    "created_at": "2024-06-21T10:30:00Z",
    "updated_at": "2024-06-21T10:30:00Z"
  }
}
```

---

## Multiple Reports per Organization

You can now create **separate reports for each office** in the same organization:

```bash
# Report 1: OFFICE OF THE COMMANDER
POST /api/v1/report/personnels
{
  "category_id": 1,
  "unit_id": 2,
  "sub_unit_id": 7,
  "officeName": "OFFICE OF THE COMMANDER",
  "report_month": "2024-06"
}
# Returns: Report ID 1, required: 12

# Report 2: OFFICE OF THE DEPUTY COMMANDER
POST /api/v1/report/personnels
{
  "category_id": 1,
  "unit_id": 2,
  "sub_unit_id": 7,
  "officeName": "OFFICE OF THE DEPUTY COMMANDER",
  "report_month": "2024-06"
}
# Returns: Report ID 2, required: 200+
```

Now you have **2 separate reports** for the same organization, one per office.

---

## Error Handling

### Invalid officeName (Not Found)

If `officeName` doesn't exist in organization items, the system:
1. Logs a warning
2. Falls back to **first office**
3. Still creates the report successfully

---

## Benefits

✅ **Organized reports** - Each office tracked separately  
✅ **Accurate tracking** - `required` count reflects actual office positions  
✅ **Flexible** - Can request specific office or auto-select  
✅ **Backward compatible** - Existing code works without changes  
✅ **Efficient** - Smaller JSON payloads per report

---

## Testing Payload (Ready for Postman)

```json
{
  "category_id": 1,
  "unit_id": 2,
  "sub_unit_id": 7,
  "officeName": "OFFICE OF THE COMMANDER",
  "report_month": "2024-06"
}
```

**Expected Result:**
- Report created with items filtered to OFFICE OF THE COMMANDER only
- `required` count = 12
- `actual` count = 0 (ready for assessment)
