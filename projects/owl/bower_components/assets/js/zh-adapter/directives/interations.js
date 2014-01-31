var ZeedhiInterations = angular.module("ZeedhiInterations", []);
//=======================================


ZeedhiInterations.directive('zhToggleClass', function() {
    return function(scope, elem, attrs) {
        var selection           = {};
        selection.siblings   = elem.siblings;
        selection.closest    = elem.closest;
        selection.parents    = elem.parents;
        var eventType = attrs.event ? attrs.event : 'click';
        elem.bind(eventType, function(event){
            attrs.zhToggleClassSelector = attrs.zhToggleClassSelector ? attrs.zhToggleClassSelector : 'siblings';
            var zhToggleClass = attrs.zhToggleClassTo ? selection[attrs.zhToggleClassSelector].call(elem, attrs.zhToggleClassTo) : elem;
            var container = attrs.zhToggleClass;
            var parent;
            if(zhToggleClass.hasClass('disabled') || attrs.categoriaOn === false){
                event.stopPropagation();
                return;
            }
            if(zhToggleClass.hasClass('selected')){
                zhToggleClass.removeClass("selected");
                return;
            }
            if(attrs.zhMultiple === undefined || attrs.zhMultiple === false){
                if (container){
                    parent = zhToggleClass.closest(container);
                } else {
                    parent = zhToggleClass.parent();
                }
                var selector = attrs.zhToggleClassTo ?  attrs.zhToggleClassTo : '[zh-toggle-class="' + attrs.zhToggleClass + '"]';
                parent.find(selector).removeClass('selected');
            }
            zhToggleClass.addClass('selected');
        });
    };
});
ZeedhiInterations.directive("zhGridRow", function(templateManager){
    return {
        priority:500,
        link : function($scope, iElm, iAttrs, controller){

            iElm.on("click",function(e){
                $scope.setFormData($scope.widget,$scope.row);
                $scope.widget.editing = true;
                templateManager.menuVisible(false);
                $scope.$apply();
                $(".tipLeft").remove();
                var y = $(this).position().top + 56;
                $(".slideGridForm ").append("<span class='tipLeft' style='top:"+y+"px;'></span>");
            });
        }
    };
});

ZeedhiInterations.directive('zhSlideForm', function(templateManager){
    return {
        restrict: 'A',
        link: function($scope, $element, iAttrs, controller) {
            var widget = $scope.widget;
            widget.hideForm = function(){
                if ($element.hasClass("in") || $element.hasClass("infull") || $element.hasClass("out")) {
                    $element.removeClass();
                    widget.editing = false;
                    $element.addClass("zh-widget slideGridForm outfull");
                }
                widget.editing = false;
            };
            widget.middleHideForm = function(){
                $element.removeClass();
                $element.addClass("zh-widget slideGridForm out zh-shadow");
            };
            widget.middleShowForm = function(){
                $element.removeClass();
                $element.addClass("zh-widget slideGridForm infull zh-shadow");
                $scope.menuVisible(false);
                $scope.$apply();                
            };
            widget.showForm = function(){
                $element.removeClass();
                $element.addClass("zh-widget slideGridForm in zh-shadow");
            };            
            $scope.$watch('widget.editing', function(value){
                if (value === true){
                    $element.removeClass();
                    $element.addClass("zh-widget slideGridForm in zh-shadow");
                } else if (value === false && ($element.hasClass('out') || $element.hasClass('in'))) {
                    $element.removeClass();
                    $element.addClass("zh-widget slideGridForm outfull");
                }
            });
            Zepto($element[0]).swipeRight(function(){
                if ($(this).hasClass("infull")) {
                    $element.removeClass();
                    $element.addClass("zh-widget slideGridForm out zh-shadow");
                } else {
                    widget.editing = false;
                    widget.hideForm();
                }
                templateManager.menuVisible(false);
                $scope.$apply();
            });
            Zepto($element[0]).swipeLeft(function(){
                $element.removeClass();
                $element.addClass("zh-widget slideGridForm infull zh-shadow");
                templateManager.menuVisible(false);
                $scope.$apply();
            });
            $element.on("click", function(){
                templateManager.menuVisible(false);
                $scope.$apply();
            });
        }
    };
});


ZeedhiInterations.directive('zhSlideMenu', function(){
    return {
        restrict: 'A',
        link: function($scope, iElm, iAttrs, controller) {
            $scope.$watch('templateManager.project.menuVisible', function(value){
                var element = $(iElm[0]);
                if(value === true){
                    element.removeClass("out");
                    element.addClass("in zh-shadow");
                } else if (value === false && element.hasClass('in')){
                    element.removeClass("in");
                    element.removeClass("zh-shadow");
                    element.addClass("out");
                }
            });
        }
    };
});

ZeedhiInterations.directive('zhMenuControl', function(templateManager){
    return {
        restrict: 'A',
        link: function($scope, $element, iAttrs, controller) {
            $element.on("click",function(){
                templateManager.menuVisible(!templateManager.project.menuVisible);
            });
        }
    };
});

ZeedhiInterations.directive('zhScroller', ["$timeout", "templateManager", function ($timeout, templateManager) {
    return {
        restrict: 'A',
        link: function ($scope, iElement, iAttrs) {
            if(Zepto.os.phone || Zepto.os.tablet ){
                $scope.scrollers = $scope.scrollers || [];
                $timeout(function(){
                    $scope.renderInfiniteScroller(iElement, function(){
                        $scope.widget.nextPage();
                    });
                });
            } else {
                iElement.css("overflow","auto").css('height','100%');
            }
        },
        priority : 10
    };
}]);

ZeedhiInterations.directive('zhUpdateScroller', ["$timeout", "templateManager", function ($timeout, templateManager) {
    return {
        restrict: 'A',
        link: function ($scope, element, iAttrs) {
            var eventType = iAttrs.zhUpdateScroller || "click";
            element.on(eventType, function(){
                $timeout(function(){
                    for(var i in templateManager.container.scrollers){
                        templateManager.container.scrollers[i].refresh();
                    }                    
                });
            });
        }
    };
}]);


ZeedhiDirectives.directive('scrollInfinito', function(templateManager, $timeout) {
    return {
        link : function(scope, element, attrs) {

            var pullUpEl = angular.element(document.getElementById('pullUp'));   
            var pullUpOffset = pullUpEl[0].offsetHeight;

            if(!Zepto.os.phone && !Zepto.os.tablet ){
                angular.element('.pullUpLabelMore', pullUpEl).text("Click para ver mais");
                pullUpEl.bind('click', function(){
                    var size = scope.widget.dataSource.data.length;
                    scope.widget.dataSource.load({}, scope.widget.dataSource.getNextPage(), function(data){
                        if(data.length === size){
                            var current = scope.widget.dataSource.getCurrentPage();
                            scope.widget.dataSource.setCurrentPage(current -1);
                        } else {                                
                            templateManager.updateTemplate();
                        }
                    });                    
                });
            } else 
            $timeout(function(){
                var scroller = templateManager.container.scrollers[element.attr('index')];

                scroller.on('refresh', function () {
                    if (pullUpEl.hasClass('loading')) {
                        pullUpEl.removeClass('loading');
                        pullUpEl.find('.pullUpLabelMore').show();
                        pullUpEl.find('.pullUpLabel').hide();
                    }
                });

                scroller.on('scroll', function () {
                    if (this.y < (this.maxScrollY - 25) && !pullUpEl.hasClass('flip')) {
                        pullUpEl.addClass('flip');
                        pullUpEl.find('.pullUpLabelMore').hide();
                        pullUpEl.find('.pullUpLabel').show();
                        this.maxScrollY = this.maxScrollY;
                    } else if (this.y > (this.maxScrollY + 25) && pullUpEl.hasClass('flip')) {
                        pullUpEl.removeClass('flip');
                        pullUpEl.find('.pullUpLabelMore').show();
                        pullUpEl.find('.pullUpLabel').hide();
                        this.maxScrollY = pullUpOffset;
                    }
                });

                scroller.on('scrollEnd', function () {
                    if (pullUpEl.hasClass('flip')) {
                        pullUpEl.removeClass('flip');
                        pullUpEl.addClass('loading');
                        var size = scope.widget.dataSource.data.length;
                        scope.widget.dataSource.load({}, scope.widget.dataSource.getNextPage(), function(data){
                            if(data.length === size){
                                var current = scope.widget.dataSource.getCurrentPage();
                                scope.widget.dataSource.setCurrentPage(current -1);
                            } else {                                
                                templateManager.updateTemplate();
                                scroller.refresh();
                            }
                        });
                    }
                });
            });
        }
    };
});


ZeedhiInterations.directive('zhTabsVertical', ["$timeout",function ($timeout) {
    return {
        restrict: 'A',
        link: function ($scope, iElement, iAttrs) {
            $scope.$watch('templateManager.project.historyItems', function(item){
                $timeout(function(){
                    var tabsContainer = $(iElement[0]);
                    if (tabsContainer.is(':visible')) {
                        tabsHeight = 0;
                        tabsContainer.children('li').each(function(){
                            tabsHeight += $(this).width();
                        });
                        tabsWidth = tabsContainer.children().height();
                        tabsContainer.css({width: tabsHeight, left: -(tabsHeight/2)+(tabsWidth/2), marginTop: -tabsWidth/2, top: tabsHeight/2});
                        $scope.resizeWindowElements();
                    }
                });
            },true);
        }
    };
}]);

ZeedhiInterations.directive('zhTabsVerticalItem', function(){
    return {
        restrict: 'A',
        link: function($scope, iElement, iAttrs){
            iElement.on('click', function(){
                $(this).addClass('active');
                $(this).siblings().removeClass('active');
                $scope.openWindow($scope.item);
                $scope.$apply();
            });
        }
    };
});

ZeedhiInterations.directive('zhMessage', function(){
    return {
        restrict: 'A',
        link: function($scope, iElement, iAttrs){
            var content = $(".zh-content");
            var timer = null;
            $scope.$watch('messagesToShow', function(messages, oldMessages){
                if (messages.length > 0) {
                    angular.forEach(messages, function(message, key){
                        if (iElement.not(":visible")) {
                            iElement.slideDown(200).addClass(message.type);    
                        }
                        iElement.children('.messagesEntry').html(message.message);

                        if (!iElement.data('resized')){
                            iElement.data('resized', true);

                            var contentHeight = content.height();
                            content.animate({height: contentHeight-50 + "px"}, 200);
                        }
                        $scope.messagesToShow.splice(key);
                    });
                    timer = setTimeout(hideMessage, 6000);
                }
            },true);

            var hideMessage = function () {
                iElement.slideUp(200);
                iElement.children('.messagesEntry').html("");
                iElement.data('resized',false);
                content.removeAttr('style');
            };

            iElement.children(".messagesClose").on("click",function(){
                hideMessage();
                clearTimeout(timer);
            });
        }
    };
});

ZeedhiInterations.directive('zhMenuDropDowm', function($timeout, templateManager){
    return {
        restrict: 'A',
        link: function($scope, iElm, iAttrs, controller) {
            $timeout(function(){
                var processMenu = function(menuObj, clickedNode){
                    var nodeInMenu = menuObj.find(clickedNode);
                    if(!nodeInMenu.hasClass('active')){
                        nodeInMenu.removeClass('inactive').addClass('active');
                        nodeInMenu.siblings().removeClass('active').addClass('inactive');
                        nodeInMenu.children('.group').css('display', 'block');
                    } else {
                        var childrens = nodeInMenu.find('.active');
                        if(childrens.length){
                            angular.forEach(childrens, function(children){
                                Zepto(children).removeClass('active').addClass('inactive');
                                Zepto(children).children('.group').css('display', 'none');
                            });
                        }
                        if (nodeInMenu.children('.group').length){
                            nodeInMenu.removeClass('active').addClass('inactive');
                            nodeInMenu.children('.group').css('display', 'none');
                        }
                    }

                    return menuObj;
                };

                var expandAllMenu = function(menuObj){
                    menuObj.find('.group').css('display','block');
                    menuObj.find('.group').parent('li').removeClass('inactive').addClass('active');
                    return menuObj;
                };

                var colapseAllMenu = function(menuObj){
                    menuObj.find('.group').css('display','none');
                    menuObj.find('.active').removeClass('active').addClass('inactive');
                    return menuObj;
                };

                var content = iElm.find('li.zh-menu-dropdowm-item');
                content.on('click', function(e) {
                    var parent = iElm;
                    var menuObj = parent.children('nav').detach();
                    menuObj = processMenu(menuObj, this);
                    parent.append(menuObj);
                    e.stopPropagation();
                });

                $scope.$watch('menuFilter', function(value) {
                    var parent = iElm;
                    var menuObj = parent.children('nav').detach();
                    if(value){
                        menuObj = expandAllMenu(menuObj, this);
                        parent.append(menuObj);
                    }else{
                        menuObj = colapseAllMenu(menuObj, this);
                        parent.append(menuObj);
                    }
                });

                $scope.$watch('templateManager.container', function(){
                    templateManager.menuVisible(false);
                    var parent = iElm;
                    var menuObj = parent.children('nav').detach();
                    menuObj = colapseAllMenu(menuObj);
                    parent.append(menuObj);
                });
            },200);

        }
    };
});

ZeedhiInterations.directive('zhBody', function($timeout, templateManager){
    return {
        restrict: 'A',
        link : function(scope, iElm, iAttrs){
            var reloadSize = function(){
                var headerHeight, footerHeight, contentHeight,

                windowHeight = $(window).height();
                headerHeight = $("header").height();
                footerHeight = $("footer").height();
                contentHeight = windowHeight - headerHeight - footerHeight;

                $('.tabsHeight').css('width', contentHeight);
                
                $timeout(function(){
                    for(var i in templateManager.container.scrollers){
                        templateManager.container.scrollers[i].refresh();
                    }      
                    var field = $( document.activeElement );
                    var closest = field.closest('[zh-scroller]');
                    if (field && closest && templateManager.container.scrollers){
                        var scroller = templateManager.container.scrollers[closest.data('index')];
                        if(scroller)
                            scroller.scrollToElement(field[0], 200);
                    }
                });
        };
        $(window).resize(reloadSize);
        window.reloadSize = reloadSize;
    }
};
});

ZeedhiInterations.directive('zhGridHeader', function(templateManager){
    return {
        restrict: 'A',
        link: function($scope, iElm, iAttrs, controller) {
            iElm.on('click', function(){
                $scope.widget.orderByValue = $scope.column.name;
                $scope.column.orderReverse = !$scope.column.orderReverse;
                $scope.widget.orderReverse = $scope.column.orderReverse;
                $scope.$apply();
            });
        }
    };
});
ZeedhiInterations.directive('zhExpandWidget', function(templateManager){
    return {
        restrict: 'A',
        link: function($scope, iElm, iAttrs, controller) {
            iElm.on('click', function(e){
                $scope.widget.expand = !$scope.widget.expand;
                if($scope.widget.expand) {
                    $scope.widget.oldHeight = $scope.widget.height;
                    $scope.widget.height = "100%";
                } else {
                    $scope.widget.height = $scope.widget.oldHeight;
                }
                $scope.$apply();
                e.stopPropagation();
            });
        }
    };
});

ZeedhiInterations.directive('zhStoryContainerHeader', [function(){
    return {
        restrict: 'C',
        link: function($scope, iElm, iAttrs, controller) {
            iElm.on('click',function(){
                var storyContainerBody = iElm.next();
                storyContainerBody.toggleClass('hide');
            });
        }
    };
}]);
