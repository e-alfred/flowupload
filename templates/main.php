<div id="content" ng-app="app" class="app-files" role="main">
  <div id="app-navigation">
    <ul id="locations" class="with-icon" ng-controller="locations">
      <li ng-repeat="location in locations" id="location-{{location.id}}" class="collapsible" ng-click="isOpen = !isOpen" ng-class="{'open' : isOpen}">
        <button class="collapse"></button>

        <a href="#" class="icon-folder">{{location.location}}</a>
        <ul>
          <li ng-click="seeUploads($event, location.id, 'pause')"><a class="icon-pause" href="#"><?= $l->t('Waiting'); ?> (<span class="upload-in-pause">{{location.pause}}</span>)</a></li>
          <li ng-click="seeUploads($event, location.id, 'uploading')"><a class="icon-upload" href="#"><?= $l->t('Uploading'); ?> (<span class="upload-uploading">{{location.uploading}}</span>)</a></li>
          <li ng-click="seeUploads($event, location.id, 'completed')"><a class="icon-checkmark" href="#"><?= $l->t('Completed'); ?> (<span class="upload-completed">{{location.completed}}</span>)</a></li>
          <li ng-click="seeUploads($event, location.id, 'aborted')"><a class="icon-close" href="#"><?= $l->t('Aborted'); ?> (<span class="upload-aborted">{{location.aborted}}</span>)</a></li>
          <li ng-click="addUpload(location.id)"><a class="icon-add" href="#"><?= $l->t('Upload in this location'); ?></a></li>
        </ul>
      </li>

      <li id="app-navigation-entry-utils-add">
        <a href="#"><?= $l->t('New destination'); ?></a>
        <div class="app-navigation-entry-utils">
          <ul>
            <li id="app-navigation-entry-utils-search" class="app-navigation-entry-utils-menu-button"><button class="icon-search"></button></li>
            <li id="app-navigation-entry-utils-create" class="app-navigation-entry-utils-menu-button"><button class="icon-add"></button></li>
          </ul>
        </div>
        <div class="app-navigation-entry-edit">
          <form>
            <input id="newLocationName" type="text" placeholder="<?= $l->t('Destination name'); ?>" ng-keydown="$event.keyCode === 13 && addNewLocation()">
            <input type="submit" value="" class="icon-close">
            <input ng-click="addNewLocation()" type="submit" value="" class="icon-checkmark">
          </form>
        </div>
      </li>
    </ul>

    <input id="currentLocation" type="hidden" />

    <div id="app-settings">
  		<div id="app-settings-header">
  			<button class="settings-button" data-apps-slide-toggle="#app-settings-content">Paramètres</button>
  		</div>
  		<div id="app-settings-content">
  			<div id="files-setting-showhidden">
  				<input class="checkbox" id="showhiddenfilesToggle" checked="checked" type="checkbox">
  				<label for="showhiddenfilesToggle"></label>
  			</div>
  			<label for="webdavurl">WebDAV</label>
  			<input id="webdavurl" readonly="readonly" value="https://cloud.nastuzzi.fr/remote.php/webdav/" type="text">
  			<em>Utilisez cette adresse pour <a href="https://docs.nextcloud.com/server/12/go.php?to=user-webdav" target="_blank" rel="noreferrer noopener">accéder à vos fichiers par WebDAV</a></em>
  		</div>
  	</div>
  </div>

  <div ng-controller="flowInfo" flow-init="beforeUploading" id="app-content" flow-prevent-drop ng-style="style" style="margin-left: 2%; width:auto">

    <span class="btn" flow-btn><?= $l->t('Select File'); ?></span>
    <span class="btn" flow-btn flow-directory ng-show="$flow.supportDirectory"><?= $l->t('Select Folder'); ?></span>

    <hr class="soften">

    <div class="alert" flow-drop flow-drag-enter="class='alert-success'" flow-drag-leave="class=''"
            ng-class="class"><?= $l->t('... or drag and drop your files here'); ?>
    </div>

    <hr class="soften">

    <h2><?= $l->t('Transfers'); ?></h2>
    <p>
      <a class="btn btn-small btn-success" ng-click="$flow.resume()"><?= $l->t('Upload'); ?></a>
      <a class="btn btn-small btn-danger" ng-click="$flow.pause()"><?= $l->t('Pause'); ?></a>
      <a class="btn btn-small btn-info" ng-click="$flow.cancel()"><?= $l->t('Cancel'); ?></a>
      <span class="label label-info"><?= $l->t('Size'); ?>: {{$flow.getSize() | bytes}}</span>
      <span class="label label-info" ng-if="$flow.isUploading()"><?= $l->t('Uploading'); ?>...</span>
    </p>
    <table class="table table-hover table-bordered table-striped" flow-transfers>
      <thead>
      <tr>
        <th style="width:5%">#</th>
        <th><?= $l->t('Name'); ?></th>
        <th style="width:10%"><?= $l->t('Size'); ?></th>
        <th style="width:20%"><?= $l->t('Progress'); ?></th>
      </tr>
      </thead>
      <tbody>
      <tr ng-repeat="file in transfers">
        <td>{{$index+1}}</td>
        <td title="UID: {{file.uniqueIdentifier}}">{{file.relativePath}}</td>
        <td title="Chunks: {{file.chunks.length}}"><span ng-if="file.isUploading()">{{file.size*file.progress() | bytes}}/</span>{{file.size | bytes}}</td>
        <td>
          <div class="btn-group" ng-if="!file.isComplete() || file.error()">
            <progress max="1" value="{{file.progress()}}" title="{{file.progress()}}" ng-if="file.isUploading()"></progress>
            <a class="btn btn-mini btn-warning" ng-click="file.pause()" ng-hide="file.paused">
              <?= $l->t('Pause'); ?>
            </a>
            <a class="btn btn-mini btn-warning" ng-click="file.resume()" ng-show="file.paused">
              <?= $l->t('Resume'); ?>
            </a>
            <a class="btn btn-mini btn-danger" ng-click="file.cancel()">
              <?= $l->t('Cancel'); ?>
            </a>
            <a class="btn btn-mini btn-info" ng-click="file.retry()" ng-show="file.error">
              <?= $l->t('Retry'); ?>
            </a>
          </div>
  	<span ng-if="file.isComplete() && !file.error()"><?= $l->t('Completed'); ?></span>
        </td>
      </tr>
      </tbody>
    </table>
    <p><a href="../files?dir=%2Fflowupload"><?= $l->t('The files will be saved in your home directory.'); ?></a></p>
 </div>
