
function URLChanger(templateManager, localStorageEngine){

	var projectName = "/" + templateManager.project.name + "DefaultURL";

	localStorageEngine.getDataSource(projectName).then(function(projectConfig){
		$scope.defaults = projectConfig;
	});

	$scope.__changeURL = function(){
		localStorageEngine.setDataSource(projectName, $scope.defaults);
	};
}