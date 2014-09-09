
  /**
   *  2011-2014
   * 
   */

  var
    mess = "",
    Base = Math.pow(10,11); 
    cellSize = Math.floor(Math.log(Base)/Math.LN10); 



  function makeArray (n, aX, Integer) {

    var 
      i=0;

    for (i=1; i<n; i++) {
      aX[i] = null;
    }

    aX[0] = Integer;
  }



  function isEmpty (aX) {

    var 
      empty=true;
      
    for (i=0; i<aX.length; i++) 
      if (aX[i]) { 
        empty = false;
        break;
      }
    return empty;
  }



  //junior school math
  function Add (n, aX,aY) {

    var
      carry=0;

    for (var i=n-1; i>=0; i--) {
      aX[i] += Number(aY[i])+Number(carry);

      if (aX[i]<Base) {
        carry = 0;
      }
      else {
        carry = 1;
        aX[i] =Number(aX[i])- Number(Base);
      }
    } 
  }



  //subtract
  function Sub (n, aX,aY) {

    for (var i=n-1; i>=0; i--) {
      aX[i] -= aY[i];
      if (aX[i]<0) {
        if (i>0) { 
          aX[i] += Base;
          aX[i-1]--;
        }
      }
    } 
  }



  //multiply big number by "small" number
  function Mul (n, aX, iMult) {

    var
      carry=0;

    for (var i=n-1; i>=0; i--) {
      prod = (aX[i])*iMult; 
      prod += carry; 
      if (prod>=Base) {
        carry = Math.floor(prod/Base);
        prod -= (carry*Base);
      }
      else {
        carry = 0;
      }
      aX[i] = prod;
    } 
  }

  //divide big number by "small" number

  function Div (n, aX, iDiv,aY) {
    var
      carry=0;

    for (var i=0; i<n; i++) {

      //add any previous carry
      currVal = Number(aX[i])+Number(carry*Base);

      //divide
      theDiv =Math.floor(currVal/iDiv);

      //find next carry
      carry = currVal-theDiv*iDiv; 

      //put the result of division in the current slot 
      aY[i] = theDiv; 
    } 
  }



  //compute arctan
  function arctan (iAng, n, aX) {

    var
      iAng_squared=iAng*iAng,
      k=3, //k is the coefficient in the series 2n-1, 3,5..
      sign=0,
      aAngle  = [],
      aDivK   = [];

    makeArray (n, aX, 0); //aX is aArctan
    makeArray (n, aAngle, 1); 

    Div (n, aAngle, iAng, aAngle); //aAngle = 1/iAng, eg 1/5
    Add (n, aX, aAngle); // aX = aAngle or long angle
    while (!isEmpty(aAngle)) {

      Div (n, aAngle, iAng_squared, aAngle); //aAngle=aAngle/iAng_squared, iAng_squared is iAng*iAng
      //mess+="iAng="+iAng+"; aAngle="+aAngle+"\n";
      Div (n, aAngle, k, aDivK); /* aDivK = aAngle/k */
      if (sign) {
        Add (n, aX, aDivK); /* aX = aX+aDivK */
      }
      else {
        Sub (n, aX, aDivK); /* aX = aX-aDivK */
      }

      k+=2;
      sign = 1-sign;
    } 
  }



  // Calculate pi
  function calcPI (numDec) { 

    var 
      ans         = "",
      t1          = new Date(),
      numDec      = Number(numDec)+5,
      iAng        = new Array(10),
      coeff       = new Array(10),
      arrayLength = Math.ceil( 1 + numDec/cellSize );

    var  
      aPI         = new Array(arrayLength),
      aArctan     = new Array(arrayLength),
      aAngle      = new Array(arrayLength),
      aDivK       = new Array(arrayLength);


    coeff[0] =  4; 
    coeff[1] = -1; 
    coeff[2] =  0;

    //iAng holds the angles, 5 for 1/5, etc
    iAng[0] =   5; 
    iAng[1] = 239; 
    iAng[2] =   0; 

    var step = 1, steps = 10;

    makeArray (arrayLength, aPI, 0);
    __progress(step++, steps, (new Date().getTime())-t1.getTime() );

    //Machin: Pi/4 = 4*arctan(1/5)-arctan(1/239)
    makeArray( arrayLength, aAngle, 0 );
    __progress(step++, steps, (new Date().getTime())-t1.getTime() );
    makeArray( arrayLength, aDivK, 0 );
    __progress(step++, steps, (new Date().getTime())-t1.getTime() );


    for (var i=0; coeff[i]!=0; i++) {
      arctan( iAng[i], arrayLength, aArctan );
    __progress(step++, steps, (new Date().getTime())-t1.getTime() );

      //multiply by coefficients of arctan
      Mul (arrayLength, aArctan, Math.abs(coeff[i]));
    __progress(step++, steps, (new Date().getTime())-t1.getTime() );
      if (coeff[i]>0) {
        Add (arrayLength, aPI, aArctan); 
      }
      else {
        Sub (arrayLength, aPI, aArctan); 
      }
    __progress(step++, steps, (new Date().getTime())-t1.getTime() );
    }

    //we have calculated pi/4, so need to finally multiply
    Mul (arrayLength, aPI, 4);
    __progress(step++, steps, (new Date().getTime())-t1.getTime() );

    //we have now calculated PI, and need to format the answer
    //to print it out
    sPI    = "";
    tempPI = "";

    //put the figures in the array into the string tempPI
    for (var i = 0; i < aPI.length; i++) {
      aPI[i] = String(aPI[i]);

      //ensure there are enough digits in each cell
      //if not, pad with leading zeros
      if ( aPI[i].length < cellSize && i != 0 ) {
        while ( aPI[i].length < cellSize ) {
          aPI[i] = "0" + aPI[i];
        }
      }
      tempPI += aPI[i];
    }

    //now put the characters into the string sPI
    for (i = 0; i <= numDec; i++ ) {

      //put the 3 on a different line, and add a decimal point
      if (i==0){
        sPI += tempPI.charAt(i) + "."; 
      } else {
        if (i%80==0){
          sPI += "<br />";
        }
        sPI += tempPI.charAt(i);
      }//i not zero
    }

    //now put the print-out together
    //print our pi
    ans += sPI;

    t2 = new Date();
    timeTaken = ( t2.getTime() - t1.getTime() );

    return ans;
  }