<template>
    <Content :class="{'icon-loading': loading}" app-name="flowupload">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    	
    	<!-- APP NAVIAGTION -->
    	<AppNavigation>
    		<AppNavigationNew
				:text="t('flowupload', 'New destination')"
				:disabled="false"
				button-id="new-location-button"
				button-class="icon-add"
				v-on:click="pickNewLocation()" />
    		<ul id="locations" class="with-icon">
    			<AppNavigationItem active="true"
    			v-bind:class="{'active': activeLocation && location.path == activeLocation.path}"
    			v-bind:title="location.path"
    			v-customLocationFileDropZone:[location]
    			v-for="location in locations"
    			v-bind:key="location.id"
    			v-bind:id="'location-' + location.path"
    			v-if="!loading"
    			v-on:click="switchActiveLocationByPath(location.path)">
				<AppNavigationCounter slot="counter" :highlighted="true">
					{{ location.flow.files.length }}
				</AppNavigationCounter>
				<template slot="actions">
					<ActionButton icon="icon-edit" v-bind:href="'/index.php/apps/files/?dir=' + location.path">
						{{ t('flowupload', 'Open') }}
					</ActionButton>
					<ActionButton icon="icon-starred" v-if="!location.starred" @click="starLocation(location.path)">
						{{ t('flowupload', 'Star') }}
					</ActionButton>
					<ActionButton icon="icon-starred" v-if="location.starred" @click="unstarLocationByPath(location.path)">
						{{ t('flowupload', 'Unstar') }}
					</ActionButton>
					<ActionButton icon="icon-delete" @click="removeLocation(location.path)">
						{{ t('flowupload', 'Remove') }}
					</ActionButton>
				</template>
			</AppNavigationItem>
		</ul>
    		<AppNavigationSettings>
    		</AppNavigationSettings>
    	</AppNavigation>
    	
    	<!-- CONTENT -->
    	<AppContent>
        	<div v-activeLocationFileDropZone style="padding: 2.5%; width:auto" v-if="!loading">
        		<div id="noLocationSelected" v-if="activeLocation === undefined">{{ t('flowupload', 'Please select a location') }}</div>
        		<div id="locationSelected" ng-cloak v-if="activeLocation != undefined">
        			<h2 id="title">{{ t('flowupload', 'Transfers') }}</h2>
        			<div class="buttonGroup">
        				<span v-uploadSelectButton class="button" uploadtype="file">
        				<span class="icon icon-file select-file-icon" style=""></span>
        				<span>{{ t('flowupload', 'Select File') }}</span>
        				</span>
        				<input id="FileSelectInput" type="file" multiple="multiple" @change="filesSelected">
        				<span v-uploadSelectButton class="button" uploadtype="folder" v-show="activeLocation.flow.supportDirectory">
        				<span class="icon icon-files" style="background-image: var(--icon-files-000);"></span>
        				<span>{{ t('flowupload', 'Select Folder') }}</span>
        				</span>
        				<input id="FolderSelectInput" type="file" multiple="multiple" webkitdirectory="webkitdirectory" @change="filesSelected">
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
        					<input type="checkbox" v-model="hideFinished">
        					<span>{{ t('flowupload', 'Hide finished uploads') }}</span>
        				</a>
        			</div>
        			<hr>
        			<p>
        				<span class="label">{{ t('flowupload', 'Size') + ' : ' + bytes(activeLocation.flow.getSize()) }}</span>
        				<span class="label" v-if="activeLocationFilesCount != 0">{{ t('flowupload', 'Progress') + ' : ' + trimDecimals(activeLocation.flow.progress()*100, 2) + '%'}}</span>
        				<span class="label" v-if="activeLocation.flow.isUploading()">{{ t('flowupload', 'Time remaining') + ' : ' + seconds(activeLocation.flow.timeRemaining()) }}</span>
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
        					<tr v-if="!(file.isComplete() && hideFinished)" v-for="(file, index) in activeLocation.flow.files">
        						<td class="hideOnMobile">{{index+1}}</td>
        						<td class="ellipsis" v-bind:title="'UID: ' + file.uniqueIdentifier">
        							<span>{{file.relativePath}}</span>
        						</td>
        						<td>
        							<div class="actions" v-if="!file.isComplete() || file.error">
        								<a class="action permanent" v-bind:title="t('flowupload', 'Resume')" v-on:click="file.resume()" v-if="!file.isUploading() && !file.error">
        								<span class="icon icon-play"></span>
        								</a>
        								<a class="action permanent" v-bind:title="t('flowupload', 'Pause')" v-on:click="file.pause()" v-if="file.isUploading() && !file.error">
        								<span class="icon icon-pause"></span>
        								</a>
        								<a class="action permanent" v-bind:title="t('flowupload', 'Retry')" v-on:click="file.retry()" v-show="file.error">
        								<span class="icon icon-play"></span>
        								</a>
        								<a class="action permanent" v-bind:title="t('flowupload', 'Cancel')" v-on:click="file.cancel()">
        								<span class="icon icon-close"></span>
        								</a>
        							</div>
        						</td>
        						<td class="hideOnMobile">
        							<span v-if="file.isUploading()">{{ byterate(file.currentSpeed) }}</span>
        						</td>
        						<td v-bind:title="'Chunks: ' + completedChunks(file) + ' / ' + file.chunks.length">
        							<span class="hideOnMobile" v-if="!file.isComplete()">{{ bytes(file.size*file.progress()) }}/</span>
        							<span>{{ bytes(file.size) }}</span>
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
    	</AppContent>
    </Content>
</template>

<script>
import Content from "@nextcloud/vue/dist/Components/Content";
import AppContent from "@nextcloud/vue/dist/Components/AppContent";
import AppNavigation from "@nextcloud/vue/dist/Components/AppNavigation";
import AppNavigationItem from "@nextcloud/vue/dist/Components/AppNavigationItem";
import AppNavigationNew from "@nextcloud/vue/dist/Components/AppNavigationNew";
import AppNavigationSettings from "@nextcloud/vue/dist/Components/AppNavigationSettings";
import AppSidebar from "@nextcloud/vue/dist/Components/AppSidebar";
import AppSidebarTab from "@nextcloud/vue/dist/Components/AppSidebarTab";
import AppNavigationCounter from "@nextcloud/vue/dist/Components/AppNavigationCounter";
import ActionButton from "@nextcloud/vue/dist/Components/ActionButton";
import ActionLink from "@nextcloud/vue/dist/Components/ActionLink";
import AppNavigationIconBullet from "@nextcloud/vue/dist/Components/AppNavigationIconBullet";
import ActionCheckbox from "@nextcloud/vue/dist/Components/ActionCheckbox";
import ActionInput from "@nextcloud/vue/dist/Components/ActionInput";
import ActionRouter from "@nextcloud/vue/dist/Components/ActionRouter";
import ActionText from "@nextcloud/vue/dist/Components/ActionText";
import ActionTextEditable from "@nextcloud/vue/dist/Components/ActionTextEditable";
import Flow from "@flowjs/flow.js";

export default {
	name: "App",
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
			loading: true,
			locations: [],
			baseUrl: OC.generateUrl("/apps/flowupload"),
			currentLocation: undefined,
			hideFinished: false,
			activeLocationPath: false,
		};
	},
	mounted: function (){
		var self = this;
		self.loadLocations().then(function(){
			console.log(self.locations);
            
			if(self.locations.length > 0){
				self.switchActiveLocationById(self.locations[0].id);
			}
            
			self.setupDynamicTitleInterval();
            
			self.loading = false;
		});
	},
	methods: {
		filesSelected: function(event) {
			this.activeLocation.flow.addFiles(event.target.files);
			$("#FileSelectInput, #FolderSelectInput").val(null);
		},
		setupDynamicTitleInterval: function() {
			var self = this;
			setInterval(function() {
				self.updateTitle();
			},500);
		},
		updateTitle: function() {
			if(this.activeLocation != undefined && this.activeLocation.flow.files.length !== 0){
				let progress = parseFloat(Math.round(this.activeLocation.flow.progress() * 100 * 100) / 100).toFixed(2); //round to two digits after comma
				document.title = "FlowUpload "+progress+"%";
			}else{
				document.title = "FlowUpload";
			}
		},
		switchActiveLocationById: function (id) {
    	    let location = this.getLocationById(id);
    	    console.log(location);
    	    
    		this.activeLocationPath = location.path;
    	},
    	switchActiveLocationByPath: function (path) {
    		this.activeLocationPath = path;
    	},
		getStarredLocations: function() {
			let url = this.baseUrl + "/directories";
			return new Promise(function (resolve, reject){
            	$.ajax({
					url: url,
					type: "GET",
					contentType: "application/json",
				}).done(function (response) {
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
			var self = this;
    	    OC.dialogs.filepicker("Select a new Upload Folder", function(path) {
				self.addLocation(false, path+"/",false);
				setTimeout(function(){
					self.switchActiveLocationByPath(path+"/");
				}, 500);
			}, false, "httpd/unix-directory", true, OC.dialogs.FILEPICKER_TYPE_CHOOSE);
    	},
		getLocationByPath: function(path) {
			for(let i=0; i < this.locations.length; i++){
				if(this.locations[i].path == path){
					return this.locations[i];
				}
			}
		},
		getLocationById: function(id) {
			for(let i=0; i < this.locations.length; i++){
				if(this.locations[i].id == id){
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
    		    "target": this.baseUrl+"/upload",
				"permanentErrors": [403, 404, 500, 501],
				"maxChunkRetries": 2,
				"chunkRetryInterval": 5000,
				"simultaneousUploads": 4
    	        }
			);
            
			this.locations.push({"id": id, "path": path, "starred": starred, "flow": newFlow});
		},
		starLocation: function(path){
			let location = this.getLocationByPath(path);
    	    
    	    
    	    $.ajax({
				url: this.baseUrl + "/directories",
				type: "POST",
				contentType: "application/json",
				data: JSON.stringify({path})
			}).done(function (response) {
				location.starred = true;
				location.id = response.id;
			});
	    },
	    unstarLocationById: function(id){
	        let location = this.getLocationById(id);
    	    
    	    $.ajax({
				url: this.baseUrl + "/directories/"+id,
				type: "DELETE",
			}).done(function (response) {
				location.starred = false;
    	        location.id = false;
			});
    	},
    	unstarLocationByPath: function(path) {
    	    let location = this.getLocationByPath(path);
    	    this.unstarLocationById(location.id);
    	},
    	toggleLocationStar: function(path) {
    	    let location = this.getLocationByPath(path);
    	    
    	    if(location.starred) {
    	        this.unstarLocationByPath(path);
    	    }else {
    	        this.starLocation(path);
    	    }
    	},
    	removeLocation: function(path) {
    	    var location = this.getLocationByPath(path);
    	    
    	    if(location.starred) {
    	        this.unstarLocationByPath(path);
    	    }
    	    
    	    if(this.activeLocation.path == path) {
    	        this.activeLocationPath = false;
    	    }
    	    
    	    this.locations = this.locations.filter(function(value, index, arr){ return value.path != path;});
    	},
    	trimDecimals: function(number, decimals = 2) {
    	    return number.toFixed(decimals);
    	},
    	bytes: function(bytes, precision) {
        	if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return "-";
        	if (typeof precision === "undefined") precision = 1;
        	    var units = ["bytes", "kB", "MB", "GB"];
        	    var	number = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)),units.length-1);
        	    return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  " " + units[number];
		},
    	byterate: function(bytes, precision) {
    		if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return "0 KB/s";
    		if (typeof precision === "undefined") precision = 1;
    		var units = ["B/s", "KB/s", "MB/s", "GB/s"];
    		var	number = Math.min(Math.floor(Math.log(bytes) / Math.log(1000)),units.length-1);
    		return (bytes / Math.pow(1000, Math.floor(number))).toFixed(precision) +  " " + units[number];
	    },
	    seconds: function(seconds, precision) {
    		if (isNaN(parseFloat(seconds)) || seconds == 0 || !isFinite(seconds)) return "-";
    		if (typeof precision === "undefined") precision = 1;
    		var units = ["s", "m", "h"];
    		var	number = Math.min(Math.floor(Math.log(seconds) / Math.log(60)),units.length-1);
    		return (seconds / Math.pow(60, Math.floor(number))).toFixed(precision) +  " " + units[number];
	    },
	    completedChunks: function(file) {
			let completeChunks = 0;
            
			file.chunks.forEach(function (c) {
				if(c.progress() === 1){
					completeChunks++;
				}
			});
            
			return completeChunks;
    	},
	},
	computed: {
		activeLocation: function() {
			if(this.activeLocationPath) {
				return this.getLocationByPath(this.activeLocationPath);
			}else {
				return undefined;
			}
		},
		filteredFiles: function() {
			if(this.activeLocation.flow) {
				return this.activeLocation.flow.files;
			}else {
				return [];
			}
		},
		activeLocationFilesCount : function() {
			if(this.activeLocation.flow.getFilesCount) {
				return this.activeLocation.flow.getFilesCount();
			}else {
				return 0;
			}
		},
	},
	filters: {
	},
	directives: {
		customLocationFileDropZone: {
			inserted: function (elm, binding, vnode) {
			    let flow = binding.arg.flow;
			    
				elm.addEventListener("drop", function (event) {
					var dataTransfer = event.dataTransfer;
                    
					if (dataTransfer.items && dataTransfer.items[0] &&
                        dataTransfer.items[0].webkitGetAsEntry) {
						flow.webkitReadDataTransfer(event);
					} else {
						flow.addFiles(dataTransfer.files, event);
					}
				});
				$(elm).on("drag dragstart dragend dragover dragenter dragleave drop", function(e) {
					e.preventDefault();
					e.stopPropagation();
				});
				$(elm).on("dragover dragenter", function() {
					$(elm).addClass("fileDrag");
				});
				$(elm).on("dragleave dragend drop", function() {
					$(elm).removeClass("fileDrag");
				});
			}
		},
		activeLocationFileDropZone: {
			inserted: function (elm, binding, vnode) {
                var self = vnode.context;
                
				elm.addEventListener("drop", function (event) {
					var dataTransfer = event.dataTransfer;
                    
					if (dataTransfer.items && dataTransfer.items[0] &&
                        dataTransfer.items[0].webkitGetAsEntry) {
						self.activeLocation.flow.webkitReadDataTransfer(event);
					} else {
						self.activeLocation.flow.addFiles(dataTransfer.files, event);
					}
				});
				$(elm).on("drag dragstart dragend dragover dragenter dragleave drop", function(e) {
					e.preventDefault();
					e.stopPropagation();
				});
				$(elm).on("dragover dragenter", function() {
					$(elm).addClass("fileDrag");
				});
				$(elm).on("dragleave dragend drop", function() {
					$(elm).removeClass("fileDrag");
				});
			}
		},
		uploadSelectButton: {
			inserted: function (elm, binding, vnode) {
				let uploadType = $(elm).attr("uploadType");
				if(uploadType == "file"){
					$(elm).on("click", function() {
						$("#FileSelectInput").click();
					});
				}else if(uploadType == "folder"){
					$(elm).on("click", function() {
						$("#FolderSelectInput").click();
					});
				}
			}
		}
	}
};
</script>
