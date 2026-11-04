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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->unsignedBigInteger('category_id');

        $table->enum('type', ['income', 'expense']);
        $table->decimal('amount', 15, 2);
        $table->text('description')->nullable();
        $table->date('transaction_date');
        $table->timestamps();

        $table->foreign(
            ['category_id', 'user_id', 'type']
        )->references(
            ['id', 'user_id', 'type']
        )->on('categories')->restrictOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
