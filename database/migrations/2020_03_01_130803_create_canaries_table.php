<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanariesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('canaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique()->notNullable()->index();
            $table->uuid('domain')->index();
            $table->uuid('site')->index();
            $table->uuid('assignee')->index();
            $table->foreign('domain')->references('uuid')->on('domains')->notNullable()->onDelete('cascade');
            $table->foreign('site')->references('uuid')->on('sites')->notNullable()->onDelete('cascade');
            $table->foreign('assignee')->references('uuid')->on('users')->nullable()->default(null);
            $table->boolean('testing')->default(false);
            $table->json('data')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('canaries');
    }
}
