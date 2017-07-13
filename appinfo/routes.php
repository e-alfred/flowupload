<?php

$this->create('flowupload_index', '/')
	->actionInclude('flowupload/index.php');

$this->create('flowupload_ajax_upload', 'ajax/upload.php')
	->actionInclude('flowupload/ajax/upload.php');