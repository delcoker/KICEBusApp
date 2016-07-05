<?php

use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           // Uncomment the below to wipe the table clean before populating
        DB::table('drivers')->delete();
 
        $driver = array(
            ['driver_id' => 1, 'name' => 'Peter', 'driver_number' => '111222', 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['driver_id' => 2, 'name' => 'Kofi Adjertey', 'driver_number' => '111333',  'created_at' => new DateTime,   'updated_at' => new DateTime],
			['driver_id' => 3, 'name' => 'Kwame', 'driver_number' => '111444',  'created_at' => new DateTime,  'updated_at' => new DateTime],
			
        );
 
        // Uncomment the below to run the seeder
        DB::table('drivers')->insert($driver);
    }
}
