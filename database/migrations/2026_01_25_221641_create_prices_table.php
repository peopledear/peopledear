<?php

declare(strict_types=1);

use App\Models\Price;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create((new Price)->getTable(), function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->timestamps();

            $table->foreignIdFor(Product::class)->constrained();
            $table->string('stripe_price_id')->nullable()->unique();
            $table->string('interval');
            $table->integer('amount');
            $table->string('currency', 3);
            $table->boolean('is_active');
        });
    }
};
