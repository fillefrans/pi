/**
 *
 * π.TYPE.constants
 *
 * @description Defines Pi Type Library constants, and support functions
 * 
 * @author Johan Telstad, jt@viewshq.no, 2011-2014
 */



 // we don't define π.TYPE, since we want to fail immediately if π.TYPE is not loaded


  π.TYPE.constants = π.TYPE.constants || {};


  π.TYPE.constants.loaded = false;

  π.require('type', function () {


    π.TYPE.toString = function(type) {
      return JSON.stringify(type);
    }



    /*  TYPE DEFINITIONS  */

    window.PI_NAN = π.TYPE.NAN;

    window.PI_NULL = π.TYPE.NULL;
    window.PI_DEFAULT = π.TYPE.DEFAULT;

    window.PI_STR = π.TYPE.STR;
    window.PI_STRING = π.TYPE.STRING;
    window.PI_NUMBER = π.TYPE.NUMBER;


    // floating point types
    window.PI_FLOAT32 = π.TYPE.FLOAT32;
    window.PI_FLOAT64 = π.TYPE.FLOAT64;


    // basic integer types

    // unsigned
    window.PI_UINT8 = π.TYPE.UINT8;
    window.PI_UINT16 = π.TYPE.UINT16;
    window.PI_UINT32 = π.TYPE.UINT32;
    window.PI_UINT64 = π.TYPE.UINT64;


    // signed
    window.PI_INT8 = π.TYPE.INT8;
    window.PI_INT16 = π.TYPE.INT16;
    window.PI_INT32 = π.TYPE.INT32;
    window.PI_INT64 = π.TYPE.INT64;


    // typed arrays, unsigned
    window.PI_UINT8ARRAY = π.TYPE.UINT8ARRAY;
    window.PI_UINT16ARRAY = π.TYPE.UINT16ARRAY;
    window.PI_UINT32ARRAY = π.TYPE.UINT32ARRAY;
    window.PI_UINT64ARRAY = π.TYPE.UINT64ARRAY;

    // typed arrays, signed
    window.PI_INT8ARRAY = π.TYPE.INT8ARRAY;
    window.PI_INT16ARRAY = π.TYPE.INT16ARRAY;
    window.PI_INT32ARRAY = π.TYPE.INT32ARRAY;
    window.PI_INT64ARRAY = π.TYPE.INT64ARRAY;


    // typed arrays, floating point values
    window.PI_FLOAT32ARRAY = π.TYPE.FLOAT32ARRAY;
    window.PI_FLOAT64ARRAY = π.TYPE.FLOAT64ARRAY;



    // complex types
    window.PI_SET       =  π.TYPE.SET;
    window.PI_SORTEDSET =  π.TYPE.SORTEDSET;

    window.PI_RANGE = π.TYPE.RANGE;
    window.PI_ARRAY = π.TYPE.ARRAY;
    window.PI_BYTEARRAY = π.TYPE.BYTEARRAY;

    // synonyms
    window.PI_STRUCT = π.TYPE.STRUCT;
    window.PI_RECORD = π.TYPE.RECORD;



    // higher order types
    window.PI_FILE = π.TYPE.FILE;
    window.PI_IMAGE = π.TYPE.IMAGE;
    window.PI_DATA = π.TYPE.DATA;
    window.PI_TEL = π.TYPE.TEL;
    window.PI_GEO = π.TYPE.GEO;
    window.PI_EMAIL = π.TYPE.EMAIL;
    window.PI_URL = π.TYPE.URL;



      // Pi internal types

      window.PI_FORMAT = π.TYPE.FORMAT;
      window.PI_CHANNEL = π.TYPE.CHANNEL;
      window.PI_ADDRESS = π.TYPE.ADDRESS;

      window.PI_IGBINARY = π.TYPE.IGBINARY;
      window.PI_BASE64 = π.TYPE.BASE64;


      // common internal object types
      window.PI_USER = π.TYPE.USER;
      window.PI_USERGROUP = π.TYPE.USERGROUP;
      window.PI_PERMISSIONS = π.TYPE.PERMISSIONS;

      window.PI_TOKEN = π.TYPE.TOKEN;
      window.PI_JSON = π.TYPE.JSON;
      window.PI_MYSQL = π.TYPE.MYSQL;
      window.PI_REDIS = π.TYPE.REDIS;
      window.PI_LIST = π.TYPE.LIST;


      // a UINT32
      window.PI_IP = π.TYPE.IP;
      window.PI_IPV4 = π.TYPE.IPV4;

      // a UINT32 QUAD ?
      window.PI_IPV6 = π.TYPE.IPV6;


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      window.PI_SHORTSTRING = π.TYPE.SHORTSTRING;

      // ANSI string, C-compatible null-terminated binary string
      window.PI_ANSISTRING = π.TYPE.ANSISTRING;

      // UTF-8 string
      window.PI_UTF8 = π.TYPE.UTF8;



    // date and time related types
    window.PI_DAY = π.TYPE.DAY;
    window.PI_WEEK = π.TYPE.WEEK;
    window.PI_TIME = π.TYPE.TIME;
    window.PI_DATE = π.TYPE.DATE;
    window.PI_DATETIME = π.TYPE.DATETIME;
    window.PI_DATETIME_LOCAL = π.TYPE.DATETIME_LOCAL;

    window.PI_UNIXTIME = π.TYPE.UNIXTIME;
    window.PI_MILLITIME = π.TYPE.MILLITIME;
    window.PI_MICROTIME = π.TYPE.MICROTIME;

    window.PI_HOUR = π.TYPE.HOUR;
    window.PI_MINUTE = π.TYPE.MINUTE;
    window.PI_SECOND = π.TYPE.SECOND;



  π.TYPE.constants.loaded = true;


  });

