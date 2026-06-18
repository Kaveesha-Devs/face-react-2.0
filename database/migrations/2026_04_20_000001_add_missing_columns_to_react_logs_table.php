<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('react_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('react_logs', 'note')) {
                $table->text('note')->nullable()->after('section_id');
            }
            if (!Schema::hasColumn('react_logs', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('note');
            }
            if (!Schema::hasColumn('react_logs', 'device_info')) {
                $table->text('device_info')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('react_logs', function (Blueprint $table) {
            $table->dropColumn(['note', 'ip_address', 'device_info']);
        });
    }
};
