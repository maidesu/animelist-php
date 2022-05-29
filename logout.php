<?php
    function redirect($page) {
        header("Location: ${page}");
        exit();
    }

    session_start();
    $_SESSION = array();
    session_destroy();

    redirect('index.php');
?>
