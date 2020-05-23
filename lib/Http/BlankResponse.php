<?php
namespace OCA\flowupload\Http;

use OCP\AppFramework\Http\Response;

class BlankResponse extends Response {

    public function __construct($statusCode) {
        $this->setStatus($statusCode);
    }

    public function render() {
        return "";
    }

}