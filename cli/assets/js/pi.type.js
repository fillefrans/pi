/**
 *
 * π.TYPE
 *
 * @description Implements typeary Plain Old Pi Object, and support functions
 *              Also provides utility memory handling functions and typeary memory operations
 * @requires    HTML5, or typed array polyfill
 * @author Johan Telstad, jt@enfield.no, 2011-2014
 */



 // we don't define π, since we want to fail immediately if π is not loaded


  π.TYPE = π.TYPE || {};


  π.TYPE.loaded = false,

  π.require('bin');






  π.TYPE.new = function (args) {

  };


  // UTILITY FUNCTIONS

  // π.bin.memcpy(dst, dstOffset, src, srcOffset, length)

  // π.bin.endianness()
  // π.bin.isBigEndian()
  // π.bin.isLittleEndian()

  // π.TYPE.


    /*  TYPE DEFINITIONS  */

    π.TYPE.NAN = 254;

    π.TYPE.NULL = 255;
    π.TYPE.DEFAULT = 255;

    π.TYPE.STR = 1;
    π.TYPE.STRING = 1;
    π.TYPE.NUMBER = 2;


    // floating point types
    π.TYPE.FLOAT32 = 5;
    π.TYPE.FLOAT64 = 6;


    // basic integer types

    // unsigned
    π.TYPE.UINT8 = 9;
    π.TYPE.UINT16 = 10;
    π.TYPE.UINT32 = 11;
    π.TYPE.UINT64 = 12;


    // signed
    π.TYPE.INT8 = 17;
    π.TYPE.INT16 = 18;
    π.TYPE.INT32 = 19;
    π.TYPE.INT64 = 20;


    // typed arrays, unsigned
    π.TYPE.UINT8ARRAY = 31;
    π.TYPE.UINT16ARRAY = 32;
    π.TYPE.UINT32ARRAY = 33;
    π.TYPE.UINT64ARRAY = 34;

    // typed arrays, signed
    π.TYPE.INT8ARRAY = 65;
    π.TYPE.INT16ARRAY = 66;
    π.TYPE.INT32ARRAY = 67;
    π.TYPE.INT64ARRAY = 68;


    // typed arrays, floating point values
    π.TYPE.FLOAT32ARRAY = 7;
    π.TYPE.FLOAT64ARRAY = 8;



    // complex types
    π.TYPE.RANGE = 123;
    π.TYPE.ARRAY = 124;
    π.TYPE.BYTEARRAY = 125;

    // synonyms
    π.TYPE.STRUCT = 127;
    π.TYPE.RECORD = 127;



    // higher order types
    π.TYPE.FILE = 128;
    π.TYPE.IMAGE = 129;
    π.TYPE.DATA = 130;
    π.TYPE.TEL = 131;
    π.TYPE.GEO = 132;
    π.TYPE.EMAIL = 133;
    π.TYPE.URL = 134;



      // Pi internal types

      π.TYPE.FORMAT = 135;
      π.TYPE.CHANNEL = 136;
      π.TYPE.ADDRESS = 137;

      π.TYPE.IGBINARY = 240;
      π.TYPE.BASE64 = 241;


      // common internal object types
      π.TYPE.USER = 100;
      π.TYPE.USERGROUP = 101;
      π.TYPE.PERMISSIONS = 102;

      π.TYPE.TOKEN = 103;
      π.TYPE.JSON = 104;
      π.TYPE.MYSQL = 105;
      π.TYPE.REDIS = 106;
      π.TYPE.LIST = 107;


      // a UINT32
      π.TYPE.IP = 108;
      π.TYPE.IPV4 = 108;

      // a UINT32 QUAD ?
      π.TYPE.IPV6 = 109;


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      π.TYPE.SHORTSTRING = 110;

      // ANSI string, C-compatible null-terminated binary string
      π.TYPE.ANSISTRING = 111;

      // UTF-8 string
      π.TYPE.UTF8 = 112;








    // create array for the reverse lookup

    π.TYPE.names = [];


    // date and time related values
    π.TYPE.names[π.TYPE.DAY] = 'DAY';
    π.TYPE.names[π.TYPE.WEEK] = 'WEEK';
    π.TYPE.names[π.TYPE.TIME] = 'TIME';
    π.TYPE.names[π.TYPE.DATE] = 'DATE';
    π.TYPE.names[π.TYPE.DATETIME] = 'DATETIME';
    π.TYPE.names[π.TYPE.DATETIME_LOCAL] = 'DATETIME_LOCAL';

    π.TYPE.names[π.TYPE.UNIXTIME] = 'UNIXTIME';
    π.TYPE.names[π.TYPE.MILLITIME] = 'MILLITIME';
    π.TYPE.names[π.TYPE.MICROTIME] = 'MICROTIME';

    π.TYPE.names[π.TYPE.HOUR] = 'HOUR';
    π.TYPE.names[π.TYPE.MINUTE] = 'MINUTE';
    π.TYPE.names[π.TYPE.SECOND] = 'SECOND';



    π.TYPE.names[π.TYPE.NAN] = 'NAN';

    π.TYPE.names[π.TYPE.NULL] = 'NULL';
    π.TYPE.names[π.TYPE.DEFAULT] = 'DEFAULT';

    π.TYPE.names[π.TYPE.STR] = 'STR';
    π.TYPE.names[π.TYPE.STRING] = 'STRING';
    π.TYPE.names[π.TYPE.NUMBER] = 'NUMBER';


    // floating point types
    π.TYPE.names[π.TYPE.FLOAT32] = 'FLOAT32';
    π.TYPE.names[π.TYPE.FLOAT64] = 'FLOAT64';


    // basic integer types

    // unsigned
    π.TYPE.names[π.TYPE.UINT8] = 'UINT8';
    π.TYPE.names[π.TYPE.UINT16] = 'UINT16';
    π.TYPE.names[π.TYPE.UINT32] = 'UINT32';
    π.TYPE.names[π.TYPE.UINT64] = 'UINT64';


    // signed
    π.TYPE.names[π.TYPE.INT8] = 'INT8';
    π.TYPE.names[π.TYPE.INT16] = 'INT16';
    π.TYPE.names[π.TYPE.INT32] = 'INT32';
    π.TYPE.names[π.TYPE.INT64] = 'INT64';


    // typed arrays, unsigned
    π.TYPE.names[π.TYPE.UINT8ARRAY] = 'UINT8ARRAY';
    π.TYPE.names[π.TYPE.UINT16ARRAY] = 'UINT16ARRAY';
    π.TYPE.names[π.TYPE.UINT32ARRAY] = 'UINT32ARRAY';
    π.TYPE.names[π.TYPE.UINT64ARRAY] = 'UINT64ARRAY';

    // typed arrays, signed
    π.TYPE.names[π.TYPE.INT8ARRAY] = 'INT8ARRAY';
    π.TYPE.names[π.TYPE.INT16ARRAY] = 'INT16ARRAY';
    π.TYPE.names[π.TYPE.INT32ARRAY] = 'INT32ARRAY';
    π.TYPE.names[π.TYPE.INT64ARRAY] = 'INT64ARRAY';


    // typed arrays, floating point values
    π.TYPE.names[π.TYPE.FLOAT32ARRAY] = 'FLOAT32ARRAY';
    π.TYPE.names[π.TYPE.FLOAT64ARRAY] = 'FLOAT64ARRAY';



    // complex types
    π.TYPE.names[π.TYPE.RANGE] = 'RANGE';
    π.TYPE.names[π.TYPE.ARRAY] = 'ARRAY';
    π.TYPE.names[π.TYPE.BYTEARRAY] = 'BYTEARRAY';

    // synonyms
    π.TYPE.names[π.TYPE.STRUCT] = 'STRUCT';
    π.TYPE.names[π.TYPE.RECORD] = 'RECORD';



    // higher order types
    π.TYPE.names[π.TYPE.FILE] = 'FILE';
    π.TYPE.names[π.TYPE.IMAGE] = 'IMAGE';
    π.TYPE.names[π.TYPE.DATA] = 'DATA';
    π.TYPE.names[π.TYPE.TEL] = 'TEL';
    π.TYPE.names[π.TYPE.GEO] = 'GEO';
    π.TYPE.names[π.TYPE.EMAIL] = 'EMAIL';
    π.TYPE.names[π.TYPE.URL] = 'URL';



      // Pi internal types

      π.TYPE.names[π.TYPE.FORMAT] = 'FORMAT';
      π.TYPE.names[π.TYPE.CHANNEL] = 'CHANNEL';
      π.TYPE.names[π.TYPE.ADDRESS] = 'ADDRESS';

      π.TYPE.names[π.TYPE.IGBINARY] = 'IGBINARY';
      π.TYPE.names[π.TYPE.BASE64] = 'BASE64';


      // common internal object types
      π.TYPE.names[π.TYPE.USER] = 'USER';
      π.TYPE.names[π.TYPE.USERGROUP] = 'USERGROUP';
      π.TYPE.names[π.TYPE.PERMISSIONS] = 'PERMISSIONS';

      π.TYPE.names[π.TYPE.TOKEN] = 'TOKEN';
      π.TYPE.names[π.TYPE.JSON] = 'JSON';
      π.TYPE.names[π.TYPE.MYSQL] = 'MYSQL';
      π.TYPE.names[π.TYPE.REDIS] = 'REDIS';
      π.TYPE.names[π.TYPE.LIST] = 'LIST';


      // a UINT32
      π.TYPE.names[π.TYPE.IP] = 'IP';
      π.TYPE.names[π.TYPE.IPV4] = 'IPV4';

      // a UINT32 QUAD ?
      π.TYPE.names[π.TYPE.IPV6] = 'IPV6';


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      π.TYPE.names[π.TYPE.SHORTSTRING] = 'SHORTSTRING';

      // ANSI string, C-compatible null-terminated binary string
      π.TYPE.names[π.TYPE.ANSISTRING] = 'ANSISTRING';

      // UTF-8 string
      π.TYPE.names[π.TYPE.UTF8] = 'UTF8';



    // date and time related types
    π.TYPE.names[π.TYPE.DAY] = 'DAY';
    π.TYPE.names[π.TYPE.WEEK] = 'WEEK';
    π.TYPE.names[π.TYPE.TIME] = 'TIME';
    π.TYPE.names[π.TYPE.DATE] = 'DATE';
    π.TYPE.names[π.TYPE.DATETIME] = 'DATETIME';
    π.TYPE.names[π.TYPE.DATETIME_LOCAL] = 'DATETIME_LOCAL';

    π.TYPE.names[π.TYPE.UNIXTIME] = 'UNIXTIME';
    π.TYPE.names[π.TYPE.MILLITIME] = 'MILLITIME';
    π.TYPE.names[π.TYPE.MICROTIME] = 'MICROTIME';

    π.TYPE.names[π.TYPE.HOUR] = 'HOUR';
    π.TYPE.names[π.TYPE.MINUTE] = 'MINUTE';
    π.TYPE.names[π.TYPE.SECOND] = 'SECOND';



    π.TYPE.list = function (sortbyindex) {
      var
        sortbyindex = sortbyindex || false,
        result = [];

      π.TYPE.names.forEach(function (p, i, arr) {
        result.push(p);
      });

      if (sortbyindex !== true) {
        // default case
        result.sort();
      }

      return result;
    }



    π.TYPE.fromString = function(str) {
      if (typeof str != "string") {
        return π.TYPE.NULL;
      }
      str = str.toUpperCase();
      if (π.TYPE[str]) {
        return π.TYPE[str];
      }
      else {
        return π.TYPE.NULL;
      }
    }


    π.TYPE.fromType = function(type) {
      if (typeof type != "number") {
        return null;
      }
      type = Math.floor(type);
      if (π.TYPE.names[type]) {
        return π.TYPE.names[type];
      }
      else {
        return null;
      }
    }







  π.TYPE.loaded = true;
