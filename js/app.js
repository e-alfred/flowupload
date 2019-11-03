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

app.controller('mainController', function($scope,$interval) {
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
});