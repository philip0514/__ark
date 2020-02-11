<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('administrator_id')->nullable()->comment('上傳者的id');
            $table->string('name', 255)->nullable()->comment('名稱')->unique();
            $table->string('orig_name', 255)->nullable()->comment('原始檔名');
            $table->string('ext', 20)->nullable()->comment('副檔名');
            $table->string('title', 255)->nullable()->comment('圖片標題');
            $table->text('description')->nullable()->comment('說明');
            $table->text('content')->nullable()->comment('圖片內容');
            $table->string('file_path', 255)->nullable()->comment('檔案路徑');
            $table->text('file_url')->nullable()->comment('檔案網址');
            $table->string('file_type', 100)->nullable()->comment('檔案類型');
            $table->integer('file_size')->nullable()->comment('檔案容量');
            $table->integer('display')->default(0)->nullable()->comment('是否顯示');
            $table->integer('image_width')->nullable()->comment('圖片寬度');
            $table->integer('image_height')->nullable()->comment('圖片高度');
            $table->string('image_size_str', 255)->nullable()->comment('圖片尺寸html');
            $table->string('image_type', 100)->nullable()->comment('圖片類型');
            $table->integer('custom_crop')->default(0)->nullable();
            $table->json('crop_data')->nullable()->comment('裁切資訊');
            $table->integer('crop_later')->default(0)->nullable();
            $table->integer('is_image')->default(0)->nullable()->comment('是否為圖片');
            $table->string('excerpt', 255)->nullable()->comment('圖片摘要');
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
        Schema::dropIfExists('media');
    }
}
