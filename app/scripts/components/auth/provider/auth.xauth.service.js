'use strict';

angular.module('treadstoneApp')
    .factory('AuthServerProvider', function loginService($http, localStorageService) {
        var xAuthToken = 'xauthtoken';
        return {
            login: function (credentials) {
                return $http.post('api/authenticate', credentials, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).success(function (response) {
                    localStorageService.set(xAuthToken, response);
                    return response;
                });
            },
            logout: function () {
                localStorageService.clearAll();
            },
            getToken: function () {
                return localStorageService.get(xAuthToken);
            },
            hasValidToken: function () {
                var token = this.getToken();
                var time = new Date().getTime() / 1000;

                return token && token.expires && (token.expires > time);
            }
        };
    });
