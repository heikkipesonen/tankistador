<?php
class Server{

	public static function getClanPlayers($clan_id){
		$players = ORM::for_table('battle_statistics')
			->where('clan_id', $clan_id)
			->select('account_id')
			->distinct()
			->find_array();

		return Util::pluck('account_id', $players);
	}



	public static function getAllPlayers(){
		$list = ORM::for_table('battle_statistics')
		->distinct()
		->select('account_id')
		->find_array();


		return Util::pluck('account_id', $list);
	}

	public static function getPlayersList($offset = 0, $limit = 100){

		$count = ORM::for_table('battle_statistics')
		->raw_query('SELECT count(DISTINCT account_id) as count FROM battle_statistics')
		->find_one();

		$count = intval( $count->count );


		$list = ORM::for_table('battle_statistics')
		->distinct()
		->select('account_id')
		->offset($offset)
		->limit($limit)
		->find_array();

		$list = Util::pluck('account_id', $list);
		return array('list'=>$list ,'count'=>count($list), 'total_count'=> $count, 'offset'=>$offset, 'limit'=>$limit );
	}




	// public static function addClan($clan_id){
	// 	$clanData = WG::getClan($clan_id);
	// 	$players = $clanData->members;
	// 	$ids = Util::pluck($players, 'account_id');
	// 	$playersData = WG::getPlayer($ids);
	// }




	public static function addClan($clan_id){
		$clans = WG::getClan($clan_id);
		$players = array();
		$result = array();

		foreach ($clans as $clan){
			$players = array_merge( $players, Util::pluck($clan->members, 'account_id') );
		}

		$result = self::fetchNewPlayerData( $players );
		if ($result){
			return array('ok'=>true);
		} else {
			return array('ok'=>false);
		}
	}





	public static function updateWatchedPlayers(){
		$list = self::getAllPlayers();
		$lists = array_chunk($list, 100);
		$result = array('ok'=>true, 'updates'=>array());

		foreach ($lists as $playerList){
			$result['updates'][] = self::fetchNewPlayerData($playerList);
		}

		return $result;
	}




	public static function fetchNewPlayerData($players){
		$list = new PlayerList( WG::getPlayer($players) );
		return $list->save();
	}





	public static function getHistory($players, $type = null, $startDatetime = null, $endDatetime = null, $limit = 100, $offset = 0){
		$battles = ORM::for_table('battle_statistics')->where_in('account_id', $players);

		if ($startDatetime){
			$battles->where_gte('modified', $startDatetime);
		}

		if ($endDatetime){
			$battles->where_lte('modified', $endDatetime);
		}

		if ($type){
			$battles->where('type', $type);
		}

		//$battles->limit($limit)->offset($offset);

		$list = Util::group('account_id', $battles->find_array() );
		foreach ($list as $key => $value) {
			$list[$key]= Util::group('type',$value);
		}

		return $list;
	}


//SELECT p.id, p.updated_at, p.type, (select p2.id from battle_statistics as p2 where p2.updated_at = p.updated_at AND p2.type = p.type) FROM `battle_statistics` as p WHERE updated_at


	public static function getPlayedGames($players, $startDatetime = null, $endDatetime = null, $type = 'all'){

		if (is_array($players)){
			$players = implode(',', $players);
		}

		$query = 'SELECT id, type, account_id,
			(SELECT battles as latestBattles
			FROM battle_statistics
			WHERE account_id = bs.account_id
			AND type='.$type.'
			ORDER BY modified DESC
			LIMIT 1)
			-
			(SELECT battles as earliestBattles
			FROM battle_statistics
			WHERE account_id = bs.account_id
			AND type='.$type.'
			ORDER BY modified ASC
			LIMIT 1)
		as recent_battles
		FROM battle_statistics AS bs
		WHERE account_id
		IN
		('.$players.')
		AND type='.$type.'
		GROUP BY account_id
		ORDER BY recent_battles';


	$o = ORM::for_table('battle_statistics')
				->where_raw($query)->find_many();

	print_r($o);

	}
}