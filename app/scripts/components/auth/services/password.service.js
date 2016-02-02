'use strict';

angular.module('treadstoneApp')
  .factory('Password', function ($resource) {
    return $resource('api/account/change_password', {}, {});
  });

angular.module('treadstoneApp')
  .factory('PasswordResetInit', function ($resource) {
    return $resource('api/account/reset_password/init', {}, {});
  });

angular.module('treadstoneApp')
  .factory('PasswordResetFinish', function ($resource) {
    return $resource('api/account/reset_password/finish', {}, {});
  });
