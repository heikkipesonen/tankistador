'use strict';

angular.module('tankistador')

.factory('Player',function($http, $q){

	function Player(id){
		this.id = id;
		this.vehicleList = null;
	}

	Player.prototype = {

		getCalculatedStatistics:function(){
			var all = this.account.all;

		},

		_get:function(from, data, params){
			var url = 'https://api.worldoftanks.eu/wot/'+from+'/'+data+'/';
			var _params = {
				application_id:'demo'
			};

			_.assign(_params, params);

			return $http({
				cache:true,
				type:'GET',
				url:url,
				params:_params
			}).then(function(response){
				return response.data && response.data.data ? response.data.data : false;
			}, function(){
				return false;
			});
		},

		getVehicleList:function(){
			var me = this;
			if (this.vehicleList){
				var d = $q.defer();
				d.resolve(this.vehicleList);
				return d.promise;
			} else {
				return this._get('tanks','stats', {account_id: this.id }).then(function(data){
					me.vehicleList = data[me.id];
					return data[me.id];
				});
			}
		},

		getAccount:function(){
			var me = this;
			if (this.account_id){
				var d = $q.defer();
				d.resolve(this.account);
				return d.promise;
			} else {
				return this._get('account','info', {account_id: this.id }).then(function(data){
					angular.extend(me, data[me.id]);
					return me;
				});
			}
		},

		load:function(){
			var me = this;
			return $q.all([
				this.getVehicleList(),
				this.getAccount()
			]).then(function(){
				console.log(me);
				return me;
			});
		}
	};

	return Player;
});