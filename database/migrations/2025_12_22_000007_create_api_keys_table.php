<?php

use App\Constants\ApiKeyConstant;
use App\Constants\BrandConstant;
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
        Schema::create(ApiKeyConstant::TABLE, function (Blueprint $table) {
            $table->uuid(ApiKeyConstant::ID)->primary();
            $table->foreignUuid(ApiKeyConstant::BRAND_ID)
                ->constrained(BrandConstant::TABLE)
                ->onDelete('cascade');
            $table->string(ApiKeyConstant::NAME);
            $table->string(ApiKeyConstant::KEY, 64)->unique();
            $table->string(ApiKeyConstant::PREFIX, 16)->index();
            $table->json(ApiKeyConstant::PERMISSIONS)->nullable();
            $table->timestamp(ApiKeyConstant::LAST_USED_AT)->nullable();
            $table->timestamp(ApiKeyConstant::EXPIRES_AT)->nullable();
            $table->boolean(ApiKeyConstant::IS_ACTIVE)->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index([ApiKeyConstant::BRAND_ID, ApiKeyConstant::IS_ACTIVE]);
            $table->index(ApiKeyConstant::EXPIRES_AT);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ApiKeyConstant::TABLE);
    }
};
