<?php

require_once "includes/database.php";
$title = "License | " . $licenseSystem->getInfo("title");
require "includes/header.php";

if (isset($_GET["remove"]) && !empty($_GET["remove"])) {
    $licenseSystem->executeQuery(
        "DELETE FROM licenses WHERE ID = :licenseId",
        [":licenseId" => $_GET["remove"]]
    );
    header("Location: ?status=rem_success");
}
if (isset($_POST["generatelicense"]) && !empty($_POST["generatelicense"])) {
    $licenseSystem->executeQuery(
        "INSERT INTO licenses (ID, license, validity, claimed) VALUES (NULL, :license, :validity, '0')",
        [
            ":license" => $licenseSystem->generateLicenseKey(),
            ":validity" => $_POST["generatelicense"],
        ]
    );
    header("Location: keys.php?status=success");
}
?>
    <section class="section_main">
        <div class="main">
        <span class="header_logo"><?= $licenseSystem->getInfo("title") ?></span>
        <p><?= $licenseSystem->getInfo("description") ?></p>
        </div>
    </section>

    <section class="section_stats">
        <div class="stats">
            <div class="stat">
                <span><?= $licenseSystem->licenseCount() ?></span>
                <p>Total</p>
            </div>
            <div class="stat">
                <span><?= $licenseSystem->usedlicenseCount() ?></span>
                <p>Used</p>
            </div>
            <div class="stat">
                <span><?= $licenseSystem->unusedlicenseCount() ?></span>
                <p>Unused</p>
            </div>
            
    </section>
    <section class="section_main">
        <div class="main">
        <h3>Generate License Key</h3>
                <form class="gen_form" action="keys.php" method="post">
                    <input type="date" name="generatelicense" placeholder="Validity" required>
                    <button type="submit">Generate</button></div>
                </form>
        </div>
    </section>
    <section class="section_zombies">
    <div id="zombies" class="zombies">
    <?php
    if (isset($_GET["status"]) && $_GET["status"] == "rem_success") {
        $licenseSystem->showToast("Removed License key");
    }
    if (isset($_GET["status"]) && $_GET["status"] == "failed") {
        $licenseSystem->showToast(
            "Invalid number",
            $duration = 3000,
            $background = "#9e2533"
        );
    }
    if (isset($_GET["status"]) && $_GET["status"] == "success") {
        $licenseSystem->showToast("Generated License key");
    }
    ?>
                    
    </div>
</div>
</section>
    <section class="section_zombies">
        <div id="zombies" class="zombies">
        <?php
        $data = $licenseSystem->executeQuery("SELECT * FROM licenses");
        while ($license = $data->fetch()): ?>
            <div class="zombie">
                <span><?= $license["license"] ?></span>
                <span><?= ($currentDate = new DateTime()) > new DateTime($license["validity"]) ? "Expired" : $currentDate->diff(new DateTime($license["validity"]))->days + 1 ." Day(s) left" ?></span>
                <a style="width: 10%"><?= $license["claimed"] == 1 ? "USED" : "UNUSED" ?></a>
                <a class="action-btn" style="background-color: #b32939;" href="keys.php?remove=<?= $license["ID"] ?>">Delete</a>
            </div>
        <?php endwhile;
        ?>
        </div>
    </section>
    <?php require "includes/footer.php"; ?>
</body>
</html>