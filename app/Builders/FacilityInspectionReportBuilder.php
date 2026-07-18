<?php

namespace App\Builders;

class FacilityInspectionReportBuilder
{
    /**
     * Build facilities items based on unit ID
     */
    public static function buildFacilitiesItems(int $unit_id): array
    {
        $facilities = [
            [
                'category' => 'Operational Facilities',
                'required_area_sqm' => 0,
                'actual_area_sqm' => 0,
                'quantitative_percentage' => 0,
                'qualitative_percentage' => 0,
                'average_percentage' => 0,
                'building_physical_inspection_report' => self::buildBuildingPhysicalInspectionReport(),
            ],
            [
                'category' => 'Operational Support Facilities',
                'required_area_sqm' => 0,
                'actual_area_sqm' => 0,
                'quantitative_percentage' => 0,
                'qualitative_percentage' => 0,
                'average_percentage' => 0,
                'building_physical_inspection_report' => self::buildBuildingPhysicalInspectionReport(),
            ],
        ];

        // Add conditional facilities for unit_id = 5
        if ($unit_id == 5) {
            $facilities[] = [
                'category' => 'Base Facilities',
                'required_area_sqm' => 0,
                'actual_area_sqm' => 0,
                'quantitative_percentage' => 0,
                'qualitative_percentage' => 0,
                'average_percentage' => 0,
                'building_physical_inspection_report' => self::buildBuildingPhysicalInspectionReport(),
            ];
            $facilities[] = [
                'category' => 'Community Facilities',
                'required_area_sqm' => 0,
                'actual_area_sqm' => 0,
                'quantitative_percentage' => 0,
                'qualitative_percentage' => 0,
                'average_percentage' => 0,
                'building_physical_inspection_report' => self::buildBuildingPhysicalInspectionReport(),
            ];
        }

        return $facilities;
    }

    /**
     * Build the building physical inspection report with all categories and rating options
     */
    public static function buildBuildingPhysicalInspectionReport(): array
    {
        return [
            'categories' => [
                [
                    'category_id' => 1,
                    'category_name' => 'Physical Appearance',
                    'weight_percentage' => null,
                    'items' => [
                        [
                            'item_name' => 'Building Interior',
                            'weight_percentage' => 15,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Excellent condition', 'numerical_rating' => 15, 'description' => 'Organized, clean, no damage on wall, roof eaves and gutter, no congestion, well-maintained paint'],
                                ['adjectival_rating' => 'Very good condition', 'numerical_rating' => [13, 14], 'description' => 'Minor clutter, minimal wear, generally clean roof eaves and gutter, generally clean, well-maintained paint'],
                                ['adjectival_rating' => 'Good condition', 'numerical_rating' => [10, 11, 12], 'description' => 'Moderate clutter, noticeable wear and tear on wall, roof eaves and gutter, minimal paint discoloration'],
                                ['adjectival_rating' => 'Fair condition', 'numerical_rating' => [8, 9], 'description' => 'Disorganized, significant damage on wall, roof eaves and gutter, minor paint discoloration'],
                                ['adjectival_rating' => 'Poor condition', 'numerical_rating' => [4, 5, 6, 7], 'description' => 'Requires minor repair and repainting'],
                                ['adjectival_rating' => 'Very Poor condition', 'numerical_rating' => [1, 2, 3], 'description' => 'Dilapidated, Requires major repair and repainting'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                        [
                            'item_name' => 'Building Exterior',
                            'weight_percentage' => 15,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Excellent condition', 'numerical_rating' => 15, 'description' => 'Organized, clean, no damage on wall, roof eaves and gutter, no congestion, well-maintained paint'],
                                ['adjectival_rating' => 'Very good condition', 'numerical_rating' => [13, 14], 'description' => 'Minor clutter, minimal wear, generally clean roof eaves and gutter, generally clean, well-maintained paint'],
                                ['adjectival_rating' => 'Good condition', 'numerical_rating' => [10, 11, 12], 'description' => 'Moderate clutter, noticeable wear and tear on wall, roof eaves and gutter, minimal paint discoloration'],
                                ['adjectival_rating' => 'Fair condition', 'numerical_rating' => [8, 9], 'description' => 'Disorganized, significant damage on wall, roof eaves and gutter, minor paint discoloration'],
                                ['adjectival_rating' => 'Poor condition', 'numerical_rating' => [4, 5, 6, 7], 'description' => 'Requires minor repair and repainting'],
                                ['adjectival_rating' => 'Very Poor condition', 'numerical_rating' => [1, 2, 3], 'description' => 'Dilapidated, Requires major repair and repainting'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                    ],
                ],
                [
                    'category_id' => 2,
                    'category_name' => 'Safety',
                    'weight_percentage' => null,
                    'items' => [
                        [
                            'item_name' => 'Structural Safety',
                            'weight_percentage' => 10,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Very High Safety', 'numerical_rating' => 10, 'description' => 'No signs of cracks, exposed reinforcing bars, corrosion, damage, dents, dilapidation'],
                                ['adjectival_rating' => 'High Safety', 'numerical_rating' => [8, 9], 'description' => 'With signs of cracks but not 3mm wide, but no exposed reinforcing bars, corrosion, damage, dents and dilapidation'],
                                ['adjectival_rating' => 'Fairly Safe', 'numerical_rating' => [6, 7], 'description' => 'With signs of cracks but not 3mm wide, with minimal exposed reinforcing bars, corrosion, damage, dents and dilapidation'],
                                ['adjectival_rating' => 'Barely Safe', 'numerical_rating' => 5, 'description' => 'With cracks 3mm wide, with minimal exposed reinforcing bars, corrosion, damage, dents and dilapidation'],
                                ['adjectival_rating' => 'Unsafe', 'numerical_rating' => [3, 4], 'description' => 'With cracks wider than 3mm, exposed reinforcing bars, corrosion, damage, dents, dilapidation can be renovated or rectified'],
                                ['adjectival_rating' => 'Very Unsafe', 'numerical_rating' => [1, 2], 'description' => 'With cracks wider than 3mm, exposed reinforcing bars, corrosion, damage, dents, dilapidation, beyond economical repair and for demolition'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                        [
                            'item_name' => 'Fire Safety',
                            'weight_percentage' => 10,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Very High Safety', 'numerical_rating' => 10, 'description' => 'Serviceable Fire/Emergency Exit. Fire-Fighting Equipment- Properly maintained and serviceable'],
                                ['adjectival_rating' => 'High Safety', 'numerical_rating' => [8, 9], 'description' => 'Serviceable Fire/Emergency Exit. Fire-Fighting Equipment- maintained but not serviceable'],
                                ['adjectival_rating' => 'Fairly Safe', 'numerical_rating' => [6, 7], 'description' => 'Serviceable Fire/Emergency Exit. Fire-Fighting Equipment- not properly maintained and not serviceable'],
                                ['adjectival_rating' => 'Barely Safe', 'numerical_rating' => 5, 'description' => 'Fire/Emergency Exit minimal damage and require minor repairs. Fire-Fighting Equipment- not properly maintained and not serviceable'],
                                ['adjectival_rating' => 'Unsafe', 'numerical_rating' => [3, 4], 'description' => 'Fire/Emergency Exit dilapidated and unserviceable. Fire-Fighting Equipment- not properly maintained and not serviceable'],
                                ['adjectival_rating' => 'Very Unsafe', 'numerical_rating' => [1, 2], 'description' => 'Either No Fire/Emergency Exit, No Fire-Fighting Equipment'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                        [
                            'item_name' => 'Building/Facility Safety',
                            'weight_percentage' => 10,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Very High Safety', 'numerical_rating' => 10, 'description' => 'Doors and Door Locks are properly working, with enhanced additional locking system, Windows and Window Locks are properly working, with enhanced additional locking system'],
                                ['adjectival_rating' => 'High Safety', 'numerical_rating' => [8, 9], 'description' => 'Doors and Door Locks are working with minimal defects, Windows and Window Locks are working with minimal defects'],
                                ['adjectival_rating' => 'Fairly Safe', 'numerical_rating' => [6, 7], 'description' => 'Doors and Door Locks are working. Windows and Window Locks are not working'],
                                ['adjectival_rating' => 'Barely Safe', 'numerical_rating' => 5, 'description' => 'Doors and Door Locks are not working. Windows and Window Locks are working'],
                                ['adjectival_rating' => 'Unsafe', 'numerical_rating' => [3, 4], 'description' => 'Doors and Door Locks are damage and not working. Windows and Window Locks are damage and not working'],
                                ['adjectival_rating' => 'Very Unsafe', 'numerical_rating' => [1, 2], 'description' => 'Either No Doors and Door Locks installed. No Windows and Window Locks installed'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                    ],
                ],
                [
                    'category_id' => 3,
                    'category_name' => 'Habitability and Sanitation',
                    'weight_percentage' => null,
                    'items' => [
                        [
                            'item_name' => 'Water Supply',
                            'weight_percentage' => 10,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Highly adequate supply', 'numerical_rating' => 8, 'description' => 'Water Source – Sufficient water source, Water Line – Not corroded, not leaking, no damaged, not dilapidated, Septic Tank – Properly maintained, not overflowing, not clogged, Sewer Lines – Not corroded, not leaking, no damaged, not dilapidated'],
                                ['adjectival_rating' => 'Adequate supply', 'numerical_rating' => [6, 7], 'description' => 'Water Source – Sufficient water source, Water Line – Minimal corroded, leaking, damaged, dilapidated, Septic Tank – Properly maintained, not overflowing, not clogged, Sewer Lines – Not corroded, leaking, damaged, dilapidated'],
                                ['adjectival_rating' => 'Fair supply', 'numerical_rating' => 5, 'description' => 'Water Source – sufficient water source, Water Line – Minimal corroded, leaking, damaged, dilapidated, Septic Tank – Properly maintained, not overflowing, not clogged, Sewer Lines – Not corroded, not leaking, no damaged, not dilapidated'],
                                ['adjectival_rating' => 'Rotational/Low supply', 'numerical_rating' => 4, 'description' => 'Water Source – insufficient water source, Water Line – Corroded, leaking, damaged, dilapidated, Septic Tank – Not properly maintained, overflowing, clogged, Sewer Lines – Minimal corroded, leaking, damaged, dilapidated'],
                                ['adjectival_rating' => 'Very low supply', 'numerical_rating' => [2, 3], 'description' => 'Water Source – insufficient water source, Water Line – Corroded, leaking, damaged, dilapidated, Septic Tank – Unmaintained, overflowing, clogged, Sewer Lines – Corroded, leaking, damaged, dilapidated'],
                                ['adjectival_rating' => 'No supply', 'numerical_rating' => 1, 'description' => 'Either No Water Source, No Water Line, No Septic Tank, No Sewer Lines'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                        [
                            'item_name' => 'Electricity',
                            'weight_percentage' => 10,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Highly adequate supply', 'numerical_rating' => 8, 'description' => 'Circuit breakers or fuse box – functioning, not dilapidated, not damaged, Electrical Wirings – No octopus wiring, no exposed wirings, not dilapidated, Electrical lightings and fixtures (bulbs, outlet, switch) – functioning, not dilapidated, not damaged'],
                                ['adjectival_rating' => 'Adequate supply', 'numerical_rating' => [6, 7], 'description' => 'Circuit breakers or fuse box – functioning, not dilapidated, not damaged, Electrical Wirings – Minimal octopus wiring, exposed wirings, Electrical lightings and fixtures (bulbs, outlet, switch) – functioning, not dilapidated, not damaged'],
                                ['adjectival_rating' => 'Fair supply', 'numerical_rating' => 5, 'description' => 'Circuit breakers or fuse box – functioning, not dilapidated, not damaged, Electrical Wirings – Minimal octopus wiring, exposed wirings, Electrical lightings and fixtures (bulbs, outlet, switch) – not functioning'],
                                ['adjectival_rating' => 'Rotational/Low supply', 'numerical_rating' => 4, 'description' => 'Circuit breakers or fuse box – not functioning, Electrical Wirings – With octopus wiring, exposed wirings, Electrical lightings and fixtures (bulbs, outlet, switch) – not functioning'],
                                ['adjectival_rating' => 'Very low supply', 'numerical_rating' => [2, 3], 'description' => 'Damaged circuit breakers or fuse box, Damaged electrical Wirings, Damaged Electrical lightings and fixtures (bulbs, outlet, switch)'],
                                ['adjectival_rating' => 'No supply', 'numerical_rating' => 1, 'description' => 'Either No circuit breakers or fuse box, No electrical Wirings, and No Electrical lightings and fixtures (bulbs, outlet, switch)'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                        [
                            'item_name' => 'Roofing Condition and Painting',
                            'weight_percentage' => 5,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Excellent Condition', 'numerical_rating' => 6, 'description' => 'Not corroded, no leaking, maintained roof paint'],
                                ['adjectival_rating' => 'Very Good Condition', 'numerical_rating' => 5, 'description' => 'Minimal corroded, not leaking'],
                                ['adjectival_rating' => 'Good Condition', 'numerical_rating' => 4, 'description' => 'Minimal corroded, Minimal leaking'],
                                ['adjectival_rating' => 'Fair Condition', 'numerical_rating' => 3, 'description' => 'Corroded, minimal leaking'],
                                ['adjectival_rating' => 'Poor Condition', 'numerical_rating' => 2, 'description' => 'Corroded, leaking and unmaintained roof paint'],
                                ['adjectival_rating' => 'Very Poor Condition', 'numerical_rating' => 1, 'description' => 'Dilapidated and damage Roofing'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                        [
                            'item_name' => 'Sanitation',
                            'weight_percentage' => 5,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Very high sanitation', 'numerical_rating' => 8, 'description' => 'Clean and with complete fixtures; Proper lighted and ventilated'],
                                ['adjectival_rating' => 'High sanitation', 'numerical_rating' => [6, 7], 'description' => 'Clean and with complete fixtures; Improperly lighted and ventilated'],
                                ['adjectival_rating' => 'Fair sanitation', 'numerical_rating' => [4, 5], 'description' => 'Damaged/not working fixtures; Proper lighted and ventilated'],
                                ['adjectival_rating' => 'Low sanitation', 'numerical_rating' => 3, 'description' => 'Unserviceable Toilet and Bathroom; Not proper lighted and ventilated'],
                                ['adjectival_rating' => 'Very low sanitation', 'numerical_rating' => [1, 2], 'description' => 'Dilapidated and damaged toilet and bathroom'],
                                ['adjectival_rating' => 'Non-existing', 'numerical_rating' => 0, 'description' => ''],
                            ],
                        ],
                    ],
                ],
                [
                    'category_id' => 4,
                    'category_name' => 'Building Plans and Documents',
                    'weight_percentage' => 10,
                    'items' => [
                        [
                            'item_name' => 'Site Development Building Plan',
                            'weight_percentage' => 2,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Complete documents', 'numerical_rating' => 2, 'description' => 'Complete documents'],
                                ['adjectival_rating' => 'Incomplete documents', 'numerical_rating' => 1, 'description' => 'Incomplete documents'],
                                ['adjectival_rating' => 'No Documents', 'numerical_rating' => 0, 'description' => 'Non-existing documents'],
                            ],
                        ],
                        [
                            'item_name' => 'Technical Inspection and Acceptance',
                            'weight_percentage' => 2,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Complete documents', 'numerical_rating' => 2, 'description' => 'Complete documents'],
                                ['adjectival_rating' => 'Incomplete documents', 'numerical_rating' => 1, 'description' => 'Incomplete documents'],
                                ['adjectival_rating' => 'No Documents', 'numerical_rating' => 0, 'description' => 'Non-existing documents'],
                            ],
                        ],
                        [
                            'item_name' => 'Electrical System Plan',
                            'weight_percentage' => 2,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Complete documents', 'numerical_rating' => 1, 'description' => 'Complete documents'],
                                ['adjectival_rating' => 'Incomplete documents', 'numerical_rating' => 0.5, 'description' => 'Incomplete documents'],
                                ['adjectival_rating' => 'No Documents', 'numerical_rating' => 0, 'description' => 'Non-existing documents'],
                            ],
                        ],
                        [
                            'item_name' => 'Water and Plumbing Plan',
                            'weight_percentage' => 2,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Complete documents', 'numerical_rating' => 1, 'description' => 'Complete documents'],
                                ['adjectival_rating' => 'Incomplete documents', 'numerical_rating' => 0.5, 'description' => 'Incomplete documents'],
                                ['adjectival_rating' => 'No Documents', 'numerical_rating' => 0, 'description' => 'Non-existing documents'],
                            ],
                        ],
                        [
                            'item_name' => 'Maintenance Record',
                            'weight_percentage' => 1,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'Complete documents', 'numerical_rating' => 1, 'description' => 'Complete documents'],
                                ['adjectival_rating' => 'Incomplete documents', 'numerical_rating' => 0.5, 'description' => 'Incomplete documents'],
                                ['adjectival_rating' => 'No Documents', 'numerical_rating' => 0, 'description' => 'Non-existing documents'],
                            ],
                        ],
                        [
                            'item_name' => 'Building Insurance',
                            'weight_percentage' => 1,
                            'adjectival_rating' => '',
                            'numerical_rating' => 0,
                            'percentage' => 0,
                            'options' => [
                                ['adjectival_rating' => 'With policy and OR', 'numerical_rating' => 3, 'description' => 'With policy and OR'],
                                ['adjectival_rating' => 'Buildidng listed in policy but w/o OR', 'numerical_rating' => 2, 'description' => 'Buildidng listed in policy but w/o OR'],
                                ['adjectival_rating' => 'Uninsured', 'numerical_rating' => 1, 'description' => 'Uninsured'],
                                ['adjectival_rating' => 'No Exisiting', 'numerical_rating' => 0, 'description' => 'No Exisiting'],
                            ],
                        ],
                    ],
                ],
            ],
            'total_numerical_rating' => 0,
            'total_percentage' => 0,
        ];
    }
}
