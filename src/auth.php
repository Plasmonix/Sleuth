<?php

require_once "includes/config.php";
error_reporting(0);
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host; dbname=$database", $user, $password);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function checkLicenseStatus($license)
{
    global $pdo;
    $query = "SELECT 
                DATE_FORMAT(l.validity, '%Y-%m-%d') as expiry,  
                l.claimed, 
                u.hwid,
                IF(u.banned = 1, 'banned', 'active') as status
            FROM licenses l
            LEFT JOIN users u ON l.ID = u.id
            WHERE l.license = :license
            LIMIT 1";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':license', $license);
    $statement->execute();

    $result = $statement->fetch($pdo::FETCH_ASSOC);

    if (!$result) {
        return json_encode(["status" => "error", "message" => "License not found"]);
    }

    if ($result['claimed'] && isset($_POST['hwid']) && $result['hwid'] != $_POST['hwid']) {
        if ($result['status'] == "banned") {
            return json_encode(["status" => "error", "message" => "License is associated with a banned user"]);
        }
        return json_encode(["status" => "error", "message" => "License is not eligible for registration"]);
    }

    $expiryDate = new DateTime($result['expiry']);
    $currentDate = new DateTime();

    if ($currentDate > $expiryDate) {
        return json_encode(["status" => "error", "message" => "License has expired"]);
    }

    return json_encode(["status" => "success", "data" => $result]);

}

function registerUser($userData)
{
    global $pdo;
    $name = $userData['name'];
    $role = $userData['role'];
    $ip = $userData['ip'];
    $hwid = $userData['hwid'];
    $license = $userData['license'];

    $licenseCheckResult = checkLicenseStatus($license);
    $licenseCheckData = json_decode($licenseCheckResult, true);

    if ($licenseCheckData['status'] === 'success' && $licenseCheckData['data']['claimed'] == 1) {
        return json_encode(["status" => "error", "message" => "License is already in use"]);
    }

    $queryInsertUser = "INSERT INTO users (name, role, ip, hwid) VALUES (:name, :role, :ip, :hwid)";
    $statementInsertUser = $pdo->prepare($queryInsertUser);
    $statementInsertUser->bindParam(':name', $name);
    $statementInsertUser->bindParam(':role', $role);
    $statementInsertUser->bindParam(':ip', $ip);
    $statementInsertUser->bindParam(':hwid', $hwid);
    $statementInsertUser->execute();

    $userId = $pdo->lastInsertId();
    $queryUpdateLicense = "UPDATE licenses SET ID = $userId, claimed = 1 WHERE license = :license";
    $statementUpdateLicense = $pdo->prepare($queryUpdateLicense);
    $statementUpdateLicense->bindParam(':license', $license);
    $statementUpdateLicense->execute();

    return json_encode(["status" => "success", "message" => "User registered successfully"]);
}


function getUserInfo($license)
{
    global $pdo;
    $query = "SELECT u.name, u.ip, u.role, u.hwid, IF(u.banned = 1, 'banned', 'active') as status, l.validity as expiry
              FROM users u
              INNER JOIN licenses l ON u.ID = l.ID
              WHERE l.license = :license";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':license', $license);
    $statement->execute();

    $result = $statement->fetch($pdo::FETCH_ASSOC);

    if ($result) {
        return json_encode(["status" => "success", "data" => $result]);
    } else {
        return json_encode(["status" => "error", "message" => "User not found"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['license']) && isset($_POST['action'])) {
    $license = $_POST['license'];
    $action = $_POST['action'];
    switch ($action) {
        case 'checkLicenseStatus':
            echo checkLicenseStatus($license);
            break;

        case 'registerUser':
            $userData = $_POST;
            $licenseCheckResult = checkLicenseStatus($license);
            $licenseCheckData = json_decode($licenseCheckResult, true);

            if ($licenseCheckData['status'] === 'success') {
                echo registerUser($userData);
            } else {
                echo $licenseCheckResult;
            }
            break;

        case 'getUserInfo':
            echo getUserInfo($license);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Invalid action"]);
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

?>