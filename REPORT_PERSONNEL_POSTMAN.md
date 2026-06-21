# Report Personnel - Postman Test Payloads

## POST - Create Report Personnel

### URL
```
POST http://localhost:8000/api/v1/report/personnels
```

### Headers
```
Authorization: Bearer {your_sanctum_token}
Content-Type: application/json
Accept: application/json
```

---

## Payload 1: Basic Report (Minimal)

```json
{
  "category_id": 1,
  "unit_id": 1,
  "report_month": "2024-06"
}
```

---

## Payload 2: Report with Sub-Unit & Office

```json
{
  "category_id": 1,
  "unit_id": 1,
  "sub_unit_id": 1,
  "office_id": 1,
  "report_month": "2024-06"
}
```

---

## Payload 3: Complete Report with All Filters

```json
{
  "category_id": 1,
  "unit_id": 1,
  "sub_unit_id": 1,
  "office_id": 1,
  "sub_office_id": 1,
  "report_month": "2024-06"
}
```

---

## Response Example (Success - 201)

```json
{
  "status": true,
  "message": "Report created successfully",
  "data": {
    "id": 1,
    "category_id": 1,
    "unit_id": 1,
    "sub_unit_id": 1,
    "office_id": 1,
    "sub_office_id": 1,
    "items": null,
    "result": null,
    "grade_points": 0,
    "afpos_points": 0,
    "required": 0,
    "actual": 0,
    "report_month": "2024-06",
    "status": 0,
    "created_by": 1,
    "created_at": "2024-06-21T10:30:00.000000Z",
    "updated_at": "2024-06-21T10:30:00.000000Z"
  }
}
```

---

## Response Example (Validation Error - 422)

```json
{
  "status": false,
  "message": {
    "category_id": [
      "The selected category_id is invalid."
    ],
    "unit_id": [
      "The selected unit_id is invalid."
    ],
    "report_month": [
      "The report_month field is required."
    ]
  }
}
```

---

## Important Notes

### Flow/Behavior:
1. **If report exists** for this unit/sub_unit/office/sub_office combination → Copies previous report data (items, result, grade_points, etc.)
2. **If no report exists** → Looks up data from SettingOrganization
3. **If organization has items** → Parses JSON and sets `required` = item count
4. **If nothing found** → Creates empty report with 0 values

### Field Rules:
- `category_id` - **required**, must exist in `pn_categories` table
- `unit_id` - **required**, must exist in `pn_units` table
- `sub_unit_id` - optional, must exist in `pn_sub_units` table (if provided)
- `office_id` - optional, must exist in `pn_offices` table (if provided)
- `sub_office_id` - optional, must exist in `pn_sub_offices` table (if provided)
- `report_month` - **required**, string format (e.g., "2024-06", "2024-06-21")

### Related GET Endpoints (for retrieving IDs):
```
GET /api/v1/categories
GET /api/v1/units
GET /api/v1/sub-units?unit_id={id}
GET /api/v1/offices?sub_unit_id={id}
GET /api/v1/sub-offices?office_id={id}
```

### Other Report Endpoints:
```
GET    /api/v1/report/personnels              - List all reports (paginated)
GET    /api/v1/report/personnels?unit_id=1   - Filter reports
GET    /api/v1/report/personnels/{id}        - Get specific report
PATCH  /api/v1/report/personnels/{id}        - Update report
DELETE /api/v1/report/personnels/{id}        - Delete report
```
