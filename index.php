<?php

\OCP\User::checkLoggedIn();

\OCP\Util::addScript('flowupload', 'angular');
\OCP\Util::addScript('flowupload', 'flow.min');
\OCP\Util::addScript('flowupload', 'app');
\OCP\Util::addStyle('flowupload', 'style');

$tpl = new OCP\Template("flowupload", "main", "user");
$tpl->printPage();
