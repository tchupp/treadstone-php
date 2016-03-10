'use strict';

angular.module('treadstoneApp')
    .factory('Router', function ($rootScope, $location) {
        return {
            toLanding: function () {
                $location.path('/landing');
            },
            toDashboard: function () {
                $location.path('/dashboard');
            },
            toAccessDenied: function () {
                $location.path('/accessdenied');
            },
            toPrevious: function () {
                $location.path($rootScope.previousRouteName);
            }
        };
    });
