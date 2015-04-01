<?php

class Util {
	public static function pluck($from, $key){
		$result = array();

		foreach ($from as $row) {
			if (is_object($row)){
				$result[] = $row->$key;
			} else {
				$result[] = $row[$key];
			}
		}

		return $result;
	}

	public static function difference($a, $b){
		if (is_array($a) && is_array($b)){
			return array_diff($a, $b);
		} else if (is_object($a) && is_object($b)){
			$result = (object) array();

			foreach ($a as $key => $value) {
				if (!isset($b)){
					$result->$key = $value;
				}
			}
		}
	}
}