<?php
 namespace OCA\flowupload\Controller;

 use OCP\IRequest;
 use OCP\AppFramework\Http\TemplateResponse;
 use OCP\AppFramework\Http\ContentSecurityPolicy;
 use OCP\AppFramework\Controller;

 class PageController extends Controller {

     public function __construct(string $AppName, IRequest $request){
         parent::__construct($AppName, $request);
     }

     /**
      * @NoAdminRequired
      * @NoCSRFRequired
      */
     public function index() {
        \OCP\Util::addScript('flowupload', 'flowupload-main');
		\OCP\Util::addStyle('flowupload', 'style');

        $response = new TemplateResponse('flowupload', 'main');
         
        $csp = new ContentSecurityPolicy();
        $csp->allowEvalScript(true);
        $response->setContentSecurityPolicy($csp);
        
        return $response;
     }
 }
