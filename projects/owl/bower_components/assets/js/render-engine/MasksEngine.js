var MaskEngine = function(){
  this.floatFormat = function(value){
    var valueFormated = "";
    if(value){
      try{
        valueFormated = window.parseFloat(value).formatMoney();
      }catch(e){
        valueFormated = value;
      }
    }
    return valueFormated;
  };
  this.currencyFormat = function(value){
    var valueFormated = "";
    if(value){
      try{
        valueFormated = window.parseFloat(value).formatMoney();
      }catch(e){
        valueFormated = value;
      }
    }
    return valueFormated;
  };
  this.currency = function(element, viewValue, params, callBack) {
      var value = viewValue || '';
      element.val(value);
      params.showSymbol = true;
      params.callback = callBack;
      $.currency.setMask(params, element);
  };

  this.float = function(element, viewValue, params, callBack){
      var value = viewValue || '';
      element.val(value);
      params.callback = callBack;
      $.currency.setMask(params, element);
  };

  this.date = function(element, viewValue, params, callBack){
    $.mask.definitions.d = '[0-3]';
    $.mask.definitions.m = '[0-1]';
    var value = viewValue || '';
    element.val(value);
    element.mask("d9/m9/9999", { completed : function(){
      var value = this.val();
      callBack(value);
    }});
  };

  this.datetime = function(element, viewValue, params, callBack){
        $.mask.definitions.d = '[0-3]';
        $.mask.definitions.m = '[0-1]';
        $.mask.definitions.h = '[0-2]';
        $.mask.definitions.s = '[0-5]';
        var value = viewValue || '';
        element.val(value);
        element.mask("d9/m9/9999 h9:s9:s9", { completed : function(){
            var value = this.val();
            callBack(value);
        }});
    };

  this.fix = function(element, viewValue, params, callBack){
    var value = viewValue || '';
    element.val(value);
    element.mask(params.mask, { completed : function(){
      var value = this.val();
      callBack(value);
    }});
    return element.bind('keyup', function() {
        if(element.mask().length === 0){
          callBack(element.mask());
        }  
    });
  };

  this.zerofill = function(element, viewValue, params, callBack){
    var formatString = function(value, length){
      if (value) {
        value = value.toString();
        while (value.length < length){
          value = "0" + value;
        } 
      } 
      return value;
    };

    if (viewValue){
      var length = element.attr('maxlength') || 4;
      var value = formatString(viewValue, length);
      element.val(value);
      callBack(value);      
    }

    return element.bind('change', function(){
      var value = $(this).val();
      var length = $(this).attr('maxlength') || 4;
      value = formatString(value, length);
      element.val(value);
      callBack(value); 
    });
  };
};