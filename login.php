<?php
    include('filestorage.php');

    // functions
    function redirect($page) {
      header("Location: ${page}");
      exit();
    }

    function validate($post, &$data, &$errors) {
      if (!isset($post["username"]) || $post["username"] == "") $errors['username'] = "Nincs megadva felhasználónév!";
      if (!isset($post["password"]) || $post["password"] == "") $errors['password'] = "Nincs megadva jelszó!";

      $data = $post;

      return count($errors) === 0;
    }

    function check_user($user_storage, $username, $password) {
      $users = $user_storage->findMany(function ($user) use ($username, $password) {
          return $user["username"] === $username && 
              password_verify($password, $user["password"]);
              //$user["password"] === $password; // Don't use plaintext
      });
      return count($users) === 1 ? array_shift($users) : NULL;
    }

    function login($user) {
      $_SESSION["user"] = $user;
    }

    // main
    session_start();
    $user_storage = new UserStorage();
    $data = [];
    $errors = [];
    if ($_POST) {
      if (validate($_POST, $data, $errors)) {
          $logged_in_user = check_user($user_storage, $data['username'], $data['password']);
          if (!$logged_in_user) {
            $errors['global'] = "Hibás felhasználónév vagy jelszó!";
          } else {
            login($logged_in_user);
            redirect('index.php');
          }
      }
    }
?>

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
    <label for="password">Jelszó: </label><br>
    <input type="password" name="password" id="password">
    <?php if (isset($errors['password'])) : ?>
      <span class="error"><?= $errors['password'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <button type="submit">Bejelentkezés</button>
  </div>
</form>