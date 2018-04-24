<?php
  // Restrict access // ToDo: Enabled for current user?
  if (!\OCP\User::isLoggedIn() || !\OCP\App::isEnabled('flowupload')) {
  	\OC_Response::setStatus(403);
  }

  function getAllLocations() {
    // ToDo: Return locations from database
    return array(
      array(
        'id' => 0,
        'location' => '/flowupload/',
        'pause' => 0,
        'uploading' => 0,
        'completed' => 0,
        'aborted' => 0
      )
    );
  }

  function addNewLocation($location) {
    // ToDo: Add to database
  	$location = preg_replace('/(\.\.\/|~|\/\/)/i', '', '/'.$location.'/');
    $location = preg_replace('/[^a-z0-9äöüß \(\)\.\-_\/]/i', '', $location);
  	$location = trim($location);

    return $location;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST = json_decode(file_get_contents('php://input'), true);

    if (isset($_POST['location']) && \OC\Files\Filesystem::isValidPath($_POST['location'])) {
      $locations = getAllLocations();

      foreach ($locations as $location) {
        if ($location['location'] === $_POST['location']) {
          \OC_Response::setStatus(409);
          die();
        }
      }

      \OC_Response::setStatus(201);

      $location = addNewLocation($_POST['location']);

      echo json_encode(array(
          'id' => preg_replace('#/#', '', $location),
          'location' => $location,
          'pause' => 0,
          'uploading' => 0,
          'completed' => 0,
          'aborted' => 0
        ));
    }
    else {
      \OC_Response::setStatus(400);
    }

    die();
  }

  echo json_encode(array(
    "locations" => getAllLocations()
  ));
?>
