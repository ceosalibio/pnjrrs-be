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
        $psgRating = ($actualCount / $requiredCount) * 100;
        $gradeRating = ($gradePoints / $actualCount) * 100;
        $gradeRating04 = $gradeRating * 0.4;
        $afposRating = ($afposPoints / $actualCount) * 100;
        $afposRating06 = $afposRating * 0.6;
        $psgRating05 = $psgRating * 0.5;
        $pqrRatingTotal = $gradeRating04 + $afposRating06;
        $pqrRating05 = $pqrRatingTotal * 0.5;
        $readiness = $psgRating05 + $pqrRating05;

        return [
            'psgRating' => $this->round($psgRating),
            'gradeRating' => $this->round($gradeRating),
            'gradeRating04' => $this->round($gradeRating04),
            'afposRating' => $this->round($afposRating),
            'afposRating06' => $this->round($afposRating06),
            'psgRating05' => $this->round($psgRating05),
            'pqrRatingTotal' => $this->round($pqrRatingTotal),
            'pqrRating05' => $this->round($pqrRating05),
            'readiness' => $this->round($readiness)
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
}
