<?php

class FeedbackController {

    const FEEDBACKS = "feedbacks";
    const FEEDBACK = "feedback";
    const ID = "id";
    const USER_ID = "user_id";
    const MESSAGE = "message";
    const CREATED_AT = "created_at";

    function addFeedback($request, $response) {
        if (!Helper::hasRequiredParams(array(self::MESSAGE), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $feedback_msg = $request_data[self::MESSAGE];
        global $user_id;

        $db = new DbOperations();
        $result = $db->addFeedback($user_id, $feedback_msg);

        if ($result == FEEDBACK_CREATED_SUCCESSFULLY) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Feedback sent successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to send subject. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    function getFeedbacks($request, $response) {
        global $user_id;

        $db = new DbOperations();
        $result = $db->getFeedbacks($user_id);

        $message[Helper::ERROR] = false;
        $message[self::FEEDBACKS] = array();

        while ($feedback = $result->fetch_assoc()) {
            array_push($message[self::FEEDBACKS], $this->extractFeedbackDetails($feedback));
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    private function extractFeedbackDetails($feedback) {
        $feedback_details = array();
        $feedback_details[self::ID] = $feedback[self::ID];
        $feedback_details[self::USER_ID] = $feedback[self::USER_ID];
        $feedback_details[self::MESSAGE] = $feedback[self::MESSAGE];
        $feedback_details[self::CREATED_AT] = $feedback[self::CREATED_AT];
        return $feedback_details;
    }

}

?>