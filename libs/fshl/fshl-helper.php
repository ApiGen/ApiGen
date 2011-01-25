<?php
/*
 * FastSHL                              | Universal Syntax HighLighter |
 * ---------------------------------------------------------------------

   Copyright (C) 2002-2005  Juraj 'hvge' Durech

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
 */

// ---------------------------------------------------------------------
// new experimental code grabbed from FSHL 0.V.x (0.5.0)
// ---------------------------------------------------------------------
class fshlHelper {
	function getStringSource($var) {
		if(!is_string($var)) {
			return fshlHelper::getVarContentSource($var);
		}
		return '"' . strtr($var, array('$'=>'\$','"'=>'\"','\\'=>'\\\\',"\x00"=>'\x00',"\x01"=>'\x01',"\x02"=>'\x02',"\x03"=>'\x03',"\x04"=>'\x04',"\x05"=>'\x05',"\x06"=>'\x06',"\x07"=>'\x07',"\x08"=>'\x08',"\x09"=>'\x09',"\x0a"=>'\x0a',"\x0b"=>'\x0b',"\x0c"=>'\x0c',"\x0d"=>'\x0d',"\x0e"=>'\x0e',"\x0f"=>'\x0f',"\x10"=>'\x10',"\x11"=>'\x11',"\x12"=>'\x12',"\x13"=>'\x13',"\x14"=>'\x14',"\x15"=>'\x15',"\x16"=>'\x16',"\x17"=>'\x17',"\x18"=>'\x18',"\x19"=>'\x19',"\x1a"=>'\x1a',"\x1b"=>'\x1b',"\x1c"=>'\x1c',"\x1d"=>'\x1d',"\x1e"=>'\x1e',"\x1f"=>'\x1f',"\x7f"=>'\x7f',"\x80"=>'\x80',"\x81"=>'\x81',"\x82"=>'\x82',"\x83"=>'\x83',"\x84"=>'\x84',"\x85"=>'\x85',"\x86"=>'\x86',"\x87"=>'\x87',"\x88"=>'\x88',"\x89"=>'\x89',"\x8a"=>'\x8a',"\x8b"=>'\x8b',"\x8c"=>'\x8c',"\x8d"=>'\x8d',"\x8e"=>'\x8e',"\x8f"=>'\x8f',"\x90"=>'\x90',"\x91"=>'\x91',"\x92"=>'\x92',"\x93"=>'\x93',"\x94"=>'\x94',"\x95"=>'\x95',"\x96"=>'\x96',"\x97"=>'\x97',"\x98"=>'\x98',"\x99"=>'\x99',"\x9a"=>'\x9a',"\x9b"=>'\x9b',"\x9c"=>'\x9c',"\x9d"=>'\x9d',"\x9e"=>'\x9e',"\x9f"=>'\x9f',"\xa0"=>'\xa0',"\xa1"=>'\xa1',"\xa2"=>'\xa2',"\xa3"=>'\xa3',"\xa4"=>'\xa4',"\xa5"=>'\xa5',"\xa6"=>'\xa6',"\xa7"=>'\xa7',"\xa8"=>'\xa8',"\xa9"=>'\xa9',"\xaa"=>'\xaa',"\xab"=>'\xab',"\xac"=>'\xac',"\xad"=>'\xad',"\xae"=>'\xae',"\xaf"=>'\xaf',"\xb0"=>'\xb0',"\xb1"=>'\xb1',"\xb2"=>'\xb2',"\xb3"=>'\xb3',"\xb4"=>'\xb4',"\xb5"=>'\xb5',"\xb6"=>'\xb6',"\xb7"=>'\xb7',"\xb8"=>'\xb8',"\xb9"=>'\xb9',"\xba"=>'\xba',"\xbb"=>'\xbb',"\xbc"=>'\xbc',"\xbd"=>'\xbd',"\xbe"=>'\xbe',"\xbf"=>'\xbf',"\xc0"=>'\xc0',"\xc1"=>'\xc1',"\xc2"=>'\xc2',"\xc3"=>'\xc3',"\xc4"=>'\xc4',"\xc5"=>'\xc5',"\xc6"=>'\xc6',"\xc7"=>'\xc7',"\xc8"=>'\xc8',"\xc9"=>'\xc9',"\xca"=>'\xca',"\xcb"=>'\xcb',"\xcc"=>'\xcc',"\xcd"=>'\xcd',"\xce"=>'\xce',"\xcf"=>'\xcf',"\xd0"=>'\xd0',"\xd1"=>'\xd1',"\xd2"=>'\xd2',"\xd3"=>'\xd3',"\xd4"=>'\xd4',"\xd5"=>'\xd5',"\xd6"=>'\xd6',"\xd7"=>'\xd7',"\xd8"=>'\xd8',"\xd9"=>'\xd9',"\xda"=>'\xda',"\xdb"=>'\xdb',"\xdc"=>'\xdc',"\xdd"=>'\xdd',"\xde"=>'\xde',"\xdf"=>'\xdf',"\xe0"=>'\xe0',"\xe1"=>'\xe1',"\xe2"=>'\xe2',"\xe3"=>'\xe3',"\xe4"=>'\xe4',"\xe5"=>'\xe5',"\xe6"=>'\xe6',"\xe7"=>'\xe7',"\xe8"=>'\xe8',"\xe9"=>'\xe9',"\xea"=>'\xea',"\xeb"=>'\xeb',"\xec"=>'\xec',"\xed"=>'\xed',"\xee"=>'\xee',"\xef"=>'\xef',"\xf0"=>'\xf0',"\xf1"=>'\xf1',"\xf2"=>'\xf2',"\xf3"=>'\xf3',"\xf4"=>'\xf4',"\xf5"=>'\xf5',"\xf6"=>'\xf6',"\xf7"=>'\xf7',"\xf8"=>'\xf8',"\xf9"=>'\xf9',"\xfa"=>'\xfa',"\xfb"=>'\xfb',"\xfc"=>'\xfc',"\xfd"=>'\xfd',"\xfe"=>'\xfe',"\xff"=>'\xff'))
		  . '"';
	}

	function getVarContentSource($var)
	{
		if(is_numeric($var)) {
			return $var;
		}
		if(is_null($var)) {
			return 'null';
		}
		if(is_bool($var)) {
			return $var ? 'true' : 'false';
		}
		if(is_string($var)) {
			return fshlHelper::getStringSource($var);
		}
		if(is_array($var)) {
			$array = 'array(';
			$tmp = ""; $cnt = 0;
			foreach($var as $key => $value) {
				$tmp .= fshlHelper::getVarContentSource($key).'=>'.fshlHelper::getVarContentSource($value);
				$tmp .= ++$cnt < count($var) ? ',' : '';
			}
			$tmp.=')';
			return $array.$tmp;
		}
		assert(0);
		return 'fshlHelper::getVarContentSource error';
	}

	function getVarSource($varname, $mixed_var) {
		return '$'.$varname."=".fshlHelper::getVarContentSource($mixed_var).";\n";
	}

	function getFncSource($fncname,$param=null) {
		return "function $fncname ($param) {\n";
	}

	function blockIndent($string, $level) {
		$tab = str_repeat("  ", $level);
		$lines = explode("\n", $string);
		$out = null;
		foreach($lines as $line) {
			$out .= $tab.$line."\n";
		}
		return $out;
	}

} //class fshlHelper


// ---------------------------------------------------------------------
// old style wrappers for generator version < 0.5.0 compatibility
// ---------------------------------------------------------------------
function get_string_source($var, $human_readable = false)
{
	return fshlHelper::getStringSource($var, $human_readable);
}

function get_array_source($var)
{
	return fshlHelper::getVarContentSource($var);
}

function get_var_source($varname,$var)
{
	return fshlHelper::getVarSource($varname, $var);
}

function get_fnc_source($fncname,$param=null)
{
	return fshlHelper::getFncSource($fncname,$param);
}
