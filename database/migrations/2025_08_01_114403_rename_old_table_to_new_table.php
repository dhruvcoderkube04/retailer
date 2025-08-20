<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameOldTableToNewTable extends Migration
{
    public function up()
    {
        Schema::rename('store_customer_details', 'customer_details');
    }

    public function down()
    {
        Schema::rename('customer_details', 'store_customer_details');
    }
}

