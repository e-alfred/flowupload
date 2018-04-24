<?php

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('flowupload');

\OCP\Util::addScript('flowupload', 'angular');
\OCP\Util::addScript('flowupload', 'ng-flow-standalone');
\OCP\Util::addScript('flowupload', 'app');
\OCP\Util::addScript('flowupload', 'script');
\OCP\Util::addStyle('flowupload', 'bootstrap-combined');

$locations = array(
  array(
    'id' => 0,
    'location' => '/flowupload/',
    'nbrInPause' => 0,
    'nbrUploading' => 0,
    'nbrCompleted' => 0,
    'nbrAborted' => 0,
  )
);

$tpl = new OCP\Template("flowupload", "main", "user");
$tpl->printPage();
