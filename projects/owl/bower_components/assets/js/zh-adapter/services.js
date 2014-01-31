
var ZeedhiServices = angular.module("ZeedhiServices", []);

var registerInstance = function(serviceName, instance) {
    ZeedhiServices.service(serviceName, function() {
        return instance;
    });
};

registerInstance("requestFactory", new RequestFactory());

registerInstance("maskEngine", new MaskEngine());

registerInstance("validateEngine", new ValidationEngine());

registerInstance("i18nEngine", new I18nEngine());

registerInstance("aclEngine", new AclEngine());

registerInstance("chartEngine", new ChartEngine());

ZeedhiServices.service("eventHandler", function(restEngine, requestFactory) {
    return new EventHandler(restEngine, requestFactory);
});

ZeedhiServices.service("templateManager", function($window) {

    var Manager = function($window) {

        var url = localStorage.getItem("ZeedhiServiceURI");

        this.showMenu = $window.showMenu;
        this.templateUrl = $window.templateUrl;
        this.metadataUrl = $window.metadataUrl;
        this.serviceUrl = url || $window.serviceUrl;

        var dataDeferred = $.Deferred();

        this.updateTemplate = function() {
            return this.onUpdate();
        };

        this.menuVisible = function(visible) {
            this.project.menuVisible = visible;
            this.updateTemplate();
        };

        this.getDataSources = function(){
            return dataDeferred;
        };

        this.setDataSources = function(data){
            this.data = data;
            dataDeferred.resolve(data);
        };

    };
    return new Manager($window);
});

function Memoizer(maxSize){
    var memo = [];
    
    this.add = function(key, value){
        if(memo.length > maxSize){
            memo.shift();
        }
        memo[key] = value;
    };
    
    this.has = function(key){
        return !!memo[key];
    };
    
    this.get = function(key){
        return memo[key];
    };
}

ZeedhiServices.service("Memoizer", function() {

    function M(){
        this.create = function(maxSize){ 
            return new Memoizer(maxSize);
        };
    }
    return new M();
});

ZeedhiServices.service("eventEngine", function(eventFactory, eventHandler, templateManager) {
    return new EventEngine(eventFactory, eventHandler, templateManager);
});

ZeedhiServices.service('restEngine', function($http, templateManager) {
    return new RestEngine($http, templateManager.serviceUrl, templateManager.metadataUrl);
});

ZeedhiServices.service('requestEngine', function(restEngine) {
    return new RequestEngine(restEngine);
});

ZeedhiServices.service('requestDatasoureceEngine', function(requestEngine, requestFactory) {
    return new RequestDatasoureceEngine(requestEngine, requestFactory);
});

ZeedhiServices.service('localStorageEngine',
        function(requestDatasoureceEngine, requestFactory, requestEngine, templateManager) {
            return new LocalStorageEngine(requestDatasoureceEngine, requestFactory, requestEngine, templateManager);
        });

ZeedhiServices.service('SQLStorageStrategy', function(requestDatasoureceEngine) {
    return new SQLStorageStrategy(requestDatasoureceEngine);
});

ZeedhiServices.service('metaDataFactory', function(localStorageEngine, requestDatasoureceEngine, SQLStorageStrategy, restEngine) {
    return new MetaDataFactory(localStorageEngine, requestDatasoureceEngine, SQLStorageStrategy, restEngine);
});

ZeedhiServices.service('renderEngine', function(restEngine, i18nEngine, metaDataFactory, templateManager, $timeout) {
    return new RenderEngine(restEngine, i18nEngine, metaDataFactory, templateManager, $timeout);
});

ZeedhiServices.service('wizardEngine',
        function(renderEngine, templateManager, metaDataFactory, eventFactory, eventHandler) {
            return new WizardEngine(renderEngine, templateManager, metaDataFactory, eventFactory, eventHandler);
});

ZeedhiServices.service("ApplicationContext", function() {
    return {};
});

ZeedhiServices.service("eventFactory", function(ApplicationContext) {
    return new EventFactory(ApplicationContext);
});

ZeedhiServices.service("ContextRegister", function(ApplicationContext, $injector){
    function ContextRegister(){        
        function instantiate(service){
            return $injector.instantiate(service, ApplicationContext);
        }
        this.register = function(name, service){
            var serviceInstance = typeof(service) !== 'function' ? service : instantiate(service);
            ApplicationContext[name] = serviceInstance;
        };
    }
    return new ContextRegister();
});

ZeedhiServices.service("RepositoryFactory", function(SQLStorageStrategy, localStorageEngine, restEngine, templateManager){
    return new RepositoryFactory(SQLStorageStrategy, localStorageEngine, restEngine, templateManager);
});

ZeedhiServices.service("Query", function(){
    return new QueryBuilder();
});
    
window.Configuration = function Configuration(config){
    ZeedhiServices.run(config);
}
