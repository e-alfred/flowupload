<div ng-app="app" flow-init id="app" ng-controller="mainController" flow-drop flow-drag-enter="class='file-drag'" flow-drag-leave="class=''" ng-class="class">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

  <h2 id="title"><?= $l->t('Transfers'); ?></h2>

  <div class="buttonGroup">
    <span class="button" flow-btn>
      <span class="icon icon-file select-file-icon" style=""></span>
      <span><?= $l->t('Select File'); ?></span>
    </span>
    <span class="button" flow-btn flow-directory ng-show="$flow.supportDirectory">
      <span class="icon icon-files" style="background-image: var(--icon-files-000);"></span>
      <span><?= $l->t('Select Folder'); ?></span>
    </span>
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
    <span class="label" ng-if="$flow.isUploading()"><?= $l->t('Uploading'); ?>...</span>
  </p>

  <hr>

  <table id="uploadsTable" flow-transfers>
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
    <tr ng-if="!(file.isComplete() && hideFinished)" ng-repeat="file in transfers | orderBy:sortType:sortReverse">
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
 </div>
