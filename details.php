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
        </table>
    </div>
</body>
</html>