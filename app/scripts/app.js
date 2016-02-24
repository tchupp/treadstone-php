'use strict';

angular.module('treadstoneApp', [
        'ngAnimate', 'ngCookies', 'ngResource', 'ngRoute', 'ngSanitize', 'ngTouch', 'LocalStorageModule'
    ])
    .run(function ($rootScope, $location, $window, Principal, Auth) {

        $rootScope.$on('$routeChangeStart', function (event, next) {
            $rootScope.nextRoute = next.$$route;
            $rootScope.nextRouteParams = next.params;

            Auth.authorize();
        });

        $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
            var pageTitle = 'TreadCourse';

            if (Principal.isAuthenticated() && $rootScope.previousRoute) {
                $rootScope.previousRoute = previous.$$route;
                $rootScope.previousRouteParams = previous.params;
            }

            if (current.$$route.data && current.$$route.data.pageTitle) {
                pageTitle = current.$$route.data.pageTitle + ' | ' + pageTitle;
            }
            $window.document.title = pageTitle;
        });

        $rootScope.back = function () {
            if ($rootScope.previousRoute.originalPath === '/activate' || $rootScope.previousRoute.originalPath === null) {
                $location.path('/');
            } else {
                $location.path($rootScope.previousRoute.originalPath);
            }
        };
    })
    .config(function ($routeProvider, $httpProvider) {

        $routeProvider.otherwise({redirectTo: '/'});


        $httpProvider.interceptors.push('ErrorInterceptor');
        $httpProvider.interceptors.push('AuthExpiredInterceptor');
        $httpProvider.interceptors.push('AuthInterceptor');
    });
