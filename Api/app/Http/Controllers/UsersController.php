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

                        $now = new DateTime(); //current date/time
                        $startTime = date_time_set($now, 06, 00, 00);
                        $endTime = date_time_set($now, 18, 00, 00);

                        //get list of unpaid users
                        $unpaidOccupants = User::join('transactions', 'transactions.occupation_id', '=', 'occupants.id')
                                ->whereNotBetween('transactions.created_at', array($startTime, $endTime))
                                ->select('occupants.name', 'occupants.balance')
                                ->get();


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


        return $res_json;
    }

    public function logout() {
        # code...
        Auth::logout();
        return '{"status":"success", "message":"logout success"}';
    }

    public function saveSettings() {
        $user = Auth::user();
        $data = Input::get();
        $now = new DateTime(); //current date/time
        $startTime = date_time_set($now, 06, 00, 00);
        $endTime = date_time_set($now, 18, 00, 00);
        $routes = Routes::all();
        $drivers = Drivers::all();
        $buses = Buses::all();

        //dd($data);
        $settings = DefaultSettings::where('occupant_id', '=', $user->id)->first();
        //dd($settings[0]->bus_id);

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

            $unpaidOccupants = User::join('transactions', 'transactions.occupation_id', '=', 'occupants.id')
                    ->whereNotBetween('transactions.created_at', array($startTime, $endTime))
                    ->select('occupants.name', 'occupants.balance')
                    ->get();

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

        return $res_json;
    }

}
