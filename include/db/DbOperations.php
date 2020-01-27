<?php

class DbOperations {

    private $conn;

    function __construct() {
        include_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* --------------------------------------------- USERS TABLE ------------------------------------------------ */

    function registerUser($name, $email, $password) {
        $password_hash = $this->getEncryptedPassword($password);
        $api_key = $this->generateApiKey();

        if ($this->isEmailRegistered($email)) {
            return USER_ALREADY_EXISTS;
        }

        $stmt = $this->conn->prepare("INSERT INTO `users`(`name`, `email`, `password_hash`, `api_key`) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password_hash, $api_key);
        if ($stmt->execute()) {
            return USER_CREATED_SUCCESSFULLY;
        } else {
            return FAILED_TO_CREATE_USER;
        }
    }

    function loginUser($email, $password) {
        $stmt = $this->conn->prepare("SELECT `password_hash` FROM `users` WHERE `email` = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        if ($num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $password_hash)) {
                return USER_AUTHENTICATED;
            } else {
                return USER_AUTHENTICATION_FAILURE;
            }
        } else {
            return USER_NOT_FOUND;
        }
    }

    function updateUser($id, $name, $password) {
        $password_hash = $this->getEncryptedPassword($password);
        $stmt = $this->conn->prepare("UPDATE `users` SET `name` = ?, `password_hash` = ? WHERE `id` = ?");
        $stmt->bind_param("ssi", $name, $password_hash, $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    function deactivateUser($id) {
        $stmt = $this->conn->prepare("UPDATE `users` SET `status` = 2 WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE `email` = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        } else {
            return USER_NOT_FOUND;
        }
    }

    function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT `id` FROM `users` WHERE `api_key` = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $user_id = $stmt->get_result()->fetch_assoc();
            return $user_id;
        } else {
            return null;
        }
    }

    function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT `id` FROM `users` WHERE `api_key` = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        return $num_rows > 0;
    }

    private function getEncryptedPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    private function isEmailRegistered($email) {
        $stmt = $this->conn->prepare("SELECT `id` FROM `users` WHERE `email` = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        return $num_rows > 0;
    }

    /* --------------------------------------------- USERS TABLE ------------------------------------------------ */

}

?>