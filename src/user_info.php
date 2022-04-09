<?php

require_once "includes/database.php";

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = $_GET["id"];
    $req = $licenseSystem->executeQuery(
        "SELECT u.*, l.validity as expire FROM users u JOIN licenses l ON u.ID = l.ID WHERE u.ID = :id",
        ["id" => htmlspecialchars($_GET["id"])]);
    
    if ($req->rowCount() == 0) {
        echo "User not found";
        die();
    }

    $user = $req->fetch();
} else {
    header("Location: users.php");
    die();
}

if (isset($_GET["id"]) && !empty($_GET["id"]) && isset($_GET["type"]) && !empty($_GET["type"])) {
    $type = $_GET["type"];
    $id = $_GET["id"];

    if ($type == "deleteuser") {
        $licenseSystem->deleteUser($id);
        header("Location: ./users.php?action=deleteuser");
        die();
    }
}

if (isset($_POST["expiry"]) && isset($_POST["role"]) && isset($_POST["status"])) {
    $licenseSystem->updateUserInfo($id, $_POST["expiry"], $_POST["role"], $_POST["status"]);
    header("Location: ./user_info.php?id=" . $id . "&action=update_details");
    die();
}
    
$enableInput = isset($_GET["edit"]) && $_GET["edit"] === "1";
$title = $user["name"] . " | " . $licenseSystem->getInfo("title");
require "includes/header.php";
?>
    <section class="section_main">
        <div class="main">
        <span class="header_logo"><?= $licenseSystem->getInfo("title") ?></span>
            <p><?= $licenseSystem->getInfo("description") ?></p>
        </div>
    </section>
    <section class="section_zombie">
        <div id="zombie" class="zombie">
            <div class="user_infos">
                <div class="user_text_infos">
                    <span><?= $user["name"] ?></span>
                </div>
            </div>
            <div class="personnals_infos">
                <div class="custom-input token-input">
                <form action="user_info.php?id=<?php echo $user[
                    "ID"
                ]; ?>"  method="POST" >
                    <label>EXPIRES ON</label>
                    <?php
                    $expire = strtotime(str_replace("/", "-", $user["expire"] . " 02:00:00"));
                    $now = time() + 7200;
                    if ($now >= $expire) {
                        echo '<input name="expiry" id="user_token" ' .
                            ($enableInput ? "" : "disabled") .
                            ' type="date" value="' .
                            $user["expire"] .
                            ' (EXPIRED)" class="select-input">';
                    } else {
                        echo '<input name="expiry" id="user_token" ' .
                            ($enableInput ? "" : "disabled") .
                            ' type="date" value="' .
                            $user["expire"] .
                            '" class="select-input">';
                    }
                    ?>
                    <label>ROLE</label>
                    <select name="role" <?php echo $enableInput ? "" : "disabled"; ?> class="select-input" style="margin-top: 12px; width: 100%">
                        <option value="user" <?php echo $user["role"] === "user" ? "selected" : ""; ?> >USER</option>
                        <option value="dev" <?php echo $user["role"] === "dev" ? "selected" : ""; ?> >DEVELOPER</option>
                        <option value="vip" <?php echo $user["role"] === "vip" ? "selected" : ""; ?> >VIP</option>
                    </select>
                    <label>LICENSE KEY</label>
                    <input name="license" id="user_token" disabled type="text" value="<?= $licenseSystem->getLicenseKey($user["ID"]) ?>" class="select-input">
                    <label>STATUS</label>
                    <select name="status" <?php echo $enableInput ? "" : "disabled"; ?> class="select-input" style="margin-top: 12px; width: 100%">
                        <option value="1" <?php echo $user["banned"] == 1 ? "selected" : ""; ?> >Banned</option>
                        <option value="0" <?php echo $user["banned"] == 0 ? "selected" : ""; ?> >Active</option>
                    </select>
                    <label>IP</label>
                    <input name="ip" id="user_token" disabled type="text" value="<?= $user["ip"] ?>" class="select-input">
                    <label>HWID</label>
                    <input name="hwid" id="user_token" disabled type="text" value="<?= $user["hwid"] ?>" class="select-input">
                    <?php
                    if (isset($_GET["action"])) {
                        if ($_GET["action"] == "ban_user") {
                            $licenseSystem->showToast("Banned user");
                        } elseif ($_GET["action"] == "update_details") {
                            $licenseSystem->showToast("Updated user info");
                        }
                    }
                    if ($enableInput) {
                        echo '<div class="btn">
                        <!--a class="delete_zombie" style="background-color: #9e2533;" href="user_info.php?id=' . $user["ID"] . '&type=ban_user">Ban user</a-->
                        <button href="user_info.php?id=' . $user["ID"] . '&type=update_details">Update details</button>
                        <a class="delete_zombie" href="user_info.php?id=' . $user["ID"] . '&type=deleteuser">Delete user</a></div>';
                    } else {
                        echo '<div class="btn">
                                <a href="user_info.php?id=' . $user["ID"] . '&edit=1">Edit</a>
                            </div>';
                    }
                    ?>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?php require "includes/footer.php"; ?>
</body>
</html>