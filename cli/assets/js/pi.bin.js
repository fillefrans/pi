/**
 *
 * π.bin
 *
 * @description Implements binary Plain Old Pi Object, and support functions
 *              Also provides utility memory handling functions and binary memory operations
 * @requires    HTML5, or typed array polyfill
 * @author Johan Telstad, jt@enfield.no, 2011-2014
 */


  π.bin = π.bin || {};


  π.bin.loaded = false,








  π.bin.new = function (args) {

  };


  // UTILITY FUNCTIONS

  /**
   * memcpy on array buffers (byte arrays)
   * @param  {uint} dst       Destination
   * @param  {uint} dstOffset Destination offset
   * @param  {uint} src       Source
   * @param  {uint} srcOffset Source offset
   * @param  {uint} length    Number of bytes in buffer
   * @return {void}           Nothing
   */
  π.bin.memcpy = function (dst, dstOffset, src, srcOffset, length) {
    var
      dstU8 = new Uint8Array(dst, dstOffset, length),
      srcU8 = new Uint8Array(src, srcOffset, length);

    dstU8.set(srcU8);
  };


  /**
   * Check endianness of host system
   * @return {int|bool} 0 for little-endian, 1 for big-endian, boolean FALSE on error.
   */
  π.bin.endianness = function() {
    var 
      a = new ArrayBuffer(4);
    var
      b = new Uint8Array(a),
      c = new Uint32Array(a);

    b[0] = 0xa1;
    b[1] = 0xb2;
    b[2] = 0xc3;
    b[3] = 0xd4;

    if (c[0] == 0xd4c3b2a1) {
      // little-endian
      return 0;
    }
    if (c[0] == 0xa1b2c3d4) {
      // big-endian
      return 1;
    }
    // error
    return false;
  };

  /**
   * Check if host system id Big-Endian
   * @return {Boolean} 
   */ 
  π.bin.isBigEndian = function() {
    return (π.bin.endianness === 1);
  };

  /**
   * Check if host system id Little-Endian
   * @return {Boolean} 
   */ 
  π.bin.isLittleEndian = function() {
    return (π.bin.endianness === 0);
  };



  π.bin.loaded = true;
