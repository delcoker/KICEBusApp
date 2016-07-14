<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use App\Transaction;
use Auth;
use Illuminate\Support\Facades\Input;
use Response;
use Carbon\Carbon;
use App\Routes;
use App\Buses;
use App\Drivers;
use App\DefaultSettings;
use App\User;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller {

    public function transaction(Request $request) {

        //rules for a transaction request
        $transactionRules = [
            'bus_id' => 'required',
            'driver_id' => 'required',
            'route_id' => 'required'
        ];

        //validate transaction request againt transaction rules
        $validation = Validator::make($request->all(), $transactionRules);
        $ar = array();

        if ($validation->passes()) {

            //Get list of occupants to proceed with transaction
            $passengers = $request->occupants;

//            dd($passengers);

            foreach ($passengers as $i) {
//                $i = explode(": ", $i); //return an array format data

                $findAccount = \App\User::find((float) $i['occupant_id']);

                //checks if a user has a minimum balance
                if ($findAccount['balance'] >= (float) $request->amount) {
                    //reduce and update new balance
                    $findAccount->balance = $findAccount['balance'] - (float) $request->amount;
                    $findAccount->update();

                    //Get information about the conductor
                    $authenticatedUser = Auth::user();

                    //create a new transaction
                    $transaction = new Transaction();
                    $transaction->conductor_id = $authenticatedUser->id;
                    $transaction->occupation_id = $findAccount->id;
                    $transaction->bus_id = $request->bus_id;
                    $transaction->driver_id = $request->driver_id;
                    $transaction->route_id = $request->route_id;
                    $transaction->save();
                } else {
                    array_push($ar, $findAccount);
//                    dd($findAccount);
                    $res_json = json_encode($ar);
                }
            }
//            return $res_json;
            if (count($ar) > 0) {
                $res_json = '{"status":"success","failed_transactions": ' . $res_json . ',' . $this->unpaidCustomers() . '}';
                //return Response::json($res_json)->setCallback(Input::get('callback'));
            } else {
                $res_json = ('{"status":"success","message": "All transaction were succesful ",' . $this->unpaidCustomers() . '}');
            }
        } else {
            $res_json = ('{"status":"fail","message":' . $validation->errors() . ',' . $this->unpaidCustomers() . '}');
        }
//        $idrisJSON = $this->unpaidCustomers();
//
//        array_push($res_json, $idrisJSON);
//
//        dd($res_json);


        return Response::json($res_json)->setCallback(Input::get('callback'));
    }

    private function unpaidCustomers() {
        $user = Auth::user();
        $data = Input::get();

        $currentTime = Carbon::now();
        $startTime = Carbon::now();
//                        $endTime = Carbon::now();

        $startTime->hour = 12;
        $startTime->minute = 00;
        $startTime->second = 00;
        // $currentTime->gt($second);


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

            $res_json = '' .
                    '"username":"' . $user->username .
                    '","defaultSettings":' . json_encode($settings) .
                    ',"unpaidCustomers":' . json_encode($unpaidOccupants) .
                    ',"routes":' . json_encode($routes) .
                    ',"drivers":' . json_encode($drivers) .
                    ',"buses":' . json_encode($buses) .
                    '';
        }
        return $res_json;
    }

}
