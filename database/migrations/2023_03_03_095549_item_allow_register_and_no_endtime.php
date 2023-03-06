<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ItemAllowRegisterAndNoEndtime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->tinyInteger('allow_re_register')->default(0);
            $table->integer('day_cycles')->nullable();
            $table->integer('schedule_cycles')->nullable();
            $table->tinyInteger('activiy_trial')->nullable();
            $table->tinyInteger('activiy_test')->nullable();
            $table->tinyInteger('activiy_visit')->nullable();
        });

        Schema::create('item_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('item_id')->index();
            $table->string('type');
            $table->bigInteger('user_id')->index();
            $table->string('date')->nullable();
            $table->string('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('allow_re_register');
            $table->dropColumn('day_cycles');
            $table->dropColumn('schedule_cycles');
            $table->dropColumn('activiy_trial');
            $table->dropColumn('activiy_test');
            $table->dropColumn('activiy_visit');
        });

        Schema::dropIfExists('item_activities');
    }
}
