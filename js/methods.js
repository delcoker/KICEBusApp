/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var bus_id = 0;
var route_id = 0;
var driver_id = 0;

var settings_bus_id = 0;
var settings_route_id = 0;
var settings_driver_id = 0;

var callback_results;

var all_routes = {};
var all_busses = {};
var all_drivers = {};

var curLong = 200;
var curLat = 100;


var http_or_https = "        s               ";                 // insert an "s" anywhere here if you want the request to go as https
//var ip = "192.168.8.102";                                     //  Home
var ip = "10.10.26.210";                                        //  School
//var ip = "192.168.100.10";                                    //  Apa

//var phonegap = "https://10.10.50.37/AshesiBusApp/Api/public/";
var phonegap = "http" + http_or_https.trim() + "://" + ip + "/AshesiBusApp/Api/public/";
//var phonegap = "http://localhost/AshesiBusApp/Api/public/";


$(document).ready(function () {
    $.mobile.allowCrossDomainPages = true;
    $.support.cors = true;
    window.setInterval(function () {
        sendBusXY();
    }, 60000);

});

function login() {

    if ($("#ip").val().length > 2) {
        ip = $("#ip").val();
        phonegap = "http" + http_or_https.trim() + "://" + ip + "/AshesiBusApp/Api/public/";
//        prompt("url", phonegap);
    }

    var username = $("#username").val();
    var password = $("#password").val();
    var url = phonegap + "login";

    var res = syncAjaxGetLogin(url, {username: username, password: password});
}

function sendBusXY() {

    var options = {
        enableHighAccuracy: true,
//        timeout: 5000,
        maximumAge: 0
    };

    var Geo = {};
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(success, error, options);
    }

    //Get the latitude and the longitude;
    function success(position) {
        Geo.lat = position.coords.latitude;
        Geo.lng = position.coords.longitude;
        populateHeader(Geo.lat, Geo.lng);
    }

    function error() {
        console.log("Geocoder failed");
    }

    function populateHeader(lat, lng) {
        curLong = lng;
        curLat = lat;
        $('.Lat').html(lat);
        $('.Long').html(lng);
    }

    var location_name = "unknown but in gh";

    var url = phonegap + 'buslocation';

    if (bus_id === 0 || curLat === 100 || curLong === 200 || curLat === -0.1869644 && curLong === 5.6037168) {
        return;
    }


    syncAjaxAddBusLocation(url, {longitude: curLong
        , latitude: curLat
        , bus_id: bus_id
        , route_id: route_id
        , name: location_name});


}

$(document).on("pageshow", "#map-page", function () {

////    sendBusXY();
    var defaultLatLng = new google.maps.LatLng(4.6037168, -0.7869644); // Default to Hollywood, CA when no geolocation support
    if (navigator.geolocation) {
        function success(pos) {
            // Location found, show map with these coordinates
            drawMap(new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude));
        }
        function fail(error) {
            drawMap(defaultLatLng); // Failed to find location, show default map
        }
        // Find the users current position.  Cache the location for 5 minutes, timeout after 6 seconds
        navigator.geolocation.getCurrentPosition(success, fail, {maximumAge: 500000, enableHighAccuracy: true, timeout: 6000});
    } else {
        drawMap(defaultLatLng); // No geolocation support, show default map
    }

    function drawMap(latlng) {
        var myOptions = {
            zoom: 15,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
        // Add an overlay to the map of current lat/lng
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: "The Bus!"
        });
        marker.addListener('click', toggleBounce);

        function toggleBounce() {
            if (marker.getAnimation() !== null) {
                marker.setAnimation(null);
            } else {
                marker.setAnimation(google.maps.Animation.BOUNCE);
            }
        }

        google.maps.event.addListener(map, 'click', function (me) {
            moveMarker();
//            var result = [me.latLng.lat(), me.latLng.lng()];
//            transition(result);
        });

        function moveMarker() {
            var latlng = new google.maps.LatLng(curLong, curLat);
            marker.setPosition(latlng);
//            if (i != numDeltas) {
//                i++;
//                setTimeout(moveMarker, delay);
//            }
        }
    }


});

function route_save(id) {

    settings_route_id = id;
    var curr = "";
    $.each(all_routes, function (key, value) {
        if (value.route_id === id) {
            curr = value.name;
        }
//        curr = value.route_id === id ? value.name : "";
    });
    $('#selected_route').text("Current Route: " + curr);
}

function driver_save(id) {
    settings_driver_id = id;
    var curr = "";
    $.each(all_drivers, function (key, value) {
        if (value.driver_id === id) {
            curr = value.name;
        }
//        curr = value.driver_id === id ? value.name : "";
    });
    $('#selected_driver').text("Current Driver: " + curr);
}

// when
function bus_save(id) {
    settings_bus_id = id;
    var curr = "";
    $.each(all_busses, function (key, value) {
        if (value.bus_id === id) {
            curr = value.name;
        }
//        curr = value.bus_id === id ? value.name : '';
    });
    $('#selected_bus').text("Current Bus: " + curr);
}

function settings_save() {

//    settings_route_id;
//    settings_driver_id;
//    settings_bus_id;

    if (settings_bus_id === 0) {
        alert("What bus will pass this route?");
        return;
    }
    else if (settings_driver_id === 0) {
        alert("There must be a driver for that bus");
        return;
    }
    if (settings_route_id === 0) {
        alert("Select a route.");
        return;
    }

    var url = phonegap + "settings";
    syncAjaxSaveSettings(url, {route_id: settings_route_id,
        driver_id: settings_driver_id,
        bus_id: settings_bus_id});
//    var res = {status: "success"};

}

function driver_select(id) {
    route_id = id;
    var url = phonegap + "drivers";
    //    var res = syncAjaxGet(url, {conductor_id: conductor_id, password: password});
//  ***************  dummy data
    var res = {status: "success", drivers: [{"id": "1", "name": "Peter Chek"}, {"id": "2", "name": "Esi Ansah"}]};
//****************

    res.drivers.sort(sort_by('name', false, function (a) {
        return a.toUpperCase();
    }));
    if (!(res.status === 'success')) {

        alert('Failed to login');
        return;
    }
    var listings = '<ul data-role="listview" data-inset="true" data-filter="true" id="drivers">';
    $.each(res.drivers, function (key, value) {
        listings += '<li><a href="#bus_select" onclick="bus_select(' + value.id + ')">';
        listings += "<img src='" + 'resources/2.jpg' + "' alt=''>";
        listings += "<h2>" + value.name + "</h2>";
        listings += "<p>" + value.name + "</p>";
        listings += '</a></li> ';
    });
    listings += '</ul>';
    $("#drivers").replaceWith(listings);
    $('#drivers').listview().listview('refresh');
    window.open("index.html#driver_select", "_self");
}

function bus_select(id) {
    driver_id = id;
    var url = phonegap + "busses";
    //    var res = syncAjaxGet(url, {conductor_id: conductor_id, password: password});
//  ***************  dummy data
    var res = {status: "success", busses: [{"id": "1", "name": "30 Seater Blue", "plate": "GT9344", "capacity": "30"},
            {"id": "4", "name": "10 Seater White", "plate": "GHS44", "capacity": "10"},
            {"id": "6", "name": "30 Seater Green", "plate": "ASH02", "capacity": "30"}]};
//****************
    res.busses.sort(sort_by('name', false, function (a) {
        return a.toUpperCase();
    }));
    if (!(res.status === 'success')) {

        alert('Failed to login');
        return;
    }
    var listings = '<ul data-role="listview" data-inset="true" data-filter="true" id="busses">';
    $.each(res.busses, function (key, value) {
        listings += '<li><a href="#passengers_select" onclick="passengers_select(' + value.id + ')">';
        listings += "<img src='" + 'resources/2.jpg' + "' alt=''>";
        listings += "<h2>" + value.name + "</h2>";
        listings += "<p>" + value.name + "</p>";
        listings += '</a></li> ';
    });
//    <a href="#passengers_select" onclick="passengers_select()">
//                            <img src='resources/2.jpg' alt=''>
//                            <h2>BUSSES</h2>
//                            <p>Bus 1</p>
    listings += '</ul>';
    $("#busses").replaceWith(listings);
    $('#busses').listview().listview('refresh');
    window.open("index.html#bus_select", "_self");
}

function passengers_select(res) {

    bus_id = res.defaultSettings[0].bus_id;
    driver_id = res.defaultSettings[0].driver_id;
    route_id = res.defaultSettings[0].route_id;

//****************

    if (res.unpaidCustomers.length > 0)
        res.unpaidCustomers.sort(sort_by('name', 0, function (a) {
            return a.toUpperCase();
        }));
    if (!(res.status === 'success')) {

        alert('Failed to login');
        return;
    }
    var listings = '<fieldset data-role="controlgroup" id="passengers" data-filter="true" data-icon="false">';
    if (res.unpaidCustomers.length > 0)
        $.each(res.unpaidCustomers, function (key, value) {
            listings += '<input type="checkbox" class="passengers_checkbox" name="passengers_checkbox" id="' + value.id + '"/>';
            listings += '<label for="' + value.id + '">';
            listings += '<span style="display: inline-block;" >';
            listings += "<span><img src='" + 'resources/2.jpg' + "' alt='' width='40' height='40'>";
            listings += "<span style='float:right; margin-left:10px'><div> " + value.name + "<br>";
            listings += "" + value.balance + "</div> </span> </span>  ";
            listings += "</span>";
            listings += '</label>';
        });
    listings += '</fieldset>';
    $("#passengers").replaceWith(listings);
    $('#passengers').controlgroup().controlgroup('refresh');
    window.open("index.html#passengers_select", "_self");
}

function payment_amount() {


    var payers = [];
    $('input[type="checkbox"]').filter('.passengers_checkbox').each(function () {
        var id = $(this).attr('id');
        if ($(this).is(':checked')) {
            // perform operation for checked
//            alert(id);
            payers.push({occupant_id: id});
        }
        else {
            // perform operation for unchecked
        }
    });

    if (payers.length < 1) {
        alert("No one has been selected");
        return;
    }

    window.open("index.html#payment_amount", "_self");
}

//sources: http://stackoverflow.com/questions/11138898/check-if-a-jquery-mobile-checkbox-is-checked
function confirm_payment() {
    var amount = $("#amount").val();
    if (amount === "" || amount < 1) {
        alert("Please enter a value");
        return;
    }
    else if (amount === 0) {
        alert("Please enter a value");
    }
    else if (amount === 0) {
        alert("Please enter a value");
    }
    var res = {amount: amount,
        bus_id: bus_id,
        driver_id: driver_id,
        route_id: route_id};
    var payers = [];
    $('input[type="checkbox"]').filter('.passengers_checkbox').each(function () {
        var id = $(this).attr('id');
        if ($(this).is(':checked')) {
            // perform operation for checked
//            alert(id);
            payers.push({occupant_id: id});
        }
        else {
            // perform operation for unchecked
        }
    });
    res.occupants = payers;

    if (res.occupants.length < 1) {
        alert("No one has been selected");
        return;
    }

    var url = phonegap + "transaction";
//    prompt("url", url);
    syncAjaxConfirmPayment(url, res);
}

function request_routes() {
    $(".caretaker_name").text(caretaker);
    $(".childs_name").text("Welcome " + child_name + " " + child_age);
//    $(".child_age").text(child_age);
}

function syncAjax(u) {
    prompt("url", u);
    var obj = $.ajax({url: u, async: false});
    return $.parseJSON(obj.responseText);
}

function logout() {
    window.open("index.html", "_self");
}

function first_page_get_info() {
    child_name = $("#child_name").val();
    if ($("#caretaker_name_signup").val() !== '') {
        caretaker = $("#caretaker_name_signup").val();
    }
    child_age = $("#child_age").val();
}

//-------------------------------------

function submit() {
    first_page_get_info();
//    window.open("index.html#thank_you_dialog", "_self");
}

function welcomers() {
    always();
//    window.open("index.html#welcome_after_signup", "_self");

}

function go_to_tit_bits() {
//    window.open("index.html#tit_bits", "_self");
}

function start_lesson() {
    $(".childs_name_hello").text("Hello " + child_name + " " + child_age);
//    window.open("index.html#start_lesson", "_self");
}

function syncAjaxGet(u, arr) {
//    alert(arr[0]);
    var obj = $.ajax({url: u, async: false
        , type: 'GET'
        , crossDomain: true
        , data: arr
        , dataType: 'jsonp'
        , jsonpCallback: 'callAjaxSuccessful'
    });
}

function syncAjaxGetLogin(u, arr) {
    var obj = $.ajax({url: u, async: false
        , type: 'GET'
        , crossDomain: true
        , data: arr
        , dataType: 'jsonp'
        , jsonpCallback: 'callbackAjaxLoginSuccessful'
    });
}

function syncAjaxSaveSettings(u, arr) {
    $.ajax({url: u, async: false
        , type: 'GET'
        , crossDomain: true
        , data: arr
        , dataType: 'jsonp'
        , jsonpCallback: 'callbackAjaxSaveSettings'
    });
}

function syncAjaxConfirmPayment(u, arr) {
    $.ajax({url: u, async: false
        , type: 'GET'
        , crossDomain: true
        , data: arr
        , dataType: 'jsonp'
        , jsonpCallback: 'callbackAjaxPay'
    });
}

function syncAjaxAddBusLocation(u, arr) {
    $.ajax({url: u, async: false
        , type: 'GET'
        , crossDomain: true
        , data: arr
        , dataType: 'jsonp'
        , jsonpCallback: 'callbackAjaxAddBusLocation'
    });
}

function callbackAjaxLoginSuccessful(data) {
    var res = $.parseJSON(data);
    if (!(res.status === 'success')) {

        alert(res.message);
        return;
    }
    if (res.defaultSettings.first_time) {
// load routes
        res.routes.sort(sort_by('name', false, function (a) {
            return a.toUpperCase();
        }));
        all_routes = res.routes;
        var route_listings = '<ul data-role="listview" data-inset="true" data-filter="true" id="settings_route">';
        $.each(res.routes, function (key, value) {
            route_listings += '<li><a href="#route_save" onclick="route_save(' + value.route_id + ')">';
            route_listings += "<img src='" + 'resources/2.jpg' + "' alt=''>";
            route_listings += "<h2>" + value.name + "</h2>";
            route_listings += "<p>" + value.name + "</p>";
            route_listings += '</a></li> ';
        });
        route_listings += '</ul>';
        $("#settings_route").replaceWith(route_listings);
        $('#settings_route').listview().listview('refresh');
        // load drivers

        all_drivers = res.drivers;
        res.drivers.sort(sort_by('name', false, function (a) {
            return a.toUpperCase();
        }));
        var driver_listings = '<ul data-role="listview" data-inset="true" data-filter="true" id="settings_driver">';
        $.each(res.drivers, function (key, value) {
            driver_listings += '<li><a href="#driver_save" onclick="driver_save(' + value.driver_id + ')">';
            driver_listings += "<img src='" + 'resources/2.jpg' + "' alt=''>";
            driver_listings += "<h2>" + value.name + "</h2>";
            driver_listings += "<p>" + value.name + "</p>";
            driver_listings += '</a></li> ';
        });
        driver_listings += '</ul>';
        $("#settings_driver").replaceWith(driver_listings);
        $('#settings_driver').listview().listview('refresh');
        // load busses

        res.buses.sort(sort_by('name', false, function (a) {
            return a.toUpperCase();
        }));
        all_busses = res.buses;

        var bus_listings = '<ul data-role="listview" data-inset="true" data-filter="true" id="settings_bus">';

        $.each(res.buses, function (key, value) {

            bus_listings += '<li><a href="#bus_save" onclick="bus_save(' + value.bus_id + ')">';
            bus_listings += "<img src='" + 'resources/2.jpg' + "' alt=''>";
            bus_listings += "<h2>" + value.name + "</h2>";
            bus_listings += "<p>" + value.name + "</p>";
            bus_listings += '</a></li> ';
        });
        bus_listings += '</ul>';
        $("#settings_bus").replaceWith(bus_listings);
        $('#settings_bus').listview().listview('refresh');
        window.open("index.html#settings_select", "_self");
    }
    else {
        passengers_select(res);
    }

//    prompt("successful ajax call ", data);
}

function callbackAjaxSaveSettings(data) {
    var res = $.parseJSON(data);
    if (!(res.status === "success")) {
        alert("Your settings could not be saved. Try at a later time");
    }
    passengers_select(res);
}

function callbackAjaxPay(data) {
    var res = $.parseJSON(data);
    if (!(res.status === "success")) {
        alert(res.message);

    }

    var failed = "";
    if ("failed_transactions" in res) {
        $.each(res.failed_transactions, function (key, value) {
            failed += value.name + "\n";
        });

        alert("Failed Transactions: (Most likely broke) \n" + failed);
//        return;
    }
    passengers_select(res);
}

function callbackAjaxAddBusLocation(data) {
    var res = $.parseJSON(data);
    if (!(res.status === "success")) {
        alert("Bus Location Not Added");
        return;
    }

}

function errorFunction() {
    alert("ajax function failed");
}

//sources: http://stackoverflow.com/questions/979256/sorting-an-array-of-javascript-objects
function sort_by(field, reverse, primer) {

    var key = primer ?
            function (x) {
                return primer(x[field]);
            } :
            function (x) {
                return x[field];
            };
    var reverse = !reverse ? 1 : -1;
    return function (a, b) {
        return a = key(a), b = key(b), reverse * ((a > b) - (b > a));
    };
}
;
