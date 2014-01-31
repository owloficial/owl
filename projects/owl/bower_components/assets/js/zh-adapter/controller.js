var ZeedhiController = angular.module('ZeedhiController', []);
ZeedhiController.controller('ZeedhiMain', function($scope, $timeout, restEngine, eventHandler,
    eventFactory, eventEngine, maskEngine, localStorageEngine, metaDataFactory, renderEngine,
    wizardEngine, templateManager, SQLStorageStrategy, $rootScope) {
    
    window.localStorageEngine = localStorageEngine;
    window.SQLStorageStrategy = SQLStorageStrategy;
    $scope.templateManager = templateManager;

    eventEngine.bindEvents(templateManager, [{
        name : "onUpdate",
        code : function(){
            var phase = $scope.$root.$$phase;

            if(!(phase == '$apply' || phase == '$digest') && !$scope.applyInProgressZh){
                $scope.applyInProgressZh = true;
                $scope.$apply();
                $scope.applyInProgressZh = false ;
            }
        },
        id : new Date().getTime()
    }]);

            $scope.lang = window.lang || "pt_br";

            request = function(url,callBack){
                restEngine.requestMetaData(url,{},callBack, "GET");
            },
            $scope.loadData = function(){
                request("../json/data.json",function(data){
                    $scope.data = data;
                    templateManager.setDataSources(data);
                    var callback = function(data){
                        SQLStorageStrategy.registerDataSource(data);
                    };
                    for(var i in data){
                        if(data[i].sql){
                            var name = data[i].name.indexOf("/") !== -1 ? data[i].name.split("/")[1] : data[i].name;
                            restEngine.requestMetaData("../json/datasources/" + name + ".json", {}, callback, "GET");
                        }
                    }
                    $scope.loadMetadata();
                });
            },
            $scope.loadMetadata = function(windowName){
                request("../json/containers.json",function(windows){
                    templateManager.containers = windows;
                    templateManager.project    = windows.zeedhi_project;
                    templateManager.project.historyItems = [],
                    $scope.openWindow(templateManager.project.mainWindow);
                });
            },
            $scope.loadI18n = function() {
                request("../json/words.json",function(words){
                    $scope.words = words;
                    $rootScope.$on("__words.change", function(e, word){
                        $scope.lang = word;
                        templateManager.updateTemplate();
                    });
                });
            },
            $scope.loadRouteList = function() {
// developer/json/routes.json
                request("../json/routes.json", function(routes) {
                    console.log("load route is not implemented yet.");
                });
            },
            $scope.groupBy = Util.groupBy,
            $scope.loadAcl = function() {
                request("../json/acl.json", function(acl) {
                    $scope.aclRules = acl;
                });
            },
            $scope.setFormData = function(widget, index){
                renderEngine.setCurrentRow(widget, index);
            },
            $scope.back = function(){
                templateManager.project.historyItems.pop();
                var last = templateManager.project.historyItems.pop();
                $scope.openWindow(last);
            },
            $scope.openWindow = function(containerName, isWizard) {
                var process = function(container){
                    // If the container to open isn't accessible (its menu is set as readOnly).
                    if (container.isAccessible === false) {
                        return;
                    }
                    if(container.stages) {
                        wizardEngine.startWizard(container);
                    } else {
                        renderEngine.changeContainer(containerName);
                    }
                    $scope.addHistoryItem(containerName);
                    $timeout(function(){
                        $scope.resizeWindowElements();    
                    }, 200);
                };
                renderEngine.getContainer(containerName).then(process);

            },
            $scope.addHistoryItem = function(containerName){
                if(jQuery.inArray(containerName, templateManager.project.historyItems) == -1){
                    if(templateManager.project.historyItems.length == 3){
                        templateManager.project.historyItems.shift();
                    }
                    templateManager.project.historyItems.push(containerName);
                }
            },
            $scope.newRow = function(widget){
                widget.newRow();
            },
            $scope.formatMoney = function(money){
                return maskEngine.currencyFormat(money) || "0.00";
            },
            $scope.actionClick = function(action, widget){
                $scope.currentWidget = widget;
                templateManager.currentWidget = widget;
                var events = action.events;
                if (events) {
                    eventEngine.bindEvents(action,events, function(){return widget.currentRow;}, function(){return widget.currentRow;});
                }
                action.click();
            },
            $scope.showProjectMenu = function(show, windowName){
                templateManager.project.showMenu = show;
                $scope.openWindow(windowName);
                templateManager.updateTemplate();
            },
            $scope.next = function() {
                wizardEngine.next(eventFactory, eventHandler);
            },
            $scope.previous = function() {
                wizardEngine.previous(eventFactory, eventHandler);
            },
            $scope.setLocalVar = function(name, value, callback) {
                return localStorageEngine.setLocalVar(name, value, callback);
            },
            $scope.getLocalVar = function(name, value, callback) {
                return  localStorageEngine.getLocalVar(name, value, callback);
            },
            $scope.resizeWindowElements = function(){
                renderEngine.resizeWindowElements();
            },
            $scope.renderInfiniteScroller = function(element, infiniteCallback){
                renderEngine.renderInfiniteScroller(element, infiniteCallback);
            },
            $scope.messagesToShow = [];
            $scope.addMessagesToShow = function(message){
                $scope.messagesToShow.push(message);
            };
            $scope.alert = function(message){
                window.alert(message);
            };
            $scope.abs = function(number){
                return Math.abs(number);
            };
            $scope.setUserIdentifier = function() {
                var userId = localStorage.getItem("USER_ID");
                if (!userId) {
                    userId = Math.random().toString(36).substr(2);
                    localStorage.setItem("USER_ID", userId);
                }
            };
// #####################################################
// Temporary, used just to test...
            $scope.userId = "00001";
// #####################################################
        $scope.loadAcl();
        $scope.loadI18n();
        //$scope.loadMetadata();
        $scope.loadData();
        $scope.setUserIdentifier();        
        //$scope.loadRouteList();
        window.showProjectMenu = $scope.showProjectMenu;
        window.openWindow = $scope.openWindow;
        window.next = $scope.next;
        window.localStorageEngine = localStorageEngine;
        
        window.setLocalVar = $scope.setLocalVar;
        window.getLocalVar = $scope.getLocalVar;
        window.metaDataFactory = metaDataFactory;
        window.filterDataSource = function(name, filters, callback){
            var dataSource = $scope.data[name];
            dataSource     = metaDataFactory.factoryDataSource(dataSource);
            dataSource.filter(filters, callback);
        };

        /*window.alert = function(message){
            $scope.addMessagesToShow({message: message, type: "sucess"});
        };*/

});
