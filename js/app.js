/*global angular */
'use strict';

/**
 * The main app module
 * @name app
 * @type {angular.Module}
 */
var app = angular.module('app',[]);

$('#FileSelectInput, #FolderSelectInput').on('change', function(event){
    angular.element(this).scope().location.flow.addFiles(event.originalEvent.target.files);
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
app.filter('completedChunks', function() {
	return function(file) {
        let completeChunks = 0;

        file.chunks.forEach(function (c) {
            if(c.progress() === 1){
                completeChunks++;
            }
        });
        return completeChunks;
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
                var dataTransfer = event.dataTransfer;
                if (dataTransfer.items && dataTransfer.items[0] &&
                    dataTransfer.items[0].webkitGetAsEntry) {
                    scope.location.flow.webkitReadDataTransfer(event);
                } else {
                    scope.location.flow.addFiles(dataTransfer.files, event);
                }
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

app.controller('flow', function($rootScope,$scope,$interval) {
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
      if($scope.location.flow.files.length !== 0){
        let progress = parseFloat(Math.round($scope.location.flow.progress() * 100 * 100) / 100).toFixed(2); //round to two digits after comma
        document.title = "FlowUpload "+progress+"%";
      }else{
        document.title = "FlowUpload";
      }
  };

  let dynamicTitleInterval = $interval(function() {
    if($rootScope.loaded && $scope.location){
        $scope.dynamicTitle();
    }
  },500);

  $scope.$on('changeLocation', function (event, newLocation) {
    $scope.location = newLocation;
  });
});

app.controller('location', function ($scope) {
	$scope.seeUploads = function ($event, type) {
		$event.stopPropagation();
		$event.preventDefault();
	};
});

app.controller('locations', function ($rootScope, $scope, $http) {
    $scope.locations = [];

    $scope.init = function (){
        $scope.loadFavoriteLocations().then(function(){
            if($scope.locations.length > 0){
                $('.locations:first').find("a").click();
            }else{
                console.log("no favorite locations available");
            }

            $rootScope.loaded = true;
        });
    }

    $scope.getLocationByPath = function(path){
        for(let i=0; i < $scope.locations.length; i++){
            if($scope.locations[i].path == path){
                return $scope.locations[i];
            }
        }
    }

	$scope.setLocation = function (path) {
	    let newLocation = $scope.getLocationByPath(path);
	    console.log(newLocation);

		$rootScope.$broadcast('changeLocation', newLocation);
		$scope.currentLocation = newLocation;

		$('.locations').each(function () {
			if ($(this).attr('id') === 'location-' + newLocation.path){
				$(this).addClass('active');
			} else{
			    $(this).removeClass('active');
			}
		});
	}

	$scope.loadFavoriteLocations = function () {
	    return new Promise(function (resolve, reject){
    		$http({
    			method: "GET",
    			url: "ajax/locations.php"
    		}).then(function mySuccess(response) {
    		    for(let i=0; i < response.data.length; i++){
    			    $scope.addNewLocation(response.data[i],true);
    			}
    			resolve();
    		});
	    });
	};

	$scope.addNewLocation = function (path, favorite) {
	    let newFlow = new Flow(
	        {query: function (flowFile, flowChunk) {
    			return {
    				target: $scope.currentLocation.path
    			};
		    },
		    "target": 'ajax/upload.php',
            "permanentErrors": [403, 404, 500, 501],
            "maxChunkRetries": 2,
            "chunkRetryInterval": 5000,
            "simultaneousUploads": 4
	        }
        );

        $scope.locations.push({"path": path, "favorite": favorite, "flow": newFlow});
        console.log($scope.locations);
	};

	$scope.pickNewLocation = function () {
	    OC.dialogs.filepicker("Select a new Upload Folder", function(path) {
            $scope.addNewLocation(path+"/",false);
            setTimeout(function(){
                $scope.setLocation(path+"/");
            }, 500);
        }, false, 'httpd/unix-directory', true, OC.dialogs.FILEPICKER_TYPE_CHOOSE);
	}

	$scope.init();
});
