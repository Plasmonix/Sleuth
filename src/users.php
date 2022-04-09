<?php

require_once "includes/database.php";
$title = "Users | " . $licenseSystem->getInfo("title");
require "includes/header.php";
?>
    <section class="section_main">
        <div class="main">
        <span class="header_logo"><?= $licenseSystem->getInfo("title") ?></span>
            <p><?= $licenseSystem->getInfo("description") ?></p>
        </div>
    </section>
    <section class="section_zombies">
        <div id="zombies" class="zombies">
        <?php
        $data = $licenseSystem->executeQuery("SELECT u.*, l.validity as expire FROM users u JOIN licenses l ON u.ID = l.ID");
        
        while ($user = $data->fetch()) {
            $expire = strtotime(str_replace("/", "-", $user["expire"] . " 02:00:00"));
            $now = time() + 7200;
            ?>
            <div class="zombie">
            <span><?= $user["name"] ?></span>
            <span><?= $now >= $expire ? $user["expire"] . " (Expired)" : $user["expire"] ?></span>
            <span><?= $user["role"] ?></span>
            <a href="user_info.php?id=<?= $user["ID"] ?>">Details</a>
            </div>
            <?php
        }
        ?>
        </div>
    </section>
    <?php require "includes/footer.php"; ?>
</body>
</html>