<?php

class BlogController {

    const BLOGS = "blogs";
    const ID = "id";
    const USER_ID = "user_id";
    const TITLE = "title";
    const BODY = "body";
    const IMAGE_URL = "image_url";
    const CREATED_AT = "created_at";

    function addBlog($request, $response) {
        if (!Helper::hasRequiredParams(array(self::TITLE, self::BODY, self::IMAGE_URL), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $title = $request_data[self::TITLE];
        $body = $request_data[self::BODY];
        $image_url = $request_data[self::IMAGE_URL];
        global $user_id;

        if (!Helper::isValidUrl($image_url, $response)) {
            return;
        }

        $db = new DbOperations();
        $result = $db->addBlog($user_id, $title, $body, $image_url);

        if ($result == BLOG_CREATED_SUCCESSFULLY) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Blog added successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to add blog. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }
}

?>