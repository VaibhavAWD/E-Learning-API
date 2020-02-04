<?php

class SubjectController {

    const SUBJECTS = "subjects";
    const ID = "id";
    const TITLE = "title";
    const SUBTITLE = "subtitle";

    function addSubject($request, $response) {
        if (!Helper::hasRequiredParams(array(self::TITLE, self::SUBTITLE), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $title = $request_data[self::TITLE];
        $subtitle = $request_data[self::SUBTITLE];

        $db = new DbOperations();
        $result = $db->addSubject($title, $subtitle);

        if ($result == SUBJECT_CREATED_SUCCESSFULLY) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Subject added successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to add subject. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

}

?>