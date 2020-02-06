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

}

?>