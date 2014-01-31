var SQLStorageStrategy = function(requestDatasoureceEngine) {
	var SQL_CREATE = "CREATE TABLE IF NOT EXISTS ";
	var SQL_INSERT = "INSERT INTO ";
	var SQL_SELECT = "SELECT * FROM ";
	var SQL_UPDATE = "UPDATE ";
	var dataSources = {};

	var deferreds = [];

	var SQLFallback = function(){
		var db = window.openDatabase(
			'ZeedHi', 
			'1.0', 
			'defaultDB',
			2 * 1024 * 1024
		);
		return function(sql, callback){
			db.transaction(function (tx) {
				tx.executeSql(sql, [], function (tx, results){
					var len = results.rows.length;
					var data = [];
					for (var i = 0; i < len; i++){
						data.push(results.rows.item(i));
					}
					if(callback) callback(data);
				});
			});
		};
	};

	var deviceSQL = function(sql, callback){
		callback(JSON.parse(window.Android.execute(sql)));
	};

	var executeSql = window.Android ? deviceSQL : SQLFallback();

	var sqlColumn = function(column){
		return column + " VARCAR(400) ";
	};

	this.registerDataSource = function(dataSource){
		for(var dataSourceName in dataSource){
			dataSourceName = dataSourceName.indexOf("/") !== -1 ? dataSourceName.split("/")[1] : dataSourceName;
			if(!dataSources[dataSourceName]){
				var toRegister = dataSource[dataSourceName];
				dataSources[dataSourceName] = toRegister;
				var create = this.sqlCreateByDataSource(toRegister);
				execute(create);
				if(deferreds[dataSourceName]){
					deferreds[dataSourceName].resolve();
				}
			}
		}
	};

	this.sqlCreateByDataSource = function(dataSource){
		var sql = "";
			var mapCB = function(column){
				return sqlColumn(column);
			};
			var tableData = dataSource;
			sql += SQL_CREATE+tableData.tableName;
			var columns = tableData.columns.map(mapCB).join(',');
		sql += " ( " + columns + " );";
		return sql;
	};

	this.sqlUpdate = function(name, obj){
			var sql = "";
			var dataSource = dataSources[name];
			var columnNames = dataSource.columns.filter(function(column){
				return !!obj[column];
			});
			var keys = dataSource.localKey.filter(function(column){
				return !!obj[column];
			});
			var properts =	columnNames.map(function(key){
				return key+" = '" + obj[key] + "'";
			});
			var values = properts.join(',');
			var where = this.sqlWhere(keys.map(function(key){
				return { "name" : key, "value" : obj[key], "operator" : "="};
			}));
			sql += SQL_UPDATE + dataSource.tableName + " SET " + values + " WHERE "+ where +";";
		return sql;		 
	};

	this.sqlInsert = function(name, obj){
			var sql = "";
			console.log("Insert");
			console.log(name);
			var dataSource = dataSources[name];
			var keys = dataSource.columns.filter(function(column){
				return !!obj[column];
			});
			var columns =	keys.join(',');
			var properts =	keys.map(function(key){
				return "'" + obj[key] + "'";
			});
			var values = properts.join(',');
			sql += SQL_INSERT + dataSource.tableName + " (" + columns + ") " + " VALUES ( " + values + " ) ; ";
		return sql;
	};

	this.sqlSelect = function(name, filters){
				var sql = "";
				var dataSource = dataSources[name];				 
				sql += SQL_SELECT + dataSource.tableName;
				if(filters && filters.length > 0){
					sql += " where " + this.sqlWhere(filters);
				}
			return sql;
	};

	this.sqlWhere = function(filters){
			var where = filters.map(function(filter){
					return filter.name + " " + filter.operator + " '" + filter.value + "'";
			}).join(' and ');
			return where;
	};

	this.getDataSource = function(name, callBack, filter){
			name = name.indexOf("/") !== -1 ? name.split("/")[1] : name;
			if(dataSources[name]){
				var sql = this.sqlSelect(name, filter);
				execute(sql, callBack);
			} else {
				deferreds[name] = deferreds[name] || $.Deferred();
				deferreds[name].then((function(){
					this.getDataSource(name, callBack, filter);
				}).bind(this));
			}
	};

	this.setDataSource = function(name, dataSource, callBack){
		var sql;
		console.log(name);
		name = name.indexOf("/") !== -1 ? name.split("/")[1] : name;
		dataSource.map((function(obj){
			if(obj.__createdLocal){
				console.log("Created");
				sql = this.sqlInsert(name, obj);
				execute(sql);
			} else if(obj.changed){
				console.log('Cahgend');
				sql = this.sqlUpdate(name, obj);
				execute(sql);
			} else {
				console.log('None');
			}
			if(callBack)
				callBack(name, dataSource);
		}).bind(this));
	};

	this.download = function(name, callBack, filters, comparator){
			self = this;
			callBack = (function(){
				var _callback = callBack;
				var filtered;
				return function(data, dataSourceName){
					if(dataSourceName && typeof comparator === "function"){
						self.getDataSource(dataSourceName, function(source){
							data.forEach(function(destinationItem){
								destinationItem.__createdLocal = true;
								filtered = source.filter(function(sourceItem){
									return comparator(sourceItem, destinationItem);
								});
								filtered = data.concat(filtered);
							});
							self.setDataSource(dataSourceName, filtered);
						});
					} else {
						filtered = data;
						data.forEach(function(destinationItem){
							destinationItem.__createdLocal = true;
						});
						self.setDataSource(dataSourceName, filtered);
					}
					_callback(filtered, dataSourceName);
				};
			})();
			requestDatasoureceEngine.getDataSource(name, callBack, filters);
	};

	var execute = function(sql, callback){
		return executeSql(sql, callback);
	};
	this.execute = execute;
};