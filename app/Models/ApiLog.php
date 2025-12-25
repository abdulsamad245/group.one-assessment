<?php

namespace App\Models;

use App\Constants\ApiLogConstant;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLog extends Model
{
    use HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ApiLogConstant::TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        ApiLogConstant::ID,
        ApiLogConstant::CORRELATION_ID,
        ApiLogConstant::METHOD,
        ApiLogConstant::PATH,
        'full_path',
        ApiLogConstant::IP_ADDRESS,
        ApiLogConstant::USER_AGENT,
        ApiLogConstant::REQUEST_HEADERS,
        ApiLogConstant::REQUEST_BODY,
        ApiLogConstant::RESPONSE_HEADERS,
        ApiLogConstant::RESPONSE_BODY,
        ApiLogConstant::STATUS_CODE,
        ApiLogConstant::CONTENT_TYPE,
        ApiLogConstant::REFERER,
        ApiLogConstant::REQUESTED_AT,
        ApiLogConstant::RESPONDED_AT,
        ApiLogConstant::DURATION_MS,
        ApiLogConstant::USER_ID,
        ApiLogConstant::BRAND_ID,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        ApiLogConstant::REQUESTED_AT => 'datetime',
        ApiLogConstant::RESPONDED_AT => 'datetime',
        ApiLogConstant::DURATION_MS => 'float',
        ApiLogConstant::STATUS_CODE => 'integer',
    ];

    /**
     * Get the user that made the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the brand associated with the request.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
