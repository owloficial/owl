
function Repository(name, storageStrategy, templateManager){

	var dataSourcesPromise = templateManager.getDataSources();
	var dataSources;

	var getFilters = function(query){
		var filters;
		if(query instanceof Query){
			filters = query.where();
		} else if(typeof(query) === 'object'){
			filters = buildQuery(query, "=");
		} else if(query){
			throw TypeError("Invalid type for query " + typeof(query) + " " + query +  ". Use Query or plain object instead.");
		}
		return filters;
	};

	var getWithDataSource = function(name, query){
		var filters = getFilters(query);
		var itemsPerPage = dataSources[name].itemsPerPage;
		var page;
		if(itemsPerPage){
			page = Math.ceil(query.limit() / itemsPerPage);
			itemsPerPage = query.limit() - itemsPerPage;
		}
		return storageStrategy.getDataSource(name, filters, page, itemsPerPage);
	};

	this.findAll = function(){
		return storageStrategy.getDataSource(name);
	};

	this.find = function(query){
		var promise;
		if(!dataSources){
			promise = dataSourcesPromise.then(function(data){
				dataSources = data;
				return getWithDataSource(name, query);
			});
		} else {
			promise = getWithDataSource(name, query);
		}
		return promise;
	};

	this.findOne = function(query){
		query = query ? query : new Query();
		query.limit(1);
		query.offset(0);
		var promise;
		if(!dataSources){
			promise = dataSourcesPromise.then(function(data){
				dataSources = data;
				return getWithDataSource(name, query);
			});
		} else {
			promise = getWithDataSource(name, query);
		}		
		return promise.then(function(all){
			return Object.prototype.toString.call( all ) === '[object Array]' ? all[0] : all;
		});
	};

	this.save = function(data){
		return storageStrategy.save(name, data);
	};

	this.remove = function(query){
		return storageStrategy.remove(name, getFilters(query));
	};

}

function RepositoryFactory(SQLStorageStrategy, localStorageEngine, restEngine, templateManager){

	var RepoType = {
		SQL 	: SQLStorageStrategy,
		LOCAL 	: localStorageEngine,
		ONLINE 	: restEngine
	}

	var instanceNames = [];

	this.factory = function(dataSourceName, type){
		if(instanceNames.indexOf(dataSourceName) !== -1){
			console.log("Ja instanciado " + dataSourceName);
		} else {
			instanceNames.push(dataSourceName);
		}
		return new Repository(dataSourceName, RepoType[type], templateManager);
	};
}

function buildFilter(name, value, operator){
	return {name : name, value : value, operator : operator};
};

function buildQuery(query, operator){
	return Object.keys(query).map(function(key){
		return buildFilter(key, query[key], operator);
	});
};

function Query(){

    var filters = [];

    var lastKey;

    var limit;

    var offset;

	this.where = function where(query){
		if(!query) return filters;
		if(typeof(query) === "object"){
			filters = filters.concat(buildQuery(query, "="));
		} else {
			lastKey = query;
		}
		return this;
	};

	this.notEquals = function notEquals(query){
		if(typeof(query) === "object"){
			filters = filters.concat(buildQuery(query, "<>"));
		} else {
			filters.push(buildFilter(lastKey, query, "<>"));
		}
		return this;
	};

	this.equals = function notEquals(query){
		if(typeof(query) === "object"){
			filters = filters.concat(buildQuery(query, "="));
		} else {
			filters.push(buildFilter(lastKey, query, "="));
		}
		return this;
	};

	this.greater = function gt(query){
		if(typeof(query) === "object"){
			filters = filters.concat(buildQuery(query, ">"));
		} else {
			filters.push(buildFilter(lastKey, query, ">"));
		}
		return this;
	};

	this.lower = function lt(query){
		if(typeof(query) === "object"){
			filters = filters.concat(buildQuery(query, "<"));
		} else {
			filters.push(buildFilter(lastKey, query, "<"));
		}
		return this;
	};

	this.limit = function(number){
		if(!number) return limit;
		if(!limit){
			limit = number;
		} else {
			throw new TypeError("Limit already assigned! limit = " + limit);
		}
		return this;
	};

	this.offset = function (number){
		if(!number) return offset;
		if(!offset){
			offset = number;
		} else {
			throw new TypeError("Offset already assigned! offset = " + limit);
		}
		return this;
	};
}

function QueryBuilder(){
	this.build = function build(){
		return new Query();
	};
}
