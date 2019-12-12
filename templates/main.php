<div id="app" ng-app="app" role="main">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

  <!-- APP NAVIAGTION -->
  <div id="app-navigation" ng-controller="locations">
    <div class="app-navigation-new">
        <ul>
            <li id="app-navigation-entry-utils-create" ng-click="pickNewLocation()" class="app-navigation-entry-utils-menu-button">
              <button class="icon-add"><?= htmlspecialchars($l->t('New destination')); ?></button>
            </li>
        </ul>
    </div>
    <ul id="locations" class="with-icon">
      <li class="fileDropZone locations" ng-cloak ng-controller="location" ng-repeat="location in locations" id="location-{{location.path}}">
        <a ng-href="" class="icon-folder" ng-click="setLocation(location.path)" title="{{ location.path }}">{{location.path}}</a>
        <div class="app-navigation-entry-utils">
            <ul>
                <li class="app-navigation-entry-utils-counter" title="{{ location.flow.files.length }} <?= htmlspecialchars($l->t('Files')); ?>">{{ location.flow.files.length }}</li>
                <li class="app-navigation-entry-utils-menu-button"><button></button></li>
            </ul>
        </div>
        <div class="app-navigation-entry-menu">
            <ul>
                <li>
                    <a href="/index.php/apps/files/?dir={{ location.path }}" target="_blank" rel="noopener noreferrer">
                        <span class="icon-files"></span>
                        <span><?= htmlspecialchars($l->t('Open')); ?></span>
                    </a>
                </li>
                <li ng-click="toggleStarredLocation(location.path)">
                    <a href="">
                        <span class="icon-starred"></span>
                        <span ng-if="!location.starred"><?= htmlspecialchars($l->t('Star')); ?></span>
                        <span ng-if="location.starred"><?= htmlspecialchars($l->t('Unstar')); ?></span>
                    </a>
                </li>
                <li ng-click="removeLocation(location.path)">
                    <a href="">
                        <span class="icon-delete"></span>
                        <span><?= htmlspecialchars($l->t('Remove')); ?></span>
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
                <?= htmlspecialchars($l->t('Settings')); ?>
            </button>
        </div>
        <div id="app-settings-content">
            <!-- Your settings content here -->
        </div>
    </div>
  </div>

  <!-- MAIN -->
  <div class="fileDropZone" ng-controller="flow" id="app-content" style="padding: 2.5%; width:auto">
    <div id="noLocationSelected"ng-cloak ng-show="location === undefined && loaded"><?= $l->t('Please select a location'); ?></div>
    <div id="locationSelected" ng-cloak ng-hide="location === undefined">
        <h2 id="title"><?= htmlspecialchars($l->t('Transfers')); ?></h2>

        <div class="buttonGroup">
          <span class="uploadSelectButton button" uploadtype="file">
            <span class="icon icon-file select-file-icon" style=""></span>
            <span><?= htmlspecialchars($l->t('Select File')); ?></span>
          </span>
          <input id="FileSelectInput" type="file" multiple="multiple">
          <span class="uploadSelectButton button" uploadtype="folder" ng-show="location.flow.supportDirectory">
            <span class="icon icon-files" style="background-image: var(--icon-files-000);"></span>
            <span><?= htmlspecialchars($l->t('Select Folder')); ?></span>
          </span>
          <input id="FolderSelectInput" type="file" multiple="multiple" webkitdirectory="webkitdirectory">
        </div>

        <hr>

        <div class="buttonGroup">
          <a class="button" ng-click="location.flow.resume()">
              <span class="icon icon-play"></span>
              <span><?= htmlspecialchars($l->t('Start/Resume')); ?></span>
          </a>
          <a class="button" ng-click="location.flow.pause()">
              <span class="icon icon-pause"></span>
              <span><?= htmlspecialchars($l->t('Pause')); ?></span>
          </a>
          <a class="button" ng-click="location.flow.cancel()">
              <span class="icon icon-close"></span>
              <span><?= htmlspecialchars($l->t('Cancel')); ?></span>
          </a>
          <a id="hideFinishedButton" class="button" ng-click="hideFinished = !hideFinished">
            <input type="checkbox" ng-model="hideFinished"></input>
            <span><?= htmlspecialchars($l->t('Hide finished uploads')); ?></span>
          </a>
        </div>

        <hr>

        <p>
          <span class="label"><?= $l->t('Size'); ?>: {{location.flow.getSize() | bytes}}</span>
          <span class="label" ng-if="location.flow.getFilesCount() != 0"><?= htmlspecialchars($l->t('Progress')); ?>: {{location.flow.progress()*100 | number:2}}%</span>
          <span class="label" ng-if="location.flow.isUploading()"><?= htmlspecialchars($l->t('Time remaining')); ?>: {{location.flow.timeRemaining() | seconds}}</span>
          <span class="label" ng-if="location.flow.isUploading()"><?= htmlspecialchars($l->t('Uploading')); ?>...</span>
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
                <span><?= htmlspecialchars($l->t('Name')); ?></span>
                <span ng-class="{'icon-triangle-n':  (sortType == 'relativePath' && sortReverse), 'icon-triangle-s': (sortType == 'relativePath' && !sortReverse)}" class="sortIndicator"></span>
              </a>
            </th>
            <th></th>
            <th class="hideOnMobile" ng-click="tableSortClicked('-currentSpeed')" style="width:10%">
              <a class="noselect">
                <span><?= htmlspecialchars($l->t('Uploadspeed')); ?></span>
                <span ng-class="{'icon-triangle-n':  (sortType == '-currentSpeed' && sortReverse), 'icon-triangle-s': (sortType == '-currentSpeed' && !sortReverse)}" class="sortIndicator"></span>
              </a>
            </th>
            <th ng-click="tableSortClicked('-size')" style="width:10%">
              <a class="noselect">
                <span><?= htmlspecialchars($l->t('Size')); ?></span>
                <span ng-class="{'icon-triangle-n':  (sortType == '-size' && sortReverse), 'icon-triangle-s': (sortType == '-size' && !sortReverse)}" class="sortIndicator"></span>
              </a>
            </th>
            <th ng-click="tableSortClicked('-progress()')" style="width:20%">
              <a class="noselect">
                <span><?= htmlspecialchars($l->t('Progress')); ?></span>
                <span ng-class="{'icon-triangle-n':  (sortType == '-progress()' && sortReverse), 'icon-triangle-s': (sortType == '-progress()' && !sortReverse)}" class="sortIndicator"></span>
              </a>
            </th>
          </tr>
          </thead>
          <tbody>
          <tr ng-if="!(file.isComplete() && hideFinished)" ng-repeat="file in location.flow.files | orderBy:sortType:sortReverse">
            <td class="hideOnMobile">{{$index+1}}</td>
            <td class="ellipsis" title="UID: {{file.uniqueIdentifier}}">
                <span>{{file.relativePath}}</span>
            </td>
            <td>
              <div class="actions" ng-if="!file.isComplete() || file.error">
                <a class="action permanent" title="<?= htmlspecialchars($l->t('Resume')); ?>" ng-click="file.resume()" ng-if="!file.isUploading() && !file.error">
                  <span class="icon icon-play"></span>
                </a>
                <a class="action permanent" title="<?= htmlspecialchars($l->t('Pause')); ?>" ng-click="file.pause()" ng-if="file.isUploading() && !file.error">
                  <span class="icon icon-pause"></span>
                </a>
                <a class="action permanent" title="<?= htmlspecialchars($l->t('Retry')); ?>" ng-click="file.retry()" ng-show="file.error">
                  <span class="icon icon-play"></span>
                </a>
                <a class="action permanent" title="<?= htmlspecialchars($l->t('Cancel')); ?>" ng-click="file.cancel()">
                  <span class="icon icon-close"></span>
                </a>
              </div>
            </td>
            <td class="hideOnMobile">
                <span ng-if="file.isUploading()">{{file.currentSpeed | byterate}}</span>
            </td>
            <td title="Chunks: {{file | completedChunks}} / {{file.chunks.length}}">
                <span class="hideOnMobile" ng-if="!file.isComplete()">{{file.size*file.progress() | bytes}}/</span>
                <span>{{file.size | bytes}}</span>
            </td>
            <td>
              <progress ng-if="!file.isComplete() && !file.error" class="progressbar hideOnMobile" max="1" value="{{file.progress()}}"></progress>
              <span ng-if="!file.isComplete() && !file.error">{{file.progress()*100 | number:2}}%</span>
              <span ng-if="file.isComplete() && !file.error"><?= htmlspecialchars($l->t('Completed')); ?></span>
              <span ng-if="file.error"><?= htmlspecialchars($l->t('Error')); ?></span>
            </td>
          </tr>
          </tbody>
        </table>

        <p id="homeDirectoryLink"><a href="../files?dir=%2Fflowupload"><?= htmlspecialchars($l->t('The files will be saved in your home directory.')); ?></a></p>
      <div>
      </div>
 </div>
