var ZeedhiTodo = angular.module('ZeedhiTodo', []);

ZeedhiTodo.directive('storyContainerHeader', [function(){

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

ZeedhiTodo.directive('subtaskData', [function(){
	return {		
		restrict: 'C',
		link: function($scope, iElm, iAttrs, controller) {
			iElm.on('mouseenter',function(){
				var buttons = iElm.children().eq(0);
				if (buttons.hasClass('subtask-buttons')) {
					buttons.removeClass('hide');
				}
			});
			iElm.on('mouseleave',function(){
				var buttons = iElm.children().eq(0);
				if (buttons.hasClass('subtask-buttons')) {
					buttons.addClass('hide');
				}
			});
		}
	};
}]	);