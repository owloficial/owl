/**
* ZH- Currency
*/
$.zerofill = {
    options: {
        length: 4
    },
    blurEvent: function(evt) {
        var value = $(this).val();
        var length = $(this).attr('maxlength') ? $(this).attr('maxlength') : 4;
        if(!value) return;
        value = $.zerofill.string(value, length);
        $(this).val(value);
    },
    init: function(input) {
        input.bindFirst('change', this.blurEvent);
    },
    destroy: function(input) {
        input.unbind('change', this.blurEvent);
    },
    string: function(value, length) {
        if (value) {
            value = value.toString();
            while (value.length < length) value = "0" + value;
        }

        return value;
    },
    unmaskString: function(value) {
        return value;
    }
};