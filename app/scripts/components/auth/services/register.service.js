'use strict';

angular.module('treadstoneApp')
  .factory('Register', function ($resource) {
    return $resource('api/register', {}, {
    });
  });


