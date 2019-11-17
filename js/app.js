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
		var units = ['bytes', 'kB', 'MB', 'GB'],
			number = Math.floor(Math.log(bytes) / Math.log(1024));
		return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
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

window.addEventListener("beforeunload", function (e) {
    var confirmationMessage = 'If you proceed all pending uploads will get cancelled !';

    (e || window.event).returnValue = confirmationMessage;
    return confirmationMessage;
});
