/*global angular */
'use strict';

/**
 * The main app module
 * @name app
 * @type {angular.Module}
 */
var app = angular.module('app', ['flow']);

app.config(['flowFactoryProvider', function (flowFactoryProvider) {
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

$('li#app-navigation-entry-utils-create').on('click', function() {
  $('li#app-navigation-entry-utils-add').addClass('editing');
});

$('li#app-navigation-entry-utils-add .icon-close').on('click', function () {
  $('li#app-navigation-entry-utils-add').removeClass('editing');
});

$('li#app-navigation-entry-utils-add .icon-checkmark').on('click', function () {
  $('li#app-navigation-entry-utils-add').removeClass('editing');
});

// FILTERS

app.filter('bytes', function() {
	return function(bytes, precision) {
		if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return '-';
		if (typeof precision === 'undefined') precision = 1;
		var units = ['bytes', 'kB', 'MB', 'GB'];
		var	number = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)),units.length-1);
		return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
	};
});
app.filter('byterate', function() {
	return function(bytes, precision) {
	    console.log(bytes);
		if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return '0 KB/s';
		if (typeof precision === 'undefined') precision = 1;
		var units = ['B/s', 'KB/s', 'MB/s', 'GB/s'];
		var	number = Math.min(Math.floor(Math.log(bytes) / Math.log(1000)),units.length-1);
		return (bytes / Math.pow(1000, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
	};
});

app.filter('seconds', function() {
	return function(seconds, precision) {
		if (isNaN(parseFloat(seconds)) || seconds == 0 || !isFinite(seconds)) return '-';
		if (typeof precision === 'undefined') precision = 1;
		var units = ['s', 'm', 'h'];
		var	number = Math.min(Math.floor(Math.log(seconds) / Math.log(60)),units.length-1);
		return (seconds / Math.pow(60, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
	};
});

// CONTROLLERS

app.controller('flow', function($scope,$interval) {
  $scope.sortType     = 'relativePath';
  $scope.sortReverse  = false;

  $scope.hideFinished  = false;

  $scope.tableSortClicked = function(newSortType){
    if($scope.sortType === newSortType){
      $scope.sortReverse = !$scope.sortReverse;
    }else{
       $scope.sortType = newSortType;
    }
  };

  $scope.dynamicTitle = function() {
      if($scope.$flow.getFilesCount() !== 0){
        let progress = parseFloat(Math.round($scope.$flow.progress() * 100 * 100) / 100).toFixed(2); //round to two digits after comma
        document.title = "FlowUpload "+progress+"%";
      }else{
        document.title = "FlowUpload";
      }
  };

  let dynamicTitleInterval = $interval(function() {
    $scope.dynamicTitle();
  },500);

  $scope.locationId = 0;

	$scope.$on('changeLocation', function (event, id, $flow) {
		$scope.locationId = id;
		$scope.$flow = $flow;

		console.log(id);
		console.log($flow);
	});
});

app.controller('location', function ($scope) {
	$scope.init = function (id, name) {
		$scope.locationId = id;
		$scope.locationName = name;
	}

	$scope.seeUploads = function ($event, type) {
		$event.stopPropagation();
		$event.preventDefault();

		console.log($scope);

		$('#locations li.active').removeClass('active');

		$($event.currentTarget).addClass('active');
	};

	$scope.beforeUploading = {
		query: function (flowFile, flowChunk) {
			// function will be called for every request
			console.log('File', flowFile);
			console.log('Chunk', flowChunk);
			return {
				//id: $scope.locationId
				// Temporary:
				target: $scope.locationName
			};
		}
	};
});

app.controller('locations', function ($rootScope, $scope, $http) {
	$scope.isOpen = false;

	$scope.reloadLocations = function () {
		setTimeout(function () {
			if ($scope.locationId === undefined)
				$($($('.locations')[0]).find('a')).click();
			else
				$($('#location-' + $scope.locationId).find('a')).click();
		}, 100);
	}

	$scope.setLocation = function (id, $flow) {
		$rootScope.$broadcast('changeLocation', id, $flow);
		$scope.$flow = $flow;
		$scope.locationId = id;

		$('.locations').each(function () {
			if ($(this).attr('id') === 'location-' + id)
				$(this).addClass('open');
			else
				$(this).removeClass('open');
			}
		);
	}

	$scope.getLocations = function () {
		$http({
			method: "GET",
			url: "ajax/locations.php"
		}).then(function mySuccess(response) {
				$scope.locations = response.data.locations;

				$scope.locationId = $scope.locations[0].id;
			}, function myError(response) {
				$scope.locations = {};
			}
		);
	};

	$scope.addNewLocation = function () {
		$http({
			method : "POST",
			url : "ajax/locations.php",
			data : {
				location: $('#newLocationName').val()
			}
		}).then(function mySuccess(response) {
			$('#newLocationName').val('');

			$scope.locationId = response.data.id;
			$scope.locations.push(response.data);
		}, function myError(response) {
			$scope.locations = {};
		});
	};

	$scope.getLocations();
});
