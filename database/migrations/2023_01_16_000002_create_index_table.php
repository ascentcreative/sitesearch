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
        //
        Schema::create('sitesearch_index', function (Blueprint $table) {
            $table->id();
            $table->string('indexable_type')->index();
            $table->integer('indexable_id')->index();
            $table->text('content');
            $table->timestamps();

            $table->index(['indexable_type', 'indexable_id']);
        });

        DB::statement('ALTER TABLE sitesearch_index ADD FULLTEXT sitesearch_index(content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('sitesearch_index');

    }
};
