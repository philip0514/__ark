<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->text('content')->nullable();
            $table->integer('news_time')->default(0)->nullable();
            $table->integer('display')->default(0)->nullable();
            $table->integer('created_by')->nullable()->comment('創建者');
            $table->integer('updated_by')->nullable()->comment('最後修改者');
            $table->integer('deleted_by')->nullable()->comment('刪除者');
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->softDeletes()->comment('軟刪除');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
