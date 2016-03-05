'use strict';

angular.module('treadstoneApp')
    .config(function ($routeProvider) {
        $routeProvider.when('/docs', {
            templateUrl: 'scripts/app/docs/docs.html',
            controller: 'DocsController',
            data: {
                roles: ['ROLE_DEV'],
                pageTitle: 'Docs'
            }
        });
    });
