'use strict';

angular.module('tankistador')

.controller('ClanSearchCtrl', function($scope, $http){
	angular.extend($scope, {
    search:{
      text:'',
      items:[],
      clan:false
    },
    querySearch:function(text){
    	if (text.length >= 3){

        return $http({
          url:'https://api.worldoftanks.eu/wgn/clans/list/',
          params:{
            limit:10,
            order_by:'name',
            application_id:'demo',
            search:text
          }
        }).then(function(response){
          console.log(response.data.data);
          return response.data.data;
        });

    	} else {
    		return false;
    	}
    }
   });

	$scope.$watch('search.clan', function(){
		console.log($scope.search.clan);
	});
});