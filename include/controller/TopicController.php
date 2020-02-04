<?php

class TopicController {

    const TOPICS = "topics";
    const TOPIC = "topic";
    const ID = "id";
    const SUBJECT_ID = "subject_id";
    const TITLE = "title";
    const SUBTITLE = "subtitle";

    function addTopic($request, $response) {
        if (!Helper::hasRequiredParams(array(self::SUBJECT_ID, self::TITLE, self::SUBTITLE), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $subject_id = $request_data[self::SUBJECT_ID];
        $title = $request_data[self::TITLE];
        $subtitle = $request_data[self::SUBTITLE];

        $db = new DbOperations();
        $isValidSubject = $db->verifySubject($subject_id);

        if ($isValidSubject) {
            $result = $db->addTopic($subject_id, $title, $subtitle);
            if ($result == TOPIC_CREATED_SUCCESSFULLY) {
                $message[Helper::ERROR] = false;
                $message[Helper::MESSAGE] = "Topic added successfully";
                return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
            } else {
                $message[Helper::ERROR] = true;
                $message[Helper::MESSAGE] = "Failed to add topic. Please try again";
                return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
            }
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Invalid subject id. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

}

?>