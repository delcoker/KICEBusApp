<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use App\Transaction;
use Auth;
use Illuminate\Support\Facades\Input;

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
            $passengersData = json_decode($passengers, true); //return an array format data

            foreach ($passengersData as $i) {
                $myVar = json_encode($i);
                $var = json_decode($myVar, true);

                //find a paticular user
                $findAccount = \App\User::find($var['id']);

                //checks if a user has a minimum balance
                if ($findAccount['balance'] > (float) $request->amount) {
                    //reduce and update new balance
                    $findAccount->balance = $findAccount['balance'] - (float) $request->amount;
                    $findAccount->update();

                    //Get information about the conductor
                    $authenticatedUser = Auth::user();

                    //create a new transaction
                    $transaction = new Transaction();
                    $transaction->conductor_id = $authenticatedUser->conductor_id;
                    $transaction->occupation_id = $var['id'];
                    $transaction->bus_id = $request->bus_id;
                    $transaction->driver_id = $request->driver_id;
                    $transaction->route_id = $request->route_id;
                    $transaction->save();
                } else {
                    array_push($ar, $findAccount);
                    $res_json = json_encode($ar);
                }
            }
            $res_json = '{"status":"Fail","Message": "' . $res_json . '"}';
            return Response::json($res_json)->setCallback(Input::get('callback'));
        }
        $res_json = '{"status":"fail","message":' . $validation->errors() . '}';
        return Response::json($res_json)->setCallback(Input::get('callback'));
    }

}
