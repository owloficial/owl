function EventEngine(eventFactory, eventHandler, templateManager){
    var eventNameToEventFunction = { 'WidgetAfterMoveRow' : "afterMoveRow", 'WidgetBeforeMoveRow' : "beforeMoveRow",'WidgetNewRow' : 'newRow','WidgetOnEnter' : 'onEnter', "WidgetOnSync": "onSync", 'WidgetOnExitWidget' : 'onExitWidget','ContainerAfterClose' : 'afterClose','ContainerBeforeClose' : 'beforeClose','ContainerAfterinit' : 'afterinit','ContainerBeforeinit' : 'beforeinit','ContainerAfterOpenreport' : 'afterOpenreport','WizardOnDecision' : 'onDecision','WizardAfterOpenStage' : 'afterOpenStage','WizardBeforePreviousStage' : 'beforePreviousStage','WizardAfterNextStage' : 'afterNextStage','WizardAfterPreviousStage' : 'afterPreviousStage','WizardBeforeNextStage' : 'beforeNextStage','WizardOnFinish' : 'onFinish','FieldOnChange' : 'change','FieldOnClick' : 'click','FieldOnBlur' : 'blur', 'ActionEvent' : 'click', 'onUpdate' : 'onUpdate'};
    var eventsWhenNotUpdateTemplete = ['onUpdate'];

    //These auxiliary functions are used within the for cycle.
    //This function is called in the case 'FieldOnTap'
    function eventResouver() {eventWidget.notify(); templateManager.updateTemplate();}
    this.bindEvents = function(owner, events, dataSourceCallback, rowCallback ){
      if(!owner.bindedEvents){
          var eventCallback = function(e){
                  eventHandler.notify(e, {"owner": owner, "dataSource" : dataSourceCallback ? dataSourceCallback() : [], "row" :  rowCallback ? rowCallback() : {}});
          };
          
         for (var i = 0; i < events.length; i++) {
              var currentEvent = events[i];
              var eventObj = eventFactory.factory(currentEvent.name,currentEvent.id,{},currentEvent.code, currentEvent.serviceName, currentEvent.requestType);
              var eventWidget = new EventWidget(eventObj, eventCallback);
              switch(currentEvent.name){
                  case 'FieldOnTap':
                      owner.on('tap', eventResouver);
                      break;
                  case 'FieldOnLongTap':
                      owner.on('longtap', eventResouver);
                      break;
                  default:
                      treatDefaultEvent(currentEvent, owner, eventWidget);
                      break;
              }
          }
          owner.bindedEvents=true;
        }
    };
    function treatDefaultEvent(currentEvent, owner, eventWidget ){
        if(eventNameToEventFunction.hasOwnProperty(currentEvent.name)){
            var methodName = eventNameToEventFunction[currentEvent.name];
            var updateTemplate = function(){
                  if(eventsWhenNotUpdateTemplete.indexOf(methodName)==-1){
                    templateManager.updateTemplate();
                  }
            };
            if (owner[methodName]) {
                if (owner.off) {
                    owner.on(methodName, function(e) {
                      // if(e._scrollerEvent){
                        if(e.__ignore__ === undefined){
                          eventWidget.notify();
                          updateTemplate();
                        }
                      // }
                    });
                } else {
                    owner[methodName](function() {
                        eventWidget.notify();
                        updateTemplate();
                    });
                }
            }else{
                createEvent(owner, methodName);
                owner[methodName](function(){ 
                  eventWidget.notify(); 
                  updateTemplate();
                });
            }
        }else{
            throw "Event "+currentEvent.name+" not maped!";
        }
    }
    function createEvent(owner, eventName, bindFirst){
        owner[eventName] = function(eventCode){
                if(!owner[eventName+'listener']){
                    owner[eventName+'listener'] = [];
                }
                if(eventCode){
                    if(bindFirst){
                        owner[eventName+'listener'].unshift(eventCode);
                    }else{
                        owner[eventName+'listener'].push(eventCode);
                    }
                }else{
                    var listeners = owner[eventName+'listener'];
                    for (var i = listeners.length - 1; i >= 0; i--) {
                        listeners[i]();
                    }
                }
        };
    }
}
var EventFactory = function(ApplicationContext) {
  this.factory = function(name, id, args, code, serviceName, requestType) {
    if(code instanceof Function ){
      var method = code;
      /*jslint evil: true */
      eval('var event = new '+name+'("'+id+'", method, serviceName, requestType);');
    }else{
      eval('var method = null; if(code) {var ctx = ApplicationContext; var method = (function(args){'+code+'}) } var event = new '+name+'("'+id+'", method, serviceName, requestType);');
    }
    return event;
  };
};
var EventHandler = function(restEngine, requestFactory) {
  /**
   * Do request
   *
   * @param EventBase event
   * @param Object args {
     owner: {},
     datasource: [],
     rowIdx: int,
     colIdx: int,
     waitFor: {}
    }
   *
   */
  this.notify = function(evt, args) {
    this.waitFor(evt);

    if(evt.method) {
      return evt.method(args);
    } else {
      //this.add(evt);
      return evt.request(restEngine, this, requestFactory,args);
    }
  };

  this.waitFor = function(evt) {
    return evt;
  };

  this.add = function(evt) {
    //eventList[evt.getId()] = evt;
  };

  this.remove = function(evt) {
    //delete eventList[evt.getId()];
  };

  this.notifyEnd = function(evt) {
    this.remove(evt);
  };
};
/**
 * Base class for events.
 */
EventBase = (function() {
  function EventBase(eventId, method, serviceName, requestType) {
    var self = this;
    this.args = null;
    this.id = eventId;
    this.method = method;
    this.serviceName = serviceName;
    this.requestType = requestType;
    this.getServiceName = function() {
        return this.serviceName;
      };

      this.getTypeRequest = function() {
        return this.args.typeRequest;
      };

      //This auxiliary function is used within the for cycle, in line 164.
      function aux1(item){return '"'+item+'"';}
      
      this.getRequestCallback = function() {
        return function(response, args) {
          for (var i in response.method){
            var method = response.method[i];
            var params = method.parameters.map(aux1).join(',');
            params += params ? ", args" : "args";
            /*jslint evil: true */
            eval(method.name+"("+params+");");
          }
          for ( i in response.messages){
            var message = response.messages[i];
            alert(message.message);
          }          
          //console.log(response.method);
        };
      };
      this.getId = function(){
        return this.id;
      };
      this.request = function(restEngine,eventHandler, requestFactory, args){
        this.args = args;
        var callback = function(response) {
          self.getRequestCallback()(response, args);
          return eventHandler.notifyEnd(self);
        };

        var request = requestFactory.factory({"serviceName": self.serviceName, "row" : args.row, 'requestType' : self.requestType, "callBack" : self.getRequestCallback() ,"dataSource" : args.dataSource });
        restEngine.request(
          request.getServiceName(),
          request.getRequestParams(),
          callback
        );
      };
  }
  return EventBase;
})();

FieldOnClick = (function(_super) {
  __extends(FieldOnClick, _super);

  function FieldOnClick() {
    _ref = FieldOnClick.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return FieldOnClick;

})(EventBase);
FieldOnChange = (function(_super) {
  __extends(FieldOnChange, _super);
  function FieldOnChange() {
    _ref = FieldOnChange.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return FieldOnChange;

})(EventBase);

FieldOnBlur = (function(_super) {
  __extends(FieldOnBlur, _super);

  function FieldOnBlur() {
    _ref = FieldOnBlur.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return FieldOnBlur;

})(EventBase);

FieldOnTap = (function(_super) {
  __extends(FieldOnTap, _super);

  function FieldOnTap() {
    _ref = FieldOnTap.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return FieldOnTap;

})(EventBase);

FieldOnLongTap = (function(_super) {
  __extends(FieldOnLongTap, _super);

  function FieldOnLongTap() {
    _ref = FieldOnLongTap.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return FieldOnLongTap;

})(EventBase);

WidgetAfterMoveRow = (function(_super) {
  __extends(WidgetAfterMoveRow, _super);

  function WidgetAfterMoveRow() {
    _ref = WidgetAfterMoveRow.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WidgetAfterMoveRow;

})(EventBase);

WidgetBeforeMoveRow = (function(_super) {
  __extends(WidgetBeforeMoveRow, _super);

  function WidgetBeforeMoveRow() {
    _ref = WidgetBeforeMoveRow.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WidgetBeforeMoveRow;

})(EventBase);

WidgetOnEnter =(function(_super) {
  __extends(WidgetOnEnter, _super);

  function WidgetOnEnter() {
    _ref = WidgetOnEnter.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WidgetOnEnter;

})(EventBase);

WidgetOnSync = (function(_super) {
  __extends(WidgetOnSync, _super);

  function WidgetOnSync() {
    _ref = WidgetOnSync.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WidgetOnSync;

})(EventBase);

ContainerBeforeinit = (function(_super) {
  __extends(ContainerBeforeinit, _super);

  function ContainerBeforeinit() {
    _ref = ContainerBeforeinit.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return ContainerBeforeinit;

})(EventBase);
ContainerAfterinit = (function(_super) {
  __extends(ContainerAfterinit, _super);

  function ContainerAfterinit() {
    _ref = ContainerAfterinit.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return ContainerAfterinit;

})(EventBase);
ActionEvent = (function(_super) {
  __extends(ActionEvent, _super);

  function ActionEvent() {
    _ref = ActionEvent.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return ActionEvent;

})(EventBase);


// Wizard Events...
WizardOnDecision = (function(_super) {
  __extends(WizardOnDecision, _super);

  function WizardOnDecision() {
    _ref = WizardOnDecision.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WizardOnDecision;

})(EventBase);
WizardAfterOpenStage = (function(_super) {
  __extends(WizardAfterOpenStage, _super);

  function WizardAfterOpenStage() {
    _ref = WizardAfterOpenStage.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WizardAfterOpenStage;

})(EventBase);
WizardBeforePreviousStage = (function(_super) {
  __extends(WizardBeforePreviousStage, _super);

  function WizardBeforePreviousStage() {
    _ref = WizardBeforePreviousStage.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WizardBeforePreviousStage;

})(EventBase);
WizardAfterNextStage = (function(_super) {
  __extends(WizardAfterNextStage, _super);

  function WizardAfterNextStage() {
    _ref = WizardAfterNextStage.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WizardAfterNextStage;

})(EventBase);
WizardAfterPreviousStage = (function(_super) {
  __extends(WizardAfterPreviousStage, _super);

  function WizardAfterPreviousStage() {
    _ref = WizardAfterPreviousStage.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WizardAfterPreviousStage;

})(EventBase);
WizardBeforeNextStage = (function(_super) {
  __extends(WizardBeforeNextStage, _super);

  function WizardBeforeNextStage() {
    _ref = WizardBeforeNextStage.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WizardBeforeNextStage;

})(EventBase);
WizardOnFinish = (function(_super) {
  __extends(WizardOnFinish, _super);

  function WizardOnFinish() {
    _ref = WizardOnFinish.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return WizardOnFinish;

})(EventBase);
var onUpdate = (function(_super) {
  __extends(onUpdate1, _super);

  function onUpdate1() {
    _ref = onUpdate.__super__.constructor.apply(this, arguments);
    return _ref;
  }

  return onUpdate1;

})(EventBase);


  