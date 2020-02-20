<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->integer('type')->nullable();
            $table->string('url', 200)->nullable();
            $table->bigInteger('start_time')->nullable();
            $table->bigInteger('end_time')->nullable();
            $table->string('title', 100)->nullable()->comment('標題');
            $table->string('description', 200)->nullable()->comment('敘述');
            $table->text('content')->nullable()->comment('內容');
            $table->integer('display')->default(0)->nullable()->comment('是否顯示');
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
        Schema::dropIfExists('pages');
    }
}
