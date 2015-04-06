'use strict';

angular.module('tankistador')

.controller('ClanPlayersCtrl', function($scope, clan, $http, app_config){
	$scope.clan = clan;
	console.log($scope.clan);

	var ids = _.pluck($scope.clan.members, 'account_id');

	$http.get(app_config.backend +'/player/history/'+ids.join(',')).then(function(response){
		console.log(response);
	})
});