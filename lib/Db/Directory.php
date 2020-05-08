<?php
namespace OCA\flowupload\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Directory extends Entity implements JsonSerializable {

    protected $directory;
    protected $userId;

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'directory' => $this->directory
        ];
    }
}