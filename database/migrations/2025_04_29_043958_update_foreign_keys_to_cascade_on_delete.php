<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         // Eliminar la foreign key actual
        /* DB::statement('ALTER TABLE audit_logs DROP FOREIGN KEY fk_audit_agent');

         // Crear nuevamente con ON DELETE CASCADE
         DB::statement('ALTER TABLE audit_logs ADD CONSTRAINT fk_audit_agent FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE');

         // Eliminar foreign key actual (sin cascade)
        DB::statement('ALTER TABLE achievement_user DROP FOREIGN KEY achievement_user_user_id_foreign');

        // Crear nuevamente con ON DELETE CASCADE
        DB::statement('ALTER TABLE achievement_user ADD CONSTRAINT achievement_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');*/


        // social_networks.user_id -> users.id
        /*Schema::table('social_networks', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
        });*/
        // Eliminar la foreign key actual (por nombre real)
        /*DB::statement('ALTER TABLE social_networks DROP FOREIGN KEY social_networks_agent_id_foreign');

        // Volver a crearla con ON DELETE CASCADE
        DB::statement('ALTER TABLE social_networks ADD CONSTRAINT social_networks_agent_id_foreign FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE');*/

        // achievement_user.user_id -> users.id
        // achievement_user.achievement_id -> achievements.id
        /*Schema::table('achievement_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['achievement_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('achievement_id')->references('id')->on('achievements')->onDelete('cascade');
        });*/

        // area_speciality_user.user_id -> users.id
        // area_speciality_user.area_speciality_id -> area_specialities.id
        /*Schema::table('area_speciality_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['area_speciality_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('area_speciality_id')->references('id')->on('area_specialities')->onDelete('cascade');
        });*/
        // Eliminar claves foráneas existentes
       /* DB::statement('ALTER TABLE area_speciality_user DROP FOREIGN KEY area_speciality_user_user_id_foreign');
        DB::statement('ALTER TABLE area_speciality_user DROP FOREIGN KEY area_speciality_user_area_speciality_id_foreign');

        // Volver a crearlas con ON DELETE CASCADE
        DB::statement('ALTER TABLE area_speciality_user ADD CONSTRAINT area_speciality_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE area_speciality_user ADD CONSTRAINT area_speciality_user_area_speciality_id_foreign FOREIGN KEY (area_speciality_id) REFERENCES area_specialities(id) ON DELETE CASCADE');*/

        // agents_documentations.agent_id -> agents.id
        /*Schema::table('agents_documentations', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
        });*/
         // Eliminar la FK actual
        /* DB::statement('ALTER TABLE agents_documentations DROP FOREIGN KEY agents_documentations_agent_id_foreign');

         // Crear la FK con ON DELETE CASCADE
         DB::statement('ALTER TABLE agents_documentations ADD CONSTRAINT agents_documentations_agent_id_foreign FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE');*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cascade_on_delete', function (Blueprint $table) {
            //
        });
    }
};
