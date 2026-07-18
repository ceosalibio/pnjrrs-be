<?php

namespace App\Services;

class ResultCalculationService
{
    /**
     * Calculate Results based on personnel report metrics
     * 
     * @param int $gradePoints
     * @param int $afposPoints
     * @param int $actualCount
     * @param int $requiredCount
     * @return array
     */
    public function calculatePersonnelResults(int $gradePoints, int $afposPoints, int $actualCount, int $requiredCount): array
    {
        // Prevent division by zero
        if ($actualCount === 0 || $requiredCount === 0) {
            return $this->getZeroRatings();
        }

        // Calculate ratings based on provided formula
        $fillUpRating = ($actualCount / $requiredCount) * 100;
        $finalFillUpRating = $fillUpRating * 0.8;
        $gradeRating = ($gradePoints / $actualCount) * 100;
        $finalGradeRating = $gradeRating * 0.2;
        $afposRating = ($afposPoints / $actualCount) * 100;
        $finalAfposRating = $afposRating * 0.2;
        $readiness = $finalFillUpRating + $finalGradeRating  + $finalAfposRating;

        return [
            'fillUpRating' => $this->round($fillUpRating),
            'finalFillUpRating' => $this->round($finalFillUpRating),
            'gradeRating' => $this->round($gradeRating),
            'finalGradeRating' => $this->round($finalGradeRating),
            'afposRating' => $this->round($afposRating),
            'finalAfposRating' => $this->round($finalAfposRating),
            'readiness' => $this->round($readiness),
            'redcon' => $this->getRedconStatus($readiness)
        ];
    }

    /**
     * Get zero ratings when division by zero is prevented
     * 
     * @return array
     */
    private function getZeroRatings(): array
    {
        return [
            'psgRating' => 0,
            'gradeRating' => 0,
            'gradeRating04' => 0,
            'afposRating' => 0,
            'afposRating06' => 0,
            'psgRating05' => 0,
            'pqrRatingTotal' => 0,
            'pqrRating05' => 0,
            'readiness' => 0
        ];
    }

    /**
     * Round number to 2 decimal places
     * 
     * @param float $value
     * @return float
     */
    private function round(float $value): float
    {
        return round($value, 2);
    }

    public function calculateTrainingResults(int $actual, int $required): array
    {
        // Prevent division by zero
        // if ($actual === 0 || $required === 0) {
        //     return $this->getZeroRatings();
        // }

        // Calculate ratings based on provided formula
        $readiness = ($actual / $required) * 100;
       

        return [
            'actual' => $this->round($actual),
            'required' => $this->round($required),
            'readiness' => $this->round($readiness),
            'redcon' => $this->getRedconStatus($readiness)
        ];
    }

    public function calculateEquipmentResults(array $items, $category_id): array
    {
        $equipmentReadiness = $this->calculateEquipmentReadiness($items, $category_id);
        $maintenanceReadiness = $this->calculateMaintenaceReadiness($items, $category_id);

        return [
            "equipment" => $equipmentReadiness,
            "maintenance" => $maintenanceReadiness
        ];
    }

    public function calculateEquipmentReadiness(array $items, $category_id): array
    {
        // Pwede mo i-adjust ang weights depende sa typeofCommand kung may ibang rule
        $mceWeight = 0.40;
        $meeWeight = $category_id == 2?0.30 : 0.70;
        $seWeight  = 0.30;

        // Category names na galing sa iyong JSON (case-sensitive, fixed "MOBILITY" typo)
        $meeCategories = ['MOBILITY', 'FIREPOWER', 'Communications'];
        $seCategories  = ['Medical', 'Dental', 'Quartermaster'];

        $mceDivisions = [];
        $meeDivisions = [];
        $seDivisions  = [];
        $mceRatings   = [];
        $meeRatings   = [];
        $seRatings    = [];

        foreach ($items as $category) {
            $categoryName = $category['category_name'];
            $isMee = in_array($categoryName, $meeCategories, true);
            $isSe  = in_array($categoryName, $seCategories, true);

            \Log::info('Equipment Readiness - Processing category', [
                'category_name' => $categoryName,
                'is_mee' => $isMee,
                'is_se' => $isSe,
                'category_id' => $category['category_id'] ?? null,
            ]);

            if (!$isMee && !$isSe) {
                \Log::debug('Equipment Readiness - Category skipped (not MEE or SE)', [
                    'category_name' => $categoryName,
                ]);
                continue; // hindi kabilang sa MEE o SE, laktawan
            }

            foreach ($category['divisions'] as $division) {
                $required = (int) $division['required'];
                $onhand   = (int) $division['onhand'];
                $rating   = $required > 0 ? round(($onhand / $required) * 100, 2) : null;

                \Log::info('Equipment Readiness - Processing division', [
                    'category' => $categoryName,
                    'division_name' => $division['division_name'] ?? 'Unknown',
                    'required' => $required,
                    'onhand' => $onhand,
                    'rating' => $rating,
                    'is_mee' => $isMee,
                    'is_se' => $isSe,
                ]);

                $row = [
                    'category_id'       => $category['category_id'],
                    'category_name'     => $category['category_name'],
                    'name'              => $division['division_name'],
                    'required'          => $division['required'],
                    'onhand'            => $division['onhand'],
                    'rating'            => $rating,
                ];

                

                if ($isMee) {
                     $meeDivisions[] = $row;
                    if ($rating !== null) {
                        $meeRatings[] = $rating;
                    }
                } elseif ($isSe) {
                    $seDivisions[] = $row;
                    if ($rating !== null) {
                        $seRatings[] = $rating;
                    }
                }
            }
        }

        $mceAverage = count($mceRatings) > 0
            ? round(array_sum($mceRatings) / count($mceRatings), 2)
            : 0;

        $meeAverage = count($meeRatings) > 0
            ? round(array_sum($meeRatings) / count($meeRatings), 2)
            : 0;

        $seAverage = count($seRatings) > 0
            ? round(array_sum($seRatings) / count($seRatings), 2)
            : 0;

        $mceWeighted = round($mceAverage * $mceWeight, 2);
        $meeWeighted = round($meeAverage * $meeWeight, 2);
        $seWeighted  = round($seAverage * $seWeight, 2);
        if($category_id != 2){
            $totalScore  = round($meeWeighted + $seWeighted, 2);
        }else{
            $totalScore  = round($mceWeighted + $meeWeighted + $seWeighted, 2);
        }

        return [
            'mce' => [
                'divisions'      => $mceDivisions,
                'total_average'  => $mceAverage,
                'weight_percent' => $mceWeight * 100,
                'weighted_score' => $mceWeighted,
            ],
            'mee' => [
                'divisions'      => $meeDivisions,
                'total_average'  => $meeAverage,
                'weight_percent' => $meeWeight * 100,
                'weighted_score' => $meeWeighted,
            ],
            'se' => [
                'divisions'      => $seDivisions,
                'total_average'  => $seAverage,
                'weight_percent' => $seWeight * 100,
                'weighted_score' => $seWeighted,
            ],
            'total_score'        => $totalScore,
            'redcon' => $this->getRedconStatus($totalScore),
        ];
    }



    public function calculateMaintenaceReadiness(array $items, $category_id): array
    {
        // Pwede mo i-adjust ang weights depende sa typeofCommand kung may ibang rule
        $mceWeight = 0.40;
        $meeWeight = $category_id == 2?0.30 : 0.70;
        $seWeight  = 0.30;

        // Category names na galing sa iyong JSON (case-sensitive, fixed "MOBILITY" typo)
        $meeCategories = ['MOBILITY', 'FIREPOWER', 'Communications'];
        $seCategories  = ['Medical', 'Dental', 'Quartermaster'];

        $mceDivisions = [];
        $meeDivisions = [];
        $seDivisions  = [];
        $mceRatings   = [];
        $meeRatings   = [];
        $seRatings    = [];

        foreach ($items as $category) {
            $categoryName = $category['category_name'];
            $isMee = in_array($categoryName, $meeCategories, true);
            $isSe  = in_array($categoryName, $seCategories, true);

            \Log::info('Maintenance Readiness - Processing category', [
                'category_name' => $categoryName,
                'is_mee' => $isMee,
                'is_se' => $isSe,
                'category_id' => $category['category_id'] ?? null,
            ]);

            if (!$isMee && !$isSe) {
                \Log::debug('Maintenance Readiness - Category skipped (not MEE or SE)', [
                    'category_name' => $categoryName,
                ]);
                continue; // hindi kabilang sa MEE o SE, laktawan
            }

            foreach ($category['divisions'] as $division) {

                $points = (float) $division['points'];
                $onhand   = (int) $division['onhand'];
                $rating   = $onhand > 0 ? round(($points / $onhand) * 100, 2) : null;

                \Log::info('Maintenance Readiness - Processing division', [
                    'category' => $categoryName,
                    'division_name' => $division['division_name'] ?? 'Unknown',
                    'points' => $points,
                    'onhand' => $onhand,
                    'calculation' => "($points / $onhand) * 100 = $rating",
                    'rating' => $rating,
                    'is_mee' => $isMee,
                    'is_se' => $isSe,
                ]);


                $row = [
                    'category_id'       => $category['category_id'],
                    'category_name'     => $category['category_name'],
                    'name'              => $division['division_name'],
                    'required'          => $division['onhand'],
                    'onhand'            => $division['points'],
                    'rating'            => $rating,
                ];

                

                if ($isMee) {
                     $meeDivisions[] = $row;
                    if ($rating !== null) {
                        $meeRatings[] = $rating;
                    }
                } elseif ($isSe) {
                    $seDivisions[] = $row;
                    if ($rating !== null) {
                        $seRatings[] = $rating;
                    }
                }
            }
        }

        $mceAverage = count($mceRatings) > 0
            ? round(array_sum($mceRatings) / count($mceRatings), 2)
            : 0;

        $meeAverage = count($meeRatings) > 0
            ? round(array_sum($meeRatings) / count($meeRatings), 2)
            : 0;

        $seAverage = count($seRatings) > 0
            ? round(array_sum($seRatings) / count($seRatings), 2)
            : 0;

        $mceWeighted = round($mceAverage * $mceWeight, 2);
        $meeWeighted = round($meeAverage * $meeWeight, 2);
        $seWeighted  = round($seAverage * $seWeight, 2);
        if($category_id != 2){
            $totalScore  = round($meeWeighted + $seWeighted, 2);
        }else{
            $totalScore  = round($mceWeighted + $meeWeighted + $seWeighted, 2);
        }

        return [
            'mce' => [
                'divisions'      => $mceDivisions,
                'total_average'  => $mceAverage,
                'weight_percent' => $mceWeight * 100,
                'weighted_score' => $mceWeighted,
            ],
            'mee' => [
                'divisions'      => $meeDivisions,
                'total_average'  => $meeAverage,
                'weight_percent' => $meeWeight * 100,
                'weighted_score' => $meeWeighted,
            ],
            'se' => [
                'divisions'      => $seDivisions,
                'total_average'  => $seAverage,
                'weight_percent' => $seWeight * 100,
                'weighted_score' => $seWeighted,
            ],
            'total_score'        => $totalScore,
            'redcon' => $this->getRedconStatus($totalScore),
        ];
    }

    // facilities redcon status
    public function calculateFacilities(array $items, $unit_id): array
    {
        // Define weights based on unit_id
        $weights = [];
        if ($unit_id == 5) {
            $weights = [
                'Operational Facilities' => 0.50,
                'Operational Support Facilities' => 0.30,
                'Base Facilities' => 0.10,
                'Community Facilities' => 0.10,
            ];
        } else {
            $weights = [
                'Operational Facilities' => 0.60,
                'Operational Support Facilities' => 0.40,
            ];
        }

        $categories = [];
        $totalWeightedScore = 0;
        $totalQualitative = 0;
        $totalQuantitative = 0;
        $totalAverage = 0;

        foreach ($items as $item) {
            $category = $item['category'];
            
            // Calculate average percentage: (qualitative_percentage + quantitative_percentage) / 2
            $qualitative = floatval($item['qualitative_percentage'] ?? 0);
            $quantitative = floatval($item['quantitative_percentage'] ?? 0);
            $averagePercentage = ($qualitative + $quantitative) / 2;
            
            // Get weight for this category
            $weight = $weights[$category] ?? 0;
            
            // Calculate weighted score
            $weightedScore = $averagePercentage * $weight;
            
            $categories[] = [
                'category' => $category,
                'required_area' => $item['required_area_sqm'] ?? 0,
                'actual_area' => $item['actual_area_sqm'] ?? 0,
                'qualitative_percentage' => $qualitative,
                'quantitative_percentage' => $quantitative,
                'average_percentage' => round($averagePercentage, 2),
                'weight_percentage' => $weight * 100,
                'weighted_score' => round($weightedScore, 2),
            ];
            
            $totalWeightedScore += $weightedScore;
            $totalQualitative += $qualitative;
            $totalQuantitative += $quantitative;
            $totalAverage += $averagePercentage;
        }

        $overallReadiness = round($totalWeightedScore, 2);
        
        // Calculate average percentages
        $itemCount = count($items);
        $avgQualitative = $itemCount > 0 ? round($totalQualitative / $itemCount, 2) : 0;
        $avgQuantitative = $itemCount > 0 ? round($totalQuantitative / $itemCount, 2) : 0;
        $avgAverage = $itemCount > 0 ? round($totalAverage / $itemCount, 2) : 0;

        return [
            'categories' => $categories,
            'overall_readiness' => $overallReadiness,
            'average_qualitative_percentage' => $avgQualitative,
            'average_quantitative_percentage' => $avgQuantitative,
            'average_percentage' => $avgAverage,
            'redcon' => $this->getRedconStatus($overallReadiness),
        ];
    }




    private function getRedconStatus(float $score): string
    {
        if ($score >= 85) return 'R1';
        if ($score >= 84) return 'R2';
        if ($score >= 74) return 'R3';
        return 'R4';
    }
}
