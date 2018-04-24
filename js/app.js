/*global angular */
'use strict';

/**
 * The main app module
 * @name app
 * @type {angular.Module}
 */
var app = angular.module('app', ['flow'])
.config(['flowFactoryProvider', function (flowFactoryProvider) {
  flowFactoryProvider.defaults = {
    target: 'ajax/upload.php',
    permanentErrors: [403, 404, 500, 501],
    maxChunkRetries: 2,
    chunkRetryInterval: 5000,
    simultaneousUploads: 4
  };
  flowFactoryProvider.on('catchAll', function (event) {
    console.log('catchAll', arguments);
  });
}]);

app.filter('bytes', function() {
	return function(bytes, precision) {
		if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return '-';
		if (typeof precision === 'undefined') precision = 1;
		var units = ['bytes', 'kB', 'MB', 'GB'],
			number = Math.floor(Math.log(bytes) / Math.log(1024));
		return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
	}
});

app.controller('flowInfo', function ($scope) {
  $scope.beforeUploading = {
    query: function (flowFile, flowChunk) {
      // function will be called for every request
      console.log(flowFile);
      console.log(flowChunk);
      return {
        id: 'Coucou'
      };
    }
  };
});

app.controller('locations', function ($scope, $http) {
  $scope.isOpen = false;

  $scope.seeUploads = function ($event, id, type) {
    $event.stopPropagation();
    $event.preventDefault();

    $('#locations li.active').removeClass('active');

    $($event.currentTarget).addClass('active');
    $('#location-' + id).addClass('active');
    $('#currentLocation').val(id);
  };

  $scope.getLocations = function () {
    $http({
      method: "GET",
      url: "ajax/locations.php"
    }).then(function mySuccess(response) {
      $scope.locations = response.data.locations;
    }, function myError(response) {
      $scope.locations = {};
    });
  };

  $scope.getLocations();

  $scope.addNewLocation = function () {
    $http({
      method : "POST",
      url : "ajax/locations.php",
      data : {
        location: $('#newLocationName').val()
      }
    }).then(function mySuccess(response) {
      $('#newLocationName').val('');

      $scope.locations.push(response.data.new);
    }, function myError(response) {
      $scope.locations = {};
    });
  };
});
