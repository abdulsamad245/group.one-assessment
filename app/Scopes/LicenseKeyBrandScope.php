<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope to filter license keys by authenticated brand.
 *
 * License keys have a direct brand_id column, so we filter directly.
 * The brand is determined from API key authentication or user authentication.
 */
class LicenseKeyBrandScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $brand = app()->bound('current_brand') ? app('current_brand') : null;

        if ($brand) {
            $builder->where('brand_id', $brand->id);
            return;
        }

        $user = auth()->user();

        if (!$user) {
            return;
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        $builder->where('brand_id', $user->brand_id);
    }
}
