<div id="app" ng-app="app" role="main">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

  <!-- APP NAVIAGTION -->
  <div id="app-navigation" ng-controller="locations">
    <div class="app-navigation-new">
        <ul>
            <li id="app-navigation-entry-utils-create" ng-click="addNewLocation()" class="app-navigation-entry-utils-menu-button">
              <button class="icon-add"><?= $l->t('New destination'); ?></button>
            </li>
        </ul>
    </div>
    <ul id="locations" class="with-icon">
      <li class="fileDropZone locations"  ng-controller="location" flow-init="init(location.id, location.location); beforeUploading" ng-repeat="location in locations" ng-init="$last && reloadLocations()" id="location-{{location.id}}">
        <a ng-href="" class="icon-folder"ng-click="setLocation(location.id, $flow)">{{location.location}}</a>
        <div class="app-navigation-entry-utils">
            <ul>
                <li class="app-navigation-entry-utils-counter" title="{{ $flow.files.length }} Files">{{ $flow.files.length }}</li>
                <li class="app-navigation-entry-utils-menu-button"><button></button></li>
            </ul>
        </div>
        <div class="app-navigation-entry-menu">
            <ul>
                <li>
                    <a href="">
                        <span class="icon-settings"></span>
                        <span>set Permanent</span>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="icon-delete"></span>
                        <span>Remove</span>
                    </a>
                </li>
            </ul>
        </div>
      </li>
    </ul>

    <input id="currentLocation" type="hidden" />
  </div>

  <!-- MAIN -->
  <div class="fileDropZone" ng-controller="flow" flow-init="beforeUploading" id="app-content" style="padding: 2.5%; width:auto">
    <h2 id="title"><?= $l->t('Transfers'); ?></h2>

    <div class="buttonGroup">
      <span class="uploadSelectButton button" uploadtype="file">
        <span class="icon icon-file select-file-icon" style=""></span>
        <span><?= $l->t('Select File'); ?></span>
      </span>
      <input id="FileSelectInput" type="file" multiple="multiple" style="visibility: hidden; position: absolute; width: 1px; height: 1px;">
      <span class="uploadSelectButton button" uploadtype="folder" ng-show="$flow.supportDirectory">
        <span class="icon icon-files" style="background-image: var(--icon-files-000);"></span>
        <span><?= $l->t('Select Folder'); ?></span>
      </span>
      <input id="FolderSelectInput" type="file" multiple="multiple" webkitdirectory="webkitdirectory" style="visibility: hidden; position: absolute; width: 1px; height: 1px;">
    </div>

    <hr>

    <div class="buttonGroup">
      <a class="button" ng-click="$flow.resume()">
          <span class="icon icon-play"></span>
          <span><?= $l->t('Start/Resume'); ?></span>
      </a>
      <a class="button" ng-click="$flow.pause()">
          <span class="icon icon-pause"></span>
          <span><?= $l->t('Pause'); ?></span>
      </a>
      <a class="button" ng-click="$flow.cancel()">
          <span class="icon icon-close"></span>
          <span><?= $l->t('Cancel'); ?></span>
      </a>
      <a id="hideFinishedButton" class="button" ng-click="hideFinished = !hideFinished">
        <input type="checkbox" ng-model="hideFinished"></input>
        <span><?= $l->t('Hide finished uploads'); ?></span>
      </a>
    </div>

    <hr>

    <p>
      <span class="label"><?= $l->t('Size'); ?>: {{$flow.getSize() | bytes}}</span>
      <span class="label" ng-if="$flow.getFilesCount() != 0"><?= $l->t('Progress'); ?>: {{$flow.progress()*100 | number:2}}%</span>
      <span class="label" ng-if="$flow.isUploading()"><?= $l->t('Time remaining'); ?>: {{$flow.timeRemaining() | seconds}}</span>
      <span class="label" ng-if="$flow.isUploading()"><?= $l->t('Uploading'); ?>...</span>
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
      <tr ng-if="!(file.isComplete() && hideFinished)" ng-repeat="file in $flow.files | orderBy:sortType:sortReverse">
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
        <td title="Chunks: {{file.completeChunks()}} / {{file.chunks.length}}">
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
