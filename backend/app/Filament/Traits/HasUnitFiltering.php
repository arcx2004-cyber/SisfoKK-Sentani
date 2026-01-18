<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait HasUnitFiltering
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user) {
            return $query;
        }

        // 1. Super Admin & Administrator Check
        // Shield usually uses 'super_admin' based on config, but we check Role 'administrator' explicitly too.
        if ($user->hasRole(['super_admin', 'administrator', 'kepala_sekolah'])) {
            return $query;
        }

        // 2. Get User's Unit ID via Guru/Staff relationship
        // Assuming 'guru' relationship exists on User model and holds the unit_id 
        // (Even for Tendik, if they are stored in gurus table)
        $unitId = $user->guru?->unit_id;

        // 3. Logic: If user has roles that require filtering (Guru, Tendik) BUT has no unit associated, 
        // force empty result to verify 'secure by default'.
        // However, if they are 'siswa', they have their own logic (usually excluded from this trait usage or handled in BaseResource restriction).
        if (!$unitId) {
            // If they are a student, this trait might not be intended for their view (Siswa sees Biodata).
            // But if a Guru has no Unit ID, they shouldn't see random data.
            return $query->whereRaw('1 = 0');
        }

        // 4. Apply Filter if column exists
        $model = new static::$model;
        if (Schema::hasColumn($model->getTable(), 'unit_id')) {
            $query->where('unit_id', $unitId);
        }

        return $query;
    }
}
