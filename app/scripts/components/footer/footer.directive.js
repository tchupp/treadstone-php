'use strict';

angular.module('treadstoneApp')
    .directive('tsFooter', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'scripts/components/footer/footer.html'
        };
    });
