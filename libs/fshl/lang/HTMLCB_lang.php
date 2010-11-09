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
 * HTML Curly Brackets (dgx) - SHL Language File
 *
 */
class HTMLCB_lang
{
	var $states;
	var $initial_state;
	var $keywords;
	var $version;
	var $signature;

	function HTMLCB_lang()
	{
		$this->signature = "SHL";
		$this->version = "1.11";
		$this->initial_state="OUT";
		$this->states = array(

			"OUT" => array (
				array(
						"<!--" =>		array("COMMENT",0),
						"{*" =>			array("COMMENT1",0),
						"<?php" =>		array("TO_PHP",0),
						"<?" =>			array("TO_PHP",0),
						"{" =>			array("TO_PHPCB",0),
						"<" =>			array("TAG",0),
						"&" =>			array("ENTITY",0),
						"_COUNTAB" =>	array("OUT",0),
						),
				0,
				null,
				null
				),

			"ENTITY" => array(
				array(
					";" =>			array("OUT",1),
					"&" =>			array("OUT",1),
					"SPACE" =>		array("OUT",1),
					),
				0,
				"html-entity",
				null),

			"TAG" => array (
				array(
					">" =>			array("OUT",1),
					"SPACE" =>		array("inTAG",0),
					"style" =>		array("CSS",1),				//zachovame farbu tagu
					"STYLE" =>		array("CSS",1),
					"script" =>		array("JAVASCRIPT",1),		//zachovame farbu tagu
					"SCRIPT" =>		array("JAVASCRIPT",1),
					"<?php" =>		array("TO_PHP",0),
					"<?" =>			array("TO_PHP",0),
					"{" =>			array("TO_PHPCB",0),
					),
				0,
				"html-tag",
				null),

			"inTAG" => array(
				array(
						"\"" =>			array("QUOTE1",0),
						">" => 			array("_RET",1),
						"'" => 			array("QUOTE2",0),
						"<?php" => 		array("TO_PHP",0),
						"<?" => 		array("TO_PHP",0),
						"{" =>			array("TO_PHPCB",0),
						"_COUNTAB" =>	array("inTAG",0),
						),
				PF_RECURSION,
				"html-tagin",
				null),

			//CSS je to iste ako inTAG az na male zmeny
			"CSS" => array(
				array(
						"\"" => 		array("QUOTE1",0),
						"'" => 			array("QUOTE2",0),
						">" => 			array("TO_CSS",0),
						"<?php" => 		array("TO_PHP",0),
						"<?" => 		array("TO_PHP",0),
						"{" =>			array("TO_PHPCB",0),
						"_COUNTAB" =>	array("inTAG",0),
						),
				PF_RECURSION,
				"html-tagin",
				null),

			//TO_CSS - port to CSS language.
			//Stav nie je virtualny, po navrate z jazyka CSS sa pouziju prechody...
			"TO_CSS" => array (
					array(
							">" => 			array("_RET",1),
							// RET preto, lebo stav CSS bol z TAG volany rekurzivne a treba tuto rekurziu niekde ukoncit (podobne ako TAGin)
							),
					PF_NEWLANG,
					"html-tag", /* =style*/
					"CSS", /* =new language*/
					),

			//JAVASCRIPT je to iste ako inTAG az na male zmeny
			"JAVASCRIPT" => array(
				array(
						"\"" => 		array("QUOTE1",0),
						"'" => 			array("QUOTE2",0),
						">" => 			array("TO_JAVASCRIPT",0),
						"<?php" => 		array("TO_PHP",0),
						"<?" => 		array("TO_PHP",0),
						"{" =>			array("TO_PHPCB",0),
						"_COUNTAB" =>	array("inTAG",0),
						),
				PF_RECURSION,
				"html-tagin",
				null),

			//TO_JAVACSRIPT - port to JS language.
			//Stav nie je virtualny, po navrate z jazyka JS sa pouziju prechody...
			"TO_JAVASCRIPT" => array (
					array(
							">" => 			array("_RET",1),
							),
					PF_NEWLANG,
					"html-tag", /* =style*/
					"JSCB", /* =new language*/
					),



			"QUOTE1" => array(
				array(
						"\"" =>			array("_RET",0),
						"<?php" =>		array("TO_PHP",0),
						"<?" =>			array("TO_PHP",0),
						"{" =>			array("TO_PHPCB",0),
						"_COUNTAB" =>	array("QUOTE1",0),
						),
				PF_RECURSION,
				"html-quote",
				null),

			"QUOTE2" => array(
				array(
						"'" => 			array("_RET",0),
						"<?php" => 		array("TO_PHP",0),
						"<?" => 		array("TO_PHP",0),
						"{" =>			array("TO_PHPCB",0),
						"_COUNTAB" =>	array("QUOTE2",0),
						),
				PF_RECURSION,
				"html-quote",
				null),

			"COMMENT" => array(
				array(
						"-->" =>	 	array("OUT",1),
						"<?php" =>		array("TO_PHP",0),
						"<?" =>			array("TO_PHP",0),
						"_COUNTAB" =>	array("COMMENT",0),
						),
				0,
				"html-comment",
				null),

			"COMMENT1" => array(
				array(
						"*}" =>	 	array("OUT",1),
						"_COUNTAB" =>	array("COMMENT1",0),
						),
				0,
				"html-comment",
				null),

			//TO_PHP - port to PHP language
			"TO_PHP" => array (
					//stav je virtualny - nie su definovane ziadne prechody (null), cize po navrate z PHP je nadalej
					//pouzivany stav, odkial bol TO_PHP volany
					null,
					PF_NEWLANG,
					"xlang", /* =style*/
					"PHP", /* =new language*/
					),

			"TO_PHPCB" => array (
					null,
					PF_NEWLANG,
					"xlang", /* =style*/
					"PHPCB", /* =new language*/
					),

		);

		$this->keywords=null;
	}
}
