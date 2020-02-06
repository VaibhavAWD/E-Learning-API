<?php

require '../libs/vendor/autoload.php';
require '../include/util/Helper.php';
require '../include/db/DbOperations.php';
require '../include/controller/AuthController.php';
require '../include/controller/UserController.php';
require '../include/controller/SubjectController.php';
require '../include/controller/TopicController.php';
require '../include/controller/SubtopicController.php';
require '../include/controller/FeedbackController.php';
require '../include/controller/ReportController.php';
require '../include/controller/BlogController.php';

$app = new Slim\App();

$message = array();

$user_id = null;

$app->post('/hello/{name}', function($request, $response, $args) {
    $name = $args['name'];
    $message = "Hello, " . $name . "!";
    return $response->write($message);
});

$app->post('/conncheck', function($request, $response, $args) {
    require_once '../include/db/DbConnect.php';
    $db = new DbConnect();
    $conn = $db->connect();
    if ($conn != null) {
        $message = "Database connection established successfully!";
        return $response->write($message);
    }
});

/* ---------------------------------------------- USERS API ---------------------------------------------- */

$app->post('/register', \UserController::class . ':register');

$app->post('/login', \UserController::class . ':login');

$app->put('/users', \UserController::class . ':update')->add(\AuthController::class);

$app->put('/profilename', \UserController::class . ':updateProfileName')->add(\AuthController::class);

$app->put('/password', \UserController::class . ':updatePassword')->add(\AuthController::class);

$app->put('/deactivate', \UserController::class . ':deactivate')->add(\AuthController::class);

/* ---------------------------------------------- USERS API ---------------------------------------------- */

/* ---------------------------------------------- SUBJECTS API ---------------------------------------------- */

$app->post('/subjects', \SubjectController::class . ':addSubject')->add(\AuthController::class);

$app->get('/subjects', \SubjectController::class . ':getSubjects');

$app->get('/subjects/{id}', \SubjectController::class . ':getSubject')->add(\AuthController::class);

$app->put('/subjects/{id}', \SubjectController::class . ':updateSubject')->add(\AuthController::class);

$app->delete('/subjects/{id}', \SubjectController::class . ':deleteSubject')->add(\AuthController::class);

$app->delete('/subjects', \SubjectController::class . ':deleteAllSubjects')->add(\AuthController::class);

/* ---------------------------------------------- SUBJECTS API ---------------------------------------------- */

/* ---------------------------------------------- TOPICS API ---------------------------------------------- */

$app->post('/topics', \TopicController::class . ':addTopic')->add(\AuthController::class);

$app->get('/topics', \TopicController::class . ':getTopics');

$app->get('/topics/{id}', \TopicController::class . ':getTopic')->add(\AuthController::class);

$app->put('/topics/{id}', \TopicController::class . ':updateTopic')->add(\AuthController::class);

$app->delete('/topics/{id}', \TopicController::class . ':deleteTopic')->add(\AuthController::class);

$app->delete('/topics', \TopicController::class . ':deleteAllTopics')->add(\AuthController::class);

/* ---------------------------------------------- TOPICS API ---------------------------------------------- */

/* ---------------------------------------------- SUBTOPICS API ---------------------------------------------- */

$app->post('/subtopics', \SubtopicController::class . ':addSubtopic')->add(\AuthController::class);

$app->get('/subtopics', \SubtopicController::class . ':getSubtopics');

$app->get('/subtopics/{id}', \SubtopicController::class . ':getSubtopic')->add(\AuthController::class);

$app->put('/subtopics/{id}', \SubtopicController::class . ':updateSubtopic')->add(\AuthController::class);

$app->delete('/subtopics/{id}', \SubtopicController::class . ':deleteSubtopic')->add(\AuthController::class);

$app->delete('/subtopics', \SubtopicController::class . ':deleteAllSubtopics')->add(\AuthController::class);

/* ---------------------------------------------- SUBTOPICS API ---------------------------------------------- */

/* ---------------------------------------------- FEEDBACKS API ---------------------------------------------- */

$app->post('/feedbacks', \FeedbackController::class . ':addFeedback')->add(\AuthController::class);

$app->get('/feedbacks', \FeedbackController::class . ':getFeedbacks')->add(\AuthController::class);

$app->get('/feedbacks/{id}', \FeedbackController::class . ':getFeedback')->add(\AuthController::class);

$app->put('/feedbacks/{id}', \FeedbackController::class . ':updateFeedback')->add(\AuthController::class);

$app->delete('/feedbacks/{id}', \FeedbackController::class . ':deleteFeedback')->add(\AuthController::class);

$app->delete('/feedbacks', \FeedbackController::class . ':deleteAllFeedbacks')->add(\AuthController::class);

/* ---------------------------------------------- FEEDBACKS API ---------------------------------------------- */

/* ---------------------------------------------- REPORTS API ---------------------------------------------- */

$app->post('/reports', \ReportController::class . ':addReport')->add(\AuthController::class);

$app->get('/reports', \ReportController::class . ':getReports')->add(\AuthController::class);

$app->get('/reports/{id}', \ReportController::class . ':getReport')->add(\AuthController::class);

$app->put('/reports/{id}', \ReportController::class . ':updateReport')->add(\AuthController::class);

$app->delete('/reports/{id}', \ReportController::class . ':deleteReport')->add(\AuthController::class);

$app->delete('/reports', \ReportController::class . ':deleteAllReports')->add(\AuthController::class);

/* ---------------------------------------------- REPORTS API ---------------------------------------------- */

/* ---------------------------------------------- BLOGS API ---------------------------------------------- */

$app->post('/blogs', \BlogController::class . ':addBlog')->add(\AuthController::class);

$app->get('/blogs', \BlogController::class . ':getBlogs')->add(\AuthController::class);

$app->get('/myblogs', \BlogController::class . ':getBlogsByUserId')->add(\AuthController::class);

$app->get('/blogs/{id}', \BlogController::class . ':getBlog')->add(\AuthController::class);

/* ---------------------------------------------- BLOGS API ---------------------------------------------- */

$app->run();

?>