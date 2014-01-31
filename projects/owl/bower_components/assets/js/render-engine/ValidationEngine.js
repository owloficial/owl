var ValidationEngine = function(){
    var isDateGreater = function (date1, date2) {
        var minDateArray = date2.split(new RegExp('[ /:]'));
        var minDateObj   = new Date(minDateArray[2], parseInt(minDateArray[1], 10)-1, minDateArray[0], 0, 0, 0);

        var dateArray = date1.split(new RegExp('[ /:]'));
        var dateObj   = new Date(dateArray[2], parseInt(dateArray[1], 10)-1, dateArray[0], 0, 0, 0);

        return dateObj.getTime() > minDateObj.getTime();
    };
    var isHourGreater = function(date1, date2) {
        var minDateArray = date2.split(new RegExp('[ /:]'));
        var minDateObj   = new Date(0, 0, 0, minDateArray[3], minDateArray[4], minDateArray[5]);

        var dateArray = date1.split(new RegExp('[ /:]'));
        var dateObj   = new Date(0, 0, 0, dateArray[3], dateArray[4], dateArray[5]);

        return dateObj.getTime() > minDateObj.getTime();
    };
	this.min = function(value, validationAttrs){
		var limit = window.parseFloat(validationAttrs.limit);
		value = window.parseFloat(value);
		return {"valid" : limit <= value, "message" : "Valor minimo "+validationAttrs.limit+""};
	};

	this.max = function(value, validationAttrs){
		var limit = window.parseFloat(validationAttrs.value);
		value = window.parseFloat(value);
		return {"valid" : limit >= value, "message" : "Valor maximo "+validationAttrs.value+""};
	};

    this.equalsTo = function(value, validationAttrs) {
        return {
            "valid" : value === validationAttrs.text,
            "message" : "Os valores digitados não coincidem"
        };
    };

    this.mail = function(value) {
        var pattern = new RegExp('^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$i');
        return {
            "valid" : pattern.test(value),
            "message": "Email inválido."
        };
    };

    this.url = function(value) {
        var protocols = ["http", "https", "ftp", "ssh"];
        var pattern   = new RegExp("(" + protocols.join("|") + "?\\:\\/\\/)?([\\da-z\\.-]+)(\\:\\d)?(\\.([a-z\\.]{2,6})([\\/\\w \\.-]*)*\\/)?$");

        return {
            "valid" : pattern.test(value),
            "message": "URL inválida."
        };
    };

    this.dateIsValid = function (value) {
        var dateArray  = value.split("/");
        var dateObj    = new Date(dateArray[2], parseInt(dateArray[1], 10)-1, dateArray[0]);
        var isValid    = parseInt(dateObj.getFullYear(), 10) === parseInt(dateArray[2], 10)   &&
                         parseInt(dateObj.getMonth(), 10)+1  === parseInt(dateArray[1], 10)   &&
                         parseInt(dateObj.getDate(), 10)     === parseInt(dateArray[0], 10);

        return {
            "valid": isValid,
            "message": "Data inválida."
        };
    };

    this.dateTimeIsValid = function(value) {
        var dateArray  = value.split(new RegExp('[ /:]'));
        var dateObj    = new Date(
            dateArray[2], parseInt(dateArray[1], 10)-1, dateArray[0], dateArray[3], dateArray[4], dateArray[5]
        );

        var isValid    = parseInt(dateObj.getFullYear(), 10) === parseInt(dateArray[2], 10)   &&
                         parseInt(dateObj.getMonth(), 10)+1  === parseInt(dateArray[1], 10)   &&
                         parseInt(dateObj.getDate(), 10)     === parseInt(dateArray[0], 10)   &&
                         parseInt(dateObj.getHours(), 10)    === parseInt(dateArray[3], 10)   &&
                         parseInt(dateObj.getMinutes(), 10)  === parseInt(dateArray[4], 10)   &&
                         parseInt(dateObj.getSeconds(), 10)  === parseInt(dateArray[5], 10);

        return {
            "valid": isValid,
            "message": "Data inválida."
        };
    };

    this.dateGreaterThan = function (value, validationAttrs) {
        var isGreater = isDateGreater(value, validationAttrs.minimumDate);
        return {
            "valid": isGreater,
            "message": "A data atual deve ser maior que a data anterior."
        };
    };

    this.dateLessThan = function (value, validationAttrs) {
        var isLess = !isDateGreater(value, validationAttrs.maximumDate);
        return {
            "valid": isLess,
            "message": "A data atual deve ser menor que a data anterior."
        };
    };

    this.hourGreaterThan = function(value, validationAttrs) {
        var isGreater = isHourGreater(value, validationAttrs.minimumHour);
        return {
            "valid": isGreater,
            "message": "Este horário deve ser maior que o anteior."
        };
    };

    this.hourLessThan = function(value, validationAttrs) {
        var isLess = !isHourGreater(value, validationAttrs.maximumHour);
        return {
            "valid": isLess,
            "message": "Este horário deve ser menor que o anteior."
        };
    };
};