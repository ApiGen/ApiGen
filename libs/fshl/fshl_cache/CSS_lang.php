<?php
/* --------------------------------------------------------------- *
 *        WARNING: ALL CHANGES IN THIS FILE WILL BE LOST
 *
 *   Source language file: W:\fshl/lang/CSS_lang.php
 *       Language version: 1.12 (Sign:SHL)
 *
 *            Target file: W:\fshl/fshl_cache/CSS_lang.php
 *             Build date: Mon 24.1.2011 18:17:43
 *
 *      Generator version: 0.4.11
 * --------------------------------------------------------------- */
class CSS_lang
{
var $trans,$flags,$data,$delim,$class,$keywords;
var $version,$signature,$initial_state,$ret,$quit;
var $pt,$pti,$generator_version;
var $names;

function CSS_lang () {
	$this->version=1.12;
	$this->signature="SHL";
	$this->generator_version="0.4.11";
	$this->initial_state=0;
	$this->trans=array(0=>array(0=>array(0=>0,1=>0),1=>array(0=>2,1=>0),2=>array(0=>1,1=>0),3=>array(0=>6,1=>0),4=>array(0=>9,1=>0),5=>array(0=>7,1=>0),6=>array(0=>7,1=>0)),1=>array(0=>array(0=>8,1=>1),1=>array(0=>6,1=>0),2=>array(0=>8,1=>1)),2=>array(0=>array(0=>4,1=>1),1=>array(0=>2,1=>0),2=>array(0=>2,1=>1),3=>array(0=>8,1=>0),4=>array(0=>6,1=>0),5=>array(0=>3,1=>0)),3=>array(0=>array(0=>3,1=>0),1=>array(0=>8,1=>1),2=>array(0=>8,1=>1),3=>array(0=>6,1=>0)),4=>array(0=>array(0=>8,1=>1),1=>array(0=>5,1=>0),2=>array(0=>8,1=>1),3=>array(0=>4,1=>0),4=>array(0=>6,1=>0)),5=>array(0=>array(0=>8,1=>1)),6=>array(0=>array(0=>6,1=>0),1=>array(0=>8,1=>0)),7=>null,9=>null);
	$this->flags=array(0=>0,1=>4,2=>4,3=>4,4=>4,5=>4,6=>4,7=>8,9=>8);
	$this->delim=array(0=>array(0=>"_COUNTAB",1=>"{",2=>".",3=>"/*",4=>"</",5=>"<?php",6=>"<?"),1=>array(0=>"SPACE",1=>"/*",2=>"{"),2=>array(0=>":",1=>"_COUNTAB",2=>";",3=>"}",4=>"/*",5=>"!SPACE"),3=>array(0=>"_COUNTAB",1=>":",2=>"}",3=>"/*"),4=>array(0=>";",1=>"#",2=>"}",3=>"_COUNTAB",4=>"/*"),5=>array(0=>"!HEXNUM"),6=>array(0=>"_COUNTAB",1=>"*/"),7=>null,9=>null);
	$this->ret=8;
	$this->quit=9;
	$this->names=array(0=>"OUT",1=>"CLASS",2=>"DEF",3=>"PROPERTY",4=>"VALUE",5=>"COLOR",6=>"COMMENT",7=>"TO_PHP",8=>"_RET",9=>"_QUIT");
	$this->data=array(0=>null,1=>null,2=>null,3=>null,4=>null,5=>null,6=>null,7=>"PHP",9=>null);
	$this->class=array(0=>null,1=>"css-class",2=>"",3=>"css-property",4=>"css-value",5=>"css-color",6=>"css-comment",7=>"xlang",9=>"html-tag");
	$this->keywords=null;
}

// OUT
function getw0 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p];
		if(($c1=="\t"||$c1=="\n")){
			return array(0,$c1,$o,1,$i-$start);
		}
		if($c1=="{"){
			return array(1,"{",$o,1,$i-$start);
		}
		if($c1=="."){
			return array(2,".",$o,1,$i-$start);
		}
		if($c2=="/*"){
			return array(3,"/*",$o,2,$i-$start);
		}
		if($c2=="</"){
			return array(4,"</",$o,2,$i-$start);
		}
		if($c5=="<?php"){
			return array(5,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(6,"<?",$o,2,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// CLASS
function getw1 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if(ctype_space($c1)){
			return array(0,$c1,$o,1,$i-$start);
		}
		if($c2=="/*"){
			return array(1,"/*",$o,2,$i-$start);
		}
		if($c1=="{"){
			return array(2,"{",$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// DEF
function getw2 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if($c1==":"){
			return array(0,":",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(1,$c1,$o,1,$i-$start);
		}
		if($c1==";"){
			return array(2,";",$o,1,$i-$start);
		}
		if($c1=="}"){
			return array(3,"}",$o,1,$i-$start);
		}
		if($c2=="/*"){
			return array(4,"/*",$o,2,$i-$start);
		}
		if(!ctype_space($c1)){
			return array(5,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// PROPERTY
function getw3 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if(($c1=="\t"||$c1=="\n")){
			return array(0,$c1,$o,1,$i-$start);
		}
		if($c1==":"){
			return array(1,":",$o,1,$i-$start);
		}
		if($c1=="}"){
			return array(2,"}",$o,1,$i-$start);
		}
		if($c2=="/*"){
			return array(3,"/*",$o,2,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// VALUE
function getw4 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if($c1==";"){
			return array(0,";",$o,1,$i-$start);
		}
		if($c1=="#"){
			return array(1,"#",$o,1,$i-$start);
		}
		if($c1=="}"){
			return array(2,"}",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(3,$c1,$o,1,$i-$start);
		}
		if($c2=="/*"){
			return array(4,"/*",$o,2,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// COLOR
function getw5 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if(!ctype_xdigit($c1)){
			return array(0,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// COMMENT
function getw6 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if(($c1=="\t"||$c1=="\n")){
			return array(0,$c1,$o,1,$i-$start);
		}
		if($c2=="*/"){
			return array(1,"*/",$o,2,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

}
