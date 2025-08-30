<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pensions', function (Blueprint $t) {
            $t->id();

            // Identitas
            $t->string('nip', 30)->unique();
            $t->string('name', 200);
            $t->string('address_line1', 255)->nullable();
            $t->string('address_line2', 255)->nullable();
            $t->string('address_line3', 255)->nullable();
            $t->string('phone', 50)->nullable();
            $t->string('phone_alt', 50)->nullable();
            $t->string('ktp', 50)->nullable();
            $t->date('birth_date')->nullable();
            $t->string('npwp', 50)->nullable();

            // Cabang / jenis
            $t->string('branch_code', 20)->nullable();
            $t->string('branch_name', 120)->nullable();
            $t->string('jenis_pensiun_code', 20)->nullable();
            $t->string('jenis_pensiun_name', 120)->nullable();
            $t->string('kode_jiwa', 20)->nullable();
            $t->string('nomor_skep', 60)->nullable();
            $t->date('tmt_pensiun')->nullable();
            $t->date('tanggal_skep')->nullable();

            // Juru bayar & rekening
            $t->string('payer_code', 40)->nullable();
            $t->string('payer_name', 200)->nullable();
            $t->string('account_number', 60)->nullable();

            // Komponen gaji/tunjangan
            $t->decimal('penpok', 18, 2)->default(0);
            $t->decimal('tunj_istri', 18, 2)->default(0);
            $t->decimal('tunj_anak', 18, 2)->default(0);
            $t->decimal('tunj_beras', 18, 2)->default(0);
            $t->decimal('penyesuaian', 18, 2)->default(0);
            $t->decimal('tunj_bulat', 18, 2)->default(0);
            $t->decimal('total_kotor', 18, 2)->default(0);
            $t->decimal('bersih', 18, 2)->default(0);

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pensions');
    }
};
