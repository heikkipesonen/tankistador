'use strict';
angular.module('tankistador')

.service('vehicleService', function(){
    angular.extend(this, {
      data:{},
      vehicleList:{},
      expectedValues:{},

      setData:function(data){
        this.data = data;
      },

      setVehicleList:function(data){
        var me = this;
        _.forEach(data, function(vehicle){
        	me.vehicleList[vehicle.tank_id] = vehicle;
        });
        return this;
      },

      setExpected:function(data){
      	var me = this;

      	_.forEach(data, function(v){
      		me.expectedValues[v.IDNum] = v;
      	});

      	return this;
      },

      getExpectedValues:function(id){
      	return this.expectedValues[id];
      },

      getVehicleById:function(id){
      	return this.vehicleList[id];
      },


      getNations:function(){
        return angular.extend({}, this.data.vehicle_nations);
      }


    });
  });