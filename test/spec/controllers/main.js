'use strict';

describe('Controller: MainCtrl', function () {

    // load the controller's module
    beforeEach(module('treadstoneApp'));

    var scope;

    // Initialize the controller and a mock scope
    beforeEach(inject(function ($controller, $rootScope) {
        scope = $rootScope.$new();
        $controller('MainCtrl', {
            $scope: scope
        });
    }));
});
