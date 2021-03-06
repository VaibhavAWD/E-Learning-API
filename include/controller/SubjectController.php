<?php

class SubjectController {

    const SUBJECTS = "subjects";
    const SUBJECT = "subject";
    const ID = "id";
    const TITLE = "title";
    const SUBTITLE = "subtitle";
    const IMAGE = "image";

    function addSubject($request, $response) {
        if (!Helper::hasRequiredParams(array(self::TITLE, self::SUBTITLE, self::IMAGE), $request, $response)) {
            return;
        }

        $request_data = $request->getParams();
        $title = $request_data[self::TITLE];
        $subtitle = $request_data[self::SUBTITLE];
        $image = $request_data[self::IMAGE];

        if (!Helper::isValidUrl($image, $response)) {
            return;
        }

        $db = new DbOperations();
        $result = $db->addSubject($title, $subtitle, $image);

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

    function getSubjects($request, $response) {
        $db = new DbOperations();
        $result = $db->getSubjects();

        $message[Helper::ERROR] = false;
        $message[self::SUBJECTS] = array();

        while ($subject = $result->fetch_assoc()) {
            array_push($message[self::SUBJECTS], $this->extractSubjectDetails($subject));
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function getSubject($request, $response, $args) {
        $subject_id = $args[self::ID];

        $db = new DbOperations();
        $result = $db->getSubject($subject_id);

        if ($result != null) {
            $message[Helper::ERROR] = false;
            $message[self::SUBJECT] = $this->extractSubjectDetails($result);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Subject not found";
        }

        return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
    }

    function updateSubject($request, $response, $args) {
        if (!Helper::hasRequiredParams(array(self::TITLE, self::SUBTITLE, self::IMAGE), $request, $response)) {
            return;
        }

        $request_data = $request->getParsedBody();
        $subject_id = $args[self::ID];
        $title = $request_data[self::TITLE];
        $subtitle = $request_data[self::SUBTITLE];
        $image = $request_data[self::IMAGE];

        if (!Helper::isValidUrl($image, $response)) {
            return;
        }

        $db = new DbOperations();
        $subjectUpdated = $db->updateSubject($subject_id, $title, $subtitle, $image);

        if ($subjectUpdated) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Subject updated successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to update subject. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    function deleteSubject($request, $response, $args) {
        $subject_id = $args[self::ID];

        $db = new DbOperations();
        $subjectDeleted = $db->deleteSubject($subject_id);

        if ($subjectDeleted) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "Subject deleted successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to delete subject. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    function deleteAllSubjects($request, $response) {
        $db = new DbOperations();
        $subjectsDeleted = $db->deleteAllSubjects();

        if ($subjectsDeleted) {
            $message[Helper::ERROR] = false;
            $message[Helper::MESSAGE] = "All subjects were deleted successfully";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        } else {
            $message[Helper::ERROR] = true;
            $message[Helper::MESSAGE] = "Failed to delete all subjects. Please try again";
            return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
        }
    }

    private function extractSubjectDetails($subject) {
        $subject_details = array();
        $subject_details[self::ID] = $subject[self::ID];
        $subject_details[self::TITLE] = $subject[self::TITLE];
        $subject_details[self::SUBTITLE] = $subject[self::SUBTITLE];
        $subject_details[self::IMAGE] = $subject[self::IMAGE];
        return $subject_details;
    }

}

?>