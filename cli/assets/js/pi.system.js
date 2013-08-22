/**
 *
 * π.system
 *
 * @author Johan Telstad, jt@enfield.no, 2011-2013
 *
 */


  π.system = {
    loaded : false,
    os : navigator.platform,
    browser : navigator.userAgent,


    device : {
      // var
      isTablet          : null,
      isHandset         : null,

      connectionType    : navigator.connection.type
    }
  };



  π.system.loaded = true;
