'use strict';

angular.module('tankistador',
	['ngAnimate',
	'ngCookies',
	'ngTouch',
	'ngSanitize',
	'restangular',
	'ui.router',
	'ngMaterial',
	'LocalStorageModule'])





  .config(function ($stateProvider, $urlRouterProvider) {
    $stateProvider
      .state('root', {
        abstract:true,
        templateUrl: 'app/main/main.html',
        resolve:{
					baseData:function($http, $q, vehicleService){
						var promises = [
						  $http.get('assets/info.json').then(function(response){
					      vehicleService.setData(response.data.data);
					      return response.data.data;
					    }),

					    $http.get('assets/list-tanks.json').then(function(response){
					    	vehicleService.setVehicleList(response.data.data);
					      return response.data.data;
					    }),

					    $http.get('assets/values-expected.json').then(function(response){
					    	vehicleService.setExpected(response.data.data);
					      return response.data.data;
					    })
					  ];

					  return $q.all(promises);
					}
        }
      })

      .state('root.main', {
      	url:'/'
      })


      .state('root.clan',{
      	url:'/clan',
      	abstract:true,
      	templateUrl:'app/clan/clan.html'
      })

      .state('root.clan.search', {
      	url:'/',
      	templateUrl:'app/clan/clan.search.html',
      	controller:'ClanSearchCtrl'
      })

      .state('root.clan.view', {
      	resolve:{
      		clanId:function($stateParams){
      			return $stateParams.clan_id;
      		},
      		clan:function($http, $stateParams){
      			var id = $stateParams.clan_id;
			      return $http({
			        url:'https://api.worldoftanks.eu/wgn/clans/info/',
			        params:{
			          application_id:'demo',
			          clan_id:id
			        }
			      }).then(function(response){
			        return response.data.data[id];
			      });
      		}
      	},
      	abstract:true,
      	url:'/:clan_id',
      	templateUrl:'app/clan/clan.view.html',
      	controller:'ClanViewCtrl'
      })

      .state('root.clan.view.info', {
      	resolve:{
	      	clanId:function(clanId){
	      		return clanId;
	      	}
      	},
      	parent:'root.clan.view',
      	url:'/',
      	templateUrl:'app/clan/clan.view.info.html'
      })

      .state('root.clan.view.players', {
      	resolve:{
	      	clanId:function(clanId){
	      		return clanId;
	      	}
      	},
      	controller:'ClanPlayersCtrl',
      	url:'/players',
      	templateUrl:'app/clan/clan.view.players.html'
      })

      .state('root.player', {
      	abstract:true,
      	url:'/player',
      	templateUrl:'app/player/player.view.html'
      })

      .state('root.player.search', {
      	url:'/:id',
      	templateUrl:'app/player/player.view.html'
      });


    $urlRouterProvider.otherwise('/');
  })
;
