'use strict';

angular.module('tankistador')

  .controller('PlayerCtrl',function($scope, $http, $stateParams, Player){
    if ($stateParams.id){
      $scope.player = new Player($stateParams.id);
      $scope.player.load();
    }

    console.log($stateParams.id);
  });