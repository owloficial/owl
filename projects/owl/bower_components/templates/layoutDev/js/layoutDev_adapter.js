(function () {
    var ZeedhiLayout = angular.module("ZeedhiLayout", []);
    var restEngine = null;
    var localStorageEngine = null;
    var requestFactory = null;
    var requestEngine = null;
    var i18nEngine    = null;
    ZeedhiLayout.controller("ZeedhiLayoutController", function($scope, $http) {
        restEngine = new RestEngine($http,"");
        requestFactory = new RequestFactory();
        requestEngine  = new RequestEngine(new RestEngine($http,"./public/index.php"));
        localStorageEngine = new LocalStorageEngine();
        i18nEngine = new I18nEngine();
        // $scope.lang = "pt_br";
        $scope.configuring = false;
        $scope.configType = null;
        $scope.configContainer = null;
        $scope.configWidget = null;
        $scope.configField = null;
        $scope.activeWidget = null;
        $scope.activeField = null;
        $scope.layoutData = {};
        $scope.templates = {};
        $scope.waitingWidget = null;
        $scope.elementBase = {
            "field" : {
                "field_id" : null,
                "label" : null,
                "template_id" : 1,
                "seqno" : null,
                "datacolumn_id" : null,
                "newline" : null,
                "default_value" : null,
                "isrequired" : null,
                "widget_id" : null,
                "is_groupby_field" : null,
                "column_template_number" : 12,
                "name" : null,
                "description" : null,
                "help" : null
            },
            "container" : {
                "container_id" : null,
                "name" : "Container",
                "label" : "Container",
                "widgets" : []
            },
            "widget" : {
                "container_widget_id" : null,
                "widget_id" : null,
                "template_id" : null,
                "datasource_id" : null,
                "widget_type" : null,
                "column_template_number" : 12,
                "label" : null,
                "name" : null,
                "description" : null,
                "help" : null,
                "isactive" : null,
                "seqno" : null,
                "session_number" : null,
                "fields" : []
            }
        };

        request = function (url, callBack) {
            restEngine.request(url,{},callBack);
        };
        // $scope.initContainersAndWidgets = function () {
        //     request("./../json/templates.json",function(data){
        //         $scope.templates = data;
        //     });
        // };
        $scope.setContainer = function (templateObj) {
            var containerBase = $scope.buildContainer(templateObj);
            $scope.addContainer(containerBase);
        };
        $scope.buildContainer = function (templateObj) {
            var containerBase  = angular.copy($scope.elementBase.container);
            containerBase.template_id = templateObj.template_id;
            return containerBase;
        };
        $scope.addContainer = function (container) {
            if (!Object.keys($scope.layoutData).length) {
                $scope.layoutData.container = container;
                var widget = angular.copy($scope.elementBase.widget);
                widget.height = 100;
                widget.column_template_number = 12;
                $scope.addWidget(widget);
            } else {
                var oldContainerId = null;
                if ($scope.layoutData.container.container_id) {
                    oldContainerId = $scope.layoutData.container.container_id;
                }

                var widgets = angular.copy($scope.layoutData.container.widgets);
                $scope.layoutData.container = container;
                $scope.layoutData.container.container_id = oldContainerId;
                $scope.layoutData.container.widgets = widgets;
            }
        };
        $scope.configureContainer = function (container) {
            if (!$scope.semaphore) {
                $scope.configContainer = angular.copy(container);
                $scope.activeContainer = container;
                $scope.configType = "container";
                $scope.configuring = true;
            }

            $scope.semaphore = false;
        };
        $scope.setOrientation = function(orientation){
            $scope.configContainer.orientation = orientation;
        };
        $scope.addContainerDivision = function(widgetIndex){
            var orientation = $scope.configContainer.orientation;
            var wid1 = $scope.layoutData.container.widgets[widgetIndex];
            var wid2 = null;
            switch(orientation){
                case 'H':
                    wid1.height = wid1.height /2;
                    wid2 = angular.copy(wid1);
                    break;
                case 'V':
                    wid1.column_template_number = wid1.column_template_number/2;
                    wid2 = angular.copy(wid1);
                    break;
            }
             
            $scope.layoutData.container.widgets.push(wid2);
            $scope.configContainer.orientation = null;
        };
        $scope.closeCotainerConfig = function(){
            $scope.configuring = false;
        };
        $scope.setWidget = function (templateObj) {
            var widgetBase = $scope.buildWidget(templateObj);
            $scope.waitingWidget = widgetBase;
        };
        $scope.selectWidgetLocation = function (widgetIndex) {
            if ($scope.waitingWidget){
                var waitingWidget = angular.copy($scope.waitingWidget);
                var widget = $scope.layoutData.container.widgets[widgetIndex];
                widget.template_id = waitingWidget.template_id;
                widget.seqno = waitingWidget.seqno;
                $scope.waitingWidget = null;
            } else if($scope.configContainer.orientation){
                $scope.addContainerDivision(widgetIndex);
            }
        };
        $scope.buildWidget = function (templateObj) {
            var widgetBase  = angular.copy($scope.elementBase.widget);
            widgetBase.template_id = templateObj.template_id;
            widgetBase.seqno = ($scope.layoutData.container.widgets.length + 1)*10 ;
            return widgetBase;
        };
        $scope.addWidget = function (widget) {
            if(Object.keys($scope.layoutData).length){
                $scope.layoutData.container.widgets.push(widget);
            }

            $scope.configureWidget(widget);
        };

        $scope.removeWidget = function (widget) {
            var widgetPos = $.inArray(widget, $scope.layoutData.container.widgets);
            if(widgetPos !== -1) {
                $scope.layoutData.container.widgets.splice(widgetPos, 1);
            }
        };
        $scope.removeField = function (field) {
            var fieldPos = $.inArray(field, $scope.configWidget.fields);
            if(fieldPos !== -1) {
                $scope.configWidget.fields.splice(fieldPos, 1);
            }
            $scope.configType = "widget";
        };
        $scope.configureWidget = function (widget) {
            if (!$scope.semaphore) {
                $scope.configWidget = angular.copy(widget);
                $scope.activeWidget = widget;
                $scope.configType = "widget";
                $scope.configuring = true;
            }

            $scope.semaphore = false;
        };
        $scope.saveWidgetConfig = function () {
            var widget = $scope.configWidget;
            if (!widget) {
                widget = getWidget($scope.activeField.widget_id);
                $scope.activeWidget = $scope.configWidget = widget;
            }

            angular.extend($scope.activeWidget, widget);
            $scope.configuring = false;
        };
        $scope.addField = function () {
            var widget = $scope.configWidget;
            var field = angular.copy($scope.elementBase.field);
            widget.fields.push(field);
            $scope.configureField(field, widget.datasource_id);
        };
        $scope.configureField = function (field, datasourceId) {
            if (datasourceId === undefined) {
                widget = getWidget(field.widget_id);
                datasourceId = widget.datasource_id;
            }

            $scope.loadDatacolumn(datasourceId);
            $scope.configField = angular.copy(field);

            $scope.activeField = field;
            $scope.configType = "field";
            $scope.configuring = true;

            // This variable is used to avoid the triggering of configureWidget when just the 
            // configureField method must be activated.
            if ($scope.configWidget) {
                $scope.semaphore = true;
            }
        };
        $scope.saveFieldConfig = function () {
            var field = $scope.configField;
            angular.extend($scope.activeField, field);
            // $scope.configType = "widget";
            $scope.configuring = false;
            $scope.saveWidgetConfig();
        };
        $scope.saveLayout = function () {
            var layout = angular.copy($scope.layoutData);
            var request = null;
            if (layout.toUpdate) {
                request = requestFactory.factory({
                    "requestType":"dataSource",
                    "serviceName":"/zeedhi-developer/update",
                    "dataSource":layout,
                    "callBack":function (data) {
                        alert(data.messages[0].message);
                    }
                });
            } else {
                request = requestFactory.factory({
                    "requestType":"dataSource",
                    "serviceName":"/zeedhi-developer/create",
                    "dataSource":layout,
                    "callBack":function (data) {
                        alert(data.messages[0].message);
                    }
                });
            }

            requestEngine.doRequest(request);
        };
        $scope.loadLayout = function () {
            var containerId = $scope.containerToLoad;
            var request = requestFactory.factory({
                "requestType": "dataSource",
                "serviceName": "/zeedhi-developer/retrieve",
                "dataSource": {"container_id": containerId},
                "callBack": function(data) {
                    if (data.dataset.layout_dev) {
                        data.dataset.layout_dev.toUpdate = true;
                        $scope.layoutData = data.dataset.layout_dev;
                    } else {
                        alert("There's no container with this ID.");
                    }
                }
            });

            requestEngine.doRequest(request);
        };
        $scope.deleteLayout = function () {
            if (confirm("Are you sure you want to delete?")) {
                var layout = angular.copy($scope.layoutData);
                var request = requestFactory.factory({
                    "requestType": "dataSource",
                    "serviceName": "/zeedhi-developer/delete",
                    "dataSource": layout,
                    "callBack": function(data) {
                        alert(data.messages[0].message);
                    }
                });

                requestEngine.doRequest(request);
            }

        };
        $scope.loadConstainerNames = function() {
            var request = requestFactory.factory({
                "requestType": "dataSource",
                "serviceName": "/zeedhi-developer/retrieve_names",
                "dataSource": null,
                "callBack": function(data) {
                    $scope.containers = data.dataset.layoutDev;
                }
            });

            requestEngine.doRequest(request);
        };

        $scope.loadTemplates = function() {
            var request = requestFactory.factory({
                "requestType": "dataSource",
                "serviceName": "/zeedhi-developer/retrieve_templates",
                "dataSource": null,
                "callBack": function(data) {
                    data = JSON.parse(data.dataset.layoutDev); 
                    $scope.templates = data;
                }
            });

            requestEngine.doRequest(request);
        };
        $scope.loadFieldgroups = function() {
            var request = requestFactory.factory({
                "requestType": "dataSource",
                "serviceName": "/zeedhi-developer/retrieve_fieldgroups",
                "dataSource": null,
                "callBack": function(data) {
                    data = JSON.parse(data.dataset.layoutDev); 
                    $scope.fieldgroups = data;
                }
            });

            requestEngine.doRequest(request);
        };
        $scope.loadDatasources = function() {
            var request = requestFactory.factory({
                "requestType": "dataSource",
                "serviceName": "/zeedhi-developer/retrieve_datasources",
                "dataSource": null,
                "callBack": function(data) {
                    data = JSON.parse(data.dataset.layoutDev);
                    $scope.datasources = data;
                }
            });

            requestEngine.doRequest(request);
        };
        $scope.loadActiongroups = function() {
            var request = requestFactory.factory({
                "requestType": "dataSource",
                "serviceName": "/zeedhi-developer/retrieve_actiongroups",
                "dataSource": null,
                "callBack": function(data) {
                    data = JSON.parse(data.dataset.layoutDev);
                    $scope.actiongroups = data;
                }
            });

            requestEngine.doRequest(request);
        };
        $scope.loadDatacolumn = function(dataSourceId) {
            var request = requestFactory.factory({
                "requestType": "filterData",
                "serviceName": "/zeedhi-developer/retrieve_datacolumns",
                "filter": {
                    "DATASOURCE_ID": dataSourceId
                },
                "callBack": function(data) {
                    data = JSON.parse(data.dataset.layoutDev);
                    $scope.datacolumns = data;
                }
            });

            requestEngine.doRequest(request);
        };
        $scope.loadMasks = function() {
            var request = requestFactory.factory({
                "requestType": "dataSource",
                "serviceName": "/zeedhi-developer/retrieve_masks",
                "dataSource": null,
                "callBack": function(data) {
                    data = JSON.parse(data.dataset.layoutDev);
                    $scope.masks = data;
                }
            });

            requestEngine.doRequest(request);
        };
        $scope.loadI18n = function() {
            request("../json/words.json",function(words){
                $scope.words = words;
            });
        };
        $scope.getTemplate = function(type, templateId){
            var templates = $scope.templates[type];
            angular.forEach(templates, function(value, key){
                if(value.template_id == templateId){
                    template = value.template;
                }
            });
            return template;
        };

        var getWidget = function(widgetId) {
            widgets = $scope.layoutData.container.widgets;
            for (i in widgets) {
                if (widgets[i].widget_id === widgetId) {
                    return widgets[i];
                }
            }
        }

        // $scope.initContainersAndWidgets();
        $scope.loadConstainerNames();
        $scope.loadDatasources();
        $scope.loadFieldgroups();
        $scope.loadActiongroups();
        $scope.loadTemplates();
        $scope.loadMasks();
        $scope.loadI18n();
    });

ZeedhiLayout.filter("i18n",function(i18nEngine){
    return function(word, words, lang){
        return i18nEngine.i18n(word, words, lang);
    }
});

ZeedhiLayout.filter("getTemplate",function(){
    return function(templates, key, templateId){
        var template = null;
        angular.forEach(templates[key], function(value){
            if (value['template_id'] == templateId) {
                template = value.template;
            }
        });
        return template;
    }
});

ZeedhiLayout.directive('zhlWidgetContainer', function(){
    return {
        restrict: 'A',
        link: function($scope, iElement, iAttrs){
            $scope.$watch('layoutData.container.widgets',function(){
                iElement.height($(window).height() * 0.9);
                iElement.children('div').height(iElement.height() * 0.9);
            }, true)
        }
    }
});

}).call(window);