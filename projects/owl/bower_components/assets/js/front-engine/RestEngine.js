var RestEngine = function($http, baseUrl, metaDataUrl) {
	var _http = $http;
	var _baseUrl = baseUrl;
	var _metaDataUrl = metaDataUrl;

	this.request = function(service, params, callBack, typeRequest){
		if(!typeRequest){
			typeRequest = "POST";
		}
		var xsrf = $.param(params);

		params = {
			url: _baseUrl+service,
			dataType: "json",
			method: typeRequest,
			data : xsrf,
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'User-Id' : localStorage.getItem("USER_ID")
			},
			withCredentials : true
		};
		
		_http(params).success(function(data){
			if (callBack) {
				callBack(data);
			}
		}).error(function(error){
			alert(error);
		});
	};
	this.requestMetaData = function(service, params, callBack){
		var typeRequest = "GET";
		var xsrf = $.param(params);
		_http({
			url: _metaDataUrl+service,
			dataType: "json",
			method: typeRequest,
			data : xsrf,
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'User-Id' : localStorage.getItem("USER_ID")
			}
		}).success(function(data){
			callBack(data);
		}).error(function(error){
			alert(error);
		});
	};
	this.post = this.request;
	this.zhdelete = function(service, params, callBack){
		this.request(service, params, callBack, "DELETE");
	};
	this.put = function(service, params, callBack){
		this.request(service, params, callBack, "PUT");
	};
	this.get = function(service,callBack){
		this.request(service, callBack, "GET");
	};
	this.setURI = function(URI){
		_baseUrl = URI;
	};
};
