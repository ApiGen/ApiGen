<?php
/* --------------------------------------------------------------- *
 *        WARNING: ALL CHANGES IN THIS FILE WILL BE LOST
 *
 *   Source language file: W:\fshl/lang/HTMLonly_lang.php
 *       Language version: 1.10 (Sign:SHL)
 *
 *            Target file: W:\fshl/fshl_cache/HTMLonly_lang.php
 *             Build date: Mon 24.1.2011 18:17:43
 *
 *      Generator version: 0.4.11
 * --------------------------------------------------------------- */
class HTMLonly_lang
{
var $trans,$flags,$data,$delim,$class,$keywords;
var $version,$signature,$initial_state,$ret,$quit;
var $pt,$pti,$generator_version;
var $names;

function HTMLonly_lang () {
	$this->version=1.10;
	$this->signature="SHL";
	$this->generator_version="0.4.11";
	$this->initial_state=0;
	$this->trans=array(0=>array(0=>array(0=>6,1=>0),1=>array(0=>2,1=>0),2=>array(0=>1,1=>0),3=>array(0=>0,1=>0)),1=>array(0=>array(0=>0,1=>1),1=>array(0=>0,1=>1),2=>array(0=>0,1=>1)),2=>array(0=>array(0=>0,1=>1),1=>array(0=>3,1=>0)),3=>array(0=>array(0=>4,1=>0),1=>array(0=>0,1=>1),2=>array(0=>3,1=>0),3=>array(0=>5,1=>0)),4=>array(0=>array(0=>3,1=>0)),5=>array(0=>array(0=>3,1=>0)),6=>array(0=>array(0=>0,1=>1),1=>array(0=>6,1=>0)));
	$this->flags=array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0);
	$this->delim=array(0=>array(0=>"<!--",1=>"<",2=>"&",3=>"_COUNTAB"),1=>array(0=>";",1=>"&",2=>"SPACE"),2=>array(0=>">",1=>"SPACE"),3=>array(0=>"\"",1=>">",2=>"_COUNTAB",3=>"'"),4=>array(0=>"\""),5=>array(0=>"'"),6=>array(0=>"-->",1=>"_COUNTAB"));
	$this->ret=7;
	$this->quit=8;
	$this->names=array(0=>"OUT",1=>"ENTITY",2=>"TAG",3=>"inTAG",4=>"QUOTE1",5=>"QUOTE2",6=>"COMMENT",7=>"_RET",8=>"_QUIT");
	$this->data=array(0=>null,1=>null,2=>null,3=>null,4=>null,5=>null,6=>null);
	$this->class=array(0=>null,1=>"html-entity",2=>"html-tag",3=>"html-tagin",4=>"html-quote",5=>"html-quote",6=>"html-comment");
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
		$c4=$c3.$s[$p];
		if($c4=="<!--"){
			return array(0,"<!--",$o,4,$i-$start);
		}
		if($c1=="<"){
			return array(1,"<",$o,1,$i-$start);
		}
		if($c1=="&"){
			return array(2,"&",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(3,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// ENTITY
function getw1 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1==";"){
			return array(0,";",$o,1,$i-$start);
		}
		if($c1=="&"){
			return array(1,"&",$o,1,$i-$start);
		}
		if(ctype_space($c1)){
			return array(2,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// TAG
function getw2 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1==">"){
			return array(0,">",$o,1,$i-$start);
		}
		if(ctype_space($c1)){
			return array(1,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// inTAG
function getw3 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1=="\""){
			return array(0,"\"",$o,1,$i-$start);
		}
		if($c1==">"){
			return array(1,">",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(2,$c1,$o,1,$i-$start);
		}
		if($c1=="'"){
			return array(3,"'",$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// QUOTE1
function getw4 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1=="\""){
			return array(0,"\"",$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// QUOTE2
function getw5 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1=="'"){
			return array(0,"'",$o,1,$i-$start);
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
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p];
		if($c3=="-->"){
			return array(0,"-->",$o,3,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(1,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

}
