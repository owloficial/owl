
var Report = function(){
	this.group = function(groups) {
		this.groupGroups = function(groups){
			var firstGroup = groups[0];
			groups.map(function(itemData,i){
				if( i  !== 0)
					firstGroup.group = itemData;
				firstGroup = itemData; 
			});
			return groups[0];
		};
		this.bandProcessFactory = function(data, band){
			return function(band){
				
			};
		};
		this.groupData = function(data, bands){
			var newData = bands.map(function(band){
				var bandData = {};
				band.fields.forEach(function(field){
					bandData[field.name] = item[field.name];
				});
				if(band.bands){

				}
				return bandData;
			});
		};
	};

};