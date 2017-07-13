<div ng-app="app" flow-init id="app" flow-prevent-drop ng-style="style" style="margin: 2em; width:auto">

  <span class="btn" flow-btn>Select File</span>
  <span class="btn" flow-btn flow-directory ng-show="$flow.supportDirectory">Select Folder</span>

  <hr class="soften">

  <div class="alert" flow-drop flow-drag-enter="class='alert-success'" flow-drag-leave="class=''"
          ng-class="class">... or drag and drop your files here
  </div>

  <hr class="soften">

  <h2>Transfers</h2>
  <p>
    <a class="btn btn-small btn-success" ng-click="$flow.resume()">Upload</a>
    <a class="btn btn-small btn-danger" ng-click="$flow.pause()">Pause</a>
    <a class="btn btn-small btn-info" ng-click="$flow.cancel()">Cancel</a>
    <span class="label label-info">Size: {{$flow.getSize() | bytes}}</span>
    <span class="label label-info" ng-if="$flow.isUploading()">Uploading...</span>
  </p>
  <table class="table table-hover table-bordered table-striped" flow-transfers>
    <thead>
    <tr>
      <th style="width:5%">#</th>
      <th>Name</th>
      <th style="width:10%">Size</th>
      <th style="width:20%">Progress</th>
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
            Pause
          </a>
          <a class="btn btn-mini btn-warning" ng-click="file.resume()" ng-show="file.paused">
            Resume
          </a>
          <a class="btn btn-mini btn-danger" ng-click="file.cancel()">
            Cancel
          </a>
          <a class="btn btn-mini btn-info" ng-click="file.retry()" ng-show="file.error">
            Retry
          </a>
        </div>
	<span ng-if="file.isComplete() && !file.error()">Completed</span>
      </td>
    </tr>
    </tbody>
  </table>
  <p><a href="../files?dir=%2Fflowupload">The files will be saved in your home directory.</a></p>
 </div>