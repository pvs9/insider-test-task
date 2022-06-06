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
        Schema::table('league_team', static function (Blueprint $table) {
            $table->unsignedTinyInteger('win_probability')
                ->nullable()
                ->after('goal_difference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('league_team', static function (Blueprint $table) {
            $table->dropColumn('win_probability');
        });
    }
};
