
$.ctrl = function(key, callback, args) {
    $(document).keydown(function(e) {
        if(!args) args=[]; // IE barks when args is null
              if(e.keyCode == key.charCodeAt(0) && e.ctrlKey) {
                  callback.apply(this, args);
                  return false;
              }
    });
};

$.shift = function(key, callback, args) {
    $(document).keydown(function(e) {
        if(!args) args=[]; // IE barks when args is null
              if(e.keyCode == key.charCodeAt(0) && e.shiftKey && e.ctrlKey) {
                  callback.apply(this, args);
                  return false;
              }
    });
};
