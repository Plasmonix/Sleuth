<?php

class LicenseSystem
{
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION["token"]) && !empty($_SESSION["token"]);
    }

    public function generateRandomString($length = 32)
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randomString = "";

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function generateLicenseKey($length = 5)
    {
        return implode(
            "-",
            array_map(function () use ($length) {
                return substr(
                    str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"),
                    0,
                    $length
                );
            }, range(1, 4))
        );
    }

    public function executeQuery($query, $params = []) {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function showToast($text, $duration = 3000, $background = "#1b1d2e")
    {
        echo "<script>
            Toastify({
                text: '$text',
                duration: $duration,
                close: true,
                style: {
                    'background': '$background',
                    'box-shadow': '0 0 20px 1px #04061d'
                }
            }).showToast();
        </script>";
    }

    public function getLicenseKey($userId)
    {
        $query = "SELECT license FROM licenses WHERE id = :user_id LIMIT 1";
        $req = $this->executeQuery($query, [':user_id' => $userId]);
        $result = $req->fetch();
        return $result ? $result['license'] : '';
    }

    public function licenseCount()
    {
        return $this->executeQuery("SELECT * FROM licenses")->rowCount();
    }

    public function usedLicenseCount()
    {
        return $this->executeQuery("SELECT * FROM licenses WHERE claimed = 1")->rowCount();
    }

    public function unusedLicenseCount()
    {
        return $this->executeQuery("SELECT * FROM licenses WHERE claimed = 0")->rowCount();
    }

    public function bannedUserCount() 
    {
        return $this->executeQuery("SELECT * FROM users WHERE banned = 1")->rowCount();
    }

    public function userCount()
    {
        return $this->executeQuery("SELECT * FROM users")->rowCount();
    }

    public function getInfo($setting)
    {
        $req = $this->executeQuery("SELECT * FROM settings");
        $value = $req->fetch();
        return $value["$setting"];
    }

    public function deleteUser($id) {
        $this->executeQuery("DELETE FROM users WHERE ID = :id;", [
            "id" => $id,
        ]);
    }

    public function updateUserInfo($id, $expiry, $role, $status) {    
        $this->executeQuery("UPDATE users SET ROLE = :role, BANNED = :status WHERE ID = :id", 
        ["id" => $id, "role" => $role, "status" => $status]);
        
        $this->executeQuery(
            "UPDATE licenses SET VALIDITY = :expiry WHERE ID = :id",
            ["id" => $id, "expiry" => $expiry]);
    }
}

?>
