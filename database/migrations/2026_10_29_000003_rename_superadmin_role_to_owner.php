<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['SUPERADMIN', 'OWNER', 'ADMIN', 'CASHIER'])->default('CASHIER')->change();
        });

        DB::table('users')->where('role', 'SUPERADMIN')->update(['role' => 'OWNER']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['OWNER', 'ADMIN', 'CASHIER'])->default('CASHIER')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['SUPERADMIN', 'OWNER', 'ADMIN', 'CASHIER'])->default('CASHIER')->change();
        });

        DB::table('users')->where('role', 'OWNER')->update(['role' => 'SUPERADMIN']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['SUPERADMIN', 'ADMIN', 'CASHIER'])->default('CASHIER')->change();
        });
    }
};
