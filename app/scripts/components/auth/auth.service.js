'use strict';

angular.module('treadstoneApp')
  .factory('Auth', function Auth($q, Account, Register, Activate, AuthServerProvider, Password, PasswordResetInit, PasswordResetFinish) {
    return {
      login: function (credentials, callback) {
        var cb = callback || angular.noop;
        var deferred = $q.defer();

        AuthServerProvider.login(credentials).then(function (data) {
          // store the login in account info somehow!
          return cb();
        }).catch(function (err) {
          this.logout();
          deferred.reject(err);
          return cb(err);
        }.bind(this));

        return deferred.promise;
      },
      logout: function () {
        AuthServerProvider.logout();
      },
      createAccount: function (account, callback) {
        var cb = callback || angular.noop;

        return Register.save(account,
          function () {
            return cb(account);
          },
          function (err) {
            this.logout();
            return cb(err);
          }.bind(this)).$promise;
      },
      updateAccount: function (account, callback) {
        var cb = callback || angular.noop;

        return Account.save(account,
          function () {
            return cb(account);
          },
          function (err) {
            return cb(err);
          }.bind(this)).$promise;
      },
      activateAccount: function (key, callback) {
        var cb = callback || angular.noop;

        return Activate.get(key,
          function (response) {
            return cb(response);
          },
          function (err) {
            return cb(err);
          }.bind(this)).$promise;
      },
      changePassword: function (newPassword, callback) {
        var cb = callback || angular.noop;

        return Password.save(newPassword, function () {
          return cb();
        }, function (err) {
          return cb(err);
        }).$promise;
      },
      resetPasswordInit: function (email, callback) {
        var cb = callback || angular.noop;

        return PasswordResetInit.save(email, function () {
          return cb();
        }, function (err) {
          return cb(err)
        }).$promise;
      },
      resetPasswordFinish: function (keyAndPassword, callback) {
        var cb = callback || angular.noop;

        return PasswordResetFinish.save(keyAndPassword, function () {
          return cb();
        }, function (err) {
          return cb(err)
        }).$promise;
      }
    };
  });
