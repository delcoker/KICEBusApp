<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Input;
use DB;
use Auth;
use DateTime;
use Validator;
use App\User;
use App\DefaultSettings;
use App\Routes;
use App\Buses;
use App\Drivers;

class UsersController extends Controller{
    //


public function postLogin(){

		$data = Input::get();
		
		//check username and password. Validate inputs
		$login_rules = array(
			'username'=>'required',
			'password'=>'required|min:5|max:60'
		);

		$validator = Validator::make($data, $login_rules);

		if($validator->passes()){

			$user = User::where('username','=',$data['username'])->first();
			//dd($user);
			if($user && $user->password==$data['password']){
				
				Auth::login($user);
				$authenticatedUser = Auth::user();
				if ($authenticatedUser->role=='conductor'){

					$defaultsettings = DefaultSettings::where('occupant_id','=',$authenticatedUser->id)->first();
					//dd($defaultsettings->first_time);

					if($defaultsettings->first_time == 1){
						$routes = Routes::all();
						$drivers = Drivers::all();
						$buses = Buses::all();
						$res_json = '{"status":"success","role":"'.$authenticatedUser->role.
									'","username":"'.$authenticatedUser->username.
									'","defaultsettings":'.json_encode($defaultsettings).
									',"routes":'.json_encode($routes).
									',"drivers":'.json_encode($drivers).
									',"buses":'.json_encode($buses).
									'}';



					}
					elseif ($defaultsettings->first_time == 0) {
						# code...
						$defaultsettings = DefaultSettings::where('occupant_id','=',$authenticatedUser->id)->first();

						$res_json='{"status":"success","role":"'.$authenticatedUser->role.
								  '","username":'.$authenticatedUser->username.
								  '","defaultsettings":'.json_encode($defaultsettings).
								  '}';

					}

					
				}
				return $res_json;
			}
			else{

				return '{"status":"fail", "message":"Wrong login details"}';
			}
	    
	    }

		return '{"status":"fail","message":'.$validator->messages().'}';
      
      }


public function logout()
{
	# code...
	Auth::logout();
	return '{"status":"success", "message":"logout success"}';
}


}
