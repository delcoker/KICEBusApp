/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var bus_id = 0;
var route_id = 0;
var driver_id = 0;


var phonegap = "http://192.168.1.101/aomg/";



function login() {
    var conductor_id = $("#conductor_id").val();
    var password = $("#password").val();

    var url = phonegap + "login";

//    var res = syncAjaxGet(url, {conductor_id: conductor_id, password: password});
//    dummy data
    var res = {status: "success", role: "conductor", routes: [{"id": "1", "name": "ctk-aburi"}, {"id": "2", "name": "atomic-abom"}]};

//****************
    if (!(res.status === 'success')) {

        alert('Failed to login');
        return;
    }
    var listings = '<ul data-role="listview" data-inset="true" data-filter="true" id="routes">';
    $.each(res.routes, function (key, value) {
        listings += '<li><a href="#driver_select" onclick="driver_select(' + value.id + ')">';
        listings += "<img src='" + 'resources/2.jpg' + "' alt=''>";
        listings += "<h2>" + value.name + "</h2>";
        listings += "<p>" + value.name + "</p>";
        listings += '</a></li> ';
    });
    listings += '</ul>';

    $("#routes").replaceWith(listings);

    $('#routes').listview().listview('refresh');


    window.open("index.html#route_select", "_self");
//    $(document).on('pageinit', '#route_select', function () {
//
//    });
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

function passengers_select(id) {
    bus_id = id;
    var url = phonegap + "passensgers";
    //    var res = syncAjaxGet(url, {conductor_id: conductor_id, password: password});
//  ***************  dummy data
    var res = {status: "success", passengers: [{"id": "1", "name": "Joseph Nti", "role": "passenger", "amount_left": "200.50"},
            {"id": "2", "name": "Esi Yenuah", "role": "passenger", "amount_left": "323.50"},
            {"id": "4", "name": "Iddris Alba", "role": "passenger", "amount_left": "2.50"},
            {"id": "5", "name": "Jessica Alba", "role": "passenger", "amount_left": "99.50"},
            {"id": "8", "name": "Zul Kyei", "role": "passenger", "amount_left": "100.50"},
            {"id": "10", "name": "King Coker", "role": "passenger", "amount_left": "4430.50"}]};

//****************

    res.passengers.sort(sort_by('name', false, function (a) {
        return a.toUpperCase();
    }));

    if (!(res.status === 'success')) {

        alert('Failed to login');
        return;
    }
    var listings = '<fieldset data-role="controlgroup" id="passengers" data-filter="true" data-icon="false">';
    $.each(res.passengers, function (key, value) {


        listings += '<input type="checkbox" class="passengers_checkbox" name="passengers_checkbox" id="' + value.id + '"/>';
        listings += '<label for="' + value.id + '">';
        listings += '<span style="display: inline-block;" >';

        listings += "<div ><span><img src='" + 'resources/2.jpg' + "' alt='' width='40' height='40'>";
        listings += "<span style='float:right; margin-left:10px'><div> " + value.name + "<br>";
        listings += " " + value.amount_left + "</div> </span> </span>  ";

        listings += "</div>";
        listings += "</div>";
        listings += "</span>";

//        listings += "<span>";
//        listings += "<input type='number' id='sth'  placeholder='amount' step='0.01'  style='float:left;display:inline'>";
//        listings += '<label for="' + "sth" + '">';
//        listings += "</span>";

        listings += '</label>';
    });
    listings += '</fieldset>';

    $("#passengers").replaceWith(listings);

    $('#passengers').listview().listview('refresh');

    window.open("index.html#passengers_select", "_self");

}

function payment_amount() {
    window.open("index.html#payment_amount", "_self");
}

//sources: http://stackoverflow.com/questions/11138898/check-if-a-jquery-mobile-checkbox-is-checked
function confirm_payment() {
    var amount = $("#amount").val();

    var payers = [{amount: amount, bus_id: bus_id, driver_id: driver_id, route_id: route_id}];
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
    var obj = $.ajax(u, {async: false
        , type: 'GET'
        , data: arr // {cmd:3} //JSON.stringify(arr)     //  {cmd:3}// ?cmd=3
//        , dataType: String
//        , success: callAjaxSuccessful   //            function(data){alert(data);}
//        , error: errorFunction
    });
    return $.parseJSON(obj.responseText);
}

function asyncAjaxGet(u, arr) {
//    alert(arr[0]);
    var obj = $.ajax(u, {async: true
        , type: 'GET'
        , data: arr // {cmd:3} //JSON.stringify(arr)     //  {cmd:3}// ?cmd=3
//        , dataType: String
        , success: callAjaxSuccessful   //            function(data){alert(data);}
        , error: errorFunction});
    return $.parseJSON(obj.responseText);
}

function syncAjaxPost(u, arr) {
//    alert(arr[0]);
    var obj = $.ajax(u, {async: false
        , type: 'POST'
        , data: arr // {cmd:3} //JSON.stringify(arr)     //  {cmd:3}// ?cmd=3
//        , dataType: String
        , success: callAjaxSuccessful   //            function(data){alert(data);}
        , error: errorFunction});
    return $.parseJSON(obj.responseText);
}

function callAjaxSuccessful(data) {
//    prompt("successful ajax call ", data);
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