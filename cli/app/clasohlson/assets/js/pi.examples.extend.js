
var 
  π   = π || {},
  pi  = π || {};




  /**
   * Sum all numeric arguments
   * 
   * @return  {int}   The sum of all numeric arguments. 
   * When an argument is of type Array, the function will
   * loop over all array elements and return the sum of 
   * all numeric values contained in the array. The function
   * will recurse into sub-arrays, if found.
   * 
   */
  pi.sum = function () {
    var 
      result = 0;


    for (var i = 0; i < arguments.length; i++) {
      if(pi.isArray(arguments[i])) {
        result += sum(arguments[i]);
      }
      if(typeof arguments[i] === "number") {
        result += arguments[i];
      }

    }
    return result;
  };



  /**
   * Example of overloading functions based on function signature
   * 
   * @return  {mixed}   
   * 
   */
  pi.overload = function () {
    var 
      sig = "";

    for (var i = 0; i < arguments.length; i++) {
      if(pi.isArray(arguments[i])) {
        result += "a";
      }
      else if(typeof arguments[i] === "null") {
        throw "Arg #" + (i+1) + " is NULL. Overloaded functions do not accept untyped parameters.";
      }
      if(typeof arguments[i] === "number") {
        result += arguments[i];
      }

    }
    return result;
  };


  /**
   * @var {Object} Object to hold functions, referenced by signature
   */
  pi.overload.signature = {};


  /**
   * Overloaded function
   * @param  {string}   str  [description]
   * @param  {int}      num  [description]
   * @param  {Array}    arr  [description]
   * @param  {Function} func [description]
   * @param  {Object}   obj  [description]
   * @param  {bool}     bool [description]
   * 
   * @return {mixed}    Same type as parent function
   */
  pi.overload.signature["snafob"] = function (str, num, arr, func, obj, bool) {
    
  }


