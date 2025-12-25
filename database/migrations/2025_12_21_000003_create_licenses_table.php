<?php

use App\Constants\BrandConstant;
use App\Constants\LicenseConstant;
use App\Constants\LicenseKeyConstant;
use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
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
        Schema::create(LicenseConstant::TABLE, function (Blueprint $table) {
            $table->uuid(LicenseConstant::ID)->primary();
            $table->foreignUuid(LicenseConstant::LICENSE_KEY_ID)
                ->constrained(LicenseKeyConstant::TABLE)
                ->onDelete('cascade');
            $table->foreignUuid(LicenseConstant::BRAND_ID)
                ->constrained(BrandConstant::TABLE)
                ->onDelete('cascade');
            $table->string(LicenseConstant::CUSTOMER_EMAIL);
            $table->string(LicenseConstant::CUSTOMER_NAME);
            $table->string(LicenseConstant::PRODUCT_NAME);
            $table->string(LicenseConstant::PRODUCT_SLUG);
            $table->string(LicenseConstant::PRODUCT_SKU)->nullable();
            $table->enum(LicenseConstant::LICENSE_TYPE, LicenseType::values())
                ->default(LicenseType::SUBSCRIPTION->value);
            $table->json(LicenseConstant::MAX_ACTIVATIONS_PER_INSTANCE);
            $table->integer(LicenseConstant::CURRENT_ACTIVATIONS)->default(0);
            $table->timestamp(LicenseConstant::EXPIRES_AT)->nullable();
            $table->enum(LicenseConstant::STATUS, LicenseStatus::values())
                ->default(LicenseStatus::ACTIVE->value);
            $table->json(LicenseConstant::METADATA)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index([LicenseConstant::BRAND_ID, LicenseConstant::CUSTOMER_EMAIL]);
            $table->index(LicenseConstant::STATUS);
            $table->index(LicenseConstant::EXPIRES_AT);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(LicenseConstant::TABLE);
    }
};

