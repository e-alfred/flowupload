<?php
 namespace OCA\flowupload\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Http\TemplateResponse;
 use OCP\AppFramework\Controller;
 use OCA\flowupload\Service\Flow\Config;
 use OCA\flowupload\Service\Flow\Request;
 use OCA\flowupload\Service\Flow\Basic;


 class UploadController extends Controller {

     public function __construct(string $AppName, IRequest $request){
         parent::__construct($AppName, $request);
     }
     
     /**
      * @NoAdminRequired
      * @NoCSRFRequired
      */
     public function upload() {
         $config = new Config();
        /*
        // check if logged in
        if (!\OC::$server->getUserSession()->isLoggedIn()) {
            \OCP\Util::writeLog('flowupload', "Flowupload rejected request, because client is not logged in", \OCP\ILogger::ERROR);
            http_response_code(403);
            die();
        }
        
        // Load upload classes
        //require_once(__DIR__ . '/Flow/Autoloader.php');
        //Flow\Autoloader::register();
        
        // Directory definitions
        $userhome = \OC_User::getHome(\OC::$server->getUserSession()->getUser()->getUID());
        $temp = $userhome.'/.flowupload_tmp/';
        
        $uploadTarget = $_REQUEST['target'] ?? '/flowupload/';
        
        // Initialize uploader
        $config = new Config();
        $config->setTempDir($temp);
        $config->setDeleteChunksOnSave(TRUE);
        $request = new Request();
        
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
        if(!\OC\Files\Filesystem::file_exists(dirname($path))) {
        	\OC\Files\Filesystem::mkdir(dirname($path));
        }
        
        // Store file
        if (Basic::save($path, $config, $request)) {
            \OC_Hook::emit(
        	    \OC\Files\Filesystem::CLASSNAME,
        	    \OC\Files\Filesystem::signal_post_update,
        	    array(\OC\Files\Filesystem::signal_param_path => $path)
            );
        
            \OC\Files\Filesystem::touch(dirname($path) . "/.flowupload_force_cache_update");
            \OC\Files\Filesystem::unlink(dirname($path) . "/.flowupload_force_cache_update");
        } else {
        	// This is not a final chunk or request is invalid, continue to upload.
        }
        
        // ToDo: error handling
        //OCP\JSON::error(array("data" => array("message" => $msg)));
        */
     }
 }