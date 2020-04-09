<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailsTable extends Migration
{
    public function up()
    {
        Schema::create('mails', function (Blueprint $table) {

			$table->increments(id)->unsigned();
			$table->string('name',100)->nullable()->comment('標題');
			$table->text('content')->nullable()->comment('內容');
			$table->integer('user_id',11)->nullable()->default('NULL')->comment('user_id');
			$table->string('user_name',100)->nullable()->default('NULL')->comment('姓名');
			$table->text('user_email')->nullable()->default('NULL')->comment('電子信箱');
			$table->integer('type')->nullable()->comment('類型');
			$table->integer('created_by')->nullable()->comment('創建者');
			$table->integer('updated_by')->nullable()->comment('最後修改者');
			$table->timestamp('created_at')->useCurrent()->nullable();
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
			$table->primary('id');

        });
    }

    public function down()
    {
        Schema::dropIfExists('mails');
    }
}