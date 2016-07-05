<?php

use Illuminate\Database\Seeder;

class BuslocaationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           // Uncomment the below to wipe the table clean before populating
        DB::table('buslocations')->delete();
 
        buslocation = array(
            ['buslocations_id' => 1, 'name' => '', 'longitude' => '11', 'latitude' => '11','route_id' => 1,'bus_id' => 1,'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['buslocations_id' => 2, 'name' => '', 'longitude' => '11','latitude' => '11', 'route_id' => 1,'bus_id' => 1, 'created_at' => new DateTime,   'updated_at' => new DateTime],
			['buslocations_id' => 3, 'name' => '', 'longitude' => '11', 'latitude' => '11','route_id' => 2, 'bus_id' => 2,'created_at' => new DateTime,  'updated_at' => new DateTime],
			
        );
 
        // Uncomment the below to run the seeder
        DB::table('buslocations')->insert($buslocation);
    }
}
