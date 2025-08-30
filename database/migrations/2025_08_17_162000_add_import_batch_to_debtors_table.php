<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void {
        Schema::table('debtors', function (Blueprint $t) {
            if (!Schema::hasColumn('debtors', 'import_batch')) {
                $t->uuid('import_batch')->nullable()->index();
            }
        });
    }

    public function down(): void {
        Schema::table('debtors', function (Blueprint $t) {
            if (Schema::hasColumn('debtors', 'import_batch')) {
                $t->dropColumn('import_batch');
            }
        });
    }
};
