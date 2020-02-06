<?php

class BlogController {

    const BLOGS = "blogs";
    const BLOG = "blog";
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
    
    function getBlogs($request, $response) {
        $db = new DbOperations();
        $result = $db->getBlogs();

        $message[Helper::ERROR] = false;
        $message[self::BLOGS] = array();

        while ($blog = $result->fetch_assoc()) {
            array_push($message[self::BLOGS], $this->extractBlogDetails($blog));
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function getBlogsByUserId($request, $response) {
        global $user_id;

        $db = new DbOperations();
        $result = $db->getBlogByUserId($user_id);

        $message[Helper::ERROR] = false;
        $message[self::BLOGS] = array();

        while ($blog = $result->fetch_assoc()) {
            array_push($message[self::BLOGS], $this->extractBlogDetails($blog));
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function getBlog($request, $response, $args) {
        $blog_id = $args[self::ID];

        $db = new DbOperations();
        $blog = $db->getBlog($blog_id);

        if ($blog != null) {
            $message[Helper::ERROR] = false;
            $message[self::BLOG] = $this->extractBlogDetails($blog);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Blog not found";
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function updateBlog($request, $response, $args) {
        if (!Helper::hasRequiredParams(array(self::TITLE, self::BODY, self::IMAGE_URL), $request, $response)) {
            return;
        }

        $request_data = $request->getParsedBody();
        $blog_id = $args[self::ID];
        $title = $request_data[self::TITLE];
        $body = $request_data[self::BODY];
        $image_url = $request_data[self::IMAGE_URL];
        global $user_id;

        if (!Helper::isValidUrl($image_url, $response)) {
            return;
        }

        $db = new DbOperations();
        $blogUpdated = $db->updateBlog($blog_id, $user_id, $title, $body, $image_url);

        if ($blogUpdated) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Blog updated successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to update blog. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    function deleteBlog($request, $response, $args) {
        $blog_id = $args[self::ID];

        $db = new DbOperations();
        $blogDeleted = $db->deleteBlog($blog_id);

        if ($blogDeleted) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Blog deleted successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to delete blog. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    private function extractBlogDetails($blog) {
        $blog_details = array();
        $blog_details[self::ID] = $blog[self::ID];
        $blog_details[self::USER_ID] = $blog[self::USER_ID];
        $blog_details[self::TITLE] = $blog[self::TITLE];
        $blog_details[self::BODY] = $blog[self::BODY];
        $blog_details[self::IMAGE_URL] = $blog[self::IMAGE_URL];
        $blog_details[self::CREATED_AT] = $blog[self::CREATED_AT];
        return $blog_details;
    }
}

?>