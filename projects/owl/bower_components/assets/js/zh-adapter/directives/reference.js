var ZeedhiReference = angular.module("ZeedhiReference", []);
ZeedhiReference.directive('zhReference', function(templateManager){
    return {
        require: '?ngModel',
        link: function($scope, element, attrs, controller){
            controller.$formatters.unshift(function(value){
                //var field = s2id_region_id]
                var fieldName = $scope.field.name;
                var fieldDescName = $scope.field.configData.text;
                var currentRow = $scope.field.widget.currentRow;
                var descValue = '';
                if(currentRow && currentRow["__"+$scope.field.name+"_data"]){
                    descValue = currentRow["__"+$scope.field.name+"_data"][fieldDescName];
                }
                if(currentRow)
                    element.val(descValue).trigger("change");
            });
            controller.$render = function() {
                var fieldDescName = $scope.field.configData.text;
                var currentRow = $scope.field.widget.currentRow;
                var descValue = '';
                if(currentRow && currentRow["__"+$scope.field.name+"_data"]){
                    descValue = currentRow["__"+$scope.field.name+"_data"][fieldDescName];
                }
                if(currentRow)
                    element.val(descValue).trigger("change");
                element.select2({
                    placeholder: $scope.field.placeholder,
                    minimumInputLength: $scope.field.minimumInputLength,
                    formatNoMatches : function(val){
                        if ($scope.field.noMatchText){
                            return "<button id='quick-insert'>"  +  $scope.field.noMatchText + "</button>";
                        }
                    },
                    multiple: $scope.field.isMultiple,
                    maximumSelectionSize: 4,
                    id : function(a){return a.id;},
                    ajax: {
                        type: "post",
                        dataType: "json",
                        quietMillis: 300,
                        "transport": function(params) {
                            var filterDefinitions = $scope.field.filterDefinitions;
                            var dataSource = $scope.field.dataSource;
                            var row = $scope.widget.currentRow;
                            filterDefinitions.fields.forEach(function(filterDefinition){
                                filterDefinition.value = params.data.query;
                            });
                            var filter =  Util.clone(filterDefinitions.fields);
                            var dataSourceFilter =  Util.clone($scope.field.dataSourceFilter);
                            if (dataSourceFilter) {
                                dataSourceFilter.map(function(dataSource){
                                    dataSource.value = Util.evaluate(dataSource.value);
                                    filter.push(dataSource);
                                });
                            }
                            for (var column in params.data.inColumns){
                                var value = params.data.inColumns[column];
                                filter.push({ operator : "=", name : column, value : value});
                            }

                            //console.log(params.data.page);
                            //                                filter = params.data.query;
                            dataSource.filter(filter, params.success, params.data.page);
                            templateManager.updateTemplate();
                        },
                        data: function(term, page) {
                            var inColumns = {};
                            var inData = $scope.field.inData;
                            for (var key in inData) {
                                inColumns[key] = $scope.widget.currentRow[inData[key]];
                            }

                            return {
                                inColumns: inColumns,
                                query: term+"%",
                                page: page
                            };
                        },
                        results: function(data, page) {
                            var obj       = [];
                            for (var i in data) {
                                obj.push({
                                    "id": data[i][$scope.field.configData.id],
                                    "text": data[i][$scope.field.configData.text],
                                    "data": data[i]
                                });
                            }

                            return {results: obj, more: data.length>=30};
                        }
                    },
                    formatResult: function(result) {return result.text;},
                    formatSelection: function(object, container) {
                        var outData = $scope.field.outData;
                        for (var key in outData) {
                            if ($scope.widget.currentRow) {
                                $scope.widget.currentRow[outData[key]] = object.data[key];
                            }
                            $scope.widget.currentRow["__"+$scope.field.name+"_data"] = object.data;
                        }

                        return object.text;
                    },
                    escapeMarkup: function(m) {return m;}
                }).change(function(){
                        //$scope.widget.currentRow.nome = $(this).val();
                        controller.$setViewValue($(this).val());
                        templateManager.updateTemplate();
                    });
            };
        }
    };
});