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
            $table->uuid('domain')->notNullable()->index();
            $table->uuid('site')->nullable()->default(null)->index();
            $table->uuid('assignee')->nullable()->default(null)->index();
            $table->uuid('updated_by')->nullable()->default(null)->index();
            $table->foreign('updated_by')->references('uuid')->on('users');
            $table->foreign('domain')->references('uuid')->on('domains')->onDelete('cascade');
            $table->foreign('site')->references('uuid')->on('sites')->onDelete('set null');
            $table->foreign('assignee')->references('uuid')->on('users')->onDelete('set null');
            $table->boolean('testing')->default(false);
            $table->boolean('setup')->default(false);
            $table->string('email')->notNullable();
            $table->string('password')->notNullable();
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
