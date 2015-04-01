'use strict';

angular.module('tankistador')

.controller('ClanPlayersCtrl', function($scope, clan){
	$scope.clan = clan;
	console.log($scope.clan);
});