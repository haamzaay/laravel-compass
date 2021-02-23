<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsCompositeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections_composite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id');
            $table->uuid('route_id');
            $table->timestamps();
        });

        Schema::table('collections_composite', function (Blueprint $table) {
            $table->foreign('collection_id')->references('id')->on('routeable_collections');
            $table->foreign('route_id')->references('uuid')->on('compass_routeables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections_composite');
    }
}
