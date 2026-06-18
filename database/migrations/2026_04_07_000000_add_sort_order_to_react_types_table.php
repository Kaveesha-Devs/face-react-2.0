<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('react_types') && !Schema::hasColumn('react_types', 'sort_order')) {
            Schema::table('react_types', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->after('tamil_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('react_types') && Schema::hasColumn('react_types', 'sort_order')) {
            Schema::table('react_types', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
