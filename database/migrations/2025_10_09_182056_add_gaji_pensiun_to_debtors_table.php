<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->decimal('gaji_pensiun', 18, 2)->default(0)->after('plafond');
        });
    }

    public function down(): void
    {
        Schema::table('debtors', function (Blueprint $table) {
            $table->dropColumn('gaji_pensiun');
        });
    }
};
