<?php

use App\Constants\LicenseConstant;
use App\Constants\LicenseEventConstant;
use App\Enums\LicenseEventType;
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
        Schema::create(LicenseEventConstant::TABLE, function (Blueprint $table) {
            $table->uuid(LicenseEventConstant::ID)->primary();
            $table->foreignUuid(LicenseEventConstant::LICENSE_ID)
                ->constrained(LicenseConstant::TABLE)
                ->onDelete('cascade');
            $table->enum(LicenseEventConstant::EVENT_TYPE, LicenseEventType::values());
            $table->text(LicenseEventConstant::DESCRIPTION)->nullable();
            $table->json('event_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index([LicenseEventConstant::LICENSE_ID, LicenseEventConstant::EVENT_TYPE]);
            $table->index(LicenseEventConstant::CREATED_AT);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(LicenseEventConstant::TABLE);
    }
};
