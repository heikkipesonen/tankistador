'use strict';

angular.module('tankistador')

.controller('ClanViewCtrl', function($scope, $http, clan){

  function getClanMemberIds(){
    return _.pluck($scope.clan.members, 'account_id');
  }

  function setClanMemberStats(statistics){
    $scope.clan.members.forEach(function(member){
      angular.extend( member, statistics[member.account_id] );
    });
  }


  angular.extend($scope, {
    clan:clan,
    lastSort:false,

    sortBy:function(key, inverse){

      if ($scope.lastSort === key && inverse === undefined){
        inverse = true;
      }

      $scope.lastSort = inverse ? '!'+key : key;

      if (key === 'statistics.all.battles'){
        $scope.clan.members.sort(function(a,b){
          if (inverse){
            return a.statistics.all.battles > b.statistics.all.battles ? -1 : 1;
          }
          return a.statistics.all.battles > b.statistics.all.battles ? 1 : -1;
        });
      } else {
        $scope.clan.members.sort(function(a,b){
          if (inverse){
            return a[key] > b[key] ? -1 : 1;
          }
          return a[key] > b[key] ? 1 : -1;
        });
      }
    },

    getPlayerStatistics:function(){
      var idList = getClanMemberIds();

      return $http({
        url:'https://api.worldoftanks.eu/wot/account/info/',
        params:{
          application_id:'demo',
          fields:'statistics.all,statistics.clan,logout_at,updated_at',
          account_id:idList.join(',')
        }
      }).then(function(response){
        setClanMemberStats(response.data.data);
        return response.data.data;
      });
    }
   });
console.log($scope.clan);
  $scope.sortBy('account_name');
  $scope.getPlayerStatistics();
});