<?php

use Illuminate\Database\Seeder;

class OccupantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Uncomment the below to wipe the table clean before populating
        DB::table('occupants')->delete();
 
        $occupant = array(
            ['occupant_id' => 1, 'name' => 'Esi Yeboah', 'username' => 'esi.yeboah','email' => 'esi.yeenuwa@ashesi.edu.gh', 'role' => 'passenger','occupant_number' => '111111','password' => 'esi.yeenuwa','created_at' => new DateTime, 'updated_at' => new DateTime],
            ['occupant_id' => 2, 'name' => 'Kingston C', 'username' => 'kingston.coker','email' => 'kingston.coker@ashesi.edu.gh', 'role' => 'passenger','occupant_number' => '222222','password' => 'kingston.coker','created_at' => new DateTime, 'updated_at' => new DateTime],
            ['occupant_id' => 3, 'name' => 'Iddris Abdul','username' => 'iddris.abdul', 'email' => 'iddris.abdul@ashesi.edu.gh','role' => 'conductor','occupant_number' => '333333','password' => 'iddris.abdul','created_at' => new DateTime, 'updated_at' => new DateTime],
        );
 
        // Uncomment the below to run the seeder
        DB::table('occupants')->insert($occupant);
    }
}
