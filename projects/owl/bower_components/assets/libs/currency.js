/**
    ZH- Currency
*/
$.currency = {
    options: {
        symbol: 'US$',
        showSymbol: false,
        symbolStay: true,
        thousands: ',',
        decimal: '.',
        precision: 2,
        defaultZero: true,
        allowZero: true,
        allowNegative: true
    },
    addThousandSeparators: function(nStr, decimalSeparator, thousandSeparator) {
        nStr += '';
        var x = nStr.split(decimalSeparator);
        var x1 = x[0];
        var x2 = x.length > 1 ? decimalSeparator + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + thousandSeparator + '$2');
        }

        return x1 + x2;
    },
    string: function(value, opt) {
        if (value) {
            if(opt.showSymbol) {
                value = value.replace(opt.symbol, '');
            }

            var strValue = parseFloat(value).toFixed(opt.precision).toString();

            if(opt.decimal) {
                strValue = strValue.replace('.', opt.decimal);
            }

            if(opt.thousands) {
                strValue = this.addThousandSeparators(strValue, opt.decimal, opt.thousands);
            }

            if(opt.showSymbol) {
                strValue = opt.symbol + '' + strValue;
            }
        }

        return strValue;
    },
    unmaskString : function(value, opt) {
        var o = $.extend({}, this.options);
        opt = $.extend(o, opt);
        if(value) {
            value = value.toString();
            if(opt.thousands) {
                var pattern = new RegExp("["+opt.thousands+"]", 'g');
                value = value.replace(pattern, '');
            }

            return parseFloat(value.replace(opt.symbol, '')
                .replace(opt.decimal, '.').trim());
        }

        return value;
    },
    setMask: function(opt, input) {
        var o = $.extend({}, this.options);
        opt = $.extend(o, opt);
        input.data('maskOptions', opt);
        input.keypress(this.keypressHandler);

        $.currency.init(input);
    },
    unsetMask : function(input){
        input.unbind('keypress', this.keypressHandler);
        input.unbind('change', this.changeHandler);
    },
    keypressHandler: function (e) {
        var opt = $(this).data('maskOptions');
        var decimalCharIsDot = (opt.decimal == '.');
        var is_number = (e.which >= 48 && e.which <= 57);
        var is_dot = (e.which == 46);
        var is_comma = (e.which == 44);
        var is_minus = (e.which == 45);
        var hasSeparator = ($(this).val().indexOf('.') != -1 || $(this).val().indexOf(',') != -1);

        if (hasSeparator && (is_dot || is_comma)) {
            e.preventDefault();
        }

        if(is_dot || is_comma || is_number || is_minus) {
            $(this).data('maskOptions', opt);
        } else {
            e.preventDefault();
        }
    },
    changeHandler: function(e) {
        var value = $(this).val();
        var options = $(this).data('maskOptions');
        value = value.replace(new RegExp('\\'+options.thousands, 'g'), '').replace(new RegExp('\\'+options.decimal, 'g'), '.');

        if(!value) return;

        value = $.currency.string(value, options);
        $(this).val(value);
        if (options.callback) options.callback(value);
    },
    init: function(input) {
        input.bindFirst('change', this.changeHandler);
    }
};