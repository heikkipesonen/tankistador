<?php

require 'bower_components/idiorm/idiorm.php';
require 'bower_components/flight/flight/Flight.php';

require_once('conf.php');

function __autoload($class){
	include 'classes/'.strtolower($class).'.class.php';
}


//print_r( wgapi::getClan(500028261) );



Server::updateClan( '500028261' );