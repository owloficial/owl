var ZeedhiFilters = angular.module("ZeedhiFilters", []);

ZeedhiFilters.filter("i18n",function(renderEngine){
	return function(word, words, lang){
		return renderEngine.i18n(word, words, lang);
	};
});
ZeedhiFilters.filter("columnFormat", function (maskEngine) {
	return function (input, column) {
		var result = ""; 
		if(column && column.mask){
			var name = column.mask.type+"Format";
			result = maskEngine[name](input);
		}else{
			result = input;
		}
		return result;
	};
});
	/**
		* Filters out all duplicate items from an array by checking the specified key
		* @param [key] {string} the name of the attribute of each object to compare for uniqueness
		if the key is empty, the entire object will be compared
		if the key === false then no filtering will be performed
		* @return {array}
	*/
	ZeedhiFilters.filter('unique', function () {

		return function (items, filterOn) {

			if (filterOn === false) {
				return items;
			}

			if ((filterOn || angular.isUndefined(filterOn)) && angular.isArray(items)) {
				var hashCheck = {}, newItems = [];

				var extractValueToCompare = function (item) {
					if (angular.isObject(item) && angular.isString(filterOn)) {
						return item[filterOn];
					} else {
						return item;
					}
				};

				angular.forEach(items, function (item) {
					var valueToCheck, isDuplicate = false;

					for (var i = 0; i < newItems.length; i++) {
						if (angular.equals(extractValueToCompare(newItems[i]), extractValueToCompare(item))) {
							isDuplicate = true;
							break;
						}
					}
					if (!isDuplicate) {
						newItems.push(item);
					}
				});
				items = newItems;
			}
			return items;
		};
	});

	ZeedhiFilters.filter('orderByHeader', ["$filter",function($filter){
		return function(data, orderValue, reverse){
			if (orderValue){
				data = $filter('orderBy')(data, orderValue, reverse);
			}
			return data;
		};
	}]);

	ZeedhiFilters.filter('hasWidgetToExpand', function(){
		return function(widgets){
			if(widgets){
				var expand = widgets.filter(function(widget){
					return widget.expand === true;
				});
				if (expand.length > 0){
					return expand;
				} else {
					return widgets;
				}
			}
		}; 
	});
	
	ZeedhiFilters.filter('groupBy', function(Memoizer){
		var memoizer = memoizer || Memoizer.create(2);
		return function(items, query){
			if(items && !memoizer.has(items)){
				var dataGroup = {};
				for (var i = items.length - 1; i >= 0; i--) {
					if(!dataGroup[items[i][query]])
						dataGroup[items[i][query]] = [];
					dataGroup[items[i][query]].push(items[i]);
				}
				memoizer.add(items, dataGroup);
			}
			return memoizer.get(items);
		};
	});
	ZeedhiFilters.filter('startsWith', function(){
		return function(items, column, query){
			if(items){
				return items.filter(function(item){
					return query === undefined || (item[column] && item[column].toLowerCase().indexOf(query.toLowerCase()) === 0);
				});				
			}else{ 
				return items;
			}
		};
	});

	ZeedhiFilters.filter('range', function(){
		return function(input, start, end){
			input = [];
			for(var i = start; i<=end; i++){
				input.push(i);
			}
			return input;
		};
	});	