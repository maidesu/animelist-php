<?php
    include('filestorage.php');

    session_start();
    $series_storage = new SeriesStorage();
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
        <div id="header-logout"><a href="./logout.php">Kijelentkezés</a></div>
    <?php else : ?>
        <div id="header-login"><a href="./login.php">Bejelentkezés</a></div>
        <div id="header-register"><a href="./register.php">Regisztráció</a></div>
    <?php endif; ?>
    </div>

    <div class="mainpage">
        <h1>(Nem) Az Én Anime Listám</h1>
        <p>Ez nem egy az 'Az Én Anime Listám' mintájára készült anime sorozatkövető weboldal.</p>

        <table>
            <tr>
                <th>Borító</th>
                <th>Cím</th>
                <th>Epizódok száma</th>
                <th>Legutóbbi epizód dátuma</th>
            </tr>
            <?php foreach ($series_storage->findAll() as $listing): ?>
                <tr>
                    <td><img src="<?= $listing["cover"] ?>" alt="<?= $listing["title"] ?>"></td>
                    <td><a href="./details.php?id=<?= $listing["id"] ?>"><?= $listing["title"] ?></a></td>
                    <td><?= count($listing["episodes"]) ?></td>
                    <td><?= $listing["episodes"][count($listing["episodes"])]["date"] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php if (isset($_SESSION["user"])) :?>
            <h2>Nézett sorozataim</h2>

            <table>
                <tr>
                    <th>Cím</th>
                    <th>Megnézett epizódok</th>
                </tr>
                <?php foreach ($series_storage->findMany(
                        function($item) { return isset($_SESSION["user"]["watched"][$item["id"]]) &&
                            ($_SESSION["user"]["watched"][$item["id"]] > 0); }
                    ) as $watching): ?>
                    <tr>
                        <td><?= $watching["title"] ?></td>
                        <td><?= $_SESSION["user"]["watched"][$watching["id"]] ?>/<?= count($watching["episodes"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <!-- PHP CSAK ADMIN FORM HOZZÁADÁSHOZ -->
    <form action="" novalidate>
    </form>
</body>
</html>
