<?php
namespace App\Services;
use App\Repositories\UserRepository;

class ApproverService 
{
    public $userRepository;
    public function __construct(UserRepository  $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function fetchApprover($filters)
    {
        $result = $this->userRepository->filterByMultiple($filters);

        // Group by approver field and get unique approver details
        $grouped = $result->groupBy('approver')->map(function ($group) {
            // Get the first user in the group to get approver details
            return [
                'approver' => $group->first()->approver,
                'position' => $group->first()->position,
                'actual' => $group->first()->approvers,
                'users' => $group->map(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'position' => $user->position,
                    'username' => $user->username,
                ])->values(),
            ];
        })->values();
        
        return $grouped;
    }

    /**
     * Get approver for a report based on its organizational filters.
     * 
     * @param object $report The report object with category_id, unit_id, sub_unit_id, office_id, sub_office_id
     * @return mixed The approver details
     */
    public function getApproverForReport($report, $type = null)
    {
        $approverFilters = [
            'report_id' => $report->id,
            'category_id' => $report->category_id,
            'unit_id' => $report->unit_id,
            'sub_unit_id' => $report->sub_unit_id ?? null,
            'office_id' => $report->office_id ?? null,
            'sub_office_id' => $report->sub_office_id ?? null,
        ];

        if ($type) {
            $approverFilters['report_type'] = $type;
        }

        return $this->fetchApprover($approverFilters);
    }
   
}