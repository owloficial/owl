var RequestFactory = function() {
	this.factory = function(e) {
		var request = null;
		if (e.requestType) {
			switch (e.requestType) {
				case "filterData":
					request = new FilterDataRequest(e.serviceName, e.filter, e.callBack, e.page);
					break;
				case "AutoCompleteData":
					request = new ReferenceAutoCompleteDataRequest(e.serviceName, value, inData);
					break;
				case "Row":
					request = new EventRequestRow(e.serviceName, e.row, e.callBack);
					break;
				case "CheckRow":
					request = new EventRequestCheckRow(e.serviceName, e.row);
					break;
				case "Empty":
					request = new EventEmpty(e.serviceName);
					break;
				case "Cell":
					request = new EventCell(e.serviceName, value);
					break;
				case "dataSource":
					request = new EventRequestDataSet(e.serviceName, e.dataSource, e.callBack);
					break;
				case "Wizard":
					request = new EventWizard(e.serviceName, dataset);
					break;
			}
		}
		return request;
	};
};
var RequestEngine = function(restEngine){
  this.doRequest = function(request){
		restEngine.post(request.getServiceName(), request.getRequestParams(), request.processResponse);
  };
};