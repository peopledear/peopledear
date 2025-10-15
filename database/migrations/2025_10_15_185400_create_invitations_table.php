<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
            $table->string('email')
                ->index();
            $table->foreignIdFor(Role::class)
                ->constrained();
            $table->foreignIdFor(User::class, 'invited_by')
                ->constrained('users');
            $table->string('token')
                ->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')
                ->nullable();
        });
    }
};
