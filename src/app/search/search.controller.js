'use strict';

angular.module('tankistador')

.controller('SearchCtrl', function($scope, $http){
	angular.extend($scope, {
    search:{
      text:'',
      items:[],
      player:false
    },
    querySearch:function(text){
    	if (text.length >= 3){
	      return $http.get('https://api.worldoftanks.eu/wot/account/list/?application_id=demo&search='+text).then(function(response){
	        return response.data.data;
	      });
    	} else {
    		return false;
    	}
    }
   });

	$scope.$watch('search.player', function(){
		console.log($scope.search.player);
	})
});