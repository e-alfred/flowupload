<?php

\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('flowupload');

\OCP\Util::addScript('flowupload', 'angular');
\OCP\Util::addScript('flowupload', 'ng-flow-standalone');
\OCP\Util::addScript('flowupload', 'app');
\OCP\Util::addStyle('flowupload', 'bootstrap-combined');

$tpl = new OCP\Template("flowupload", "main", "user");
$tpl->printPage();
