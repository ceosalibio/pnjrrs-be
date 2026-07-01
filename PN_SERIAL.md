# PnSerial Documentation

## Overview

`PnSerial` is a model that tracks serial numbers for personnel reports within the PNJRRS system. Each serial record represents a personnel item that is associated with a report in a specific month. The model maintains relationships with personnel reports, categories, units, offices, and ranks.

---

## Model Structure

### Model Class
- **Namespace:** `App\Models\PnSerial`
- **Table:** `pn_serials`
- **Uses:** `HasFactory`, `SoftDeletes`

### Fillable Fields

```php
protected $fillable = [
    'personnel_report_id',    // Foreign key to report_personnel
    'category_id',            // Category reference
    'unit_id',                // Unit reference
    'sub_unit_id',            // Sub-unit reference (nullable)
    'office_id',              // Office reference (nullable)
    'sub_office_id',          // Sub-office reference (nullable)
    'rank_id',                // Rank reference (nullable)
    'serial',                 // Serial number/identifier (indexed)
    'name',                   // Name/description (indexed, nullable)
    'report_month',           // Report month in format YYYY-MM (indexed)
];
```

---

## Database Schema

### Table: `pn_serials`

| Column | Type | Attributes | Notes |
|--------|------|-----------|-------|
| `id` | bigint | Primary Key | Auto-increment |
| `personnel_report_id` | bigint unsigned | Foreign Key, Indexed | References `report_personnel.id` |
| `category_id` | bigint unsigned | Foreign Key, Indexed | References `pn_categories.id` |
| `unit_id` | bigint unsigned | Foreign Key, Indexed | References `pn_units.id` |
| `sub_unit_id` | bigint unsigned | Nullable, Indexed | References `pn_sub_units.id` |
| `office_id` | bigint unsigned | Nullable, Indexed | References `pn_offices.id` |
| `sub_office_id` | bigint unsigned | Nullable, Indexed | References `pn_sub_offices.id` |
| `rank_id` | bigint unsigned | Nullable, Indexed | References `item_ranks.id` |
| `serial` | varchar(255) | Nullable, Indexed | Unique identifier/serial number |
| `name` | varchar(255) | Nullable, Indexed | Name or description |
| `report_month` | varchar(7) | Indexed | Format: YYYY-MM |
| `created_at` | timestamp | | Creation timestamp |
| `updated_at` | timestamp | | Update timestamp |
| `deleted_at` | timestamp | Nullable | Soft delete timestamp |

---

## Relationships

```php
// Get the personnel report associated with this serial
$serial->personnelReport();  // BelongsTo ReportPersonnel

// Get the category associated with this serial
$serial->category();         // BelongsTo PnCategory

// Get the unit associated with this serial
$serial->unit();            // BelongsTo PnUnit

// Get the sub unit associated with this serial
$serial->subUnit();         // BelongsTo PnSubUnit

// Get the office associated with this serial
$serial->office();          // BelongsTo PnOffice

// Get the sub office associated with this serial
$serial->subOffice();       // BelongsTo PnSubOffice

// Get the rank associated with this serial
$serial->rank();            // BelongsTo ItemRank
```

---

## Service Methods (PnSerialService)

### Retrieval Methods

#### `getAllSerials()`
Returns all serial records as a collection.

```php
$service->getAllSerials();
// Returns: Illuminate\Database\Eloquent\Collection
```

#### `getSerialById(int $id)`
Retrieves a single serial record by ID.

```php
$serial = $service->getSerialById(1);
// Returns: PnSerial|null
```

#### `getPaginatedSerials(int $perPage = 15)`
Returns paginated serial records.

```php
$paginated = $service->getPaginatedSerials(20);
// Returns: LengthAwarePaginator
```

#### `getSerialsByReportMonth(string $reportMonth)`
Retrieves all serials for a specific report month (format: YYYY-MM).

```php
$serials = $service->getSerialsByReportMonth('2024-06');
// Returns: Collection of PnSerial records
```

#### `getSerialsByPersonnelReportId(int $personnelReportId)`
Retrieves all serials associated with a specific personnel report.

```php
$serials = $service->getSerialsByPersonnelReportId(5);
// Returns: Collection of PnSerial records
```

### Create/Update Methods

#### `createSerial(array $data)`
Creates a new serial record.

```php
$serial = $service->createSerial([
    'personnel_report_id' => 1,
    'category_id' => 2,
    'unit_id' => 3,
    'sub_unit_id' => 4,
    'office_id' => 5,
    'sub_office_id' => 6,
    'rank_id' => 7,
    'serial' => 'SER-2024-001',
    'name' => 'Personnel Item Name',
    'report_month' => '2024-06',
]);
// Returns: PnSerial (newly created)
```

#### `updateSerial(int $id, array $data): bool`
Updates an existing serial record.

```php
$success = $service->updateSerial(1, [
    'serial' => 'SER-2024-002',
    'name' => 'Updated Name',
]);
// Returns: bool (true if successful)
```

#### `deleteSerial(int $id): bool`
Soft deletes a serial record.

```php
$success = $service->deleteSerial(1);
// Returns: bool (true if successful)
```

### Validation Methods

#### `isSerialNumberExistsInMonth(string $serialNumber, string $reportMonth, ?int $excludePersonnelReportId = null): bool`
Checks if a serial number already exists in a specific month.

```php
// Check if serial exists in any report for the month
$exists = $service->isSerialNumberExistsInMonth('SEL-2024-001', '2024-06');
// Returns: bool

// Check if exists, excluding a specific personnel report
$exists = $service->isSerialNumberExistsInMonth(
    'SEL-2024-001', 
    '2024-06', 
    5  // excludes personnel_report_id 5
);
// Returns: bool
```

#### `isSerialExistingInReportMonthButDifferentPersonnelReport(string $reportMonth, int $personnelReportId): bool`
Checks if any serial exists in a report month for a different personnel report.

```php
$exists = $service->isSerialExistingInReportMonthButDifferentPersonnelReport(
    '2024-06', 
    5
);
// Returns: bool
```

---

## Repository Methods (PnSerialRepository)

The repository provides direct Eloquent query access:

| Method | Returns | Description |
|--------|---------|-------------|
| `all()` | Collection | Get all serials |
| `findById(int $id)` | PnSerial\|null | Get single serial |
| `create(array $data)` | PnSerial | Create new serial |
| `update(int $id, array $data)` | bool | Update serial |
| `delete(int $id)` | bool | Delete serial |
| `paginate(int $perPage)` | LengthAwarePaginator | Paginated results |
| `findByReportMonth(string $reportMonth)` | Collection | Serials by month |
| `findByPersonnelReportId(int $personnelReportId)` | Collection | Serials by report |

---

## Usage Examples

### Example 1: Create Serial Records from Report Items

```php
// In ReportPersonnelService
private function createSerialForReport($report)
{
    if (!$report || !$report->items) {
        return;
    }

    $items = is_array($report->items) 
        ? $report->items 
        : json_decode($report->items, true);
    
    foreach ($items as $item) {
        if (isset($item['serial']) && !empty($item['serial'])) {
            $this->pnSerialService->createSerial([
                'personnel_report_id' => $report->id,
                'category_id' => $report->category_id,
                'unit_id' => $report->unit_id,
                'sub_unit_id' => $report->sub_unit_id,
                'office_id' => $report->office_id,
                'sub_office_id' => $report->sub_office_id,
                'rank_id' => $item['rank_id'] ?? null,
                'serial' => $item['serial'],
                'name' => $item['name'] ?? null,
                'report_month' => $report->report_month,
            ]);
        }
    }
}
```

### Example 2: Validate Serial Uniqueness

```php
// Check if serial number is unique for the month
if ($service->isSerialNumberExistsInMonth(
    $request->serial, 
    $request->report_month
)) {
    throw new Exception('The serial number already exists for the selected month.');
}
```

### Example 3: Get All Serials for a Report

```php
$personnelReport = ReportPersonnel::find(1);
$serials = $service->getSerialsByPersonnelReportId($personnelReport->id);

foreach ($serials as $serial) {
    echo $serial->serial . " - " . $serial->name;
}
```

### Example 4: Retrieve Serials for a Specific Month

```php
$serials = $service->getSerialsByReportMonth('2024-06');
// Use foreach to process each serial
```

---

## Integration with ReportPersonnel

The `PnSerial` model works in conjunction with the `ReportPersonnel` model. When a report is created or updated:

1. The report's items array contains serial information
2. `createSerialForReport()` is called to create corresponding `PnSerial` records
3. Each item in the report can generate one or more serial records
4. Validation ensures serial uniqueness within the same report month

---

## Important Notes

- **Soft Deletes:** PnSerial uses soft deletes, so deleted records are marked but not permanently removed
- **Serial Uniqueness:** Serial numbers should be unique within the same report month (validated at service level)
- **Report Month Format:** Always use `YYYY-MM` format for report_month (e.g., "2024-06")
- **Nullable Fields:** `sub_unit_id`, `office_id`, `sub_office_id`, `rank_id`, `serial`, and `name` are optional
- **Indexed Fields:** `personnel_report_id`, `category_id`, `unit_id`, `serial`, `name`, and `report_month` are indexed for query performance

---

## API Reference Summary

| Operation | Method | Returns |
|-----------|--------|---------|
| Create | `createSerial(array)` | PnSerial |
| Read | `getSerialById(int)` | PnSerial\|null |
| Read All | `getAllSerials()` | Collection |
| Update | `updateSerial(int, array)` | bool |
| Delete | `deleteSerial(int)` | bool |
| List Paginated | `getPaginatedSerials(int)` | LengthAwarePaginator |
| Filter by Month | `getSerialsByReportMonth(string)` | Collection |
| Filter by Report | `getSerialsByPersonnelReportId(int)` | Collection |
| Validate Unique | `isSerialNumberExistsInMonth(string, string, ?int)` | bool |
| Check Cross-Report | `isSerialExistingInReportMonthButDifferentPersonnelReport(string, int)` | bool |

---

*Last Updated: June 30, 2026*
