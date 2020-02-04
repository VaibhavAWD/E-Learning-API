<?php

class SubtopicController {

    const SUBTOPICS = "subtopics";
    const SUBTOPIC = "subtopic";
    const ID = "id";
    const TOPIC_ID = "topic_id";
    const TITLE = "title";
    const BODY = "body";
    const URL = "url";
    const THUMBNAIL = "thumbnail";
    const TIME = "time";

    function addSubtopic($request, $response) {
        if (!Helper::hasRequiredParams(array(self::TOPIC_ID, self::TITLE, self::BODY, self::URL, 
            self::THUMBNAIL, self::TIME), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $topic_id = $request_data[self::TOPIC_ID];
        $title = $request_data[self::TITLE];
        $body = $request_data[self::BODY];
        $url = $request_data[self::URL];
        $thumbnail = $request_data[self::THUMBNAIL];
        $time = $request_data[self::TIME];

        if (!Helper::isValidUrl($url, $response)) {
            return;
        }

        if (!Helper::isValidUrl($thumbnail, $response)) {
            return;
        }

        $db = new DbOperations();
        $isValidTopic = $db->verifyTopic($topic_id);

        if ($isValidTopic) {
            $result = $db->addSubtopic($topic_id, $title, $body, $url, $thumbnail, $time);
            if ($result == SUBTOPIC_CREATED_SUCCESSFULLY) {
                $message[Helper::ERROR] = false;
                $message[Helper::MESSAGE] = "Subtopic added successfully";
                return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
            } else {
                $message[Helper::ERROR] = true;
                $message[Helper::MESSAGE] = "Failed to add subtopic. Please try again";
                return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
            }
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Invalid topic id. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    function getSubtopics($request, $response) {
        if (!Helper::hasRequiredParams(array(self::TOPIC_ID), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $topic_id = $request_data[self::TOPIC_ID];

        $db = new DbOperations();
        $result = $db->getSubtopics($topic_id);

        $message[Helper::ERROR] = false;
        $message[self::SUBTOPICS] = array();

        while ($subtopic = $result->fetch_assoc()) {
            array_push($message[self::SUBTOPICS], $this->extractSubtopicDetails($subtopic));
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function getSubtopic($request, $response, $args) {
        $subtopic_id = $args[self::ID];

        $db = new DbOperations();
        $subtopic = $db->getSubtopic($subtopic_id);

        if ($subtopic != null) {
            $message[Helper::ERROR] = false;
            $message[self::SUBTOPIC] = $this->extractSubtopicDetails($subtopic);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Subtopic not found";
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function updateSubtopic($request, $response, $args) {
        if (!Helper::hasRequiredParams(array(self::TOPIC_ID, self::TITLE, self::BODY, self::URL, 
            self::THUMBNAIL, self::TIME), $request, $response)) {
            return;
        }

        $request_data = $request->getParsedBody();
        $subtopic_id = $args[self::ID];
        $topic_id = $request_data[self::TOPIC_ID];
        $title = $request_data[self::TITLE];
        $body = $request_data[self::BODY];
        $url = $request_data[self::URL];
        $thumbnail = $request_data[self::THUMBNAIL];
        $time = $request_data[self::TIME];

        $db = new DbOperations();
        $isValidTopic = $db->verifyTopic($topic_id);

        if ($isValidTopic) {
            $subtopicUpdated = $db->updateSubtopic($subtopic_id, $topic_id, $title, $body, $url, $thumbnail, $time);
            if ($subtopicUpdated) {
                $message[Helper::ERROR] = false;
                $message[Helper::MESSAGE] = "Subtopic updated successfully";
                return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
            } else {
                $message[Helper::ERROR] = true;
                $message[Helper::MESSAGE] = "Failed to update subtopic. Please try again";
                return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
            }
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Invalid topic id. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    private function extractSubtopicDetails($subtopic) {
        $subtopic_details = array();
        $subtopic_details[self::ID] = $subtopic[self::ID];
        $subtopic_details[self::TOPIC_ID] = $subtopic[self::TOPIC_ID];
        $subtopic_details[self::TITLE] = $subtopic[self::TITLE];
        $subtopic_details[self::BODY] = $subtopic[self::BODY];
        $subtopic_details[self::URL] = $subtopic[self::URL];
        $subtopic_details[self::THUMBNAIL] = $subtopic[self::THUMBNAIL];
        $subtopic_details[self::TIME] = $subtopic[self::TIME];
        return $subtopic_details;
    }

}

?>