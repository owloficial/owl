var ZeedhiEvents = angular.module("ZeedhiEvents", []);
ZeedhiEvents.directive('zhContainerEvents', function(templateManager, eventEngine){
    return {
        link : function($scope){
            $scope.$watch('templateManager.container', function(){
                var container = templateManager.container;
                var events = container.events;
                if (events) {
                    eventEngine.bindEvents(container,events, function(){return [];}, function(){return {};});
                }
                if(container.beforeinit){
                    container.beforeinit();
                }
                if(container.afterinit){
                    container.afterinit();
                }
            });
        }
    };
});
ZeedhiEvents.directive('zhWidgetEvents', function(templateManager, eventEngine){
    return function postLink($scope, element, attrs) {
        var widget = $scope.widget;
        var events = widget.events;
        if (events) {
            eventEngine.bindEvents(widget,events, function(){return widget.dataSource.data;}, function(){return widget.currentRow;});
            if(widget.onEnter)
                widget.onEnter();
        }
    };
});

ZeedhiEvents.directive('zhEvents', function(templateManager, eventEngine){
    return function postLink($scope, element, attrs) {
        var events = $scope.field.events;
        var field = $scope.field;
        var widget = field.widget;
        element.field = field;
        if (events) {
            eventEngine.bindEvents(element,events, function(){if(widget.dataSource){ return widget.dataSource.data;}else{ return null; }}, function(){return widget.currentRow;});
        }
    };
});

ZeedhiEvents.directive('zhPushValue', function(templateManager){
    return function postLink($scope, element, attrs){
        var execDirective = function(){
            var value = $scope.$eval(attrs.ngBind || attrs.ngModel);
            var fieldName = $scope.$eval(attrs.zhPushValue);
            var removed;
            if($scope.widget.currentRow[fieldName].push){
                for(var i = 0; i < $scope.widget.currentRow[fieldName].length; i++){
                    if($scope.widget.currentRow[fieldName][i] === $scope.row){
                        $scope.widget.currentRow[fieldName].splice(i, 1);
                        removed = true;
                        element.change();
                    }
                }
                if(!removed){
                    $scope.widget.currentRow[fieldName].push($scope.row);
                    element.change();
                }
            } else {
                if($scope.widget.currentRow[fieldName].indexOf(value) === -1){
                    $scope.widget.currentRow[fieldName] += value + "; ";
                } else {
                    $scope.widget.currentRow[fieldName] = $scope.widget.currentRow[fieldName].replace(value + "; ", "");
                }
            }
        };
        element.on('click', execDirective);
    };
});