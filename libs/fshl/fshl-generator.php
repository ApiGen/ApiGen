<?php
/*
 * FastSHL                              | Universal Syntax HighLighter |
 * ---------------------------------------------------------------------

   Copyright (C) 2002-2006  Juraj 'hvge' Durech

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
 * fshl-generator.php   - language code generator
 *
 *
 * Version      Changes                                         Authors
 * ---------------------------------------------------------------------
 *  0.4.0     - TW/SHL signature support                        hvge
 *            - added print_error()
 *
 *  0.4.1     - better '_ALL' condition optimalization          hvge
 *            - In some cases generator producing faster code
 *              in variable $xXX initialization section.*)
 *
 *            - experimental ctype library support
 *              In this file set manually FSHL_USE_CTYPE
 *              to true, and generator produce code, which
 *              using ctype library. This library is available
 *              in PHP4.2.0 and greater by default. See PHP
 *              documentation for details.
 *
 *  0.4.2     - fixed some ctype bugs	                        hvge
 *
 *  0.4.3     - new group delimiter _COUNTAB					hvge
 *            - CTYPE is default
 *
 *  0.4.4     - new group delimiters: SAFECHAR, !SAFECHAR
 *
 *  0.4.5     - arrays with string indexes was completely		hvge
 *				removed
 *
 *  0.4.6     - new experimental fshl-helper.php
 *
 *  0.4.7     - statistic mode was added
 *
 *  0.4.8     - NEW optimization, without backward compatibility
 *              This generator produces lexers for FSHL parser
 *              latest than V0.4.14 and newer.
 *              isdXX functions was replaced with getwXX()
 *
 *  0.4.9     - Johno's optimizations
 *
 *  0.4.10    - Added support for case (non) sensitivity in grammars
 *            - Support for group delimiters with variable length
 *            - New group delimiter DOT_NUMBER
 *
 *  0.4.11    - Fixed bug where group delimiter with variable length returns
 *			    incorrect text size.
 */

define('FSHL_GENERATOR_VERSION',	'0.4.11' );
define('FSHL_USE_CTYPE',			true  );

if(!defined('FSHL_PATH')) {
	define('FSHL_PATH', dirname(__FILE__).'/');		// thanx to martin*cohen for this great hack:)
}

// Group delimiters
$group_delimiters=array(
	// delimiter name	required size for compare
	"SPACE"		=>		1,
	"!SPACE"	=>		1,
	"NUMBER"	=>		1,
	"!NUMBER"	=>		1,
	"ALPHA"		=>		1,
	"!ALPHA"	=>		1,
	"ALNUM"		=>		1,
	"!ALNUM"	=>		1,
	"HEXNUM"	=>		1,
	"!HEXNUM"	=>		1,
	"SAFECHAR"	=>		1,
	"!SAFECHAR"	=>		1,
	"_ALL"		=>		1,
	"_COUNTAB"	=>		1,	// line counter & Tab indent delimiter ('\n' || '\t')
	"DOT_NUMBER"  	=>	2,	// match ".N" where N is number
	"!DOT_NUMBER"	=>	2,

	// TODO: Add special language depended groups here.
	//       See function shlParser::isdelimiter()
	//       or fshlGenerator::get_ctype_condition() or get_older_condition()
	"PHP_DELIM"	=>		1,
);

$fshl_signatures = array("SHL","TW");

require_once(FSHL_PATH.'fshl-config.php');
require_once(FSHL_PATH.'fshl-helper.php');

class fshlGenerator
{
	// class variables
	var $lang, $flang, $options, $signature, $version, $langname, $language;
	var $out, $groups;

	var $_trans, $_flags, $_data, $_delim, $_class, $_states, $_names;
	var $_ret,$_quit;

	var $error;
	var $inject_statistic_code;

	// USER LEVEL functions
	//
	// class constructor
	function fshlGenerator($language,$options=P_DEFAULT)
	{
		global $group_delimiters;
		global $fshl_signatures;

		$this->error = false;
		$this->language = $language;
		$this->langname = $language."_lang";
		$lang_filename = FSHL_LANG."$this->langname.php";
		$this->inject_statistic_code = $options & P_STATISTIC;
		if(file_exists($lang_filename)) {
			require_once ($lang_filename);
			$this->lang = new $this->langname;
			$this->options  = $options;
			$this->groups = &$group_delimiters;
			$this->signature = $this->lang->signature;
			$this->version = $this->lang->version;
			if(!in_array($this->signature,$fshl_signatures))
			{
				$this->print_error("Unknown signature '<b>$this->signature</b>'.");
				return;
			}
			if($this->language_array_optimise()) {
				return;
			}
			$this->make();
		} else {
			$this->print_error("Language file '<b>$lang_filename</b>' not found.");
		}
	}

	function get_source()
	{
		if($this->is_error())
			return null;
		else
			return $this->out;
	}

	function is_error()
	{
		return $this->error;
	}

	function write($filename=null)
	{
		if($filename==null)
			$filename=FSHL_CACHE."$this->langname.php";
		if($this->is_error())
		{
			$this->print_error("File '<b>$filename</b>' not saved.");
			return;
		}
		$filedes = fopen($filename,"w");
		fwrite($filedes,$this->out,strlen($this->out));
		fclose($filedes);
	}

	//
	// LOW LEVEL FUNCTIONS
	//
	function print_error($text)
	{
		print ("fshl-generator: <b>".FSHL_LANG."$this->langname.php:</b> $text\n");
		$this->error = true;
	}

	function make()
	{
		$this->out = null;

		// make source
		$this->out.="<?php\n";
		$this->out.="/* --------------------------------------------------------------- *\n";
		$this->out.=" *        WARNING: ALL CHANGES IN THIS FILE WILL BE LOST\n *\n";
		$this->out.=" *   Source language file: ".FSHL_LANG.$this->langname.".php\n";
		$this->out.=" *       Language version: $this->version (Sign:$this->signature)\n *\n";
		$this->out.=" *            Target file: ".FSHL_CACHE.$this->langname.".php\n";
		$this->out.=" *             Build date: ".date("D j.n.Y H:i:s")."\n *\n";
		$this->out.=" *      Generator version: ".FSHL_GENERATOR_VERSION."\n";
		$this->out.=" * --------------------------------------------------------------- */\n";

		// make class
		$this->out.="class $this->langname";

		if($this->signature=="TW")
			$this->out.=" extends ".$this->language."_base";

		$this->out.="\n{\n";
		$this->out.='var $trans,$flags,$data,$delim,$class,$keywords;'."\n";
		$this->out.='var $version,$signature,$initial_state,$ret,$quit;'."\n";
		$this->out.='var $pt,$pti,$generator_version;'."\n";
		$this->out.='var $names;'."\n";

		if($this->inject_statistic_code) {
			$this->out.='var $statistic, $total_statistic;'."\n";
		}

		$this->out.="\n";

		// make constructor
		$this->out.=get_fnc_source($this->langname);

		// make class variables
		$this->out.="\t".get_var_source("this->version",$this->version);
		$this->out.="\t".get_var_source("this->signature",$this->signature);
		$this->out.="\t".get_var_source("this->generator_version",FSHL_GENERATOR_VERSION);
		$this->out.="\t".get_var_source("this->initial_state",$this->lang->initial_state);
		$this->out.="\t".get_var_source("this->trans",$this->_trans);
		$this->out.="\t".get_var_source("this->flags",$this->_flags);
		$this->out.="\t".get_var_source("this->delim",$this->_delim);
		$this->out.="\t".get_var_source("this->ret",$this->_ret);
		$this->out.="\t".get_var_source("this->quit",$this->_quit);
		$this->out.="\t".get_var_source("this->names",$this->_names);
		if($this->signature!="TW") {
			$this->out.="\t".get_var_source("this->data",$this->_data);
			$this->out.="\t".get_var_source("this->class",$this->_class);
			$this->out.="\t".get_var_source("this->keywords",$this->lang->keywords);
		}
		if($this->inject_statistic_code) {
			$this->out.="\t".get_var_source("this->statistic",array());
			$this->out.="\t".get_var_source("this->total_statistic",array());
		}

		//end constructor
		$this->out.="}\n\n";

		// make ISDx() functions

		foreach($this->_delim as $state=>$delim)
			if($delim==null)
				continue;
			else
				$this->make_state_code($state);

		$this->out.="}\n";	//end class
		$this->out.="?>";	//end source <? (hack for PSpad :))
	}

	function inject_statistic_code($state, $hit, $tab = true) {
		if($this->inject_statistic_code) {
			$tab = $tab ? "\t\t\t" : "\t\t";
			$code_piece = '$this->statistic['.$state.']['.$hit.']';
			$this->out .= $tab."if(isset($code_piece)) { $code_piece++; }else{ $code_piece=1; }\n";
		} else {
			return;
		}
	}
/*
// code template
function getw4 (&$s, $i, $l) {
	$o = null;
	while($i < $l) {
		$c1=$s[$i++];
		if(!ctype_xdigit($c1)){
			return array(0,$c1,$o);
		}
		$o.=$c1;
	}
	return array(-1,-1,$o);
}
*/

	function make_state_code($state)
	{
		$statename=array_keys($this->_states,$state);
		$tab = "\t"; $tab2 = "\t\t"; $tab3 = "\t\t\t";
		$nl = "\n";
		$this->out.="// $statename[0]\n";
		$this->out.=fshlHelper::getFncSource("getw$state", '&$s, $i, $l');

		//
		// generate local variables initialization
		//
		$max = 1;
		$var_init = null;
		$transitions = 0;
		$all_break = false;
		foreach($this->_delim[$state] as $del)
		{
			$transitions++;
			if(isset($this->groups[$del])) {
				// group delimiter
				if($del == '_ALL') {
					$all_break = true;
					$tab3 = $tab2;
					$tab2 = $tab;
				}
				// length for group delims is stored in array now
				$len = $this->groups[$del];
			} else {
				// normal delimiter
				$len = strlen($del);
			}
			if($max < $len) {
				$max = $len;
			}
		}
		if($max > 1)
		{
			$var_init.=					$tab2.'$p=$i;'.$nl;			// p - stream pointer
			if($transitions > 1)
			{
				$var_init.=				$tab2.'$c1=$s[$p++];'.$nl;
				for($x=2;$x<=$max;$x++)
				{
					$xx=$x-1;
					if($x!=$max) {
						$var_init.=		$tab2."\$c$x=\$c$xx.\$s[\$p++];".$nl;
					} else {
						$var_init.=		$tab2."\$c$x=\$c$xx.\$s[\$p];".$nl;
					}
				}
			}
			else
			{
				// only one transition
				$var_init.=				$tab2.'$c1=';
				for($x=1;$x<=$max;$x++)
				{
					if($x!=$max) {
						$var_init.=					'$s[$p++].';
					} else {
						$var_init.=					'$s[$p];'.$nl;
					}
				}
			}
		}
		else
		{
			$var_init.=					$tab2.'$c1=$s[$i];'.$nl;
		}

		$this->out.= 				$tab.'$o = false;'.		$nl;
		$this->out.= 				$tab.'$start = $i;'.		$nl;
		if(!$all_break) {
			$this->out.= 			$tab.'while($i<$l) {'.	$nl;
		}
		$this->out.=				$var_init;

		//
		// generate conditions and transitions
		//
		$this->inject_statistic_code($state, -1, false);
		$cond = null;
		$i = 0;
		foreach($this->_delim[$state] as $del)
		{
			$size = strlen($del);
			$delstring=get_string_source($del);
			if(isset($this->groups[$del]))
			{
				// delimiter is group delimiter
				// make condition
				$size = $this->groups[$del];
				if( FSHL_USE_CTYPE )	$cond = $this->get_ctype_condition($del);
				else					$cond = $this->get_older_condition($del);
				if($cond == "1") break;

				$this->out.=			$tab2."if($cond){".$nl;

				$this->inject_statistic_code($state, $i);

				$this->out.=				$tab3."return array($i,\$c1,\$o,$size,\$i-\$start);".$nl;
				$this->out.=			$tab2.'}'.$nl;
			}
			else
			{
				// delimiter is not group delimiter
				//$i_str = $size == 1 ? '$i' : '$i+'.($size-1);
				$this->out.=			$tab2."if(\$c$size==$delstring){".$nl;

				$this->inject_statistic_code($state, $i);

				//$this->out.=				$tab3."return array($i,$delstring,\$o,$i_str);".$nl;
				$this->out.=				$tab3."return array($i,$delstring,\$o,$size,\$i-\$start);".$nl;
				$this->out.=			$tab2.'}'.$nl;
			}
			$i++;
		} // END foreach()

		$this->inject_statistic_code($state, $i, false);

		if($cond == "1") {
			$this->out.=				$tab2."return array($i,\$c1,false,\$i-\$start);".$nl;
		} else {
			$this->out.=				$tab2.'$o.=$c1;'.$nl;
			$this->out.=				$tab2.'$i++;'.$nl;
		}

		if(!$all_break) {
			$this->out.=			$tab."}".$nl;	//end while()
			$this->out.=			$tab.'return array(-1,-1,$o,-1,-1);'.$nl;
		}

		$this->out.=			"}".$nl.$nl;	//end getw() function

	} // END make_state_code()


	// condition generator for older PHP's
	//
	function get_older_condition($del)
	{
		switch ($del)
		{
			case "SPACE":		$cond=	"strchr(\" \\t\\n\\r\",\$c1)";	break;
			case "!SPACE":		$cond=	"!strchr(\" \\t\\n\\r\",\$c1)";	break;
			case "NUMBER":		$cond=	"strchr('0123456789',\$c1)";	break;
			case "!NUMBER":		$cond=	"!strchr('0123456789',\$c1)";	break;
			case "ALPHA":		$cond=	"stristr('eaoinltsrvdukzmcpyhjbfgxwq',\$c1)";	break;
			case "!ALPHA":		$cond=	"!stristr('eaoinltsrvdukzmcpyhjbfgxwq',\$c1)";	break;
			case "ALNUM":		$cond=	"stristr('eaoinltsrvdukzmcpyhjbfgxwq0123456789',\$c1)";	break;
			case "!ALNUM":		$cond=	"!stristr('eaoinltsrvdukzmcpyhjbfgxwq0123456789',\$c1)";	break;
			case "HEXNUM":		$cond=	"stristr('0123456789abcdef',\$c1)"; break;
			case "!HEXNUM":		$cond=	"!stristr('0123456789abcdef',\$c1)"; break;
			case "_ALL":		$cond=	"1"; break;
			case "_COUNTAB":	$cond=	"strchr(\"\\t\\n\",\$c1)"; break;
			case "SAFECHAR":	$cond=  "stristr('eaoinltsrvdukzmcpyhjbfgxwq_0123456789',\$c1)";	break;
			case "!SAFECHAR":	$cond=  "!stristr('eaoinltsrvdukzmcpyhjbfgxwq_0123456789',\$c1)";	break;
			case "DOT_NUMBER":	$cond=	"\$c1=='.' && strchr('0123456789',\$c2[1])";	break;
			case "!DOT_NUMBER":	$cond=	"!(\$c1=='.' && strchr('0123456789',\$c2[1]))";	break;
			// Special group delimiters

			case "PHP_DELIM":	$cond=	"strchr(\" \\t\\n\\r;,:(){}[]!=%&|+-*/\",\$c1)"; break;

			default:			$cond = "BAD_FSHL_GENERATOR_VERSION__check_older";
								$this->print_error("Group delimiter '<b>$del</b>' is not implemented.");
								break;
		} // END switch($del)
		return $cond;
	}

	// new ctype condition generator (is really faster?)
	//
	function get_ctype_condition($del)
	{
		switch ($del)
		{
			case "SPACE":		$cond=	'ctype_space($c1)';		break;
			case "!SPACE":		$cond=	'!ctype_space($c1)';	break;
			case "NUMBER":		$cond=	'ctype_digit($c1)';		break;
			case "!NUMBER":		$cond=	'!ctype_digit($c1)';	break;
			case "ALPHA":		$cond=	'ctype_alpha($c1)';		break;
			case "!ALPHA":		$cond=	'!ctype_alpha($c1)';	break;
			case "ALNUM":		$cond=	'ctype_alnum($c1)';		break;
			case "!ALNUM":		$cond=	'!ctype_alnum($c1)';	break;
			case "HEXNUM":		$cond=	'ctype_xdigit($c1)';	break;
			case "!HEXNUM":		$cond=	'!ctype_xdigit($c1)';	break;
			case "_ALL":		$cond=	"1"; break;
			case "_COUNTAB":	$cond=	"(\$c1==\"\\t\"||\$c1==\"\\n\")"; break;
			case "SAFECHAR":	$cond=	"ctype_alnum(\$c1)||\$c1=='_'";	break;
			case "!SAFECHAR":	$cond=	"!(\$c1=='_'||ctype_alnum(\$c1))";	break;
			case "DOT_NUMBER":	$cond=	"\$c1=='.'&&ctype_digit(\$c2[1])";	break;
			case "!DOT_NUMBER":	$cond=	"!(\$c1=='.'&&ctype_digit(\$c2[1]))";	break;

			// Special group delimiters

			case "PHP_DELIM":	$cond=	"strchr(\" \\t\\n\\r;,:(){}[]!=%&|+-*/\",\$c1)"; break;

			default:			$cond = "BAD_FSHL_GENERATOR_VERSION";
								$this->print_error("Group delimiter '<b>$del</b>' is not implemented.");
								break;
		} // END switch($del)
		return $cond;
	}


	function language_array_optimise()
	{
		// internal language structures initialization
		$j=0;
		foreach ($this->lang->states as $state => $state_array)
		{
			if($state==P_QUIT_STATE)
				continue;
			$this->_states[$state]=$j;
			$this->_names[$j++]=$state;
		}
		$this->_states[P_RET_STATE] = $this->_ret = $j++;
		$this->_states[P_QUIT_STATE] = $this->_quit = $j++;
		$this->_names[$this->_ret] = P_RET_STATE;
		$this->_names[$this->_quit] = P_QUIT_STATE;

		foreach ($this->lang->states as $statename => $state_array)
		{
			$state = $this->_states[$statename];
			$this->_flags[$state] = $state_array[XL_FLAGS];
			$this->_data[$state] = $state_array[XL_DATA];
			$this->_class[$state] = $state_array[XL_CLASS];
			if(is_array($state_array[XL_DIAGR]))
			{
				$i=0;
				foreach ($state_array[XL_DIAGR] as $delimiter => $trans_array )
				{
					$transname=$trans_array[XL_DSTATE];
					if(!isset($this->_states[$transname]))
					{
						$delimiter=str_replace("<","&lt;",$delimiter);
						$this->print_error("Unknown state in transition '$statename [$delimiter] => <b>$transname</b>'.");
						return $this->error = true;
					}
					// V 0.4.5
					$this->_delim[$state][$i]=$delimiter;
					$trans_array[XL_DSTATE]=$this->_states[$transname];
					$this->_trans[$state][$i]=$trans_array;
					$i++;
				}
			}
			else
			{
				$this->_delim[$state]=null;
				$this->_trans[$state]=null;
			}
		}
		$initial = $this->lang->initial_state;
		if(!isset($this->_states[$initial]))
		{
			$this->print_error("Unknown initial state '<b>$initial</b>'.");
			return $this->error = true;
		}
		$this->lang->initial_state=$this->_states[$initial];
		return false;
	}


} // END class fshlGenerator
