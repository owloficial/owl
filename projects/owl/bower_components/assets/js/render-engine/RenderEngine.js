var EventWidget = function(eventObj, callback){
	var self = this;
	this.e = eventObj;
	this.callback = callback;
	this.notify = function(){
		return self.callback(self.e);
	};
};

var RenderEngine = function(restEngine, i18nEngine, metadataFactory, templateManager, $timeout){
	var self = this;
	var _restEngine = restEngine;
	var scroller = null;
	this.i18n  = function(word, words, lang){
		return i18nEngine.i18n(word, words, lang);
	};
	this.getWidget = function(widgetName, container){
		var widget = container.widgets.filter(function(current){
			return current.name ===  widgetName;
		})[0];
		if(widget){
			return widget;
		}else{
			throw new Error("widget not found");
		}
	};
	this.getContainer = function(containerName){
		var deferred = $.Deferred();
        var container = templateManager.containers[containerName];
        if(!container){
            request("../json/containers/"+containerName+".json",function(containerJson){
                templateManager.containers[containerName] = containerJson;
                deferred.resolve(containerJson);
            });
        }else{
            deferred.resolve(container);
        }				
		return deferred;
	};
	this.bindWidgetDataSource = function(dataSources, container) {
		for (var i = container.widgets.length - 1; i >= 0; i--) {
			var widget = container.widgets[i];
			if(!widget.dataSource && dataSources && dataSources.hasOwnProperty(widget.name)){
				widget.dataSource = metadataFactory.factoryDataSource(dataSources[widget.name], widget.getDataSourceFilter());
			}
		}
	};
	this.setCurrentRow = function(widget, row){
		widget.setCurrentRow(row);
	};

	this.changeContainer = function(containerName) {
		templateManager.container = templateManager.containers[containerName] = metadataFactory.containerFactory(templateManager.containers[containerName]);
		templateManager.container.wizard = templateManager.wizard;
		this.bindWidgetDataSource(templateManager.data, templateManager.container);
	};

	this.resizeWindowElements = function(){
		var windowHeight, headerHeight, footerHeight, contentHeight;

		windowHeight = $(window).height();
		headerHeight = $("header").height();
		footerHeight = $("footer").height();
		contentHeight = windowHeight - headerHeight - footerHeight;

		$('.tabsHeight').css('width', contentHeight);

		var widgetActions = $('.zh-actions').height();
		var widgetScroll = $('.zh-scroller').height();

		// $('.zh-scroller').css('height', widgetScroll - widgetActions);
		$('.zh-scroller').each(function(){
			var parentHeight = $(this).parent().height();
			var widgetActions = $(this).parent().find('.zh-actions').height();
			$(this).css('height', parentHeight - widgetActions);
		});
	};

	this.renderInfiniteScroller = function(element, infiniteCallback){
		var scroller = new IScroll(element[0], {
			probeType: 3,
			preventDefault : false
		});  
		templateManager.container.scrollers = templateManager.container.scrollers || [];
		element.attr('index', templateManager.container.scrollers.length);
		templateManager.container.scrollers.push(scroller);				
		element.attr('lastScroll', 0);
		setTimeout(function() {
			window.reloadSize();
		});
	};
};