<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cross_domain_auth_tokens', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->foreignIdFor(Organization::class);
            $table->foreignIdFor(User::class);
            $table->string('nonce')->unique();
            $table->string('intended');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
        });
    }
};
