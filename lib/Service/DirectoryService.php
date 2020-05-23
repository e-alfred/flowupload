<?php
namespace OCA\flowupload\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\flowupload\Db\Directory;
use OCA\flowupload\Db\DirectoryMapper;


class DirectoryService {

    /** @var DirectoryMapper */
    private $mapper;

    public function __construct(DirectoryMapper $mapper){
        $this->mapper = $mapper;
    }

    public function findAll(string $userId): array {
        return $this->mapper->findAll($userId);
    }

    private function handleException (Exception $e): void {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new DirectoryNotFound($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function find($id, $userId) {
        try {
            return $this->mapper->find($id, $userId);

        // in order to be able to plug in different storage backends like files
        // for instance it is a good idea to turn storage related exceptions
        // into service related exceptions so controllers and service users
        // have to deal with only one type of exception
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function create($userId, $path) {
        $directory = new Directory();
        $directory->setDirectory($path);
        $directory->setUserId($userId);
        return $this->mapper->insert($directory);
    }

    public function update($id) {
    }

    public function delete($id, $userId) {
        try {
            $directory = $this->mapper->find($id, $userId);
            $this->mapper->delete($directory);
            return $directory;
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

}