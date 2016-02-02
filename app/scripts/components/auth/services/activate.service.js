'use strict';

angular.module('treadstoneApp')
  .factory('Activate', function ($resource) {
    return $resource('api/activate', {}, {
      'get': {method: 'GET', params: {}, isArray: false}
    });
  });
