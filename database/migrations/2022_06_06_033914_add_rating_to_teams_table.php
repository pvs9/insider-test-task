<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('teams', static function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')
                ->default(0)
                ->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('teams', static function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};
