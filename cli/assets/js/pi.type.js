/**
 *
 * π.type
 *
 * Implements Pi Type Library in JS and HTML
 * Also provides utility memory handling functions
 * and some binary memory operations
 *
 * @requires    HTML5, or typed array polyfill
 * @author Johan Telstad, jt@viewshq.no, 2011-2014
 */



 // we don't define π, since we want to fail immediately if π is not loaded


  π.type = π.type || {};


  π.type.loaded = false,

  // load the binary operations lib (DataView/HTML5 typed array stuff)
  π.require('bin');






  π.type.new = function (args) {

  };


  // UTILITY FUNCTIONS

  // π.bin.memcpy(dst, dstOffset, src, srcOffset, length)

  // π.bin.endianness()
  // π.bin.isBigEndian()
  // π.bin.isLittleEndian()

    // create type name lookup object
    π.type.def = {};

    // create alias
    π.def = π.type.def;


    /*  TYPE DEFINITIONS  */

    π.def.NAN = 254;

    π.def.NULL = 255;
    π.def.DEFAULT = 255;

    π.def.STR = 1;
    π.def.STRING = 1;
    π.def.NUMBER = 2;


    // floating point types
    π.def.FLOAT32 = 5;
    π.def.FLOAT64 = 6;


    // basic integer types

    // unsigned
    π.def.UINT8 = 9;
    π.def.UINT16 = 10;
    π.def.UINT32 = 11;
    π.def.UINT64 = 12;


    // signed
    π.def.INT8 = 17;
    π.def.INT16 = 18;
    π.def.INT32 = 19;
    π.def.INT64 = 20;


    // typed arrays, unsigned
    π.def.UINT8ARRAY = 31;
    π.def.UINT16ARRAY = 32;
    π.def.UINT32ARRAY = 33;
    π.def.UINT64ARRAY = 34;

    // typed arrays, signed
    π.def.INT8ARRAY = 65;
    π.def.INT16ARRAY = 66;
    π.def.INT32ARRAY = 67;
    π.def.INT64ARRAY = 68;


    // typed arrays, floating point values
    π.def.FLOAT32ARRAY = 7;
    π.def.FLOAT64ARRAY = 8;



    // complex types

    π.def.SET        = 200;
    π.def.SORTEDSET  = 201;

    π.def.RANGE = 123;
    π.def.ARRAY = 124;
    π.def.BYTEARRAY = 125;

    // synonyms
    π.def.STRUCT = 127;
    π.def.RECORD = 127;



    // higher order types
    π.def.FILE = 128;
    π.def.IMAGE = 129;
    π.def.DATA = 130;
    π.def.TEL = 131;
    π.def.GEO = 132;
    π.def.EMAIL = 133;
    π.def.URL = 134;



      // Pi internal types

      π.def.FORMAT = 135;
      π.def.CHANNEL = 136;
      π.def.ADDRESS = 137;

      π.def.IGBINARY = 240;
      π.def.BASE64 = 241;


      // common internal object types
      π.def.USER   = 100;
      π.def.GROUP  = 101;
      π.def.PERMISSIONS = 102;

      π.def.TOKEN  = 103;
      π.def.JSON   = 104;
      π.def.MYSQL  = 105;
      π.def.REDIS  = 106;
      π.def.LIST   = 107;


      // a UINT32
      π.def.IP = 108;
      π.def.IPV4 = 108;

      // a UINT32 QUAD ?
      π.def.IPV6 = 109;


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      π.def.SHORTSTRING = 110;

      // ANSI string, C-compatible null-terminated binary string
      π.def.ANSISTRING = 111;

      // UTF-8 string
      π.def.UTF8 = 112;








    /** @var  {array}  types  lookup array for type ids to names */
    π.type.index = [];

    /** @var  {object} names  reverse lookup object for mapping names to HTML elements */
    π.type.names = {};


    // date and time related values
    π.type.index[π.def.DAY] = 'DAY';
    π.type.index[π.def.WEEK] = 'WEEK';
    π.type.index[π.def.TIME] = 'TIME';
    π.type.index[π.def.DATE] = 'DATE';
    π.type.index[π.def.DATETIME] = 'DATETIME';
    π.type.index[π.def.DATETIME_LOCAL] = 'DATETIME_LOCAL';

    π.type.index[π.def.UNIXTIME] = 'UNIXTIME';
    π.type.index[π.def.MILLITIME] = 'MILLITIME';
    π.type.index[π.def.MICROTIME] = 'MICROTIME';

    π.type.index[π.def.HOUR] = 'HOUR';
    π.type.index[π.def.MINUTE] = 'MINUTE';
    π.type.index[π.def.SECOND] = 'SECOND';



    π.type.index[π.def.NAN] = 'NAN';

    π.type.index[π.def.NULL] = 'NULL';
    π.type.index[π.def.DEFAULT] = 'DEFAULT';

    π.type.index[π.def.STR] = 'STR';
    π.type.index[π.def.STRING] = 'STRING';
    π.type.index[π.def.NUMBER] = 'NUMBER';


    // floating point types
    π.type.index[π.def.FLOAT32] = 'FLOAT32';
    π.type.index[π.def.FLOAT64] = 'FLOAT64';


    // basic integer types

    // unsigned
    π.type.index[π.def.UINT8] = 'UINT8';
    π.type.index[π.def.UINT16] = 'UINT16';
    π.type.index[π.def.UINT32] = 'UINT32';
    π.type.index[π.def.UINT64] = 'UINT64';


    // signed
    π.type.index[π.def.INT8] = 'INT8';
    π.type.index[π.def.INT16] = 'INT16';
    π.type.index[π.def.INT32] = 'INT32';
    π.type.index[π.def.INT64] = 'INT64';


    // typed arrays, unsigned
    π.type.index[π.def.UINT8ARRAY] = 'UINT8ARRAY';
    π.type.index[π.def.UINT16ARRAY] = 'UINT16ARRAY';
    π.type.index[π.def.UINT32ARRAY] = 'UINT32ARRAY';
    π.type.index[π.def.UINT64ARRAY] = 'UINT64ARRAY';

    // typed arrays, signed
    π.type.index[π.def.INT8ARRAY] = 'INT8ARRAY';
    π.type.index[π.def.INT16ARRAY] = 'INT16ARRAY';
    π.type.index[π.def.INT32ARRAY] = 'INT32ARRAY';
    π.type.index[π.def.INT64ARRAY] = 'INT64ARRAY';


    // typed arrays, floating point values
    π.type.index[π.def.FLOAT32ARRAY] = 'FLOAT32ARRAY';
    π.type.index[π.def.FLOAT64ARRAY] = 'FLOAT64ARRAY';



    // complex types

    π.type.index[π.def.SET]        = 'SET';
    π.type.index[π.def.SORTEDSET]  = 'SORTEDSET';

    π.type.index[π.def.RANGE] = 'RANGE';
    π.type.index[π.def.ARRAY] = 'ARRAY';
    π.type.index[π.def.BYTEARRAY] = 'BYTEARRAY';

    // synonyms
    π.type.index[π.def.STRUCT] = 'STRUCT';
    π.type.index[π.def.RECORD] = 'RECORD';



    // higher order types
    π.type.index[π.def.FILE]   = 'FILE';
    π.type.index[π.def.IMAGE]  = 'IMAGE';
    π.type.index[π.def.DATA]   = 'DATA';
    π.type.index[π.def.TEL]    = 'TEL';
    π.type.index[π.def.GEO]    = 'GEO';
    π.type.index[π.def.EMAIL]  = 'EMAIL';
    π.type.index[π.def.URL]    = 'URL';



      // Pi internal types

      π.type.index[π.def.FORMAT]   = 'FORMAT';
      π.type.index[π.def.CHANNEL]  = 'CHANNEL';
      π.type.index[π.def.ADDRESS]  = 'ADDRESS';

      π.type.index[π.def.IGBINARY] = 'IGBINARY';
      π.type.index[π.def.BASE64]   = 'BASE64';


      // common internal object types
      π.type.index[π.def.USER]         = 'USER';
      π.type.index[π.def.USERGROUP]    = 'USERGROUP';
      π.type.index[π.def.PERMISSIONS]  = 'PERMISSIONS';

      π.type.index[π.def.TOKEN]  = 'TOKEN';
      π.type.index[π.def.JSON]   = 'JSON';
      π.type.index[π.def.MYSQL]  = 'MYSQL';
      π.type.index[π.def.REDIS]  = 'REDIS';
      π.type.index[π.def.LIST]   = 'LIST';


      // a UINT32
      π.type.index[π.def.IP]   = 'IP';
      π.type.index[π.def.IPV4] = 'IPV4';

      // a UINT32 QUAD ?
      π.type.index[π.def.IPV6] = 'IPV6';


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      π.type.index[π.def.SHORTSTRING]  = 'SHORTSTRING';

      // ANSI string, C-compatible null-terminated binary string
      π.type.index[π.def.ANSISTRING]   = 'ANSISTRING';

      // UTF-8 string
      π.type.index[π.def.UTF8] = 'UTF8';



    // date and time related types
    π.type.index[π.def.DAY]  = 'DAY';
    π.type.index[π.def.WEEK] = 'WEEK';
    π.type.index[π.def.TIME] = 'TIME';
    π.type.index[π.def.DATE] = 'DATE';

    π.type.index[π.def.DATETIME]       = 'DATETIME';
    π.type.index[π.def.DATETIME_LOCAL] = 'DATETIME_LOCAL';

    π.type.index[π.def.UNIXTIME]   = 'UNIXTIME';
    π.type.index[π.def.MILLITIME]  = 'MILLITIME';
    π.type.index[π.def.MICROTIME]  = 'MICROTIME';

    π.type.index[π.def.HOUR]       = 'HOUR';
    π.type.index[π.def.MINUTE]     = 'MINUTE';
    π.type.index[π.def.SECOND]     = 'SECOND';



    π.type.list = function (sortbyindex) {
      var
        sortbyindex = sortbyindex || false,
        result = [];

      π.type.index.forEach(function (p, i, arr) {
        result.push(p);
      });

      if (sortbyindex !== true) {
        // default case
        result.sort();
      }

      return result;
    }



    π.type.fromString = function(str) {
      if (typeof str != "string") {
        return π.type.NULL;
      }
      str = str.toUpperCase();
      if (π.TYPE[str]) {
        return π.TYPE[str];
      }
      else {
        return π.type.NULL;
      }
    }


    π.type.fromType = function(type) {
      if (typeof type != "number") {
        return null;
      }
      type = Math.floor(type);
      if (π.type.index[type]) {
        return π.type.index[type];
      }
      else {
        return null;
      }
    }


    π.type._init = function() {

    }

    π.type.run = function() {
      π.type._init();
    }

  π.type.run();


  π.type.loaded = true;
