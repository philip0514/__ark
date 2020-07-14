/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/sprintf-js/src/sprintf.js":
/*!************************************************!*\
  !*** ./node_modules/sprintf-js/src/sprintf.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_RESULT__;/* global window, exports, define */

!function() {
    'use strict'

    var re = {
        not_string: /[^s]/,
        not_bool: /[^t]/,
        not_type: /[^T]/,
        not_primitive: /[^v]/,
        number: /[diefg]/,
        numeric_arg: /[bcdiefguxX]/,
        json: /[j]/,
        not_json: /[^j]/,
        text: /^[^\x25]+/,
        modulo: /^\x25{2}/,
        placeholder: /^\x25(?:([1-9]\d*)\$|\(([^)]+)\))?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-gijostTuvxX])/,
        key: /^([a-z_][a-z_\d]*)/i,
        key_access: /^\.([a-z_][a-z_\d]*)/i,
        index_access: /^\[(\d+)\]/,
        sign: /^[+-]/
    }

    function sprintf(key) {
        // `arguments` is not an array, but should be fine for this call
        return sprintf_format(sprintf_parse(key), arguments)
    }

    function vsprintf(fmt, argv) {
        return sprintf.apply(null, [fmt].concat(argv || []))
    }

    function sprintf_format(parse_tree, argv) {
        var cursor = 1, tree_length = parse_tree.length, arg, output = '', i, k, ph, pad, pad_character, pad_length, is_positive, sign
        for (i = 0; i < tree_length; i++) {
            if (typeof parse_tree[i] === 'string') {
                output += parse_tree[i]
            }
            else if (typeof parse_tree[i] === 'object') {
                ph = parse_tree[i] // convenience purposes only
                if (ph.keys) { // keyword argument
                    arg = argv[cursor]
                    for (k = 0; k < ph.keys.length; k++) {
                        if (arg == undefined) {
                            throw new Error(sprintf('[sprintf] Cannot access property "%s" of undefined value "%s"', ph.keys[k], ph.keys[k-1]))
                        }
                        arg = arg[ph.keys[k]]
                    }
                }
                else if (ph.param_no) { // positional argument (explicit)
                    arg = argv[ph.param_no]
                }
                else { // positional argument (implicit)
                    arg = argv[cursor++]
                }

                if (re.not_type.test(ph.type) && re.not_primitive.test(ph.type) && arg instanceof Function) {
                    arg = arg()
                }

                if (re.numeric_arg.test(ph.type) && (typeof arg !== 'number' && isNaN(arg))) {
                    throw new TypeError(sprintf('[sprintf] expecting number but found %T', arg))
                }

                if (re.number.test(ph.type)) {
                    is_positive = arg >= 0
                }

                switch (ph.type) {
                    case 'b':
                        arg = parseInt(arg, 10).toString(2)
                        break
                    case 'c':
                        arg = String.fromCharCode(parseInt(arg, 10))
                        break
                    case 'd':
                    case 'i':
                        arg = parseInt(arg, 10)
                        break
                    case 'j':
                        arg = JSON.stringify(arg, null, ph.width ? parseInt(ph.width) : 0)
                        break
                    case 'e':
                        arg = ph.precision ? parseFloat(arg).toExponential(ph.precision) : parseFloat(arg).toExponential()
                        break
                    case 'f':
                        arg = ph.precision ? parseFloat(arg).toFixed(ph.precision) : parseFloat(arg)
                        break
                    case 'g':
                        arg = ph.precision ? String(Number(arg.toPrecision(ph.precision))) : parseFloat(arg)
                        break
                    case 'o':
                        arg = (parseInt(arg, 10) >>> 0).toString(8)
                        break
                    case 's':
                        arg = String(arg)
                        arg = (ph.precision ? arg.substring(0, ph.precision) : arg)
                        break
                    case 't':
                        arg = String(!!arg)
                        arg = (ph.precision ? arg.substring(0, ph.precision) : arg)
                        break
                    case 'T':
                        arg = Object.prototype.toString.call(arg).slice(8, -1).toLowerCase()
                        arg = (ph.precision ? arg.substring(0, ph.precision) : arg)
                        break
                    case 'u':
                        arg = parseInt(arg, 10) >>> 0
                        break
                    case 'v':
                        arg = arg.valueOf()
                        arg = (ph.precision ? arg.substring(0, ph.precision) : arg)
                        break
                    case 'x':
                        arg = (parseInt(arg, 10) >>> 0).toString(16)
                        break
                    case 'X':
                        arg = (parseInt(arg, 10) >>> 0).toString(16).toUpperCase()
                        break
                }
                if (re.json.test(ph.type)) {
                    output += arg
                }
                else {
                    if (re.number.test(ph.type) && (!is_positive || ph.sign)) {
                        sign = is_positive ? '+' : '-'
                        arg = arg.toString().replace(re.sign, '')
                    }
                    else {
                        sign = ''
                    }
                    pad_character = ph.pad_char ? ph.pad_char === '0' ? '0' : ph.pad_char.charAt(1) : ' '
                    pad_length = ph.width - (sign + arg).length
                    pad = ph.width ? (pad_length > 0 ? pad_character.repeat(pad_length) : '') : ''
                    output += ph.align ? sign + arg + pad : (pad_character === '0' ? sign + pad + arg : pad + sign + arg)
                }
            }
        }
        return output
    }

    var sprintf_cache = Object.create(null)

    function sprintf_parse(fmt) {
        if (sprintf_cache[fmt]) {
            return sprintf_cache[fmt]
        }

        var _fmt = fmt, match, parse_tree = [], arg_names = 0
        while (_fmt) {
            if ((match = re.text.exec(_fmt)) !== null) {
                parse_tree.push(match[0])
            }
            else if ((match = re.modulo.exec(_fmt)) !== null) {
                parse_tree.push('%')
            }
            else if ((match = re.placeholder.exec(_fmt)) !== null) {
                if (match[2]) {
                    arg_names |= 1
                    var field_list = [], replacement_field = match[2], field_match = []
                    if ((field_match = re.key.exec(replacement_field)) !== null) {
                        field_list.push(field_match[1])
                        while ((replacement_field = replacement_field.substring(field_match[0].length)) !== '') {
                            if ((field_match = re.key_access.exec(replacement_field)) !== null) {
                                field_list.push(field_match[1])
                            }
                            else if ((field_match = re.index_access.exec(replacement_field)) !== null) {
                                field_list.push(field_match[1])
                            }
                            else {
                                throw new SyntaxError('[sprintf] failed to parse named argument key')
                            }
                        }
                    }
                    else {
                        throw new SyntaxError('[sprintf] failed to parse named argument key')
                    }
                    match[2] = field_list
                }
                else {
                    arg_names |= 2
                }
                if (arg_names === 3) {
                    throw new Error('[sprintf] mixing positional and named placeholders is not (yet) supported')
                }

                parse_tree.push(
                    {
                        placeholder: match[0],
                        param_no:    match[1],
                        keys:        match[2],
                        sign:        match[3],
                        pad_char:    match[4],
                        align:       match[5],
                        width:       match[6],
                        precision:   match[7],
                        type:        match[8]
                    }
                )
            }
            else {
                throw new SyntaxError('[sprintf] unexpected placeholder')
            }
            _fmt = _fmt.substring(match[0].length)
        }
        return sprintf_cache[fmt] = parse_tree
    }

    /**
     * export to either browser or node.js
     */
    /* eslint-disable quote-props */
    if (true) {
        exports['sprintf'] = sprintf
        exports['vsprintf'] = vsprintf
    }
    if (typeof window !== 'undefined') {
        window['sprintf'] = sprintf
        window['vsprintf'] = vsprintf

        if (true) {
            !(__WEBPACK_AMD_DEFINE_RESULT__ = (function() {
                return {
                    'sprintf': sprintf,
                    'vsprintf': vsprintf
                }
            }).call(exports, __webpack_require__, exports, module),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__))
        }
    }
    /* eslint-enable quote-props */
}(); // eslint-disable-line


/***/ }),

/***/ "./node_modules/underscore.string/camelize.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/camelize.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var trim = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");
var decap = __webpack_require__(/*! ./decapitalize */ "./node_modules/underscore.string/decapitalize.js");

module.exports = function camelize(str, decapitalize) {
  str = trim(str).replace(/[-_\s]+(.)?/g, function(match, c) {
    return c ? c.toUpperCase() : '';
  });

  if (decapitalize === true) {
    return decap(str);
  } else {
    return str;
  }
};


/***/ }),

/***/ "./node_modules/underscore.string/capitalize.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore.string/capitalize.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function capitalize(str, lowercaseRest) {
  str = makeString(str);
  var remainingChars = !lowercaseRest ? str.slice(1) : str.slice(1).toLowerCase();

  return str.charAt(0).toUpperCase() + remainingChars;
};


/***/ }),

/***/ "./node_modules/underscore.string/chars.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/chars.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function chars(str) {
  return makeString(str).split('');
};


/***/ }),

/***/ "./node_modules/underscore.string/chop.js":
/*!************************************************!*\
  !*** ./node_modules/underscore.string/chop.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function chop(str, step) {
  if (str == null) return [];
  str = String(str);
  step = ~~step;
  return step > 0 ? str.match(new RegExp('.{1,' + step + '}', 'g')) : [str];
};


/***/ }),

/***/ "./node_modules/underscore.string/classify.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/classify.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var capitalize = __webpack_require__(/*! ./capitalize */ "./node_modules/underscore.string/capitalize.js");
var camelize = __webpack_require__(/*! ./camelize */ "./node_modules/underscore.string/camelize.js");
var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function classify(str) {
  str = makeString(str);
  return capitalize(camelize(str.replace(/[\W_]/g, ' ')).replace(/\s/g, ''));
};


/***/ }),

/***/ "./node_modules/underscore.string/clean.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/clean.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var trim = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");

module.exports = function clean(str) {
  return trim(str).replace(/\s\s+/g, ' ');
};


/***/ }),

/***/ "./node_modules/underscore.string/cleanDiacritics.js":
/*!***********************************************************!*\
  !*** ./node_modules/underscore.string/cleanDiacritics.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

var from  = 'ąàáäâãåæăćčĉęèéëêĝĥìíïîĵłľńňòóöőôõðøśșşšŝťțţŭùúüűûñÿýçżźž',
  to    = 'aaaaaaaaaccceeeeeghiiiijllnnoooooooossssstttuuuuuunyyczzz';

from += from.toUpperCase();
to += to.toUpperCase();

to = to.split('');

// for tokens requireing multitoken output
from += 'ß';
to.push('ss');


module.exports = function cleanDiacritics(str) {
  return makeString(str).replace(/.{1}/g, function(c){
    var index = from.indexOf(c);
    return index === -1 ? c : to[index];
  });
};


/***/ }),

/***/ "./node_modules/underscore.string/count.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/count.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function(str, substr) {
  str = makeString(str);
  substr = makeString(substr);

  if (str.length === 0 || substr.length === 0) return 0;
  
  return str.split(substr).length - 1;
};


/***/ }),

/***/ "./node_modules/underscore.string/dasherize.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore.string/dasherize.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var trim = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");

module.exports = function dasherize(str) {
  return trim(str).replace(/([A-Z])/g, '-$1').replace(/[-_\s]+/g, '-').toLowerCase();
};


/***/ }),

/***/ "./node_modules/underscore.string/decapitalize.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore.string/decapitalize.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function decapitalize(str) {
  str = makeString(str);
  return str.charAt(0).toLowerCase() + str.slice(1);
};


/***/ }),

/***/ "./node_modules/underscore.string/dedent.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore.string/dedent.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

function getIndent(str) {
  var matches = str.match(/^[\s\\t]*/gm);
  var indent = matches[0].length;
  
  for (var i = 1; i < matches.length; i++) {
    indent = Math.min(matches[i].length, indent);
  }

  return indent;
}

module.exports = function dedent(str, pattern) {
  str = makeString(str);
  var indent = getIndent(str);
  var reg;

  if (indent === 0) return str;

  if (typeof pattern === 'string') {
    reg = new RegExp('^' + pattern, 'gm');
  } else {
    reg = new RegExp('^[ \\t]{' + indent + '}', 'gm');
  }

  return str.replace(reg, '');
};


/***/ }),

/***/ "./node_modules/underscore.string/endsWith.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/endsWith.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var toPositive = __webpack_require__(/*! ./helper/toPositive */ "./node_modules/underscore.string/helper/toPositive.js");

module.exports = function endsWith(str, ends, position) {
  str = makeString(str);
  ends = '' + ends;
  if (typeof position == 'undefined') {
    position = str.length - ends.length;
  } else {
    position = Math.min(toPositive(position), str.length) - ends.length;
  }
  return position >= 0 && str.indexOf(ends, position) === position;
};


/***/ }),

/***/ "./node_modules/underscore.string/escapeHTML.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore.string/escapeHTML.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var escapeChars = __webpack_require__(/*! ./helper/escapeChars */ "./node_modules/underscore.string/helper/escapeChars.js");

var regexString = '[';
for(var key in escapeChars) {
  regexString += key;
}
regexString += ']';

var regex = new RegExp( regexString, 'g');

module.exports = function escapeHTML(str) {

  return makeString(str).replace(regex, function(m) {
    return '&' + escapeChars[m] + ';';
  });
};


/***/ }),

/***/ "./node_modules/underscore.string/exports.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore.string/exports.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function() {
  var result = {};

  for (var prop in this) {
    if (!this.hasOwnProperty(prop) || prop.match(/^(?:include|contains|reverse|join|map|wrap)$/)) continue;
    result[prop] = this[prop];
  }

  return result;
};


/***/ }),

/***/ "./node_modules/underscore.string/helper/adjacent.js":
/*!***********************************************************!*\
  !*** ./node_modules/underscore.string/helper/adjacent.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function adjacent(str, direction) {
  str = makeString(str);
  if (str.length === 0) {
    return '';
  }
  return str.slice(0, -1) + String.fromCharCode(str.charCodeAt(str.length - 1) + direction);
};


/***/ }),

/***/ "./node_modules/underscore.string/helper/defaultToWhiteSpace.js":
/*!**********************************************************************!*\
  !*** ./node_modules/underscore.string/helper/defaultToWhiteSpace.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var escapeRegExp = __webpack_require__(/*! ./escapeRegExp */ "./node_modules/underscore.string/helper/escapeRegExp.js");

module.exports = function defaultToWhiteSpace(characters) {
  if (characters == null)
    return '\\s';
  else if (characters.source)
    return characters.source;
  else
    return '[' + escapeRegExp(characters) + ']';
};


/***/ }),

/***/ "./node_modules/underscore.string/helper/escapeChars.js":
/*!**************************************************************!*\
  !*** ./node_modules/underscore.string/helper/escapeChars.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* We're explicitly defining the list of entities we want to escape.
nbsp is an HTML entity, but we don't want to escape all space characters in a string, hence its omission in this map.

*/
var escapeChars = {
  '¢' : 'cent',
  '£' : 'pound',
  '¥' : 'yen',
  '€': 'euro',
  '©' :'copy',
  '®' : 'reg',
  '<' : 'lt',
  '>' : 'gt',
  '"' : 'quot',
  '&' : 'amp',
  '\'' : '#39'
};

module.exports = escapeChars;


/***/ }),

/***/ "./node_modules/underscore.string/helper/escapeRegExp.js":
/*!***************************************************************!*\
  !*** ./node_modules/underscore.string/helper/escapeRegExp.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function escapeRegExp(str) {
  return makeString(str).replace(/([.*+?^=!:${}()|[\]\/\\])/g, '\\$1');
};


/***/ }),

/***/ "./node_modules/underscore.string/helper/htmlEntities.js":
/*!***************************************************************!*\
  !*** ./node_modules/underscore.string/helper/htmlEntities.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*
We're explicitly defining the list of entities that might see in escape HTML strings
*/
var htmlEntities = {
  nbsp: ' ',
  cent: '¢',
  pound: '£',
  yen: '¥',
  euro: '€',
  copy: '©',
  reg: '®',
  lt: '<',
  gt: '>',
  quot: '"',
  amp: '&',
  apos: '\''
};

module.exports = htmlEntities;


/***/ }),

/***/ "./node_modules/underscore.string/helper/makeString.js":
/*!*************************************************************!*\
  !*** ./node_modules/underscore.string/helper/makeString.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/**
 * Ensure some object is a coerced to a string
 **/
module.exports = function makeString(object) {
  if (object == null) return '';
  return '' + object;
};


/***/ }),

/***/ "./node_modules/underscore.string/helper/strRepeat.js":
/*!************************************************************!*\
  !*** ./node_modules/underscore.string/helper/strRepeat.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function strRepeat(str, qty){
  if (qty < 1) return '';
  var result = '';
  while (qty > 0) {
    if (qty & 1) result += str;
    qty >>= 1, str += str;
  }
  return result;
};


/***/ }),

/***/ "./node_modules/underscore.string/helper/toPositive.js":
/*!*************************************************************!*\
  !*** ./node_modules/underscore.string/helper/toPositive.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function toPositive(number) {
  return number < 0 ? 0 : (+number || 0);
};


/***/ }),

/***/ "./node_modules/underscore.string/humanize.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/humanize.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var capitalize = __webpack_require__(/*! ./capitalize */ "./node_modules/underscore.string/capitalize.js");
var underscored = __webpack_require__(/*! ./underscored */ "./node_modules/underscore.string/underscored.js");
var trim = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");

module.exports = function humanize(str) {
  return capitalize(trim(underscored(str).replace(/_id$/, '').replace(/_/g, ' ')));
};


/***/ }),

/***/ "./node_modules/underscore.string/include.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore.string/include.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function include(str, needle) {
  if (needle === '') return true;
  return makeString(str).indexOf(needle) !== -1;
};


/***/ }),

/***/ "./node_modules/underscore.string/index.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/index.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/*
* Underscore.string
* (c) 2010 Esa-Matti Suuronen <esa-matti aet suuronen dot org>
* Underscore.string is freely distributable under the terms of the MIT license.
* Documentation: https://github.com/epeli/underscore.string
* Some code is borrowed from MooTools and Alexandru Marasteanu.
* Version '3.3.4'
* @preserve
*/



function s(value) {
  /* jshint validthis: true */
  if (!(this instanceof s)) return new s(value);
  this._wrapped = value;
}

s.VERSION = '3.3.4';

s.isBlank          = __webpack_require__(/*! ./isBlank */ "./node_modules/underscore.string/isBlank.js");
s.stripTags        = __webpack_require__(/*! ./stripTags */ "./node_modules/underscore.string/stripTags.js");
s.capitalize       = __webpack_require__(/*! ./capitalize */ "./node_modules/underscore.string/capitalize.js");
s.decapitalize     = __webpack_require__(/*! ./decapitalize */ "./node_modules/underscore.string/decapitalize.js");
s.chop             = __webpack_require__(/*! ./chop */ "./node_modules/underscore.string/chop.js");
s.trim             = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");
s.clean            = __webpack_require__(/*! ./clean */ "./node_modules/underscore.string/clean.js");
s.cleanDiacritics  = __webpack_require__(/*! ./cleanDiacritics */ "./node_modules/underscore.string/cleanDiacritics.js");
s.count            = __webpack_require__(/*! ./count */ "./node_modules/underscore.string/count.js");
s.chars            = __webpack_require__(/*! ./chars */ "./node_modules/underscore.string/chars.js");
s.swapCase         = __webpack_require__(/*! ./swapCase */ "./node_modules/underscore.string/swapCase.js");
s.escapeHTML       = __webpack_require__(/*! ./escapeHTML */ "./node_modules/underscore.string/escapeHTML.js");
s.unescapeHTML     = __webpack_require__(/*! ./unescapeHTML */ "./node_modules/underscore.string/unescapeHTML.js");
s.splice           = __webpack_require__(/*! ./splice */ "./node_modules/underscore.string/splice.js");
s.insert           = __webpack_require__(/*! ./insert */ "./node_modules/underscore.string/insert.js");
s.replaceAll       = __webpack_require__(/*! ./replaceAll */ "./node_modules/underscore.string/replaceAll.js");
s.include          = __webpack_require__(/*! ./include */ "./node_modules/underscore.string/include.js");
s.join             = __webpack_require__(/*! ./join */ "./node_modules/underscore.string/join.js");
s.lines            = __webpack_require__(/*! ./lines */ "./node_modules/underscore.string/lines.js");
s.dedent           = __webpack_require__(/*! ./dedent */ "./node_modules/underscore.string/dedent.js");
s.reverse          = __webpack_require__(/*! ./reverse */ "./node_modules/underscore.string/reverse.js");
s.startsWith       = __webpack_require__(/*! ./startsWith */ "./node_modules/underscore.string/startsWith.js");
s.endsWith         = __webpack_require__(/*! ./endsWith */ "./node_modules/underscore.string/endsWith.js");
s.pred             = __webpack_require__(/*! ./pred */ "./node_modules/underscore.string/pred.js");
s.succ             = __webpack_require__(/*! ./succ */ "./node_modules/underscore.string/succ.js");
s.titleize         = __webpack_require__(/*! ./titleize */ "./node_modules/underscore.string/titleize.js");
s.camelize         = __webpack_require__(/*! ./camelize */ "./node_modules/underscore.string/camelize.js");
s.underscored      = __webpack_require__(/*! ./underscored */ "./node_modules/underscore.string/underscored.js");
s.dasherize        = __webpack_require__(/*! ./dasherize */ "./node_modules/underscore.string/dasherize.js");
s.classify         = __webpack_require__(/*! ./classify */ "./node_modules/underscore.string/classify.js");
s.humanize         = __webpack_require__(/*! ./humanize */ "./node_modules/underscore.string/humanize.js");
s.ltrim            = __webpack_require__(/*! ./ltrim */ "./node_modules/underscore.string/ltrim.js");
s.rtrim            = __webpack_require__(/*! ./rtrim */ "./node_modules/underscore.string/rtrim.js");
s.truncate         = __webpack_require__(/*! ./truncate */ "./node_modules/underscore.string/truncate.js");
s.prune            = __webpack_require__(/*! ./prune */ "./node_modules/underscore.string/prune.js");
s.words            = __webpack_require__(/*! ./words */ "./node_modules/underscore.string/words.js");
s.pad              = __webpack_require__(/*! ./pad */ "./node_modules/underscore.string/pad.js");
s.lpad             = __webpack_require__(/*! ./lpad */ "./node_modules/underscore.string/lpad.js");
s.rpad             = __webpack_require__(/*! ./rpad */ "./node_modules/underscore.string/rpad.js");
s.lrpad            = __webpack_require__(/*! ./lrpad */ "./node_modules/underscore.string/lrpad.js");
s.sprintf          = __webpack_require__(/*! ./sprintf */ "./node_modules/underscore.string/sprintf.js");
s.vsprintf         = __webpack_require__(/*! ./vsprintf */ "./node_modules/underscore.string/vsprintf.js");
s.toNumber         = __webpack_require__(/*! ./toNumber */ "./node_modules/underscore.string/toNumber.js");
s.numberFormat     = __webpack_require__(/*! ./numberFormat */ "./node_modules/underscore.string/numberFormat.js");
s.strRight         = __webpack_require__(/*! ./strRight */ "./node_modules/underscore.string/strRight.js");
s.strRightBack     = __webpack_require__(/*! ./strRightBack */ "./node_modules/underscore.string/strRightBack.js");
s.strLeft          = __webpack_require__(/*! ./strLeft */ "./node_modules/underscore.string/strLeft.js");
s.strLeftBack      = __webpack_require__(/*! ./strLeftBack */ "./node_modules/underscore.string/strLeftBack.js");
s.toSentence       = __webpack_require__(/*! ./toSentence */ "./node_modules/underscore.string/toSentence.js");
s.toSentenceSerial = __webpack_require__(/*! ./toSentenceSerial */ "./node_modules/underscore.string/toSentenceSerial.js");
s.slugify          = __webpack_require__(/*! ./slugify */ "./node_modules/underscore.string/slugify.js");
s.surround         = __webpack_require__(/*! ./surround */ "./node_modules/underscore.string/surround.js");
s.quote            = __webpack_require__(/*! ./quote */ "./node_modules/underscore.string/quote.js");
s.unquote          = __webpack_require__(/*! ./unquote */ "./node_modules/underscore.string/unquote.js");
s.repeat           = __webpack_require__(/*! ./repeat */ "./node_modules/underscore.string/repeat.js");
s.naturalCmp       = __webpack_require__(/*! ./naturalCmp */ "./node_modules/underscore.string/naturalCmp.js");
s.levenshtein      = __webpack_require__(/*! ./levenshtein */ "./node_modules/underscore.string/levenshtein.js");
s.toBoolean        = __webpack_require__(/*! ./toBoolean */ "./node_modules/underscore.string/toBoolean.js");
s.exports          = __webpack_require__(/*! ./exports */ "./node_modules/underscore.string/exports.js");
s.escapeRegExp     = __webpack_require__(/*! ./helper/escapeRegExp */ "./node_modules/underscore.string/helper/escapeRegExp.js");
s.wrap             = __webpack_require__(/*! ./wrap */ "./node_modules/underscore.string/wrap.js");
s.map              = __webpack_require__(/*! ./map */ "./node_modules/underscore.string/map.js");

// Aliases
s.strip     = s.trim;
s.lstrip    = s.ltrim;
s.rstrip    = s.rtrim;
s.center    = s.lrpad;
s.rjust     = s.lpad;
s.ljust     = s.rpad;
s.contains  = s.include;
s.q         = s.quote;
s.toBool    = s.toBoolean;
s.camelcase = s.camelize;
s.mapChars  = s.map;


// Implement chaining
s.prototype = {
  value: function value() {
    return this._wrapped;
  }
};

function fn2method(key, fn) {
  if (typeof fn !== 'function') return;
  s.prototype[key] = function() {
    var args = [this._wrapped].concat(Array.prototype.slice.call(arguments));
    var res = fn.apply(null, args);
    // if the result is non-string stop the chain and return the value
    return typeof res === 'string' ? new s(res) : res;
  };
}

// Copy functions to instance methods for chaining
for (var key in s) fn2method(key, s[key]);

fn2method('tap', function tap(string, fn) {
  return fn(string);
});

function prototype2method(methodName) {
  fn2method(methodName, function(context) {
    var args = Array.prototype.slice.call(arguments, 1);
    return String.prototype[methodName].apply(context, args);
  });
}

var prototypeMethods = [
  'toUpperCase',
  'toLowerCase',
  'split',
  'replace',
  'slice',
  'substring',
  'substr',
  'concat'
];

for (var method in prototypeMethods) prototype2method(prototypeMethods[method]);


module.exports = s;


/***/ }),

/***/ "./node_modules/underscore.string/insert.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore.string/insert.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var splice = __webpack_require__(/*! ./splice */ "./node_modules/underscore.string/splice.js");

module.exports = function insert(str, i, substr) {
  return splice(str, i, 0, substr);
};


/***/ }),

/***/ "./node_modules/underscore.string/isBlank.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore.string/isBlank.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function isBlank(str) {
  return (/^\s*$/).test(makeString(str));
};


/***/ }),

/***/ "./node_modules/underscore.string/join.js":
/*!************************************************!*\
  !*** ./node_modules/underscore.string/join.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var slice = [].slice;

module.exports = function join() {
  var args = slice.call(arguments),
    separator = args.shift();

  return args.join(makeString(separator));
};


/***/ }),

/***/ "./node_modules/underscore.string/levenshtein.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore.string/levenshtein.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

/**
 * Based on the implementation here: https://github.com/hiddentao/fast-levenshtein
 */
module.exports = function levenshtein(str1, str2) {
  'use strict';
  str1 = makeString(str1);
  str2 = makeString(str2);

  // Short cut cases  
  if (str1 === str2) return 0;
  if (!str1 || !str2) return Math.max(str1.length, str2.length);

  // two rows
  var prevRow = new Array(str2.length + 1);

  // initialise previous row
  for (var i = 0; i < prevRow.length; ++i) {
    prevRow[i] = i;
  }

  // calculate current row distance from previous row
  for (i = 0; i < str1.length; ++i) {
    var nextCol = i + 1;

    for (var j = 0; j < str2.length; ++j) {
      var curCol = nextCol;

      // substution
      nextCol = prevRow[j] + ( (str1.charAt(i) === str2.charAt(j)) ? 0 : 1 );
      // insertion
      var tmp = curCol + 1;
      if (nextCol > tmp) {
        nextCol = tmp;
      }
      // deletion
      tmp = prevRow[j + 1] + 1;
      if (nextCol > tmp) {
        nextCol = tmp;
      }

      // copy current col value into previous (in preparation for next iteration)
      prevRow[j] = curCol;
    }

    // copy last col value into previous (in preparation for next iteration)
    prevRow[j] = nextCol;
  }

  return nextCol;
};


/***/ }),

/***/ "./node_modules/underscore.string/lines.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/lines.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function lines(str) {
  if (str == null) return [];
  return String(str).split(/\r\n?|\n/);
};


/***/ }),

/***/ "./node_modules/underscore.string/lpad.js":
/*!************************************************!*\
  !*** ./node_modules/underscore.string/lpad.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var pad = __webpack_require__(/*! ./pad */ "./node_modules/underscore.string/pad.js");

module.exports = function lpad(str, length, padStr) {
  return pad(str, length, padStr);
};


/***/ }),

/***/ "./node_modules/underscore.string/lrpad.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/lrpad.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var pad = __webpack_require__(/*! ./pad */ "./node_modules/underscore.string/pad.js");

module.exports = function lrpad(str, length, padStr) {
  return pad(str, length, padStr, 'both');
};


/***/ }),

/***/ "./node_modules/underscore.string/ltrim.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/ltrim.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var defaultToWhiteSpace = __webpack_require__(/*! ./helper/defaultToWhiteSpace */ "./node_modules/underscore.string/helper/defaultToWhiteSpace.js");
var nativeTrimLeft = String.prototype.trimLeft;

module.exports = function ltrim(str, characters) {
  str = makeString(str);
  if (!characters && nativeTrimLeft) return nativeTrimLeft.call(str);
  characters = defaultToWhiteSpace(characters);
  return str.replace(new RegExp('^' + characters + '+'), '');
};


/***/ }),

/***/ "./node_modules/underscore.string/map.js":
/*!***********************************************!*\
  !*** ./node_modules/underscore.string/map.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function(str, callback) {
  str = makeString(str);

  if (str.length === 0 || typeof callback !== 'function') return str;

  return str.replace(/./g, callback);
};


/***/ }),

/***/ "./node_modules/underscore.string/naturalCmp.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore.string/naturalCmp.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function naturalCmp(str1, str2) {
  if (str1 == str2) return 0;
  if (!str1) return -1;
  if (!str2) return 1;

  var cmpRegex = /(\.\d+|\d+|\D+)/g,
    tokens1 = String(str1).match(cmpRegex),
    tokens2 = String(str2).match(cmpRegex),
    count = Math.min(tokens1.length, tokens2.length);

  for (var i = 0; i < count; i++) {
    var a = tokens1[i],
      b = tokens2[i];

    if (a !== b) {
      var num1 = +a;
      var num2 = +b;
      if (num1 === num1 && num2 === num2) {
        return num1 > num2 ? 1 : -1;
      }
      return a < b ? -1 : 1;
    }
  }

  if (tokens1.length != tokens2.length)
    return tokens1.length - tokens2.length;

  return str1 < str2 ? -1 : 1;
};


/***/ }),

/***/ "./node_modules/underscore.string/numberFormat.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore.string/numberFormat.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function numberFormat(number, dec, dsep, tsep) {
  if (isNaN(number) || number == null) return '';

  number = number.toFixed(~~dec);
  tsep = typeof tsep == 'string' ? tsep : ',';

  var parts = number.split('.'),
    fnums = parts[0],
    decimals = parts[1] ? (dsep || '.') + parts[1] : '';

  return fnums.replace(/(\d)(?=(?:\d{3})+$)/g, '$1' + tsep) + decimals;
};


/***/ }),

/***/ "./node_modules/underscore.string/pad.js":
/*!***********************************************!*\
  !*** ./node_modules/underscore.string/pad.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var strRepeat = __webpack_require__(/*! ./helper/strRepeat */ "./node_modules/underscore.string/helper/strRepeat.js");

module.exports = function pad(str, length, padStr, type) {
  str = makeString(str);
  length = ~~length;

  var padlen = 0;

  if (!padStr)
    padStr = ' ';
  else if (padStr.length > 1)
    padStr = padStr.charAt(0);

  switch (type) {
  case 'right':
    padlen = length - str.length;
    return str + strRepeat(padStr, padlen);
  case 'both':
    padlen = length - str.length;
    return strRepeat(padStr, Math.ceil(padlen / 2)) + str + strRepeat(padStr, Math.floor(padlen / 2));
  default: // 'left'
    padlen = length - str.length;
    return strRepeat(padStr, padlen) + str;
  }
};


/***/ }),

/***/ "./node_modules/underscore.string/pred.js":
/*!************************************************!*\
  !*** ./node_modules/underscore.string/pred.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var adjacent = __webpack_require__(/*! ./helper/adjacent */ "./node_modules/underscore.string/helper/adjacent.js");

module.exports = function succ(str) {
  return adjacent(str, -1);
};


/***/ }),

/***/ "./node_modules/underscore.string/prune.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/prune.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/**
 * _s.prune: a more elegant version of truncate
 * prune extra chars, never leaving a half-chopped word.
 * @author github.com/rwz
 */
var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var rtrim = __webpack_require__(/*! ./rtrim */ "./node_modules/underscore.string/rtrim.js");

module.exports = function prune(str, length, pruneStr) {
  str = makeString(str);
  length = ~~length;
  pruneStr = pruneStr != null ? String(pruneStr) : '...';

  if (str.length <= length) return str;

  var tmpl = function(c) {
      return c.toUpperCase() !== c.toLowerCase() ? 'A' : ' ';
    },
    template = str.slice(0, length + 1).replace(/.(?=\W*\w*$)/g, tmpl); // 'Hello, world' -> 'HellAA AAAAA'

  if (template.slice(template.length - 2).match(/\w\w/))
    template = template.replace(/\s*\S+$/, '');
  else
    template = rtrim(template.slice(0, template.length - 1));

  return (template + pruneStr).length > str.length ? str : str.slice(0, template.length) + pruneStr;
};


/***/ }),

/***/ "./node_modules/underscore.string/quote.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/quote.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var surround = __webpack_require__(/*! ./surround */ "./node_modules/underscore.string/surround.js");

module.exports = function quote(str, quoteChar) {
  return surround(str, quoteChar || '"');
};


/***/ }),

/***/ "./node_modules/underscore.string/repeat.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore.string/repeat.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var strRepeat = __webpack_require__(/*! ./helper/strRepeat */ "./node_modules/underscore.string/helper/strRepeat.js");

module.exports = function repeat(str, qty, separator) {
  str = makeString(str);

  qty = ~~qty;

  // using faster implementation if separator is not needed;
  if (separator == null) return strRepeat(str, qty);

  // this one is about 300x slower in Google Chrome
  /*eslint no-empty: 0*/
  for (var repeat = []; qty > 0; repeat[--qty] = str) {}
  return repeat.join(separator);
};


/***/ }),

/***/ "./node_modules/underscore.string/replaceAll.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore.string/replaceAll.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function replaceAll(str, find, replace, ignorecase) {
  var flags = (ignorecase === true)?'gi':'g';
  var reg = new RegExp(find, flags);

  return makeString(str).replace(reg, replace);
};


/***/ }),

/***/ "./node_modules/underscore.string/reverse.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore.string/reverse.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var chars = __webpack_require__(/*! ./chars */ "./node_modules/underscore.string/chars.js");

module.exports = function reverse(str) {
  return chars(str).reverse().join('');
};


/***/ }),

/***/ "./node_modules/underscore.string/rpad.js":
/*!************************************************!*\
  !*** ./node_modules/underscore.string/rpad.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var pad = __webpack_require__(/*! ./pad */ "./node_modules/underscore.string/pad.js");

module.exports = function rpad(str, length, padStr) {
  return pad(str, length, padStr, 'right');
};


/***/ }),

/***/ "./node_modules/underscore.string/rtrim.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/rtrim.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var defaultToWhiteSpace = __webpack_require__(/*! ./helper/defaultToWhiteSpace */ "./node_modules/underscore.string/helper/defaultToWhiteSpace.js");
var nativeTrimRight = String.prototype.trimRight;

module.exports = function rtrim(str, characters) {
  str = makeString(str);
  if (!characters && nativeTrimRight) return nativeTrimRight.call(str);
  characters = defaultToWhiteSpace(characters);
  return str.replace(new RegExp(characters + '+$'), '');
};


/***/ }),

/***/ "./node_modules/underscore.string/slugify.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore.string/slugify.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var trim = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");
var dasherize = __webpack_require__(/*! ./dasherize */ "./node_modules/underscore.string/dasherize.js");
var cleanDiacritics = __webpack_require__(/*! ./cleanDiacritics */ "./node_modules/underscore.string/cleanDiacritics.js");

module.exports = function slugify(str) {
  return trim(dasherize(cleanDiacritics(str).replace(/[^\w\s-]/g, '-').toLowerCase()), '-');
};


/***/ }),

/***/ "./node_modules/underscore.string/splice.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore.string/splice.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var chars = __webpack_require__(/*! ./chars */ "./node_modules/underscore.string/chars.js");

module.exports = function splice(str, i, howmany, substr) {
  var arr = chars(str);
  arr.splice(~~i, ~~howmany, substr);
  return arr.join('');
};


/***/ }),

/***/ "./node_modules/underscore.string/sprintf.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore.string/sprintf.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var deprecate = __webpack_require__(/*! util-deprecate */ "./node_modules/util-deprecate/browser.js");

module.exports = deprecate(__webpack_require__(/*! sprintf-js */ "./node_modules/sprintf-js/src/sprintf.js").sprintf,
  'sprintf() will be removed in the next major release, use the sprintf-js package instead.');


/***/ }),

/***/ "./node_modules/underscore.string/startsWith.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore.string/startsWith.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var toPositive = __webpack_require__(/*! ./helper/toPositive */ "./node_modules/underscore.string/helper/toPositive.js");

module.exports = function startsWith(str, starts, position) {
  str = makeString(str);
  starts = '' + starts;
  position = position == null ? 0 : Math.min(toPositive(position), str.length);
  return str.lastIndexOf(starts, position) === position;
};


/***/ }),

/***/ "./node_modules/underscore.string/strLeft.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore.string/strLeft.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function strLeft(str, sep) {
  str = makeString(str);
  sep = makeString(sep);
  var pos = !sep ? -1 : str.indexOf(sep);
  return~ pos ? str.slice(0, pos) : str;
};


/***/ }),

/***/ "./node_modules/underscore.string/strLeftBack.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore.string/strLeftBack.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function strLeftBack(str, sep) {
  str = makeString(str);
  sep = makeString(sep);
  var pos = str.lastIndexOf(sep);
  return~ pos ? str.slice(0, pos) : str;
};


/***/ }),

/***/ "./node_modules/underscore.string/strRight.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/strRight.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function strRight(str, sep) {
  str = makeString(str);
  sep = makeString(sep);
  var pos = !sep ? -1 : str.indexOf(sep);
  return~ pos ? str.slice(pos + sep.length, str.length) : str;
};


/***/ }),

/***/ "./node_modules/underscore.string/strRightBack.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore.string/strRightBack.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function strRightBack(str, sep) {
  str = makeString(str);
  sep = makeString(sep);
  var pos = !sep ? -1 : str.lastIndexOf(sep);
  return~ pos ? str.slice(pos + sep.length, str.length) : str;
};


/***/ }),

/***/ "./node_modules/underscore.string/stripTags.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore.string/stripTags.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function stripTags(str) {
  return makeString(str).replace(/<\/?[^>]+>/g, '');
};


/***/ }),

/***/ "./node_modules/underscore.string/succ.js":
/*!************************************************!*\
  !*** ./node_modules/underscore.string/succ.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var adjacent = __webpack_require__(/*! ./helper/adjacent */ "./node_modules/underscore.string/helper/adjacent.js");

module.exports = function succ(str) {
  return adjacent(str, 1);
};


/***/ }),

/***/ "./node_modules/underscore.string/surround.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/surround.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function surround(str, wrapper) {
  return [wrapper, str, wrapper].join('');
};


/***/ }),

/***/ "./node_modules/underscore.string/swapCase.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/swapCase.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function swapCase(str) {
  return makeString(str).replace(/\S/g, function(c) {
    return c === c.toUpperCase() ? c.toLowerCase() : c.toUpperCase();
  });
};


/***/ }),

/***/ "./node_modules/underscore.string/titleize.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/titleize.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function titleize(str) {
  return makeString(str).toLowerCase().replace(/(?:^|\s|-)\S/g, function(c) {
    return c.toUpperCase();
  });
};


/***/ }),

/***/ "./node_modules/underscore.string/toBoolean.js":
/*!*****************************************************!*\
  !*** ./node_modules/underscore.string/toBoolean.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var trim = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");

function boolMatch(s, matchers) {
  var i, matcher, down = s.toLowerCase();
  matchers = [].concat(matchers);
  for (i = 0; i < matchers.length; i += 1) {
    matcher = matchers[i];
    if (!matcher) continue;
    if (matcher.test && matcher.test(s)) return true;
    if (matcher.toLowerCase() === down) return true;
  }
}

module.exports = function toBoolean(str, trueValues, falseValues) {
  if (typeof str === 'number') str = '' + str;
  if (typeof str !== 'string') return !!str;
  str = trim(str);
  if (boolMatch(str, trueValues || ['true', '1'])) return true;
  if (boolMatch(str, falseValues || ['false', '0'])) return false;
};


/***/ }),

/***/ "./node_modules/underscore.string/toNumber.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/toNumber.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function toNumber(num, precision) {
  if (num == null) return 0;
  var factor = Math.pow(10, isFinite(precision) ? precision : 0);
  return Math.round(num * factor) / factor;
};


/***/ }),

/***/ "./node_modules/underscore.string/toSentence.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore.string/toSentence.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var rtrim = __webpack_require__(/*! ./rtrim */ "./node_modules/underscore.string/rtrim.js");

module.exports = function toSentence(array, separator, lastSeparator, serial) {
  separator = separator || ', ';
  lastSeparator = lastSeparator || ' and ';
  var a = array.slice(),
    lastMember = a.pop();

  if (array.length > 2 && serial) lastSeparator = rtrim(separator) + lastSeparator;

  return a.length ? a.join(separator) + lastSeparator + lastMember : lastMember;
};


/***/ }),

/***/ "./node_modules/underscore.string/toSentenceSerial.js":
/*!************************************************************!*\
  !*** ./node_modules/underscore.string/toSentenceSerial.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var toSentence = __webpack_require__(/*! ./toSentence */ "./node_modules/underscore.string/toSentence.js");

module.exports = function toSentenceSerial(array, sep, lastSep) {
  return toSentence(array, sep, lastSep, true);
};


/***/ }),

/***/ "./node_modules/underscore.string/trim.js":
/*!************************************************!*\
  !*** ./node_modules/underscore.string/trim.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var defaultToWhiteSpace = __webpack_require__(/*! ./helper/defaultToWhiteSpace */ "./node_modules/underscore.string/helper/defaultToWhiteSpace.js");
var nativeTrim = String.prototype.trim;

module.exports = function trim(str, characters) {
  str = makeString(str);
  if (!characters && nativeTrim) return nativeTrim.call(str);
  characters = defaultToWhiteSpace(characters);
  return str.replace(new RegExp('^' + characters + '+|' + characters + '+$', 'g'), '');
};


/***/ }),

/***/ "./node_modules/underscore.string/truncate.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/truncate.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function truncate(str, length, truncateStr) {
  str = makeString(str);
  truncateStr = truncateStr || '...';
  length = ~~length;
  return str.length > length ? str.slice(0, length) + truncateStr : str;
};


/***/ }),

/***/ "./node_modules/underscore.string/underscored.js":
/*!*******************************************************!*\
  !*** ./node_modules/underscore.string/underscored.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var trim = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");

module.exports = function underscored(str) {
  return trim(str).replace(/([a-z\d])([A-Z]+)/g, '$1_$2').replace(/[-\s]+/g, '_').toLowerCase();
};


/***/ }),

/***/ "./node_modules/underscore.string/unescapeHTML.js":
/*!********************************************************!*\
  !*** ./node_modules/underscore.string/unescapeHTML.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");
var htmlEntities = __webpack_require__(/*! ./helper/htmlEntities */ "./node_modules/underscore.string/helper/htmlEntities.js");

module.exports = function unescapeHTML(str) {
  return makeString(str).replace(/\&([^;]{1,10});/g, function(entity, entityCode) {
    var match;

    if (entityCode in htmlEntities) {
      return htmlEntities[entityCode];
    /*eslint no-cond-assign: 0*/
    } else if (match = entityCode.match(/^#x([\da-fA-F]+)$/)) {
      return String.fromCharCode(parseInt(match[1], 16));
    /*eslint no-cond-assign: 0*/
    } else if (match = entityCode.match(/^#(\d+)$/)) {
      return String.fromCharCode(~~match[1]);
    } else {
      return entity;
    }
  });
};


/***/ }),

/***/ "./node_modules/underscore.string/unquote.js":
/*!***************************************************!*\
  !*** ./node_modules/underscore.string/unquote.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function unquote(str, quoteChar) {
  quoteChar = quoteChar || '"';
  if (str[0] === quoteChar && str[str.length - 1] === quoteChar)
    return str.slice(1, str.length - 1);
  else return str;
};


/***/ }),

/***/ "./node_modules/underscore.string/vsprintf.js":
/*!****************************************************!*\
  !*** ./node_modules/underscore.string/vsprintf.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var deprecate = __webpack_require__(/*! util-deprecate */ "./node_modules/util-deprecate/browser.js");

module.exports = deprecate(__webpack_require__(/*! sprintf-js */ "./node_modules/sprintf-js/src/sprintf.js").vsprintf,
  'vsprintf() will be removed in the next major release, use the sprintf-js package instead.');


/***/ }),

/***/ "./node_modules/underscore.string/words.js":
/*!*************************************************!*\
  !*** ./node_modules/underscore.string/words.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var isBlank = __webpack_require__(/*! ./isBlank */ "./node_modules/underscore.string/isBlank.js");
var trim = __webpack_require__(/*! ./trim */ "./node_modules/underscore.string/trim.js");

module.exports = function words(str, delimiter) {
  if (isBlank(str)) return [];
  return trim(str, delimiter).split(delimiter || /\s+/);
};


/***/ }),

/***/ "./node_modules/underscore.string/wrap.js":
/*!************************************************!*\
  !*** ./node_modules/underscore.string/wrap.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Wrap
// wraps a string by a certain width

var makeString = __webpack_require__(/*! ./helper/makeString */ "./node_modules/underscore.string/helper/makeString.js");

module.exports = function wrap(str, options){
  str = makeString(str);
  
  options = options || {};
  
  var width = options.width || 75;
  var seperator = options.seperator || '\n';
  var cut = options.cut || false;
  var preserveSpaces = options.preserveSpaces || false;
  var trailingSpaces = options.trailingSpaces || false;
  
  var result;
  
  if(width <= 0){
    return str;
  }
  
  else if(!cut){
  
    var words = str.split(' ');
    var current_column = 0;
    result = '';
  
    while(words.length > 0){
      
      // if adding a space and the next word would cause this line to be longer than width...
      if(1 + words[0].length + current_column > width){
        //start a new line if this line is not already empty
        if(current_column > 0){
          // add a space at the end of the line is preserveSpaces is true
          if (preserveSpaces){
            result += ' ';
            current_column++;
          }
          // fill the rest of the line with spaces if trailingSpaces option is true
          else if(trailingSpaces){
            while(current_column < width){
              result += ' ';
              current_column++;
            }            
          }
          //start new line
          result += seperator;
          current_column = 0;
        }
      }
  
      // if not at the begining of the line, add a space in front of the word
      if(current_column > 0){
        result += ' ';
        current_column++;
      }
  
      // tack on the next word, update current column, a pop words array
      result += words[0];
      current_column += words[0].length;
      words.shift();
  
    }
  
    // fill the rest of the line with spaces if trailingSpaces option is true
    if(trailingSpaces){
      while(current_column < width){
        result += ' ';
        current_column++;
      }            
    }
  
    return result;
  
  }
  
  else {
  
    var index = 0;
    result = '';
  
    // walk through each character and add seperators where appropriate
    while(index < str.length){
      if(index % width == 0 && index > 0){
        result += seperator;
      }
      result += str.charAt(index);
      index++;
    }
  
    // fill the rest of the line with spaces if trailingSpaces option is true
    if(trailingSpaces){
      while(index % width > 0){
        result += ' ';
        index++;
      }            
    }
    
    return result;
  }
};


/***/ }),

/***/ "./node_modules/underscore/modules/index-all.js":
/*!******************************************************!*\
  !*** ./node_modules/underscore/modules/index-all.js ***!
  \******************************************************/
/*! exports provided: default, VERSION, iteratee, restArguments, each, forEach, map, collect, reduce, foldl, inject, reduceRight, foldr, find, detect, filter, select, reject, every, all, some, any, contains, includes, include, invoke, pluck, where, findWhere, max, min, shuffle, sample, sortBy, groupBy, indexBy, countBy, toArray, size, partition, first, head, take, initial, last, rest, tail, drop, compact, flatten, without, uniq, unique, union, intersection, difference, unzip, zip, object, findIndex, findLastIndex, sortedIndex, indexOf, lastIndexOf, range, chunk, bind, partial, bindAll, memoize, delay, defer, throttle, debounce, wrap, negate, compose, after, before, once, keys, allKeys, values, mapObject, pairs, invert, functions, methods, extend, extendOwn, assign, findKey, pick, omit, defaults, create, clone, tap, isMatch, isEqual, isEmpty, isElement, isArray, isObject, isArguments, isFunction, isString, isNumber, isDate, isRegExp, isError, isSymbol, isMap, isWeakMap, isSet, isWeakSet, isFinite, isNaN, isBoolean, isNull, isUndefined, has, identity, constant, noop, property, propertyOf, matcher, matches, times, random, now, escape, unescape, result, uniqueId, templateSettings, template, chain, mixin */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _index_default_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index-default.js */ "./node_modules/underscore/modules/index-default.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _index_default_js__WEBPACK_IMPORTED_MODULE_0__["default"]; });

/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.js */ "./node_modules/underscore/modules/index.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "VERSION", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["VERSION"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "iteratee", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["iteratee"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "restArguments", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["restArguments"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "each", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["each"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "forEach", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["forEach"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "map", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["map"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "collect", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["collect"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reduce", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["reduce"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "foldl", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["foldl"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "inject", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["inject"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reduceRight", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["reduceRight"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "foldr", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["foldr"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "find", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["find"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "detect", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["detect"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "filter", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["filter"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "select", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["select"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "reject", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["reject"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "every", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["every"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "all", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["all"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "some", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["some"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "any", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["any"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "contains", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["contains"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "includes", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["includes"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "include", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["include"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "invoke", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["invoke"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pluck", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["pluck"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "where", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["where"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findWhere", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["findWhere"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "max", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["max"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "min", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["min"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "shuffle", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["shuffle"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sample", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["sample"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sortBy", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["sortBy"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "groupBy", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["groupBy"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "indexBy", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["indexBy"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "countBy", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["countBy"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "toArray", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["toArray"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "size", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["size"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "partition", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["partition"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "first", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["first"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "head", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["head"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "take", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["take"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "initial", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["initial"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "last", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["last"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "rest", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["rest"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "tail", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["tail"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "drop", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["drop"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "compact", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["compact"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "flatten", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["flatten"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "without", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["without"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "uniq", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["uniq"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unique", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["unique"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "union", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["union"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "intersection", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["intersection"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "difference", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["difference"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unzip", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["unzip"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "zip", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["zip"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "object", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["object"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findIndex", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["findIndex"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findLastIndex", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["findLastIndex"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "sortedIndex", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["sortedIndex"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "indexOf", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["indexOf"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "lastIndexOf", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["lastIndexOf"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "range", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["range"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "chunk", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["chunk"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "bind", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["bind"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "partial", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["partial"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "bindAll", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["bindAll"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "memoize", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["memoize"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "delay", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["delay"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "defer", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["defer"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "throttle", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["throttle"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "debounce", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["debounce"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "wrap", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["wrap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "negate", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["negate"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "compose", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["compose"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "after", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["after"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "before", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["before"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "once", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["once"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "keys", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["keys"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "allKeys", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["allKeys"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "values", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["values"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "mapObject", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["mapObject"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pairs", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["pairs"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "invert", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["invert"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "functions", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["functions"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "methods", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["methods"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "extend", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["extend"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "extendOwn", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["extendOwn"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "assign", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["assign"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "findKey", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["findKey"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "pick", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["pick"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "omit", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["omit"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "defaults", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["defaults"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "create", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["create"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "clone", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["clone"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "tap", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["tap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isMatch", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isMatch"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEqual", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isEqual"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEmpty", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isEmpty"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isElement", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isElement"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isArray", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isArray"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isObject", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isObject"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isArguments", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isArguments"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isFunction", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isFunction"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isString", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isString"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNumber", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isNumber"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isDate", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isDate"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isRegExp", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isRegExp"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isError", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isError"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isSymbol", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isSymbol"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isMap", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isMap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isWeakMap", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isWeakMap"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isSet", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isSet"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isWeakSet", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isWeakSet"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isFinite", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isFinite"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNaN", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isNaN"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isBoolean", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isBoolean"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isNull", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isNull"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isUndefined", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["isUndefined"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "has", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["has"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "identity", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["identity"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "constant", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["constant"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "noop", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["noop"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "property", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["property"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "propertyOf", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["propertyOf"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "matcher", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["matcher"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "matches", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["matches"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "times", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["times"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "random", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["random"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "now", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["now"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "escape", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["escape"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "unescape", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["unescape"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "result", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["result"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "uniqueId", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["uniqueId"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "templateSettings", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["templateSettings"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "template", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["template"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "chain", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["chain"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "mixin", function() { return _index_js__WEBPACK_IMPORTED_MODULE_1__["mixin"]; });





/***/ }),

/***/ "./node_modules/underscore/modules/index-default.js":
/*!**********************************************************!*\
  !*** ./node_modules/underscore/modules/index-default.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index.js */ "./node_modules/underscore/modules/index.js");



// Add all of the Underscore functions to the wrapper object.
var _ = Object(_index_js__WEBPACK_IMPORTED_MODULE_0__["mixin"])(_index_js__WEBPACK_IMPORTED_MODULE_0__);
// Legacy Node.js API
_._ = _;
// Export the Underscore API.
/* harmony default export */ __webpack_exports__["default"] = (_);


/***/ }),

/***/ "./node_modules/underscore/modules/index.js":
/*!**************************************************!*\
  !*** ./node_modules/underscore/modules/index.js ***!
  \**************************************************/
/*! exports provided: default, VERSION, iteratee, restArguments, each, forEach, map, collect, reduce, foldl, inject, reduceRight, foldr, find, detect, filter, select, reject, every, all, some, any, contains, includes, include, invoke, pluck, where, findWhere, max, min, shuffle, sample, sortBy, groupBy, indexBy, countBy, toArray, size, partition, first, head, take, initial, last, rest, tail, drop, compact, flatten, without, uniq, unique, union, intersection, difference, unzip, zip, object, findIndex, findLastIndex, sortedIndex, indexOf, lastIndexOf, range, chunk, bind, partial, bindAll, memoize, delay, defer, throttle, debounce, wrap, negate, compose, after, before, once, keys, allKeys, values, mapObject, pairs, invert, functions, methods, extend, extendOwn, assign, findKey, pick, omit, defaults, create, clone, tap, isMatch, isEqual, isEmpty, isElement, isArray, isObject, isArguments, isFunction, isString, isNumber, isDate, isRegExp, isError, isSymbol, isMap, isWeakMap, isSet, isWeakSet, isFinite, isNaN, isBoolean, isNull, isUndefined, has, identity, constant, noop, property, propertyOf, matcher, matches, times, random, now, escape, unescape, result, uniqueId, templateSettings, template, chain, mixin */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "VERSION", function() { return VERSION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "iteratee", function() { return iteratee; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "restArguments", function() { return restArguments; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "each", function() { return each; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "forEach", function() { return each; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "map", function() { return map; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "collect", function() { return map; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "reduce", function() { return reduce; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "foldl", function() { return reduce; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "inject", function() { return reduce; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "reduceRight", function() { return reduceRight; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "foldr", function() { return reduceRight; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "find", function() { return find; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "detect", function() { return find; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "filter", function() { return filter; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "select", function() { return filter; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "reject", function() { return reject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "every", function() { return every; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "all", function() { return every; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "some", function() { return some; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "any", function() { return some; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "contains", function() { return contains; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "includes", function() { return contains; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "include", function() { return contains; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "invoke", function() { return invoke; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "pluck", function() { return pluck; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "where", function() { return where; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "findWhere", function() { return findWhere; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "max", function() { return max; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "min", function() { return min; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "shuffle", function() { return shuffle; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "sample", function() { return sample; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "sortBy", function() { return sortBy; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "groupBy", function() { return groupBy; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "indexBy", function() { return indexBy; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "countBy", function() { return countBy; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toArray", function() { return toArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "size", function() { return size; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "partition", function() { return partition; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "first", function() { return first; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "head", function() { return first; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "take", function() { return first; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "initial", function() { return initial; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "last", function() { return last; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "rest", function() { return rest; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "tail", function() { return rest; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "drop", function() { return rest; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "compact", function() { return compact; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "flatten", function() { return flatten; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "without", function() { return without; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "uniq", function() { return uniq; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "unique", function() { return uniq; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "union", function() { return union; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "intersection", function() { return intersection; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "difference", function() { return difference; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "unzip", function() { return unzip; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "zip", function() { return zip; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "object", function() { return object; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "findIndex", function() { return findIndex; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "findLastIndex", function() { return findLastIndex; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "sortedIndex", function() { return sortedIndex; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "indexOf", function() { return indexOf; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "lastIndexOf", function() { return lastIndexOf; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "range", function() { return range; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "chunk", function() { return chunk; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "bind", function() { return bind; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "partial", function() { return partial; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "bindAll", function() { return bindAll; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "memoize", function() { return memoize; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "delay", function() { return delay; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "defer", function() { return defer; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "throttle", function() { return throttle; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "debounce", function() { return debounce; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "wrap", function() { return wrap; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "negate", function() { return negate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "compose", function() { return compose; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "after", function() { return after; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "before", function() { return before; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "once", function() { return once; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "keys", function() { return keys; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "allKeys", function() { return allKeys; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "values", function() { return values; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mapObject", function() { return mapObject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "pairs", function() { return pairs; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "invert", function() { return invert; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "functions", function() { return functions; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "methods", function() { return functions; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "extend", function() { return extend; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "extendOwn", function() { return extendOwn; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "assign", function() { return extendOwn; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "findKey", function() { return findKey; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "pick", function() { return pick; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "omit", function() { return omit; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "defaults", function() { return defaults; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "create", function() { return create; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "clone", function() { return clone; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "tap", function() { return tap; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isMatch", function() { return isMatch; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEqual", function() { return isEqual; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEmpty", function() { return isEmpty; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isElement", function() { return isElement; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isArray", function() { return isArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isObject", function() { return isObject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isArguments", function() { return isArguments; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFunction", function() { return isFunction; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isString", function() { return isString; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNumber", function() { return isNumber; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isDate", function() { return isDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isRegExp", function() { return isRegExp; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isError", function() { return isError; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isSymbol", function() { return isSymbol; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isMap", function() { return isMap; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isWeakMap", function() { return isWeakMap; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isSet", function() { return isSet; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isWeakSet", function() { return isWeakSet; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFinite", function() { return isFinite; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNaN", function() { return isNaN; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isBoolean", function() { return isBoolean; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNull", function() { return isNull; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isUndefined", function() { return isUndefined; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "has", function() { return has; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "identity", function() { return identity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "constant", function() { return constant; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "noop", function() { return noop; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "property", function() { return property; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "propertyOf", function() { return propertyOf; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "matcher", function() { return matcher; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "matches", function() { return matcher; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "times", function() { return times; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "random", function() { return random; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "now", function() { return now; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "escape", function() { return escape; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "unescape", function() { return unescape; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "result", function() { return result; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "uniqueId", function() { return uniqueId; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "templateSettings", function() { return templateSettings; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "template", function() { return template; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "chain", function() { return chain; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mixin", function() { return mixin; });
//     Underscore.js 1.10.2
//     https://underscorejs.org
//     (c) 2009-2020 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
//     Underscore may be freely distributed under the MIT license.

// Baseline setup
// --------------

// Establish the root object, `window` (`self`) in the browser, `global`
// on the server, or `this` in some virtual machines. We use `self`
// instead of `window` for `WebWorker` support.
var root = typeof self == 'object' && self.self === self && self ||
          typeof global == 'object' && global.global === global && global ||
          Function('return this')() ||
          {};

// Save bytes in the minified (but not gzipped) version:
var ArrayProto = Array.prototype, ObjProto = Object.prototype;
var SymbolProto = typeof Symbol !== 'undefined' ? Symbol.prototype : null;

// Create quick reference variables for speed access to core prototypes.
var push = ArrayProto.push,
    slice = ArrayProto.slice,
    toString = ObjProto.toString,
    hasOwnProperty = ObjProto.hasOwnProperty;

// All **ECMAScript 5** native function implementations that we hope to use
// are declared here.
var nativeIsArray = Array.isArray,
    nativeKeys = Object.keys,
    nativeCreate = Object.create;

// Create references to these builtin functions because we override them.
var _isNaN = root.isNaN,
    _isFinite = root.isFinite;

// Naked function reference for surrogate-prototype-swapping.
var Ctor = function(){};

// The Underscore object. All exported functions below are added to it in the
// modules/index-all.js using the mixin function.
function _(obj) {
  if (obj instanceof _) return obj;
  if (!(this instanceof _)) return new _(obj);
  this._wrapped = obj;
}

// Current version.
var VERSION = _.VERSION = '1.10.2';

// Internal function that returns an efficient (for current engines) version
// of the passed-in callback, to be repeatedly applied in other Underscore
// functions.
function optimizeCb(func, context, argCount) {
  if (context === void 0) return func;
  switch (argCount == null ? 3 : argCount) {
    case 1: return function(value) {
      return func.call(context, value);
    };
    // The 2-argument case is omitted because we’re not using it.
    case 3: return function(value, index, collection) {
      return func.call(context, value, index, collection);
    };
    case 4: return function(accumulator, value, index, collection) {
      return func.call(context, accumulator, value, index, collection);
    };
  }
  return function() {
    return func.apply(context, arguments);
  };
}

// An internal function to generate callbacks that can be applied to each
// element in a collection, returning the desired result — either `identity`,
// an arbitrary callback, a property matcher, or a property accessor.
function baseIteratee(value, context, argCount) {
  if (value == null) return identity;
  if (isFunction(value)) return optimizeCb(value, context, argCount);
  if (isObject(value) && !isArray(value)) return matcher(value);
  return property(value);
}

// External wrapper for our callback generator. Users may customize
// `_.iteratee` if they want additional predicate/iteratee shorthand styles.
// This abstraction hides the internal-only argCount argument.
_.iteratee = iteratee;
function iteratee(value, context) {
  return baseIteratee(value, context, Infinity);
}

// The function we actually call internally. It invokes _.iteratee if
// overridden, otherwise baseIteratee.
function cb(value, context, argCount) {
  if (_.iteratee !== iteratee) return _.iteratee(value, context);
  return baseIteratee(value, context, argCount);
}

// Some functions take a variable number of arguments, or a few expected
// arguments at the beginning and then a variable number of values to operate
// on. This helper accumulates all remaining arguments past the function’s
// argument length (or an explicit `startIndex`), into an array that becomes
// the last argument. Similar to ES6’s "rest parameter".
function restArguments(func, startIndex) {
  startIndex = startIndex == null ? func.length - 1 : +startIndex;
  return function() {
    var length = Math.max(arguments.length - startIndex, 0),
        rest = Array(length),
        index = 0;
    for (; index < length; index++) {
      rest[index] = arguments[index + startIndex];
    }
    switch (startIndex) {
      case 0: return func.call(this, rest);
      case 1: return func.call(this, arguments[0], rest);
      case 2: return func.call(this, arguments[0], arguments[1], rest);
    }
    var args = Array(startIndex + 1);
    for (index = 0; index < startIndex; index++) {
      args[index] = arguments[index];
    }
    args[startIndex] = rest;
    return func.apply(this, args);
  };
}

// An internal function for creating a new object that inherits from another.
function baseCreate(prototype) {
  if (!isObject(prototype)) return {};
  if (nativeCreate) return nativeCreate(prototype);
  Ctor.prototype = prototype;
  var result = new Ctor;
  Ctor.prototype = null;
  return result;
}

function shallowProperty(key) {
  return function(obj) {
    return obj == null ? void 0 : obj[key];
  };
}

function _has(obj, path) {
  return obj != null && hasOwnProperty.call(obj, path);
}

function deepGet(obj, path) {
  var length = path.length;
  for (var i = 0; i < length; i++) {
    if (obj == null) return void 0;
    obj = obj[path[i]];
  }
  return length ? obj : void 0;
}

// Helper for collection methods to determine whether a collection
// should be iterated as an array or as an object.
// Related: https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength
// Avoids a very nasty iOS 8 JIT bug on ARM-64. #2094
var MAX_ARRAY_INDEX = Math.pow(2, 53) - 1;
var getLength = shallowProperty('length');
function isArrayLike(collection) {
  var length = getLength(collection);
  return typeof length == 'number' && length >= 0 && length <= MAX_ARRAY_INDEX;
}

// Collection Functions
// --------------------

// The cornerstone, an `each` implementation, aka `forEach`.
// Handles raw objects in addition to array-likes. Treats all
// sparse array-likes as if they were dense.
function each(obj, iteratee, context) {
  iteratee = optimizeCb(iteratee, context);
  var i, length;
  if (isArrayLike(obj)) {
    for (i = 0, length = obj.length; i < length; i++) {
      iteratee(obj[i], i, obj);
    }
  } else {
    var _keys = keys(obj);
    for (i = 0, length = _keys.length; i < length; i++) {
      iteratee(obj[_keys[i]], _keys[i], obj);
    }
  }
  return obj;
}


// Return the results of applying the iteratee to each element.
function map(obj, iteratee, context) {
  iteratee = cb(iteratee, context);
  var _keys = !isArrayLike(obj) && keys(obj),
      length = (_keys || obj).length,
      results = Array(length);
  for (var index = 0; index < length; index++) {
    var currentKey = _keys ? _keys[index] : index;
    results[index] = iteratee(obj[currentKey], currentKey, obj);
  }
  return results;
}


// Create a reducing function iterating left or right.
function createReduce(dir) {
  // Wrap code that reassigns argument variables in a separate function than
  // the one that accesses `arguments.length` to avoid a perf hit. (#1991)
  var reducer = function(obj, iteratee, memo, initial) {
    var _keys = !isArrayLike(obj) && keys(obj),
        length = (_keys || obj).length,
        index = dir > 0 ? 0 : length - 1;
    if (!initial) {
      memo = obj[_keys ? _keys[index] : index];
      index += dir;
    }
    for (; index >= 0 && index < length; index += dir) {
      var currentKey = _keys ? _keys[index] : index;
      memo = iteratee(memo, obj[currentKey], currentKey, obj);
    }
    return memo;
  };

  return function(obj, iteratee, memo, context) {
    var initial = arguments.length >= 3;
    return reducer(obj, optimizeCb(iteratee, context, 4), memo, initial);
  };
}

// **Reduce** builds up a single result from a list of values, aka `inject`,
// or `foldl`.
var reduce = createReduce(1);


// The right-associative version of reduce, also known as `foldr`.
var reduceRight = createReduce(-1);


// Return the first value which passes a truth test.
function find(obj, predicate, context) {
  var keyFinder = isArrayLike(obj) ? findIndex : findKey;
  var key = keyFinder(obj, predicate, context);
  if (key !== void 0 && key !== -1) return obj[key];
}


// Return all the elements that pass a truth test.
function filter(obj, predicate, context) {
  var results = [];
  predicate = cb(predicate, context);
  each(obj, function(value, index, list) {
    if (predicate(value, index, list)) results.push(value);
  });
  return results;
}


// Return all the elements for which a truth test fails.
function reject(obj, predicate, context) {
  return filter(obj, negate(cb(predicate)), context);
}

// Determine whether all of the elements match a truth test.
function every(obj, predicate, context) {
  predicate = cb(predicate, context);
  var _keys = !isArrayLike(obj) && keys(obj),
      length = (_keys || obj).length;
  for (var index = 0; index < length; index++) {
    var currentKey = _keys ? _keys[index] : index;
    if (!predicate(obj[currentKey], currentKey, obj)) return false;
  }
  return true;
}


// Determine if at least one element in the object matches a truth test.
function some(obj, predicate, context) {
  predicate = cb(predicate, context);
  var _keys = !isArrayLike(obj) && keys(obj),
      length = (_keys || obj).length;
  for (var index = 0; index < length; index++) {
    var currentKey = _keys ? _keys[index] : index;
    if (predicate(obj[currentKey], currentKey, obj)) return true;
  }
  return false;
}


// Determine if the array or object contains a given item (using `===`).
function contains(obj, item, fromIndex, guard) {
  if (!isArrayLike(obj)) obj = values(obj);
  if (typeof fromIndex != 'number' || guard) fromIndex = 0;
  return indexOf(obj, item, fromIndex) >= 0;
}


// Invoke a method (with arguments) on every item in a collection.
var invoke = restArguments(function(obj, path, args) {
  var contextPath, func;
  if (isFunction(path)) {
    func = path;
  } else if (isArray(path)) {
    contextPath = path.slice(0, -1);
    path = path[path.length - 1];
  }
  return map(obj, function(context) {
    var method = func;
    if (!method) {
      if (contextPath && contextPath.length) {
        context = deepGet(context, contextPath);
      }
      if (context == null) return void 0;
      method = context[path];
    }
    return method == null ? method : method.apply(context, args);
  });
});

// Convenience version of a common use case of `map`: fetching a property.
function pluck(obj, key) {
  return map(obj, property(key));
}

// Convenience version of a common use case of `filter`: selecting only objects
// containing specific `key:value` pairs.
function where(obj, attrs) {
  return filter(obj, matcher(attrs));
}

// Convenience version of a common use case of `find`: getting the first object
// containing specific `key:value` pairs.
function findWhere(obj, attrs) {
  return find(obj, matcher(attrs));
}

// Return the maximum element (or element-based computation).
function max(obj, iteratee, context) {
  var result = -Infinity, lastComputed = -Infinity,
      value, computed;
  if (iteratee == null || typeof iteratee == 'number' && typeof obj[0] != 'object' && obj != null) {
    obj = isArrayLike(obj) ? obj : values(obj);
    for (var i = 0, length = obj.length; i < length; i++) {
      value = obj[i];
      if (value != null && value > result) {
        result = value;
      }
    }
  } else {
    iteratee = cb(iteratee, context);
    each(obj, function(v, index, list) {
      computed = iteratee(v, index, list);
      if (computed > lastComputed || computed === -Infinity && result === -Infinity) {
        result = v;
        lastComputed = computed;
      }
    });
  }
  return result;
}

// Return the minimum element (or element-based computation).
function min(obj, iteratee, context) {
  var result = Infinity, lastComputed = Infinity,
      value, computed;
  if (iteratee == null || typeof iteratee == 'number' && typeof obj[0] != 'object' && obj != null) {
    obj = isArrayLike(obj) ? obj : values(obj);
    for (var i = 0, length = obj.length; i < length; i++) {
      value = obj[i];
      if (value != null && value < result) {
        result = value;
      }
    }
  } else {
    iteratee = cb(iteratee, context);
    each(obj, function(v, index, list) {
      computed = iteratee(v, index, list);
      if (computed < lastComputed || computed === Infinity && result === Infinity) {
        result = v;
        lastComputed = computed;
      }
    });
  }
  return result;
}

// Shuffle a collection.
function shuffle(obj) {
  return sample(obj, Infinity);
}

// Sample **n** random values from a collection using the modern version of the
// [Fisher-Yates shuffle](https://en.wikipedia.org/wiki/Fisher–Yates_shuffle).
// If **n** is not specified, returns a single random element.
// The internal `guard` argument allows it to work with `map`.
function sample(obj, n, guard) {
  if (n == null || guard) {
    if (!isArrayLike(obj)) obj = values(obj);
    return obj[random(obj.length - 1)];
  }
  var sample = isArrayLike(obj) ? clone(obj) : values(obj);
  var length = getLength(sample);
  n = Math.max(Math.min(n, length), 0);
  var last = length - 1;
  for (var index = 0; index < n; index++) {
    var rand = random(index, last);
    var temp = sample[index];
    sample[index] = sample[rand];
    sample[rand] = temp;
  }
  return sample.slice(0, n);
}

// Sort the object's values by a criterion produced by an iteratee.
function sortBy(obj, iteratee, context) {
  var index = 0;
  iteratee = cb(iteratee, context);
  return pluck(map(obj, function(value, key, list) {
    return {
      value: value,
      index: index++,
      criteria: iteratee(value, key, list)
    };
  }).sort(function(left, right) {
    var a = left.criteria;
    var b = right.criteria;
    if (a !== b) {
      if (a > b || a === void 0) return 1;
      if (a < b || b === void 0) return -1;
    }
    return left.index - right.index;
  }), 'value');
}

// An internal function used for aggregate "group by" operations.
function group(behavior, partition) {
  return function(obj, iteratee, context) {
    var result = partition ? [[], []] : {};
    iteratee = cb(iteratee, context);
    each(obj, function(value, index) {
      var key = iteratee(value, index, obj);
      behavior(result, value, key);
    });
    return result;
  };
}

// Groups the object's values by a criterion. Pass either a string attribute
// to group by, or a function that returns the criterion.
var groupBy = group(function(result, value, key) {
  if (_has(result, key)) result[key].push(value); else result[key] = [value];
});

// Indexes the object's values by a criterion, similar to `groupBy`, but for
// when you know that your index values will be unique.
var indexBy = group(function(result, value, key) {
  result[key] = value;
});

// Counts instances of an object that group by a certain criterion. Pass
// either a string attribute to count by, or a function that returns the
// criterion.
var countBy = group(function(result, value, key) {
  if (_has(result, key)) result[key]++; else result[key] = 1;
});

var reStrSymbol = /[^\ud800-\udfff]|[\ud800-\udbff][\udc00-\udfff]|[\ud800-\udfff]/g;
// Safely create a real, live array from anything iterable.
function toArray(obj) {
  if (!obj) return [];
  if (isArray(obj)) return slice.call(obj);
  if (isString(obj)) {
    // Keep surrogate pair characters together
    return obj.match(reStrSymbol);
  }
  if (isArrayLike(obj)) return map(obj, identity);
  return values(obj);
}

// Return the number of elements in an object.
function size(obj) {
  if (obj == null) return 0;
  return isArrayLike(obj) ? obj.length : keys(obj).length;
}

// Split a collection into two arrays: one whose elements all satisfy the given
// predicate, and one whose elements all do not satisfy the predicate.
var partition = group(function(result, value, pass) {
  result[pass ? 0 : 1].push(value);
}, true);

// Array Functions
// ---------------

// Get the first element of an array. Passing **n** will return the first N
// values in the array. The **guard** check allows it to work with `map`.
function first(array, n, guard) {
  if (array == null || array.length < 1) return n == null ? void 0 : [];
  if (n == null || guard) return array[0];
  return initial(array, array.length - n);
}


// Returns everything but the last entry of the array. Especially useful on
// the arguments object. Passing **n** will return all the values in
// the array, excluding the last N.
function initial(array, n, guard) {
  return slice.call(array, 0, Math.max(0, array.length - (n == null || guard ? 1 : n)));
}

// Get the last element of an array. Passing **n** will return the last N
// values in the array.
function last(array, n, guard) {
  if (array == null || array.length < 1) return n == null ? void 0 : [];
  if (n == null || guard) return array[array.length - 1];
  return rest(array, Math.max(0, array.length - n));
}

// Returns everything but the first entry of the array. Especially useful on
// the arguments object. Passing an **n** will return the rest N values in the
// array.
function rest(array, n, guard) {
  return slice.call(array, n == null || guard ? 1 : n);
}


// Trim out all falsy values from an array.
function compact(array) {
  return filter(array, Boolean);
}

// Internal implementation of a recursive `flatten` function.
function _flatten(input, shallow, strict, output) {
  output = output || [];
  var idx = output.length;
  for (var i = 0, length = getLength(input); i < length; i++) {
    var value = input[i];
    if (isArrayLike(value) && (isArray(value) || isArguments(value))) {
      // Flatten current level of array or arguments object.
      if (shallow) {
        var j = 0, len = value.length;
        while (j < len) output[idx++] = value[j++];
      } else {
        _flatten(value, shallow, strict, output);
        idx = output.length;
      }
    } else if (!strict) {
      output[idx++] = value;
    }
  }
  return output;
}

// Flatten out an array, either recursively (by default), or just one level.
function flatten(array, shallow) {
  return _flatten(array, shallow, false);
}

// Return a version of the array that does not contain the specified value(s).
var without = restArguments(function(array, otherArrays) {
  return difference(array, otherArrays);
});

// Produce a duplicate-free version of the array. If the array has already
// been sorted, you have the option of using a faster algorithm.
// The faster algorithm will not work with an iteratee if the iteratee
// is not a one-to-one function, so providing an iteratee will disable
// the faster algorithm.
function uniq(array, isSorted, iteratee, context) {
  if (!isBoolean(isSorted)) {
    context = iteratee;
    iteratee = isSorted;
    isSorted = false;
  }
  if (iteratee != null) iteratee = cb(iteratee, context);
  var result = [];
  var seen = [];
  for (var i = 0, length = getLength(array); i < length; i++) {
    var value = array[i],
        computed = iteratee ? iteratee(value, i, array) : value;
    if (isSorted && !iteratee) {
      if (!i || seen !== computed) result.push(value);
      seen = computed;
    } else if (iteratee) {
      if (!contains(seen, computed)) {
        seen.push(computed);
        result.push(value);
      }
    } else if (!contains(result, value)) {
      result.push(value);
    }
  }
  return result;
}


// Produce an array that contains the union: each distinct element from all of
// the passed-in arrays.
var union = restArguments(function(arrays) {
  return uniq(_flatten(arrays, true, true));
});

// Produce an array that contains every item shared between all the
// passed-in arrays.
function intersection(array) {
  var result = [];
  var argsLength = arguments.length;
  for (var i = 0, length = getLength(array); i < length; i++) {
    var item = array[i];
    if (contains(result, item)) continue;
    var j;
    for (j = 1; j < argsLength; j++) {
      if (!contains(arguments[j], item)) break;
    }
    if (j === argsLength) result.push(item);
  }
  return result;
}

// Take the difference between one array and a number of other arrays.
// Only the elements present in just the first array will remain.
var difference = restArguments(function(array, rest) {
  rest = _flatten(rest, true, true);
  return filter(array, function(value){
    return !contains(rest, value);
  });
});

// Complement of zip. Unzip accepts an array of arrays and groups
// each array's elements on shared indices.
function unzip(array) {
  var length = array && max(array, getLength).length || 0;
  var result = Array(length);

  for (var index = 0; index < length; index++) {
    result[index] = pluck(array, index);
  }
  return result;
}

// Zip together multiple lists into a single array -- elements that share
// an index go together.
var zip = restArguments(unzip);

// Converts lists into objects. Pass either a single array of `[key, value]`
// pairs, or two parallel arrays of the same length -- one of keys, and one of
// the corresponding values. Passing by pairs is the reverse of pairs.
function object(list, values) {
  var result = {};
  for (var i = 0, length = getLength(list); i < length; i++) {
    if (values) {
      result[list[i]] = values[i];
    } else {
      result[list[i][0]] = list[i][1];
    }
  }
  return result;
}

// Generator function to create the findIndex and findLastIndex functions.
function createPredicateIndexFinder(dir) {
  return function(array, predicate, context) {
    predicate = cb(predicate, context);
    var length = getLength(array);
    var index = dir > 0 ? 0 : length - 1;
    for (; index >= 0 && index < length; index += dir) {
      if (predicate(array[index], index, array)) return index;
    }
    return -1;
  };
}

// Returns the first index on an array-like that passes a predicate test.
var findIndex = createPredicateIndexFinder(1);
var findLastIndex = createPredicateIndexFinder(-1);

// Use a comparator function to figure out the smallest index at which
// an object should be inserted so as to maintain order. Uses binary search.
function sortedIndex(array, obj, iteratee, context) {
  iteratee = cb(iteratee, context, 1);
  var value = iteratee(obj);
  var low = 0, high = getLength(array);
  while (low < high) {
    var mid = Math.floor((low + high) / 2);
    if (iteratee(array[mid]) < value) low = mid + 1; else high = mid;
  }
  return low;
}

// Generator function to create the indexOf and lastIndexOf functions.
function createIndexFinder(dir, predicateFind, sortedIndex) {
  return function(array, item, idx) {
    var i = 0, length = getLength(array);
    if (typeof idx == 'number') {
      if (dir > 0) {
        i = idx >= 0 ? idx : Math.max(idx + length, i);
      } else {
        length = idx >= 0 ? Math.min(idx + 1, length) : idx + length + 1;
      }
    } else if (sortedIndex && idx && length) {
      idx = sortedIndex(array, item);
      return array[idx] === item ? idx : -1;
    }
    if (item !== item) {
      idx = predicateFind(slice.call(array, i, length), isNaN);
      return idx >= 0 ? idx + i : -1;
    }
    for (idx = dir > 0 ? i : length - 1; idx >= 0 && idx < length; idx += dir) {
      if (array[idx] === item) return idx;
    }
    return -1;
  };
}

// Return the position of the first occurrence of an item in an array,
// or -1 if the item is not included in the array.
// If the array is large and already in sort order, pass `true`
// for **isSorted** to use binary search.
var indexOf = createIndexFinder(1, findIndex, sortedIndex);
var lastIndexOf = createIndexFinder(-1, findLastIndex);

// Generate an integer Array containing an arithmetic progression. A port of
// the native Python `range()` function. See
// [the Python documentation](https://docs.python.org/library/functions.html#range).
function range(start, stop, step) {
  if (stop == null) {
    stop = start || 0;
    start = 0;
  }
  if (!step) {
    step = stop < start ? -1 : 1;
  }

  var length = Math.max(Math.ceil((stop - start) / step), 0);
  var range = Array(length);

  for (var idx = 0; idx < length; idx++, start += step) {
    range[idx] = start;
  }

  return range;
}

// Chunk a single array into multiple arrays, each containing `count` or fewer
// items.
function chunk(array, count) {
  if (count == null || count < 1) return [];
  var result = [];
  var i = 0, length = array.length;
  while (i < length) {
    result.push(slice.call(array, i, i += count));
  }
  return result;
}

// Function (ahem) Functions
// ------------------

// Determines whether to execute a function as a constructor
// or a normal function with the provided arguments.
function executeBound(sourceFunc, boundFunc, context, callingContext, args) {
  if (!(callingContext instanceof boundFunc)) return sourceFunc.apply(context, args);
  var self = baseCreate(sourceFunc.prototype);
  var result = sourceFunc.apply(self, args);
  if (isObject(result)) return result;
  return self;
}

// Create a function bound to a given object (assigning `this`, and arguments,
// optionally). Delegates to **ECMAScript 5**'s native `Function.bind` if
// available.
var bind = restArguments(function(func, context, args) {
  if (!isFunction(func)) throw new TypeError('Bind must be called on a function');
  var bound = restArguments(function(callArgs) {
    return executeBound(func, bound, context, this, args.concat(callArgs));
  });
  return bound;
});

// Partially apply a function by creating a version that has had some of its
// arguments pre-filled, without changing its dynamic `this` context. _ acts
// as a placeholder by default, allowing any combination of arguments to be
// pre-filled. Set `partial.placeholder` for a custom placeholder argument.
var partial = restArguments(function(func, boundArgs) {
  var placeholder = partial.placeholder;
  var bound = function() {
    var position = 0, length = boundArgs.length;
    var args = Array(length);
    for (var i = 0; i < length; i++) {
      args[i] = boundArgs[i] === placeholder ? arguments[position++] : boundArgs[i];
    }
    while (position < arguments.length) args.push(arguments[position++]);
    return executeBound(func, bound, this, this, args);
  };
  return bound;
});

partial.placeholder = _;

// Bind a number of an object's methods to that object. Remaining arguments
// are the method names to be bound. Useful for ensuring that all callbacks
// defined on an object belong to it.
var bindAll = restArguments(function(obj, _keys) {
  _keys = _flatten(_keys, false, false);
  var index = _keys.length;
  if (index < 1) throw new Error('bindAll must be passed function names');
  while (index--) {
    var key = _keys[index];
    obj[key] = bind(obj[key], obj);
  }
});

// Memoize an expensive function by storing its results.
function memoize(func, hasher) {
  var memoize = function(key) {
    var cache = memoize.cache;
    var address = '' + (hasher ? hasher.apply(this, arguments) : key);
    if (!_has(cache, address)) cache[address] = func.apply(this, arguments);
    return cache[address];
  };
  memoize.cache = {};
  return memoize;
}

// Delays a function for the given number of milliseconds, and then calls
// it with the arguments supplied.
var delay = restArguments(function(func, wait, args) {
  return setTimeout(function() {
    return func.apply(null, args);
  }, wait);
});

// Defers a function, scheduling it to run after the current call stack has
// cleared.
var defer = partial(delay, _, 1);

// Returns a function, that, when invoked, will only be triggered at most once
// during a given window of time. Normally, the throttled function will run
// as much as it can, without ever going more than once per `wait` duration;
// but if you'd like to disable the execution on the leading edge, pass
// `{leading: false}`. To disable execution on the trailing edge, ditto.
function throttle(func, wait, options) {
  var timeout, context, args, result;
  var previous = 0;
  if (!options) options = {};

  var later = function() {
    previous = options.leading === false ? 0 : now();
    timeout = null;
    result = func.apply(context, args);
    if (!timeout) context = args = null;
  };

  var throttled = function() {
    var _now = now();
    if (!previous && options.leading === false) previous = _now;
    var remaining = wait - (_now - previous);
    context = this;
    args = arguments;
    if (remaining <= 0 || remaining > wait) {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }
      previous = _now;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    } else if (!timeout && options.trailing !== false) {
      timeout = setTimeout(later, remaining);
    }
    return result;
  };

  throttled.cancel = function() {
    clearTimeout(timeout);
    previous = 0;
    timeout = context = args = null;
  };

  return throttled;
}

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.
function debounce(func, wait, immediate) {
  var timeout, result;

  var later = function(context, args) {
    timeout = null;
    if (args) result = func.apply(context, args);
  };

  var debounced = restArguments(function(args) {
    if (timeout) clearTimeout(timeout);
    if (immediate) {
      var callNow = !timeout;
      timeout = setTimeout(later, wait);
      if (callNow) result = func.apply(this, args);
    } else {
      timeout = delay(later, wait, this, args);
    }

    return result;
  });

  debounced.cancel = function() {
    clearTimeout(timeout);
    timeout = null;
  };

  return debounced;
}

// Returns the first function passed as an argument to the second,
// allowing you to adjust arguments, run code before and after, and
// conditionally execute the original function.
function wrap(func, wrapper) {
  return partial(wrapper, func);
}

// Returns a negated version of the passed-in predicate.
function negate(predicate) {
  return function() {
    return !predicate.apply(this, arguments);
  };
}

// Returns a function that is the composition of a list of functions, each
// consuming the return value of the function that follows.
function compose() {
  var args = arguments;
  var start = args.length - 1;
  return function() {
    var i = start;
    var result = args[start].apply(this, arguments);
    while (i--) result = args[i].call(this, result);
    return result;
  };
}

// Returns a function that will only be executed on and after the Nth call.
function after(times, func) {
  return function() {
    if (--times < 1) {
      return func.apply(this, arguments);
    }
  };
}

// Returns a function that will only be executed up to (but not including) the Nth call.
function before(times, func) {
  var memo;
  return function() {
    if (--times > 0) {
      memo = func.apply(this, arguments);
    }
    if (times <= 1) func = null;
    return memo;
  };
}

// Returns a function that will be executed at most one time, no matter how
// often you call it. Useful for lazy initialization.
var once = partial(before, 2);

// Object Functions
// ----------------

// Keys in IE < 9 that won't be iterated by `for key in ...` and thus missed.
var hasEnumBug = !{toString: null}.propertyIsEnumerable('toString');
var nonEnumerableProps = ['valueOf', 'isPrototypeOf', 'toString',
  'propertyIsEnumerable', 'hasOwnProperty', 'toLocaleString'];

function collectNonEnumProps(obj, _keys) {
  var nonEnumIdx = nonEnumerableProps.length;
  var constructor = obj.constructor;
  var proto = isFunction(constructor) && constructor.prototype || ObjProto;

  // Constructor is a special case.
  var prop = 'constructor';
  if (_has(obj, prop) && !contains(_keys, prop)) _keys.push(prop);

  while (nonEnumIdx--) {
    prop = nonEnumerableProps[nonEnumIdx];
    if (prop in obj && obj[prop] !== proto[prop] && !contains(_keys, prop)) {
      _keys.push(prop);
    }
  }
}

// Retrieve the names of an object's own properties.
// Delegates to **ECMAScript 5**'s native `Object.keys`.
function keys(obj) {
  if (!isObject(obj)) return [];
  if (nativeKeys) return nativeKeys(obj);
  var _keys = [];
  for (var key in obj) if (_has(obj, key)) _keys.push(key);
  // Ahem, IE < 9.
  if (hasEnumBug) collectNonEnumProps(obj, _keys);
  return _keys;
}

// Retrieve all the property names of an object.
function allKeys(obj) {
  if (!isObject(obj)) return [];
  var _keys = [];
  for (var key in obj) _keys.push(key);
  // Ahem, IE < 9.
  if (hasEnumBug) collectNonEnumProps(obj, _keys);
  return _keys;
}

// Retrieve the values of an object's properties.
function values(obj) {
  var _keys = keys(obj);
  var length = _keys.length;
  var values = Array(length);
  for (var i = 0; i < length; i++) {
    values[i] = obj[_keys[i]];
  }
  return values;
}

// Returns the results of applying the iteratee to each element of the object.
// In contrast to map it returns an object.
function mapObject(obj, iteratee, context) {
  iteratee = cb(iteratee, context);
  var _keys = keys(obj),
      length = _keys.length,
      results = {};
  for (var index = 0; index < length; index++) {
    var currentKey = _keys[index];
    results[currentKey] = iteratee(obj[currentKey], currentKey, obj);
  }
  return results;
}

// Convert an object into a list of `[key, value]` pairs.
// The opposite of object.
function pairs(obj) {
  var _keys = keys(obj);
  var length = _keys.length;
  var pairs = Array(length);
  for (var i = 0; i < length; i++) {
    pairs[i] = [_keys[i], obj[_keys[i]]];
  }
  return pairs;
}

// Invert the keys and values of an object. The values must be serializable.
function invert(obj) {
  var result = {};
  var _keys = keys(obj);
  for (var i = 0, length = _keys.length; i < length; i++) {
    result[obj[_keys[i]]] = _keys[i];
  }
  return result;
}

// Return a sorted list of the function names available on the object.
function functions(obj) {
  var names = [];
  for (var key in obj) {
    if (isFunction(obj[key])) names.push(key);
  }
  return names.sort();
}


// An internal function for creating assigner functions.
function createAssigner(keysFunc, defaults) {
  return function(obj) {
    var length = arguments.length;
    if (defaults) obj = Object(obj);
    if (length < 2 || obj == null) return obj;
    for (var index = 1; index < length; index++) {
      var source = arguments[index],
          _keys = keysFunc(source),
          l = _keys.length;
      for (var i = 0; i < l; i++) {
        var key = _keys[i];
        if (!defaults || obj[key] === void 0) obj[key] = source[key];
      }
    }
    return obj;
  };
}

// Extend a given object with all the properties in passed-in object(s).
var extend = createAssigner(allKeys);

// Assigns a given object with all the own properties in the passed-in object(s).
// (https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Object/assign)
var extendOwn = createAssigner(keys);


// Returns the first key on an object that passes a predicate test.
function findKey(obj, predicate, context) {
  predicate = cb(predicate, context);
  var _keys = keys(obj), key;
  for (var i = 0, length = _keys.length; i < length; i++) {
    key = _keys[i];
    if (predicate(obj[key], key, obj)) return key;
  }
}

// Internal pick helper function to determine if `obj` has key `key`.
function keyInObj(value, key, obj) {
  return key in obj;
}

// Return a copy of the object only containing the whitelisted properties.
var pick = restArguments(function(obj, _keys) {
  var result = {}, iteratee = _keys[0];
  if (obj == null) return result;
  if (isFunction(iteratee)) {
    if (_keys.length > 1) iteratee = optimizeCb(iteratee, _keys[1]);
    _keys = allKeys(obj);
  } else {
    iteratee = keyInObj;
    _keys = _flatten(_keys, false, false);
    obj = Object(obj);
  }
  for (var i = 0, length = _keys.length; i < length; i++) {
    var key = _keys[i];
    var value = obj[key];
    if (iteratee(value, key, obj)) result[key] = value;
  }
  return result;
});

// Return a copy of the object without the blacklisted properties.
var omit = restArguments(function(obj, _keys) {
  var iteratee = _keys[0], context;
  if (isFunction(iteratee)) {
    iteratee = negate(iteratee);
    if (_keys.length > 1) context = _keys[1];
  } else {
    _keys = map(_flatten(_keys, false, false), String);
    iteratee = function(value, key) {
      return !contains(_keys, key);
    };
  }
  return pick(obj, iteratee, context);
});

// Fill in a given object with default properties.
var defaults = createAssigner(allKeys, true);

// Creates an object that inherits from the given prototype object.
// If additional properties are provided then they will be added to the
// created object.
function create(prototype, props) {
  var result = baseCreate(prototype);
  if (props) extendOwn(result, props);
  return result;
}

// Create a (shallow-cloned) duplicate of an object.
function clone(obj) {
  if (!isObject(obj)) return obj;
  return isArray(obj) ? obj.slice() : extend({}, obj);
}

// Invokes interceptor with the obj, and then returns obj.
// The primary purpose of this method is to "tap into" a method chain, in
// order to perform operations on intermediate results within the chain.
function tap(obj, interceptor) {
  interceptor(obj);
  return obj;
}

// Returns whether an object has a given set of `key:value` pairs.
function isMatch(object, attrs) {
  var _keys = keys(attrs), length = _keys.length;
  if (object == null) return !length;
  var obj = Object(object);
  for (var i = 0; i < length; i++) {
    var key = _keys[i];
    if (attrs[key] !== obj[key] || !(key in obj)) return false;
  }
  return true;
}


// Internal recursive comparison function for `isEqual`.
function eq(a, b, aStack, bStack) {
  // Identical objects are equal. `0 === -0`, but they aren't identical.
  // See the [Harmony `egal` proposal](https://wiki.ecmascript.org/doku.php?id=harmony:egal).
  if (a === b) return a !== 0 || 1 / a === 1 / b;
  // `null` or `undefined` only equal to itself (strict comparison).
  if (a == null || b == null) return false;
  // `NaN`s are equivalent, but non-reflexive.
  if (a !== a) return b !== b;
  // Exhaust primitive checks
  var type = typeof a;
  if (type !== 'function' && type !== 'object' && typeof b != 'object') return false;
  return deepEq(a, b, aStack, bStack);
}

// Internal recursive comparison function for `isEqual`.
function deepEq(a, b, aStack, bStack) {
  // Unwrap any wrapped objects.
  if (a instanceof _) a = a._wrapped;
  if (b instanceof _) b = b._wrapped;
  // Compare `[[Class]]` names.
  var className = toString.call(a);
  if (className !== toString.call(b)) return false;
  switch (className) {
    // Strings, numbers, regular expressions, dates, and booleans are compared by value.
    case '[object RegExp]':
    // RegExps are coerced to strings for comparison (Note: '' + /a/i === '/a/i')
    case '[object String]':
      // Primitives and their corresponding object wrappers are equivalent; thus, `"5"` is
      // equivalent to `new String("5")`.
      return '' + a === '' + b;
    case '[object Number]':
      // `NaN`s are equivalent, but non-reflexive.
      // Object(NaN) is equivalent to NaN.
      if (+a !== +a) return +b !== +b;
      // An `egal` comparison is performed for other numeric values.
      return +a === 0 ? 1 / +a === 1 / b : +a === +b;
    case '[object Date]':
    case '[object Boolean]':
      // Coerce dates and booleans to numeric primitive values. Dates are compared by their
      // millisecond representations. Note that invalid dates with millisecond representations
      // of `NaN` are not equivalent.
      return +a === +b;
    case '[object Symbol]':
      return SymbolProto.valueOf.call(a) === SymbolProto.valueOf.call(b);
  }

  var areArrays = className === '[object Array]';
  if (!areArrays) {
    if (typeof a != 'object' || typeof b != 'object') return false;

    // Objects with different constructors are not equivalent, but `Object`s or `Array`s
    // from different frames are.
    var aCtor = a.constructor, bCtor = b.constructor;
    if (aCtor !== bCtor && !(isFunction(aCtor) && aCtor instanceof aCtor &&
                             isFunction(bCtor) && bCtor instanceof bCtor)
                        && ('constructor' in a && 'constructor' in b)) {
      return false;
    }
  }
  // Assume equality for cyclic structures. The algorithm for detecting cyclic
  // structures is adapted from ES 5.1 section 15.12.3, abstract operation `JO`.

  // Initializing stack of traversed objects.
  // It's done here since we only need them for objects and arrays comparison.
  aStack = aStack || [];
  bStack = bStack || [];
  var length = aStack.length;
  while (length--) {
    // Linear search. Performance is inversely proportional to the number of
    // unique nested structures.
    if (aStack[length] === a) return bStack[length] === b;
  }

  // Add the first object to the stack of traversed objects.
  aStack.push(a);
  bStack.push(b);

  // Recursively compare objects and arrays.
  if (areArrays) {
    // Compare array lengths to determine if a deep comparison is necessary.
    length = a.length;
    if (length !== b.length) return false;
    // Deep compare the contents, ignoring non-numeric properties.
    while (length--) {
      if (!eq(a[length], b[length], aStack, bStack)) return false;
    }
  } else {
    // Deep compare objects.
    var _keys = keys(a), key;
    length = _keys.length;
    // Ensure that both objects contain the same number of properties before comparing deep equality.
    if (keys(b).length !== length) return false;
    while (length--) {
      // Deep compare each member
      key = _keys[length];
      if (!(_has(b, key) && eq(a[key], b[key], aStack, bStack))) return false;
    }
  }
  // Remove the first object from the stack of traversed objects.
  aStack.pop();
  bStack.pop();
  return true;
}

// Perform a deep comparison to check if two objects are equal.
function isEqual(a, b) {
  return eq(a, b);
}

// Is a given array, string, or object empty?
// An "empty" object has no enumerable own-properties.
function isEmpty(obj) {
  if (obj == null) return true;
  if (isArrayLike(obj) && (isArray(obj) || isString(obj) || isArguments(obj))) return obj.length === 0;
  return keys(obj).length === 0;
}

// Is a given value a DOM element?
function isElement(obj) {
  return !!(obj && obj.nodeType === 1);
}

// Internal function for creating a toString-based type tester.
function tagTester(name) {
  return function(obj) {
    return toString.call(obj) === '[object ' + name + ']';
  };
}

// Is a given value an array?
// Delegates to ECMA5's native Array.isArray
var isArray = nativeIsArray || tagTester('Array');

// Is a given variable an object?
function isObject(obj) {
  var type = typeof obj;
  return type === 'function' || type === 'object' && !!obj;
}

// Add some isType methods: isArguments, isFunction, isString, isNumber, isDate, isRegExp, isError, isMap, isWeakMap, isSet, isWeakSet.
var isArguments = tagTester('Arguments');
var isFunction = tagTester('Function');
var isString = tagTester('String');
var isNumber = tagTester('Number');
var isDate = tagTester('Date');
var isRegExp = tagTester('RegExp');
var isError = tagTester('Error');
var isSymbol = tagTester('Symbol');
var isMap = tagTester('Map');
var isWeakMap = tagTester('WeakMap');
var isSet = tagTester('Set');
var isWeakSet = tagTester('WeakSet');

// Define a fallback version of the method in browsers (ahem, IE < 9), where
// there isn't any inspectable "Arguments" type.
(function() {
  if (!isArguments(arguments)) {
    isArguments = function(obj) {
      return _has(obj, 'callee');
    };
  }
}());

// Optimize `isFunction` if appropriate. Work around some typeof bugs in old v8,
// IE 11 (#1621), Safari 8 (#1929), and PhantomJS (#2236).
var nodelist = root.document && root.document.childNodes;
if ( true && typeof Int8Array != 'object' && typeof nodelist != 'function') {
  isFunction = function(obj) {
    return typeof obj == 'function' || false;
  };
}

// Is a given object a finite number?
function isFinite(obj) {
  return !isSymbol(obj) && _isFinite(obj) && !_isNaN(parseFloat(obj));
}

// Is the given value `NaN`?
function isNaN(obj) {
  return isNumber(obj) && _isNaN(obj);
}

// Is a given value a boolean?
function isBoolean(obj) {
  return obj === true || obj === false || toString.call(obj) === '[object Boolean]';
}

// Is a given value equal to null?
function isNull(obj) {
  return obj === null;
}

// Is a given variable undefined?
function isUndefined(obj) {
  return obj === void 0;
}

// Shortcut function for checking if an object has a given property directly
// on itself (in other words, not on a prototype).
function has(obj, path) {
  if (!isArray(path)) {
    return _has(obj, path);
  }
  var length = path.length;
  for (var i = 0; i < length; i++) {
    var key = path[i];
    if (obj == null || !hasOwnProperty.call(obj, key)) {
      return false;
    }
    obj = obj[key];
  }
  return !!length;
}

// Utility Functions
// -----------------

// Keep the identity function around for default iteratees.
function identity(value) {
  return value;
}

// Predicate-generating functions. Often useful outside of Underscore.
function constant(value) {
  return function() {
    return value;
  };
}

function noop(){}

// Creates a function that, when passed an object, will traverse that object’s
// properties down the given `path`, specified as an array of keys or indexes.
function property(path) {
  if (!isArray(path)) {
    return shallowProperty(path);
  }
  return function(obj) {
    return deepGet(obj, path);
  };
}

// Generates a function for a given object that returns a given property.
function propertyOf(obj) {
  if (obj == null) {
    return function(){};
  }
  return function(path) {
    return !isArray(path) ? obj[path] : deepGet(obj, path);
  };
}

// Returns a predicate for checking whether an object has a given set of
// `key:value` pairs.
function matcher(attrs) {
  attrs = extendOwn({}, attrs);
  return function(obj) {
    return isMatch(obj, attrs);
  };
}


// Run a function **n** times.
function times(n, iteratee, context) {
  var accum = Array(Math.max(0, n));
  iteratee = optimizeCb(iteratee, context, 1);
  for (var i = 0; i < n; i++) accum[i] = iteratee(i);
  return accum;
}

// Return a random integer between min and max (inclusive).
function random(min, max) {
  if (max == null) {
    max = min;
    min = 0;
  }
  return min + Math.floor(Math.random() * (max - min + 1));
}

// A (possibly faster) way to get the current timestamp as an integer.
var now = Date.now || function() {
  return new Date().getTime();
};

// List of HTML entities for escaping.
var escapeMap = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#x27;',
  '`': '&#x60;'
};
var unescapeMap = invert(escapeMap);

// Functions for escaping and unescaping strings to/from HTML interpolation.
function createEscaper(map) {
  var escaper = function(match) {
    return map[match];
  };
  // Regexes for identifying a key that needs to be escaped.
  var source = '(?:' + keys(map).join('|') + ')';
  var testRegexp = RegExp(source);
  var replaceRegexp = RegExp(source, 'g');
  return function(string) {
    string = string == null ? '' : '' + string;
    return testRegexp.test(string) ? string.replace(replaceRegexp, escaper) : string;
  };
}
var escape = createEscaper(escapeMap);
var unescape = createEscaper(unescapeMap);

// Traverses the children of `obj` along `path`. If a child is a function, it
// is invoked with its parent as context. Returns the value of the final
// child, or `fallback` if any child is undefined.
function result(obj, path, fallback) {
  if (!isArray(path)) path = [path];
  var length = path.length;
  if (!length) {
    return isFunction(fallback) ? fallback.call(obj) : fallback;
  }
  for (var i = 0; i < length; i++) {
    var prop = obj == null ? void 0 : obj[path[i]];
    if (prop === void 0) {
      prop = fallback;
      i = length; // Ensure we don't continue iterating.
    }
    obj = isFunction(prop) ? prop.call(obj) : prop;
  }
  return obj;
}

// Generate a unique integer id (unique within the entire client session).
// Useful for temporary DOM ids.
var idCounter = 0;
function uniqueId(prefix) {
  var id = ++idCounter + '';
  return prefix ? prefix + id : id;
}

// By default, Underscore uses ERB-style template delimiters, change the
// following template settings to use alternative delimiters.
var templateSettings = _.templateSettings = {
  evaluate: /<%([\s\S]+?)%>/g,
  interpolate: /<%=([\s\S]+?)%>/g,
  escape: /<%-([\s\S]+?)%>/g
};

// When customizing `templateSettings`, if you don't want to define an
// interpolation, evaluation or escaping regex, we need one that is
// guaranteed not to match.
var noMatch = /(.)^/;

// Certain characters need to be escaped so that they can be put into a
// string literal.
var escapes = {
  "'": "'",
  '\\': '\\',
  '\r': 'r',
  '\n': 'n',
  '\u2028': 'u2028',
  '\u2029': 'u2029'
};

var escapeRegExp = /\\|'|\r|\n|\u2028|\u2029/g;

var escapeChar = function(match) {
  return '\\' + escapes[match];
};

// JavaScript micro-templating, similar to John Resig's implementation.
// Underscore templating handles arbitrary delimiters, preserves whitespace,
// and correctly escapes quotes within interpolated code.
// NB: `oldSettings` only exists for backwards compatibility.
function template(text, settings, oldSettings) {
  if (!settings && oldSettings) settings = oldSettings;
  settings = defaults({}, settings, _.templateSettings);

  // Combine delimiters into one regular expression via alternation.
  var matcher = RegExp([
    (settings.escape || noMatch).source,
    (settings.interpolate || noMatch).source,
    (settings.evaluate || noMatch).source
  ].join('|') + '|$', 'g');

  // Compile the template source, escaping string literals appropriately.
  var index = 0;
  var source = "__p+='";
  text.replace(matcher, function(match, escape, interpolate, evaluate, offset) {
    source += text.slice(index, offset).replace(escapeRegExp, escapeChar);
    index = offset + match.length;

    if (escape) {
      source += "'+\n((__t=(" + escape + "))==null?'':_.escape(__t))+\n'";
    } else if (interpolate) {
      source += "'+\n((__t=(" + interpolate + "))==null?'':__t)+\n'";
    } else if (evaluate) {
      source += "';\n" + evaluate + "\n__p+='";
    }

    // Adobe VMs need the match returned to produce the correct offset.
    return match;
  });
  source += "';\n";

  // If a variable is not specified, place data values in local scope.
  if (!settings.variable) source = 'with(obj||{}){\n' + source + '}\n';

  source = "var __t,__p='',__j=Array.prototype.join," +
    "print=function(){__p+=__j.call(arguments,'');};\n" +
    source + 'return __p;\n';

  var render;
  try {
    render = new Function(settings.variable || 'obj', '_', source);
  } catch (e) {
    e.source = source;
    throw e;
  }

  var template = function(data) {
    return render.call(this, data, _);
  };

  // Provide the compiled source as a convenience for precompilation.
  var argument = settings.variable || 'obj';
  template.source = 'function(' + argument + '){\n' + source + '}';

  return template;
}

// Add a "chain" function. Start chaining a wrapped Underscore object.
function chain(obj) {
  var instance = _(obj);
  instance._chain = true;
  return instance;
}

// OOP
// ---------------
// If Underscore is called as a function, it returns a wrapped object that
// can be used OO-style. This wrapper holds altered versions of all the
// underscore functions. Wrapped objects may be chained.

// Helper function to continue chaining intermediate results.
function chainResult(instance, obj) {
  return instance._chain ? _(obj).chain() : obj;
}

// Add your own custom functions to the Underscore object.
function mixin(obj) {
  each(functions(obj), function(name) {
    var func = _[name] = obj[name];
    _.prototype[name] = function() {
      var args = [this._wrapped];
      push.apply(args, arguments);
      return chainResult(this, func.apply(_, args));
    };
  });
  return _;
}

// Add all mutator Array functions to the wrapper.
each(['pop', 'push', 'reverse', 'shift', 'sort', 'splice', 'unshift'], function(name) {
  var method = ArrayProto[name];
  _.prototype[name] = function() {
    var obj = this._wrapped;
    method.apply(obj, arguments);
    if ((name === 'shift' || name === 'splice') && obj.length === 0) delete obj[0];
    return chainResult(this, obj);
  };
});

// Add all accessor Array functions to the wrapper.
each(['concat', 'join', 'slice'], function(name) {
  var method = ArrayProto[name];
  _.prototype[name] = function() {
    return chainResult(this, method.apply(this._wrapped, arguments));
  };
});

// Extracts the result from a wrapped and chained object.
_.prototype.value = function() {
  return this._wrapped;
};

// Provide unwrapping proxy for some methods used in engine operations
// such as arithmetic and JSON stringification.
_.prototype.valueOf = _.prototype.toJSON = _.prototype.value;

_.prototype.toString = function() {
  return String(this._wrapped);
};

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/util-deprecate/browser.js":
/*!************************************************!*\
  !*** ./node_modules/util-deprecate/browser.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {
/**
 * Module exports.
 */

module.exports = deprecate;

/**
 * Mark that a method should not be used.
 * Returns a modified function which warns once by default.
 *
 * If `localStorage.noDeprecation = true` is set, then it is a no-op.
 *
 * If `localStorage.throwDeprecation = true` is set, then deprecated functions
 * will throw an Error when invoked.
 *
 * If `localStorage.traceDeprecation = true` is set, then deprecated functions
 * will invoke `console.trace()` instead of `console.error()`.
 *
 * @param {Function} fn - the function to deprecate
 * @param {String} msg - the string to print to the console when `fn` is invoked
 * @returns {Function} a new "deprecated" version of `fn`
 * @api public
 */

function deprecate (fn, msg) {
  if (config('noDeprecation')) {
    return fn;
  }

  var warned = false;
  function deprecated() {
    if (!warned) {
      if (config('throwDeprecation')) {
        throw new Error(msg);
      } else if (config('traceDeprecation')) {
        console.trace(msg);
      } else {
        console.warn(msg);
      }
      warned = true;
    }
    return fn.apply(this, arguments);
  }

  return deprecated;
}

/**
 * Checks `localStorage` for boolean values for the given `name`.
 *
 * @param {String} name
 * @returns {Boolean}
 * @api private
 */

function config (name) {
  // accessing global.localStorage can trigger a DOMException in sandboxed iframes
  try {
    if (!global.localStorage) return false;
  } catch (_) {
    return false;
  }
  var val = global.localStorage[name];
  if (null == val) return false;
  return String(val).toLowerCase() === 'true';
}

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/bootstrap4/button.js":
/*!**********************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/bootstrap4/button.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'bs4-btn',
    'label': 'Button',
    'open': false
  };
  $blockManager.add('button', {
    category: $category,
    media: '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M10 110v80h280v-80H10zm0-10h280a10 10 0 0 1 10 10v80a10 10 0 0 1-10 10H10a10 10 0 0 1-10-10v-80a10 10 0 0 1 10-10z"></path><path d="M50 145h200v10H50z"></path></svg>',
    label: 'Button',
    content: "<button type=\"button\" class=\"btn btn-primary\">Button</button>"
  });
  $blockManager.add('button-group', {
    category: $category,
    media: '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M10 110v80h280v-80H10zm0-10h280a10 10 0 0 1 10 10v80a10 10 0 0 1-10 10H10a10 10 0 0 1-10-10v-80a10 10 0 0 1 10-10z"></path><path d="M145 100h10v100h-10zM30 145h90v10H30zM181 145h90v10h-90z"></path></svg>',
    label: 'Button',
    content: "<div class=\"btn-group\" role=\"group\" aria-label=\"Basic example\">\n            <button type=\"button\" class=\"btn btn-secondary\">Left</button>\n            <button type=\"button\" class=\"btn btn-secondary\">Middle</button>\n            <button type=\"button\" class=\"btn btn-secondary\">Right</button>\n        </div>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/bootstrap4/card.js":
/*!********************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/bootstrap4/card.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'bs4-card',
    'label': 'Card',
    'open': true
  };
  $blockManager.add('bs4-card-1', {
    category: $category,
    media: "<svg viewBox=\"0 0 300 300\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M60 60v180h180V60H60zm0-10h180a10 10 0 0 1 10 10v180a10 10 0 0 1-10 10H60a10 10 0 0 1-10-10V60a10 10 0 0 1 10-10z\"></path><path d=\"M50 93h200v10H50z\"></path></svg>",
    label: 'Card',
    content: "<div class=\"card\">\n        <img src=\"https://via.placeholder.com/500\" class=\"card-img-top\">\n        <div class=\"card-body\">\n          <h5 class=\"card-title\">Card title</h5>\n          <h6 class=\"card-subtitle text-muted mb-2\">Card subtitle</h6>\n          <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n          <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>\n        </div>\n      </div>"
  });
  $blockManager.add('bs4-card-2', {
    category: $category,
    media: "<svg viewBox=\"0 0 300 300\" xmlns=\"http://www.w3.org/2000/svg\">\n        <path d=\"M60 60v180h180V60H60zm0-10h180a10 10 0 0 1 10 10v180a10 10 0 0 1-10 10H60a10 10 0 0 1-10-10V60a10 10 0 0 1 10-10z\"></path>\n        <path d=\"M50 93h200v10H50z\" transform=\"translate(213) rotate(90)\"></path>\n        </svg>",
    label: 'Card Horizontal',
    content: "<div class=\"card\">\n        <div class=\"row no-gutters\">\n          <div class=\"col-md-4\">\n            <img src=\"https://via.placeholder.com/360x500\" class=\"card-img\">\n          </div>\n          <div class=\"col-md-8\">\n            <div class=\"card-body\">\n              <h5 class=\"card-title\">Card title</h5>\n              <p class=\"card-text\">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>\n              <p class=\"card-text\"><small class=\"text-muted\">Last updated 3 mins ago</small></p>\n            </div>\n          </div>\n        </div>\n      </div>"
  });
  $blockManager.add('bs4-card-3', {
    category: $category,
    media: "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 60.806 60.806\">\n        <g id=\"Repeat_Grid_256\" data-name=\"Repeat Grid 256\" transform=\"translate(-50 -50)\">\n          <path id=\"Path_55\" data-name=\"Path 55\" d=\"M53.04,53.04v54.726h54.726V53.04Zm0-3.04h54.726a3.04,3.04,0,0,1,3.04,3.04v54.726a3.04,3.04,0,0,1-3.04,3.04H53.04a3.04,3.04,0,0,1-3.04-3.04V53.04A3.04,3.04,0,0,1,53.04,50Z\"/>\n          <rect id=\"Rectangle_1730\" data-name=\"Rectangle 1730\" width=\"22\" height=\"4\" rx=\"2\" transform=\"translate(56 56)\"/>\n          <rect id=\"Rectangle_1731\" data-name=\"Rectangle 1731\" width=\"18\" height=\"4\" rx=\"2\" transform=\"translate(56 62)\" fill=\"#e2e6eb\"/>\n          <rect id=\"Rectangle_1732\" data-name=\"Rectangle 1732\" width=\"9\" height=\"4\" rx=\"2\" transform=\"translate(56 68)\" fill=\"#e2e6eb\"/>\n          <path id=\"Path_59\" data-name=\"Path 59\" d=\"M69.957,116l14.957,20.6H55Z\" transform=\"translate(1 -35.597)\"/>\n          <path id=\"Path_60\" data-name=\"Path 60\" d=\"M162.26,150l12.26,12.26H150Z\" transform=\"translate(-70.706 -61.26)\"/>\n        </g>\n      </svg>\n      ",
    label: 'Card Overlay',
    content: "<div class=\"card bg-dark text-white\">\n        <img src=\"https://via.placeholder.com/800x450\" class=\"card-img\">\n        <div class=\"card-img-overlay\">\n          <h5 class=\"card-title\">Card title</h5>\n          <p class=\"card-text\">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>\n          <p class=\"card-text\">Last updated 3 mins ago</p>\n        </div>\n      </div>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/bootstrap4/general.js":
/*!***********************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/bootstrap4/general.js ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'bs4-general',
    'label': 'General',
    'open': false
  };
  $blockManager.add('table', {
    category: $category,
    media: '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M10 77v147h280V77H10zm0-10h280a10 10 0 0 1 10 10v147a10 10 0 0 1-10 10H10a10 10 0 0 1-10-10V77a10 10 0 0 1 10-10z"></path><path d="M95 67h10v167H95zM195 67h10v167h-10z"></path><path d="M0 104h300v30H0zM0 164h300v30H0z"></path></svg>',
    label: 'Table',
    content: "\n        <table class=\"table\">\n            <thead>\n                <tr>\n                    <th><div>Title</div></th>\n                    <th><div>Title</div></th>\n                    <th><div>Title</div></th>\n                </tr>\n            </thead>\n            <tbody>\n                <tr>\n                    <td><div>Cell</div></td>\n                    <td><div>Cell</div></td>\n                    <td><div>Cell</div></td>\n                </tr>\n                <tr>\n                    <td><div>Cell</div></td>\n                    <td><div>Cell</div></td>\n                    <td><div>Cell</div></td>\n                </tr>\n                <tr>\n                    <td><div>Cell</div></td>\n                    <td><div>Cell</div></td>\n                    <td><div>Cell</div></td>\n                </tr>\n            </tbody>\n        </table>\n        "
  });
  $blockManager.add('alert', {
    category: $category,
    media: "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\"><path d=\"M5 3h14c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2m8 10V7h-2v6h2m0 4v-2h-2v2h2z\"></path></svg>",
    label: 'Alert',
    content: "<div class=\"alert alert-primary\" role=\"alert\">\n            A simple primary alert\u2014check it out!\n        </div>"
  });
  $blockManager.add('badge', {
    category: $category,
    media: "<svg viewBox=\"0 0 300 300\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M50 100h200a50 50 0 0 1 0 100H50a50 50 0 0 1 0-100zm38.3 82h15v-63.7H89.6l-21 15.4 7.9 10.7 11.9-9.2V182zm38.8 0h46v-13.1h-26l14.8-13.2 4.2-4c1.3-1.3 2.5-2.8 3.6-4.3A19.7 19.7 0 0 0 173 136a18 18 0 0 0-7.1-14.8 22.3 22.3 0 0 0-7.4-3.6 31.3 31.3 0 0 0-17.5.3 23.1 23.1 0 0 0-12.7 10.4 22.6 22.6 0 0 0-2.7 8.7l14.5 2a12 12 0 0 1 2.8-7 8 8 0 0 1 6.2-2.7c2.4 0 4.3.7 5.8 2.2a7.4 7.4 0 0 1 2.1 5.5c0 2-.5 3.6-1.4 5.2-1 1.6-2.2 3.1-3.8 4.6L127 169.4V182zm104.4-18.5a14.3 14.3 0 0 0-3.4-9.4 16 16 0 0 0-8.8-5.4v-.3c3.1-.8 5.7-2.4 7.7-4.9 2-2.4 3-5.4 3-9a15.7 15.7 0 0 0-7-13.5 22 22 0 0 0-7.2-3.3 31.5 31.5 0 0 0-16 0c-2.5.6-4.8 1.6-7 3a21.6 21.6 0 0 0-9 12l14 3.3a9 9 0 0 1 3.2-5.2c1.6-1.3 3.4-1.9 5.6-1.9 2.1 0 4 .6 5.6 1.8 1.6 1.2 2.4 3 2.4 5.2 0 1.6-.3 2.8-1 3.8-.6 1-1.4 1.7-2.4 2.3-1.1.6-2.3 1-3.6 1.2-1.3.2-2.7.3-4.1.3H199v11h4c1.6 0 3.1.2 4.7.5 1.5.2 3 .7 4.2 1.3a7 7 0 0 1 4 6.6c0 1.5-.2 2.8-.8 3.8-.6 1-1.3 1.8-2.2 2.5a9 9 0 0 1-3 1.4c-1 .3-2.1.4-3.2.4-2.9 0-5.3-.8-7.1-2.4-1.9-1.6-3.1-3.4-3.7-5.5l-14 3.7a21.7 21.7 0 0 0 17 16 35.7 35.7 0 0 0 17.2-.3c2.8-.8 5.4-2 7.7-3.7a18.4 18.4 0 0 0 7.7-15.3z\"></path></svg>",
    label: 'Badge',
    content: "<span class=\"badge badge-secondary\">New</span>"
  });
  $blockManager.add('link', {
    category: $category,
    media: "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\"><path d=\"M10.59 13.41c.41.39.41 1.03 0 1.42-.39.39-1.03.39-1.42 0a5.003 5.003 0 0 1 0-7.07l3.54-3.54a5.003 5.003 0 0 1 7.07 0 5.003 5.003 0 0 1 0 7.07l-1.49 1.49c.01-.82-.12-1.64-.4-2.42l.47-.48a2.982 2.982 0 0 0 0-4.24 2.982 2.982 0 0 0-4.24 0l-3.53 3.53a2.982 2.982 0 0 0 0 4.24m2.82-4.24c.39-.39 1.03-.39 1.42 0a5.003 5.003 0 0 1 0 7.07l-3.54 3.54a5.003 5.003 0 0 1-7.07 0 5.003 5.003 0 0 1 0-7.07l1.49-1.49c-.01.82.12 1.64.4 2.43l-.47.47a2.982 2.982 0 0 0 0 4.24 2.982 2.982 0 0 0 4.24 0l3.53-3.53a2.982 2.982 0 0 0 0-4.24.973.973 0 0 1 0-1.42z\"></path></svg>",
    label: 'Link',
    content: "<a href=\"#\">Link</a>"
  });
  $blockManager.add('Image', {
    category: $category,
    media: "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\"><path d=\"M8.5 13.5l2.5 3 3.5-4.5 4.5 6H5m16 1V5a2 2 0 0 0-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2z\"></path></svg>",
    label: 'Image',
    content: "<img src=\"https://via.placeholder.com/200\" class=\"img-fluid\">"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/bootstrap4/grid.js":
/*!********************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/bootstrap4/grid.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'bs4-grid',
    'label': 'Grid',
    'open': false
  };
  $blockManager.add('section-1', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/component/section.svg">',
    label: 'Section',
    content: "\n        <section>\n            <div class=\"container\"></div>\n        </section>"
  });
  $blockManager.add('column-1', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/component/1-column.svg">',
    label: '1 Column',
    content: "\n        <div class=\"row\">\n            <div class=\"col col-12\">\n                <div>COLUMN</div>\n            </div>\n        </div>"
  });
  $blockManager.add('column-2', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/component/2-column.svg">',
    label: '2 Column',
    content: "\n        <div class=\"row\">\n            <div class=\"col col-6\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-6\">\n                <div>COLUMN</div>\n            </div>\n        </div>"
  });
  $blockManager.add('column-3', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/component/3-column.svg">',
    label: '3 Column',
    content: "\n        <div class=\"row\">\n            <div class=\"col col-4\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-4\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-4\">\n                <div>COLUMN</div>\n            </div>\n        </div>"
  });
  $blockManager.add('column-4', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/component/4-column.svg">',
    label: '4 Column',
    content: "\n        <div class=\"row\">\n            <div class=\"col col-3\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-3\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-3\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-3\">\n                <div>COLUMN</div>\n            </div>\n        </div>"
  });
  $blockManager.add('column-5', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/component/5-column.svg">',
    label: '5 Column',
    content: "\n        <div class=\"row\">\n            <div class=\"col col-2\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-2\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-2\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-2\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-2\">\n                <div>COLUMN</div>\n            </div>\n            <div class=\"col col-2\">\n                <div>COLUMN</div>\n            </div>\n        </div>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/bootstrap4/text.js":
/*!********************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/bootstrap4/text.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'bs4-text',
    'label': 'Text',
    'open': false
  };
  $blockManager.add('header', {
    category: $category,
    media: '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M94.6 141.8h107.6V75.4c0-11.8-.7-19.6-2.2-23.4a15.3 15.3 0 0 0-7.2-7.3c-4.9-2.7-10-4.1-15.5-4.1H169v-6.3h99.1v6.3H260c-5.5 0-10.7 1.3-15.6 4a14.4 14.4 0 0 0-7.4 8.2c-1.3 3.7-2 11.3-2 22.6V225c0 11.7.8 19.5 2.3 23.2 1.1 2.9 3.4 5.3 7 7.4 5 2.7 10.2 4 15.7 4h8.2v6.4h-99.1v-6.3h8.2c9.4 0 16.3-2.8 20.7-8.4 2.8-3.6 4.2-12.4 4.2-26.3v-70.6H94.6V225c0 11.7.7 19.5 2.2 23.2 1.1 2.9 3.5 5.3 7.2 7.4 4.9 2.7 10 4 15.5 4h8.4v6.4H28.6v-6.3h8.2c9.6 0 16.5-2.8 20.9-8.4 2.7-3.6 4-12.4 4-26.3V75.4c0-11.8-.7-19.6-2.2-23.4a15.4 15.4 0 0 0-7-7.3c-5-2.7-10.2-4.1-15.7-4.1h-8.2v-6.3h99.3v6.3h-8.4c-5.4 0-10.6 1.3-15.5 4-3.6 1.7-6 4.5-7.4 8.2-1.3 3.7-2 11.3-2 22.6v66.4z"></path></svg>',
    label: 'Header',
    content: "<h1>Title</h1>"
  });
  $blockManager.add('blockquotes', {
    category: $category,
    media: '<svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><path d="M44.1 239.4V226a91 91 0 0 0 43.5-32.1 76.6 76.6 0 0 0 15.9-45.5c0-3.4-.6-6-1.6-7.6-.6-1-1.3-1.6-2.2-1.6-.9 0-2.3.7-4.3 2.2a35.9 35.9 0 0 1-21.5 6.1 36.2 36.2 0 0 1-27.2-12.4 43.8 43.8 0 0 1 1.7-61.5c9-9 20-13.6 33-13.6 15 0 27.9 6.1 38.9 18.4 11 12.3 16.5 28.8 16.5 49.5 0 24.2-7.4 45.9-22.3 65-14.9 19.2-38.3 34.7-70.4 46.5zm129.2 0V226a91 91 0 0 0 43.5-32.1 76.6 76.6 0 0 0 15.9-45.5c0-3.4-.6-6-1.7-7.6-.5-1-1.2-1.6-2.1-1.6-.9 0-2.3.7-4.3 2.2a35.9 35.9 0 0 1-21.5 6.1 36.2 36.2 0 0 1-27.2-12.4 43.8 43.8 0 0 1 1.7-61.5c9-9 20-13.6 33-13.6 14.9 0 27.8 6.1 38.8 18.4 11 12.3 16.6 28.8 16.6 49.5 0 24.2-7.5 45.9-22.3 65-14.9 19.2-38.3 34.7-70.4 46.5z"></path></svg>',
    label: 'Blockquotes',
    content: "<blockquote class=\"blockquote\">\n            <p class=\"mb-0\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>\n            <div class=\"blockquote-footer\">Someone famous in Source Title</div>\n        </blockquote>"
  });
  $blockManager.add('text', {
    category: $category,
    media: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18.5 4l1.16 4.35-.96.26c-.45-.87-.91-1.74-1.44-2.18C16.73 6 16.11 6 15.5 6H13v10.5c0 .5 0 1 .33 1.25.34.25 1 .25 1.67.25v1H9v-1c.67 0 1.33 0 1.67-.25.33-.25.33-.75.33-1.25V6H8.5c-.61 0-1.23 0-1.76.43-.53.44-.99 1.31-1.44 2.18l-.96-.26L5.5 4h13z"></path></svg>',
    label: 'Text',
    content: "<div>Insert your text here</div>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/init.js":
/*!*********************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/init.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _theme_header_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./theme/header.js */ "./resources/theme/bootstrap4/src/js/block/theme/header.js");
/* harmony import */ var _theme_footer_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./theme/footer.js */ "./resources/theme/bootstrap4/src/js/block/theme/footer.js");
/* harmony import */ var _theme_basic_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./theme/basic.js */ "./resources/theme/bootstrap4/src/js/block/theme/basic.js");
/* harmony import */ var _theme_card_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./theme/card.js */ "./resources/theme/bootstrap4/src/js/block/theme/card.js");
/* harmony import */ var _theme_user_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./theme/user.js */ "./resources/theme/bootstrap4/src/js/block/theme/user.js");
/* harmony import */ var _bootstrap4_grid_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./bootstrap4/grid.js */ "./resources/theme/bootstrap4/src/js/block/bootstrap4/grid.js");
/* harmony import */ var _bootstrap4_general_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./bootstrap4/general.js */ "./resources/theme/bootstrap4/src/js/block/bootstrap4/general.js");
/* harmony import */ var _bootstrap4_text_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./bootstrap4/text.js */ "./resources/theme/bootstrap4/src/js/block/bootstrap4/text.js");
/* harmony import */ var _bootstrap4_button_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./bootstrap4/button.js */ "./resources/theme/bootstrap4/src/js/block/bootstrap4/button.js");
/* harmony import */ var _bootstrap4_card_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./bootstrap4/card.js */ "./resources/theme/bootstrap4/src/js/block/bootstrap4/card.js");










/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $blockManager = $editor.BlockManager,
      $blocks = $blockManager.getAllVisible();
  /*
  themeHeader($editor);
  themeBasic($editor);
  themeCard($editor);
  themeUser($editor);
  themeFooter($editor);
  */

  Object(_bootstrap4_grid_js__WEBPACK_IMPORTED_MODULE_5__["default"])($editor);
  Object(_bootstrap4_general_js__WEBPACK_IMPORTED_MODULE_6__["default"])($editor);
  Object(_bootstrap4_text_js__WEBPACK_IMPORTED_MODULE_7__["default"])($editor);
  Object(_bootstrap4_button_js__WEBPACK_IMPORTED_MODULE_8__["default"])($editor);
  Object(_bootstrap4_card_js__WEBPACK_IMPORTED_MODULE_9__["default"])($editor);

  function remove() {
    var $block_id = [];
    $.each($blocks.models, function (index, val) {
      $block_id.push(val.id);
    });
    $.each($block_id, function (index, val) {
      $blockManager.remove(val);
    });
  }

  var $selectOption = "\n    <option value=\"bootstrap4\" selected>Bootstrap 4</option>\n    <optgroup label=\"Theme\">\n        <option value=\"theme-all\">All Theme</option>\n        <option value=\"theme-header\">Header</option>\n        <option value=\"theme-basic\">Basic</option>\n        <option value=\"theme-card\">Card</option>\n        <option value=\"theme-user\">User</option>\n        <option value=\"theme-footer\">Footer</option>\n    </optgroup>\n    ";
  $('.block-selector').append($selectOption);
  $('.block-selector').change(function () {
    var $value = $(this).val();

    switch ($value) {
      default:
      case 'all':
        remove();
        Object(_theme_header_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
        Object(_theme_basic_js__WEBPACK_IMPORTED_MODULE_2__["default"])($editor);
        Object(_theme_card_js__WEBPACK_IMPORTED_MODULE_3__["default"])($editor);
        Object(_theme_user_js__WEBPACK_IMPORTED_MODULE_4__["default"])($editor);
        Object(_theme_footer_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor);
        Object(_bootstrap4_grid_js__WEBPACK_IMPORTED_MODULE_5__["default"])($editor);
        Object(_bootstrap4_general_js__WEBPACK_IMPORTED_MODULE_6__["default"])($editor);
        Object(_bootstrap4_text_js__WEBPACK_IMPORTED_MODULE_7__["default"])($editor);
        Object(_bootstrap4_button_js__WEBPACK_IMPORTED_MODULE_8__["default"])($editor);
        Object(_bootstrap4_card_js__WEBPACK_IMPORTED_MODULE_9__["default"])($editor);
        $blockManager.render();
        break;

      case 'theme-all':
        remove();
        Object(_theme_header_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
        Object(_theme_basic_js__WEBPACK_IMPORTED_MODULE_2__["default"])($editor);
        Object(_theme_card_js__WEBPACK_IMPORTED_MODULE_3__["default"])($editor);
        Object(_theme_user_js__WEBPACK_IMPORTED_MODULE_4__["default"])($editor);
        Object(_theme_footer_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor);
        $blockManager.render();
        break;

      case 'theme-header':
        remove();
        Object(_theme_header_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
        $blockManager.render();
        break;

      case 'theme-basic':
        remove();
        Object(_theme_header_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
        $blockManager.render();
        break;

      case 'theme-card':
        remove();
        Object(_theme_card_js__WEBPACK_IMPORTED_MODULE_3__["default"])($editor);
        $blockManager.render();
        break;

      case 'theme-user':
        remove();
        Object(_theme_user_js__WEBPACK_IMPORTED_MODULE_4__["default"])($editor);
        $blockManager.render();
        break;

      case 'theme-footer':
        remove();
        Object(_theme_footer_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor);
        $blockManager.render();
        break;

      case 'bootstrap4':
        remove();
        Object(_bootstrap4_grid_js__WEBPACK_IMPORTED_MODULE_5__["default"])($editor);
        Object(_bootstrap4_general_js__WEBPACK_IMPORTED_MODULE_6__["default"])($editor);
        Object(_bootstrap4_text_js__WEBPACK_IMPORTED_MODULE_7__["default"])($editor);
        Object(_bootstrap4_button_js__WEBPACK_IMPORTED_MODULE_8__["default"])($editor);
        Object(_bootstrap4_card_js__WEBPACK_IMPORTED_MODULE_9__["default"])($editor);
        $blockManager.render();
        break;
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/theme/basic.js":
/*!****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/theme/basic.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'theme-basic',
    'label': 'Basic',
    'open': false
  };
  $blockManager.add('basic-1', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/01.png">',
    content: "\n        <section class=\"h-100 d-flex align-items-center bg-image-center\" style=\"background-image: url('/theme/bootstrap4/img/landscape/01.jpg'); \">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col col-12 col-xl-5 offset-xl-2 col-lg-6 offset-lg-1 col-md-8 offset-md-1\">\n                        <h1>Title</h1>\n                        <p class=\"lead text-muted\">Something short and leading about the collection below\u2014its contents, the creator, etc. Make it short and sweet, but not too short so folks don\u2019t simply skip over it entirely.</p>\n                        <p>\n                            <a href=\"#\" class=\"btn btn-primary my-2\"><i class=\"fa fa-search\"></i> Click Button</a>\n                        </p>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-2', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/02.png">',
    content: "\n        <section class=\"h-100 d-flex align-items-center bg-image-center\" style=\"background-image: url('/theme/bootstrap4/img/landscape/02.jpg');\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col col-12 col-xl-5 offset-xl-6 col-lg-8 offset-lg-4 col-md-8 offset-md-4\">\n                        <h1>Title</h1>\n                        <p class=\"lead text-muted\">Something short and leading about the collection below\u2014its contents, the creator, etc. Make it short and sweet, but not too short so folks don\u2019t simply skip over it entirely.</p>\n                        <p>\n                            <a href=\"#\" class=\"btn btn-primary my-2\"><i class=\"fa fa-search\"></i> Click Button</a>\n                        </p>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-3', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/03.png">',
    content: "\n        <section class=\"h-100 d-flex align-items-center\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-center\">\n                        <p class=\"lead text-muted\">Something short and leading</p>\n                        <h1>THE WEELS ARE SPINNING</h1>\n                        <p>\n                            <a href=\"#\" class=\"btn btn-primary my-2\"><i class=\"fa fa-search\"></i> Click Button</a>\n                        </p>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-4', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/04.png">',
    content: "\n        <section class=\"h-100 d-flex align-items-center\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-center\">\n                        <img style=\"width:100px\" src=\"data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgc3R5bGU9ImZpbGw6IHJnYmEoMCwwLDAsMC4xNSk7IHRyYW5zZm9ybTogc2NhbGUoMC43NSkiPgogICAgICAgIDxwYXRoIGQ9Ik04LjUgMTMuNWwyLjUgMyAzLjUtNC41IDQuNSA2SDVtMTYgMVY1YTIgMiAwIDAgMC0yLTJINWMtMS4xIDAtMiAuOS0yIDJ2MTRjMCAxLjEuOSAyIDIgMmgxNGMxLjEgMCAyLS45IDItMnoiPjwvcGF0aD4KICAgICAgPC9zdmc+\">\n                        <h1>Title</h1>\n                        <p class=\"lead text-muted\">Something short and leading about the collection below\u2014its contents, the creator, etc. Make it short and sweet, but not too short so folks don\u2019t simply skip over it entirely.</p>\n                        <p>\n                            <a href=\"#\" class=\"btn btn-primary my-2\"><i class=\"fa fa-search\"></i> Click Button</a>\n                        </p>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-5', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/05.png">',
    content: "\n        <section class=\"bg-image-center text-center\" style=\"background-image: url('/theme/bootstrap4/img/landscape/03.jpg');\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-white\">\n                        <h2>About us</h2>\n                        <p class=\"my-4\">Start Bootstrap has everything you need to get your new website up and running in no time!</p>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-6', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/06.png">',
    content: "\n        <section class=\"bg-primary bg-image-center\">\n            <div class=\"container\">\n                <div class=\"row justify-content-center\">\n                    <div class=\"col col-lg-8 text-center\">\n                        <h2 class=\"text-white mt-0\">We've got what you need!</h2>\n                        <hr class=\"divider light my-4\">\n                        <p class=\"text-white-50 mb-4\">Start Bootstrap has everything you need to get your new website up and running in no time! Choose one of our open source, free to download, and easy to use themes! No strings attached!</p>\n                        <a class=\"btn btn-light btn-xl\" href=\"#\">Get Started!</a>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-7', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/07.png">',
    content: "\n        <section>\n            <div class=\"container-fluid p-0\">\n                <div class=\"row no-gutters\">\n                    <div class=\"col col-12 col-lg-6\">\n                        <figure>\n                            <img src=\"/theme/bootstrap4/img/landscape/03.jpg\" class=\"img-fluid\">\n                        </figure>\n                    </div>\n                    <div class=\"col col-10 offset-1 col-lg-5 offset-lg-1 align-self-center\">\n                        <h4>Lorem Ipsum is simply text</h4>\n                        <p><small class=\"text-muted\">By Casey Lansford</small></p>\n                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>\n                    </div>\n                </div>\n\n                <div class=\"row no-gutters\">\n                    <div class=\"col col-10 offset-1 col-lg-4 offset-lg-1 align-self-center\">\n                        <h4>Lorem Ipsum is simply text</h4>\n                        <p><small class=\"text-muted\">By Casey Lansford</small></p>\n                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>\n                    </div>\n                    <div class=\"col col-12 col-lg-6 offset-lg-1\">\n                        <figure>\n                            <img src=\"/theme/bootstrap4/img/landscape/03.jpg\" class=\"img-fluid\">\n                        </figure>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-8', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/08.png">',
    content: "\n        <section class=\"border-top\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 col-lg-6\">\n                        <figure>\n                            <img src=\"/theme/bootstrap4/img/square/01.jpg\" class=\"img-fluid\">\n                        </figure>\n                    </div>\n                    <div class=\"col col-12 col-lg-6 align-self-center\">\n                        <h4>Lorem Ipsum is simply text</h4>\n                        <p><small class=\"text-muted\">By Casey Lansford</small></p>\n                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>\n                    </div>\n                </div>\n                <div class=\"row \">\n                    <div class=\"col col-12 col-lg-6 align-self-center\">\n                        <h4>Lorem Ipsum is simply text</h4>\n                        <p><small class=\"text-muted\">By Casey Lansford</small></p>\n                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>\n                    </div>\n                    <div class=\"col col-12 col-lg-6\">\n                        <figure>\n                            <img src=\"/theme/bootstrap4/img/square/02.jpg\" class=\"img-fluid\">\n                        </figure>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-9', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/13.png">',
    content: "\n        <section>\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-center\">\n                        <h2 class=\"title-style\">At Your Service</h2>\n                    </div>\n                </div>\n                <div class=\"row\">\n                    <div class=\"col col-12 col-lg-3 col-md-6 text-center\">\n                        <div class=\"mt-5\">\n                            <svg class=\"svg-inline--fa fa-gem fa-w-18 fa-4x text-primary mb-4\" aria-hidden=\"true\" focusable=\"false\" data-prefix=\"fas\" data-icon=\"gem\" role=\"img\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 576 512\" data-fa-i2svg=\"\"><path fill=\"currentColor\" d=\"M485.5 0L576 160H474.9L405.7 0h79.8zm-128 0l69.2 160H149.3L218.5 0h139zm-267 0h79.8l-69.2 160H0L90.5 0zM0 192h100.7l123 251.7c1.5 3.1-2.7 5.9-5 3.3L0 192zm148.2 0h279.6l-137 318.2c-1 2.4-4.5 2.4-5.5 0L148.2 192zm204.1 251.7l123-251.7H576L357.3 446.9c-2.3 2.7-6.5-.1-5-3.2z\"></path></svg><!-- <i class=\"fas fa-4x fa-gem text-primary mb-4\"></i> -->\n                            <h3 class=\"h4 mb-2\">Sturdy Themes</h3>\n                            <p class=\"text-muted mb-0\">Our themes are updated regularly to keep them bug free!</p>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6 text-center\">\n                        <div class=\"mt-5\">\n                            <svg class=\"svg-inline--fa fa-laptop-code fa-w-20 fa-4x text-primary mb-4\" aria-hidden=\"true\" focusable=\"false\" data-prefix=\"fas\" data-icon=\"laptop-code\" role=\"img\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 640 512\" data-fa-i2svg=\"\"><path fill=\"currentColor\" d=\"M255.03 261.65c6.25 6.25 16.38 6.25 22.63 0l11.31-11.31c6.25-6.25 6.25-16.38 0-22.63L253.25 192l35.71-35.72c6.25-6.25 6.25-16.38 0-22.63l-11.31-11.31c-6.25-6.25-16.38-6.25-22.63 0l-58.34 58.34c-6.25 6.25-6.25 16.38 0 22.63l58.35 58.34zm96.01-11.3l11.31 11.31c6.25 6.25 16.38 6.25 22.63 0l58.34-58.34c6.25-6.25 6.25-16.38 0-22.63l-58.34-58.34c-6.25-6.25-16.38-6.25-22.63 0l-11.31 11.31c-6.25 6.25-6.25 16.38 0 22.63L386.75 192l-35.71 35.72c-6.25 6.25-6.25 16.38 0 22.63zM624 416H381.54c-.74 19.81-14.71 32-32.74 32H288c-18.69 0-33.02-17.47-32.77-32H16c-8.8 0-16 7.2-16 16v16c0 35.2 28.8 64 64 64h512c35.2 0 64-28.8 64-64v-16c0-8.8-7.2-16-16-16zM576 48c0-26.4-21.6-48-48-48H112C85.6 0 64 21.6 64 48v336h512V48zm-64 272H128V64h384v256z\"></path></svg><!-- <i class=\"fas fa-4x fa-laptop-code text-primary mb-4\"></i> -->\n                            <h3 class=\"h4 mb-2\">Up to Date</h3>\n                            <p class=\"text-muted mb-0\">All dependencies are kept current to keep things fresh.</p>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6 text-center\">\n                        <div class=\"mt-5\">\n                            <svg class=\"svg-inline--fa fa-globe fa-w-16 fa-4x text-primary mb-4\" aria-hidden=\"true\" focusable=\"false\" data-prefix=\"fas\" data-icon=\"globe\" role=\"img\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 496 512\" data-fa-i2svg=\"\"><path fill=\"currentColor\" d=\"M336.5 160C322 70.7 287.8 8 248 8s-74 62.7-88.5 152h177zM152 256c0 22.2 1.2 43.5 3.3 64h185.3c2.1-20.5 3.3-41.8 3.3-64s-1.2-43.5-3.3-64H155.3c-2.1 20.5-3.3 41.8-3.3 64zm324.7-96c-28.6-67.9-86.5-120.4-158-141.6 24.4 33.8 41.2 84.7 50 141.6h108zM177.2 18.4C105.8 39.6 47.8 92.1 19.3 160h108c8.7-56.9 25.5-107.8 49.9-141.6zM487.4 192H372.7c2.1 21 3.3 42.5 3.3 64s-1.2 43-3.3 64h114.6c5.5-20.5 8.6-41.8 8.6-64s-3.1-43.5-8.5-64zM120 256c0-21.5 1.2-43 3.3-64H8.6C3.2 212.5 0 233.8 0 256s3.2 43.5 8.6 64h114.6c-2-21-3.2-42.5-3.2-64zm39.5 96c14.5 89.3 48.7 152 88.5 152s74-62.7 88.5-152h-177zm159.3 141.6c71.4-21.2 129.4-73.7 158-141.6h-108c-8.8 56.9-25.6 107.8-50 141.6zM19.3 352c28.6 67.9 86.5 120.4 158 141.6-24.4-33.8-41.2-84.7-50-141.6h-108z\"></path></svg><!-- <i class=\"fas fa-4x fa-globe text-primary mb-4\"></i> -->\n                            <h3 class=\"h4 mb-2\">Ready to Publish</h3>\n                            <p class=\"text-muted mb-0\">You can use this design as is, or you can make changes!</p>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6 text-center\">\n                        <div class=\"mt-5\">\n                            <svg class=\"svg-inline--fa fa-heart fa-w-16 fa-4x text-primary mb-4\" aria-hidden=\"true\" focusable=\"false\" data-prefix=\"fas\" data-icon=\"heart\" role=\"img\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\" data-fa-i2svg=\"\"><path fill=\"currentColor\" d=\"M462.3 62.6C407.5 15.9 326 24.3 275.7 76.2L256 96.5l-19.7-20.3C186.1 24.3 104.5 15.9 49.7 62.6c-62.8 53.6-66.1 149.8-9.9 207.9l193.5 199.8c12.5 12.9 32.8 12.9 45.3 0l193.5-199.8c56.3-58.1 53-154.3-9.8-207.9z\"></path></svg><!-- <i class=\"fas fa-4x fa-heart text-primary mb-4\"></i> -->\n                            <h3 class=\"h4 mb-2\">Made with Love</h3>\n                            <p class=\"text-muted mb-0\">Is it really open source if it's not made with love?</p>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('basic-10', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/14.png">',
    content: "\n        <section class=\"border-top\">\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-center\">\n                        <h2 class=\"title-style\">Gallery</h2>\n                    </div>\n                </div>\n                <div class=\"row no-gutters\">\n                    <div class=\"col col-12 col-lg-3 col-md-6\">\n                        <img class=\"img-fluid\" src=\"/theme/bootstrap4/img/square/05.jpg\">\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6\">\n                        <img class=\"img-fluid\" src=\"/theme/bootstrap4/img/square/06.jpg\">\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6\">\n                        <img class=\"img-fluid\" src=\"/theme/bootstrap4/img/square/07.jpg\">\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6\">\n                        <img class=\"img-fluid\" src=\"/theme/bootstrap4/img/square/08.jpg\">\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/theme/card.js":
/*!***************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/theme/card.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'theme-card',
    'label': 'Card',
    'open': false
  };
  $blockManager.add('card-1', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/09.png">',
    content: "\n        <section class=\"py-5 border-top\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 col-md-4\">\n                        <div class=\"card\">\n                            <img src=\"/theme/bootstrap4/img/square/01.jpg\" class=\"card-img-top\">\n                            <div class=\"card-body\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n                                <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-md-4\">\n                        <div class=\"card\">\n                            <img src=\"/theme/bootstrap4/img/square/02.jpg\" class=\"card-img-top\">\n                            <div class=\"card-body\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n                                <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-md-4\">\n                        <div class=\"card\">\n                            <img src=\"/theme/bootstrap4/img/square/03.jpg\" class=\"card-img-top\">\n                            <div class=\"card-body\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n                                <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('card-2', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/10.png">',
    content: "\n        <section class=\"py-5 border-top\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 col-lg-3 col-md-6\">\n                        <div class=\"card mb-3\">\n                            <img src=\"/theme/bootstrap4/img/square/01.jpg\" class=\"card-img-top\">\n                            <div class=\"card-body\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n                                <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6\">\n                        <div class=\"card mb-3\">\n                            <img src=\"/theme/bootstrap4/img/square/02.jpg\" class=\"card-img-top\">\n                            <div class=\"card-body\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n                                <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6\">\n                        <div class=\"card mb-3\">\n                            <img src=\"/theme/bootstrap4/img/square/03.jpg\" class=\"card-img-top\">\n                            <div class=\"card-body\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n                                <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-lg-3 col-md-6\">\n                        <div class=\"card mb-3\">\n                            <img src=\"/theme/bootstrap4/img/square/04.jpg\" class=\"card-img-top\">\n                            <div class=\"card-body\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n                                <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('card-3', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/11.png">',
    content: "\n        <section class=\"py-5\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 col-lg-6\">\n                        <div class=\"card mb-3\">\n                            <div class=\"row no-gutters\">\n                                <div class=\"col col-md-4\">\n                                    <img src=\"/theme/bootstrap4/img/portrait/02.jpg\" class=\"card-img\">\n                                </div>\n                                <div class=\"col col-md-8\">\n                                    <div class=\"card-body\">\n                                        <h5 class=\"card-title\">Card title</h5>\n                                        <p class=\"card-text\">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>\n                                        <p class=\"card-text\"><small class=\"text-muted\">Last updated 3 mins ago</small></p>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-lg-6\">\n                        <div class=\"card mb-3\">\n                            <div class=\"row no-gutters\">\n                                <div class=\"col col-md-4\">\n                                    <img src=\"/theme/bootstrap4/img/portrait/03.jpg\" class=\"card-img\">\n                                </div>\n                                <div class=\"col col-md-8\">\n                                    <div class=\"card-body\">\n                                        <h5 class=\"card-title\">Card title</h5>\n                                        <p class=\"card-text\">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>\n                                        <p class=\"card-text\"><small class=\"text-muted\">Last updated 3 mins ago</small></p>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('card-4', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/12.png">',
    content: "\n        <section class=\"py-5\">\n            <div class=\"container-fluid\">\n                <div class=\"row no-gutters\">\n                    <div class=\"col col-12 col-lg-8\">\n                        <div class=\"card text-white\">\n                            <img src=\"/theme/bootstrap4/img/landscape/03.jpg\" class=\"card-img\">\n                            <div class=\"card-img-overlay\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>\n                                <p class=\"card-text\">Last updated 3 mins ago</p>\n                            </div>\n                        </div>\n                    </div>\n\n                    <div class=\"col col-12 col-lg-4\">\n                        <div class=\"card text-white\">\n                            <img src=\"/theme/bootstrap4/img/landscape/04.jpg\" class=\"card-img\">\n                            <div class=\"card-img-overlay\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>\n                                <p class=\"card-text\">Last updated 3 mins ago</p>\n                            </div>\n                        </div>\n                        <div class=\"card text-white\">\n                            <img src=\"/theme/bootstrap4/img/landscape/05.jpg\" class=\"card-img\">\n                            <div class=\"card-img-overlay\">\n                                <h5 class=\"card-title\">Card title</h5>\n                                <p class=\"card-text\">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>\n                                <p class=\"card-text\">Last updated 3 mins ago</p>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/theme/footer.js":
/*!*****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/theme/footer.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'theme-footer',
    'label': 'Footer',
    'open': false
  };
  $blockManager.add('footer-1', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/21.png">',
    content: "\n        <footer class=\"border-top footer-dark\">\n            <div class=\"container py-5\">\n                <div class=\"row text-white\">\n                    <div class=\"col col-12 col-lg-6 mb-lg-0 mb-4\">\n                        <h5>Subscribe Our Newsletter</h5>\n                        <p class=\"text-lighter\">\n                            Contrary to popular belief of lorem Ipsm Latin amet ltin from industry. Phasellus blandit massa enim varius nunc.</div>\n                        </p>\n                    <div class=\"col col-12 col-lg-6 align-self-center\">\n                        <form>\n                            <div class=\"input-group mb-3\">\n                                <input type=\"text\" class=\"form-control\" id=\"email\" name=\"email\" placeholder=\"Email Subscribe\">\n                                <div class=\"input-group-append\">\n                                    <button class=\"btn btn-outline-light\" type=\"button\" id=\"button-addon2\"><i class=\"fas fa-paper-plane\"></i></button>\n                                </div>\n                            </div>\n                        </form>\n                    </div>\n                </div>\n            </div>\n            <div class=\"container pb-5\">\n                <div class=\"row\">\n                    <div class=\"col col-12 col-lg-3 text-white\">\n                        <div class=\"footer-brand\">Ark UI Kit</div>\n                        <div class=\"mb-3\">Build better websites</div>\n                        <div class=\"icon-list-social mb-5\">\n                            <div>\n                                <span class=\"pr-3\">\n                                    <a href=\"#\"><i class=\"fab fa-facebook-f\"></i></a>\n                                </span>\n                                <span class=\"pr-3\">\n                                    <a href=\"#\"><i class=\"fab fa-twitter\"></i></a>\n                                </span>\n                                <span class=\"pr-3\">\n                                    <a href=\"#\"><i class=\"fab fa-instagram\"></i></a>\n                                </span>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col col-12 col-lg-9 text-white\">\n                        <div class=\"row\">\n                            <div class=\"col col-12 col-lg-3 col-md-6\">\n                                <h5>Account</h5>\n                                <div class=\"footer-inner-content\">\n                                    <ul class=\"list-unstyled\">\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Register</a>\n                                        </li>\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">My Account</a>\n                                        </li>\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">My Orders</a>\n                                        </li>\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Discount</a>\n                                        </li>\n                                    </ul>\n                                </div>\n                            </div>\n                            <div class=\"col col-12 col-lg-3 col-md-6\">\n                                <h5>More Links</h5>\n                                <div class=\"footer-inner-content\">\n                                    <ul class=\"list-unstyled\">\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Custom Image Title</a>\n                                        </li>\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Custom Font Style</a>\n                                        </li>\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Parallax Sections</a>\n                                        </li>\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Contact us</a>\n                                        </li>\n                                    </ul>\n                                </div>\n                            </div>\n                            <div class=\"col col-12 col-lg-3 col-md-6\">\n                                <h5>LEGAL</h5>\n                                <div class=\"footer-inner-content\">\n                                    <ul class=\"list-unstyled\">\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Privacy Policy</a>\n                                        </li>\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Terms</a>\n                                        </li>\n                                        <li>\n                                            <a href=\"#\" class=\"text-white\">Contact us</a>\n                                        </li>\n                                    </ul>\n                                </div>\n                            </div>\n                            <div class=\"col col-12 col-lg-3 col-md-6\">\n                                <h5 class=\"text-white\">Contact Info</h5>\n                                <div class=\"footer-inner-content\">\n                                    <div class=\"d-flex\">\n                                        <span class=\"pr-2\"><i class=\"fas fa-map-marker-alt\"></i></span> \n                                        <span>West 21th Street, Suite 721</span>\n                                    </div>\n                                    <div class=\"d-flex\">\n                                        <span class=\"pr-2\"><i class=\"fas fa-envelope\"></i></span> \n                                        <span>youremail@yourdomain.com</span>\n                                    </div>\n                                    <div class=\"d-flex\">\n                                        <span class=\"pr-2\"><i class=\"fas fa-phone\"></i></span> \n                                        <span>+88 (0) 202 0000 001</span>\n                                    </div>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-center py-3 footer-darker\">\n                        <p>\n                            \xA9 Copyright philip.place\n                        </p>\n                    </div>\n                </div>\n            </div>\n        </footer>"
  });
  $blockManager.add('footer-2', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/20.png">',
    content: "\n        <footer>\n            <div class=\"container-fluid\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-center py-3 footer-darker\">\n                        <p>\n                            \xA9 Copyright philip.place\n                        </p>\n                    </div>\n                </div>\n            </div>\n        </footer>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/theme/header.js":
/*!*****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/theme/header.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'theme-header',
    'label': 'Header',
    'open': false
  };
  $blockManager.add('header-root', {
    category: $category,
    //label: 'Navbar',
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/header/navbar.svg">',
    content: "\n        <header id=\"mainHeader\" class=\"transparent-header fixed-top\">\n            <nav class=\"navbar navbar-expand-lg bg-white\">\n                <div class=\"container d-flex\">\n\n                    <div class=\"align-self-center nav-logo\">\n                        <a class=\"navbar-brand\" href=\"/\">\n                            <img src=\"data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgc3R5bGU9ImZpbGw6IHJnYmEoMCwwLDAsMC4xNSk7IHRyYW5zZm9ybTogc2NhbGUoMC43NSkiPgogICAgICAgIDxwYXRoIGQ9Ik04LjUgMTMuNWwyLjUgMyAzLjUtNC41IDQuNSA2SDVtMTYgMVY1YTIgMiAwIDAgMC0yLTJINWMtMS4xIDAtMiAuOS0yIDJ2MTRjMCAxLjEuOSAyIDIgMmgxNGMxLjEgMCAyLS45IDItMnoiPjwvcGF0aD4KICAgICAgPC9zdmc+\">\n                        </a>\n                    </div>\n\n                    <button class=\"navbar-toggler\" type=\"button\" data-trigger=\"#nav-main\">\n                        <i class=\"fa fa-bars\"></i>\n                    </button>\n\n                    <div id=\"nav-main\" class=\"navbar-collapse\">\n                        <div class=\"p-4 bg-light border-bottom d-block d-lg-none\">\n                            <button class=\"btn btn-link btn-close text-dark\"> \xD7 Close </button>\n                            <h6 class=\"mb-0\">Menu</h6>\n                        </div>\n                        <ul class=\"navbar-nav\">\n                            <li class=\"nav-item\">\n                                <a class=\"nav-link\" href=\"#\">About</a>\n                            </li>\n                            <li class=\"nav-item\">\n                                <a class=\"nav-link\" href=\"#\">Services</a>\n                            </li>\n                            <li class=\"nav-item dropdown\">\n                                <a class=\"nav-link dropdown-toggle\" href=\"#\" data-toggle=\"dropdown\">Menu</a>\n                                <ul class=\"dropdown-menu\">\n                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 1</a></li>\n                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 2</a></li>\n                                    <li class=\"dropdown-submenu\">\n                                        <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 3</a>\n                                        <ul class=\"dropdown-menu\">\n                                            <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-1</a></li>\n                                            <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-2</a></li>\n                                            <li class=\"dropdown-submenu\">\n                                                <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 3-3</a>\n                                                <ul class=\"dropdown-menu\">\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-3-1</a></li>\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-3-2</a></li>\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-3-3</a></li>\n                                                </ul>\n                                            </li>\n                                            <li class=\"dropdown-submenu\">\n                                                <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 3-4</a>\n                                                <ul class=\"dropdown-menu\">\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-4-1</a></li>\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-4-2</a></li>\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-4-3</a></li>\n                                                </ul>\n                                            </li>\n                                        </ul>\n                                    </li>\n                                    <li class=\"dropdown-submenu\">\n                                        <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 4</a>\n                                        <ul class=\"dropdown-menu\">\n                                            <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-1</a></li>\n                                            <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-2</a></li>\n                                            <li class=\"dropdown-submenu\">\n                                                <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 4-3</a>\n                                                <ul class=\"dropdown-menu\">\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-3-1</a></li>\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-3-2</a></li>\n                                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-3-3</a></li>\n                                                </ul>\n                                            </li>\n                                        </ul>\n                                    </li>\n                                </ul>\n                            </li>\n                            <li class=\"nav-item dropdown\">\n                                <a class=\"nav-link dropdown-toggle\" href=\"#\" data-toggle=\"dropdown\">Menu</a>\n                                <ul class=\"dropdown-menu\">\n                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu item 1</a></li>\n                                    <li><a class=\"dropdown-item\" href=\"#\">Submenu item 2</a></li>\n                                </ul>\n                            </li>\n                            <li class=\"nav-item dropdown has-megamenu\">\n                                <a class=\"nav-link dropdown-toggle\" href=\"#\" data-toggle=\"dropdown\" aria-expanded=\"true\">Mega menu</a>\n                                <div class=\"dropdown-menu megamenu megamenu-no-gutter\" role=\"menu\">\n                                    <div class=\"container\">\n                                        <div class=\"row\">\n                                            <div class=\"col-lg-3 col-sm-4 col-12 d-lg-block d-none\">\n                                                <img class=\"img-fluid\" src=\"/theme/bootstrap4/img/portrait/01.jpg\">\n                                            </div>\n                                            <div class=\"col-lg-3 col-sm-4 col-12\">\n                                                <ul class=\"list-unstyled pt-lg-4\">\n                                                    <li class=\"dropdown-item title\"><div>Title</div></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"col-lg-3 col-sm-4 col-12\">\n                                                <ul class=\"list-unstyled pt-lg-4\">\n                                                    <li class=\"dropdown-item title\"><div>Title</div></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                </ul>\n                                            </div>\n                                            <div class=\"col-lg-3 col-sm-4 col-12\">\n                                                <ul class=\"list-unstyled pt-lg-4\">\n                                                    <li class=\"dropdown-item title\"><div>Title</div></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                    <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                                </ul>\n                                            </div>\n                                        </div>\n                                    </div>\n                                </div>\n                            </li>\n                        </ul>\n                    </div>\n\n                    <div class=\"navbar-right pr-1\">\n                        <ul class=\"navbar-nav ml-auto d-flex flex-row\">\n                            <li class=\"nav-item nav-item-search\">\n                                <a href=\"javascript:;\" class=\"btn btn-link btn-search nav-link\">\n                                    <i class=\"fa fa-search\"></i>\n                                </a>\n                            </li>\n                            <li class=\"nav-item nav-item-user\">\n                                <a href=\"javascript:;\" class=\"btn btn-link btn-user nav-link\">\n                                    <i class=\"fa fa-user\"></i>\n                                </a>\n                            </li>\n                            <li class=\"nav-item nav-item-cart\">\n                                <a href=\"javascript:;\" class=\"btn btn-link btn-cart nav-link\">\n                                    <i class=\"fa fa-shopping-cart\"></i>\n                                </a>\n                            </li>\n                        </ul>\n                    </div>\n\n                </div>\n            </nav>\n        </header>"
  });
  $blockManager.add('header-link', {
    category: $category,
    //label: 'Link',
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/header/link.svg">',
    content: "<li class=\"nav-item\"><a class=\"nav-link\" href=\"#\">Menu</a></li>"
  });
  $blockManager.add('header-dropdown', {
    category: $category,
    //label: 'Dropdown',
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/header/dropdown.svg">',
    content: "\n        <li class=\"nav-item dropdown\">\n            <a class=\"nav-link dropdown-toggle\" href=\"#\" data-toggle=\"dropdown\">Menu</a>\n            <ul class=\"dropdown-menu\">\n                <li><a class=\"dropdown-item\" href=\"#\">Submenu 1</a></li>\n                <li><a class=\"dropdown-item\" href=\"#\">Submenu 2</a></li>\n                <li class=\"dropdown-submenu\">\n                    <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 3</a>\n                    <ul class=\"dropdown-menu\">\n                        <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-1</a></li>\n                        <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-2</a></li>\n                        <li class=\"dropdown-submenu\">\n                            <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 3-3</a>\n                            <ul class=\"dropdown-menu\">\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-3-1</a></li>\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-3-2</a></li>\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-3-3</a></li>\n                            </ul>\n                        </li>\n                        <li class=\"dropdown-submenu\">\n                            <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 3-4</a>\n                            <ul class=\"dropdown-menu\">\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-4-1</a></li>\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-4-2</a></li>\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 3-4-3</a></li>\n                            </ul>\n                        </li>\n                    </ul>\n                </li>\n                <li class=\"dropdown-submenu\">\n                    <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 4</a>\n                    <ul class=\"dropdown-menu\">\n                        <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-1</a></li>\n                        <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-2</a></li>\n                        <li class=\"dropdown-submenu\">\n                            <a class=\"dropdown-item dropdown-toggle\" href=\"#\">Submenu 4-3</a>\n                            <ul class=\"dropdown-menu\">\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-3-1</a></li>\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-3-2</a></li>\n                                <li><a class=\"dropdown-item\" href=\"#\">Submenu 4-3-3</a></li>\n                            </ul>\n                        </li>\n                    </ul>\n                </li>\n            </ul>\n        </li>"
  });
  $blockManager.add('header-megamenu', {
    category: $category,
    //label: 'Megamenu',
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/header/megamenu.svg">',
    content: "\n        <li class=\"nav-item dropdown has-megamenu\">\n            <a class=\"nav-link dropdown-toggle\" href=\"#\" data-toggle=\"dropdown\" aria-expanded=\"true\">Mega menu</a>\n            <div class=\"dropdown-menu megamenu megamenu-no-gutter\" role=\"menu\">\n                <div class=\"container\">\n                    <div class=\"row\">\n                        <div class=\"col-lg-3 col-sm-4 col-12 d-lg-block d-none\">\n                            <img class=\"img-fluid\" src=\"/theme/bootstrap4/img/portrait/01.jpg\">\n                        </div>\n                        <div class=\"col-lg-3 col-sm-4 col-12\">\n                            <ul class=\"list-unstyled pt-lg-4\">\n                                <li class=\"dropdown-item title\"><div>Title</div></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                            </ul>\n                        </div>\n                        <div class=\"col-lg-3 col-sm-4 col-12\">\n                            <ul class=\"list-unstyled pt-lg-4\">\n                                <li class=\"dropdown-item title\"><div>Title</div></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                            </ul>\n                        </div>\n                        <div class=\"col-lg-3 col-sm-4 col-12\">\n                            <ul class=\"list-unstyled pt-lg-4\">\n                                <li class=\"dropdown-item title\"><div>Title</div></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                                <li class=\"dropdown-item\"><a href=\"#\">Item</a></li>\n                            </ul>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </li>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/block/theme/user.js":
/*!***************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/block/theme/user.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $blockManager = $editor.BlockManager,
      $category = {
    'id': 'theme-user',
    'label': 'User',
    'open': false
  };
  $blockManager.add('user-login', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/15.png">',
    content: "\n        <section>\n            <div class=\"container-fluid\">\n                <div class=\"row h-100vh\">\n                    <div class=\"col col-11 col-lg-8 col-sm-10 bg-image-center\" style=\"background-image: url('/theme/bootstrap4/img/landscape/03.jpg');\">\n                        <p> </p>\n                    </div>\n                    <div class=\"col col-1 col-lg-4 col-sm-2 d-flex align-items-center\">\n                        <form method=\"post\" id=\"form-login\" name=\"form-login\" class=\"mx-auto w-80\" action=\"{{ route('login_process') }}\">\n                            <h1 class=\"mb-4\">Login</h1>\n                            <div class=\"form-group\">\n                                <label class=\"form-control-label required\" for=\"login-username\">Email address</label>\n                                <input type=\"email\" class=\"form-control\" id=\"login-username\" name=\"username\" placeholder=\"Email\" required>\n                                <div class=\"invalid-feedback\"></div>\n                                <div class=\"help-feedback\"></div>\n                            </div>\n                            <div class=\"form-group\">\n                                <label class=\"form-control-label required\" for=\"login-password\">Password</label>\n                                <input type=\"password\" class=\"form-control\" id=\"login-password\" name=\"password\" placeholder=\"Password\" required>\n                                <div class=\"invalid-feedback\"></div>\n                                <div class=\"help-feedback\"></div>\n                            </div>\n\n                            @if($login_failed)\n                            <div class=\"mt-5 login-failed\">\n                                <div class=\"alert alert-danger\">\n                                        Login failed.\n                                        <br>\n                                        Please check your username and password.\n                                </div>\n                            </div>\n                            @endif\n\n                            <button type=\"submit\" class=\"btn btn-primary btn-block\">Submit</button>\n                            <div class=\"text-right pt-2 text-muted small\">\n                                <a href=\"{{ route('forgot_password') }}\">\u5FD8\u8A18\u5BC6\u78BC</a>\n                            </div>\n                        </form>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('user-register', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/16.png">',
    content: "\n        <section>\n            <div class=\"container-fluid\">\n                <div class=\"row h-100vh\">\n                    <div class=\"col col-11 col-lg-8 col-sm-10  bg-image-center\" style=\"background-image: url('/theme/bootstrap4/img/landscape/04.jpg');\"></div>\n                    <div class=\"col col-1 col-lg-4 col-sm-2 d-flex align-items-center\">\n                        <form method=\"post\" id=\"form-register\" name=\"form-register\" class=\"mx-auto w-80\" action=\"{{ route('register_process') }}\">\n                            <h1 class=\"mb-4\">Register</h1>\n\n                            <div class=\"form-group\">\n                                <label class=\"form-control-label required\" for=\"register-name\">\n                                    Your Name\n                                </label>\n                                <input type=\"text\" class=\"form-control\" id=\"register-name\" name=\"name\" placeholder=\"Name\" required />\n                                <div class=\"invalid-feedback\"></div>\n                                <div class=\"help-feedback\">Required</div>\n                            </div>\n                            <div class=\"form-group\">\n                                <label class=\"form-control-label required\" for=\"register-username\">Email address</label>\n                                <input type=\"email\" class=\"form-control\" id=\"register-username\" name=\"username\" placeholder=\"Enter email\" required>\n                                <div class=\"invalid-feedback\"></div>\n                                <div class=\"help-feedback\"></div>\n                            </div>\n                            <div class=\"form-group\">\n                                <label class=\"form-control-label required\" for=\"register-password\">Password</label>\n                                <input type=\"password\" class=\"form-control\" id=\"register-password\" name=\"password\" placeholder=\"Password\" required>\n                                <div class=\"invalid-feedback\"></div>\n                                <div class=\"help-feedback\"></div>\n                            </div>\n                            <div class=\"form-group\">\n                                <label class=\"form-control-label\" for=\"register-password-confirm\">Confirm Password</label>\n                                <input type=\"password\" class=\"form-control\" id=\"register-password-confirm\" name=\"password_confirm\" placeholder=\"Confirm Password\">\n                                <div class=\"invalid-feedback\"></div>\n                                <div class=\"help-feedback\"></div>\n                            </div>\n                            <button type=\"submit\" class=\"btn btn-primary btn-block\">Submit</button>\n                        </form>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('user-register-completed', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/17.png">',
    content: "\n        <section class=\"h-100 d-flex align-items-center bg-image-center\" style=\"background-image: url('/theme/bootstrap4/img/landscape/06.jpg');\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-center text-white\">\n                        <div style=\"font-size: 60px;\">\n                            <i class=\"fas fa-users\"></i>\n                        </div>\n                        <h1>Registration Completed</h1>\n                        <div class=\"text-center\">\n                            <a href=\"#\" class=\"btn btn-primary\"><i class=\"fas fa-home\"></i> Home</a>\n                            <a href=\"#\" class=\"btn btn-primary\"><i class=\"fas fa-house-user\"></i> Account</a>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('user-forgot-password', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/18.png">',
    content: "\n        <section>\n            <div class=\"container-fluid\">\n                <div class=\"row h-100vh\">\n                    <div class=\"col col-11 col-lg-8 col-sm-10 bg-image-center\" style=\"background-image: url('/theme/bootstrap4/img/landscape/05.jpg');\"></div>\n                    <div class=\"col col-1 col-lg-4 col-sm-1 d-flex align-items-center\">\n                        <form method=\"post\" id=\"form-forgot-password\" name=\"form-forgot-password\" class=\"mx-auto w-80\" action=\"{{ route('forgot_password_process') }}\">\n                            <h1 class=\"mb-4\">Forgot Password</h1>\n\n                            <div class=\"form-group\">\n                                <label class=\"form-control-label required\" for=\"forgot-password-username\">Email address</label>\n                                <input type=\"email\" class=\"form-control\" id=\"forgot-password-username\" name=\"username\" placeholder=\"Enter email\" required>\n                                <div class=\"invalid-feedback\"></div>\n                                <div class=\"help-feedback\"></div>\n                            </div>\n                            <button type=\"submit\" class=\"btn btn-primary btn-block\">Submit</button>\n                        </form>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
  $blockManager.add('user-forgot-password-completed', {
    category: $category,
    media: '<img class="img-fluid" src="/theme/bootstrap4/img/preview/basic/19.png">',
    content: "\n        <section class=\"h-100 d-flex align-items-center bg-image-center\" style=\"background-image: url('/theme/bootstrap4/img/landscape/07.jpg');\">\n            <div class=\"container\">\n                <div class=\"row\">\n                    <div class=\"col col-12 text-center text-white\">\n                        <div style=\"font-size: 60px;\">\n                            <i class=\"fas fa-users\"></i>\n                        </div>\n                        <h1>New Password is send to your mailbox</h1>\n                        <div class=\"text-center\">\n                            <a href=\"#\" class=\"btn btn-primary\"><i class=\"fas fa-home\"></i> Home</a>\n                            <a href=\"#\" class=\"btn btn-primary\"><i class=\"fas fa-sign-in-alt\"></i> Login</a>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </section>"
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/config/background-color.js":
/*!**********************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/config/background-color.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _color_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./color.js */ "./resources/theme/bootstrap4/src/js/config/color.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }



/* harmony default export */ __webpack_exports__["default"] = ({
  type: 'class_select',
  options: [{
    value: '',
    name: 'Transparent'
  }, {
    value: 'bg-white',
    name: 'White'
  }].concat(_toConsumableArray(_color_js__WEBPACK_IMPORTED_MODULE_1__["default"].map(function (v) {
    return {
      value: "bg-".concat(v),
      name: underscore_string__WEBPACK_IMPORTED_MODULE_0___default.a.capitalize(v)
    };
  }))),
  label: 'Background Color'
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/config/btn-color.js":
/*!***************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/config/btn-color.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _color_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./color.js */ "./resources/theme/bootstrap4/src/js/config/color.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }



/* harmony default export */ __webpack_exports__["default"] = ({
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }].concat(_toConsumableArray(_color_js__WEBPACK_IMPORTED_MODULE_1__["default"].map(function (v) {
    return {
      value: "btn-".concat(v),
      name: underscore_string__WEBPACK_IMPORTED_MODULE_0___default.a.capitalize(v)
    };
  })), _toConsumableArray(_color_js__WEBPACK_IMPORTED_MODULE_1__["default"].map(function (v) {
    return {
      value: "btn-outline-".concat(v),
      name: underscore_string__WEBPACK_IMPORTED_MODULE_0___default.a.capitalize(v) + ' (Outline)'
    };
  }))),
  label: 'Button Color',
  name: 'btn-color'
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/config/btn-sizes.js":
/*!***************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/config/btn-sizes.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  'lg': 'Large',
  'sm': 'Small'
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/config/color.js":
/*!***********************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/config/color.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark']);

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/config/column.js":
/*!************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/config/column.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

/* harmony default export */ __webpack_exports__["default"] = ([{
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }, {
    value: 'col-xl',
    name: 'Equal'
  }, {
    value: 'col-xl-auto',
    name: 'Variable'
  }].concat(_toConsumableArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'col-xl-' + i,
      name: i + '/12'
    };
  }))),
  label: 'XL Width'
}, {
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }].concat(_toConsumableArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'offset-xl-' + i,
      name: i + '/12'
    };
  }))),
  label: 'XL Offset'
}, {
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }, {
    value: 'col-lg',
    name: 'Equal'
  }, {
    value: 'col-lg-auto',
    name: 'Variable'
  }].concat(_toConsumableArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'col-lg-' + i,
      name: i + '/12'
    };
  }))),
  label: 'LG Width'
}, {
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }].concat(_toConsumableArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'offset-lg-' + i,
      name: i + '/12'
    };
  }))),
  label: 'LG Offset'
}, {
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }, {
    value: 'col-md',
    name: 'Equal'
  }, {
    value: 'col-md-auto',
    name: 'Variable'
  }].concat(_toConsumableArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'col-md-' + i,
      name: i + '/12'
    };
  }))),
  label: 'MD Width'
}, {
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }].concat(_toConsumableArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'offset-md-' + i,
      name: i + '/12'
    };
  }))),
  label: 'MD Offset'
}, {
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }, {
    value: 'col-sm',
    name: 'Equal'
  }, {
    value: 'col-sm-auto',
    name: 'Variable'
  }].concat(_toConsumableArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'col-sm-' + i,
      name: i + '/12'
    };
  }))),
  label: 'SM Width'
}, {
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }].concat(_toConsumableArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'offset-sm-' + i,
      name: i + '/12'
    };
  }))),
  label: 'SM Offset'
}, {
  type: 'class_select',
  options: [{
    value: 'col',
    name: 'Equal'
  }, {
    value: 'col-auto',
    name: 'Variable'
  }].concat(_toConsumableArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'col-' + i,
      name: i + '/12'
    };
  }))),
  label: 'XS Width'
}, {
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }].concat(_toConsumableArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) {
    return {
      value: 'offset-' + i,
      name: i + '/12'
    };
  }))),
  label: 'XS Offset'
}]);

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/config/text-color.js":
/*!****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/config/text-color.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _color_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./color.js */ "./resources/theme/bootstrap4/src/js/config/color.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }



/* harmony default export */ __webpack_exports__["default"] = ({
  type: 'class_select',
  options: [{
    value: '',
    name: 'Default'
  }, {
    value: 'text-white',
    name: 'White'
  }, {
    value: 'text-muted',
    name: 'Muted'
  }].concat(_toConsumableArray(_color_js__WEBPACK_IMPORTED_MODULE_1__["default"].map(function (v) {
    return {
      value: "text-".concat(v),
      name: underscore_string__WEBPACK_IMPORTED_MODULE_0___default.a.capitalize(v)
    };
  }))),
  label: 'Text Color'
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/config/text-size.js":
/*!***************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/config/text-size.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  type: 'class_select',
  options: [{
    value: '',
    name: 'None'
  }, {
    value: 'display-1',
    name: 'One (largest)'
  }, {
    value: 'display-2',
    name: 'Two '
  }, {
    value: 'display-3',
    name: 'Three '
  }, {
    value: 'display-4',
    name: 'Four (smallest)'
  }],
  label: 'Text Size'
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/config/text-type.js":
/*!***************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/config/text-type.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  type: 'select',
  options: [{
    value: 'div',
    name: 'Div'
  }, {
    value: 'p',
    name: 'P'
  }, {
    value: 'span',
    name: 'Span'
  }],
  name: 'tagName',
  label: 'Type',
  changeProp: 1
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/alert.js":
/*!******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/alert.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _config_color_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/color.js */ "./resources/theme/bootstrap4/src/js/config/color.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }



/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $textType = $domComponents.getType('text'),
      $textModel = $textType.model,
      $textView = $textType.view;
  $domComponents.addType('alert', {
    model: $textModel.extend({
      defaults: _objectSpread(_objectSpread({}, $textModel.prototype.defaults), {}, {
        'custom-name': 'Alert',
        tagName: 'div',
        droppable: false,
        classes: ['alert'],
        traits: [{
          type: 'class_select',
          options: _toConsumableArray(_config_color_js__WEBPACK_IMPORTED_MODULE_1__["default"].map(function (v) {
            return {
              value: "alert-".concat(v),
              name: underscore_string__WEBPACK_IMPORTED_MODULE_0___default.a.capitalize(v)
            };
          })),
          label: 'Color'
        }, {
          type: 'checkbox',
          label: 'Dismissible',
          name: 'dismissible',
          changeProp: 1
        }]
      }),
      init: function init() {
        this.listenTo(this, 'change:dismissible', this.dismissible);
      },
      dismissible: function dismissible() {
        var state = this.get('dismissible');
        var children = this.components();
        var existing = children.filter(function (comp) {
          return comp.attributes.type === 'button' && comp.attributes.attributes['data-dismiss'] == 'alert';
        })[0];

        if (state && !existing) {
          this.addClass('alert-dismissible');
          this.addClass('fade');
          this.addClass('show');
          var button = children.add({
            type: 'button',
            tagName: 'button',
            classes: 'close',
            attributes: {
              type: 'button',
              'data-dismiss': 'alert',
              'aria-label': 'Close'
            }
          });
          button.components().add({
            type: 'text',
            tagName: 'span',
            content: '&times;',
            attributes: {
              'aria-hidden': 'true'
            }
          });
        } else if (!state) {
          this.removeClass('alert-dismissible');
          this.removeClass('fade');
          this.removeClass('show');
          existing.destroy();
        }
      }
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('alert')) {
          return {
            type: 'alert'
          };
        }
      }
    }),
    view: $textView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/badge.js":
/*!******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/badge.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _config_color_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/color.js */ "./resources/theme/bootstrap4/src/js/config/color.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }



/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $textType = $domComponents.getType('text'),
      $textModel = $textType.model,
      $textView = $textType.view;
  $domComponents.addType('badge', {
    model: $textModel.extend({
      defaults: Object.assign({}, $textModel.prototype.defaults, {
        'custom-name': 'Badge',
        tagName: 'span',
        classes: ['badge'],
        traits: [{
          type: 'class_select',
          options: [{
            value: '',
            name: 'None'
          }].concat(_toConsumableArray(_config_color_js__WEBPACK_IMPORTED_MODULE_1__["default"].map(function (v) {
            return {
              value: "badge-".concat(v),
              name: underscore_string__WEBPACK_IMPORTED_MODULE_0___default.a.capitalize(v)
            };
          }))),
          label: 'Context'
        }, {
          type: 'class_select',
          options: [{
            value: '',
            name: 'Default'
          }, {
            value: 'badge-pill',
            name: 'Pill'
          }],
          label: 'Shape'
        }].concat($textModel.prototype.defaults.traits)
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('badge')) {
          return {
            type: 'badge'
          };
        }
      }
    }),
    view: $textView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/blockquotes.js":
/*!************************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/blockquotes.js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _config_btn_sizes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/btn-sizes */ "./resources/theme/bootstrap4/src/js/config/btn-sizes.js");


/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view;
  $domComponents.addType('blockquotes', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Blockquotes',
        tagName: 'blockquotes'
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['blockquotes'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'blockquotes'
          };
        }
      }
    }),
    view: $defaultView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/button-group.js":
/*!*************************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/button-group.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _config_btn_sizes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/btn-sizes */ "./resources/theme/bootstrap4/src/js/config/btn-sizes.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }



/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view;
  $domComponents.addType('button-group', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Button Group',
        tagName: 'div',
        classes: ['btn-group'],
        droppable: '.btn',
        attributes: {
          role: 'group'
        },
        traits: [{
          type: 'class_select',
          options: [{
            value: '',
            name: 'Default'
          }].concat(_toConsumableArray(Object.keys(_config_btn_sizes__WEBPACK_IMPORTED_MODULE_1__["default"]).map(function (k) {
            return {
              value: 'btn-group-' + k,
              name: _config_btn_sizes__WEBPACK_IMPORTED_MODULE_1__["default"][k]
            };
          }))),
          label: 'Size'
        }, {
          type: 'class_select',
          options: [{
            value: '',
            name: 'Horizontal'
          }, {
            value: 'btn-group-vertical',
            name: 'Vertical'
          }],
          label: 'Size'
        }, 'id', 'title', {
          type: 'Text',
          label: 'ARIA Label',
          name: 'aria-label',
          placeholder: 'A group of buttons'
        }]
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('btn-group')) {
          return {
            type: 'button-group'
          };
        }
      }
    }),
    view: $defaultView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/button.js":
/*!*******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/button.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _config_btn_color_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/btn-color.js */ "./resources/theme/bootstrap4/src/js/config/btn-color.js");
/* harmony import */ var _config_btn_sizes__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../config/btn-sizes */ "./resources/theme/bootstrap4/src/js/config/btn-sizes.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }




/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view,
      $linkType = $domComponents.getType('link'),
      $linkModel = $linkType.model,
      $linkView = $linkType.view;
  $domComponents.addType('button', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Button',
        tagName: 'button',
        droppable: false,
        attributes: {
          role: 'button'
        },
        traits: [{
          label: 'Element',
          type: 'select',
          options: [{
            value: 'button',
            name: 'Button'
          }, {
            value: 'a',
            name: 'Link'
          }],
          name: 'tagName',
          changeProp: 1
        }, {
          type: 'content',
          label: 'Text',
          name: 'button-content'
        }, {
          label: 'Type',
          type: 'select',
          name: 'type',
          options: [{
            value: 'button',
            name: 'Button'
          }, {
            value: 'submit',
            name: 'Submit'
          }, {
            value: 'reset',
            name: 'Reset'
          }]
        }, _config_btn_color_js__WEBPACK_IMPORTED_MODULE_1__["default"], {
          type: 'class_select',
          options: [{
            value: '',
            name: 'Default'
          }].concat(_toConsumableArray(Object.keys(_config_btn_sizes__WEBPACK_IMPORTED_MODULE_2__["default"]).map(function (k) {
            return {
              value: "btn-".concat(k),
              name: _config_btn_sizes__WEBPACK_IMPORTED_MODULE_2__["default"][k]
            };
          }))),
          label: 'Size',
          name: 'btn-size'
        }, {
          type: 'class_select',
          options: [{
            value: '',
            name: 'Inline'
          }, {
            value: 'btn-block',
            name: 'Block'
          }],
          label: 'Width',
          name: 'btn-width'
        }]
      }),
      init: function init() {
        this.listenTo(this.model, 'change:button-content', this.updateContent);
        this.listenTo(this, 'change:tagName', this.changeTagName);
      },
      updateContent: function updateContent() {
        this.el.innerHTML = this.model.get('content');
      },
      changeTagName: function changeTagName() {
        var $tagName = this.attributes.tagName;
        var component = $editor.getSelected();

        switch ($tagName) {
          case 'a':
            component.removeTrait('button-content');
            component.removeTrait('type');
            component.addTrait('target', {
              at: 1
            });
            component.addTrait({
              'name': 'href_button',
              'label': 'Link',
              'type': 'href_button'
            }, {
              at: 2
            });
            component.addTrait('href', {
              at: 3
            });
            component.setAttributes({
              href: '#'
            });
            break;

          default:
            component.removeTrait('target');
            component.removeTrait('href_button');
            component.removeTrait('href');
            component.addTrait({
              type: 'content',
              label: 'Text',
              name: 'button-content'
            }, {
              at: 1
            });
            component.addTrait({
              label: 'Type',
              type: 'select',
              name: 'type',
              options: [{
                value: 'button',
                name: 'Button'
              }, {
                value: 'submit',
                name: 'Submit'
              }, {
                value: 'reset',
                name: 'Reset'
              }],
              changeProp: 1
            }, {
              at: 2
            });
            component.setAttributes({
              role: 'button',
              type: 'button'
            });
            break;
        }
      }
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['button'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'button'
          };
        }
      }
    }),
    view: $defaultView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/card.js":
/*!*****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/card.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore */ "./node_modules/underscore/modules/index-all.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _config_color_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../config/color.js */ "./resources/theme/bootstrap4/src/js/config/color.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }




/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view,
      $imageType = $domComponents.getType('image'),
      $imageModel = $imageType.model,
      $imageView = $imageType.view;
  $domComponents.addType('card', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Card',
        classes: ['card'],
        traits: [{
          type: 'class_select',
          options: [{
            value: '',
            name: 'Default'
          }].concat(_toConsumableArray(_config_color_js__WEBPACK_IMPORTED_MODULE_2__["default"].map(function (v) {
            var $value;

            switch (v) {
              case 'light':
                $value = "bg-".concat(v);
                break;

              default:
                $value = "bg-".concat(v, " text-white");
                break;
            }

            return {
              value: $value,
              name: underscore_string__WEBPACK_IMPORTED_MODULE_1___default.a.capitalize(v)
            };
          }))),
          label: 'Background Color'
        }, {
          type: 'class_select',
          options: [{
            value: '',
            name: 'Default'
          }].concat(_toConsumableArray(_config_color_js__WEBPACK_IMPORTED_MODULE_2__["default"].map(function (v) {
            return {
              value: "border-".concat(v),
              name: underscore_string__WEBPACK_IMPORTED_MODULE_1___default.a.capitalize(v)
            };
          }))),
          label: 'Border Color'
        }, {
          type: 'select',
          label: 'Image Position',
          name: 'card-img',
          options: [{
            value: 'top',
            name: 'Top'
          }, {
            value: 'bottom',
            name: 'Bottom'
          }],
          changeProp: 1
        }, {
          type: 'checkbox',
          label: 'Header',
          name: 'card-header',
          changeProp: 1
        }, {
          type: 'checkbox',
          label: 'Body',
          name: 'card-body',
          changeProp: 1
        }, {
          type: 'checkbox',
          label: 'Footer',
          name: 'card-footer',
          changeProp: 1
        }].concat($defaultModel.prototype.defaults.traits)
      }),
      init: function init() {
        var $this = this;
        var children = this.components();
        var $cardElement = [];
        children.filter(function (comp) {
          var $attribute = comp.attributes.type;
          $cardElement.push($attribute);

          switch ($attribute) {
            case 'card-img':
              if ($cardElement.indexOf('card-img') > $cardElement.indexOf('card-body')) {
                $this.set($attribute, 'top');
              } else {
                $this.set($attribute, 'bottom');
              }

              break;

            default:
              $this.set($attribute, true);
              break;
          }
        });
        this.listenTo(this, 'change:card-img', this.cardImage);
        this.listenTo(this, 'change:card-header', this.cardHeader);
        this.listenTo(this, 'change:card-body', this.cardBody);
        this.listenTo(this, 'change:card-footer', this.cardFooter);
        this.components().comparator = 'card-order';
      },
      cardImage: function cardImage() {
        this.updateCardImage('card-img');
      },
      cardHeader: function cardHeader() {
        this.createCardComponent('card-header');
      },
      cardBody: function cardBody() {
        this.createCardComponent('card-body');
      },
      cardFooter: function cardFooter() {
        this.createCardComponent('card-footer');
      },
      updateCardImage: function updateCardImage(prop) {
        var position = this.get(prop);
        var children = this.components();
        var $component = children.filter(function (comp) {
          if (comp.attributes.type == 'card-img') {
            return comp;
          }
        })[0];

        if ($component) {
          $component.destroy();
        }

        switch (position) {
          case 'top':
            children.add({
              type: 'card-img',
              tagName: 'img',
              classes: ['card-img-top'],
              attributes: {
                src: 'https://via.placeholder.com/500'
              }
            }, {
              at: 0
            });
            break;

          case 'bottom':
            children.add({
              type: 'card-img',
              tagName: 'img',
              classes: ['card-img-top'],
              attributes: {
                src: 'https://via.placeholder.com/500'
              }
            }, {
              at: 5
            });
            break;
        }
      },
      createCardComponent: function createCardComponent(prop) {
        var state = this.get(prop);
        var type = prop;
        var children = this.components();
        var existing = children.filter(function (comp) {
          return comp.attributes.type === type;
        })[0];
        var comp, comp_children;

        if (state && !existing) {
          switch (prop) {
            case 'card-header':
              comp = children.add({
                type: type
              }, {
                at: 1
              });
              comp_children = comp.components();
              comp_children.add({
                type: 'header',
                tagName: 'h4',
                style: {
                  'margin-bottom': '0px'
                },
                content: 'Card Header'
              });
              break;

            case 'card-body':
              comp = children.add({
                type: type
              }, {
                at: 2
              });
              comp_children = comp.components();
              comp_children.add({
                type: 'header',
                tagName: 'h4',
                classes: ['card-title'],
                content: 'Card title'
              });
              comp_children.add({
                type: 'header',
                tagName: 'h6',
                classes: ['card-subtitle', 'text-muted', 'mb-2'],
                content: 'Card subtitle'
              });
              comp_children.add({
                type: 'text',
                tagName: 'p',
                classes: ['card-text'],
                content: "Some quick example text to build on the card title and make up the bulk of the card's content."
              });
              comp_children.add({
                type: 'link',
                classes: ['card-link', 'btn', 'btn-primary'],
                attributes: {
                  href: '#'
                },
                content: 'Card link'
              });
              break;

            case 'card-footer':
              comp = children.add({
                type: type
              }, {
                at: 3
              });
              comp_children = comp.components();
              comp_children.add({
                type: 'text',
                tagName: 'div',
                content: 'Card Footer'
              });
              break;
          }
        } else if (!state) {
          existing.destroy();
        }
      }
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('card')) {
          return {
            type: 'card'
          };
        }
      }
    }),
    view: $defaultView
  });
  $domComponents.addType('card-horizontal', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Card Horizontal',
        classes: ['card'],
        traits: [].concat($defaultModel.prototype.defaults.traits)
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('card')) {
          var $hasRow = false;
          var children = el.children;
          children.forEach(function (element) {
            if (element.classList && element.classList.contains('row')) {
              $hasRow = true;
            }
          });

          if ($hasRow) {
            return {
              type: 'card-horizontal'
            };
          }
        }
      }
    }),
    view: $defaultView
  });
  $domComponents.addType('card-overlay', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Card Overlay',
        classes: ['card']
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('card')) {
          var $overlay = false;
          var children = el.children;
          children.forEach(function (element) {
            if (element.classList && element.classList.contains('card-img-overlay')) {
              $overlay = true;
            }
          });

          if ($overlay) {
            return {
              type: 'card-overlay'
            };
          }
        }
      }
    }),
    view: $defaultView.extend({
      events: {
        'dblclick': 'openAssets'
      },
      openAssets: function openAssets(e) {
        $editor.runCommand('open-assets');
      }
    })
  });
  $domComponents.addType('card-img', {
    model: $imageModel.extend({
      defaults: Object.assign({}, $imageModel.prototype.defaults, {
        'custom-name': 'Card Image',
        classes: ['card-img'],
        attributes: {
          src: 'https://via.placeholder.com/500'
        },
        'card-order': 3
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && (el.classList.contains('card-img') || el.classList.contains('card-img-top'))) {
          return {
            type: 'card-img'
          };
        }
      }
    }),
    view: $imageView
  });
  $domComponents.addType('card-img-overlay', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Card Image Overlay',
        selectable: false,
        hoverable: false,
        classes: ['card-img-overlay'],
        'card-order': 4
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('card-img-overlay')) {
          return {
            type: 'card-img-overlay'
          };
        }
      }
    }),
    view: $defaultView
  });
  $domComponents.addType('card-header', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Card Header',
        classes: ['card-header'],
        'card-order': 2
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('card-header')) {
          return {
            type: 'card-header'
          };
        }
      }
    }),
    view: $defaultView
  });
  $domComponents.addType('card-body', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Card Body',
        classes: ['card-body'],
        'card-order': 5
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('card-body')) {
          return {
            type: 'card-body'
          };
        }
      }
    }),
    view: $defaultView
  });
  $domComponents.addType('card-footer', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Card Footer',
        classes: ['card-footer'],
        'card-order': 6
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('card-footer')) {
          return {
            type: 'card-footer'
          };
        }
      }
    }),
    view: $defaultView
  });
  $domComponents.addType('card-container', {
    model: $defaultModel.extend({
      defaults: Object.assign({}, $defaultModel.prototype.defaults, {
        'custom-name': 'Card Container',
        classes: ['card-group'],
        droppable: '.card',
        traits: [{
          type: 'class_select',
          options: [{
            value: 'card-group',
            name: 'Group'
          }, {
            value: 'card-deck',
            name: 'Deck'
          }, {
            value: 'card-columns',
            name: 'Columns'
          }],
          label: 'Layout'
        }].concat($defaultModel.prototype.defaults.traits)
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && underscore__WEBPACK_IMPORTED_MODULE_0__["default"].intersection(el.classList, ['card-group', 'card-deck', 'card-columns']).length) {
          return {
            type: 'card-container'
          };
        }
      }
    }),
    view: $defaultView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/column.js":
/*!*******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/column.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _config_background_color__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../config/background-color */ "./resources/theme/bootstrap4/src/js/config/background-color.js");
/* harmony import */ var _config_column_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/column.js */ "./resources/theme/bootstrap4/src/js/config/column.js");
/* harmony import */ var _trait_noChildren__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../trait/noChildren */ "./resources/theme/bootstrap4/src/js/trait/noChildren.js");



/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view;
  $domComponents.addType('column', {
    model: {
      defaults: {
        'custom-name': 'Column',
        tagName: 'div',
        draggable: '.row',
        droppable: true,
        selectable: true,
        hoverable: true,
        traits: [].concat(_config_column_js__WEBPACK_IMPORTED_MODULE_1__["default"]).concat(_config_background_color__WEBPACK_IMPORTED_MODULE_0__["default"])
      },
      init: function init() {
        Object(_trait_noChildren__WEBPACK_IMPORTED_MODULE_2__["default"])(this);
      }
    },
    isComponent: function isComponent(el) {
      var match = false;

      if (el && el.classList) {
        el.classList.forEach(function (klass) {
          if (klass == "col" || klass.match(/^col-/)) {
            match = true;
          }
        });
      }

      if (match) {
        return {
          type: 'column'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/container.js":
/*!**********************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/container.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _config_background_color__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../config/background-color */ "./resources/theme/bootstrap4/src/js/config/background-color.js");
/* harmony import */ var _trait_noChildren__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../trait/noChildren */ "./resources/theme/bootstrap4/src/js/trait/noChildren.js");


/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('container', {
    model: {
      defaults: {
        'custom-name': 'Container',
        tagName: 'div',
        draggable: 'section, footer',
        droppable: '.row',
        selectable: true,
        hoverable: true,
        copyable: false,
        removable: false,
        traits: [{
          type: 'checkbox',
          name: "fluid",
          changeProp: 1
        }, _config_background_color__WEBPACK_IMPORTED_MODULE_0__["default"]]
      },
      init: function init() {
        Object(_trait_noChildren__WEBPACK_IMPORTED_MODULE_1__["default"])(this);
        var $attr = this.attributes,
            $models = $attr.classes.models,
            $fluid = false;
        $models.forEach(function (element) {
          if (element.id == 'container-fluid') {
            $fluid = true;
          }
        });
        $attr.fluid = $fluid;
        var t = ["fluid"].map(function (t) {
          return "change:".concat(t);
        });
        this.listenTo(this, t.join(" "), this.updateClass), this.updateClass();
      },
      updateClass: function updateClass() {
        var $attr = this.attributes,
            $fluid = $attr.fluid;

        if ($fluid) {
          this.removeClass('container');
          this.addClass('container-fluid');
        } else {
          this.removeClass('container-fluid');
          this.addClass('container');
        }
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && (el.classList.contains('container') || el.classList.contains('container-fluid'))) {
        return {
          type: 'container'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/init.js":
/*!*****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/init.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _container_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./container.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/container.js");
/* harmony import */ var _row_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./row.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/row.js");
/* harmony import */ var _column_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./column.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/column.js");
/* harmony import */ var _card_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./card.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/card.js");
/* harmony import */ var _table_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./table.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/table.js");
/* harmony import */ var _alert_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./alert.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/alert.js");
/* harmony import */ var _blockquotes_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./blockquotes.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/blockquotes.js");
/* harmony import */ var _link_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./link.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/link.js");
/* harmony import */ var _button_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./button.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/button.js");
/* harmony import */ var _button_group_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./button-group.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/button-group.js");
/* harmony import */ var _badge_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./badge.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/badge.js");











/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  Object(_container_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
  Object(_row_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor);
  Object(_column_js__WEBPACK_IMPORTED_MODULE_2__["default"])($editor);
  Object(_card_js__WEBPACK_IMPORTED_MODULE_3__["default"])($editor);
  Object(_table_js__WEBPACK_IMPORTED_MODULE_4__["default"])($editor);
  Object(_alert_js__WEBPACK_IMPORTED_MODULE_5__["default"])($editor);
  Object(_blockquotes_js__WEBPACK_IMPORTED_MODULE_6__["default"])($editor);
  Object(_link_js__WEBPACK_IMPORTED_MODULE_7__["default"])($editor);
  Object(_button_js__WEBPACK_IMPORTED_MODULE_8__["default"])($editor);
  Object(_button_group_js__WEBPACK_IMPORTED_MODULE_9__["default"])($editor);
  Object(_badge_js__WEBPACK_IMPORTED_MODULE_10__["default"])($editor);
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/link.js":
/*!*****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/link.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _config_btn_color_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/btn-color.js */ "./resources/theme/bootstrap4/src/js/config/btn-color.js");
/* harmony import */ var _config_btn_sizes__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../config/btn-sizes */ "./resources/theme/bootstrap4/src/js/config/btn-sizes.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }




/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view,
      $linkType = $domComponents.getType('link'),
      $linkModel = $linkType.model,
      $linkView = $linkType.view;
  $domComponents.addType('link', {
    model: $linkModel.extend({
      defaults: Object.assign({}, $linkModel.prototype.defaults, {
        'custom-name': 'Link',
        tagName: 'a',
        traits: [{
          label: 'Type',
          type: 'select',
          options: [{
            value: 'button',
            name: 'Button'
          }, {
            value: 'a',
            name: 'Link'
          }],
          name: 'tagName',
          changeProp: 1
        }, 'target', {
          'name': 'href_button',
          'label': 'Link',
          'type': 'href_button'
        }, 'href', _config_btn_color_js__WEBPACK_IMPORTED_MODULE_1__["default"], {
          type: 'class_select',
          options: [{
            value: '',
            name: 'Default'
          }].concat(_toConsumableArray(Object.keys(_config_btn_sizes__WEBPACK_IMPORTED_MODULE_2__["default"]).map(function (k) {
            return {
              value: "btn-".concat(k),
              name: _config_btn_sizes__WEBPACK_IMPORTED_MODULE_2__["default"][k]
            };
          }))),
          label: 'Size',
          name: 'btn-size'
        }, {
          type: 'class_select',
          options: [{
            value: '',
            name: 'Inline'
          }, {
            value: 'btn-block',
            name: 'Block'
          }],
          label: 'Width',
          name: 'btn-width'
        }]
      }),
      init: function init() {
        this.listenTo(this.model, 'change:button-content', this.updateContent);
        this.listenTo(this, 'change:tagName', this.changeTagName);
      },
      updateContent: function updateContent() {
        this.el.innerHTML = this.model.get('content');
      },
      changeTagName: function changeTagName() {
        var $tagName = this.attributes.tagName;
        var component = $editor.getSelected();

        switch ($tagName) {
          case 'a':
            component.removeTrait('button-content');
            component.removeTrait('type');
            component.addTrait('target', {
              at: 1
            });
            component.addTrait({
              'name': 'href_button',
              'label': 'Link',
              'type': 'href_button'
            }, {
              at: 2
            });
            component.addTrait('href', {
              at: 3
            });
            component.setAttributes({
              href: '#'
            });
            break;

          default:
            component.removeTrait('target');
            component.removeTrait('href_button');
            component.removeTrait('href');
            component.addTrait({
              type: 'content',
              label: 'Text',
              name: 'button-content'
            }, {
              at: 1
            });
            component.addTrait({
              label: 'Type',
              type: 'select',
              name: 'type',
              options: [{
                value: 'button',
                name: 'Button'
              }, {
                value: 'submit',
                name: 'Submit'
              }, {
                value: 'reset',
                name: 'Reset'
              }],
              changeProp: 1
            }, {
              at: 2
            });
            component.setAttributes({
              role: 'button',
              type: 'button'
            });
            break;
        }
      }
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['a'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'link'
          };
        }
      }
    }),
    view: $linkView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/row.js":
/*!****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/row.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _config_background_color__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../config/background-color */ "./resources/theme/bootstrap4/src/js/config/background-color.js");
/* harmony import */ var _trait_noChildren__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../trait/noChildren */ "./resources/theme/bootstrap4/src/js/trait/noChildren.js");


/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('div-row', {
    model: {
      defaults: {
        'custom-name': 'Row',
        tagName: 'div',
        draggable: '.container, .card',
        droppable: '.col',
        selectable: true,
        hoverable: true,
        traits: [{
          type: 'checkbox',
          label: 'No Gutters',
          name: "noGutter",
          changeProp: 1
        }, _config_background_color__WEBPACK_IMPORTED_MODULE_0__["default"]]
      },
      init: function init() {
        Object(_trait_noChildren__WEBPACK_IMPORTED_MODULE_1__["default"])(this);
        var $attr = this.attributes,
            $models = $attr.classes.models,
            $noGutter = false;
        $models.forEach(function (element) {
          if (element.id == 'no-gutters') {
            $noGutter = true;
          }
        });
        $attr.noGutter = $noGutter;
        var t = ["noGutter"].map(function (t) {
          return "change:".concat(t);
        });
        this.listenTo(this, t.join(" "), this.updateClass), this.updateClass();
      },
      updateClass: function updateClass() {
        var $attr = this.attributes,
            $noGutter = $attr.noGutter;

        if ($noGutter) {
          this.addClass('no-gutters');
        } else {
          this.removeClass('no-gutters');
        }
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('row')) {
        return {
          type: 'div-row'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/bootstrap/table.js":
/*!******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/bootstrap/table.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore */ "./node_modules/underscore/modules/index-all.js");

/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view,
      //table
  $tableType = $domComponents.getType('table'),
      $tableModel = $tableType.model,
      $tableView = $tableType.view,
      //thead
  $theadType = $domComponents.getType('thead'),
      $theadModel = $theadType.model,
      $theadView = $theadType.view,
      //tbody
  $tbodyType = $domComponents.getType('tbody'),
      $tbodyModel = $tbodyType.model,
      $tbodyView = $tbodyType.view,
      //tfoot
  $tfootType = $domComponents.getType('tfoot'),
      $tfootModel = $tfootType.model,
      $tfootView = $tfootType.view,
      //tr
  $rowType = $domComponents.getType('row'),
      $rowModel = $rowType.model,
      $rowView = $rowType.view,
      //td, th
  $cellType = $domComponents.getType('cell'),
      $cellModel = $cellType.model,
      $cellView = $cellType.view;
  $domComponents.addType('table', {
    model: $tableModel.extend({
      defaults: Object.assign({}, $tableModel.prototype.defaults, {
        'custom-name': 'Table',
        classes: ['table'],
        traits: [{
          type: 'checkbox',
          label: 'Header',
          name: 'thead',
          changeProp: 1
        }, {
          type: 'checkbox',
          label: 'Footer',
          name: 'tfoot',
          changeProp: 1
        }, {
          type: 'checkbox',
          label: 'Dark',
          name: 'table-dark',
          changeProp: 1
        }, {
          type: 'checkbox',
          label: 'Hover',
          name: 'table-hover',
          changeProp: 1
        }, {
          type: 'checkbox',
          label: 'Small',
          name: 'table-sm',
          changeProp: 1
        }, {
          type: 'checkbox',
          label: 'Striped',
          name: 'table-striped',
          changeProp: 1
        }, {
          type: 'class_select',
          label: 'Border',
          options: [{
            value: '',
            name: 'Default'
          }, {
            value: 'table-bordered',
            name: 'Bordered'
          }, {
            value: 'table-borderless',
            name: 'Borderless'
          }]
        }] //.concat($tableModel.prototype.defaults.traits)

      }),
      init: function init() {
        var $this = this;
        var children = $this.components();
        var classes = $this.attributes.classes;
        children.filter(function (comp) {
          var $attribute = comp.attributes.type;
          $this.set($attribute, true);
        });
        classes.filter(function (comp) {
          var $class = comp.attributes.name;
          $this.set($class, true);
        });
        $this.listenTo(this, 'change:thead', this.tableThead);
        $this.listenTo(this, 'change:tfoot', this.tableTfoot);
        $this.listenTo(this, 'change:table-hover', this.tableHover);
        $this.listenTo(this, 'change:table-dark', this.tableDark);
        $this.listenTo(this, 'change:table-sm', this.tableSmall);
        $this.listenTo(this, 'change:table-striped', this.tableStriped);
      },
      tableThead: function tableThead() {
        this.createTableComponent('thead');
      },
      tableTfoot: function tableTfoot() {
        this.createTableComponent('tfoot');
      },
      tableHover: function tableHover() {
        this.updateClass('table-hover');
      },
      tableDark: function tableDark() {
        this.updateClass('table-dark');
      },
      tableSmall: function tableSmall() {
        this.updateClass('table-sm');
      },
      tableStriped: function tableStriped() {
        this.updateClass('table-striped');
      },
      createTableComponent: function createTableComponent(prop) {
        var $state = this.get(prop);
        var $children = this.components();
        var $tdCount = 0;
        $children.filter(function (comp) {
          var $type = comp.attributes.type;

          if ($type == 'tbody') {
            $tdCount = comp.components().length;
          }
        });
        var $existing = false;

        switch (prop) {
          case 'thead':
          case 'tfoot':
            $existing = $children.filter(function (comp) {
              return comp.attributes.type === prop;
            })[0];

            if ($state && !$existing) {
              var $tContainer = $children.add({
                type: prop,
                tagName: prop
              });
              var $tr = $tContainer.components();
              $tr.filter(function (comp) {
                var $classes = comp.attributes.classes;
                $classes.filter(function (comp1) {
                  var $class = comp1.attributes.name;
                  comp.removeClass($class);
                });
                comp.components("");

                for (var $i = 0; $i < $tdCount; $i++) {
                  var $th = comp.components().add({
                    type: 'cell',
                    tagName: 'th'
                  });
                  $th.components().add({
                    type: 'text',
                    tagName: 'div',
                    content: 'Title'
                  });
                }
              });
            } else if (!$state) {
              $existing.destroy();
            }

            break;
        }
      },
      updateClass: function updateClass(prop) {
        var $classes = this.attributes.classes;
        var $hasClass = false;
        $classes.filter(function (comp) {
          var $class = comp.attributes.name;

          if ($class == prop) {
            $hasClass = true;
          }
        });

        if ($hasClass) {
          this.removeClass(prop);
        } else {
          this.addClass(prop);
        }
      }
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['table'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'table'
          };
        }
      }
    }),
    view: $tableView
  });
  $domComponents.addType('thead', {
    model: $theadModel.extend({
      defaults: Object.assign({}, $theadModel.prototype.defaults, {
        'custom-name': 'Table Header'
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['thead'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'thead'
          };
        }
      }
    }),
    view: $theadView
  });
  $domComponents.addType('tbody', {
    model: $tbodyModel.extend({
      defaults: Object.assign({}, $tbodyModel.prototype.defaults, {
        'custom-name': 'Table Body'
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['tbody'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'tbody'
          };
        }
      }
    }),
    view: $tbodyView
  });
  $domComponents.addType('tfoot', {
    model: $tfootModel.extend({
      defaults: Object.assign({}, $tfootModel.prototype.defaults, {
        'custom-name': 'Table Footer',
        tagName: 'tfoot'
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['tfoot'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'tfoot'
          };
        }
      }
    }),
    view: $tfootView
  });
  $domComponents.addType('row', {
    model: $rowModel.extend({
      defaults: Object.assign({}, $rowModel.prototype.defaults, {
        'custom-name': 'Table Row',
        classes: null
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['tr'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'row'
          };
        }
      }
    }),
    view: $rowView
  });
  $domComponents.addType('cell', {
    model: $cellModel.extend({
      defaults: Object.assign({}, $cellModel.prototype.defaults, {
        'custom-name': 'Table Cell'
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['td', 'th'].includes(el.tagName.toLowerCase())) {
          return {
            type: 'cell'
          };
        }
      }
    }),
    view: $cellView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/container.js":
/*!*******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/container.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('header-container', {
    model: {
      defaults: {
        'custom-name': 'Container',
        tagName: 'div',
        draggable: false,
        droppable: false,
        selectable: true,
        hoverable: true,
        copyable: false,
        removable: false,
        traits: [{
          type: 'checkbox',
          name: "fluid",
          changeProp: 1
        }]
      },
      init: function init() {
        var $attr = this.attributes,
            $models = $attr.classes.models,
            $fluid = false;
        $models.forEach(function (element) {
          if (element.id == 'container-fluid') {
            $fluid = true;
          }
        });
        $attr.fluid = $fluid;
        var t = ["fluid"].map(function (t) {
          return "change:".concat(t);
        });
        this.listenTo(this, t.join(" "), this.updateClass), this.updateClass();
      },
      updateClass: function updateClass() {
        var $attr = this.attributes,
            $fluid = $attr.fluid;

        if ($fluid) {
          this.removeClass('container');
          this.addClass('container-fluid');
        } else {
          this.removeClass('container-fluid');
          this.addClass('container');
        }
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && (el.classList.contains('container') || el.classList.contains('container-fluid'))) {
        if (el.parentElement.tagName.toLowerCase() == 'nav') {
          return {
            type: 'header-container'
          };
        }
      }
    }
  });
  $domComponents.addType('header-nav-main', {
    model: {
      defaults: {
        draggable: '.container',
        droppable: '.navbar-nav',
        selectable: false,
        hoverable: false,
        copyable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.id && el.id == 'nav-main') {
        return {
          type: 'header-nav-main'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/dropdown.js":
/*!******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/dropdown.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $linkType = $domComponents.getType('link'),
      $linkModel = $linkType.model,
      $linkView = $linkType.view;
  $domComponents.addType('header-dropdown-ul', {
    model: {
      defaults: {
        'custom-name': 'UL',
        tagName: 'ul',
        draggable: false,
        droppable: 'li',
        selectable: false,
        hoverable: false,
        copyable: false,
        removable: false,
        traits: []
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('dropdown-menu')) {
        return {
          type: 'header-dropdown-ul'
        };
      }
    }
  });
  $domComponents.addType('header-dropdown-li', {
    model: {
      defaults: {
        'custom-name': 'Box',
        tagName: 'li',
        draggable: true,
        droppable: false,
        selectable: true,
        hoverable: true,
        traits: []
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.parentElement.classList && el.parentElement.classList.contains('dropdown-menu')) {
        return {
          type: 'header-dropdown-li'
        };
      }
    }
  });
  $domComponents.addType('header-dropdown-link', {
    model: $linkModel.extend({
      defaults: Object.assign({}, $linkModel.prototype.defaults, {
        'custom-name': 'Link',
        tagName: 'a',
        droppable: false,
        draggable: false,
        selectable: true,
        hoverable: true,
        removable: false,
        copyable: false,
        traits: [{
          'name': 'button',
          'label': 'Link',
          'type': 'href_button'
        }, 'href', 'title', 'target'],
        toolbar: []
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('dropdown-item') && el.closest(".dropdown-menu")) {
          return {
            type: 'header-dropdown-link'
          };
        }
      }
    }),
    view: $linkView
  });
  $domComponents.addType('header-dropdown-submenu', {
    model: {
      defaults: {
        'custom-name': 'Box',
        tagName: 'li',
        droppable: false,
        selectable: true,
        hoverable: true,
        draggable: "ul",
        script: function script() {
          var el = this;
          el.addEventListener('click', function (e) {
            var $path = e.path;

            if ($path[0] == el || $path[1] == el) {
              var $element;

              if ($path[0] == el) {
                $element = $path[0];
              } else {
                $element = $path[1];
              }

              var $sibling = $element.parentElement.firstChild;
              var $dropdown, $display;

              while ($sibling) {
                if ($sibling != $element) {
                  if ($sibling.classList && $sibling.classList.contains('dropdown-submenu')) {
                    $dropdown = $sibling.querySelector('.dropdown-menu');
                    $display = $dropdown.style.display;

                    if ($dropdown) {
                      if ($display == 'block') {
                        $dropdown.style.display = '';
                      }
                    }
                  }
                }

                $sibling = $sibling.nextSibling;
              }

              $dropdown = $element.querySelector('.dropdown-menu');
              $display = $dropdown.style.display;

              if ($dropdown) {
                if ($display == 'block') {
                  $display = '';
                } else {
                  $display = 'block';
                }

                $dropdown.style.display = $display;
              }
            }
          });
        }
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('dropdown-submenu')) {
        return {
          type: 'header-dropdown-submenu'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/init.js":
/*!**************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/init.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _root_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./root.js */ "./resources/theme/bootstrap4/src/js/dom/header/root.js");
/* harmony import */ var _nav_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./nav.js */ "./resources/theme/bootstrap4/src/js/dom/header/nav.js");
/* harmony import */ var _container_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./container.js */ "./resources/theme/bootstrap4/src/js/dom/header/container.js");
/* harmony import */ var _logo_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./logo.js */ "./resources/theme/bootstrap4/src/js/dom/header/logo.js");
/* harmony import */ var _ulli_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ulli.js */ "./resources/theme/bootstrap4/src/js/dom/header/ulli.js");
/* harmony import */ var _link_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./link.js */ "./resources/theme/bootstrap4/src/js/dom/header/link.js");
/* harmony import */ var _dropdown_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./dropdown.js */ "./resources/theme/bootstrap4/src/js/dom/header/dropdown.js");
/* harmony import */ var _megamenu_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./megamenu.js */ "./resources/theme/bootstrap4/src/js/dom/header/megamenu.js");
/* harmony import */ var _nav_right_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./nav-right.js */ "./resources/theme/bootstrap4/src/js/dom/header/nav-right.js");









/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  Object(_root_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
  Object(_nav_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor);
  Object(_container_js__WEBPACK_IMPORTED_MODULE_2__["default"])($editor);
  Object(_logo_js__WEBPACK_IMPORTED_MODULE_3__["default"])($editor);
  Object(_ulli_js__WEBPACK_IMPORTED_MODULE_4__["default"])($editor);
  Object(_link_js__WEBPACK_IMPORTED_MODULE_5__["default"])($editor);
  Object(_dropdown_js__WEBPACK_IMPORTED_MODULE_6__["default"])($editor);
  Object(_megamenu_js__WEBPACK_IMPORTED_MODULE_7__["default"])($editor);
  Object(_nav_right_js__WEBPACK_IMPORTED_MODULE_8__["default"])($editor);
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/link.js":
/*!**************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/link.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $linkType = $domComponents.getType('link'),
      $linkModel = $linkType.model,
      $linkView = $linkType.view;
  $domComponents.addType('navbar-link', {
    model: $linkModel.extend({
      defaults: Object.assign({}, $linkModel.prototype.defaults, {
        'custom-name': 'Link',
        tagName: 'a',
        draggable: false,
        selectable: true,
        hoverable: true,
        removable: false,
        copyable: false,
        traits: [{
          'name': 'button',
          'label': 'Link',
          'type': 'href_button'
        }, 'href', 'title', 'target'],
        toolbar: []
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.classList.contains('nav-link')) {
          return {
            type: 'navbar-link'
          };
        }
      }
    }),
    view: $linkView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/logo.js":
/*!**************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/logo.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $imageType = $domComponents.getType('image'),
      $imageModel = $imageType.model,
      $imageView = $imageType.view;
  $domComponents.addType('header-logo', {
    model: {
      defaults: {
        selectable: false,
        hoverable: false,
        copyable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('nav-logo')) {
        return {
          type: 'header-logo'
        };
      }
    }
  });
  $domComponents.addType('header-logo-link', {
    model: {
      defaults: {
        selectable: false,
        hoverable: false,
        copyable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('navbar-brand')) {
        return {
          type: 'header-logo-link'
        };
      }
    }
  });
  $domComponents.addType('header-logo-image', {
    model: $imageModel.extend({
      defaults: Object.assign({}, $imageModel.prototype.defaults, {
        'custom-name': 'Logo',
        tagName: 'img',
        draggable: false,
        selectable: true,
        hoverable: true,
        copyable: false,
        removable: false
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.tagName && ['img'].includes(el.tagName.toLowerCase())) {
          if (el.parentElement.classList.contains('navbar-brand')) {
            return {
              type: 'header-logo-image'
            };
          }
        }
      }
    }),
    view: $imageView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/megamenu.js":
/*!******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/megamenu.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _config_column_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../config/column.js */ "./resources/theme/bootstrap4/src/js/config/column.js");

/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view,
      $textType = $domComponents.getType('text'),
      $textModel = $textType.model,
      $textView = $textType.view,
      $linkType = $domComponents.getType('link'),
      $linkModel = $linkType.model,
      $linkView = $linkType.view,
      $imageType = $domComponents.getType('image'),
      $imageModel = $imageType.model,
      $imageView = $imageType.view;
  $domComponents.addType('header-megamenu-container', {
    model: {
      defaults: {
        'custom-name': 'Container',
        tagName: 'div',
        draggable: false,
        droppable: false,
        selectable: true,
        hoverable: true,
        traits: [{
          type: 'checkbox',
          name: "fluid",
          changeProp: 1
        }]
      },
      init: function init() {
        var $attr = this.attributes,
            $models = $attr.classes.models,
            $fluid = false;
        $models.forEach(function (element) {
          if (element.id == 'container-fluid') {
            $fluid = true;
          }
        });
        $attr.fluid = $fluid;
        var t = ["fluid"].map(function (t) {
          return "change:".concat(t);
        });
        this.listenTo(this, t.join(" "), this.updateClass), this.updateClass();
      },
      updateClass: function updateClass() {
        var $attr = this.attributes,
            $fluid = $attr.fluid;

        if ($fluid) {
          this.removeClass('container');
          this.addClass('container-fluid');
        } else {
          this.removeClass('container-fluid');
          this.addClass('container');
        }
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && (el.classList.contains('container') || el.classList.contains('container-fluid')) && el.parentElement.classList && el.parentElement.classList.contains('megamenu') && el.parentElement.classList.contains('dropdown-menu') && el.closest(".megamenu")) {
        return {
          type: 'header-megamenu-container'
        };
      }
    }
  });
  $domComponents.addType('header-megamenu-row', {
    model: {
      defaults: {
        'custom-name': 'Row',
        tagName: 'div',
        draggable: false,
        droppable: '.col-12',
        selectable: false,
        hoverable: false,
        traits: []
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('row') && el.closest(".megamenu")) {
        return {
          type: 'header-megamenu-row'
        };
      }
    }
  });
  $domComponents.addType('header-megamenu-column', {
    model: {
      defaults: {
        'custom-name': 'Column',
        tagName: 'div',
        draggable: '.row',
        droppable: true,
        selectable: true,
        hoverable: true,
        traits: _config_column_js__WEBPACK_IMPORTED_MODULE_0__["default"]
      }
    },
    isComponent: function isComponent(el) {
      var match = false;

      if (el && el.classList && el.closest(".megamenu")) {
        el.classList.forEach(function (klass) {
          if (klass == "col" || klass.match(/^col-/)) {
            match = true;
          }
        });
      }

      if (match) {
        return {
          type: 'header-megamenu-column'
        };
      }
    }
  });
  $domComponents.addType('header-megamenu-ul', {
    model: {
      defaults: {
        tagName: 'ul',
        selectable: false,
        hoverable: false,
        droppable: "li"
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && el.tagName.toLowerCase() == 'ul' && el.closest(".megamenu")) {
        return {
          type: 'header-megamenu-ul'
        };
      }
    }
  });
  $domComponents.addType('header-megamenu-li', {
    model: {
      defaults: {
        tagName: 'li',
        'custom-name': 'Box',
        selectable: true,
        hoverable: true,
        draggable: "ul"
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && el.tagName.toLowerCase() == 'li' && el.closest(".megamenu")) {
        return {
          type: 'header-megamenu-li'
        };
      }
    }
  });
  $domComponents.addType('header-megamenu-image', {
    model: $imageModel.extend({
      defaults: Object.assign({}, $imageModel.prototype.defaults, {
        'custom-name': 'Image',
        tagName: 'img',
        draggable: false,
        selectable: true,
        hoverable: true,
        removable: false,
        copyable: false,
        toolbar: []
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.tagName.toLowerCase() == 'img' && el.closest(".megamenu")) {
          return {
            type: 'header-megamenu-image'
          };
        }
      }
    }),
    view: $imageView
  });
  $domComponents.addType('header-megamenu-link', {
    model: $linkModel.extend({
      defaults: Object.assign({}, $linkModel.prototype.defaults, {
        'custom-name': 'Link',
        tagName: 'a',
        draggable: false,
        selectable: true,
        hoverable: true,
        removable: false,
        copyable: false,
        traits: [{
          'name': 'button',
          'label': 'Link',
          'type': 'href_button'
        }, 'href', 'title', 'target'],
        toolbar: []
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.classList && el.tagName.toLowerCase() == 'a' && el.parentElement.classList && el.parentElement.classList.contains('dropdown-item') && el.closest(".megamenu")) {
          return {
            type: 'header-megamenu-link'
          };
        }
      }
    }),
    view: $linkView
  });
  $domComponents.addType('header-megamenu-title', {
    model: $textModel.extend({
      defaults: Object.assign({}, $textModel.prototype.defaults, {
        'custom-name': 'Text',
        tagName: 'div',
        draggable: false,
        selectable: true,
        hoverable: true,
        removable: false,
        copyable: false,
        toolbar: []
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && el.parentElement.classList && el.parentElement.classList.contains('title') && el.closest(".megamenu")) {
          return {
            type: 'header-megamenu-title'
          };
        }
      }
    }),
    view: $textView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/nav-right.js":
/*!*******************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/nav-right.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('header-nav-right', {
    model: {
      defaults: {
        'custom-name': 'Right Nav',
        draggable: false,
        droppable: false,
        selectable: true,
        hoverable: true,
        copyable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('navbar-right')) {
        return {
          type: 'header-nav-right'
        };
      }
    }
  });
  $domComponents.addType('header-nav-right-search', {
    model: {
      defaults: {
        'custom-name': 'Search',
        draggable: '.navbar-nav',
        droppable: false,
        selectable: true,
        hoverable: true,
        copyable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('nav-item-search')) {
        return {
          type: 'header-nav-right-search'
        };
      }
    }
  });
  $domComponents.addType('header-nav-right-user', {
    model: {
      defaults: {
        'custom-name': 'User',
        draggable: '.navbar-nav',
        droppable: false,
        selectable: true,
        hoverable: true,
        copyable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('nav-item-user')) {
        return {
          type: 'header-nav-right-user'
        };
      }
    }
  });
  $domComponents.addType('header-nav-right-cart', {
    model: {
      defaults: {
        'custom-name': 'Cart',
        draggable: '.navbar-nav',
        droppable: false,
        selectable: true,
        hoverable: true,
        copyable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('nav-item-cart')) {
        return {
          type: 'header-nav-right-cart'
        };
      }
    }
  });
  $domComponents.addType('header-nav-icon', {
    model: {
      defaults: {
        draggable: false,
        droppable: false,
        selectable: false,
        hoverable: false,
        copyable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && (el.classList.contains('btn-search') || el.classList.contains('btn-user') || el.classList.contains('btn-cart'))) {
        return {
          type: 'header-nav-icon'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/nav.js":
/*!*************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/nav.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('header-nav', {
    model: {
      defaults: {
        'custom-name': 'Nav',
        tagName: 'nav',
        draggable: "header",
        droppable: false,
        selectable: false,
        hoverable: false
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && ['nav'].includes(el.tagName.toLowerCase())) {
        if (el.parentElement.tagName.toLowerCase() == 'header') {
          return {
            type: 'header-nav'
          };
        }
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/root.js":
/*!**************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/root.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('header-root', {
    extendFn: ['initToolbar'],
    model: {
      defaults: {
        'custom-name': 'Header',
        tagName: 'header',
        draggable: "#wrapper",
        droppable: "nav",
        selectable: true,
        hoverable: true,
        copyable: false,
        traits: [{
          type: 'header_class',
          options: [{
            value: 'bg-transparent',
            name: 'Transparent'
          }, {
            value: 'bg-white',
            name: 'White'
          }, {
            value: 'bg-primary',
            name: 'Blue'
          }, {
            value: 'bg-light',
            name: 'Light Gray'
          }, {
            value: 'bg-secondary',
            name: 'Dark Gray'
          }, {
            value: 'bg-success',
            name: 'Green'
          }, {
            value: 'bg-danger',
            name: 'Red'
          }, {
            value: 'bg-warning',
            name: 'Yellow'
          }, {
            value: 'bg-info',
            name: 'Light Blue'
          }, {
            value: 'bg-dark',
            name: 'Dark'
          }],
          label: 'Background Style'
        }]
      }
      /*
      initToolbar() {
          const { em } = this;
          const model = this;
          var tb = model.get('toolbar');
          tb.push({
              attributes: { class: 'fas fa-ellipsis-h' },
              command: 'select-db'
          });
          model.set('toolbar', tb);
      },
      */

    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && ['header'].includes(el.tagName.toLowerCase())) {
        return {
          type: 'header-root'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/header/ulli.js":
/*!**************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/header/ulli.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('header-nav-ul', {
    model: {
      defaults: {
        tagName: 'ul',
        selectable: false,
        hoverable: false,
        copyable: false,
        removable: false,
        draggable: false,
        droppable: "li"
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('navbar-nav')) {
        return {
          type: 'header-nav-ul'
        };
      }
    }
  });
  $domComponents.addType('header-nav-li', {
    model: {
      defaults: {
        'custom-name': 'Box',
        tagName: 'li',
        selectable: true,
        hoverable: true,
        draggable: "ul",
        script: function script() {
          var el = this;
          el.addEventListener('click', function (e) {
            var $path = e.path;
            var $div = el.parentElement.parentElement;
            var $li = $div.querySelectorAll('ul.navbar-nav > li');
            var $same = 0;
            var $dropdown;
            var $dropdownOpenedID = null;
            Array.from($li).forEach(function (element) {
              $dropdown = element.querySelector('.dropdown-menu');

              if ($dropdown) {
                if ($dropdown.style.display) {
                  $dropdownOpenedID = $dropdown.parentElement.id;
                }
              }
            });
            $dropdown = el.querySelector('.dropdown-menu');

            if ($dropdown) {
              var $display = $dropdown.style.display;
              Array.from($path).forEach(function (element) {
                if (element.id && $dropdownOpenedID && element.id == $dropdownOpenedID) {
                  $same = 1;
                }
              });

              if (!$same) {
                if ($display == 'block') {
                  $display = '';
                  $dropdownOpenedID = null;
                } else {
                  $display = 'block';
                  $dropdownOpenedID = $dropdown.parentElement.id;
                }

                $dropdown.style.display = $display;
                Array.from($li).forEach(function (element) {
                  if (el != element) {
                    $dropdown = element.querySelector('.dropdown-menu');

                    if ($dropdown) {
                      $dropdown.style.display = '';
                    }
                  }
                });
              } else {
                if (e.path[0] == el || e.path[1] == el) {
                  $display = '';
                  $dropdownOpenedID = null;
                  $dropdown.style.display = $display;
                }
              }
            } else {
              $dropdownOpenedID = null;
              Array.from($li).forEach(function (element) {
                $dropdown = element.querySelector('.dropdown-menu');

                if ($dropdown) {
                  $dropdown.style.display = '';
                }
              });
            }
          });
        }
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.classList && el.classList.contains('nav-item')) {
        return {
          type: 'header-nav-li'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/html/footer.js":
/*!**************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/html/footer.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('footer-root', {
    model: {
      defaults: {
        'custom-name': 'Footer',
        tagName: 'footer',
        draggable: "#wrapper",
        droppable: ".container",
        selectable: true,
        hoverable: true,
        copyable: false
        /*
        traits: [
            {
                type: 'header_class',
                options: [
                    {value: 'bg-transparent', name: 'Transparent'},
                    {value: 'bg-white', name: 'White'},
                    {value: 'bg-primary', name: 'Blue'},
                    {value: 'bg-light', name: 'Light Gray'},
                    {value: 'bg-secondary', name: 'Dark Gray'},
                    {value: 'bg-success', name: 'Green'},
                    {value: 'bg-danger', name: 'Red'},
                    {value: 'bg-warning', name: 'Yellow'},
                    {value: 'bg-info', name: 'Light Blue'},
                    {value: 'bg-dark', name: 'Dark'},
                ],
                label: 'Background Style',
            },
        ],
        */

      }
    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && ['footer'].includes(el.tagName.toLowerCase())) {
        return {
          type: 'footer-root'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/html/header.js":
/*!**************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/html/header.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _config_text_color__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../config/text-color */ "./resources/theme/bootstrap4/src/js/config/text-color.js");
/* harmony import */ var _config_text_size__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/text-size */ "./resources/theme/bootstrap4/src/js/config/text-size.js");
/* harmony import */ var _config_background_color__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../config/background-color */ "./resources/theme/bootstrap4/src/js/config/background-color.js");



/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $defaultType = $domComponents.getType('default'),
      $defaultModel = $defaultType.model,
      $defaultView = $defaultType.view,
      $textType = $domComponents.getType('text'),
      $textModel = $textType.model,
      $textView = $textType.view;
  $domComponents.addType('header', {
    model: $textModel.extend({
      defaults: Object.assign({}, $textModel.prototype.defaults, {
        'custom-name': 'Header',
        tagName: 'h1',
        traits: $defaultModel.prototype.defaults.traits.concat([{
          type: 'select',
          options: [{
            value: 'h1',
            name: 'H1 (largest)'
          }, {
            value: 'h2',
            name: 'H2'
          }, {
            value: 'h3',
            name: 'H3'
          }, {
            value: 'h4',
            name: 'H4'
          }, {
            value: 'h5',
            name: 'H5'
          }, {
            value: 'h6',
            name: 'H6(smallest)'
          }],
          label: 'Heading',
          name: 'tagName',
          changeProp: 1
        }, _config_text_size__WEBPACK_IMPORTED_MODULE_1__["default"], _config_text_color__WEBPACK_IMPORTED_MODULE_0__["default"], _config_background_color__WEBPACK_IMPORTED_MODULE_2__["default"]])
      })
    }, {
      isComponent: function isComponent(el) {
        if (el && ['H1', 'H2', 'H3', 'H4', 'H5', 'H6'].includes(el.tagName)) {
          return {
            type: 'header'
          };
        }
      }
    }),
    view: $textView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/html/init.js":
/*!************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/html/init.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _section_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./section.js */ "./resources/theme/bootstrap4/src/js/dom/html/section.js");
/* harmony import */ var _header_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./header.js */ "./resources/theme/bootstrap4/src/js/dom/html/header.js");
/* harmony import */ var _text_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./text.js */ "./resources/theme/bootstrap4/src/js/dom/html/text.js");
/* harmony import */ var _footer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./footer.js */ "./resources/theme/bootstrap4/src/js/dom/html/footer.js");
/* harmony import */ var _input_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./input.js */ "./resources/theme/bootstrap4/src/js/dom/html/input.js");

 //import link from './link.js';




/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  Object(_section_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
  Object(_header_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor); //link($editor);

  Object(_text_js__WEBPACK_IMPORTED_MODULE_2__["default"])($editor);
  Object(_footer_js__WEBPACK_IMPORTED_MODULE_3__["default"])($editor);
  Object(_input_js__WEBPACK_IMPORTED_MODULE_4__["default"])($editor);
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/html/input.js":
/*!*************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/html/input.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('input', {
    model: {
      defaults: {
        'custom-name': 'Input',
        tagName: 'input',
        draggable: false,
        droppable: false,
        selectable: true,
        hoverable: true,
        removable: false,
        copyable: false,
        traits: ['placeholder']
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && ['input'].includes(el.tagName.toLowerCase())) {
        return {
          type: 'input'
        };
      }
    }
  });
  $domComponents.addType('radio', {
    model: {
      defaults: {
        'custom-name': 'Radio',
        tagName: 'input',
        draggable: false,
        droppable: false,
        selectable: false,
        hoverable: false,
        removable: false,
        copyable: false,
        traits: []
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && ['input'].includes(el.tagName.toLowerCase()) && el.type && el.type == "radio") {
        return {
          type: 'radio'
        };
      }
    }
  });
  $domComponents.addType('select', {
    model: {
      defaults: {
        'custom-name': 'Select',
        tagName: 'select',
        draggable: false,
        droppable: false,
        selectable: true,
        hoverable: true,
        removable: false,
        copyable: false,
        traits: []
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && ['select'].includes(el.tagName.toLowerCase())) {
        return {
          type: 'select'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/html/section.js":
/*!***************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/html/section.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _config_background_color__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../config/background-color */ "./resources/theme/bootstrap4/src/js/config/background-color.js");
/* harmony import */ var _trait_noChildren__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../trait/noChildren */ "./resources/theme/bootstrap4/src/js/trait/noChildren.js");


/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  $domComponents.addType('section', {
    model: {
      defaults: {
        'custom-name': 'Section',
        tagName: 'section',
        draggable: "#wrapper",
        droppable: ".container",
        selectable: true,
        hoverable: true,
        copyable: true,
        traits: ['id', _config_background_color__WEBPACK_IMPORTED_MODULE_0__["default"]]
      },
      init: function init() {
        Object(_trait_noChildren__WEBPACK_IMPORTED_MODULE_1__["default"])(this);
      }
    },
    isComponent: function isComponent(el) {
      if (el && el.tagName && ['section'].includes(el.tagName.toLowerCase())) {
        return {
          type: 'section'
        };
      }
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/html/text.js":
/*!************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/html/text.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! underscore.string */ "./node_modules/underscore.string/index.js");
/* harmony import */ var underscore_string__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(underscore_string__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _config_background_color__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../config/background-color */ "./resources/theme/bootstrap4/src/js/config/background-color.js");
/* harmony import */ var _config_text_color__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../config/text-color */ "./resources/theme/bootstrap4/src/js/config/text-color.js");
/* harmony import */ var _config_text_size__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../config/text-size */ "./resources/theme/bootstrap4/src/js/config/text-size.js");
/* harmony import */ var _config_text_type__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../config/text-type */ "./resources/theme/bootstrap4/src/js/config/text-type.js");
/* harmony import */ var _trait_noChildren__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../trait/noChildren */ "./resources/theme/bootstrap4/src/js/trait/noChildren.js");






/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $domComponents = $editor.DomComponents;
  var $textType = $domComponents.getType('text'),
      $textModel = $textType.model,
      $textView = $textType.view;
  $domComponents.addType('text', {
    model: $textModel.extend({
      defaults: Object.assign({}, $textModel.prototype.defaults, {
        'custom-name': 'Text',
        traits: $textModel.prototype.defaults.traits.concat([_config_text_type__WEBPACK_IMPORTED_MODULE_4__["default"], _config_text_size__WEBPACK_IMPORTED_MODULE_3__["default"], _config_text_color__WEBPACK_IMPORTED_MODULE_2__["default"], _config_background_color__WEBPACK_IMPORTED_MODULE_1__["default"]])
      }),
      init: function init() {
        Object(_trait_noChildren__WEBPACK_IMPORTED_MODULE_5__["default"])(this, 'gjs-empty-text');
      }
    }),
    view: $textView
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/dom/init.js":
/*!*******************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/dom/init.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _bootstrap_init_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./bootstrap/init.js */ "./resources/theme/bootstrap4/src/js/dom/bootstrap/init.js");
/* harmony import */ var _header_init_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./header/init.js */ "./resources/theme/bootstrap4/src/js/dom/header/init.js");
/* harmony import */ var _html_init_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./html/init.js */ "./resources/theme/bootstrap4/src/js/dom/html/init.js");



/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  Object(_bootstrap_init_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
  Object(_header_init_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor);
  Object(_html_init_js__WEBPACK_IMPORTED_MODULE_2__["default"])($editor);
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/grapes.js":
/*!*****************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/grapes.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _block_init_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./block/init.js */ "./resources/theme/bootstrap4/src/js/block/init.js");
/* harmony import */ var _dom_init_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./dom/init.js */ "./resources/theme/bootstrap4/src/js/dom/init.js");
/* harmony import */ var _trait_init_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./trait/init.js */ "./resources/theme/bootstrap4/src/js/trait/init.js");
//block
 //dom

 //trait


/* harmony default export */ __webpack_exports__["default"] = (grapesjs.plugins.add('bootstrap4', function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $config = $option;
  var $blockManager = $editor.BlockManager; //block

  Object(_block_init_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor); //dom

  Object(_dom_init_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor); //trait

  Object(_trait_init_js__WEBPACK_IMPORTED_MODULE_2__["default"])($editor);
}));

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/trait/class_select.js":
/*!*****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/trait/class_select.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $traitManager = $editor.TraitManager;
  $traitManager.addType('class_select', {
    events: {
      'change': 'onChange' // trigger parent onChange method on input change

    },
    getInputEl: function getInputEl() {
      if (!this.inputEl) {
        var md = this.model;
        var opts = md.get('options') || [];
        var input = document.createElement('select');
        var target = this.target;
        var target_view_el = this.target.view.el;

        for (var i = 0; i < opts.length; i++) {
          var name = opts[i].name;
          var value = opts[i].value;

          if (value == '') {
            value = 'GJS_NO_CLASS';
          } // 'GJS_NO_CLASS' represents no class--empty string does not trigger value change


          var option = document.createElement('option');
          option.text = name;
          option.value = value;
          var value_a = value.split(' ');

          if (target_view_el.classList.contains(value)) {
            //if(_.intersection(target_view_el.classList, value_a).length == value_a.length) {
            option.setAttribute('selected', 'selected');
          }

          input.append(option);
        }

        this.inputEl = input;
      }

      return this.inputEl;
    },
    onValueChange: function onValueChange() {
      var $name = this.model.get('name');
      var classes = this.model.get('options').map(function (opt) {
        return opt.value;
      });

      for (var i = 0; i < classes.length; i++) {
        if (classes[i].length > 0) {
          var classes_i_a = classes[i].split(' ');

          for (var j = 0; j < classes_i_a.length; j++) {
            if (classes_i_a[j].length > 0) {
              this.target.removeClass(classes_i_a[j]);
            }
          }
        }
      }

      var value = this.model.get('value');

      if (value.length > 0 && value != 'GJS_NO_CLASS') {
        var value_a = value.split(' ');

        for (var _i = 0; _i < value_a.length; _i++) {
          this.target.addClass(value_a[_i]);
        }
      }

      switch ($name) {
        case 'btn-color':
          var $targetClasses = this.target.get('classes');

          if (value == 'GJS_NO_CLASS') {
            this.target.removeClass('btn');
            this.target.removeClass('btn-block');
            this.target.removeClass('btn-lg');
            this.target.removeClass('btn-sm');
          } else {
            var $hasBtn = false;

            for (var _i2 = 0; _i2 < $targetClasses.length; _i2++) {
              if ($targetClasses.models[_i2].id == 'btn') {
                $hasBtn = true;
              }
            }

            if (!$hasBtn) {
              this.target.addClass('btn');
            }
          }

          break;
      }

      this.target.em.trigger('component:toggled');
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/trait/content.js":
/*!************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/trait/content.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $traitManager = $editor.TraitManager,
      $textTrait = $traitManager.getType('text');
  $traitManager.addType('content', {
    events: {
      'keyup': 'onChange'
    },
    onValueChange: function onValueChange() {
      var md = this.model;
      var target = md.target;
      target.set('content', md.get('value'));
    },
    getInputEl: function getInputEl() {
      if (!this.inputEl) {
        this.inputEl = $textTrait.prototype.getInputEl.bind(this)();
        this.inputEl.value = this.target.get('content');
      }

      return this.inputEl;
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/trait/header_class.js":
/*!*****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/trait/header_class.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $traitManager = $editor.TraitManager;
  $traitManager.addType('header_class', {
    events: {
      'change': 'onChange' // trigger parent onChange method on input change

    },
    getInputEl: function getInputEl() {
      if (!this.inputEl) {
        var md = this.model;
        var opts = md.get('options') || [];
        var input = document.createElement('select');
        var target = this.target;
        var target_view_el = this.target.view.el;

        for (var i = 0; i < opts.length; i++) {
          var name = opts[i].name;
          var value = opts[i].value;

          if (value == '') {
            value = 'GJS_NO_CLASS';
          } // 'GJS_NO_CLASS' represents no class--empty string does not trigger value change


          var option = document.createElement('option');
          option.text = name;
          option.value = value;
          var value_a = value.split(' ');

          if (target_view_el.querySelector('nav').classList.contains(value)) {
            option.setAttribute('selected', 'selected');
          }

          input.append(option);
        }

        this.inputEl = input;
      }

      return this.inputEl;
    },
    onValueChange: function onValueChange() {
      var classes = this.model.get('options').map(function (opt) {
        return opt.value;
      });
      var $target = this.target;
      var $nav = this.target.attributes.components.at(0);

      for (var i = 0; i < classes.length; i++) {
        if (classes[i].length > 0) {
          var classes_i_a = classes[i].split(' ');

          for (var j = 0; j < classes_i_a.length; j++) {
            if (classes_i_a[j].length > 0) {
              //this.target.removeClass(classes_i_a[j]);
              $nav.removeClass(classes_i_a[j]);
            }
          }
        }
      }

      $target.removeClass('transparent-header');
      $nav.removeClass('navbar-dark');
      var value = this.model.get('value');

      if (value.length > 0 && value != 'GJS_NO_CLASS') {
        var value_a = value.split(' ');

        for (var _i = 0; _i < value_a.length; _i++) {
          $nav.addClass(value_a[_i]);

          switch (value_a[_i]) {
            case 'bg-primary':
            case 'bg-secondary':
            case 'bg-success':
            case 'bg-danger':
            case 'bg-info':
            case 'bg-dark':
              $nav.addClass('navbar-dark');
              break;

            case 'bg-transparent':
              $target.addClass('transparent-header');
              break;
          }
        }
      }

      this.target.em.trigger('component:toggled');
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/trait/href_button.js":
/*!****************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/trait/href_button.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  var $option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var $traitManager = $editor.TraitManager;
  $traitManager.addType('href_button', {
    events: {
      'click': function click(e) {
        $.ajax({
          type: 'GET',
          url: '/admin/url/manager',
          error: function error(xhr, textStatus) {
            console.log(xhr + ' ' + textStatus);
          },
          success: function success(data, textStatus, jqXHR) {
            $('.modal-main').html(data);
            $('#modal-url').modal();
          }
        });
      }
    },
    getInputEl: function getInputEl() {
      var button = document.createElement('button');
      button.classList.add('btn');
      button.classList.add('btn-primary');
      button.classList.add('btn-block');
      button.classList.add('btn-sm');
      button.type = 'button';
      button.innerHTML = "Find Link";
      return button;
    }
  });
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/trait/init.js":
/*!*********************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/trait/init.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _class_select_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./class_select.js */ "./resources/theme/bootstrap4/src/js/trait/class_select.js");
/* harmony import */ var _header_class_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./header_class.js */ "./resources/theme/bootstrap4/src/js/trait/header_class.js");
/* harmony import */ var _href_button_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./href_button.js */ "./resources/theme/bootstrap4/src/js/trait/href_button.js");
/* harmony import */ var _content_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./content.js */ "./resources/theme/bootstrap4/src/js/trait/content.js");




/* harmony default export */ __webpack_exports__["default"] = (function ($editor) {
  Object(_class_select_js__WEBPACK_IMPORTED_MODULE_0__["default"])($editor);
  Object(_header_class_js__WEBPACK_IMPORTED_MODULE_1__["default"])($editor);
  Object(_href_button_js__WEBPACK_IMPORTED_MODULE_2__["default"])($editor);
  Object(_content_js__WEBPACK_IMPORTED_MODULE_3__["default"])($editor);
});

/***/ }),

/***/ "./resources/theme/bootstrap4/src/js/trait/noChildren.js":
/*!***************************************************************!*\
  !*** ./resources/theme/bootstrap4/src/js/trait/noChildren.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function ($this) {
  var $class = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'gjs-empty';
  var children = $this.components();

  if (!children.length && !$this.attributes.content) {
    $this.addClass($class);
  } else {
    $this.removeClass($class);
  }

  $this.listenTo($this, 'change', function () {
    var children = $this.components();

    if (!children.length && !$this.attributes.content) {
      $this.addClass($class);
    } else {
      $this.removeClass($class);
    }
  });
});

/***/ }),

/***/ 0:
/*!***********************************************************!*\
  !*** multi ./resources/theme/bootstrap4/src/js/grapes.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/arch/resources/theme/bootstrap4/src/js/grapes.js */"./resources/theme/bootstrap4/src/js/grapes.js");


/***/ })

/******/ });