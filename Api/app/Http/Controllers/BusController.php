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
use App\BusLocation;
use Carbon\Carbon;

class BusController extends Controller {

    //

    function addBusLocation() {


        $data = Input::get();
        $busLocation = new BusLocation;

        $busLocation->name = $data['name'];
        $busLocation->longitude = $data['longitude'];
        $busLocation->latitude = $data['latitude'];
        $busLocation->route_id = $data['route_id'];
        $busLocation->bus_id = $data['bus_id'];

        if ($busLocation->save()) {
            $res_json = '{"status":"success", "message":"Bus location added"}';
        } else {
            $res_json = '{"status":"failed", "message":"Unable to add bus location"}';
        }

        return Response::json($res_json)->setCallback(Input::get('callback'));
    }

}
