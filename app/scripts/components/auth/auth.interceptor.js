'use strict';

angular.module('treadstoneApp')
    .factory('AuthInterceptor', function ($injector) {
        return {
            request: function (config) {
                config.headers = config.headers || {};

                var AuthServerProvider = $injector.get('AuthServerProvider');
                if (AuthServerProvider.hasValidToken()) {
                    config.headers['x-auth-token'] = AuthServerProvider.getToken().authToken;
                }

                return config;
            }
        };
    })
    .factory('AuthExpiredInterceptor', function ($injector, $q) {
        return {
            responseError: function (response) {
                if (response.status === 401 &&
                    (response.data.description === 'Authentication Failed' || response.data.description === 'Authentication Missing')) {
                    var Auth = $injector.get('Auth');
                    Auth.logout();

                    var Principal = $injector.get('Principal');
                    if (Principal.isAuthenticated()) {
                        Auth.authorize(true);
                    }
                }
                return $q.reject(response);
            }
        };
    });
