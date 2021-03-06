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

    function getReports($request, $response) {
        global $user_id;

        $db = new DbOperations();
        $result = $db->getReports($user_id);

        $message[Helper::ERROR] = false;
        $message[self::REPORTS] = array();

        while ($report = $result->fetch_assoc()) {
            array_push($message[self::REPORTS], $this->extractReportDetails($report));
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function getReport($request, $response, $args) {
        $report_id = $args[self::ID];

        $db = new DbOperations();
        $report = $db->getReport($report_id);

        if ($report != null) {
            $message[Helper::ERROR] = false;
            $message[self::REPORT] = $this->extractReportDetails($report);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Report not found";
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function updateReport($request, $response, $args) {
        if (!Helper::hasRequiredParams(array(self::MESSAGE), $request, $response)) {
            return;
        }

        $request_data = $request->getParsedBody();
        $report_id = $args[self::ID];
        $report_msg = $request_data[self::MESSAGE];
        global $user_id;

        $db = new DbOperations();
        $reportUpdated = $db->updateReport($report_id, $user_id, $report_msg);

        if ($reportUpdated) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Report updated successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to update report. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    function deleteReport($request, $response, $args) {
        $report_id = $args[self::ID];

        $db = new DbOperations();
        $reportDeleted = $db->deleteReport($report_id);

        if ($reportDeleted) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Report deleted successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to delete report. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    function deleteAllReports($request, $response) {
        global $user_id;

        $db = new DbOperations();
        $reportsDeleted = $db->deleteAllReports($user_id);

        if ($reportsDeleted) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "All reports were deleted successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to delete all reports. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    private function extractReportDetails($report) {
        $report_details = array();
        $report_details[self::ID] = $report[self::ID];
        $report_details[self::USER_ID] = $report[self::USER_ID];
        $report_details[self::MESSAGE] = $report[self::MESSAGE];
        $report_details[self::CREATED_AT] = $report[self::CREATED_AT];
        return $report_details;
    }

}

?>