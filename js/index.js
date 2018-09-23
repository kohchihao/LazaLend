function loginFail () {
  new Noty({
  	type: 'error', //alert (default), success, error, warning, info - ClassName generator uses this value → noty_type__${type}
    layout: 'topRight', //top, topLeft, topCenter, topRight (default), center, centerLeft, centerRight, bottom, bottomLeft, bottomCenter, bottomRight - ClassName generator uses this value → noty_layout__${layout}
    theme: 'bootstrap-v4', //relax, mint (default), metroui - ClassName generator uses this value → noty_theme__${theme}
    text: 'Fail to login. Please try again.', //This string can contain HTML too. But be careful and don't pass user inputs to this parameter.
    timeout: 2500, // false (default), 1000, 3000, 3500, etc. Delay for closing event in milliseconds (ms). Set 'false' for sticky notifications.
    progressBar: true, //Default, progress before fade out is displayed
    //closeWith: 'click' //default; alternative: button
    
    /*animation: {
            open: 'animated bounceInRight', // Animate.css class names
            close: 'animated bounceOutRight' // Animate.css class names
        }*/
  }).show();
}

function registerFail () {
  new Noty({
  	type: 'error', //alert (default), success, error, warning, info - ClassName generator uses this value → noty_type__${type}
    layout: 'topRight', //top, topLeft, topCenter, topRight (default), center, centerLeft, centerRight, bottom, bottomLeft, bottomCenter, bottomRight - ClassName generator uses this value → noty_layout__${layout}
    theme: 'bootstrap-v4', //relax, mint (default), metroui - ClassName generator uses this value → noty_theme__${theme}
    text: 'Fail to register. Please try again.', //This string can contain HTML too. But be careful and don't pass user inputs to this parameter.
    timeout: 2500, // false (default), 1000, 3000, 3500, etc. Delay for closing event in milliseconds (ms). Set 'false' for sticky notifications.
    progressBar: true, //Default, progress before fade out is displayed
    //closeWith: 'click' //default; alternative: button
    
    /*animation: {
            open: 'animated bounceInRight', // Animate.css class names
            close: 'animated bounceOutRight' // Animate.css class names
        }*/
  }).show();
}
