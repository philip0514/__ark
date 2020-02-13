<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_relations', function (Blueprint $table) {
            $table->bigInteger('media_id');
            $table->bigInteger('media_relations_id');
            $table->string('media_relations_type', 100);
            $table->string('type', 50)->nullable();
            $table->integer('sort')->default(0)->nullable();

            $table->unique(['media_id', 'media_relations_id', 'media_relations_type', 'type'], 'media_uq');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_relations');
    }
}
