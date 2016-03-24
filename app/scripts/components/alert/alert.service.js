'use strict';

angular.module('treadstoneApp')
    .factory('AlertService', function ($timeout) {
        var alertId = 0;
        var alerts = [];
        var timeout = 5000;

        function closeAlertByIndex(index) {
            return alerts.splice(index, 1);
        }

        function closeAlert(id) {
            return closeAlertByIndex(alerts.indexOf(id));
        }

        function clear() {
            alerts = [];
        }

        function get() {
            return alerts;
        }

        function addAlert(options) {
            options.alertId = alertId++;
            options.timeout = timeout;

            factory(options);

            $timeout(function () {
                closeAlert(options.alertId);
            }, options.timeout);
        }

        function factory(options) {
            return alerts.push({
                type: options.type,
                msg: options.msg,
                id: options.alertId,
                timeout: options.timeout,
                close: function () {
                    return service.closeAlert(this);
                }
            });
        }

        function success(msg) {
            addAlert({
                type: 'success',
                msg: msg
            });
        }

        function error(msg) {
            addAlert({
                type: 'danger',
                msg: msg
            });
        }

        function warning(msg) {
            addAlert({
                type: 'warning',
                msg: msg
            });
        }

        function info(msg) {
            addAlert({
                type: 'info',
                msg: msg
            });
        }

        var service = {
            clear: clear,
            get: get,
            success: success,
            error: error,
            warning: warning,
            info: info,
            add: addAlert,
            factory: factory,
            closeAlert: closeAlert,
            closeAlertByIndex: closeAlertByIndex
        };

        return service;
    });
