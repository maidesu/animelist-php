<?php
    include('filestorage.php');

    session_start();
    $series_storage = new SeriesStorage();
    $user_storage = new UserStorage();

    if (!isset($_GET["id"]) || $_GET["id"] === "" || is_null($series_storage->findById($_GET["id"]))) { http_response_code(404); return; }
    $series = $series_storage->findById($_GET["id"]);

    if (isset($_GET["viewed"]) && is_numeric($_GET["viewed"])) {
        $_SESSION["user"]["watched"][$_GET["id"]] = $_GET["viewed"];
        $user_storage->update($_SESSION["user"]["id"], $_SESSION["user"]);
    }


    function redirect($page) {
        header("Location: ${page}");
        exit();
    }

    function validate($post, &$data, &$errors) {
        if (!isset($post["id"]) || $post["id"] == "") $errors['id'] = "Nincs megadva ID!";

        if (!isset($post["title"]) || $post["title"] == "") $errors['title'] = "Nincs megadva cím!";

        if (!isset($post["date"]) || $post["date"] == "") $errors['date'] = "Nincs megadva dátum!";

        if (!isset($post["plot"]) || $post["plot"] == "") $errors['plot'] = "Nincs megadva leírás!";

        if (!isset($post["rating"]) || $post["rating"] == "") $errors['rating'] = "Nincs megadva értékelés!";
        else if (!is_float($post["rating"] + 0) || $post["rating"] < 0 || $post["rating"] > 10) $errors['rating'] = "Az értékelés 0 és 10 közti lebegőpontos szám legyen!";

        $data = $post;
        return count($errors) === 0;
    }

    function episode_exists($series_storage, $title) {
        $series = $series_storage->findById($_GET["id"]);
        return in_array($title, $series["episodes"]);
    }

    function add_episode($series_storage, $data) {
        $series = $series_storage->findById($_GET["id"]);
        $episode = [
            "id" => $data['id'],
            "date" => $data['date'],
            "title" => $data['title'],
            "plot" => $data['plot'],
            "rating" => $data['rating']
        ];
        $series["episodes"][$episode["id"]] = $episode;

        return $series_storage->update($_GET["id"], $series);
    }

    $errors = [];
    $data = [];
    if (count($_POST) > 0) {
      if (validate($_POST, $data, $errors)) {
          if (episode_exists($series_storage, $data['title'])) {
            $errors['global'] = "Episode already exists";
          } else {
            add_episode($series_storage, $data);
            header("Refresh:0");
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
    <?php if (isset($_SESSION["user"])): ?>
        <div id="header-user">Bejelentkezett, mint <?= $_SESSION["user"]["username"] ?></div>
    <?php endif; ?>
        <div id="header-back"><a href="./index.php">Vissza a főoldalra</a></div>
    </div>
    <div>
        <table>
            <tr>
                <td rowspan="4"><img src="<?= $series["cover"] ?>" alt="<?= $series["title"] ?>"></td>
                <th>Cím</th>
                <td><?= $series["title"] ?></td>
            </tr>
            <tr>
                <th>Évjárat</th>
                <td><?= $series["year"] ?></td>
            </tr>
            <tr>
                <th>Leírás</th>
                <td><?= $series["plot"] ?></td>
            </tr>
            <tr>
                <th>Epizódok száma</th>
                <td><?= count($series["episodes"]) ?></td>
            </tr>
        </table>
    </div>

    <div>
        <table>
            <tr>
                <th>#</th>
                <th>Epizód címe</th>
                <th>Dátum</th>
                <th>Leírás</th>
                <th>Értékelés</th>
                <?php if (isset($_SESSION["user"])) :?><th>Megnéztem</th><?php endif; ?>
            </tr>
            <?php foreach ($series["episodes"] as $i=>$details): ?>
                <tr>
                    <td <?php if(isset($_SESSION["user"]) &&
                        isset($_SESSION["user"]["watched"][$_GET["id"]]) &&
                        $i <= $_SESSION["user"]["watched"][$_GET["id"]]) echo 'class="viewed"'; ?>><?= $i ?></td>
                    <td><?= $details["title"] ?></td>
                    <td><?= $details["date"] ?></td>
                    <td><?= $details["plot"] ?></td>
                    <td><?= $details["rating"] ?></td>
                    <?php if (isset($_SESSION["user"]) &&
                        isset($_SESSION["user"]["watched"][$_GET["id"]]) &&
                        $i === $_SESSION["user"]["watched"][$_GET["id"]] + 1 ||
                        ($i === 1 && !isset($_SESSION["user"]["watched"][$_GET["id"]]))) :?><td><a href="./details.php?id=<?= $series["id"] ?>&viewed=<?= $i ?>">+</a></td><?php endif; ?>
                </tr>
            <?php endforeach; ?>
            <tr>
                <form action="" method="post" novalidate>
                    <td>
                        <input type="text" name="id" id="id" value="<?= $_POST['id'] ?? "" ?>">
                        <?php if (isset($errors['id'])) : ?>
                            <span class="error"><?= $errors['id'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <input type="text" name="title" id="title" value="<?= $_POST['title'] ?? "" ?>">
                        <?php if (isset($errors['title'])) : ?>
                            <span class="error"><?= $errors['title'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <input type="text" name="date" id="date" value="<?= $_POST['date'] ?? "" ?>">
                        <?php if (isset($errors['date'])) : ?>
                            <span class="error"><?= $errors['date'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <textarea name="plot" id="plot"><?= $_POST['plot'] ?? "" ?></textarea>
                        <?php if (isset($errors['plot'])) : ?>
                            <span class="plot"><?= $errors['plot'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <input type="text" name="rating" id="rating" value="<?= $_POST['rating'] ?? "" ?>">
                        <?php if (isset($errors['rating'])) : ?>
                            <span class="error"><?= $errors['rating'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="submit">Hozzáadás</button>
                    </td>
                </form>
                <?php if (isset($errors['global'])) : ?>
                    <p><span class="error"><?= $errors['global'] ?></span></p>
                <?php endif; ?>
            </tr>
        </table>
    </div>
</body>
</html>
