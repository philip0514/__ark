<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn(['created_at', 'updated_at']);
            $table->string('facebook_id', 100)->nullable();
            $table->string('twitter_id', 100)->nullable();
            $table->string('google_id', 100)->nullable();
            $table->integer('gender')->default(0)->nullable();
            $table->string('birthday', 100)->nullable();
            $table->integer('nation_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('area_id')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('mobile', 100)->nullable();
            $table->string('code', 100)->nullable();
            $table->integer('display')->default(1)->nullable();
            $table->integer('checked')->default(0)->nullable();
            $table->string('checked_auth', 100)->nullable();
            $table->integer('newsletter')->default(0)->nullable()->comment('是否訂閱電子報');
            $table->integer('newsletter_submit')->default(0)->nullable()->comment('是否為來自訂閱電子報的會員');
            $table->timestamp('login_time')->nullable();
            $table->integer('created_by')->nullable()->comment('創建者');
            $table->integer('updated_by')->nullable()->comment('最後修改者');
            $table->integer('deleted_by')->nullable()->comment('刪除者');
            $table->softDeletes()->comment('軟刪除');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn(['facebook_id', 'twitter_id', 'google_id', 'gender', 'birthday', 'nation_id', 'city_id', 'area_id', 'address', 'phone', 'mobile', 'display', 'checked', 'checked_auth', 'newsletter', 'newsletter_submit', 'login_time', 
            'created_by', 'updated_by', 'deleted_by', 'deleted_at']);
        });
    }
}
