<?php

use App\Constants\BrandConstant;
use App\Constants\LicenseKeyConstant;
use App\Enums\LicenseKeyStatus;
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
        Schema::create(LicenseKeyConstant::TABLE, function (Blueprint $table) {
            $table->uuid(LicenseKeyConstant::ID)->primary();
            $table->foreignUuid(LicenseKeyConstant::BRAND_ID)
                ->constrained(BrandConstant::TABLE)
                ->onDelete('cascade');
            $table->string(LicenseKeyConstant::CUSTOMER_EMAIL);
            $table->text(LicenseKeyConstant::KEY)
                ->comment('License key in format: XXXXX-XXXXX-XXXXX-XXXXX-XXXXX (5 groups of 5 characters)');
            $table->string(LicenseKeyConstant::KEY_HASH, 64)->unique()
                ->comment('SHA-256 hash of the license key for secure lookups');
            $table->enum(LicenseKeyConstant::STATUS, LicenseKeyStatus::values())
                ->default(LicenseKeyStatus::ACTIVE->value);
            $table->timestamp(LicenseKeyConstant::EXPIRES_AT)->nullable();
            $table->json(LicenseKeyConstant::METADATA)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index([LicenseKeyConstant::BRAND_ID, LicenseKeyConstant::CUSTOMER_EMAIL]);
            $table->index(LicenseKeyConstant::STATUS);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(LicenseKeyConstant::TABLE);
    }
};
