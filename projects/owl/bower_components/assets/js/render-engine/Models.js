(function(){
    var Util = function(){
    };
    Util.groupBy = function(data, key, owner){
        if(!owner.dataGroup){
            owner.dataGroup = {};
            for (var i = data.length - 1; i >= 0; i--) {
                if(!owner.dataGroup[data[i][key]])
                    owner.dataGroup[data[i][key]] = [];
                owner.dataGroup[data[i][key]].push(data[i]);
            }
        }
        return owner.dataGroup;
    };
    Util.clone =  function(obj) {
        return angular.copy(obj);
    };
    Util.evaluate = function($string) {
        var $a, $function_detector, $function_new, $ret, $string_ret, _i, _len;
        $string_ret = $string;
        $function_detector = /\@(.*?)\@/g;
        if($string_ret){
            $ret = $string_ret.match($function_detector);
            if ($ret) {
                for (_i = 0, _len = $ret.length; _i < _len; _i++) {
                    $a = $ret[_i];
                    $function_new = $a.replace(/\@/g, '');
                    /*jslint evil: true */
                    eval('$return = ' + $function_new + ';');
                    $string_ret = $string_ret.replace($a, $return);
                }
            }
        }

        return $string_ret;
    };
    var Container = function(){
        var self = this;
        this.refresh = function(){
            setTimeout(function(){
                if(self.scrollers){
                    self.scrollers.forEach(function(scroller){
                        scroller.refresh();
                    });
                }
            }, 1000);
        };
        this.setVar  =  function(){
        };
        this.getVar =   function(){
        };
        this.getWidget =    function(name){
            return this.widgets.filter(function(item){
                return item.name === name;
            })[0];
        };
        this.getActions = function(showType){
            if(!this.actions){
                this.actions = [];
                this.widgets.forEach(function(widget){
                    this.actions = this.actions.concat(widget.actions);
                }.bind(this));                
            }
            return this.actions.filter(function(action){
                return action.showAsAction === showType || showType === undefined;
            });
        };
    };
    var Wizard = function(){
        this.history = [];
        this.init = function(){
            this.currentStage = this.stages[this.currentStageName];
            this.indexfyWizard();
        };
        this.indexfyWizard = function(){
            var current = this.stages[this.currentStageName];
            var i = 0;
            this.stagesIndexed = [];
            for(var key in this.stages) {
                current.__index = i++;
                this.stagesIndexed.push(current);
                if(current.nextStages && current.nextStages.length >0){
                    current = this.stages[current.nextStages[0].name];
                } else {
                    break;
                }
            }
        };
    };
    var Widget = function() {

        this.editing = false;
        this.moveToFirst = function(){
            this.setCurrentRow(this.dataSource.getFirstRow());
        };
        this.moveToLast = function(){
            this.setCurrentRow(this.dataSource.getLastRow());
        };
        this.getDataSourceFilter = function(){
            if(this.dataSourceFilter){
                result = Util.clone(this.dataSourceFilter).map(function(item){
                    var tempItem = Util.clone(item);
                    tempItem.value = Util.evaluate(tempItem .value);
                    return tempItem;
                });
            }else{
                result = [];
            }
            return result;
        };
        this.reload =  function(){
            if(this.dataSource){
                this.dataSource.load(this.getDataSourceFilter(), 1, this.container.refresh);
            }
        };
        this.nextPage =  function(){
            if(this.dataSource)
                this.dataSource.load(this.getDataSourceFilter(), this.dataSource.getNextPage());
        };
        this.order =  function(){

        };
        this.saveRow =  function(){
            if (this.currentRow) {
                this.currentRow.changed = true;
                this.dataSource.save();
            } else {
                throw 'Current row is undefinded. Unable to save row.';
            }
        };
        this.newRow =  function(){
            if(this.dataSource){
                this.dataSource.newRow();
                // this.setCurrentRow(this.dataSource.data.length-1);
                var data = this.dataSource.data;
                this.setCurrentRow(data[data.length-1]);
                for (var i in this.fields) {
                    if (this.fields.hasOwnProperty(i)) {
                        item = this.fields[i];
                        item.applyDefaultValue();
                    }
                }

                this.editing = true;
            }
        };
        this.lastIntex =  function(){
            return this.dataSource.length -1;
        };
        this.remove =  function(){
            this.dataSource.remove(this.currentRow);
            this.reload();
        };
        this.sync =  function(){
            if (this.onSync) {
                this.onSync();
            }

            return this.dataSource.sync();
        };
        this.setCurrentRow = function(row){
            if(this.beforeMoveRow){
                this.beforeMoveRow();
            }
            // this.currentRowIdex = index;
            this.currentRow = row;
            this.oldValues = Util.clone(row);
            if(this.afterMoveRow){
                this.afterMoveRow();
            }
        };
        this.getField =  function(name){
            return this.fields.filter(function(item){
                return item.name === name;
            })[0];
        };
    };
    var Field = function() {
        this.reload = function(){
            if(this.dataSource)
                this.dataSource.load(this.dataSourceFilter);
        };
        this.getDatasource = function(){
        };
        this.show = function(){

        };
        this.hide = function(){

        };
        this.value = function(value){
            if( typeof value !== "undefined"){
                this.widget.currentRow[this.name] = value;
            }else{
                return this.widget.currentRow[this.name];
            }
        };
        this.applyDefaultValue = function(){
            this.widget.currentRow[this.name] = this.defaultValue === undefined ? "" : Util.evaluate(this.defaultValue);
        };
    };
    var DataSource = function(storageStrategy){
        var self = this;
        var currentPage = 0;
        
        this.getCurrentPage = function(){
            return currentPage;
        };
        
        this.setCurrentPage = function(page){
            currentPage = page;
        };
        
        this.getNextPage = function(){
            return currentPage +1;
        };
        
        this.getPrevPage = function(){
            return currentPage -1;
        };

        this.getFirstRow = function(){
          return this.data[0] ? this.data[0] : undefined;
        };
        this.getLastRow = function(){
          return this.data[this.data.length-1] ? this.data[this.data.length-1] : undefined;
        };        
        this.newRow = function(){
            if(!this.data){
                this.data = [];
            }
            return this.data.push({ "__createdLocal" : true});
        };
        var setData = function(data){
            // if(self.data){
            //     if(data instanceof Array){
            //         var localCreateds = self.data.filter(function(row){
            //             return row.__createdLocal;
            //         });
            //         for (var i = localCreateds.length - 1; i >= 0; i--) {
            //             if(data.unshift){
            //                 data.unshift(localCreateds[i]);
            //             }
            //         }
            //     }
            // }
            self.data = data;
        };
        this.load = function(filter, page, callBack){
            if(!filter){
                filter = [];
            }
            if(page){
                currentPage = page;
            } else if(self.itemsPerPage) {
                currentPage = 1;
            }
            if(storageStrategy)
                storageStrategy.getDataSource(this.name, function(data){
                    setData(data);
                    if(callBack){
                        callBack(data);
                    }
                }, filter, currentPage, self.itemsPerPage);
        };
        this.storage = function(){
            storageStrategy.setDataSource(self.name, self.data, function(){});
        };
        this.reload = function(){
            this.load();
        };
        this.filter = function(filter, callback, page){
            storageStrategy.getDataSource(self.name, callback, filter, page);
        };
        this.order = function(fields){
            if(fields){
                fields.forEach(function(){});
            }
        };
        this.save = function(){
            this.storage();
        };
        this.remove = function(row){
            storageStrategy.remove(self.name, row, function(){});
        };
        this.sync = function(){
            storageStrategy.sync(self.name, function(){});
        };
    };
    var Factory = function(localStorageEngine, requestDatasoureceEngine, SQLStorageStrategy, restEngine) {
        var _extends = function(dest, source){
            for (var key in source){
                if(source.hasOwnProperty(key)){
                    dest[key] = source[key];
                }
            }
            return dest;
        };
        var _factoryDatasource = function(obj, filter) {
            if(!filter){
                filter = [];
            }
            if(obj.localStorage || obj.rest || obj.sql){
                var dataSource = null;
                if(obj.sql){
                    dataSource =  _extends(new DataSource(SQLStorageStrategy), obj);
                    var name = obj.name.indexOf("/") !== -1 ? obj.name.split("/")[1] : obj.name;
                    restEngine.requestMetaData("../json/datasources/" + name + ".json", {}, function(data){
                        SQLStorageStrategy.registerDataSource(data);
                    }, "GET");
                } else if(obj.rest){
                    dataSource =  _extends(new DataSource(requestDatasoureceEngine),obj);
                } else {
                    dataSource =  _extends(new DataSource(localStorageEngine),obj);
                }
                dataSource.load(filter);
                return dataSource;
            }
            return _extends(new DataSource(false),obj);
        };
        this.factoryDataSource = _factoryDatasource;
        var _factoryField = function(obj){
            var field =  _extends(new Field(),obj);
            if(field.dataSource){
                field.dataSource = _factoryDatasource(field.dataSource, field.dataSourceFilter);
            }
            return field;
        };
        var _factoryWidget = function(obj){
            var widget =  _extends(new Widget(),obj);
            widget.fields = widget.fields.map(_factoryField);
            widget.fields.forEach(function(item){
                item.widget = widget;
            });

            if(widget.actions){
                widget.actions.forEach(function(item){
                    item.widget = widget;
                });
            }
            if(widget.dataSource){
                widget.dataSource = _factoryDatasource(widget.dataSource, widget.getDataSourceFilter());
            }
            return widget;
        };
        this.containerFactory = function(obj){
            var container;
            if(!obj.$initialized){
                container     = _extends(new Container(), obj);
                container.widgets = container.widgets.map(_factoryWidget);
                container.widgets.forEach(function(item){
                    item.container = container;
                });
                container.$initialized = true;
            }else{
                container = obj;
                container.widgets.forEach(function(item){
                    item.reload();
                    item.fields.forEach(function(field){
                        field.reload();
                    });
                });
            }
            return container;
        };
        this.wizardFactory = function(obj) {
            var wizard = _extends(new Wizard(), obj);
            wizard.init();
            return wizard;
        };
    };
    window.MetaDataFactory = Factory;
    window.Util = Util;
}).call();
