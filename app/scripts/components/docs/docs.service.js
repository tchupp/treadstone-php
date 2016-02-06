'use strict';

angular.module('treadstoneApp')
    .factory('Docs', function Docs($resource) {
        return $resource('api/docs', {}, {
            'query': {method: 'GET', isArray: false}
        });
    });

