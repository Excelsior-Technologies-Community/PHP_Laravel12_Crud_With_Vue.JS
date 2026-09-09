<?php

namespace App\Policies;

use App\Models\FilterPreset;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilterPresetPolicy
{
    public function view(User $user, FilterPreset $filterPreset): bool
    {
        return $user->id === $filterPreset->user_id;
    }

    public function delete(User $user, FilterPreset $filterPreset): bool
    {
        return $user->id === $filterPreset->user_id;
    }
}
