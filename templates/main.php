<div ng-app="app" flow-init id="app" flow-prevent-drop ng-style="style" style="margin: 2em; width:100%">

  <span class="btn" flow-btn><?= $l->t('Select File'); ?></span>
  <span class="btn" flow-btn flow-directory ng-show="$flow.supportDirectory"><?= $l->t('Select Folder'); ?></span>

  <hr class="soften">

  <div class="alert" flow-drop flow-drag-enter="class='alert-success'" flow-drag-leave="class=''"
          ng-class="class"><?= $l->t('... or drag and drop your files here'); ?>
  </div>

  <hr class="soften">

  <h2><?= $l->t('Transfers'); ?></h2>
  <p>
    <a class="btn btn-small btn-success" ng-click="$flow.resume()"><?= $l->t('Upload/Resume all'); ?></a>
    <a class="btn btn-small btn-danger" ng-click="$flow.pause()"><?= $l->t('Pause'); ?></a>
    <a class="btn btn-small btn-info" ng-click="$flow.cancel()"><?= $l->t('Cancel'); ?></a>
    <span class="label label-info"><?= $l->t('Size'); ?>: {{$flow.getSize() | bytes}}</span>
    <span class="label label-info" ng-if="$flow.getFilesCount() != 0"><?= $l->t('Progress'); ?>: {{$flow.progress()*100 | number:2}}%</span>
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
      <td title="Chunks: {{file.completeChunks()}} / {{file.chunks.length}}"><span ng-if="file.isUploading()">{{file.size*file.progress() | bytes}}/</span>{{file.size | bytes}}</td>
      <td>
        <div class="btn-group" ng-if="!file.isComplete() || file.error">
          <progress max="1" value="{{file.progress()}}" title="{{file.progress()}}" ng-if="file.isUploading()" style="width:auto; height:auto; display:inline"></progress>
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
	<span ng-if="file.isComplete() && !file.error"><?= $l->t('Completed'); ?></span>
      </td>
    </tr>
    </tbody>
  </table>
  <p><a href="../files?dir=%2Fflowupload"><?= $l->t('The files will be saved in your home directory.'); ?></a></p>
 </div>
