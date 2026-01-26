<?php

declare(strict_types=1);

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create((new Admin)->getTable(), function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->timestamps();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
        });
    }
};
