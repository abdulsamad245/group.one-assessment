<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope to filter license events by authenticated user's brand.
 *
 * This scope joins through the licenses table to filter by brand_id.
 * Super admins can bypass this scope to see events across all brands.
 */
class LicenseEventBrandScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        $builder->whereHas('license', function ($query) use ($user) {
            $query->where('brand_id', $user->brand_id);
        });
    }
}
