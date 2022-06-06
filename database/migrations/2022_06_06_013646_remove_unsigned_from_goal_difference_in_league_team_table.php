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
            $table->integer('goal_difference')
                ->default(0)
                ->change();
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
            $table->unsignedInteger('goal_difference')
                ->default(0)
                ->change();
        });
    }
};
