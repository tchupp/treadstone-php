'use strict';

angular.module('treadstoneApp', [
        'ngAnimate', 'ngCookies', 'ngResource', 'ngRoute', 'ngSanitize', 'ngTouch', 'LocalStorageModule'
    ])
    .run(function ($rootScope, $window, Principal, Auth) {

        $rootScope.$on('$routeChangeStart', function (event, next, current) {
            $rootScope.nextRoute = next.$$route;
            $rootScope.currentRoute = current;

            if (Principal.isIdentityResolved()) {
                Auth.authorize();
            }
        });

        $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
            var pageTitle = 'TreadCourse';

            if (current.$$route.data && current.$$route.data.pageTitle) {
                pageTitle += ' | ' + current.$$route.data.pageTitle;
            }
            $window.document.title = pageTitle;
        });
    })
    .config(function ($routeProvider, $httpProvider) {

        $routeProvider.otherwise({redirectTo: '/'});

        $httpProvider.interceptors.push('AuthExpiredInterceptor');
        $httpProvider.interceptors.push('AuthInterceptor');
    });
