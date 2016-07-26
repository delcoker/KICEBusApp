<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	//
   
    protected $table = 'transactions';
	protected $guarded ='transaction_id';
	protected $primaryKey ='transaction_id';
	
	public function occupant()
	{
		return $this->belongsTo('\App\Models\Occupant', 'occupant_id', 'occupant_id');
	}
	
	public function driver()
	{
		return $this->belongsTo('\App\Models\Driver', 'driver_id', 'driver_id');
	}

	public function bus()
	{
		return $this->belongsTo('\App\Models\Bus', 'bus_id', 'bus_id');
	}

	public function route()
	{
		return $this->belongsTo('\App\Models\Route', 'route_id', 'route_id');
	}
}
