<?php

use App\Models\League;
use App\Models\Team;
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
        Schema::create('league_team', static function (Blueprint $table) {
            $table->foreignIdFor(League::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignIdFor(Team::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('played')->default(0);
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('draws')->default(0);
            $table->unsignedInteger('losses')->default(0);

            $table->primary(['league_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('league_teams');
    }
};
