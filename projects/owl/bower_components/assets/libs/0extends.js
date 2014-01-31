var _ref, __hasProp = {}.hasOwnProperty;
var __extends = function(child, parent) { 
	for (var key in parent) { 
		if (__hasProp.call(parent, key)) child[key] = parent[key]; 
	} 
function ctor() { 
	this.constructor = child; 
} 
ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };
Number.prototype.formatMoney = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "." : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
var getDateBr = function (d){
	month = d.getMonth()+1;
	if(month <10 ){
		month = "0"+month;
	}
	day = d.getDate();
	if(day <10){
		day = "0"+day;
	}
	var date_str = day+"/"+month+"/"+d.getFullYear()
	return date_str;
};
Date.prototype.getDateBr = function(){
	return getDateBr(this);
};
Date.prototype.addDays = function (e){
return this.addMilliseconds(e*86400000);
};
Date.prototype.addMilliseconds = function (e){
this.setMilliseconds(this.getMilliseconds()+e);
return this
};