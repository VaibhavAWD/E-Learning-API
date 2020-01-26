<?php

class DbConnect {

    function connect() {
        include_once dirname(__FILE__) . '/DbConfig.php';
        
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        if (mysqli_connect_errno()) {
            echo "Failed to establish database connection due to " . mysqli_connect_error();
            return null;
        } else {
            return $conn;
        }
    }

}

?>