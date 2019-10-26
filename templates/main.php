<div ng-app="app" flow-init id="app" ng-controller="mainController" ng-init="init()" flow-drop flow-drag-enter="class='file-drag'" flow-drag-leave="class=''" ng-class="class" ng-style="style">
  <h2 id="title"><?= $l->t('Transfers'); ?></h2>
  
  <span class="button" flow-btn>
      <span class="icon icon-file" style="background-image: var(--icon-file-000);"></span>
      <span><?= $l->t('Select File'); ?></span>
  </span>
  <span class="button" flow-btn flow-directory ng-show="$flow.supportDirectory">
      <span class="icon icon-files" style="background-image: var(--icon-files-000);"></span>
      <span><?= $l->t('Select Folder'); ?></span>
  </span>

  <hr class="soften">

  <p>
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
    <a class="button" ng-click="hideFinished = !hideFinished">
      <input id="hideFinishedCheckbox" type="checkbox" ng-model="hideFinished"></input>
      <span id="hideFinishedText"><?= $l->t('Hide finished uploads'); ?></span>
    </a>
  </p>
  
  <hr class="soften">
  
  <p>
    <span class="label"><?= $l->t('Size'); ?>: {{$flow.getSize() | bytes}}</span>
    <span class="label" ng-if="$flow.getFilesCount() != 0"><?= $l->t('Progress'); ?>: {{$flow.progress()*100 | number:2}}%</span>
    <span class="label" ng-if="$flow.isUploading()"><?= $l->t('Uploading'); ?>...</span>
  </p>
  
  <hr class="soften">
  
  <table id="uploadsTable" flow-transfers>
    <thead id="uploadsTableThead">
    <tr id="uploadsTableTheadTr">
      <th class="uploadsTableTheadTh" style="width:5%">
        <span class="columntitle noselect">#</span>
      </th>
      <th class="uploadsTableTheadTh" ng-click="tableSortClicked('relativePath')">
        <a class="columntitle noselect">
          <span><?= htmlspecialchars($l->t('Name')); ?></span>
          <span ng-class="{ 'icon-triangle-n':  (sortType == 'relativePath' && sortReverse), 'icon-triangle-s': (sortType == 'relativePath' && !sortReverse)}" class="sort-indicator"></span>
        </a>
      </th>
      <th class="uploadsTableTheadTh"></th>
      <th class="uploadsTableTheadTh" ng-click="tableSortClicked('-size')" style="width:10%">
        <a class="columntitle noselect">
          <span><?= htmlspecialchars($l->t('Size')); ?></span>
          <span ng-class="{ 'icon-triangle-n':  (sortType == '-size' && sortReverse), 'icon-triangle-s': (sortType == '-size' && !sortReverse)}" class="sort-indicator"></span>
        </a>
      </th>
      <th class="uploadsTableTheadTh" ng-click="tableSortClicked('-progress()')" style="width:20%">
        <a class="columntitle noselect">
          <span><?= htmlspecialchars($l->t('Progress')); ?></span>
          <span ng-class="{ 'icon-triangle-n':  (sortType == '-progress()' && sortReverse), 'icon-triangle-s': (sortType == '-progress()' && !sortReverse)}" class="sort-indicator"></span>
        </a>
      </th>
    </tr>
    </thead>
    <tbody id="uploadsTableBody">
    <tr class="uploadsTableTbodyTr" ng-if="!(file.isComplete() && hideFinished)" ng-repeat="file in transfers | orderBy:sortType:sortReverse">
      <td class="uploadsTableTbodyTd">{{$index+1}}</td>
      <td class="uploadsTableTbodyTd" title="UID: {{file.uniqueIdentifier}}">{{file.relativePath}}</td>
      <td class="uploadsTableTbodyTd">
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
      <td class="uploadsTableTbodyTd" title="Chunks: {{file.completeChunks()}} / {{file.chunks.length}}">
          <span ng-if="!file.isComplete()">{{file.size*file.progress() | bytes}}/</span>
          <span>{{file.size | bytes}}</span>
      </td>
      <td class="uploadsTableTbodyTd">
        <progress ng-if="!file.isComplete() && !file.error" class="progressbar" max="1" value="{{file.progress()}}" title="{{file.progress()*100 | number:2}}%"></progress>
        <span ng-if="!file.isComplete() && !file.error">{{file.progress()*100 | number:2}}%</span>
        <span ng-if="file.isComplete() && !file.error"><?= htmlspecialchars($l->t('Completed')); ?></span>
        <span ng-if="file.error"><?= htmlspecialchars($l->t('Error')); ?></span>
      </td>
    </tr>
    </tbody>
  </table>
  <p style="margin-top: 25px;"><a href="../files?dir=%2Fflowupload"><?= htmlspecialchars($l->t('The files will be saved in your home directory.')); ?></a></p>
 </div>