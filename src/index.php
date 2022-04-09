<?php

require_once "includes/database.php";
$title = $licenseSystem->getInfo("title");
require "includes/header.php";
?>

    <!-- Main -->
    <section class="section_main">
        <div class="main">
        <span class="header_logo"><?= $licenseSystem->getInfo("title") ?></span>
            <p><?= $licenseSystem->getInfo("description") ?></p>
            <a href="users.php">View Users</a>
        </div>
    </section>

    <section class="section_stats">
        <div class="stats">
            <div class="stat">
                <span><?= $licenseSystem->licenseCount() ?></span>
                <p>Keys</p>
            </div>

            <div class="stat">
                <span><?= $licenseSystem->userCount() ?></span>
                <p>Users</p>
            </div>

            <div class="stat">
                <span><?= $licenseSystem->bannedUserCount() ?></span>
                <p>Banned</p>
            </div>
        </div>
    </section>
    <?php require "includes/footer.php"; ?>
</body>
</html>