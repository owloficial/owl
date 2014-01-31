var I18nEngine = function(){
	this.i18n  = function(word, words, lang){
		var wordTrl = false, langWords = false;
		if(words[lang]){
			langWords = words[lang];
		}else if(words['default']){
			langWords = words['default'];
		}
		if(langWords[word]){
			wordTrl = langWords[word];
		}
      return wordTrl || word;
	};
};