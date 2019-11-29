<?php

$this->create('flowupload_index', '/')
	->actionInclude('flowupload/index.php');

$this->create('flowupload_ajax_upload', 'ajax/upload.php')
	->actionInclude('flowupload/ajax/upload.php');

$this->create('flowupload_ajax_getStarredLocations', 'ajax/getStarredLocations.php')
	->actionInclude('flowupload/ajax/getStarredLocations.php');

$this->create('flowupload_ajax_starLocations', 'ajax/starLocation.php')
	->actionInclude('flowupload/ajax/starLocation.php');

$this->create('flowupload_ajax_unstarLocations', 'ajax/unstarLocation.php')
	->actionInclude('flowupload/ajax/unstarLocation.php');
