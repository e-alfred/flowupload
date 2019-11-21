<?php
// Restrict access
if (!\OC::$server->getUserSession()->isLoggedIn()) {
  http_response_code(403);
}

// Load upload classes
require_once(__DIR__ . '/Flow/Autoloader.php');
Flow\Autoloader::register();

// Directory definitions
$userhome = OC_User::getHome(\OC::$server->getUserSession()->getUser()->getUID());
$temp = $userhome.'/.flowupload_tmp/';
$uploadTarget = $_REQUEST['target'] ?? '/flowupload/';
$uploadTarget = '/'.$uploadTarget.'/';

// Initialize uploader
$config = new \Flow\Config();
$config->setTempDir($temp);
$request = new \Flow\Request();

$fileRelativePath = $request->getRelativePath();

// Filter paths
$fileRelativePath = preg_replace('/(\.\.\/|~|\/\/)/i', '', $fileRelativePath);
$fileRelativePath = html_entity_decode(htmlentities($fileRelativePath, ENT_QUOTES, 'UTF-8'));
$fileRelativePath = trim($fileRelativePath, '/');

// Skip existing files // ToDo: Check if file size changed?
if (\OC\Files\Filesystem::file_exists($uploadTarget . $fileRelativePath)) {
	http_response_code(200);
	die();
}

// Process upload
if (\OC\Files\Filesystem::isValidPath($fileRelativePath)) {

	// Create temporary upload folder
	if(!file_exists($temp)) {
		mkdir($temp);
	}

	// Create destination directory
	$dir = dirname($uploadTarget . $fileRelativePath);
	if(!\OC\Files\Filesystem::file_exists($dir)) {
		\OC\Files\Filesystem::mkdir($dir);
	}

	// Store file
	if (\Flow\Basic::save($userhome . "/files/" . $uploadTarget . $fileRelativePath, $config, $request)) {

                // no real copy, file comes from somewhere else, e.g. version rollback -> just update the file cache and the webdav properties without all the other post_write actions
//                \OC\Files\Cache\Cache::checkUpdate($uploadTarget . $fileRelativePath);
//                \OC\Files\Filesystem::removeETagHook(array("path" => $uploadTarget . $fileRelativePath));


		OC_Hook::emit(
			\OC\Files\Filesystem::CLASSNAME,
			\OC\Files\Filesystem::signal_post_write,
			array( \OC\Files\Filesystem::signal_param_path => $uploadTarget . $fileRelativePath)
		);

		\OC\Files\Filesystem::touch($uploadTarget . $fileRelativePath);

	} else {
		// This is not a final chunk or request is invalid, continue to upload.
	}

	// Remove old chunks
	\Flow\Uploader::pruneChunks($temp);
}

// ToDo: error handling
//OCP\JSON::error(array("data" => array("message" => $msg)));
//OCP\Util::writeLog('flowupload', "Failed to create file: " . $fileRelativePath, OC_Log::ERROR);
?>
