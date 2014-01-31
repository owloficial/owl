var RequestDatasoureceEngine = function(restEngine, requestFactory) {

	this.getDataSource = function(name, callBack, filter, page, itemsPerPage){
        var localFilter;
        if(filter === undefined) {
            filter = [];
        }else{
			localFilter = Util.clone(filter);
			localFilter.map(function(f){
				f.value  = Util.evaluate(f.value);
			});
        }
        if(page === undefined) {
            page = 1;
        }
        var setResult = function(data){
            for(var i in data.dataset){
                    callBack(data.dataset[i], '/'+i);
            }
            if(data.messages){
                 alert(data.messages[0].message);
            }
        };
        var request = requestFactory.factory({"requestType" : "filterData", "filter" : localFilter, "page" : page, "itemsPerPage" : itemsPerPage, "serviceName" : name, "callBack" : setResult }, name, setResult);
        restEngine.doRequest(request);
	};

	this.setDataSource = function(name, dataSource,callBack){
		var setResult = function(data){
			if(callBack){
				callBack(data);
			}
		};
		var dataSourceRequest = dataSource.filter(function(item){
			return item.changed;
		});
		var request = requestFactory.factory({"requestType" : "dataSource", 'dataSource' : dataSourceRequest, "serviceName" : name+"/save", "callBack" : setResult }, name, setResult);
		restEngine.doRequest(request);

	};
	this.remove = function(name, row, callBack){
		var setResult = function(data){
			alert('Removed : '+name);
		};
		var request = requestFactory.factory({"requestType" : "Row", 'row' : row, "serviceName" : name+"/remove", "callBack" : setResult }, name, setResult);
		restEngine.doRequest(request);
		if (callBack) {
			callBack();
		}
	};
};
