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

    function getTopics($request, $response) {
        if (!Helper::hasRequiredParams(array(self::SUBJECT_ID), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $subject_id = $request_data[self::SUBJECT_ID];

        $db = new DbOperations();
        $result = $db->getTopics($subject_id);

        $message[Helper::ERROR] = false;
        $message[self::TOPICS] = array();

        while ($topic = $result->fetch_assoc()) {
            array_push($message[self::TOPICS], $this->extractTopicDetails($topic));
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function getTopic($request, $response, $args) {
        $topic_id = $args[self::ID];

        $db = new DbOperations();
        $topic = $db->getTopic($topic_id);

        if ($topic != null) {
            $message[Helper::ERROR] = false;
            $message[self::TOPIC] = $this->extractTopicDetails($topic);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Topic not found";
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function updateTopic($request, $response, $args) {
        if (!Helper::hasRequiredParams(array(self::SUBJECT_ID, self::TITLE, self::SUBTITLE), $request, $response)) {
            return;
        }

        $request_data = $request->getParsedBody();
        $topic_id = $args[self::ID];
        $subject_id = $request_data[self::SUBJECT_ID];
        $title = $request_data[self::TITLE];
        $subtitle = $request_data[self::SUBTITLE];

        $db = new DbOperations();
        $isValidSubject = $db->verifySubject($subject_id);

        if ($isValidSubject) {
            $topicUpdated = $db->updateTopic($topic_id, $subject_id, $title, $subtitle);
            if ($topicUpdated) {
                $message[Helper::ERROR] = false;
                $message[Helper::MESSAGE] = "Topic updated successfully";
                return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
            } else {
                $message[Helper::ERROR] = true;
                $message[Helper::MESSAGE] = "Failed to update topic. Please try again";
                return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
            }
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Invalid subject id. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    function deleteTopic($request, $response, $args) {
        $topic_id = $args[self::ID];

        $db = new DbOperations();
        $topicDeleted = $db->deleteTopic($topic_id);

        if ($topicDeleted) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Topic deleted successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to delete topic. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    private function extractTopicDetails($topic) {
        $topic_details = array();
        $topic_details[self::ID] = $topic[self::ID];
        $topic_details[self::SUBJECT_ID] = $topic[self::SUBJECT_ID];
        $topic_details[self::TITLE] = $topic[self::TITLE];
        $topic_details[self::SUBTITLE] = $topic[self::SUBTITLE];
        return $topic_details;
    }

}

?>