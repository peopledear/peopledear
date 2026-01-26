<?php

declare(strict_types=1);

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create((new Product)->getTable(), function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->timestamps();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('stripe_product_id')->nullable()->unique();
            $table->boolean('is_active');
        });
    }
};
