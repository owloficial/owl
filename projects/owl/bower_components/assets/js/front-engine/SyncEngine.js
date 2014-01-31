var SyncEngine = function(requestFactory, localStorageEngine, requestEngine, templateManager) {
    var SERVICE         =  "/queue/sync-queue";
    var SYNCHRONIZED_AT = "SYNCHRONIZED_AT";

    this.sync = function(serviceName, data, filters) {
        var widget = templateManager.currentWidget;
        if (widget.onSync) {
            widget.onSync();
        }

        var dataSource = {
            "requestType": "DataSet",
            "dataset": data,
            "serviceName": serviceName+"/save",
            "filters": filters
        };
        var request = requestFactory.factory({
            "requestType": "dataSource",
            "dataSource": dataSource,
            "serviceName": SERVICE
        });

        requestEngine.doRequest(request);
        localStorage.setItem(SYNCHRONIZED_AT, getCurrentDate());
    };

    var getCurrentDate = function() {
        var date    = new Date();
        var year    = date.getFullYear();
        var month   = ("0" + (date.getMonth() + 1)).slice(-2);
        var day     = ("0" + date.getDate()).slice(-2);
        var hour    = ("0" + date.getHours()).slice(-2);
        var minutes = ("0" + date.getMinutes()).slice(-2);
        var seconds = ("0" + date.getSeconds()).slice(-2);

        return day + "/" + month + "/" + year + " " + hour + ":" +
               minutes + ":" + seconds;
    };
};