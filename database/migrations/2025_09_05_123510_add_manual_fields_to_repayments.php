<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('repayments', function (Blueprint $t) {
            if (!Schema::hasColumn('repayments','debit_date')) {
                $t->date('debit_date')->nullable()->after('period_date');
            }
            if (!Schema::hasColumn('repayments','note')) {
                $t->string('note', 500)->nullable()->after('debit_date'); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('repayments', function (Blueprint $t) {
            if (Schema::hasColumn('repayments','note')) $t->dropColumn('note');
            if (Schema::hasColumn('repayments','debit_date')) $t->dropColumn('debit_date');
        });
    }
};
