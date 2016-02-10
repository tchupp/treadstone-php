'use strict';

angular.module('treadstoneApp')
    .factory('Courses', function Courses($resource) {
        return $resource('api/courses/:semester/:subject', {}, {
            'query': {method: 'GET', isArray: true}
        });
    });
