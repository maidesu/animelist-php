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

        <?php if (isset($_SESSION["user"]["isAdmin"]) && $_SESSION["user"]["isAdmin"] === 1) :?>
            <h2>Sorozat hozzáadása</h2>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Cím</th>
                    <th>Évjárat</th>
                    <th>Leírás</th>
                    <th>Borító</th>
                </tr>
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
                            <input type="text" name="cover" id="cover" value="<?= $_POST['cover'] ?? "" ?>">
                            <?php if (isset($errors['cover'])) : ?>
                                <span class="error"><?= $errors['cover'] ?></span>
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
        <?php endif; ?>

        <h2>Összes sorozat</h2>

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
</body>
</html>
