<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Input;
use DB;
use Auth;
use Validator;
use App\User;
use App\DefaultSettings;
use App\Routes;
use App\Buses;
use App\Drivers;
use Carbon\Carbon;
use Response;

class UsersController extends Controller {

//


    public function postLogin() {

        $data = Input::get();

//check username and password. Validate inputs
        $login_rules = array(
            'username' => 'required',
            'password' => 'required|min:5|max:60'
        );

        $validator = Validator::make($data, $login_rules);

        if ($validator->passes()) {

            $user = User::where('username', '=', $data['username'])->first();
//dd($user);
            if ($user && $user->password == $data['password']) {

                Auth::login($user);
                $authenticatedUser = Auth::user();
                // dd($authenticatedUser);
                if ($authenticatedUser->role == 'conductor') {

                    $defaultSettings = DefaultSettings::where('occupant_id', '=', $authenticatedUser->id)->first();
//dd($defaultSettings->first_time);
                    $routes = Routes::all();
                    $drivers = Drivers::all();
                    $buses = Buses::all();

                    if ($defaultSettings->first_time == 1) {

                        $res_json = '{"status":"success","role":"' . $authenticatedUser->role .
                                '","username":"' . $authenticatedUser->username .
                                '","defaultSettings":' . json_encode($defaultSettings) .
                                ',"routes":' . json_encode($routes) .
                                ',"drivers":' . json_encode($drivers) .
                                ',"buses":' . json_encode($buses) .
                                '}';
                    } elseif ($defaultSettings->first_time == 0) {
# code...
//get user settings
                        $settings = DefaultSettings::where('occupant_id', '=', $authenticatedUser->id)
                                ->where('first_time', '=', 0)
//->join('occupants', 'occupants.id', '=', 'defaultSettings.occupant_id')
                                ->join('busses', 'busses.bus_id', '=', 'defaultsettings.bus_id')
                                ->join('drivers', 'drivers.driver_id', '=', 'defaultsettings.driver_id')
                                ->join('routes', 'routes.route_id', '=', 'defaultsettings.route_id')
                                ->select('drivers.driver_id', 'drivers.name as driverName', 'routes.route_id', 'routes.name as routeName', 'busses.bus_id', 'busses.name as busName')
                                ->get();

//dd($settings);
                        $currentTime = Carbon::now();
                        $startTime = Carbon::now();
//                        $endTime = Carbon::now();

                        $startTime->hour = 12;
                        $startTime->minute = 00;
                        $startTime->second = 00;
                        // $currentTime->gt($second);
                        if ($currentTime->hour < 12) {
                            $unpaidOccupants = DB::select(DB::raw('SELECT distinct `occupants`.`id`, `occupants`.`name` ,`occupants`.`balance` '
                                                    . 'FROM occupants where occupants.id not in ( SELECT occupation_id from transactions '
                                                    . 'where date(`transactions`.`created_at`) ="' . Carbon::today()->toDateString() . '" '
                                                    . 'and transactions.created_at < "' . $startTime . '")'));
                        } else if ($currentTime->hour >= 12) {
                            $unpaidOccupants = DB::select(DB::raw('SELECT distinct `occupants`.`id`, `occupants`.`name` ,`occupants`.`balance` '
                                                    . 'FROM occupants where occupants.id not in ( SELECT occupation_id from transactions '
                                                    . 'where date(`transactions`.`created_at`) ="' . Carbon::today()->toDateString() . '" '
                                                    . 'and transactions.created_at > "' . $startTime . '")'));
                        }

//                        dd($unpaidOccupants);
//get list of unpaid users
//                        $unpaidOccupants = User::leftjoin('transactions', 'transactions.occupation_id', '=', 'occupants.id')
//                                ->whereNotBetween('transactions.created_at', array($startTime, $endTime))
//                                ->whereDate('transactions.created_at', '=', Carbon::today()->toDateString())
//                                ->orWhere('transactions.transaction_id', "=", null)
//                                ->select('occupants.id', 'occupants.name', 'occupants.balance')
//                                ->distinct()
//                                ->get();
                        //  -- ocuppant that paid after 12
                        //dd(DB::getQueryLog($unpaidOccupants));
                        $res_json = '{"status":"success","role":"' . $authenticatedUser->role .
                                '","username":"' . $authenticatedUser->username .
                                '","defaultSettings":' . json_encode($settings) .
                                ',"unpaidCustomers":' . json_encode($unpaidOccupants) .
                                ',"routes":' . json_encode($routes) .
                                ',"drivers":' . json_encode($drivers) .
                                ',"buses":' . json_encode($buses) .
                                '}';
                    }
                }
            } else {

                $res_json = '{"status":"fail", "message":"Wrong login details"}';
            }
        } else {
            $res_json = '{"status":"fail","message":' . $validator->messages() . '}';
        }


        return Response::json($res_json)->setCallback(Input::get('callback'));
    }

    public function logout() {
# code...
        Auth::logout();
        return '{"status":"success", "message":"logout success"}';
    }

    public function saveSettings() {
        $user = Auth::user();
        $data = Input::get();
        $currentTime = Carbon::now();
        $startTime = Carbon::now();
//                        $endTime = Carbon::now();

        $startTime->hour = 12;
        $startTime->minute = 00;
        $startTime->second = 00;
        // $currentTime->gt($second);
        if ($currentTime->hour < 12) {
            $unpaidOccupants = DB::select(DB::raw('SELECT distinct `occupants`.`id`, `occupants`.`name` ,`occupants`.`balance` '
                                    . 'FROM occupants where occupants.id not in ( SELECT occupation_id from transactions '
                                    . 'where date(`transactions`.`created_at`) ="' . Carbon::today()->toDateString() . '" '
                                    . 'and transactions.created_at < "' . $startTime . '")'));
        } else if ($currentTime->hour >= 12) {
            $unpaidOccupants = DB::select(DB::raw('SELECT distinct `occupants`.`id`, `occupants`.`name` ,`occupants`.`balance` '
                                    . 'FROM occupants where occupants.id not in ( SELECT occupation_id from transactions '
                                    . 'where date(`transactions`.`created_at`) ="' . Carbon::today()->toDateString() . '" '
                                    . 'and transactions.created_at > "' . $startTime . '")'));
        }
        //  dd($startTime);
        $routes = Routes::all();
        $drivers = Drivers::all();
        $buses = Buses::all();

//dd($data);
        $settings = DefaultSettings::where('occupant_id', '=', $user->id)->first();
//dd($settings);

        $settings->bus_id = $data['bus_id'];
        $settings->driver_id = $data['driver_id'];
        $settings->route_id = $data['route_id'];
        $settings->first_time = 0;

        if ($settings->save()) {
            $settings = $settings::join('busses', 'busses.bus_id', '=', 'defaultsettings.bus_id')
                    ->join('drivers', 'drivers.driver_id', '=', 'defaultsettings.driver_id')
                    ->join('routes', 'routes.route_id', '=', 'defaultsettings.route_id')
                    ->select('drivers.driver_id', 'drivers.name as driverName', 'routes.route_id', 'routes.name as routeName', 'busses.bus_id', 'busses.name as busName')
                    ->get();
            //dd(Carbon::today()->toDateString());
            // dd($unpaidOccupants);
            $res_json = '{"status":"success","role":"' . $user->role .
                    '","username":"' . $user->username .
                    '","defaultSettings":' . json_encode($settings) .
                    ',"unpaidCustomers":' . json_encode($unpaidOccupants) .
                    ',"routes":' . json_encode($routes) .
                    ',"drivers":' . json_encode($drivers) .
                    ',"buses":' . json_encode($buses) .
                    '}';
        } else {
            $res_json = '{"status":"fail","message":"Unable to save settings. Please  try again."}';
        }

        return response()->json($res_json)->setCallback(Input::get('callback'));
    }



    function getUnpaidOccupants(){

    	$user = Auth::user();
        $startTime = Carbon::now();
        $startTime->hour = 5;
        $startTime->minute = 00;
        $startTime->second = 00;


        $endTime = Carbon::now();
        $endTime->hour = 9;
        $endTime->minute = 00;
        $endTime->second = 00;

    	$unpaidOccupants = User::leftjoin('transactions', 'transactions.occupation_id', '=', 'occupants.id')
                    ->whereNotBetween('transactions.created_at', array($startTime, $endTime))
                    ->orWhere('transactions.transaction_id', "=", null)
                    ->select('occupants.id', 'occupants.name', 'occupants.balance')
                    ->distinct()
                    ->get();


        $res_json = '{"status":"success", "unpaidOccupants":'. json_encode($unpaidOccupants).'}';


		return $res_json;
    	}


}
