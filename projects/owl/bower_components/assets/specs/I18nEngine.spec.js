describe("I18N", function() {
	var words = {
					"pt_br" : {
						"name" : "nome"
					},
					"en_us" : {
						"name" : "name"
					},
					"es" : {

					},
					"default" : {
						"name" : "nome"
					}
				};
	it("deve existir I18nEngine",function(){
		expect(I18nEngine).not.toEqual(undefined);
	});
	it("deve deve traduzir coretamente",function(){
		var i18n = new I18nEngine();
		expect(i18n.i18n('name', words, 'pt_br')).toEqual("nome");
	});	
});