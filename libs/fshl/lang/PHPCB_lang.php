<?php
/*
 * FastSHL                              | Universal Syntax HighLighter |
 * ---------------------------------------------------------------------

   Copyright (C) 2002-2004  Juraj 'hvge' Durech

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 * ---------------------------------------------------------------------
 * PHP Curly Brackets (dgx) - SHL Language File
 *
 * Changes:
 *	V1.01	fixed QUOTE1
 *			added '{}' to VAR
 *  V1.20   removed nasty function list from keywords
 *  V1.21   added FUNCTION state and fixed some problems (md5 etc..)
 *  V1.22   fixed 'keyword_bug' in FUNCTION state with SAFECHAR transition
 *  V1.23   added transition ?> to COMMENT1
 *  V1.24   added # comments
 *  V1.25   fixed "\\" bug in QUOTE state
 *  V1.26	added nasty function list to keywords (ehmm..)
 *  V1.27   fixed "{$this->test}" variable inside string
 *          fixed '\\' bug in QUOTE1
 *          fixed <?php when PHP is not embeded
 *  V1.28   PHP5 keywords added
 *  V1.29   Fixed bug where methods was highlighted as variables
 *  V1.30   PHP 5.3 compatible
 */
class PHPCB_lang
{
	var $states;
	var $initial_state;
	var $keywords;
	var $version;
	var $signature;

	function PHPCB_lang()
	{
		$this->signature = "SHL";
		$this->version = "1.29";
		$this->initial_state = "OUT";
		$this->states = array(

			"OUT" => array (
				array(
						"_COUNTAB" => array("OUT",0),
						//"PHP_DELIM" => array("OUT",0),
						"$" => array("VAR",0),
						"ALPHA" => array("FUNCTION",-1),		// -1 - char back to stream
						"'" => array("QUOTE1",0),
						'"' => array("QUOTE",0),
						"NUMBER" => array("NUM",0),
						"?>" => array("_QUIT",0),
						"}" => array("_QUIT",0),
						"<?" => array("DUMMY_PHP",-1),
						),
				0,
				null,											// null = "normal"
				null),

			"DUMMY_PHP" => array(
				array(
						"<?php" => array("_RET",0),
						"<?" => array("_RET",0),
						),
				PF_RECURSION,
				"xlang",
				null),


			"FUNCTION" => array(
				array(
						"!SAFECHAR" => array("_RET",1),
						),
				PF_KEYWORD | PF_RECURSION,
				null,									// temporary php comment
				null),

			//rekurzivna implementacia var
			"VAR" => array(
				array(
						//"->" => array("VAR",0),	// "method as variable bug"
						'$' => array("VAR",0),
						'{' => array("VAR",0),
						"}" => array("_QUIT",-1),
						"!SAFECHAR" => array("_RET",1),	//char back to stream
						),
				PF_RECURSION,
				"php-var",
				null),

			//rekurzivna implementacia var
			"VAR_STR" => array(
				array(
						"}" 	=> array("_RET",0),
						"SPACE" => array("_RET",0),
						),
				PF_RECURSION,
				"php-var",
				null),


			//rekurzivna implementacia stringu
			"QUOTE" => array(
				array(
						'"' => array("_RET",0),
						'\\\\' => array("QUOTE",0),
						'\"' => array("QUOTE",0),
						'$' => array("VAR",0),
						'{$' => array("VAR_STR",0),
						"_COUNTAB" => array("QUOTE",0),
						),
				PF_RECURSION,
				"php-quote",
				null),

			"QUOTE1" => array(
				array(
						"'" => array("_RET",0),
						"\\\\" => array("QUOTE1",0),
						"\'" => array("QUOTE1",0),
						"_COUNTAB" => array("QUOTE1",0),
						),
				PF_RECURSION,
				"php-quote",
				null),

			//rekurzivna implementacia cisla
			"NUM" => array(
				array(
						"x" => array("HEX_NUM",0),
						"!NUMBER" => array("_RET",1),	//char back to stream
						"NUMBER" => array("DEC_NUM",0),
						),
				PF_RECURSION,
				"php-num",
				null),

			"DEC_NUM" => array(
				array(
						"!NUMBER" => array("_RET",1)	//char back to stream
						),
				0,
				"php-num",
				null),


			"HEX_NUM" => array(
				array(
						"!HEXNUM" => array("_RET",1)	//char back to stream
						),
				0,
				"php-num",
				null),

			"_QUIT" => array (null, PF_NEWLANG, "xlang", /* =style*/ "", /* =new language*/)

		);
// keywords
		$this->keywords = array(
			"php-keyword",
			array(
				"block" => 1,
				"snippet" => 1,
				"link" => 1,
				"plink" => 1,

				"abstract" => 1,
				"and" => 1,
				"array" => 1,
				"break" => 1,
				"case" => 1,
				"catch" => 1,
				"class" => 1,
				"clone" => 1,
				"const" => 1,
				"continue" => 1,
				"declare" => 1,
				"default" => 1,
				"do" => 1,
				"else" => 1,
				"elseif" => 1,
				"enddeclare" => 1,
				"endfor" => 1,
				"endforeach" => 1,
				"endif" => 1,
				"endswitch" => 1,
				"endwhile" => 1,
				"extends" => 1,
				"final" => 1,
				"for" => 1,
				"foreach" => 1,
				"function" => 1,
				"global" => 1,
				"goto" => 1,
				"if" => 1,
				"implements" => 1,
				"interface" => 1,
				"instanceof" => 1,
				"namespace" => 1,
				"new" => 1,
				"or" => 1,
				"private" => 1,
				"protected" => 1,
				"public" => 1,
				"static" => 1,
				"switch" => 1,
				"throw" => 1,
				"try" => 1,
				"use" => 1,
				"var" => 1,
				"while" => 1,
				"xor" => 1,
				"__CLASS__" => 1,
				"__DIR__" => 1,
				"__FILE__" => 1,
				"__FUNCTION__" => 1,
				"__METHOD__" => 1,
				"__NAMESPACE__" => 1,
				"die" => 1,
				"echo" => 1,
				"empty" => 1,
				"exit" => 1,
				"eval" => 1,
				"include" => 1,
				"include_once" => 1,
				"isset" => 1,
				"list" => 1,
				"require" => 1,
				"require_once" => 1,
				"return" => 1,
				"print" => 1,
				"unset" => 1,

				// types
				"true" => 1,
				"false" => 1,
				"null" => 1,
			),
			false	// case non sensitive
		);
	}
}
