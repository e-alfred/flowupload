<template>
    <div id="app" role="main">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    	<!-- APP NAVIAGTION -->
    	<div id="app-navigation">
    		<div class="app-navigation-new">
    			<ul>
    				<li id="app-navigation-entry-utils-create" v-on:click="pickNewLocation()" class="app-navigation-entry-utils-menu-button">
    					<button class="icon-add">{{ t('flowupload', 'New destination') }}></button>
    				</li>
    			</ul>
    		</div>
    		<ul id="locations" class="with-icon">
    			<li class="fileDropZone" v-for="location in locations" v-bind:key="location.id" v-bind:id="'location-' + location.path">
    				<a ng-href="" class="icon-folder" v-on:click="switchActiveLocationById(location.id)" v-bind:title="location.path">{{location.path}}</a>
    				<div class="app-navigation-entry-utils">
    					<ul>
    						<li class="app-navigation-entry-utils-counter" v-bind:title="location.flow.files.length + ' ' + t('flowupload', 'Files')">{{ location.flow.files.length }}</li>
    						<li class="app-navigation-entry-utils-menu-button"><button></button></li>
    					</ul>
    				</div>
    				<div appNavigationEntryMenu>
    					<ul>
    						<li>
    							<a v-bind:href="'/index.php/apps/files/?dir=' + location.path" target="_blank" rel="noopener noreferrer">
    							<span class="icon-files"></span>
    							<span>{{ t('flowupload', 'Open') }}</span>
    							</a>
    						</li>
    						<li v-on:click="toggleStarredLocation(location.path)">
    							<a href="">
    							<span class="icon-starred"></span>
    							<span v-if="!location.starred">{{ t('flowupload', 'Star') }}</span>
    							<span v-if="location.starred">{{ t('flowupload', 'Unstar') }}</span>
    							</a>
    						</li>
    						<li v-on:click="removeLocation(location.path)">
    							<a href="">
    							<span class="icon-delete"></span>
    							<span>{{ t('flowupload', 'Remove') }}</span>
    							</a>
    						</li>
    					</ul>
    				</div>
    			</li>
    		</ul>
    		<div id="app-settings">
    			<div id="app-settings-header">
    				<button class="settings-button"
    					data-apps-slide-toggle="#app-settings-content">
    				{{ t('flowupload', 'Settings') }}
    				</button>
    			</div>
    			<div id="app-settings-content">
    				<!-- Your settings content here -->
    			</div>
    		</div>
    	</div>
    	
    	<!-- CONTENT -->
    	<div class="fileDropZone" id="app-content" style="padding: 2.5%; width:auto">
    		<div id="noLocationSelected" v-show="activeLocation === undefined && loaded">{{ t('flowupload', 'Please select a location') }}</div>
    		<div id="locationSelected" ng-cloak v-show="activeLocation != undefined">
    			<h2 id="title">{{ t('flowupload', 'Transfers') }}</h2>
    			<div class="buttonGroup">
    				<span class="uploadSelectButton button" uploadtype="file">
    				<span class="icon icon-file select-file-icon" style=""></span>
    				<span>{{ t('flowupload', 'Select File') }}</span>
    				</span>
    				<input id="FileSelectInput" type="file" multiple="multiple">
    				<span class="uploadSelectButton button" uploadtype="folder" v-show="activeLocation.flow.supportDirectory">
    				<span class="icon icon-files" style="background-image: var(--icon-files-000);"></span>
    				<span>{{ t('flowupload', 'Select Folder') }}</span>
    				</span>
    				<input id="FolderSelectInput" type="file" multiple="multiple" webkitdirectory="webkitdirectory">
    			</div>
    			<hr>
    			<div class="buttonGroup">
    				<a class="button" v-on:click="activeLocation.flow.resume()">
    				    <span class="icon icon-play"></span>
    				    <span>{{ t('flowupload', 'Start/Resume') }}</span>
    				</a>
    				<a class="button" v-on:click="activeLocation.flow.pause()">
    				    <span class="icon icon-pause"></span>
    				    <span>{{ t('flowupload', 'Pause') }}</span>
    				</a>
    				<a class="button" v-on:click="activeLocation.flow.cancel()">
    				    <span class="icon icon-close"></span>
    				<span>{{ t('flowupload', 'Cancel') }}</span>
    				</a>
    				<a id="hideFinishedButton" class="button" v-on:click="hideFinished = !hideFinished">
    				    <input type="checkbox" ng-model="hideFinished"></input>
    				    <span>{{ t('flowupload', 'Hide finished uploads') }}</span>
    				</a>
    			</div>
    			<hr>
    			<p>
    				<span class="label">{{ t('flowupload', 'Size') + ' : ' + activeLocation.flow.getSize() | bytes }}</span>
    				<span class="label" v-if="activeLocation.flow.getFilesCount() != 0">{{ t('flowupload', 'Progress') + ' : ' + trimDecimals(activeLocation.flow.progress()*100, 2) + '%'}}</span>
    				<span class="label" v-if="activeLocation.flow.isUploading()">{{ t('flowupload', 'Time remaining') + ' : ' + activeLocation.flow.timeRemaining() | seconds }}</span>
    				<span class="label" v-if="activeLocation.flow.isUploading()">{{ t('flowupload', 'Uploading') + '...' }}</span>
    			</p>
    			<hr>
    			<table id="uploadsTable">
    				<thead>
    					<tr>
    						<th class="hideOnMobile" style="width:5%">
    							<span class="noselect">#</span>
    						</th>
    						<th ng-click="tableSortClicked('relativePath')">
    							<a class="noselect">
    							<span>{{ t('flowupload', 'Name') }}</span>
    							<span ng-class="{'icon-triangle-n':  (sortType == 'relativePath' && sortReverse), 'icon-triangle-s': (sortType == 'relativePath' && !sortReverse)}" class="sortIndicator"></span>
    							</a>
    						</th>
    						<th></th>
    						<th class="hideOnMobile" ng-click="tableSortClicked('-currentSpeed')" style="width:10%">
    							<a class="noselect">
    							<span>{{ t('flowupload', 'Uploadspeed') }}</span>
    							<span ng-class="{'icon-triangle-n':  (sortType == '-currentSpeed' && sortReverse), 'icon-triangle-s': (sortType == '-currentSpeed' && !sortReverse)}" class="sortIndicator"></span>
    							</a>
    						</th>
    						<th ng-click="tableSortClicked('-size')" style="width:10%">
    							<a class="noselect">
    							<span>{{ t('flowupload', 'Size') }}</span>
    							<span ng-class="{'icon-triangle-n':  (sortType == '-size' && sortReverse), 'icon-triangle-s': (sortType == '-size' && !sortReverse)}" class="sortIndicator"></span>
    							</a>
    						</th>
    						<th ng-click="tableSortClicked('-progress()')" style="width:20%">
    							<a class="noselect">
    							<span>{{ t('flowupload', 'Progress') }}</span>
    							<span ng-class="{'icon-triangle-n':  (sortType == '-progress()' && sortReverse), 'icon-triangle-s': (sortType == '-progress()' && !sortReverse)}" class="sortIndicator"></span>
    							</a>
    						</th>
    					</tr>
    				</thead>
    				<tbody>
    					<tr v-if="!(file.isComplete() && hideFinished)" v-for="file in filteredFiles">
    						<td class="hideOnMobile">{{$index+1}}</td>
    						<td class="ellipsis" v-bind:title="'UID: ' + file.uniqueIdentifier">
    							<span>{{file.relativePath}}</span>
    						</td>
    						<td>
    							<div class="actions" v-if="!file.isComplete() || file.error">
    								<a class="action permanent" v-bind:title="t('flowupload', 'Resume')" v-click="file.resume()" v-if="!file.isUploading() && !file.error">
    								<span class="icon icon-play"></span>
    								</a>
    								<a class="action permanent" v-bind:title="t('flowupload', 'Pause')" v-click="file.pause()" v-if="file.isUploading() && !file.error">
    								<span class="icon icon-pause"></span>
    								</a>
    								<a class="action permanent" v-bind:title="t('flowupload', 'Retry')" v-click="file.retry()" v-show="file.error">
    								<span class="icon icon-play"></span>
    								</a>
    								<a class="action permanent" v-bind:title="t('flowupload', 'Cancel')" v-click="file.cancel()">
    								<span class="icon icon-close"></span>
    								</a>
    							</div>
    						</td>
    						<td class="hideOnMobile">
    							<span v-if="file.isUploading()">{{file.currentSpeed | byterate}}</span>
    						</td>
    						<td title="'Chunks: ' + file | completedChunks + ' / ' + file.chunks.length">
    							<span class="hideOnMobile" v-if="!file.isComplete()">{{file.size*file.progress() | bytes}}/</span>
    							<span>{{file.size | bytes}}</span>
    						</td>
    						<td>
    							<progress v-if="!file.isComplete() && !file.error" class="progressbar hideOnMobile" max="1" v-bind:value="file.progress()"></progress>
    							<span v-if="!file.isComplete() && !file.error">{{ trimDecimals(file.progress()*100, 2) }}%</span>
    							<span v-if="file.isComplete() && !file.error">{{ t('flowupload', 'Completed') }}</span>
    							<span v-if="file.error">{{ t('flowupload', 'Error') }}</span>
    						</td>
    					</tr>
    				</tbody>
    			</table>
    			<p id="homeDirectoryLink"><a href="../files?dir=%2Fflowupload">{{ t('flowupload', 'The files will be saved in your home directory.') }}</a></p>
    		</div>
    	</div>
    </div>
</template>

<script>
import Content from '@nextcloud/vue/dist/Components/Content'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings'
import AppSidebar from '@nextcloud/vue/dist/Components/AppSidebar'
import AppSidebarTab from '@nextcloud/vue/dist/Components/AppSidebarTab'
import AppNavigationCounter from '@nextcloud/vue/dist/Components/AppNavigationCounter'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import ActionLink from '@nextcloud/vue/dist/Components/ActionLink'
import AppNavigationIconBullet from '@nextcloud/vue/dist/Components/AppNavigationIconBullet'
import ActionCheckbox from '@nextcloud/vue/dist/Components/ActionCheckbox'
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
import ActionRouter from '@nextcloud/vue/dist/Components/ActionRouter'
import ActionText from '@nextcloud/vue/dist/Components/ActionText'
import ActionTextEditable from '@nextcloud/vue/dist/Components/ActionTextEditable'

export default {
	name: 'App',
	components: {
		Content,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
		AppNavigationSettings,
		AppSidebar,
		AppSidebarTab,
		AppNavigationCounter,
		ActionButton,
		ActionLink,
		AppNavigationIconBullet,
		ActionCheckbox,
		ActionInput,
		ActionRouter,
		ActionText,
		ActionTextEditable,
	},
	data: function() {
		return {
			loading: false,
			locations: [],
            baseUrl: OC.generateUrl('/apps/flowupload'),
            currentLocation: undefined,
            hideFinished: false
		}
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
    },
}
</script>