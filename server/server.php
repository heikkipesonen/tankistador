<?php

require_once('bower_components/idiorm/idiorm.php');
require_once('bower_components/flight/flight/Flight.php');
require_once('classes/server.class.php');
require_once('classes/playerlist.class.php');
require_once('classes/sqlhelper.class.php');
require_once('classes/wg.class.php');
require_once('classes/util.class.php');
require_once('conf.php');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  // return only the headers and not the content
  // only allow CORS if we're doing a GET - i.e. no saving for now.
  //if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'GET') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, User, Password');
  //}
  exit;
}
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, User, Password');

// function __autoload($class){
// 	include 'classes/'.strtolower($class).'.class.php';
// }


function isValidType($type){
	return in_array($type, array('all','clan','company','stronghold_defense','stronghold_skirmish','team') );
}

function validateList($id_list){
	return preg_match('/^([0-9\,])+$/', $id_list);
}


function error(){
	Flight::json(array('ok'=>false,'error'=>true));

}

//print_r( wgapi::getClan(500028261) );

Flight::route('GET /recent/@list', function($list){
	if (validateList($list)){

	}
});



/**
 * update all watched players
 */
Flight::route('GET /update', function(){
	Flight::json( Server::updateWatchedPlayers() );
});

Flight::route('GET /clan/add/@clan_id', function($clan_id){
	if (validateList($clan_id)){
		Flight::json( array('ok'=>true, 'data'=> Server::addClan($clan_id) ) );
	} else {
		error();
	}
});

Flight::route('GET /clan/@clan_id/list', function($clan_id){

	if (validateList($clan_id)){
		Flight::json( array('ok'=>true, 'data'=> Server::getClanPlayers($clan_id) ) );
	} else {
		error();
	}
});


Flight::route('GET /player/list', function(){
	$request = Flight::request();
	$offset = 0;
	$limit = 100;



	if (isset($request->query) && isset($request->query['offset']) && is_numeric($request->query['offset']) ){
		$offset = intval( $request->query['offset'] );
	}

	if (isset($request->query) && isset($request->query['limit']) && is_numeric($request->query['limit'])){
		$limit = intval( $request->query['limit'] );
	}

	Flight::json( array('ok'=>true, 'data'=> Server::getPlayersList($offset, $limit) ) );
});

Flight::route('GET /player/history/@id_list(/@type)', function($id_list, $type){

	if (validateList($id_list) && (!$type OR isValidType($type))){

		$request = Flight::request();
		$list = explode(',',$id_list);

		$start_datetime = null;
		$end_datetime = null;

		if (isset($request->query['start_datetime']) && is_numeric($request->query['start_datetime'])){
		 $start_datetime = $request->query['start_datetime'];
		}

		if (isset($request->query['end_datetime']) && is_numeric($request->query['end_datetime'])){
			$end_datetime = $request->query['end_datetime'];
		}
		// $type = $request->query['type'] || null;
		// $offset = $request->query['offset'] || 0;
		// $limit = $request->query['limit'] || 100;

		Flight::json( Server::getHistory($list, $type, $start_datetime, $end_datetime) );
	} else {
		error();
	}
});


Flight::start();



//Server::updateClan( '500028261' );