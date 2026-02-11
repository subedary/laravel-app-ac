<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts_items_links', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Keys
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('item_id');

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
        Schema::dropIfExists('contacts_items_links');
    }
};