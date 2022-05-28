<?php
    include('seriesstorage.php');

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
    <div>
        <h1>(Nem) Az Én Anime Listám</h1>
        <p>Ez nem egy az 'Az Én Anime Listám' mintájára készült anime sorozatkövető weboldal.</p>

        <table>
            <tr>
                <th>Borító</th>
                <th>Cím</th>
                <th>Epizódok száma</th>
                <th>Legutóbbi epizód dátuma</th>
            </tr>
            <?php foreach ($series_storage->findAll()["series"] as $listing): ?>
                <tr>
                    <td><?= $listing["cover"] ?></td>
                    <td><a href="./details.php?id=<?= $listing["id"] ?>"><?= $listing["title"] ?></a></td>
                    <td><?= count($listing["episodes"]) ?></td>
                    <td><?= $listing["episodes"][count($listing["episodes"])]["date"] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- PHP BEJELENTKEZETT FELHASZNÁLÓ LEGALÁBB EGY MEGTEKINTETT LISTA -->

    <!-- PHP CSAK ADMIN FORM HOZZÁADÁSHOZ -->
    <form action="" novalidate>
    </form>
</body>
</html>