var app = new Vue({
    el: '#app',
    data: {
        locations: [],
        baseUrl: OC.generateUrl('/apps/flowupload'),
        currentLocation: undefined,
        hideFinished: false
    },
    mounted: function (){
        var self = this;
        self.loadLocations().then(function(){
            console.log(self.locations);
            if(self.locations.length > 0){
                self.switchActiveLocationById(self.locations[0].id);
            }else{
                console.log("no starred locations available");
            }
        });
    },
    methods: {
        switchActiveLocationByPath: function (path) {
    	    let location = this.getLocationByPath(path);
    	    console.log(location);
    	    
    		this.activeLocationId = location.id;
    	},
    	switchActiveLocationById: function (id) {
    		this.activeLocationId = id;
    	},
        getStarredLocations: function() {
            let url = this.baseUrl + '/directories';
            return new Promise(function (resolve, reject){
            	$.ajax({
                    url: url,
                    type: 'GET',
                    contentType: 'application/json',
                }).done(function (response) {
                    console.log(response);
                    resolve(response);
                });
            });
        },
        loadLocations: function() {
            var self = this;
            return new Promise(function (resolve, reject){
                self.getStarredLocations().then(function(locations) {
                    self.locations = [];
                    
                    for(let i=0; i < locations.length; i++){
            			    self.addLocation(locations[i].id, locations[i].directory, true);
            		}
            		
            		resolve();
                });
            });
        },
        pickNewLocation: function () {
    	    OC.dialogs.filepicker("Select a new Upload Folder", function(path) {
                $scope.addNewLocation(path+"/",false);
                setTimeout(function(){
                    $scope.setLocation(path+"/");
                }, 500);
            }, false, 'httpd/unix-directory', true, OC.dialogs.FILEPICKER_TYPE_CHOOSE);
    	},
        getLocationByPath: function(path) {
            for(let i=0; i < this.locations.length; i++){
                if(this.locations[i].path == path){
                    return this.locations[i];
                }
            }
        },
        addLocation: function(id, path, starred) {
    	    let newFlow = new Flow(
    	        {query: function (flowFile, flowChunk) {
        			return {
        				target: path
        			};
    		    },
    		    "target": this.baseUrl+'/upload',
                "permanentErrors": [403, 404, 500, 501],
                "maxChunkRetries": 2,
                "chunkRetryInterval": 5000,
                "simultaneousUploads": 4
    	        }
            );
            
            this.locations.push({"id": id, "path": path, "starred": starred, "flow": newFlow});
            console.log(this.locations);
        },
        starLocation: function(path){
    	    this.getLocationByPath(path).starred = true;
    	    
    	    $.ajax({
                url: this.baseUrl + '/directories',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({path})
            }).done(function (response) {
            });
	    },
	    unstarLocation: function(path){
    	    this.getLocationByPath(path).starred = false;
    	    //TODO: send to server
    	},
    	trimDecimals: function(number, decimals = 2) {
    	    return number.toFixed(decimals);
    	}
    },
    computed: {
        activeLocation: function() {
            if(this.activeLocationId) {
                return this.locations.find(location => location.id == this.activeLocationId);
            }else {
                return false;
            }
        },
        filteredFiles: function() {
            if(this.activeLocationId) {
                return this.activeLocation.flow.files;
            }else {
                return [];
            }
        }
    },
    filters: {
        bytes: function(bytes, precision) {
        	if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return '-';
        	if (typeof precision === 'undefined') precision = 1;
        	    var units = ['bytes', 'kB', 'MB', 'GB'];
        	    var	number = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)),units.length-1);
        	    return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
        },
        byterate: function(bytes, precision) {
    		if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return '0 KB/s';
    		if (typeof precision === 'undefined') precision = 1;
    		var units = ['B/s', 'KB/s', 'MB/s', 'GB/s'];
    		var	number = Math.min(Math.floor(Math.log(bytes) / Math.log(1000)),units.length-1);
    		return (bytes / Math.pow(1000, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
	    },
	    seconds: function(seconds, precision) {
    		if (isNaN(parseFloat(seconds)) || seconds == 0 || !isFinite(seconds)) return '-';
    		if (typeof precision === 'undefined') precision = 1;
    		var units = ['s', 'm', 'h'];
    		var	number = Math.min(Math.floor(Math.log(seconds) / Math.log(60)),units.length-1);
    		return (seconds / Math.pow(60, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
	    },
	    completedChunks: function(file) {
            let completeChunks = 0;
            
            file.chunks.forEach(function (c) {
                if(c.progress() === 1){
                    completeChunks++;
                }
            });
            
            return completeChunks;
    	}
    }
});

Vue.component("app-navigation-entry-menu", function() {
    
});