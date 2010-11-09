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
 * HTMLonly - SHL Language File
 *
 *
 *
 */
class HTMLonly_lang
{
	var $states;
	var $initial_state;
	var $keywords;
	var $version;
	var $signature;

	function HTMLonly_lang()
	{
		$this->signature = "SHL";
		$this->version = "1.10";
		$this->initial_state="OUT";
		$this->states = array(

			"OUT" => array (
				array(
						"<!--" => 		array("COMMENT",0),
						"<" => 			array("TAG",0),
						"&" =>			array("ENTITY",0),
						"_COUNTAB" => 	array("OUT",0),
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
						">" => 			array("OUT",1),
						"SPACE" => 		array("inTAG",0),
						),
				0,
				"html-tag",
				null),

			"inTAG" => array(
				array(
						"\"" => 		array("QUOTE1",0),
						">" => 			array("OUT",1),
						"_COUNTAB" => 	array("inTAG",0),
						"'" => 			array("QUOTE2",0),
						),
				0,
				"html-tagin",
				null),

			"QUOTE1" => array(
				array(
						"\"" => array("inTAG",0),
						),
				0,
				"html-quote",
				null),

			"QUOTE2" => array(
				array(
						"'" => 	array("inTAG",0),
						),
				0,
				"html-quote",
				null),

			"COMMENT" => array(
				array(
						"-->" => 		array("OUT",1),
						"_COUNTAB" => 	array("COMMENT",0),
						),
				0,
				"html-comment",
				null),
		);

		$this->keywords=null;
	}
}
