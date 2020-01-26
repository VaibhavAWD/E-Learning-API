<?php

class UserController {

    // user constants
    const USER = "user";
    const ID = "id";
    const NAME = "name";
    const EMAIL = "email";
    const PASSWORD = "password";
    const PASSWORD_HASH = "password_hash";
    const API_KEY = "api_key";
    const CREATED_AT = "created_at";
    const STATUS = "status";

    function register($request, $response) {
        if (!Helper::hasRequiredParams(array(self::NAME, self::EMAIL, self::PASSWORD), $response)) {
            return;
        }

        $request_data = $request->getParams();
        $name = $request_data[self::NAME];
        $email = $request_data[self::EMAIL];
        $password = $request_data[self::PASSWORD];

        if (!Helper::isValidEmail($email, $response)) {
            return;
        }

        $db = new DbOperations();
        $result = $db->registerUser($name, $email, $password);

        if ($result == USER_CREATED_SUCCESSFULLY) {
            $user = $db->getUserByEmail($email);
            $message[Helper::ERROR] = false;
            $message[self::USER] = $this->extractUserDetails($user);
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else if ($result == FAILED_TO_CREATE_USER) {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Problem registering user at this moment. Please try again later";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else { // user already exists
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "User with email is already registered. Please try again";
            return Helper::buildResponse(Helper::STATUS_CONFLICT, $message, $response);
        }
    }

    private function extractUserDetails($user) {
        $user_details = array();
        $user_details[self::ID] = $user[self::ID];
        $user_details[self::NAME] = $user[self::NAME];
        $user_details[self::EMAIL] = $user[self::EMAIL];
        $user_details[self::PASSWORD_HASH] = $user[self::PASSWORD_HASH];
        $user_details[self::API_KEY] = $user[self::API_KEY];
        $user_details[self::CREATED_AT] = $user[self::CREATED_AT];
        $user_details[self::STATUS] = $user[self::STATUS];
        return $user_details;
    }

}

?>