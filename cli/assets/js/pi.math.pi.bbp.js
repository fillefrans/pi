/*  
    This program implements the BBP algorithm to generate a few hexadecimal
    digits beginning immediately after a given position id, or in other words
    beginning at position id + 1.  On most systems using IEEE 64-bit floating-
    point arithmetic, this code works correctly so long as d is less than
    approximately 1.18 x 10^7.  If 80-bit arithmetic can be employed, this limit
    is significantly higher.  Whatever arithmetic is used, results for a given
    position id can be checked by repeating with id-1 or id+1, and verifying 
    that the hex digits perfectly overlap with an offset of one, except possibly
    for a few trailing digits.  The resulting fractions are typically accurate 
    to at least 11 decimal digits, and to at least 9 hex digits.  
*/

/*  David H. Bailey     2006-09-08 */


/**
 * JavaScript version of the Bailey–Borwein–Plouffe formula
 *  Translated into javascript by Johan Telstad, <jt@enfield.no> 
 *
 * @author David H. Bailey     2006-09-08
 *
 * 
 */



  function bbp(id) {
  /**
   * JavaScript version of the Bailey–Borwein–Plouffe formula
   */

    var
      pid = s1 = s2 = s3 = s4 = 0,
      NHX = 16;

    /*  id is the digit position.  Digits generated follow immediately after id. */

    s1 = series (1, id);
   // console.log("s1: " + s1);
    s2 = series (4, id);
    s3 = series (5, id);
    s4 = series (6, id);
    pid = (4 * s1) - (2 * s2) - s3 - s4;
    pid = pid - Math.floor(pid) + 1;
    
    // return as hex string
    var hex = ihex( pid, NHX ); 

///    console.log("pid: " + pid + ", hex: " + hex);
    return hex;
  }


  function ihex ( x, nhx ) {

  /*  This returns, in chx, the first nhx hex digits of the fraction of x. */

  var
    i = 0,
    y = 0,
    hx  = "0123456789abcdef",
    chx = "0000000000000000";

    y = Math.abs (x);

    for (i = 0; i < nhx; i++){
      y = 16 * (y - Math.floor (y));
      chx[i] = hx[Math.floor(y)];
    }
  return chx;
  }



  function series( m, id ) {

  /*  This routine evaluates the series  sum_k 16^(id-k)/(8*k+m) 
      using the modular exponentiation technique. */

  var

    k = 0, ak = 0, eps = 1e-17, p = 0, s = 0, t=0;
    
  /*  Sum the series up to id. */

    for (k = 0; k < id; k++){
      ak = k<<3 + m;
      var bk = k*8 + m;
      p = id - k;
      t = expm (p, ak);
      s = s + (t / ak);
      s = s - parseInt(s);
    }

    if (logged){
      logged = true;
      console.log("s: " + s + ", eps: " + eps + ", t: " + t + ", ak: " + ak + ", bk: " + bk);
    }

  /*  Compute a few terms where k >= id. */

    for (k = id; k <= id + 100; k++){
      ak = k<<3 + m;
      t = Math.pow(16, (id - k)) / ak;
      if (t < eps) 
        break;
      s = s + t;
      s = s - parseInt(s);
    }
    return s;
  }


  function expm ( p, ak ) {
  /*  expm = 16^p mod ak.  This routine uses the left-to-right binary 
      exponentiation scheme. */
    var
      i = 0, j = 0,
      p1 = 0, pt = 0, r = 0,
      ntp = 25;

    var
      tp = [];
      tp.length = ntp,
      tp1 = 0;

    /*  If this is the first call to expm, fill the power of two table tp. */

    if (tp1 == 0) {
      tp1 = 1;
      tp[0] = 1;

      for (i = 1; i < ntp; i++) {
        tp[i] = 2 * tp[i-1];
      }
    }

    if (ak == 1) return 0;

  /*  Find the greatest power of two less than or equal to p. */

    for (i = 0; i < ntp; i++) {
      if (tp[i] > p) break;
    }

    pt = tp[i-1];
    p1 = p;
    r = 1;

  /*  Perform binary exponentiation algorithm modulo ak. */

    for (j = 1; j <= i; j++){
      if (p1 >= pt){
        r *= 16;
        r -= parseInt(r / ak) * ak;
        p1 -= pt;
      }
      pt *= 0.5;
      if (pt >= 1){
        r *= r;
        r -= parseInt(r / ak) * ak;
      }
    }
//    console.log("returning: " + (r || 0));
    if(!r) return 0;
    return r;
  }
