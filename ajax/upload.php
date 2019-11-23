<?php
// check if logged in
if (!\OC::$server->getUserSession()->isLoggedIn()) {
    \OCP\Util::writeLog('flowupload', "Flowupload rejected request, because client is not logged in", \OCP\ILogger::ERROR);
    http_response_code(403);
    die();
}

// Load upload classes
require_once(__DIR__ . '/Flow/Autoloader.php');
Flow\Autoloader::register();

// Directory definitions
$userhome = OC_User::getHome(\OC::$server->getUserSession()->getUser()->getUID());
$temp = $userhome.'/.flowupload_tmp/';
$uploadTarget = $_REQUEST['target'] ?? '/flowupload/';
//$uploadTarget = '/'.$uploadTarget.'/';

// Initialize uploader
$config = new \Flow\Config();
$config->setTempDir($temp);
$request = new \Flow\Request();

$fileRelativePath = $request->getRelativePath();

// Filter paths
$fileRelativePath = preg_replace('/(\.\.\/|~|\/\/)/i', '', $fileRelativePath);
$fileRelativePath = html_entity_decode(htmlentities($fileRelativePath, ENT_QUOTES, 'UTF-8'));
$fileRelativePath = trim($fileRelativePath, '/');

$path = $uploadTarget . $fileRelativePath;

// Skip existing files // ToDo: Check if file size changed?
if (\OC\Files\Filesystem::file_exists($path)) {
    echo("file already exists \n");
	http_response_code(200);
	die();
}

// check if path is valid
if (!\OC\Files\Filesystem::isValidPath($path)) {
    \OCP\Util::writeLog('flowupload', "Upload to a invalid Path failed", \OCP\ILogger::ERROR);
    http_response_code(403);
    die();
}

// Create temporary upload folder
if(!file_exists($temp)) {
	mkdir($temp);
}

// Create destination directory
$dir = dirname($path);
if(!\OC\Files\Filesystem::file_exists($dir)) {
	\OC\Files\Filesystem::mkdir($dir);
}

// Store file
if (\Flow\Basic::save($userhome . "/files/" . $path, $config, $request)) {
	OC_Hook::emit(
		\OC\Files\Filesystem::CLASSNAME,
		\OC\Files\Filesystem::signal_post_write,
		array( \OC\Files\Filesystem::signal_param_path => $path)
	);
	\OC\Files\Filesystem::touch($path);
} else {
	// This is not a final chunk or request is invalid, continue to upload.
}

// Remove old chunks
\Flow\Uploader::pruneChunks($temp);
	
// ToDo: error handling
//OCP\JSON::error(array("data" => array("message" => $msg)));
?>
