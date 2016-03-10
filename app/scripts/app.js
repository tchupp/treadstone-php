'use strict';

angular.module('treadstoneApp', [
        'ngAnimate', 'ngCookies', 'ngResource', 'ngRoute', 'ngSanitize', 'ngTouch', 'LocalStorageModule'
    ])
    .run(function ($rootScope, $window, Principal, Auth, Router) {

        $rootScope.$on('$routeChangeStart', function (event, next) {
            $rootScope.nextRouteName = next.$$route.originalPath;
            $rootScope.nextRouteData = next.$$route.data;
            $rootScope.nextRouteParams = next.params;

            Auth.authorize();
        });

        $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
            var pageTitle = 'TreadCourse';

            if (Principal.isAuthenticated() && $rootScope.previousRouteName) {
                $rootScope.previousRouteName = previous.$$route.originalPath;
                $rootScope.previousRouteData = previous.$$route.data;
                $rootScope.previousRouteParams = previous.params;
            }

            var currentRoute = current.$$route;
            if (currentRoute.data && currentRoute.data.pageTitle) {
                pageTitle = currentRoute.data.pageTitle + ' | ' + pageTitle;
            }
            $window.document.title = pageTitle;
        });

        $rootScope.back = function () {
            if (!$rootScope.previousRouteName || $rootScope.previousRouteName === '/activate' || $rootScope.previousRouteName === null) {
                Router.toDashboard();
            } else {
                Router.toPrevious();
            }
        };
    })
    .config(function ($routeProvider, $httpProvider) {
        $routeProvider.otherwise({redirectTo: '/landing'});

        $httpProvider.interceptors.push('ErrorInterceptor');
        $httpProvider.interceptors.push('AuthExpiredInterceptor');
        $httpProvider.interceptors.push('AuthInterceptor');
    });
