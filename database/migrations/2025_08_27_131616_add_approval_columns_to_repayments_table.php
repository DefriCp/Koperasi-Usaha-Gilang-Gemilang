<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('repayments', function (Blueprint $t) {
            if (!Schema::hasColumn('repayments','approved_by')) {
                $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $t->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('repayments','rejected_by')) {
                $t->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
                $t->timestamp('rejected_at')->nullable();
                $t->string('rejected_reason', 500)->nullable();
            }
            if (!Schema::hasColumn('repayments','status')) {
                $t->string('status')->default('UNPAID')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('repayments', function (Blueprint $t) {
            $t->dropColumn(['approved_by','approved_at','rejected_by','rejected_at','rejected_reason','status']);
        });
    }
};
