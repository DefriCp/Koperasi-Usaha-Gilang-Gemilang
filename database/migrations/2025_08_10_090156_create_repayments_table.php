<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debtor_id')->constrained()->cascadeOnDelete();
            $table->date('period_date');                       // pakai tanggal 01
            $table->decimal('amount_due', 18, 2)->default(0);  // kewajiban bulan tsb
            $table->decimal('amount_paid', 18, 2)->default(0); // realisasi bayar
            $table->date('paid_date')->nullable();
            $table->string('status')->default('UNPAID');       // UNPAID|PAID
            $table->timestamps();

            $table->unique(['debtor_id','period_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};
