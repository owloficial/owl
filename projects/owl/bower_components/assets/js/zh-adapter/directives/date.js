var ZeedhiDiretiveDate = angular.module("ZeedhiDiretiveDate", []);

(function(){

    function daysInMonth(iMonth, iYear){
        return 32 - new Date(iYear, iMonth, 32).getDate();
    }

    
    function DateCtrl($scope, element, attrs, controller){
        var dayField        = $('.selectDay',   element);
        var monthField      = $('.selectMouth', element);
        var yearField       = $('.selectYear',  element);

        this.init = function(){
            this.getDateValue();

            this.loadYears(element);

            dayField.change(this.apply.bind(this));

            monthField.change(this.apply.bind(this));

            yearField.change(this.apply.bind(this));
        };

        this.getDateValue = function(){
            var day = dayField.val();
            var month = monthField.val();
            var year = yearField.val();

            var maxDayForMonth = daysInMonth(month -1, year);

            $scope.__maxDay__ = maxDayForMonth;

            day = day > maxDayForMonth ? maxDayForMonth : day;

            return day + "/" + month + "/" + year;
        };

        this.loadYears = function(){
            var initialized = element.children().attr("data-future");
            if(!initialized){
                var newestYearElement = angular.element("[data-newest]", element);
                var newestYear = newestYearElement.val();
                var thisYear   = new Date().getFullYear(); 
                var options = [];
                while(newestYear < (thisYear + 20)){
                    var option = $("<option></option>");
                    option.val(++newestYear);
                    option.text(newestYear);
                    option.data("future");
                    options.unshift(option);
                }
                newestYearElement.parent().prepend(options);
            }
        };

        this.apply = function(){
            var value = this.getDateValue();
            var _apply = function () {
                controller.$setViewValue(value);
            };
            if(!($scope.$$phase == '$apply' || $scope.$$phase == '$digest') && !$scope.applyInProgressZh){
                $scope.$apply(_apply);
            } else {
                _apply();
            }
            element.change();
        };

        this.change = function(value){
            if(value){
                var dateValues = value.split('/');
                dayField.val(dateValues[0]);
                monthField.val(dateValues[1]);
                yearField.val(dateValues[2]);
            }
            return value;
        };

    }

    ZeedhiDiretiveDate.directive('zhDateField', function(){
        return {
            require: '?ngModel',
            link: function($scope, element, attrs, controller){

                var dateCtrl = new DateCtrl($scope, element, attrs, controller);

                dateCtrl.init();

                controller.$parsers.unshift(dateCtrl.change.bind(dateCtrl));

                controller.$formatters.unshift(dateCtrl.change.bind(dateCtrl));

            }
        };
    });


})();