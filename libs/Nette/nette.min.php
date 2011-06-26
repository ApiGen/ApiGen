<?php
/**
 * Nette Framework (version 2.0-dev released on $WCDATE$, http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */
 namespace Nette\Caching{use Nette;interface IStorage{function read($key);function
write($key,$data,array$dependencies);function remove($key);function clean(array$conds);}}

 namespace Nette\Caching\Storages{use Nette;interface IJournal{function write($key,array$dependencies);function
clean(array$conditions);}}

 namespace Nette{use Nette;class ArrayHash implements \ArrayAccess,\Countable,\IteratorAggregate{public
static function from($arr,$recursive=TRUE){$obj=new static;foreach($arr as$key=>$value){if($recursive&&is_array($value)){$obj->$key=static::from($value,TRUE);}else{$obj->$key=$value;}}return$obj;}public
function getIterator(){return new \RecursiveArrayIterator($this);}public function
count(){return count((array)$this);}public function offsetSet($key,$value){if(!is_scalar($key)){throw
new InvalidArgumentException("Key must be either a string or an integer, ".gettype($key)." given.");}$this->$key=$value;}public
function offsetGet($key){return$this->$key;}public function offsetExists($key){return
isset($this->$key);}public function offsetUnset($key){unset($this->$key);}}}

 namespace Nette{use Nette;final class Environment{const DEVELOPMENT='development',PRODUCTION='production',CONSOLE='console';private
static$configurator;private static$context;final public function __construct(){throw
new StaticClassException;}public static function setConfigurator(Configurator$configurator){self::$configurator=$configurator;}public
static function getConfigurator(){if(self::$configurator===NULL){self::$configurator=Configurator::$instance?:new
Configurator;}return self::$configurator;}public static function isConsole(){return
self::getContext()->params['consoleMode'];}public static function isProduction(){return
self::getContext()->params['productionMode'];}public static function setProductionMode($value=TRUE){self::getContext()->params['productionMode']=(bool)$value;}public
static function setVariable($name,$value,$expand=TRUE){if($expand&&is_string($value)){$value=self::getContext()->expand($value);}self::getContext()->params[$name]=$value;}public
static function getVariable($name,$default=NULL){if(isset(self::getContext()->params[$name])){return
self::getContext()->params[$name];}elseif(func_num_args()>1){return$default;}else{throw
new InvalidStateException("Unknown environment variable '$name'.");}}public static
function getVariables(){return self::getContext()->params;}public static function
expand($s){return self::getContext()->expand($s);}public static function setContext(DI\IContainer$context){self::$context=$context;}public
static function getContext(){if(self::$context===NULL){self::$context=self::getConfigurator()->getContainer();}return
self::$context;}public static function getService($name){return self::getContext()->getService($name);}public
static function __callStatic($name,$args){if(!$args&&strncasecmp($name,'get',3)===0){return
self::getContext()->getService(lcfirst(substr($name,3)));}else{throw new MemberAccessException("Call to undefined static method Nette\\Environment::$name().");}}public
static function getHttpRequest(){return self::getContext()->httpRequest;}public static
function getHttpContext(){return self::getContext()->httpContext;}public static function
getHttpResponse(){return self::getContext()->httpResponse;}public static function
getApplication(){return self::getContext()->application;}public static function getUser(){return
self::getContext()->user;}public static function getRobotLoader(){return self::getContext()->robotLoader;}public
static function getCache($namespace=''){return new Caching\Cache(self::getContext()->cacheStorage,$namespace);}public
static function getSession($namespace=NULL){return$namespace===NULL?self::getContext()->session:self::getContext()->session->getSection($namespace);}public
static function loadConfig($file=NULL,$section=NULL){self::getConfigurator()->loadConfig($file,$section);return
self::getContext()->params;}public static function getConfig($key=NULL,$default=NULL){$params=self::getContext()->params;if(func_num_args()){return
isset($params[$key])?$params[$key]:$default;}else{return$params;}}}}

 namespace Nette{use Nette;class ArgumentOutOfRangeException extends \InvalidArgumentException{}class
InvalidStateException extends \RuntimeException{}class NotImplementedException extends
\LogicException{}class NotSupportedException extends \LogicException{}class DeprecatedException
extends NotSupportedException{}class MemberAccessException extends \LogicException{}class
IOException extends \RuntimeException{}class FileNotFoundException extends IOException{}class
DirectoryNotFoundException extends IOException{}class InvalidArgumentException extends
\InvalidArgumentException{}class OutOfRangeException extends \OutOfRangeException{}class
UnexpectedValueException extends \UnexpectedValueException{}class StaticClassException
extends \LogicException{}class FatalErrorException extends \ErrorException{public
function __construct($message,$code,$severity,$file,$line,$context){parent::__construct($message,$code,$severity,$file,$line);$this->context=$context;}}}

 namespace Nette{use Nette;final class Framework{const NAME='Nette Framework',VERSION='2.0-beta',REVISION='$WCREV$ released on $WCDATE$';public
static$iAmUsingBadHost=FALSE;final public function __construct(){throw new StaticClassException;}}}

 namespace Nette{use Nette;interface IFreezable{function freeze();function isFrozen();}}

 namespace Nette{use Nette;abstract class Object{public static function getReflection(){return
new Reflection\ClassType(get_called_class());}public function __call($name,$args){return
ObjectMixin::call($this,$name,$args);}public static function __callStatic($name,$args){return
ObjectMixin::callStatic(get_called_class(),$name,$args);}public static function extensionMethod($name,$callback=NULL){if(strpos($name,'::')===FALSE){$class=get_called_class();}else{list($class,$name)=explode('::',$name);}$class=new
Reflection\ClassType($class);if($callback===NULL){return$class->getExtensionMethod($name);}else{$class->setExtensionMethod($name,$callback);}}public
function&__get($name){return ObjectMixin::get($this,$name);}public function __set($name,$value){return
ObjectMixin::set($this,$name,$value);}public function __isset($name){return ObjectMixin::has($this,$name);}public
function __unset($name){ObjectMixin::remove($this,$name);}}}

 namespace Nette{use Nette;final class ObjectMixin{private static$methods;final public
function __construct(){throw new StaticClassException;}public static function call($_this,$name,$args){$class=new
Reflection\ClassType($_this);if($name===''){throw new MemberAccessException("Call to class '$class->name' method without name.");}if($class->hasEventProperty($name)){if(is_array($list=$_this->$name)||$list
instanceof \Traversable){foreach($list as$handler){callback($handler)->invokeArgs($args);}}return
NULL;}if($cb=$class->getExtensionMethod($name)){array_unshift($args,$_this);return$cb->invokeArgs($args);}throw
new MemberAccessException("Call to undefined method $class->name::$name().");}public
static function callStatic($class,$name,$args){throw new MemberAccessException("Call to undefined static method $class::$name().");}public
static function&get($_this,$name){$class=get_class($_this);if($name===''){throw new
MemberAccessException("Cannot read a class '$class' property without name.");}if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";$m='get'.$name;if(isset(self::$methods[$class][$m])){$val=$_this->$m();return$val;}$m='is'.$name;if(isset(self::$methods[$class][$m])){$val=$_this->$m();return$val;}$type=isset(self::$methods[$class]['set'.$name])?'a write-only':'an undeclared';$name=func_get_arg(1);throw
new MemberAccessException("Cannot read $type property $class::\$$name.");}public
static function set($_this,$name,$value){$class=get_class($_this);if($name===''){throw
new MemberAccessException("Cannot write to a class '$class' property without name.");}if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";$m='set'.$name;if(isset(self::$methods[$class][$m])){$_this->$m($value);return;}$type=isset(self::$methods[$class]['get'.$name])||isset(self::$methods[$class]['is'.$name])?'a read-only':'an undeclared';$name=func_get_arg(1);throw
new MemberAccessException("Cannot write to $type property $class::\$$name.");}public
static function remove($_this,$name){$class=get_class($_this);throw new MemberAccessException("Cannot unset the property $class::\$$name.");}public
static function has($_this,$name){if($name===''){return FALSE;}$class=get_class($_this);if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";return
isset(self::$methods[$class]['get'.$name])||isset(self::$methods[$class]['is'.$name]);}}}

 namespace Nette\DI{use Nette;interface IContainer{function addService($name,$service);function
getService($name);function removeService($name);function hasService($name);}}

 namespace Nette\Diagnostics{use Nette;final class Debugger{public static$productionMode;public
static$consoleMode;public static$time;private static$ajaxDetected;public static$source;public
static$editor='editor://open/?file=%file&line=%line';public static$maxDepth=3;public
static$maxLen=150;public static$showLocation=FALSE;const DEVELOPMENT=FALSE,PRODUCTION=TRUE,DETECT=NULL;public
static$blueScreen;public static$strictMode=FALSE;public static$scream=FALSE;public
static$onFatalError=array();private static$enabled=FALSE;private static$lastError=FALSE;public
static$logger;public static$fireLogger;public static$logDirectory;public static$email;public
static$mailer;public static$emailSnooze;public static$bar;private static$errorPanel;private
static$dumpPanel;const DEBUG='debug',INFO='info',WARNING='warning',ERROR='error',CRITICAL='critical';final
public function __construct(){throw new Nette\StaticClassException;}public static
function _init(){self::$time=microtime(TRUE);self::$consoleMode=PHP_SAPI==='cli';self::$productionMode=self::DETECT;if(self::$consoleMode){self::$source=empty($_SERVER['argv'])?'cli':'cli: '.implode(' ',$_SERVER['argv']);}else{self::$ajaxDetected=isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&$_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';if(isset($_SERVER['REQUEST_URI'])){self::$source=(isset($_SERVER['HTTPS'])&&strcasecmp($_SERVER['HTTPS'],'off')?'https://':'http://').(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'')).$_SERVER['REQUEST_URI'];}}self::$logger=new
Logger;self::$logDirectory=&self::$logger->directory;self::$email=&self::$logger->email;self::$mailer=&self::$logger->mailer;self::$emailSnooze=&Logger::$emailSnooze;self::$fireLogger=new
FireLogger;self::$blueScreen=new BlueScreen;self::$blueScreen->addPanel(function($e){if($e
instanceof Nette\Templating\FilterException){return array('tab'=>'Template','panel'=>'<p><b>File:</b> '.Helpers::editorLink($e->sourceFile,$e->sourceLine).'&nbsp; <b>Line:</b> '.($e->sourceLine?$e->sourceLine:'n/a').'</p>'.($e->sourceLine?'<pre>'.BlueScreen::highlightFile($e->sourceFile,$e->sourceLine).'</pre>':''));}});self::$bar=new
Bar;self::$bar->addPanel(new DefaultBarPanel('time'));self::$bar->addPanel(new DefaultBarPanel('memory'));self::$bar->addPanel(self::$errorPanel=new
DefaultBarPanel('errors'));self::$bar->addPanel(self::$dumpPanel=new DefaultBarPanel('dumps'));}public
static function enable($mode=NULL,$logDirectory=NULL,$email=NULL){error_reporting(E_ALL|E_STRICT);if(is_bool($mode)){self::$productionMode=$mode;}elseif(is_string($mode)){$mode=preg_split('#[,\s]+#',"$mode 127.0.0.1 ::1");}if(is_array($mode)){self::$productionMode=!isset($_SERVER['REMOTE_ADDR'])||!in_array($_SERVER['REMOTE_ADDR'],$mode,TRUE);}if(self::$productionMode===self::DETECT){if(class_exists('Nette\Environment')){self::$productionMode=Nette\Environment::isProduction();}elseif(isset($_SERVER['SERVER_ADDR'])||isset($_SERVER['LOCAL_ADDR'])){$addrs=array();if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){$addrs=preg_split('#,\s*#',$_SERVER['HTTP_X_FORWARDED_FOR']);}if(isset($_SERVER['REMOTE_ADDR'])){$addrs[]=$_SERVER['REMOTE_ADDR'];}$addrs[]=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['LOCAL_ADDR'];self::$productionMode=FALSE;foreach($addrs
as$addr){$oct=explode('.',$addr);if($addr!=='::1'&&(count($oct)!==4||($oct[0]!=='10'&&$oct[0]!=='127'&&($oct[0]!=='172'||$oct[1]<16||$oct[1]>31)&&($oct[0]!=='169'||$oct[1]!=='254')&&($oct[0]!=='192'||$oct[1]!=='168')))){self::$productionMode=TRUE;break;}}}else{self::$productionMode=!self::$consoleMode;}}if(is_string($logDirectory)){self::$logDirectory=realpath($logDirectory);if(self::$logDirectory===FALSE){throw
new Nette\DirectoryNotFoundException("Directory '$logDirectory' is not found.");}}elseif($logDirectory===FALSE){self::$logDirectory=FALSE;}elseif(self::$logDirectory===NULL){self::$logDirectory=defined('APP_DIR')?APP_DIR.'/../log':getcwd().'/log';}if(self::$logDirectory){ini_set('error_log',self::$logDirectory.'/php_error.log');}if(function_exists('ini_set')){ini_set('display_errors',!self::$productionMode);ini_set('html_errors',FALSE);ini_set('log_errors',FALSE);}elseif(ini_get('display_errors')!=!self::$productionMode&&ini_get('display_errors')!==(self::$productionMode?'stderr':'stdout')){throw
new Nette\NotSupportedException('Function ini_set() must be enabled.');}if($email){if(!is_string($email)){throw
new Nette\InvalidArgumentException('Email address must be a string.');}self::$email=$email;}if(!defined('E_DEPRECATED')){define('E_DEPRECATED',8192);}if(!defined('E_USER_DEPRECATED')){define('E_USER_DEPRECATED',16384);}if(!self::$enabled){register_shutdown_function(array(__CLASS__,'_shutdownHandler'));set_exception_handler(array(__CLASS__,'_exceptionHandler'));set_error_handler(array(__CLASS__,'_errorHandler'));self::$enabled=TRUE;}}public
static function isEnabled(){return self::$enabled;}public static function log($message,$priority=self::INFO){if(self::$logDirectory===FALSE){return;}elseif(!self::$logDirectory){throw
new Nette\InvalidStateException('Logging directory is not specified in Nette\Diagnostics\Debugger::$logDirectory.');}if($message
instanceof \Exception){$exception=$message;$message="PHP Fatal error: ".($message
instanceof Nette\FatalErrorException?$exception->getMessage():"Uncaught exception ".get_class($exception)." with message '".$exception->getMessage()."'")." in ".$exception->getFile().":".$exception->getLine();$hash=md5($exception);$exceptionFilename="exception ".@date('Y-m-d H-i-s')." $hash.html";foreach(new
\DirectoryIterator(self::$logDirectory)as$entry){if(strpos($entry,$hash)){$exceptionFilename=NULL;break;}}}self::$logger->log(array(@date('[Y-m-d H-i-s]'),$message,self::$source?' @  '.self::$source:NULL,!empty($exceptionFilename)?' @@  '.$exceptionFilename:NULL),$priority);if(!empty($exceptionFilename)&&$logHandle=@fopen(self::$logDirectory.'/'.$exceptionFilename,'w')){ob_start();ob_start(function($buffer)use($logHandle){fwrite($logHandle,$buffer);},1);self::$blueScreen->render($exception);ob_end_flush();ob_end_clean();fclose($logHandle);}}public
static function _shutdownHandler(){if(!self::$enabled){return;}static$types=array(E_ERROR=>1,E_CORE_ERROR=>1,E_COMPILE_ERROR=>1,E_PARSE=>1,);$error=error_get_last();if(isset($types[$error['type']])){self::_exceptionHandler(new
Nette\FatalErrorException($error['message'],0,$error['type'],$error['file'],$error['line'],NULL));}if(self::$bar&&!self::$productionMode&&!self::$ajaxDetected&&!self::$consoleMode&&!preg_match('#^Content-Type: (?!text/html)#im',implode("\n",headers_list()))){self::$bar->render();}}public
static function _exceptionHandler(\Exception$exception){if(!headers_sent()){header('HTTP/1.1 500 Internal Server Error');}$htmlMode=!self::$ajaxDetected&&!preg_match('#^Content-Type: (?!text/html)#im',implode("\n",headers_list()));try{if(self::$productionMode){self::log($exception,self::ERROR);if(self::$consoleMode){echo
"ERROR: the server encountered an internal error and was unable to complete your request.\n";}elseif($htmlMode){require
__DIR__.'/templates/error.phtml';}}else{if(self::$consoleMode){echo"$exception\n";}elseif($htmlMode){self::$blueScreen->render($exception);if(self::$bar){self::$bar->render();}}elseif(!self::fireLog($exception,self::ERROR)){self::log($exception);}}foreach(self::$onFatalError
as$handler){call_user_func($handler,$exception);}}catch(\Exception$e){echo "\nNette\\Debug FATAL ERROR: thrown ",get_class($e),': ',$e->getMessage(),"\nwhile processing ",get_class($exception),': ',$exception->getMessage(),"\n";}self::$enabled=FALSE;exit(255);}public
static function _errorHandler($severity,$message,$file,$line,$context){if(self::$scream){error_reporting(E_ALL|E_STRICT);}if(self::$lastError!==FALSE&&($severity&error_reporting())===$severity){self::$lastError=new
\ErrorException($message,0,$severity,$file,$line);return NULL;}if($severity===E_RECOVERABLE_ERROR||$severity===E_USER_ERROR){throw
new Nette\FatalErrorException($message,0,$severity,$file,$line,$context);}elseif(($severity&error_reporting())!==$severity){return
FALSE;}elseif(self::$strictMode&&!self::$productionMode){self::_exceptionHandler(new
Nette\FatalErrorException($message,0,$severity,$file,$line,$context));}static$types=array(E_WARNING=>'Warning',E_COMPILE_WARNING=>'Warning',E_USER_WARNING=>'Warning',E_NOTICE=>'Notice',E_USER_NOTICE=>'Notice',E_STRICT=>'Strict standards',E_DEPRECATED=>'Deprecated',E_USER_DEPRECATED=>'Deprecated',);$message='PHP '.(isset($types[$severity])?$types[$severity]:'Unknown error').": $message";$count=&self::$errorPanel->data["$message|$file|$line"];if($count++){return
NULL;}elseif(self::$productionMode){self::log("$message in $file:$line",self::ERROR);return
NULL;}else{$ok=self::fireLog(new \ErrorException($message,0,$severity,$file,$line),self::WARNING);return
self::$consoleMode||(!self::$bar&&!$ok)?FALSE:NULL;}return FALSE;}public static function
toStringException(\Exception$exception){if(self::$enabled){self::_exceptionHandler($exception);}else{trigger_error($exception->getMessage(),E_USER_ERROR);}}public
static function tryError(){if(!self::$enabled&&self::$lastError===FALSE){set_error_handler(array(__CLASS__,'_errorHandler'));}self::$lastError=NULL;}public
static function catchError(&$error){if(!self::$enabled&&self::$lastError!==FALSE){restore_error_handler();}$error=self::$lastError;self::$lastError=FALSE;return
(bool)$error;}public static function dump($var,$return=FALSE){if(!$return&&self::$productionMode){return$var;}$output="<pre class=\"nette-dump\">".Helpers::htmlDump($var)."</pre>\n";if(!$return){$trace=debug_backtrace();$i=!isset($trace[1]['class'])&&isset($trace[1]['function'])&&$trace[1]['function']==='dump'?1:0;if(isset($trace[$i]['file'],$trace[$i]['line'])&&is_file($trace[$i]['file'])){$lines=file($trace[$i]['file']);preg_match('#dump\((.*)\)#',$lines[$trace[$i]['line']-1],$m);$output=substr_replace($output,' title="'.htmlspecialchars((isset($m[0])?"$m[0] \n":'')."in file {$trace[$i]['file']} on line {$trace[$i]['line']}").'"',4,0);if(self::$showLocation){$output=substr_replace($output,' <small>in '.Helpers::editorLink($trace[$i]['file'],$trace[$i]['line']).":{$trace[$i]['line']}</small>",-8,0);}}}if(self::$consoleMode){$output=htmlspecialchars_decode(strip_tags($output),ENT_NOQUOTES);}if($return){return$output;}else{echo$output;return$var;}}public
static function timer($name=NULL){static$time=array();$now=microtime(TRUE);$delta=isset($time[$name])?$now-$time[$name]:0;$time[$name]=$now;return$delta;}public
static function barDump($var,$title=NULL){if(!self::$productionMode){$dump=array();foreach((is_array($var)?$var:array(''=>$var))as$key=>$val){$dump[$key]=Helpers::clickableDump($val);}self::$dumpPanel->data[]=array('title'=>$title,'dump'=>$dump);}return$var;}public
static function fireLog($message){if(!self::$productionMode){return self::$fireLogger->log($message);}}public
static function addPanel(IBarPanel$panel,$id=NULL){self::$bar->addPanel($panel,$id);}}}

 namespace Nette\Diagnostics{use Nette;interface IBarPanel{function getTab();function
getPanel();}}

 namespace Nette\Iterators{use Nette;class CachingIterator extends \CachingIterator
implements \Countable{private$counter=0;public function __construct($iterator){if(is_array($iterator)||$iterator
instanceof \stdClass){$iterator=new \ArrayIterator($iterator);}elseif($iterator instanceof
\Traversable){if($iterator instanceof \IteratorAggregate){$iterator=$iterator->getIterator();}elseif(!$iterator
instanceof \Iterator){$iterator=new \IteratorIterator($iterator);}}else{throw new
Nette\InvalidArgumentException("Invalid argument passed to foreach resp. ".__CLASS__."; array or Traversable expected, ".(is_object($iterator)?get_class($iterator):gettype($iterator))." given.");}parent::__construct($iterator,0);}public
function isFirst($width=NULL){return$this->counter===1||($width&&$this->counter!==0&&(($this->counter-1)%$width)===0);}public
function isLast($width=NULL){return!$this->hasNext()||($width&&($this->counter%$width)===0);}public
function isEmpty(){return$this->counter===0;}public function isOdd(){return$this->counter%2===1;}public
function isEven(){return$this->counter%2===0;}public function getCounter(){return$this->counter;}public
function count(){$inner=$this->getInnerIterator();if($inner instanceof \Countable){return$inner->count();}else{throw
new Nette\NotSupportedException('Iterator is not countable.');}}public function next(){parent::next();if(parent::valid()){$this->counter++;}}public
function rewind(){parent::rewind();$this->counter=parent::valid()?1:0;}public function
getNextKey(){return$this->getInnerIterator()->key();}public function getNextValue(){return$this->getInnerIterator()->current();}public
function __call($name,$args){return Nette\ObjectMixin::call($this,$name,$args);}public
function&__get($name){return Nette\ObjectMixin::get($this,$name);}public function
__set($name,$value){return Nette\ObjectMixin::set($this,$name,$value);}public function
__isset($name){return Nette\ObjectMixin::has($this,$name);}public function __unset($name){Nette\ObjectMixin::remove($this,$name);}}}

 namespace Nette\Iterators{use Nette;class Filter extends \FilterIterator{private$callback;public
function __construct(\Iterator$iterator,$callback){parent::__construct($iterator);$this->callback=$callback;}public
function accept(){return call_user_func($this->callback,$this);}}}

 namespace Nette\Latte{use Nette;interface IMacro{function initialize();function finalize();function
nodeOpened(MacroNode$node);function nodeClosed(MacroNode$node);}}

 namespace Nette\Reflection{use Nette,Nette\ObjectMixin;class ClassType extends \ReflectionClass{private
static$extMethods;public static function from($class){return new static($class);}public
function __toString(){return 'Class '.$this->getName();}public function hasEventProperty($name){if(preg_match('#^on[A-Z]#',$name)&&$this->hasProperty($name)){$rp=$this->getProperty($name);return$rp->isPublic()&&!$rp->isStatic();}return
FALSE;}public function setExtensionMethod($name,$callback){$l=&self::$extMethods[strtolower($name)];$l[strtolower($this->getName())]=callback($callback);$l['']=NULL;return$this;}public
function getExtensionMethod($name){$class=strtolower($this->getName());$l=&self::$extMethods[strtolower($name)];if(empty($l)){return
FALSE;}elseif(isset($l[''][$class])){return$l[''][$class];}$cl=$class;do{if(isset($l[$cl])){return$l[''][$class]=$l[$cl];}}while(($cl=strtolower(get_parent_class($cl)))!=='');foreach(class_implements($class)as$cl){$cl=strtolower($cl);if(isset($l[$cl])){return$l[''][$class]=$l[$cl];}}return$l[''][$class]=FALSE;}public
function getConstructor(){return($ref=parent::getConstructor())?Method::from($this->getName(),$ref->getName()):NULL;}public
function getExtension(){return($name=$this->getExtensionName())?new Extension($name):NULL;}public
function getInterfaces(){$res=array();foreach(parent::getInterfaceNames()as$val){$res[$val]=new
static($val);}return$res;}public function getMethod($name){return new Method($this->getName(),$name);}public
function getMethods($filter=-1){foreach($res=parent::getMethods($filter)as$key=>$val){$res[$key]=new
Method($this->getName(),$val->getName());}return$res;}public function getParentClass(){return($ref=parent::getParentClass())?new
static($ref->getName()):NULL;}public function getProperties($filter=-1){foreach($res=parent::getProperties($filter)as$key=>$val){$res[$key]=new
Property($this->getName(),$val->getName());}return$res;}public function getProperty($name){return
new Property($this->getName(),$name);}public function hasAnnotation($name){$res=AnnotationsParser::getAll($this);return!empty($res[$name]);}public
function getAnnotation($name){$res=AnnotationsParser::getAll($this);return isset($res[$name])?end($res[$name]):NULL;}public
function getAnnotations(){return AnnotationsParser::getAll($this);}public function
getDescription(){return$this->getAnnotation('description');}public static function
getReflection(){return new ClassType(get_called_class());}public function __call($name,$args){return
ObjectMixin::call($this,$name,$args);}public function&__get($name){return ObjectMixin::get($this,$name);}public
function __set($name,$value){return ObjectMixin::set($this,$name,$value);}public
function __isset($name){return ObjectMixin::has($this,$name);}public function __unset($name){ObjectMixin::remove($this,$name);}}}

 namespace Nette\Templating{use Nette,Nette\Utils\Strings,Nette\Forms\Form,Nette\Utils\Html;final
class DefaultHelpers{private static$helpers=array('normalize'=>'Nette\Utils\Strings::normalize','toascii'=>'Nette\Utils\Strings::toAscii','webalize'=>'Nette\Utils\Strings::webalize','truncate'=>'Nette\Utils\Strings::truncate','lower'=>'Nette\Utils\Strings::lower','upper'=>'Nette\Utils\Strings::upper','firstupper'=>'Nette\Utils\Strings::firstUpper','capitalize'=>'Nette\Utils\Strings::capitalize','trim'=>'Nette\Utils\Strings::trim','padleft'=>'Nette\Utils\Strings::padLeft','padright'=>'Nette\Utils\Strings::padRight','replacere'=>'Nette\Utils\Strings::replace','url'=>'rawurlencode','striptags'=>'strip_tags','nl2br'=>'nl2br','substr'=>'iconv_substr','repeat'=>'str_repeat','implode'=>'implode','number'=>'number_format',);public
static$dateFormat='%x';public static function loader($helper){if(method_exists(__CLASS__,$helper)){return
callback(__CLASS__,$helper);}elseif(isset(self::$helpers[$helper])){return self::$helpers[$helper];}}public
static function escapeHtml($s,$quotes=ENT_QUOTES){if(is_object($s)&&($s instanceof
ITemplate||$s instanceof Html||$s instanceof Form)){return$s->__toString(TRUE);}return
htmlSpecialChars($s,$quotes);}public static function escapeHtmlComment($s){return
str_replace('--','--><!-- ',$s);}public static function escapeXML($s){return htmlSpecialChars(preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#','',$s),ENT_QUOTES);}public
static function escapeCss($s){return addcslashes($s,"\x00..\x1F!\"#$%&'()*+,./:;<=>?@[\\]^`{|}~");}public
static function escapeJs($s){if(is_object($s)&&($s instanceof ITemplate||$s instanceof
Html||$s instanceof Form)){$s=$s->__toString(TRUE);}return str_replace(']]>',']]\x3E',Nette\Utils\Json::encode($s));}public
static function escapeICal($s){return addcslashes(preg_replace('#[\x00-\x08\x0B\x0C-\x1F]+#','',$s),"\";\\,:\n");}public
static function strip($s){return Strings::replace($s,'#(</textarea|</pre|</script|^).*?(?=<textarea|<pre|<script|$)#si',function($m){return
trim(preg_replace("#[ \t\r\n]+#"," ",$m[0]));});}public static function indent($s,$level=1,$chars="\t"){if($level>=1){$s=Strings::replace($s,'#<(textarea|pre).*?</\\1#si',function($m){return
strtr($m[0]," \t\r\n","\x1F\x1E\x1D\x1A");});$s=Strings::indent($s,$level,$chars);$s=strtr($s,"\x1F\x1E\x1D\x1A"," \t\r\n");}return$s;}public
static function date($time,$format=NULL){if($time==NULL){return NULL;}if(!isset($format)){$format=self::$dateFormat;}$time=Nette\DateTime::from($time);return
strpos($format,'%')===FALSE?$time->format($format):strftime($format,$time->format('U'));}public
static function bytes($bytes,$precision=2){$bytes=round($bytes);$units=array('B','kB','MB','GB','TB','PB');foreach($units
as$unit){if(abs($bytes)<1024||$unit===end($units)){break;}$bytes=$bytes/1024;}return
round($bytes,$precision).' '.$unit;}public static function length($var){return is_string($var)?Strings::length($var):count($var);}public
static function replace($subject,$search,$replacement=''){return str_replace($search,$replacement,$subject);}public
static function dataStream($data,$type=NULL){if($type===NULL){$type=Nette\Utils\MimeTypeDetector::fromString($data,NULL);}return
'data:'.($type?"$type;":'').'base64,'.base64_encode($data);}public static function
null($value){return '';}}}

 namespace Nette\Templating{use Nette;interface ITemplate{function render();}}

 namespace Nette\Utils{use Nette;final class Json{const FORCE_ARRAY=1;private static$messages=array(JSON_ERROR_DEPTH=>'The maximum stack depth has been exceeded',JSON_ERROR_STATE_MISMATCH=>'Syntax error, malformed JSON',JSON_ERROR_CTRL_CHAR=>'Unexpected control character found',JSON_ERROR_SYNTAX=>'Syntax error, malformed JSON',);final
public function __construct(){throw new Nette\StaticClassException;}public static
function encode($value){Nette\Diagnostics\Debugger::tryError();if(function_exists('ini_set')){$old=ini_set('display_errors',0);$json=json_encode($value);ini_set('display_errors',$old);}else{$json=json_encode($value);}if(Nette\Diagnostics\Debugger::catchError($e)){throw
new JsonException($e->getMessage());}return$json;}public static function decode($json,$options=0){$json=(string)$json;$value=json_decode($json,(bool)($options&self::FORCE_ARRAY));if($value===NULL&&$json!==''&&strcasecmp($json,'null')){$error=PHP_VERSION_ID>=50300?json_last_error():0;throw
new JsonException(isset(self::$messages[$error])?self::$messages[$error]:'Unknown error',$error);}return$value;}}class
JsonException extends \Exception{}}

 namespace Nette\Utils{use Nette;final class LimitedScope{private static$vars;final
public function __construct(){throw new Nette\StaticClassException;}public static
function evaluate(){if(func_num_args()>1){self::$vars=func_get_arg(1);extract(self::$vars);}$res=eval('?>'.func_get_arg(0));if($res===FALSE&&($error=error_get_last())&&$error['type']===E_PARSE){throw
new Nette\FatalErrorException($error['message'],0,$error['type'],$error['file'],$error['line'],NULL);}return$res;}public
static function load(){if(func_num_args()>1){self::$vars=func_get_arg(1);extract(self::$vars);}return
include func_get_arg(0);}}}

 namespace Nette\Utils{use Nette;class Strings{final public function __construct(){throw
new Nette\StaticClassException;}public static function checkEncoding($s,$encoding='UTF-8'){return$s===self::fixEncoding($s,$encoding);}public
static function fixEncoding($s,$encoding='UTF-8'){$s=@iconv('UTF-16',$encoding.'//IGNORE',iconv($encoding,'UTF-16//IGNORE',$s));return
str_replace("\xEF\xBB\xBF",'',$s);}public static function chr($code,$encoding='UTF-8'){return
iconv('UTF-32BE',$encoding.'//IGNORE',pack('N',$code));}public static function startsWith($haystack,$needle){return
strncmp($haystack,$needle,strlen($needle))===0;}public static function endsWith($haystack,$needle){return
strlen($needle)===0||substr($haystack,-strlen($needle))===$needle;}public static
function normalize($s){$s=str_replace("\r\n","\n",$s);$s=strtr($s,"\r","\n");$s=preg_replace('#[\x00-\x08\x0B-\x1F\x7F]+#','',$s);$s=preg_replace("#[\t ]+$#m",'',$s);$s=trim($s,"\n");return$s;}public
static function toAscii($s){$s=preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u','',$s);$s=strtr($s,'`\'"^~',"\x01\x02\x03\x04\x05");if(ICONV_IMPL==='glibc'){$s=@iconv('UTF-8','WINDOWS-1250//TRANSLIT',$s);$s=strtr($s,"\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"."\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"."\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"."\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe","ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt");}else{$s=@iconv('UTF-8','ASCII//TRANSLIT',$s);}$s=str_replace(array('`',"'",'"','^','~'),'',$s);return
strtr($s,"\x01\x02\x03\x04\x05",'`\'"^~');}public static function webalize($s,$charlist=NULL,$lower=TRUE){$s=self::toAscii($s);if($lower){$s=strtolower($s);}$s=preg_replace('#[^a-z0-9'.preg_quote($charlist,'#').']+#i','-',$s);$s=trim($s,'-');return$s;}public
static function truncate($s,$maxLen,$append="\xE2\x80\xA6"){if(self::length($s)>$maxLen){$maxLen=$maxLen-self::length($append);if($maxLen<1){return$append;}elseif($matches=self::match($s,'#^.{1,'.$maxLen.'}(?=[\s\x00-/:-@\[-`{-~])#us')){return$matches[0].$append;}else{return
iconv_substr($s,0,$maxLen,'UTF-8').$append;}}return$s;}public static function indent($s,$level=1,$chars="\t"){return$level<1?$s:self::replace($s,'#(?:^|[\r\n]+)(?=[^\r\n])#','$0'.str_repeat($chars,$level));}public
static function lower($s){return mb_strtolower($s,'UTF-8');}public static function
upper($s){return mb_strtoupper($s,'UTF-8');}public static function firstUpper($s){return
self::upper(mb_substr($s,0,1,'UTF-8')).mb_substr($s,1,self::length($s),'UTF-8');}public
static function capitalize($s){return mb_convert_case($s,MB_CASE_TITLE,'UTF-8');}public
static function compare($left,$right,$len=NULL){if($len<0){$left=iconv_substr($left,$len,-$len,'UTF-8');$right=iconv_substr($right,$len,-$len,'UTF-8');}elseif($len!==NULL){$left=iconv_substr($left,0,$len,'UTF-8');$right=iconv_substr($right,0,$len,'UTF-8');}return
self::lower($left)===self::lower($right);}public static function length($s){return
function_exists('mb_strlen')?mb_strlen($s,'UTF-8'):strlen(utf8_decode($s));}public
static function trim($s,$charlist=" \t\n\r\0\x0B\xC2\xA0"){$charlist=preg_quote($charlist,'#');return
self::replace($s,'#^['.$charlist.']+|['.$charlist.']+$#u','');}public static function
padLeft($s,$length,$pad=' '){$length=max(0,$length-self::length($s));$padLen=self::length($pad);return
str_repeat($pad,$length/$padLen).iconv_substr($pad,0,$length%$padLen,'UTF-8').$s;}public
static function padRight($s,$length,$pad=' '){$length=max(0,$length-self::length($s));$padLen=self::length($pad);return$s.str_repeat($pad,$length/$padLen).iconv_substr($pad,0,$length%$padLen,'UTF-8');}public
static function random($length=10,$charlist='0-9a-z'){$charlist=str_shuffle(preg_replace_callback('#.-.#',function($m){return
implode('',range($m[0][0],$m[0][2]));},$charlist));$chLen=strlen($charlist);$s='';for($i=0;$i<$length;$i++){if($i%5===0){$rand=lcg_value();$rand2=microtime(TRUE);}$rand*=$chLen;$s.=$charlist[($rand+$rand2)%$chLen];$rand-=(int)$rand;}return$s;}public
static function split($subject,$pattern,$flags=0){Nette\Diagnostics\Debugger::tryError();$res=preg_split($pattern,$subject,-1,$flags|PREG_SPLIT_DELIM_CAPTURE);self::catchPregError($pattern);return$res;}public
static function match($subject,$pattern,$flags=0,$offset=0){Nette\Diagnostics\Debugger::tryError();$res=preg_match($pattern,$subject,$m,$flags,$offset);self::catchPregError($pattern);if($res){return$m;}}public
static function matchAll($subject,$pattern,$flags=0,$offset=0){Nette\Diagnostics\Debugger::tryError();$res=preg_match_all($pattern,$subject,$m,($flags&PREG_PATTERN_ORDER)?$flags:($flags|PREG_SET_ORDER),$offset);self::catchPregError($pattern);return$m;}public
static function replace($subject,$pattern,$replacement=NULL,$limit=-1){Nette\Diagnostics\Debugger::tryError();if(is_object($replacement)||is_array($replacement)){if($replacement
instanceof Nette\Callback){$replacement=$replacement->getNative();}if(!is_callable($replacement,FALSE,$textual)){Nette\Diagnostics\Debugger::catchError($foo);throw
new Nette\InvalidStateException("Callback '$textual' is not callable.");}$res=preg_replace_callback($pattern,$replacement,$subject,$limit);if(Nette\Diagnostics\Debugger::catchError($e)){$trace=$e->getTrace();if(isset($trace[2]['class'])&&$trace[2]['class']===__CLASS__){throw
new RegexpException($e->getMessage()." in pattern: $pattern");}}}elseif(is_array($pattern)){$res=preg_replace(array_keys($pattern),array_values($pattern),$subject,$limit);}else{$res=preg_replace($pattern,$replacement,$subject,$limit);}self::catchPregError($pattern);return$res;}public
static function catchPregError($pattern){if(Nette\Diagnostics\Debugger::catchError($e)){throw
new RegexpException($e->getMessage()." in pattern: $pattern");}elseif(preg_last_error()){static$messages=array(PREG_INTERNAL_ERROR=>'Internal error',PREG_BACKTRACK_LIMIT_ERROR=>'Backtrack limit was exhausted',PREG_RECURSION_LIMIT_ERROR=>'Recursion limit was exhausted',PREG_BAD_UTF8_ERROR=>'Malformed UTF-8 data',5=>'Offset didn\'t correspond to the begin of a valid UTF-8 code point',);$code=preg_last_error();throw
new RegexpException((isset($messages[$code])?$messages[$code]:'Unknown error')." (pattern: $pattern)",$code);}}}class
RegexpException extends \Exception{}}

 namespace Nette\Caching{use Nette;class Cache extends Nette\Object implements \ArrayAccess{const
PRIORITY='priority',EXPIRATION='expire',EXPIRE='expire',SLIDING='sliding',TAGS='tags',FILES='files',ITEMS='items',CONSTS='consts',CALLBACKS='callbacks',ALL='all';const
NAMESPACE_SEPARATOR="\x00";private$storage;private$namespace;private$key;private$data;public
function __construct(IStorage$storage,$namespace=NULL){$this->storage=$storage;$this->namespace=$namespace.self::NAMESPACE_SEPARATOR;}public
function getStorage(){return$this->storage;}public function getNamespace(){return
(string) substr($this->namespace,0,-1);}public function derive($namespace){$derived=new
static($this->storage,$this->namespace.$namespace);return$derived;}public function
release(){$this->key=$this->data=NULL;}public function load($key){$key=is_scalar($key)?(string)$key:serialize($key);if($this->key===$key){return$this->data;}$this->key=$key;$this->data=$this->storage->read($this->namespace.md5($key));return$this->data;}public
function save($key,$data,array$dp=NULL){$this->key=is_scalar($key)?(string)$key:serialize($key);$key=$this->namespace.md5($this->key);if(isset($dp[Cache::EXPIRATION])){$dp[Cache::EXPIRATION]=Nette\DateTime::from($dp[Cache::EXPIRATION])->format('U')-time();}if(isset($dp[self::FILES])){foreach((array)$dp[self::FILES]as$item){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkFile'),$item,@filemtime($item));}unset($dp[self::FILES]);}if(isset($dp[self::ITEMS])){$dp[self::ITEMS]=(array)$dp[self::ITEMS];foreach($dp[self::ITEMS]as$k=>$item){$dp[self::ITEMS][$k]=$this->namespace.md5(is_scalar($item)?$item:serialize($item));}}if(isset($dp[self::CONSTS])){foreach((array)$dp[self::CONSTS]as$item){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkConst'),$item,constant($item));}unset($dp[self::CONSTS]);}if($data
instanceof Nette\Callback||$data instanceof \Closure){Nette\Utils\CriticalSection::enter();$data=$data->__invoke();Nette\Utils\CriticalSection::leave();}if(is_object($data)){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkSerializationVersion'),get_class($data),Nette\Reflection\ClassType::from($data)->getAnnotation('serializationVersion'));}$this->data=$data;if($data===NULL){$this->storage->remove($key);}else{$this->storage->write($key,$data,(array)$dp);}return$data;}public
function clean(array$conds=NULL){$this->release();$this->storage->clean((array)$conds);}public
function call($function){$key=func_get_args();if($this->load($key)===NULL){array_shift($key);return$this->save($this->key,call_user_func_array($function,$key));}else{return$this->data;}}public
function start($key){if($this->offsetGet($key)===NULL){return new OutputHelper($this,$key);}else{echo$this->data;}}public
function offsetSet($key,$data){$this->save($key,$data);}public function offsetGet($key){return$this->load($key);}public
function offsetExists($key){return$this->load($key)!==NULL;}public function offsetUnset($key){$this->save($key,NULL);}public
static function checkCallbacks($callbacks){foreach($callbacks as$callback){$func=array_shift($callback);if(!call_user_func_array($func,$callback)){return
FALSE;}}return TRUE;}private static function checkConst($const,$value){return defined($const)&&constant($const)===$value;}private
static function checkFile($file,$time){return@filemtime($file)==$time;}private static
function checkSerializationVersion($class,$value){return Nette\Reflection\ClassType::from($class)->getAnnotation('serializationVersion')===$value;}}}

 namespace Nette\Caching\Storages{use Nette,Nette\Caching\Cache;class FileStorage
extends Nette\Object implements Nette\Caching\IStorage{const META_HEADER_LEN=28,META_TIME='time',META_SERIALIZED='serialized',META_EXPIRE='expire',META_DELTA='delta',META_ITEMS='di',META_CALLBACKS='callbacks';const
FILE='file',HANDLE='handle';public static$gcProbability=0.001;public static$useDirectories=TRUE;private$dir;private$useDirs;private$journal;public
function __construct($dir,IJournal$journal=NULL){$this->dir=realpath($dir);if($this->dir===FALSE){throw
new Nette\DirectoryNotFoundException("Directory '$dir' not found.");}$this->useDirs=(bool)
self::$useDirectories;$this->journal=$journal;if(mt_rand()/mt_getrandmax()<self::$gcProbability){$this->clean(array());}}public
function read($key){$meta=$this->readMetaAndLock($this->getCacheFile($key),LOCK_SH);if($meta&&$this->verify($meta)){return$this->readData($meta);}else{return
NULL;}}private function verify($meta){do{if(!empty($meta[self::META_DELTA])){if(filemtime($meta[self::FILE])+$meta[self::META_DELTA]<time()){break;}touch($meta[self::FILE]);}elseif(!empty($meta[self::META_EXPIRE])&&$meta[self::META_EXPIRE]<time()){break;}if(!empty($meta[self::META_CALLBACKS])&&!Cache::checkCallbacks($meta[self::META_CALLBACKS])){break;}if(!empty($meta[self::META_ITEMS])){foreach($meta[self::META_ITEMS]as$depFile=>$time){$m=$this->readMetaAndLock($depFile,LOCK_SH);if($m[self::META_TIME]!==$time||($m&&!$this->verify($m))){break
2;}}}return TRUE;}while(FALSE);$this->delete($meta[self::FILE],$meta[self::HANDLE]);return
FALSE;}public function write($key,$data,array$dp){$meta=array(self::META_TIME=>microtime(),);if(isset($dp[Cache::EXPIRATION])){if(empty($dp[Cache::SLIDING])){$meta[self::META_EXPIRE]=$dp[Cache::EXPIRATION]+time();}else{$meta[self::META_DELTA]=(int)$dp[Cache::EXPIRATION];}}if(isset($dp[Cache::ITEMS])){foreach((array)$dp[Cache::ITEMS]as$item){$depFile=$this->getCacheFile($item);$m=$this->readMetaAndLock($depFile,LOCK_SH);$meta[self::META_ITEMS][$depFile]=$m[self::META_TIME];unset($m);}}if(isset($dp[Cache::CALLBACKS])){$meta[self::META_CALLBACKS]=$dp[Cache::CALLBACKS];}$cacheFile=$this->getCacheFile($key);if($this->useDirs&&!is_dir($dir=dirname($cacheFile))){umask(0000);if(!mkdir($dir,0777)){return;}}$handle=@fopen($cacheFile,'r+b');if(!$handle){$handle=fopen($cacheFile,'wb');if(!$handle){return;}}if(isset($dp[Cache::TAGS])||isset($dp[Cache::PRIORITY])){if(!$this->journal){throw
new Nette\InvalidStateException('CacheJournal has not been provided.');}$this->journal->write($cacheFile,$dp);}flock($handle,LOCK_EX);ftruncate($handle,0);if(!is_string($data)){$data=serialize($data);$meta[self::META_SERIALIZED]=TRUE;}$head=serialize($meta).'?>';$head='<?php //netteCache[01]'.str_pad((string)
strlen($head),6,'0',STR_PAD_LEFT).$head;$headLen=strlen($head);$dataLen=strlen($data);do{if(fwrite($handle,str_repeat("\x00",$headLen),$headLen)!==$headLen){break;}if(fwrite($handle,$data,$dataLen)!==$dataLen){break;}fseek($handle,0);if(fwrite($handle,$head,$headLen)!==$headLen){break;}flock($handle,LOCK_UN);fclose($handle);return
TRUE;}while(FALSE);$this->delete($cacheFile,$handle);}public function remove($key){$this->delete($this->getCacheFile($key));}public
function clean(array$conds){$all=!empty($conds[Cache::ALL]);$collector=empty($conds);if($all||$collector){$now=time();foreach(Nette\Utils\Finder::find('*')->from($this->dir)->childFirst()as$entry){$path=(string)$entry;if($entry->isDir()){@rmdir($path);continue;}if($all){$this->delete($path);}else{$meta=$this->readMetaAndLock($path,LOCK_SH);if(!$meta){continue;}if(!empty($meta[self::META_EXPIRE])&&$meta[self::META_EXPIRE]<$now){$this->delete($path,$meta[self::HANDLE]);continue;}flock($meta[self::HANDLE],LOCK_UN);fclose($meta[self::HANDLE]);}}if($this->journal){$this->journal->clean($conds);}return;}if($this->journal){foreach($this->journal->clean($conds)as$file){$this->delete($file);}}}protected
function readMetaAndLock($file,$lock){$handle=@fopen($file,'r+b');if(!$handle){return
NULL;}flock($handle,$lock);$head=stream_get_contents($handle,self::META_HEADER_LEN);if($head&&strlen($head)===self::META_HEADER_LEN){$size=(int)
substr($head,-6);$meta=stream_get_contents($handle,$size,self::META_HEADER_LEN);$meta=@unserialize($meta);if(is_array($meta)){fseek($handle,$size+self::META_HEADER_LEN);$meta[self::FILE]=$file;$meta[self::HANDLE]=$handle;return$meta;}}flock($handle,LOCK_UN);fclose($handle);return
NULL;}protected function readData($meta){$data=stream_get_contents($meta[self::HANDLE]);flock($meta[self::HANDLE],LOCK_UN);fclose($meta[self::HANDLE]);if(empty($meta[self::META_SERIALIZED])){return$data;}else{return@unserialize($data);}}protected
function getCacheFile($key){$file=urlencode($key);if($this->useDirs&&$a=strrpos($file,'%00')){$file=substr_replace($file,'/_',$a,3);}return$this->dir.'/_'.$file;}private
static function delete($file,$handle=NULL){if(@unlink($file)){if($handle){flock($handle,LOCK_UN);fclose($handle);}return;}if(!$handle){$handle=@fopen($file,'r+');}if($handle){flock($handle,LOCK_EX);ftruncate($handle,0);flock($handle,LOCK_UN);fclose($handle);@unlink($file);}}}}

 namespace Nette{use Nette;final class Callback extends Object{private$cb;public function
__construct($t,$m=NULL){if($m===NULL){if(is_string($t)){$t=explode('::',$t,2);$this->cb=isset($t[1])?$t:$t[0];}elseif(is_object($t)){$this->cb=$t
instanceof \Closure?$t:array($t,'__invoke');}else{$this->cb=$t;}}else{$this->cb=array($t,$m);}if(!is_callable($this->cb,TRUE)){throw
new InvalidArgumentException("Invalid callback.");}}public function __invoke(){if(!is_callable($this->cb)){throw
new InvalidStateException("Callback '$this' is not callable.");}$args=func_get_args();return
call_user_func_array($this->cb,$args);}public function invoke(){if(!is_callable($this->cb)){throw
new InvalidStateException("Callback '$this' is not callable.");}$args=func_get_args();return
call_user_func_array($this->cb,$args);}public function invokeArgs(array$args){if(!is_callable($this->cb)){throw
new InvalidStateException("Callback '$this' is not callable.");}return call_user_func_array($this->cb,$args);}public
function invokeNamedArgs(array$args){$ref=$this->toReflection();if(is_array($this->cb)){return$ref->invokeNamedArgs(is_object($this->cb[0])?$this->cb[0]:NULL,$args);}else{return$ref->invokeNamedArgs($args);}}public
function isCallable(){return is_callable($this->cb);}public function getNative(){return$this->cb;}public
function toReflection(){if(is_array($this->cb)){return new Nette\Reflection\Method($this->cb[0],$this->cb[1]);}else{return
new Nette\Reflection\GlobalFunction($this->cb);}}public function isStatic(){return
is_array($this->cb)?is_string($this->cb[0]):is_string($this->cb);}public function
__toString(){if($this->cb instanceof \Closure){return '{closure}';}elseif(is_string($this->cb)&&$this->cb[0]==="\0"){return
'{lambda}';}else{is_callable($this->cb,TRUE,$textual);return$textual;}}}}

 namespace Nette{use Nette,Nette\Caching\Cache,Nette\DI;class Configurator extends
Object{public static$instance;public$defaultConfigFile='%appDir%/config.neon';private$container;public
function __construct($containerClass='Nette\DI\Container'){self::$instance=$this;$this->container=new$containerClass;foreach(get_class_methods($this)as$name){if(substr($name,0,13)==='createService'){$this->container->addService(strtolower($name[13]).substr($name,14),array(get_called_class(),$name));}}$this->container->params=new
ArrayHash;defined('WWW_DIR')&&$this->container->params['wwwDir']=realpath(WWW_DIR);defined('APP_DIR')&&$this->container->params['appDir']=realpath(APP_DIR);defined('LIBS_DIR')&&$this->container->params['libsDir']=realpath(LIBS_DIR);defined('TEMP_DIR')&&$this->container->params['tempDir']=realpath(TEMP_DIR);$this->container->params['productionMode']=self::detectProductionMode();$this->container->params['consoleMode']=PHP_SAPI==='cli';}public
function getContainer(){return$this->container;}public function loadConfig($file,$section=NULL){if($file===NULL){$file=$this->defaultConfigFile;}$container=$this->container;$file=$container->expand($file);if(!is_file($file)){$file=preg_replace('#\.neon$#','.ini',$file);}if($section===NULL){if(PHP_SAPI==='cli'){$section=Environment::CONSOLE;}else{$section=$container->params['productionMode']?Environment::PRODUCTION:Environment::DEVELOPMENT;}}$cache=new
Cache($container->templateCacheStorage,'Nette.Configurator');$cacheKey=array((array)$container->params,$file,$section);$cached=$cache->load($cacheKey);if($cached){require$cached['file'];fclose($cached['handle']);return$this->container;}$config=Nette\Config\Config::fromFile($file,$section);$code="<?php\n// source file $file\n\n";foreach(array('service','variable')as$item){if(isset($config[$item])){trigger_error(basename($file).": Section '$item' is deprecated; use plural form '{$item}s' instead.",E_USER_WARNING);$config[$item.'s']=$config[$item];unset($config[$item]);}}while(!empty($config['variables'])){$old=$config['variables'];foreach($config['variables']as$key=>$value){try{$code.=$this->generateCode('$container->params[?] = ?',$key,$container->params[$key]=$container->expand($value));unset($config['variables'][$key]);}catch(Nette\InvalidArgumentException$e){}}if($old===$config['variables']){throw
new InvalidStateException("Unable to expand variables: ".implode(', ',array_keys($old)).".");}}unset($config['variables']);if(isset($config['services'])){foreach($config['services']as$key=>&$def){if(preg_match('#^Nette\\\\.*\\\\I?([a-zA-Z]+)$#',strtr($key,'-','\\'),$m)){$m[1][0]=strtolower($m[1][0]);trigger_error(basename($file).": service name '$key' has been renamed to '$m[1]'",E_USER_WARNING);$key=$m[1];}if(is_scalar($def)){$def=array('class'=>$def);}if(method_exists(get_called_class(),"createService$key")){$container->removeService($key);if(!isset($def['factory'])&&!isset($def['class'])){$def['factory']=array(get_called_class(),"createService$key");}}if(isset($def['option'])){$def['arguments'][]=$def['option'];}if(!empty($def['run'])){$def['tags']=array('run');}}$builder=new
DI\ContainerBuilder;$code.=$builder->generateCode($config['services']);unset($config['services']);}array_walk_recursive($config,function(&$val){$val=Environment::expand($val);});if(isset($config['php'])){foreach($config['php']as$key=>$value){if(is_array($value)){foreach($value
as$k=>$v){$code.=$this->configurePhp("$key.$k",$v);}}else{$code.=$this->configurePhp($key,$value);}}unset($config['php']);}if(isset($config['const'])){foreach($config['const']as$key=>$value){$code.=$this->generateCode('define',$key,$value);}unset($config['const']);}if(isset($config['mode'])){trigger_error(basename($file).": Section 'mode' is deprecated; use 'params' instead.",E_USER_WARNING);foreach($config['mode']as$mode=>$state){$code.=$this->generateCode('$container->params[?] = ?',$mode.'Mode',(bool)$state);}unset($config['mode']);}foreach($config
as$key=>$value){$code.=$this->generateCode('$container->params[?] = '.(is_array($value)?'Nette\ArrayHash::from(?)':'?'),$key,$value);}$code.=self::preloadEnvironment($container);$code.='foreach ($container->getServiceNamesByTag("run") as $name => $foo) { $container->getService($name); }'."\n";$cache->save($cacheKey,$code,array(Cache::FILES=>$file,));Nette\Utils\LimitedScope::evaluate($code,array('container'=>$container));return$this->container;}public
static function detectProductionMode(){if(!isset($_SERVER['SERVER_ADDR'])&&!isset($_SERVER['LOCAL_ADDR'])){return
TRUE;}$addrs=array();if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){$addrs=preg_split('#,\s*#',$_SERVER['HTTP_X_FORWARDED_FOR']);}if(isset($_SERVER['REMOTE_ADDR'])){$addrs[]=$_SERVER['REMOTE_ADDR'];}$addrs[]=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['LOCAL_ADDR'];foreach($addrs
as$addr){$oct=explode('.',$addr);if($addr!=='::1'&&(count($oct)!==4||($oct[0]!=='10'&&$oct[0]!=='127'&&($oct[0]!=='172'||$oct[1]<16||$oct[1]>31)&&($oct[0]!=='169'||$oct[1]!=='254')&&($oct[0]!=='192'||$oct[1]!=='168')))){return
TRUE;}}return FALSE;}public function configurePhp($name,$value){if(!is_scalar($value)){throw
new Nette\InvalidStateException("Configuration value for directive '$name' is not scalar.");}switch($name){case
'include_path':return$this->generateCode('set_include_path',str_replace(';',PATH_SEPARATOR,$value));case
'ignore_user_abort':return$this->generateCode('ignore_user_abort',$value);case 'max_execution_time':return$this->generateCode('set_time_limit',$value);case
'date.timezone':return$this->generateCode('date_default_timezone_set',$value);}if(function_exists('ini_set')){return$this->generateCode('ini_set',$name,$value);}elseif(ini_get($name)!=$value&&!Framework::$iAmUsingBadHost){throw
new Nette\NotSupportedException('Required function ini_set() is disabled.');}}private
static function generateCode($statement){$args=func_get_args();unset($args[0]);foreach($args
as&$arg){$arg=var_export($arg,TRUE);}if(strpos($statement,'?')===FALSE){return$statement.='('.implode(', ',$args).");\n\n";}$a=strpos($statement,'?');$i=1;while($a!==FALSE){$statement=substr_replace($statement,$args[$i],$a,1);$a=strpos($statement,'?',$a+strlen($args[$i]));$i++;}return$statement.";\n\n";}public
static function createServiceApplication(DI\Container$container,array$options=NULL){$context=new
DI\Container;$context->addService('httpRequest',$container->httpRequest);$context->addService('httpResponse',$container->httpResponse);$context->addService('session',$container->session);$context->addService('presenterFactory',$container->presenterFactory);$context->addService('router',$container->router);Nette\Application\UI\Presenter::$invalidLinkMode=$container->params['productionMode']?Nette\Application\UI\Presenter::INVALID_LINK_SILENT:Nette\Application\UI\Presenter::INVALID_LINK_WARNING;$class=isset($options['class'])?$options['class']:'Nette\Application\Application';$application=new$class($context);$application->catchExceptions=$container->params['productionMode'];if($container->session->exists()){$application->onStartup[]=function()use($container){$container->session->start();};}return$application;}public
static function createServicePresenterFactory(DI\Container$container){return new
Nette\Application\PresenterFactory(isset($container->params['appDir'])?$container->params['appDir']:NULL,$container);}public
static function createServiceRouter(DI\Container$container){return new Nette\Application\Routers\RouteList;}public
static function createServiceHttpRequest(){$factory=new Nette\Http\RequestFactory;$factory->setEncoding('UTF-8');return$factory->createHttpRequest();}public
static function createServiceHttpResponse(){return new Nette\Http\Response;}public
static function createServiceHttpContext(DI\Container$container){return new Nette\Http\Context($container->httpRequest,$container->httpResponse);}public
static function createServiceSession(DI\Container$container,array$options=NULL){$session=new
Nette\Http\Session($container->httpRequest,$container->httpResponse);$session->setOptions((array)$options);if(isset($options['expiration'])){$session->setExpiration($options['expiration']);}return$session;}public
static function createServiceUser(DI\Container$container){$context=new DI\Container;$context->addService('authenticator',function()use($container){return$container->authenticator;});$context->addService('authorizator',function()use($container){return$container->authorizator;});$context->addService('session',$container->session);return
new Nette\Http\User($context);}public static function createServiceCacheStorage(DI\Container$container){if(!isset($container->params['tempDir'])){throw
new Nette\InvalidStateException("Service cacheStorage requires that parameter 'tempDir' contains path to temporary directory.");}$dir=$container->expand('%tempDir%/cache');umask(0000);@mkdir($dir,0777);return
new Nette\Caching\Storages\FileStorage($dir,$container->cacheJournal);}public static
function createServiceTemplateCacheStorage(DI\Container$container){if(!isset($container->params['tempDir'])){throw
new Nette\InvalidStateException("Service templateCacheStorage requires that parameter 'tempDir' contains path to temporary directory.");}$dir=$container->expand('%tempDir%/cache');umask(0000);@mkdir($dir,0777);return
new Nette\Caching\Storages\PhpFileStorage($dir);}public static function createServiceCacheJournal(DI\Container$container){return
new Nette\Caching\Storages\FileJournal($container->params['tempDir']);}public static
function createServiceMailer(DI\Container$container,array$options=NULL){if(empty($options['smtp'])){return
new Nette\Mail\SendmailMailer;}else{return new Nette\Mail\SmtpMailer($options);}}public
static function createServiceRobotLoader(DI\Container$container,array$options=NULL){$loader=new
Nette\Loaders\RobotLoader;$loader->autoRebuild=isset($options['autoRebuild'])?$options['autoRebuild']:!$container->params['productionMode'];$loader->setCacheStorage($container->cacheStorage);if(isset($options['directory'])){$loader->addDirectory($options['directory']);}else{foreach(array('appDir','libsDir')as$var){if(isset($container->params[$var])){$loader->addDirectory($container->params[$var]);}}}$loader->register();return$loader;}public
static function preloadEnvironment(DI\Container$container){$code='';$dir=$container->expand('%tempDir%/cache');umask(0000);@mkdir($dir,0777);$uniq=uniqid('_',TRUE);umask(0000);if(!@mkdir("$dir/$uniq",0777)){throw
new Nette\InvalidStateException("Unable to write to directory '$dir'. Make this directory writable.");}$useDirs=@file_put_contents("$dir/$uniq/_",'')!==FALSE;@unlink("$dir/$uniq/_");@rmdir("$dir/$uniq");$code.=self::generateCode('Nette\Caching\Storages\FileStorage::$useDirectories = ?',$useDirs);return$code;}}}

 namespace Nette{use Nette;abstract class FreezableObject extends Object implements
IFreezable{private$frozen=FALSE;public function freeze(){$this->frozen=TRUE;}final
public function isFrozen(){return$this->frozen;}public function __clone(){$this->frozen=FALSE;}protected
function updating(){if($this->frozen){$class=get_class($this);throw new InvalidStateException("Cannot modify a frozen object $class.");}}}}

 namespace Nette\Diagnostics{use Nette;class Bar extends Nette\Object{private$panels=array();public
function addPanel(IBarPanel$panel,$id=NULL){if($id===NULL){$c=0;do{$id=get_class($panel).($c++?"-$c":'');}while(isset($this->panels[$id]));}$this->panels[$id]=$panel;}public
function render(){$panels=array();foreach($this->panels as$id=>$panel){try{$panels[]=array('id'=>preg_replace('#[^a-z0-9]+#i','-',$id),'tab'=>$tab=(string)$panel->getTab(),'panel'=>$tab?(string)$panel->getPanel():NULL,);}catch(\Exception$e){$panels[]=array('id'=>"error-$id",'tab'=>"Error: $id",'panel'=>nl2br(htmlSpecialChars((string)$e)),);}}require
__DIR__.'/templates/bar.phtml';}}}

 namespace Nette\Diagnostics{use Nette;class BlueScreen extends Nette\Object{private$panels=array();public
function addPanel($panel,$id=NULL){if($id===NULL){$this->panels[]=$panel;}else{$this->panels[$id]=$panel;}}public
function render(\Exception$exception){$panels=$this->panels;require __DIR__.'/templates/bluescreen.phtml';}public
static function highlightFile($file,$line,$count=15,$vars=array()){if(function_exists('ini_set')){ini_set('highlight.comment','#999; font-style: italic');ini_set('highlight.default','#000');ini_set('highlight.html','#06B');ini_set('highlight.keyword','#D24; font-weight: bold');ini_set('highlight.string','#080');}$start=max(1,$line-floor($count/2));$source=@file_get_contents($file);if(!$source){return;}$source=explode("\n",highlight_string($source,TRUE));$spans=1;$out=$source[0];$source=explode('<br />',$source[1]);array_unshift($source,NULL);$i=$start;while(--$i>=1){if(preg_match('#.*(</?span[^>]*>)#',$source[$i],$m)){if($m[1]!=='</span>'){$spans++;$out.=$m[1];}break;}}$source=array_slice($source,$start,$count,TRUE);end($source);$numWidth=strlen((string)
key($source));foreach($source as$n=>$s){$spans+=substr_count($s,'<span')-substr_count($s,'</span');$s=str_replace(array("\r","\n"),array('',''),$s);preg_match_all('#<[^>]+>#',$s,$tags);if($n===$line){$out.=sprintf("<span class='highlight'>%{$numWidth}s:    %s\n</span>%s",$n,strip_tags($s),implode('',$tags[0]));}else{$out.=sprintf("<span class='line'>%{$numWidth}s:</span>    %s\n",$n,$s);}}$out.=str_repeat('</span>',$spans).'</code>';$out=preg_replace_callback('#">\$(\w+)(&nbsp;)?</span>#',function($m)use($vars){return
isset($vars[$m[1]])?'" title="'.str_replace('"','&quot;',strip_tags(Helpers::htmlDump($vars[$m[1]]))).$m[0]:$m[0];},$out);return$out;}}}

 namespace Nette\Diagnostics{use Nette;final class DefaultBarPanel extends Nette\Object
implements IBarPanel{private$id;public$data;public function __construct($id){$this->id=$id;}public
function getTab(){ob_start();$data=$this->data;if($this->id==='time'){require __DIR__.'/templates/bar.time.tab.phtml';}elseif($this->id==='memory'){require
__DIR__.'/templates/bar.memory.tab.phtml';}elseif($this->id==='dumps'&&$this->data){require
__DIR__.'/templates/bar.dumps.tab.phtml';}elseif($this->id==='errors'&&$this->data){require
__DIR__.'/templates/bar.errors.tab.phtml';}return ob_get_clean();}public function
getPanel(){ob_start();$data=$this->data;if($this->id==='dumps'){require __DIR__.'/templates/bar.dumps.panel.phtml';}elseif($this->id==='errors'){require
__DIR__.'/templates/bar.errors.panel.phtml';}return ob_get_clean();}}}

 namespace Nette\Diagnostics{use Nette;class FireLogger extends Nette\Object{const
DEBUG='debug',INFO='info',WARNING='warning',ERROR='error',CRITICAL='critical';private
static$payload=array('logs'=>array());public static function log($message,$priority=self::DEBUG){if(!isset($_SERVER['HTTP_X_FIRELOGGER'])||headers_sent()){return
FALSE;}$item=array('name'=>'PHP','level'=>$priority,'order'=>count(self::$payload['logs']),'time'=>str_pad(number_format((microtime(TRUE)-Debugger::$time)*1000,1,'.',' '),8,'0',STR_PAD_LEFT).' ms','template'=>'','message'=>'','style'=>'background:#767ab6',);$args=func_get_args();if(isset($args[0])&&is_string($args[0])){$item['template']=array_shift($args);}if(isset($args[0])&&$args[0]instanceof
\Exception){$e=array_shift($args);$trace=$e->getTrace();if(isset($trace[0]['class'])&&$trace[0]['class']==='Nette\Diagnostics\Debugger'&&($trace[0]['function']==='_shutdownHandler'||$trace[0]['function']==='_errorHandler')){unset($trace[0]);}$file=str_replace(dirname(dirname(dirname($e->getFile()))),"\xE2\x80\xA6",$e->getFile());$item['template']=($e
instanceof \ErrorException?'':get_class($e).': ').$e->getMessage().($e->getCode()?' #'.$e->getCode():'').' in '.$file.':'.$e->getLine();$item['pathname']=$e->getFile();$item['lineno']=$e->getLine();}else{$trace=debug_backtrace();if(isset($trace[1]['class'])&&$trace[1]['class']==='Nette\Diagnostics\Debugger'&&($trace[1]['function']==='fireLog')){unset($trace[0]);}foreach($trace
as$frame){if(isset($frame['file'])&&is_file($frame['file'])){$item['pathname']=$frame['file'];$item['lineno']=$frame['line'];break;}}}$item['exc_info']=array('','',array());$item['exc_frames']=array();foreach($trace
as$frame){$frame+=array('file'=>NULL,'line'=>NULL,'class'=>NULL,'type'=>NULL,'function'=>NULL,'object'=>NULL,'args'=>NULL);$item['exc_info'][2][]=array($frame['file'],$frame['line'],"$frame[class]$frame[type]$frame[function]",$frame['object']);$item['exc_frames'][]=$frame['args'];}if(isset($args[0])&&in_array($args[0],array(self::DEBUG,self::INFO,self::WARNING,self::ERROR,self::CRITICAL),TRUE)){$item['level']=array_shift($args);}$item['args']=$args;self::$payload['logs'][]=self::jsonDump($item,-1);foreach(str_split(base64_encode(@json_encode(self::$payload)),4990)as$k=>$v){header("FireLogger-de11e-$k:$v");}return
TRUE;}private static function jsonDump(&$var,$level=0){if(is_bool($var)||is_null($var)||is_int($var)||is_float($var)){return$var;}elseif(is_string($var)){if(Debugger::$maxLen&&strlen($var)>Debugger::$maxLen){$var=substr($var,0,Debugger::$maxLen)." \xE2\x80\xA6 ";}return
Nette\Utils\Strings::fixEncoding($var);}elseif(is_array($var)){static$marker;if($marker===NULL){$marker=uniqid("\x00",TRUE);}if(isset($var[$marker])){return
"\xE2\x80\xA6RECURSION\xE2\x80\xA6";}elseif($level<Debugger::$maxDepth||!Debugger::$maxDepth){$var[$marker]=TRUE;$res=array();foreach($var
as$k=>&$v){if($k!==$marker){$res[self::jsonDump($k)]=self::jsonDump($v,$level+1);}}unset($var[$marker]);return$res;}else{return
" \xE2\x80\xA6 ";}}elseif(is_object($var)){$arr=(array)$var;static$list=array();if(in_array($var,$list,TRUE)){return
"\xE2\x80\xA6RECURSION\xE2\x80\xA6";}elseif($level<Debugger::$maxDepth||!Debugger::$maxDepth){$list[]=$var;$res=array("\x00"=>'(object) '.get_class($var));foreach($arr
as$k=>&$v){if($k[0]==="\x00"){$k=substr($k,strrpos($k,"\x00")+1);}$res[self::jsonDump($k)]=self::jsonDump($v,$level+1);}array_pop($list);return$res;}else{return
" \xE2\x80\xA6 ";}}elseif(is_resource($var)){return "resource ".get_resource_type($var);}else{return
"unknown type";}}}}

 namespace Nette\Diagnostics{use Nette;class Logger extends Nette\Object{const DEBUG='debug',INFO='info',WARNING='warning',ERROR='error',CRITICAL='critical';public
static$emailSnooze=172800;public$mailer=array(__CLASS__,'defaultMailer');public$directory;public$email;public
function log($message,$priority=self::INFO){if(!is_dir($this->directory)){throw new
Nette\DirectoryNotFoundException("Directory '$this->directory' is not found or is not directory.");}if(is_array($message)){$message=implode(' ',$message);}$res=error_log(trim($message).PHP_EOL,3,$this->directory.'/'.strtolower($priority).'.log');if(($priority===self::ERROR||$priority===self::CRITICAL)&&$this->email&&$this->mailer&&@filemtime($this->directory.'/email-sent')+self::$emailSnooze<time()&&@file_put_contents($this->directory.'/email-sent','sent')){call_user_func($this->mailer,$message,$this->email);}return$res;}private
static function defaultMailer($message,$email){$host=isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'');$parts=str_replace(array("\r\n","\n"),array("\n",PHP_EOL),array('headers'=>"From: noreply@$host\nX-Mailer: Nette Framework\n",'subject'=>"PHP: An error occurred on the server $host",'body'=>"[".@date('Y-m-d H:i:s')."] $message",));mail($email,$parts['subject'],$parts['body'],$parts['headers']);}}}

 namespace Nette\Latte{use Nette;class Engine extends Nette\Object{public$parser;public
function __construct(){$this->parser=new Parser;Macros\CoreMacros::install($this->parser);$this->parser->addMacro('cache',new
Macros\CacheMacro($this->parser));Macros\UIMacros::install($this->parser);Macros\FormMacros::install($this->parser);}public
function __invoke($s){$this->parser->context=array(Parser::CONTEXT_TEXT);$this->parser->setDelimiters('\\{(?![\\s\'"{}])','\\}');return$this->parser->parse($s);}}}

 namespace Nette\Latte{use Nette;class HtmlNode extends Nette\Object{public$name;public$isEmpty=FALSE;public$attrs=array();public$closing=FALSE;public$offset;public
function __construct($name){$this->name=$name;$this->isEmpty=isset(Nette\Utils\Html::$emptyElements[strtolower($this->name)]);}}}

 namespace Nette\Latte{use Nette;class MacroNode extends Nette\Object{public$macro;public$name;public$isEmpty=FALSE;public$args;public$modifiers;public$closing=FALSE;public$tokenizer;public$offset;public$parentNode;public$content;public$data;public
function __construct(IMacro$macro,$name,$args=NULL,$modifiers=NULL,MacroNode$parentNode=NULL){$this->macro=$macro;$this->name=(string)$name;$this->modifiers=(string)$modifiers;$this->parentNode=$parentNode;$this->tokenizer=new
MacroTokenizer($this->args);$this->data=new \stdClass;$this->setArgs($args);}public
function setArgs($args){$this->args=(string)$args;$this->tokenizer->tokenize($this->args);}public
function close($content){$this->closing=TRUE;$this->content=$content;return$this->macro->nodeClosed($this);}}}

 namespace Nette\Latte\Macros{use Nette,Nette\Latte;class CacheMacro extends Nette\Object
implements Latte\IMacro{private$used;public function initialize(){$this->used=FALSE;}public
function finalize(){if($this->used){return array('Nette\Latte\Macros\CacheMacro::initRuntime($template, $_g);');}}public
function nodeOpened(Latte\MacroNode$node){$this->used=TRUE;$node->isEmpty=FALSE;return
Latte\PhpWriter::using($node)->write('<?php if (Nette\Latte\Macros\CacheMacro::createCache($netteCacheStorage, %var, $_g->caches, %node.array?)) { ?>',Nette\Utils\Strings::random());}public
function nodeClosed(Latte\MacroNode$node){return '<?php $_l->tmp = array_pop($_g->caches); if (!$_l->tmp instanceof stdClass) $_l->tmp->end(); } ?>';}public
static function initRuntime($template,$global){if(!empty($global->caches)){end($global->caches)->dependencies[Nette\Caching\Cache::FILES][]=$template->getFile();}}public
static function createCache(Nette\Caching\IStorage$cacheStorage,$key,&$parents,$args=NULL){if($args){if(array_key_exists('if',$args)&&!$args['if']){return$parents[]=(object)
NULL;}$key=array_merge(array($key),array_intersect_key($args,range(0,count($args))));}if($parents){end($parents)->dependencies[Nette\Caching\Cache::ITEMS][]=$key;}$cache=new
Nette\Caching\Cache($cacheStorage,'Nette.Templating.Cache');if($helper=$cache->start($key)){$helper->dependencies=array(Nette\Caching\Cache::TAGS=>isset($args['tags'])?$args['tags']:NULL,Nette\Caching\Cache::EXPIRATION=>isset($args['expire'])?$args['expire']:'+ 7 days',);$parents[]=$helper;}return$helper;}}}

 namespace Nette\Latte\Macros{use Nette,Nette\Latte,Nette\Latte\MacroNode;class MacroSet
extends Nette\Object implements Latte\IMacro{public$parser;private$macros;public
function __construct(Latte\Parser$parser){$this->parser=$parser;}public function
addMacro($name,$begin,$end=NULL){$this->macros[$name]=array($begin,$end);$this->parser->addMacro($name,$this);}public
static function install(Latte\Parser$parser){return new static($parser);}public function
initialize(){}public function finalize(){}public function nodeOpened(MacroNode$node){$node->isEmpty=!isset($this->macros[$node->name][1]);return$this->compile($node,$this->macros[$node->name][0]);}public
function nodeClosed(MacroNode$node){return$this->compile($node,$this->macros[$node->name][1]);}private
function compile(MacroNode$node,$def){$writer=Latte\PhpWriter::using($node,$this->parser->context);if(is_string($def)){$code=$writer->write($def);}else{$code=callback($def)->invoke($node,$writer);if($code===FALSE){return
FALSE;}}return"<?php $code ?>";}}}

 namespace Nette\Latte{use Nette,Nette\Utils\Strings;class Parser extends Nette\Object{const
RE_STRING='\'(?:\\\\.|[^\'\\\\])*\'|"(?:\\\\.|[^"\\\\])*"';const N_PREFIX='n:';private$macroRe;private$input;private$output;private$offset;private$macros;private$macroHandlers;private$htmlNodes=array();private$macroNodes=array();public$context;public$templateId;const
CONTEXT_TEXT='text',CONTEXT_CDATA='cdata',CONTEXT_TAG='tag',CONTEXT_ATTRIBUTE='attribute',CONTEXT_NONE='none',CONTEXT_COMMENT='comment';public
function __construct(){$this->macroHandlers=new \SplObjectStorage;$this->setDelimiters('\\{(?![\\s\'"{}])','\\}');$this->context=array(self::CONTEXT_NONE,'text');}public
function addMacro($name,IMacro$macro){$this->macros[$name][]=$macro;$this->macroHandlers->attach($macro);return$this;}public
function parse($s){if(!Strings::checkEncoding($s)){throw new ParseException('Template is not valid UTF-8 stream.');}$s=str_replace("\r\n","\n",$s);$this->templateId=Strings::random();$this->input=&$s;$this->offset=0;$this->output='';$this->htmlNodes=$this->macroNodes=array();foreach($this->macroHandlers
as$handler){$handler->initialize($this);}$len=strlen($s);try{while($this->offset<$len){$matches=$this->{"context".$this->context[0]}();if(!$matches){break;}elseif(!empty($matches['comment'])){}elseif(!empty($matches['macro'])){list($macroName,$macroArgs,$macroModifiers)=$this->parseMacro($matches['macro']);$isRightmost=$this->offset>=$len||$this->input[$this->offset]==="\n";$this->writeMacro($macroName,$macroArgs,$macroModifiers,$isRightmost);}else{$this->output.=$matches[0];}}}catch(ParseException$e){if(!$e->sourceLine){$e->sourceLine=$this->getLine();}throw$e;}$this->output.=substr($this->input,$this->offset);foreach($this->htmlNodes
as$node){if(!empty($node->attrs)){throw new ParseException("Missing end tag </$node->name> for macro-attribute ".self::N_PREFIX.implode(' and '.self::N_PREFIX,array_keys($node->attrs)).".",0,$this->getLine());}}$prologs=$epilogs='';foreach($this->macroHandlers
as$handler){$res=$handler->finalize();$prologs.=isset($res[0])?"<?php $res[0]\n?>":'';$epilogs.=isset($res[1])?"<?php $res[1]\n?>":'';}$this->output=($prologs?$prologs."<?php\n//\n// main template\n//\n?>\n":'').$this->output.$epilogs;if($this->macroNodes){throw
new ParseException("There are unclosed macros.",0,$this->getLine());}return$this->output;}private
function contextText(){$matches=$this->match('~
			(?:(?<=\n|^)[ \t]*)?<(?P<closing>/?)(?P<tag>[a-z0-9:]+)|  ##  begin of HTML tag <tag </tag - ignores <!DOCTYPE
			<(?P<htmlcomment>!--)|           ##  begin of HTML comment <!--
			'.$this->macroRe.'           ##  curly tag
		~xsi');if(!$matches||!empty($matches['macro'])||!empty($matches['comment'])){}elseif(!empty($matches['htmlcomment'])){$this->context=array(self::CONTEXT_COMMENT);}elseif(empty($matches['closing'])){$this->htmlNodes[]=$node=new
HtmlNode($matches['tag']);$node->offset=strlen($this->output);$this->context=array(self::CONTEXT_TAG);}else{do{$node=array_pop($this->htmlNodes);if(!$node){$node=new
HtmlNode($matches['tag']);}}while(strcasecmp($node->name,$matches['tag']));$this->htmlNodes[]=$node;$node->closing=TRUE;$node->offset=strlen($this->output);$this->context=array(self::CONTEXT_TAG);}return$matches;}private
function contextCData(){$node=end($this->htmlNodes);$matches=$this->match('~
			</'.$node->name.'(?![a-z0-9:])| ##  end HTML tag </tag
			'.$this->macroRe.'           ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])&&empty($matches['comment'])){$node->closing=TRUE;$node->offset=strlen($this->output);$this->context=array(self::CONTEXT_TAG);}return$matches;}private
function contextTag(){$matches=$this->match('~
			(?P<end>\ ?/?>)(?P<tagnewline>[ \t]*\n)?|  ##  end of HTML tag
			'.$this->macroRe.'|          ##  curly tag
			\s*(?P<attr>[^\s/>={]+)(?:\s*=\s*(?P<value>["\']|[^\s/>{]+))? ## begin of HTML attribute
		~xsi');if(!$matches||!empty($matches['macro'])||!empty($matches['comment'])){}elseif(!empty($matches['end'])){$node=end($this->htmlNodes);$isEmpty=!$node->closing&&(strpos($matches['end'],'/')!==FALSE||$node->isEmpty);if($isEmpty){$matches[0]=(Nette\Utils\Html::$xhtml?' />':'>').(isset($matches['tagnewline'])?$matches['tagnewline']:'');}if(!empty($node->attrs)){$code=substr($this->output,$node->offset).$matches[0];$this->output=substr($this->output,0,$node->offset);$this->writeAttrsMacro($code,$node->attrs,$node->closing);if($isEmpty){$this->writeAttrsMacro('',$node->attrs,TRUE);}$matches[0]='';}if($isEmpty){$node->closing=TRUE;}if(!$node->closing&&(strcasecmp($node->name,'script')===0||strcasecmp($node->name,'style')===0)){$this->context=array(self::CONTEXT_CDATA,strcasecmp($node->name,'style')?'js':'css');}else{$this->context=array(self::CONTEXT_TEXT);if($node->closing){array_pop($this->htmlNodes);}}}else{$name=$matches['attr'];$value=isset($matches['value'])?$matches['value']:'';$node=end($this->htmlNodes);if(Strings::startsWith($name,self::N_PREFIX)){$name=substr($name,strlen(self::N_PREFIX));if($value==='"'||$value==="'"){if($matches=$this->match('~(.*?)'.$value.'~xsi')){$value=$matches[1];}}$node->attrs[$name]=$value;$matches[0]='';}elseif($value==='"'||$value==="'"){$this->context=array(self::CONTEXT_ATTRIBUTE,$name,$value);}}return$matches;}private
function contextAttribute(){$matches=$this->match('~
			('.$this->context[2].')|      ##  1) end of HTML attribute
			'.$this->macroRe.'                ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])&&empty($matches['comment'])){$this->context=array(self::CONTEXT_TAG);}return$matches;}private
function contextComment(){$matches=$this->match('~
			(--\s*>)|                    ##  1) end of HTML comment
			'.$this->macroRe.'           ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])&&empty($matches['comment'])){$this->context=array(self::CONTEXT_TEXT);}return$matches;}private
function contextNone(){$matches=$this->match('~
			'.$this->macroRe.'           ##  curly tag
		~xsi');return$matches;}private
function match($re){if($matches=Strings::match($this->input,$re,PREG_OFFSET_CAPTURE,$this->offset)){$this->output.=substr($this->input,$this->offset,$matches[0][1]-$this->offset);$this->offset=$matches[0][1]+strlen($matches[0][0]);foreach($matches
as$k=>$v)$matches[$k]=$v[0];}return$matches;}public function getLine(){return$this->input&&$this->offset?substr_count($this->input,"\n",0,$this->offset-1)+1:NULL;}public
function setDelimiters($left,$right){$this->macroRe='
			(?P<comment>'.$left.'\\*.*?\\*'.$right.'\n{0,2})|
			'.$left.'
				(?P<macro>(?:'.self::RE_STRING.'|[^\'"]+?)*?)
			'.$right.'
			(?P<rmargin>[ \t]*(?=\n))?
		';return$this;}public
function writeMacro($name,$args=NULL,$modifiers=NULL,$isRightmost=FALSE){$isLeftmost=trim(substr($this->output,$leftOfs=strrpos("\n$this->output","\n")))==='';if($name[0]==='/'){$node=end($this->macroNodes);if(!$node||("/$node->name"!==$name&&'/'!==$name)||$modifiers||($args&&$node->args&&!Strings::startsWith("$node->args ","$args "))){$name.=$args?' ':'';throw
new ParseException("Unexpected macro {{$name}{$args}{$modifiers}}".($node?", expecting {/$node->name}".($args&&$node->args?" or eventually {/$node->name $node->args}":''):''),0,$this->getLine());}array_pop($this->macroNodes);if(!$node->args){$node->setArgs($args);}if($isLeftmost&&$isRightmost){$this->output=substr($this->output,0,$leftOfs);}$code=$node->close(substr($this->output,$node->offset));if(!$isLeftmost&&$isRightmost&&substr($code,-2)==='?>'){$code.="\n";}$this->output=substr($this->output,0,$node->offset).$node->content.$code;}else{list($node,$code)=$this->expandMacro($name,$args,$modifiers);if(!$node->isEmpty){$this->macroNodes[]=$node;}if($isRightmost){if($isLeftmost&&substr($code,0,11)!=='<?php echo '){$this->output=substr($this->output,0,$leftOfs);}elseif(substr($code,-2)==='?>'){$code.="\n";}}$this->output.=$code;$node->offset=strlen($this->output);}}public
function writeAttrsMacro($code,$attrs,$closing){$left=$right=array();foreach($this->macros
as$name=>$foo){if($name[0]==='@'){$name=substr($name,1);if(isset($attrs[$name])){if(!$closing){$pos=strrpos($code,'>');if($code[$pos-1]==='/'){$pos--;}list(,$macroCode)=$this->expandMacro("@$name",$attrs[$name]);$code=substr_replace($code,$macroCode,$pos,0);}unset($attrs[$name]);}}$macro=$closing?"/$name":$name;if(isset($attrs[$name])){if($closing){$right[]=array($macro,'');}else{array_unshift($left,array($macro,$attrs[$name]));}}$innerName="inner-$name";if(isset($attrs[$innerName])){if($closing){$left[]=array($macro,'');}else{array_unshift($right,array($macro,$attrs[$innerName]));}}$tagName="tag-$name";if(isset($attrs[$tagName])){array_unshift($left,array($name,$attrs[$tagName]));$right[]=array("/$name",'');}unset($attrs[$name],$attrs[$innerName],$attrs[$tagName]);}if($attrs){throw
new ParseException("Unknown macro-attribute ".self::N_PREFIX.implode(' and '.self::N_PREFIX,array_keys($attrs)),0,$this->getLine());}foreach($left
as$item){$this->writeMacro($item[0],$item[1]);if(substr($this->output,-2)==='?>'){$this->output.="\n";}}$this->output.=$code;foreach($right
as$item){$this->writeMacro($item[0],$item[1]);if(substr($this->output,-2)==='?>'){$this->output.="\n";}}}public
function expandMacro($name,$args,$modifiers=NULL){if(empty($this->macros[$name])){throw
new ParseException("Unknown macro {{$name}}",0,$this->getLine());}foreach(array_reverse($this->macros[$name])as$macro){$node=new
MacroNode($macro,$name,$args,$modifiers,$this->macroNodes?end($this->macroNodes):NULL);$code=$macro->nodeOpened($node);if($code!==FALSE){return
array($node,$code);}}throw new ParseException("Unhandled macro {{$name}}",0,$this->getLine());}public
function parseMacro($macro){$match=Strings::match($macro,'~^
			(
				(?P<name>\?|/?[a-z]\w*+(?:[.:]\w+)*+(?!::|\())|   ## ?, name, /name, but not function( or class::
				(?P<noescape>!?)(?P<shortname>/?[=\~#%^&_]?)      ## [!] [=] expression to print
			)(?P<args>.*?)
			(?P<modifiers>\|[a-z](?:'.Parser::RE_STRING.'|[^\'"]+)*)?
		()$~isx');if(!$match){return
FALSE;}if($match['name']===''){$match['name']=$match['shortname']?:'=';if(!$match['noescape']&&substr($match['shortname'],0,1)!=='/'){$match['modifiers'].='|escape';}}return
array($match['name'],trim($match['args']),$match['modifiers']);}}}

 namespace Nette\Latte{use Nette;class PhpWriter extends Nette\Object{private$argsTokenizer;private$modifiers;private$context;public
static function using(MacroNode$node,$context=NULL){return new static($node->tokenizer,$node->modifiers,$context);}public
function __construct(MacroTokenizer$argsTokenizer,$modifiers=NULL,$context=NULL){$this->argsTokenizer=$argsTokenizer;$this->modifiers=$modifiers;$this->context=$context;}public
function write($mask){$args=func_get_args();array_shift($args);$word=strpos($mask,'%node.word')===FALSE?NULL:$this->argsTokenizer->fetchWord();$me=$this;$mask=Nette\Utils\Strings::replace($mask,'#%escape(\(([^()]*+|(?1))+\))#',function($m)use($me){return$me->escape(substr($m[1],1,-1));});$mask=Nette\Utils\Strings::replace($mask,'#%modify(\(([^()]*+|(?1))+\))#',function($m)use($me){return$me->formatModifiers(substr($m[1],1,-1));});return
Nette\Utils\Strings::replace($mask,'#([,+]\s*)?%(node\.word|node\.array|node\.args|var|raw)(\?)?(\s*\+\s*)?()#',function($m)use($me,$word,&$args){list(,$l,$macro,$cond,$r)=$m;switch($macro){case
'node.word':$code=$me->formatWord($word);break;case 'node.args':$code=$me->formatArgs();break;case
'node.array':$code=$me->formatArray();$code=$cond&&$code==='array()'?'':$code;break;case
'var':$code=var_export(array_shift($args),TRUE);break;case 'raw':$code=(string) array_shift($args);break;}if($cond&&$code===''){return$r?$l:$r;}else{return$l.$code.$r;}});}public
function formatModifiers($var){$modifiers=ltrim($this->modifiers,'|');if(!$modifiers){return$var;}$tokenizer=$this->preprocess(new
MacroTokenizer($modifiers));$inside=FALSE;while($token=$tokenizer->fetchToken()){if($token['type']===MacroTokenizer::T_WHITESPACE){$var=rtrim($var).' ';}elseif(!$inside){if($token['type']===MacroTokenizer::T_SYMBOL){if($this->context&&$token['value']==='escape'){$var=$this->escape($var);$tokenizer->fetch('|');}else{$var="\$template->".$token['value']."($var";$inside=TRUE;}}else{throw
new ParseException("Modifier name must be alphanumeric string, '$token[value]' given.");}}else{if($token['value']===':'||$token['value']===','){$var=$var.', ';}elseif($token['value']==='|'){$var=$var.')';$inside=FALSE;}else{$var.=$this->canQuote($tokenizer)?"'$token[value]'":$token['value'];}}}return$inside?"$var)":$var;}public
function formatArgs(){$out='';$tokenizer=$this->preprocess();while($token=$tokenizer->fetchToken()){$out.=$this->canQuote($tokenizer)?"'$token[value]'":$token['value'];}return$out;}public
function formatArray(){$out='';$expand=NULL;$tokenizer=$this->preprocess();while($token=$tokenizer->fetchToken()){if($token['value']==='(expand)'&&$token['depth']===0){$expand=TRUE;$out.='),';}elseif($expand&&($token['value']===',')&&!$token['depth']){$expand=FALSE;$out.=', array(';}else{$out.=$this->canQuote($tokenizer)?"'$token[value]'":$token['value'];}}if($expand===NULL){return"array($out)";}else{return"array_merge(array($out".($expand?', array(':'')."))";}}public
function formatWord($s){return(is_numeric($s)||strspn($s,'\'"$')||in_array(strtolower($s),array('true','false','null')))?$s:'"'.$s.'"';}public
function canQuote($tokenizer){return$tokenizer->isCurrent(MacroTokenizer::T_SYMBOL)&&(!$tokenizer->hasPrev()||$tokenizer->isPrev(',','(','[','=','=>',':','?'))&&(!$tokenizer->hasNext()||$tokenizer->isNext(',',')',']','=','=>',':','|'));}public
function preprocess(MacroTokenizer$tokenizer=NULL){$tokenizer=$tokenizer===NULL?$this->argsTokenizer:$tokenizer;$inTernary=$prev=NULL;$tokens=$arrays=array();while($token=$tokenizer->fetchToken()){$token['depth']=$depth=count($arrays);if($token['type']===MacroTokenizer::T_COMMENT){continue;}elseif($token['type']===MacroTokenizer::T_WHITESPACE){$tokens[]=$token;continue;}if($token['value']==='?'){$inTernary=$depth;}elseif($token['value']===':'){$inTernary=NULL;}elseif($inTernary===$depth&&($token['value']===','||$token['value']===')'||$token['value']===']')){$tokens[]=MacroTokenizer::createToken(':')+array('depth'=>$depth);$tokens[]=MacroTokenizer::createToken('null')+array('depth'=>$depth);$inTernary=NULL;}if($token['value']==='['){if($arrays[]=$prev['value']!==']'&&$prev['type']!==MacroTokenizer::T_SYMBOL&&$prev['type']!==MacroTokenizer::T_VARIABLE){$tokens[]=MacroTokenizer::createToken('array')+array('depth'=>$depth);$token=MacroTokenizer::createToken('(');}}elseif($token['value']===']'){if(array_pop($arrays)===TRUE){$token=MacroTokenizer::createToken(')');}}elseif($token['value']==='('){$arrays[]='(';}elseif($token['value']===')'){array_pop($arrays);}$tokens[]=$prev=$token;}if($inTernary!==NULL){$tokens[]=MacroTokenizer::createToken(':')+array('depth'=>count($arrays));$tokens[]=MacroTokenizer::createToken('null')+array('depth'=>count($arrays));}$tokenizer=clone$tokenizer;$tokenizer->position=0;$tokenizer->tokens=$tokens;return$tokenizer;}public
function escape($s){switch($this->context[0]){case Parser::CONTEXT_TEXT:return"Nette\\Templating\\DefaultHelpers::escapeHtml($s, ENT_NOQUOTES)";case
Parser::CONTEXT_TAG:return"Nette\\Templating\\DefaultHelpers::escapeHtml($s)";case
Parser::CONTEXT_ATTRIBUTE:list(,$name,$quote)=$this->context;$quote=$quote==='"'?'':', ENT_QUOTES';if(strncasecmp($name,'on',2)===0){return"htmlSpecialChars(Nette\\Templating\\DefaultHelpers::escapeJs($s)$quote)";}elseif($name==='style'){return"htmlSpecialChars(Nette\\Templating\\DefaultHelpers::escapeCss($s)$quote)";}else{return"htmlSpecialChars($s$quote)";}case
Parser::CONTEXT_COMMENT:return"Nette\\Templating\\DefaultHelpers::escapeHtmlComment($s)";case
Parser::CONTEXT_CDATA;return 'Nette\Templating\DefaultHelpers::escape'.ucfirst($this->context[1])."($s)";case
Parser::CONTEXT_NONE:switch(isset($this->context[1])?$this->context[1]:NULL){case
'xml':case 'js':case 'css':return 'Nette\Templating\DefaultHelpers::escape'.ucfirst($this->context[1])."($s)";case
'text':return$s;default:return"\$template->escape($s)";}}}}}

 namespace Nette\Templating{use Nette;class FilterException extends Nette\InvalidStateException{public$sourceFile;public$sourceLine;public
function __construct($message,$code=0,$sourceLine=0){$this->sourceLine=(int)$sourceLine;parent::__construct($message,$code);}public
function setSourceFile($file){$this->sourceFile=(string)$file;$this->message=rtrim($this->message,'.')." in ".str_replace(dirname(dirname($file)),'...',$file).($this->sourceLine?":$this->sourceLine":'');}}}

 namespace Nette\Templating{use Nette;interface IFileTemplate extends ITemplate{function
setFile($file);function getFile();}}

 namespace Nette\Templating{use Nette,Nette\Caching;class Template extends Nette\Object
implements ITemplate{public$warnOnUndefined=TRUE;public$onPrepareFilters=array();private$source;private$params=array();private$filters=array();private$helpers=array();private$helperLoaders=array();private$cacheStorage;public
function setSource($source){$this->source=$source;return$this;}public function getSource(){return$this->source;}public
function render(){$cache=new Caching\Cache($storage=$this->getCacheStorage(),'Nette.Template');$cached=$compiled=$cache->load($this->source);if($compiled===NULL){$compiled=$this->compile();$cache->save($this->source,$compiled,array(Caching\Cache::CONSTS=>'Nette\Framework::REVISION'));$cache->release();$cached=$cache->load($this->source);}if($cached!==NULL&&$storage
instanceof Caching\Storages\PhpFileStorage){Nette\Utils\LimitedScope::load($cached['file'],$this->getParams());}else{Nette\Utils\LimitedScope::evaluate($compiled,$this->getParams());}}public
function save($file){if(file_put_contents($file,$this->__toString(TRUE))===FALSE){throw
new Nette\IOException("Unable to save file '$file'.");}}public function __toString(){$args=func_get_args();ob_start();try{$this->render();return
ob_get_clean();}catch(\Exception$e){ob_end_clean();if($args&&$args[0]){throw$e;}else{Nette\Diagnostics\Debugger::toStringException($e);}}}public
function compile(){if(!$this->filters){$this->onPrepareFilters($this);}$code=$this->getSource();foreach($this->filters
as$filter){$code=self::extractPhp($code,$blocks);$code=$filter($code);$code=strtr($code,$blocks);}return
self::optimizePhp($code);}public function registerFilter($callback){$callback=callback($callback);if(in_array($callback,$this->filters)){throw
new Nette\InvalidStateException("Filter '$callback' was registered twice.");}$this->filters[]=$callback;}final
public function getFilters(){return$this->filters;}public function registerHelper($name,$callback){$this->helpers[strtolower($name)]=callback($callback);}public
function registerHelperLoader($callback){$this->helperLoaders[]=callback($callback);}final
public function getHelpers(){return$this->helpers;}public function __call($name,$args){$lname=strtolower($name);if(!isset($this->helpers[$lname])){foreach($this->helperLoaders
as$loader){$helper=$loader($lname);if($helper){$this->registerHelper($lname,$helper);return$this->helpers[$lname]->invokeArgs($args);}}return
parent::__call($name,$args);}return$this->helpers[$lname]->invokeArgs($args);}public
function setTranslator(Nette\Localization\ITranslator$translator=NULL){$this->registerHelper('translate',$translator===NULL?NULL:array($translator,'translate'));return$this;}public
function add($name,$value){if(array_key_exists($name,$this->params)){throw new Nette\InvalidStateException("The variable '$name' already exists.");}$this->params[$name]=$value;}public
function setParams(array$params){$this->params=$params+$this->params;return$this;}public
function getParams(){$this->params['template']=$this;return$this->params;}public
function __set($name,$value){$this->params[$name]=$value;}public function&__get($name){if($this->warnOnUndefined&&!array_key_exists($name,$this->params)){trigger_error("The variable '$name' does not exist in template.",E_USER_NOTICE);}return$this->params[$name];}public
function __isset($name){return isset($this->params[$name]);}public function __unset($name){unset($this->params[$name]);}public
function setCacheStorage(Caching\IStorage$storage){$this->cacheStorage=$storage;}public
function getCacheStorage(){if($this->cacheStorage===NULL){return new Caching\Storages\DevNullStorage;}return$this->cacheStorage;}private
static function extractPhp($source,&$blocks){$res='';$blocks=array();$tokens=token_get_all($source);foreach($tokens
as$n=>$token){if(is_array($token)){if($token[0]===T_INLINE_HTML||$token[0]===T_CLOSE_TAG){$res.=$token[1];continue;}elseif($token[0]===T_OPEN_TAG&&$token[1]==='<?'&&isset($tokens[$n+1][1])&&$tokens[$n+1][1]==='xml'){$php=&$res;$token[1]='<<?php ?>?';}elseif($token[0]===T_OPEN_TAG||$token[0]===T_OPEN_TAG_WITH_ECHO){$res.=$id="<?php \x01@php:p".count($blocks)."@\x02";$php=&$blocks[$id];}$php.=$token[1];}else{$php.=$token;}}return$res;}public
static function optimizePhp($source,$lineLength=80,$existenceOfThisParameterSolvesDamnBugInPHP535=NULL){$res=$php='';$lastChar=';';$tokens=new
\ArrayIterator(token_get_all($source));foreach($tokens as$key=>$token){if(is_array($token)){if($token[0]===T_INLINE_HTML){$lastChar='';$res.=$token[1];}elseif($token[0]===T_CLOSE_TAG){$next=isset($tokens[$key+1])?$tokens[$key+1]:NULL;if(substr($res,-1)!=='<'&&preg_match('#^<\?php\s*$#',$php)){$php='';}elseif(is_array($next)&&$next[0]===T_OPEN_TAG){if(!strspn($lastChar,';{}:/')){$php.=$lastChar=';';}if(substr($next[1],-1)==="\n"){$php.="\n";}$tokens->next();}elseif($next){$res.=preg_replace('#;?(\s)*$#','$1',$php).$token[1];if(strlen($res)-strrpos($res,"\n")>$lineLength&&(!is_array($next)||strpos($next[1],"\n")===FALSE)){$res.="\n";}$php='';}else{if(!strspn($lastChar,'};')){$php.=';';}}}elseif($token[0]===T_ELSE||$token[0]===T_ELSEIF){if($tokens[$key+1]===':'&&$lastChar==='}'){$php.=';';}$lastChar='';$php.=$token[1];}else{if(!in_array($token[0],array(T_WHITESPACE,T_COMMENT,T_DOC_COMMENT,T_OPEN_TAG))){$lastChar='';}$php.=$token[1];}}else{$php.=$lastChar=$token;}}return$res.$php;}}}

 namespace Nette\Utils{use Nette,RecursiveIteratorIterator;class Finder extends Nette\Object
implements \IteratorAggregate{private$paths=array();private$groups;private$exclude=array();private$order=RecursiveIteratorIterator::SELF_FIRST;private$maxDepth=-1;private$cursor;public
static function find($mask){if(!is_array($mask)){$mask=func_get_args();}$finder=new
static;return$finder->select(array(),'isDir')->select($mask,'isFile');}public static
function findFiles($mask){if(!is_array($mask)){$mask=func_get_args();}$finder=new
static;return$finder->select($mask,'isFile');}public static function findDirectories($mask){if(!is_array($mask)){$mask=func_get_args();}$finder=new
static;return$finder->select($mask,'isDir');}private function select($masks,$type){$this->cursor=&$this->groups[];$pattern=self::buildPattern($masks);if($type||$pattern){$this->filter(function($file)use($type,$pattern){return(!$type||$file->$type())&&!$file->isDot()&&(!$pattern||preg_match($pattern,'/'.strtr($file->getSubPathName(),'\\','/')));});}return$this;}public
function in($path){if(!is_array($path)){$path=func_get_args();}$this->maxDepth=0;return$this->from($path);}public
function from($path){if($this->paths){throw new Nette\InvalidStateException('Directory to search has already been specified.');}if(!is_array($path)){$path=func_get_args();}$this->paths=$path;$this->cursor=&$this->exclude;return$this;}public
function childFirst(){$this->order=RecursiveIteratorIterator::CHILD_FIRST;return$this;}private
static function buildPattern($masks){$pattern=array();foreach($masks as$mask){$mask=rtrim(strtr($mask,'\\','/'),'/');$prefix='';if($mask===''){continue;}elseif($mask==='*'){return
NULL;}elseif($mask[0]==='/'){$mask=ltrim($mask,'/');$prefix='(?<=^/)';}$pattern[]=$prefix.strtr(preg_quote($mask,'#'),array('\*\*'=>'.*','\*'=>'[^/]*','\?'=>'[^/]','\[\!'=>'[^','\['=>'[','\]'=>']','\-'=>'-'));}return$pattern?'#/('.implode('|',$pattern).')$#i':NULL;}public
function getIterator(){if(!$this->paths){throw new Nette\InvalidStateException('Call in() or from() to specify directory to search.');}elseif(count($this->paths)===1){return$this->buildIterator($this->paths[0]);}else{$iterator=new
\AppendIterator();foreach($this->paths as$path){$iterator->append($this->buildIterator($path));}return$iterator;}}private
function buildIterator($path){if(PHP_VERSION_ID<50301){$iterator=new Nette\Utils\RecursiveDirectoryIteratorFixed($path);}else{$iterator=new
\RecursiveDirectoryIterator($path,\RecursiveDirectoryIterator::FOLLOW_SYMLINKS);}if($this->exclude){$filters=$this->exclude;$iterator=new
Nette\Iterators\RecursiveFilter($iterator,function($file)use($filters){if(!$file->isFile()){foreach($filters
as$filter){if(!call_user_func($filter,$file)){return FALSE;}}}return TRUE;});}if($this->maxDepth!==0){$iterator=new
RecursiveIteratorIterator($iterator,$this->order);$iterator->setMaxDepth($this->maxDepth);}if($this->groups){$groups=$this->groups;$iterator=new
Nette\Iterators\Filter($iterator,function($file)use($groups){foreach($groups as$filters){foreach($filters
as$filter){if(!call_user_func($filter,$file)){continue 2;}}return TRUE;}return FALSE;});}return$iterator;}public
function exclude($masks){if(!is_array($masks)){$masks=func_get_args();}$pattern=self::buildPattern($masks);if($pattern){$this->filter(function($file)use($pattern){return!preg_match($pattern,'/'.strtr($file->getSubPathName(),'\\','/'));});}return$this;}public
function filter($callback){$this->cursor[]=$callback;return$this;}public function
limitDepth($depth){$this->maxDepth=$depth;return$this;}public function size($operator,$size=NULL){if(func_num_args()===1){if(!preg_match('#^(?:([=<>!]=?|<>)\s*)?((?:\d*\.)?\d+)\s*(K|M|G|)B?$#i',$operator,$matches)){throw
new Nette\InvalidArgumentException('Invalid size predicate format.');}list(,$operator,$size,$unit)=$matches;static$units=array(''=>1,'k'=>1e3,'m'=>1e6,'g'=>1e9);$size*=$units[strtolower($unit)];$operator=$operator?$operator:'=';}return$this->filter(function($file)use($operator,$size){return
Finder::compare($file->getSize(),$operator,$size);});}public function date($operator,$date=NULL){if(func_num_args()===1){if(!preg_match('#^(?:([=<>!]=?|<>)\s*)?(.+)$#i',$operator,$matches)){throw
new Nette\InvalidArgumentException('Invalid date predicate format.');}list(,$operator,$date)=$matches;$operator=$operator?$operator:'=';}$date=Nette\DateTime::from($date)->format('U');return$this->filter(function($file)use($operator,$date){return
Finder::compare($file->getMTime(),$operator,$date);});}public static function compare($l,$operator,$r){switch($operator){case
'>':return$l>$r;case '>=':return$l>=$r;case '<':return$l<$r;case '<=':return$l<=$r;case
'=':case '==':return$l==$r;case '!':case '!=':case '<>':return$l!=$r;}throw new Nette\InvalidArgumentException("Unknown operator $operator.");}}if(PHP_VERSION_ID<50301){class
RecursiveDirectoryIteratorFixed extends \RecursiveDirectoryIterator{function hasChildren(){return
parent::hasChildren(TRUE);}}}}

 namespace Nette\Utils{use Nette;class Html extends Nette\Object implements \ArrayAccess,\Countable,\IteratorAggregate{private$name;private$isEmpty;public$attrs=array();protected$children=array();public
static$xhtml=TRUE;public static$emptyElements=array('img'=>1,'hr'=>1,'br'=>1,'input'=>1,'meta'=>1,'area'=>1,'embed'=>1,'keygen'=>1,'source'=>1,'base'=>1,'col'=>1,'link'=>1,'param'=>1,'basefont'=>1,'frame'=>1,'isindex'=>1,'wbr'=>1,'command'=>1);public
static function el($name=NULL,$attrs=NULL){$el=new static;$parts=explode(' ',$name,2);$el->setName($parts[0]);if(is_array($attrs)){$el->attrs=$attrs;}elseif($attrs!==NULL){$el->setText($attrs);}if(isset($parts[1])){foreach(Strings::matchAll($parts[1].' ','#([a-z0-9:-]+)(?:=(["\'])?(.*?)(?(2)\\2|\s))?#i')as$m){$el->attrs[$m[1]]=isset($m[3])?$m[3]:TRUE;}}return$el;}final
public function setName($name,$isEmpty=NULL){if($name!==NULL&&!is_string($name)){throw
new Nette\InvalidArgumentException("Name must be string or NULL, ".gettype($name)." given.");}$this->name=$name;$this->isEmpty=$isEmpty===NULL?isset(self::$emptyElements[$name]):(bool)$isEmpty;return$this;}final
public function getName(){return$this->name;}final public function isEmpty(){return$this->isEmpty;}public
function addAttributes(array$attrs){$this->attrs=$attrs+$this->attrs;return$this;}final
public function __set($name,$value){$this->attrs[$name]=$value;}final public function&__get($name){return$this->attrs[$name];}final
public function __unset($name){unset($this->attrs[$name]);}final public function
__call($m,$args){$p=substr($m,0,3);if($p==='get'||$p==='set'||$p==='add'){$m=substr($m,3);$m[0]=$m[0]|"\x20";if($p==='get'){return
isset($this->attrs[$m])?$this->attrs[$m]:NULL;}elseif($p==='add'){$args[]=TRUE;}}if(count($args)===0){}elseif(count($args)===1){$this->attrs[$m]=$args[0];}elseif((string)$args[0]===''){$tmp=&$this->attrs[$m];}elseif(!isset($this->attrs[$m])||is_array($this->attrs[$m])){$this->attrs[$m][$args[0]]=$args[1];}else{$this->attrs[$m]=array($this->attrs[$m],$args[0]=>$args[1]);}return$this;}final
public function href($path,$query=NULL){if($query){$query=http_build_query($query,NULL,'&');if($query!==''){$path.='?'.$query;}}$this->attrs['href']=$path;return$this;}final
public function setHtml($html){if($html===NULL){$html='';}elseif(is_array($html)){throw
new Nette\InvalidArgumentException("Textual content must be a scalar, ".gettype($html)." given.");}else{$html=(string)$html;}$this->removeChildren();$this->children[]=$html;return$this;}final
public function getHtml(){$s='';foreach($this->children as$child){if(is_object($child)){$s.=$child->render();}else{$s.=$child;}}return$s;}final
public function setText($text){if(!is_array($text)){$text=htmlspecialchars((string)$text,ENT_NOQUOTES);}return$this->setHtml($text);}final
public function getText(){return html_entity_decode(strip_tags($this->getHtml()),ENT_QUOTES,'UTF-8');}final
public function add($child){return$this->insert(NULL,$child);}final public function
create($name,$attrs=NULL){$this->insert(NULL,$child=static::el($name,$attrs));return$child;}public
function insert($index,$child,$replace=FALSE){if($child instanceof Html||is_scalar($child)){if($index===NULL){$this->children[]=$child;}else{array_splice($this->children,(int)$index,$replace?1:0,array($child));}}else{throw
new Nette\InvalidArgumentException("Child node must be scalar or Html object, ".(is_object($child)?get_class($child):gettype($child))." given.");}return$this;}final
public function offsetSet($index,$child){$this->insert($index,$child,TRUE);}final
public function offsetGet($index){return$this->children[$index];}final public function
offsetExists($index){return isset($this->children[$index]);}public function offsetUnset($index){if(isset($this->children[$index])){array_splice($this->children,(int)$index,1);}}final
public function count(){return count($this->children);}public function removeChildren(){$this->children=array();}final
public function getIterator($deep=FALSE){if($deep){$deep=$deep>0?\RecursiveIteratorIterator::SELF_FIRST:\RecursiveIteratorIterator::CHILD_FIRST;return
new \RecursiveIteratorIterator(new Nette\Iterators\Recursor(new \ArrayIterator($this->children)),$deep);}else{return
new Nette\Iterators\Recursor(new \ArrayIterator($this->children));}}final public
function getChildren(){return$this->children;}final public function render($indent=NULL){$s=$this->startTag();if(!$this->isEmpty){if($indent!==NULL){$indent++;}foreach($this->children
as$child){if(is_object($child)){$s.=$child->render($indent);}else{$s.=$child;}}$s.=$this->endTag();}if($indent!==NULL){return
"\n".str_repeat("\t",$indent-1).$s."\n".str_repeat("\t",max(0,$indent-2));}return$s;}final
public function __toString(){return$this->render();}final public function startTag(){if($this->name){return
'<'.$this->name.$this->attributes().(self::$xhtml&&$this->isEmpty?' />':'>');}else{return
'';}}final public function endTag(){return$this->name&&!$this->isEmpty?'</'.$this->name.'>':'';}final
public function attributes(){if(!is_array($this->attrs)){return '';}$s='';foreach($this->attrs
as$key=>$value){if($value===NULL||$value===FALSE){continue;}elseif($value===TRUE){if(self::$xhtml){$s.=' '.$key.'="'.$key.'"';}else{$s.=' '.$key;}continue;}elseif(is_array($value)){if($key==='data'){foreach($value
as$k=>$v){if($v!==NULL&&$v!==FALSE){$s.=' data-'.$k.'="'.htmlspecialchars((string)$v).'"';}}continue;}$tmp=NULL;foreach($value
as$k=>$v){if($v!=NULL){$tmp[]=$v===TRUE?$k:(is_string($k)?$k.':'.$v:$v);}}if($tmp===NULL){continue;}$value=implode($key==='style'||!strncmp($key,'on',2)?';':' ',$tmp);}else{$value=(string)$value;}$s.=' '.$key.'="'.htmlspecialchars($value).'"';}$s=str_replace('@','&#64;',$s);return$s;}public
function __clone(){foreach($this->children as$key=>$value){if(is_object($value)){$this->children[$key]=clone$value;}}}}}

 namespace Nette\Utils{use Nette;class Neon extends Nette\Object{const BLOCK=1;private
static$patterns=array('\'[^\'\n]*\'|"(?:\\\\.|[^"\\\\\n])*"','@[a-zA-Z_0-9\\\\]+','[:-](?=\s|$)|[,=[\]{}()]','?:#.*','\n[\t ]*','[^#"\',:=@[\]{}()<>\x00-\x20!`](?:[^#,:=\]})>\x00-\x1F]+|:(?!\s|$)|(?<!\s)#)*(?<!\s)','?:[\t ]+',);private
static$tokenizer;private static$brackets=array('['=>']','{'=>'}','('=>')',);private$n=0;private$indentTabs;public
static function encode($var,$options=NULL){if($var instanceof \DateTime){return$var->format('Y-m-d H:i:s O');}if(is_object($var)){$obj=$var;$var=array();foreach($obj
as$k=>$v){$var[$k]=$v;}}if(is_array($var)){$isArray=array_keys($var)===range(0,count($var)-1);$s='';if($options&self::BLOCK){foreach($var
as$k=>$v){$v=self::encode($v,self::BLOCK);$s.=($isArray?'-':self::encode($k).':').(strpos($v,"\n")===FALSE?' '.$v:"\n\t".str_replace("\n","\n\t",$v))."\n";continue;}return$s;}else{foreach($var
as$k=>$v){$s.=($isArray?'':self::encode($k).': ').self::encode($v).', ';}return($isArray?'[':'{').substr($s,0,-2).($isArray?']':'}');}}elseif(is_string($var)&&!is_numeric($var)&&!preg_match('~[\x00-\x1F]|^\d{4}|^(true|false|yes|no|on|off|null)$~i',$var)&&preg_match('~^'.self::$patterns[5].'$~',$var)){return$var;}else{return
json_encode($var);}}public static function decode($input){if(!is_string($input)){throw
new Nette\InvalidArgumentException("Argument must be a string, ".gettype($input)." given.");}if(!self::$tokenizer){self::$tokenizer=new
Tokenizer(self::$patterns,'mi');}$input=str_replace("\r",'',$input);self::$tokenizer->tokenize($input);$parser=new
static;$res=$parser->parse(0);while(isset(self::$tokenizer->tokens[$parser->n])){if(self::$tokenizer->tokens[$parser->n][0]==="\n"){$parser->n++;}else{$parser->error();}}return$res;}private
function parse($indent=NULL,$result=NULL){$inlineParser=$indent===NULL;$value=$key=$object=NULL;$hasValue=$hasKey=FALSE;$tokens=self::$tokenizer->tokens;$n=&$this->n;$count=count($tokens);for(;$n<$count;$n++){$t=$tokens[$n];if($t===','){if(!$hasValue||!$inlineParser){$this->error();}if($hasKey){$result[$key]=$value;}else{$result[]=$value;}$hasKey=$hasValue=FALSE;}elseif($t===':'||$t==='='){if($hasKey||!$hasValue){$this->error();}if(is_array($value)||(is_object($value)&&!method_exists($value,'__toString'))){$this->error('Unacceptable key');}else{$key=(string)$value;}$hasKey=TRUE;$hasValue=FALSE;}elseif($t==='-'){if($hasKey||$hasValue||$inlineParser){$this->error();}$key=NULL;$hasKey=TRUE;}elseif(isset(self::$brackets[$t])){if($hasValue){$this->error();}$endBracket=self::$brackets[$tokens[$n++]];$hasValue=TRUE;$value=$this->parse(NULL,array());if(!isset($tokens[$n])||$tokens[$n]!==$endBracket){$this->error();}}elseif($t===']'||$t==='}'||$t===')'){if(!$inlineParser){$this->error();}break;}elseif($t[0]==='@'){$object=$t;}elseif($t[0]==="\n"){if($inlineParser){if($hasValue){if($hasKey){$result[$key]=$value;}else{$result[]=$value;}$hasKey=$hasValue=FALSE;}}else{while(isset($tokens[$n+1])&&$tokens[$n+1][0]==="\n")$n++;if(!isset($tokens[$n+1])){break;}$newIndent=strlen($tokens[$n])-1;if($indent===NULL){$indent=$newIndent;}if($newIndent){if($this->indentTabs===NULL){$this->indentTabs=$tokens[$n][1]==="\t";}if(strpos($tokens[$n],$this->indentTabs?' ':"\t")){$this->error('Either tabs or spaces may be used as indenting chars, but not both.');}}if($newIndent>$indent){if($hasValue||!$hasKey){$n++;$this->error('Unexpected indentation.');}elseif($key===NULL){$result[]=$this->parse($newIndent);}else{$result[$key]=$this->parse($newIndent);}$newIndent=isset($tokens[$n])?strlen($tokens[$n])-1:0;$hasKey=FALSE;}else{if($hasValue&&!$hasKey){break;}elseif($hasKey){$value=$hasValue?$value:NULL;if($key===NULL){$result[]=$value;}else{$result[$key]=$value;}$hasKey=$hasValue=FALSE;}}if($newIndent<$indent){return$result;}}}else{if($hasValue){$this->error();}static$consts=array('true'=>TRUE,'True'=>TRUE,'TRUE'=>TRUE,'yes'=>TRUE,'Yes'=>TRUE,'YES'=>TRUE,'on'=>TRUE,'On'=>TRUE,'ON'=>TRUE,'false'=>FALSE,'False'=>FALSE,'FALSE'=>FALSE,'no'=>FALSE,'No'=>FALSE,'NO'=>FALSE,'off'=>FALSE,'Off'=>FALSE,'OFF'=>FALSE,);if($t[0]==='"'){$value=preg_replace_callback('#\\\\(?:u[0-9a-f]{4}|x[0-9a-f]{2}|.)#i',array($this,'cbString'),substr($t,1,-1));}elseif($t[0]==="'"){$value=substr($t,1,-1);}elseif(isset($consts[$t])){$value=$consts[$t];}elseif($t==='null'||$t==='Null'||$t==='NULL'){$value=NULL;}elseif(is_numeric($t)){$value=$t*1;}elseif(preg_match('#\d\d\d\d-\d\d?-\d\d?(?:(?:[Tt]| +)\d\d?:\d\d:\d\d(?:\.\d*)? *(?:Z|[-+]\d\d?(?::\d\d)?)?)?$#A',$t)){$value=new
Nette\DateTime($t);}else{$value=$t;}$hasValue=TRUE;}}if($inlineParser){if($hasValue){if($hasKey){$result[$key]=$value;}else{$result[]=$value;}}elseif($hasKey){$this->error();}}else{if($hasValue&&!$hasKey){if($result===NULL){$result=$value;}else{$this->error();}}elseif($hasKey){$value=$hasValue?$value:NULL;if($key===NULL){$result[]=$value;}else{$result[$key]=$value;}}}return$result;}private
function cbString($m){static$mapping=array('t'=>"\t",'n'=>"\n",'"'=>'"','\\'=>'\\','/'=>'/','_'=>"\xc2\xa0");$sq=$m[0];if(isset($mapping[$sq[1]])){return$mapping[$sq[1]];}elseif($sq[1]==='u'&&strlen($sq)===6){return
Strings::chr(hexdec(substr($sq,2)));}elseif($sq[1]==='x'&&strlen($sq)===4){return
chr(hexdec(substr($sq,2)));}else{$this->error("Invalid escaping sequence $sq");}}private
function error($message="Unexpected '%s'"){list(,$line,$col)=self::$tokenizer->getOffset($this->n);$token=isset(self::$tokenizer->tokens[$this->n])?str_replace("\n",'<new line>',Strings::truncate(self::$tokenizer->tokens[$this->n],40)):'end';throw
new NeonException(str_replace('%s',$token,$message)." on line $line, column $col.");}}class
NeonException extends \Exception{}}

 namespace Nette\Utils{use Nette,Nette\Utils\Strings;class Tokenizer extends Nette\Object{public$tokens;public$position=0;public$ignored=array();private$input;private$re;private$types;public$current;public
function __construct(array$patterns,$flags=''){$this->re='~('.implode(')|(',$patterns).')~A'.$flags;$keys=array_keys($patterns);$this->types=$keys===range(0,count($patterns)-1)?FALSE:$keys;}public
function tokenize($input){$this->input=$input;if($this->types){$this->tokens=Strings::matchAll($input,$this->re);$len=0;$count=count($this->types);$line=1;foreach($this->tokens
as&$match){$type=NULL;for($i=1;$i<=$count;$i++){if(!isset($match[$i])){break;}elseif($match[$i]!=NULL){$type=$this->types[$i-1];break;}}$match=self::createToken($match[0],$type,$line);$len+=strlen($match['value']);$line+=substr_count($match['value'],"\n");}if($len!==strlen($input)){$errorOffset=$len;}}else{$this->tokens=Strings::split($input,$this->re,PREG_SPLIT_NO_EMPTY);if($this->tokens&&!Strings::match(end($this->tokens),$this->re)){$tmp=Strings::split($this->input,$this->re,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);list(,$errorOffset)=end($tmp);}}if(isset($errorOffset)){$line=$errorOffset?substr_count($this->input,"\n",0,$errorOffset)+1:1;$col=$errorOffset-strrpos(substr($this->input,0,$errorOffset),"\n")+1;$token=str_replace("\n",'\n',substr($input,$errorOffset,10));throw
new TokenizerException("Unexpected '$token' on line $line, column $col.");}return$this->tokens;}public
static function createToken($value,$type=NULL,$line=NULL){return array('value'=>$value,'type'=>$type,'line'=>$line);}public
function getOffset($i){$tokens=Strings::split($this->input,$this->re,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);$offset=isset($tokens[$i])?$tokens[$i][1]:strlen($this->input);return
array($offset,($offset?substr_count($this->input,"\n",0,$offset)+1:1),$offset-strrpos(substr($this->input,0,$offset),"\n"),);}public
function fetch(){$args=func_get_args();return$this->scan($args,TRUE);}public function
fetchToken(){$args=func_get_args();return$this->scan($args,TRUE)===FALSE?FALSE:$this->current;}public
function fetchAll(){$args=func_get_args();return$this->scan($args,FALSE);}public
function fetchUntil($arg){$args=func_get_args();return$this->scan($args,FALSE,TRUE,TRUE);}public
function isNext($arg){$args=func_get_args();return (bool)$this->scan($args,TRUE,FALSE);}public
function isPrev($arg){$args=func_get_args();return (bool)$this->scan($args,TRUE,FALSE,FALSE,TRUE);}public
function hasNext(){return isset($this->tokens[$this->position]);}public function
hasPrev(){return$this->position>1;}public function isCurrent($arg){$args=func_get_args();if(is_array($this->current)){return
in_array($this->current['value'],$args,TRUE)||in_array($this->current['type'],$args,TRUE);}else{return
in_array($this->current,$args,TRUE);}}private function scan($wanted,$first,$advance=TRUE,$neg=FALSE,$prev=FALSE){$res=FALSE;$pos=$this->position+($prev?-2:0);while(isset($this->tokens[$pos])){$token=$this->tokens[$pos];$pos+=$prev?-1:1;$value=is_array($token)?$token['value']:$token;$type=is_array($token)?$token['type']:$token;if(!$wanted||(in_array($value,$wanted,TRUE)||in_array($type,$wanted,TRUE))^$neg){if($advance){$this->position=$pos;$this->current=$token;}$res.=$value;if($first){break;}}elseif($neg||!in_array($type,$this->ignored,TRUE)){break;}}return$res;}}class
TokenizerException extends \Exception{}}

 namespace Nette\Caching\Storages{use Nette;class PhpFileStorage extends FileStorage{public$hint;protected
function readData($meta){return array('file'=>$meta[self::FILE],'handle'=>$meta[self::HANDLE],);}protected
function getCacheFile($key){return parent::getCacheFile(substr_replace($key,trim(strtr($this->hint,'\\/@','.._'),'.').'-',strpos($key,Nette\Caching\Cache::NAMESPACE_SEPARATOR)+1,0)).'.php';}}}

 namespace Nette\DI{use Nette;class Container extends Nette\FreezableObject implements
IContainer{const TAG_TYPEHINT='typeHint';public$params=array();private$registry=array();private$factories=array();private$tags=array();private$creating;public
function addService($name,$service,$tags=NULL){$this->updating();if(!is_string($name)||$name===''){throw
new Nette\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}if(isset($this->registry[$name])||method_exists($this,"createService$name")){throw
new Nette\InvalidStateException("Service '$name' has already been registered.");}if(is_string($tags)){$tags=array(self::TAG_TYPEHINT=>array($tags));}elseif(is_array($tags)){foreach($tags
as$id=>$attrs){if(is_int($id)&&is_string($attrs)){$tags[$attrs]=array();unset($tags[$id]);}elseif(!is_array($attrs)){$tags[$id]=(array)$attrs;}}}if(is_string($service)&&strpos($service,':')===FALSE){if(!isset($tags[self::TAG_TYPEHINT][0])){$tags[self::TAG_TYPEHINT][0]=$service;}$service=new
ServiceBuilder($service);}if($service instanceof IServiceBuilder){$factory=array($service,'createService');}elseif(is_object($service)&&!$service
instanceof \Closure&&!$service instanceof Nette\Callback){$this->registry[$name]=$service;$this->tags[$name]=$tags;return$this;}else{$factory=$service;}$this->factories[$name]=array(callback($factory));$this->tags[$name]=$tags;$this->registry[$name]=&$this->factories[$name][1];return$service;}public
function removeService($name){$this->updating();unset($this->registry[$name],$this->factories[$name]);}public
function getService($name){if(isset($this->registry[$name])){return$this->registry[$name];}elseif(isset($this->creating[$name])){throw
new Nette\InvalidStateException("Circular reference detected for services: ".implode(', ',array_keys($this->creating)).".");}if(isset($this->factories[$name])){list($factory)=$this->factories[$name];if(!$factory->isCallable()){throw
new Nette\InvalidStateException("Unable to create service '$name', factory '$factory' is not callable.");}$this->creating[$name]=TRUE;try{$service=$factory($this);}catch(\Exception$e){}}elseif(method_exists($this,$factory='createService'.ucfirst($name))){$this->creating[$name]=TRUE;try{$service=$this->$factory();}catch(\Exception$e){}}else{throw
new MissingServiceException("Service '$name' not found.");}unset($this->creating[$name]);if(isset($e)){throw$e;}elseif(!is_object($service)){throw
new Nette\UnexpectedValueException("Unable to create service '$name', value returned by factory '$factory' is not object.");}elseif(isset($this->tags[$name][self::TAG_TYPEHINT][0])&&!$service
instanceof$this->tags[$name][self::TAG_TYPEHINT][0]){throw new Nette\UnexpectedValueException("Unable to create service '$name', value returned by factory '$factory' is not '{$this->tags[$name][self::TAG_TYPEHINT][0]}' type.");}unset($this->factories[$name]);return$this->registry[$name]=$service;}public
function getServiceByType($type){foreach($this->registry as$name=>$service){if(isset($this->tags[$name][self::TAG_TYPEHINT][0])?!strcasecmp($this->tags[$name][self::TAG_TYPEHINT][0],$type):$service
instanceof$type){$found[]=$name;}}if(!isset($found)){throw new MissingServiceException("Service matching '$type' type not found.");}elseif(count($found)>1){throw
new AmbiguousServiceException("Found more than one service ('".implode("', '",$found)."') matching '$type' type.");}return$this->getService($found[0]);}public
function getServiceNamesByTag($tag){$found=array();foreach($this->registry as$name=>$service){if(isset($this->tags[$name][$tag])){$found[$name]=$this->tags[$name][$tag];}}return$found;}public
function hasService($name){return isset($this->registry[$name])||isset($this->factories[$name])||method_exists($this,"createService$name");}public
function checkServiceType($name,$type){return isset($this->tags[$name][self::TAG_TYPEHINT][0])?!strcasecmp($this->tags[$name][self::TAG_TYPEHINT][0],$type):(isset($this->registry[$name])&&$this->registry[$name]instanceof$type);}public
function expand($s){if(is_string($s)&&strpos($s,'%')!==FALSE){$that=$this;return@preg_replace_callback('#%([a-z0-9._-]*)%#i',function($m)use($that){list(,$param)=$m;if($param===''){return
'%';}elseif(!is_scalar($val=Nette\Utils\Arrays::get((array)$that->params,explode('.',$param)))){throw
new Nette\InvalidStateException("Parameter '$param' is not scalar.");}return$val;},$s);}return$s;}public
function&__get($name){if(!isset($this->registry[$name])){$this->getService($name);}return$this->registry[$name];}public
function __set($name,$service){$this->updating();if(!is_string($name)||$name===''){throw
new Nette\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}elseif(isset($this->registry[$name])||method_exists($this,"createService$name")){throw
new Nette\InvalidStateException("Service '$name' has already been registered.");}elseif(!is_object($service)){throw
new Nette\InvalidArgumentException("Service must be a object, ".gettype($service)." given.");}$this->registry[$name]=$service;}public
function __isset($name){return$this->hasService($name);}public function __unset($name){$this->removeService($name);}}}

 namespace Nette\Latte\Macros{use Nette,Nette\Latte,Nette\Latte\ParseException,Nette\Latte\MacroNode;class
CoreMacros extends MacroSet{public static function install(Latte\Parser$parser){$me=new
static($parser);$me->addMacro('if',array($me,'macroIf'),array($me,'macroEndIf'));$me->addMacro('elseif','elseif (%node.args):');$me->addMacro('else','else:');$me->addMacro('ifset','if (isset(%node.args)):','endif');$me->addMacro('elseifset','elseif (isset(%node.args)):');$me->addMacro('foreach',array($me,'macroForeach'),'$iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its)');$me->addMacro('for','for (%node.args):','endfor');$me->addMacro('while','while (%node.args):','endwhile');$me->addMacro('continueIf','if (%node.args) continue');$me->addMacro('breakIf','if (%node.args) break');$me->addMacro('first','if ($iterator->isFirst(%node.args)):','endif');$me->addMacro('last','if ($iterator->isLast(%node.args)):','endif');$me->addMacro('sep','if (!$iterator->isLast(%node.args)):','endif');$me->addMacro('var',array($me,'macroVar'));$me->addMacro('assign',array($me,'macroVar'));$me->addMacro('default',array($me,'macroVar'));$me->addMacro('dump',array($me,'macroDump'));$me->addMacro('debugbreak',array($me,'macroDebugbreak'));$me->addMacro('l','?>{<?php');$me->addMacro('r','?>}<?php');$me->addMacro('_',array($me,'macroTranslate'),array($me,'macroTranslate'));$me->addMacro('=',array($me,'macroExpr'));$me->addMacro('?',array($me,'macroExpr'));$me->addMacro('syntax',array($me,'macroSyntax'),array($me,'macroSyntax'));$me->addMacro('capture',array($me,'macroCapture'),array($me,'macroCaptureEnd'));$me->addMacro('include',array($me,'macroInclude'));$me->addMacro('use',array($me,'macroUse'));$me->addMacro('@href',NULL,NULL);$me->addMacro('@class',array($me,'macroClass'));$me->addMacro('@attr',array($me,'macroAttr'));$me->addMacro('attr',array($me,'macroOldAttr'));}public
function finalize(){return array('list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, '.var_export($this->parser->templateId,TRUE).')');}public
function macroIf(MacroNode$node,$writer){if($node->data->capture=($node->args==='')){return
'ob_start()';}return$writer->write('if (%node.args):');}public function macroEndIf(MacroNode$node,$writer){if($node->data->capture){if($node->args===''){throw
new ParseException('Missing condition in {if} macro.');}return$writer->write('if (%node.args) ob_end_flush(); else ob_end_clean()');}return
'endif';}public function macroTranslate(MacroNode$node,$writer){if($node->closing){return$writer->write('echo %modify($template->translate(ob_get_clean()))');}elseif($node->isEmpty=($node->args!=='')){return$writer->write('echo %modify($template->translate(%node.args))');}else{return
'ob_start()';}}public function macroSyntax(MacroNode$node){if($node->closing){$node->args='latte';}switch($node->args){case
'':case 'latte':$this->parser->setDelimiters('\\{(?![\\s\'"{}])','\\}');break;case
'double':$this->parser->setDelimiters('\\{\\{(?![\\s\'"{}])','\\}\\}');break;case
'asp':$this->parser->setDelimiters('<%\s*','\s*%>');break;case 'python':$this->parser->setDelimiters('\\{[{%]\s*','\s*[%}]\\}');break;case
'off':$this->parser->setDelimiters('[^\x00-\xFF]','');break;default:throw new ParseException("Unknown syntax '$node->args'");}}public
function macroInclude(MacroNode$node,$writer){$code=$writer->write('Nette\Latte\Macros\CoreMacros::includeTemplate(%node.word, %node.array? + $template->getParams(), $_l->templates[%var])',$this->parser->templateId);if($node->modifiers){return$writer->write('echo %modify(%raw->__toString(TRUE))',$code);}else{return$code.'->render()';}}public
function macroUse(MacroNode$node,$writer){call_user_func(array($node->tokenizer->fetchWord(),'install'),$this->parser)->initialize();}public
function macroCapture(MacroNode$node,$writer){$variable=$node->tokenizer->fetchWord();if(substr($variable,0,1)!=='$'){throw
new ParseException("Invalid capture block variable '$variable'");}$node->data->variable=$variable;return
'ob_start()';}public function macroCaptureEnd(MacroNode$node,$writer){return$writer->write("{$node->data->variable} = %modify(ob_get_clean())");}public
function macroForeach(MacroNode$node,$writer){return '$iterations = 0; foreach ($iterator = $_l->its[] = new Nette\Iterators\CachingIterator('.preg_replace('#(.*)\s+as\s+#i','$1) as ',$writer->formatArgs(),1).'):';}public
function macroClass(MacroNode$node,$writer){return$writer->write('if ($_l->tmp = trim(implode(" ", array_unique(%node.array)))) echo \' class="\' . %escape($_l->tmp) . \'"\'');}public
function macroAttr(MacroNode$node,$writer){return$writer->write('echo Nette\Utils\Html::el(NULL, %node.array)->attributes()');}public
function macroOldAttr(MacroNode$node){return Nette\Utils\Strings::replace($node->args.' ','#\)\s+#',')->');}public
function macroDump(MacroNode$node,$writer){$args=$writer->formatArgs();return$writer->write('Nette\Diagnostics\Debugger::barDump('.($node->args?"array(%var => $args)":'get_defined_vars()').', "Template " . str_replace(dirname(dirname($template->getFile())), "\xE2\x80\xA6", $template->getFile()))',$args);}public
function macroDebugbreak(MacroNode$node,$writer){return$writer->write(($node->args==NULL?'':'if (!(%node.args)); else').'if (function_exists("debugbreak")) debugbreak(); elseif (function_exists("xdebug_break")) xdebug_break()');}public
function macroVar(MacroNode$node,$writer){$out='';$var=TRUE;$tokenizer=$writer->preprocess();while($token=$tokenizer->fetchToken()){if($var&&($token['type']===Latte\MacroTokenizer::T_SYMBOL||$token['type']===Latte\MacroTokenizer::T_VARIABLE)){if($node->name==='default'){$out.="'".ltrim($token['value'],"$")."'";}else{$out.='$'.ltrim($token['value'],"$");}$var=NULL;}elseif(($token['value']==='='||$token['value']==='=>')&&$token['depth']===0){$out.=$node->name==='default'?'=>':'=';$var=FALSE;}elseif($token['value']===','&&$token['depth']===0){$out.=$node->name==='default'?',':';';$var=TRUE;}elseif($var===NULL&&$node->name==='default'&&$token['type']!==Latte\MacroTokenizer::T_WHITESPACE){throw
new ParseException("Unexpected '$token[value]' in {default $node->args}");}else{$out.=$writer->canQuote($tokenizer)?"'$token[value]'":$token['value'];}}return$node->name==='default'?"extract(array($out), EXTR_SKIP)":$out;}public
function macroExpr(MacroNode$node,$writer){return$writer->write(($node->name==='?'?'':'echo ').'%modify(%node.args)');}public
static function includeTemplate($destination,$params,$template){if($destination instanceof
Nette\Templating\ITemplate){$tpl=$destination;}elseif($destination==NULL){throw new
Nette\InvalidArgumentException("Template file name was not specified.");}else{$tpl=clone$template;if($template
instanceof Nette\Templating\IFileTemplate){if(substr($destination,0,1)!=='/'&&substr($destination,1,1)!==':'){$destination=dirname($template->getFile()).'/'.$destination;}$tpl->setFile($destination);}}$tpl->setParams($params);return$tpl;}public
static function initRuntime($template,$templateId){if(isset($template->_l)){$local=$template->_l;unset($template->_l);}else{$local=(object)
NULL;}$local->templates[$templateId]=$template;if(!isset($template->_g)){$template->_g=(object)
NULL;}return array($local,$template->_g);}}}

 namespace Nette\Latte\Macros{use Nette,Nette\Latte,Nette\Latte\MacroNode,Nette\Latte\ParseException,Nette\Utils\Strings;class
FormMacros extends MacroSet{public static function install(Latte\Parser$parser){$me=new
static($parser);$me->addMacro('form','$form = $control[%node.word]; echo $form->getElementPrototype()->addAttributes(%node.array)->startTag()','?>
<div><?php
foreach ($form->getComponents(TRUE, \'Nette\Forms\Controls\HiddenField\') as $_tmp)
	echo $_tmp->getControl();
?></div>
<?php echo $form->getElementPrototype()->endTag()');$me->addMacro('label',array($me,'macroLabel'),'?></label><?php');$me->addMacro('input','echo $form[%node.word]->getControl()->addAttributes(%node.array)');}public
function macroLabel(MacroNode$node,$writer){$cmd='if ($_label = $form[%node.word]->getLabel()) echo $_label->addAttributes(%node.array)';if($node->isEmpty=(substr($node->args,-1)==='/')){$node->setArgs(substr($node->args,0,-1));return$writer->write($cmd);}else{return$writer->write($cmd.'->startTag()');}}}}

 namespace Nette\Latte\Macros{use Nette,Nette\Latte,Nette\Latte\MacroNode,Nette\Latte\ParseException,Nette\Utils\Strings;class
UIMacros extends MacroSet{const RE_IDENTIFIER='[_a-zA-Z\x7F-\xFF][_a-zA-Z0-9\x7F-\xFF]*';private$namedBlocks=array();private$extends;public
static function install(Latte\Parser$parser){$me=new static($parser);$me->addMacro('include',array($me,'macroInclude'));$me->addMacro('includeblock',array($me,'macroIncludeBlock'));$me->addMacro('extends',array($me,'macroExtends'));$me->addMacro('layout',array($me,'macroExtends'));$me->addMacro('block',array($me,'macroBlock'),array($me,'macroBlockEnd'));$me->addMacro('define',array($me,'macroBlock'),array($me,'macroBlockEnd'));$me->addMacro('snippet',array($me,'macroBlock'),array($me,'macroBlockEnd'));$me->addMacro('ifset',array($me,'macroIfset'),'endif');$me->addMacro('widget',array($me,'macroControl'));$me->addMacro('control',array($me,'macroControl'));$me->addMacro('@href',function(MacroNode$node,$writer)use($me){return
' ?> href="<?php '.$me->macroLink($node,$writer).' ?>"<?php ';});$me->addMacro('plink',array($me,'macroLink'));$me->addMacro('link',array($me,'macroLink'));$me->addMacro('ifCurrent',array($me,'macroIfCurrent'),'endif');$me->addMacro('contentType',array($me,'macroContentType'));$me->addMacro('status',array($me,'macroStatus'));}public
function initialize(){$this->namedBlocks=array();$this->extends=NULL;}public function
finalize(){try{$this->parser->writeMacro('/block');}catch(ParseException$e){}$epilog=$prolog=array();if($this->namedBlocks){foreach($this->namedBlocks
as$name=>$code){$func='_lb'.substr(md5($this->parser->templateId.$name),0,10).'_'.preg_replace('#[^a-z0-9_]#i','_',$name);$snippet=$name[0]==='_';$prolog[]="//\n// block $name\n//\n"."if (!function_exists(\$_l->blocks[".var_export($name,TRUE)."][] = '$func')) { "."function $func(\$_l, \$_args) { ".(PHP_VERSION_ID>50208?'extract($_args)':'foreach ($_args as $__k => $__v) $$__k = $__v').($snippet?'; $control->validateControl('.var_export(substr($name,1),TRUE).')':'')."\n?>$code<?php\n}}";}$prolog[]="//\n// end of blocks\n//";}if($this->namedBlocks||$this->extends){$prolog[]="// template extending and snippets support";if(is_bool($this->extends)){$prolog[]='$_l->extends = '.var_export($this->extends,TRUE).'; unset($_extends, $template->_extends);';}else{$prolog[]='$_l->extends = empty($template->_extends) ? FALSE : $template->_extends; unset($_extends, $template->_extends);';}$prolog[]='
if ($_l->extends) {
	ob_start();
} elseif (!empty($control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($control, $_l, get_defined_vars());
}';$epilog[]='
// template extending support
if ($_l->extends) {
	ob_end_clean();
	Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}';}else{$prolog[]='
// snippets support
if (!empty($control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($control, $_l, get_defined_vars());
}';}return
array(implode("\n\n",$prolog),implode("\n",$epilog));}public function macroInclude(MacroNode$node,$writer){$destination=$node->tokenizer->fetchWord();if(substr($destination,0,1)!=='#'){return
FALSE;}$destination=ltrim($destination,'#');if(!Strings::match($destination,'#^\$?'.self::RE_IDENTIFIER.'$#')){throw
new ParseException("Included block name must be alphanumeric string, '$destination' given.");}$parent=$destination==='parent';if($destination==='parent'||$destination==='this'){$item=$node->parentNode;while($item&&$item->name!=='block'&&!isset($item->data->name))$item=$item->parentNode;if(!$item){throw
new ParseException("Cannot include $destination block outside of any block.");}$destination=$item->data->name;}$name=$destination[0]==='$'?$destination:var_export($destination,TRUE);if(isset($this->namedBlocks[$destination])&&!$parent){$cmd="call_user_func(reset(\$_l->blocks[$name]), \$_l, %node.array? + \$template->getParams())";}else{$cmd='Nette\Latte\Macros\UIMacros::callBlock'.($parent?'Parent':'')."(\$_l, $name, %node.array? + \$template->getParams())";}if($node->modifiers){return$writer->write("ob_start(); $cmd; echo %modify(ob_get_clean())");}else{return$writer->write($cmd);}}public
function macroIncludeBlock(MacroNode$node,$writer){return$writer->write('Nette\Latte\Macros\CoreMacros::includeTemplate(%node.word, %node.array? + get_defined_vars(), $_l->templates[%var])->render()',$this->parser->templateId);}public
function macroExtends(MacroNode$node,$writer){if(!$node->args){throw new ParseException("Missing destination in {extends}");}if(!empty($node->parentNode)){throw
new ParseException("{extends} must be placed outside any macro.");}if($this->extends!==NULL){throw
new ParseException("Multiple {extends} declarations are not allowed.");}$this->extends=$node->args!=='none';return$this->extends?'$_l->extends = '.($node->args==='auto'?'$layout':$writer->formatArgs()):'';}public
function macroBlock(MacroNode$node,$writer){$name=$node->tokenizer->fetchWord();if($node->name==='block'&&$name===FALSE){return$node->modifiers===''?'':'ob_start()';}$node->data->name=$name=ltrim($name,'#');$node->data->end='';if($name==NULL){if($node->name!=='snippet'){throw
new ParseException("Missing block name.");}}elseif(!Strings::match($name,'#^'.self::RE_IDENTIFIER.'$#')){if($node->name==='snippet'){$parent=$node->parentNode;while($parent&&$parent->name!=='snippet')$parent=$parent->parentNode;if(!$parent){throw
new ParseException("Dynamic snippets are allowed only inside static snippet.");}$parent->data->dynamic=TRUE;$tag=trim($node->tokenizer->fetchWord(),'<>');$tag=$tag?$tag:'div';$node->data->leave=TRUE;$node->data->end="\$_dynSnippets[\$_dynSnippetId] = ob_get_flush() ?>\n</$tag><?php";return$writer->write("?>\n<$tag id=\"<?php echo \$_dynSnippetId = \$control->getSnippetId({$writer->formatWord($name)}) ?>\"><?php ob_start()");}else{$node->data->leave=TRUE;$fname=$writer->formatWord($name);$node->data->end="}} call_user_func(reset(\$_l->blocks[$fname]), \$_l, get_defined_vars())";$func='_lb'.substr(md5($this->parser->templateId.$name),0,10).'_'.preg_replace('#[^a-z0-9_]#i','_',$name);return"//\n// block $name\n//\n"."if (!function_exists(\$_l->blocks[$fname][] = '$func')) { "."function $func(\$_l, \$_args) { ".(PHP_VERSION_ID>50208?'extract($_args)':'foreach ($_args as $__k => $__v) $$__k = $__v');}}if($node->name==='snippet'){$node->data->name=$name='_'.$name;}if(isset($this->namedBlocks[$name])){throw
new ParseException("Cannot redeclare static block '$name'");}$top=empty($node->parentNode);$this->namedBlocks[$name]=TRUE;$include='call_user_func(reset($_l->blocks[%var]), $_l, '.($node->name==='snippet'?'$template->getParams()':'get_defined_vars()').')';if($node->modifiers){$include="ob_start(); $include; echo %modify(ob_get_clean())";}if($node->name==='snippet'){$tag=trim($node->tokenizer->fetchWord(),'<>');$tag=$tag?$tag:'div';return$writer->write("?>\n<$tag id=\"<?php echo \$control->getSnippetId(%var) ?>\"><?php $include ?>\n</$tag><?php ",(string)
substr($name,1),$name);}elseif($node->name==='define'){return '';}elseif(!$top){return$writer->write($include,$name);}elseif($this->extends){return
'';}else{return$writer->write("if (!\$_l->extends) { $include; }",$name);}}public
function macroBlockEnd(MacroNode$node,$writer){if(isset($node->data->name)){if(empty($node->data->leave)){if(!empty($node->data->dynamic)){$node->content.='<?php if (isset($_dynSnippets)) return $_dynSnippets; ?>';}$this->namedBlocks[$node->data->name]=$node->content;$node->content='';}return$node->data->end;}elseif($node->modifiers){return$writer->write('echo %modify(ob_get_clean())');}}public
function macroIfset(MacroNode$node,$writer){if(strpos($node->args,'#')===FALSE){return
FALSE;}$list=array();while(($name=$node->tokenizer->fetchWord())!==FALSE){$list[]=$name[0]==='#'?'$_l->blocks["'.substr($name,1).'"]':$name;}return
'if (isset('.implode(', ',$list).')):';}public function macroControl(MacroNode$node,$writer){$pair=$node->tokenizer->fetchWord();if($pair===FALSE){throw
new ParseException("Missing control name in {control}");}$pair=explode(':',$pair,2);$name=$writer->formatWord($pair[0]);$method=isset($pair[1])?ucfirst($pair[1]):'';$method=Strings::match($method,'#^('.self::RE_IDENTIFIER.'|)$#')?"render$method":"{\"render$method\"}";$param=$writer->formatArray();if(strpos($node->args,'=>')===FALSE){$param=substr($param,6,-1);}return($name[0]==='$'?"if (is_object($name)) \$_ctrl = $name; else ":'').'$_ctrl = $control->getWidget('.$name.'); '.'if ($_ctrl instanceof Nette\Application\UI\IPartiallyRenderable) $_ctrl->validateControl(); '."\$_ctrl->$method($param)";}public
function macroLink(MacroNode$node,$writer){return$writer->write('echo %escape('.($node->name==='plink'?'$presenter':'$control').'->link(%node.word, %node.array?))');}public
function macroIfCurrent(MacroNode$node,$writer){return$writer->write(($node->args?'try { $presenter->link(%node.word, %node.array?); } catch (Nette\Application\UI\InvalidLinkException $e) {}':'').'; if ($presenter->getLastCreatedRequestFlag("current")):');}public
function macroContentType(MacroNode$node,$writer){if(strpos($node->args,'html')!==FALSE){$this->parser->context=array(Latte\Parser::CONTEXT_TEXT);}elseif(strpos($node->args,'xml')!==FALSE){$this->parser->context=array(Latte\Parser::CONTEXT_NONE,'xml');}elseif(strpos($node->args,'javascript')!==FALSE){$this->parser->context=array(Latte\Parser::CONTEXT_NONE,'js');}elseif(strpos($node->args,'css')!==FALSE){$this->parser->context=array(Latte\Parser::CONTEXT_NONE,'css');}elseif(strpos($node->args,'plain')!==FALSE){$this->parser->context=array(Latte\Parser::CONTEXT_NONE,'text');}else{$this->parser->context=array(Latte\Parser::CONTEXT_NONE);}if(strpos($node->args,'/')){return$writer->write('$netteHttpResponse->setHeader("Content-Type", %var)',$node->args);}}public
function macroStatus(MacroNode$node,$writer){return$writer->write((substr($node->args,-1)==='?'?'if (!$netteHttpResponse->isSent()) ':'').'$netteHttpResponse->setCode(%var)',(int)$node->args);}public
static function callBlock($context,$name,$params){if(empty($context->blocks[$name])){throw
new Nette\InvalidStateException("Cannot include undefined block '$name'.");}$block=reset($context->blocks[$name]);$block($context,$params);}public
static function callBlockParent($context,$name,$params){if(empty($context->blocks[$name])||($block=next($context->blocks[$name]))===FALSE){throw
new Nette\InvalidStateException("Cannot include undefined parent block '$name'.");}$block($context,$params);}public
static function renderSnippets($control,$local,$params){$control->snippetMode=FALSE;$payload=$control->getPresenter()->getPayload();if(isset($local->blocks)){foreach($local->blocks
as$name=>$function){if($name[0]!=='_'||!$control->isControlInvalid(substr($name,1))){continue;}ob_start();$function=reset($function);$snippets=$function($local,$params);$payload->snippets[$id=$control->getSnippetId(substr($name,1))]=ob_get_clean();if($snippets){$payload->snippets+=$snippets;unset($payload->snippets[$id]);}}}if($control
instanceof Nette\Application\UI\Control){foreach($control->getComponents(FALSE,'Nette\Application\UI\Control')as$child){if($child->isControlInvalid()){$child->snippetMode=TRUE;$child->render();$child->snippetMode=FALSE;}}}}}}

 namespace Nette\Latte{use Nette;class MacroTokenizer extends Nette\Utils\Tokenizer{const
T_WHITESPACE=1,T_COMMENT=2,T_SYMBOL=3,T_NUMBER=4,T_VARIABLE=5,T_STRING=6,T_CAST=7,T_KEYWORD=8,T_CHAR=9;public
function __construct($input){parent::__construct(array(self::T_WHITESPACE=>'\s+',self::T_COMMENT=>'(?s)/\*.*?\*/',self::T_STRING=>Parser::RE_STRING,self::T_KEYWORD=>'(?:true|false|null|and|or|xor|clone|new|instanceof|return|continue|break|[A-Z_][A-Z0-9_]{2,})(?![\w\pL_])',self::T_CAST=>'\([a-z]+\)',self::T_VARIABLE=>'\$[\w\pL_]+',self::T_NUMBER=>'[+-]?[0-9]+(?:\.[0-9]+)?(?:e[0-9]+)?',self::T_SYMBOL=>'[\w\pL_]+(?:-[\w\pL_]+)*',self::T_CHAR=>'::|=>|[^"\']',),'u');$this->ignored=array(self::T_COMMENT,self::T_WHITESPACE);$this->tokenize($input);}public
function fetchWord(){$word=$this->fetchUntil(self::T_WHITESPACE,',');$this->fetch(',');$this->fetchAll(self::T_WHITESPACE,self::T_COMMENT);return$word;}}}

 namespace Nette\Latte{use Nette;class ParseException extends Nette\Templating\FilterException{}}

 namespace Nette\Templating{use Nette,Nette\Caching;class FileTemplate extends Template
implements IFileTemplate{private$file;public function __construct($file=NULL){if($file!==NULL){$this->setFile($file);}}public
function setFile($file){$this->file=realpath($file);if(!$this->file){throw new Nette\FileNotFoundException("Missing template file '$file'.");}return$this;}public
function getFile(){return$this->file;}public function getSource(){return file_get_contents($this->file);}public
function render(){if($this->file==NULL){throw new Nette\InvalidStateException("Template file name was not specified.");}$cache=new
Caching\Cache($storage=$this->getCacheStorage(),'Nette.FileTemplate');if($storage
instanceof Caching\Storages\PhpFileStorage){$storage->hint=str_replace(dirname(dirname($this->file)),'',$this->file);}$cached=$compiled=$cache->load($this->file);if($compiled===NULL){try{$compiled="<?php\n\n// source file: $this->file\n\n?>".$this->compile();}catch(FilterException$e){$e->setSourceFile($this->file);throw$e;}$cache->save($this->file,$compiled,array(Caching\Cache::FILES=>$this->file,Caching\Cache::CONSTS=>'Nette\Framework::REVISION',));$cache->release();$cached=$cache->load($this->file);}if($cached!==NULL&&$storage
instanceof Caching\Storages\PhpFileStorage){Nette\Utils\LimitedScope::load($cached['file'],$this->getParams());}else{Nette\Utils\LimitedScope::evaluate($compiled,$this->getParams());}}}}


namespace {error_reporting(E_ALL|E_STRICT);iconv_set_encoding('internal_encoding','UTF-8');extension_loaded('mbstring')&&mb_internal_encoding('UTF-8');function
callback($callback,$m=NULL){return($m===NULL&&$callback instanceof Nette\Callback)?$callback:new
Nette\Callback($callback,$m);}Nette\Diagnostics\Debugger::_init();}

