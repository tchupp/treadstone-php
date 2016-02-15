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
    });
