var WizardEngine = function(renderEngine, templateManager, metadataFactory, eventFactory, eventHandler) {
	var _createStageEvents = function(wizard) {
			var stage  = wizard.currentStage;
			var events = stage.events;
			if (events) {
				var eventFunction = function(e) {
						return eventHandler.notify(e, {"owner": stage, "dataSource" : [], "row" :  templateManager.container.widgets[0].currentRow, "container" : templateManager.container});
				};
				for (var i in events) {
					var currentEvent = events[i];
					var eventObj     = eventFactory.factory(currentEvent.name,currentEvent.id,{},currentEvent.code);
					var eventWidget  = new EventWidget(eventObj, eventFunction);

					switch (currentEvent.name) {
						case "WizardOnDecision":
							stage.onDecision = eventWidget.notify;
							break;
						case "WizardAfterOpenStage":
							stage.afterOpenStage = eventWidget.notify;
							break;
						case "WizardBeforePreviousStage":
							stage.beforePreviousStage  = eventWidget.notify;
							break;
						case "WizardAfterNextStage":
							stage.afterNextStage = eventWidget.notify;
							break;
						case "WizardAfterPreviousStage":
							stage.afterPreviousStage = eventWidget.notify;
							break;
						case "WizardBeforeNextStage":
							stage.beforeNextStage = eventWidget.notify;
							break;
						case "WizardOnFinish":
							stage.onFinish = eventWidget.notify;
							break;
						default:
							throw "Event "+currentEvent+" not mapped!";
					}
				}
			}
		};
		var _executeEvent = function(eventCallback) {
			if (eventCallback) {
				return eventCallback();
			}
		};

		var _choseRoute = function(stage) {
			var routeName = _executeEvent(stage.onDecision);
			if (routeName) {
				var nextStages = stage.nextStages;
				for (var i in nextStages) {
					if (nextStages[i].route === routeName) {
						return nextStages[i].name;
					}
				}
			} else {
				return stage.nextStages[0].name;
			}
		};
		var _moveStage = function(wizard, stage) {
                        return renderEngine.getContainer(stage.window).then(function(){
                            renderEngine.changeContainer(stage.window);
                            _executeEvent(wizard.currentStage.afterOpenStage);
                            wizard.currentStage = stage;
                        });
		};

		var _next = function(wizard) {
			wizard.history.push(wizard.currentStage);
			var chosenRoute  = _choseRoute(wizard.currentStage);
			var stage        = wizard.stages[chosenRoute];
			return _moveStage(wizard, stage);
		};
	this.startWizard = function(container){
		var wizard = metadataFactory.wizardFactory(container);
		_createStageEvents(wizard);
		templateManager.wizard = wizard;
		var containerName = templateManager.wizard.currentStage.window;
		renderEngine.getContainer(templateManager.wizard.currentStage.window).then(function(container){
			renderEngine.changeContainer(containerName);	
			_executeEvent(wizard.currentStage.afterOpenStage);
		});
	};

	this.next = function() {
		var wizard = templateManager.wizard;

		if (wizard.currentStage.nextStages && wizard.currentStage.nextStages.length > 0) {
			// It must be done because the method "_next" updates the value of currentStage.
			var afterNextStageEvent = wizard.currentStage.afterNextStage;
			_executeEvent(wizard.currentStage.beforeNextStage);
			_next(wizard).then(function(){
                            _createStageEvents(wizard);
                            _executeEvent(afterNextStageEvent);
                            _executeEvent(wizard.currentStage.afterOpenStage);                        
                        });
		} else {
			_executeEvent(wizard.currentStage.onFinish);
		}

	};

	this.changeStage = function(stage) {
            /*
             * Disparar Eventos?
             */ 
            stage = templateManager.wizard.stages[stage];
            _moveStage(templateManager.wizard, stage);
        };

	this.previous = function() {
		var wizard              = templateManager.wizard;
		_createStageEvents(wizard);
		var previousStage       = wizard.history.pop();
		if (previousStage) {
			// It must be done because the method "_moveStage" updates the valu of currentStage.
			var afterPreviousStageEvent = wizard.currentStage.afterPreviousStage;
			_executeEvent(wizard.currentStage.beforePreviousStage);
			_moveStage(wizard, previousStage).then(function(){
                            _executeEvent(afterPreviousStageEvent);                            
                        });
		}

	};
};