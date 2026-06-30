<?php

namespace App\Repositories;

use App\Models\PnSerial;
use Illuminate\Database\Eloquent\Collection;

class PnSerialRepository
{
    public function all(): Collection
    {
        return PnSerial::all();
    }

    public function findById(int $id): ?PnSerial
    {
        return PnSerial::find($id);
    }

    public function create(array $data): PnSerial
    {
        return PnSerial::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $serial = $this->findById($id);
        if (!$serial) {
            return false;
        }
        return $serial->update($data);
    }

    public function delete(int $id): bool
    {
        $serial = $this->findById($id);
        if (!$serial) {
            return false;
        }
        return $serial->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return PnSerial::paginate($perPage);
    }

    public function findByReportMonth(string $reportMonth)
    {
        return PnSerial::where('report_month', $reportMonth)->get();
    }

    public function findByPersonnelReportId(int $personnelReportId)
    {
        return PnSerial::where('personnel_report_id', $personnelReportId)->get();
    }
}
