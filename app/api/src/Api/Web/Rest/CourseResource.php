<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Database\SectionRepository;
use Api\Database\SemesterRepository;
use Api\Database\SubjectRepository;
use Exception;

class CourseResource {

    public static function registerApi(Application $app) {
        $app->get('/semesters', self::getAllSemesters($app));
        $app->get('/semesters/:semester', self::getOneSemester($app));
        $app->get('/semesters/:semester/subjects', self::getAllSubjectsBySemester($app));
        $app->get('/semesters/:semester/subjects/:subject', self::getOneSubjectBySemester($app));
        $app->get('/semesters/:semester/subjects/:subject/sections', self::getAllSectionsBySubjectAndSemester($app));
        $app->get('/semesters/:semester/subjects/:subject/sections/:number', self::getOneSectionBySubjectAndSemester($app));
    }

    public static function documentation() {
        $semesterSchema = ['code' => 'string', 'name' => 'string'];
        $subjectSchema = ['subjectCode'  => 'string', 'subjectName' => 'string',
                          'semesterCode' => 'string', 'semesterName' => 'string'];
        $sectionSchema = ['semester'      => ['code' => 'string', 'name' => 'string'],
                          'subject'       => ['code' => 'string', 'name' => 'string'],
                          'sectionNumber' => 'int',
                          'times'         => [
                              ['day'       => 'string',
                               'startTime' => 'time',
                               'endTime'   => 'time']
                          ]];
        $errorSchema = ['status' => 'int', 'statusText' => 'string', 'description' => 'string', 'path' => 'string'];

        $docs[] = ['uri'       => '/semesters', 'method' => 'GET', 'roles' => ['ROLE_USER'],
                   'responses' => [
                       ['status' => 200,
                        'body'   => ['string' => [$semesterSchema]]],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/semesters/:semester', 'method' => 'GET', 'roles' => ['ROLE_USER'],
                   'responses' => [
                       ['status' => 200,
                        'body'   => $semesterSchema],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/semesters/:semester/subjects', 'method' => 'GET', 'roles' => ['ROLE_USER'],
                   'responses' => [
                       ['status' => 200,
                        'body'   => [$subjectSchema]],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/semesters/:semester/subjects/:subject', 'method' => 'GET', 'roles' => ['ROLE_USER'],
                   'responses' => [
                       ['status' => 200,
                        'body'   => $subjectSchema],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/semesters/:semester/subjects/:subject/sections', 'method' => 'GET', 'roles' => ['ROLE_USER'],
                   'responses' => [
                       ['status' => 200,
                        'body'   => [$sectionSchema]],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/semesters/:semester/subjects/:subject/sections/:number', 'method' => 'GET', 'roles' => ['ROLE_USER'],
                   'responses' => [
                       ['status' => 200,
                        'body'   => $sectionSchema],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        return $docs;
    }

    private static function getAllSemesters(Application $app) {
        return function () use ($app) {
            $response = $app->response;

            $semesterRepository = SemesterRepository::autowire();

            $semesters = $semesterRepository->findAll();

            if (empty($semesters)) {
                throw new Exception('Semesters not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($semesters, JSON_PRETTY_PRINT));
        };
    }

    private static function getOneSemester(Application $app) {
        return function ($semester) use ($app) {
            $response = $app->response;

            $semesterRepository = SemesterRepository::autowire();

            $semesters = $semesterRepository->findOneByCode($semester);

            if (empty($semesters)) {
                throw new Exception('Semester not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($semesters, JSON_PRETTY_PRINT));
        };
    }

    private static function getAllSubjectsBySemester(Application $app) {
        return function ($semester) use ($app) {
            $response = $app->response;

            $subjectRepository = SubjectRepository::autowire();

            $subjects = $subjectRepository->findAllBySemester($semester);

            if (empty($subjects)) {
                throw new Exception('Subjects not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($subjects, JSON_PRETTY_PRINT));
        };
    }

    private static function getOneSubjectBySemester(Application $app) {
        return function ($semester, $subject) use ($app) {
            $response = $app->response;

            $subjectRepository = SubjectRepository::autowire();

            $subjects = $subjectRepository->findOneBySemesterAndSubject($semester, $subject);

            if (empty($subjects)) {
                throw new Exception('Subject not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($subjects, JSON_PRETTY_PRINT));
        };
    }

    private static function getAllSectionsBySubjectAndSemester(Application $app) {
        return function ($semester, $subject) use ($app) {
            $response = $app->response;

            $sectionRepository = SectionRepository::autowire();

            $sections = $sectionRepository->findAllBySemesterAndSubject($semester, $subject);

            if (empty($sections)) {
                throw new Exception('Sections not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($sections, JSON_PRETTY_PRINT));
        };
    }

    private static function getOneSectionBySubjectAndSemester(Application $app) {
        return function ($semester, $subject, $number) use ($app) {
            $response = $app->response;

            $sectionRepository = SectionRepository::autowire();

            $section = $sectionRepository->findOneBySectionNumberAndSemesterAndSubject($semester, $subject, $number);

            if (empty($section)) {
                throw new Exception('Section not found', 404);
            }
            $response->setStatus(200);
            $response->setBody(json_encode($section, JSON_PRETTY_PRINT));
        };
    }
}
