<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPpiIndexOnWpsSegments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $driver = Schema::connection($this->getConnection())->getConnection()->getDriverName();
        
        if ($driver !== 'sqlite') {
            Schema::disableForeignKeyConstraints();
        
            Schema::table('WPS_Segments', function(Blueprint $table)
            {
                $table->string('PPI')->index()->unique()->change();
            });
            
            Schema::table('WPS_Segments', function(Blueprint $table)
            {
                $table->dropPrimary('wps_segments_ppi_primary');
            });
            
            Schema::table('WPS_Segments', function(Blueprint $table)
            {
                $table->increments('id');
            });
            
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
