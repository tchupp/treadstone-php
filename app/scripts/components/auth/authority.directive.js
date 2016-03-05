'use strict';

angular.module('treadstoneApp')
    .directive('hasAuthority', ['Principal', function (Principal) {
        return {
            restrict: 'A',
            link: function (scope, element, attribute) {
                var setVisible = function () {
                        element.removeClass('hidden');
                    }, setHidden = function () {
                        element.addClass('hidden');
                    }, defineVisibility = function (reset) {
                        if (reset) {
                            setVisible();
                        }

                        if (Principal.hasAuthority(role)) {
                            setVisible();
                        } else {
                            setHidden();
                        }
                    },
                    role = attribute.hasAuthority.replace(/\s+/g, '');

                if (role.length > 0) {
                    defineVisibility(true);
                }
            }
        };
    }]);
