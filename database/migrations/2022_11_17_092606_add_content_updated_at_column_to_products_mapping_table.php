<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentUpdatedAtColumnToProductsMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_mapping', function (Blueprint $table) {
            //
            $table->timestamp('content_updated_at')->nullable()->after('source_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_mapping', function (Blueprint $table) {
            //
        });
    }
}
