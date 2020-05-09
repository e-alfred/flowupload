<?php
 namespace OCA\flowupload\Controller;
 
 use OCP\AppFramework\Http;
 use OCA\flowupload\Http\BlankResponse;

 use OCP\IRequest;
 use OCP\AppFramework\Controller;
 use OCA\flowupload\Service\Flow\Config;
 use OCA\flowupload\Service\Flow\Request;
 use OCA\flowupload\Service\Flow\File;

 class UploadController extends Controller {

     public function __construct(string $AppName, IRequest $request){
         parent::__construct($AppName, $request);
     }
     
    private function calculatePaths() {
        $this->userhome = \OC_User::getHome(\OC::$server->getUserSession()->getUser()->getUID());
        $this->temp = $this->userhome.'/.flowupload_tmp/';
        
        $uploadTarget = $_REQUEST['target'] ?? '/flowupload/';
        
        // Initialize uploader
        $this->config = new Config();
        $this->config->setTempDir($this->temp);
        $this->config->setDeleteChunksOnSave(TRUE);
        $this->request = new Request();
        
        $fileRelativePath = $this->request->getRelativePath();
        
        // Filter paths
        $fileRelativePath = preg_replace('/(\.\.\/|~|\/\/)/i', '', $fileRelativePath);
        $fileRelativePath = html_entity_decode(htmlentities($fileRelativePath, ENT_QUOTES, 'UTF-8'));
        $fileRelativePath = trim($fileRelativePath, '/');
        
        $this->path = $uploadTarget . $fileRelativePath;
     }
     
     /**
      * @NoAdminRequired
      * @NoCSRFRequired
      */
     public function checkChunk() {
        $this->calculatePaths();
        
        $file = new File($this->config, $this->request);
        
        // Skip existing files
        /*if (\OC\Files\Filesystem::file_exists($this->path)) {
            return new BlankResponse(204);
        }*/
         
        if ($file->checkChunk()) {
            return new BlankResponse(200);
        } else {
            // The 204 response MUST NOT include a message-body, and thus is always terminated by the first empty line after the header fields.
            return new BlankResponse(204);
        }
    }
     
     /**
      * @NoAdminRequired
      * @NoCSRFRequired
      */
     public function upload() {
         
         $this->calculatePaths();
        
        // check if path is valid
        if (!\OC\Files\Filesystem::isValidPath($this->path)) {
            \OCP\Util::writeLog('flowupload', "Upload to a invalid Path failed", \OCP\ILogger::ERROR);
            print("invalid path");
            return new BlankResponse(400);
        }
        
        // Create temp folder
        if(!file_exists($this->temp)) {
        	mkdir($this->temp);
        }
        
        // Create destination directory
        if(!\OC\Files\Filesystem::file_exists(dirname($this->path))) {
        	\OC\Files\Filesystem::mkdir(dirname($this->path));
        }
        
        $file = new File($this->config, $this->request);
        
        // save chunk
        if ($file->validateChunk()) {
            $file->saveChunk();
        } else {
            // error, invalid chunk upload request, retry
            return new BlankResponse(400);
        }
        
        //check if last chunk
        if ($file->validateFile()) {
            //assemble and move to destination
            if($file->save($this->path)) {
                $this->updateFileCache($this->path);
                return new BlankResponse(200);
            }else {
                return new BlankResponse(400);
            }
        }
        
        return new BlankResponse(200);
    }
    
    private function updateFileCache($path) {
        \OC\Files\Filesystem::touch($path);

	    \OC_Hook::emit(
		    \OC\Files\Filesystem::CLASSNAME,
		    \OC\Files\Filesystem::signal_post_create,
		    array( \OC\Files\Filesystem::signal_param_path => $path)
	    );
    }
 }