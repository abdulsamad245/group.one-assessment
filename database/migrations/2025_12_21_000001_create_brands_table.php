<?php

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
        Schema::create(BrandConstant::TABLE, function (Blueprint $table) {
            $table->uuid(BrandConstant::ID)->primary();
            $table->string(BrandConstant::NAME)->unique();
            $table->string(BrandConstant::SLUG)->unique();
            $table->text(BrandConstant::DESCRIPTION)->nullable();
            $table->string(BrandConstant::CONTACT_EMAIL)->nullable();
            $table->string(BrandConstant::WEBSITE)->nullable();
            $table->json(BrandConstant::SETTINGS)->nullable();
            $table->boolean(BrandConstant::IS_ACTIVE)->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(BrandConstant::SLUG);
            $table->index(BrandConstant::IS_ACTIVE);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(BrandConstant::TABLE);
    }
};
