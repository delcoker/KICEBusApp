<?php

use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           // Uncomment the below to wipe the table clean before populating
        DB::table('busses')->delete();
 
        $route = array(
            ['bus_id' => 1, 'name' => '33SeaterAtomic-Kwabenya', 'plate' => 'AU123GH', 'created_at' => new DateTime,'capacity' => 33,  'updated_at' => new DateTime],
            ['bus_id' => 2, 'name' => '33SeaterCTK-Aburi', 'plate' => 'AU321GH',  'created_at' => new DateTime, 'capacity' => 33,  'updated_at' => new DateTime],
			['bus_id' => 3, 'name' => '12Berekuso-Ashesi', 'plate' => 'AU381GH',  'created_at' => new DateTime, 'capacity' => 12,  'updated_at' => new DateTime],
			
        );
 
        // Uncomment the below to run the seeder
        DB::table('routes')->insert($route);
    }
}
