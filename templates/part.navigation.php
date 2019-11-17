<li id="location-<?= $location['id']; ?>" class="collapsible">
  <button class="collapse"></button>

  <a href="#" class="icon-folder"><?= $location['location']; ?></a>
  <ul>
    <li><a class="icon-pause" href="#"><?= $l->t('Waiting'); ?> (<span class="upload-in-pause"><?= $location['nbrInPause']; ?></span>)</a></li>
    <li><a class="icon-upload" href="#"><?= $l->t('Uploading'); ?> (<span class="upload-uploading"><?= $location['nbrUploading']; ?></span>)</a></li>
    <li><a class="icon-checkmark" href="#"><?= $l->t('Completed'); ?> (<span class="upload-completed"><?= $location['nbrCompleted']; ?></span>)</a></li>
    <li><a class="icon-close" href="#"><?= $l->t('Aborted'); ?> (<span class="upload-aborted"><?= $location['nbrAborted']; ?></span>)</a></li>
  </ul>
</li>
