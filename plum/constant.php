<?php
namespace Plum;

define('PARAM_INT',     0x1);
define('PARAM_FLOAT',   0x2);
define('PARAM_TEXT',    0x3);
define('PARAM_RAW',     0x4);
define('PARAM_BOOL',    0x5);

/** Set as powers of 2 so methods can be bitwise added. **/
/** There is also NO GET. Plum takes exclusive control of GET. **/
define('FROM_REQUEST', 0x0);
define('FROM_POST',    0x1);
define('FROM_COOKIE',  0x2);
