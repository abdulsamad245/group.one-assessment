<?php

use App\Constants\ApiLogConstant;
use App\Constants\BrandConstant;
use App\Constants\UserConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(ApiLogConstant::TABLE, function (Blueprint $table) {
            $table->uuid(ApiLogConstant::ID)->primary();
            $table->uuid(ApiLogConstant::CORRELATION_ID)->index();
            $table->string(ApiLogConstant::METHOD, 10);
            $table->string(ApiLogConstant::PATH, 255)->index();
            $table->text('full_path')->nullable()
                ->comment('Full path if longer than 255 characters');
            $table->string(ApiLogConstant::IP_ADDRESS, 45)->nullable();
            $table->text(ApiLogConstant::USER_AGENT)->nullable();
            $table->json(ApiLogConstant::REQUEST_HEADERS)->nullable();
            $table->json(ApiLogConstant::REQUEST_BODY)->nullable();
            $table->json(ApiLogConstant::RESPONSE_HEADERS)->nullable();
            $table->json(ApiLogConstant::RESPONSE_BODY)->nullable();
            $table->integer(ApiLogConstant::STATUS_CODE)->nullable()->index();
            $table->string(ApiLogConstant::CONTENT_TYPE)->nullable();
            $table->text(ApiLogConstant::REFERER)->nullable();
            $table->timestamp(ApiLogConstant::REQUESTED_AT)->nullable()->index();
            $table->timestamp(ApiLogConstant::RESPONDED_AT)->nullable();
            $table->float(ApiLogConstant::DURATION_MS)->nullable()
                ->comment('Request duration in milliseconds');
            $table->foreignUuid(ApiLogConstant::USER_ID)
                ->nullable()
                ->constrained(UserConstant::TABLE)
                ->nullOnDelete();
            $table->foreignUuid(ApiLogConstant::BRAND_ID)
                ->nullable()
                ->constrained(BrandConstant::TABLE)
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ApiLogConstant::TABLE);
    }
};
