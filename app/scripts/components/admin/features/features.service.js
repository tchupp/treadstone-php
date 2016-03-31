'use strict';

angular.module('treadstoneApp')
    .factory('Features', function Features($resource) {
        return $resource('api/features/:id', {}, {
            'query': {method: 'GET', isArray: true},
            'get': {
                method: 'GET',
                transformResponse: function (data) {
                    data = angular.fromJson(data);
                    return data;
                }
            }
        });
    });
