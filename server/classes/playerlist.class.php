<?php
class PlayerList{


	/**
	 * list of player ids
	 * @var Array
	 */
	private $players;


	/**
	 * list of table rows in player_statistics table
	 * @var array
	 */
	public $playerStatisticFields = array(
					'max_frags_tank_id',
					'max_xp_tank_id',
					'max_xp',
					'trees_cut',
					'max_frags',
					'max_damage_tank_id',
					'frags',
					'max_damage',
					'max_damage_vehicle');


	/**
	 * list of battle types to save into database
	 * @var array
	 */
	public $battleTypes = array(
					'all',
					'clan',
					'company',
					'team',
					'stronghold_defense',
					'stronghold_skirmish'
				);


	/**
	 * fields to save in battle statistics table
	 * @var array
	 */
	public $battleStatisticsFields = array(
					'spotted',
					//'avg_damage_assisted_track',
					//'avg_damage_blocked',
					//'direct_hits_received',
					//'explosion_hits',
					//'piercings_received',
					//'piercings',
					'xp',
					'survived_battles',
					'dropped_capture_points',
					'hits_percents',
					'draws',
					'battles',
					'damage_received',
					'avg_damage_assisted',
					'frags',
					'avg_damage_assisted_radio',
					'capture_points',
					//'base_xp',
					'hits',
					'battle_avg_xp',
					'wins',
					'losses',
					'damage_dealt',
					//'no_damage_direct_hits_received',
					'shots'
					//'explosion_hits_received',
					//'tanking_factor'
				);



	/**
	 * add players
	 * @param [Object] $players list of players
	 */
	public function addPlayers($players){
		foreach ($players as $id => $data) {
			$this->addPlayer($data);
		}
		return $this;
	}


	/**
	 * add one player
	 * @param [Object] $player player data object
	 */
	public function addPlayer($player){
		$this->players[] = $player;
		return $this;
	}


	/**
	 * convert player table insert thing into sql string
	 * @return [String] sql- string to use
	 */
	public function getPlayerSqlString(){
		$fields = $this->playerStatisticFields;
		array_push($fields, 'account_id');
		array_push($fields, 'clan_id');

		$sqlHelper = new SQLHelper('player_statistics',$fields);

		foreach ($this->players as $player) {
			$row = $player->statistics;
			$row->account_id = $player->account_id;
			$row->clan_id = $player->clan_id;

			$sqlHelper->add($row);
		}


		return $sqlHelper->sql();
	}


	/**
	 * get battle_statistics sql-string
	 * @return [String] sql-string to insert values into database in one query
	 */
	public function getBattleSqlString(){
		$fields = $this->battleStatisticsFields;
		$fields[] = 'account_id';
		$fields[] = 'clan_id';
		$fields[] = 'type';

		$sqlHelper = new SQLHelper('battle_statistics', $fields);

		foreach ($this->players as $player) {

			foreach ($this->battleTypes as $type){
				if ( $player->statistics->$type->battles > 0 ){
					$row = $player->statistics->$type;
					$row->type = $type;
					$row->account_id = $player->account_id;
					$row->clan_id = $player->clan_id;
					$sqlHelper->addRow($row);
				}
			}
		}

		return $sqlHelper->sql();
	}


	/**
	 * save contents to database
	 * convert data to strings and insert all in one sqö query
	 * @return [Int] 1 if success, 0 if failed
	 */
	public function save(){
		$battles = $this->getBattleSqlString();
		$players = $this->getPlayerSqlString();

		return ORM::raw_execute( $battles.$players );
	}


	/**
	 * constructor
	 * @param Array $playerData list of player data objects
	 */
	public function __construct($playerData = null){

		if ($playerData != null){
			$this->addPlayers($playerData);
		}
	}
}
