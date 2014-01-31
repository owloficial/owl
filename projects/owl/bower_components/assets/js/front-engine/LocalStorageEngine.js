var LocalStorageEngine = function(requestDatasoureceEngine, requestFactory, requestEngine, templateManager) {
/*var LocalStorageEngine = function(requestDatasoureceEngine) {*/
	var self = this;

	/* Refatorar */
	var dataSources = [];

	var clearData = function(data){
		var dataToReturn = angular.copy(data);
		delete dataToReturn.$error;
		return dataToReturn;
	};

	this.getDataSource = function(name, callBack, filter, page, itemsPerPage){
		if(callBack && typeof(callBack) !== 'function'){
			filter = arguments[1];
			page = arguments[2];
			itemsPerPage = arguments[3];
			callBack = false;
		}
		var data = localStorage.getItem(name);
		if(data && data !== 'undefined'){
			var dataToReturn = JSON.parse(data);//RJSON.unpack();
			dataToReturn = this.processFilter(dataToReturn, filter);
			if(itemsPerPage && page)
				dataToReturn.splice(page * itemsPerPage, dataToReturn.length-1);
				if(callBack) {
					callBack(dataToReturn);
				} else {
					var deferred = $.Deferred();
					deferred.resolve(dataToReturn);
					return deferred;
				}
		}
		else {
			callBack([]);
		}
	};

	this.save = function(name, object){
		var deferred = $.Deferred();
		var resolve = function(data){
			if(!object.__createdLocal){
				var key = dataSources[name].localKey;
				data = data.filter(function(item){
					return item[key] != object[key];
				});
			}
			data.push(object);
			self.setDataSource(name, data, function(){
				deferred.resolve();
			});
		};
		self.getDataSource(name, function(data){
			if(!dataSources[name]){
				restEngine.requestMetaData("../json/datasources/" + name + ".json", {}, function(dataSource){
					dataSources[name] = dataSource[name];
					resolve(data);
				}, "GET");
			} else {
				resolve(data);
			}
		});
		return deferred;
	};

	this.remove = function(name, filters){
		var deferred = $.Deferred();
		for(var i in filters){
			var filter = filters[i];
			filter.operator='<>';
		}
		self.getDataSource(name, function(data){
			self.setDataSource(name, data, function(){
				deferred.resolve();
			});
		}, filters);
		return deferred;
	};

	this.setDataSource = function(name, dataSource, callBack){
		dataSource = angular.isArray(dataSource) ? dataSource : [dataSource];
		if (dataSource) {
			var dataToStorage = dataSource.map(clearData);
			localStorage.setItem(name, JSON.stringify(dataToStorage));
			if(callBack) {
				callBack(dataToStorage);
			} else {
				var deferred = $.Deferred();
				deferred.resolve(dataToReturn);
				return deferred;
			}
		}
	};

	this.getLocalVar = function(name){
		var data = localStorage.getItem(name);
		if(data && data !== 'undefined'){
			return(JSON.parse(data));
		}
		else{
			return;
		}
	};
	this.setLocalVar = function(name, localVar){
		localStorage.setItem(name, JSON.stringify(localVar));
		return;
	};
	this.download = function(name, callBack, filters, comparator){
		callBack = (function(){
			var _callback = callBack;
			return function(data, dataSourceName){
				var filtered;
				if(dataSourceName && typeof comparator === "function"){
					var source = getLocalVar(dataSourceName);
					data.forEach(function(sourceItem){
						filtered = source.filter(function(destinationItem){
							return comparator(sourceItem, destinationItem);
						});
						filtered = data.concat(filtered);
					});
				} else {
					filtered = data;
				}
				localStorageEngine.setDataSource(dataSourceName, filtered, function(){});
				_callback(filtered, dataSourceName);
			};
		})();
		requestDatasoureceEngine.getDataSource(name, callBack, filters);
	};
		
	this.saveInServer = function(name, callBack){
		this.getDataSource(name, function(data){
			requestDatasoureceEngine.setDataSource(name, data, callBack);
		});
	};

	this.sync = function(name, callBack, filters){
		var localStorageEngine = this;
		this.getDataSource (
			name,
			function(data) {
				var syncEngine = new SyncEngine(requestFactory, localStorageEngine, requestEngine, templateManager);
				syncEngine.sync(name, data, filters);
				self.download(name, callBack, filters);
			},
			filters
		);
	};

	//	this.sync = function(name, callBack, filters){
	//	alert('Sinc : '+name);
	//	var localStorageEngine = this;
	//	this.getDataSource(name, function(data){
	//		requestDatasoureceEngine.setDataSource(name, data, function(){
	//			self.download(name, callBack, filters);
	//		});
	//	}, filters);
	//	};

	//=================DataQueryFilter===============//

	this.processFilter = function(data, filters){
		var filteredValues = data;
		for(var i in filters){
			filteredValues = this.queryFilter(filteredValues, filters[i]);
		}
		return filteredValues;
	};

	this.queryFilter = function(data, queryParams){
		var filteredData = data;
		if(!queryParams.value && queryParams.value !== false){
			console.error("Valor invalido para o filtro: value = " + queryParams.value);
			return [];
		}
		switch(queryParams.operator){
			case 'LIKE':
				filteredData = this.likeFilter(filteredData, queryParams);
				break;
			case '=':
				filteredData = this.equalFilter(filteredData, queryParams);
				break;
			case '<>':
				filteredData = this.notEqualFilter(filteredData, queryParams);
				break;
			case '<':
				filteredData = this.lessEqualFilter(filteredData, queryParams);
				break;
			case '>':
				filteredData = this.moreEqualFilter(filteredData, queryParams);
				break;				
			default:
		}
		return filteredData;
	};

	this.likeFilter = function(data, params){
		var filterValue = "^"+params.value+"$";
		var rgx = /\%/;
		while (rgx.test(filterValue)) {
			filterValue = filterValue.replace(rgx,'(.*?)');
		}

		var reg = new RegExp(filterValue,'i');
		filteredData = data.filter(function(item){
			if(reg.test(item[params.name])){
				return item;
			}
		});
		return filteredData;
	};
	this.equalFilter = function(data, params) {
		var valEvaluated = Util.evaluate(params.value.toString());
		return data.filter(function(testeObj){
			if (testeObj[params.name] === undefined || params.value === undefined) {
				return false;
			}

			var result = false;
			if(testeObj[params.name]){
				result = testeObj[params.name].toString() === valEvaluated;
			}
			return result;
		});
	};
	this.notEqualFilter = function(data, params) {
		var valEvaluated = Util.evaluate(params.value.toString());
		return data.filter(function(testeObj){
			if (testeObj[params.name] === undefined || params.value === undefined) {
				return false;
			}
			var result = false;
			if(testeObj[params.name]){
				result = testeObj[params.name].toString() !== valEvaluated;
			}
			return result;
		});
	};
	this.lessEqualFilter = function(data, params) {
		var valEvaluated = Util.evaluate(params.value.toString());
		return data.filter(function(testeObj){
			if (testeObj[params.name] === undefined || params.value === undefined) {
				return false;
			}
			var result = false;
			if(testeObj[params.name]){
				result = testeObj[params.name].toString() < valEvaluated;
			}
			return result;
		});
	};
	this.moreEqualFilter = function(data, params) {
		var valEvaluated = Util.evaluate(params.value.toString());
		return data.filter(function(testeObj){
			if (testeObj[params.name] === undefined || params.value === undefined) {
				return false;
			}
			var result = false;
			if(testeObj[params.name]){
				result = testeObj[params.name].toString() > valEvaluated;
			}
			return result;
		});
	};
};