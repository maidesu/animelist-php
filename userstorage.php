<?php
include('storage.php');

class UserStorage extends Storage {
  public function __construct() {
    parent::__construct(new JsonIO('user.json'));
  }
}
?>
