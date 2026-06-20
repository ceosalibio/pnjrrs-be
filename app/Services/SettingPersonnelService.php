<?php
namespace App\Services;
use App\Repositories\SettingPersonnelRepository;

class SettingPersonnelService
{
    public function __construct(private SettingPersonnelRepository $repository)
    {
    }

    // Implement service methods that call the repository methods
}