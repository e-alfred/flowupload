<?php

$this->create('flowupload_index', '/')
	->actionInclude('flowupload/index.php');

$this->create('flowupload_ajax_upload', 'ajax/upload.php')
	->actionInclude('flowupload/ajax/upload.php');

$this->create('flowupload_ajax_locations', 'ajax/locations.php')
	->actionInclude('flowupload/ajax/locations.php');
