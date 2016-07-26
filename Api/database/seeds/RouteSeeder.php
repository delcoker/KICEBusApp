<?php

use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           // Uncomment the below to wipe the table clean before populating
        DB::table('routes')->delete();
 
        $route = array(
            ['route_id' => 1, 'name' => 'Atomic-Kwabenya', 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['route_id' => 2, 'name' => 'CTK-Aburi', 'created_at' => new DateTime, 'updated_at' => new DateTime],
        );
 
        // Uncomment the below to run the seeder
        DB::table('routes')->insert($route);
    }
}
