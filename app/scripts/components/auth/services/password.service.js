'use strict';

angular.module('treadstoneApp')
  .factory('Password', function ($resource) {
    return $resource('api/account/change_password', {}, {});
  });
