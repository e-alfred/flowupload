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
    //console.log('catchAll', arguments);
  });
}]);

$('#FileSelectInput, #FolderSelectInput').on('change', function(event){
    angular.element(this).scope().addFilesFromEvent(event);
    $('#FileSelectInput, #FolderSelectInput').val(null); //otherwise selecting the same file twice isn't possible
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

// DIRECTIVES

app.directive('appNavigationEntryUtils', function () {
    'use strict';
    return {
        restrict: 'C',
        link: function (scope, elm) {
            var menu = elm.siblings('.app-navigation-entry-menu');
            var button = $(elm)
                .find('.app-navigation-entry-utils-menu-button button');

            button.click(function () {
                menu.toggleClass('open');
            });

            $(menu).on('click', function (event) {
                if (event.target !== button[0]) {
                    menu.removeClass('open');
                }
            });
        }
    };
});

app.directive('fileDropZone', function() {
    'use strict';
    return {
        restrict: 'C',
        link: function (scope, elm){
            elm[0].addEventListener('drop', function (event) {
                scope.$flow.addFiles(event.dataTransfer.files);
            });
            elm.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
            elm.on('dragover dragenter', function() {
                elm.addClass('fileDrag');
            });
            elm.on('dragleave dragend drop', function() {
                elm.removeClass('fileDrag');
            });
        }
    };
});

app.directive('uploadSelectButton', function() {
    'use strict';
    return {
        restrict: 'C',
        link: function (scope, elm, attr){
            if(attr.uploadtype == "file"){
                elm.on('click', function() {
                    $("#FileSelectInput").click();
                });
            }else if(attr.uploadtype == "folder"){
                elm.on('click', function() {
                    $("#FolderSelectInput").click();
                });
            }
        }
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

  $scope.addFilesFromEvent = function(event) {
      console.log(event.target.files);
      $scope.$flow.addFiles(event.target.files);
  };

  let dynamicTitleInterval = $interval(function() {
    $scope.dynamicTitle();
  },500);

  $scope.locationId = 0;

  $scope.$on('changeLocation', function (event, id, $flow) {
    $scope.locationId = id;
    $scope.$flow = $flow;

    console.log($flow);

	$('#locations .locations').removeClass('active');
	$("#location-"+id).addClass('active');
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
	};

	$scope.beforeUploading = {
		query: function (flowFile, flowChunk) {
			// function will be called for every request
			return {
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
	    OC.dialogs.filepicker("Select a new Upload Folder", function(datapath, returntype) {
    		$http({
    			method : "POST",
    			url : "ajax/locations.php",
    			data : {
    				location: "/"+datapath
    			}
    		}).then(function mySuccess(response) {
    			$scope.locationId = response.data.id;
    			$scope.locations.push(response.data);
    		});
        }, false, 'httpd/unix-directory', true, OC.dialogs.FILEPICKER_TYPE_CHOOSE);
	};

	$scope.getLocations();
});
