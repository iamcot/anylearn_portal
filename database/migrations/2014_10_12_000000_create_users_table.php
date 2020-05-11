<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('role', 20)->index();
            $table->tinyInteger('status')->index()->default(0);
            $table->tinyInteger('update_doc')->default(0);
            $table->bigInteger('ballance')->default(0);
            $table->integer('expire')->index()->default(0);
            $table->bigInteger('commission')->default(0);
            $table->bigInteger('user_id')->index()->nullable();
            $table->tinyInteger('is_hot')->index()->default(0);
            $table->text('image')->nullable();
            $table->text('introduce')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('user_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index();
            $table->string('type');
            $table->string('store')->default('local');
            $table->string('file_ext')->nullable();
            $table->string('data');
            $table->timestamps();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->string('type', 20)->index();
            $table->bigInteger('user_id')->index();
            $table->text('image')->nullable();
            $table->text('short_content')->nullable();
            $table->text('content');
            $table->bigInteger('price')->default(0);
            $table->bigInteger('sale_price')->nullable();
            $table->integer('date_start')->default(0);
            $table->integer('date_end')->default(0);
            $table->tinyInteger('is_hot')->index()->default(0);
            $table->tinyInteger('status')->index()->default(1);
            $table->text('seo_title')->nullable();
            $table->string('seo_url')->nullable()->index();
            $table->text('seo_desc')->nullable();
            $table->timestamps();
        });

        Schema::create('course_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('item_id')->index();
            $table->integer('date')->index();
            $table->text('content')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('course_participations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('item_id')->index();
            $table->bigInteger('schedule_id')->index();
            $table->bigInteger('organizer_user_id')->index();
            $table->bigInteger('participant_user_id')->index();
            $table->tinyInteger('organizer_confirm')->default(0);
            $table->tinyInteger('participant_confirm')->default(0);
            $table->string('organizer_comment')->nullable();
            $table->string('participant_comment')->nullable();
            $table->timestamps();
        });

        Schema::create('course_resources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('item_id')->index();
            $table->string('type', 20)->index();
            $table->text('title')->nullable();
            $table->text('desc')->nullable();
            $table->text('data');
            $table->timestamps();
        });

        Schema::create('configurations', function (Blueprint $table) {
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index();
            $table->integer('quantity')->default(1);
            $table->bigInteger('amount')->default(0);
            $table->string('status')->index();
            $table->string('delivery_name')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->string('payment')->nullable();
            $table->timestamps();
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index();
            $table->bigInteger('item_id')->index();
            $table->bigInteger('unit_price');
            $table->bigInteger('paid_price');
            $table->integer('quanity');
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index();
            $table->bigInteger('item_id')->index();
            $table->bigInteger('ref_user_id')->index();
            $table->string('content');
            $table->bigInteger('amount');
            $table->bigInteger('ref_amount');
            $table->integer('status')->index();
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_documents');
        Schema::dropIfExists('items');
        Schema::dropIfExists('course_schedules');
        Schema::dropIfExists('course_resources');
        Schema::dropIfExists('course_participations');
        Schema::dropIfExists('configurations');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('commissions');
    }
}
