<?php
require_once "includes/database.php";
$title = "Login |" . $licenseSystem->getInfo("title");
require "includes/header.php";

$req = $licenseSystem->executeQuery("SELECT * FROM settings")->fetch();
$password = $req["login_key"];

if (isset($_POST["password"]) && !empty($_POST["password"])) {
    if ($_POST["password"] == $password) {
        $_SESSION["token"] = $licenseSystem->generateRandomString(42);
        header("Location: index.php");
    } else {
        header("Location: login.php?error=true");
    }
}
?>
    <section class="section_main">
        <div class="main">
        <span class="header_logo"><?= $licenseSystem->getInfo("title") ?></span>
            <p><?= $licenseSystem->getInfo("description") ?></p>
            <form class="login_form" action="login.php" method="post">
                <input type="password" name="password" placeholder="Master Key" required>
                <button type="submit">Login</button>
                <?php if (isset($_GET["error"]) && $_GET["error"] == "true") {
                    $licenseSystem->showToast(
                        "Invalid password",
                        $duration = 3000,
                        $background = "#9e2533"
                    );
                } ?>
            </form>
        </div>
    </section>
    <?php require "includes/footer.php"; ?>
</body>
</html>