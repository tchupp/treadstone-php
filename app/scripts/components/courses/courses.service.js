'use strict';

angular.module('treadstoneApp')
    .factory('Semester', function Semester($resource) {
        return $resource('api/semesters/:semester', {}, {
            'query': {method: 'GET'}
        });
    })
    .factory('Subject', function Courses($resource) {
        return $resource('api/semesters/:semester/subjects/:subject', {}, {
            'query': {method: 'GET', isArray: true}
        });
    })
    .factory('Section', function Courses($resource) {
        return $resource('api/semesters/:semester/subjects/:subject/sections/:number', {}, {
            'query': {method: 'GET', isArray: true}
        });
    });
