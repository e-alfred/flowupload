<?php
// Restrict access // ToDo: Enabled for current user?
if (!\OC::$server->getUserSession()->isLoggedIn()) {
  http_response_code(403);
}

// Load upload classes
require_once(__DIR__ . '/Flow/Autoloader.php');
Flow\Autoloader::register();

// Directory definitions
$userhome = OC_User::getHome(OC_User::getUser());
$temp = $userhome.'/.flowupload_tmp/';
$result = '/flowupload/';

// Initialize uploader
$config = new \Flow\Config();
$config->setTempDir($temp);
$request = new \Flow\Request();

// Filter paths
$path = preg_replace('/(\.\.\/|~|\/\/)/i', '', $request->getRelativePath());
$path = preg_replace('/[^a-z0-9äöüßáàâãéèêíìîóòõôúùûºªç&$%*#@ \(\)\.\-_\/]/i', '', $path);
$path = trim($path, '/');

// Skip existing files // ToDo: Check if file size changed?
if (\OC\Files\Filesystem::file_exists($result . $path)) {
	//http_response_code(200);  	//Comment if same name file upload.
	//die();			//Comment if same name file upload.
	
	// Create files version
	$source = $result . $path;
	$list_uid_filename = OCA\Files_Versions\Storage::getUidAndFilename($source);
	$store = OCA\Files_Versions\Storage::store($list_uid_filename[1]);
	
	$file_exist = true;
}

// Process upload
if (\OC\Files\Filesystem::isValidPath($path)) {

	// Create temporary upload folder
	if(!file_exists($temp)) {
		mkdir($temp);
	}

	// Create destination directory
	$dir = dirname($result . $path);
	if(!\OC\Files\Filesystem::file_exists($dir)) {
		\OC\Files\Filesystem::mkdir($dir);
	}

	// Store file
	if (\Flow\Basic::save($userhome . "/files/" . $result . $path, $config, $request)) {

                // no real copy, file comes from somewhere else, e.g. version rollback -> just update the file cache and the webdav properties without all the other post_write actions
		// \OC\Files\Cache\Cache::checkUpdate($result . $path);
		// \OC\Files\Filesystem::removeETagHook(array("path" => $result . $path));

		/*
		Create thumb and Preview for uploaded file
		*/
		if($file_exist) {
			OC_Hook::emit(
				\OC\Files\Filesystem::CLASSNAME,
				\OC\Files\Filesystem::signal_post_write,
				array( \OC\Files\Filesystem::signal_param_path => $result . $path)
			);
		}

		\OC\Files\Filesystem::touch($result . $path);

	} else {
		// This is not a final chunk or request is invalid, continue to upload.
	}

	// Remove old chunks
	\Flow\Uploader::pruneChunks($temp);
}

// ToDo: error handling
//OCP\JSON::error(array("data" => array("message" => $msg)));
//OCP\Util::writeLog('flowupload', "Failed to create file: " . $path, OC_Log::ERROR);
?>
