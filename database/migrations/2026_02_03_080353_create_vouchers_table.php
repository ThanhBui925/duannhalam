<?php

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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            // null = voucher công khai

            $table->string('code')->unique();

            $table->enum('type', ['percent', 'fixed']);

            $table->decimal('value', 15, 2);
            // percent: 0–100 | fixed: số tiền

            $table->decimal('max_discount_amount', 15, 2)->nullable();
            $table->decimal('min_order_amount', 15, 2)->default(0);

            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->string('status')->default('active');
            // active | inactive | expired

            $table->boolean('is_public')->default(true);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
