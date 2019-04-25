<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackerSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('tracker-session.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type',\gpibarra\TrackerSession\Models\TrackerSession::$enumType)->index();
//            $table->morphs('authenticatable')->nulleable();
            $table->string('authenticatable_type')->nullable();
            $table->integer('authenticatable_id')->unsigned()->nullable();
            $table->string('sessionKey')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at')->nullable()->index();
            $table->timestamp('see_at')->nullable()->index();
            $table->timestamp('logout_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable()->index();

            $table->index(['authenticatable_id', 'authenticatable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('tracker-session.table_name'));
    }
}

