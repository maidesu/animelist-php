<?php
    include('filestorage.php');

    // functions
    function redirect($page) {
      header("Location: ${page}");
      exit();
    }

    function validate($post, &$data, &$errors) {
      if (!isset($post["username"]) || $post["username"] == "") $errors['username'] = "Nincs megadva felhasználónév!";

      if (!isset($post["email"]) || $post["email"] == "") $errors['email'] = "Nincs megadva email cím!";
      else if (!filter_var($post["email"], FILTER_VALIDATE_EMAIL)) $errors['email'] = "Az email cím érvénytelen!";

      if (!isset($post["password"]) || $post["password"] == "") $errors['password'] = "Nincs megadva jelszó!";
      else if (!isset($post["password_rp"]) || $post["password_rp"] == "") $errors['password_rp'] = "Nincs megadva még egyszer jelszó!";
      else if ($post["password"] !== $post["password_rp"]) $errors['password_rp'] = $errors['password'] = "A jelszavak nem egyeznek!";

      $data = $post;
      return count($errors) === 0;
    }

    function user_exists($user_storage, $username) {
      $users = $user_storage->findOne(['username' => $username]);
      return !is_null($users);
    }

    function add_user($user_storage, $data) {
      $user = [
          'username' => $data['username'],
          'email' => $data['email'],
          'password' => password_hash($data['password'], PASSWORD_DEFAULT),
          'watched' => new ArrayObject(),
          'isAdmin' => 0
      ];
      return $user_storage->add($user);
    }

    // main
    $user_storage = new UserStorage();
    $errors = [];
    $data = [];
    if (count($_POST) > 0) {
      if (validate($_POST, $data, $errors)) {
          if (user_exists($user_storage, $data['username'])) {
            $errors['global'] = "User already exists";
          } else {
            add_user($user_storage, $data);
            redirect('login.php');
          }
      }
    }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>(Not) my anime list</title>
</head>

<body>
<div class="header">
  <div id="header-back"><a href="./index.php">Vissza a főoldalra</a></div>
</div>
<?php if (isset($errors['global'])) : ?>
  <p><span class="error"><?= $errors['global'] ?></span></p>
<?php endif; ?>
<form action="" method="post" novalidate>
  <div>
    <label for="username">Felhasználónév: </label><br>
    <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
    <?php if (isset($errors['username'])) : ?>
      <span class="error"><?= $errors['username'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <label for="email">Email cím: </label><br>
    <input type="email" name="email" id="email" value="<?= $_POST['email'] ?? "" ?>">
    <?php if (isset($errors['email'])) : ?>
      <span class="error"><?= $errors['email'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <label for="password">Jelszó: </label><br>
    <input type="password" name="password" id="password">
    <?php if (isset($errors['password'])) : ?>
      <span class="error"><?= $errors['password'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <label for="password_rp">Jelszó még egyszer: </label><br>
    <input type="password" name="password_rp" id="password_rp">
    <?php if (isset($errors['password_rp'])) : ?>
      <span class="error"><?= $errors['password_rp'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <button type="submit">Regisztráció</button>
  </div>
</form>
</body>
</html>
