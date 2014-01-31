var ZeedhiDirectives = angular.module("ZeedhiDirectives", []);

ZeedhiDirectives.directive('zhMask', function(maskEngine, templateManager){
    return {
        require: '?ngModel',
        link: function($scope, element, attrs, controller){
            var maskMethod = false;
            if($scope.field.mask){
                    maskMethod = $scope.field.mask.type;
                    controller.$formatters.unshift(function(value){
                        var maskFormatMethod = maskEngine[maskMethod+"Format"];
                        var result = false;
                        if(value && $scope.field.mask && maskFormatMethod){
                            result = maskFormatMethod(value);
                        }
                        else {
                            result = value;
                        }
                        return result;
                    });
                    controller.$render = function(){
                        if (maskMethod) {
                            var params = $scope.field.mask.params;
                            var callBack = function(value){controller.$setViewValue(value); templateManager.updateTemplate();};
                            var funcMask = maskEngine[maskMethod];
                            funcMask(element, controller.$viewValue, params, callBack);
                        } else {
                            // element.val(controller.$viewValue);
                            controller.$viewValue = element.val();
                        }
                    };
            }
        }
    };
});
ZeedhiDirectives.directive('zhValidate', function(templateManager){
    return {
        require: '?ngModel',
        link: function($scope, element, attrs, controller){
            controller.$parsers.unshift(function(value){
                var widget = $scope.widget;
                var validations = $scope.field.validations;
                if(validations){
                    var fieldName = $scope.field.name;
                    widget.currentRow.$error = {};
                    widget.currentRow.$error[fieldName] = [];
                    for (var validation in validations) {
                        var validateResult = validateEngine[validation](value,validations[validation]);
                        if(!validateResult.valid)
                            widget.currentRow.$error[fieldName].push(validateResult);
                    }
                    if(widget.currentRow.$error[fieldName].length === 0)
                        return value;
                }
                widget.currentRow.changed = true;
                return value;
            });
        }
    };
});
ZeedhiDirectives.directive("zhAcl", function(templateManager, aclEngine){
    return {
        link: function($scope, element, attrs) {
            var elementType = attrs.zhAcl;
            if (elementType == "menu") {
                aclEngine.processMenuAcl($scope.menu, $scope.aclRules, templateManager.containers,$scope.userId);
            } else {
                aclEngine.processAcl(templateManager.container, null, $scope.aclRules, $scope.userId);
            }
        }
    };
});
ZeedhiDirectives.directive('zhStyle', function(){
    return {
        require: "?ngModel",
        link : function($scope, element, attrs, controller){
            $scope.$watch(attrs.ngModel, function(value){
                var fieldStyle = false;
                var row = $scope.row;
                if($scope.column)
                    fieldStyle = $scope.column.styles;
                else if($scope.field){
                    fieldStyle = $scope.field.styles;
                }else {
                    fieldStyle = $scope.widget.styles;
                }

                    
                if(fieldStyle) {
                    angular.forEach(fieldStyle, function(style){
                        if (style.condition){
                            /*jslint evil: true */
                            var condition = eval(style.condition);
                            if(condition){
                                element.addClass(style.cssClass);
                            } else {
                                element.removeClass(style.cssClass);
                            }
                        } else {
                            element.addClass(style.cssClass);
                        }
                    });
                }
            });
        }
    };
});



ZeedhiDirectives.directive('zhLoader', [function () {
    return {
        link: function ($scope, element, attrs) {
            var started = 0;
            $scope.$on('request.stop', function(){
                started--;
                if(element.is(':visible') && started === 0){
                    element.hide();
                }
            });
            $scope.$on('request.start', function(){
                started++;
                if(!element.is(':visible')){
                    element.show();
                }
            });
        }
    };
}]);

ZeedhiDirectives.directive('zhHideEmpty', function(){
    return function($scope, $element, $attrs) {
       var empty = true;
 
       var children = $element.children();
       for (var i = children.length - 1; i >= 0; i--) {
            if(children[i].is(":visible")){
                empty = false;
                break;
            }               
       }
       if(empty) {
            $element.addClass("zh-hide");
       } else {
            $element.removeClass("zh-hide");
       }
    };  
});
