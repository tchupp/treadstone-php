'use strict';

/**
 * @ngdoc overview
 * @name treadstoneApp
 * @description
 * # treadstoneApp
 *
 * Main module of the application.
 */
angular.module('treadstoneApp', [
        'ngAnimate',
        'ngCookies',
        'ngResource',
        'ngRoute',
        'ngSanitize',
        'ngTouch',
        'LocalStorageModule'
    ])
    .config(function ($routeProvider, $httpProvider) {
        $routeProvider.otherwise({
            redirectTo: '/'
        });

        $httpProvider.interceptors.push('AuthInterceptor');
    });
