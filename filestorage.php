<?php
include('storage.php');

class SeriesStorage extends Storage {
  public function __construct() {
    parent::__construct(new JsonIO('series.json'));
  }
}

class UserStorage extends Storage {
  public function __construct() {
    parent::__construct(new JsonIO('user.json'));
  }
}
?>
