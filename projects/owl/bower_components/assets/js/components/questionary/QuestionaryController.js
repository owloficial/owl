
var QuestionaryController  = function($scope, $http){
	$scope.$watch('widget.dataSource.data', function(value){
		$scope.questionary = $scope.widget.dataSource.data;
		if(!$scope.questionary.answer){
			$scope.questionary.answer = {};
		}
		if($scope.questionary.questions){
			$scope.questionary.questions.map(function(question){
				if(!$scope.questionary.answer[question.name]){
					$scope.questionary.answer[question.name] = {};
				}
			});
		}		
	});
        $scope.showOtherOption = function(question, questionary){
            questionary.answer[question.name].other_value = true ; 
        };
        $scope.hideOtherOption  = function(question){
            question.other_value = false;
            var answers = $scope.questionary.answer;
            answers[question.name].other_text = "";
            answers[question.name].other_value = false;
            
        };
        window.validateRequiredAnwers =  function(){
				var answers = $scope.questionary.answer;
				var questionNotAnswered = $scope.questionary.questions.filter(function(item){
					return (!answers.hasOwnProperty(item.name) || $scope.isPresent(answers[item.name])) || !item.required;
				});
			var message = questionNotAnswered.map(function(question){
				return parseInt(question.seqno, 10);
			}).sort().join(" , ");
			for(var q in $scope.questionary.questions) {
				$scope.questionary.questions[q].error = false;
			}

			if(questionNotAnswered.length > 0) {
				alert("As questões "+message+" são obrigatorias e não foram respondidas!");
				for(q in questionNotAnswered) {
					questionNotAnswered[q].error = true;
				}

				return false;
			}

			return true;
		};
      window.checkIfAnswered = function() {
          
        var answers = $scope.questionary.answer;
        var questionNotAnswered = $scope.questionary.questions.filter(function(item){
                return (!answers.hasOwnProperty(item.name) || $scope.isPresent(answers[item.name])) || !item.required;
        });
        
        //clean
        for(var j in answers) {
            answers[j].empty = false;
        } 
        
        //set empty atribute
        for(var i in questionNotAnswered) {
            answers[questionNotAnswered[i].name].empty = true;
            
        }
      };
      
      $scope.isPresent = function(answer) {
          return !(
                  !!answer.value                          || //answer type unique or multiple choice
                  $scope.otherChoiceIsPresent(answer)     || //other option is checked
                  $scope.textIsPresent(answer));              //answer type text 
      };
      
      
      $scope.checkIfClean = function(answer, clean) {
          if(clean) {
              answer.other_text = "";
              answer.other_value = false;
          }
      };
      
      $scope.textIsPresent = function(answer) {
        if(!!answer.value) {
          return (typeof(answer.value) == "string" ? answer.value.trim().length > 0 : false);
        }
      };
      
      $scope.otherChoiceIsPresent = function(answer) {
        //check by presence of optional text option  
        if(!!answer.other_value) {
            return (!!answer.other_text) && (answer.other_text.trim().length > 0);
        }
        //check by presence of usual options        
        if(!!answer.options){
            for(var i in answer.options) {
                if(answer.options[i] === true) {
                    return true;
                }
            }
        }        
        return false;
      };
      
      window.cleanQuestionary = function(args) {
          var answers = $scope.questionary.answer;
          for(var i in answers) {
              //console.log(answers[i]);
              if(answers[i].type === "short_text" || answers[i].type === "large_text" || answers[i].type === "numeric") {
                answers[i].value = "";
              } else{
                  if(answers[i].type === "unique") {
                      answers[i].other_value = false;
                      answers[i].other_text = "";
                      answers[i].value = null;
                  } else {
                      if(answers[i].type === "multiple") {
                        answers[i].other_value = false;
                        answers[i].other_text = "";
                        answers[i].options = [];
                      }
                  }
              }
          }
          checkIfAnswered();
          //alert("limpando");
          
      };
      
};
