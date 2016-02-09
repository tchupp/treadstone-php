<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Database\SectionRepository;
use Exception;

class CourseResource {

    public static function registerApi(Application $app) {
        $app->get('/courses', self::getAllCourses($app));
        $app->get('/courses/:semester', self::getAllCoursesBySemester($app));
        $app->get('/courses/:semester/:subject', self::getAllCoursesBySemesterAndSubject($app));
    }

    public static function documentation() {
        $courseSchema = ['semester'      => ['code' => 'string', 'name' => 'string'],
                         'subject'       => ['code' => 'string', 'name' => 'string'],
                         'sectionNumber' => 'int',
                         'times'         => [['day'       => 'string',
                                              'startTime' => 'time',
                                              'endTime'   => 'time']]
        ];
        $errorSchema = ['status' => 'int', 'statusText' => 'string', 'description' => 'string'];
        $docs[] = ['uri'       => '/courses', 'method' => 'GET',
                   'responses' => [
                       ['status' => 200,
                        'body'   => [$courseSchema]],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/courses/:semester', 'method' => 'GET',
                   'responses' => [
                       ['status' => 200,
                        'body'   => [$courseSchema]],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/courses/:semester/:subject', 'method' => 'GET',
                   'responses' => [
                       ['status' => 200,
                        'body'   => [$courseSchema]],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        return $docs;
    }

    private static function getAllCourses(Application $app) {
        return function () use ($app) {
            $response = $app->response;

            $sectionRepository = SectionRepository::autowire();

            $courses = $sectionRepository->findAll();

            if (empty($courses)) {
                throw new Exception('Course not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($courses, JSON_PRETTY_PRINT));
        };
    }

    private static function getAllCoursesBySemester(Application $app) {
        return function ($semester) use ($app) {
            $response = $app->response;

            $sectionRepository = SectionRepository::autowire();

            $courses = $sectionRepository->findAllBySemester($semester);

            if (empty($courses)) {
                throw new Exception('Course not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($courses, JSON_PRETTY_PRINT));
        };
    }

    private static function getAllCoursesBySemesterAndSubject(Application $app) {
        return function ($semester, $subject) use ($app) {
            $response = $app->response;

            $sectionRepository = SectionRepository::autowire();

            $courses = $sectionRepository->findAllBySemesterAndSubject($semester, $subject);

            if (empty($courses)) {
                throw new Exception('Course not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($courses, JSON_PRETTY_PRINT));
        };
    }
}
