<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('debtor_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debtor_id')->constrained()->cascadeOnDelete();

            // MANUAL header
            $table->date('input_date')->nullable();                 // TANGGAL INPUT
            $table->string('loan_number')->nullable();              // NOMOR PINJAMAN (11 juga)

            // 1–6
            $table->string('project_text')->nullable();             // Project (teks asal, opsional)
            $table->string('payer')->nullable();                    // Jurabayar
            $table->string('pension')->nullable();                  // Taspen/Asabri
            $table->string('area')->nullable();                     // I/II/III/IV
            $table->string('branch')->nullable();                   // Cabang
            $table->string('submission_type')->nullable();          // NEW/TAKE OVER/TOP UP

            // 8 – alamat
            $table->text('address')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();

            // 12–14, 26–28 (angka)
            $table->decimal('interest_rate',8,4)->nullable();       // Bunga Bank (%)
            $table->decimal('baa_percent',8,4)->nullable();         // Biaya Adm Angs (%)
            $table->date('agreement_date')->nullable();             // Tgl Perjanjian Kredit (15)
            $table->date('start_credit_date')->nullable();          // TGL Awal Kredit (16)
            $table->date('end_credit_date')->nullable();            // TGL Akhir Kredit (17)
            $table->date('disbursement_date')->nullable();          // TGL Droping (19)

            $table->decimal('provisi',18,2)->nullable();
            $table->decimal('administrasi',18,2)->nullable();
            $table->decimal('asuransi',18,2)->nullable();
            $table->decimal('tata_kelola',18,2)->nullable();
            $table->decimal('angsuran_dimuka',18,2)->nullable();

            $table->date('birth_date')->nullable();                 // 25 TGL Lahir
            $table->decimal('baa_value',18,2)->nullable();          // 27 Biaya Adm Angsuran (nilai)
            $table->decimal('total_installment',18,2)->nullable();  // 28 Total Angsuran
            $table->string('account_number')->nullable();           // 29 Nomor Rekening
            $table->string('bank_alias')->nullable();               // 30 Bank (alias Project)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debtor_details');
    }
};
