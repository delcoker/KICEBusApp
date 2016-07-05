<?php

use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Uncomment the below to wipe the table clean before populating
        DB::table('prices')->delete();
 
        $price = array(
            ['price_id' => 1, 'price' => 2.00, 'created_at' => new DateTime, 'updated_at' => new DateTime],
            ['price_id' => 2, 'price' => 3.00, 'created_at' => new DateTime, 'updated_at' => new DateTime],
        );
 
        // Uncomment the below to run the seeder
        DB::table('prices')->insert($price);
    }
}
