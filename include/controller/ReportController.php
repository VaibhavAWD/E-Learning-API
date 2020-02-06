<?php

class ReportController {

    const REPORTS = "reports";
    const REPORT = "report";
    const ID = "id";
    const USER_ID = "user_id";
    const MESSAGE = "message";
    const CREATED_AT = "created_at";

    function addReport($request, $response) {
        if (!Helper::hasRequiredParams(array(self::MESSAGE), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $report_msg = $request_data[self::MESSAGE];
        global $user_id;

        $db = new DbOperations();
        $result = $db->addReport($user_id, $report_msg);

        if ($result == REPORT_CREATED_SUCCESSFULLY) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Report submitted successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to submit report. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

}

?>