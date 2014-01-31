

function LanguageChanger($scope, $rootScope, templateManager){

	$scope.languages = Object.keys($scope.words).map(function(word){
		return {
			name : word,
			label : $scope.words[word].$label
		};
	});

	$scope.language = $scope.languages.find(function(lang){
		return lang.name === $scope.lang;
	});

	templateManager.updateTemplate();

	$scope.__changeLanguage = function(lang){		
		$rootScope.$broadcast("__words.change", lang.name);
	};
}