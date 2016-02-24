'use strict';

angular.module('treadstoneApp')
    .factory('ErrorInterceptor', function ($q, $rootScope) {
        return {
            'responseError': function (response) {
                if (!(response.status === 401 && response.data.path.indexOf('/account') === 0)) {
                    $rootScope.$emit('treadstoneApp.httpError', response);
                }
                return $q.reject(response);
            }
        };
    });
