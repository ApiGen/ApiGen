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
 * CSS - SHL Language File
 *
 * V1.11 - fixed escaping to HTML via </ in OUT state (this solution is <!-- comment insensitive)
 * V1.12 - fixed comment in VALUE and DEF state
 */
class CSS_lang
{
	var $states;
	var $initial_state;
	var $keywords;
	var $version;
	var $signature;

	function CSS_lang()
	{
		$this->signature = "SHL";
		$this->version = "1.12";
		$this->initial_state = "OUT";
		$this->states = array(

			"OUT" => array (
				array(
						"_COUNTAB" =>	 	array("OUT",0),
						"{" =>				array("DEF",0),
						"." =>				array("CLASS",0),
						"/*" =>				array("COMMENT",0),
						"</" =>				array("_QUIT",0),
						"<?php" => 			array("TO_PHP",0),
						"<?" =>				array("TO_PHP",0),
					),
				0,
				null,
				null),

			"CLASS" => array (
				array(
						"SPACE" => 			array("_RET",1),
						"/*" =>				array("COMMENT",0),
						"{" => 				array("_RET",1),
					),
				PF_RECURSION,
				"css-class",
				null),

			"DEF" => array (
				array(
						":" => 				array("VALUE",1),
						"_COUNTAB" =>	 	array("DEF",0),
						";" => 				array("DEF",1), 	//1.12
						"}" => 				array("_RET",0),
						"/*" =>				array("COMMENT",0),
						"!SPACE" =>			array("PROPERTY",0),
					),
				PF_RECURSION,
				"",
				null),

					"PROPERTY" => array (
						array(
								"_COUNTAB" => 	array("PROPERTY",0),
								":" => 			array("_RET",1),
								"}" => 			array("_RET",1),
								"/*" =>			array("COMMENT",0),
							),
						PF_RECURSION,
						"css-property",
						null),

					"VALUE" => array (
						array(
								";" => 			array("_RET",1),
								"#" => 			array("COLOR",0),
								"}" => 			array("_RET",1),
								"_COUNTAB" => 	array("VALUE",0),
								"/*" =>			array("COMMENT",0),
							),
						PF_RECURSION,
						"css-value",
						null),

					"COLOR" => array (
						array(
								"!HEXNUM" => 	array("_RET",1),
							),
						PF_RECURSION,
						"css-color",
						null),


			"COMMENT" => array (
					array(
							"_COUNTAB" =>		array("COMMENT",0),
							"*/" => 			array("_RET",0),
						),
					PF_RECURSION,
					"css-comment",
					null),


			"TO_PHP" => array (null, PF_NEWLANG, "xlang", /* =style*/ "PHP" /*  =new language*/),
			"_QUIT" => array (null, PF_NEWLANG, "html-tag", /* =style*/ null, /* =new language*/)	//return to previous language
		);

// keywords
		$this->keywords = null;
	}
}
