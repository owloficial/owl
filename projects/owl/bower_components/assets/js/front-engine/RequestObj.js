var RequestObj = function(url) {
	this.url = url;
	this.processResponse = function(e){
		console.log(e);
	};
    this.getServiceName = function(){
        return this.url;
    };
};

var EventRequest =  (function(_super) {
  __extends(EventRequest1, _super);

  function EventRequest1() {
    var _ref = EventRequest.__super__.constructor.apply(this, arguments);
    return _ref;
  }
  return EventRequest1;

})(RequestObj);

var FilterDataRequest = (function(_super) {
    __extends(FilterDataRequest1, _super);

    function FilterDataRequest1(url, filter, callBack, page) {
        this.filter = filter;
        this.page = page;
        var _ref = FilterDataRequest.__super__.constructor.apply(this, arguments);
        this.processResponse = callBack;
        this.getRequestParams = function(){
            return {
                "filter" : this.filter,
                "page" : this.page,
                "requestType": "FilterData",
                "params": {
                    "ContainerName": "regions",
                    "WidgetName": ["regions"]
                }
            };
        };
        return _ref;
    }
    return FilterDataRequest1;

})(RequestObj);

var ReferenceAutoCompleteDataRequest = (function(_super) {
    __extends(ReferenceAutoCompleteDataRequest1, _super);

    function ReferenceAutoCompleteDataRequest1(url, value, inData) {
        this.value  = value;
        this.inData = inData;

        var _ref = ReferenceAutoCompleteDataRequest.__super__.constructor.apply(this, arguments);
        this.getRequestParams = function(){
            return {"value" : this.value,"in": this.inData , "requestType": "AutoCompleteData"};
        };

        return _ref;
    }
    return ReferenceAutoCompleteDataRequest1;

})(RequestObj);

var EventRequestRow = (function(_super) {
    __extends(EventRequestRow1, _super);

    function EventRequestRow1(url, row, callBack) {
        this.row = row;
        var _ref = EventRequestRow.__super__.constructor.apply(this, arguments);
        this.processResponse = callBack;
        this.getRequestParams = function(){
            return {"row" : this.row, "requestType": "Row"};
        };
        return _ref;
    }
    return EventRequestRow1;

})(EventRequest);

var EventRequestCheckRow = (function(_super) {
    __extends(EventRequestCheckRow1, _super);

    function EventRequestCheckRow1(url, row) {
        this.row = row;
        var _ref = EventRequestCheckRow.__super__.constructor.apply(this, arguments);

        this.getRequestParams = function(){
            return {"row" : this.row, "requestType": "CheckRow"};
        };

        return _ref;
    }
    return EventRequestCheckRow1;

})(EventRequest);

var EventEmpty = (function(_super) {
    __extends(EventEmpty1, _super);

    function EventEmpty1(url) {
        var _ref = EventEmpty.__super__.constructor.apply(this, arguments);
        this.getRequestParams = function(){
            return {"requestType": "Empty"};
        };

        return _ref;
    }
    return EventEmpty1;

})(EventRequest);

var EventCell = (function(_super) {
    __extends(EventCell1, _super);

    function EventCell1(url, value) {
        this.value = value;
        var _ref = EventCell.__super__.constructor.apply(this, arguments);

        this.getRequestParams = function(){
            return {"value" : this.value, "requestType": "Cell"};
        };

        return _ref;
    }
    return EventCell1;

})(EventRequest);

var EventRequestDataSet = (function(_super) {
    __extends(EventRequestDataSet1, _super);

    function EventRequestDataSet1(url, dataset, callBack) {
        this.dataset = dataset;
        var _ref = EventRequestDataSet.__super__.constructor.apply(this, arguments);

        this.getRequestParams = function() {
            return {"dataset" : this.dataset, "requestType": "DataSet"};
        };
        this.processResponse = callBack;
        return _ref;
    }
    return EventRequestDataSet1;

})(EventRequest);

var EventWizard = (function(_super) {
    __extends(EventWizard1, _super);

    function EventWizard1(url, dataset) {
        this.dataset = dataset;
        var _ref = EventWizard.__super__.constructor.apply(this, arguments);
        this.getRequestParams = function() {
            return {"dataset" : this.dataset, "requestType": "Wizard"};
        };

        return _ref;
    }
    return EventWizard1;

})(EventRequest);