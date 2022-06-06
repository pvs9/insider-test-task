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
        Schema::create('league_matches', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(League::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('week');
            $table->foreignId('away_team_id')
                ->constrained('teams')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('house_team_id')
                ->constrained('teams')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('away_points')->nullable();
            $table->unsignedTinyInteger('house_points')->nullable();
            $table->timestamps();

            $table->index(['league_id', 'week']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('league_matches');
    }
};
