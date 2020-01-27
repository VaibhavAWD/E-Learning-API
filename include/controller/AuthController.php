<?php

class AuthController {

    /**
     * Authenticate user before procedding.
     */
    function __invoke($request, $response, $next) {
        // Getting request headers
        $headers = apache_request_headers();
    
        // Verifying Authorization Header
        if (isset($headers['Authorization'])) {
            $db = new DbOperations();
    
            // get the api key
            $api_key = $headers['Authorization'];
            // validating api key
            if (!$db->isValidApiKey($api_key)) {
                // api key is not present in users table
                $message["error"] = true;
                $message["message"] = "Access Denied. Invalid Api key";
                return Helper::buildResponse(Helper::STATUS_UNAUTHORIZED, $message, $response);
            } else {
                global $user_id;
                // get user primary key id
                $user = $db->getUserId($api_key);
                if ($user != NULL)
                    $user_id = $user["id"];
                    // proceed ahead
                    $response = $next($request, $response);
                    return $response;
            }
        } else {
            // api key is missing in header
            $message["error"] = true;
            $message["message"] = "Api key is misssing";
            return Helper::buildResponse(Helper::STATUS_BAD_REQUEST, $message, $response);
        }
    }
    
}

?>