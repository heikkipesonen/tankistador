<?php
class Server{

	public static function getPlayersList(){
		$list = ORM::for_table('player_statistics')->distinct()->select('account_id')->find_many();
		return Util::pluck($list, 'account_id');
	}

	public static function addClan($clan_id){
		$clanData = WG::getClan($clan_id);
		$players = $clanData->members;
		$ids = Util::pluck($players, 'account_id');
		$playersData = WG::getPlayer($ids);
	}

	public static function updateClan($clan_id){
		$clanData = WG::getClan($clan_id);
		$players = $clanData->members;

		return self::fetchNewPLayerData( Util::pluck($players, 'account_id') );
	}

	public static function fetchNewPLayerData($players){
		$list = new PlayerList( WG::getPlayer($players) );
		return $list->save();
	}

	public static function getPlayedGames($players){

	}
}