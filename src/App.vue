<template>
	<Content :class="{'icon-loading': loading}" app-name="flowupload">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<!-- APP NAVIAGTION -->
		<AppNavigation>
			<AppNavigationNew
				:text="t('flowupload', 'New destination')"
				:disabled="false"
				button-id="new-location-button"
				button-class="icon-add"
				@click="pickNewLocation()" />
			<ul id="locations" class="with-icon">
				<AppNavigationItem
					v-for="location in locations"
					:id="'location-' + location.path"
					v-if="!loading"
					:key="location.path"
					v-customLocationFileDropZone:[location]
					active="true"
					:class="{'active': activeLocation && location.path == activeLocation.path}"
					:title="location.path"
					icon="icon-folder"
					@click="switchActiveLocationByPath(location.path)">
					<AppNavigationCounter slot="counter" :highlighted="true">
						{{ location.flow.files.length }}
					</AppNavigationCounter>
					<template slot="actions">
						<ActionButton icon="icon-edit" @click="openLocationInFiles(location.path)">
							{{ t('flowupload', 'Open') }}
						</ActionButton>
						<ActionButton v-if="!location.starred" icon="icon-starred" @click="starLocation(location.path)">
							{{ t('flowupload', 'Star') }}
						</ActionButton>
						<ActionButton v-if="location.starred" icon="icon-starred" @click="unstarLocationByPath(location.path)">
							{{ t('flowupload', 'Unstar') }}
						</ActionButton>
						<ActionButton icon="icon-delete" @click="removeLocation(location.path)">
							{{ t('flowupload', 'Remove') }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul>
		</AppNavigation>
		<!-- CONTENT -->
		<AppContent>
			<div v-if="!loading" v-activeLocationFileDropZone style="margin-left: 4%; margin-right: 4%; margin-top: 7px; width:auto">
				<div v-if="activeLocation === undefined" id="noLocationSelected">
					{{ t('flowupload', 'Please select a location') }}
				</div>
				<div v-if="activeLocation != undefined" id="locationSelected">
					<h2 id="title">
						{{ t('flowupload', 'Transfers') }}
					</h2>
					<div class="buttonGroup">
						<span v-uploadSelectButton class="button" uploadtype="file">
							<span class="icon icon-file select-file-icon" />
							<span>{{ t('flowupload', 'Select File') }}</span>
						</span>
						<input id="FileSelectInput"
							type="file"
							multiple="multiple"
							@change="filesSelected">
						<span v-show="activeLocation.flow.supportDirectory"
							v-uploadSelectButton
							class="button"
							uploadtype="folder">
							<span class="icon icon-files" style="background-image: var(--icon-files-000);" />
							<span>{{ t('flowupload', 'Select Folder') }}</span>
						</span>
						<input id="FolderSelectInput"
							type="file"
							multiple="multiple"
							webkitdirectory="webkitdirectory"
							@change="filesSelected">
					</div>
					<hr>
					<div class="buttonGroup">
						<a class="button" @click="activeLocation.flow.resume()">
							<span class="icon icon-play" />
							<span>{{ t('flowupload', 'Start/Resume') }}</span>
						</a>
						<a class="button" @click="activeLocation.flow.pause()">
							<span class="icon icon-pause" />
							<span>{{ t('flowupload', 'Pause') }}</span>
						</a>
						<a class="button" @click="activeLocation.flow.cancel()">
							<span class="icon icon-close" />
							<span>{{ t('flowupload', 'Cancel') }}</span>
						</a>
						<a id="hideFinishedButton" class="button" @click="hideFinished = !hideFinished">
							<input v-model="hideFinished" type="checkbox">
							<span>{{ t('flowupload', 'Hide finished uploads') }}</span>
						</a>
					</div>
					<hr>
					<p>
						<span class="label">{{ t('flowupload', 'Size') + ' : ' + bytes(activeLocation.flow.getSize()) }}</span>
						<span v-if="activeLocationFilesCount != 0" class="label">{{ t('flowupload', 'Progress') + ' : ' + trimDecimals(activeLocation.flow.progress()*100, 2) + '%' }}</span>
						<span v-if="activeLocation.flow.isUploading()" class="label">{{ t('flowupload', 'Time remaining') + ' : ' + seconds(activeLocation.flow.timeRemaining()) }}</span>
						<span v-if="activeLocation.flow.isUploading()" class="label">{{ t('flowupload', 'Uploading') + '...' }}</span>
					</p>
					<hr>
					<table id="uploadsTable">
						<thead>
							<tr>
								<th class="hideOnMobile" style="width:5%">
									<span class="noselect">#</span>
								</th>
								<th @click="selectSortingMethod('name')">
									<a class="noselect">
										<span>{{ t('flowupload', 'Name') }}</span>
										<span :class="{'icon-triangle-n': (sort == 'name' && sortReverse), 'icon-triangle-s': (sort == 'name' && !sortReverse)}" class="sortIndicator" />
									</a>
								</th>
								<th />
								<th class="hideOnMobile" style="width:10%" @click="selectSortingMethod('uploadspeed')">
									<a class="noselect">
										<span>{{ t('flowupload', 'Upload speed') }}</span>
										<span :class="{'icon-triangle-n': (sort == 'uploadspeed' && sortReverse), 'icon-triangle-s': (sort == 'uploadspeed' && !sortReverse)}" class="sortIndicator" />
									</a>
								</th>
								<th style="width:10%" @click="selectSortingMethod('size')">
									<a class="noselect">
										<span>{{ t('flowupload', 'Size') }}</span>
										<span :class="{'icon-triangle-n': (sort == 'size' && sortReverse), 'icon-triangle-s': (sort == 'size' && !sortReverse)}" class="sortIndicator" />
									</a>
								</th>
								<th style="width:20%" @click="selectSortingMethod('progress')">
									<a class="noselect">
										<span>{{ t('flowupload', 'Progress') }}</span>
										<span :class="{'icon-triangle-n': (sort == 'progress' && sortReverse), 'icon-triangle-s': (sort == 'progress' && !sortReverse)}" class="sortIndicator" />
									</a>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(file, index) in filteredFiles" v-if="!(file.isComplete() && hideFinished)">
								<td class="hideOnMobile">
									{{ index+1 }}
								</td>
								<td class="ellipsis" :title="'UID: ' + file.uniqueIdentifier">
									<span>{{ file.relativePath }}</span>
								</td>
								<td>
									<div v-if="!file.isComplete() || file.error" class="actions">
										<a v-if="!file.isUploading() && !file.error"
											class="action permanent"
											:title="t('flowupload', 'Resume')"
											@click="file.resume()">
											<span class="icon icon-play" />
										</a>
										<a v-if="file.isUploading() && !file.error"
											class="action permanent"
											:title="t('flowupload', 'Pause')"
											@click="file.pause()">
											<span class="icon icon-pause" />
										</a>
										<a v-show="file.error"
											class="action permanent"
											:title="t('flowupload', 'Retry')"
											@click="file.retry()">
											<span class="icon icon-play" />
										</a>
										<a class="action permanent" :title="t('flowupload', 'Cancel')" @click="file.cancel()">
											<span class="icon icon-close" />
										</a>
									</div>
								</td>
								<td class="hideOnMobile">
									<span v-if="file.isUploading()">{{ byterate(file.currentSpeed) }}</span>
								</td>
								<td :title="'Chunks: ' + completedChunks(file) + ' / ' + file.chunks.length">
									<span v-if="!file.isComplete()" class="hideOnMobile">{{ bytes(file.size*file.progress()) }}/</span>
									<span>{{ bytes(file.size) }}</span>
								</td>
								<td>
									<progress v-if="!file.isComplete() && !file.error"
										class="progressbar hideOnMobile"
										max="1"
										:value="file.progress()" />
									<span v-if="!file.isComplete() && !file.error">{{ trimDecimals(file.progress()*100, 2) }}%</span>
									<span v-if="file.isComplete() && !file.error">{{ t('flowupload', 'Completed') }}</span>
									<span v-if="file.error">{{ t('flowupload', 'Error') }}</span>
								</td>
							</tr>
						</tbody>
					</table>
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
	filters: {},
	directives: {
		customLocationFileDropZone: {
			inserted(elm, binding, vnode) {
				const flow = binding.arg.flow;

				elm.addEventListener("drop", function(event) {
					const dataTransfer = event.dataTransfer;

					if (dataTransfer.items && dataTransfer.items[0]
						&& dataTransfer.items[0].webkitGetAsEntry) {
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
			},
		},
		activeLocationFileDropZone: {
			inserted(elm, binding, vnode) {
				const self = vnode.context;

				elm.addEventListener("drop", function(event) {
					const dataTransfer = event.dataTransfer;

					if (dataTransfer.items && dataTransfer.items[0]
						&& dataTransfer.items[0].webkitGetAsEntry) {
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
			},
		},
		uploadSelectButton: {
			inserted(elm, binding, vnode) {
				const uploadType = $(elm).attr("uploadType");
				if (uploadType == "file") {
					$(elm).on("click", function() {
						$("#FileSelectInput").click();
					});
				} else if (uploadType == "folder") {
					$(elm).on("click", function() {
						$("#FolderSelectInput").click();
					});
				}
			},
		},
	},
	data() {
		return {
			loading: true,
			locations: [],
			baseUrl: OC.generateUrl("/apps/flowupload"),
			currentLocation: undefined,
			hideFinished: false,
			activeLocationPath: false,
			sort: "name",
			sortReverse: false,
			search: "",
		};
	},
	computed: {
		activeLocation() {
			if (this.activeLocationPath) {
				return this.getLocationByPath(this.activeLocationPath);
			} else {
				return undefined;
			}
		},
		filteredFiles() {
		    const self = this;

			if (this.activeLocation.flow) {
				let sorted;

				if (this.sort == "name") {
					sorted = this.activeLocation.flow.files.sort(function(a, b) {
						console.log(a);
						const nameA = a.relativePath.toLowerCase();
						const nameB = b.relativePath.toLowerCase();
						if (nameA < nameB) // sort string ascending
						{ return -1; }
						if (nameA > nameB) { return 1; }
						return 0; // default return value (no sorting)
					});
				} else if (this.sort == "size") {
					sorted = this.activeLocation.flow.files.sort(function(a, b) {
						return b.size - a.size;
					});
				} else if (this.sort == "progress") {
					sorted = this.activeLocation.flow.files.sort(function(a, b) {
						return b.progress() - a.progress();
					});
				} else if (this.sort == "uploadspeed") {
					sorted = this.activeLocation.flow.files.sort(function(a, b) {
						return b.averageSpeed - a.averageSpeed;
					});
				}

				if (this.sortReverse) {
					sorted.reverse();
				}

				if (this.search != "") {
				    sorted = sorted.filter(function(file) {
				        return file.relativePath.toLowerCase().includes(self.search.toLowerCase());
				    });
				}

				return sorted;
			} else {
				return [];
			}
		},
		activeLocationFilesCount() {
			if (this.activeLocation.flow.getFilesCount) {
				return this.activeLocation.flow.getFilesCount();
			} else {
				return 0;
			}
		},
	},
	mounted() {
		const self = this;
		self.loadLocations().then(function() {
			console.log(self.locations);

			if (self.locations.length > 0) {
				self.switchActiveLocationById(self.locations[0].id);
			}

			self.setupDynamicTitleInterval();
			self.setupSearch();

			self.loading = false;
		});
	},
	methods: {
		filesSelected(event) {
			this.activeLocation.flow.addFiles(event.target.files);
			$("#FileSelectInput, #FolderSelectInput").val(null);
		},
		setupDynamicTitleInterval() {
			const self = this;
			setInterval(function() {
				self.updateTitle();
			}, 500);
		},
		setupSearch() {
		    const self = this;
		    new OCA.Search(function(value) {
				self.search = value;
		    }, function() {
		        self.search = "";
		    });
		},
		updateTitle() {
			if (this.activeLocation != undefined && this.activeLocation.flow.files.length !== 0) {
				const progress = parseFloat(Math.round(this.activeLocation.flow.progress() * 100 * 100) / 100).toFixed(2); // round to two digits after comma
				document.title = "FlowUpload " + progress + "%";
			} else {
				document.title = "FlowUpload";
			}
		},
		switchActiveLocationById(id) {
			const location = this.getLocationById(id);
			console.log(location);

			this.activeLocationPath = location.path;
		},
		switchActiveLocationByPath(path) {
			this.activeLocationPath = path;
		},
		getStarredLocations() {
			const url = this.baseUrl + "/directories";
			return new Promise(function(resolve, reject) {
				$.ajax({
					url,
					type: "GET",
					contentType: "application/json",
				}).done(function(response) {
					resolve(response);
				});
			});
		},
		loadLocations() {
			const self = this;
			return new Promise(function(resolve, reject) {
				self.getStarredLocations().then(function(locations) {
					self.locations = [];

					for (let i = 0; i < locations.length; i++) {
						self.addLocation(locations[i].id, locations[i].directory, true);
					}

					resolve();
				});
			});
		},
		pickNewLocation() {
			const self = this;
			OC.dialogs.filepicker("Select a new Upload Folder", function(path) {
				self.addLocation(false, path + "/", false);
				setTimeout(function() {
					self.switchActiveLocationByPath(path + "/");
				}, 500);
			}, false, "httpd/unix-directory", true, OC.dialogs.FILEPICKER_TYPE_CHOOSE);
		},
		getLocationByPath(path) {
			for (let i = 0; i < this.locations.length; i++) {
				if (this.locations[i].path == path) {
					return this.locations[i];
				}
			}
			return false;
		},
		getLocationById(id) {
			for (let i = 0; i < this.locations.length; i++) {
				if (this.locations[i].id == id) {
					return this.locations[i];
				}
			}
			return false;
		},
		addLocation(id, path, starred) {
		    if (!this.getLocationByPath(path)) {
    			const newFlow = new Flow({
    				query(flowFile, flowChunk) {
    					return {
    						target: path,
    					};
    				},
    				target: this.baseUrl + "/upload",
    				permanentErrors: [403, 404, 500, 501],
    				maxChunkRetries: 2,
    				chunkRetryInterval: 5000,
    				simultaneousUploads: 4,
    			});

    			this.locations.push({
    				id,
    				path,
    				starred,
    				flow: newFlow,
    			});
		    } else {
		        OC.Notification.showTemporary(t("flowupload", "This location already exists"));
		    }
		},
		starLocation(path) {
			const location = this.getLocationByPath(path);

			$.ajax({
				url: this.baseUrl + "/directories",
				type: "POST",
				contentType: "application/json",
				data: JSON.stringify({
					path,
				}),
			}).done(function(response) {
				location.starred = true;
				location.id = response.id;
			});
		},
		unstarLocationById(id) {
			const location = this.getLocationById(id);

			$.ajax({
				url: this.baseUrl + "/directories/" + id,
				type: "DELETE",
			}).done(function(response) {
				location.starred = false;
				location.id = false;
			});
		},
		unstarLocationByPath(path) {
			const location = this.getLocationByPath(path);
			this.unstarLocationById(location.id);
		},
		toggleLocationStar(path) {
			const location = this.getLocationByPath(path);

			if (location.starred) {
				this.unstarLocationByPath(path);
			} else {
				this.starLocation(path);
			}
		},
		removeLocation(path) {
			const location = this.getLocationByPath(path);

			if (location.starred) {
				this.unstarLocationByPath(path);
			}

			if (this.activeLocation.path == path) {
				this.activeLocationPath = false;
			}

			this.locations = this.locations.filter(function(value, index, arr) {
				return value.path != path;
			});
		},
		trimDecimals(number, decimals = 2) {
			return number.toFixed(decimals);
		},
		bytes(bytes, precision) {
			if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return "-";
			if (typeof precision === "undefined") precision = 1;
			const units = ["bytes", "kB", "MB", "GB"];
			const number = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
			return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) + " " + units[number];
		},
		byterate(bytes, precision) {
			if (isNaN(parseFloat(bytes)) || bytes == 0 || !isFinite(bytes)) return "0 KB/s";
			if (typeof precision === "undefined") precision = 1;
			const units = ["B/s", "KB/s", "MB/s", "GB/s"];
			const number = Math.min(Math.floor(Math.log(bytes) / Math.log(1000)), units.length - 1);
			return (bytes / Math.pow(1000, Math.floor(number))).toFixed(precision) + " " + units[number];
		},
		seconds(seconds, precision) {
			if (isNaN(parseFloat(seconds)) || seconds == 0 || !isFinite(seconds)) return "-";
			if (typeof precision === "undefined") precision = 1;
			const units = ["s", "m", "h"];
			const number = Math.min(Math.floor(Math.log(seconds) / Math.log(60)), units.length - 1);
			return (seconds / Math.pow(60, Math.floor(number))).toFixed(precision) + " " + units[number];
		},
		completedChunks(file) {
			let completeChunks = 0;

			file.chunks.forEach(function(c) {
				if (c.progress() === 1) {
					completeChunks++;
				}
			});

			return completeChunks;
		},
		openLocationInFiles(path) {
			window.open("/index.php/apps/files/?dir=" + path, "_blank");
		},
		selectSortingMethod(sortMethod) {
			if (this.sort == sortMethod) {
				this.sortReverse = !this.sortReverse;
			} else {
				this.sort = sortMethod;
				this.sortReverse = false;
			}

			console.log(this.sort);
			console.log(this.sortReverse);
		},
	},
};
</script>
