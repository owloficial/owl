var ZeedhiChart = angular.module("ZeedhiChart", []);
ZeedhiChart.directive('zhChartBar',function(chartEngine){
    return {
        require: '?ngModel',
        link: function($scope, element, attrs, controller){
            $scope.$watch("widget.dataSource.data",function(){
              chartEngine.render($scope.widget.grid.columns, $scope.widget.id, $scope.widget.dataSource, $scope.widget.title, 'bar' , $scope.widget);
            });
        }
    };
});
ZeedhiChart.directive('zhChartPie', function(chartEngine){
    return {
            require: '?ngModel',
            link: function($scope, element, attrs, controller){
                $scope.$watch("widget.dataSource.data",function(){
                    chartEngine.render($scope.widget.grid.columns, $scope.widget.id, $scope.widget.dataSource, $scope.widget.title, 'pie', $scope.widget);
                });
            }
        };
});

ZeedhiChart.directive('zhChartData', function(chartEngine){
    return {
        require: '?ngModel',
        link: function($scope, element, attrs, controller){
            $scope.$watch("widget.dataSource.data",function(){
               chartEngine.render($scope.widget.grid.columns, $scope.widget.id, $scope.widget.dataSource, $scope.widget.title, 'column', $scope.widget);
            });
        } // end link
    }; // end return
}); 
ZeedhiChart.directive('zhChartStacked', function(chartEngine) {
    return {
        require: '?ngModel',
        link: function($scope, element, attrs, controller) {
            $scope.$watch("widget.dataSource.data",function(){
                chartEngine.render($scope.widget.grid.columns, $scope.widget.id, $scope.widget.dataSource, $scope.widget.title, 'column', $scope.widget);
            });
        } // end link
    }; // return
});