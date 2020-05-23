<?php

namespace OCA\flowupload\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\flowupload\Service\DirectoryNotFound;


trait Errors {

    protected function handleNotFound (Closure $callback): DataResponse {
        try {
            return new DataResponse($callback());
        } catch(DirectoryNotFound $e) {
            $message = ['message' => $e->getMessage()];
            return new DataResponse($message, Http::STATUS_NOT_FOUND);
        }
    }

}