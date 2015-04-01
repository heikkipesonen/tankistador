<?php

class WG{

	public static $url = 'https://api.worldoftanks.eu';
	public static $application_id = 'demo';


	/**
	 * get data from wargaming api
	 * @param  String $from     first route param
	 * @param  String $what     second route param
	 * @param  Array  $params   get parameters
	 * @param  string $endpoint
	 * @return object           response object from wargaming api server
	 */
	public static function get($from, $what, $params, $endpoint = 'wot'){
		$getParams = array('application_id='.self::$application_id);

		foreach ($params as $key => $value) {
			$getParams[] =	$key.'='.$value;
		}

		return json_decode( file_get_contents(self::$url.'/'.$endpoint.'/'.$from.'/'.$what.'/?'. implode('&', $getParams) ) );
	}


	/**
	 * simplify retarded api response data
	 * remove useless wrappings and return the required data
	 * @param  object $data api response data
	 * @return object       simplified object or false if not successfull
	 */
	public static function simplify($data){
		$response = array();
		if (isset($data->status) && $data->status == 'ok' && $data->count > 0){
			foreach ($data->data as $id=>$data){
				$response[] = $data;
			}

			if (count($response) ==1 ){
				return $response[0];
			}

			return $response;
		} else if (is_object($data)){

			foreach ($data as $id => $value) {
				$response[] = $value;
			}

			return $response;
		}
		return false;
	}


	/**
	 * get player from api
	 * @param  String,Array  $idList
	 * @param  boolean $simplify
	 * @return object
	 */
	public static function getPlayer($idList, $simplify = true){
		if (is_array($idList)){
			$idList = implode(',', $idList);
		}

		$response = self::get('account', 'info', array('account_id'=> $idList ) );

		if ($simplify){
			return self::simplify( $response );
		} else {
			return $response;
		}
	}


	/**
	 * get clan from api
	 * @param  string, array  $idList
	 * @param  boolean $simplify
	 * @return object
	 */
	public static function getClan($idList, $simplify = true){
		if (is_array($idList)){
			$idList = implode(',', $idList);
		}

		$response = self::get('clans', 'info', array('clan_id'=> $idList), 'wgn');
		if ($simplify){
			return self::simplify( $response );
		} else {
			return $response;
		}
	}


}