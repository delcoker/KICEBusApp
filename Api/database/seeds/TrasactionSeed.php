<?php

use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           // Uncomment the below to wipe the table clean before populating
        DB::table('transactions')->delete();
 
        $transaction = array(
            ['transaction_id' => 1, 'name' => '33SeaterAtomic-Kwabenya', 'plate' => 'AU123GH', 'created_at' => new DateTime,'capacity' => 33,  'updated_at' => new DateTime],
            ['transaction_id' => 2, 'name' => '33SeaterCTK-Aburi', 'plate' => 'AU321GH',  'created_at' => new DateTime, 'capacity' => 33,  'updated_at' => new DateTime],
			['transaction_id' => 3, 'name' => '12Berekuso-Ashesi', 'plate' => 'AU381GH',  'created_at' => new DateTime, 'capacity' => 12,  'updated_at' => new DateTime],
			
        );
 
        // Uncomment the below to run the seeder
        DB::table('routes')->insert($transaction);
    }
}
