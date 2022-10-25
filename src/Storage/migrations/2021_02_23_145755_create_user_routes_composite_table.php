<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRoutesCompositeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_routes_composite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('collection_id');
            $table->boolean('shared')->default(0);
            $table->string('shared_by')->nullable();
            $table->timestamps();
        });

        Schema::table('user_routes_composite', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('collection_id')->references('id')->on('routeable_collections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_routes_composite');
    }
}