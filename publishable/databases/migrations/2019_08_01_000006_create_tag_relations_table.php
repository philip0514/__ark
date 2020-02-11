<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_relations', function (Blueprint $table) {
            $table->bigInteger('tag_id');
            $table->bigInteger('tag_relations_id');
            $table->string('tag_relations_type', 100);
            $table->integer('sort')->default(0)->nullable();

            $table->unique(['tag_id', 'tag_relations_id', 'tag_relations_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_relations');
    }
}
