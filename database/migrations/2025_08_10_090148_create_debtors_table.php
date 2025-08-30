<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('debtors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id'); // pengaju (inputer)
            $table->string('nopen')->unique();
            $table->string('name');

            $table->decimal('plafond', 18, 2)->default(0);
            $table->decimal('installment', 18, 2)->default(0);  // angsuran/bln
            $table->unsignedInteger('tenor')->default(0);       // total bulan
            $table->unsignedInteger('installment_no')->default(0); // angsuran ke-
            $table->date('akad_date')->nullable();

            // cache kalkulasi cepat
            $table->decimal('outstanding', 18, 2)->default(0);
            $table->decimal('arrears', 18, 2)->default(0);

            $table->string('status')->default('pending'); // pending|approved|rejected
            $table->foreignId('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });

        // View opsional untuk list cepat
        DB::statement("
            CREATE OR REPLACE VIEW v_debtors_basic AS
            SELECT d.*, p.name AS project_name
            FROM debtors d
            JOIN projects p ON p.id = d.project_id
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('debtors');
        DB::statement('DROP VIEW IF EXISTS v_debtors_basic');
    }
};
