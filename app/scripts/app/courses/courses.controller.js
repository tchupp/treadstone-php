'use strict';

angular.module('treadstoneApp')
    .controller('CoursesController', function ($scope, Semester, Subject, Section) {
        $scope.selectedSchool = 'Michigan State University';
        $scope.selectedSemester = null;
        $scope.selectedSubject = null;
        $scope.selectedSection = null;

        $scope.semesters = [];
        $scope.subjects = [];
        $scope.sections = [];
        $scope.loading = true;

        function loadAllSemesters() {
            Semester.query(function (data) {
                $scope.loading = false;
                $scope.semesters = data;
            }, function () {
                $scope.loading = false;
            });
        }

        loadAllSemesters();

        $scope.clearSemester = function () {
            $scope.selectedSemester = null;
            $scope.subjects = [];

            $scope.selectedSection = null;

            $scope.selectedSubject = null;
            $scope.sections = [];
        };

        $scope.clearSubject = function () {
            $scope.selectedSection = null;

            $scope.selectedSubject = null;
            $scope.sections = [];
        };

        $scope.clearSection = function () {
            $scope.selectedSection = null;
        };

        $scope.selectSemester = function (semester) {
            $scope.clearSemester();

            $scope.selectedSemester = semester.code;

            Subject.query({semester: $scope.selectedSemester}, function (data) {
                $scope.loading = false;
                $scope.subjects = data;
            }, function () {
                $scope.loading = false;
            });
        };

        $scope.selectSubject = function (subject) {
            $scope.selectedSubject = null;
            $scope.selectedSection = null;

            $scope.selectedSubject = subject.subjectCode;

            Section.query({semester: $scope.selectedSemester, subject: $scope.selectedSubject}, function (data) {
                $scope.loading = false;
                $scope.sections = data;
            }, function () {
                $scope.loading = false;
            });
        };

        $scope.selectSection = function (section) {
            $scope.selectedSection = null;

            $scope.selectedSection = section;
        };
    });
