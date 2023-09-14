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
        Schema::table('sitesearch_index', function (Blueprint $table) {
            $table->timestamp('indexable_created_at')->after('content')->index();
            $table->timestamp('indexable_updated_at')->after('content')->index();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('sitesearch_index', function (Blueprint $table) {
            $table->dropColumn('indexable_created_at');
            $table->dropColumn('indexable_updated_at');
        }); 

    }
};
