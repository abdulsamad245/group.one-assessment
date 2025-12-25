<?php

use App\Constants\ActivationConstant;
use App\Constants\LicenseConstant;
use App\Constants\LicenseKeyConstant;
use App\Enums\ActivationStatus;
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
        Schema::create(ActivationConstant::TABLE, function (Blueprint $table) {
            $table->uuid(ActivationConstant::ID)->primary();
            $table->foreignUuid(ActivationConstant::LICENSE_KEY_ID)
                ->constrained(LicenseKeyConstant::TABLE)
                ->onDelete('cascade');
            $table->foreignUuid('license_id')
                ->constrained(LicenseConstant::TABLE)
                ->onDelete('cascade');
            $table->string(ActivationConstant::DEVICE_IDENTIFIER)->nullable();
            $table->string(ActivationConstant::DEVICE_NAME)->nullable();
            $table->string(ActivationConstant::INSTANCE_TYPE, 50)
                ->nullable();
            $table->string(ActivationConstant::INSTANCE_VALUE, 255)
                ->nullable();
            $table->string(ActivationConstant::IP_ADDRESS)->nullable();
            $table->string(ActivationConstant::USER_AGENT)->nullable();
            $table->enum(ActivationConstant::STATUS, ActivationStatus::values())
                ->default(ActivationStatus::ACTIVE->value);
            $table->timestamp(ActivationConstant::ACTIVATED_AT);
            $table->timestamp(ActivationConstant::DEACTIVATED_AT)->nullable();
            $table->timestamp(ActivationConstant::LAST_CHECKED_AT)->nullable();
            $table->json(ActivationConstant::METADATA)->nullable();
            $table->timestamps();

            $table->index([ActivationConstant::LICENSE_KEY_ID, ActivationConstant::STATUS]);
            $table->index(['license_id', ActivationConstant::STATUS]);
            $table->index(ActivationConstant::DEVICE_IDENTIFIER);
            $table->index(['license_id', ActivationConstant::INSTANCE_TYPE, ActivationConstant::INSTANCE_VALUE], 'activations_license_instance_idx');
            $table->index(ActivationConstant::ACTIVATED_AT);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ActivationConstant::TABLE);
    }
};
