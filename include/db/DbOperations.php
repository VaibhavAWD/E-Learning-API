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
                $this->activateUser($email);
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

    function updateUserName($id, $name) {
        $stmt = $this->conn->prepare("UPDATE `users` SET `name` = ? WHERE `id` = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    function verifyPassword($id, $password) {
        $stmt = $this->conn->prepare("SELECT `password_hash` FROM `users` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
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

    function updatePassword($id, $new_password) {
        $password_hash = $this->getEncryptedPassword($new_password);
        $stmt = $this->conn->prepare("UPDATE `users` SET `password_hash` = ? WHERE `id` = ?");
        $stmt->bind_param("si", $password_hash, $id);
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

    private function activateUser($email) {
        $stmt = $this->conn->prepare("UPDATE `users` SET `status` = 1 WHERE `email` = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
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

    /* --------------------------------------------- SUBJECTS TABLE ------------------------------------------------ */

    function addSubject($title, $subtitle) {
        $stmt = $this->conn->prepare("INSERT INTO `subjects`(`title`, `subtitle`) VALUES(? , ?)");
        $stmt->bind_param("ss", $title, $subtitle);
        if ($stmt->execute()) {
            return SUBJECT_CREATED_SUCCESSFULLY;
        } else {
            return FAILED_TO_CREATE_SUBJECT;
        }
    }

    function getSubjects() {
        $stmt = $this->conn->prepare("SELECT * FROM `subjects`");
        $stmt->execute();
        return $stmt->get_result();
    }

    function getSubject($id) {
        $stmt = $this->conn->prepare("SELECT * FROM `subjects` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        } else {
            return null;
        }
    }

    function updateSubject($id, $title, $subtitle) {
        $stmt = $this->conn->prepare("UPDATE `subjects` SET `title` = ?, `subtitle` = ? WHERE `id` = ?");
        $stmt->bind_param("ssi", $title, $subtitle, $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    function deleteSubject($id) {
        $stmt = $this->conn->prepare("DELETE FROM `subjects` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    function deleteAllSubjects() {
        $stmt = $this->conn->prepare("DELETE FROM `subjects`");
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    /* --------------------------------------------- SUBJECTS TABLE ------------------------------------------------ */

    /* --------------------------------------------- TOPICS TABLE ------------------------------------------------ */

    function addTopic($subject_id, $title, $subtitle) {
        $stmt = $this->conn->prepare("INSERT INTO `topics`(`subject_id`, `title`, `subtitle`) VALUES(?, ? , ?)");
        $stmt->bind_param("iss", $subject_id, $title, $subtitle);
        if ($stmt->execute()) {
            return TOPIC_CREATED_SUCCESSFULLY;
        } else {
            return FAILED_TO_CREATE_TOPIC;
        }
    }

    function verifySubject($id) {
        $stmt = $this->conn->prepare("SELECT * FROM `subjects` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        return $num_rows > 0;
    }

    function getTopics($subject_id) {
        $stmt = $this->conn->prepare("SELECT * FROM `topics` WHERE `subject_id` = ?");
        $stmt->bind_param("i", $subject_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getTopic($id) {
        $stmt = $this->conn->prepare("SELECT * FROM `topics` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        } else {
            return null;
        }
    }

    function updateTopic($id, $subject_id, $title, $subtitle) {
        $stmt = $this->conn->prepare("UPDATE `topics` SET `subject_id` = ?, `title` = ?, `subtitle` = ? WHERE `id` = ?");
        $stmt->bind_param("issi", $subject_id, $title, $subtitle, $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    function deleteTopic($id) {
        $stmt = $this->conn->prepare("DELETE FROM `topics` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    function deleteAllTopics() {
        $stmt = $this->conn->prepare("DELETE FROM `topics`");
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    /* --------------------------------------------- TOPICS TABLE ------------------------------------------------ */

    /* --------------------------------------------- SUBTOPICS TABLE ------------------------------------------------ */

    function addSubtopic($topic_id, $title, $body, $url, $thumbnail, $time) {
        $stmt = $this->conn->prepare("INSERT INTO `subtopics`(`topic_id`, `title`, `body`, `url`, `thumbnail`, `time`) 
            VALUES(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $topic_id, $title, $body, $url, $thumbnail, $time);
        if ($stmt->execute()) {
            return SUBTOPIC_CREATED_SUCCESSFULLY;
        } else {
            return FAILED_TO_CREATE_SUBTOPIC;
        }
    }

    function verifyTopic($id) {
        $stmt = $this->conn->prepare("SELECT * FROM `topics` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        return $num_rows > 0;
    }

    function getSubtopics($topic_id) {
        $stmt = $this->conn->prepare("SELECT * FROM `subtopics` WHERE `topic_id` = ?");
        $stmt->bind_param("i", $topic_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    function getSubtopic($id) {
        $stmt = $this->conn->prepare("SELECT * FROM `subtopics` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        } else {
            return null;
        }
    }

    function updateSubtopic($id, $topic_id, $title, $body, $url, $thumbnail, $time) {
        $stmt = $this->conn->prepare("UPDATE `subtopics` SET `topic_id` = ?, `title` = ?, `body` = ?,
            `url` = ?, `thumbnail` = ?, `time` = ? WHERE `id` = ?");
        $stmt->bind_param("isssssi", $topic_id, $title, $body, $url, $thumbnail, $time, $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    function deleteSubtopic($id) {
        $stmt = $this->conn->prepare("DELETE FROM `subtopics` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_affected_rows = $stmt->affected_rows;
        return $num_affected_rows > 0;
    }

    /* --------------------------------------------- SUBTOPICS TABLE ------------------------------------------------ */

}

?>