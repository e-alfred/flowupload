<?php
namespace OCA\flowupload\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Directory extends Entity implements JsonSerializable {

    protected $title;
    protected $content;
    protected $userId;

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content
        ];
    }
}