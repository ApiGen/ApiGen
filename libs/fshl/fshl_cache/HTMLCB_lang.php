<?php
/* --------------------------------------------------------------- *
 *        WARNING: ALL CHANGES IN THIS FILE WILL BE LOST
 *
 *   Source language file: W:\fshl/lang/HTMLCB_lang.php
 *       Language version: 1.11 (Sign:SHL)
 *
 *            Target file: W:\fshl/fshl_cache/HTMLCB_lang.php
 *             Build date: Mon 24.1.2011 18:17:43
 *
 *      Generator version: 0.4.11
 * --------------------------------------------------------------- */
class HTMLCB_lang
{
var $trans,$flags,$data,$delim,$class,$keywords;
var $version,$signature,$initial_state,$ret,$quit;
var $pt,$pti,$generator_version;
var $names;

function HTMLCB_lang () {
	$this->version=1.11;
	$this->signature="SHL";
	$this->generator_version="0.4.11";
	$this->initial_state=0;
	$this->trans=array(0=>array(0=>array(0=>10,1=>0),1=>array(0=>11,1=>0),2=>array(0=>12,1=>0),3=>array(0=>12,1=>0),4=>array(0=>13,1=>0),5=>array(0=>2,1=>0),6=>array(0=>1,1=>0),7=>array(0=>0,1=>0)),1=>array(0=>array(0=>0,1=>1),1=>array(0=>0,1=>1),2=>array(0=>0,1=>1)),2=>array(0=>array(0=>0,1=>1),1=>array(0=>3,1=>0),2=>array(0=>4,1=>1),3=>array(0=>4,1=>1),4=>array(0=>6,1=>1),5=>array(0=>6,1=>1),6=>array(0=>12,1=>0),7=>array(0=>12,1=>0),8=>array(0=>13,1=>0)),3=>array(0=>array(0=>8,1=>0),1=>array(0=>14,1=>1),2=>array(0=>9,1=>0),3=>array(0=>12,1=>0),4=>array(0=>12,1=>0),5=>array(0=>13,1=>0),6=>array(0=>3,1=>0)),4=>array(0=>array(0=>8,1=>0),1=>array(0=>9,1=>0),2=>array(0=>5,1=>0),3=>array(0=>12,1=>0),4=>array(0=>12,1=>0),5=>array(0=>13,1=>0),6=>array(0=>3,1=>0)),5=>array(0=>array(0=>14,1=>1)),6=>array(0=>array(0=>8,1=>0),1=>array(0=>9,1=>0),2=>array(0=>7,1=>0),3=>array(0=>12,1=>0),4=>array(0=>12,1=>0),5=>array(0=>13,1=>0),6=>array(0=>3,1=>0)),7=>array(0=>array(0=>14,1=>1)),8=>array(0=>array(0=>14,1=>0),1=>array(0=>12,1=>0),2=>array(0=>12,1=>0),3=>array(0=>13,1=>0),4=>array(0=>8,1=>0)),9=>array(0=>array(0=>14,1=>0),1=>array(0=>12,1=>0),2=>array(0=>12,1=>0),3=>array(0=>13,1=>0),4=>array(0=>9,1=>0)),10=>array(0=>array(0=>0,1=>1),1=>array(0=>12,1=>0),2=>array(0=>12,1=>0),3=>array(0=>10,1=>0)),11=>array(0=>array(0=>0,1=>1),1=>array(0=>11,1=>0)),12=>null,13=>null);
	$this->flags=array(0=>0,1=>0,2=>0,3=>4,4=>4,5=>8,6=>4,7=>8,8=>4,9=>4,10=>0,11=>0,12=>8,13=>8);
	$this->delim=array(0=>array(0=>"<!--",1=>"{*",2=>"<?php",3=>"<?",4=>"{",5=>"<",6=>"&",7=>"_COUNTAB"),1=>array(0=>";",1=>"&",2=>"SPACE"),2=>array(0=>">",1=>"SPACE",2=>"style",3=>"STYLE",4=>"script",5=>"SCRIPT",6=>"<?php",7=>"<?",8=>"{"),3=>array(0=>"\"",1=>">",2=>"'",3=>"<?php",4=>"<?",5=>"{",6=>"_COUNTAB"),4=>array(0=>"\"",1=>"'",2=>">",3=>"<?php",4=>"<?",5=>"{",6=>"_COUNTAB"),5=>array(0=>">"),6=>array(0=>"\"",1=>"'",2=>">",3=>"<?php",4=>"<?",5=>"{",6=>"_COUNTAB"),7=>array(0=>">"),8=>array(0=>"\"",1=>"<?php",2=>"<?",3=>"{",4=>"_COUNTAB"),9=>array(0=>"'",1=>"<?php",2=>"<?",3=>"{",4=>"_COUNTAB"),10=>array(0=>"-->",1=>"<?php",2=>"<?",3=>"_COUNTAB"),11=>array(0=>"*}",1=>"_COUNTAB"),12=>null,13=>null);
	$this->ret=14;
	$this->quit=15;
	$this->names=array(0=>"OUT",1=>"ENTITY",2=>"TAG",3=>"inTAG",4=>"CSS",5=>"TO_CSS",6=>"JAVASCRIPT",7=>"TO_JAVASCRIPT",8=>"QUOTE1",9=>"QUOTE2",10=>"COMMENT",11=>"COMMENT1",12=>"TO_PHP",13=>"TO_PHPCB",14=>"_RET",15=>"_QUIT");
	$this->data=array(0=>null,1=>null,2=>null,3=>null,4=>null,5=>"CSS",6=>null,7=>"JSCB",8=>null,9=>null,10=>null,11=>null,12=>"PHP",13=>"PHPCB");
	$this->class=array(0=>null,1=>"html-entity",2=>"html-tag",3=>"html-tagin",4=>"html-tagin",5=>"html-tag",6=>"html-tagin",7=>"html-tag",8=>"html-quote",9=>"html-quote",10=>"html-comment",11=>"html-comment",12=>"xlang",13=>"xlang");
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
		if($c4=="<!--"){
			return array(0,"<!--",$o,4,$i-$start);
		}
		if($c2=="{*"){
			return array(1,"{*",$o,2,$i-$start);
		}
		if($c5=="<?php"){
			return array(2,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(3,"<?",$o,2,$i-$start);
		}
		if($c1=="{"){
			return array(4,"{",$o,1,$i-$start);
		}
		if($c1=="<"){
			return array(5,"<",$o,1,$i-$start);
		}
		if($c1=="&"){
			return array(6,"&",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(7,$c1,$o,1,$i-$start);
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
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p++];
		$c6=$c5.$s[$p];
		if($c1==">"){
			return array(0,">",$o,1,$i-$start);
		}
		if(ctype_space($c1)){
			return array(1,$c1,$o,1,$i-$start);
		}
		if($c5=="style"){
			return array(2,"style",$o,5,$i-$start);
		}
		if($c5=="STYLE"){
			return array(3,"STYLE",$o,5,$i-$start);
		}
		if($c6=="script"){
			return array(4,"script",$o,6,$i-$start);
		}
		if($c6=="SCRIPT"){
			return array(5,"SCRIPT",$o,6,$i-$start);
		}
		if($c5=="<?php"){
			return array(6,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(7,"<?",$o,2,$i-$start);
		}
		if($c1=="{"){
			return array(8,"{",$o,1,$i-$start);
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
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p];
		if($c1=="\""){
			return array(0,"\"",$o,1,$i-$start);
		}
		if($c1==">"){
			return array(1,">",$o,1,$i-$start);
		}
		if($c1=="'"){
			return array(2,"'",$o,1,$i-$start);
		}
		if($c5=="<?php"){
			return array(3,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(4,"<?",$o,2,$i-$start);
		}
		if($c1=="{"){
			return array(5,"{",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(6,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// CSS
function getw4 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p];
		if($c1=="\""){
			return array(0,"\"",$o,1,$i-$start);
		}
		if($c1=="'"){
			return array(1,"'",$o,1,$i-$start);
		}
		if($c1==">"){
			return array(2,">",$o,1,$i-$start);
		}
		if($c5=="<?php"){
			return array(3,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(4,"<?",$o,2,$i-$start);
		}
		if($c1=="{"){
			return array(5,"{",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(6,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// TO_CSS
function getw5 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1==">"){
			return array(0,">",$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// JAVASCRIPT
function getw6 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p];
		if($c1=="\""){
			return array(0,"\"",$o,1,$i-$start);
		}
		if($c1=="'"){
			return array(1,"'",$o,1,$i-$start);
		}
		if($c1==">"){
			return array(2,">",$o,1,$i-$start);
		}
		if($c5=="<?php"){
			return array(3,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(4,"<?",$o,2,$i-$start);
		}
		if($c1=="{"){
			return array(5,"{",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(6,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// TO_JAVASCRIPT
function getw7 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1==">"){
			return array(0,">",$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// QUOTE1
function getw8 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p];
		if($c1=="\""){
			return array(0,"\"",$o,1,$i-$start);
		}
		if($c5=="<?php"){
			return array(1,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(2,"<?",$o,2,$i-$start);
		}
		if($c1=="{"){
			return array(3,"{",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(4,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// QUOTE2
function getw9 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p];
		if($c1=="'"){
			return array(0,"'",$o,1,$i-$start);
		}
		if($c5=="<?php"){
			return array(1,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(2,"<?",$o,2,$i-$start);
		}
		if($c1=="{"){
			return array(3,"{",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(4,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// COMMENT
function getw10 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p];
		if($c3=="-->"){
			return array(0,"-->",$o,3,$i-$start);
		}
		if($c5=="<?php"){
			return array(1,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(2,"<?",$o,2,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(3,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// COMMENT1
function getw11 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if($c2=="*}"){
			return array(0,"*}",$o,2,$i-$start);
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
