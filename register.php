<?php
    include('userstorage.php');

    // functions
    function validate($post, &$data, &$errors) {
    // username, password, fullname are not empty
    // ...
    $data = $post;
    return count($errors) === 0;
    }
    function user_exists($user_storage, $username) {
    $users = $user_storage->findOne(['username' => $username]);
    return !is_null($users);
    }
    function add_user($user_storage, $data) {
    $user = [
        'username'  => $data['username'],
        'password'  => password_hash($data['password'], PASSWORD_DEFAULT),
        'fullname'  => $data['fullname'],
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

<?php if (isset($errors['global'])) : ?>
  <p><span class="error"><?= $errors['global'] ?></span></p>
<?php endif; ?>
<form action="" method="post" novalidate>
  <div>
    <label for="username">Username: </label><br>
    <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
    <?php if (isset($errors['username'])) : ?>
      <span class="error"><?= $errors['username'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <label for="password">Password: </label><br>
    <input type="password" name="password" id="password">
    <?php if (isset($errors['password'])) : ?>
      <span class="error"><?= $errors['password'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <label for="fullname">Full name: </label><br>
    <input type="text" name="fullname" id="fullname" value="<?= $_POST['fullname'] ?? "" ?>">
    <?php if (isset($errors['fullname'])) : ?>
      <span class="error"><?= $errors['fullname'] ?></span>
    <?php endif; ?>
  </div>
  <div>
    <button type="submit">Register</button>
  </div>
</form>