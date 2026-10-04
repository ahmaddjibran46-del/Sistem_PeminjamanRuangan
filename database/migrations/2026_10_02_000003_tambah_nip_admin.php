<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admin', 'nip')) {
            Schema::table('admin', fn (Blueprint $t) => $t->string('nip', 18)->nullable()->unique());
        }
    }

    public function down(): void
    {
        Schema::table('admin', fn (Blueprint $t) => $t->dropUnique(['nip']));
        Schema::table('admin', fn (Blueprint $t) => $t->dropColumn('nip'));
    }
};