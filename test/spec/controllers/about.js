'use strict';

describe('Controller: AboutCtrl', function () {

    // load the controller's module
    beforeEach(module('treadstoneApp'));

    var scope, Features;

    // Initialize the controller and a mock scope
    beforeEach(inject(function ($controller, $rootScope) {
        scope = $rootScope.$new();

        Features = {
            query: function(suc) {
                suc([
                    {
                        id: 'html5boilerplate',
                        name: 'HTML5 Boilerplate',
                        href: '/api/features/html5-boilerplate'
                    },
                    {
                        id: 'angular',
                        name: 'Angular',
                        href: '/api/features/angular'
                    },
                    {
                        id: 'karma',
                        name: 'Karma',
                        href: '/api/features/karma'
                    }
                ]);
            },
            get: function (data, suc) {
                var features = {
                    html5boilerplate: {
                        name: 'HTML5 Boilerplate',
                        description: 'HTML5 Boilerplate is a professional front-end template' +
                        ' for building fast, robust, and adaptable web apps or sites.'
                    },
                    angular: {
                        name: 'Angular',
                        description: 'AngularJS is a toolset for building the framework most' +
                        ' suited to your application development.'
                    },
                    karma: {
                        name: 'Karma',
                        description: 'Spectacular Test Runner for JavaScript.'
                    }
                };
                suc(features[data.id]);
            }
        };

        $controller('AboutCtrl', {
            $scope: scope,
            Features: Features
        });
    }));

    it('should attach a list of awesomeThings to the scope', function () {
        expect(scope.awesomeThings.length).toBe(3);
    });
});
