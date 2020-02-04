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

}

?>