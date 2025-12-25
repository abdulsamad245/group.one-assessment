<?php

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
        Schema::create(UserConstant::TABLE, function (Blueprint $table) {
            $table->uuid(UserConstant::ID)->primary();
            $table->foreignUuid(UserConstant::BRAND_ID)
                ->constrained(BrandConstant::TABLE)
                ->onDelete('cascade');
            $table->string(UserConstant::NAME);
            $table->string(UserConstant::EMAIL)->unique();
            $table->timestamp(UserConstant::EMAIL_VERIFIED_AT)->nullable();
            $table->string(UserConstant::PASSWORD);
            $table->string(UserConstant::ROLE)
                ->default('user')
                ->comment('user, admin, super_admin');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index([UserConstant::BRAND_ID, UserConstant::ROLE]);
            $table->index(UserConstant::EMAIL);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuidMorphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists(UserConstant::TABLE);
    }
};
