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
            'readiness' => $this->round($readiness)
        ];
    }
}
