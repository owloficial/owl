var ZeedhiApp = angular.module("ZeedhiApp", [
    'ui.select2',
    'ZeedhiServices',
    'ZeedhiDirectives',
    'ZeedhiDiretiveDate',
    'ZeedhiFilters',
    'ZeedhiChart',
    'ZeedhiReference',
    'ZeedhiEvents',
    'ZeedhiInterations',
    'ZeedhiReference',
    'ZeedhiController',
    'ZeedhiTodo'
]); 

/*
 * CORS support
 */
ZeedhiApp.config(function($httpProvider) {
      $httpProvider.defaults.useXDomain = true;
      delete $httpProvider.defaults.headers.common['X-Requested-With'];
});

ZeedhiApp.run(function($injector){
    if(window.zhControllers){
        window.zhControllers.forEach(function(controller){
            ZeedhiApp.controller(controller.name,function(){
                var serviceInstance = $injector.instantiate(controller.controller);
                return serviceInstance;
            });
        });
    }
});

ZeedhiApp.run(function(templateManager, restEngine){
    window.__changeURI = function(uri){
        templateManager.serviceUrl = uri;
        restEngine.setURI(uri);
        localStorage.setItem("ZeedhiServiceURI", uri);
        templateManager.updateTemplate();
    };
    window.__resetURI = function(){
        localStorage.removeItem("ZeedhiServiceURI");
        templateManager.serviceUrl = window.serviceUrl;
        restEngine.setURI(window.serviceUrl);
        templateManager.updateTemplate();
    };
});

(function(){
    var rootScope;

    ZeedhiApp.run(function($rootScope) {
        rootScope = $rootScope;
    });

    ZeedhiApp.config(function($httpProvider) {   
        $httpProvider.responseInterceptors.push('loader');
        var spinnerFunction = function(data, headersGetter) {
            rootScope.$broadcast("request.start");
            return data;
        };
        $httpProvider.defaults.transformRequest.push(spinnerFunction);
    });

    ZeedhiApp.factory('loader', function($q) {
        return function(promise) {
            return promise.then(function(response) {
                rootScope.$broadcast("request.stop");
                return response;

            }, function(response) {
                rootScope.$broadcast("request.stop");
                return $q.reject(response);
            });
        };
    });
    
})();