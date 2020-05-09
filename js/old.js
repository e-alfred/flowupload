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


  $scope.tableSortClicked = function(newSortType){
    if($scope.sortType === newSortType){
      $scope.sortReverse = !$scope.sortReverse;
    }else{
       $scope.sortType = newSortType;
    }
  };

  $scope.dynamicTitle = function() {
      if($scope.location != undefined && $scope.location.flow.files.length !== 0){
        let progress = parseFloat(Math.round($scope.location.flow.progress() * 100 * 100) / 100).toFixed(2); //round to two digits after comma
        document.title = "FlowUpload "+progress+"%";
      }else{
        document.title = "FlowUpload";
      }
  };

  let dynamicTitleInterval = $interval(function() {
    if($rootScope.loaded){
        $scope.dynamicTitle();
    }
  },500);

	
	$scope.toggleStarredLocation = function(path){
	    if($scope.getLocationByPath(path).starred){
	        $scope.unstarLocation(path);
	    }else{
	        $scope.starLocation(path);
	    }
	}
	
	$scope.removeLocation = function(path) {
	    for(let i=0; i < $scope.locations.length; i++){
	        if($scope.locations[i].path == path){
	            if($scope.locations[i].starred){
	                $scope.unstarLocation(path);
	            }
	            $scope.locations.splice(i,1);
	        }
	    }
	    
	    if($scope.currentLocation.path == path) {
	        $scope.currentLocation = undefined;
	        $rootScope.$broadcast('changeLocation', undefined);
	    }
	}
