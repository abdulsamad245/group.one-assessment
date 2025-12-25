<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * BrandScope
 *
 * Automatically filters queries by the authenticated brand.
 * This ensures multi-tenant data isolation.
 *
 * The brand is determined from:
 * 1. API key authentication (brand associated with the API key)
 * 2. User authentication (user's brand_id)
 */
class BrandScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $brand = app()->bound('current_brand') ? app('current_brand') : null;

        if ($brand) {
            $builder->where($model->getTable() . '.brand_id', $brand->id);
            return;
        }

        $user = auth()->user();

        if (!$user) {
            return;
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        $builder->where($model->getTable() . '.brand_id', $user->brand_id);
    }
}
