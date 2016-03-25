'use strict';

angular.module('treadstoneApp')
    .factory('Auth', function Auth($rootScope, $q, Principal, Account, Register, Activate, AuthServerProvider, Password, PasswordResetInit, PasswordResetFinish, Router) {
        return {
            login: function (credentials, callback) {
                var cb = callback || angular.noop;
                var deferred = $q.defer();

                credentials = btoa(credentials);

                AuthServerProvider.login(credentials).then(function (data) {
                    Principal.identity(true).then(function () {
                        deferred.resolve(data);
                    });

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
                Principal.authenticate(null);
            },
            authorize: function (force) {
                return Principal.identity(force).then(function () {
                    var isAuthenticated = Principal.isAuthenticated();

                    var toRegister = $rootScope.nextRouteName === '/register';
                    var toActivate = $rootScope.nextRouteName === '/activate';
                    var toLanding = $rootScope.nextRouteName === '/landing';
                    if (isAuthenticated && (toRegister || toActivate || toLanding)) {
                        Router.toDashboard();
                    }

                    var nextRouteData = $rootScope.nextRouteData;
                    if (nextRouteData && nextRouteData.roles &&
                        nextRouteData.roles.length > 0 && !Principal.hasAnyAuthority(nextRouteData.roles)) {
                        if (isAuthenticated) {
                            Router.toAccessDenied();
                        } else {
                            $rootScope.previousRouteName = $rootScope.nextRouteName;
                            $rootScope.previousRouteData = $rootScope.nextRouteData;
                            $rootScope.previousRouteParams = $rootScope.nextRouteParams;

                            Router.toLanding();
                        }
                    }
                });
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
                    return cb(err);
                }).$promise;
            },
            resetPasswordFinish: function (keyAndPassword, callback) {
                var cb = callback || angular.noop;

                return PasswordResetFinish.save(keyAndPassword, function () {
                    return cb();
                }, function (err) {
                    return cb(err);
                }).$promise;
            }
        };
    });
