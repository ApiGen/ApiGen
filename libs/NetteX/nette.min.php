<?php //netteloader=NetteX\Framework

namespace {/**
 * NetteX Framework (version 2.0-dev released on 2011-04-15, http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

error_reporting(E_ALL|E_STRICT);@set_magic_quotes_runtime(FALSE);iconv_set_encoding('internal_encoding','UTF-8');extension_loaded('mbstring')&&mb_internal_encoding('UTF-8');@header('X-Powered-By: NetteX Framework');define('NETTEX',TRUE);define('NETTEX_DIR',__DIR__);define('NETTEX_VERSION_ID',20000);define('NETTEX_PACKAGE','5.3');NetteX\Utils\SafeStream::register();function
callback($callback,$m=NULL){return($m===NULL&&$callback
instanceof
NetteX\Callback)?$callback:new
NetteX\Callback($callback,$m);}function
dump($var){foreach(func_get_args()as$arg)NetteX\Diagnostics\Debugger::dump($arg);return$var;}}namespace NetteX\Diagnostics{use
NetteX;interface
IPanel{function
getTab();function
getPanel();function
getId();}}namespace NetteX\Application{use
NetteX;interface
IPresenter{function
run(Request$request);}interface
IPresenterFactory{function
getPresenterClass(&$name);function
createPresenter($name);}interface
IResponse{function
send(NetteX\Http\IRequest$httpRequest,NetteX\Http\IResponse$httpResponse);}interface
IRouter{const
ONE_WAY=1;const
SECURED=2;function
match(NetteX\Http\IRequest$httpRequest);function
constructUrl(Request$appRequest,NetteX\Http\Url$refUri);}}namespace NetteX{use
NetteX;interface
IFreezable{function
freeze();function
isFrozen();}}namespace NetteX\ComponentModel{use
NetteX;interface
IComponent{const
NAME_SEPARATOR='-';function
getName();function
getParent();function
setParent(IContainer$parent=NULL,$name=NULL);}interface
IContainer
extends
IComponent{function
addComponent(IComponent$component,$name);function
removeComponent(IComponent$component);function
getComponent($name);function
getComponents($deep=FALSE,$filterType=NULL);}}namespace NetteX\Application\UI{use
NetteX;interface
ISignalReceiver{function
signalReceived($signal);}interface
IStatePersistent{function
loadState(array$params);function
saveState(array&$params);}interface
IPartiallyRenderable
extends
IRenderable{}interface
IRenderable{function
invalidateControl();function
isControlInvalid();}}namespace NetteX\Caching{use
NetteX;interface
IStorage{function
read($key);function
write($key,$data,array$dependencies);function
remove($key);function
clean(array$conds);}}namespace NetteX\Caching\Storages{use
NetteX;interface
IJournal{function
write($key,array$dependencies);function
clean(array$conditions);}}namespace NetteX\Config{use
NetteX;interface
IAdapter{static
function
load($file);static
function
save($config,$file);}}namespace NetteX\Database{use
NetteX;interface
ISupplementalDriver{function
delimite($name);function
formatDateTime(\DateTime$value);function
formatLike($value,$pos);function
applyLimit(&$sql,$limit,$offset);function
normalizeRow($row,$statement);}}namespace NetteX\DI{use
NetteX;interface
IContext{function
addService($name,$service,$singleton=TRUE,array$options=NULL);function
getService($name,array$options=NULL);function
removeService($name);function
hasService($name);}}namespace NetteX\Forms{use
NetteX;interface
IControl{function
loadHttpData();function
setValue($value);function
getValue();function
getRules();function
getErrors();function
isDisabled();function
translate($s,$count=NULL);}interface
ISubmitterControl
extends
IControl{function
isSubmittedBy();function
getValidationScope();}interface
IFormRenderer{function
render(Form$form);}}namespace NetteX\Http{use
NetteX;interface
IRequest{const
GET='GET',POST='POST',HEAD='HEAD',PUT='PUT',DELETE='DELETE';function
getUri();function
getQuery($key=NULL,$default=NULL);function
getPost($key=NULL,$default=NULL);function
getFile($key);function
getFiles();function
getCookie($key,$default=NULL);function
getCookies();function
getMethod();function
isMethod($method);function
getHeader($header,$default=NULL);function
getHeaders();function
isSecured();function
isAjax();function
getRemoteAddress();function
getRemoteHost();}interface
IResponse{const
PERMANENT=2116333333;const
BROWSER=0;const
S200_OK=200,S204_NO_CONTENT=204,S300_MULTIPLE_CHOICES=300,S301_MOVED_PERMANENTLY=301,S302_FOUND=302,S303_SEE_OTHER=303,S303_POST_GET=303,S304_NOT_MODIFIED=304,S307_TEMPORARY_REDIRECT=307,S400_BAD_REQUEST=400,S401_UNAUTHORIZED=401,S403_FORBIDDEN=403,S404_NOT_FOUND=404,S405_METHOD_NOT_ALLOWED=405,S410_GONE=410,S500_INTERNAL_SERVER_ERROR=500,S501_NOT_IMPLEMENTED=501,S503_SERVICE_UNAVAILABLE=503;function
setCode($code);function
getCode();function
setHeader($name,$value);function
addHeader($name,$value);function
setContentType($type,$charset=NULL);function
redirect($url,$code=self::S302_FOUND);function
setExpiration($seconds);function
isSent();function
getHeaders();function
setCookie($name,$value,$expire,$path=NULL,$domain=NULL,$secure=NULL,$httpOnly=NULL);function
deleteCookie($name,$path=NULL,$domain=NULL,$secure=NULL);}interface
ISessionStorage{function
open($savePath,$sessionName);function
close();function
read($id);function
write($id,$data);function
remove($id);function
clean($maxlifetime);}interface
IUser{function
login();function
logout($clearIdentity=FALSE);function
isLoggedIn();function
getIdentity();function
setAuthenticationHandler(NetteX\Security\IAuthenticator$handler);function
getAuthenticationHandler();function
setNamespace($namespace);function
getNamespace();function
getRoles();function
isInRole($role);function
isAllowed();function
setAuthorizationHandler(NetteX\Security\IAuthorizator$handler);function
getAuthorizationHandler();}}namespace NetteX\Localization{use
NetteX;interface
ITranslator{function
translate($message,$count=NULL);}}namespace NetteX\Mail{use
NetteX;interface
IMailer{function
send(Message$mail);}}namespace NetteX\Reflection{use
NetteX;interface
IAnnotation{function
__construct(array$values);}}namespace NetteX\Security{use
NetteX;interface
IAuthenticator{const
USERNAME=0,PASSWORD=1;const
IDENTITY_NOT_FOUND=1,INVALID_CREDENTIAL=2,FAILURE=3,NOT_APPROVED=4;function
authenticate(array$credentials);}interface
IAuthorizator{const
ALL=NULL;const
ALLOW=TRUE;const
DENY=FALSE;function
isAllowed($role,$resource,$privilege);}interface
IIdentity{function
getId();function
getRoles();}interface
IResource{function
getResourceId();}interface
IRole{function
getRoleId();}}namespace NetteX\Templating{use
NetteX;interface
ITemplate{function
render();}interface
IFileTemplate
extends
ITemplate{function
setFile($file);function
getFile();}}namespace NetteX{use
NetteX;class
ArgumentOutOfRangeException
extends\InvalidArgumentException{}class
InvalidStateException
extends\RuntimeException{}class
NotImplementedException
extends\LogicException{}class
NotSupportedException
extends\LogicException{}class
DeprecatedException
extends
NotSupportedException{}class
MemberAccessException
extends\LogicException{}class
IOException
extends\RuntimeException{}class
FileNotFoundException
extends
IOException{}class
DirectoryNotFoundException
extends
IOException{}class
InvalidArgumentException
extends\InvalidArgumentException{}class
OutOfRangeException
extends\OutOfRangeException{}class
UnexpectedValueException
extends\UnexpectedValueException{}class
StaticClassException
extends\LogicException{}class
FatalErrorException
extends\ErrorException{function
__construct($message,$code,$severity,$file,$line,$context){parent::__construct($message,$code,$severity,$file,$line);$this->context=$context;}}abstract
class
Object{static
function
getReflection(){return
new
Reflection\ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}static
function
__callStatic($name,$args){return
ObjectMixin::callStatic(get_called_class(),$name,$args);}static
function
extensionMethod($name,$callback=NULL){if(strpos($name,'::')===FALSE){$class=get_called_class();}else{list($class,$name)=explode('::',$name);}$class=new
Reflection\ClassType($class);if($callback===NULL){return$class->getExtensionMethod($name);}else{$class->setExtensionMethod($name,$callback);}}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}}namespace NetteX\Utils{use
NetteX;final
class
LimitedScope{private
static$vars;final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
evaluate(){if(func_num_args()>1){self::$vars=func_get_arg(1);extract(self::$vars);}return
eval('?>'.func_get_arg(0));}static
function
load(){if(func_num_args()>1){self::$vars=func_get_arg(1);extract(self::$vars);}return include func_get_arg(0);}}}namespace NetteX\Loaders{use
NetteX;abstract
class
AutoLoader
extends
NetteX\Object{static
private$loaders=array();public
static$count=0;final
static
function
load($type){foreach(func_get_args()as$type){if(!class_exists($type)){throw
new
NetteX\InvalidStateException("Unable to load class or interface '$type'.");}}}final
static
function
getLoaders(){return
array_values(self::$loaders);}function
register(){if(!function_exists('spl_autoload_register')){throw
new
NetteX\NotSupportedException('spl_autoload does not exist in this PHP installation.');}spl_autoload_register(array($this,'tryLoad'));self::$loaders[spl_object_hash($this)]=$this;}function
unregister(){unset(self::$loaders[spl_object_hash($this)]);return
spl_autoload_unregister(array($this,'tryLoad'));}abstract
function
tryLoad($type);}}namespace NetteX\Diagnostics{use
NetteX;final
class
Helpers{static
function
renderBlueScreen(\Exception$exception){if(class_exists('NetteX\Environment',FALSE)){$application=NetteX\Environment::getContext()->hasService('NetteX\\Application\\Application',TRUE)?NetteX\Environment::getContext()->getService('NetteX\\Application\\Application'):NULL;}static$errorTypes=array(E_ERROR=>'Fatal Error',E_USER_ERROR=>'User Error',E_RECOVERABLE_ERROR=>'Recoverable Error',E_CORE_ERROR=>'Core Error',E_COMPILE_ERROR=>'Compile Error',E_PARSE=>'Parse Error',E_WARNING=>'Warning',E_CORE_WARNING=>'Core Warning',E_COMPILE_WARNING=>'Compile Warning',E_USER_WARNING=>'User Warning',E_NOTICE=>'Notice',E_USER_NOTICE=>'User Notice',E_STRICT=>'Strict',E_DEPRECATED=>'Deprecated',E_USER_DEPRECATED=>'User Deprecated');$title=($exception
instanceof
NetteX\FatalErrorException&&isset($errorTypes[$exception->getSeverity()]))?$errorTypes[$exception->getSeverity()]:get_class($exception);$expandPath=NETTEX_DIR.DIRECTORY_SEPARATOR;$counter=0;?><!-- "' --></script></style></pre></xmp></table>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="robots" content="noindex,noarchive">
	<meta name="generator" content="NetteX Framework">

	<title><?php echo
htmlspecialchars($title)?></title><!-- <?php
$ex=$exception;echo$ex->getMessage(),($ex->getCode()?' #'.$ex->getCode():'');while((method_exists($ex,'getPrevious')&&$ex=$ex->getPrevious())||(isset($ex->previous)&&$ex=$ex->previous))echo'; caused by ',get_class($ex),' ',$ex->getMessage(),($ex->getCode()?' #'.$ex->getCode():'');?> -->

	<style type="text/css" class="nette">html{overflow-y:scroll}body{margin:0 0 2em;padding:0}#netteBluescreen{font:9pt/1.5 Verdana,sans-serif;background:white;color:#333;position:absolute;left:0;top:0;width:100%;z-index:23178;text-align:left}#netteBluescreen *{font:inherit;color:inherit;background:transparent;border:none;margin:0;padding:0;text-align:inherit;text-indent:0}#netteBluescreen b{font-weight:bold}#netteBluescreen i{font-style:italic}#netteBluescreen a{text-decoration:none;color:#328ADC;padding:2px 4px;margin:-2px -4px}#netteBluescreen a:hover,#netteBluescreen a:active,#netteBluescreen a:focus{color:#085AA3}#netteBluescreen a abbr{font-family:sans-serif;color:#BBB}#netteBluescreenIcon{position:absolute;right:.5em;top:.5em;z-index:23179;text-decoration:none;background:#CD1818;padding:3px}#netteBluescreenError{background:#CD1818;color:white;font:13pt/1.5 Verdana,sans-serif!important;display:block}#netteBluescreenError #netteBsSearch{color:#CD1818;font-size:.7em}#netteBluescreenError:hover #netteBsSearch{color:#ED8383}#netteBluescreen h1{font-size:18pt;font-weight:normal;text-shadow:1px 1px 0 rgba(0,0,0,.4);margin:.7em 0}#netteBluescreen h2{font:14pt/1.5 sans-serif!important;color:#888;margin:.6em 0}#netteBluescreen h3{font:bold 10pt/1.5 Verdana,sans-serif!important;margin:1em 0;padding:0}#netteBluescreen p,#netteBluescreen pre{margin:.8em 0}#netteBluescreen pre,#netteBluescreen code,#netteBluescreen table{font:9pt/1.5 Consolas,monospace!important}#netteBluescreen pre,#netteBluescreen table{background:#FDF5CE;padding:.4em .7em;border:1px dotted silver;overflow:auto}#netteBluescreen table pre{padding:0;margin:0;border:none}#netteBluescreen pre.nette-dump span{color:#C22}#netteBluescreen pre.nette-dump a{color:#333}#netteBluescreen div.panel{padding:1px 25px}#netteBluescreen div.inner{background:#F4F3F1;padding:.1em 1em 1em;border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px}#netteBluescreen table{border-collapse:collapse;width:100%}#netteBluescreen .outer{overflow:auto}#netteBluescreen td,#netteBluescreen th{vertical-align:top;text-align:left;padding:2px 6px;border:1px solid #e6dfbf}#netteBluescreen th{width:10%;font-weight:bold}#netteBluescreen tr:nth-child(2n),#netteBluescreen tr:nth-child(2n) pre{background-color:#F7F0CB}#netteBluescreen ol{margin:1em 0;padding-left:2.5em}#netteBluescreen ul{font:7pt/1.5 Verdana,sans-serif!important;padding:2em 4em;margin:1em 0 0;color:#777;background:#F6F5F3 url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFEAAAAjCAMAAADbuxbOAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAADBQTFRF/fz24d7Y7Onj5uLd9vPu3drUzMvG09LN39zW8e7o2NbQ3NnT29jS0M7J1tXQAAAApvmsFgAAABB0Uk5T////////////////////AOAjXRkAAAKlSURBVHja7FbbsqQgDAwENEgc//9vN+SCWDtbtXPmZR/Wc6o02mlC58LA9ckFAOszvMV8xNgyUjyXhojfMVKvRL0ZHavxXYy5JrmchMdzou8YlTClxajtK8ZGGpWRoBr1+gFjKfHkJPbizabLgzE3pH7Iu4K980xgFvlrVzMZoVBWhtvouCDdcTDmTgMCJdVxJ9MKO6XxnliM7hxi5lbj2ZVM4l8DqYyKoNLYcfqBB1/LpHYxEcfVG6ZpMDgyFUVWY/Q1sSYPpIdSAKWqLWL0XqWiMWc4hpH0OQOMOAgdycY4N9Sb7wWANQs3rsDSdLAYiuxi5siVfOhBWIrtH0G3kNaF/8Q4kCPE1kMucG/ZMUBUCOgiKJkPuWWTLGVgLGpwns1DraUayCtoBqERyaYtVsm85NActRooezvSLO/sKZP/nq8n4+xcyjNsRu8zW6KWpdb7wjiQd4WrtFZYFiKHENSmWp6xshh96c2RQ+c7Lt+qbijyEjHWUJ/pZsy8MGIUuzNiPySK2Gqoh6ZTRF6ko6q3nVTkaA//itIrDpW6l3SLo8juOmqMXkYknu5FdQxWbhCfKHEGDhxxyTVaXJF3ZjSl3jMksjSOOKmne9pI+mcG5QvaUJhI9HpkmRo2NpCrDJvsktRhRE2MM6F2n7dt4OaMUq8bCctk0+PoMRzL+1l5PZ2eyM/Owr86gf8z/tOM53lom5+nVcFuB+eJVzlXwAYy9TZ9s537tfqcsJWbEU4nBngZo6FfO9T9CdhfBtmk2dLiAy8uS4zwOpMx2HqYbTC+amNeAYTpsP4SIgvWfUBWXxn3CMHW3ffd7k3+YIkx7w0t/CVGvcPejoeOlzOWzeGbawOHqXQGUTMZRcfj4XPCgW9y/fuvVn8zD9P1QHzv80uAAQA0i3Jer7Jr7gAAAABJRU5ErkJggg==') 99% 10px no-repeat;border-top:1px solid #DDD}#netteBluescreen .highlight{background:#CD1818;color:white;font-weight:bold;font-style:normal;display:block;padding:0 .4em;margin:0 -.4em}#netteBluescreen .line{color:#9F9C7F;font-weight:normal;font-style:normal}#netteBluescreen a[href^=editor\:]{color:inherit;border-bottom:1px dotted #C1D2E1}</style>
</head>



<body>
<div id="netteBluescreen">
	<a id="netteBluescreenIcon" href="#" rel="next"><abbr>&#x25bc;</abbr></a

	><div>
		<div id="netteBluescreenError" class="panel">
			<h1><?php echo
htmlspecialchars($title),($exception->getCode()?' #'.$exception->getCode():'')?></h1>

			<p><?php echo
htmlspecialchars($exception->getMessage())?> <a href="http://www.google.cz/search?sourceid=nette&amp;q=<?php echo
urlencode($title.' '.preg_replace('#\'.*\'|".*"#Us','',$exception->getMessage()))?>" id="netteBsSearch">search&#x25ba;</a></p>
		</div>



		<?php $ex=$exception;$level=0;?>
		<?php do{?>

			<?php if($level++):?>
			<div class="panel">
			<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>">Caused by <abbr><?php echo($collapsed=$level>2)?'&#x25ba;':'&#x25bc;'?></abbr></a></h2>

			<div id="netteBsPnl<?php echo$counter?>" class="<?php echo$collapsed?'nette-collapsed ':''?>inner">
				<div class="panel">
					<h1><?php echo
htmlspecialchars(get_class($ex)),($ex->getCode()?' #'.$ex->getCode():'')?></h1>

					<p><b><?php echo
htmlspecialchars($ex->getMessage())?></b></p>
				</div>
			<?php endif?>



			<?php if($ex
instanceof
IPanel&&($tab=$ex->getTab())&&($panel=$ex->getPanel())):?>
			<div class="panel">
				<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>"><?php echo
htmlSpecialChars($tab)?> <abbr>&#x25bc;</abbr></a></h2>

				<div id="netteBsPnl<?php echo$counter?>" class="inner">
				<?php echo$panel?>
			</div></div>
			<?php endif?>



			<?php $stack=$ex->getTrace();$expanded=NULL?>
			<?php if(strpos($ex->getFile(),$expandPath)===0){foreach($stack
as$key=>$row){if(isset($row['file'])&&strpos($row['file'],$expandPath)!==0){$expanded=$key;break;}}}?>
			<?php if(is_file($ex->getFile())):?>
			<div class="panel">
			<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>">Source file <abbr><?php echo($collapsed=$expanded!==NULL)?'&#x25ba;':'&#x25bc;'?></abbr></a></h2>

			<div id="netteBsPnl<?php echo$counter?>" class="<?php echo$collapsed?'nette-collapsed ':''?>inner">
				<p><b>File:</b> <?php if(Debugger::$editor)echo'<a href="',htmlspecialchars(Helpers::editorLink($ex->getFile(),$ex->getLine())),'">'?>
				<?php echo
htmlspecialchars($ex->getFile()),(Debugger::$editor?'</a>':'')?> &nbsp; <b>Line:</b> <?php echo$ex->getLine()?></p>
				<pre><?php echo
Helpers::highlightFile($ex->getFile(),$ex->getLine())?></pre>
			</div></div>
			<?php endif?>



			<?php if(isset($stack[0]['class'])&&$stack[0]['class']==='NetteX\Diagnostics\Debugger'&&($stack[0]['function']==='_shutdownHandler'||$stack[0]['function']==='_errorHandler'))unset($stack[0])?>
			<?php if($stack):?>
			<div class="panel">
				<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>">Call stack <abbr>&#x25bc;</abbr></a></h2>

				<div id="netteBsPnl<?php echo$counter?>" class="inner">
				<ol>
					<?php foreach($stack
as$key=>$row):?>
					<li><p>

					<?php if(isset($row['file'])&&is_file($row['file'])):?>
						<?php echo
Debugger::$editor?'<a href="'.htmlspecialchars(Helpers::editorLink($row['file'],$row['line'])).'"':'<span';?> title="<?php echo
htmlSpecialChars($row['file'])?>">
						<?php echo
htmlSpecialChars(basename(dirname($row['file']))),'/<b>',htmlSpecialChars(basename($row['file'])),'</b>',(Debugger::$editor?'</a>':'</span>'),' (',$row['line'],')'?>
					<?php else:?>
						<i>inner-code</i><?php if(isset($row['line']))echo' (',$row['line'],')'?>
					<?php endif?>

					<?php if(isset($row['file'])&&is_file($row['file'])):?><a href="#" rel="netteBsSrc<?php echo"$level-$key"?>">source <abbr>&#x25ba;</abbr></a>&nbsp; <?php endif?>

					<?php if(isset($row['class']))echo$row['class'].$row['type']?>
					<?php echo$row['function']?>

					(<?php if(!empty($row['args'])):?><a href="#" rel="netteBsArgs<?php echo"$level-$key"?>">arguments <abbr>&#x25ba;</abbr></a><?php endif?>)
					</p>

					<?php if(!empty($row['args'])):?>
						<div class="nette-collapsed outer" id="netteBsArgs<?php echo"$level-$key"?>">
						<table>
						<?php

try{$r=isset($row['class'])?new\ReflectionMethod($row['class'],$row['function']):new\ReflectionFunction($row['function']);$params=$r->getParameters();}catch(\Exception$e){$params=array();}foreach($row['args']as$k=>$v){echo'<tr><th>',(isset($params[$k])?'$'.$params[$k]->name:"#$k"),'</th><td>';echo
Helpers::clickableDump($v);echo"</td></tr>\n";}?>
						</table>
						</div>
					<?php endif?>


					<?php if(isset($row['file'])&&is_file($row['file'])):?>
						<pre <?php if($expanded!==$key)echo'class="nette-collapsed"';?> id="netteBsSrc<?php echo"$level-$key"?>"><?php echo
Helpers::highlightFile($row['file'],$row['line'])?></pre>
					<?php endif?>

					</li>
					<?php endforeach?>
				</ol>
			</div></div>
			<?php endif?>



			<?php if(isset($ex->context)&&is_array($ex->context)):?>
			<div class="panel">
			<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>">Variables <abbr>&#x25ba;</abbr></a></h2>

			<div id="netteBsPnl<?php echo$counter?>" class="nette-collapsed inner">
			<div class="outer">
			<table>
			<?php

foreach($ex->context
as$k=>$v){echo'<tr><th>$',htmlspecialchars($k),'</th><td>',Helpers::clickableDump($v),"</td></tr>\n";}?>
			</table>
			</div>
			</div></div>
			<?php endif?>

		<?php }while((method_exists($ex,'getPrevious')&&$ex=$ex->getPrevious())||(isset($ex->previous)&&$ex=$ex->previous));?>
		<?php while(--$level)echo'</div></div>'?>



		<?php if(!empty($application)):?>
		<div class="panel">
		<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>">NetteX Application <abbr>&#x25ba;</abbr></a></h2>

		<div id="netteBsPnl<?php echo$counter?>" class="nette-collapsed inner">
			<h3>Requests</h3>
			<?php echo
Helpers::clickableDump($application->getRequests())?>

			<h3>Presenter</h3>
			<?php echo
Helpers::clickableDump($application->getPresenter())?>
		</div></div>
		<?php endif?>



		<div class="panel">
		<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>">Environment <abbr>&#x25ba;</abbr></a></h2>

		<div id="netteBsPnl<?php echo$counter?>" class="nette-collapsed inner">
			<?php
$list=get_defined_constants(TRUE);if(!empty($list['user'])):?>
			<h3><a href="#" rel="netteBsPnl-env-const">Constants <abbr>&#x25bc;</abbr></a></h3>
			<div class="outer">
			<table id="netteBsPnl-env-const">
			<?php

foreach($list['user']as$k=>$v){echo'<tr><th>',htmlspecialchars($k),'</th>';echo'<td>',Helpers::clickableDump($v),"</td></tr>\n";}?>
			</table>
			</div>
			<?php endif?>


			<h3><a href="#" rel="netteBsPnl-env-files">Included files <abbr>&#x25ba;</abbr></a> (<?php echo
count(get_included_files())?>)</h3>
			<div class="outer">
			<table id="netteBsPnl-env-files" class="nette-collapsed">
			<?php

foreach(get_included_files()as$v){echo'<tr><td>',htmlspecialchars($v),"</td></tr>\n";}?>
			</table>
			</div>


			<h3>$_SERVER</h3>
			<?php if(empty($_SERVER)):?>
			<p><i>empty</i></p>
			<?php else:?>
			<div class="outer">
			<table>
			<?php

foreach($_SERVER
as$k=>$v)echo'<tr><th>',htmlspecialchars($k),'</th><td>',Helpers::clickableDump($v),"</td></tr>\n";?>
			</table>
			</div>
			<?php endif?>
		</div></div>



		<div class="panel">
		<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>">HTTP request <abbr>&#x25ba;</abbr></a></h2>

		<div id="netteBsPnl<?php echo$counter?>" class="nette-collapsed inner">
			<?php if(function_exists('apache_request_headers')):?>
			<h3>Headers</h3>
			<div class="outer">
			<table>
			<?php

foreach(apache_request_headers()as$k=>$v)echo'<tr><th>',htmlspecialchars($k),'</th><td>',htmlspecialchars($v),"</td></tr>\n";?>
			</table>
			</div>
			<?php endif?>


			<?php foreach(array('_GET','_POST','_COOKIE')as$name):?>
			<h3>$<?php echo$name?></h3>
			<?php if(empty($GLOBALS[$name])):?>
			<p><i>empty</i></p>
			<?php else:?>
			<div class="outer">
			<table>
			<?php

foreach($GLOBALS[$name]as$k=>$v)echo'<tr><th>',htmlspecialchars($k),'</th><td>',Helpers::clickableDump($v),"</td></tr>\n";?>
			</table>
			</div>
			<?php endif?>
			<?php endforeach?>
		</div></div>



		<div class="panel">
		<h2><a href="#" rel="netteBsPnl<?php echo++$counter?>">HTTP response <abbr>&#x25ba;</abbr></a></h2>

		<div id="netteBsPnl<?php echo$counter?>" class="nette-collapsed inner">
			<h3>Headers</h3>
			<?php if(headers_list()):?>
			<pre><?php

foreach(headers_list()as$s)echo
htmlspecialchars($s),'<br>';?></pre>
			<?php else:?>
			<p><i>no headers</i></p>
			<?php endif?>
		</div></div>


		<ul>
			<li>Report generated at <?php echo@date('Y/m/d H:i:s',Debugger::$time)?></li>
			<?php if(preg_match('#^https?://#',Debugger::$source)):?>
				<li><a href="<?php echo
htmlSpecialChars(Debugger::$source)?>"><?php echo
htmlSpecialChars(Debugger::$source)?></a></li>
			<?php elseif(Debugger::$source):?>
				<li><?php echo
htmlSpecialChars(Debugger::$source)?></li>
			<?php endif?>
			<li>PHP <?php echo
htmlSpecialChars(PHP_VERSION)?></li>
			<?php if(isset($_SERVER['SERVER_SOFTWARE'])):?><li><?php echo
htmlSpecialChars($_SERVER['SERVER_SOFTWARE'])?></li><?php endif?>
			<?php if(class_exists('NetteX\Framework')):?><li><?php echo
htmlSpecialChars('NetteX Framework '.NetteX\Framework::VERSION)?> <i>(revision <?php echo
htmlSpecialChars(NetteX\Framework::REVISION)?>)</i></li><?php endif?>
		</ul>
	</div>
</div>

<script type="text/javascript">/*<![CDATA[*/var bs=document.getElementById("netteBluescreen");document.body.appendChild(bs);document.onkeyup=function(b){b=b||window.event;b.keyCode==27&&!b.shiftKey&&!b.altKey&&!b.ctrlKey&&!b.metaKey&&bs.onclick({target:document.getElementById("netteBluescreenIcon")})};
for(var i=0,styles=document.styleSheets;i<styles.length;i++)if((styles[i].owningElement||styles[i].ownerNode).className!=="nette"){styles[i].oldDisabled=styles[i].disabled;styles[i].disabled=true}else styles[i].addRule?styles[i].addRule(".nette-collapsed","display: none"):styles[i].insertRule(".nette-collapsed { display: none }",0);
bs.onclick=function(b){b=b||window.event;for(var a=b.target||b.srcElement;a&&a.tagName&&a.tagName.toLowerCase()!=="a";)a=a.parentNode;if(!a||!a.rel)return true;for(var d=a.getElementsByTagName("abbr")[0],c=a.rel==="next"?a.nextSibling:document.getElementById(a.rel);c.nodeType!==1;)c=c.nextSibling;b=c.currentStyle?c.currentStyle.display=="none":getComputedStyle(c,null).display=="none";try{d.innerHTML=String.fromCharCode(b?9660:9658)}catch(e){}c.style.display=b?c.tagName.toLowerCase()==="code"?"inline":
"block":"none";if(a.id==="netteBluescreenIcon"){a=0;for(d=document.styleSheets;a<d.length;a++)if((d[a].owningElement||d[a].ownerNode).className!=="nette")d[a].disabled=b?true:d[a].oldDisabled}return false};/*]]>*/</script>
</body>
</html><?php }static
function
renderDebugBar($panels){foreach($panels
as$key=>$panel){try{$panels[$key]=array('id'=>preg_replace('#[^a-z0-9]+#i','-',$panel->getId()),'tab'=>$tab=(string)$panel->getTab(),'panel'=>$tab?(string)$panel->getPanel():NULL);}catch(\Exception$e){$panels[$key]=array('id'=>"error-$key",'tab'=>"Error: $key",'panel'=>nl2br(htmlSpecialChars((string)$e)));}}?>




<!-- NetteX Debug Bar -->

<?php ob_start()?>
&nbsp;

<style id="nette-debug-style" class="nette">#nette-debug{display:none}body#nette-debug{margin:5px 5px 0;display:block}#nette-debug *{font:inherit;color:inherit;background:transparent;margin:0;padding:0;border:none;text-align:inherit;list-style:inherit}#nette-debug .nette-fixed-coords{position:fixed;_position:absolute;right:0;bottom:0}#nette-debug a{color:#125EAE;text-decoration:none}#nette-debug .nette-panel a{color:#125EAE;text-decoration:none}#nette-debug a:hover,#nette-debug a:active,#nette-debug a:focus{background-color:#125EAE;color:white}#nette-debug .nette-panel h2,#nette-debug .nette-panel h3,#nette-debug .nette-panel p{margin:.4em 0}#nette-debug .nette-panel table{border-collapse:collapse;background:#FDF5CE}#nette-debug .nette-panel tr:nth-child(2n) td{background:#F7F0CB}#nette-debug .nette-panel td,#nette-debug .nette-panel th{border:1px solid #E6DFBF;padding:2px 5px;vertical-align:top;text-align:left}#nette-debug .nette-panel th{background:#F4F3F1;color:#655E5E;font-size:90%;font-weight:bold}#nette-debug .nette-panel pre,#nette-debug .nette-panel code{font:9pt/1.5 Consolas,monospace}#nette-debug table .nette-right{text-align:right}.nette-hidden,.nette-collapsed{display:none}#nette-debug-bar{font:normal normal 12px/21px Tahoma,sans-serif;color:#333;border:1px solid #c9c9c9;background:#EDEAE0 url('data:image/png;base64,R0lGODlhAQAVALMAAOTh1/Px6eHe1fHv5e/s4vLw6Ofk2u3q4PPw6PPx6PDt5PLw5+Dd1OXi2Ojm3Orn3iH5BAAAAAAALAAAAAABABUAAAQPMISEyhpYkfOcaQAgCEwEADs=') repeat-x bottom;position:relative;height:1.75em;min-height:21px;_float:left;min-width:50px;white-space:nowrap;z-index:23181;opacity:.9;border-radius:3px;-moz-border-radius:3px;box-shadow:1px 1px 10px rgba(0,0,0,.15);-moz-box-shadow:1px 1px 10px rgba(0,0,0,.15);-webkit-box-shadow:1px 1px 10px rgba(0,0,0,.15)}#nette-debug-bar:hover{opacity:1}#nette-debug-bar ul{list-style:none none;margin-left:4px}#nette-debug-bar li{float:left}#nette-debug-bar img{vertical-align:middle;position:relative;top:-1px;margin-right:3px}#nette-debug-bar li a{color:#000;display:block;padding:0 4px}#nette-debug-bar li a:hover{color:black;background:#c3c1b8}#nette-debug-bar li .nette-warning{color:#D32B2B;font-weight:bold}#nette-debug-bar li>span{padding:0 4px}#nette-debug-logo{background:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAPCAYAAABwfkanAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABiFJREFUSMe1VglPlGcQ5i+1xjZNqxREtGq8ahCPWsVGvEDBA1BBRQFBDjkE5BYUzwpovRBUREBEBbl3OVaWPfj2vi82eTrvbFHamLRJ4yYTvm+u95mZZ96PoKAv+LOatXBYZ+Bx6uFy6DGnt1m0EOKwSmQzwmHTgX5B/1W+yM9GYJ02CX6/B/5ZF+w2A4x6FYGTYDVp4PdY2Tbrs5N+mnRa2Km4/wV6rhPzQQj5fDc1mJM5nd0iYdZtQWtrCxobGnDpUiledTynbuvg99mgUMhw924Trl2rR01NNSTNJE9iDpTV8innv4K2kZPLroPXbYLHZeSu2K1aeF0muJ2GvwGzmNSwU2E+svm8ZrgdBliMaha/34Vx+RAKCgpwpa4OdbW1UE/L2cc/68WtWzdRVlaG6uoqtD1/BA/pA1MIxLvtes7pc5vhoDOE/rOgbVSdf9aJWa8dBp0Kyg+jdLiTx2vQKWEyqGmcNkqg4iTC1+dzQatWkK+cJqPD7KyFaKEjvRuNjY24fLkGdXW1ePjwAeX4QHonDNI0A75+/RpqqqshH+6F2UAUMaupYXouykV0mp6SQ60coxgL8Z4aMg/4x675/V60v3jKB+Xl5WJibIC4KPEIS0qKqWv5GOh7BZ/HSIk9kA33o7y8DOfPZ6GQOipkXDZAHXKxr4ipqqpkKS6+iIrycgz2dyMnJxtVlZUsotNZWZmor79KBbvgpdjm5sfIzc1hv4L8fKJPDTfJZZc+gRYKr8sAEy2DcBRdEEk62ltx9uwZ5qNILoDU1l6mbrvx5EkzUlKSuTiR7PHjR3x4fv4FyIbeIic7G5WVFUyN+qtX+Lnt2SPcvn2LfURjhF7kE4WPDr+Bx+NEUVEhkpNPoImm5CSOl5aUIC3tLOMR59gtAY4HidGIzj14cB8ZGRkM8kJeHk6cOI4xWR8vSl5uLlJTT6O74xnT5lB8PM6cSYXVqILb5UBWZiYSExMYkE4zzjqX00QHG+h9AjPqMei0k3ywy2khMdNiq6BVCf04T6ekuBgJCUdRUVHOBQwPvkNSUiLjaGi4Q/5qFgYtHgTXRJdTT59GenoaA5gY64deq0Bc3EGuNj4+DnppEheLijhZRkY6SktLsGPHdi6irOwSFTRAgO04deokTSIFsbExuHfvLnFSx8DevelAfFwcA0lJTqZi5PDS9aci/sbE7Oe4wsICbtD27b/ye1NTI3FeSX4W2gdFALRD3A4eM44ePcKViuD79/8gnZP5Kg4+cCAW2dnnqUM2Lujw4UM4ePAA2ztfPsHIYA/sdOt43A50d7UFCjkUj+joXVBMDJDeDhcVk08cjd61C3v37uFYp8PKXX3X8xJRUTtw7FgSn3Xzxg10d7ZCqRjkM+02C7pettDNogqAFjzxuI3YHR2Nffv2coXy0V44HGZERm7kJNu2/cK8bW9rwbp1axnMnj27uUijQQOb1QyTcYZ3YMOGn/Hbzp1crAAvaDfY38O5hW3//n0ce+TIYWiUcub1xo0R2Lp1y8cYsUMWM125VhPe93Zj7do1vEPi26GfUdBFbhK8tGHrli1YsWwpgoOD0dXRQqAtXMCy8DBs3rwJoSGLsWrVclylBdoUGYlVK1dg9eqVCFsSSs8/4btvvmUwEnE0KTERISE/IiIiAsGLF2HhwgU8qbc97QgPX8qFr1mzGgu+/opzdL5o5l1aEhqC9evXYWlYKFYsD6e/YVj0w/dMGZVyBDMqeaDTRuKpkxYjIz2dOyeup6H3r2kkOuJ1H3N5Z1QUzp3LQF9vJ4xGLQYHXiM9LY0pEhsTg+PHj9HNcJu4OcL3uaQZY86LiZw8mcJTkmhBTUYJbU8fcoygobgWR4Z6iKtTPLE7d35HYkICT1dIZuY59HQ9412StBPQTMvw8Z6WaMNFxy3Gab4TeQT0M9IHwUT/G0i0MGIJ9CTiJjBIH+iQaQbC7+QnfEXiQL6xgF09TjETHCt8RbeMuil+D8RNsV1LHdQoZfR/iJJzCZuYmEE/Bd3MJNs/+0UURgFWJJ//aQ8k+CsxVTqnVytHObkQrUoG8T4/bs4u4ubbxLPwFzYNPc8HI2zijLm84l39Dx8hfwJenFezFBKKQwAAAABJRU5ErkJggg==') 0 50% no-repeat;min-width:45px;cursor:move}#nette-debug-logo span{display:none}#nette-debug-bar-bgl,#nette-debug-bar-bgx,#nette-debug-bar-bgr{position:absolute;z-index:-1;top:-7px;height:37px}#nette-debug .nette-panel{font:normal normal 12px/1.5 sans-serif;background:white;color:#333}#nette-debug h1{font:normal normal 23px/1.4 Tahoma,sans-serif;color:#575753;background:#EDEAE0;margin:-5px -5px 5px;padding:0 25px 5px 5px}#nette-debug .nette-mode-peek .nette-inner,#nette-debug .nette-mode-float .nette-inner{max-width:700px;max-height:500px;overflow:auto}#nette-debug .nette-panel .nette-icons{display:none}#nette-debug .nette-mode-peek{display:none;position:relative;z-index:23180;padding:5px;min-width:150px;min-height:50px;border:5px solid #EDEAE0;border-radius:5px;-moz-border-radius:5px}#nette-debug .nette-mode-peek h1{cursor:move}#nette-debug .nette-mode-float{position:relative;z-index:23179;padding:5px;min-width:150px;min-height:50px;border:5px solid #EDEAE0;border-radius:5px;-moz-border-radius:5px;opacity:.9;box-shadow:1px 1px 6px #666;-moz-box-shadow:1px 1px 6px rgba(0,0,0,.45);-webkit-box-shadow:1px 1px 6px #666}#nette-debug .nette-focused{z-index:23180;opacity:1}#nette-debug .nette-mode-float h1{cursor:move}#nette-debug .nette-mode-float .nette-icons{display:block;position:absolute;top:0;right:0;font-size:18px}#nette-debug .nette-icons a{color:#575753}#nette-debug .nette-icons a:hover{color:white}</style>

<!--[if lt IE 8]><style class="nette">#nette-debug-bar img{display:none}#nette-debug-bar li{border-left:1px solid #DCD7C8;padding:0 3px}#nette-debug-logo span{background:#edeae0;display:inline}</style><![endif]-->


<script id="nette-debug-script">/*<![CDATA[*/var NetteX=NetteX||{};
(function(){NetteX.Class=function(a){var b=a.constructor||function(){},c,d=Object.prototype.hasOwnProperty;delete a.constructor;if(a.Extends){var f=function(){this.constructor=b};f.prototype=a.Extends.prototype;b.prototype=new f;delete a.Extends}if(a.Static){for(c in a.Static)if(d.call(a.Static,c))b[c]=a.Static[c];delete a.Static}for(c in a)if(d.call(a,c))b.prototype[c]=a[c];return b};NetteX.Q=NetteX.Class({Static:{factory:function(a){return new NetteX.Q(a)},implement:function(a){var b,c=NetteX.Q.implement,
d=NetteX.Q.prototype,f=Object.prototype.hasOwnProperty;for(b in a)if(f.call(a,b)){c[b]=a[b];d[b]=function(i){return function(){return this.each(c[i],arguments)}}(b)}}},constructor:function(a){if(typeof a==="string")a=this._find(document,a);else if(!a||a.nodeType||a.length===undefined||a===window)a=[a];for(var b=0,c=a.length;b<c;b++)if(a[b])this[this.length++]=a[b]},length:0,find:function(a){return new NetteX.Q(this._find(this[0],a))},_find:function(a,b){if(!a||!b)return[];else if(document.querySelectorAll)return a.querySelectorAll(b);
else if(b.charAt(0)==="#")return[document.getElementById(b.substring(1))];else{b=b.split(".");var c=a.getElementsByTagName(b[0]||"*");if(b[1]){for(var d=[],f=RegExp("(^|\\s)"+b[1]+"(\\s|$)"),i=0,k=c.length;i<k;i++)f.test(c[i].className)&&d.push(c[i]);return d}else return c}},dom:function(){return this[0]},each:function(a,b){for(var c=0,d;c<this.length;c++)if((d=a.apply(this[c],b||[]))!==undefined)return d;return this}});var h=NetteX.Q.factory,e=NetteX.Q.implement;e({bind:function(a,b){if(document.addEventListener&&
(a==="mouseenter"||a==="mouseleave")){var c=b;a=a==="mouseenter"?"mouseover":"mouseout";b=function(g){for(var j=g.relatedTarget;j;j=j.parentNode)if(j===this)return;c.call(this,g)}}var d=e.data.call(this);d=d.events=d.events||{};if(!d[a]){var f=this,i=d[a]=[],k=e.bind.genericHandler=function(g){if(!g.target)g.target=g.srcElement;if(!g.preventDefault)g.preventDefault=function(){g.returnValue=false};if(!g.stopPropagation)g.stopPropagation=function(){g.cancelBubble=true};g.stopImmediatePropagation=function(){this.stopPropagation();
j=i.length};for(var j=0;j<i.length;j++)i[j].call(f,g)};if(document.addEventListener)this.addEventListener(a,k,false);else document.attachEvent&&this.attachEvent("on"+a,k)}d[a].push(b)},addClass:function(a){this.className=this.className.replace(/^|\s+|$/g," ").replace(" "+a+" "," ")+" "+a},removeClass:function(a){this.className=this.className.replace(/^|\s+|$/g," ").replace(" "+a+" "," ")},hasClass:function(a){return this.className.replace(/^|\s+|$/g," ").indexOf(" "+a+" ")>-1},show:function(){var a=
e.show.display=e.show.display||{},b=this.tagName;if(!a[b]){var c=document.body.appendChild(document.createElement(b));a[b]=e.css.call(c,"display")}this.style.display=a[b]},hide:function(){this.style.display="none"},css:function(a){return this.currentStyle?this.currentStyle[a]:window.getComputedStyle?document.defaultView.getComputedStyle(this,null).getPropertyValue(a):undefined},data:function(){return this.nette?this.nette:this.nette={}},val:function(){var a;if(!this.nodeName){a=0;for(len=this.length;a<
len;a++)if(this[a].checked)return this[a].value;return null}if(this.nodeName.toLowerCase()==="select"){a=this.selectedIndex;var b=this.options;if(a<0)return null;else if(this.type==="select-one")return b[a].value;a=0;values=[];for(len=b.length;a<len;a++)b[a].selected&&values.push(b[a].value);return values}if(this.type==="checkbox")return this.checked;return this.value.replace(/^\s+|\s+$/g,"")},_trav:function(a,b,c){for(b=b.split(".");a&&!(a.nodeType===1&&(!b[0]||a.tagName.toLowerCase()===b[0])&&(!b[1]||
e.hasClass.call(a,b[1])));)a=a[c];return h(a)},closest:function(a){return e._trav(this,a,"parentNode")},prev:function(a){return e._trav(this.previousSibling,a,"previousSibling")},next:function(a){return e._trav(this.nextSibling,a,"nextSibling")},offset:function(a){for(var b=this,c=a?{left:-a.left||0,top:-a.top||0}:e.position.call(b);b=b.offsetParent;){c.left+=b.offsetLeft;c.top+=b.offsetTop}if(a)e.position.call(this,{left:-c.left,top:-c.top});else return c},position:function(a){if(a){this.nette&&
this.nette.onmove&&this.nette.onmove.call(this,a);this.style.left=(a.left||0)+"px";this.style.top=(a.top||0)+"px"}else return{left:this.offsetLeft,top:this.offsetTop,width:this.offsetWidth,height:this.offsetHeight}},draggable:function(a){var b=h(this),c=document.documentElement,d;a=a||{};h(a.handle||this).bind("mousedown",function(f){f.preventDefault();f.stopPropagation();if(e.draggable.binded)return c.onmouseup(f);var i=b[0].offsetLeft-f.clientX,k=b[0].offsetTop-f.clientY;e.draggable.binded=true;
d=false;c.onmousemove=function(g){g=g||event;if(!d){a.draggedClass&&b.addClass(a.draggedClass);a.start&&a.start(g,b);d=true}b.position({left:g.clientX+i,top:g.clientY+k});return false};c.onmouseup=function(g){if(d){a.draggedClass&&b.removeClass(a.draggedClass);if(a.stop)a.stop(g||event,b)}e.draggable.binded=c.onmousemove=c.onmouseup=null;return false}}).bind("click",function(f){if(d){f.stopImmediatePropagation();preventClick=false}})}})})();
(function(){NetteX.Debug={};var h=NetteX.Q.factory,e=NetteX.Debug.Panel=NetteX.Class({Extends:NetteX.Q,Static:{PEEK:"nette-mode-peek",FLOAT:"nette-mode-float",WINDOW:"nette-mode-window",FOCUSED:"nette-focused",factory:function(a){return new e(a)},_toggle:function(a){var b=a.rel;b=b.charAt(0)==="#"?h(b):h(a)[b==="prev"?"prev":"next"](b.substring(4));if(b.css("display")==="none"){b.show();a.innerHTML=a.innerHTML.replace("►","▼")}else{b.hide();a.innerHTML=a.innerHTML.replace("▼","►")}}},constructor:function(a){NetteX.Q.call(this,
"#nette-debug-panel-"+a.replace("nette-debug-panel-",""))},reposition:function(){if(this.hasClass(e.WINDOW))window.resizeBy(document.documentElement.scrollWidth-document.documentElement.clientWidth,document.documentElement.scrollHeight-document.documentElement.clientHeight);else{this.position(this.position());if(this.position().width)document.cookie=this.dom().id+"="+this.position().left+":"+this.position().top+"; path=/"}},focus:function(){if(this.hasClass(e.WINDOW))this.data().win.focus();else{clearTimeout(this.data().blurTimeout);
this.addClass(e.FOCUSED).show()}},blur:function(){this.removeClass(e.FOCUSED);if(this.hasClass(e.PEEK)){var a=this;this.data().blurTimeout=setTimeout(function(){a.hide()},50)}},toFloat:function(){this.removeClass(e.WINDOW).removeClass(e.PEEK).addClass(e.FLOAT).show().reposition();return this},toPeek:function(){this.removeClass(e.WINDOW).removeClass(e.FLOAT).addClass(e.PEEK).hide();document.cookie=this.dom().id+"=; path=/"},toWindow:function(){var a=this,b,c;c=this.offset();var d=this.dom().id;c.left+=
typeof window.screenLeft==="number"?window.screenLeft:window.screenX+10;c.top+=typeof window.screenTop==="number"?window.screenTop:window.screenY+50;if(b=window.open("",d.replace(/-/g,"_"),"left="+c.left+",top="+c.top+",width="+c.width+",height="+(c.height+15)+",resizable=yes,scrollbars=yes")){c=b.document;c.write('<!DOCTYPE html><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>'+h("#nette-debug-style").dom().innerHTML+"</style><script>"+h("#nette-debug-script").dom().innerHTML+
'<\/script><body id="nette-debug">');c.body.innerHTML='<div class="nette-panel nette-mode-window" id="'+d+'">'+this.dom().innerHTML+"</div>";b.NetteX.Debug.Panel.factory(d).initToggler().reposition();c.title=a.find("h1").dom().innerHTML;h([b]).bind("unload",function(){a.toPeek();b.close()});h(c).bind("keyup",function(f){f.keyCode===27&&!f.shiftKey&&!f.altKey&&!f.ctrlKey&&!f.metaKey&&b.close()});document.cookie=d+"=window; path=/";this.hide().removeClass(e.FLOAT).removeClass(e.PEEK).addClass(e.WINDOW).data().win=
b}},init:function(){var a=this,b;a.data().onmove=function(c){var d=document,f=window.innerHeight||d.documentElement.clientHeight||d.body.clientHeight;c.left=Math.max(Math.min(c.left,0.8*this.offsetWidth),0.2*this.offsetWidth-(window.innerWidth||d.documentElement.clientWidth||d.body.clientWidth));c.top=Math.max(Math.min(c.top,0.8*this.offsetHeight),this.offsetHeight-f)};h(window).bind("resize",function(){a.reposition()});a.draggable({handle:a.find("h1"),stop:function(){a.toFloat()}}).bind("mouseenter",
function(){a.focus()}).bind("mouseleave",function(){a.blur()});this.initToggler();a.find(".nette-icons").find("a").bind("click",function(c){this.rel==="close"?a.toPeek():a.toWindow();c.preventDefault()});if(b=document.cookie.match(RegExp(a.dom().id+"=(window|(-?[0-9]+):(-?[0-9]+))")))b[2]?a.toFloat().position({left:b[2],top:b[3]}):a.toWindow();else a.addClass(e.PEEK)},initToggler:function(){var a=this;this.bind("click",function(b){var c=h(b.target).closest("a").dom();if(c&&c.rel){e._toggle(c);b.preventDefault();
a.reposition()}});return this}});NetteX.Debug.Bar=NetteX.Class({Extends:NetteX.Q,constructor:function(){NetteX.Q.call(this,"#nette-debug-bar")},init:function(){var a=this,b;a.data().onmove=function(c){var d=document,f=window.innerHeight||d.documentElement.clientHeight||d.body.clientHeight;c.left=Math.max(Math.min(c.left,0),this.offsetWidth-(window.innerWidth||d.documentElement.clientWidth||d.body.clientWidth));c.top=Math.max(Math.min(c.top,0),this.offsetHeight-f)};h(window).bind("resize",function(){a.position(a.position())});
a.draggable({draggedClass:"nette-dragged",stop:function(){document.cookie=a.dom().id+"="+a.position().left+":"+a.position().top+"; path=/"}});a.find("a").bind("click",function(c){if(this.rel==="close"){h("#nette-debug").hide();window.opera&&h("body").show()}else if(this.rel){var d=e.factory(this.rel);if(c.shiftKey)d.toFloat().toWindow();else if(d.hasClass(e.FLOAT)){var f=h(this).offset();d.offset({left:f.left-d.position().width+f.width+4,top:f.top-d.position().height-4}).toPeek()}else d.toFloat().position({left:d.position().left-
Math.round(Math.random()*100)-20,top:d.position().top-Math.round(Math.random()*100)-20}).reposition()}c.preventDefault()}).bind("mouseenter",function(){if(!(!this.rel||this.rel==="close"||a.hasClass("nette-dragged"))){var c=e.factory(this.rel);c.focus();if(c.hasClass(e.PEEK)){var d=h(this).offset();c.offset({left:d.left-c.position().width+d.width+4,top:d.top-c.position().height-4})}}}).bind("mouseleave",function(){!this.rel||this.rel==="close"||a.hasClass("nette-dragged")||e.factory(this.rel).blur()});
if(b=document.cookie.match(RegExp(a.dom().id+"=(-?[0-9]+):(-?[0-9]+)")))a.position({left:b[1],top:b[2]});a.find("a").each(function(){!this.rel||this.rel==="close"||e.factory(this.rel).init()})}})})();/*]]>*/</script>


<?php foreach($panels
as$id=>$panel):?>
<div class="nette-fixed-coords">
	<div class="nette-panel" id="nette-debug-panel-<?php echo$panel['id']?>">
		<div id="nette-debug-<?php echo$panel['id']?>"><?php echo$panel['panel']?></div>
		<div class="nette-icons">
			<a href="#" title="open in window">&curren;</a>
			<a href="#" rel="close" title="close window">&times;</a>
		</div>
	</div>
</div>
<?php endforeach?>

<div class="nette-fixed-coords">
	<div id="nette-debug-bar">
		<ul>
			<li id="nette-debug-logo" title="PHP <?php echo
htmlSpecialChars(PHP_VERSION." |\n".(isset($_SERVER['SERVER_SOFTWARE'])?$_SERVER['SERVER_SOFTWARE']." |\n":'').(class_exists('NetteX\Framework')?'NetteX Framework '.NetteX\Framework::VERSION.' ('.substr(NetteX\Framework::REVISION,8).')':''))?>">&nbsp;<span>NetteX Framework</span></li>
			<?php foreach($panels
as$panel):if(!$panel['tab'])continue;?>
			<li><?php if($panel['panel']):?><a href="#" rel="<?php echo$panel['id']?>"><?php echo
trim($panel['tab'])?></a><?php else:echo'<span>',trim($panel['tab']),'</span>';endif?></li>
			<?php endforeach?>
			<li><a href="#" rel="close" title="close debug bar">&times;</a></li>
		</ul>
	</div>
</div>
<?php $output=ob_get_clean();?>


<div id="nette-debug"></div>

<script>
(function (onloadOrig) {
	window.onload = function() {
		if (typeof onloadOrig === 'function') onloadOrig();
		var debug = document.getElementById('nette-debug');
		document.body.appendChild(debug);
		debug.innerHTML = <?php echo
json_encode(@iconv('UTF-16','UTF-8//IGNORE',iconv('UTF-8','UTF-16//IGNORE',$output)))?>;
		for (var i = 0, scripts = debug.getElementsByTagName('script'); i < scripts.length; i++) eval(scripts[i].innerHTML);
		(new NetteX.Debug.Bar).init();
		NetteX.Q.factory(debug).show();
	};
})(window.onload);
</script>

<!-- /NetteX Debug Bar -->
<?php }static
function
renderTab($id,$data){switch($id){case'time':?>
<span title="Execution time"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJ6SURBVDjLjZO7T1NhGMY7Mji6uJgYt8bElTjof6CDg4sMSqIxJsRGB5F4TwQSIg1QKC0KWmkZEEsKtEcSxF5ohV5pKSicXqX3aqGn957z+PUEGopiGJ583/A+v3znvPkJAAjWR0VNJG0kGhKahCFhXcN3YBFfx8Kry6ym4xIzce88/fbWGY2k5WRb77UTTbWuYA9gDGg7EVmSIOF4g5T7HZKuMcSW5djWDyL0uRf0dCc8inYYxTcw9fAiCMBYB3gVj1z7gLhNTjKCqHkYP79KENC9Bq3uxrrqORzy+9D3tPAAccspVx1gWg0KbaZFbGllWFM+xrKkFQudV0CeDfJsjN4+C2nracjunoPq5VXIBrowMK4V1gG1LGyWdbZwCalsBYUyh2KFQzpXxVqkAGswD3+qBDpZwow9iYE5v26/VwfUQnnznyhvjguQYabIIpKpYD1ahI8UTT92MUSFuP5Z/9TBTgOgFrVjp3nakaG/0VmEfpX58pwzjUEquNk362s+PP8XYD/KpYTBHmRg9Wch0QX1R80dCZhYipudYQY2Auib8RmODVCa4hfUK4ngaiiLNFNFdKeCWWscXZMbWy9Unv9/gsIQU09a4pwvUeA3Uapy2C2wCKXL0DqTePLexbWPOv79E8f0UWrencZ2poxciUWZlKssB4bcHeE83NsFuMgpo2iIpMuNa1TNu4XjhggWvb+R2K3wZdLlAZl8Fd9jRb5sD+Xx0RJBx5gdom6VsMEFDyWF0WyCeSOFcDKPnRxZYTQL5Rc/nn1w4oFsBaIhC3r6FRh5erPRhYMyHdeFw4C6zkRhmijM7CnMu0AUZonCDCnRJBqSus5/ABD6Ba5CkQS8AAAAAElFTkSuQmCC"
/><?php echo
number_format((microtime(TRUE)-Debugger::$time)*1000,1,'.',' ')?> ms</span>
<?php
return;case'memory':?>
<span title="The peak of allocated memory"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAGvSURBVDjLpZO7alZREEbXiSdqJJDKYJNCkPBXYq12prHwBezSCpaidnY+graCYO0DpLRTQcR3EFLl8p+9525xgkRIJJApB2bN+gZmqCouU+NZzVef9isyUYeIRD0RTz482xouBBBNHi5u4JlkgUfx+evhxQ2aJRrJ/oFjUWysXeG45cUBy+aoJ90Sj0LGFY6anw2o1y/mK2ZS5pQ50+2XiBbdCvPk+mpw2OM/Bo92IJMhgiGCox+JeNEksIC11eLwvAhlzuAO37+BG9y9x3FTuiWTzhH61QFvdg5AdAZIB3Mw50AKsaRJYlGsX0tymTzf2y1TR9WwbogYY3ZhxR26gBmocrxMuhZNE435FtmSx1tP8QgiHEvj45d3jNlONouAKrjjzWaDv4CkmmNu/Pz9CzVh++Yd2rIz5tTnwdZmAzNymXT9F5AtMFeaTogJYkJfdsaaGpyO4E62pJ0yUCtKQFxo0hAT1JU2CWNOJ5vvP4AIcKeao17c2ljFE8SKEkVdWWxu42GYK9KE4c3O20pzSpyyoCx4v/6ECkCTCqccKorNxR5uSXgQnmQkw2Xf+Q+0iqQ9Ap64TwAAAABJRU5ErkJggg=="
/><?php echo
function_exists('memory_get_peak_usage')?number_format(memory_get_peak_usage()/1000000,2,'.',' '):'n/a';?> MB</span>
<?php
return;case'dumps':if(!$data)return;?>
<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIASURBVDjLpVPPaxNREJ6Vt01caH4oWk1T0ZKlGIo9RG+BUsEK4kEP/Q8qPXnpqRdPBf8A8Wahhx7FQ0GF9FJ6UksqwfTSBDGyB5HkkphC9tfb7jfbtyQQTx142byZ75v5ZnZWC4KALmICPy+2DkvKIX2f/POz83LxCL7nrz+WPNcll49DrhM9v7xdO9JW330DuXrrqkFSgig5iR2Cfv3t3gNxOnv5BwU+eZ5HuON5/PMPJZKJ+yKQfpW0S7TxdC6WJaWkyvff1LDaFRAeLZj05MHsiPTS6hua0PUqtwC5sHq9zv9RYWl+nu5cETcnJ1M0M5WlWq3GsX6/T+VymRzHDluZiGYAAsw0TQahV8uyyGq1qFgskm0bHIO/1+sx1rFtchJhArwEyIQ1Gg2WD2A6nWawHQJVDIWgIJfLhQowTIeE9D0mKAU8qPC0220afsWFQoH93W6X7yCDJ+DEBeBmsxnPIJVKxWQVUwry+XyUwBlKMKwA8jqdDhOVCqVAzQDVvXAXhOdGBFgymYwrGoZBmUyGjxCCdF0fSahaFdgoTHRxfTveMCXvWfkuE3Y+f40qhgT/nMitupzApdvT18bu+YeDQwY9Xl4aG9/d/URiMBhQq/dvZMeVghtT17lSZW9/rAKsvPa/r9Fc2dw+Pe0/xI6kM9mT5vtXy+Nw2kU/5zOGRpvuMIu0YAAAAABJRU5ErkJggg==" />variables
<?php
return;case'errors':if(!$data)return;?>
<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIsSURBVDjLpVNLSJQBEP7+h6uu62vLVAJDW1KQTMrINQ1vPQzq1GOpa9EppGOHLh0kCEKL7JBEhVCHihAsESyJiE4FWShGRmauu7KYiv6Pma+DGoFrBQ7MzGFmPr5vmDFIYj1mr1WYfrHPovA9VVOqbC7e/1rS9ZlrAVDYHig5WB0oPtBI0TNrUiC5yhP9jeF4X8NPcWfopoY48XT39PjjXeF0vWkZqOjd7LJYrmGasHPCCJbHwhS9/F8M4s8baid764Xi0Ilfp5voorpJfn2wwx/r3l77TwZUvR+qajXVn8PnvocYfXYH6k2ioOaCpaIdf11ivDcayyiMVudsOYqFb60gARJYHG9DbqQFmSVNjaO3K2NpAeK90ZCqtgcrjkP9aUCXp0moetDFEeRXnYCKXhm+uTW0CkBFu4JlxzZkFlbASz4CQGQVBFeEwZm8geyiMuRVntzsL3oXV+YMkvjRsydC1U+lhwZsWXgHb+oWVAEzIwvzyVlk5igsi7DymmHlHsFQR50rjl+981Jy1Fw6Gu0ObTtnU+cgs28AKgDiy+Awpj5OACBAhZ/qh2HOo6i+NeA73jUAML4/qWux8mt6NjW1w599CS9xb0mSEqQBEDAtwqALUmBaG5FV3oYPnTHMjAwetlWksyByaukxQg2wQ9FlccaK/OXA3/uAEUDp3rNIDQ1ctSk6kHh1/jRFoaL4M4snEMeD73gQx4M4PsT1IZ5AfYH68tZY7zv/ApRMY9mnuVMvAAAAAElFTkSuQmCC"
/><span class="nette-warning"><?php echo
array_sum($data)?> errors</span>
<?php }}static
function
renderPanel($id,$data){switch($id){case'dumps':?>
<style>#nette-debug-dumps h2{font:11pt/1.5 sans-serif;margin:0;padding:2px 8px;background:#3484d2;color:white}#nette-debug-dumps table{width:100%}#nette-debug #nette-debug-dumps a{color:#333;background:transparent}#nette-debug-dumps a abbr{font-family:sans-serif;color:#999}#nette-debug-dumps pre.nette-dump span{color:#c16549}</style>


<h1>Dumped variables</h1>

<div class="nette-inner">
<?php foreach($data
as$item):?>
	<?php if($item['title']):?>
	<h2><?php echo
htmlspecialchars($item['title'])?></h2>
	<?php endif?>

	<table>
	<?php $i=0?>
	<?php foreach($item['dump']as$key=>$dump):?>
	<tr class="<?php echo$i++%
2?'nette-alt':''?>">
		<th><?php echo
htmlspecialchars($key)?></th>
		<td><?php echo$dump?></td>
	</tr>
	<?php endforeach?>
	</table>
<?php endforeach?>
</div>
<?php
return;case'errors':?>
<h1>Errors</h1>

<?php $relative=isset($_SERVER['SCRIPT_FILENAME'])?strtr(dirname(dirname($_SERVER['SCRIPT_FILENAME'])),'/',DIRECTORY_SEPARATOR):NULL?>

<div class="nette-inner">
<table>
<?php $i=0?>
<?php foreach($data
as$item=>$count):list($message,$file,$line)=explode('|',$item)?>
<tr class="<?php echo$i++%
2?'nette-alt':''?>">
	<td class="nette-right"><?php echo$count?"$count\xC3\x97":''?></td>
	<td><pre><?php echo
htmlspecialchars($message),' in ',(Debugger::$editor?'<a href="'.Helpers::editorLink($file,$line).'">':''),htmlspecialchars(($relative?str_replace($relative,"...",$file):$file)),':',$line,(Debugger::$editor?'</a>':'')?></pre></td>
</tr>
<?php endforeach?>
</table>
</div><?php }}static
function
highlightFile($file,$line,$count=15){if(function_exists('ini_set')){ini_set('highlight.comment','#999; font-style: italic');ini_set('highlight.default','#000');ini_set('highlight.html','#06B');ini_set('highlight.keyword','#D24; font-weight: bold');ini_set('highlight.string','#080');}$start=max(1,$line-floor($count/2));$source=@file_get_contents($file);if(!$source)return;$source=explode("\n",highlight_string($source,TRUE));$spans=1;$out=$source[0];$source=explode('<br />',$source[1]);array_unshift($source,NULL);$i=$start;while(--$i>=1){if(preg_match('#.*(</?span[^>]*>)#',$source[$i],$m)){if($m[1]!=='</span>'){$spans++;$out.=$m[1];}break;}}$source=array_slice($source,$start,$count,TRUE);end($source);$numWidth=strlen((string)key($source));foreach($source
as$n=>$s){$spans+=substr_count($s,'<span')-substr_count($s,'</span');$s=str_replace(array("\r","\n"),array('',''),$s);preg_match_all('#<[^>]+>#',$s,$tags);if($n===$line){$out.=sprintf("<span class='highlight'>%{$numWidth}s:    %s\n</span>%s",$n,strip_tags($s),implode('',$tags[0]));}else{$out.=sprintf("<span class='line'>%{$numWidth}s:</span>    %s\n",$n,$s);}}return$out.str_repeat('</span>',$spans).'</code>';}static
function
editorLink($file,$line){return
strtr(Debugger::$editor,array('%file'=>rawurlencode($file),'%line'=>$line));}static
function
htmlDump(&$var,$level=0){static$tableUtf,$tableBin,$reBinary='#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u';if($tableUtf===NULL){foreach(range("\x00","\xFF")as$ch){if(ord($ch)<32&&strpos("\r\n\t",$ch)===FALSE){$tableUtf[$ch]=$tableBin[$ch]='\\x'.str_pad(dechex(ord($ch)),2,'0',STR_PAD_LEFT);}elseif(ord($ch)<127){$tableUtf[$ch]=$tableBin[$ch]=$ch;}else{$tableUtf[$ch]=$ch;$tableBin[$ch]='\\x'.dechex(ord($ch));}}$tableBin["\\"]='\\\\';$tableBin["\r"]='\\r';$tableBin["\n"]='\\n';$tableBin["\t"]='\\t';$tableUtf['\\x']=$tableBin['\\x']='\\\\x';}if(is_bool($var)){return($var?'TRUE':'FALSE')."\n";}elseif($var===NULL){return"NULL\n";}elseif(is_int($var)){return"$var\n";}elseif(is_float($var)){$var=var_export($var,TRUE);if(strpos($var,'.')===FALSE)$var.='.0';return"$var\n";}elseif(is_string($var)){if(Debugger::$maxLen&&strlen($var)>Debugger::$maxLen){$s=htmlSpecialChars(substr($var,0,Debugger::$maxLen),ENT_NOQUOTES).' ... ';}else{$s=htmlSpecialChars($var,ENT_NOQUOTES);}$s=strtr($s,preg_match($reBinary,$s)||preg_last_error()?$tableBin:$tableUtf);$len=strlen($var);return"\"$s\"".($len>1?" ($len)":"")."\n";}elseif(is_array($var)){$s="<span>array</span>(".count($var).") ";$space=str_repeat($space1='   ',$level);$brackets=range(0,count($var)-1)===array_keys($var)?"[]":"{}";static$marker;if($marker===NULL)$marker=uniqid("\x00",TRUE);if(empty($var)){}elseif(isset($var[$marker])){$brackets=$var[$marker];$s.="$brackets[0] *RECURSION* $brackets[1]";}elseif($level<Debugger::$maxDepth||!Debugger::$maxDepth){$s.="<code>$brackets[0]\n";$var[$marker]=$brackets;foreach($var
as$k=>&$v){if($k===$marker)continue;$k=is_int($k)?$k:'"'.htmlSpecialChars(strtr($k,preg_match($reBinary,$k)||preg_last_error()?$tableBin:$tableUtf)).'"';$s.="$space$space1$k => ".self::htmlDump($v,$level+1);}unset($var[$marker]);$s.="$space$brackets[1]</code>";}else{$s.="$brackets[0] ... $brackets[1]";}return$s."\n";}elseif(is_object($var)){$arr=(array)$var;$s="<span>".get_class($var)."</span>(".count($arr).") ";$space=str_repeat($space1='   ',$level);static$list=array();if(empty($arr)){}elseif(in_array($var,$list,TRUE)){$s.="{ *RECURSION* }";}elseif($level<Debugger::$maxDepth||!Debugger::$maxDepth){$s.="<code>{\n";$list[]=$var;foreach($arr
as$k=>&$v){$m='';if($k[0]==="\x00"){$m=$k[1]==='*'?' <span>protected</span>':' <span>private</span>';$k=substr($k,strrpos($k,"\x00")+1);}$k=htmlSpecialChars(strtr($k,preg_match($reBinary,$k)||preg_last_error()?$tableBin:$tableUtf));$s.="$space$space1\"$k\"$m => ".self::htmlDump($v,$level+1);}array_pop($list);$s.="$space}</code>";}else{$s.="{ ... }";}return$s."\n";}elseif(is_resource($var)){return"<span>".htmlSpecialChars(get_resource_type($var))." resource</span>\n";}else{return"<span>unknown type</span>\n";}}static
function
jsonDump(&$var,$level=0){if(is_bool($var)||is_null($var)||is_int($var)||is_float($var)){return$var;}elseif(is_string($var)){if(Debugger::$maxLen&&strlen($var)>Debugger::$maxLen){$var=substr($var,0,Debugger::$maxLen)." \xE2\x80\xA6 ";}return@iconv('UTF-16','UTF-8//IGNORE',iconv('UTF-8','UTF-16//IGNORE',$var));}elseif(is_array($var)){static$marker;if($marker===NULL)$marker=uniqid("\x00",TRUE);if(isset($var[$marker])){return"\xE2\x80\xA6RECURSION\xE2\x80\xA6";}elseif($level<Debugger::$maxDepth||!Debugger::$maxDepth){$var[$marker]=TRUE;$res=array();foreach($var
as$k=>&$v){if($k!==$marker)$res[self::jsonDump($k)]=self::jsonDump($v,$level+1);}unset($var[$marker]);return$res;}else{return" \xE2\x80\xA6 ";}}elseif(is_object($var)){$arr=(array)$var;static$list=array();if(in_array($var,$list,TRUE)){return"\xE2\x80\xA6RECURSION\xE2\x80\xA6";}elseif($level<Debugger::$maxDepth||!Debugger::$maxDepth){$list[]=$var;$res=array("\x00"=>'(object) '.get_class($var));foreach($arr
as$k=>&$v){if($k[0]==="\x00"){$k=substr($k,strrpos($k,"\x00")+1);}$res[self::jsonDump($k)]=self::jsonDump($v,$level+1);}array_pop($list);return$res;}else{return" \xE2\x80\xA6 ";}}elseif(is_resource($var)){return"resource ".get_resource_type($var);}else{return"unknown type";}}static
function
clickableDump($dump){return'<pre class="nette-dump">'.preg_replace_callback('#^( *)((?>[^(]{1,200}))\((\d+)\) <code>#m',function($m){return"$m[1]<a href='#' rel='next'>$m[2]($m[3]) ".(trim($m[1])||$m[3]<7?'<abbr>&#x25bc;</abbr> </a><code>':'<abbr>&#x25ba;</abbr> </a><code class="nette-collapsed">');},self::htmlDump($dump)).'</pre>';}}}namespace NetteX\Utils{use
NetteX;final
class
SafeStream{const
PROTOCOL='safe';private$handle;private$tempHandle;private$file;private$tempFile;private$deleteFile;private$writeError=FALSE;static
function
register(){return
stream_wrapper_register(self::PROTOCOL,__CLASS__);}function
stream_open($path,$mode,$options,&$opened_path){$path=substr($path,strlen(self::PROTOCOL)+3);$flag=trim($mode,'rwax+');$mode=trim($mode,'tb');$use_path=(bool)(STREAM_USE_PATH&$options);if($mode==='r'){return$this->checkAndLock($this->tempHandle=fopen($path,'r'.$flag,$use_path),LOCK_SH);}elseif($mode==='r+'){if(!$this->checkAndLock($this->handle=fopen($path,'r'.$flag,$use_path),LOCK_EX)){return
FALSE;}}elseif($mode[0]==='x'){if(!$this->checkAndLock($this->handle=fopen($path,'x'.$flag,$use_path),LOCK_EX)){return
FALSE;}$this->deleteFile=TRUE;}elseif($mode[0]==='w'||$mode[0]==='a'){if($this->checkAndLock($this->handle=@fopen($path,'x'.$flag,$use_path),LOCK_EX)){$this->deleteFile=TRUE;}elseif(!$this->checkAndLock($this->handle=fopen($path,'a+'.$flag,$use_path),LOCK_EX)){return
FALSE;}}else{trigger_error("Unknown mode $mode",E_USER_WARNING);return
FALSE;}$tmp='~~'.lcg_value().'.tmp';if(!$this->tempHandle=fopen($path.$tmp,(strpos($mode,'+')?'x+':'x').$flag,$use_path)){$this->clean();return
FALSE;}$this->tempFile=realpath($path.$tmp);$this->file=substr($this->tempFile,0,-strlen($tmp));if($mode==='r+'||$mode[0]==='a'){$stat=fstat($this->handle);fseek($this->handle,0);if(stream_copy_to_stream($this->handle,$this->tempHandle)!==$stat['size']){$this->clean();return
FALSE;}if($mode[0]==='a'){fseek($this->tempHandle,0,SEEK_END);}}return
TRUE;}private
function
checkAndLock($handle,$lock){if(!$handle){return
FALSE;}elseif(!flock($handle,$lock)){fclose($handle);return
FALSE;}return
TRUE;}private
function
clean(){flock($this->handle,LOCK_UN);fclose($this->handle);if($this->deleteFile){unlink($this->file);}if($this->tempHandle){fclose($this->tempHandle);unlink($this->tempFile);}}function
stream_close(){if(!$this->tempFile){flock($this->tempHandle,LOCK_UN);fclose($this->tempHandle);return;}flock($this->handle,LOCK_UN);fclose($this->handle);fclose($this->tempHandle);if($this->writeError||!rename($this->tempFile,$this->file)){unlink($this->tempFile);if($this->deleteFile){unlink($this->file);}}}function
stream_read($length){return
fread($this->tempHandle,$length);}function
stream_write($data){$len=strlen($data);$res=fwrite($this->tempHandle,$data,$len);if($res!==$len){$this->writeError=TRUE;}return$res;}function
stream_tell(){return
ftell($this->tempHandle);}function
stream_eof(){return
feof($this->tempHandle);}function
stream_seek($offset,$whence){return
fseek($this->tempHandle,$offset,$whence)===0;}function
stream_stat(){return
fstat($this->tempHandle);}function
url_stat($path,$flags){$path=substr($path,strlen(self::PROTOCOL)+3);return($flags&STREAM_URL_STAT_LINK)?@lstat($path):@stat($path);}function
unlink($path){$path=substr($path,strlen(self::PROTOCOL)+3);return
unlink($path);}}}namespace NetteX\Application{use
NetteX;class
Application
extends
NetteX\Object{public
static$maxLoop=20;public$catchExceptions;public$errorPresenter;public$onStartup;public$onShutdown;public$onRequest;public$onResponse;public$onError;public$allowedMethods=array('GET','POST','HEAD','PUT','DELETE');private$requests=array();private$presenter;private$context;function
run(){$httpRequest=$this->getHttpRequest();$httpResponse=$this->getHttpResponse();if($this->allowedMethods){$method=$httpRequest->getMethod();if(!in_array($method,$this->allowedMethods,TRUE)){$httpResponse->setCode(NetteX\Http\IResponse::S501_NOT_IMPLEMENTED);$httpResponse->setHeader('Allow',implode(',',$this->allowedMethods));echo'<h1>Method '.htmlSpecialChars($method).' is not implemented</h1>';return;}}$request=NULL;$repeatedError=FALSE;do{try{if(count($this->requests)>self::$maxLoop){throw
new
ApplicationException('Too many loops detected in application life cycle.');}if(!$request){$this->onStartup($this);$session=$this->getSession();if(!$session->isStarted()&&$session->exists()){$session->start();}$router=$this->getRouter();NetteX\Diagnostics\Debugger::addPanel(new
Diagnostics\RoutingPanel($router,$httpRequest));$request=$router->match($httpRequest);if(!$request
instanceof
Request){$request=NULL;throw
new
BadRequestException('No route for HTTP request.');}if(strcasecmp($request->getPresenterName(),$this->errorPresenter)===0){throw
new
BadRequestException('Invalid request. Presenter is not achievable.');}}$this->requests[]=$request;$this->onRequest($this,$request);$presenterName=$request->getPresenterName();try{$this->presenter=$this->getPresenterFactory()->createPresenter($presenterName);}catch(InvalidPresenterException$e){throw
new
BadRequestException($e->getMessage(),404,$e);}$this->getPresenterFactory()->getPresenterClass($presenterName);$request->setPresenterName($presenterName);$request->freeze();$response=$this->presenter->run($request);$this->onResponse($this,$response);if($response
instanceof
Responses\ForwardResponse){$request=$response->getRequest();continue;}elseif($response
instanceof
IResponse){$response->send($httpRequest,$httpResponse);}break;}catch(\Exception$e){$this->onError($this,$e);if(!$this->catchExceptions){$this->onShutdown($this,$e);throw$e;}if($repeatedError){$e=new
ApplicationException('An error occured while executing error-presenter',0,$e);}if(!$httpResponse->isSent()){$httpResponse->setCode($e
instanceof
BadRequestException?$e->getCode():500);}if(!$repeatedError&&$this->errorPresenter){$repeatedError=TRUE;if($this->presenter
instanceof
UI\Presenter){try{$this->presenter->forward(":$this->errorPresenter:",array('exception'=>$e));}catch(AbortException$foo){$request=$this->presenter->getLastCreatedRequest();}}else{$request=new
Request($this->errorPresenter,Request::FORWARD,array('exception'=>$e));}}else{if($e
instanceof
BadRequestException){$code=$e->getCode();}else{$code=500;NetteX\Diagnostics\Debugger::log($e,NetteX\Diagnostics\Debugger::ERROR);}$messages=array(0=>array('Oops...','Your browser sent a request that this server could not understand or process.'),403=>array('Access Denied','You do not have permission to view this page. Please try contact the web site administrator if you believe you should be able to view this page.'),404=>array('Page Not Found','The page you requested could not be found. It is possible that the address is incorrect, or that the page no longer exists. Please use a search engine to find what you are looking for.'),405=>array('Method Not Allowed','The requested method is not allowed for the URL.'),410=>array('Page Not Found','The page you requested has been taken off the site. We apologize for the inconvenience.'),500=>array('Server Error','We\'re sorry! The server encountered an internal error and was unable to complete your request. Please try again later.'));$message=isset($messages[$code])?$messages[$code]:$messages[0];?>
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name=robots content=noindex><meta name=generator content="NetteX Framework">
<style>body{color:#333;background:white;width:500px;margin:100px auto}h1{font:bold 47px/1.5 sans-serif;margin:.6em 0}p{font:21px/1.5 Georgia,serif;margin:1.5em 0}small{font-size:70%;color:gray}</style>

<title><?php echo$message[0]?></title>

<h1><?php echo$message[0]?></h1>

<p><?php echo$message[1]?></p>

<?php if($code):?><p><small>error <?php echo$code?></small></p><?php endif?>
<?php
break;}}}while(1);$this->onShutdown($this,isset($e)?$e:NULL);}final
function
getRequests(){return$this->requests;}final
function
getPresenter(){return$this->presenter;}function
setContext(NetteX\DI\IContext$context){$this->context=$context;return$this;}final
function
getContext(){return$this->context;}final
function
getService($name,array$options=NULL){return$this->context->getService($name,$options);}function
getRouter(){return$this->context->getService('NetteX\\Application\\IRouter');}function
setRouter(IRouter$router){$this->context->addService('NetteX\\Application\\IRouter',$router);return$this;}function
getPresenterFactory(){return$this->context->getService('NetteX\\Application\\IPresenterFactory');}protected
function
getHttpRequest(){return$this->context->getService('NetteX\\Web\\IHttpRequest');}protected
function
getHttpResponse(){return$this->context->getService('NetteX\\Web\\IHttpResponse');}protected
function
getSession($namespace=NULL){$handler=$this->context->getService('NetteX\\Web\\Session');return$namespace===NULL?$handler:$handler->getNamespace($namespace);}function
storeRequest($expiration='+ 10 minutes'){$session=$this->getSession('NetteX.Application/requests');do{$key=NetteX\StringUtils::random(5);}while(isset($session[$key]));$session[$key]=end($this->requests);$session->setExpiration($expiration,$key);return$key;}function
restoreRequest($key){$session=$this->getSession('NetteX.Application/requests');if(isset($session[$key])){$request=clone$session[$key];unset($session[$key]);$request->setFlag(Request::RESTORED,TRUE);$this->presenter->sendResponse(new
Responses\ForwardResponse($request));}}}}namespace NetteX\Diagnostics{use
NetteX;class
Panel
extends
NetteX\Object
implements
IPanel{private$id;private$tabCb;private$panelCb;public$data;function
__construct($id,$tabCb,$panelCb){$this->id=$id;$this->tabCb=$tabCb;$this->panelCb=$panelCb;}function
getId(){return$this->id;}function
getTab(){ob_start();call_user_func($this->tabCb,$this->id,$this->data);return
ob_get_clean();}function
getPanel(){ob_start();call_user_func($this->panelCb,$this->id,$this->data);return
ob_get_clean();}}}namespace NetteX\Application\Diagnostics{use
NetteX;use
NetteX\Application\Routers;use
NetteX\Application\UI\Presenter;use
NetteX\Diagnostics\Debugger;class
RoutingPanel
extends
NetteX\Diagnostics\Panel{private$router;private$httpRequest;private$routers=array();private$request;function
__construct(NetteX\Application\IRouter$router,NetteX\Http\IRequest$httpRequest){$this->router=$router;$this->httpRequest=$httpRequest;parent::__construct('RoutingPanel',array($this,'renderTab'),array($this,'renderPanel'));}function
renderTab(){$this->analyse($this->router);?>
<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJHSURBVDjLlZPNi81hFMc/z7137p1mTCFvNZfGSzLIWNjZKRvFRoqNhRCSYm8xS3+AxRRZ2JAFJWJHSQqTQkbEzYwIM+6Yid/znJfH4prLXShOnb6r8/nWOd8Tcs78bz0/f+KMu50y05nK/wy+uHDylbutqS5extvGcxaWqtoGDA8PZ3dnrs2srQc2Zko41UXLmLdyDW5OfvsUkUgbYGbU63UAQggdmvMzFmzZCgTi7CQmkZwdEaX0JwDgTnGbTCaE0G4zw80omhPI92lcEtkNkdgJCCHwJX7mZvNaB0A14SaYJlwTrpHsTkoFlV1nt2c3x5YYo1/vM9A/gKpxdfwyu/v3teCayKq4JEwT5EB2R6WgYmrs2bYbcUNNUVfEhIfFYy69uci+1fuRX84mkawFSxd/4nVWUopUVIykwlQxRTJBTIDA4Pp1jBZPuNW4wUAPmCqWIn29X1k4f5Ku8g9mpKCkakRLVEs1auVuauVuyqHMo8ejNCe+sWPVTkQKXCMmkeZUmUZjETF1tc6ooly+fgUVw9So1/tRN6YnZji46QghBFKKuAouERNhMlbAHZFE6e7pB+He8MMw+GGI4xtOMf1+lsl3TQ4NHf19BSlaO1DB9BfMHdX0O0iqSgiBbJkjm491hClJbA1LxCURgpPzXwAHhg63necAIi3XngXLcRU0fof8ETMljIyM5LGxMcbHxzvy/6fuXdWgt6+PWncv1e4euqo1ZmabvHs5+jn8yzufO7hiiZmuNpNBM13rbvVSpbrXJE7/BMkHtU9jFIC/AAAAAElFTkSuQmCC"
/><?php if(empty($this->request)):?>no route<?php else:echo$this->request->getPresenterName().':'.(isset($this->request->params[Presenter::ACTION_KEY])?$this->request->params[Presenter::ACTION_KEY]:Presenter::DEFAULT_ACTION).(isset($this->request->params[Presenter::SIGNAL_KEY])?" {$this->request->params[Presenter::SIGNAL_KEY]}!":'');endif?>
<?php }function
renderPanel(){?>
<style>#nette-debug-RoutingDebugger table{font:9pt/1.5 Consolas,monospace}#nette-debug-RoutingDebugger .yes td{color:green}#nette-debug-RoutingDebugger .may td{color:#67F}#nette-debug-RoutingDebugger pre,#nette-debug-RoutingDebugger code{display:inline}</style>

<h1>
<?php if(empty($this->request)):?>
	no route
<?php else:?>
	<?php echo$this->request->getPresenterName().':'.(isset($this->request->params[Presenter::ACTION_KEY])?$this->request->params[Presenter::ACTION_KEY]:Presenter::DEFAULT_ACTION).(isset($this->request->params[Presenter::SIGNAL_KEY])?" {$this->request->params[Presenter::SIGNAL_KEY]}!":'')?>
<?php endif?>
</h1>

<?php if(!empty($this->request)):?>
	<?php $params=$this->request->getParams()?>
	<?php if(empty($params)):?>
		<p>No parameters.</p>

	<?php else:?>
		<table>
		<thead>
		<tr>
			<th>Parameter</th>
			<th>Value</th>
		</tr>
		</thead>
		<tbody>
		<?php unset($params[Presenter::ACTION_KEY],$params[Presenter::SIGNAL_KEY])?>
		<?php foreach($params
as$key=>$value):?>
		<tr>
			<td><code><?php echo
htmlSpecialChars($key)?></code></td>
			<td><?php if(is_string($value)):?><code><?php echo
htmlSpecialChars($value)?></code><?php else:echo
Debugger::dump($value,TRUE);endif?></td>
		</tr>
		<?php endforeach?>
		</tbody>
		</table>
	<?php endif?>
<?php endif?>

<h2>Routers</h2>

<?php if(empty($this->routers)):?>
	<p>No routers defined.</p>

<?php else:?>
	<div class="nette-inner">
	<table>
	<thead>
	<tr>
		<th>Matched?</th>
		<th>Class</th>
		<th>Mask</th>
		<th>Defaults</th>
		<th>Request</th>
	</tr>
	</thead>

	<tbody>
	<?php foreach($this->routers
as$router):?>
	<tr class="<?php echo$router['matched']?>">
		<td><?php echo$router['matched']?></td>

		<td><code title="<?php echo
htmlSpecialChars($router['class'])?>"><?php echo
preg_replace('#.+\\\\#','',htmlSpecialChars($router['class']))?></code></td>

		<td><code><strong><?php echo
htmlSpecialChars($router['mask'])?></strong></code></td>

		<td><code>
		<?php foreach($router['defaults']as$key=>$value):?>
			<?php echo
htmlSpecialChars($key),"&nbsp;=&nbsp;",is_string($value)?htmlSpecialChars($value):str_replace("\n</pre",'</pre',Debugger::dump($value,TRUE))?><br />
		<?php endforeach?>
		</code></td>

		<td><?php if($router['request']):?><code>
		<?php $params=$router['request']->getParams();?>
		<strong><?php echo
htmlSpecialChars($router['request']->getPresenterName().':'.(isset($params[Presenter::ACTION_KEY])?$params[Presenter::ACTION_KEY]:Presenter::DEFAULT_ACTION))?></strong><br />
		<?php unset($params[Presenter::ACTION_KEY])?>
		<?php foreach($params
as$key=>$value):?>
			<?php echo
htmlSpecialChars($key),"&nbsp;=&nbsp;",is_string($value)?htmlSpecialChars($value):str_replace("\n</pre",'</pre',Debugger::dump($value,TRUE))?><br />
		<?php endforeach?>
		</code><?php endif?></td>
	</tr>
	<?php endforeach?>
	</tbody>
	</table>
	</div>
<?php endif?>
<?php }private
function
analyse($router){if($router
instanceof
Routers\RouteList){foreach($router
as$subRouter){$this->analyse($subRouter);}return;}$request=$router->match($this->httpRequest);$matched=$request===NULL?'no':'may';if($request!==NULL&&empty($this->request)){$this->request=$request;$matched='yes';}$this->routers[]=array('matched'=>$matched,'class'=>get_class($router),'defaults'=>$router
instanceof
Routers\Route||$router
instanceof
Routers\SimpleRouter?$router->getDefaults():array(),'mask'=>$router
instanceof
Routers\Route?$router->getMask():NULL,'request'=>$request);}}}namespace NetteX\Application{use
NetteX;class
AbortException
extends\Exception{}class
ApplicationException
extends\Exception{}class
InvalidPresenterException
extends\Exception{}class
BadRequestException
extends\Exception{protected$defaultCode=404;function
__construct($message='',$code=0,\Exception$previous=NULL){if($code<200||$code>504){$code=$this->defaultCode;}{parent::__construct($message,$code,$previous);}}}class
ForbiddenRequestException
extends
BadRequestException{protected$defaultCode=403;}class
PresenterFactory
implements
IPresenterFactory{public$caseSensitive=FALSE;private$baseDir;private$cache=array();private$context;function
__construct($baseDir,NetteX\DI\IContext$context){$this->baseDir=$baseDir;$this->context=$context;}function
createPresenter($name){$class=$this->getPresenterClass($name);$presenter=new$class;$presenter->setContext($this->context);return$presenter;}function
getPresenterClass(&$name){if(isset($this->cache[$name])){list($class,$name)=$this->cache[$name];return$class;}if(!is_string($name)||!NetteX\StringUtils::match($name,"#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*$#")){throw
new
InvalidPresenterException("Presenter name must be alphanumeric string, '$name' is invalid.");}$class=$this->formatPresenterClass($name);if(!class_exists($class)){$file=$this->formatPresenterFile($name);if(is_file($file)&&is_readable($file)){NetteX\Utils\LimitedScope::load($file);}if(!class_exists($class)){throw
new
InvalidPresenterException("Cannot load presenter '$name', class '$class' was not found in '$file'.");}}$reflection=new
NetteX\Reflection\ClassType($class);$class=$reflection->getName();if(!$reflection->implementsInterface('NetteX\Application\IPresenter')){throw
new
InvalidPresenterException("Cannot load presenter '$name', class '$class' is not NetteX\\Application\\IPresenter implementor.");}if($reflection->isAbstract()){throw
new
InvalidPresenterException("Cannot load presenter '$name', class '$class' is abstract.");}$realName=$this->unformatPresenterClass($class);if($name!==$realName){if($this->caseSensitive){throw
new
InvalidPresenterException("Cannot load presenter '$name', case mismatch. Real name is '$realName'.");}else{$this->cache[$name]=array($class,$realName);$name=$realName;}}else{$this->cache[$name]=array($class,$realName);}return$class;}function
formatPresenterClass($presenter){return
str_replace(':','Module\\',$presenter).'Presenter';}function
unformatPresenterClass($class){return
str_replace('Module\\',':',substr($class,0,-9));}function
formatPresenterFile($presenter){$path='/'.str_replace(':','Module/',$presenter);return$this->baseDir.substr_replace($path,'/presenters',strrpos($path,'/'),0).'Presenter.php';}}}namespace NetteX{use
NetteX;abstract
class
FreezableObject
extends
Object
implements
IFreezable{private$frozen=FALSE;function
freeze(){$this->frozen=TRUE;}final
function
isFrozen(){return$this->frozen;}function
__clone(){$this->frozen=FALSE;}protected
function
updating(){if($this->frozen){throw
new
InvalidStateException("Cannot modify a frozen object {$this->reflection->name}.");}}}}namespace NetteX\Application{use
NetteX;final
class
Request
extends
NetteX\FreezableObject{const
FORWARD='FORWARD';const
SECURED='secured';const
RESTORED='restored';private$method;private$flags=array();private$name;private$params;private$post;private$files;function
__construct($name,$method,array$params,array$post=array(),array$files=array(),array$flags=array()){$this->name=$name;$this->method=$method;$this->params=$params;$this->post=$post;$this->files=$files;$this->flags=$flags;}function
setPresenterName($name){$this->updating();$this->name=$name;return$this;}function
getPresenterName(){return$this->name;}function
setParams(array$params){$this->updating();$this->params=$params;return$this;}function
getParams(){return$this->params;}function
setPost(array$params){$this->updating();$this->post=$params;return$this;}function
getPost(){return$this->post;}function
setFiles(array$files){$this->updating();$this->files=$files;return$this;}function
getFiles(){return$this->files;}function
setMethod($method){$this->method=$method;return$this;}function
getMethod(){return$this->method;}function
isMethod($method){return
strcasecmp($this->method,$method)===0;}function
isPost(){return
strcasecmp($this->method,'post')===0;}function
setFlag($flag,$value=TRUE){$this->updating();$this->flags[$flag]=(bool)$value;return$this;}function
hasFlag($flag){return!empty($this->flags[$flag]);}}}namespace NetteX\Application\Responses{use
NetteX;class
FileResponse
extends
NetteX\Object
implements
NetteX\Application\IResponse{private$file;private$contentType;private$name;public$resuming=TRUE;function
__construct($file,$name=NULL,$contentType=NULL){if(!is_file($file)){throw
new
NetteX\Application\BadRequestException("File '$file' doesn't exist.");}$this->file=$file;$this->name=$name?$name:basename($file);$this->contentType=$contentType?$contentType:'application/octet-stream';}final
function
getFile(){return$this->file;}final
function
getName(){return$this->name;}final
function
getContentType(){return$this->contentType;}function
send(NetteX\Http\IRequest$httpRequest,NetteX\Http\IResponse$httpResponse){$httpResponse->setContentType($this->contentType);$httpResponse->setHeader('Content-Disposition','attachment; filename="'.$this->name.'"');$filesize=$length=filesize($this->file);$handle=fopen($this->file,'r');if($this->resuming){$httpResponse->setHeader('Accept-Ranges','bytes');$range=$httpRequest->getHeader('Range');if($range!==NULL){$range=substr($range,6);list($start,$end)=explode('-',$range);if($start==NULL){$start=0;}if($end==NULL){$end=$filesize-1;}if($start<0||$end<=$start||$end>$filesize-1){$httpResponse->setCode(416);return;}$httpResponse->setCode(206);$httpResponse->setHeader('Content-Range','bytes '.$start.'-'.$end.'/'.$filesize);$length=$end-$start+1;fseek($handle,$start);}else{$httpResponse->setHeader('Content-Range','bytes 0-'.($filesize-1).'/'.$filesize);}}$httpResponse->setHeader('Content-Length',$length);while(!feof($handle)){echo
fread($handle,4e6);}fclose($handle);}}class
ForwardResponse
extends
NetteX\Object
implements
NetteX\Application\IResponse{private$request;function
__construct(NetteX\Application\Request$request){$this->request=$request;}final
function
getRequest(){return$this->request;}function
send(NetteX\Http\IRequest$httpRequest,NetteX\Http\IResponse$httpResponse){}}class
JsonResponse
extends
NetteX\Object
implements
NetteX\Application\IResponse{private$payload;private$contentType;function
__construct($payload,$contentType=NULL){if(!is_array($payload)&&!is_object($payload)){throw
new
NetteX\InvalidArgumentException("Payload must be array or object class, ".gettype($payload)." given.");}$this->payload=$payload;$this->contentType=$contentType?$contentType:'application/json';}final
function
getPayload(){return$this->payload;}final
function
getContentType(){return$this->contentType;}function
send(NetteX\Http\IRequest$httpRequest,NetteX\Http\IResponse$httpResponse){$httpResponse->setContentType($this->contentType);$httpResponse->setExpiration(FALSE);echo
NetteX\Utils\Json::encode($this->payload);}}use
NetteX\Http;class
RedirectResponse
extends
NetteX\Object
implements
NetteX\Application\IResponse{private$uri;private$code;function
__construct($uri,$code=Http\IResponse::S302_FOUND){$this->uri=(string)$uri;$this->code=(int)$code;}final
function
getUri(){return$this->uri;}final
function
getCode(){return$this->code;}function
send(Http\IRequest$httpRequest,Http\IResponse$httpResponse){$httpResponse->redirect($this->uri,$this->code);}}class
TextResponse
extends
NetteX\Object
implements
NetteX\Application\IResponse{private$source;function
__construct($source){$this->source=$source;}final
function
getSource(){return$this->source;}function
send(NetteX\Http\IRequest$httpRequest,NetteX\Http\IResponse$httpResponse){if($this->source
instanceof
NetteX\Templating\ITemplate){$this->source->render();}else{echo$this->source;}}}}namespace NetteX\Application\Routers{use
NetteX;use
NetteX\Application;class
CliRouter
extends
NetteX\Object
implements
Application\IRouter{const
PRESENTER_KEY='action';private$defaults;function
__construct($defaults=array()){$this->defaults=$defaults;}function
match(NetteX\Http\IRequest$httpRequest){if(empty($_SERVER['argv'])||!is_array($_SERVER['argv'])){return
NULL;}$names=array(self::PRESENTER_KEY);$params=$this->defaults;$args=$_SERVER['argv'];array_shift($args);$args[]='--';foreach($args
as$arg){$opt=preg_replace('#/|-+#A','',$arg);if($opt===$arg){if(isset($flag)||$flag=array_shift($names)){$params[$flag]=$arg;}else{$params[]=$arg;}$flag=NULL;continue;}if(isset($flag)){$params[$flag]=TRUE;$flag=NULL;}if($opt!==''){$pair=explode('=',$opt,2);if(isset($pair[1])){$params[$pair[0]]=$pair[1];}else{$flag=$pair[0];}}}if(!isset($params[self::PRESENTER_KEY])){throw
new
NetteX\InvalidStateException('Missing presenter & action in route definition.');}$presenter=$params[self::PRESENTER_KEY];if($a=strrpos($presenter,':')){$params[self::PRESENTER_KEY]=substr($presenter,$a+1);$presenter=substr($presenter,0,$a);}return
new
Application\Request($presenter,'CLI',$params);}function
constructUrl(Application\Request$appRequest,NetteX\Http\Url$refUri){return
NULL;}function
getDefaults(){return$this->defaults;}}use
NetteX\StringUtils;class
Route
extends
NetteX\Object
implements
Application\IRouter{const
PRESENTER_KEY='presenter';const
MODULE_KEY='module';const
CASE_SENSITIVE=256;const
HOST=1,PATH=2,RELATIVE=3;const
VALUE='value';const
PATTERN='pattern';const
FILTER_IN='filterIn';const
FILTER_OUT='filterOut';const
FILTER_TABLE='filterTable';const
OPTIONAL=0,PATH_OPTIONAL=1,CONSTANT=2;public
static$defaultFlags=0;public
static$styles=array('#'=>array(self::PATTERN=>'[^/]+',self::FILTER_IN=>'rawurldecode',self::FILTER_OUT=>'rawurlencode'),'?#'=>array(),'module'=>array(self::PATTERN=>'[a-z][a-z0-9.-]*',self::FILTER_IN=>array(__CLASS__,'path2presenter'),self::FILTER_OUT=>array(__CLASS__,'presenter2path')),'presenter'=>array(self::PATTERN=>'[a-z][a-z0-9.-]*',self::FILTER_IN=>array(__CLASS__,'path2presenter'),self::FILTER_OUT=>array(__CLASS__,'presenter2path')),'action'=>array(self::PATTERN=>'[a-z][a-z0-9-]*',self::FILTER_IN=>array(__CLASS__,'path2action'),self::FILTER_OUT=>array(__CLASS__,'action2path')),'?module'=>array(),'?presenter'=>array(),'?action'=>array());private$mask;private$sequence;private$re;private$metadata=array();private$xlat;private$type;private$flags;function
__construct($mask,$metadata=array(),$flags=0){if(is_string($metadata)){$a=strrpos($metadata,':');if(!$a){throw
new
NetteX\InvalidArgumentException("Second argument must be array or string in format Presenter:action, '$metadata' given.");}$metadata=array(self::PRESENTER_KEY=>substr($metadata,0,$a),'action'=>$a===strlen($metadata)-1?Application\UI\Presenter::DEFAULT_ACTION:substr($metadata,$a+1));}$this->flags=$flags|self::$defaultFlags;$this->setMask($mask,$metadata);}function
match(NetteX\Http\IRequest$httpRequest){$uri=$httpRequest->getUri();if($this->type===self::HOST){$path='//'.$uri->getHost().$uri->getPath();}elseif($this->type===self::RELATIVE){$basePath=$uri->getBasePath();if(strncmp($uri->getPath(),$basePath,strlen($basePath))!==0){return
NULL;}$path=(string)substr($uri->getPath(),strlen($basePath));}else{$path=$uri->getPath();}if($path!==''){$path=rtrim($path,'/').'/';}if(!$matches=StringUtils::match($path,$this->re)){return
NULL;}$params=array();foreach($matches
as$k=>$v){if(is_string($k)&&$v!==''){$params[str_replace('___','-',$k)]=$v;}}foreach($this->metadata
as$name=>$meta){if(isset($params[$name])){}elseif(isset($meta['fixity'])&&$meta['fixity']!==self::OPTIONAL){$params[$name]=NULL;}}if($this->xlat){$params+=self::renameKeys($httpRequest->getQuery(),array_flip($this->xlat));}else{$params+=$httpRequest->getQuery();}foreach($this->metadata
as$name=>$meta){if(isset($params[$name])){if(!is_scalar($params[$name])){}elseif(isset($meta[self::FILTER_TABLE][$params[$name]])){$params[$name]=$meta[self::FILTER_TABLE][$params[$name]];}elseif(isset($meta[self::FILTER_IN])){$params[$name]=call_user_func($meta[self::FILTER_IN],(string)$params[$name]);if($params[$name]===NULL&&!isset($meta['fixity'])){return
NULL;}}}elseif(isset($meta['fixity'])){$params[$name]=$meta[self::VALUE];}}if(!isset($params[self::PRESENTER_KEY])){throw
new
NetteX\InvalidStateException('Missing presenter in route definition.');}if(isset($this->metadata[self::MODULE_KEY])){if(!isset($params[self::MODULE_KEY])){throw
new
NetteX\InvalidStateException('Missing module in route definition.');}$presenter=$params[self::MODULE_KEY].':'.$params[self::PRESENTER_KEY];unset($params[self::MODULE_KEY],$params[self::PRESENTER_KEY]);}else{$presenter=$params[self::PRESENTER_KEY];unset($params[self::PRESENTER_KEY]);}return
new
Application\Request($presenter,$httpRequest->getMethod(),$params,$httpRequest->getPost(),$httpRequest->getFiles(),array(Application\Request::SECURED=>$httpRequest->isSecured()));}function
constructUrl(Application\Request$appRequest,NetteX\Http\Url$refUri){if($this->flags&self::ONE_WAY){return
NULL;}$params=$appRequest->getParams();$metadata=$this->metadata;$presenter=$appRequest->getPresenterName();$params[self::PRESENTER_KEY]=$presenter;if(isset($metadata[self::MODULE_KEY])){$module=$metadata[self::MODULE_KEY];if(isset($module['fixity'])&&strncasecmp($presenter,$module[self::VALUE].':',strlen($module[self::VALUE])+1)===0){$a=strlen($module[self::VALUE]);}else{$a=strrpos($presenter,':');}if($a===FALSE){$params[self::MODULE_KEY]='';}else{$params[self::MODULE_KEY]=substr($presenter,0,$a);$params[self::PRESENTER_KEY]=substr($presenter,$a+1);}}foreach($metadata
as$name=>$meta){if(!isset($params[$name]))continue;if(isset($meta['fixity'])){if(is_scalar($params[$name])&&strcasecmp($params[$name],$meta[self::VALUE])===0){unset($params[$name]);continue;}elseif($meta['fixity']===self::CONSTANT){return
NULL;}}if(!is_scalar($params[$name])){}elseif(isset($meta['filterTable2'][$params[$name]])){$params[$name]=$meta['filterTable2'][$params[$name]];}elseif(isset($meta[self::FILTER_OUT])){$params[$name]=call_user_func($meta[self::FILTER_OUT],$params[$name]);}if(isset($meta[self::PATTERN])&&!preg_match($meta[self::PATTERN],rawurldecode($params[$name]))){return
NULL;}}$sequence=$this->sequence;$brackets=array();$required=0;$uri='';$i=count($sequence)-1;do{$uri=$sequence[$i].$uri;if($i===0)break;$i--;$name=$sequence[$i];$i--;if($name===']'){$brackets[]=$uri;}elseif($name[0]==='['){$tmp=array_pop($brackets);if($required<count($brackets)+1){if($name!=='[!'){$uri=$tmp;}}else{$required=count($brackets);}}elseif($name[0]==='?'){continue;}elseif(isset($params[$name])&&$params[$name]!=''){$required=count($brackets);$uri=$params[$name].$uri;unset($params[$name]);}elseif(isset($metadata[$name]['fixity'])){$uri=$metadata[$name]['defOut'].$uri;}else{return
NULL;}}while(TRUE);if($this->xlat){$params=self::renameKeys($params,$this->xlat);}$sep=ini_get('arg_separator.input');$query=http_build_query($params,'',$sep?$sep[0]:'&');if($query!='')$uri.='?'.$query;if($this->type===self::RELATIVE){$uri='//'.$refUri->getAuthority().$refUri->getBasePath().$uri;}elseif($this->type===self::PATH){$uri='//'.$refUri->getAuthority().$uri;}if(strpos($uri,'//',2)!==FALSE){return
NULL;}$uri=($this->flags&self::SECURED?'https:':'http:').$uri;return$uri;}private
function
setMask($mask,array$metadata){$this->mask=$mask;if(substr($mask,0,2)==='//'){$this->type=self::HOST;}elseif(substr($mask,0,1)==='/'){$this->type=self::PATH;}else{$this->type=self::RELATIVE;}foreach($metadata
as$name=>$meta){if(!is_array($meta)){$metadata[$name]=array(self::VALUE=>$meta,'fixity'=>self::CONSTANT);}elseif(array_key_exists(self::VALUE,$meta)){$metadata[$name]['fixity']=self::CONSTANT;}}$parts=StringUtils::split($mask,'/<([^>#= ]+)(=[^># ]*)? *([^>#]*)(#?[^>\[\]]*)>|(\[!?|\]|\s*\?.*)/');$this->xlat=array();$i=count($parts)-1;if(isset($parts[$i-1])&&substr(ltrim($parts[$i-1]),0,1)==='?'){$matches=StringUtils::matchAll($parts[$i-1],'/(?:([a-zA-Z0-9_.-]+)=)?<([^># ]+) *([^>#]*)(#?[^>]*)>/');foreach($matches
as$match){list(,$param,$name,$pattern,$class)=$match;if($class!==''){if(!isset(self::$styles[$class])){throw
new
NetteX\InvalidStateException("Parameter '$name' has '$class' flag, but Route::\$styles['$class'] is not set.");}$meta=self::$styles[$class];}elseif(isset(self::$styles['?'.$name])){$meta=self::$styles['?'.$name];}else{$meta=self::$styles['?#'];}if(isset($metadata[$name])){$meta=$metadata[$name]+$meta;}if(array_key_exists(self::VALUE,$meta)){$meta['fixity']=self::OPTIONAL;}unset($meta['pattern']);$meta['filterTable2']=empty($meta[self::FILTER_TABLE])?NULL:array_flip($meta[self::FILTER_TABLE]);$metadata[$name]=$meta;if($param!==''){$this->xlat[$name]=$param;}}$i-=6;}$brackets=0;$re='';$sequence=array();$autoOptional=array(0,0);do{array_unshift($sequence,$parts[$i]);$re=preg_quote($parts[$i],'#').$re;if($i===0)break;$i--;$part=$parts[$i];if($part==='['||$part===']'||$part==='[!'){$brackets+=$part[0]==='['?-1:1;if($brackets<0){throw
new
NetteX\InvalidArgumentException("Unexpected '$part' in mask '$mask'.");}array_unshift($sequence,$part);$re=($part[0]==='['?'(?:':')?').$re;$i-=5;continue;}$class=$parts[$i];$i--;$pattern=trim($parts[$i]);$i--;$default=$parts[$i];$i--;$name=$parts[$i];$i--;array_unshift($sequence,$name);if($name[0]==='?'){$re='(?:'.preg_quote(substr($name,1),'#').'|'.$pattern.')'.$re;$sequence[1]=substr($name,1).$sequence[1];continue;}if(preg_match('#[^a-z0-9_-]#i',$name)){throw
new
NetteX\InvalidArgumentException("Parameter name must be alphanumeric string due to limitations of PCRE, '$name' given.");}if($class!==''){if(!isset(self::$styles[$class])){throw
new
NetteX\InvalidStateException("Parameter '$name' has '$class' flag, but Route::\$styles['$class'] is not set.");}$meta=self::$styles[$class];}elseif(isset(self::$styles[$name])){$meta=self::$styles[$name];}else{$meta=self::$styles['#'];}if(isset($metadata[$name])){$meta=$metadata[$name]+$meta;}if($pattern==''&&isset($meta[self::PATTERN])){$pattern=$meta[self::PATTERN];}if($default!==''){$meta[self::VALUE]=(string)substr($default,1);$meta['fixity']=self::PATH_OPTIONAL;}$meta['filterTable2']=empty($meta[self::FILTER_TABLE])?NULL:array_flip($meta[self::FILTER_TABLE]);if(array_key_exists(self::VALUE,$meta)){if(isset($meta['filterTable2'][$meta[self::VALUE]])){$meta['defOut']=$meta['filterTable2'][$meta[self::VALUE]];}elseif(isset($meta[self::FILTER_OUT])){$meta['defOut']=call_user_func($meta[self::FILTER_OUT],$meta[self::VALUE]);}else{$meta['defOut']=$meta[self::VALUE];}}$meta[self::PATTERN]="#(?:$pattern)$#A".($this->flags&self::CASE_SENSITIVE?'':'iu');$re='(?P<'.str_replace('-','___',$name).'>'.$pattern.')'.$re;if($brackets){if(!isset($meta[self::VALUE])){$meta[self::VALUE]=$meta['defOut']=NULL;}$meta['fixity']=self::PATH_OPTIONAL;}elseif(isset($meta['fixity'])){$re='(?:'.substr_replace($re,')?',strlen($re)-$autoOptional[0],0);array_splice($sequence,count($sequence)-$autoOptional[1],0,array(']',''));array_unshift($sequence,'[','');$meta['fixity']=self::PATH_OPTIONAL;}else{$autoOptional=array(strlen($re),count($sequence));}$metadata[$name]=$meta;}while(TRUE);if($brackets){throw
new
NetteX\InvalidArgumentException("Missing closing ']' in mask '$mask'.");}$this->re='#'.$re.'/?$#A'.($this->flags&self::CASE_SENSITIVE?'':'iu');$this->metadata=$metadata;$this->sequence=$sequence;}function
getMask(){return$this->mask;}function
getDefaults(){$defaults=array();foreach($this->metadata
as$name=>$meta){if(isset($meta['fixity'])){$defaults[$name]=$meta[self::VALUE];}}return$defaults;}function
getTargetPresenter(){if($this->flags&self::ONE_WAY){return
FALSE;}$m=$this->metadata;$module='';if(isset($m[self::MODULE_KEY])){if(isset($m[self::MODULE_KEY]['fixity'])&&$m[self::MODULE_KEY]['fixity']===self::CONSTANT){$module=$m[self::MODULE_KEY][self::VALUE].':';}else{return
NULL;}}if(isset($m[self::PRESENTER_KEY]['fixity'])&&$m[self::PRESENTER_KEY]['fixity']===self::CONSTANT){return$module.$m[self::PRESENTER_KEY][self::VALUE];}return
NULL;}private
static
function
renameKeys($arr,$xlat){if(empty($xlat))return$arr;$res=array();$occupied=array_flip($xlat);foreach($arr
as$k=>$v){if(isset($xlat[$k])){$res[$xlat[$k]]=$v;}elseif(!isset($occupied[$k])){$res[$k]=$v;}}return$res;}private
static
function
action2path($s){$s=preg_replace('#(.)(?=[A-Z])#','$1-',$s);$s=strtolower($s);$s=rawurlencode($s);return$s;}private
static
function
path2action($s){$s=strtolower($s);$s=preg_replace('#-(?=[a-z])#',' ',$s);$s=substr(ucwords('x'.$s),1);$s=str_replace(' ','',$s);return$s;}private
static
function
presenter2path($s){$s=strtr($s,':','.');$s=preg_replace('#([^.])(?=[A-Z])#','$1-',$s);$s=strtolower($s);$s=rawurlencode($s);return$s;}private
static
function
path2presenter($s){$s=strtolower($s);$s=preg_replace('#([.-])(?=[a-z])#','$1 ',$s);$s=ucwords($s);$s=str_replace('. ',':',$s);$s=str_replace('- ','',$s);return$s;}static
function
addStyle($style,$parent='#'){if(isset(self::$styles[$style])){throw
new
NetteX\InvalidArgumentException("Style '$style' already exists.");}if($parent!==NULL){if(!isset(self::$styles[$parent])){throw
new
NetteX\InvalidArgumentException("Parent style '$parent' doesn't exist.");}self::$styles[$style]=self::$styles[$parent];}else{self::$styles[$style]=array();}}static
function
setStyleProperty($style,$key,$value){if(!isset(self::$styles[$style])){throw
new
NetteX\InvalidArgumentException("Style '$style' doesn't exist.");}self::$styles[$style][$key]=$value;}}}namespace NetteX{use
NetteX;class
ArrayList
extends
Object
implements\ArrayAccess,\Countable,\IteratorAggregate{private$list=array();function
getIterator(){return
new\ArrayIterator($this->list);}function
count(){return
count($this->list);}function
offsetSet($index,$value){if($index===NULL){$this->list[]=$value;}elseif($index<0||$index>=count($this->list)){throw
new
NetteX\OutOfRangeException("Offset invalid or out of range");}else{$this->list[(int)$index]=$value;}}function
offsetGet($index){if($index<0||$index>=count($this->list)){throw
new
NetteX\OutOfRangeException("Offset invalid or out of range");}return$this->list[(int)$index];}function
offsetExists($index){return$index>=0&&$index<count($this->list);}function
offsetUnset($index){if($index<0||$index>=count($this->list)){throw
new
NetteX\OutOfRangeException("Offset invalid or out of range");}array_splice($this->list,(int)$index,1);}}}namespace NetteX\Application\Routers{use
NetteX;class
RouteList
extends
NetteX\ArrayList
implements
NetteX\Application\IRouter{private$cachedRoutes;private$module;function
__construct($module=NULL){$this->module=$module?$module.':':'';}function
match(NetteX\Http\IRequest$httpRequest){foreach($this
as$route){$appRequest=$route->match($httpRequest);if($appRequest!==NULL){$appRequest->setPresenterName($this->module.$appRequest->getPresenterName());return$appRequest;}}return
NULL;}function
constructUrl(NetteX\Application\Request$appRequest,NetteX\Http\Url$refUri){if($this->cachedRoutes===NULL){$routes=array();$routes['*']=array();foreach($this
as$route){$presenter=$route
instanceof
Route?$route->getTargetPresenter():NULL;if($presenter===FALSE)continue;if(is_string($presenter)){$presenter=strtolower($presenter);if(!isset($routes[$presenter])){$routes[$presenter]=$routes['*'];}$routes[$presenter][]=$route;}else{foreach($routes
as$id=>$foo){$routes[$id][]=$route;}}}$this->cachedRoutes=$routes;}if($this->module){if(strncasecmp($tmp=$appRequest->getPresenterName(),$this->module,strlen($this->module))===0){$appRequest=clone$appRequest;$appRequest->setPresenterName(substr($tmp,strlen($this->module)));}else{return
NULL;}}$presenter=strtolower($appRequest->getPresenterName());if(!isset($this->cachedRoutes[$presenter]))$presenter='*';foreach($this->cachedRoutes[$presenter]as$route){$uri=$route->constructUrl($appRequest,$refUri);if($uri!==NULL){return$uri;}}return
NULL;}function
offsetSet($index,$route){if(!$route
instanceof
NetteX\Application\IRouter){throw
new
NetteX\InvalidArgumentException("Argument must be IRouter descendant.");}parent::offsetSet($index,$route);}}use
NetteX\Application;class
SimpleRouter
extends
NetteX\Object
implements
Application\IRouter{const
PRESENTER_KEY='presenter';const
MODULE_KEY='module';private$module='';private$defaults;private$flags;function
__construct($defaults=array(),$flags=0){if(is_string($defaults)){$a=strrpos($defaults,':');if(!$a){throw
new
NetteX\InvalidArgumentException("Argument must be array or string in format Presenter:action, '$defaults' given.");}$defaults=array(self::PRESENTER_KEY=>substr($defaults,0,$a),'action'=>$a===strlen($defaults)-1?Application\UI\Presenter::DEFAULT_ACTION:substr($defaults,$a+1));}if(isset($defaults[self::MODULE_KEY])){$this->module=$defaults[self::MODULE_KEY].':';unset($defaults[self::MODULE_KEY]);}$this->defaults=$defaults;$this->flags=$flags;}function
match(NetteX\Http\IRequest$httpRequest){if($httpRequest->getUri()->getPathInfo()!==''){return
NULL;}$params=$httpRequest->getQuery();$params+=$this->defaults;if(!isset($params[self::PRESENTER_KEY])){throw
new
NetteX\InvalidStateException('Missing presenter.');}$presenter=$this->module.$params[self::PRESENTER_KEY];unset($params[self::PRESENTER_KEY]);return
new
Application\Request($presenter,$httpRequest->getMethod(),$params,$httpRequest->getPost(),$httpRequest->getFiles(),array(Application\Request::SECURED=>$httpRequest->isSecured()));}function
constructUrl(Application\Request$appRequest,NetteX\Http\Url$refUri){$params=$appRequest->getParams();$presenter=$appRequest->getPresenterName();if(strncasecmp($presenter,$this->module,strlen($this->module))===0){$params[self::PRESENTER_KEY]=substr($presenter,strlen($this->module));}else{return
NULL;}foreach($this->defaults
as$key=>$value){if(isset($params[$key])&&$params[$key]==$value){unset($params[$key]);}}$uri=($this->flags&self::SECURED?'https://':'http://').$refUri->getAuthority().$refUri->getPath();$sep=ini_get('arg_separator.input');$query=http_build_query($params,'',$sep?$sep[0]:'&');if($query!=''){$uri.='?'.$query;}return$uri;}function
getDefaults(){return$this->defaults;}}}namespace NetteX\Application\UI{use
NetteX;class
BadSignalException
extends
NetteX\Application\BadRequestException{protected$defaultCode=403;}}namespace NetteX\ComponentModel{use
NetteX;abstract
class
Component
extends
NetteX\Object
implements
IComponent{private$parent;private$name;private$monitors=array();function
__construct(IContainer$parent=NULL,$name=NULL){if($parent!==NULL){$parent->addComponent($this,$name);}elseif(is_string($name)){$this->name=$name;}}function
lookup($type,$need=TRUE){if(!isset($this->monitors[$type])){$obj=$this->parent;$path=self::NAME_SEPARATOR.$this->name;$depth=1;while($obj!==NULL){if($obj
instanceof$type)break;$path=self::NAME_SEPARATOR.$obj->getName().$path;$depth++;$obj=$obj->getParent();if($obj===$this)$obj=NULL;}if($obj){$this->monitors[$type]=array($obj,$depth,substr($path,1),FALSE);}else{$this->monitors[$type]=array(NULL,NULL,NULL,FALSE);}}if($need&&$this->monitors[$type][0]===NULL){throw
new
NetteX\InvalidStateException("Component '$this->name' is not attached to '$type'.");}return$this->monitors[$type][0];}function
lookupPath($type,$need=TRUE){$this->lookup($type,$need);return$this->monitors[$type][2];}function
monitor($type){if(empty($this->monitors[$type][3])){if($obj=$this->lookup($type,FALSE)){$this->attached($obj);}$this->monitors[$type][3]=TRUE;}}function
unmonitor($type){unset($this->monitors[$type]);}protected
function
attached($obj){}protected
function
detached($obj){}final
function
getName(){return$this->name;}final
function
getParent(){return$this->parent;}function
setParent(IContainer$parent=NULL,$name=NULL){if($parent===NULL&&$this->parent===NULL&&$name!==NULL){$this->name=$name;return$this;}elseif($parent===$this->parent&&$name===NULL){return$this;}if($this->parent!==NULL&&$parent!==NULL){throw
new
NetteX\InvalidStateException("Component '$this->name' already has a parent.");}if($parent===NULL){$this->refreshMonitors(0);$this->parent=NULL;}else{$this->validateParent($parent);$this->parent=$parent;if($name!==NULL)$this->name=$name;$tmp=array();$this->refreshMonitors(0,$tmp);}return$this;}protected
function
validateParent(IContainer$parent){}private
function
refreshMonitors($depth,&$missing=NULL,&$listeners=array()){if($this
instanceof
IContainer){foreach($this->getComponents()as$component){if($component
instanceof
Component){$component->refreshMonitors($depth+1,$missing,$listeners);}}}if($missing===NULL){foreach($this->monitors
as$type=>$rec){if(isset($rec[1])&&$rec[1]>$depth){if($rec[3]){$this->monitors[$type]=array(NULL,NULL,NULL,TRUE);$listeners[]=array($this,$rec[0]);}else{unset($this->monitors[$type]);}}}}else{foreach($this->monitors
as$type=>$rec){if(isset($rec[0])){continue;}elseif(!$rec[3]){unset($this->monitors[$type]);}elseif(isset($missing[$type])){$this->monitors[$type]=array(NULL,NULL,NULL,TRUE);}else{$this->monitors[$type]=NULL;if($obj=$this->lookup($type,FALSE)){$listeners[]=array($this,$obj);}else{$missing[$type]=TRUE;}$this->monitors[$type][3]=TRUE;}}}if($depth===0){$method=$missing===NULL?'detached':'attached';foreach($listeners
as$item){$item[0]->$method($item[1]);}}}function
__clone(){if($this->parent===NULL){return;}elseif($this->parent
instanceof
Container){$this->parent=$this->parent->_isCloning();if($this->parent===NULL){$this->refreshMonitors(0);}}else{$this->parent=NULL;$this->refreshMonitors(0);}}final
function
__wakeup(){throw
new
NetteX\NotImplementedException;}}class
Container
extends
Component
implements
IContainer{private$components=array();private$cloning;function
addComponent(IComponent$component,$name,$insertBefore=NULL){if($name===NULL){$name=$component->getName();}if(is_int($name)){$name=(string)$name;}elseif(!is_string($name)){throw
new
NetteX\InvalidArgumentException("Component name must be integer or string, ".gettype($name)." given.");}elseif(!preg_match('#^[a-zA-Z0-9_]+$#',$name)){throw
new
NetteX\InvalidArgumentException("Component name must be non-empty alphanumeric string, '$name' given.");}if(isset($this->components[$name])){throw
new
NetteX\InvalidStateException("Component with name '$name' already exists.");}$obj=$this;do{if($obj===$component){throw
new
NetteX\InvalidStateException("Circular reference detected while adding component '$name'.");}$obj=$obj->getParent();}while($obj!==NULL);$this->validateChildComponent($component);try{if(isset($this->components[$insertBefore])){$tmp=array();foreach($this->components
as$k=>$v){if($k===$insertBefore)$tmp[$name]=$component;$tmp[$k]=$v;}$this->components=$tmp;}else{$this->components[$name]=$component;}$component->setParent($this,$name);}catch(\Exception$e){unset($this->components[$name]);throw$e;}}function
removeComponent(IComponent$component){$name=$component->getName();if(!isset($this->components[$name])||$this->components[$name]!==$component){throw
new
NetteX\InvalidArgumentException("Component named '$name' is not located in this container.");}unset($this->components[$name]);$component->setParent(NULL);}final
function
getComponent($name,$need=TRUE){if(is_int($name)){$name=(string)$name;}elseif(!is_string($name)){throw
new
NetteX\InvalidArgumentException("Component name must be integer or string, ".gettype($name)." given.");}else{$a=strpos($name,self::NAME_SEPARATOR);if($a!==FALSE){$ext=(string)substr($name,$a+1);$name=substr($name,0,$a);}if($name===''){throw
new
NetteX\InvalidArgumentException("Component or subcomponent name must not be empty string.");}}if(!isset($this->components[$name])){$component=$this->createComponent($name);if($component
instanceof
IComponent&&$component->getParent()===NULL){$this->addComponent($component,$name);}}if(isset($this->components[$name])){if(!isset($ext)){return$this->components[$name];}elseif($this->components[$name]instanceof
IContainer){return$this->components[$name]->getComponent($ext,$need);}elseif($need){throw
new
NetteX\InvalidArgumentException("Component with name '$name' is not container and cannot have '$ext' component.");}}elseif($need){throw
new
NetteX\InvalidArgumentException("Component with name '$name' does not exist.");}}protected
function
createComponent($name){$ucname=ucfirst($name);$method='createComponent'.$ucname;if($ucname!==$name&&method_exists($this,$method)&&$this->getReflection()->getMethod($method)->getName()===$method){$component=$this->$method($name);if(!$component
instanceof
IComponent&&!isset($this->components[$name])){throw
new
NetteX\UnexpectedValueException("Method {$this->reflection->name}::$method() did not return or create the desired component.");}return$component;}}final
function
getComponents($deep=FALSE,$filterType=NULL){$iterator=new
RecursiveComponentIterator($this->components);if($deep){$deep=$deep>0?\RecursiveIteratorIterator::SELF_FIRST:\RecursiveIteratorIterator::CHILD_FIRST;$iterator=new\RecursiveIteratorIterator($iterator,$deep);}if($filterType){$iterator=new
NetteX\Iterators\InstanceFilter($iterator,$filterType);}return$iterator;}protected
function
validateChildComponent(IComponent$child){}function
__clone(){if($this->components){$oldMyself=reset($this->components)->getParent();$oldMyself->cloning=$this;foreach($this->components
as$name=>$component){$this->components[$name]=clone$component;}$oldMyself->cloning=NULL;}parent::__clone();}function
_isCloning(){return$this->cloning;}}}namespace NetteX\Application\UI{use
NetteX;abstract
class
PresenterComponent
extends
NetteX\ComponentModel\Container
implements
ISignalReceiver,IStatePersistent,\ArrayAccess{protected$params=array();function
__construct(NetteX\ComponentModel\IContainer$parent=NULL,$name=NULL){$this->monitor('NetteX\Application\UI\Presenter');parent::__construct($parent,$name);}function
getPresenter($need=TRUE){return$this->lookup('NetteX\Application\UI\Presenter',$need);}function
getUniqueId(){return$this->lookupPath('NetteX\Application\UI\Presenter',TRUE);}protected
function
attached($presenter){if($presenter
instanceof
Presenter){$this->loadState($presenter->popGlobalParams($this->getUniqueId()));}}protected
function
tryCall($method,array$params){$rc=$this->getReflection();if($rc->hasMethod($method)){$rm=$rc->getMethod($method);if($rm->isPublic()&&!$rm->isAbstract()&&!$rm->isStatic()){$rm->invokeNamedArgs($this,$params);return
TRUE;}}return
FALSE;}static
function
getReflection(){return
new
PresenterComponentReflection(get_called_class());}function
loadState(array$params){foreach($this->getReflection()->getPersistentParams()as$nm=>$meta){if(isset($params[$nm])){if(isset($meta['def'])){if(is_array($params[$nm])&&!is_array($meta['def'])){$params[$nm]=$meta['def'];}else{settype($params[$nm],gettype($meta['def']));}}$this->$nm=&$params[$nm];}}$this->params=$params;}function
saveState(array&$params,$reflection=NULL){$reflection=$reflection===NULL?$this->getReflection():$reflection;foreach($reflection->getPersistentParams()as$nm=>$meta){if(isset($params[$nm])){$val=$params[$nm];}elseif(array_key_exists($nm,$params)){continue;}elseif(!isset($meta['since'])||$this
instanceof$meta['since']){$val=$this->$nm;}else{continue;}if(is_object($val)){throw
new
NetteX\InvalidStateException("Persistent parameter must be scalar or array, {$this->reflection->name}::\$$nm is ".gettype($val));}else{if(isset($meta['def'])){settype($val,gettype($meta['def']));if($val===$meta['def'])$val=NULL;}else{if((string)$val==='')$val=NULL;}$params[$nm]=$val;}}}final
function
getParam($name=NULL,$default=NULL){if(func_num_args()===0){return$this->params;}elseif(isset($this->params[$name])){return$this->params[$name];}else{return$default;}}final
function
getParamId($name){$uid=$this->getUniqueId();return$uid===''?$name:$uid.self::NAME_SEPARATOR.$name;}static
function
getPersistentParams(){$rc=new
NetteX\Reflection\ClassType(get_called_class());$params=array();foreach($rc->getProperties(\ReflectionProperty::IS_PUBLIC)as$rp){if(!$rp->isStatic()&&$rp->hasAnnotation('persistent')){$params[]=$rp->getName();}}return$params;}function
signalReceived($signal){if(!$this->tryCall($this->formatSignalMethod($signal),$this->params)){throw
new
BadSignalException("There is no handler for signal '$signal' in class {$this->reflection->name}.");}}function
formatSignalMethod($signal){return$signal==NULL?NULL:'handle'.$signal;}function
link($destination,$args=array()){if(!is_array($args)){$args=func_get_args();array_shift($args);}try{return$this->getPresenter()->createRequest($this,$destination,$args,'link');}catch(InvalidLinkException$e){return$this->getPresenter()->handleInvalidLink($e);}}function
lazyLink($destination,$args=array()){if(!is_array($args)){$args=func_get_args();array_shift($args);}return
new
Link($this,$destination,$args);}function
isLinkCurrent($destination=NULL,$args=array()){if($destination!==NULL){if(!is_array($args)){$args=func_get_args();array_shift($args);}$this->link($destination,$args);}return$this->getPresenter()->getLastCreatedRequestFlag('current');}function
redirect($code,$destination=NULL,$args=array()){if(!is_numeric($code)){$args=$destination;$destination=$code;$code=NULL;}if(!is_array($args)){$args=func_get_args();if(is_numeric(array_shift($args)))array_shift($args);}$presenter=$this->getPresenter();$presenter->redirectUri($presenter->createRequest($this,$destination,$args,'redirect'),$code);}final
function
offsetSet($name,$component){$this->addComponent($component,$name);}final
function
offsetGet($name){return$this->getComponent($name,TRUE);}final
function
offsetExists($name){return$this->getComponent($name,FALSE)!==NULL;}final
function
offsetUnset($name){$component=$this->getComponent($name,FALSE);if($component!==NULL){$this->removeComponent($component);}}}abstract
class
Control
extends
PresenterComponent
implements
IPartiallyRenderable{private$template;private$invalidSnippets=array();final
function
getTemplate(){if($this->template===NULL){$value=$this->createTemplate();if(!$value
instanceof
NetteX\Templating\ITemplate&&$value!==NULL){$class=get_class($value);throw
new
NetteX\UnexpectedValueException("Object returned by {$this->reflection->name}::createTemplate() must be instance of NetteX\\Templating\\ITemplate, '$class' given.");}$this->template=$value;}return$this->template;}protected
function
createTemplate(){$template=new
NetteX\Templating\FileTemplate;$presenter=$this->getPresenter(FALSE);$template->onPrepareFilters[]=callback($this,'templatePrepareFilters');$template->control=$this;$template->presenter=$presenter;$template->user=NetteX\Environment::getUser();$template->baseUri=rtrim(NetteX\Environment::getVariable('baseUri',NULL),'/');$template->basePath=preg_replace('#https?://[^/]+#A','',$template->baseUri);if($presenter!==NULL&&$presenter->hasFlashSession()){$id=$this->getParamId('flash');$template->flashes=$presenter->getFlashSession()->$id;}if(!isset($template->flashes)||!is_array($template->flashes)){$template->flashes=array();}$template->registerHelper('escape','NetteX\Templating\DefaultHelpers::escapeHtml');$template->registerHelper('escapeUrl','rawurlencode');$template->registerHelper('stripTags','strip_tags');$template->registerHelper('nl2br','nl2br');$template->registerHelper('substr','iconv_substr');$template->registerHelper('repeat','str_repeat');$template->registerHelper('replaceRE','NetteX\StringUtils::replace');$template->registerHelper('implode','implode');$template->registerHelper('number','number_format');$template->registerHelperLoader('NetteX\Templating\DefaultHelpers::loader');return$template;}function
templatePrepareFilters($template){$template->registerFilter(new
NetteX\Latte\Engine);}function
getWidget($name){return$this->getComponent($name);}function
flashMessage($message,$type='info'){$id=$this->getParamId('flash');$messages=$this->getPresenter()->getFlashSession()->$id;$messages[]=$flash=(object)array('message'=>$message,'type'=>$type);$this->getTemplate()->flashes=$messages;$this->getPresenter()->getFlashSession()->$id=$messages;return$flash;}function
invalidateControl($snippet=NULL){$this->invalidSnippets[$snippet]=TRUE;}function
validateControl($snippet=NULL){if($snippet===NULL){$this->invalidSnippets=array();}else{unset($this->invalidSnippets[$snippet]);}}function
isControlInvalid($snippet=NULL){if($snippet===NULL){if(count($this->invalidSnippets)>0){return
TRUE;}else{foreach($this->getComponents()as$component){if($component
instanceof
IRenderable&&$component->isControlInvalid()){return
TRUE;}}return
FALSE;}}else{return
isset($this->invalidSnippets[NULL])||isset($this->invalidSnippets[$snippet]);}}function
getSnippetId($name=NULL){return'snippet-'.$this->getUniqueId().'-'.$name;}}}namespace NetteX\Forms{use
NetteX;class
Container
extends
NetteX\ComponentModel\Container
implements\ArrayAccess{public$onValidate;protected$currentGroup;protected$valid;function
setDefaults($values,$erase=FALSE){$form=$this->getForm(FALSE);if(!$form||!$form->isAnchored()||!$form->isSubmitted()){$this->setValues($values,$erase);}return$this;}function
setValues($values,$erase=FALSE){if($values
instanceof\Traversable){$values=iterator_to_array($values);}elseif(!is_array($values)){throw
new
NetteX\InvalidArgumentException("First parameter must be an array, ".gettype($values)." given.");}foreach($this->getComponents()as$name=>$control){if($control
instanceof
IControl){if(array_key_exists($name,$values)){$control->setValue($values[$name]);}elseif($erase){$control->setValue(NULL);}}elseif($control
instanceof
Container){if(array_key_exists($name,$values)){$control->setValues($values[$name],$erase);}elseif($erase){$control->setValues(array(),$erase);}}}return$this;}function
getValues(){$values=new
NetteX\ArrayHash;foreach($this->getComponents()as$name=>$control){if($control
instanceof
IControl&&!$control->isDisabled()&&!$control
instanceof
ISubmitterControl){$values->$name=$control->getValue();}elseif($control
instanceof
Container){$values->$name=$control->getValues();}}return$values;}function
isValid(){if($this->valid===NULL){$this->validate();}return$this->valid;}function
validate(){$this->valid=TRUE;$this->onValidate($this);foreach($this->getControls()as$control){if(!$control->getRules()->validate()){$this->valid=FALSE;}}}function
setCurrentGroup(ControlGroup$group=NULL){$this->currentGroup=$group;return$this;}function
getCurrentGroup(){return$this->currentGroup;}function
addComponent(NetteX\ComponentModel\IComponent$component,$name,$insertBefore=NULL){parent::addComponent($component,$name,$insertBefore);if($this->currentGroup!==NULL&&$component
instanceof
IControl){$this->currentGroup->add($component);}}function
getControls(){return$this->getComponents(TRUE,'NetteX\Forms\IControl');}function
getForm($need=TRUE){return$this->lookup('NetteX\Forms\Form',$need);}function
addText($name,$label=NULL,$cols=NULL,$maxLength=NULL){return$this[$name]=new
Controls\TextInput($label,$cols,$maxLength);}function
addPassword($name,$label=NULL,$cols=NULL,$maxLength=NULL){$control=new
Controls\TextInput($label,$cols,$maxLength);$control->setType('password');return$this[$name]=$control;}function
addTextArea($name,$label=NULL,$cols=40,$rows=10){return$this[$name]=new
Controls\TextArea($label,$cols,$rows);}function
addFile($name,$label=NULL){return$this[$name]=new
Controls\UploadControl($label);}function
addHidden($name,$default=NULL){$control=new
Controls\HiddenField;$control->setDefaultValue($default);return$this[$name]=$control;}function
addCheckbox($name,$caption=NULL){return$this[$name]=new
Controls\Checkbox($caption);}function
addRadioList($name,$label=NULL,array$items=NULL){return$this[$name]=new
Controls\RadioList($label,$items);}function
addSelect($name,$label=NULL,array$items=NULL,$size=NULL){return$this[$name]=new
Controls\SelectBox($label,$items,$size);}function
addMultiSelect($name,$label=NULL,array$items=NULL,$size=NULL){return$this[$name]=new
Controls\MultiSelectBox($label,$items,$size);}function
addSubmit($name,$caption=NULL){return$this[$name]=new
Controls\SubmitButton($caption);}function
addButton($name,$caption){return$this[$name]=new
Controls\Button($caption);}function
addImage($name,$src=NULL,$alt=NULL){return$this[$name]=new
Controls\ImageButton($src,$alt);}function
addContainer($name){$control=new
Container;$control->currentGroup=$this->currentGroup;return$this[$name]=$control;}final
function
offsetSet($name,$component){$this->addComponent($component,$name);}final
function
offsetGet($name){return$this->getComponent($name,TRUE);}final
function
offsetExists($name){return$this->getComponent($name,FALSE)!==NULL;}final
function
offsetUnset($name){$component=$this->getComponent($name,FALSE);if($component!==NULL){$this->removeComponent($component);}}final
function
__clone(){throw
new
NetteX\NotImplementedException('Form cloning is not supported yet.');}}class
Form
extends
Container{const
EQUAL=':equal',IS_IN=':equal',FILLED=':filled',VALID=':valid';const
PROTECTION='NetteX\Forms\Controls\HiddenField::validateEqual';const
SUBMITTED=':submitted';const
MIN_LENGTH=':minLength',MAX_LENGTH=':maxLength',LENGTH=':length',EMAIL=':email',URL=':url',REGEXP=':regexp',PATTERN=':pattern',INTEGER=':integer',NUMERIC=':integer',FLOAT=':float',RANGE=':range';const
MAX_FILE_SIZE=':fileSize',MIME_TYPE=':mimeType',IMAGE=':image';const
GET='get',POST='post';const
TRACKER_ID='_form_';const
PROTECTOR_ID='_token_';public$onSubmit;public$onInvalidSubmit;private$submittedBy;private$httpData;private$element;private$renderer;private$translator;private$groups=array();private$errors=array();function
__construct($name=NULL){$this->element=NetteX\Utils\Html::el('form');$this->element->action='';$this->element->method=self::POST;$this->element->id='frm-'.$name;$this->monitor(__CLASS__);if($name!==NULL){$tracker=new
Controls\HiddenField($name);$tracker->unmonitor(__CLASS__);$this[self::TRACKER_ID]=$tracker;}parent::__construct(NULL,$name);}protected
function
attached($obj){if($obj
instanceof
self){throw
new
NetteX\InvalidStateException('Nested forms are forbidden.');}}final
function
getForm($need=TRUE){return$this;}function
setAction($url){$this->element->action=$url;return$this;}function
getAction(){return$this->element->action;}function
setMethod($method){if($this->httpData!==NULL){throw
new
NetteX\InvalidStateException(__METHOD__.'() must be called until the form is empty.');}$this->element->method=strtolower($method);return$this;}function
getMethod(){return$this->element->method;}function
addProtection($message=NULL,$timeout=NULL){$session=$this->getSession()->getNamespace('NetteX.Forms.Form/CSRF');$key="key$timeout";if(isset($session->$key)){$token=$session->$key;}else{$session->$key=$token=NetteX\StringUtils::random();}$session->setExpiration($timeout,$key);$this[self::PROTECTOR_ID]=new
Controls\HiddenField($token);$this[self::PROTECTOR_ID]->addRule(self::PROTECTION,$message,$token);}function
addGroup($caption=NULL,$setAsCurrent=TRUE){$group=new
ControlGroup;$group->setOption('label',$caption);$group->setOption('visual',TRUE);if($setAsCurrent){$this->setCurrentGroup($group);}if(isset($this->groups[$caption])){return$this->groups[]=$group;}else{return$this->groups[$caption]=$group;}}function
removeGroup($name){if(is_string($name)&&isset($this->groups[$name])){$group=$this->groups[$name];}elseif($name
instanceof
ControlGroup&&in_array($name,$this->groups,TRUE)){$group=$name;$name=array_search($group,$this->groups,TRUE);}else{throw
new
NetteX\InvalidArgumentException("Group not found in form '$this->name'");}foreach($group->getControls()as$control){$this->removeComponent($control);}unset($this->groups[$name]);}function
getGroups(){return$this->groups;}function
getGroup($name){return
isset($this->groups[$name])?$this->groups[$name]:NULL;}function
setTranslator(NetteX\Localization\ITranslator$translator=NULL){$this->translator=$translator;return$this;}final
function
getTranslator(){return$this->translator;}function
isAnchored(){return
TRUE;}final
function
isSubmitted(){if($this->submittedBy===NULL){$this->getHttpData();$this->submittedBy=!empty($this->httpData);}return$this->submittedBy;}function
setSubmittedBy(ISubmitterControl$by=NULL){$this->submittedBy=$by===NULL?FALSE:$by;return$this;}final
function
getHttpData(){if($this->httpData===NULL){if(!$this->isAnchored()){throw
new
NetteX\InvalidStateException('Form is not anchored and therefore can not determine whether it was submitted.');}$this->httpData=(array)$this->receiveHttpData();}return$this->httpData;}function
fireEvents(){if(!$this->isSubmitted()){return;}elseif($this->submittedBy
instanceof
ISubmitterControl){if(!$this->submittedBy->getValidationScope()||$this->isValid()){$this->submittedBy->click();$this->onSubmit($this);}else{$this->submittedBy->onInvalidClick($this->submittedBy);$this->onInvalidSubmit($this);}}elseif($this->isValid()){$this->onSubmit($this);}else{$this->onInvalidSubmit($this);}}protected
function
receiveHttpData(){$httpRequest=$this->getHttpRequest();if(strcasecmp($this->getMethod(),$httpRequest->getMethod())){return;}if($httpRequest->isMethod('post')){$data=NetteX\ArrayUtils::mergeTree($httpRequest->getPost(),$httpRequest->getFiles());}else{$data=$httpRequest->getQuery();}if($tracker=$this->getComponent(self::TRACKER_ID,FALSE)){if(!isset($data[self::TRACKER_ID])||$data[self::TRACKER_ID]!==$tracker->getValue()){return;}}return$data;}function
getValues(){$values=parent::getValues();unset($values[self::TRACKER_ID],$values[self::PROTECTOR_ID]);return$values;}function
addError($message){$this->valid=FALSE;if($message!==NULL&&!in_array($message,$this->errors,TRUE)){$this->errors[]=$message;}}function
getErrors(){return$this->errors;}function
hasErrors(){return(bool)$this->getErrors();}function
cleanErrors(){$this->errors=array();$this->valid=NULL;}function
getElementPrototype(){return$this->element;}function
setRenderer(IFormRenderer$renderer){$this->renderer=$renderer;return$this;}final
function
getRenderer(){if($this->renderer===NULL){$this->renderer=new
Rendering\DefaultFormRenderer;}return$this->renderer;}function
render(){$args=func_get_args();array_unshift($args,$this);echo
call_user_func_array(array($this->getRenderer(),'render'),$args);}function
__toString(){try{return$this->getRenderer()->render($this);}catch(\Exception$e){if(func_get_args()&&func_get_arg(0)){throw$e;}else{NetteX\Diagnostics\Debugger::toStringException($e);}}}protected
function
getHttpRequest(){return
NetteX\Environment::getHttpRequest();}protected
function
getSession(){return
NetteX\Environment::getSession();}}}namespace NetteX\Application\UI{use
NetteX;class
Form
extends
NetteX\Forms\Form
implements
ISignalReceiver{function
__construct(NetteX\ComponentModel\IContainer$parent=NULL,$name=NULL){parent::__construct();$this->monitor('NetteX\Application\UI\Presenter');if($parent!==NULL){$parent->addComponent($this,$name);}}function
getPresenter($need=TRUE){return$this->lookup('NetteX\Application\UI\Presenter',$need);}protected
function
attached($presenter){if($presenter
instanceof
Presenter){$name=$this->lookupPath('NetteX\Application\UI\Presenter');if(!isset($this->getElementPrototype()->id)){$this->getElementPrototype()->id='frm-'.$name;}$this->setAction(new
Link($presenter,$name.self::NAME_SEPARATOR.'submit!',array()));if($this->isSubmitted()){foreach($this->getControls()as$control){$control->loadHttpData();}}}parent::attached($presenter);}function
isAnchored(){return(bool)$this->getPresenter(FALSE);}protected
function
receiveHttpData(){$presenter=$this->getPresenter();if(!$presenter->isSignalReceiver($this,'submit')){return;}$isPost=$this->getMethod()===self::POST;$request=$presenter->getRequest();if($request->isMethod('forward')||$request->isMethod('post')!==$isPost){return;}if($isPost){return
NetteX\ArrayUtils::mergeTree($request->getPost(),$request->getFiles());}else{return$request->getParams();}}function
signalReceived($signal){if($signal==='submit'){$this->fireEvents();}else{throw
new
BadSignalException("Missing handler for signal '$signal' in {$this->reflection->name}.");}}}class
InvalidLinkException
extends\Exception{}class
Link
extends
NetteX\Object{private$component;private$destination;private$params;function
__construct(PresenterComponent$component,$destination,array$params){$this->component=$component;$this->destination=$destination;$this->params=$params;}function
getDestination(){return$this->destination;}function
setParam($key,$value){$this->params[$key]=$value;return$this;}function
getParam($key){return
isset($this->params[$key])?$this->params[$key]:NULL;}function
getParams(){return$this->params;}function
__toString(){try{return$this->component->link($this->destination,$this->params);}catch(\Exception$e){NetteX\Diagnostics\Debugger::toStringException($e);}}}use
NetteX\Application;use
NetteX\Application\Responses;use
NetteX\Http;use
NetteX\Reflection;use
NetteX\Environment;abstract
class
Presenter
extends
Control
implements
Application\IPresenter{const
INVALID_LINK_SILENT=1,INVALID_LINK_WARNING=2,INVALID_LINK_EXCEPTION=3;const
SIGNAL_KEY='do',ACTION_KEY='action',FLASH_KEY='_fid',DEFAULT_ACTION='default';public
static$invalidLinkMode;public$onShutdown;private$request;private$response;public$autoCanonicalize=TRUE;public$absoluteUrls=FALSE;private$globalParams;private$globalState;private$globalStateSinces;private$action;private$view;private$layout;private$payload;private$signalReceiver;private$signal;private$ajaxMode;private$startupCheck;private$lastCreatedRequest;private$lastCreatedRequestFlag;private$context;final
function
getRequest(){return$this->request;}final
function
getPresenter($need=TRUE){return$this;}final
function
getUniqueId(){return'';}function
run(Application\Request$request){try{$this->request=$request;$this->payload=(object)NULL;$this->setParent($this->getParent(),$request->getPresenterName());$this->initGlobalParams();$this->startup();if(!$this->startupCheck){$class=$this->reflection->getMethod('startup')->getDeclaringClass()->getName();throw
new
NetteX\InvalidStateException("Method $class::startup() or its descendant doesn't call parent::startup().");}$this->tryCall($this->formatActionMethod($this->getAction()),$this->params);if($this->autoCanonicalize){$this->canonicalize();}if($this->getHttpRequest()->isMethod('head')){$this->terminate();}$this->processSignal();$this->beforeRender();$this->tryCall($this->formatRenderMethod($this->getView()),$this->params);$this->afterRender();$this->saveGlobalState();if($this->isAjax()){$this->payload->state=$this->getGlobalState();}$this->sendTemplate();}catch(Application\AbortException$e){if($this->isAjax())try{$hasPayload=(array)$this->payload;unset($hasPayload['state']);if($this->response
instanceof
Responses\TextResponse&&$this->isControlInvalid()){$this->response->send($this->getHttpRequest(),$this->getHttpResponse());$this->sendPayload();}elseif(!$this->response&&$hasPayload){$this->sendPayload();}}catch(Application\AbortException$e){}if($this->hasFlashSession()){$this->getFlashSession()->setExpiration($this->response
instanceof
Responses\RedirectResponse?'+ 30 seconds':'+ 3 seconds');}$this->onShutdown($this,$this->response);$this->shutdown($this->response);return$this->response;}}protected
function
startup(){$this->startupCheck=TRUE;}protected
function
beforeRender(){}protected
function
afterRender(){}protected
function
shutdown($response){}function
processSignal(){if($this->signal===NULL)return;$component=$this->signalReceiver===''?$this:$this->getComponent($this->signalReceiver,FALSE);if($component===NULL){throw
new
BadSignalException("The signal receiver component '$this->signalReceiver' is not found.");}elseif(!$component
instanceof
ISignalReceiver){throw
new
BadSignalException("The signal receiver component '$this->signalReceiver' is not ISignalReceiver implementor.");}$component->signalReceived($this->signal);$this->signal=NULL;}final
function
getSignal(){return$this->signal===NULL?NULL:array($this->signalReceiver,$this->signal);}final
function
isSignalReceiver($component,$signal=NULL){if($component
instanceof
NetteX\ComponentModel\Component){$component=$component===$this?'':$component->lookupPath(__CLASS__,TRUE);}if($this->signal===NULL){return
FALSE;}elseif($signal===TRUE){return$component===''||strncmp($this->signalReceiver.'-',$component.'-',strlen($component)+1)===0;}elseif($signal===NULL){return$this->signalReceiver===$component;}else{return$this->signalReceiver===$component&&strcasecmp($signal,$this->signal)===0;}}final
function
getAction($fullyQualified=FALSE){return$fullyQualified?':'.$this->getName().':'.$this->action:$this->action;}function
changeAction($action){if(NetteX\StringUtils::match($action,"#^[a-zA-Z0-9][a-zA-Z0-9_\x7f-\xff]*$#")){$this->action=$action;$this->view=$action;}else{throw
new
Application\BadRequestException("Action name '$action' is not alphanumeric string.");}}final
function
getView(){return$this->view;}function
setView($view){$this->view=(string)$view;return$this;}final
function
getLayout(){return$this->layout;}function
setLayout($layout){$this->layout=$layout===FALSE?FALSE:(string)$layout;return$this;}function
sendTemplate(){$template=$this->getTemplate();if(!$template)return;if($template
instanceof
NetteX\Templating\IFileTemplate&&!$template->getFile()){$files=$this->formatTemplateFiles($this->getName(),$this->view);foreach($files
as$file){if(is_file($file)){$template->setFile($file);break;}}if(!$template->getFile()){$file=str_replace(Environment::getVariable('appDir'),"\xE2\x80\xA6",reset($files));throw
new
Application\BadRequestException("Page not found. Missing template '$file'.");}}if($this->layout!==FALSE){$files=$this->formatLayoutTemplateFiles($this->getName(),$this->layout?$this->layout:'layout');foreach($files
as$file){if(is_file($file)){$template->layout=$file;$template->_extends=$file;break;}}if(empty($template->layout)&&$this->layout!==NULL){$file=str_replace(Environment::getVariable('appDir'),"\xE2\x80\xA6",reset($files));throw
new
NetteX\FileNotFoundException("Layout not found. Missing template '$file'.");}}$this->sendResponse(new
Responses\TextResponse($template));}function
formatLayoutTemplateFiles($presenter,$layout){$appDir=Environment::getVariable('appDir');$path='/'.str_replace(':','Module/',$presenter);$pathP=substr_replace($path,'/templates',strrpos($path,'/'),0);$list=array("$appDir$pathP/@$layout.latte","$appDir$pathP.@$layout.latte","$appDir$pathP/@$layout.phtml","$appDir$pathP.@$layout.phtml");while(($path=substr($path,0,strrpos($path,'/')))!==FALSE){$list[]="$appDir$path/templates/@$layout.latte";$list[]="$appDir$path/templates/@$layout.phtml";}return$list;}function
formatTemplateFiles($presenter,$view){$appDir=Environment::getVariable('appDir');$path='/'.str_replace(':','Module/',$presenter);$pathP=substr_replace($path,'/templates',strrpos($path,'/'),0);$path=substr_replace($path,'/templates',strrpos($path,'/'));return
array("$appDir$pathP/$view.latte","$appDir$pathP.$view.latte","$appDir$pathP/$view.phtml","$appDir$pathP.$view.phtml","$appDir$path/@global.$view.phtml");}protected
static
function
formatActionMethod($action){return'action'.$action;}protected
static
function
formatRenderMethod($view){return'render'.$view;}final
function
getPayload(){return$this->payload;}function
isAjax(){if($this->ajaxMode===NULL){$this->ajaxMode=$this->getHttpRequest()->isAjax();}return$this->ajaxMode;}function
sendPayload(){$this->sendResponse(new
Responses\JsonResponse($this->payload));}function
sendResponse(Application\IResponse$response){$this->response=$response;$this->terminate();}function
terminate(){if(func_num_args()!==0){trigger_error(__METHOD__.' is not intended to send a Application\Response; use sendResponse() instead.',E_USER_WARNING);$this->sendResponse(func_get_arg(0));}throw
new
Application\AbortException();}function
forward($destination,$args=array()){if($destination
instanceof
Application\Request){$this->sendResponse(new
Responses\ForwardResponse($destination));}elseif(!is_array($args)){$args=func_get_args();array_shift($args);}$this->createRequest($this,$destination,$args,'forward');$this->sendResponse(new
Responses\ForwardResponse($this->lastCreatedRequest));}function
redirectUri($uri,$code=NULL){if($this->isAjax()){$this->payload->redirect=(string)$uri;$this->sendPayload();}elseif(!$code){$code=$this->getHttpRequest()->isMethod('post')?Http\IResponse::S303_POST_GET:Http\IResponse::S302_FOUND;}$this->sendResponse(new
Responses\RedirectResponse($uri,$code));}function
backlink(){return$this->getAction(TRUE);}function
getLastCreatedRequest(){return$this->lastCreatedRequest;}function
getLastCreatedRequestFlag($flag){return!empty($this->lastCreatedRequestFlag[$flag]);}function
canonicalize(){if(!$this->isAjax()&&($this->request->isMethod('get')||$this->request->isMethod('head'))){$uri=$this->createRequest($this,$this->action,$this->getGlobalState()+$this->request->params,'redirectX');if($uri!==NULL&&!$this->getHttpRequest()->getUri()->isEqual($uri)){$this->sendResponse(new
Responses\RedirectResponse($uri,Http\IResponse::S301_MOVED_PERMANENTLY));}}}function
lastModified($lastModified,$etag=NULL,$expire=NULL){if(!Environment::isProduction()){return;}if($expire!==NULL){$this->getHttpResponse()->setExpiration($expire);}if(!$this->getHttpContext()->isModified($lastModified,$etag)){$this->terminate();}}final
protected
function
createRequest($component,$destination,array$args,$mode){static$presenterFactory,$router,$refUri;if($presenterFactory===NULL){$presenterFactory=$this->getApplication()->getPresenterFactory();$router=$this->getApplication()->getRouter();$refUri=new
Http\Url($this->getHttpRequest()->getUri());$refUri->setPath($this->getHttpRequest()->getUri()->getScriptPath());}$this->lastCreatedRequest=$this->lastCreatedRequestFlag=NULL;$a=strpos($destination,'#');if($a===FALSE){$fragment='';}else{$fragment=substr($destination,$a);$destination=substr($destination,0,$a);}$a=strpos($destination,'?');if($a!==FALSE){parse_str(substr($destination,$a+1),$args);$destination=substr($destination,0,$a);}$a=strpos($destination,'//');if($a===FALSE){$scheme=FALSE;}else{$scheme=substr($destination,0,$a);$destination=substr($destination,$a+2);}if(!$component
instanceof
Presenter||substr($destination,-1)==='!'){$signal=rtrim($destination,'!');$a=strrpos($signal,':');if($a!==FALSE){$component=$component->getComponent(strtr(substr($signal,0,$a),':','-'));$signal=(string)substr($signal,$a+1);}if($signal==NULL){throw
new
InvalidLinkException("Signal must be non-empty string.");}$destination='this';}if($destination==NULL){throw
new
InvalidLinkException("Destination must be non-empty string.");}$current=FALSE;$a=strrpos($destination,':');if($a===FALSE){$action=$destination==='this'?$this->action:$destination;$presenter=$this->getName();$presenterClass=get_class($this);}else{$action=(string)substr($destination,$a+1);if($destination[0]===':'){if($a<2){throw
new
InvalidLinkException("Missing presenter name in '$destination'.");}$presenter=substr($destination,1,$a-1);}else{$presenter=$this->getName();$b=strrpos($presenter,':');if($b===FALSE){$presenter=substr($destination,0,$a);}else{$presenter=substr($presenter,0,$b+1).substr($destination,0,$a);}}$presenterClass=$presenterFactory->getPresenterClass($presenter);}if(isset($signal)){$reflection=new
PresenterComponentReflection(get_class($component));if($signal==='this'){$signal='';if(array_key_exists(0,$args)){throw
new
InvalidLinkException("Unable to pass parameters to 'this!' signal.");}}elseif(strpos($signal,self::NAME_SEPARATOR)===FALSE){$method=$component->formatSignalMethod($signal);if(!$reflection->hasCallableMethod($method)){throw
new
InvalidLinkException("Unknown signal '$signal', missing handler {$reflection->name}::$method()");}if($args){self::argsToParams(get_class($component),$method,$args);}}if($args&&array_intersect_key($args,$reflection->getPersistentParams())){$component->saveState($args);}if($args&&$component!==$this){$prefix=$component->getUniqueId().self::NAME_SEPARATOR;foreach($args
as$key=>$val){unset($args[$key]);$args[$prefix.$key]=$val;}}}if(is_subclass_of($presenterClass,__CLASS__)){if($action===''){$action=self::DEFAULT_ACTION;}$current=($action==='*'||$action===$this->action)&&$presenterClass===get_class($this);$reflection=new
PresenterComponentReflection($presenterClass);if($args||$destination==='this'){$method=$presenterClass::formatActionMethod($action);if(!$reflection->hasCallableMethod($method)){$method=$presenterClass::formatRenderMethod($action);if(!$reflection->hasCallableMethod($method)){$method=NULL;}}if($method===NULL){if(array_key_exists(0,$args)){throw
new
InvalidLinkException("Unable to pass parameters to action '$presenter:$action', missing corresponding method.");}}elseif($destination==='this'){self::argsToParams($presenterClass,$method,$args,$this->params);}else{self::argsToParams($presenterClass,$method,$args);}}if($args&&array_intersect_key($args,$reflection->getPersistentParams())){$this->saveState($args,$reflection);}$globalState=$this->getGlobalState($destination==='this'?NULL:$presenterClass);if($current&&$args){$tmp=$globalState+$this->params;foreach($args
as$key=>$val){if((string)$val!==(isset($tmp[$key])?(string)$tmp[$key]:'')){$current=FALSE;break;}}}$args+=$globalState;}$args[self::ACTION_KEY]=$action;if(!empty($signal)){$args[self::SIGNAL_KEY]=$component->getParamId($signal);$current=$current&&$args[self::SIGNAL_KEY]===$this->getParam(self::SIGNAL_KEY);}if(($mode==='redirect'||$mode==='forward')&&$this->hasFlashSession()){$args[self::FLASH_KEY]=$this->getParam(self::FLASH_KEY);}$this->lastCreatedRequest=new
Application\Request($presenter,Application\Request::FORWARD,$args,array(),array());$this->lastCreatedRequestFlag=array('current'=>$current);if($mode==='forward')return;$uri=$router->constructUrl($this->lastCreatedRequest,$refUri);if($uri===NULL){unset($args[self::ACTION_KEY]);$params=urldecode(http_build_query($args,NULL,', '));throw
new
InvalidLinkException("No route for $presenter:$action($params)");}if($mode==='link'&&$scheme===FALSE&&!$this->absoluteUrls){$hostUri=$refUri->getHostUri();if(strncmp($uri,$hostUri,strlen($hostUri))===0){$uri=substr($uri,strlen($hostUri));}}return$uri.$fragment;}private
static
function
argsToParams($class,$method,&$args,$supplemental=array()){static$cache;$params=&$cache[strtolower($class.':'.$method)];if($params===NULL){$params=Reflection\Method::from($class,$method)->getDefaultParameters();}$i=0;foreach($params
as$name=>$def){if(array_key_exists($i,$args)){$args[$name]=$args[$i];unset($args[$i]);$i++;}elseif(array_key_exists($name,$args)){}elseif(array_key_exists($name,$supplemental)){$args[$name]=$supplemental[$name];}else{continue;}if($def===NULL){if((string)$args[$name]==='')$args[$name]=NULL;}else{settype($args[$name],gettype($def));if($args[$name]===$def)$args[$name]=NULL;}}if(array_key_exists($i,$args)){$method=Reflection\Method::from($class,$method)->getName();throw
new
InvalidLinkException("Passed more parameters than method $class::$method() expects.");}}protected
function
handleInvalidLink($e){if(self::$invalidLinkMode===NULL){self::$invalidLinkMode=Environment::isProduction()?self::INVALID_LINK_SILENT:self::INVALID_LINK_WARNING;}if(self::$invalidLinkMode===self::INVALID_LINK_SILENT){return'#';}elseif(self::$invalidLinkMode===self::INVALID_LINK_WARNING){return'error: '.$e->getMessage();}else{throw$e;}}static
function
getPersistentComponents(){return(array)Reflection\ClassType::from(get_called_class())->getAnnotation('persistent');}private
function
getGlobalState($forClass=NULL){$sinces=&$this->globalStateSinces;if($this->globalState===NULL){$state=array();foreach($this->globalParams
as$id=>$params){$prefix=$id.self::NAME_SEPARATOR;foreach($params
as$key=>$val){$state[$prefix.$key]=$val;}}$this->saveState($state,$forClass?new
PresenterComponentReflection($forClass):NULL);if($sinces===NULL){$sinces=array();foreach($this->getReflection()->getPersistentParams()as$nm=>$meta){$sinces[$nm]=$meta['since'];}}$components=$this->getReflection()->getPersistentComponents();$iterator=$this->getComponents(TRUE,'NetteX\Application\UI\IStatePersistent');foreach($iterator
as$name=>$component){if($iterator->getDepth()===0){$since=isset($components[$name]['since'])?$components[$name]['since']:FALSE;}$prefix=$component->getUniqueId().self::NAME_SEPARATOR;$params=array();$component->saveState($params);foreach($params
as$key=>$val){$state[$prefix.$key]=$val;$sinces[$prefix.$key]=$since;}}}else{$state=$this->globalState;}if($forClass!==NULL){$since=NULL;foreach($state
as$key=>$foo){if(!isset($sinces[$key])){$x=strpos($key,self::NAME_SEPARATOR);$x=$x===FALSE?$key:substr($key,0,$x);$sinces[$key]=isset($sinces[$x])?$sinces[$x]:FALSE;}if($since!==$sinces[$key]){$since=$sinces[$key];$ok=$since&&(is_subclass_of($forClass,$since)||$forClass===$since);}if(!$ok){unset($state[$key]);}}}return$state;}protected
function
saveGlobalState(){foreach($this->globalParams
as$id=>$foo){$this->getComponent($id,FALSE);}$this->globalParams=array();$this->globalState=$this->getGlobalState();}private
function
initGlobalParams(){$this->globalParams=array();$selfParams=array();$params=$this->request->getParams();if($this->isAjax()){$params=$this->request->getPost()+$params;}foreach($params
as$key=>$value){$a=strlen($key)>2?strrpos($key,self::NAME_SEPARATOR,-2):FALSE;if($a===FALSE){$selfParams[$key]=$value;}else{$this->globalParams[substr($key,0,$a)][substr($key,$a+1)]=$value;}}$this->changeAction(isset($selfParams[self::ACTION_KEY])?$selfParams[self::ACTION_KEY]:self::DEFAULT_ACTION);$this->signalReceiver=$this->getUniqueId();if(!empty($selfParams[self::SIGNAL_KEY])){$param=$selfParams[self::SIGNAL_KEY];$pos=strrpos($param,'-');if($pos){$this->signalReceiver=substr($param,0,$pos);$this->signal=substr($param,$pos+1);}else{$this->signalReceiver=$this->getUniqueId();$this->signal=$param;}if($this->signal==NULL){$this->signal=NULL;}}$this->loadState($selfParams);}final
function
popGlobalParams($id){if(isset($this->globalParams[$id])){$res=$this->globalParams[$id];unset($this->globalParams[$id]);return$res;}else{return
array();}}function
hasFlashSession(){return!empty($this->params[self::FLASH_KEY])&&$this->getSession()->hasNamespace('NetteX.Application.Flash/'.$this->params[self::FLASH_KEY]);}function
getFlashSession(){if(empty($this->params[self::FLASH_KEY])){$this->params[self::FLASH_KEY]=NetteX\StringUtils::random(4);}return$this->getSession('NetteX.Application.Flash/'.$this->params[self::FLASH_KEY]);}function
setContext(NetteX\DI\IContext$context){$this->context=$context;return$this;}final
function
getContext(){return$this->context;}protected
function
getHttpRequest(){return$this->context->getService('NetteX\\Web\\IHttpRequest');}protected
function
getHttpResponse(){return$this->context->getService('NetteX\\Web\\IHttpResponse');}protected
function
getHttpContext(){return$this->context->getService('NetteX\\Web\\HttpContext');}function
getApplication(){return$this->context->getService('NetteX\\Application\\Application');}function
getSession($namespace=NULL){$handler=$this->context->getService('NetteX\\Web\\Session');return$namespace===NULL?$handler:$handler->getNamespace($namespace);}function
getUser(){return$this->context->getService('NetteX\\Web\\IUser');}}}namespace NetteX\Reflection{use
NetteX;use
NetteX\ObjectMixin;class
ClassType
extends\ReflectionClass{private
static$extMethods;static
function
from($class){return
new
static($class);}function
__toString(){return'Class '.$this->getName();}function
hasEventProperty($name){if(preg_match('#^on[A-Z]#',$name)&&$this->hasProperty($name)){$rp=$this->getProperty($name);return$rp->isPublic()&&!$rp->isStatic();}return
FALSE;}function
setExtensionMethod($name,$callback){$l=&self::$extMethods[strtolower($name)];$l[strtolower($this->getName())]=callback($callback);$l['']=NULL;return$this;}function
getExtensionMethod($name){$class=strtolower($this->getName());$l=&self::$extMethods[strtolower($name)];if(empty($l)){return
FALSE;}elseif(isset($l[''][$class])){return$l[''][$class];}$cl=$class;do{if(isset($l[$cl])){return$l[''][$class]=$l[$cl];}}while(($cl=strtolower(get_parent_class($cl)))!=='');foreach(class_implements($class)as$cl){$cl=strtolower($cl);if(isset($l[$cl])){return$l[''][$class]=$l[$cl];}}return$l[''][$class]=FALSE;}function
getConstructor(){return($ref=parent::getConstructor())?Method::from($this->getName(),$ref->getName()):NULL;}function
getExtension(){return($name=$this->getExtensionName())?new
Extension($name):NULL;}function
getInterfaces(){$res=array();foreach(parent::getInterfaceNames()as$val){$res[$val]=new
static($val);}return$res;}function
getMethod($name){return
new
Method($this->getName(),$name);}function
getMethods($filter=-1){foreach($res=parent::getMethods($filter)as$key=>$val){$res[$key]=new
Method($this->getName(),$val->getName());}return$res;}function
getParentClass(){return($ref=parent::getParentClass())?new
static($ref->getName()):NULL;}function
getProperties($filter=-1){foreach($res=parent::getProperties($filter)as$key=>$val){$res[$key]=new
Property($this->getName(),$val->getName());}return$res;}function
getProperty($name){return
new
Property($this->getName(),$name);}function
hasAnnotation($name){$res=AnnotationsParser::getAll($this);return!empty($res[$name]);}function
getAnnotation($name){$res=AnnotationsParser::getAll($this);return
isset($res[$name])?end($res[$name]):NULL;}function
getAnnotations(){return
AnnotationsParser::getAll($this);}static
function
getReflection(){return
new
ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}}namespace NetteX\Application\UI{use
NetteX;class
PresenterComponentReflection
extends
NetteX\Reflection\ClassType{private
static$ppCache=array();private
static$pcCache=array();private
static$mcCache=array();function
getPersistentParams($class=NULL){$class=$class===NULL?$this->getName():$class;$params=&self::$ppCache[$class];if($params!==NULL)return$params;$params=array();if(is_subclass_of($class,'NetteX\Application\UI\PresenterComponent')){$defaults=get_class_vars($class);foreach(call_user_func(array($class,'getPersistentParams'),$class)as$name=>$meta){if(is_string($meta))$name=$meta;$params[$name]=array('def'=>$defaults[$name],'since'=>$class);}$params=$this->getPersistentParams(get_parent_class($class))+$params;}return$params;}function
getPersistentComponents(){$class=$this->getName();$components=&self::$pcCache[$class];if($components!==NULL)return$components;$components=array();if(is_subclass_of($class,'NetteX\Application\UI\Presenter')){foreach(call_user_func(array($class,'getPersistentComponents'),$class)as$name=>$meta){if(is_string($meta))$name=$meta;$components[$name]=array('since'=>$class);}$components=self::getPersistentComponents(get_parent_class($class))+$components;}return$components;}function
hasCallableMethod($method){$class=$this->getName();$cache=&self::$mcCache[strtolower($class.':'.$method)];if($cache===NULL)try{$cache=FALSE;$rm=NetteX\Reflection\Method::from($class,$method);$cache=$this->isInstantiable()&&$rm->isPublic()&&!$rm->isAbstract()&&!$rm->isStatic();}catch(\ReflectionException$e){}return$cache;}}}namespace NetteX\Caching{use
NetteX;class
Cache
extends
NetteX\Object
implements\ArrayAccess{const
PRIORITY='priority',EXPIRATION='expire',EXPIRE='expire',SLIDING='sliding',TAGS='tags',FILES='files',ITEMS='items',CONSTS='consts',CALLBACKS='callbacks',ALL='all';const
NAMESPACE_SEPARATOR="\x00";private$storage;private$namespace;private$key;private$data;function
__construct(IStorage$storage,$namespace=NULL){$this->storage=$storage;$this->namespace=$namespace.self::NAMESPACE_SEPARATOR;}function
getStorage(){return$this->storage;}function
getNamespace(){return(string)substr($this->namespace,0,-1);}function
derive($namespace){$derived=new
self($this->storage,$this->namespace.$namespace);return$derived;}function
release(){$this->key=$this->data=NULL;}function
save($key,$data,array$dp=NULL){$this->key=is_scalar($key)?(string)$key:serialize($key);$key=$this->namespace.md5($this->key);if(isset($dp[Cache::EXPIRATION])){$dp[Cache::EXPIRATION]=NetteX\DateTime::from($dp[Cache::EXPIRATION])->format('U')-time();}if(isset($dp[self::FILES])){foreach((array)$dp[self::FILES]as$item){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkFile'),$item,@filemtime($item));}unset($dp[self::FILES]);}if(isset($dp[self::ITEMS])){$dp[self::ITEMS]=(array)$dp[self::ITEMS];foreach($dp[self::ITEMS]as$k=>$item){$dp[self::ITEMS][$k]=$this->namespace.md5(is_scalar($item)?$item:serialize($item));}}if(isset($dp[self::CONSTS])){foreach((array)$dp[self::CONSTS]as$item){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkConst'),$item,constant($item));}unset($dp[self::CONSTS]);}if($data
instanceof
NetteX\Callback||$data
instanceof\Closure){NetteX\Utils\CriticalSection::enter();$data=$data->__invoke();NetteX\Utils\CriticalSection::leave();}if(is_object($data)){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkSerializationVersion'),get_class($data),NetteX\Reflection\ClassType::from($data)->getAnnotation('serializationVersion'));}$this->data=$data;if($data===NULL){$this->storage->remove($key);}else{$this->storage->write($key,$data,(array)$dp);}return$data;}function
clean(array$conds=NULL){$this->release();$this->storage->clean((array)$conds);}function
call($function){$key=func_get_args();if($this->offsetGet($key)===NULL){array_shift($key);return$this->save($this->key,call_user_func_array($function,$key));}else{return$this->data;}}function
offsetSet($key,$data){$this->save($key,$data);}function
offsetGet($key){$key=is_scalar($key)?(string)$key:serialize($key);if($this->key===$key){return$this->data;}$this->key=$key;$this->data=$this->storage->read($this->namespace.md5($key));return$this->data;}function
offsetExists($key){return$this->offsetGet($key)!==NULL;}function
offsetUnset($key){$this->save($key,NULL);}static
function
checkCallbacks($callbacks){foreach($callbacks
as$callback){$func=array_shift($callback);if(!call_user_func_array($func,$callback)){return
FALSE;}}return
TRUE;}private
static
function
checkConst($const,$value){return
defined($const)&&constant($const)===$value;}private
static
function
checkFile($file,$time){return@filemtime($file)==$time;}private
static
function
checkSerializationVersion($class,$value){return
NetteX\Reflection\ClassType::from($class)->getAnnotation('serializationVersion')===$value;}}class
OutputHelper
extends
NetteX\Object{private$frame;private$key;static
function
create($key,&$parents,$args=NULL){if($args){if(array_key_exists('if',$args)&&!$args['if']){return$parents[]=new
self;}$key=array_merge(array($key),array_intersect_key($args,range(0,count($args))));}if($parents){end($parents)->frame[Cache::ITEMS][]=$key;}$cache=self::getCache();if(isset($cache[$key])){echo$cache[$key];return
FALSE;}else{$obj=new
self;$obj->key=$key;$obj->frame=array(Cache::TAGS=>isset($args['tags'])?$args['tags']:NULL,Cache::EXPIRATION=>isset($args['expire'])?$args['expire']:'+ 7 days');ob_start();return$parents[]=$obj;}}function
save(){if($this->key!==NULL){$this->getCache()->save($this->key,ob_get_flush(),$this->frame);}$this->key=$this->frame=NULL;}function
addFile($file){$this->frame[Cache::FILES][]=$file;}protected
static
function
getCache(){return
NetteX\Environment::getCache('NetteX.Template.Cache');}}}namespace NetteX\Caching\Storages{use
NetteX;class
DevNullStorage
extends
NetteX\Object
implements
NetteX\Caching\IStorage{function
read($key){}function
write($key,$data,array$dp){}function
remove($key){}function
clean(array$conds){}}use
NetteX\Caching\Cache;class
FileJournal
extends
NetteX\Object
implements
IJournal{const
FILE='btfj.dat';const
FILE_MAGIC=0x6274666A;const
INDEX_MAGIC=0x696E6465;const
DATA_MAGIC=0x64617461;const
NODE_SIZE=4096;const
BITROT=12;const
HEADER_SIZE=4096;const
INT32_SIZE=4;const
INFO='i',TYPE='t',IS_LEAF='il',PREV_NODE='p',END='e',MAX='m',INDEX_DATA='id',LAST_INDEX='l';const
TAGS='t',PRIORITY='p',ENTRIES='e';const
DATA='d',KEY='k',DELETED='d';public
static$debug=FALSE;private$file;private$handle;private$lastNode=2;private$lastModTime=NULL;private$nodeCache=array();private$nodeChanged=array();private$toCommit=array();private$deletedLinks=array();private$dataNodeFreeSpace=array();private
static$startNode=array(self::TAGS=>0,self::PRIORITY=>1,self::ENTRIES=>2,self::DATA=>3);function
__construct($dir){$this->file=$dir.'/'.self::FILE;if(!file_exists($this->file)){$init=@fopen($this->file,'xb');if(!$init){clearstatcache();if(!file_exists($this->file)){throw
new
NetteX\InvalidStateException("Cannot create journal file $this->file.");}}else{$writen=fwrite($init,pack('N2',self::FILE_MAGIC,$this->lastNode));fclose($init);if($writen!==self::INT32_SIZE*2){throw
new
NetteX\InvalidStateException("Cannot write journal header.");}}}$this->handle=fopen($this->file,'r+b');if(!$this->handle){throw
new
NetteX\InvalidStateException("Cannot open journal file '$this->file'.");}if(!flock($this->handle,LOCK_SH)){throw
new
NetteX\InvalidStateException('Cannot acquire shared lock on journal.');}$header=stream_get_contents($this->handle,2*self::INT32_SIZE,0);flock($this->handle,LOCK_UN);list(,$fileMagic,$this->lastNode)=unpack('N2',$header);if($fileMagic!==self::FILE_MAGIC){fclose($this->handle);$this->handle=false;throw
new
NetteX\InvalidStateException("Malformed journal file '$this->file'.");}}function
__destruct(){if($this->handle){$this->headerCommit();flock($this->handle,LOCK_UN);fclose($this->handle);$this->handle=false;}}function
write($key,array$dependencies){$this->lock();$priority=!isset($dependencies[Cache::PRIORITY])?FALSE:(int)$dependencies[Cache::PRIORITY];$tags=empty($dependencies[Cache::TAGS])?FALSE:(array)$dependencies[Cache::TAGS];$exists=FALSE;$keyHash=crc32($key);list($entriesNodeId,$entriesNode)=$this->findIndexNode(self::ENTRIES,$keyHash);if(isset($entriesNode[$keyHash])){$entries=$this->mergeIndexData($entriesNode[$keyHash]);foreach($entries
as$link=>$foo){$dataNode=$this->getNode($link>>self::BITROT);if($dataNode[$link][self::KEY]===$key){if($dataNode[$link][self::TAGS]==$tags&&$dataNode[$link][self::PRIORITY]===$priority){if($dataNode[$link][self::DELETED]){$dataNode[$link][self::DELETED]=FALSE;$this->saveNode($link>>self::BITROT,$dataNode);}$exists=TRUE;}else{$toDelete=array();foreach($dataNode[$link][self::TAGS]as$tag){$toDelete[self::TAGS][$tag][$link]=TRUE;}if($dataNode[$link][self::PRIORITY]!==FALSE){$toDelete[self::PRIORITY][$dataNode[$link][self::PRIORITY]][$link]=TRUE;}$toDelete[self::ENTRIES][$keyHash][$link]=TRUE;$this->cleanFromIndex($toDelete);$entriesNode=$this->getNode($entriesNodeId);unset($dataNode[$link]);$this->saveNode($link>>self::BITROT,$dataNode);}break;}}}if($exists===FALSE){$requiredSize=strlen($key)+75;if($tags){foreach($tags
as$tag){$requiredSize+=strlen($tag)+13;}}$requiredSize+=$priority?10:1;$freeDataNode=$this->findFreeDataNode($requiredSize);$data=$this->getNode($freeDataNode);if($data===FALSE){$data=array(self::INFO=>array(self::LAST_INDEX=>($freeDataNode<<self::BITROT),self::TYPE=>self::DATA));}$dataNodeKey=++$data[self::INFO][self::LAST_INDEX];$data[$dataNodeKey]=array(self::KEY=>$key,self::TAGS=>$tags?$tags:array(),self::PRIORITY=>$priority,self::DELETED=>FALSE);$this->saveNode($freeDataNode,$data);$entriesNode[$keyHash][$dataNodeKey]=1;$this->saveNode($entriesNodeId,$entriesNode);if($tags){foreach($tags
as$tag){list($nodeId,$node)=$this->findIndexNode(self::TAGS,$tag);$node[$tag][$dataNodeKey]=1;$this->saveNode($nodeId,$node);}}if($priority){list($nodeId,$node)=$this->findIndexNode(self::PRIORITY,$priority);$node[$priority][$dataNodeKey]=1;$this->saveNode($nodeId,$node);}}$this->commit();$this->unlock();}function
clean(array$conditions){$this->lock();if(!empty($conditions[Cache::ALL])){$this->nodeCache=$this->nodeChanged=$this->dataNodeFreeSpace=array();$this->deleteAll();$this->unlock();return;}$toDelete=array(self::TAGS=>array(),self::PRIORITY=>array(),self::ENTRIES=>array());$entries=array();if(!empty($conditions[Cache::TAGS])){$entries=$this->cleanTags((array)$conditions[Cache::TAGS],$toDelete);}if(isset($conditions[Cache::PRIORITY])){$this->arrayAppend($entries,$this->cleanPriority((int)$conditions[Cache::PRIORITY],$toDelete));}$this->deletedLinks=array();$this->cleanFromIndex($toDelete);$this->commit();$this->unlock();return$entries;}private
function
cleanTags(array$tags,array&$toDelete){$entries=array();foreach($tags
as$tag){list($nodeId,$node)=$this->findIndexNode(self::TAGS,$tag);if(isset($node[$tag])){$ent=$this->cleanLinks($this->mergeIndexData($node[$tag]),$toDelete);$this->arrayAppend($entries,$ent);}}return$entries;}private
function
cleanPriority($priority,array&$toDelete){list($nodeId,$node)=$this->findIndexNode(self::PRIORITY,$priority);ksort($node);$allData=array();foreach($node
as$prior=>$data){if($prior===self::INFO){continue;}elseif($prior>$priority){break;}$this->arrayAppendKeys($allData,$this->mergeIndexData($data));}$nodeInfo=$node[self::INFO];while($nodeInfo[self::PREV_NODE]!==-1){$nodeId=$nodeInfo[self::PREV_NODE];$node=$this->getNode($nodeId);if($node===FALSE){if(self::$debug)throw
new
NetteX\InvalidStateException("Cannot load node number $nodeId.");break;}$nodeInfo=$node[self::INFO];unset($node[self::INFO]);foreach($node
as$prior=>$data){$this->arrayAppendKeys($allData,$this->mergeIndexData($data));}}return$this->cleanLinks($allData,$toDelete);}private
function
cleanLinks(array$data,array&$toDelete){$return=array();$data=array_keys($data);sort($data);$max=count($data);$data[]=0;$i=0;while($i<$max){$searchLink=$data[$i];if(isset($this->deletedLinks[$searchLink])){++$i;continue;}$nodeId=$searchLink>>self::BITROT;$node=$this->getNode($nodeId);if($node===FALSE){if(self::$debug)throw
new
NetteX\InvalidStateException('Cannot load node number '.($nodeId).'.');++$i;continue;}do{$link=$data[$i];if(!isset($node[$link])){if(self::$debug)throw
new
NetteX\InvalidStateException("Link with ID $searchLink is not in node ".($nodeId).'.');continue;}elseif(isset($this->deletedLinks[$link])){continue;}$nodeLink=&$node[$link];if(!$nodeLink[self::DELETED]){$nodeLink[self::DELETED]=TRUE;$return[]=$nodeLink[self::KEY];}else{foreach($nodeLink[self::TAGS]as$tag){$toDelete[self::TAGS][$tag][$link]=TRUE;}if($nodeLink[self::PRIORITY]!==FALSE){$toDelete[self::PRIORITY][$nodeLink[self::PRIORITY]][$link]=TRUE;}$toDelete[self::ENTRIES][crc32($nodeLink[self::KEY])][$link]=TRUE;unset($node[$link]);$this->deletedLinks[$link]=TRUE;}}while(($data[++$i]>>self::BITROT)===$nodeId);$this->saveNode($nodeId,$node);}return$return;}private
function
cleanFromIndex(array$toDeleteFromIndex){foreach($toDeleteFromIndex
as$type=>$toDelete){ksort($toDelete);while(!empty($toDelete)){reset($toDelete);$searchKey=key($toDelete);list($masterNodeId,$masterNode)=$this->findIndexNode($type,$searchKey);if(!isset($masterNode[$searchKey])){if(self::$debug)throw
new
NetteX\InvalidStateException('Bad index.');unset($toDelete[$searchKey]);continue;}foreach($toDelete
as$key=>$links){if(isset($masterNode[$key])){foreach($links
as$link=>$foo){if(isset($masterNode[$key][$link])){unset($masterNode[$key][$link],$links[$link]);}}if(!empty($links)&&isset($masterNode[$key][self::INDEX_DATA])){$this->cleanIndexData($masterNode[$key][self::INDEX_DATA],$links,$masterNode[$key]);}if(empty($masterNode[$key])){unset($masterNode[$key]);}unset($toDelete[$key]);}else{break;}}$this->saveNode($masterNodeId,$masterNode);}}}private
function
mergeIndexData(array$data){while(isset($data[self::INDEX_DATA])){$id=$data[self::INDEX_DATA];unset($data[self::INDEX_DATA]);$childNode=$this->getNode($id);if($childNode===FALSE){if(self::$debug)throw
new
NetteX\InvalidStateException("Cannot load node number $id.");break;}$this->arrayAppendKeys($data,$childNode[self::INDEX_DATA]);}return$data;}private
function
cleanIndexData($nextNodeId,array$links,&$masterNodeLink){$prev=-1;while($nextNodeId&&!empty($links)){$nodeId=$nextNodeId;$node=$this->getNode($nodeId);if($node===FALSE){if(self::$debug)throw
new
NetteX\InvalidStateException("Cannot load node number $nodeId.");break;}foreach($links
as$link=>$foo){if(isset($node[self::INDEX_DATA][$link])){unset($node[self::INDEX_DATA][$link],$links[$link]);}}if(isset($node[self::INDEX_DATA][self::INDEX_DATA])){$nextNodeId=$node[self::INDEX_DATA][self::INDEX_DATA];}else{$nextNodeId=FALSE;}if(empty($node[self::INDEX_DATA])||(count($node[self::INDEX_DATA])===1&&$nextNodeId)){if($prev===-1){if($nextNodeId===FALSE){unset($masterNodeLink[self::INDEX_DATA]);}else{$masterNodeLink[self::INDEX_DATA]=$nextNodeId;}}else{$prevNode=$this->getNode($prev);if($prevNode===FALSE){if(self::$debug)throw
new
NetteX\InvalidStateException("Cannot load node number $prev.");}else{if($nextNodeId===FALSE){unset($prevNode[self::INDEX_DATA][self::INDEX_DATA]);if(empty($prevNode[self::INDEX_DATA])){unset($prevNode[self::INDEX_DATA]);}}else{$prevNode[self::INDEX_DATA][self::INDEX_DATA]=$nextNodeId;}$this->saveNode($prev,$prevNode);}}unset($node[self::INDEX_DATA]);}else{$prev=$nodeId;}$this->saveNode($nodeId,$node);}}private
function
getNode($id){if(isset($this->nodeCache[$id])){return$this->nodeCache[$id];}$binary=stream_get_contents($this->handle,self::NODE_SIZE,self::HEADER_SIZE+self::NODE_SIZE*$id);if(empty($binary)){return
FALSE;}list(,$magic,$lenght)=unpack('N2',$binary);if($magic!==self::INDEX_MAGIC&&$magic!==self::DATA_MAGIC){if(!empty($magic)){if(self::$debug)throw
new
NetteX\InvalidStateException("Node $id has malformed header.");$this->deleteNode($id);}return
FALSE;}$data=substr($binary,2*self::INT32_SIZE,$lenght-2*self::INT32_SIZE);$node=@unserialize($data);if($node===FALSE){$this->deleteNode($id);if(self::$debug)throw
new
NetteX\InvalidStateException("Cannot deserialize node number $id.");return
FALSE;}return$this->nodeCache[$id]=$node;}private
function
saveNode($id,array$node){if(count($node)===1){$nodeInfo=$node[self::INFO];if($nodeInfo[self::TYPE]!==self::DATA){if($nodeInfo[self::END]!==-1){$this->nodeCache[$id]=$node;$this->nodeChanged[$id]=TRUE;return;}if($nodeInfo[self::MAX]===-1){$max=PHP_INT_MAX;}else{$max=$nodeInfo[self::MAX];}list(,,$parentId)=$this->findIndexNode($nodeInfo[self::TYPE],$max,$id);if($parentId!==-1&&$parentId!==$id){$parentNode=$this->getNode($parentId);if($parentNode===FALSE){if(self::$debug)throw
new
NetteX\InvalidStateException("Cannot load node number $parentId.");}else{if($parentNode[self::INFO][self::END]===$id){if(count($parentNode)===1){$parentNode[self::INFO][self::END]=-1;}else{end($parentNode);$lastKey=key($parentNode);$parentNode[self::INFO][self::END]=$parentNode[$lastKey];unset($parentNode[$lastKey]);}}else{unset($parentNode[$nodeInfo[self::MAX]]);}$this->saveNode($parentId,$parentNode);}}if($nodeInfo[self::TYPE]===self::PRIORITY){if($nodeInfo[self::MAX]===-1){if($nodeInfo[self::PREV_NODE]!==-1){$prevNode=$this->getNode($nodeInfo[self::PREV_NODE]);if($prevNode===FALSE){if(self::$debug){throw
new
NetteX\InvalidStateException('Cannot load node number '.$nodeInfo[self::PREV_NODE].'.');}}else{$prevNode[self::INFO][self::MAX]=-1;$this->saveNode($nodeInfo[self::PREV_NODE],$prevNode);}}}else{list($nextId,$nextNode)=$this->findIndexNode($nodeInfo[self::TYPE],$nodeInfo[self::MAX]+1,NULL,$id);if($nextId!==-1&&$nextId!==$id){$nextNode[self::INFO][self::PREV_NODE]=$nodeInfo[self::PREV_NODE];$this->saveNode($nextId,$nextNode);}}}}$this->nodeCache[$id]=FALSE;}else{$this->nodeCache[$id]=$node;}$this->nodeChanged[$id]=TRUE;}private
function
commit(){do{foreach($this->nodeChanged
as$id=>$foo){if($this->prepareNode($id,$this->nodeCache[$id])){unset($this->nodeChanged[$id]);}}}while(!empty($this->nodeChanged));foreach($this->toCommit
as$node=>$str){$this->commitNode($node,$str);}$this->toCommit=array();}private
function
prepareNode($id,$node){if($node===FALSE){if($id<$this->lastNode){$this->lastNode=$id;}unset($this->nodeCache[$id]);unset($this->dataNodeFreeSpace[$id]);$this->deleteNode($id);return
TRUE;}$data=serialize($node);$dataSize=strlen($data)+2*self::INT32_SIZE;$isData=$node[self::INFO][self::TYPE]===self::DATA;if($dataSize>self::NODE_SIZE){if($isData){throw
new
NetteX\InvalidStateException('Saving node is bigger than maximum node size.');}else{$this->bisectNode($id,$node);return
FALSE;}}$this->toCommit[$id]=pack('N2',$isData?self::DATA_MAGIC:self::INDEX_MAGIC,$dataSize).$data;if($this->lastNode<$id){$this->lastNode=$id;}if($isData){$this->dataNodeFreeSpace[$id]=self::NODE_SIZE-$dataSize;}return
TRUE;}private
function
commitNode($id,$str){fseek($this->handle,self::HEADER_SIZE+self::NODE_SIZE*$id);$writen=fwrite($this->handle,$str);if($writen===FALSE){throw
new
NetteX\InvalidStateException("Cannot write node number $id to journal.");}}private
function
findIndexNode($type,$search,$childId=NULL,$prevId=NULL){$nodeId=self::$startNode[$type];$parentId=-1;while(TRUE){$node=$this->getNode($nodeId);if($node===FALSE){return
array($nodeId,array(self::INFO=>array(self::TYPE=>$type,self::IS_LEAF=>TRUE,self::PREV_NODE=>-1,self::END=>-1,self::MAX=>-1)),$parentId);}if($node[self::INFO][self::IS_LEAF]||$nodeId===$childId||$node[self::INFO][self::PREV_NODE]===$prevId){return
array($nodeId,$node,$parentId);}$parentId=$nodeId;if(isset($node[$search])){$nodeId=$node[$search];}else{foreach($node
as$key=>$childNode){if($key>$search
and$key!==self::INFO){$nodeId=$childNode;continue
2;}}$nodeId=$node[self::INFO][self::END];}}}private
function
findFreeNode($count=1){$id=$this->lastNode;$nodesId=array();do{if(isset($this->nodeCache[$id])){++$id;continue;}$offset=self::HEADER_SIZE+self::NODE_SIZE*$id;$binary=stream_get_contents($this->handle,self::INT32_SIZE,$offset);if(empty($binary)){$nodesId[]=$id;}else{list(,$magic)=unpack('N',$binary);if($magic!==self::INDEX_MAGIC&&$magic!==self::DATA_MAGIC){$nodesId[]=$id;}}++$id;}while(count($nodesId)!==$count);if($count===1){return$nodesId[0];}else{return$nodesId;}}private
function
findFreeDataNode($size){foreach($this->dataNodeFreeSpace
as$id=>$freeSpace){if($freeSpace>$size){return$id;}}$id=self::$startNode[self::DATA];while(TRUE){if(isset($this->dataNodeFreeSpace[$id])||isset($this->nodeCache[$id])){++$id;continue;}$offset=self::HEADER_SIZE+self::NODE_SIZE*$id;$binary=stream_get_contents($this->handle,2*self::INT32_SIZE,$offset);if(empty($binary)){$this->dataNodeFreeSpace[$id]=self::NODE_SIZE;return$id;}list(,$magic,$nodeSize)=unpack('N2',$binary);if(empty($magic)){$this->dataNodeFreeSpace[$id]=self::NODE_SIZE;return$id;}elseif($magic===self::DATA_MAGIC){$freeSpace=self::NODE_SIZE-$nodeSize;$this->dataNodeFreeSpace[$id]=$freeSpace;if($freeSpace>$size){return$id;}}++$id;}}private
function
bisectNode($id,array$node){$nodeInfo=$node[self::INFO];unset($node[self::INFO]);if(count($node)===1){$key=key($node);$dataId=$this->findFreeDataNode(self::NODE_SIZE);$this->saveNode($dataId,array(self::INDEX_DATA=>$node[$key],self::INFO=>array(self::TYPE=>self::DATA,self::LAST_INDEX=>($dataId<<self::BITROT))));unset($node[$key]);$node[$key][self::INDEX_DATA]=$dataId;$node[self::INFO]=$nodeInfo;$this->saveNode($id,$node);return;}ksort($node);$halfCount=ceil(count($node)/2);list($first,$second)=array_chunk($node,$halfCount,TRUE);end($first);$halfKey=key($first);if($id<=2){list($firstId,$secondId)=$this->findFreeNode(2);$first[self::INFO]=array(self::TYPE=>$nodeInfo[self::TYPE],self::IS_LEAF=>$nodeInfo[self::IS_LEAF],self::PREV_NODE=>-1,self::END=>-1,self::MAX=>$halfKey);$this->saveNode($firstId,$first);$second[self::INFO]=array(self::TYPE=>$nodeInfo[self::TYPE],self::IS_LEAF=>$nodeInfo[self::IS_LEAF],self::PREV_NODE=>$firstId,self::END=>$nodeInfo[self::END],self::MAX=>-1);$this->saveNode($secondId,$second);$parentNode=array(self::INFO=>array(self::TYPE=>$nodeInfo[self::TYPE],self::IS_LEAF=>FALSE,self::PREV_NODE=>-1,self::END=>$secondId,self::MAX=>-1),$halfKey=>$firstId);$this->saveNode($id,$parentNode);}else{$firstId=$this->findFreeNode();$first[self::INFO]=array(self::TYPE=>$nodeInfo[self::TYPE],self::IS_LEAF=>$nodeInfo[self::IS_LEAF],self::PREV_NODE=>$nodeInfo[self::PREV_NODE],self::END=>-1,self::MAX=>$halfKey);$this->saveNode($firstId,$first);$second[self::INFO]=array(self::TYPE=>$nodeInfo[self::TYPE],self::IS_LEAF=>$nodeInfo[self::IS_LEAF],self::PREV_NODE=>$firstId,self::END=>$nodeInfo[self::END],self::MAX=>$nodeInfo[self::MAX]);$this->saveNode($id,$second);list(,,$parent)=$this->findIndexNode($nodeInfo[self::TYPE],$halfKey);$parentNode=$this->getNode($parent);if($parentNode===FALSE){if(self::$debug)throw
new
NetteX\InvalidStateException("Cannot load node number $parent.");}else{$parentNode[$halfKey]=$firstId;ksort($parentNode);$this->saveNode($parent,$parentNode);}}}private
function
headerCommit(){fseek($this->handle,self::INT32_SIZE);@fwrite($this->handle,pack('N',$this->lastNode));}private
function
deleteNode($id){fseek($this->handle,0,SEEK_END);$end=ftell($this->handle);if($end<=(self::HEADER_SIZE+self::NODE_SIZE*($id+1))){$packedNull=pack('N',0);do{$binary=stream_get_contents($this->handle,self::INT32_SIZE,(self::HEADER_SIZE+self::NODE_SIZE*--$id));}while(empty($binary)||$binary===$packedNull);if(!ftruncate($this->handle,self::HEADER_SIZE+self::NODE_SIZE*($id+1))){throw
new
NetteX\InvalidStateException('Cannot truncate journal file.');}}else{fseek($this->handle,self::HEADER_SIZE+self::NODE_SIZE*$id);$writen=fwrite($this->handle,pack('N',0));if($writen!==self::INT32_SIZE){throw
new
NetteX\InvalidStateException("Cannot delete node number $id from journal.");}}}private
function
deleteAll(){if(!ftruncate($this->handle,self::HEADER_SIZE)){throw
new
NetteX\InvalidStateException('Cannot truncate journal file.');}}private
function
lock(){if(!$this->handle){throw
new
NetteX\InvalidStateException('File journal file is not opened');}if(!flock($this->handle,LOCK_EX)){throw
new
NetteX\InvalidStateException('Cannot acquire exclusive lock on journal.');}if($this->lastModTime!==NULL){clearstatcache();if($this->lastModTime<@filemtime($this->file)){$this->nodeCache=$this->dataNodeFreeSpace=array();}}}private
function
unlock(){if($this->handle){fflush($this->handle);flock($this->handle,LOCK_UN);clearstatcache();$this->lastModTime=@filemtime($this->file);}}private
function
arrayAppend(array&$array,array$append){foreach($append
as$value){$array[]=$value;}}private
function
arrayAppendKeys(array&$array,array$append){foreach($append
as$key=>$value){$array[$key]=$value;}}}class
FileStorage
extends
NetteX\Object
implements
NetteX\Caching\IStorage{const
META_HEADER_LEN=28,META_TIME='time',META_SERIALIZED='serialized',META_EXPIRE='expire',META_DELTA='delta',META_ITEMS='di',META_CALLBACKS='callbacks';const
FILE='file',HANDLE='handle';public
static$gcProbability=0.001;public
static$useDirectories;private$dir;private$useDirs;private$journal;function
__construct($dir,IJournal$journal=NULL){$this->dir=realpath($dir);if($this->dir===FALSE){throw
new
NetteX\DirectoryNotFoundException("Directory '$dir' not found.");}if(self::$useDirectories===NULL){$uniq=uniqid('_',TRUE);umask(0000);if(!@mkdir("$dir/$uniq",0777)){throw
new
NetteX\InvalidStateException("Unable to write to directory '$dir'. Make this directory writable.");}self::$useDirectories=!ini_get('safe_mode');if(!self::$useDirectories&&@file_put_contents("$dir/$uniq/_",'')!==FALSE){self::$useDirectories=TRUE;unlink("$dir/$uniq/_");}@rmdir("$dir/$uniq");}$this->useDirs=(bool)self::$useDirectories;$this->journal=$journal;if(mt_rand()/mt_getrandmax()<self::$gcProbability){$this->clean(array());}}function
read($key){$meta=$this->readMetaAndLock($this->getCacheFile($key),LOCK_SH);if($meta&&$this->verify($meta)){return$this->readData($meta);}else{return
NULL;}}private
function
verify($meta){do{if(!empty($meta[self::META_DELTA])){if(filemtime($meta[self::FILE])+$meta[self::META_DELTA]<time())break;touch($meta[self::FILE]);}elseif(!empty($meta[self::META_EXPIRE])&&$meta[self::META_EXPIRE]<time()){break;}if(!empty($meta[self::META_CALLBACKS])&&!Cache::checkCallbacks($meta[self::META_CALLBACKS])){break;}if(!empty($meta[self::META_ITEMS])){foreach($meta[self::META_ITEMS]as$depFile=>$time){$m=$this->readMetaAndLock($depFile,LOCK_SH);if($m[self::META_TIME]!==$time)break
2;if($m&&!$this->verify($m))break
2;}}return
TRUE;}while(FALSE);$this->delete($meta[self::FILE],$meta[self::HANDLE]);return
FALSE;}function
write($key,$data,array$dp){$meta=array(self::META_TIME=>microtime());if(isset($dp[Cache::EXPIRATION])){if(empty($dp[Cache::SLIDING])){$meta[self::META_EXPIRE]=$dp[Cache::EXPIRATION]+time();}else{$meta[self::META_DELTA]=(int)$dp[Cache::EXPIRATION];}}if(isset($dp[Cache::ITEMS])){foreach((array)$dp[Cache::ITEMS]as$item){$depFile=$this->getCacheFile($item);$m=$this->readMetaAndLock($depFile,LOCK_SH);$meta[self::META_ITEMS][$depFile]=$m[self::META_TIME];unset($m);}}if(isset($dp[Cache::CALLBACKS])){$meta[self::META_CALLBACKS]=$dp[Cache::CALLBACKS];}$cacheFile=$this->getCacheFile($key);if($this->useDirs&&!is_dir($dir=dirname($cacheFile))){umask(0000);if(!mkdir($dir,0777)){return;}}$handle=@fopen($cacheFile,'r+b');if(!$handle){$handle=fopen($cacheFile,'wb');if(!$handle){return;}}if(isset($dp[Cache::TAGS])||isset($dp[Cache::PRIORITY])){if(!$this->journal){throw
new
NetteX\InvalidStateException('CacheJournal has not been provided.');}$this->journal->write($cacheFile,$dp);}flock($handle,LOCK_EX);ftruncate($handle,0);if(!is_string($data)){$data=serialize($data);$meta[self::META_SERIALIZED]=TRUE;}$head=serialize($meta).'?>';$head='<?php //netteCache[01]'.str_pad((string)strlen($head),6,'0',STR_PAD_LEFT).$head;$headLen=strlen($head);$dataLen=strlen($data);do{if(fwrite($handle,str_repeat("\x00",$headLen),$headLen)!==$headLen){break;}if(fwrite($handle,$data,$dataLen)!==$dataLen){break;}fseek($handle,0);if(fwrite($handle,$head,$headLen)!==$headLen){break;}flock($handle,LOCK_UN);fclose($handle);return
TRUE;}while(FALSE);$this->delete($cacheFile,$handle);}function
remove($key){$this->delete($this->getCacheFile($key));}function
clean(array$conds){$all=!empty($conds[Cache::ALL]);$collector=empty($conds);if($all||$collector){$now=time();foreach(NetteX\Utils\Finder::find('*')->from($this->dir)->childFirst()as$entry){$path=(string)$entry;if($entry->isDir()){@rmdir($path);continue;}if($all){$this->delete($path);}else{$meta=$this->readMetaAndLock($path,LOCK_SH);if(!$meta)continue;if(!empty($meta[self::META_EXPIRE])&&$meta[self::META_EXPIRE]<$now){$this->delete($path,$meta[self::HANDLE]);continue;}flock($meta[self::HANDLE],LOCK_UN);fclose($meta[self::HANDLE]);}}if($this->journal){$this->journal->clean($conds);}return;}if($this->journal){foreach($this->journal->clean($conds)as$file){$this->delete($file);}}}protected
function
readMetaAndLock($file,$lock){$handle=@fopen($file,'r+b');if(!$handle)return
NULL;flock($handle,$lock);$head=stream_get_contents($handle,self::META_HEADER_LEN);if($head&&strlen($head)===self::META_HEADER_LEN){$size=(int)substr($head,-6);$meta=stream_get_contents($handle,$size,self::META_HEADER_LEN);$meta=@unserialize($meta);if(is_array($meta)){fseek($handle,$size+self::META_HEADER_LEN);$meta[self::FILE]=$file;$meta[self::HANDLE]=$handle;return$meta;}}flock($handle,LOCK_UN);fclose($handle);return
NULL;}protected
function
readData($meta){$data=stream_get_contents($meta[self::HANDLE]);flock($meta[self::HANDLE],LOCK_UN);fclose($meta[self::HANDLE]);if(empty($meta[self::META_SERIALIZED])){return$data;}else{return@unserialize($data);}}protected
function
getCacheFile($key){if($this->useDirs){return$this->dir.'/_'.str_replace('%00','/_',urlencode($key));}else{return$this->dir.'/_'.urlencode($key);}}private
static
function
delete($file,$handle=NULL){if(@unlink($file)){if($handle){flock($handle,LOCK_UN);fclose($handle);}return;}if(!$handle){$handle=@fopen($file,'r+');}if($handle){flock($handle,LOCK_EX);ftruncate($handle,0);flock($handle,LOCK_UN);fclose($handle);@unlink($file);}}}class
MemcachedStorage
extends
NetteX\Object
implements
NetteX\Caching\IStorage{const
META_CALLBACKS='callbacks',META_DATA='data',META_DELTA='delta';private$memcache;private$prefix;private$journal;static
function
isAvailable(){return
extension_loaded('memcache');}function
__construct($host='localhost',$port=11211,$prefix='',IJournal$journal=NULL){if(!self::isAvailable()){throw
new
NetteX\NotSupportedException("PHP extension 'memcache' is not loaded.");}$this->prefix=$prefix;$this->journal=$journal;$this->memcache=new\Memcache;NetteX\Diagnostics\Debugger::tryError();$this->memcache->connect($host,$port);if(NetteX\Diagnostics\Debugger::catchError($e)){throw
new
NetteX\InvalidStateException('Memcache::connect(): '.$e->getMessage(),0,$e);}}function
read($key){$key=$this->prefix.$key;$meta=$this->memcache->get($key);if(!$meta)return
NULL;if(!empty($meta[self::META_CALLBACKS])&&!Cache::checkCallbacks($meta[self::META_CALLBACKS])){$this->memcache->delete($key,0);return
NULL;}if(!empty($meta[self::META_DELTA])){$this->memcache->replace($key,$meta,0,$meta[self::META_DELTA]+time());}return$meta[self::META_DATA];}function
write($key,$data,array$dp){if(isset($dp[Cache::ITEMS])){throw
new
NetteX\NotSupportedException('Dependent items are not supported by MemcachedStorage.');}$key=$this->prefix.$key;$meta=array(self::META_DATA=>$data);$expire=0;if(isset($dp[Cache::EXPIRATION])){$expire=(int)$dp[Cache::EXPIRATION];if(!empty($dp[Cache::SLIDING])){$meta[self::META_DELTA]=$expire;}}if(isset($dp[Cache::CALLBACKS])){$meta[self::META_CALLBACKS]=$dp[Cache::CALLBACKS];}if(isset($dp[Cache::TAGS])||isset($dp[Cache::PRIORITY])){if(!$this->journal){throw
new
NetteX\InvalidStateException('CacheJournal has not been provided.');}$this->journal->write($key,$dp);}$this->memcache->set($key,$meta,0,$expire);}function
remove($key){$this->memcache->delete($this->prefix.$key,0);}function
clean(array$conds){if(!empty($conds[Cache::ALL])){$this->memcache->flush();}elseif($this->journal){foreach($this->journal->clean($conds)as$entry){$this->memcache->delete($entry,0);}}}}class
MemoryStorage
extends
NetteX\Object
implements
NetteX\Caching\IStorage{private$data=array();function
read($key){return
isset($this->data[$key])?$this->data[$key]:NULL;}function
write($key,$data,array$dp){$this->data[$key]=$data;}function
remove($key){unset($this->data[$key]);}function
clean(array$conds){if(!empty($conds[NetteX\Caching\Cache::ALL])){$this->data=array();}}}}namespace NetteX{use
NetteX;class
ArrayHash
implements\ArrayAccess,\Countable,\IteratorAggregate{static
function
from($arr){$obj=new
static;foreach($arr
as$key=>$value){$obj->$key=$value;}return$obj;}function
getIterator(){return
new\ArrayIterator($this);}function
count(){return
count((array)$this);}function
offsetSet($key,$value){if(!is_scalar($key)){throw
new
NetteX\InvalidArgumentException("Key must be either a string or an integer, ".gettype($key)." given.");}$this->$key=$value;}function
offsetGet($key){return$this->$key;}function
offsetExists($key){return
isset($this->$key);}function
offsetUnset($key){unset($this->$key);}}final
class
ArrayUtils{final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
get(array$arr,$key,$default=NULL){foreach(is_array($key)?$key:array($key)as$k){if(is_array($arr)&&array_key_exists($k,$arr)){$arr=$arr[$k];}else{return$default;}}return$arr;}static
function&getRef(&$arr,$key){foreach(is_array($key)?$key:array($key)as$k){if(is_array($arr)||$arr===NULL){$arr=&$arr[$k];}else{throw
new
NetteX\InvalidArgumentException('Traversed item is not an array.');}}return$arr;}static
function
mergeTree($arr1,$arr2){$res=$arr1+$arr2;foreach(array_intersect_key($arr1,$arr2)as$k=>$v){if(is_array($v)&&is_array($arr2[$k])){$res[$k]=self::mergeTree($v,$arr2[$k]);}}return$res;}static
function
searchKey($arr,$key){$foo=array($key=>NULL);return
array_search(key($foo),array_keys($arr),TRUE);}static
function
insertBefore(array&$arr,$key,array$inserted){$offset=self::searchKey($arr,$key);$arr=array_slice($arr,0,$offset,TRUE)+$inserted+array_slice($arr,$offset,count($arr),TRUE);}static
function
insertAfter(array&$arr,$key,array$inserted){$offset=self::searchKey($arr,$key);$offset=$offset===FALSE?count($arr):$offset+1;$arr=array_slice($arr,0,$offset,TRUE)+$inserted+array_slice($arr,$offset,count($arr),TRUE);}static
function
renameKey(array&$arr,$oldKey,$newKey){$offset=self::searchKey($arr,$oldKey);if($offset!==FALSE){$keys=array_keys($arr);$keys[$offset]=$newKey;$arr=array_combine($keys,$arr);}}static
function
grep(array$arr,$pattern,$flags=0){Diagnostics\Debugger::tryError();$res=preg_grep($pattern,$arr,$flags);StringUtils::catchPregError($pattern);return$res;}}final
class
Callback
extends
Object{private$cb;function
__construct($t,$m=NULL){if($m===NULL){$this->cb=$t;}else{$this->cb=array($t,$m);}if(!is_callable($this->cb,TRUE)){throw
new
NetteX\InvalidArgumentException("Invalid callback.");}}function
__invoke(){if(!is_callable($this->cb)){throw
new
InvalidStateException("Callback '$this' is not callable.");}$args=func_get_args();return
call_user_func_array($this->cb,$args);}function
invoke(){if(!is_callable($this->cb)){throw
new
InvalidStateException("Callback '$this' is not callable.");}$args=func_get_args();return
call_user_func_array($this->cb,$args);}function
invokeArgs(array$args){if(!is_callable($this->cb)){throw
new
InvalidStateException("Callback '$this' is not callable.");}return
call_user_func_array($this->cb,$args);}function
isCallable(){return
is_callable($this->cb);}function
getNative(){return$this->cb;}function
isStatic(){return
is_array($this->cb)?is_string($this->cb[0]):is_string($this->cb);}function
__toString(){is_callable($this->cb,TRUE,$textual);return$textual;}}class
DateTime
extends\DateTime{const
MINUTE=60;const
HOUR=3600;const
DAY=86400;const
WEEK=604800;const
MONTH=2629800;const
YEAR=31557600;static
function
from($time){if($time
instanceof\DateTime){return
clone$time;}elseif(is_numeric($time)){if($time<=self::YEAR){$time+=time();}return
new
self(date('Y-m-d H:i:s',$time));}else{return
new
self($time);}}}final
class
Environment{const
DEVELOPMENT='development',PRODUCTION='production',CONSOLE='console';private
static$configurator;private
static$modes=array();private
static$config;private
static$context;private
static$vars=array();private
static$aliases=array('getHttpContext'=>'NetteX\\Web\\HttpContext','getHttpRequest'=>'NetteX\\Web\\IHttpRequest','getHttpResponse'=>'NetteX\\Web\\IHttpResponse','getApplication'=>'NetteX\\Application\\Application','getUser'=>'NetteX\\Web\\IUser','getRobotLoader'=>'NetteX\\Loaders\\RobotLoader');final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
setConfigurator(DI\Configurator$configurator){self::$configurator=$configurator;}static
function
getConfigurator(){if(self::$configurator===NULL){self::$configurator=new
DI\Configurator;}return
self::$configurator;}static
function
setName($name){if(!isset(self::$vars['environment'])){self::setVariable('environment',$name,FALSE);}else{throw
new
InvalidStateException('Environment name has already been set.');}}static
function
getName(){$name=self::getVariable('environment',NULL);if($name===NULL){$name=self::getConfigurator()->detect('environment');self::setVariable('environment',$name,FALSE);}return$name;}static
function
setMode($mode,$value=TRUE){self::$modes[$mode]=(bool)$value;}static
function
getMode($mode){if(isset(self::$modes[$mode])){return
self::$modes[$mode];}else{return
self::$modes[$mode]=self::getConfigurator()->detect($mode);}}static
function
isConsole(){return
self::getMode('console');}static
function
isProduction(){return
self::getMode('production');}static
function
setVariable($name,$value,$expand=TRUE){if(!is_string($value)){$expand=FALSE;}self::$vars[$name]=array($value,(bool)$expand);}static
function
getVariable($name,$default=NULL){if(isset(self::$vars[$name])){list($var,$exp)=self::$vars[$name];if($exp){$var=self::expand($var);self::$vars[$name]=array($var,FALSE);}return$var;}else{$const=strtoupper(preg_replace('#(.)([A-Z]+)#','$1_$2',$name));$list=get_defined_constants(TRUE);if(isset($list['user'][$const])){self::$vars[$name]=array($list['user'][$const],FALSE);return$list['user'][$const];}elseif(func_num_args()>1){return$default;}else{throw
new
InvalidStateException("Unknown environment variable '$name'.");}}}static
function
getVariables(){$res=array();foreach(self::$vars
as$name=>$foo){$res[$name]=self::getVariable($name);}return$res;}static
function
expand($var){static$livelock;if(is_string($var)&&strpos($var,'%')!==FALSE){return@preg_replace_callback('#%([a-z0-9_-]*)%#i',function($m)use(&$livelock){list(,$var)=$m;if($var==='')return'%';if(isset($livelock[$var])){throw
new
InvalidStateException("Circular reference detected for variables: ".implode(', ',array_keys($livelock)).".");}try{$livelock[$var]=TRUE;$val=Environment::getVariable($var);unset($livelock[$var]);}catch(\Exception$e){$livelock=array();throw$e;}if(!is_scalar($val)){throw
new
InvalidStateException("Environment variable '$var' is not scalar.");}return$val;},$var);}return$var;}static
function
getContext(){if(self::$context===NULL){self::$context=self::getConfigurator()->createContext();}return
self::$context;}static
function
getService($name,array$options=NULL){return
self::getContext()->getService($name,$options);}static
function
setServiceAlias($service,$alias){self::$aliases['get'.ucfirst($alias)]=$service;}static
function
__callStatic($name,$args){if(isset(self::$aliases[$name])){return
self::getContext()->getService(self::$aliases[$name],$args);}else{throw
new
MemberAccessException("Call to undefined static method NetteX\\Environment::$name().");}}static
function
getHttpRequest(){return
self::getContext()->getService(self::$aliases[__FUNCTION__]);}static
function
getHttpContext(){return
self::getContext()->getService(self::$aliases[__FUNCTION__]);}static
function
getHttpResponse(){return
self::getContext()->getService(self::$aliases[__FUNCTION__]);}static
function
getApplication(){return
self::getContext()->getService(self::$aliases[__FUNCTION__]);}static
function
getUser(){return
self::getContext()->getService(self::$aliases[__FUNCTION__]);}static
function
getRobotLoader(){return
self::getContext()->getService(self::$aliases[__FUNCTION__]);}static
function
getCache($namespace=''){return
new
Caching\Cache(self::getService('NetteX\\Caching\\ICacheStorage'),$namespace);}static
function
getSession($namespace=NULL){$handler=self::getService('NetteX\\Web\\Session');return$namespace===NULL?$handler:$handler->getNamespace($namespace);}static
function
loadConfig($file=NULL){return
self::$config=self::getConfigurator()->loadConfig($file);}static
function
getConfig($key=NULL,$default=NULL){if(func_num_args()){return
isset(self::$config[$key])?self::$config[$key]:$default;}else{return
self::$config;}}}final
class
Framework{const
NAME='NetteX Framework',VERSION='2.0-dev',REVISION='e46c8e8 released on 2011-04-15';public
static$iAmUsingBadHost=FALSE;final
function
__construct(){throw
new
NetteX\StaticClassException;}}class
Image
extends
Object{const
ENLARGE=1;const
STRETCH=2;const
FIT=0;const
FILL=4;const
JPEG=IMAGETYPE_JPEG,PNG=IMAGETYPE_PNG,GIF=IMAGETYPE_GIF;const
EMPTY_GIF="GIF89a\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;";private$image;static
function
rgb($red,$green,$blue,$transparency=0){return
array('red'=>max(0,min(255,(int)$red)),'green'=>max(0,min(255,(int)$green)),'blue'=>max(0,min(255,(int)$blue)),'alpha'=>max(0,min(127,(int)$transparency)));}static
function
fromFile($file,&$format=NULL){if(!extension_loaded('gd')){throw
new
NetteX\NotSupportedException("PHP extension GD is not loaded.");}$info=@getimagesize($file);switch($format=$info[2]){case
self::JPEG:return
new
static(imagecreatefromjpeg($file));case
self::PNG:return
new
static(imagecreatefrompng($file));case
self::GIF:return
new
static(imagecreatefromgif($file));default:throw
new
UnknownImageFileException("Unknown image type or file '$file' not found.");}}static
function
getFormatFromString($s){$types=array('image/jpeg'=>self::JPEG,'image/gif'=>self::GIF,'image/png'=>self::PNG);$type=Utils\MimeTypeDetector::fromString($s);return
isset($types[$type])?$types[$type]:NULL;}static
function
fromString($s,&$format=NULL){if(!extension_loaded('gd')){throw
new
NetteX\NotSupportedException("PHP extension GD is not loaded.");}$format=static::getFormatFromString($s);return
new
static(imagecreatefromstring($s));}static
function
fromBlank($width,$height,$color=NULL){if(!extension_loaded('gd')){throw
new
NetteX\NotSupportedException("PHP extension GD is not loaded.");}$width=(int)$width;$height=(int)$height;if($width<1||$height<1){throw
new
NetteX\InvalidArgumentException('Image width and height must be greater than zero.');}$image=imagecreatetruecolor($width,$height);if(is_array($color)){$color+=array('alpha'=>0);$color=imagecolorallocatealpha($image,$color['red'],$color['green'],$color['blue'],$color['alpha']);imagealphablending($image,FALSE);imagefilledrectangle($image,0,0,$width-1,$height-1,$color);imagealphablending($image,TRUE);}return
new
static($image);}function
__construct($image){$this->setImageResource($image);imagesavealpha($image,TRUE);}function
getWidth(){return
imagesx($this->image);}function
getHeight(){return
imagesy($this->image);}protected
function
setImageResource($image){if(!is_resource($image)||get_resource_type($image)!=='gd'){throw
new
NetteX\InvalidArgumentException('Image is not valid.');}$this->image=$image;return$this;}function
getImageResource(){return$this->image;}function
resize($width,$height,$flags=self::FIT){list($newWidth,$newHeight)=self::calculateSize($this->getWidth(),$this->getHeight(),$width,$height,$flags);if($newWidth!==$this->getWidth()||$newHeight!==$this->getHeight()){$newImage=self::fromBlank($newWidth,$newHeight,self::RGB(0,0,0,127))->getImageResource();imagecopyresampled($newImage,$this->getImageResource(),0,0,0,0,$newWidth,$newHeight,$this->getWidth(),$this->getHeight());$this->image=$newImage;}if($width<0||$height<0){$newImage=self::fromBlank($newWidth,$newHeight,self::RGB(0,0,0,127))->getImageResource();imagecopyresampled($newImage,$this->getImageResource(),0,0,$width<0?$newWidth-1:0,$height<0?$newHeight-1:0,$newWidth,$newHeight,$width<0?-$newWidth:$newWidth,$height<0?-$newHeight:$newHeight);$this->image=$newImage;}return$this;}static
function
calculateSize($srcWidth,$srcHeight,$newWidth,$newHeight,$flags=self::FIT){if(substr($newWidth,-1)==='%'){$newWidth=round($srcWidth/100*abs($newWidth));$flags|=self::ENLARGE;$percents=TRUE;}else{$newWidth=(int)abs($newWidth);}if(substr($newHeight,-1)==='%'){$newHeight=round($srcHeight/100*abs($newHeight));$flags|=empty($percents)?self::ENLARGE:self::STRETCH;}else{$newHeight=(int)abs($newHeight);}if($flags&self::STRETCH){if(empty($newWidth)||empty($newHeight)){throw
new
NetteX\InvalidArgumentException('For stretching must be both width and height specified.');}if(($flags&self::ENLARGE)===0){$newWidth=round($srcWidth*min(1,$newWidth/$srcWidth));$newHeight=round($srcHeight*min(1,$newHeight/$srcHeight));}}else{if(empty($newWidth)&&empty($newHeight)){throw
new
NetteX\InvalidArgumentException('At least width or height must be specified.');}$scale=array();if($newWidth>0){$scale[]=$newWidth/$srcWidth;}if($newHeight>0){$scale[]=$newHeight/$srcHeight;}if($flags&self::FILL){$scale=array(max($scale));}if(($flags&self::ENLARGE)===0){$scale[]=1;}$scale=min($scale);$newWidth=round($srcWidth*$scale);$newHeight=round($srcHeight*$scale);}return
array(max((int)$newWidth,1),max((int)$newHeight,1));}function
crop($left,$top,$width,$height){list($left,$top,$width,$height)=self::calculateCutout($this->getWidth(),$this->getHeight(),$left,$top,$width,$height);$newImage=self::fromBlank($width,$height,self::RGB(0,0,0,127))->getImageResource();imagecopy($newImage,$this->getImageResource(),0,0,$left,$top,$width,$height);$this->image=$newImage;return$this;}static
function
calculateCutout($srcWidth,$srcHeight,$left,$top,$newWidth,$newHeight){if(substr($newWidth,-1)==='%'){$newWidth=round($srcWidth/100*$newWidth);}if(substr($newHeight,-1)==='%'){$newHeight=round($srcHeight/100*$newHeight);}if(substr($left,-1)==='%'){$left=round(($srcWidth-$newWidth)/100*$left);}if(substr($top,-1)==='%'){$top=round(($srcHeight-$newHeight)/100*$top);}if($left<0){$newWidth+=$left;$left=0;}if($top<0){$newHeight+=$top;$top=0;}$newWidth=min((int)$newWidth,$srcWidth-$left);$newHeight=min((int)$newHeight,$srcHeight-$top);return
array($left,$top,$newWidth,$newHeight);}function
sharpen(){imageconvolution($this->getImageResource(),array(array(-1,-1,-1),array(-1,24,-1),array(-1,-1,-1)),16,0);return$this;}function
place(Image$image,$left=0,$top=0,$opacity=100){$opacity=max(0,min(100,(int)$opacity));if(substr($left,-1)==='%'){$left=round(($this->getWidth()-$image->getWidth())/100*$left);}if(substr($top,-1)==='%'){$top=round(($this->getHeight()-$image->getHeight())/100*$top);}if($opacity===100){imagecopy($this->getImageResource(),$image->getImageResource(),$left,$top,0,0,$image->getWidth(),$image->getHeight());}elseif($opacity<>0){imagecopymerge($this->getImageResource(),$image->getImageResource(),$left,$top,0,0,$image->getWidth(),$image->getHeight(),$opacity);}return$this;}function
save($file=NULL,$quality=NULL,$type=NULL){if($type===NULL){switch(strtolower(pathinfo($file,PATHINFO_EXTENSION))){case'jpg':case'jpeg':$type=self::JPEG;break;case'png':$type=self::PNG;break;case'gif':$type=self::GIF;}}switch($type){case
self::JPEG:$quality=$quality===NULL?85:max(0,min(100,(int)$quality));return
imagejpeg($this->getImageResource(),$file,$quality);case
self::PNG:$quality=$quality===NULL?9:max(0,min(9,(int)$quality));return
imagepng($this->getImageResource(),$file,$quality);case
self::GIF:return$file===NULL?imagegif($this->getImageResource()):imagegif($this->getImageResource(),$file);default:throw
new
NetteX\InvalidArgumentException("Unsupported image type.");}}function
toString($type=self::JPEG,$quality=NULL){ob_start();$this->save(NULL,$quality,$type);return
ob_get_clean();}function
__toString(){try{return$this->toString();}catch(\Exception$e){Diagnostics\Debugger::toStringException($e);}}function
send($type=self::JPEG,$quality=NULL){if($type!==self::GIF&&$type!==self::PNG&&$type!==self::JPEG){throw
new
NetteX\InvalidArgumentException("Unsupported image type.");}header('Content-Type: '.image_type_to_mime_type($type));return$this->save(NULL,$quality,$type);}function
__call($name,$args){$function='image'.$name;if(function_exists($function)){foreach($args
as$key=>$value){if($value
instanceof
self){$args[$key]=$value->getImageResource();}elseif(is_array($value)&&isset($value['red'])){$args[$key]=imagecolorallocatealpha($this->getImageResource(),$value['red'],$value['green'],$value['blue'],$value['alpha']);}}array_unshift($args,$this->getImageResource());$res=call_user_func_array($function,$args);return
is_resource($res)&&get_resource_type($res)==='gd'?$this->setImageResource($res):$res;}return
parent::__call($name,$args);}}class
UnknownImageFileException
extends\Exception{}final
class
ObjectMixin{private
static$methods;final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
call($_this,$name,$args){$class=new
Reflection\ClassType($_this);if($name===''){throw
new
MemberAccessException("Call to class '$class->name' method without name.");}if($class->hasEventProperty($name)){if(is_array($list=$_this->$name)||$list
instanceof\Traversable){foreach($list
as$handler){callback($handler)->invokeArgs($args);}}return
NULL;}if($cb=$class->getExtensionMethod($name)){array_unshift($args,$_this);return$cb->invokeArgs($args);}throw
new
MemberAccessException("Call to undefined method $class->name::$name().");}static
function
callStatic($class,$name,$args){throw
new
MemberAccessException("Call to undefined static method $class::$name().");}static
function&get($_this,$name){$class=get_class($_this);if($name===''){throw
new
MemberAccessException("Cannot read a class '$class' property without name.");}if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";$m='get'.$name;if(isset(self::$methods[$class][$m])){$val=$_this->$m();return$val;}$m='is'.$name;if(isset(self::$methods[$class][$m])){$val=$_this->$m();return$val;}$type=isset(self::$methods[$class]['set'.$name])?'a write-only':'an undeclared';$name=func_get_arg(1);throw
new
MemberAccessException("Cannot read $type property $class::\$$name.");}static
function
set($_this,$name,$value){$class=get_class($_this);if($name===''){throw
new
MemberAccessException("Cannot write to a class '$class' property without name.");}if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";$m='set'.$name;if(isset(self::$methods[$class][$m])){$_this->$m($value);return;}$type=isset(self::$methods[$class]['get'.$name])||isset(self::$methods[$class]['is'.$name])?'a read-only':'an undeclared';$name=func_get_arg(1);throw
new
MemberAccessException("Cannot write to $type property $class::\$$name.");}static
function
remove($_this,$name){$class=get_class($_this);throw
new
MemberAccessException("Cannot unset the property $class::\$$name.");}static
function
has($_this,$name){if($name===''){return
FALSE;}$class=get_class($_this);if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";return
isset(self::$methods[$class]['get'.$name])||isset(self::$methods[$class]['is'.$name]);}}class
StringUtils{final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
checkEncoding($s,$encoding='UTF-8'){return$s===self::fixEncoding($s,$encoding);}static
function
fixEncoding($s,$encoding='UTF-8'){return@iconv('UTF-16',$encoding.'//IGNORE',iconv($encoding,'UTF-16//IGNORE',$s));}static
function
chr($code,$encoding='UTF-8'){return
iconv('UTF-32BE',$encoding.'//IGNORE',pack('N',$code));}static
function
startsWith($haystack,$needle){return
strncmp($haystack,$needle,strlen($needle))===0;}static
function
endsWith($haystack,$needle){return
strlen($needle)===0||substr($haystack,-strlen($needle))===$needle;}static
function
normalize($s){$s=str_replace("\r\n","\n",$s);$s=strtr($s,"\r","\n");$s=preg_replace('#[\x00-\x08\x0B-\x1F]+#','',$s);$s=preg_replace("#[\t ]+$#m",'',$s);$s=trim($s,"\n");return$s;}static
function
toAscii($s){$s=preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u','',$s);$s=strtr($s,'`\'"^~',"\x01\x02\x03\x04\x05");if(ICONV_IMPL==='glibc'){$s=@iconv('UTF-8','WINDOWS-1250//TRANSLIT',$s);$s=strtr($s,"\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"."\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"."\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"."\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe","ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt");}else{$s=@iconv('UTF-8','ASCII//TRANSLIT',$s);}$s=str_replace(array('`',"'",'"','^','~'),'',$s);return
strtr($s,"\x01\x02\x03\x04\x05",'`\'"^~');}static
function
webalize($s,$charlist=NULL,$lower=TRUE){$s=self::toAscii($s);if($lower)$s=strtolower($s);$s=preg_replace('#[^a-z0-9'.preg_quote($charlist,'#').']+#i','-',$s);$s=trim($s,'-');return$s;}static
function
truncate($s,$maxLen,$append="\xE2\x80\xA6"){if(self::length($s)>$maxLen){$maxLen=$maxLen-self::length($append);if($maxLen<1){return$append;}elseif($matches=self::match($s,'#^.{1,'.$maxLen.'}(?=[\s\x00-/:-@\[-`{-~])#us')){return$matches[0].$append;}else{return
iconv_substr($s,0,$maxLen,'UTF-8').$append;}}return$s;}static
function
indent($s,$level=1,$chars="\t"){return$level<1?$s:self::replace($s,'#(?:^|[\r\n]+)(?=[^\r\n])#','$0'.str_repeat($chars,$level));}static
function
lower($s){return
mb_strtolower($s,'UTF-8');}static
function
upper($s){return
mb_strtoupper($s,'UTF-8');}static
function
firstUpper($s){return
self::upper(mb_substr($s,0,1,'UTF-8')).mb_substr($s,1,self::length($s),'UTF-8');}static
function
capitalize($s){return
mb_convert_case($s,MB_CASE_TITLE,'UTF-8');}static
function
compare($left,$right,$len=NULL){if($len<0){$left=iconv_substr($left,$len,-$len,'UTF-8');$right=iconv_substr($right,$len,-$len,'UTF-8');}elseif($len!==NULL){$left=iconv_substr($left,0,$len,'UTF-8');$right=iconv_substr($right,0,$len,'UTF-8');}return
self::lower($left)===self::lower($right);}static
function
length($s){return
function_exists('mb_strlen')?mb_strlen($s,'UTF-8'):strlen(utf8_decode($s));}static
function
trim($s,$charlist=" \t\n\r\0\x0B\xC2\xA0"){$charlist=preg_quote($charlist,'#');return
self::replace($s,'#^['.$charlist.']+|['.$charlist.']+$#u','');}static
function
padLeft($s,$length,$pad=' '){$length=max(0,$length-self::length($s));$padLen=self::length($pad);return
str_repeat($pad,$length/$padLen).iconv_substr($pad,0,$length
%$padLen,'UTF-8').$s;}static
function
padRight($s,$length,$pad=' '){$length=max(0,$length-self::length($s));$padLen=self::length($pad);return$s.str_repeat($pad,$length/$padLen).iconv_substr($pad,0,$length
%$padLen,'UTF-8');}static
function
random($length=10,$charlist='0-9a-z'){$charlist=str_shuffle(preg_replace_callback('#.-.#',function($m){return
implode('',range($m[0][0],$m[0][2]));},$charlist));$chLen=strlen($charlist);$s='';for($i=0;$i<$length;$i++){if($i
%
5===0){$rand=lcg_value();$rand2=microtime(TRUE);}$rand*=$chLen;$s.=$charlist[($rand+$rand2)%$chLen];$rand-=(int)$rand;}return$s;}static
function
split($subject,$pattern,$flags=0){Diagnostics\Debugger::tryError();$res=preg_split($pattern,$subject,-1,$flags|PREG_SPLIT_DELIM_CAPTURE);self::catchPregError($pattern);return$res;}static
function
match($subject,$pattern,$flags=0,$offset=0){Diagnostics\Debugger::tryError();$res=preg_match($pattern,$subject,$m,$flags,$offset);self::catchPregError($pattern);if($res){return$m;}}static
function
matchAll($subject,$pattern,$flags=0,$offset=0){Diagnostics\Debugger::tryError();$res=preg_match_all($pattern,$subject,$m,($flags&PREG_PATTERN_ORDER)?$flags:($flags|PREG_SET_ORDER),$offset);self::catchPregError($pattern);return$m;}static
function
replace($subject,$pattern,$replacement=NULL,$limit=-1){Diagnostics\Debugger::tryError();if(is_object($replacement)||is_array($replacement)){if($replacement
instanceof
Callback){$replacement=$replacement->getNative();}if(!is_callable($replacement,FALSE,$textual)){Diagnostics\Debugger::catchError($foo);throw
new
InvalidStateException("Callback '$textual' is not callable.");}$res=preg_replace_callback($pattern,$replacement,$subject,$limit);if(Diagnostics\Debugger::catchError($e)){$trace=$e->getTrace();if(isset($trace[2]['class'])&&$trace[2]['class']===__CLASS__){throw
new
RegexpException($e->getMessage()." in pattern: $pattern");}}}elseif(is_array($pattern)){$res=preg_replace(array_keys($pattern),array_values($pattern),$subject,$limit);}else{$res=preg_replace($pattern,$replacement,$subject,$limit);}self::catchPregError($pattern);return$res;}static
function
catchPregError($pattern){if(Diagnostics\Debugger::catchError($e)){throw
new
RegexpException($e->getMessage()." in pattern: $pattern");}elseif(preg_last_error()){static$messages=array(PREG_INTERNAL_ERROR=>'Internal error',PREG_BACKTRACK_LIMIT_ERROR=>'Backtrack limit was exhausted',PREG_RECURSION_LIMIT_ERROR=>'Recursion limit was exhausted',PREG_BAD_UTF8_ERROR=>'Malformed UTF-8 data',5=>'Offset didn\'t correspond to the begin of a valid UTF-8 code point');$code=preg_last_error();throw
new
RegexpException((isset($messages[$code])?$messages[$code]:'Unknown error')." (pattern: $pattern)",$code);}}}class
RegexpException
extends\Exception{}}namespace NetteX\ComponentModel{use
NetteX;class
RecursiveComponentIterator
extends\RecursiveArrayIterator
implements\Countable{function
hasChildren(){return$this->current()instanceof
IContainer;}function
getChildren(){return$this->current()->getComponents();}function
count(){return
iterator_count($this);}}}namespace NetteX\Config{use
NetteX;class
Config
implements\ArrayAccess,\IteratorAggregate{private
static$extensions=array('ini'=>'NetteX\Config\IniAdapter','neon'=>'NetteX\Config\NeonAdapter');static
function
registerExtension($extension,$class){if(!class_exists($class)){throw
new
NetteX\InvalidArgumentException("Class '$class' was not found.");}if(!NetteX\Reflection\ClassType::from($class)->implementsInterface('NetteX\Config\IAdapter')){throw
new
NetteX\InvalidArgumentException("Configuration adapter '$class' is not NetteX\\Config\\IAdapter implementor.");}self::$extensions[strtolower($extension)]=$class;}static
function
fromFile($file,$section=NULL){$extension=strtolower(pathinfo($file,PATHINFO_EXTENSION));if(!isset(self::$extensions[$extension])){throw
new
NetteX\InvalidArgumentException("Unknown file extension '$file'.");}$data=call_user_func(array(self::$extensions[$extension],'load'),$file,$section);if($section){if(!isset($data[$section])||!is_array($data[$section])){throw
new
NetteX\InvalidStateException("There is not section [$section] in '$file'.");}$data=$data[$section];}return
new
static($data);}function
__construct($arr=NULL){foreach((array)$arr
as$k=>$v){$this->$k=is_array($v)?new
static($v):$v;}}function
save($file){$extension=strtolower(pathinfo($file,PATHINFO_EXTENSION));if(!isset(self::$extensions[$extension])){throw
new
NetteX\InvalidArgumentException("Unknown file extension '$file'.");}return
call_user_func(array(self::$extensions[$extension],'save'),$this,$file);}function
__set($key,$value){if(!is_scalar($key)){throw
new
NetteX\InvalidArgumentException("Key must be either a string or an integer.");}elseif($value===NULL){unset($this->$key);}else{$this->$key=$value;}}function&__get($key){if(!is_scalar($key)){throw
new
NetteX\InvalidArgumentException("Key must be either a string or an integer.");}return$this->$key;}function
__isset($key){return
FALSE;}function
__unset($key){}function
offsetSet($key,$value){$this->__set($key,$value);}function
offsetGet($key){if(!is_scalar($key)){throw
new
NetteX\InvalidArgumentException("Key must be either a string or an integer.");}elseif(!isset($this->$key)){return
NULL;}return$this->$key;}function
offsetExists($key){if(!is_scalar($key)){throw
new
NetteX\InvalidArgumentException("Key must be either a string or an integer.");}return
isset($this->$key);}function
offsetUnset($key){if(!is_scalar($key)){throw
new
NetteX\InvalidArgumentException("Key must be either a string or an integer.");}unset($this->$key);}function
getIterator(){return
new
NetteX\Iterators\Recursor(new\ArrayIterator($this));}function
toArray(){$arr=array();foreach($this
as$k=>$v){$arr[$k]=$v
instanceof
self?$v->toArray():$v;}return$arr;}}final
class
IniAdapter
implements
IAdapter{public
static$keySeparator='.';public
static$sectionSeparator=' < ';public
static$rawSection='!';final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
load($file){if(!is_file($file)||!is_readable($file)){throw
new
NetteX\FileNotFoundException("File '$file' is missing or is not readable.");}NetteX\Diagnostics\Debugger::tryError();$ini=parse_ini_file($file,TRUE);if(NetteX\Diagnostics\Debugger::catchError($e)){throw
new
NetteX\InvalidStateException('parse_ini_file(): '.$e->getMessage(),0,$e);}$separator=trim(self::$sectionSeparator);$data=array();foreach($ini
as$secName=>$secData){if(is_array($secData)){if(substr($secName,-1)===self::$rawSection){$secName=substr($secName,0,-1);}elseif(self::$keySeparator){$tmp=array();foreach($secData
as$key=>$val){$cursor=&$tmp;foreach(explode(self::$keySeparator,$key)as$part){if(!isset($cursor[$part])||is_array($cursor[$part])){$cursor=&$cursor[$part];}else{throw
new
NetteX\InvalidStateException("Invalid key '$key' in section [$secName] in '$file'.");}}$cursor=$val;}$secData=$tmp;}$parts=$separator?explode($separator,strtr($secName,':',$separator)):array($secName);if(count($parts)>1){$parent=trim($parts[1]);$cursor=&$data;foreach(self::$keySeparator?explode(self::$keySeparator,$parent):array($parent)as$part){if(isset($cursor[$part])&&is_array($cursor[$part])){$cursor=&$cursor[$part];}else{throw
new
NetteX\InvalidStateException("Missing parent section [$parent] in '$file'.");}}$secData=NetteX\ArrayUtils::mergeTree($secData,$cursor);}$secName=trim($parts[0]);if($secName===''){throw
new
NetteX\InvalidStateException("Invalid empty section name in '$file'.");}}if(self::$keySeparator){$cursor=&$data;foreach(explode(self::$keySeparator,$secName)as$part){if(!isset($cursor[$part])||is_array($cursor[$part])){$cursor=&$cursor[$part];}else{throw
new
NetteX\InvalidStateException("Invalid section [$secName] in '$file'.");}}}else{$cursor=&$data[$secName];}if(is_array($secData)&&is_array($cursor)){$secData=NetteX\ArrayUtils::mergeTree($secData,$cursor);}$cursor=$secData;}return$data;}static
function
save($config,$file){$output=array();$output[]='; generated by NetteX';$output[]='';foreach($config
as$secName=>$secData){if(!(is_array($secData)||$secData
instanceof\Traversable)){throw
new
NetteX\InvalidStateException("Invalid section '$section'.");}$output[]="[$secName]";self::build($secData,$output,'');$output[]='';}if(!file_put_contents($file,implode(PHP_EOL,$output))){throw
new
NetteX\IOException("Cannot write file '$file'.");}}private
static
function
build($input,&$output,$prefix){foreach($input
as$key=>$val){if(is_array($val)||$val
instanceof\Traversable){self::build($val,$output,$prefix.$key.self::$keySeparator);}elseif(is_bool($val)){$output[]="$prefix$key = ".($val?'true':'false');}elseif(is_numeric($val)){$output[]="$prefix$key = $val";}elseif(is_string($val)){$output[]="$prefix$key = \"$val\"";}else{throw
new
NetteX\InvalidArgumentException("The '$prefix$key' item must be scalar or array, ".gettype($val)." given.");}}}}use
NetteX\Utils\Neon;final
class
NeonAdapter
implements
IAdapter{public
static$sectionSeparator=' < ';public
static$keySeparator='.';final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
load($file){if(!is_file($file)||!is_readable($file)){throw
new
NetteX\FileNotFoundException("File '$file' is missing or is not readable.");}$neon=Neon::decode(file_get_contents($file));$separator=trim(self::$sectionSeparator);$data=array();foreach($neon
as$secName=>$secData){if($secData===NULL){$secData=array();}if(is_array($secData)){$parts=$separator?explode($separator,$secName):array($secName);if(count($parts)>1){$parent=trim($parts[1]);$cursor=&$data;foreach(self::$keySeparator?explode(self::$keySeparator,$parent):array($parent)as$part){if(isset($cursor[$part])&&is_array($cursor[$part])){$cursor=&$cursor[$part];}else{throw
new
NetteX\InvalidStateException("Missing parent section $parent in '$file'.");}}$secData=NetteX\ArrayUtils::mergeTree($secData,$cursor);}$secName=trim($parts[0]);if($secName===''){throw
new
NetteX\InvalidStateException("Invalid empty section name in '$file'.");}}if(self::$keySeparator){$cursor=&$data;foreach(explode(self::$keySeparator,$secName)as$part){if(!isset($cursor[$part])||is_array($cursor[$part])){$cursor=&$cursor[$part];}else{throw
new
NetteX\InvalidStateException("Invalid section [$secName] in '$file'.");}}}else{$cursor=&$data[$secName];}if(is_array($secData)&&is_array($cursor)){$secData=NetteX\ArrayUtils::mergeTree($secData,$cursor);}$cursor=$secData;}return$data;}static
function
save($config,$file){if(!file_put_contents($file,"# generated by NetteX\n\n".Neon::encode($config,Neon::BLOCK))){throw
new
NetteX\IOException("Cannot write file '$file'.");}}}}namespace NetteX\Database{use
NetteX;use
NetteX\ObjectMixin;use
PDO;if(class_exists('PDO')){class
Connection
extends
PDO{private$driver;private$preprocessor;public$databaseReflection;public$cache;public$substitutions=array();public$onQuery;function
__construct($dsn,$username=NULL,$password=NULL,array$options=NULL){parent::__construct($dsn,$username,$password,$options);$this->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$this->setAttribute(PDO::ATTR_STATEMENT_CLASS,array('NetteX\Database\Statement',array($this)));$class='NetteX\Database\Drivers\\'.$this->getAttribute(PDO::ATTR_DRIVER_NAME).'Driver';if(class_exists($class)){$this->driver=new$class($this,(array)$options);}$this->preprocessor=new
SqlPreprocessor($this);$this->databaseReflection=new
Reflection\DatabaseReflection;if(!NetteX\Diagnostics\Debugger::$productionMode){NetteX\Diagnostics\Debugger::addPanel($panel=new
Diagnostics\ConnectionPanel($dsn));$this->onQuery[]=callback($panel,'logQuery');}}function
getSupplementalDriver(){return$this->driver;}function
query($statement){$args=func_get_args();return$this->queryArgs(array_shift($args),$args);}function
exec($statement){$args=func_get_args();return$this->queryArgs(array_shift($args),$args)->rowCount();}function
queryArgs($statement,$params){foreach($params
as$value){if(is_array($value)||is_object($value)){$need=TRUE;break;}}if(isset($need)||strpos($statement,':')!==FALSE&&$this->preprocessor!==NULL){list($statement,$params)=$this->preprocessor->process($statement,$params);}return$this->prepare($statement)->execute($params);}function
fetch($args){$args=func_get_args();return$this->queryArgs(array_shift($args),$args)->fetch();}function
fetchColumn($args){$args=func_get_args();return$this->queryArgs(array_shift($args),$args)->fetchColumn();}function
fetchPairs($args){$args=func_get_args();return$this->queryArgs(array_shift($args),$args)->fetchPairs();}function
fetchAll($args){$args=func_get_args();return$this->queryArgs(array_shift($args),$args)->fetchAll();}function
table($table){return
new
Table\Selection($table,$this);}function
loadFile($file){@set_time_limit(0);$handle=@fopen($file,'r');if(!$handle){throw
new
NetteX\FileNotFoundException("Cannot open file '$file'.");}$count=0;$sql='';while(!feof($handle)){$s=fgets($handle);$sql.=$s;if(substr(rtrim($s),-1)===';'){parent::exec($sql);$sql='';$count++;}}fclose($handle);return$count;}static
function
highlightSql($sql){static$keywords1='SELECT|UPDATE|INSERT(?:\s+INTO)?|REPLACE(?:\s+INTO)?|DELETE|FROM|WHERE|HAVING|GROUP\s+BY|ORDER\s+BY|LIMIT|OFFSET|SET|VALUES|LEFT\s+JOIN|INNER\s+JOIN|TRUNCATE';static$keywords2='ALL|DISTINCT|DISTINCTROW|AS|USING|ON|AND|OR|IN|IS|NOT|NULL|LIKE|TRUE|FALSE';$sql=" $sql ";$sql=preg_replace("#(?<=[\\s,(])($keywords1)(?=[\\s,)])#i","\n\$1",$sql);$sql=preg_replace('#[ \t]{2,}#'," ",$sql);$sql=wordwrap($sql,100);$sql=preg_replace("#([ \t]*\r?\n){2,}#","\n",$sql);$sql=htmlSpecialChars($sql);$sql=preg_replace_callback("#(/\\*.+?\\*/)|(\\*\\*.+?\\*\\*)|(?<=[\\s,(])($keywords1)(?=[\\s,)])|(?<=[\\s,(=])($keywords2)(?=[\\s,)=])#is",function($matches){if(!empty($matches[1]))return'<em style="color:gray">'.$matches[1].'</em>';if(!empty($matches[2]))return'<strong style="color:red">'.$matches[2].'</strong>';if(!empty($matches[3]))return'<strong style="color:blue">'.$matches[3].'</strong>';if(!empty($matches[4]))return'<strong style="color:green">'.$matches[4].'</strong>';},$sql);return'<pre class="dump">'.trim($sql)."</pre>\n";}static
function
getReflection(){return
new
NetteX\Reflection\ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}}}namespace NetteX\Database\Diagnostics{use
NetteX;class
ConnectionPanel
extends
NetteX\Object
implements
NetteX\Diagnostics\IPanel{static
public$maxLength=1000;public$totalTime=0;public$queries=array();public$name;public$explain=TRUE;public$disabled=FALSE;function
logQuery(NetteX\Database\Statement$result,array$params=NULL){if($this->disabled){return;}$source=NULL;foreach(debug_backtrace(FALSE)as$row){if(isset($row['file'])&&is_file($row['file'])&&strpos($row['file'],NETTEX_DIR.DIRECTORY_SEPARATOR)!==0){$source=array($row['file'],(int)$row['line']);break;}}$this->totalTime+=$result->time;$this->queries[]=array($result->queryString,$params,$result->time,$result->rowCount(),$result->getConnection(),$source);}function
getId(){return'database';}function
getTab(){return'<span title="NetteX\\Database '.htmlSpecialChars($this->name).'">'.'<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEYSURBVBgZBcHPio5hGAfg6/2+R980k6wmJgsJ5U/ZOAqbSc2GnXOwUg7BESgLUeIQ1GSjLFnMwsKGGg1qxJRmPM97/1zXFAAAAEADdlfZzr26miup2svnelq7d2aYgt3rebl585wN6+K3I1/9fJe7O/uIePP2SypJkiRJ0vMhr55FLCA3zgIAOK9uQ4MS361ZOSX+OrTvkgINSjS/HIvhjxNNFGgQsbSmabohKDNoUGLohsls6BaiQIMSs2FYmnXdUsygQYmumy3Nhi6igwalDEOJEjPKP7CA2aFNK8Bkyy3fdNCg7r9/fW3jgpVJbDmy5+PB2IYp4MXFelQ7izPrhkPHB+P5/PjhD5gCgCenx+VR/dODEwD+A3T7nqbxwf1HAAAAAElFTkSuQmCC" />'.count($this->queries).' queries'.($this->totalTime?' / '.sprintf('%0.1f',$this->totalTime*1000).'ms':'').'</span>';}function
getPanel(){$this->disabled=TRUE;$s='';$h='htmlSpecialChars';foreach($this->queries
as$i=>$query){list($sql,$params,$time,$rows,$connection,$source)=$query;$explain=NULL;if($this->explain&&preg_match('#\s*SELECT\s#iA',$sql)){try{$explain=$connection->queryArgs('EXPLAIN '.$sql,$params)->fetchAll();}catch(\PDOException$e){}}$s.='<tr><td>'.sprintf('%0.3f',$time*1000);if($explain){$s.="<br /><a href='#' class='nette-toggler' rel='#nette-debug-database-row-{$h($this->name)}-$i'>explain&nbsp;&#x25ba;</a>";}$s.='</td><td class="database-sql">'.NetteX\Database\Connection::highlightSql(NetteX\StringUtils::truncate($sql,self::$maxLength));if($explain){$s.="<table id='nette-debug-database-row-{$h($this->name)}-$i' class='nette-collapsed'><tr>";foreach($explain[0]as$col=>$foo){$s.="<th>{$h($col)}</th>";}$s.="</tr>";foreach($explain
as$row){$s.="<tr>";foreach($row
as$col){$s.="<td>{$h($col)}</td>";}$s.="</tr>";}$s.="</table>";}if($source){list($file,$line)=$source;$s.=(NetteX\Diagnostics\Debugger::$editor?"<a href='{$h(NetteX\Diagnostics\Helpers::editorLink($file,$line))}'":'<span')." class='database-source' title='{$h($file)}:$line'>"."{$h(basename(dirname($file)).'/'.basename($file))}:$line".(NetteX\Diagnostics\Debugger::$editor?'</a>':'</span>');}$s.='</td><td>';foreach($params
as$param){$s.="{$h(NetteX\StringUtils::truncate($param,self::$maxLength))}<br>";}$s.='</td><td>'.$rows.'</td></tr>';}return
empty($this->queries)?'':'<style> #nette-debug-database td.database-sql { background: white !important }
			#nette-debug-database .database-source { color: #BBB !important }
			#nette-debug-database tr table { margin: 8px 0; max-height: 150px; overflow:auto } </style>
			<h1>Queries: '.count($this->queries).($this->totalTime?', time: '.sprintf('%0.3f',$this->totalTime*1000).' ms':'').'</h1>
			<div class="nette-inner">
			<table>
				<tr><th>Time&nbsp;ms</th><th>SQL Statement</th><th>Params</th><th>Rows</th></tr>'.$s.'
			</table>
			</div>';}}}namespace NetteX\Database\Drivers{use
NetteX;class
MsSqlDriver
extends
NetteX\Object
implements
NetteX\Database\ISupplementalDriver{private$connection;function
__construct(NetteX\Database\Connection$connection,array$options){$this->connection=$connection;}function
delimite($name){return'['.str_replace(array('[',']'),array('[[',']]'),$name).']';}function
formatDateTime(\DateTime$value){return$value->format("'Y-m-d H:i:s'");}function
formatLike($value,$pos){$value=strtr($value,array("'"=>"''",'%'=>'[%]','_'=>'[_]','['=>'[[]'));return($pos<=0?"'%":"'").$value.($pos>=0?"%'":"'");}function
applyLimit(&$sql,$limit,$offset){if($limit>=0){$sql='SELECT TOP '.(int)$limit.' * FROM ('.$sql.') t';}if($offset){throw
new
NetteX\NotImplementedException('Offset is not implemented.');}}function
normalizeRow($row,$statement){return$row;}}class
MySqlDriver
extends
NetteX\Object
implements
NetteX\Database\ISupplementalDriver{private$connection;function
__construct(NetteX\Database\Connection$connection,array$options){$this->connection=$connection;$charset=isset($options['charset'])?$options['charset']:'utf8';if($charset){$connection->exec("SET NAMES '$charset'");}if(isset($options['sqlmode'])){$connection->exec("SET sql_mode='$options[sqlmode]'");}$connection->exec("SET time_zone='".date('P')."'");}function
delimite($name){return'`'.str_replace('`','``',$name).'`';}function
formatDateTime(\DateTime$value){return$value->format("'Y-m-d H:i:s'");}function
formatLike($value,$pos){$value=addcslashes(str_replace('\\','\\\\',$value),"\x00\n\r\\'%_");return($pos<=0?"'%":"'").$value.($pos>=0?"%'":"'");}function
applyLimit(&$sql,$limit,$offset){if($limit<0&&$offset<1)return;$sql.=' LIMIT '.($limit<0?'18446744073709551615':(int)$limit).($offset>0?' OFFSET '.(int)$offset:'');}function
normalizeRow($row,$statement){return$row;}}class
OciDriver
extends
NetteX\Object
implements
NetteX\Database\ISupplementalDriver{private$connection;private$fmtDateTime;function
__construct(NetteX\Database\Connection$connection,array$options){$this->connection=$connection;$this->fmtDateTime=isset($options['formatDateTime'])?$options['formatDateTime']:'U';}function
delimite($name){return'"'.str_replace('"','""',$name).'"';}function
formatDateTime(\DateTime$value){return$value->format($this->fmtDateTime);}function
formatLike($value,$pos){throw
new
NetteX\NotImplementedException;}function
applyLimit(&$sql,$limit,$offset){if($offset>0){$sql='SELECT * FROM (SELECT t.*, ROWNUM AS "__rnum" FROM ('.$sql.') t '.($limit>=0?'WHERE ROWNUM <= '.((int)$offset+(int)$limit):'').') WHERE "__rnum" > '.(int)$offset;}elseif($limit>=0){$sql='SELECT * FROM ('.$sql.') WHERE ROWNUM <= '.(int)$limit;}}function
normalizeRow($row,$statement){return$row;}}class
OdbcDriver
extends
NetteX\Object
implements
NetteX\Database\ISupplementalDriver{private$connection;function
__construct(NetteX\Database\Connection$connection,array$options){$this->connection=$connection;}function
delimite($name){return'['.str_replace(array('[',']'),array('[[',']]'),$name).']';}function
formatDateTime(\DateTime$value){return$value->format("#m/d/Y H:i:s#");}function
formatLike($value,$pos){$value=strtr($value,array("'"=>"''",'%'=>'[%]','_'=>'[_]','['=>'[[]'));return($pos<=0?"'%":"'").$value.($pos>=0?"%'":"'");}function
applyLimit(&$sql,$limit,$offset){if($limit>=0){$sql='SELECT TOP '.(int)$limit.' * FROM ('.$sql.')';}if($offset){throw
new
NetteX\InvalidArgumentException('Offset is not implemented in driver odbc.');}}function
normalizeRow($row,$statement){return$row;}}class
PgSqlDriver
extends
NetteX\Object
implements
NetteX\Database\ISupplementalDriver{private$connection;function
__construct(NetteX\Database\Connection$connection,array$options){$this->connection=$connection;}function
delimite($name){return'"'.str_replace('"','""',$name).'"';}function
formatDateTime(\DateTime$value){return$value->format("'Y-m-d H:i:s'");}function
formatLike($value,$pos){throw
new
NetteX\NotImplementedException;}function
applyLimit(&$sql,$limit,$offset){if($limit>=0)$sql.=' LIMIT '.(int)$limit;if($offset>0)$sql.=' OFFSET '.(int)$offset;}function
normalizeRow($row,$statement){return$row;}}class
SqliteDriver
extends
NetteX\Object
implements
NetteX\Database\ISupplementalDriver{private$connection;private$fmtDateTime;function
__construct(NetteX\Database\Connection$connection,array$options){$this->connection=$connection;$this->fmtDateTime=isset($options['formatDateTime'])?$options['formatDateTime']:'U';}function
delimite($name){return'['.strtr($name,'[]','  ').']';}function
formatDateTime(\DateTime$value){return$value->format($this->fmtDateTime);}function
formatLike($value,$pos){$value=addcslashes(substr($this->connection->quote($value),1,-1),'%_\\');return($pos<=0?"'%":"'").$value.($pos>=0?"%'":"'")." ESCAPE '\\'";}function
applyLimit(&$sql,$limit,$offset){if($limit<0&&$offset<1)return;$sql.=' LIMIT '.$limit.($offset>0?' OFFSET '.(int)$offset:'');}function
normalizeRow($row,$statement){return$row;}}class
Sqlite2Driver
extends
SqliteDriver{function
formatLike($value,$pos){throw
new
NetteX\NotSupportedException;}function
normalizeRow($row,$statement){if(!is_object($row)){$iterator=$row;}elseif($row
instanceof\Traversable){$iterator=iterator_to_array($row);}else{$iterator=(array)$row;}foreach($iterator
as$key=>$value){unset($row[$key]);if($key[0]==='['||$key[0]==='"'){$key=substr($key,1,-1);}$row[$key]=$value;}return$row;}}}namespace NetteX\Database\Reflection{use
NetteX;class
DatabaseReflection
extends
NetteX\Object{const
FIELD_TEXT='string',FIELD_BINARY='bin',FIELD_BOOL='bool',FIELD_INTEGER='int',FIELD_FLOAT='float',FIELD_DATETIME='datetime';private$primary;private$foreign;private$table;function
__construct($primary='id',$foreign='%s_id',$table='%s'){$this->primary=$primary;$this->foreign=$foreign;$this->table=$table;}function
getPrimary($table){return
sprintf($this->primary,$table);}function
getReferencingColumn($name,$table){return$this->getReferencedColumn($table,$name);}function
getReferencedColumn($name,$table){if($this->table!=='%s'&&preg_match('(^'.str_replace('%s','(.*)',preg_quote($this->table)).'$)',$name,$match)){$name=$match[1];}return
sprintf($this->foreign,$name,$table);}function
getReferencedTable($name,$table){return
sprintf($this->table,$name,$table);}static
function
detectType($type){static$types,$patterns=array('BYTEA|BLOB|BIN'=>self::FIELD_BINARY,'TEXT|CHAR'=>self::FIELD_TEXT,'YEAR|BYTE|COUNTER|SERIAL|INT|LONG'=>self::FIELD_INTEGER,'CURRENCY|REAL|MONEY|FLOAT|DOUBLE|DECIMAL|NUMERIC|NUMBER'=>self::FIELD_FLOAT,'TIME|DATE'=>self::FIELD_DATETIME,'BOOL|BIT'=>self::FIELD_BOOL);if(!isset($types[$type])){$types[$type]='string';foreach($patterns
as$s=>$val){if(preg_match("#$s#i",$type)){return$types[$type]=$val;}}}return$types[$type];}}}namespace NetteX\Database{use
NetteX;class
Row
extends
NetteX\ArrayHash{function
__construct($statement){$statement->normalizeRow($this);}function
offsetGet($key){if(is_int($key)){$arr=array_values((array)$this);return$arr[$key];}return$this->$key;}}class
SqlLiteral{public$value='';function
__construct($value){$this->value=(string)$value;}}class
SqlPreprocessor
extends
NetteX\Object{private$connection;private$driver;private$params;private$remaining;private$counter;private$arrayMode;function
__construct(Connection$connection){$this->connection=$connection;$this->driver=$connection->getSupplementalDriver();}function
process($sql,$params){$this->params=$params;$this->counter=0;$this->remaining=array();$cmd=strtoupper(substr(ltrim($sql),0,6));$this->arrayMode=$cmd==='INSERT'||$cmd==='REPLAC'?'values':'assoc';$sql=NetteX\StringUtils::replace($sql,'~\'.*?\'|".*?"|:[a-zA-Z0-9_]+:|\?~s',array($this,'callback'));while($this->counter<count($params)){$sql.=' '.$this->formatValue($params[$this->counter++]);}return
array($sql,$this->remaining);}function
callback($m){$m=$m[0];if($m[0]==="'"||$m[0]==='"'){return$m;}elseif($m[0]==='?'){return$this->formatValue($this->params[$this->counter++]);}elseif($m[0]===':'){$s=substr($m,1,-1);return
isset($this->connection->substitutions[$s])?$this->connection->substitutions[$s]:$m;}}private
function
formatValue($value){if(is_string($value)){if(strlen($value)>20){$this->remaining[]=$value;return'?';}else{return$this->connection->quote($value);}}elseif(is_int($value)){return(string)$value;}elseif(is_float($value)){return
rtrim(rtrim(number_format($value,10,'.',''),'0'),'.');}elseif(is_bool($value)){return$value?1:0;}elseif($value===NULL){return'NULL';}elseif(is_array($value)||$value
instanceof\Traversable){$vx=$kx=array();if(isset($value[0])){foreach($value
as$v){$vx[]=$this->formatValue($v);}return
implode(', ',$vx);}elseif($this->arrayMode==='values'){$this->arrayMode='multi';foreach($value
as$k=>$v){$kx[]=$this->driver->delimite($k);$vx[]=$this->formatValue($v);}return'('.implode(', ',$kx).') VALUES ('.implode(', ',$vx).')';}elseif($this->arrayMode==='assoc'){foreach($value
as$k=>$v){$vx[]=$this->driver->delimite($k).'='.$this->formatValue($v);}return
implode(', ',$vx);}elseif($this->arrayMode==='multi'){foreach($value
as$k=>$v){$vx[]=$this->formatValue($v);}return', ('.implode(', ',$vx).')';}}elseif($value
instanceof\DateTime){return$this->driver->formatDateTime($value);}elseif($value
instanceof
SqlLiteral){return$value->value;}else{$this->remaining[]=$value;return'?';}}}use
PDO;use
NetteX\ObjectMixin;if(class_exists('PDO')){class
Statement
extends\PDOStatement{private$connection;public$time;private$types;protected
function
__construct(Connection$connection){$this->connection=$connection;$this->setFetchMode(PDO::FETCH_CLASS,'NetteX\Database\Row',array($this));}function
getConnection(){return$this->connection;}function
execute($params=array()){static$types=array('boolean'=>PDO::PARAM_BOOL,'integer'=>PDO::PARAM_INT,'resource'=>PDO::PARAM_LOB,'NULL'=>PDO::PARAM_NULL);foreach($params
as$key=>$value){$type=gettype($value);$this->bindValue(is_int($key)?$key+1:$key,$value,isset($types[$type])?$types[$type]:PDO::PARAM_STR);}$time=microtime(TRUE);try{parent::execute();}catch(\PDOException$e){$e->queryString=$this->queryString;throw$e;}$this->time=microtime(TRUE)-$time;$this->connection->__call('onQuery',array($this,$params));return$this;}function
fetchPairs(){return$this->fetchAll(PDO::FETCH_KEY_PAIR);}function
normalizeRow($row){if($this->types===NULL){try{$this->types=array();foreach($row
as$key=>$foo){$type=$this->getColumnMeta(count($this->types));if(isset($type['native_type'])){$this->types[$key]=Reflection\DatabaseReflection::detectType($type['native_type']);}}}catch(\PDOException$e){}}foreach($this->types
as$key=>$type){$value=$row[$key];if($value===NULL||$value===FALSE||$type===Reflection\DatabaseReflection::FIELD_TEXT){}elseif($type===Reflection\DatabaseReflection::FIELD_INTEGER){$row[$key]=is_float($tmp=$value*1)?$value:$tmp;}elseif($type===Reflection\DatabaseReflection::FIELD_FLOAT){$row[$key]=(string)($tmp=(float)$value)===$value?$tmp:$value;}elseif($type===Reflection\DatabaseReflection::FIELD_BOOL){$row[$key]=((bool)$value)&&$value!=='f'&&$value!=='F';}}return$this->connection->getSupplementalDriver()->normalizeRow($row,$this);}function
dump(){echo"\n<table class=\"dump\">\n<caption>".htmlSpecialChars($this->queryString)."</caption>\n";if(!$this->columnCount()){echo"\t<tr>\n\t\t<th>Affected rows:</th>\n\t\t<td>",$this->rowCount(),"</td>\n\t</tr>\n</table>\n";return;}$i=0;foreach($this
as$row){if($i===0){echo"<thead>\n\t<tr>\n\t\t<th>#row</th>\n";foreach($row
as$col=>$foo){echo"\t\t<th>".htmlSpecialChars($col)."</th>\n";}echo"\t</tr>\n</thead>\n<tbody>\n";}echo"\t<tr>\n\t\t<th>",$i,"</th>\n";foreach($row
as$col){echo"\t\t<td>",htmlSpecialChars($col),"</td>\n";}echo"\t</tr>\n";$i++;}if($i===0){echo"\t<tr>\n\t\t<td><em>empty result set</em></td>\n\t</tr>\n</table>\n";}else{echo"</tbody>\n</table>\n";}}static
function
getReflection(){return
new
NetteX\Reflection\ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}}}namespace NetteX\Database\Table{use
NetteX;class
ActiveRow
extends
NetteX\Object
implements\IteratorAggregate,\ArrayAccess{protected$table;protected$data;private$modified=array();function
__construct(array$data,Selection$table){$this->data=$data;$this->table=$table;}function
__toString(){return(string)$this[$this->table->primary];}function
toArray(){$this->access(NULL);return$this->data;}function
ref($name){$referenced=$this->table->getReferencedTable($name,$column);if(isset($referenced[$this[$column]])){$res=$referenced[$this[$column]];return$res;}}function
related($table){$referencing=$this->table->getReferencingTable($table);$referencing->active=$this[$this->table->primary];return$referencing;}function
update($data=NULL){if($data===NULL){$data=$this->modified;}return$this->table->connection->table($this->table->name)->where($this->table->primary,$this[$this->table->primary])->update($data);}function
delete(){return$this->table->connection->table($this->table->name)->where($this->table->primary,$this[$this->table->primary])->delete();}function
getIterator(){$this->access(NULL);return
new\ArrayIterator($this->data);}function
offsetSet($key,$value){$this->__set($key,$value);}function
offsetGet($key){return$this->__get($key);}function
offsetExists($key){return$this->__isset($key);}function
offsetUnset($key){$this->__unset($key);}function
__set($key,$value){$this->data[$key]=$value;$this->modified[$key]=$value;}function&__get($key){if(array_key_exists($key,$this->data)){$this->access($key);return$this->data[$key];}$column=$this->table->connection->databaseReflection->getReferencedColumn($key,$this->table->name);if(array_key_exists($column,$this->data)){$value=$this->data[$column];$referenced=$this->table->getReferencedTable($key);$ret=isset($referenced[$value])?$referenced[$value]:NULL;return$ret;}$this->access($key);if(array_key_exists($key,$this->data)){return$this->data[$key];}else{$this->access($key,TRUE);$this->access($column);if(array_key_exists($column,$this->data)){$value=$this->data[$column];$referenced=$this->table->getReferencedTable($key);$ret=isset($referenced[$value])?$referenced[$value]:NULL;}else{$this->access($column,TRUE);trigger_error("Unknown column $key",E_USER_WARNING);$ret=NULL;}return$ret;}}function
__isset($key){$this->access($key);$return=array_key_exists($key,$this->data);if(!$return){$this->access($key,TRUE);}return$return;}function
__unset($key){unset($this->data[$key]);unset($this->modified[$key]);}function
access($key,$delete=FALSE){if($this->table->connection->cache&&$this->table->access($key,$delete)){$this->data=$this->table[$this->data[$this->table->primary]]->data;}}}use
PDO;class
Selection
extends
NetteX\Object
implements\Iterator,\ArrayAccess,\Countable{public$connection;public$name;public$primary;protected$rows;protected$data;protected$select=array();protected$where=array();protected$conditions=array();protected$parameters=array();protected$order=array();protected$limit=NULL;protected$offset=NULL;protected$group='';protected$having='';protected$referenced=array();protected$referencing=array();protected$aggregation=array();protected$accessed;protected$prevAccessed;protected$keys=array();protected$delimitedName;protected$delimitedPrimary;function
__construct($table,NetteX\Database\Connection$connection){$this->name=$table;$this->connection=$connection;$this->primary=$this->getPrimary($table);$this->delimitedName=$connection->getSupplementalDriver()->delimite($this->name);$this->delimitedPrimary=$connection->getSupplementalDriver()->delimite($this->primary);}function
__destruct(){if($this->connection->cache&&!$this->select&&$this->rows!==NULL){$accessed=$this->accessed;if(is_array($accessed)){$accessed=array_filter($accessed);}$this->connection->cache[array(__CLASS__,$this->name,$this->conditions)]=$accessed;}$this->rows=NULL;}function
get($key){$clone=clone$this;$clone->where($this->delimitedPrimary,$key);return$clone->fetch();}function
select($columns){$this->__destruct();$this->select[]=$this->tryDelimite($columns);return$this;}function
find($key){return$this->where($this->delimitedPrimary,$key);}function
where($condition,$parameters=array()){if(is_array($condition)){foreach($condition
as$key=>$val){$this->where($key,$val);}return$this;}$this->__destruct();$this->conditions[]=$condition=$this->tryDelimite($condition);$args=func_num_args();if($args!==2||strpbrk($condition,'?:')){if($args!==2||!is_array($parameters)){$parameters=func_get_args();array_shift($parameters);}$this->parameters=array_merge($this->parameters,$parameters);}elseif($parameters===NULL){$condition.=' IS NULL';}elseif($parameters
instanceof
Selection){$clone=clone$parameters;if(!$clone->select){$clone->select=array($this->getPrimary($clone->name));}if($this->connection->getAttribute(PDO::ATTR_DRIVER_NAME)!=='mysql'){$condition.=" IN ($clone)";}else{$in=array();foreach($clone
as$row){$this->parameters[]=array_values(iterator_to_array($row));$in[]=(count($row)===1?'?':'(?)');}$condition.=' IN ('.($in?implode(', ',$in):'NULL').')';}}elseif(!is_array($parameters)){$condition.=' = ?';$this->parameters[]=$parameters;}else{if($parameters){$condition.=" IN (?)";$this->parameters[]=$parameters;}else{$condition.=" IN (NULL)";}}$this->where[]=$condition;return$this;}function
order($columns){$this->rows=NULL;$this->order[]=$this->tryDelimite($columns);return$this;}function
limit($limit,$offset=NULL){$this->rows=NULL;$this->limit=$limit;$this->offset=$offset;return$this;}function
group($columns,$having=''){$this->__destruct();$this->group=$this->tryDelimite($columns);$this->having=$having;return$this;}function
aggregation($function){$join=$this->createJoins(implode(',',$this->conditions),TRUE)+$this->createJoins($function);$query="SELECT $function FROM $this->delimitedName".implode($join);if($this->where){$query.=' WHERE ('.implode(') AND (',$this->where).')';}foreach($this->query($query)->fetch()as$val){return$val;}}function
count($column=''){if(!$column){$this->execute();return
count($this->data);}return$this->aggregation("COUNT({$this->tryDelimite($column)})");}function
min($column){return$this->aggregation("MIN({$this->tryDelimite($column)})");}function
max($column){return$this->aggregation("MAX({$this->tryDelimite($column)})");}function
sum($column){return$this->aggregation("SUM({$this->tryDelimite($column)})");}function
getSql(){$join=$this->createJoins(implode(',',$this->conditions),TRUE)+$this->createJoins(implode(',',$this->select).",$this->group,$this->having,".implode(',',$this->order));if($this->rows===NULL&&$this->connection->cache&&!is_string($this->prevAccessed)){$this->accessed=$this->prevAccessed=$this->connection->cache[array(__CLASS__,$this->name,$this->conditions)];}$prefix=$join?"$this->delimitedName.":'';if($this->select){$cols=implode(', ',$this->select);}elseif($this->prevAccessed){$cols=$prefix.implode(', '.$prefix,array_map(array($this->connection->getSupplementalDriver(),'delimite'),array_keys($this->prevAccessed)));}else{$cols=$prefix.'*';}return"SELECT{$this->topString()} $cols FROM $this->delimitedName".implode($join).$this->whereString();}protected
function
createJoins($val,$inner=FALSE){$supplementalDriver=$this->connection->getSupplementalDriver();$joins=array();preg_match_all('~\\b(\\w+)\\.(\\w+)(\\s+IS\\b|\\s*<=>)?~i',$val,$matches,PREG_SET_ORDER);foreach($matches
as$match){$name=$match[1];if($name!==$this->name){$table=$this->connection->databaseReflection->getReferencedTable($name,$this->name);$column=$this->connection->databaseReflection->getReferencedColumn($name,$this->name);$primary=$this->getPrimary($table);$joins[$name]=' '.(!isset($joins[$name])&&$inner&&!isset($match[3])?'INNER':'LEFT').' JOIN '.$supplementalDriver->delimite($table).($table!==$name?' AS '.$supplementalDriver->delimite($name):'')." ON $this->delimitedName.".$supplementalDriver->delimite($column).' = '.$supplementalDriver->delimite($name).'.'.$supplementalDriver->delimite($primary);}}return$joins;}protected
function
execute(){if($this->rows!==NULL){return;}try{$result=$this->query($this->getSql());}catch(\PDOException$exception){if(!$this->select&&$this->prevAccessed){$this->prevAccessed='';$this->accessed=array();$result=$this->query($this->getSql());}else{throw$exception;}}$this->rows=array();$result->setFetchMode(PDO::FETCH_ASSOC);foreach($result
as$key=>$row){$row=$result->normalizeRow($row);$this->rows[isset($row[$this->primary])?$row[$this->primary]:$key]=new
ActiveRow($row,$this);}$this->data=$this->rows;if(isset($row[$this->primary])&&!is_string($this->accessed)){$this->accessed[$this->primary]=TRUE;}}protected
function
whereString(){$return='';$driver=$this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);$where=$this->where;if($this->limit!==NULL&&$driver==='oci'){$where[]=($this->offset?"rownum > $this->offset AND ":'').'rownum <= '.($this->limit+$this->offset);}if($where){$return.=' WHERE ('.implode(') AND (',$where).')';}if($this->group){$return.=" GROUP BY $this->group";}if($this->having){$return.=" HAVING $this->having";}if($this->order){$return.=' ORDER BY '.implode(', ',$this->order);}if($this->limit!==NULL&&$driver!=='oci'&&$driver!=='dblib'){$return.=" LIMIT $this->limit";if($this->offset!==NULL){$return.=" OFFSET $this->offset";}}return$return;}protected
function
topString(){if($this->limit!==NULL&&$this->connection->getAttribute(PDO::ATTR_DRIVER_NAME)==='dblib'){return" TOP ($this->limit)";}return'';}protected
function
tryDelimite($s){return
preg_match('#^[a-z_][a-z0-9_.]*$#i',$s)?implode('.',array_map(array($this->connection->getSupplementalDriver(),'delimite'),explode('.',$s))):$s;}protected
function
query($query){return$this->connection->queryArgs($query,$this->parameters);}function
access($key,$delete=FALSE){if($delete){if(is_array($this->accessed)){$this->accessed[$key]=FALSE;}return
FALSE;}if($key===NULL){$this->accessed='';}elseif(!is_string($this->accessed)){$this->accessed[$key]=TRUE;}if(!$this->select&&$this->prevAccessed&&($key===NULL||!isset($this->prevAccessed[$key]))){$this->prevAccessed='';$this->rows=NULL;return
TRUE;}return
FALSE;}function
insert($data){if($data
instanceof
Selection){$data=$data->getSql();}elseif($data
instanceof\Traversable){$data=iterator_to_array($data);}$return=$this->connection->query("INSERT INTO $this->delimitedName",$data);$this->rows=NULL;if(!is_array($data)){return$return->rowCount();}if(!isset($data[$this->primary])&&($id=$this->connection->lastInsertId())){$data[$this->primary]=$id;}return
new
ActiveRow($data,$this);}function
update($data){if($data
instanceof\Traversable){$data=iterator_to_array($data);}elseif(!is_array($data)){throw
new
NetteX\InvalidArgumentException;}if(!$data){return
0;}return$this->connection->queryArgs('UPDATE'.$this->topString()." $this->delimitedName SET ?".$this->whereString(),array_merge(array($data),$this->parameters))->rowCount();}function
delete(){return$this->query('DELETE'.$this->topString()." FROM $this->delimitedName".$this->whereString())->rowCount();}function
getReferencedTable($name,&$column=NULL){$column=$this->connection->databaseReflection->getReferencedColumn($name,$this->name);$referenced=&$this->referenced[$name];if($referenced===NULL){$keys=array();foreach($this->rows
as$row){if($row[$column]!==NULL){$keys[$row[$column]]=NULL;}}if($keys){$table=$this->connection->databaseReflection->getReferencedTable($name,$this->name);$referenced=new
Selection($table,$this->connection);$referenced->where($table.'.'.$this->getPrimary($table),array_keys($keys));}else{$referenced=array();}}return$referenced;}function
getReferencingTable($table){$column=$this->connection->databaseReflection->getReferencingColumn($table,$this->name);$referencing=new
GroupedSelection($table,$this,$column);$referencing->where("$table.$column",array_keys((array)$this->rows));return$referencing;}private
function
getPrimary($table){return$this->connection->databaseReflection->getPrimary($table);}function
rewind(){$this->execute();$this->keys=array_keys($this->data);reset($this->keys);}function
current(){return$this->data[current($this->keys)];}function
key(){return
current($this->keys);}function
next(){next($this->keys);}function
valid(){return
current($this->keys)!==FALSE;}function
offsetSet($key,$value){$this->execute();$this->data[$key]=$value;}function
offsetGet($key){$this->execute();return$this->data[$key];}function
offsetExists($key){$this->execute();return
isset($this->data[$key]);}function
offsetUnset($key){$this->execute();unset($this->data[$key]);}function
fetch(){$this->execute();$return=current($this->data);next($this->data);return$return;}function
fetchPairs($key,$value=''){$return=array();foreach($this
as$row){$return[$row[$key]]=($value!==''?$row[$value]:$row);}return$return;}}class
GroupedSelection
extends
Selection{private$refTable;private$column;private$delimitedColumn;public$active;function
__construct($name,Selection$refTable,$column){parent::__construct($name,$refTable->connection);$this->refTable=$refTable;$this->through($column);}function
through($column){$this->column=$column;$this->delimitedColumn=$this->refTable->connection->getSupplementalDriver()->delimite($this->column);return$this;}function
select($columns){if(!$this->select){$this->select[]="$this->delimitedName.$this->delimitedColumn";}return
parent::select($columns);}function
order($columns){if(!$this->order){$this->order[]="$this->delimitedName.$this->delimitedColumn".(preg_match('~\\bDESC$~i',$columns)?' DESC':'');}return
parent::order($columns);}function
aggregation($function){$join=$this->createJoins(implode(',',$this->conditions),TRUE)+$this->createJoins($function);$column=($join?"$this->table.":'').$this->column;$query="SELECT $function, $this->delimitedColumn FROM $this->delimitedName".implode($join);if($this->where){$query.=' WHERE ('.implode(') AND (',$this->where).')';}$query.=" GROUP BY $this->delimitedColumn";$aggregation=&$this->refTable->aggregation[$query];if($aggregation===NULL){$aggregation=array();foreach($this->query($query,$this->parameters)as$row){$aggregation[$row[$this->column]]=$row;}}foreach($aggregation[$this->active]as$val){return$val;}}function
insert($data){if($data
instanceof\Traversable&&!$data
instanceof
Selection){$data=iterator_to_array($data);}if(is_array($data)){$data[$this->column]=$this->active;}return
parent::insert($data);}function
update($data){$where=$this->where;$this->where[0]="$this->delimitedColumn = ".$this->connection->quote($this->active);$return=parent::update($data);$this->where=$where;return$return;}function
delete(){$where=$this->where;$this->where[0]="$this->delimitedColumn = ".$this->connection->quote($this->active);$return=parent::delete();$this->where=$where;return$return;}protected
function
execute(){if($this->rows!==NULL){return;}$referencing=&$this->refTable->referencing[$this->getSql()];if($referencing===NULL){$limit=$this->limit;$rows=count($this->refTable->rows);if($this->limit&&$rows>1){$this->limit=NULL;}parent::execute();$this->limit=$limit;$referencing=array();$offset=array();foreach($this->rows
as$key=>$row){$ref=&$referencing[$row[$this->column]];$skip=&$offset[$row[$this->column]];if($limit===NULL||$rows<=1||(count($ref)<$limit&&$skip>=$this->offset)){$ref[$key]=$row;}else{unset($this->rows[$key]);}$skip++;unset($ref,$skip);}}$this->data=&$referencing[$this->active];if($this->data===NULL){$this->data=array();}}}}namespace NetteX\DI{use
NetteX;class
AmbiguousServiceException
extends\Exception{}use
NetteX\Environment;use
NetteX\Config\Config;class
Configurator
extends
NetteX\Object{public$defaultConfigFile='%appDir%/config.neon';public$defaultServices=array('NetteX\\Application\\Application'=>array(__CLASS__,'createApplication'),'NetteX\\Web\\HttpContext'=>'NetteX\Http\Context','NetteX\\Web\\IHttpRequest'=>array(__CLASS__,'createHttpRequest'),'NetteX\\Web\\IHttpResponse'=>'NetteX\Http\Response','NetteX\\Web\\IUser'=>'NetteX\Http\User','NetteX\\Caching\\ICacheStorage'=>array(__CLASS__,'createCacheStorage'),'NetteX\\Caching\\ICacheJournal'=>array(__CLASS__,'createCacheJournal'),'NetteX\\Mail\\IMailer'=>array(__CLASS__,'createMailer'),'NetteX\\Web\\Session'=>'NetteX\Http\Session','NetteX\\Loaders\\RobotLoader'=>array(__CLASS__,'createRobotLoader'));function
detect($name){switch($name){case'environment':if($this->detect('console')){return
Environment::CONSOLE;}else{return
Environment::getMode('production')?Environment::PRODUCTION:Environment::DEVELOPMENT;}case'production':if(PHP_SAPI==='cli'){return
FALSE;}elseif(isset($_SERVER['SERVER_ADDR'])||isset($_SERVER['LOCAL_ADDR'])){$addrs=array();if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){$addrs=preg_split('#,\s*#',$_SERVER['HTTP_X_FORWARDED_FOR']);}if(isset($_SERVER['REMOTE_ADDR'])){$addrs[]=$_SERVER['REMOTE_ADDR'];}$addrs[]=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['LOCAL_ADDR'];foreach($addrs
as$addr){$oct=explode('.',$addr);if($addr!=='::1'&&(count($oct)!==4||($oct[0]!=='10'&&$oct[0]!=='127'&&($oct[0]!=='172'||$oct[1]<16||$oct[1]>31)&&($oct[0]!=='169'||$oct[1]!=='254')&&($oct[0]!=='192'||$oct[1]!=='168')))){return
TRUE;}}return
FALSE;}else{return
TRUE;}case'console':return
PHP_SAPI==='cli';default:return
NULL;}}function
loadConfig($file){$name=Environment::getName();if($file
instanceof
Config){$config=$file;$file=NULL;}else{if($file===NULL){$file=$this->defaultConfigFile;}$file=Environment::expand($file);if(!is_file($file)){$file=preg_replace('#\.neon$#','.ini',$file);}$config=Config::fromFile($file,$name);}if($config->variable
instanceof
Config){foreach($config->variable
as$key=>$value){Environment::setVariable($key,$value);}}$iterator=new\RecursiveIteratorIterator($config);foreach($iterator
as$key=>$value){$tmp=$iterator->getDepth()?$iterator->getSubIterator($iterator->getDepth()-1)->current():$config;$tmp[$key]=Environment::expand($value);}$runServices=array();$context=Environment::getContext();if($config->service
instanceof
Config){foreach($config->service
as$key=>$value){$key=strtr($key,'-','\\');if(is_string($value)){$context->removeService($key);$context->addService($key,$value);}else{if($value->factory||isset($this->defaultServices[$key])){$context->removeService($key);$context->addService($key,$value->factory?$value->factory:$this->defaultServices[$key],isset($value->singleton)?$value->singleton:TRUE,(array)$value->option);}else{throw
new
NetteX\InvalidStateException("Factory method is not specified for service $key.");}if($value->run){$runServices[]=$key;}}}}if(!$config->php){$config->php=$config->set;unset($config->set);}if($config->php
instanceof
Config){if(PATH_SEPARATOR!==';'&&isset($config->php->include_path)){$config->php->include_path=str_replace(';',PATH_SEPARATOR,$config->php->include_path);}foreach(clone$config->php
as$key=>$value){if($value
instanceof
Config){unset($config->php->$key);foreach($value
as$k=>$v){$config->php->{"$key.$k"}=$v;}}}foreach($config->php
as$key=>$value){$key=strtr($key,'-','.');if(!is_scalar($value)){throw
new
NetteX\InvalidStateException("Configuration value for directive '$key' is not scalar.");}if($key==='date.timezone'){date_default_timezone_set($value);}if(function_exists('ini_set')){ini_set($key,$value);}else{switch($key){case'include_path':set_include_path($value);break;case'iconv.internal_encoding':iconv_set_encoding('internal_encoding',$value);break;case'mbstring.internal_encoding':mb_internal_encoding($value);break;case'date.timezone':date_default_timezone_set($value);break;case'error_reporting':error_reporting($value);break;case'ignore_user_abort':ignore_user_abort($value);break;case'max_execution_time':set_time_limit($value);break;default:if(ini_get($key)!=$value){throw
new
NetteX\NotSupportedException('Required function ini_set() is disabled.');}}}}}if($config->const
instanceof
Config){foreach($config->const
as$key=>$value){define($key,$value);}}if(isset($config->mode)){foreach($config->mode
as$mode=>$state){Environment::setMode($mode,$state);}}foreach($runServices
as$name){$context->getService($name);}return$config;}function
createContext(){$context=new
Context;foreach($this->defaultServices
as$name=>$service){$context->addService($name,$service);}return$context;}static
function
createApplication(array$options=NULL){if(Environment::getVariable('baseUri',NULL)===NULL){Environment::setVariable('baseUri',Environment::getHttpRequest()->getUri()->getBaseUri());}$context=clone
Environment::getContext();$context->addService('NetteX\\Application\\IRouter','NetteX\Application\Routers\RouteList');if(!$context->hasService('NetteX\\Application\\IPresenterFactory')){$context->addService('NetteX\\Application\\IPresenterFactory',function()use($context){return
new
NetteX\Application\PresenterFactory(Environment::getVariable('appDir'),$context);});}$class=isset($options['class'])?$options['class']:'NetteX\Application\Application';$application=new$class;$application->setContext($context);$application->catchExceptions=Environment::isProduction();return$application;}static
function
createHttpRequest(){$factory=new
NetteX\Http\RequestFactory;$factory->setEncoding('UTF-8');return$factory->createHttpRequest();}static
function
createCacheStorage(){$dir=Environment::getVariable('tempDir').'/cache';umask(0000);@mkdir($dir,0777);return
new
NetteX\Caching\Storages\FileStorage($dir,Environment::getService('NetteX\\Caching\\ICacheJournal'));}static
function
createCacheJournal(){return
new
NetteX\Caching\Storages\FileJournal(Environment::getVariable('tempDir'));}static
function
createMailer(array$options=NULL){if(empty($options['smtp'])){return
new
NetteX\Mail\SendmailMailer;}else{return
new
NetteX\Mail\SmtpMailer($options);}}static
function
createRobotLoader(array$options=NULL){$loader=new
NetteX\Loaders\RobotLoader;$loader->autoRebuild=isset($options['autoRebuild'])?$options['autoRebuild']:!Environment::isProduction();$loader->setCacheStorage(Environment::getService('NetteX\\Caching\\ICacheStorage'));if(isset($options['directory'])){$loader->addDirectory($options['directory']);}else{foreach(array('appDir','libsDir')as$var){if($dir=Environment::getVariable($var,NULL)){$loader->addDirectory($dir);}}}$loader->register();return$loader;}}class
Context
extends
NetteX\FreezableObject
implements
IContext{private$registry=array();private$factories=array();function
addService($name,$service,$singleton=TRUE,array$options=NULL){$this->updating();if(!is_string($name)||$name===''){throw
new
NetteX\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}$lower=strtolower($name);if(isset($this->registry[$lower])){throw
new
AmbiguousServiceException("Service named '$name' has already been registered.");}if($service
instanceof
self){$this->registry[$lower]=&$service->registry[$lower];$this->factories[$lower]=&$service->factories[$lower];}elseif(is_object($service)&&!($service
instanceof\Closure||$service
instanceof
NetteX\Callback)){if(!$singleton||$options){throw
new
NetteX\InvalidArgumentException("Service named '$name' is an instantiated object and must therefore be singleton without options.");}$this->registry[$lower]=$service;}else{if(!$service){throw
new
NetteX\InvalidArgumentException("Service named '$name' is empty.");}$this->factories[$lower]=array($service,$singleton,$options);$this->registry[$lower]=&$this->factories[$lower][3];}return$this;}function
removeService($name){$this->updating();if(!is_string($name)||$name===''){throw
new
NetteX\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}$lower=strtolower($name);unset($this->registry[$lower],$this->factories[$lower]);}function
getService($name,array$options=NULL){if(!is_string($name)||$name===''){throw
new
NetteX\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}$lower=strtolower($name);if(isset($this->registry[$lower])){if($options){throw
new
NetteX\InvalidArgumentException("Service named '$name' is singleton and therefore can not have options.");}return$this->registry[$lower];}elseif(isset($this->factories[$lower])){list($factory,$singleton,$defOptions)=$this->factories[$lower];if($singleton&&$options){throw
new
NetteX\InvalidArgumentException("Service named '$name' is singleton and therefore can not have options.");}elseif($defOptions){$options=$options?$options+$defOptions:$defOptions;}if(is_string($factory)&&strpos($factory,':')===FALSE){if(!class_exists($factory)){throw
new
AmbiguousServiceException("Cannot instantiate service '$name', class '$factory' not found.");}$service=new$factory;if($options){if(method_exists($service,'setOptions')){$service->setOptions($options);}else{throw
new
NetteX\InvalidStateException("Unable to set options, method $factory::setOptions() is missing.");}}}else{$factory=callback($factory);if(!$factory->isCallable()){throw
new
NetteX\InvalidStateException("Cannot instantiate service '$name', handler '$factory' is not callable.");}$service=$factory($options);if(!is_object($service)){throw
new
AmbiguousServiceException("Cannot instantiate service '$name', value returned by '$factory' is not object.");}}if($singleton){$this->registry[$lower]=$service;unset($this->factories[$lower]);}return$service;}else{throw
new
NetteX\InvalidStateException("Service '$name' not found.");}}function
hasService($name,$created=FALSE){if(!is_string($name)||$name===''){throw
new
NetteX\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}$lower=strtolower($name);return
isset($this->registry[$lower])||(!$created&&isset($this->factories[$lower]));}}}namespace NetteX\Diagnostics{use
NetteX;final
class
Debugger{public
static$productionMode;public
static$consoleMode;public
static$time;private
static$firebugDetected;private
static$ajaxDetected;public
static$source;public
static$maxDepth=3;public
static$maxLen=150;public
static$showLocation=FALSE;const
DEVELOPMENT=FALSE,PRODUCTION=TRUE,DETECT=NULL;public
static$strictMode=FALSE;public
static$scream=FALSE;public
static$onFatalError=array();public
static$logDirectory;public
static$email;public
static$mailer=array(__CLASS__,'defaultMailer');public
static$emailSnooze=172800;public
static$editor='editor://open/?file=%file&line=%line';private
static$enabled=FALSE;private
static$lastError=FALSE;public
static$showBar=TRUE;private
static$panels=array();private
static$errors;const
DEBUG='debug',INFO='info',WARNING='warning',ERROR='error',CRITICAL='critical';final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
_init(){self::$time=microtime(TRUE);self::$consoleMode=PHP_SAPI==='cli';self::$productionMode=self::DETECT;if(self::$consoleMode){self::$source=empty($_SERVER['argv'])?'cli':'cli: '.implode(' ',$_SERVER['argv']);}else{self::$firebugDetected=isset($_SERVER['HTTP_X_FIRELOGGER']);self::$ajaxDetected=isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&$_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';if(isset($_SERVER['REQUEST_URI'])){self::$source=(isset($_SERVER['HTTPS'])&&strcasecmp($_SERVER['HTTPS'],'off')?'https://':'http://').(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'')).$_SERVER['REQUEST_URI'];}}$tab=array('NetteX\Diagnostics\Helpers','renderTab');$panel=array('NetteX\Diagnostics\Helpers','renderPanel');self::addPanel(new
Panel('time',$tab,$panel));self::addPanel(new
Panel('memory',$tab,$panel));self::addPanel($tmp=new
Panel('errors',$tab,$panel));$tmp->data=&self::$errors;self::addPanel(new
Panel('dumps',$tab,$panel));}static
function
enable($mode=NULL,$logDirectory=NULL,$email=NULL){error_reporting(E_ALL|E_STRICT);if(is_bool($mode)){self::$productionMode=$mode;}elseif(is_string($mode)){$mode=preg_split('#[,\s]+#',"$mode 127.0.0.1 ::1");}if(is_array($mode)){self::$productionMode=!isset($_SERVER['REMOTE_ADDR'])||!in_array($_SERVER['REMOTE_ADDR'],$mode,TRUE);}if(self::$productionMode===self::DETECT){if(class_exists('NetteX\Environment')){self::$productionMode=NetteX\Environment::isProduction();}elseif(isset($_SERVER['SERVER_ADDR'])||isset($_SERVER['LOCAL_ADDR'])){$addrs=array();if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){$addrs=preg_split('#,\s*#',$_SERVER['HTTP_X_FORWARDED_FOR']);}if(isset($_SERVER['REMOTE_ADDR'])){$addrs[]=$_SERVER['REMOTE_ADDR'];}$addrs[]=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['LOCAL_ADDR'];self::$productionMode=FALSE;foreach($addrs
as$addr){$oct=explode('.',$addr);if($addr!=='::1'&&(count($oct)!==4||($oct[0]!=='10'&&$oct[0]!=='127'&&($oct[0]!=='172'||$oct[1]<16||$oct[1]>31)&&($oct[0]!=='169'||$oct[1]!=='254')&&($oct[0]!=='192'||$oct[1]!=='168')))){self::$productionMode=TRUE;break;}}}else{self::$productionMode=!self::$consoleMode;}}if(is_string($logDirectory)){self::$logDirectory=realpath($logDirectory);if(self::$logDirectory===FALSE){throw
new
NetteX\DirectoryNotFoundException("Directory '$logDirectory' is not found.");}}elseif($logDirectory===FALSE){self::$logDirectory=FALSE;}else{self::$logDirectory=defined('APP_DIR')?APP_DIR.'/../log':getcwd().'/log';}if(self::$logDirectory){ini_set('error_log',self::$logDirectory.'/php_error.log');}if(function_exists('ini_set')){ini_set('display_errors',!self::$productionMode);ini_set('html_errors',FALSE);ini_set('log_errors',FALSE);}elseif(ini_get('display_errors')!=!self::$productionMode&&ini_get('display_errors')!==(self::$productionMode?'stderr':'stdout')){throw
new
NetteX\NotSupportedException('Function ini_set() must be enabled.');}if($email){if(!is_string($email)){throw
new
NetteX\InvalidArgumentException('Email address must be a string.');}self::$email=$email;}if(!defined('E_DEPRECATED')){define('E_DEPRECATED',8192);}if(!defined('E_USER_DEPRECATED')){define('E_USER_DEPRECATED',16384);}if(!self::$enabled){register_shutdown_function(array(__CLASS__,'_shutdownHandler'));set_exception_handler(array(__CLASS__,'_exceptionHandler'));set_error_handler(array(__CLASS__,'_errorHandler'));self::$enabled=TRUE;}}static
function
isEnabled(){return
self::$enabled;}static
function
log($message,$priority=self::INFO){if(self::$logDirectory===FALSE){return;}elseif(!self::$logDirectory){throw
new
NetteX\InvalidStateException('Logging directory is not specified in NetteX\Diagnostics\Debugger::$logDirectory.');}elseif(!is_dir(self::$logDirectory)){throw
new
NetteX\DirectoryNotFoundException("Directory '".self::$logDirectory."' is not found or is not directory.");}if($message
instanceof\Exception){$exception=$message;$message="PHP Fatal error: ".($message
instanceof
NetteX\FatalErrorException?$exception->getMessage():"Uncaught exception ".get_class($exception)." with message '".$exception->getMessage()."'")." in ".$exception->getFile().":".$exception->getLine();$hash=md5($exception);$exceptionFilename="exception ".@date('Y-m-d H-i-s')." $hash.html";foreach(new\DirectoryIterator(self::$logDirectory)as$entry){if(strpos($entry,$hash)){$exceptionFilename=NULL;break;}}}error_log(@date('[Y-m-d H-i-s] ').trim($message).(self::$source?'  @  '.self::$source:'').(!empty($exceptionFilename)?'  @@  '.$exceptionFilename:'').PHP_EOL,3,self::$logDirectory.'/'.strtolower($priority).'.log');if(($priority===self::ERROR||$priority===self::CRITICAL)&&self::$email&&@filemtime(self::$logDirectory.'/email-sent')+self::$emailSnooze<time()&&@file_put_contents(self::$logDirectory.'/email-sent','sent')){call_user_func(self::$mailer,$message);}if(!empty($exceptionFilename)&&$logHandle=@fopen(self::$logDirectory.'/'.$exceptionFilename,'w')){ob_start();ob_start(function($buffer)use($logHandle){fwrite($logHandle,$buffer);},1);Helpers::renderBlueScreen($exception);ob_end_flush();ob_end_clean();fclose($logHandle);}}static
function
_shutdownHandler(){static$types=array(E_ERROR=>1,E_CORE_ERROR=>1,E_COMPILE_ERROR=>1,E_PARSE=>1);$error=error_get_last();if(isset($types[$error['type']])){self::_exceptionHandler(new
NetteX\FatalErrorException($error['message'],0,$error['type'],$error['file'],$error['line'],NULL));}if(self::$showBar&&!self::$productionMode&&!self::$ajaxDetected&&!self::$consoleMode&&!preg_match('#^Content-Type: (?!text/html)#im',implode("\n",headers_list()))){Helpers::renderDebugBar(self::$panels);}}static
function
_exceptionHandler(\Exception$exception){if(!headers_sent()){header('HTTP/1.1 500 Internal Server Error');}$htmlMode=!self::$ajaxDetected&&!preg_match('#^Content-Type: (?!text/html)#im',implode("\n",headers_list()));try{if(self::$productionMode){self::log($exception,self::ERROR);if(self::$consoleMode){echo"ERROR: the server encountered an internal error and was unable to complete your request.\n";}elseif($htmlMode){?>
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name=robots content=noindex><meta name=generator content="NetteX Framework">
<style>body{color:#333;background:white;width:500px;margin:100px auto}h1{font:bold 47px/1.5 sans-serif;margin:.6em 0}p{font:21px/1.5 Georgia,serif;margin:1.5em 0}small{font-size:70%;color:gray}</style>

<title>Server Error</title>

<h1>Server Error</h1>

<p>We're sorry! The server encountered an internal error and was unable to complete your request. Please try again later.</p>

<p><small>error 500</small></p>
<?php }}else{if(self::$consoleMode){echo"$exception\n";}elseif($htmlMode){Helpers::renderBlueScreen($exception);if(self::$showBar){Helpers::renderDebugBar(self::$panels);}}elseif(!self::fireLog($exception,self::ERROR)){self::log($exception);}}foreach(self::$onFatalError
as$handler){call_user_func($handler,$exception);}}catch(\Exception$e){echo"\nNetteX\\Debug FATAL ERROR: thrown ",get_class($e),': ',$e->getMessage(),"\nwhile processing ",get_class($exception),': ',$exception->getMessage(),"\n";}exit(255);}static
function
_errorHandler($severity,$message,$file,$line,$context){if(self::$scream){error_reporting(E_ALL|E_STRICT);}if(self::$lastError!==FALSE&&($severity&error_reporting())===$severity){self::$lastError=new\ErrorException($message,0,$severity,$file,$line);return
NULL;}if($severity===E_RECOVERABLE_ERROR||$severity===E_USER_ERROR){throw
new
NetteX\FatalErrorException($message,0,$severity,$file,$line,$context);}elseif(($severity&error_reporting())!==$severity){return
FALSE;}elseif(self::$strictMode&&!self::$productionMode){self::_exceptionHandler(new
NetteX\FatalErrorException($message,0,$severity,$file,$line,$context));}static$types=array(E_WARNING=>'Warning',E_COMPILE_WARNING=>'Warning',E_USER_WARNING=>'Warning',E_NOTICE=>'Notice',E_USER_NOTICE=>'Notice',E_STRICT=>'Strict standards',E_DEPRECATED=>'Deprecated',E_USER_DEPRECATED=>'Deprecated');$message='PHP '.(isset($types[$severity])?$types[$severity]:'Unknown error').": $message";$count=&self::$errors["$message|$file|$line"];if($count++){return
NULL;}elseif(self::$productionMode){self::log("$message in $file:$line",self::ERROR);return
NULL;}else{$ok=self::fireLog(new\ErrorException($message,0,$severity,$file,$line),self::WARNING);return
self::$consoleMode||(!self::$showBar&&!$ok)?FALSE:NULL;}return
FALSE;}static
function
processException(\Exception$exception){trigger_error(__METHOD__.'() is deprecated; use '.__CLASS__.'::log($exception, Debug::ERROR) instead.',E_USER_WARNING);self::log($exception,self::ERROR);}static
function
toStringException(\Exception$exception){if(self::$enabled){self::_exceptionHandler($exception);}else{trigger_error($exception->getMessage(),E_USER_ERROR);}}static
function
tryError(){if(!self::$enabled&&self::$lastError===FALSE){set_error_handler(array(__CLASS__,'_errorHandler'));}self::$lastError=NULL;}static
function
catchError(&$error){if(!self::$enabled&&self::$lastError!==FALSE){restore_error_handler();}$error=self::$lastError;self::$lastError=FALSE;return(bool)$error;}private
static
function
defaultMailer($message){$host=isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'');$parts=str_replace(array("\r\n","\n"),array("\n",PHP_EOL),array('headers'=>"From: noreply@$host\nX-Mailer: NetteX Framework\n",'subject'=>"PHP: An error occurred on the server $host",'body'=>"[".@date('Y-m-d H:i:s')."] $message"));mail(self::$email,$parts['subject'],$parts['body'],$parts['headers']);}static
function
dump($var,$return=FALSE){if(!$return&&self::$productionMode){return$var;}$output="<pre class=\"nette-dump\">".Helpers::htmlDump($var)."</pre>\n";if(!$return&&self::$showLocation){$trace=debug_backtrace();$i=isset($trace[1]['class'])&&$trace[1]['class']===__CLASS__?1:0;if(isset($trace[$i]['file'],$trace[$i]['line'])){$output=substr_replace($output,' <small>'.htmlspecialchars("in file {$trace[$i]['file']} on line {$trace[$i]['line']}",ENT_NOQUOTES).'</small>',-8,0);}}if(self::$consoleMode){$output=htmlspecialchars_decode(strip_tags($output),ENT_NOQUOTES);}if($return){return$output;}else{echo$output;return$var;}}static
function
timer($name=NULL){static$time=array();$now=microtime(TRUE);$delta=isset($time[$name])?$now-$time[$name]:0;$time[$name]=$now;return$delta;}static
function
addPanel(IPanel$panel){self::$panels[]=$panel;}static
function
barDump($var,$title=NULL){if(!self::$productionMode){$dump=array();foreach((is_array($var)?$var:array(''=>$var))as$key=>$val){$dump[$key]=Helpers::clickableDump($val);}self::$panels[3]->data[]=array('title'=>$title,'dump'=>$dump);}return$var;}static
function
fireLog($message){if(self::$productionMode){return;}elseif(!self::$firebugDetected||headers_sent()){return
FALSE;}static$payload=array('logs'=>array());$item=array('name'=>'PHP','level'=>'debug','order'=>count($payload['logs']),'time'=>str_pad(number_format((microtime(TRUE)-self::$time)*1000,1,'.',' '),8,'0',STR_PAD_LEFT).' ms','template'=>'','message'=>'','style'=>'background:#767ab6');$args=func_get_args();if(isset($args[0])&&is_string($args[0])){$item['template']=array_shift($args);}if(isset($args[0])&&$args[0]instanceof\Exception){$e=array_shift($args);$trace=$e->getTrace();if(isset($trace[0]['class'])&&$trace[0]['class']===__CLASS__&&($trace[0]['function']==='_shutdownHandler'||$trace[0]['function']==='_errorHandler')){unset($trace[0]);}$item['exc_info']=array($e->getMessage(),$e->getFile(),array());$item['exc_frames']=array();foreach($trace
as$frame){$frame+=array('file'=>NULL,'line'=>NULL,'class'=>NULL,'type'=>NULL,'function'=>NULL,'object'=>NULL,'args'=>NULL);$item['exc_info'][2][]=array($frame['file'],$frame['line'],"$frame[class]$frame[type]$frame[function]",$frame['object']);$item['exc_frames'][]=$frame['args'];}$file=str_replace(dirname(dirname(dirname($e->getFile()))),"\xE2\x80\xA6",$e->getFile());$item['template']=($e
instanceof\ErrorException?'':get_class($e).': ').$e->getMessage().($e->getCode()?' #'.$e->getCode():'').' in '.$file.':'.$e->getLine();array_unshift($trace,array('file'=>$e->getFile(),'line'=>$e->getLine()));}else{$trace=debug_backtrace();if(isset($trace[0]['class'])&&$trace[0]['class']===__CLASS__&&($trace[0]['function']==='_shutdownHandler'||$trace[0]['function']==='_errorHandler')){unset($trace[0]);}}if(isset($args[0])&&in_array($args[0],array(self::DEBUG,self::INFO,self::WARNING,self::ERROR,self::CRITICAL),TRUE)){$item['level']=array_shift($args);}$item['args']=$args;foreach($trace
as$frame){if(isset($frame['file'])&&is_file($frame['file'])){$item['pathname']=$frame['file'];$item['lineno']=$frame['line'];break;}}$payload['logs'][]=Helpers::jsonDump($item,-1);foreach(str_split(base64_encode(@json_encode($payload)),4990)as$k=>$v){header("FireLogger-de11e-$k:$v");}return
TRUE;}}Debugger::_init();}namespace NetteX\Forms{use
NetteX;class
ControlGroup
extends
NetteX\Object{protected$controls;private$options=array();function
__construct(){$this->controls=new\SplObjectStorage;}function
add(){foreach(func_get_args()as$num=>$item){if($item
instanceof
IControl){$this->controls->attach($item);}elseif($item
instanceof\Traversable||is_array($item)){foreach($item
as$control){$this->controls->attach($control);}}else{throw
new
NetteX\InvalidArgumentException("Only IFormControl items are allowed, the #$num parameter is invalid.");}}return$this;}function
getControls(){return
iterator_to_array($this->controls);}function
setOption($key,$value){if($value===NULL){unset($this->options[$key]);}else{$this->options[$key]=$value;}return$this;}final
function
getOption($key,$default=NULL){return
isset($this->options[$key])?$this->options[$key]:$default;}final
function
getOptions(){return$this->options;}}}namespace NetteX\Forms\Controls{use
NetteX;use
NetteX\Forms\IControl;use
NetteX\Utils\Html;use
NetteX\Forms\Form;use
NetteX\Forms\Rule;abstract
class
BaseControl
extends
NetteX\ComponentModel\Component
implements
IControl{public
static$idMask='frm%s-%s';public$caption;protected$value;protected$control;protected$label;private$errors=array();private$disabled=FALSE;private$htmlId;private$htmlName;private$rules;private$translator=TRUE;private$options=array();function
__construct($caption=NULL){$this->monitor('NetteX\Forms\Form');parent::__construct();$this->control=Html::el('input');$this->label=Html::el('label');$this->caption=$caption;$this->rules=new
NetteX\Forms\Rules($this);}protected
function
attached($form){if(!$this->disabled&&$form
instanceof
Form&&$form->isAnchored()&&$form->isSubmitted()){$this->htmlName=NULL;$this->loadHttpData();}}function
getForm($need=TRUE){return$this->lookup('NetteX\Forms\Form',$need);}function
getHtmlName(){if($this->htmlName===NULL){$name=str_replace(self::NAME_SEPARATOR,'][',$this->lookupPath('NetteX\Forms\Form'),$count);if($count){$name=substr_replace($name,'',strpos($name,']'),1).']';}if(is_numeric($name)||in_array($name,array('attributes','children','elements','focus','length','reset','style','submit','onsubmit'))){$name.='_';}$this->htmlName=$name;}return$this->htmlName;}function
setHtmlId($id){$this->htmlId=$id;return$this;}function
getHtmlId(){if($this->htmlId===FALSE){return
NULL;}elseif($this->htmlId===NULL){$this->htmlId=sprintf(self::$idMask,$this->getForm()->getName(),$this->lookupPath('NetteX\Forms\Form'));}return$this->htmlId;}function
setAttribute($name,$value=TRUE){$this->control->$name=$value;return$this;}function
setOption($key,$value){if($value===NULL){unset($this->options[$key]);}else{$this->options[$key]=$value;}return$this;}final
function
getOption($key,$default=NULL){return
isset($this->options[$key])?$this->options[$key]:$default;}final
function
getOptions(){return$this->options;}function
setTranslator(NetteX\Localization\ITranslator$translator=NULL){$this->translator=$translator;return$this;}final
function
getTranslator(){if($this->translator===TRUE){return$this->getForm(FALSE)?$this->getForm()->getTranslator():NULL;}return$this->translator;}function
translate($s,$count=NULL){$translator=$this->getTranslator();return$translator===NULL||$s==NULL?$s:$translator->translate($s,$count);}function
setValue($value){$this->value=$value;return$this;}function
getValue(){return$this->value;}function
isFilled(){return(string)$this->getValue()!=='';}function
setDefaultValue($value){$form=$this->getForm(FALSE);if(!$form||!$form->isAnchored()||!$form->isSubmitted()){$this->setValue($value);}return$this;}function
loadHttpData(){$path=explode('[',strtr(str_replace(array('[]',']'),'',$this->getHtmlName()),'.','_'));$this->setValue(NetteX\ArrayUtils::get($this->getForm()->getHttpData(),$path));}function
setDisabled($value=TRUE){$this->disabled=(bool)$value;return$this;}function
isDisabled(){return$this->disabled;}function
getControl(){$this->setOption('rendered',TRUE);$control=clone$this->control;$control->name=$this->getHtmlName();$control->disabled=$this->disabled;$control->id=$this->getHtmlId();$control->required=$this->isRequired();$rules=self::exportRules($this->rules);$rules=substr(json_encode($rules),1,-1);$rules=preg_replace('#"([a-z0-9]+)":#i','$1:',$rules);$rules=preg_replace('#(?<!\\\\)"([^\\\\\',]*)"#i',"'$1'",$rules);$control->data('nette-rules',$rules?$rules:NULL);return$control;}function
getLabel($caption=NULL){$label=clone$this->label;$label->for=$this->getHtmlId();if($caption!==NULL){$label->setText($this->translate($caption));}elseif($this->caption
instanceof
Html){$label->add($this->caption);}else{$label->setText($this->translate($this->caption));}return$label;}final
function
getControlPrototype(){return$this->control;}final
function
getLabelPrototype(){return$this->label;}function
addRule($operation,$message=NULL,$arg=NULL){$this->rules->addRule($operation,$message,$arg);return$this;}function
addCondition($operation,$value=NULL){return$this->rules->addCondition($operation,$value);}function
addConditionOn(IControl$control,$operation,$value=NULL){return$this->rules->addConditionOn($control,$operation,$value);}final
function
getRules(){return$this->rules;}final
function
setRequired($message=NULL){return$this->addRule(Form::FILLED,$message);}final
function
isRequired(){foreach($this->rules
as$rule){if($rule->type===Rule::VALIDATOR&&!$rule->isNegative&&$rule->operation===Form::FILLED){return
TRUE;}}return
FALSE;}private
static
function
exportRules($rules){$payload=array();foreach($rules
as$rule){if(!is_string($op=$rule->operation)){$op=callback($op);if(!$op->isStatic()){continue;}}if($rule->type===Rule::VALIDATOR){$item=array('op'=>($rule->isNegative?'~':'').$op,'msg'=>$rules->formatMessage($rule,FALSE));}elseif($rule->type===Rule::CONDITION){$item=array('op'=>($rule->isNegative?'~':'').$op,'rules'=>self::exportRules($rule->subRules),'control'=>$rule->control->getHtmlName());if($rule->subRules->getToggles()){$item['toggle']=$rule->subRules->getToggles();}}if(is_array($rule->arg)){foreach($rule->arg
as$key=>$value){$item['arg'][$key]=$value
instanceof
IControl?(object)array('control'=>$value->getHtmlName()):$value;}}elseif($rule->arg!==NULL){$item['arg']=$rule->arg
instanceof
IControl?(object)array('control'=>$rule->arg->getHtmlName()):$rule->arg;}$payload[]=$item;}return$payload;}static
function
validateEqual(IControl$control,$arg){$value=$control->getValue();foreach((is_array($value)?$value:array($value))as$val){foreach((is_array($arg)?$arg:array($arg))as$item){if((string)$val===(string)($item
instanceof
IControl?$item->value:$item)){return
TRUE;}}}return
FALSE;}static
function
validateFilled(IControl$control){return$control->isFilled();}static
function
validateValid(IControl$control){return$control->rules->validate(TRUE);}function
addError($message){if(!in_array($message,$this->errors,TRUE)){$this->errors[]=$message;}$this->getForm()->addError($message);}function
getErrors(){return$this->errors;}function
hasErrors(){return(bool)$this->errors;}function
cleanErrors(){$this->errors=array();}}class
Button
extends
BaseControl{function
__construct($caption=NULL){parent::__construct($caption);$this->control->type='button';}function
getLabel($caption=NULL){return
NULL;}function
getControl($caption=NULL){$control=parent::getControl();$control->value=$this->translate($caption===NULL?$this->caption:$caption);return$control;}}class
Checkbox
extends
BaseControl{function
__construct($label=NULL){parent::__construct($label);$this->control->type='checkbox';$this->value=FALSE;}function
setValue($value){$this->value=is_scalar($value)?(bool)$value:FALSE;return$this;}function
getControl(){return
parent::getControl()->checked($this->value);}}class
HiddenField
extends
BaseControl{private$forcedValue;function
__construct($forcedValue=NULL){parent::__construct();$this->control->type='hidden';$this->value=(string)$forcedValue;$this->forcedValue=$forcedValue;}function
getLabel($caption=NULL){return
NULL;}function
setValue($value){$this->value=is_scalar($value)?(string)$value:'';return$this;}function
getControl(){return
parent::getControl()->value($this->forcedValue===NULL?$this->value:$this->forcedValue)->data('nette-rules',NULL);}}class
SubmitButton
extends
Button
implements
NetteX\Forms\ISubmitterControl{public$onClick;public$onInvalidClick;private$validationScope=TRUE;function
__construct($caption=NULL){parent::__construct($caption);$this->control->type='submit';}function
setValue($value){$this->value=is_scalar($value)&&(bool)$value;$form=$this->getForm();if($this->value||!is_object($form->isSubmitted())){$this->value=TRUE;$form->setSubmittedBy($this);}return$this;}function
isSubmittedBy(){return$this->getForm()->isSubmitted()===$this;}function
setValidationScope($scope){$this->validationScope=(bool)$scope;$this->control->formnovalidate=!$this->validationScope;return$this;}final
function
getValidationScope(){return$this->validationScope;}function
click(){$this->onClick($this);}static
function
validateSubmitted(NetteX\Forms\ISubmitterControl$control){return$control->isSubmittedBy();}}class
ImageButton
extends
SubmitButton{function
__construct($src=NULL,$alt=NULL){parent::__construct();$this->control->type='image';$this->control->src=$src;$this->control->alt=$alt;}function
getHtmlName(){$name=parent::getHtmlName();return
strpos($name,'[')===FALSE?$name:$name.'[]';}function
loadHttpData(){$path=$this->getHtmlName();$path=explode('[',strtr(str_replace(']','',strpos($path,'[')===FALSE?$path.'.x':substr($path,0,-2)),'.','_'));$this->setValue(NetteX\ArrayUtils::get($this->getForm()->getHttpData(),$path)!==NULL);}}class
SelectBox
extends
BaseControl{private$items=array();protected$allowed=array();private$skipFirst=FALSE;private$useKeys=TRUE;function
__construct($label=NULL,array$items=NULL,$size=NULL){parent::__construct($label);$this->control->setName('select');$this->control->size=$size>1?(int)$size:NULL;if($items!==NULL){$this->setItems($items);}}function
getValue(){$allowed=$this->allowed;if($this->skipFirst){$allowed=array_slice($allowed,1,count($allowed),TRUE);}return
is_scalar($this->value)&&isset($allowed[$this->value])?$this->value:NULL;}function
getRawValue(){return
is_scalar($this->value)?$this->value:NULL;}function
isFilled(){$value=$this->getValue();return
is_array($value)?count($value)>0:$value!==NULL;}function
skipFirst($item=NULL){if(is_bool($item)){$this->skipFirst=$item;}else{$this->skipFirst=TRUE;if($item!==NULL){$this->items=array(''=>$item)+$this->items;$this->allowed=array(''=>'')+$this->allowed;}}return$this;}final
function
isFirstSkipped(){return$this->skipFirst;}final
function
areKeysUsed(){return$this->useKeys;}function
setItems(array$items,$useKeys=TRUE){$this->items=$items;$this->allowed=array();$this->useKeys=(bool)$useKeys;foreach($items
as$key=>$value){if(!is_array($value)){$value=array($key=>$value);}foreach($value
as$key2=>$value2){if(!$this->useKeys){if(!is_scalar($value2)){throw
new
NetteX\InvalidArgumentException("All items must be scalar.");}$key2=$value2;}if(isset($this->allowed[$key2])){throw
new
NetteX\InvalidArgumentException("Items contain duplication for key '$key2'.");}$this->allowed[$key2]=$value2;}}return$this;}final
function
getItems(){return$this->items;}function
getSelectedItem(){if(!$this->useKeys){return$this->getValue();}else{$value=$this->getValue();return$value===NULL?NULL:$this->allowed[$value];}}function
getControl(){$control=parent::getControl();if($this->skipFirst){reset($this->items);$control->data('nette-empty-value',$this->useKeys?key($this->items):current($this->items));}$selected=$this->getValue();$selected=is_array($selected)?array_flip($selected):array($selected=>TRUE);$option=NetteX\Utils\Html::el('option');foreach($this->items
as$key=>$value){if(!is_array($value)){$value=array($key=>$value);$dest=$control;}else{$dest=$control->create('optgroup')->label($key);}foreach($value
as$key2=>$value2){if($value2
instanceof
NetteX\Utils\Html){$dest->add((string)$value2->selected(isset($selected[$key2])));}else{$key2=$this->useKeys?$key2:$value2;$value2=$this->translate((string)$value2);$dest->add((string)$option->value($key2===$value2?NULL:$key2)->selected(isset($selected[$key2]))->setText($value2));}}}return$control;}}class
MultiSelectBox
extends
SelectBox{function
getValue(){$allowed=array_keys($this->allowed);if($this->isFirstSkipped()){unset($allowed[0]);}return
array_intersect($this->getRawValue(),$allowed);}function
getRawValue(){if(is_scalar($this->value)){$value=array($this->value);}elseif(!is_array($this->value)){$value=array();}else{$value=$this->value;}$res=array();foreach($value
as$val){if(is_scalar($val)){$res[]=$val;}}return$res;}function
getSelectedItem(){if(!$this->areKeysUsed()){return$this->getValue();}else{$res=array();foreach($this->getValue()as$value){$res[$value]=$this->allowed[$value];}return$res;}}function
getHtmlName(){return
parent::getHtmlName().'[]';}function
getControl(){$control=parent::getControl();$control->multiple=TRUE;return$control;}}class
RadioList
extends
BaseControl{protected$separator;protected$container;protected$items=array();function
__construct($label=NULL,array$items=NULL){parent::__construct($label);$this->control->type='radio';$this->container=Html::el();$this->separator=Html::el('br');if($items!==NULL)$this->setItems($items);}function
getValue($raw=FALSE){return
is_scalar($this->value)&&($raw||isset($this->items[$this->value]))?$this->value:NULL;}function
isFilled(){return$this->getValue()!==NULL;}function
setItems(array$items){$this->items=$items;return$this;}final
function
getItems(){return$this->items;}final
function
getSeparatorPrototype(){return$this->separator;}final
function
getContainerPrototype(){return$this->container;}function
getControl($key=NULL){if($key===NULL){$container=clone$this->container;$separator=(string)$this->separator;}elseif(!isset($this->items[$key])){return
NULL;}$control=parent::getControl();$id=$control->id;$counter=-1;$value=$this->value===NULL?NULL:(string)$this->getValue();$label=Html::el('label');foreach($this->items
as$k=>$val){$counter++;if($key!==NULL&&$key!=$k)continue;$control->id=$label->for=$id.'-'.$counter;$control->checked=(string)$k===$value;$control->value=$k;if($val
instanceof
Html){$label->setHtml($val);}else{$label->setText($this->translate((string)$val));}if($key!==NULL){return(string)$control.(string)$label;}$container->add((string)$control.(string)$label.$separator);$control->data('nette-rules',NULL);}return$container;}function
getLabel($caption=NULL){$label=parent::getLabel($caption);$label->for=NULL;return$label;}}use
NetteX\StringUtils;abstract
class
TextBase
extends
BaseControl{protected$emptyValue='';protected$filters=array();function
setValue($value){$this->value=is_scalar($value)?(string)$value:'';return$this;}function
getValue(){$value=$this->value;foreach($this->filters
as$filter){$value=(string)$filter($value);}return$value===$this->translate($this->emptyValue)?'':$value;}function
setEmptyValue($value){$this->emptyValue=(string)$value;return$this;}final
function
getEmptyValue(){return$this->emptyValue;}function
addFilter($filter){$this->filters[]=callback($filter);return$this;}function
getControl(){$control=parent::getControl();foreach($this->getRules()as$rule){if($rule->type===NetteX\Forms\Rule::VALIDATOR&&!$rule->isNegative&&($rule->operation===Form::LENGTH||$rule->operation===Form::MAX_LENGTH)){$control->maxlength=is_array($rule->arg)?$rule->arg[1]:$rule->arg;}}if($this->emptyValue!==''){$control->data('nette-empty-value',$this->translate($this->emptyValue));}return$control;}function
addRule($operation,$message=NULL,$arg=NULL){if($operation===Form::FLOAT){$this->addFilter(callback(__CLASS__,'filterFloat'));}return
parent::addRule($operation,$message,$arg);}static
function
validateMinLength(TextBase$control,$length){return
StringUtils::length($control->getValue())>=$length;}static
function
validateMaxLength(TextBase$control,$length){return
StringUtils::length($control->getValue())<=$length;}static
function
validateLength(TextBase$control,$range){if(!is_array($range)){$range=array($range,$range);}$len=StringUtils::length($control->getValue());return($range[0]===NULL||$len>=$range[0])&&($range[1]===NULL||$len<=$range[1]);}static
function
validateEmail(TextBase$control){$atom="[-a-z0-9!#$%&'*+/=?^_`{|}~]";$localPart="(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)";$chars="a-z0-9\x80-\xFF";$domain="[$chars](?:[-$chars]{0,61}[$chars])";return(bool)StringUtils::match($control->getValue(),"(^$localPart@(?:$domain?\\.)+[-$chars]{2,19}\\z)i");}static
function
validateUrl(TextBase$control){$chars="a-z0-9\x80-\xFF";return(bool)StringUtils::match($control->getValue(),"#^(?:https?://|)(?:[$chars](?:[-$chars]{0,61}[$chars])?\\.)+[-$chars]{2,19}(/\S*)?$#i");}static
function
validateRegexp(TextBase$control,$regexp){return(bool)StringUtils::match($control->getValue(),$regexp);}static
function
validatePattern(TextBase$control,$pattern){return(bool)StringUtils::match($control->getValue(),"\x01^($pattern)$\x01u");}static
function
validateInteger(TextBase$control){return(bool)StringUtils::match($control->getValue(),'/^-?[0-9]+$/');}static
function
validateFloat(TextBase$control){return(bool)StringUtils::match($control->getValue(),'/^-?[0-9]*[.,]?[0-9]+$/');}static
function
validateRange(TextBase$control,$range){return($range[0]===NULL||$control->getValue()>=$range[0])&&($range[1]===NULL||$control->getValue()<=$range[1]);}static
function
filterFloat($s){return
str_replace(array(' ',','),array('','.'),$s);}}class
TextArea
extends
TextBase{function
__construct($label=NULL,$cols=NULL,$rows=NULL){parent::__construct($label);$this->control->setName('textarea');$this->control->cols=$cols;$this->control->rows=$rows;$this->value='';}function
getControl(){$control=parent::getControl();$control->setText($this->getValue()===''?$this->translate($this->emptyValue):$this->value);return$control;}}class
TextInput
extends
TextBase{function
__construct($label=NULL,$cols=NULL,$maxLength=NULL){parent::__construct($label);$this->control->type='text';$this->control->size=$cols;$this->control->maxlength=$maxLength;$this->filters[]=callback($this,'sanitize');$this->value='';}function
sanitize($value){if($this->control->maxlength&&NetteX\StringUtils::length($value)>$this->control->maxlength){$value=iconv_substr($value,0,$this->control->maxlength,'UTF-8');}return
NetteX\StringUtils::trim(strtr($value,"\r\n",'  '));}function
setType($type){$this->control->type=$type;return$this;}function
setPasswordMode($mode=TRUE){$this->control->type=$mode?'password':'text';return$this;}function
getControl(){$control=parent::getControl();foreach($this->getRules()as$rule){if($rule->isNegative||$rule->type!==NetteX\Forms\Rule::VALIDATOR){}elseif($rule->operation===NetteX\Forms\Form::RANGE&&$control->type!=='text'){list($control->min,$control->max)=$rule->arg;}elseif($rule->operation===NetteX\Forms\Form::PATTERN){$control->pattern=$rule->arg;}}if($control->type!=='password'){$control->value=$this->getValue()===''?$this->translate($this->emptyValue):$this->value;}return$control;}}use
NetteX\Http;class
UploadControl
extends
BaseControl{function
__construct($label=NULL){parent::__construct($label);$this->control->type='file';}protected
function
attached($form){if($form
instanceof
NetteX\Forms\Form){if($form->getMethod()!==NetteX\Forms\Form::POST){throw
new
NetteX\InvalidStateException('File upload requires method POST.');}$form->getElementPrototype()->enctype='multipart/form-data';}parent::attached($form);}function
setValue($value){if(is_array($value)){$this->value=new
Http\FileUpload($value);}elseif($value
instanceof
Http\FileUpload){$this->value=$value;}else{$this->value=new
Http\FileUpload(NULL);}return$this;}function
isFilled(){return$this->value
instanceof
Http\FileUpload&&$this->value->isOK();}static
function
validateFileSize(UploadControl$control,$limit){$file=$control->getValue();return$file
instanceof
Http\FileUpload&&$file->getSize()<=$limit;}static
function
validateMimeType(UploadControl$control,$mimeType){$file=$control->getValue();if($file
instanceof
Http\FileUpload){$type=strtolower($file->getContentType());$mimeTypes=is_array($mimeType)?$mimeType:explode(',',$mimeType);if(in_array($type,$mimeTypes,TRUE)){return
TRUE;}if(in_array(preg_replace('#/.*#','/*',$type),$mimeTypes,TRUE)){return
TRUE;}}return
FALSE;}static
function
validateImage(UploadControl$control){$file=$control->getValue();return$file
instanceof
Http\FileUpload&&$file->isImage();}}}namespace NetteX\Forms\Rendering{use
NetteX;use
NetteX\Utils\Html;class
DefaultFormRenderer
extends
NetteX\Object
implements
NetteX\Forms\IFormRenderer{public$wrappers=array('form'=>array('container'=>NULL,'errors'=>TRUE),'error'=>array('container'=>'ul class=error','item'=>'li'),'group'=>array('container'=>'fieldset','label'=>'legend','description'=>'p'),'controls'=>array('container'=>'table'),'pair'=>array('container'=>'tr','.required'=>'required','.optional'=>NULL,'.odd'=>NULL),'control'=>array('container'=>'td','.odd'=>NULL,'errors'=>FALSE,'description'=>'small','requiredsuffix'=>'','.required'=>'required','.text'=>'text','.password'=>'text','.file'=>'text','.submit'=>'button','.image'=>'imagebutton','.button'=>'button'),'label'=>array('container'=>'th','suffix'=>NULL,'requiredsuffix'=>''),'hidden'=>array('container'=>'div'));protected$form;protected$counter;function
render(NetteX\Forms\Form$form,$mode=NULL){if($this->form!==$form){$this->form=$form;$this->init();}$s='';if(!$mode||$mode==='begin'){$s.=$this->renderBegin();}if((!$mode&&$this->getValue('form errors'))||$mode==='errors'){$s.=$this->renderErrors();}if(!$mode||$mode==='body'){$s.=$this->renderBody();}if(!$mode||$mode==='end'){$s.=$this->renderEnd();}return$s;}function
setClientScript(){trigger_error(__METHOD__.'() is deprecated; use unobstructive JavaScript instead.',E_USER_WARNING);return$this;}protected
function
init(){$wrapper=&$this->wrappers['control'];foreach($this->form->getControls()as$control){if($control->isRequired()&&isset($wrapper['.required'])){$control->getLabelPrototype()->class($wrapper['.required'],TRUE);}$el=$control->getControlPrototype();if($el->getName()==='input'&&isset($wrapper['.'.$el->type])){$el->class($wrapper['.'.$el->type],TRUE);}}}function
renderBegin(){$this->counter=0;foreach($this->form->getControls()as$control){$control->setOption('rendered',FALSE);}if(strcasecmp($this->form->getMethod(),'get')===0){$el=clone$this->form->getElementPrototype();$uri=explode('?',(string)$el->action,2);$el->action=$uri[0];$s='';if(isset($uri[1])){foreach(preg_split('#[;&]#',$uri[1])as$param){$parts=explode('=',$param,2);$name=urldecode($parts[0]);if(!isset($this->form[$name])){$s.=Html::el('input',array('type'=>'hidden','name'=>$name,'value'=>urldecode($parts[1])));}}$s="\n\t".$this->getWrapper('hidden container')->setHtml($s);}return$el->startTag().$s;}else{return$this->form->getElementPrototype()->startTag();}}function
renderEnd(){$s='';foreach($this->form->getControls()as$control){if($control
instanceof
NetteX\Forms\Controls\HiddenField&&!$control->getOption('rendered')){$s.=(string)$control->getControl();}}if($s){$s=$this->getWrapper('hidden container')->setHtml($s)."\n";}return$s.$this->form->getElementPrototype()->endTag()."\n";}function
renderErrors(NetteX\Forms\IControl$control=NULL){$errors=$control===NULL?$this->form->getErrors():$control->getErrors();if(count($errors)){$ul=$this->getWrapper('error container');$li=$this->getWrapper('error item');foreach($errors
as$error){$item=clone$li;if($error
instanceof
Html){$item->add($error);}else{$item->setText($error);}$ul->add($item);}return"\n".$ul->render(0);}}function
renderBody(){$s=$remains='';$defaultContainer=$this->getWrapper('group container');$translator=$this->form->getTranslator();foreach($this->form->getGroups()as$group){if(!$group->getControls()||!$group->getOption('visual'))continue;$container=$group->getOption('container',$defaultContainer);$container=$container
instanceof
Html?clone$container:Html::el($container);$s.="\n".$container->startTag();$text=$group->getOption('label');if($text
instanceof
Html){$s.=$text;}elseif(is_string($text)){if($translator!==NULL){$text=$translator->translate($text);}$s.="\n".$this->getWrapper('group label')->setText($text)."\n";}$text=$group->getOption('description');if($text
instanceof
Html){$s.=$text;}elseif(is_string($text)){if($translator!==NULL){$text=$translator->translate($text);}$s.=$this->getWrapper('group description')->setText($text)."\n";}$s.=$this->renderControls($group);$remains=$container->endTag()."\n".$remains;if(!$group->getOption('embedNext')){$s.=$remains;$remains='';}}$s.=$remains.$this->renderControls($this->form);$container=$this->getWrapper('form container');$container->setHtml($s);return$container->render(0);}function
renderControls($parent){if(!($parent
instanceof
NetteX\Forms\Container||$parent
instanceof
NetteX\Forms\ControlGroup)){throw
new
NetteX\InvalidArgumentException("Argument must be FormContainer or FormGroup instance.");}$container=$this->getWrapper('controls container');$buttons=NULL;foreach($parent->getControls()as$control){if($control->getOption('rendered')||$control
instanceof
NetteX\Forms\Controls\HiddenField||$control->getForm(FALSE)!==$this->form){}elseif($control
instanceof
NetteX\Forms\Controls\Button){$buttons[]=$control;}else{if($buttons){$container->add($this->renderPairMulti($buttons));$buttons=NULL;}$container->add($this->renderPair($control));}}if($buttons){$container->add($this->renderPairMulti($buttons));}$s='';if(count($container)){$s.="\n".$container."\n";}return$s;}function
renderPair(NetteX\Forms\IControl$control){$pair=$this->getWrapper('pair container');$pair->add($this->renderLabel($control));$pair->add($this->renderControl($control));$pair->class($this->getValue($control->isRequired()?'pair .required':'pair .optional'),TRUE);$pair->class($control->getOption('class'),TRUE);if(++$this->counter
%
2)$pair->class($this->getValue('pair .odd'),TRUE);$pair->id=$control->getOption('id');return$pair->render(0);}function
renderPairMulti(array$controls){$s=array();foreach($controls
as$control){if(!$control
instanceof
NetteX\Forms\IControl){throw
new
NetteX\InvalidArgumentException("Argument must be array of IFormControl instances.");}$s[]=(string)$control->getControl();}$pair=$this->getWrapper('pair container');$pair->add($this->renderLabel($control));$pair->add($this->getWrapper('control container')->setHtml(implode(" ",$s)));return$pair->render(0);}function
renderLabel(NetteX\Forms\IControl$control){$head=$this->getWrapper('label container');if($control
instanceof
NetteX\Forms\Controls\Checkbox||$control
instanceof
NetteX\Forms\Controls\Button){return$head->setHtml(($head->getName()==='td'||$head->getName()==='th')?'&nbsp;':'');}else{$label=$control->getLabel();$suffix=$this->getValue('label suffix').($control->isRequired()?$this->getValue('label requiredsuffix'):'');if($label
instanceof
Html){$label->setHtml($label->getHtml().$suffix);$suffix='';}return$head->setHtml((string)$label.$suffix);}}function
renderControl(NetteX\Forms\IControl$control){$body=$this->getWrapper('control container');if($this->counter
%
2)$body->class($this->getValue('control .odd'),TRUE);$description=$control->getOption('description');if($description
instanceof
Html){$description=' '.$control->getOption('description');}elseif(is_string($description)){$description=' '.$this->getWrapper('control description')->setText($control->translate($description));}else{$description='';}if($control->isRequired()){$description=$this->getValue('control requiredsuffix').$description;}if($this->getValue('control errors')){$description.=$this->renderErrors($control);}if($control
instanceof
NetteX\Forms\Controls\Checkbox||$control
instanceof
NetteX\Forms\Controls\Button){return$body->setHtml((string)$control->getControl().(string)$control->getLabel().$description);}else{return$body->setHtml((string)$control->getControl().$description);}}protected
function
getWrapper($name){$data=$this->getValue($name);return$data
instanceof
Html?clone$data:Html::el($data);}protected
function
getValue($name){$name=explode(' ',$name);$data=&$this->wrappers[$name[0]][$name[1]];return$data;}}}namespace NetteX\Forms{use
NetteX;final
class
Rule
extends
NetteX\Object{const
CONDITION=1;const
VALIDATOR=2;const
FILTER=3;public$control;public$operation;public$arg;public$type;public$isNegative=FALSE;public$message;public$subRules;}final
class
Rules
extends
NetteX\Object
implements\IteratorAggregate{const
VALIDATE_PREFIX='validate';public
static$defaultMessages=array(Form::PROTECTION=>'Security token did not match. Possible CSRF attack.',Form::EQUAL=>'Please enter %s.',Form::FILLED=>'Please complete mandatory field.',Form::MIN_LENGTH=>'Please enter a value of at least %d characters.',Form::MAX_LENGTH=>'Please enter a value no longer than %d characters.',Form::LENGTH=>'Please enter a value between %d and %d characters long.',Form::EMAIL=>'Please enter a valid email address.',Form::URL=>'Please enter a valid URL.',Form::INTEGER=>'Please enter a numeric value.',Form::FLOAT=>'Please enter a numeric value.',Form::RANGE=>'Please enter a value between %d and %d.',Form::MAX_FILE_SIZE=>'The size of the uploaded file can be up to %d bytes.',Form::IMAGE=>'The uploaded file must be image in format JPEG, GIF or PNG.');private$rules=array();private$parent;private$toggles=array();private$control;function
__construct(IControl$control){$this->control=$control;}function
addRule($operation,$message=NULL,$arg=NULL){$rule=new
Rule;$rule->control=$this->control;$rule->operation=$operation;$this->adjustOperation($rule);$rule->arg=$arg;$rule->type=Rule::VALIDATOR;if($message===NULL&&is_string($rule->operation)&&isset(self::$defaultMessages[$rule->operation])){$rule->message=self::$defaultMessages[$rule->operation];}else{$rule->message=$message;}$this->rules[]=$rule;return$this;}function
addCondition($operation,$arg=NULL){return$this->addConditionOn($this->control,$operation,$arg);}function
addConditionOn(IControl$control,$operation,$arg=NULL){$rule=new
Rule;$rule->control=$control;$rule->operation=$operation;$this->adjustOperation($rule);$rule->arg=$arg;$rule->type=Rule::CONDITION;$rule->subRules=new
self($this->control);$rule->subRules->parent=$this;$this->rules[]=$rule;return$rule->subRules;}function
elseCondition(){$rule=clone
end($this->parent->rules);$rule->isNegative=!$rule->isNegative;$rule->subRules=new
self($this->parent->control);$rule->subRules->parent=$this->parent;$this->parent->rules[]=$rule;return$rule->subRules;}function
endCondition(){return$this->parent;}function
toggle($id,$hide=TRUE){$this->toggles[$id]=$hide;return$this;}function
validate($onlyCheck=FALSE){foreach($this->rules
as$rule){if($rule->control->isDisabled())continue;$success=($rule->isNegative
xor$this->getCallback($rule)->invoke($rule->control,$rule->arg));if($rule->type===Rule::CONDITION&&$success){if(!$rule->subRules->validate($onlyCheck)){return
FALSE;}}elseif($rule->type===Rule::VALIDATOR&&!$success){if(!$onlyCheck){$rule->control->addError(self::formatMessage($rule,TRUE));}return
FALSE;}}return
TRUE;}final
function
getIterator(){return
new\ArrayIterator($this->rules);}final
function
getToggles(){return$this->toggles;}private
function
adjustOperation($rule){if(is_string($rule->operation)&&ord($rule->operation[0])>127){$rule->isNegative=TRUE;$rule->operation=~$rule->operation;}if(!$this->getCallback($rule)->isCallable()){$operation=is_scalar($rule->operation)?" '$rule->operation'":'';throw
new
NetteX\InvalidArgumentException("Unknown operation$operation for control '{$rule->control->name}'.");}}private
function
getCallback($rule){$op=$rule->operation;if(is_string($op)&&strncmp($op,':',1)===0){return
callback(get_class($rule->control),self::VALIDATE_PREFIX.ltrim($op,':'));}else{return
callback($op);}}static
function
formatMessage($rule,$withValue){$message=$rule->message;if(!isset($message)){$message=self::$defaultMessages[$rule->operation];}if($translator=$rule->control->getForm()->getTranslator()){$message=$translator->translate($message,is_int($rule->arg)?$rule->arg:NULL);}$message=vsprintf(preg_replace('#%(name|label|value)#','%$0',$message),(array)$rule->arg);$message=str_replace('%name',$rule->control->getName(),$message);$message=str_replace('%label',$rule->control->translate($rule->control->caption),$message);if($withValue&&strpos($message,'%value')!==FALSE){$message=str_replace('%value',$rule->control->getValue(),$message);}return$message;}}}namespace NetteX\Http{use
NetteX;class
Context
extends
NetteX\Object{function
isModified($lastModified=NULL,$etag=NULL){$response=$this->getResponse();$request=$this->getRequest();if($lastModified){$response->setHeader('Last-Modified',$response->date($lastModified));}if($etag){$response->setHeader('ETag','"'.addslashes($etag).'"');}$ifNoneMatch=$request->getHeader('If-None-Match');if($ifNoneMatch==='*'){$match=TRUE;}elseif($ifNoneMatch!==NULL){$etag=$response->getHeader('ETag');if($etag==NULL||strpos(' '.strtr($ifNoneMatch,",\t",'  '),' '.$etag)===FALSE){return
TRUE;}else{$match=TRUE;}}$ifModifiedSince=$request->getHeader('If-Modified-Since');if($ifModifiedSince!==NULL){$lastModified=$response->getHeader('Last-Modified');if($lastModified!=NULL&&strtotime($lastModified)<=strtotime($ifModifiedSince)){$match=TRUE;}else{return
TRUE;}}if(empty($match)){return
TRUE;}$response->setCode(IResponse::S304_NOT_MODIFIED);return
FALSE;}function
getRequest(){return
NetteX\Environment::getHttpRequest();}function
getResponse(){return
NetteX\Environment::getHttpResponse();}}class
FileUpload
extends
NetteX\Object{private$name;private$type;private$size;private$tmpName;private$error;function
__construct($value){foreach(array('name','type','size','tmp_name','error')as$key){if(!isset($value[$key])||!is_scalar($value[$key])){$this->error=UPLOAD_ERR_NO_FILE;return;}}$this->name=$value['name'];$this->size=$value['size'];$this->tmpName=$value['tmp_name'];$this->error=$value['error'];}function
getName(){return$this->name;}function
getContentType(){if($this->isOk()&&$this->type===NULL){$this->type=NetteX\Utils\MimeTypeDetector::fromFile($this->tmpName);}return$this->type;}function
getSize(){return$this->size;}function
getTemporaryFile(){return$this->tmpName;}function
__toString(){return$this->tmpName;}function
getError(){return$this->error;}function
isOk(){return$this->error===UPLOAD_ERR_OK;}function
move($dest){$dir=dirname($dest);if(@mkdir($dir,0755,TRUE)){chmod($dir,0755);}$func=is_uploaded_file($this->tmpName)?'move_uploaded_file':'rename';if(!$func($this->tmpName,$dest)){throw
new
NetteX\InvalidStateException("Unable to move uploaded file '$this->tmpName' to '$dest'.");}chmod($dest,0644);$this->tmpName=$dest;return$this;}function
isImage(){return
in_array($this->getContentType(),array('image/gif','image/png','image/jpeg'),TRUE);}function
toImage(){return
NetteX\Image::fromFile($this->tmpName);}function
getImageSize(){return$this->isOk()?@getimagesize($this->tmpName):NULL;}function
getContents(){return$this->isOk()?file_get_contents($this->tmpName):NULL;}}class
Request
extends
NetteX\Object
implements
IRequest{private$method;private$uri;private$query;private$post;private$files;private$cookies;private$headers;private$remoteAddress;private$remoteHost;function
__construct(UrlScript$uri,$query=NULL,$post=NULL,$files=NULL,$cookies=NULL,$headers=NULL,$method=NULL,$remoteAddress=NULL,$remoteHost=NULL){$this->uri=$uri;$this->uri->freeze();if($query===NULL){parse_str($uri->query,$this->query);}else{$this->query=(array)$query;}$this->post=(array)$post;$this->files=(array)$files;$this->cookies=(array)$cookies;$this->headers=(array)$headers;$this->method=$method;$this->remoteAddress=$remoteAddress;$this->remoteHost=$remoteHost;}final
function
getUri(){return$this->uri;}final
function
getQuery($key=NULL,$default=NULL){if(func_num_args()===0){return$this->query;}elseif(isset($this->query[$key])){return$this->query[$key];}else{return$default;}}final
function
getPost($key=NULL,$default=NULL){if(func_num_args()===0){return$this->post;}elseif(isset($this->post[$key])){return$this->post[$key];}else{return$default;}}final
function
getFile($key){$args=func_get_args();return
NetteX\ArrayUtils::get($this->files,$args);}final
function
getFiles(){return$this->files;}final
function
getCookie($key,$default=NULL){if(func_num_args()===0){return$this->cookies;}elseif(isset($this->cookies[$key])){return$this->cookies[$key];}else{return$default;}}final
function
getCookies(){return$this->cookies;}function
getMethod(){return$this->method;}function
isMethod($method){return
strcasecmp($this->method,$method)===0;}function
isPost(){return$this->isMethod('POST');}final
function
getHeader($header,$default=NULL){$header=strtolower($header);if(isset($this->headers[$header])){return$this->headers[$header];}else{return$default;}}function
getHeaders(){return$this->headers;}final
function
getReferer(){return
isset($this->headers['referer'])?new
Url($this->headers['referer']):NULL;}function
isSecured(){return$this->uri->scheme==='https';}function
isAjax(){return$this->getHeader('X-Requested-With')==='XMLHttpRequest';}function
getRemoteAddress(){return$this->remoteAddress;}function
getRemoteHost(){if(!$this->remoteHost){$this->remoteHost=$this->remoteAddress?getHostByAddr($this->remoteAddress):NULL;}return$this->remoteHost;}function
detectLanguage(array$langs){$header=$this->getHeader('Accept-Language');if(!$header){return
NULL;}$s=strtolower($header);$s=strtr($s,'_','-');rsort($langs);preg_match_all('#('.implode('|',$langs).')(?:-[^\s,;=]+)?\s*(?:;\s*q=([0-9.]+))?#',$s,$matches);if(!$matches[0]){return
NULL;}$max=0;$lang=NULL;foreach($matches[1]as$key=>$value){$q=$matches[2][$key]===''?1.0:(float)$matches[2][$key];if($q>$max){$max=$q;$lang=$value;}}return$lang;}}use
NetteX\StringUtils;class
RequestFactory
extends
NetteX\Object{const
NONCHARS='#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u';public$uriFilters=array('path'=>array('#/{2,}#'=>'/'),'uri'=>array());private$encoding;function
setEncoding($encoding){$this->encoding=$encoding;return$this;}function
createHttpRequest(){$uri=new
UrlScript;$uri->scheme=isset($_SERVER['HTTPS'])&&strcasecmp($_SERVER['HTTPS'],'off')?'https':'http';$uri->user=isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:'';$uri->password=isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:'';if(isset($_SERVER['HTTP_HOST'])){$pair=explode(':',$_SERVER['HTTP_HOST']);}elseif(isset($_SERVER['SERVER_NAME'])){$pair=explode(':',$_SERVER['SERVER_NAME']);}else{$pair=array('');}$uri->host=preg_match('#^[-._a-z0-9]+$#',$pair[0])?$pair[0]:'';if(isset($pair[1])){$uri->port=(int)$pair[1];}elseif(isset($_SERVER['SERVER_PORT'])){$uri->port=(int)$_SERVER['SERVER_PORT'];}if(isset($_SERVER['REQUEST_URI'])){$requestUri=$_SERVER['REQUEST_URI'];}elseif(isset($_SERVER['ORIG_PATH_INFO'])){$requestUri=$_SERVER['ORIG_PATH_INFO'];if(isset($_SERVER['QUERY_STRING'])&&$_SERVER['QUERY_STRING']!=''){$requestUri.='?'.$_SERVER['QUERY_STRING'];}}else{$requestUri='';}$requestUri=StringUtils::replace($requestUri,$this->uriFilters['uri']);$tmp=explode('?',$requestUri,2);$uri->path=StringUtils::replace($tmp[0],$this->uriFilters['path']);$uri->query=isset($tmp[1])?$tmp[1]:'';$uri->canonicalize();$uri->path=StringUtils::fixEncoding($uri->path);if(isset($_SERVER['DOCUMENT_ROOT'],$_SERVER['SCRIPT_FILENAME'])&&strncmp($_SERVER['DOCUMENT_ROOT'],$_SERVER['SCRIPT_FILENAME'],strlen($_SERVER['DOCUMENT_ROOT']))===0){$script='/'.ltrim(strtr(substr($_SERVER['SCRIPT_FILENAME'],strlen($_SERVER['DOCUMENT_ROOT'])),'\\','/'),'/');}elseif(isset($_SERVER['SCRIPT_NAME'])){$script=$_SERVER['SCRIPT_NAME'];}else{$script='/';}if(strncasecmp($uri->path.'/',$script.'/',strlen($script)+1)===0){$uri->scriptPath=substr($uri->path,0,strlen($script));}elseif(strncasecmp($uri->path,$script,strrpos($script,'/')+1)===0){$uri->scriptPath=substr($uri->path,0,strrpos($script,'/')+1);}else{$uri->scriptPath='/';}$useFilter=(!in_array(ini_get('filter.default'),array('','unsafe_raw'))||ini_get('filter.default_flags'));parse_str($uri->query,$query);if(!$query){$query=$useFilter?filter_input_array(INPUT_GET,FILTER_UNSAFE_RAW):(empty($_GET)?array():$_GET);}$post=$useFilter?filter_input_array(INPUT_POST,FILTER_UNSAFE_RAW):(empty($_POST)?array():$_POST);$cookies=$useFilter?filter_input_array(INPUT_COOKIE,FILTER_UNSAFE_RAW):(empty($_COOKIE)?array():$_COOKIE);$gpc=(bool)get_magic_quotes_gpc();$old=error_reporting(error_reporting()^E_NOTICE);if($gpc||$this->encoding){$utf=strcasecmp($this->encoding,'UTF-8')===0;$list=array(&$query,&$post,&$cookies);while(list($key,$val)=each($list)){foreach($val
as$k=>$v){unset($list[$key][$k]);if($gpc){$k=stripslashes($k);}if($this->encoding&&is_string($k)&&(preg_match(self::NONCHARS,$k)||preg_last_error())){}elseif(is_array($v)){$list[$key][$k]=$v;$list[]=&$list[$key][$k];}else{if($gpc&&!$useFilter){$v=stripSlashes($v);}if($this->encoding){if($utf){$v=StringUtils::fixEncoding($v);}else{if(!StringUtils::checkEncoding($v)){$v=iconv($this->encoding,'UTF-8//IGNORE',$v);}$v=html_entity_decode($v,ENT_QUOTES,'UTF-8');}$v=preg_replace(self::NONCHARS,'',$v);}$list[$key][$k]=$v;}}}unset($list,$key,$val,$k,$v);}$files=array();$list=array();if(!empty($_FILES)){foreach($_FILES
as$k=>$v){if($this->encoding&&is_string($k)&&(preg_match(self::NONCHARS,$k)||preg_last_error()))continue;$v['@']=&$files[$k];$list[]=$v;}}while(list(,$v)=each($list)){if(!isset($v['name'])){continue;}elseif(!is_array($v['name'])){if($gpc){$v['name']=stripSlashes($v['name']);}if($this->encoding){$v['name']=preg_replace(self::NONCHARS,'',StringUtils::fixEncoding($v['name']));}$v['@']=new
FileUpload($v);continue;}foreach($v['name']as$k=>$foo){if($this->encoding&&is_string($k)&&(preg_match(self::NONCHARS,$k)||preg_last_error()))continue;$list[]=array('name'=>$v['name'][$k],'type'=>$v['type'][$k],'size'=>$v['size'][$k],'tmp_name'=>$v['tmp_name'][$k],'error'=>$v['error'][$k],'@'=>&$v['@'][$k]);}}error_reporting($old);if(function_exists('apache_request_headers')){$headers=array_change_key_case(apache_request_headers(),CASE_LOWER);}else{$headers=array();foreach($_SERVER
as$k=>$v){if(strncmp($k,'HTTP_',5)==0){$k=substr($k,5);}elseif(strncmp($k,'CONTENT_',8)){continue;}$headers[strtr(strtolower($k),'_','-')]=$v;}}return
new
Request($uri,$query,$post,$files,$cookies,$headers,isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:NULL,isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:NULL,isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:NULL);}}final
class
Response
extends
NetteX\Object
implements
IResponse{private
static$fixIE=TRUE;public$cookieDomain='';public$cookiePath='/';public$cookieSecure=FALSE;public$cookieHttpOnly=TRUE;private$code=self::S200_OK;function
setCode($code){$code=(int)$code;static$allowed=array(200=>1,201=>1,202=>1,203=>1,204=>1,205=>1,206=>1,300=>1,301=>1,302=>1,303=>1,304=>1,307=>1,400=>1,401=>1,403=>1,404=>1,406=>1,408=>1,410=>1,412=>1,415=>1,416=>1,500=>1,501=>1,503=>1,505=>1);if(!isset($allowed[$code])){throw
new
NetteX\InvalidArgumentException("Bad HTTP response '$code'.");}elseif(headers_sent($file,$line)){throw
new
NetteX\InvalidStateException("Cannot set HTTP code after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}else{$this->code=$code;$protocol=isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:'HTTP/1.1';header($protocol.' '.$code,TRUE,$code);}return$this;}function
getCode(){return$this->code;}function
setHeader($name,$value){if(headers_sent($file,$line)){throw
new
NetteX\InvalidStateException("Cannot send header after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}if($value===NULL&&function_exists('header_remove')){header_remove($name);}else{header($name.': '.$value,TRUE,$this->code);}return$this;}function
addHeader($name,$value){if(headers_sent($file,$line)){throw
new
NetteX\InvalidStateException("Cannot send header after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}header($name.': '.$value,FALSE,$this->code);}function
setContentType($type,$charset=NULL){$this->setHeader('Content-Type',$type.($charset?'; charset='.$charset:''));return$this;}function
redirect($url,$code=self::S302_FOUND){if(isset($_SERVER['SERVER_SOFTWARE'])&&preg_match('#^Microsoft-IIS/[1-5]#',$_SERVER['SERVER_SOFTWARE'])&&$this->getHeader('Set-Cookie')!==NULL){$this->setHeader('Refresh',"0;url=$url");return;}$this->setCode($code);$this->setHeader('Location',$url);echo"<h1>Redirect</h1>\n\n<p><a href=\"".htmlSpecialChars($url)."\">Please click here to continue</a>.</p>";}function
setExpiration($time){if(!$time){$this->setHeader('Cache-Control','s-maxage=0, max-age=0, must-revalidate');$this->setHeader('Expires','Mon, 23 Jan 1978 10:00:00 GMT');return$this;}$time=NetteX\DateTime::from($time);$this->setHeader('Cache-Control','max-age='.($time->format('U')-time()));$this->setHeader('Expires',self::date($time));return$this;}function
isSent(){return
headers_sent();}function
getHeader($header,$default=NULL){$header.=':';$len=strlen($header);foreach(headers_list()as$item){if(strncasecmp($item,$header,$len)===0){return
ltrim(substr($item,$len));}}return$default;}function
getHeaders(){$headers=array();foreach(headers_list()as$header){$a=strpos($header,':');$headers[substr($header,0,$a)]=(string)substr($header,$a+2);}return$headers;}static
function
date($time=NULL){$time=NetteX\DateTime::from($time);$time->setTimezone(new\DateTimeZone('GMT'));return$time->format('D, d M Y H:i:s \G\M\T');}function
__destruct(){if(self::$fixIE&&isset($_SERVER['HTTP_USER_AGENT'])&&strpos($_SERVER['HTTP_USER_AGENT'],'MSIE ')!==FALSE&&in_array($this->code,array(400,403,404,405,406,408,409,410,500,501,505),TRUE)&&$this->getHeader('Content-Type','text/html')==='text/html'){echo
NetteX\StringUtils::random(2e3," \t\r\n");self::$fixIE=FALSE;}}function
setCookie($name,$value,$time,$path=NULL,$domain=NULL,$secure=NULL,$httpOnly=NULL){if(headers_sent($file,$line)){throw
new
NetteX\InvalidStateException("Cannot set cookie after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}setcookie($name,$value,$time?NetteX\DateTime::from($time)->format('U'):0,$path===NULL?$this->cookiePath:(string)$path,$domain===NULL?$this->cookieDomain:(string)$domain,$secure===NULL?$this->cookieSecure:(bool)$secure,$httpOnly===NULL?$this->cookieHttpOnly:(bool)$httpOnly);return$this;}function
deleteCookie($name,$path=NULL,$domain=NULL,$secure=NULL){if(headers_sent($file,$line)){throw
new
NetteX\InvalidStateException("Cannot delete cookie after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}setcookie($name,FALSE,254400000,$path===NULL?$this->cookiePath:(string)$path,$domain===NULL?$this->cookieDomain:(string)$domain,$secure===NULL?$this->cookieSecure:(bool)$secure,TRUE);}}class
Session
extends
NetteX\Object{const
DEFAULT_FILE_LIFETIME=10800;private$regenerationNeeded;private
static$started;private$options=array('referer_check'=>'','use_cookies'=>1,'use_only_cookies'=>1,'use_trans_sid'=>0,'cookie_lifetime'=>0,'cookie_path'=>'/','cookie_domain'=>'','cookie_secure'=>FALSE,'cookie_httponly'=>TRUE,'gc_maxlifetime'=>self::DEFAULT_FILE_LIFETIME,'cache_limiter'=>NULL,'cache_expire'=>NULL,'hash_function'=>NULL,'hash_bits_per_character'=>NULL);function
start(){if(self::$started){return;}elseif(self::$started===NULL&&defined('SID')){throw
new
NetteX\InvalidStateException('A session had already been started by session.auto-start or session_start().');}$this->configure($this->options);NetteX\Diagnostics\Debugger::tryError();session_start();if(NetteX\Diagnostics\Debugger::catchError($e)){@session_write_close();throw
new
NetteX\InvalidStateException('session_start(): '.$e->getMessage(),0,$e);}self::$started=TRUE;if($this->regenerationNeeded){session_regenerate_id(TRUE);$this->regenerationNeeded=FALSE;}unset($_SESSION['__NT'],$_SESSION['__NS'],$_SESSION['__NM']);$nf=&$_SESSION['__NF'];if(empty($nf)){$nf=array('C'=>0);}else{$nf['C']++;}$browserKey=$this->getHttpRequest()->getCookie('nette-browser');if(!$browserKey){$browserKey=NetteX\StringUtils::random();}$browserClosed=!isset($nf['B'])||$nf['B']!==$browserKey;$nf['B']=$browserKey;$this->sendCookie();if(isset($nf['META'])){$now=time();foreach($nf['META']as$namespace=>$metadata){if(is_array($metadata)){foreach($metadata
as$variable=>$value){if((!empty($value['B'])&&$browserClosed)||(!empty($value['T'])&&$now>$value['T'])||($variable!==''&&is_object($nf['DATA'][$namespace][$variable])&&(isset($value['V'])?$value['V']:NULL)!==NetteX\Reflection\ClassType::from($nf['DATA'][$namespace][$variable])->getAnnotation('serializationVersion'))){if($variable===''){unset($nf['META'][$namespace],$nf['DATA'][$namespace]);continue
2;}unset($nf['META'][$namespace][$variable],$nf['DATA'][$namespace][$variable]);}}}}}register_shutdown_function(array($this,'clean'));}function
isStarted(){return(bool)self::$started;}function
close(){if(self::$started){$this->clean();session_write_close();self::$started=FALSE;}}function
destroy(){if(!self::$started){throw
new
NetteX\InvalidStateException('Session is not started.');}session_destroy();$_SESSION=NULL;self::$started=FALSE;if(!$this->getHttpResponse()->isSent()){$params=session_get_cookie_params();$this->getHttpResponse()->deleteCookie(session_name(),$params['path'],$params['domain'],$params['secure']);}}function
exists(){return
self::$started||$this->getHttpRequest()->getCookie(session_name())!==NULL;}function
regenerateId(){if(self::$started){if(headers_sent($file,$line)){throw
new
NetteX\InvalidStateException("Cannot regenerate session ID after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}session_regenerate_id(TRUE);}else{$this->regenerationNeeded=TRUE;}}function
getId(){return
session_id();}function
setName($name){if(!is_string($name)||!preg_match('#[^0-9.][^.]*$#A',$name)){throw
new
NetteX\InvalidArgumentException('Session name must be a string and cannot contain dot.');}session_name($name);return$this->setOptions(array('name'=>$name));}function
getName(){return
session_name();}function
getNamespace($namespace,$class='NetteX\Http\SessionNamespace'){if(!is_string($namespace)||$namespace===''){throw
new
NetteX\InvalidArgumentException('Session namespace must be a non-empty string.');}if(!self::$started){$this->start();}return
new$class($_SESSION['__NF']['DATA'][$namespace],$_SESSION['__NF']['META'][$namespace]);}function
hasNamespace($namespace){if($this->exists()&&!self::$started){$this->start();}return!empty($_SESSION['__NF']['DATA'][$namespace]);}function
getIterator(){if($this->exists()&&!self::$started){$this->start();}if(isset($_SESSION['__NF']['DATA'])){return
new\ArrayIterator(array_keys($_SESSION['__NF']['DATA']));}else{return
new\ArrayIterator;}}function
clean(){if(!self::$started||empty($_SESSION)){return;}$nf=&$_SESSION['__NF'];if(isset($nf['META'])&&is_array($nf['META'])){foreach($nf['META']as$name=>$foo){if(empty($nf['META'][$name])){unset($nf['META'][$name]);}}}if(empty($nf['META'])){unset($nf['META']);}if(empty($nf['DATA'])){unset($nf['DATA']);}if(empty($_SESSION)){}}function
setOptions(array$options){if(self::$started){$this->configure($options);}$this->options=$options+$this->options;return$this;}function
getOptions(){return$this->options;}private
function
configure(array$config){$special=array('cache_expire'=>1,'cache_limiter'=>1,'save_path'=>1,'name'=>1);foreach($config
as$key=>$value){if(!strncmp($key,'session.',8)){$key=substr($key,8);}if($value===NULL){continue;}elseif(isset($special[$key])){if(self::$started){throw
new
NetteX\InvalidStateException("Unable to set '$key' when session has been started.");}$key="session_$key";$key($value);}elseif(strncmp($key,'cookie_',7)===0){if(!isset($cookie)){$cookie=session_get_cookie_params();}$cookie[substr($key,7)]=$value;}elseif(!function_exists('ini_set')){if(ini_get($key)!=$value&&!NetteX\Framework::$iAmUsingBadHost){throw
new
NetteX\NotSupportedException('Required function ini_set() is disabled.');}}else{if(self::$started){throw
new
NetteX\InvalidStateException("Unable to set '$key' when session has been started.");}ini_set("session.$key",$value);}}if(isset($cookie)){session_set_cookie_params($cookie['lifetime'],$cookie['path'],$cookie['domain'],$cookie['secure'],$cookie['httponly']);if(self::$started){$this->sendCookie();}}}function
setExpiration($time){if(empty($time)){return$this->setOptions(array('gc_maxlifetime'=>self::DEFAULT_FILE_LIFETIME,'cookie_lifetime'=>0));}else{$time=NetteX\DateTime::from($time)->format('U')-time();return$this->setOptions(array('gc_maxlifetime'=>$time,'cookie_lifetime'=>$time));}}function
setCookieParams($path,$domain=NULL,$secure=NULL){return$this->setOptions(array('cookie_path'=>$path,'cookie_domain'=>$domain,'cookie_secure'=>$secure));}function
getCookieParams(){return
session_get_cookie_params();}function
setSavePath($path){return$this->setOptions(array('save_path'=>$path));}function
setStorage(ISessionStorage$storage){if(self::$started){throw
new
NetteX\InvalidStateException("Unable to set storage when session has been started.");}session_set_save_handler(array($storage,'open'),array($storage,'close'),array($storage,'read'),array($storage,'write'),array($storage,'remove'),array($storage,'clean'));}private
function
sendCookie(){$cookie=$this->getCookieParams();$this->getHttpResponse()->setCookie(session_name(),session_id(),$cookie['lifetime']?$cookie['lifetime']+time():0,$cookie['path'],$cookie['domain'],$cookie['secure'],$cookie['httponly'])->setCookie('nette-browser',$_SESSION['__NF']['B'],Response::BROWSER,$cookie['path'],$cookie['domain']);}protected
function
getHttpRequest(){return
NetteX\Environment::getHttpRequest();}protected
function
getHttpResponse(){return
NetteX\Environment::getHttpResponse();}}final
class
SessionNamespace
extends
NetteX\Object
implements\IteratorAggregate,\ArrayAccess{private$data;private$meta;public$warnOnUndefined=FALSE;function
__construct(&$data,&$meta){$this->data=&$data;$this->meta=&$meta;}function
getIterator(){if(isset($this->data)){return
new\ArrayIterator($this->data);}else{return
new\ArrayIterator;}}function
__set($name,$value){$this->data[$name]=$value;if(is_object($value)){$this->meta[$name]['V']=NetteX\Reflection\ClassType::from($value)->getAnnotation('serializationVersion');}}function&__get($name){if($this->warnOnUndefined&&!array_key_exists($name,$this->data)){trigger_error("The variable '$name' does not exist in session namespace",E_USER_NOTICE);}return$this->data[$name];}function
__isset($name){return
isset($this->data[$name]);}function
__unset($name){unset($this->data[$name],$this->meta[$name]);}function
offsetSet($name,$value){$this->__set($name,$value);}function
offsetGet($name){return$this->__get($name);}function
offsetExists($name){return$this->__isset($name);}function
offsetUnset($name){$this->__unset($name);}function
setExpiration($time,$variables=NULL){if(empty($time)){$time=NULL;$whenBrowserIsClosed=TRUE;}else{$time=NetteX\DateTime::from($time)->format('U');$whenBrowserIsClosed=FALSE;}if($variables===NULL){$this->meta['']['T']=$time;$this->meta['']['B']=$whenBrowserIsClosed;}elseif(is_array($variables)){foreach($variables
as$variable){$this->meta[$variable]['T']=$time;$this->meta[$variable]['B']=$whenBrowserIsClosed;}}else{$this->meta[$variables]['T']=$time;$this->meta[$variables]['B']=$whenBrowserIsClosed;}return$this;}function
removeExpiration($variables=NULL){if($variables===NULL){unset($this->meta['']['T'],$this->meta['']['B']);}elseif(is_array($variables)){foreach($variables
as$variable){unset($this->meta[$variable]['T'],$this->meta[$variable]['B']);}}else{unset($this->meta[$variables]['T'],$this->meta[$variable]['B']);}}function
remove(){$this->data=NULL;$this->meta=NULL;}}class
Url
extends
NetteX\FreezableObject{public
static$defaultPorts=array('http'=>80,'https'=>443,'ftp'=>21,'news'=>119,'nntp'=>119);private$scheme='';private$user='';private$pass='';private$host='';private$port=NULL;private$path='';private$query='';private$fragment='';function
__construct($uri=NULL){if(is_string($uri)){$parts=@parse_url($uri);if($parts===FALSE){throw
new
NetteX\InvalidArgumentException("Malformed or unsupported URI '$uri'.");}foreach($parts
as$key=>$val){$this->$key=$val;}if(!$this->port&&isset(self::$defaultPorts[$this->scheme])){$this->port=self::$defaultPorts[$this->scheme];}if($this->path===''&&($this->scheme==='http'||$this->scheme==='https')){$this->path='/';}}elseif($uri
instanceof
self){foreach($this
as$key=>$val){$this->$key=$uri->$key;}}}function
setScheme($value){$this->updating();$this->scheme=(string)$value;return$this;}function
getScheme(){return$this->scheme;}function
setUser($value){$this->updating();$this->user=(string)$value;return$this;}function
getUser(){return$this->user;}function
setPassword($value){$this->updating();$this->pass=(string)$value;return$this;}function
getPassword(){return$this->pass;}function
setHost($value){$this->updating();$this->host=(string)$value;return$this;}function
getHost(){return$this->host;}function
setPort($value){$this->updating();$this->port=(int)$value;return$this;}function
getPort(){return$this->port;}function
setPath($value){$this->updating();$this->path=(string)$value;return$this;}function
getPath(){return$this->path;}function
setQuery($value){$this->updating();$this->query=(string)(is_array($value)?http_build_query($value,'','&'):$value);return$this;}function
appendQuery($value){$this->updating();$value=(string)(is_array($value)?http_build_query($value,'','&'):$value);$this->query.=($this->query===''||$value==='')?$value:'&'.$value;}function
getQuery(){return$this->query;}function
setFragment($value){$this->updating();$this->fragment=(string)$value;return$this;}function
getFragment(){return$this->fragment;}function
getAbsoluteUri(){return$this->scheme.'://'.$this->getAuthority().$this->path.($this->query===''?'':'?'.$this->query).($this->fragment===''?'':'#'.$this->fragment);}function
getAuthority(){$authority=$this->host;if($this->port&&isset(self::$defaultPorts[$this->scheme])&&$this->port!==self::$defaultPorts[$this->scheme]){$authority.=':'.$this->port;}if($this->user!==''&&$this->scheme!=='http'&&$this->scheme!=='https'){$authority=$this->user.($this->pass===''?'':':'.$this->pass).'@'.$authority;}return$authority;}function
getHostUri(){return$this->scheme.'://'.$this->getAuthority();}function
getBasePath(){$pos=strrpos($this->path,'/');return$pos===FALSE?'':substr($this->path,0,$pos+1);}function
getBaseUri(){return$this->scheme.'://'.$this->getAuthority().$this->getBasePath();}function
getRelativeUri(){return(string)substr($this->getAbsoluteUri(),strlen($this->getBaseUri()));}function
isEqual($uri){$part=self::unescape(strtok($uri,'?#'),'%/');if(strncmp($part,'//',2)===0){if($part!=='//'.$this->getAuthority().$this->path)return
FALSE;}elseif(strncmp($part,'/',1)===0){if($part!==$this->path)return
FALSE;}else{if($part!==$this->scheme.'://'.$this->getAuthority().$this->path)return
FALSE;}$part=preg_split('#[&;]#',self::unescape(strtr((string)strtok('?#'),'+',' '),'%&;=+'));sort($part);$query=preg_split('#[&;]#',$this->query);sort($query);return$part===$query;}function
canonicalize(){$this->updating();$this->path=$this->path===''?'/':self::unescape($this->path,'%/');$this->host=strtolower(rawurldecode($this->host));$this->query=self::unescape(strtr($this->query,'+',' '),'%&;=+');}function
__toString(){return$this->getAbsoluteUri();}static
function
unescape($s,$reserved='%;/?:@&=+$,'){preg_match_all('#(?<=%)[a-f0-9][a-f0-9]#i',$s,$matches,PREG_OFFSET_CAPTURE|PREG_SET_ORDER);foreach(array_reverse($matches)as$match){$ch=chr(hexdec($match[0][0]));if(strpos($reserved,$ch)===FALSE){$s=substr_replace($s,$ch,$match[0][1]-1,3);}}return$s;}}class
UrlScript
extends
Url{private$scriptPath='/';function
setScriptPath($value){$this->updating();$this->scriptPath=(string)$value;return$this;}function
getScriptPath(){return$this->scriptPath;}function
getBasePath(){$pos=strrpos($this->scriptPath,'/');return$pos===FALSE?'':substr($this->path,0,$pos+1);}function
getPathInfo(){return(string)substr($this->path,strlen($this->scriptPath));}}use
NetteX\Environment;use
NetteX\Security\IAuthenticator;use
NetteX\Security\IAuthorizator;use
NetteX\Security\IIdentity;class
User
extends
NetteX\Object
implements
IUser{const
MANUAL=1,INACTIVITY=2,BROWSER_CLOSED=3;public$guestRole='guest';public$authenticatedRole='authenticated';public$onLoggedIn;public$onLoggedOut;private$authenticationHandler;private$authorizationHandler;private$namespace='';private$session;function
login($username=NULL,$password=NULL){$handler=$this->getAuthenticationHandler();if($handler===NULL){throw
new
NetteX\InvalidStateException('Authentication handler has not been set.');}$this->logout(TRUE);$credentials=func_get_args();$this->setIdentity($handler->authenticate($credentials));$this->setAuthenticated(TRUE);$this->onLoggedIn($this);}final
function
logout($clearIdentity=FALSE){if($this->isLoggedIn()){$this->setAuthenticated(FALSE);$this->onLoggedOut($this);}if($clearIdentity){$this->setIdentity(NULL);}}final
function
isLoggedIn(){$session=$this->getSessionNamespace(FALSE);return$session&&$session->authenticated;}final
function
getIdentity(){$session=$this->getSessionNamespace(FALSE);return$session?$session->identity:NULL;}function
getId(){$identity=$this->getIdentity();return$identity?$identity->getId():NULL;}function
setAuthenticationHandler(IAuthenticator$handler){$this->authenticationHandler=$handler;return$this;}final
function
getAuthenticationHandler(){if($this->authenticationHandler===NULL){$this->authenticationHandler=Environment::getService('NetteX\\Security\\IAuthenticator');}return$this->authenticationHandler;}function
setNamespace($namespace){if($this->namespace!==$namespace){$this->namespace=(string)$namespace;$this->session=NULL;}return$this;}final
function
getNamespace(){return$this->namespace;}function
setExpiration($time,$whenBrowserIsClosed=TRUE,$clearIdentity=FALSE){$session=$this->getSessionNamespace(TRUE);if($time){$time=NetteX\DateTime::from($time)->format('U');$session->expireTime=$time;$session->expireDelta=$time-time();}else{unset($session->expireTime,$session->expireDelta);}$session->expireIdentity=(bool)$clearIdentity;$session->expireBrowser=(bool)$whenBrowserIsClosed;$session->browserCheck=TRUE;$session->setExpiration(0,'browserCheck');return$this;}final
function
getLogoutReason(){$session=$this->getSessionNamespace(FALSE);return$session?$session->reason:NULL;}protected
function
getSessionNamespace($need){if($this->session!==NULL){return$this->session;}$sessionHandler=$this->getSession();if(!$need&&!$sessionHandler->exists()){return
NULL;}$this->session=$session=$sessionHandler->getNamespace('NetteX.Web.User/'.$this->namespace);if(!$session->identity
instanceof
IIdentity||!is_bool($session->authenticated)){$session->remove();}if($session->authenticated&&$session->expireBrowser&&!$session->browserCheck){$session->reason=self::BROWSER_CLOSED;$session->authenticated=FALSE;$this->onLoggedOut($this);if($session->expireIdentity){unset($session->identity);}}if($session->authenticated&&$session->expireDelta>0){if($session->expireTime<time()){$session->reason=self::INACTIVITY;$session->authenticated=FALSE;$this->onLoggedOut($this);if($session->expireIdentity){unset($session->identity);}}$session->expireTime=time()+$session->expireDelta;}if(!$session->authenticated){unset($session->expireTime,$session->expireDelta,$session->expireIdentity,$session->expireBrowser,$session->browserCheck,$session->authTime);}return$this->session;}protected
function
setAuthenticated($state){$session=$this->getSessionNamespace(TRUE);$session->authenticated=(bool)$state;$this->getSession()->regenerateId();if($state){$session->reason=NULL;$session->authTime=time();}else{$session->reason=self::MANUAL;$session->authTime=NULL;}return$this;}protected
function
setIdentity(IIdentity$identity=NULL){$this->getSessionNamespace(TRUE)->identity=$identity;return$this;}function
getRoles(){if(!$this->isLoggedIn()){return
array($this->guestRole);}$identity=$this->getIdentity();return$identity?$identity->getRoles():array($this->authenticatedRole);}final
function
isInRole($role){return
in_array($role,$this->getRoles(),TRUE);}function
isAllowed($resource=IAuthorizator::ALL,$privilege=IAuthorizator::ALL){$handler=$this->getAuthorizationHandler();if(!$handler){throw
new
NetteX\InvalidStateException("Authorization handler has not been set.");}foreach($this->getRoles()as$role){if($handler->isAllowed($role,$resource,$privilege))return
TRUE;}return
FALSE;}function
setAuthorizationHandler(IAuthorizator$handler){$this->authorizationHandler=$handler;return$this;}final
function
getAuthorizationHandler(){if($this->authorizationHandler===NULL){$this->authorizationHandler=Environment::getService('NetteX\\Security\\IAuthorizator');}return$this->authorizationHandler;}protected
function
getSession(){return
Environment::getSession();}}}namespace NetteX\Iterators{use
NetteX;class
CachingIterator
extends\CachingIterator
implements\Countable{private$counter=0;function
__construct($iterator){if(is_array($iterator)||$iterator
instanceof\stdClass){$iterator=new\ArrayIterator($iterator);}elseif($iterator
instanceof\Traversable){if($iterator
instanceof\IteratorAggregate){$iterator=$iterator->getIterator();}elseif(!$iterator
instanceof\Iterator){$iterator=new\IteratorIterator($iterator);}}else{throw
new
NetteX\InvalidArgumentException("Invalid argument passed to foreach resp. ".__CLASS__."; array or Traversable expected, ".(is_object($iterator)?get_class($iterator):gettype($iterator))." given.");}parent::__construct($iterator,0);}function
isFirst($width=NULL){return$this->counter===1||($width&&$this->counter!==0&&(($this->counter-1)%$width)===0);}function
isLast($width=NULL){return!$this->hasNext()||($width&&($this->counter
%$width)===0);}function
isEmpty(){return$this->counter===0;}function
isOdd(){return$this->counter
%
2===1;}function
isEven(){return$this->counter
%
2===0;}function
getCounter(){return$this->counter;}function
count(){$inner=$this->getInnerIterator();if($inner
instanceof\Countable){return$inner->count();}else{throw
new
NetteX\NotSupportedException('Iterator is not countable.');}}function
next(){parent::next();if(parent::valid()){$this->counter++;}}function
rewind(){parent::rewind();$this->counter=parent::valid()?1:0;}function
getNextKey(){return$this->getInnerIterator()->key();}function
getNextValue(){return$this->getInnerIterator()->current();}function
__call($name,$args){return
NetteX\ObjectMixin::call($this,$name,$args);}function&__get($name){return
NetteX\ObjectMixin::get($this,$name);}function
__set($name,$value){return
NetteX\ObjectMixin::set($this,$name,$value);}function
__isset($name){return
NetteX\ObjectMixin::has($this,$name);}function
__unset($name){NetteX\ObjectMixin::remove($this,$name);}}class
Filter
extends\FilterIterator{private$callback;function
__construct(\Iterator$iterator,$callback){parent::__construct($iterator);$this->callback=$callback;}function
accept(){return
call_user_func($this->callback,$this);}}class
InstanceFilter
extends\FilterIterator
implements\Countable{private$type;function
__construct(\Iterator$iterator,$type){$this->type=$type;parent::__construct($iterator);}function
accept(){return$this->current()instanceof$this->type;}function
count(){return
iterator_count($this);}}class
Mapper
extends\IteratorIterator{private$callback;function
__construct(\Traversable$iterator,$callback){parent::__construct($iterator);$this->callback=$callback;}function
current(){return
call_user_func($this->callback,parent::current(),parent::key());}}class
RecursiveFilter
extends\FilterIterator
implements\RecursiveIterator{private$callback;private$childrenCallback;function
__construct(\RecursiveIterator$iterator,$callback,$childrenCallback=NULL){parent::__construct($iterator);$this->callback=$callback;$this->childrenCallback=$childrenCallback;}function
accept(){return$this->callback===NULL||call_user_func($this->callback,$this);}function
hasChildren(){return$this->getInnerIterator()->hasChildren()&&($this->childrenCallback===NULL||call_user_func($this->childrenCallback,$this));}function
getChildren(){return
new
self($this->getInnerIterator()->getChildren(),$this->callback,$this->childrenCallback);}}class
Recursor
extends\IteratorIterator
implements\RecursiveIterator,\Countable{function
hasChildren(){$obj=$this->current();return($obj
instanceof\IteratorAggregate&&$obj->getIterator()instanceof\RecursiveIterator)||$obj
instanceof\RecursiveIterator;}function
getChildren(){$obj=$this->current();return$obj
instanceof\IteratorAggregate?$obj->getIterator():$obj;}function
count(){return
iterator_count($this);}}}namespace NetteX\Latte{use
NetteX;use
NetteX\StringUtils;use
NetteX\Utils\Tokenizer;class
DefaultMacros
extends
NetteX\Object{public
static$defaultMacros=array('syntax'=>'%:macroSyntax%','/syntax'=>'%:macroSyntax%','block'=>'<?php %:macroBlock% ?>','/block'=>'<?php %:macroBlockEnd% ?>','capture'=>'<?php %:macroCapture% ?>','/capture'=>'<?php %:macroCaptureEnd% ?>','snippet'=>'<?php %:macroSnippet% ?>','/snippet'=>'<?php %:macroSnippetEnd% ?>','cache'=>'<?php %:macroCache% ?>','/cache'=>'<?php array_pop($_l->g->caches)->save(); } ?>','if'=>'<?php if (%%): ?>','elseif'=>'<?php elseif (%%): ?>','else'=>'<?php else: ?>','/if'=>'<?php endif ?>','ifset'=>'<?php if (isset(%:macroIfset%)): ?>','/ifset'=>'<?php endif ?>','elseifset'=>'<?php elseif (isset(%%)): ?>','foreach'=>'<?php foreach (%:macroForeach%): ?>','/foreach'=>'<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>','for'=>'<?php for (%%): ?>','/for'=>'<?php endfor ?>','while'=>'<?php while (%%): ?>','/while'=>'<?php endwhile ?>','continueIf'=>'<?php if (%%) continue ?>','breakIf'=>'<?php if (%%) break ?>','first'=>'<?php if ($iterator->isFirst(%%)): ?>','/first'=>'<?php endif ?>','last'=>'<?php if ($iterator->isLast(%%)): ?>','/last'=>'<?php endif ?>','sep'=>'<?php if (!$iterator->isLast(%%)): ?>','/sep'=>'<?php endif ?>','include'=>'<?php %:macroInclude% ?>','extends'=>'<?php %:macroExtends% ?>','layout'=>'<?php %:macroExtends% ?>','plink'=>'<?php echo %:escape%(%:macroPlink%) ?>','link'=>'<?php echo %:escape%(%:macroLink%) ?>','ifCurrent'=>'<?php %:macroIfCurrent% ?>','/ifCurrent'=>'<?php endif ?>','widget'=>'<?php %:macroControl% ?>','control'=>'<?php %:macroControl% ?>','@href'=>' href="<?php echo %:escape%(%:macroLink%) ?>"','@class'=>'<?php if ($_l->tmp = trim(implode(" ", array_unique(%:formatArray%)))) echo \' class="\' . %:escape%($_l->tmp) . \'"\' ?>','@attr'=>'<?php if (($_l->tmp = (string) (%%)) !== \'\') echo \' @@="\' . %:escape%($_l->tmp) . \'"\' ?>','attr'=>'<?php echo NetteX\Utils\Html::el(NULL)->%:macroAttr%attributes() ?>','contentType'=>'<?php %:macroContentType% ?>','status'=>'<?php NetteX\Environment::getHttpResponse()->setCode(%%) ?>','var'=>'<?php %:macroVar% ?>','assign'=>'<?php %:macroVar% ?>','default'=>'<?php %:macroDefault% ?>','dump'=>'<?php %:macroDump% ?>','debugbreak'=>'<?php %:macroDebugbreak% ?>','l'=>'{','r'=>'}','!_'=>'<?php echo %:macroTranslate% ?>','_'=>'<?php echo %:escape%(%:macroTranslate%) ?>','!='=>'<?php echo %:macroModifiers% ?>','='=>'<?php echo %:escape%(%:macroModifiers%) ?>','!$'=>'<?php echo %:macroDollar% ?>','$'=>'<?php echo %:escape%(%:macroDollar%) ?>','?'=>'<?php %:macroModifiers% ?>');const
RE_IDENTIFIER='[_a-zA-Z\x7F-\xFF][_a-zA-Z0-9\x7F-\xFF]*';const
T_WHITESPACE=T_WHITESPACE,T_COMMENT=T_COMMENT,T_SYMBOL=-1,T_NUMBER=-2,T_VARIABLE=-3;public$macros;private$tokenizer;private$filter;private$nodes=array();private$blocks=array();private$namedBlocks=array();private$extends;private$uniq;private$cacheCounter;const
BLOCK_NAMED=1,BLOCK_CAPTURE=2,BLOCK_ANONYMOUS=3;function
__construct(){$this->macros=self::$defaultMacros;$this->tokenizer=new
Tokenizer(array(self::T_WHITESPACE=>'\s+',self::T_COMMENT=>'(?s)/\*.*?\*/',Engine::RE_STRING,'(?:true|false|null|and|or|xor|clone|new|instanceof|return|continue|break|[A-Z_][A-Z0-9_]{2,})(?!\w)','\([a-z]+\)',self::T_VARIABLE=>'\$\w+',self::T_NUMBER=>'[+-]?[0-9]+(?:\.[0-9]+)?(?:e[0-9]+)?',self::T_SYMBOL=>'\w+(?:-\w+)*','::|=>|[^"\']'),'u');}function
initialize($filter,&$s){$this->filter=$filter;$this->nodes=array();$this->blocks=array();$this->namedBlocks=array();$this->extends=NULL;$this->uniq=StringUtils::random();$this->cacheCounter=0;$filter->context=Engine::CONTEXT_TEXT;$filter->escape='NetteX\Templating\DefaultHelpers::escapeHtml';}function
finalize(&$s){if(count($this->blocks)===1){$s.=$this->macro('/block');}elseif($this->blocks){throw
new
ParseException("There are unclosed blocks.",0,$this->filter->line);}if($this->namedBlocks||$this->extends){$s='<?php
if ($_l->extends) {
	ob_start();
} elseif (isset($presenter, $control) && $presenter->isAjax() && $control->isControlInvalid()) {
	return NetteX\Latte\DefaultMacros::renderSnippets($control, $_l, get_defined_vars());
}
?>'.$s.'<?php
if ($_l->extends) {
	ob_end_clean();
	NetteX\Latte\DefaultMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
';}else{$s='<?php
if (isset($presenter, $control) && $presenter->isAjax() && $control->isControlInvalid()) {
	return NetteX\Latte\DefaultMacros::renderSnippets($control, $_l, get_defined_vars());
}
?>'.$s;}if($this->namedBlocks){$uniq=$this->uniq;foreach(array_reverse($this->namedBlocks,TRUE)as$name=>$foo){$code=&$this->namedBlocks[$name];$namere=preg_quote($name,'#');$s=StringUtils::replace($s,"#{block $namere} \?>(.*)<\?php {/block $namere}#sU",function($matches)use($name,&$code,$uniq){list(,$content)=$matches;$func='_lb'.substr(md5($uniq.$name),0,10).'_'.preg_replace('#[^a-z0-9_]#i','_',$name);$code="//\n// block $name\n//\n"."if (!function_exists(\$_l->blocks[".var_export($name,TRUE)."][] = '$func')) { "."function $func(\$_l, \$_args) { ".(PHP_VERSION_ID>50208?'extract($_args)':'foreach ($_args as $__k => $__v) $$__k = $__v').($name[0]==='_'?'; $control->validateControl('.var_export(substr($name,1),TRUE).')':'')."\n?>$content<?php\n}}";return'';});}$s="<?php\n\n".implode("\n\n\n",$this->namedBlocks)."\n\n//\n// end of blocks\n//\n?>".$s;}$s="<?php\n".'$_l = NetteX\Latte\DefaultMacros::initRuntime($template, '.var_export($this->extends,TRUE).', '.var_export($this->uniq,TRUE).'); unset($_extends);'."\n?>".$s;}function
macro($macro,$content='',$modifiers=''){if(func_num_args()===1){list(,$macro,$content,$modifiers)=StringUtils::match($macro,'#^(/?[a-z0-9.:]+)?(.*?)(\\|[a-z](?:'.Engine::RE_STRING.'|[^\'"]+)*)?$()#is');$content=trim($content);}if($macro===''){$macro=substr($content,0,2);if(!isset($this->macros[$macro])){$macro=substr($content,0,1);if(!isset($this->macros[$macro])){return
FALSE;}}$content=substr($content,strlen($macro));}elseif(!isset($this->macros[$macro])){return
FALSE;}$closing=$macro[0]==='/';if($closing){$node=array_pop($this->nodes);if(!$node||"/$node->name"!==$macro||($content&&!StringUtils::startsWith("$node->content ","$content "))||$modifiers){$macro.=$content?' ':'';throw
new
ParseException("Unexpected macro {{$macro}{$content}{$modifiers}}".($node?", expecting {/$node->name}".($content&&$node->content?" or eventually {/$node->name $node->content}":''):''),0,$this->filter->line);}$node->content=$node->modifiers='';}else{$node=(object)NULL;$node->name=$macro;$node->content=$content;$node->modifiers=$modifiers;if(isset($this->macros["/$macro"])){$this->nodes[]=$node;}}$This=$this;return
StringUtils::replace($this->macros[$macro],'#%(.*?)%#',function($m)use($This,$node){if($m[1]){return
callback($m[1][0]===':'?array($This,substr($m[1],1)):$m[1])->invoke($node->content,$node->modifiers);}else{return$This->formatMacroArgs($node->content);}});}function
tagMacro($name,$attrs,$closing){$knownTags=array('include'=>'block','for'=>'each','block'=>'name','if'=>'cond','elseif'=>'cond');return$this->macro($closing?"/$name":$name,isset($knownTags[$name],$attrs[$knownTags[$name]])?$attrs[$knownTags[$name]]:preg_replace("#'([^\\'$]+)'#",'$1',substr(var_export($attrs,TRUE),8,-1)),isset($attrs['modifiers'])?$attrs['modifiers']:'');}function
attrsMacro($code,$attrs,$closing){foreach($attrs
as$name=>$content){if(substr($name,0,5)==='attr-'){if(!$closing){$pos=strrpos($code,'>');if($code[$pos-1]==='/')$pos--;$code=substr_replace($code,str_replace('@@',substr($name,5),$this->macro("@attr",$content)),$pos,0);}unset($attrs[$name]);}}$left=$right=array();foreach($this->macros
as$name=>$foo){if($name[0]==='@'){$name=substr($name,1);if(isset($attrs[$name])){if(!$closing){$pos=strrpos($code,'>');if($code[$pos-1]==='/')$pos--;$code=substr_replace($code,$this->macro("@$name",$attrs[$name]),$pos,0);}unset($attrs[$name]);}}if(!isset($this->macros["/$name"])){continue;}$macro=$closing?"/$name":$name;if(isset($attrs[$name])){if($closing){$right[]=array($macro,'');}else{array_unshift($left,array($macro,$attrs[$name]));}}$innerName="inner-$name";if(isset($attrs[$innerName])){if($closing){$left[]=array($macro,'');}else{array_unshift($right,array($macro,$attrs[$innerName]));}}$tagName="tag-$name";if(isset($attrs[$tagName])){array_unshift($left,array($name,$attrs[$tagName]));$right[]=array("/$name",'');}unset($attrs[$name],$attrs[$innerName],$attrs[$tagName]);}if($attrs){return
FALSE;}$s='';foreach($left
as$item){$s.=$this->macro($item[0],$item[1]);}$s.=$code;foreach($right
as$item){$s.=$this->macro($item[0],$item[1]);}return$s;}function
macroDollar($var,$modifiers){return$this->formatModifiers($this->formatMacroArgs('$'.$var),$modifiers);}function
macroTranslate($var,$modifiers){return$this->formatModifiers($this->formatMacroArgs($var),'|translate'.$modifiers);}function
macroSyntax($var){switch($var){case'':case'latte':$this->filter->setDelimiters('\\{(?![\\s\'"{}])','\\}');break;case'double':$this->filter->setDelimiters('\\{\\{(?![\\s\'"{}])','\\}\\}');break;case'asp':$this->filter->setDelimiters('<%\s*','\s*%>');break;case'python':$this->filter->setDelimiters('\\{[{%]\s*','\s*[%}]\\}');break;case'off':$this->filter->setDelimiters('[^\x00-\xFF]','');break;default:throw
new
ParseException("Unknown syntax '$var'",0,$this->filter->line);}}function
macroInclude($content,$modifiers,$isDefinition=FALSE){$destination=$this->fetchToken($content);$params=$this->formatArray($content).($content?' + ':'');if($destination===NULL){throw
new
ParseException("Missing destination in {include}",0,$this->filter->line);}elseif($destination[0]==='#'){$destination=ltrim($destination,'#');if(!StringUtils::match($destination,'#^\$?'.self::RE_IDENTIFIER.'$#')){throw
new
ParseException("Included block name must be alphanumeric string, '$destination' given.",0,$this->filter->line);}$parent=$destination==='parent';if($destination==='parent'||$destination==='this'){$item=end($this->blocks);while($item&&$item[0]!==self::BLOCK_NAMED)$item=prev($this->blocks);if(!$item){throw
new
ParseException("Cannot include $destination block outside of any block.",0,$this->filter->line);}$destination=$item[1];}$name=$destination[0]==='$'?$destination:var_export($destination,TRUE);$params.=$isDefinition?'get_defined_vars()':'$template->getParams()';$cmd=isset($this->namedBlocks[$destination])&&!$parent?"call_user_func(reset(\$_l->blocks[$name]), \$_l, $params)":'NetteX\Latte\DefaultMacros::callBlock'.($parent?'Parent':'')."(\$_l, $name, $params)";return$modifiers?"ob_start(); $cmd; echo ".$this->formatModifiers('ob_get_clean()',$modifiers):$cmd;}else{$destination=$this->formatString($destination);$cmd='NetteX\Latte\DefaultMacros::includeTemplate('.$destination.', '.$params.'$template->getParams(), $_l->templates['.var_export($this->uniq,TRUE).'])';return$modifiers?'echo '.$this->formatModifiers($cmd.'->__toString(TRUE)',$modifiers):$cmd.'->render()';}}function
macroExtends($content){if(!$content){throw
new
ParseException("Missing destination in {extends}",0,$this->filter->line);}if(!empty($this->blocks)){throw
new
ParseException("{extends} must be placed outside any block.",0,$this->filter->line);}if($this->extends!==NULL){throw
new
ParseException("Multiple {extends} declarations are not allowed.",0,$this->filter->line);}$this->extends=$content!=='none';return$this->extends?'$_l->extends = '.($content==='auto'?'$layout':$this->formatMacroArgs($content)):'';}function
macroBlock($content,$modifiers){$name=$this->fetchToken($content);if($name===NULL){$this->blocks[]=array(self::BLOCK_ANONYMOUS,NULL,$modifiers);return$modifiers===''?'':'ob_start()';}else{$name=ltrim($name,'#');if(!StringUtils::match($name,'#^'.self::RE_IDENTIFIER.'$#')){throw
new
ParseException("Block name must be alphanumeric string, '$name' given.",0,$this->filter->line);}elseif(isset($this->namedBlocks[$name])){throw
new
ParseException("Cannot redeclare block '$name'",0,$this->filter->line);}$top=empty($this->blocks);$this->namedBlocks[$name]=$name;$this->blocks[]=array(self::BLOCK_NAMED,$name,'');if($name[0]==='_'){$tag=$this->fetchToken($content);$tag=trim($tag,'<>');$namePhp=var_export(substr($name,1),TRUE);if(!$tag)$tag='div';return"?><$tag id=\"<?php echo \$control->getSnippetId($namePhp) ?>\"><?php ".$this->macroInclude('#'.$name,$modifiers)." ?></$tag><?php {block $name}";}elseif(!$top){return$this->macroInclude('#'.$name,$modifiers,TRUE)."{block $name}";}elseif($this->extends){return"{block $name}";}else{return'if (!$_l->extends) { '.$this->macroInclude('#'.$name,$modifiers,TRUE)."; } {block $name}";}}}function
macroBlockEnd($content){list($type,$name,$modifiers)=array_pop($this->blocks);if($type===self::BLOCK_CAPTURE){$this->blocks[]=array($type,$name,$modifiers);return$this->macroCaptureEnd($content);}elseif($type===self::BLOCK_NAMED){return"{/block $name}";}else{return$modifiers===''?'':'echo '.$this->formatModifiers('ob_get_clean()',$modifiers);}}function
macroSnippet($content){return$this->macroBlock('_'.$content,'');}function
macroSnippetEnd($content){return$this->macroBlockEnd('','');}function
macroCapture($content,$modifiers){$name=$this->fetchToken($content);if(substr($name,0,1)!=='$'){throw
new
ParseException("Invalid capture block parameter '$name'",0,$this->filter->line);}$this->blocks[]=array(self::BLOCK_CAPTURE,$name,$modifiers);return'ob_start()';}function
macroCaptureEnd($content){list($type,$name,$modifiers)=array_pop($this->blocks);return$name.'='.$this->formatModifiers('ob_get_clean()',$modifiers);}function
macroCache($content){return'if (NetteX\Caching\OutputHelper::create('.var_export($this->uniq.':'.$this->cacheCounter++,TRUE).', $_l->g->caches'.$this->formatArray($content,', ').')) {';}function
macroForeach($content){return'$iterator = $_l->its[] = new NetteX\Iterators\CachingIterator('.preg_replace('#(.*)\s+as\s+#i','$1) as ',$this->formatMacroArgs($content),1);}function
macroIfset($content){if(strpos($content,'#')===FALSE)return$content;$list=array();while(($name=$this->fetchToken($content))!==NULL){$list[]=$name[0]==='#'?'$_l->blocks["'.substr($name,1).'"]':$name;}return
implode(', ',$list);}function
macroAttr($content){return
StringUtils::replace($content.' ','#\)\s+#',')->');}function
macroContentType($content){if(strpos($content,'html')!==FALSE){$this->filter->escape='NetteX\Templating\DefaultHelpers::escapeHtml';$this->filter->context=Engine::CONTEXT_TEXT;}elseif(strpos($content,'xml')!==FALSE){$this->filter->escape='NetteX\Templating\DefaultHelpers::escapeXml';$this->filter->context=Engine::CONTEXT_NONE;}elseif(strpos($content,'javascript')!==FALSE){$this->filter->escape='NetteX\Templating\DefaultHelpers::escapeJs';$this->filter->context=Engine::CONTEXT_NONE;}elseif(strpos($content,'css')!==FALSE){$this->filter->escape='NetteX\Templating\DefaultHelpers::escapeCss';$this->filter->context=Engine::CONTEXT_NONE;}elseif(strpos($content,'plain')!==FALSE){$this->filter->escape='';$this->filter->context=Engine::CONTEXT_NONE;}else{$this->filter->escape='$template->escape';$this->filter->context=Engine::CONTEXT_NONE;}if(strpos($content,'/')){return'NetteX\Environment::getHttpResponse()->setHeader("Content-Type", "'.$content.'")';}}function
macroDump($content){return'NetteX\Diagnostics\Debugger::barDump('.($content?'array('.var_export($this->formatMacroArgs($content),TRUE)." => $content)":'get_defined_vars()').', "Template " . str_replace(dirname(dirname($template->getFile())), "\xE2\x80\xA6", $template->getFile()))';}function
macroDebugbreak(){return'if (function_exists("debugbreak")) debugbreak(); elseif (function_exists("xdebug_break")) xdebug_break()';}function
macroControl($content){$pair=$this->fetchToken($content);if($pair===NULL){throw
new
ParseException("Missing control name in {control}",0,$this->filter->line);}$pair=explode(':',$pair,2);$name=$this->formatString($pair[0]);$method=isset($pair[1])?ucfirst($pair[1]):'';$method=StringUtils::match($method,'#^('.self::RE_IDENTIFIER.'|)$#')?"render$method":"{\"render$method\"}";$param=$this->formatArray($content);if(strpos($content,'=>')===FALSE)$param=substr($param,6,-1);return($name[0]==='$'?"if (is_object($name)) \$_ctrl = $name; else ":'').'$_ctrl = $control->getWidget('.$name.'); '.'if ($_ctrl instanceof NetteX\Application\UI\IPartiallyRenderable) $_ctrl->validateControl(); '."\$_ctrl->$method($param)";}function
macroLink($content,$modifiers){return$this->formatModifiers('$control->link('.$this->formatLink($content).')',$modifiers);}function
macroPlink($content,$modifiers){return$this->formatModifiers('$presenter->link('.$this->formatLink($content).')',$modifiers);}function
macroIfCurrent($content){return($content?'try { $presenter->link('.$this->formatLink($content).'); } catch (NetteX\Application\UI\InvalidLinkException $e) {}':'').'; if ($presenter->getLastCreatedRequestFlag("current")):';}private
function
formatLink($content){return$this->formatString($this->fetchToken($content)).$this->formatArray($content,', ');}function
macroVar($content,$modifiers,$extract=FALSE){$out='';$var=TRUE;foreach($this->parseMacro($content)as$token){if($var&&($token['type']===self::T_SYMBOL||$token['type']===self::T_VARIABLE)){if($extract){$out.="'".trim($token['value'],"'$")."'";}else{$out.='$'.trim($token['value'],"'$");}}elseif(($token['value']==='='||$token['value']==='=>')&&$token['depth']===0){$out.=$extract?'=>':'=';$var=FALSE;}elseif($token['value']===','&&$token['depth']===0){$out.=$extract?',':';';$var=TRUE;}else{$out.=$token['value'];}}return$out;}function
macroDefault($content){return'extract(array('.$this->macroVar($content,'',TRUE).'), EXTR_SKIP)';}function
macroModifiers($content,$modifiers){return$this->formatModifiers($this->formatMacroArgs($content),$modifiers);}function
escape($content){return$this->filter->escape;}function
formatModifiers($var,$modifiers){if(!$modifiers)return$var;$inside=FALSE;foreach($this->parseMacro(ltrim($modifiers,'|'))as$token){if($token['type']===self::T_WHITESPACE){$var=rtrim($var).' ';}elseif(!$inside){if($token['type']===self::T_SYMBOL){$var="\$template->".trim($token['value'],"'")."($var";$inside=TRUE;}else{throw
new
ParseException("Modifier name must be alphanumeric string, '$token[value]' given.",0,$this->filter->line);}}else{if($token['value']===':'||$token['value']===','){$var=$var.', ';}elseif($token['value']==='|'){$var=$var.')';$inside=FALSE;}else{$var.=$token['value'];}}}return$inside?"$var)":$var;}function
fetchToken(&$s){if($matches=StringUtils::match($s,'#^((?>'.Engine::RE_STRING.'|[^\'"\s,]+)+)\s*,?\s*(.*)$#s')){$s=$matches[2];return$matches[1];}return
NULL;}function
formatMacroArgs($input){$out='';foreach($this->parseMacro($input)as$token){$out.=$token['value'];}return$out;}function
formatArray($input,$prefix=''){$tokens=$this->parseMacro($input);if(!$tokens){return'';}$out='';$expand=NULL;$tokens[]=NULL;foreach($tokens
as$token){if($token['value']==='(expand)'&&$token['depth']===0){$expand=TRUE;$out.='),';}elseif($expand&&($token['value']===','||$token['value']===NULL)&&!$token['depth']){$expand=FALSE;$out.=', array(';}else{$out.=$token['value'];}}return$prefix.($expand===NULL?"array($out)":"array_merge(array($out))");}function
formatString($s){static$keywords=array('true'=>1,'false'=>1,'null'=>1);return(is_numeric($s)||strspn($s,'\'"$')||isset($keywords[strtolower($s)]))?$s:'"'.$s.'"';}private
function
parseMacro($input){$this->tokenizer->tokenize($input);$inTernary=$lastSymbol=$prev=NULL;$tokens=$arrays=array();$n=-1;while(++$n<count($this->tokenizer->tokens)){$token=$this->tokenizer->tokens[$n];$token['depth']=$depth=count($arrays);if($token['type']===self::T_COMMENT){continue;}elseif($token['type']===self::T_WHITESPACE){$tokens[]=$token;continue;}elseif($token['type']===self::T_SYMBOL&&($prev===NULL||in_array($prev['value'],array(',','(','[','=','=>',':','?')))){$lastSymbol=count($tokens);}elseif(is_int($lastSymbol)&&in_array($token['value'],array(',',')',']','=','=>',':','|'))){$tokens[$lastSymbol]['value']="'".$tokens[$lastSymbol]['value']."'";$lastSymbol=NULL;}else{$lastSymbol=NULL;}if($token['value']==='?'){$inTernary=$depth;}elseif($token['value']===':'){$inTernary=NULL;}elseif($inTernary===$depth&&($token['value']===','||$token['value']===')'||$token['value']===']')){$tokens[]=Tokenizer::createToken(':')+array('depth'=>$depth);$tokens[]=Tokenizer::createToken('null')+array('depth'=>$depth);$inTernary=NULL;}if($token['value']==='['){if($arrays[]=$prev['value']!==']'&&$prev['type']!==self::T_SYMBOL&&$prev['type']!==self::T_VARIABLE){$tokens[]=Tokenizer::createToken('array')+array('depth'=>$depth);$token=Tokenizer::createToken('(');}}elseif($token['value']===']'){if(array_pop($arrays)===TRUE){$token=Tokenizer::createToken(')');}}elseif($token['value']==='('){$arrays[]='(';}elseif($token['value']===')'){array_pop($arrays);}$tokens[]=$prev=$token;}if(is_int($lastSymbol)){$tokens[$lastSymbol]['value']="'".$tokens[$lastSymbol]['value']."'";}if($inTernary!==NULL){$tokens[]=Tokenizer::createToken(':')+array('depth'=>count($arrays));$tokens[]=Tokenizer::createToken('null')+array('depth'=>count($arrays));}return$tokens;}static
function
callBlock($context,$name,$params){if(empty($context->blocks[$name])){throw
new
NetteX\InvalidStateException("Cannot include undefined block '$name'.");}$block=reset($context->blocks[$name]);$block($context,$params);}static
function
callBlockParent($context,$name,$params){if(empty($context->blocks[$name])||($block=next($context->blocks[$name]))===FALSE){throw
new
NetteX\InvalidStateException("Cannot include undefined parent block '$name'.");}$block($context,$params);}static
function
includeTemplate($destination,$params,$template){if($destination
instanceof
NetteX\Templating\ITemplate){$tpl=$destination;}elseif($destination==NULL){throw
new
NetteX\InvalidArgumentException("Template file name was not specified.");}else{$tpl=clone$template;if($template
instanceof
NetteX\Templating\IFileTemplate){if(substr($destination,0,1)!=='/'&&substr($destination,1,1)!==':'){$destination=dirname($template->getFile()).'/'.$destination;}$tpl->setFile($destination);}}$tpl->setParams($params);return$tpl;}static
function
initRuntime($template,$extends,$realFile){$local=(object)NULL;if(isset($template->_l)){$local->blocks=&$template->_l->blocks;$local->templates=&$template->_l->templates;}$local->templates[$realFile]=$template;$local->extends=is_bool($extends)?$extends:(empty($template->_extends)?FALSE:$template->_extends);unset($template->_l,$template->_extends);if(!isset($template->_g)){$template->_g=(object)NULL;}$local->g=$template->_g;if(!empty($local->g->caches)){end($local->g->caches)->addFile($template->getFile());}return$local;}static
function
renderSnippets($control,$local,$params){$payload=$control->getPresenter()->getPayload();if(isset($local->blocks)){foreach($local->blocks
as$name=>$function){if($name[0]!=='_'||!$control->isControlInvalid(substr($name,1)))continue;ob_start();$function=reset($function);$function($local,$params);$payload->snippets[$control->getSnippetId(substr($name,1))]=ob_get_clean();}}if($control
instanceof
NetteX\Application\UI\Control){foreach($control->getComponents(FALSE,'NetteX\Application\UI\Control')as$child){if($child->isControlInvalid()){$child->render();}}}}}class
Engine
extends
NetteX\Object{const
RE_STRING='\'(?:\\\\.|[^\'\\\\])*\'|"(?:\\\\.|[^"\\\\])*"';const
HTML_PREFIX='n:';private$handler;private$macroRe;private$input,$output;private$offset;private$quote;private$tags;public$context,$escape;const
CONTEXT_TEXT='text',CONTEXT_CDATA='cdata',CONTEXT_TAG='tag',CONTEXT_ATTRIBUTE='attribute',CONTEXT_NONE='none',CONTEXT_COMMENT='comment';function
setHandler($handler){$this->handler=$handler;return$this;}function
getHandler(){if($this->handler===NULL){$this->handler=new
DefaultMacros;}return$this->handler;}function
__invoke($s){if(!StringUtils::checkEncoding($s)){throw
new
ParseException('Template is not valid UTF-8 stream.');}if(!$this->macroRe){$this->setDelimiters('\\{(?![\\s\'"{}*])','\\}');}$this->context=Engine::CONTEXT_NONE;$this->escape='$template->escape';$this->getHandler()->initialize($this,$s);$s=$this->parse("\n".$s);$this->getHandler()->finalize($s);return$s;}private
function
parse($s){$this->input=&$s;$this->offset=0;$this->output='';$this->tags=array();$len=strlen($s);while($this->offset<$len){$matches=$this->{"context$this->context"}();if(!$matches){break;}elseif(!empty($matches['comment'])){}elseif(!empty($matches['macro'])){$code=$this->handler->macro($matches['macro']);if($code===FALSE){throw
new
ParseException("Unknown macro {{$matches['macro']}}",0,$this->line);}$nl=isset($matches['newline'])?"\n":'';if($nl&&$matches['indent']&&strncmp($code,'<?php echo ',11)){$this->output.="\n".$code;}else{$this->output.=$matches['indent'].$code.(substr($code,-2)==='?>'&&$this->output!==''?$nl:'');}}else{$this->output.=$matches[0];}}foreach($this->tags
as$tag){if(!$tag->isMacro&&!empty($tag->attrs)){throw
new
ParseException("Missing end tag </$tag->name> for macro-attribute ".self::HTML_PREFIX.implode(' and '.self::HTML_PREFIX,array_keys($tag->attrs)).".",0,$this->line);}}return$this->output.substr($this->input,$this->offset);}private
function
contextText(){$matches=$this->match('~
			(?:\n[ \t]*)?<(?P<closing>/?)(?P<tag>[a-z0-9:]+)|  ##  begin of HTML tag <tag </tag - ignores <!DOCTYPE
			<(?P<htmlcomment>!--)|           ##  begin of HTML comment <!--
			'.$this->macroRe.'           ##  curly tag
		~xsi');if(!$matches||!empty($matches['macro'])||!empty($matches['comment'])){}elseif(!empty($matches['htmlcomment'])){$this->context=self::CONTEXT_COMMENT;$this->escape='NetteX\Templating\DefaultHelpers::escapeHtmlComment';}elseif(empty($matches['closing'])){$tag=$this->tags[]=(object)NULL;$tag->name=$matches['tag'];$tag->closing=FALSE;$tag->isMacro=StringUtils::startsWith($tag->name,self::HTML_PREFIX);$tag->attrs=array();$tag->pos=strlen($this->output);$this->context=self::CONTEXT_TAG;$this->escape='NetteX\Templating\DefaultHelpers::escapeHtml';}else{do{$tag=array_pop($this->tags);if(!$tag){$tag=(object)NULL;$tag->name=$matches['tag'];$tag->isMacro=StringUtils::startsWith($tag->name,self::HTML_PREFIX);}}while(strcasecmp($tag->name,$matches['tag']));$this->tags[]=$tag;$tag->closing=TRUE;$tag->pos=strlen($this->output);$this->context=self::CONTEXT_TAG;$this->escape='NetteX\Templating\DefaultHelpers::escapeHtml';}return$matches;}private
function
contextCData(){$tag=end($this->tags);$matches=$this->match('~
			</'.$tag->name.'(?![a-z0-9:])| ##  end HTML tag </tag
			'.$this->macroRe.'           ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])&&empty($matches['comment'])){$tag->closing=TRUE;$tag->pos=strlen($this->output);$this->context=self::CONTEXT_TAG;$this->escape='NetteX\Templating\DefaultHelpers::escapeHtml';}return$matches;}private
function
contextTag(){$matches=$this->match('~
			(?P<end>\ ?/?>)(?P<tagnewline>[\ \t]*(?=\r|\n))?|  ##  end of HTML tag
			'.$this->macroRe.'|          ##  curly tag
			\s*(?P<attr>[^\s/>={]+)(?:\s*=\s*(?P<value>["\']|[^\s/>{]+))? ## begin of HTML attribute
		~xsi');if(!$matches||!empty($matches['macro'])||!empty($matches['comment'])){}elseif(!empty($matches['end'])){$tag=end($this->tags);$isEmpty=!$tag->closing&&(strpos($matches['end'],'/')!==FALSE||isset(NetteX\Utils\Html::$emptyElements[strtolower($tag->name)]));if($isEmpty){$matches[0]=(NetteX\Utils\Html::$xhtml?' />':'>').(isset($matches['tagnewline'])?$matches['tagnewline']:'');}if($tag->isMacro||!empty($tag->attrs)){if($tag->isMacro){$code=$this->handler->tagMacro(substr($tag->name,strlen(self::HTML_PREFIX)),$tag->attrs,$tag->closing);if($code===FALSE){throw
new
ParseException("Unknown tag-macro <$tag->name>",0,$this->line);}if($isEmpty){$code.=$this->handler->tagMacro(substr($tag->name,strlen(self::HTML_PREFIX)),$tag->attrs,TRUE);}}else{$code=substr($this->output,$tag->pos).$matches[0].(isset($matches['tagnewline'])?"\n":'');$code=$this->handler->attrsMacro($code,$tag->attrs,$tag->closing);if($code===FALSE){throw
new
ParseException("Unknown macro-attribute ".self::HTML_PREFIX.implode(' or '.self::HTML_PREFIX,array_keys($tag->attrs)),0,$this->line);}if($isEmpty){$code=$this->handler->attrsMacro($code,$tag->attrs,TRUE);}}$this->output=substr_replace($this->output,$code,$tag->pos);$matches[0]='';}if($isEmpty){$tag->closing=TRUE;}if(!$tag->closing&&(strcasecmp($tag->name,'script')===0||strcasecmp($tag->name,'style')===0)){$this->context=self::CONTEXT_CDATA;$this->escape='NetteX\Templating\DefaultHelpers::escape'.(strcasecmp($tag->name,'style')?'Js':'Css');}else{$this->context=self::CONTEXT_TEXT;$this->escape='NetteX\Templating\DefaultHelpers::escapeHtml';if($tag->closing)array_pop($this->tags);}}else{$name=$matches['attr'];$value=isset($matches['value'])?$matches['value']:'';if($isSpecial=StringUtils::startsWith($name,self::HTML_PREFIX)){$name=substr($name,strlen(self::HTML_PREFIX));}$tag=end($this->tags);if($isSpecial||$tag->isMacro){if($value==='"'||$value==="'"){if($matches=$this->match('~(.*?)'.$value.'~xsi')){$value=$matches[1];}}$tag->attrs[$name]=$value;$matches[0]='';}elseif($value==='"'||$value==="'"){$this->context=self::CONTEXT_ATTRIBUTE;$this->quote=$value;$this->escape=strncasecmp($name,'on',2)?('NetteX\Templating\DefaultHelpers::escape'.(strcasecmp($name,'style')?'Html':'Css')):'NetteX\Templating\DefaultHelpers::escapeHtmlJs';}}return$matches;}private
function
contextAttribute(){$matches=$this->match('~
			('.$this->quote.')|      ##  1) end of HTML attribute
			'.$this->macroRe.'           ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])&&empty($matches['comment'])){$this->context=self::CONTEXT_TAG;$this->escape='NetteX\Templating\DefaultHelpers::escapeHtml';}return$matches;}private
function
contextComment(){$matches=$this->match('~
			(--\s*>)|                    ##  1) end of HTML comment
			'.$this->macroRe.'           ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])&&empty($matches['comment'])){$this->context=self::CONTEXT_TEXT;$this->escape='NetteX\Templating\DefaultHelpers::escapeHtml';}return$matches;}private
function
contextNone(){$matches=$this->match('~
			'.$this->macroRe.'           ##  curly tag
		~xsi');return$matches;}private
function
match($re){if($matches=StringUtils::match($this->input,$re,PREG_OFFSET_CAPTURE,$this->offset)){$this->output.=substr($this->input,$this->offset,$matches[0][1]-$this->offset);$this->offset=$matches[0][1]+strlen($matches[0][0]);foreach($matches
as$k=>$v)$matches[$k]=$v[0];}return$matches;}function
getLine(){return
substr_count($this->input,"\n",0,$this->offset);}function
setDelimiters($left,$right){$this->macroRe='
			(?:\r?\n?)(?P<comment>\\{\\*.*?\\*\\}[\r\n]{0,2})|
			(?P<indent>\n[\ \t]*)?
			'.$left.'
				(?P<macro>(?:'.self::RE_STRING.'|[^\'"]+?)*?)
			'.$right.'
			(?P<newline>[\ \t]*(?=\r|\n))?
		';return$this;}static
function
formatModifiers($var,$modifiers){trigger_error(__METHOD__.'() is deprecated; use LatteMacros::formatModifiers() instead.',E_USER_WARNING);return
DefaultMacros::formatModifiers($var,$modifiers);}static
function
fetchToken(&$s){trigger_error(__METHOD__.'() is deprecated; use LatteMacros::fetchToken() instead.',E_USER_WARNING);return
DefaultMacros::fetchToken($s);}static
function
formatArray($input,$prefix=''){trigger_error(__METHOD__.'() is deprecated; use LatteMacros::formatArray() instead.',E_USER_WARNING);return
DefaultMacros::formatArray($input,$prefix);}static
function
formatString($s){trigger_error(__METHOD__.'() is deprecated; use LatteMacros::formatString() instead.',E_USER_WARNING);return
DefaultMacros::formatString($s);}}}namespace NetteX\Templating{use
NetteX;class
FilterException
extends
NetteX\InvalidStateException
implements
NetteX\Diagnostics\IPanel{public$sourceFile;public$sourceLine;function
__construct($message,$code=0,$sourceLine=0){$this->sourceLine=(int)$sourceLine;parent::__construct($message,$code);}function
setSourceFile($file){$this->sourceFile=(string)$file;$this->message=rtrim($this->message,'.')." in ".str_replace(dirname(dirname($file)),'...',$file).($this->sourceLine?":$this->sourceLine":'');}function
getTab(){return'Template';}function
getPanel(){$link=NetteX\Diagnostics\Helpers::editorLink($this->sourceFile,$this->sourceLine);return'<p><b>File:</b> '.($link?'<a href="'.htmlspecialchars($link).'">':'').htmlspecialchars($this->sourceFile).($link?'</a>':'').'&nbsp; <b>Line:</b> '.($this->sourceLine?$this->sourceLine:'n/a').'</p>'.($this->sourceLine?'<pre>'.NetteX\Diagnostics\Helpers::highlightFile($this->sourceFile,$this->sourceLine).'</pre>':'');}function
getId(){}}}namespace NetteX\Latte{use
NetteX;class
ParseException
extends
NetteX\Templating\FilterException{}}namespace NetteX\Loaders{use
NetteX;use
NetteX\StringUtils;use
NetteX\Caching\Cache;class
RobotLoader
extends
AutoLoader{public$scanDirs;public$ignoreDirs='.*, *.old, *.bak, *.tmp, temp';public$acceptFiles='*.php, *.php5';public$autoRebuild=TRUE;private$list=array();private$files;private$rebuilt=FALSE;private$cacheStorage;function
__construct(){if(!extension_loaded('tokenizer')){throw
new
NetteX\NotSupportedException("PHP extension Tokenizer is not loaded.");}}function
register(){$cache=$this->getCache();$key=$this->getKey();if(isset($cache[$key])){$this->list=$cache[$key];}else{$this->rebuild();}if(isset($this->list[strtolower(__CLASS__)])&&class_exists('NetteX\Loaders\NetteXLoader',FALSE)){NetteXLoader::getInstance()->unregister();}parent::register();}function
tryLoad($type){$type=ltrim(strtolower($type),'\\');if(isset($this->list[$type][0])&&!is_file($this->list[$type][0])){unset($this->list[$type]);}if(!isset($this->list[$type])){$trace=debug_backtrace();$initiator=&$trace[2]['function'];if($initiator==='class_exists'||$initiator==='interface_exists'){$this->list[$type]=FALSE;if($this->autoRebuild&&$this->rebuilt){$this->getCache()->save($this->getKey(),$this->list,array(Cache::CONSTS=>'NetteX\Framework::REVISION'));}}if($this->autoRebuild&&!$this->rebuilt){$this->rebuild();}}if(isset($this->list[$type][0])){NetteX\Utils\LimitedScope::load($this->list[$type][0]);self::$count++;}}function
rebuild(){$this->getCache()->save($this->getKey(),callback($this,'_rebuildCallback'),array(Cache::CONSTS=>'NetteX\Framework::REVISION'));$this->rebuilt=TRUE;}function
_rebuildCallback(){foreach($this->list
as$pair){if($pair)$this->files[$pair[0]]=$pair[1];}foreach(array_unique($this->scanDirs)as$dir){$this->scanDirectory($dir);}$this->files=NULL;return$this->list;}function
getIndexedClasses(){$res=array();foreach($this->list
as$class=>$pair){if($pair)$res[$pair[2]]=$pair[0];}return$res;}function
addDirectory($path){foreach((array)$path
as$val){$real=realpath($val);if($real===FALSE){throw
new
NetteX\DirectoryNotFoundException("Directory '$val' not found.");}$this->scanDirs[]=$real;}}private
function
addClass($class,$file,$time){$lClass=strtolower($class);if(isset($this->list[$lClass][0])&&($file2=$this->list[$lClass][0])!==$file&&is_file($file2)){if($this->files[$file2]!==filemtime($file2)){$this->scanScript($file2);return$this->addClass($class,$file,$time);}$e=new
NetteX\InvalidStateException("Ambiguous class '$class' resolution; defined in $file and in ".$this->list[$lClass][0].".");{throw$e;}}$this->list[$lClass]=array($file,$time,$class);$this->files[$file]=$time;}private
function
scanDirectory($dir){if(is_dir($dir)){$disallow=array();$iterator=NetteX\Utils\Finder::findFiles(StringUtils::split($this->acceptFiles,'#[,\s]+#'))->filter(function($file)use(&$disallow){return!isset($disallow[$file->getPathname()]);})->from($dir)->exclude(StringUtils::split($this->ignoreDirs,'#[,\s]+#'))->filter($filter=function($dir)use(&$disallow){$path=$dir->getPathname();if(is_file("$path/netterobots.txt")){foreach(file("$path/netterobots.txt")as$s){if($matches=StringUtils::match($s,'#^disallow\\s*:\\s*(\\S+)#i')){$disallow[$path.str_replace('/',DIRECTORY_SEPARATOR,rtrim('/'.ltrim($matches[1],'/'),'/'))]=TRUE;}}}return!isset($disallow[$path]);});$filter(new\SplFileInfo($dir));}else{$iterator=new\ArrayIterator(array(new\SplFileInfo($dir)));}foreach($iterator
as$entry){$path=$entry->getPathname();if(!isset($this->files[$path])||$this->files[$path]!==$entry->getMTime()){$this->scanScript($path);}}}private
function
scanScript($file){$T_NAMESPACE=PHP_VERSION_ID<50300?-1:T_NAMESPACE;$T_NS_SEPARATOR=PHP_VERSION_ID<50300?-1:T_NS_SEPARATOR;$expected=FALSE;$namespace='';$level=$minLevel=0;$time=filemtime($file);$s=file_get_contents($file);foreach($this->list
as$class=>$pair){if($pair&&$pair[0]===$file)unset($this->list[$class]);}if($matches=StringUtils::match($s,'#//nette'.'loader=(\S*)#')){foreach(explode(',',$matches[1])as$name){$this->addClass($name,$file,$time);}return;}foreach(token_get_all($s)as$token){if(is_array($token)){switch($token[0]){case
T_COMMENT:case
T_DOC_COMMENT:case
T_WHITESPACE:continue
2;case$T_NS_SEPARATOR:case
T_STRING:if($expected){$name.=$token[1];}continue
2;case$T_NAMESPACE:case
T_CLASS:case
T_INTERFACE:$expected=$token[0];$name='';continue
2;case
T_CURLY_OPEN:case
T_DOLLAR_OPEN_CURLY_BRACES:$level++;}}if($expected){switch($expected){case
T_CLASS:case
T_INTERFACE:if($level===$minLevel){$this->addClass($namespace.$name,$file,$time);}break;case$T_NAMESPACE:$namespace=$name?$name.'\\':'';$minLevel=$token==='{'?1:0;}$expected=NULL;}if($token==='{'){$level++;}elseif($token==='}'){$level--;}}}function
setCacheStorage(NetteX\Caching\IStorage$storage){$this->cacheStorage=$storage;return$this;}function
getCacheStorage(){return$this->cacheStorage;}protected
function
getCache(){if(!$this->cacheStorage){trigger_error('Missing cache storage.',E_USER_WARNING);$this->cacheStorage=new
NetteX\Caching\Storages\DevNullStorage;}return
new
Cache($this->cacheStorage,'NetteX.RobotLoader');}protected
function
getKey(){return"v2|$this->ignoreDirs|$this->acceptFiles|".implode('|',$this->scanDirs);}}}namespace NetteX\Mail{use
NetteX;use
NetteX\StringUtils;class
MimePart
extends
NetteX\Object{const
ENCODING_BASE64='base64',ENCODING_7BIT='7bit',ENCODING_8BIT='8bit',ENCODING_QUOTED_PRINTABLE='quoted-printable';const
EOL="\r\n";const
LINE_LENGTH=76;private$headers=array();private$parts=array();private$body;function
setHeader($name,$value,$append=FALSE){if(!$name||preg_match('#[^a-z0-9-]#i',$name)){throw
new
NetteX\InvalidArgumentException("Header name must be non-empty alphanumeric string, '$name' given.");}if($value==NULL){if(!$append){unset($this->headers[$name]);}}elseif(is_array($value)){$tmp=&$this->headers[$name];if(!$append||!is_array($tmp)){$tmp=array();}foreach($value
as$email=>$name){if($name!==NULL&&!StringUtils::checkEncoding($name)){throw
new
NetteX\InvalidArgumentException("Name is not valid UTF-8 string.");}if(!preg_match('#^[^@",\s]+@[^@",\s]+\.[a-z]{2,10}$#i',$email)){throw
new
NetteX\InvalidArgumentException("Email address '$email' is not valid.");}if(preg_match('#[\r\n]#',$name)){throw
new
NetteX\InvalidArgumentException("Name must not contain line separator.");}$tmp[$email]=$name;}}else{$value=(string)$value;if(!StringUtils::checkEncoding($value)){throw
new
NetteX\InvalidArgumentException("Header is not valid UTF-8 string.");}$this->headers[$name]=preg_replace('#[\r\n]+#',' ',$value);}return$this;}function
getHeader($name){return
isset($this->headers[$name])?$this->headers[$name]:NULL;}function
clearHeader($name){unset($this->headers[$name]);return$this;}function
getEncodedHeader($name){$offset=strlen($name)+2;if(!isset($this->headers[$name])){return
NULL;}elseif(is_array($this->headers[$name])){$s='';foreach($this->headers[$name]as$email=>$name){if($name!=NULL){$s.=self::encodeHeader(strpbrk($name,'.,;<@>()[]"=?')?'"'.addcslashes($name,'"\\').'"':$name,$offset);$email=" <$email>";}$email.=',';if($s!==''&&$offset+strlen($email)>self::LINE_LENGTH){$s.=self::EOL."\t";$offset=1;}$s.=$email;$offset+=strlen($email);}return
substr($s,0,-1);}else{return
self::encodeHeader($this->headers[$name],$offset);}}function
getHeaders(){return$this->headers;}function
setContentType($contentType,$charset=NULL){$this->setHeader('Content-Type',$contentType.($charset?"; charset=$charset":''));return$this;}function
setEncoding($encoding){$this->setHeader('Content-Transfer-Encoding',$encoding);return$this;}function
getEncoding(){return$this->getHeader('Content-Transfer-Encoding');}function
addPart(MimePart$part=NULL){return$this->parts[]=$part===NULL?new
self:$part;}function
setBody($body){$this->body=$body;return$this;}function
getBody(){return$this->body;}function
generateMessage(){$output='';$boundary='--------'.StringUtils::random();foreach($this->headers
as$name=>$value){$output.=$name.': '.$this->getEncodedHeader($name);if($this->parts&&$name==='Content-Type'){$output.=';'.self::EOL."\tboundary=\"$boundary\"";}$output.=self::EOL;}$output.=self::EOL;$body=(string)$this->body;if($body!==''){switch($this->getEncoding()){case
self::ENCODING_QUOTED_PRINTABLE:$output.=function_exists('quoted_printable_encode')?quoted_printable_encode($body):self::encodeQuotedPrintable($body);break;case
self::ENCODING_BASE64:$output.=rtrim(chunk_split(base64_encode($body),self::LINE_LENGTH,self::EOL));break;case
self::ENCODING_7BIT:$body=preg_replace('#[\x80-\xFF]+#','',$body);case
self::ENCODING_8BIT:$body=str_replace(array("\x00","\r"),'',$body);$body=str_replace("\n",self::EOL,$body);$output.=$body;break;default:throw
new
NetteX\InvalidStateException('Unknown encoding.');}}if($this->parts){if(substr($output,-strlen(self::EOL))!==self::EOL)$output.=self::EOL;foreach($this->parts
as$part){$output.='--'.$boundary.self::EOL.$part->generateMessage().self::EOL;}$output.='--'.$boundary.'--';}return$output;}private
static
function
encodeHeader($s,&$offset=0){$o='';if($offset>=55){$o=self::EOL."\t";$offset=1;}if(strspn($s,"!\"#$%&\'()*+,-./0123456789:;<>@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^`abcdefghijklmnopqrstuvwxyz{|}=? _\r\n\t")===strlen($s)&&($offset+strlen($s)<=self::LINE_LENGTH)){$offset+=strlen($s);return$o.$s;}$o.=str_replace("\n ","\n\t",substr(iconv_mime_encode(str_repeat(' ',$offset),$s,array('scheme'=>'B','input-charset'=>'UTF-8','output-charset'=>'UTF-8')),$offset+2));$offset=strlen($o)-strrpos($o,"\n");return$o;}}class
Message
extends
MimePart{const
HIGH=1,NORMAL=3,LOW=5;public
static$defaultMailer='NetteX\Mail\SendmailMailer';public
static$defaultHeaders=array('MIME-Version'=>'1.0','X-Mailer'=>'NetteX Framework');private$mailer;private$attachments=array();private$inlines=array();private$html;private$basePath;function
__construct(){foreach(self::$defaultHeaders
as$name=>$value){$this->setHeader($name,$value);}$this->setHeader('Date',date('r'));}function
setFrom($email,$name=NULL){$this->setHeader('From',$this->formatEmail($email,$name));return$this;}function
getFrom(){return$this->getHeader('From');}function
addReplyTo($email,$name=NULL){$this->setHeader('Reply-To',$this->formatEmail($email,$name),TRUE);return$this;}function
setSubject($subject){$this->setHeader('Subject',$subject);return$this;}function
getSubject(){return$this->getHeader('Subject');}function
addTo($email,$name=NULL){$this->setHeader('To',$this->formatEmail($email,$name),TRUE);return$this;}function
addCc($email,$name=NULL){$this->setHeader('Cc',$this->formatEmail($email,$name),TRUE);return$this;}function
addBcc($email,$name=NULL){$this->setHeader('Bcc',$this->formatEmail($email,$name),TRUE);return$this;}private
function
formatEmail($email,$name){if(!$name&&preg_match('#^(.+) +<(.*)>$#',$email,$matches)){return
array($matches[2]=>$matches[1]);}else{return
array($email=>$name);}}function
setReturnPath($email){$this->setHeader('Return-Path',$email);return$this;}function
getReturnPath(){return$this->getHeader('From');}function
setPriority($priority){$this->setHeader('X-Priority',(int)$priority);return$this;}function
getPriority(){return$this->getHeader('X-Priority');}function
setHtmlBody($html,$basePath=NULL){$this->html=$html;$this->basePath=$basePath;return$this;}function
getHtmlBody(){return$this->html;}function
addEmbeddedFile($file,$content=NULL,$contentType=NULL){return$this->inlines[$file]=$this->createAttachment($file,$content,$contentType,'inline')->setHeader('Content-ID',$this->getRandomId());}function
addAttachment($file,$content=NULL,$contentType=NULL){return$this->attachments[]=$this->createAttachment($file,$content,$contentType,'attachment');}private
function
createAttachment($file,$content,$contentType,$disposition){$part=new
MimePart;if($content===NULL){$content=file_get_contents($file);if($content===FALSE){throw
new
NetteX\FileNotFoundException("Unable to read file '$file'.");}}else{$content=(string)$content;}$part->setBody($content);$part->setContentType($contentType?$contentType:NetteX\Utils\MimeTypeDetector::fromString($content));$part->setEncoding(preg_match('#(multipart|message)/#A',$contentType)?self::ENCODING_8BIT:self::ENCODING_BASE64);$part->setHeader('Content-Disposition',$disposition.'; filename="'.StringUtils::fixEncoding(basename($file)).'"');return$part;}function
send(){$this->getMailer()->send($this->build());}function
setMailer(IMailer$mailer){$this->mailer=$mailer;return$this;}function
getMailer(){if($this->mailer===NULL){$this->mailer=is_object(self::$defaultMailer)?self::$defaultMailer:new
self::$defaultMailer;}return$this->mailer;}function
generateMessage(){if($this->getHeader('Message-ID')){return
parent::generateMessage();}else{return$this->build()->generateMessage();}}protected
function
build(){$mail=clone$this;$mail->setHeader('Message-ID',$this->getRandomId());$mail->buildHtml();$mail->buildText();$cursor=$mail;if($mail->attachments){$tmp=$cursor->setContentType('multipart/mixed');$cursor=$cursor->addPart();foreach($mail->attachments
as$value){$tmp->addPart($value);}}if($mail->html!=NULL){$tmp=$cursor->setContentType('multipart/alternative');$cursor=$cursor->addPart();$alt=$tmp->addPart();if($mail->inlines){$tmp=$alt->setContentType('multipart/related');$alt=$alt->addPart();foreach($mail->inlines
as$name=>$value){$tmp->addPart($value);}}$alt->setContentType('text/html','UTF-8')->setEncoding(preg_match('#[\x80-\xFF]#',$mail->html)?self::ENCODING_8BIT:self::ENCODING_7BIT)->setBody($mail->html);}$text=$mail->getBody();$mail->setBody(NULL);$cursor->setContentType('text/plain','UTF-8')->setEncoding(preg_match('#[\x80-\xFF]#',$text)?self::ENCODING_8BIT:self::ENCODING_7BIT)->setBody($text);return$mail;}protected
function
buildHtml(){if($this->html
instanceof
NetteX\Templating\ITemplate){$this->html->mail=$this;if($this->basePath===NULL&&$this->html
instanceof
NetteX\Templating\IFileTemplate){$this->basePath=dirname($this->html->getFile());}$this->html=$this->html->__toString(TRUE);}if($this->basePath!==FALSE){$cids=array();$matches=StringUtils::matchAll($this->html,'#(src\s*=\s*|background\s*=\s*|url\()(["\'])(?![a-z]+:|[/\\#])(.+?)\\2#i',PREG_OFFSET_CAPTURE);foreach(array_reverse($matches)as$m){$file=rtrim($this->basePath,'/\\').'/'.$m[3][0];if(!isset($cids[$file])){$cids[$file]=substr($this->addEmbeddedFile($file)->getHeader("Content-ID"),1,-1);}$this->html=substr_replace($this->html,"{$m[1][0]}{$m[2][0]}cid:{$cids[$file]}{$m[2][0]}",$m[0][1],strlen($m[0][0]));}}if(!$this->getSubject()&&$matches=StringUtils::match($this->html,'#<title>(.+?)</title>#is')){$this->setSubject(html_entity_decode($matches[1],ENT_QUOTES,'UTF-8'));}}protected
function
buildText(){$text=$this->getBody();if($text
instanceof
NetteX\Templating\ITemplate){$text->mail=$this;$this->setBody($text->__toString(TRUE));}elseif($text==NULL&&$this->html!=NULL){$text=StringUtils::replace($this->html,array('#<(style|script|head).*</\\1>#Uis'=>'','#<t[dh][ >]#i'=>" $0",'#[ \t\r\n]+#'=>' ','#<(/?p|/?h\d|li|br|/tr)[ >/]#i'=>"\n$0"));$text=html_entity_decode(strip_tags($text),ENT_QUOTES,'UTF-8');$this->setBody(trim($text));}}private
function
getRandomId(){return'<'.StringUtils::random().'@'.(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost')).'>';}}class
SendmailMailer
extends
NetteX\Object
implements
IMailer{function
send(Message$mail){$tmp=clone$mail;$tmp->setHeader('Subject',NULL);$tmp->setHeader('To',NULL);$parts=explode(Message::EOL.Message::EOL,$tmp->generateMessage(),2);NetteX\Diagnostics\Debugger::tryError();$res=mail(str_replace(Message::EOL,PHP_EOL,$mail->getEncodedHeader('To')),str_replace(Message::EOL,PHP_EOL,$mail->getEncodedHeader('Subject')),str_replace(Message::EOL,PHP_EOL,$parts[1]),str_replace(Message::EOL,PHP_EOL,$parts[0]));if(NetteX\Diagnostics\Debugger::catchError($e)){throw
new
NetteX\InvalidStateException('mail(): '.$e->getMessage(),0,$e);}elseif(!$res){throw
new
NetteX\InvalidStateException('Unable to send email.');}}}class
SmtpMailer
extends
NetteX\Object
implements
IMailer{private$connection;private$host;private$port;private$username;private$password;private$secure;private$timeout;function
__construct(array$options=array()){if(isset($options['host'])){$this->host=$options['host'];$this->port=isset($options['port'])?(int)$options['port']:NULL;}else{$this->host=ini_get('SMTP');$this->port=(int)ini_get('smtp_port');}$this->username=isset($options['username'])?$options['username']:'';$this->password=isset($options['password'])?$options['password']:'';$this->secure=isset($options['secure'])?$options['secure']:'';$this->timeout=isset($options['timeout'])?(int)$options['timeout']:20;if(!$this->port){$this->port=$this->secure==='ssl'?465:25;}}function
send(Message$mail){$data=$mail->generateMessage();$this->connect();$from=$mail->getHeader('From');if($from){$from=array_keys($from);$this->write("MAIL FROM:<$from[0]>",250);}foreach(array_merge((array)$mail->getHeader('To'),(array)$mail->getHeader('Cc'),(array)$mail->getHeader('Bcc'))as$email=>$name){$this->write("RCPT TO:<$email>",array(250,251));}$this->write('DATA',354);$data=preg_replace('#^\.#m','..',$data);$this->write($data);$this->write('.',250);$this->write('QUIT',221);$this->disconnect();}private
function
connect(){$this->connection=@fsockopen(($this->secure==='ssl'?'ssl://':'').$this->host,$this->port,$errno,$error,$this->timeout);if(!$this->connection){throw
new
SmtpException($error,$errno);}stream_set_timeout($this->connection,$this->timeout,0);$this->read();$self=isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost';$this->write("EHLO $self");if((int)$this->read()!==250){$this->write("HELO $self",250);}if($this->secure==='tls'){$this->write('STARTTLS',220);if(!stream_socket_enable_crypto($this->connection,TRUE,STREAM_CRYPTO_METHOD_TLS_CLIENT)){throw
new
SmtpException('Unable to connect via TLS.');}}if($this->username!=NULL&&$this->password!=NULL){$this->write('AUTH LOGIN',334);$this->write(base64_encode($this->username),334,'username');$this->write(base64_encode($this->password),235,'password');}}private
function
disconnect(){fclose($this->connection);$this->connection=NULL;}private
function
write($line,$expectedCode=NULL,$message=NULL){fwrite($this->connection,$line.Message::EOL);if($expectedCode&&!in_array((int)$this->read(),(array)$expectedCode)){throw
new
SmtpException('SMTP server did not accept '.($message?$message:$line));}}private
function
read(){$s='';while(($line=fgets($this->connection,1e3))!=NULL){$s.=$line;if(substr($line,3,1)===' '){break;}}return$s;}}class
SmtpException
extends\Exception{}}namespace NetteX\Reflection{use
NetteX;class
Annotation
extends
NetteX\Object
implements
IAnnotation{function
__construct(array$values){foreach($values
as$k=>$v){$this->$k=$v;}}function
__toString(){return$this->value;}}use
NetteX\StringUtils;/**
 * Annotations support for PHP.
 *
 * @author     David Grudl
 * @Annotation
 */final
class
AnnotationsParser{const
RE_STRING='\'(?:\\\\.|[^\'\\\\])*\'|"(?:\\\\.|[^"\\\\])*"';const
RE_IDENTIFIER='[_a-zA-Z\x7F-\xFF][_a-zA-Z0-9\x7F-\xFF-]*';public
static$useReflection;private
static$cache;private
static$timestamps;final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
getAll(\Reflector$r){if($r
instanceof\ReflectionClass){$type=$r->getName();$member='';}elseif($r
instanceof\ReflectionMethod){$type=$r->getDeclaringClass()->getName();$member=$r->getName();}else{$type=$r->getDeclaringClass()->getName();$member='$'.$r->getName();}if(!self::$useReflection){$file=$r
instanceof\ReflectionClass?$r->getFileName():$r->getDeclaringClass()->getFileName();if($file&&isset(self::$timestamps[$file])&&self::$timestamps[$file]!==filemtime($file)){unset(self::$cache[$type]);}unset(self::$timestamps[$file]);}if(isset(self::$cache[$type][$member])){return
self::$cache[$type][$member];}if(self::$useReflection===NULL){self::$useReflection=(bool)ClassType::from(__CLASS__)->getDocComment();}if(self::$useReflection){return
self::$cache[$type][$member]=self::parseComment($r->getDocComment());}else{if(self::$cache===NULL){self::$cache=(array)self::getCache()->offsetGet('list');self::$timestamps=isset(self::$cache['*'])?self::$cache['*']:array();}if(!isset(self::$cache[$type])&&$file){self::$cache['*'][$file]=filemtime($file);self::parseScript($file);self::getCache()->save('list',self::$cache);}if(isset(self::$cache[$type][$member])){return
self::$cache[$type][$member];}else{return
self::$cache[$type][$member]=array();}}}private
static
function
parseComment($comment){static$tokens=array('true'=>TRUE,'false'=>FALSE,'null'=>NULL,''=>TRUE);$matches=StringUtils::matchAll(trim($comment,'/*'),'~
				(?<=\s)@('.self::RE_IDENTIFIER.')[ \t]*      ##  annotation
				(
					\((?>'.self::RE_STRING.'|[^\'")@]+)+\)|  ##  (value)
					[^(@\r\n][^@\r\n]*|)                     ##  value
			~xi');$res=array();foreach($matches
as$match){list(,$name,$value)=$match;if(substr($value,0,1)==='('){$items=array();$key='';$val=TRUE;$value[0]=',';while($m=StringUtils::match($value,'#\s*,\s*(?>('.self::RE_IDENTIFIER.')\s*=\s*)?('.self::RE_STRING.'|[^\'"),\s][^\'"),]*)#A')){$value=substr($value,strlen($m[0]));list(,$key,$val)=$m;if($val[0]==="'"||$val[0]==='"'){$val=substr($val,1,-1);}elseif(is_numeric($val)){$val=1*$val;}else{$lval=strtolower($val);$val=array_key_exists($lval,$tokens)?$tokens[$lval]:$val;}if($key===''){$items[]=$val;}else{$items[$key]=$val;}}$value=count($items)<2&&$key===''?$val:$items;}else{$value=trim($value);if(is_numeric($value)){$value=1*$value;}else{$lval=strtolower($value);$value=array_key_exists($lval,$tokens)?$tokens[$lval]:$value;}}$class=$name.'Annotation';if(class_exists($class)){$res[$name][]=new$class(is_array($value)?$value:array('value'=>$value));}else{$res[$name][]=is_array($value)?new\ArrayObject($value,\ArrayObject::ARRAY_AS_PROPS):$value;}}return$res;}private
static
function
parseScript($file){$T_NAMESPACE=PHP_VERSION_ID<50300?-1:T_NAMESPACE;$T_NS_SEPARATOR=PHP_VERSION_ID<50300?-1:T_NS_SEPARATOR;$s=file_get_contents($file);if(StringUtils::match($s,'#//nette'.'loader=(\S*)#')){return;}$expected=$namespace=$class=$docComment=NULL;$level=$classLevel=0;foreach(token_get_all($s)as$token){if(is_array($token)){switch($token[0]){case
T_DOC_COMMENT:$docComment=$token[1];case
T_WHITESPACE:case
T_COMMENT:continue
2;case
T_STRING:case$T_NS_SEPARATOR:case
T_VARIABLE:if($expected){$name.=$token[1];}continue
2;case
T_FUNCTION:case
T_VAR:case
T_PUBLIC:case
T_PROTECTED:case$T_NAMESPACE:case
T_CLASS:case
T_INTERFACE:$expected=$token[0];$name=NULL;continue
2;case
T_STATIC:case
T_ABSTRACT:case
T_FINAL:continue
2;case
T_CURLY_OPEN:case
T_DOLLAR_OPEN_CURLY_BRACES:$level++;}}if($expected){switch($expected){case
T_CLASS:case
T_INTERFACE:$class=$namespace.$name;$classLevel=$level;$name='';case
T_FUNCTION:if($token==='&')continue
2;case
T_VAR:case
T_PUBLIC:case
T_PROTECTED:if($class&&$name!==NULL&&$docComment){self::$cache[$class][$name]=self::parseComment($docComment);}break;case$T_NAMESPACE:$namespace=$name.'\\';}$expected=$docComment=NULL;}if($token===';'){$docComment=NULL;}elseif($token==='{'){$docComment=NULL;$level++;}elseif($token==='}'){$level--;if($level===$classLevel){$class=NULL;}}}}protected
static
function
getCache(){return
NetteX\Environment::getCache('NetteX.Annotations');}}use
NetteX\ObjectMixin;class
Extension
extends\ReflectionExtension{function
__toString(){return'Extension '.$this->getName();}function
getClasses(){$res=array();foreach(parent::getClassNames()as$val){$res[$val]=new
ClassType($val);}return$res;}function
getFunctions(){foreach($res=parent::getFunctions()as$key=>$val){$res[$key]=new
GlobalFunction($key);}return$res;}static
function
getReflection(){return
new
ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}class
GlobalFunction
extends\ReflectionFunction{private$value;function
__construct($name){parent::__construct($this->value=$name);}function
__toString(){return'Function '.$this->getName().'()';}function
getClosure(){return$this->isClosure()?$this->value:NULL;}function
getExtension(){return($name=$this->getExtensionName())?new
Extension($name):NULL;}function
getParameters(){foreach($res=parent::getParameters()as$key=>$val){$res[$key]=new
Parameter($this->value,$val->getName());}return$res;}static
function
getReflection(){return
new
ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}class
Method
extends\ReflectionMethod{static
function
from($class,$method){return
new
static(is_object($class)?get_class($class):$class,$method);}function
getDefaultParameters(){$res=array();foreach(parent::getParameters()as$param){$res[$param->getName()]=$param->isDefaultValueAvailable()?$param->getDefaultValue():NULL;if($param->isArray()){settype($res[$param->getName()],'array');}}return$res;}function
invokeNamedArgs($object,$args){$res=array();$i=0;foreach($this->getDefaultParameters()as$name=>$def){if(isset($args[$name])){$val=$args[$name];if($def!==NULL){settype($val,gettype($def));}$res[$i++]=$val;}else{$res[$i++]=$def;}}return$this->invokeArgs($object,$res);}function
getCallback(){return
new
NetteX\Callback(parent::getDeclaringClass()->getName(),$this->getName());}function
__toString(){return'Method '.parent::getDeclaringClass()->getName().'::'.$this->getName().'()';}function
getDeclaringClass(){return
new
ClassType(parent::getDeclaringClass()->getName());}function
getPrototype(){$prototype=parent::getPrototype();return
new
Method($prototype->getDeclaringClass()->getName(),$prototype->getName());}function
getExtension(){return($name=$this->getExtensionName())?new
Extension($name):NULL;}function
getParameters(){$me=array(parent::getDeclaringClass()->getName(),$this->getName());foreach($res=parent::getParameters()as$key=>$val){$res[$key]=new
Parameter($me,$val->getName());}return$res;}function
hasAnnotation($name){$res=AnnotationsParser::getAll($this);return!empty($res[$name]);}function
getAnnotation($name){$res=AnnotationsParser::getAll($this);return
isset($res[$name])?end($res[$name]):NULL;}function
getAnnotations(){return
AnnotationsParser::getAll($this);}static
function
getReflection(){return
new
ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}class
Parameter
extends\ReflectionParameter{private$function;function
__construct($function,$parameter){parent::__construct($this->function=$function,$parameter);}function
getClass(){return($ref=parent::getClass())?new
ClassType($ref->getName()):NULL;}function
getClassName(){return($tmp=NetteX\StringUtils::match($this,'#>\s+([a-z0-9_\\\\]+)#i'))?$tmp[1]:NULL;}function
getDeclaringClass(){return($ref=parent::getDeclaringClass())?new
ClassType($ref->getName()):NULL;}function
getDeclaringFunction(){return
is_array($this->function)?new
Method($this->function[0],$this->function[1]):new
GlobalFunction($this->function);}static
function
getReflection(){return
new
ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}class
Property
extends\ReflectionProperty{function
__toString(){return'Property '.parent::getDeclaringClass()->getName().'::$'.$this->getName();}function
getDeclaringClass(){return
new
ClassType(parent::getDeclaringClass()->getName());}function
hasAnnotation($name){$res=AnnotationsParser::getAll($this);return!empty($res[$name]);}function
getAnnotation($name){$res=AnnotationsParser::getAll($this);return
isset($res[$name])?end($res[$name]):NULL;}function
getAnnotations(){return
AnnotationsParser::getAll($this);}static
function
getReflection(){return
new
ClassType(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){ObjectMixin::remove($this,$name);}}}namespace NetteX\Security{use
NetteX;class
AuthenticationException
extends\Exception{}class
Identity
extends
NetteX\FreezableObject
implements
IIdentity{private$id;private$roles;private$data;function
__construct($id,$roles=NULL,$data=NULL){$this->setId($id);$this->setRoles((array)$roles);$this->data=$data
instanceof\Traversable?iterator_to_array($data):(array)$data;}function
setId($id){$this->updating();$this->id=is_numeric($id)?1*$id:$id;return$this;}function
getId(){return$this->id;}function
setRoles(array$roles){$this->updating();$this->roles=$roles;return$this;}function
getRoles(){return$this->roles;}function
getData(){return$this->data;}function
__set($key,$value){$this->updating();if(parent::__isset($key)){parent::__set($key,$value);}else{$this->data[$key]=$value;}}function&__get($key){if(parent::__isset($key)){return
parent::__get($key);}else{return$this->data[$key];}}function
__isset($key){return
isset($this->data[$key])||parent::__isset($key);}function
__unset($name){ObjectMixin::remove($this,$name);}}class
Permission
extends
NetteX\Object
implements
IAuthorizator{private$roles=array();private$resources=array();private$rules=array('allResources'=>array('allRoles'=>array('allPrivileges'=>array('type'=>self::DENY,'assert'=>NULL),'byPrivilege'=>array()),'byRole'=>array()),'byResource'=>array());private$queriedRole,$queriedResource;function
addRole($role,$parents=NULL){$this->checkRole($role,FALSE);if(isset($this->roles[$role])){throw
new
NetteX\InvalidStateException("Role '$role' already exists in the list.");}$roleParents=array();if($parents!==NULL){if(!is_array($parents)){$parents=array($parents);}foreach($parents
as$parent){$this->checkRole($parent);$roleParents[$parent]=TRUE;$this->roles[$parent]['children'][$role]=TRUE;}}$this->roles[$role]=array('parents'=>$roleParents,'children'=>array());return$this;}function
hasRole($role){$this->checkRole($role,FALSE);return
isset($this->roles[$role]);}private
function
checkRole($role,$need=TRUE){if(!is_string($role)||$role===''){throw
new
NetteX\InvalidArgumentException("Role must be a non-empty string.");}elseif($need&&!isset($this->roles[$role])){throw
new
NetteX\InvalidStateException("Role '$role' does not exist.");}}function
getRoleParents($role){$this->checkRole($role);return
array_keys($this->roles[$role]['parents']);}function
roleInheritsFrom($role,$inherit,$onlyParents=FALSE){$this->checkRole($role);$this->checkRole($inherit);$inherits=isset($this->roles[$role]['parents'][$inherit]);if($inherits||$onlyParents){return$inherits;}foreach($this->roles[$role]['parents']as$parent=>$foo){if($this->roleInheritsFrom($parent,$inherit)){return
TRUE;}}return
FALSE;}function
removeRole($role){$this->checkRole($role);foreach($this->roles[$role]['children']as$child=>$foo)unset($this->roles[$child]['parents'][$role]);foreach($this->roles[$role]['parents']as$parent=>$foo)unset($this->roles[$parent]['children'][$role]);unset($this->roles[$role]);foreach($this->rules['allResources']['byRole']as$roleCurrent=>$rules){if($role===$roleCurrent){unset($this->rules['allResources']['byRole'][$roleCurrent]);}}foreach($this->rules['byResource']as$resourceCurrent=>$visitor){if(isset($visitor['byRole'])){foreach($visitor['byRole']as$roleCurrent=>$rules){if($role===$roleCurrent){unset($this->rules['byResource'][$resourceCurrent]['byRole'][$roleCurrent]);}}}}return$this;}function
removeAllRoles(){$this->roles=array();foreach($this->rules['allResources']['byRole']as$roleCurrent=>$rules)unset($this->rules['allResources']['byRole'][$roleCurrent]);foreach($this->rules['byResource']as$resourceCurrent=>$visitor){foreach($visitor['byRole']as$roleCurrent=>$rules){unset($this->rules['byResource'][$resourceCurrent]['byRole'][$roleCurrent]);}}return$this;}function
addResource($resource,$parent=NULL){$this->checkResource($resource,FALSE);if(isset($this->resources[$resource])){throw
new
NetteX\InvalidStateException("Resource '$resource' already exists in the list.");}if($parent!==NULL){$this->checkResource($parent);$this->resources[$parent]['children'][$resource]=TRUE;}$this->resources[$resource]=array('parent'=>$parent,'children'=>array());return$this;}function
hasResource($resource){$this->checkResource($resource,FALSE);return
isset($this->resources[$resource]);}private
function
checkResource($resource,$need=TRUE){if(!is_string($resource)||$resource===''){throw
new
NetteX\InvalidArgumentException("Resource must be a non-empty string.");}elseif($need&&!isset($this->resources[$resource])){throw
new
NetteX\InvalidStateException("Resource '$resource' does not exist.");}}function
resourceInheritsFrom($resource,$inherit,$onlyParent=FALSE){$this->checkResource($resource);$this->checkResource($inherit);if($this->resources[$resource]['parent']===NULL){return
FALSE;}$parent=$this->resources[$resource]['parent'];if($inherit===$parent){return
TRUE;}elseif($onlyParent){return
FALSE;}while($this->resources[$parent]['parent']!==NULL){$parent=$this->resources[$parent]['parent'];if($inherit===$parent){return
TRUE;}}return
FALSE;}function
removeResource($resource){$this->checkResource($resource);$parent=$this->resources[$resource]['parent'];if($parent!==NULL){unset($this->resources[$parent]['children'][$resource]);}$removed=array($resource);foreach($this->resources[$resource]['children']as$child=>$foo){$this->removeResource($child);$removed[]=$child;}foreach($removed
as$resourceRemoved){foreach($this->rules['byResource']as$resourceCurrent=>$rules){if($resourceRemoved===$resourceCurrent){unset($this->rules['byResource'][$resourceCurrent]);}}}unset($this->resources[$resource]);return$this;}function
removeAllResources(){foreach($this->resources
as$resource=>$foo){foreach($this->rules['byResource']as$resourceCurrent=>$rules){if($resource===$resourceCurrent){unset($this->rules['byResource'][$resourceCurrent]);}}}$this->resources=array();return$this;}function
allow($roles=self::ALL,$resources=self::ALL,$privileges=self::ALL,$assertion=NULL){$this->setRule(TRUE,self::ALLOW,$roles,$resources,$privileges,$assertion);return$this;}function
deny($roles=self::ALL,$resources=self::ALL,$privileges=self::ALL,$assertion=NULL){$this->setRule(TRUE,self::DENY,$roles,$resources,$privileges,$assertion);return$this;}function
removeAllow($roles=self::ALL,$resources=self::ALL,$privileges=self::ALL){$this->setRule(FALSE,self::ALLOW,$roles,$resources,$privileges);return$this;}function
removeDeny($roles=self::ALL,$resources=self::ALL,$privileges=self::ALL){$this->setRule(FALSE,self::DENY,$roles,$resources,$privileges);return$this;}protected
function
setRule($toAdd,$type,$roles,$resources,$privileges,$assertion=NULL){if($roles===self::ALL){$roles=array(self::ALL);}else{if(!is_array($roles)){$roles=array($roles);}foreach($roles
as$role){$this->checkRole($role);}}if($resources===self::ALL){$resources=array(self::ALL);}else{if(!is_array($resources)){$resources=array($resources);}foreach($resources
as$resource){$this->checkResource($resource);}}if($privileges===self::ALL){$privileges=array();}elseif(!is_array($privileges)){$privileges=array($privileges);}$assertion=$assertion?callback($assertion):NULL;if($toAdd){foreach($resources
as$resource){foreach($roles
as$role){$rules=&$this->getRules($resource,$role,TRUE);if(count($privileges)===0){$rules['allPrivileges']['type']=$type;$rules['allPrivileges']['assert']=$assertion;if(!isset($rules['byPrivilege'])){$rules['byPrivilege']=array();}}else{foreach($privileges
as$privilege){$rules['byPrivilege'][$privilege]['type']=$type;$rules['byPrivilege'][$privilege]['assert']=$assertion;}}}}}else{foreach($resources
as$resource){foreach($roles
as$role){$rules=&$this->getRules($resource,$role);if($rules===NULL){continue;}if(count($privileges)===0){if($resource===self::ALL&&$role===self::ALL){if($type===$rules['allPrivileges']['type']){$rules=array('allPrivileges'=>array('type'=>self::DENY,'assert'=>NULL),'byPrivilege'=>array());}continue;}if($type===$rules['allPrivileges']['type']){unset($rules['allPrivileges']);}}else{foreach($privileges
as$privilege){if(isset($rules['byPrivilege'][$privilege])&&$type===$rules['byPrivilege'][$privilege]['type']){unset($rules['byPrivilege'][$privilege]);}}}}}}return$this;}function
isAllowed($role=self::ALL,$resource=self::ALL,$privilege=self::ALL){$this->queriedRole=$role;if($role!==self::ALL){if($role
instanceof
IRole){$role=$role->getRoleId();}$this->checkRole($role);}$this->queriedResource=$resource;if($resource!==self::ALL){if($resource
instanceof
IResource){$resource=$resource->getResourceId();}$this->checkResource($resource);}if($privilege===self::ALL){do{if($role!==NULL&&NULL!==($result=$this->roleDFSAllPrivileges($role,$resource))){break;}if(NULL!==($rules=$this->getRules($resource,self::ALL))){foreach($rules['byPrivilege']as$privilege=>$rule){if(self::DENY===($ruleTypeOnePrivilege=$this->getRuleType($resource,NULL,$privilege))){$result=self::DENY;break
2;}}if(NULL!==($ruleTypeAllPrivileges=$this->getRuleType($resource,NULL,NULL))){$result=self::ALLOW===$ruleTypeAllPrivileges;break;}}$resource=$this->resources[$resource]['parent'];}while(TRUE);}else{do{if($role!==NULL&&NULL!==($result=$this->roleDFSOnePrivilege($role,$resource,$privilege))){break;}if(NULL!==($ruleType=$this->getRuleType($resource,NULL,$privilege))){$result=self::ALLOW===$ruleType;break;}elseif(NULL!==($ruleTypeAllPrivileges=$this->getRuleType($resource,NULL,NULL))){$result=self::ALLOW===$ruleTypeAllPrivileges;break;}$resource=$this->resources[$resource]['parent'];}while(TRUE);}$this->queriedRole=$this->queriedResource=NULL;return$result;}function
getQueriedRole(){return$this->queriedRole;}function
getQueriedResource(){return$this->queriedResource;}private
function
roleDFSAllPrivileges($role,$resource){$dfs=array('visited'=>array(),'stack'=>array($role));while(NULL!==($role=array_pop($dfs['stack']))){if(!isset($dfs['visited'][$role])){if(NULL!==($result=$this->roleDFSVisitAllPrivileges($role,$resource,$dfs))){return$result;}}}return
NULL;}private
function
roleDFSVisitAllPrivileges($role,$resource,&$dfs){if(NULL!==($rules=$this->getRules($resource,$role))){foreach($rules['byPrivilege']as$privilege=>$rule){if(self::DENY===$this->getRuleType($resource,$role,$privilege)){return
self::DENY;}}if(NULL!==($type=$this->getRuleType($resource,$role,NULL))){return
self::ALLOW===$type;}}$dfs['visited'][$role]=TRUE;foreach($this->roles[$role]['parents']as$roleParent=>$foo){$dfs['stack'][]=$roleParent;}return
NULL;}private
function
roleDFSOnePrivilege($role,$resource,$privilege){$dfs=array('visited'=>array(),'stack'=>array($role));while(NULL!==($role=array_pop($dfs['stack']))){if(!isset($dfs['visited'][$role])){if(NULL!==($result=$this->roleDFSVisitOnePrivilege($role,$resource,$privilege,$dfs))){return$result;}}}return
NULL;}private
function
roleDFSVisitOnePrivilege($role,$resource,$privilege,&$dfs){if(NULL!==($type=$this->getRuleType($resource,$role,$privilege))){return
self::ALLOW===$type;}if(NULL!==($type=$this->getRuleType($resource,$role,NULL))){return
self::ALLOW===$type;}$dfs['visited'][$role]=TRUE;foreach($this->roles[$role]['parents']as$roleParent=>$foo)$dfs['stack'][]=$roleParent;return
NULL;}private
function
getRuleType($resource,$role,$privilege){if(NULL===($rules=$this->getRules($resource,$role))){return
NULL;}if($privilege===self::ALL){if(isset($rules['allPrivileges'])){$rule=$rules['allPrivileges'];}else{return
NULL;}}elseif(!isset($rules['byPrivilege'][$privilege])){return
NULL;}else{$rule=$rules['byPrivilege'][$privilege];}if($rule['assert']===NULL||$rule['assert']->__invoke($this,$role,$resource,$privilege)){return$rule['type'];}elseif($resource!==self::ALL||$role!==self::ALL||$privilege!==self::ALL){return
NULL;}elseif(self::ALLOW===$rule['type']){return
self::DENY;}else{return
self::ALLOW;}}private
function&getRules($resource,$role,$create=FALSE){if($resource===self::ALL){$visitor=&$this->rules['allResources'];}else{if(!isset($this->rules['byResource'][$resource])){if(!$create){$null=NULL;return$null;}$this->rules['byResource'][$resource]=array();}$visitor=&$this->rules['byResource'][$resource];}if($role===self::ALL){if(!isset($visitor['allRoles'])){if(!$create){$null=NULL;return$null;}$visitor['allRoles']['byPrivilege']=array();}return$visitor['allRoles'];}if(!isset($visitor['byRole'][$role])){if(!$create){$null=NULL;return$null;}$visitor['byRole'][$role]['byPrivilege']=array();}return$visitor['byRole'][$role];}}class
SimpleAuthenticator
extends
NetteX\Object
implements
IAuthenticator{private$userlist;function
__construct(array$userlist){$this->userlist=$userlist;}function
authenticate(array$credentials){list($username,$password)=$credentials;foreach($this->userlist
as$name=>$pass){if(strcasecmp($name,$username)===0){if($pass===$password){return
new
Identity($name);}else{throw
new
AuthenticationException("Invalid password.",self::INVALID_CREDENTIAL);}}}throw
new
AuthenticationException("User '$username' not found.",self::IDENTITY_NOT_FOUND);}}}namespace NetteX\Templating{use
NetteX;use
NetteX\StringUtils;use
NetteX\Forms\Form;use
NetteX\Utils\Html;final
class
DefaultHelpers{public
static$dateFormat='%x';final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
loader($helper){$callback=callback('NetteX\Templating\DefaultHelpers',$helper);if($callback->isCallable()){return$callback;}$callback=callback('NetteX\StringUtils',$helper);if($callback->isCallable()){return$callback;}}static
function
escapeHtml($s){if(is_object($s)&&($s
instanceof
ITemplate||$s
instanceof
Html||$s
instanceof
Form)){return$s->__toString(TRUE);}return
htmlSpecialChars($s,ENT_QUOTES);}static
function
escapeHtmlComment($s){return
str_replace('--','--><!-- ',$s);}static
function
escapeXML($s){return
htmlSpecialChars(preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#','',$s),ENT_QUOTES);}static
function
escapeCss($s){return
addcslashes($s,"\x00..\x1F!\"#$%&'()*+,./:;<=>?@[\\]^`{|}~");}static
function
escapeHtmlCss($s){return
htmlSpecialChars(self::escapeCss($s),ENT_QUOTES);}static
function
escapeJs($s){if(is_object($s)&&($s
instanceof
ITemplate||$s
instanceof
Html||$s
instanceof
Form)){$s=$s->__toString(TRUE);}return
str_replace(']]>',']]\x3E',NetteX\Utils\Json::encode($s));}static
function
escapeHtmlJs($s){return
htmlSpecialChars(self::escapeJs($s),ENT_QUOTES);}static
function
strip($s){return
StringUtils::replace($s,'#(</textarea|</pre|</script|^).*?(?=<textarea|<pre|<script|$)#si',function($m){return
trim(preg_replace("#[ \t\r\n]+#"," ",$m[0]));});}static
function
indent($s,$level=1,$chars="\t"){if($level>=1){$s=StringUtils::replace($s,'#<(textarea|pre).*?</\\1#si',function($m){return
strtr($m[0]," \t\r\n","\x1F\x1E\x1D\x1A");});$s=StringUtils::indent($s,$level,$chars);$s=strtr($s,"\x1F\x1E\x1D\x1A"," \t\r\n");}return$s;}static
function
date($time,$format=NULL){if($time==NULL){return
NULL;}if(!isset($format)){$format=self::$dateFormat;}$time=NetteX\DateTime::from($time);return
strpos($format,'%')===FALSE?$time->format($format):strftime($format,$time->format('U'));}static
function
bytes($bytes,$precision=2){$bytes=round($bytes);$units=array('B','kB','MB','GB','TB','PB');foreach($units
as$unit){if(abs($bytes)<1024||$unit===end($units))break;$bytes=$bytes/1024;}return
round($bytes,$precision).' '.$unit;}static
function
length($var){return
is_string($var)?StringUtils::length($var):count($var);}static
function
replace($subject,$search,$replacement=''){return
str_replace($search,$replacement,$subject);}static
function
dataStream($data,$type=NULL){if($type===NULL){$type=NetteX\Utils\MimeTypeDetector::fromString($data,NULL);}return'data:'.($type?"$type;":'').'base64,'.base64_encode($data);}static
function
null($value){return'';}}abstract
class
Template
extends
NetteX\Object
implements
ITemplate{public$warnOnUndefined=TRUE;public$onPrepareFilters=array();private$params=array();private$filters=array();private$helpers=array();private$helperLoaders=array();function
registerFilter($callback){$callback=callback($callback);if(in_array($callback,$this->filters)){throw
new
NetteX\InvalidStateException("Filter '$callback' was registered twice.");}$this->filters[]=$callback;}final
function
getFilters(){return$this->filters;}function
render(){throw
new
NetteX\NotImplementedException;}function
save($file){if(file_put_contents($file,$this->__toString(TRUE))===FALSE){throw
new
NetteX\IOException("Unable to save file '$file'.");}}function
__toString(){ob_start();try{$this->render();return
ob_get_clean();}catch(\Exception$e){ob_end_clean();if(func_num_args()&&func_get_arg(0)){throw$e;}else{NetteX\Diagnostics\Debugger::toStringException($e);}}}function
compile($content){if(!$this->filters){$this->onPrepareFilters($this);}foreach($this->filters
as$filter){$content=self::extractPhp($content,$blocks);$content=$filter($content);$content=strtr($content,$blocks);}return
self::optimizePhp($content);}function
registerHelper($name,$callback){$this->helpers[strtolower($name)]=callback($callback);}function
registerHelperLoader($callback){$this->helperLoaders[]=callback($callback);}final
function
getHelpers(){return$this->helpers;}function
__call($name,$args){$lname=strtolower($name);if(!isset($this->helpers[$lname])){foreach($this->helperLoaders
as$loader){$helper=$loader($lname);if($helper){$this->registerHelper($lname,$helper);return$this->helpers[$lname]->invokeArgs($args);}}return
parent::__call($name,$args);}return$this->helpers[$lname]->invokeArgs($args);}function
setTranslator(NetteX\Localization\ITranslator$translator=NULL){$this->registerHelper('translate',$translator===NULL?NULL:array($translator,'translate'));return$this;}function
add($name,$value){if(array_key_exists($name,$this->params)){throw
new
NetteX\InvalidStateException("The variable '$name' already exists.");}$this->params[$name]=$value;}function
setParams(array$params){$this->params=$params;return$this;}function
getParams(){return$this->params;}function
__set($name,$value){$this->params[$name]=$value;}function&__get($name){if($this->warnOnUndefined&&!array_key_exists($name,$this->params)){trigger_error("The variable '$name' does not exist in template.",E_USER_NOTICE);}return$this->params[$name];}function
__isset($name){return
isset($this->params[$name]);}function
__unset($name){unset($this->params[$name]);}private
static
function
extractPhp($source,&$blocks){$res='';$blocks=array();$tokens=token_get_all($source);foreach($tokens
as$n=>$token){if(is_array($token)){if($token[0]===T_INLINE_HTML){$res.=$token[1];continue;}elseif($token[0]===T_OPEN_TAG&&$token[1]==='<?'&&isset($tokens[$n+1][1])&&$tokens[$n+1][1]==='xml'){$php=&$res;$token[1]='<<?php ?>?';}elseif($token[0]===T_OPEN_TAG||$token[0]===T_OPEN_TAG_WITH_ECHO){$res.=$id="\x01@php:p".count($blocks)."@\x02";$php=&$blocks[$id];}$php.=$token[1];}else{$php.=$token;}}return$res;}static
function
optimizePhp($source){$res=$php='';$lastChar=';';$tokens=new\ArrayIterator(token_get_all($source));foreach($tokens
as$key=>$token){if(is_array($token)){if($token[0]===T_INLINE_HTML){$lastChar='';$res.=$token[1];}elseif($token[0]===T_CLOSE_TAG){$next=isset($tokens[$key+1])?$tokens[$key+1]:NULL;if(substr($res,-1)!=='<'&&preg_match('#^<\?php\s*$#',$php)){$php='';}elseif(is_array($next)&&$next[0]===T_OPEN_TAG){if(!strspn($lastChar,';{}:/')){$php.=$lastChar=';';}if(substr($next[1],-1)==="\n"){$php.="\n";}$tokens->next();}elseif($next){$res.=preg_replace('#;?(\s)*$#','$1',$php).$token[1];$php='';}else{if(!strspn($lastChar,'};')){$php.=';';}}}elseif($token[0]===T_ELSE||$token[0]===T_ELSEIF){if($tokens[$key+1]===':'&&$lastChar==='}'){$php.=';';}$lastChar='';$php.=$token[1];}else{if(!in_array($token[0],array(T_WHITESPACE,T_COMMENT,T_DOC_COMMENT,T_OPEN_TAG))){$lastChar='';}$php.=$token[1];}}else{$php.=$lastChar=$token;}}return$res.$php;}}use
NetteX\Caching\Cache;class
FileTemplate
extends
Template
implements
IFileTemplate{private$cacheStorage;private$file;function
__construct($file=NULL){if($file!==NULL){$this->setFile($file);}}function
setFile($file){$this->file=realpath($file);if(!$this->file){throw
new
NetteX\FileNotFoundException("Missing template file '$file'.");}return$this;}function
getFile(){return$this->file;}function
render(){if($this->file==NULL){throw
new
NetteX\InvalidStateException("Template file name was not specified.");}$this->__set('template',$this);$cache=new
Cache($storage=$this->getCacheStorage(),'NetteX.FileTemplate');if($storage
instanceof
PhpFileStorage){$storage->hint=str_replace(dirname(dirname($this->file)),'',$this->file);}$cached=$content=$cache[$this->file];if($content===NULL){try{$content=$this->compile(file_get_contents($this->file));$content="<?php\n\n// source file: $this->file\n\n?>$content";}catch(FilterException$e){$e->setSourceFile($this->file);throw$e;}$cache->save($this->file,$content,array(Cache::FILES=>$this->file,Cache::CONSTS=>'NetteX\Framework::REVISION'));$cache->release();$cached=$cache[$this->file];}if($cached!==NULL&&$storage
instanceof
PhpFileStorage){NetteX\Utils\LimitedScope::load($cached['file'],$this->getParams());flock($cached['handle'],LOCK_UN);fclose($cached['handle']);}else{NetteX\Utils\LimitedScope::evaluate($content,$this->getParams());}}function
setCacheStorage(NetteX\Caching\IStorage$storage){$this->cacheStorage=$storage;}function
getCacheStorage(){if($this->cacheStorage===NULL){$dir=NetteX\Environment::getVariable('tempDir').'/cache';umask(0000);@mkdir($dir,0777);$this->cacheStorage=new
PhpFileStorage($dir);}return$this->cacheStorage;}}class
PhpFileStorage
extends
NetteX\Caching\Storages\FileStorage{public$hint;protected
function
readData($meta){return
array('file'=>$meta[self::FILE],'handle'=>$meta[self::HANDLE]);}protected
function
getCacheFile($key){return
parent::getCacheFile(substr_replace($key,trim(strtr($this->hint,'\\/@','.._'),'.').'-',strpos($key,NetteX\Caching\Cache::NAMESPACE_SEPARATOR)+1,0)).'.php';}}}namespace NetteX\Utils{use
NetteX;final
class
CriticalSection{private
static$criticalSections;final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
enter(){if(self::$criticalSections){throw
new
NetteX\InvalidStateException('Critical section has already been entered.');}$handle=substr(PHP_OS,0,3)==='WIN'?@fopen(NETTEX_DIR.'/lockfile','w'):@fopen(__FILE__,'r');if(!$handle){throw
new
NetteX\InvalidStateException("Unable initialize critical section.");}flock(self::$criticalSections=$handle,LOCK_EX);}static
function
leave(){if(!self::$criticalSections){throw
new
NetteX\InvalidStateException('Critical section has not been initialized.');}flock(self::$criticalSections,LOCK_UN);fclose(self::$criticalSections);self::$criticalSections=NULL;}}use
RecursiveIteratorIterator;class
Finder
extends
NetteX\Object
implements\IteratorAggregate{private$paths=array();private$groups;private$exclude=array();private$order=RecursiveIteratorIterator::SELF_FIRST;private$maxDepth=-1;private$cursor;static
function
find($mask){if(!is_array($mask)){$mask=func_get_args();}$finder=new
self;return$finder->select(array(),'isDir')->select($mask,'isFile');}static
function
findFiles($mask){if(!is_array($mask)){$mask=func_get_args();}$finder=new
self;return$finder->select($mask,'isFile');}static
function
findDirectories($mask){if(!is_array($mask)){$mask=func_get_args();}$finder=new
self;return$finder->select($mask,'isDir');}private
function
select($masks,$type){$this->cursor=&$this->groups[];$pattern=self::buildPattern($masks);if($type||$pattern){$this->filter(function($file)use($type,$pattern){return(!$type||$file->$type())&&!$file->isDot()&&(!$pattern||preg_match($pattern,'/'.strtr($file->getSubPathName(),'\\','/')));});}return$this;}function
in($path){if(!is_array($path)){$path=func_get_args();}$this->maxDepth=0;return$this->from($path);}function
from($path){if($this->paths){throw
new
NetteX\InvalidStateException('Directory to search has already been specified.');}if(!is_array($path)){$path=func_get_args();}$this->paths=$path;$this->cursor=&$this->exclude;return$this;}function
childFirst(){$this->order=RecursiveIteratorIterator::CHILD_FIRST;return$this;}private
static
function
buildPattern($masks){$pattern=array();foreach($masks
as$mask){$mask=rtrim(strtr($mask,'\\','/'),'/');$prefix='';if($mask===''){continue;}elseif($mask==='*'){return
NULL;}elseif($mask[0]==='/'){$mask=ltrim($mask,'/');$prefix='(?<=^/)';}$pattern[]=$prefix.strtr(preg_quote($mask,'#'),array('\*\*'=>'.*','\*'=>'[^/]*','\?'=>'[^/]','\[\!'=>'[^','\['=>'[','\]'=>']','\-'=>'-'));}return$pattern?'#/('.implode('|',$pattern).')$#i':NULL;}function
getIterator(){if(!$this->paths){throw
new
NetteX\InvalidStateException('Call in() or from() to specify directory to search.');}elseif(count($this->paths)===1){return$this->buildIterator($this->paths[0]);}else{$iterator=new\AppendIterator();foreach($this->paths
as$path){$iterator->append($this->buildIterator($path));}return$iterator;}}private
function
buildIterator($path){if(PHP_VERSION_ID<50301){$iterator=new
NetteX\RecursiveDirectoryIteratorFixed($path);}else{$iterator=new\RecursiveDirectoryIterator($path,\RecursiveDirectoryIterator::FOLLOW_SYMLINKS);}if($this->exclude){$filters=$this->exclude;$iterator=new
NetteX\Iterators\RecursiveFilter($iterator,function($file)use($filters){if(!$file->isFile()){foreach($filters
as$filter){if(!call_user_func($filter,$file)){return
FALSE;}}}return
TRUE;});}if($this->maxDepth!==0){$iterator=new
RecursiveIteratorIterator($iterator,$this->order);$iterator->setMaxDepth($this->maxDepth);}if($this->groups){$groups=$this->groups;$iterator=new
NetteX\Iterators\Filter($iterator,function($file)use($groups){foreach($groups
as$filters){foreach($filters
as$filter){if(!call_user_func($filter,$file)){continue
2;}}return
TRUE;}return
FALSE;});}return$iterator;}function
exclude($masks){if(!is_array($masks)){$masks=func_get_args();}$pattern=self::buildPattern($masks);if($pattern){$this->filter(function($file)use($pattern){return!preg_match($pattern,'/'.strtr($file->getSubPathName(),'\\','/'));});}return$this;}function
filter($callback){$this->cursor[]=$callback;return$this;}function
limitDepth($depth){$this->maxDepth=$depth;return$this;}function
size($operator,$size=NULL){if(func_num_args()===1){if(!preg_match('#^(?:([=<>!]=?|<>)\s*)?((?:\d*\.)?\d+)\s*(K|M|G|)B?$#i',$operator,$matches)){throw
new
NetteX\InvalidArgumentException('Invalid size predicate format.');}list(,$operator,$size,$unit)=$matches;static$units=array(''=>1,'k'=>1e3,'m'=>1e6,'g'=>1e9);$size*=$units[strtolower($unit)];$operator=$operator?$operator:'=';}return$this->filter(function($file)use($operator,$size){return
Finder::compare($file->getSize(),$operator,$size);});}function
date($operator,$date=NULL){if(func_num_args()===1){if(!preg_match('#^(?:([=<>!]=?|<>)\s*)?(.+)$#i',$operator,$matches)){throw
new
NetteX\InvalidArgumentException('Invalid date predicate format.');}list(,$operator,$date)=$matches;$operator=$operator?$operator:'=';}$date=NetteX\DateTime::from($date)->format('U');return$this->filter(function($file)use($operator,$date){return
Finder::compare($file->getMTime(),$operator,$date);});}static
function
compare($l,$operator,$r){switch($operator){case'>':return$l>$r;case'>=':return$l>=$r;case'<':return$l<$r;case'<=':return$l<=$r;case'=':case'==':return$l==$r;case'!':case'!=':case'<>':return$l!=$r;}throw
new
NetteX\InvalidArgumentException("Unknown operator $operator.");}}if(PHP_VERSION_ID<50301){class
RecursiveDirectoryIteratorFixed
extends\RecursiveDirectoryIterator{function
hasChildren(){return
parent::hasChildren(TRUE);}}}class
Html
extends
NetteX\Object
implements\ArrayAccess,\Countable,\IteratorAggregate{private$name;private$isEmpty;public$attrs=array();protected$children=array();public
static$xhtml=TRUE;public
static$emptyElements=array('img'=>1,'hr'=>1,'br'=>1,'input'=>1,'meta'=>1,'area'=>1,'embed'=>1,'keygen'=>1,'source'=>1,'base'=>1,'col'=>1,'link'=>1,'param'=>1,'basefont'=>1,'frame'=>1,'isindex'=>1,'wbr'=>1,'command'=>1);static
function
el($name=NULL,$attrs=NULL){$el=new
static;$parts=explode(' ',$name,2);$el->setName($parts[0]);if(is_array($attrs)){$el->attrs=$attrs;}elseif($attrs!==NULL){$el->setText($attrs);}if(isset($parts[1])){foreach(NetteX\StringUtils::matchAll($parts[1].' ','#([a-z0-9:-]+)(?:=(["\'])?(.*?)(?(2)\\2|\s))?#i')as$m){$el->attrs[$m[1]]=isset($m[3])?$m[3]:TRUE;}}return$el;}final
function
setName($name,$isEmpty=NULL){if($name!==NULL&&!is_string($name)){throw
new
NetteX\InvalidArgumentException("Name must be string or NULL, ".gettype($name)." given.");}$this->name=$name;$this->isEmpty=$isEmpty===NULL?isset(self::$emptyElements[$name]):(bool)$isEmpty;return$this;}final
function
getName(){return$this->name;}final
function
isEmpty(){return$this->isEmpty;}function
addAttributes(array$attrs){$this->attrs=$attrs+$this->attrs;return$this;}final
function
__set($name,$value){$this->attrs[$name]=$value;}final
function&__get($name){return$this->attrs[$name];}final
function
__unset($name){unset($this->attrs[$name]);}final
function
__call($m,$args){$p=substr($m,0,3);if($p==='get'||$p==='set'||$p==='add'){$m=substr($m,3);$m[0]=$m[0]|"\x20";if($p==='get'){return
isset($this->attrs[$m])?$this->attrs[$m]:NULL;}elseif($p==='add'){$args[]=TRUE;}}if(count($args)===0){}elseif(count($args)===1){$this->attrs[$m]=$args[0];}elseif((string)$args[0]===''){$tmp=&$this->attrs[$m];}elseif(!isset($this->attrs[$m])||is_array($this->attrs[$m])){$this->attrs[$m][$args[0]]=$args[1];}else{$this->attrs[$m]=array($this->attrs[$m],$args[0]=>$args[1]);}return$this;}final
function
href($path,$query=NULL){if($query){$query=http_build_query($query,NULL,'&');if($query!=='')$path.='?'.$query;}$this->attrs['href']=$path;return$this;}final
function
setHtml($html){if($html===NULL){$html='';}elseif(is_array($html)){throw
new
NetteX\InvalidArgumentException("Textual content must be a scalar, ".gettype($html)." given.");}else{$html=(string)$html;}$this->removeChildren();$this->children[]=$html;return$this;}final
function
getHtml(){$s='';foreach($this->children
as$child){if(is_object($child)){$s.=$child->render();}else{$s.=$child;}}return$s;}final
function
setText($text){if(!is_array($text)){$text=htmlspecialchars((string)$text,ENT_NOQUOTES);}return$this->setHtml($text);}final
function
getText(){return
html_entity_decode(strip_tags($this->getHtml()),ENT_QUOTES,'UTF-8');}final
function
add($child){return$this->insert(NULL,$child);}final
function
create($name,$attrs=NULL){$this->insert(NULL,$child=static::el($name,$attrs));return$child;}function
insert($index,$child,$replace=FALSE){if($child
instanceof
Html||is_scalar($child)){if($index===NULL){$this->children[]=$child;}else{array_splice($this->children,(int)$index,$replace?1:0,array($child));}}else{throw
new
NetteX\InvalidArgumentException("Child node must be scalar or Html object, ".(is_object($child)?get_class($child):gettype($child))." given.");}return$this;}final
function
offsetSet($index,$child){$this->insert($index,$child,TRUE);}final
function
offsetGet($index){return$this->children[$index];}final
function
offsetExists($index){return
isset($this->children[$index]);}function
offsetUnset($index){if(isset($this->children[$index])){array_splice($this->children,(int)$index,1);}}final
function
count(){return
count($this->children);}function
removeChildren(){$this->children=array();}final
function
getIterator($deep=FALSE){if($deep){$deep=$deep>0?\RecursiveIteratorIterator::SELF_FIRST:\RecursiveIteratorIterator::CHILD_FIRST;return
new\RecursiveIteratorIterator(new
NetteX\Iterators\Recursor(new\ArrayIterator($this->children)),$deep);}else{return
new
NetteX\Iterators\Recursor(new\ArrayIterator($this->children));}}final
function
getChildren(){return$this->children;}final
function
render($indent=NULL){$s=$this->startTag();if(!$this->isEmpty){if($indent!==NULL){$indent++;}foreach($this->children
as$child){if(is_object($child)){$s.=$child->render($indent);}else{$s.=$child;}}$s.=$this->endTag();}if($indent!==NULL){return"\n".str_repeat("\t",$indent-1).$s."\n".str_repeat("\t",max(0,$indent-2));}return$s;}final
function
__toString(){return$this->render();}final
function
startTag(){if($this->name){return'<'.$this->name.$this->attributes().(self::$xhtml&&$this->isEmpty?' />':'>');}else{return'';}}final
function
endTag(){return$this->name&&!$this->isEmpty?'</'.$this->name.'>':'';}final
function
attributes(){if(!is_array($this->attrs)){return'';}$s='';foreach($this->attrs
as$key=>$value){if($value===NULL||$value===FALSE)continue;if($value===TRUE){if(self::$xhtml)$s.=' '.$key.'="'.$key.'"';else$s.=' '.$key;continue;}elseif(is_array($value)){if($key==='data'){foreach($value
as$k=>$v){if($v!==NULL&&$v!==FALSE){$s.=' data-'.$k.'="'.htmlspecialchars((string)$v).'"';}}continue;}$tmp=NULL;foreach($value
as$k=>$v){if($v==NULL)continue;$tmp[]=$v===TRUE?$k:(is_string($k)?$k.':'.$v:$v);}if($tmp===NULL)continue;$value=implode($key==='style'||!strncmp($key,'on',2)?';':' ',$tmp);}else{$value=(string)$value;}$s.=' '.$key.'="'.htmlspecialchars($value).'"';}$s=str_replace('@','&#64;',$s);return$s;}function
__clone(){foreach($this->children
as$key=>$value){if(is_object($value)){$this->children[$key]=clone$value;}}}}final
class
Json{const
FORCE_ARRAY=1;private
static$messages=array(JSON_ERROR_DEPTH=>'The maximum stack depth has been exceeded',JSON_ERROR_STATE_MISMATCH=>'Syntax error, malformed JSON',JSON_ERROR_CTRL_CHAR=>'Unexpected control character found',JSON_ERROR_SYNTAX=>'Syntax error, malformed JSON');final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
encode($value){NetteX\Diagnostics\Debugger::tryError();if(function_exists('ini_set')){$old=ini_set('display_errors',0);$json=json_encode($value);ini_set('display_errors',$old);}else{$json=json_encode($value);}if(NetteX\Diagnostics\Debugger::catchError($e)){throw
new
JsonException($e->getMessage());}return$json;}static
function
decode($json,$options=0){$json=(string)$json;$value=json_decode($json,(bool)($options&self::FORCE_ARRAY));if($value===NULL&&$json!==''&&strcasecmp($json,'null')){$error=PHP_VERSION_ID>=50300?json_last_error():0;throw
new
JsonException(isset(self::$messages[$error])?self::$messages[$error]:'Unknown error',$error);}return$value;}}class
JsonException
extends\Exception{}final
class
MimeTypeDetector{final
function
__construct(){throw
new
NetteX\StaticClassException;}static
function
fromFile($file){if(!is_file($file)){throw
new
NetteX\FileNotFoundException("File '$file' not found.");}$info=@getimagesize($file);if(isset($info['mime'])){return$info['mime'];}elseif(extension_loaded('fileinfo')){$type=preg_replace('#[\s;].*$#','',finfo_file(finfo_open(FILEINFO_MIME),$file));}elseif(function_exists('mime_content_type')){$type=mime_content_type($file);}return
isset($type)&&preg_match('#^\S+/\S+$#',$type)?$type:'application/octet-stream';}static
function
fromString($data){if(extension_loaded('fileinfo')&&preg_match('#^(\S+/[^\s;]+)#',finfo_buffer(finfo_open(FILEINFO_MIME),$data),$m)){return$m[1];}elseif(strncmp($data,"\xff\xd8",2)===0){return'image/jpeg';}elseif(strncmp($data,"\x89PNG",4)===0){return'image/png';}elseif(strncmp($data,"GIF",3)===0){return'image/gif';}else{return'application/octet-stream';}}}class
Neon
extends
NetteX\Object{const
BLOCK=1;private
static$patterns=array('\'[^\'\n]*\'|"(?:\\\\.|[^"\\\\\n])*"','@[a-zA-Z_0-9\\\\]+','[:-](?=\s|$)|[,=[\]{}()]','?:#.*','\n[\t ]*','[^#"\',:=@[\]{}()<>\x00-\x20!`](?:[^#,:=\]})>\x00-\x1F]+|:(?!\s|$)|(?<!\s)#)*(?<!\s)','?:[\t ]+');private
static$tokenizer;private
static$brackets=array('['=>']','{'=>'}','('=>')');private$n=0;private$indentTabs;static
function
encode($var,$options=NULL){if($var
instanceof\DateTime){return$var->format('Y-m-d H:i:s O');}if(is_object($var)){$obj=$var;$var=array();foreach($obj
as$k=>$v){$var[$k]=$v;}}if(is_array($var)){$isArray=array_keys($var)===range(0,count($var)-1);$s='';if($options&self::BLOCK){foreach($var
as$k=>$v){$v=self::encode($v,self::BLOCK);$s.=($isArray?'-':self::encode($k).':').(strpos($v,"\n")===FALSE?' '.$v:"\n\t".str_replace("\n","\n\t",$v))."\n";continue;}return$s;}else{foreach($var
as$k=>$v){$s.=($isArray?'':self::encode($k).': ').self::encode($v).', ';}return($isArray?'[':'{').substr($s,0,-2).($isArray?']':'}');}}elseif(is_string($var)&&!is_numeric($var)&&!preg_match('~[\x00-\x1F]|^\d{4}|^(true|false|yes|no|on|off|null)$~i',$var)&&preg_match('~^'.self::$patterns[5].'$~',$var)){return$var;}else{return
json_encode($var);}}static
function
decode($input){if(!is_string($input)){throw
new
NetteX\InvalidArgumentException("Argument must be a string, ".gettype($input)." given.");}if(!self::$tokenizer){self::$tokenizer=new
Tokenizer(self::$patterns,'mi');}$input=str_replace("\r",'',$input);self::$tokenizer->tokenize($input);$parser=new
self;$res=$parser->parse(0);while(isset(self::$tokenizer->tokens[$parser->n])){if(self::$tokenizer->tokens[$parser->n][0]==="\n"){$parser->n++;}else{$parser->error();}}return$res;}private
function
parse($indent=NULL,$result=NULL){$inlineParser=$indent===NULL;$value=$key=$object=NULL;$hasValue=$hasKey=FALSE;$tokens=self::$tokenizer->tokens;$n=&$this->n;$count=count($tokens);for(;$n<$count;$n++){$t=$tokens[$n];if($t===','){if(!$hasValue||!$inlineParser){$this->error();}if($hasKey)$result[$key]=$value;else$result[]=$value;$hasKey=$hasValue=FALSE;}elseif($t===':'||$t==='='){if($hasKey||!$hasValue){$this->error();}if(is_array($value)||(is_object($value)&&!method_exists($value,'__toString'))){$this->error('Unacceptable key');}else{$key=(string)$value;}$hasKey=TRUE;$hasValue=FALSE;}elseif($t==='-'){if($hasKey||$hasValue||$inlineParser){$this->error();}$key=NULL;$hasKey=TRUE;}elseif(isset(self::$brackets[$t])){if($hasValue){$this->error();}$endBracket=self::$brackets[$tokens[$n++]];$hasValue=TRUE;$value=$this->parse(NULL,array());if(!isset($tokens[$n])||$tokens[$n]!==$endBracket){$this->error();}}elseif($t===']'||$t==='}'||$t===')'){if(!$inlineParser){$this->error();}break;}elseif($t[0]==='@'){$object=$t;}elseif($t[0]==="\n"){if($inlineParser){if($hasValue){if($hasKey)$result[$key]=$value;else$result[]=$value;$hasKey=$hasValue=FALSE;}}else{while(isset($tokens[$n+1])&&$tokens[$n+1][0]==="\n")$n++;if(!isset($tokens[$n+1]))break;$newIndent=strlen($tokens[$n])-1;if($indent===NULL){$indent=$newIndent;}if($newIndent){if($this->indentTabs===NULL){$this->indentTabs=$tokens[$n][1]==="\t";}if(strpos($tokens[$n],$this->indentTabs?' ':"\t")){$this->error('Either tabs or spaces may be used as indenting chars, but not both.');}}if($newIndent>$indent){if($hasValue||!$hasKey){$n++;$this->error('Unexpected indentation.');}elseif($key===NULL){$result[]=$this->parse($newIndent);}else{$result[$key]=$this->parse($newIndent);}$newIndent=isset($tokens[$n])?strlen($tokens[$n])-1:0;$hasKey=FALSE;}else{if($hasValue&&!$hasKey){break;}elseif($hasKey){$value=$hasValue?$value:NULL;if($key===NULL)$result[]=$value;else$result[$key]=$value;$hasKey=$hasValue=FALSE;}}if($newIndent<$indent){return$result;}}}else{if($hasValue){$this->error();}static$consts=array('true'=>TRUE,'True'=>TRUE,'TRUE'=>TRUE,'yes'=>TRUE,'Yes'=>TRUE,'YES'=>TRUE,'on'=>TRUE,'On'=>TRUE,'ON'=>TRUE,'false'=>FALSE,'False'=>FALSE,'FALSE'=>FALSE,'no'=>FALSE,'No'=>FALSE,'NO'=>FALSE,'off'=>FALSE,'Off'=>FALSE,'OFF'=>FALSE);if($t[0]==='"'){$value=preg_replace_callback('#\\\\(?:u[0-9a-f]{4}|x[0-9a-f]{2}|.)#i',array($this,'cbString'),substr($t,1,-1));}elseif($t[0]==="'"){$value=substr($t,1,-1);}elseif(isset($consts[$t])){$value=$consts[$t];}elseif($t==='null'||$t==='Null'||$t==='NULL'){$value=NULL;}elseif(is_numeric($t)){$value=$t*1;}elseif(preg_match('#\d\d\d\d-\d\d?-\d\d?(?:(?:[Tt]| +)\d\d?:\d\d:\d\d(?:\.\d*)? *(?:Z|[-+]\d\d?(?::\d\d)?)?)?$#A',$t)){$value=new
NetteX\DateTime($t);}else{$value=$t;}$hasValue=TRUE;}}if($inlineParser){if($hasValue){if($hasKey)$result[$key]=$value;else$result[]=$value;}elseif($hasKey){$this->error();}}else{if($hasValue&&!$hasKey){if($result===NULL){$result=$value;}else{$this->error();}}elseif($hasKey){$value=$hasValue?$value:NULL;if($key===NULL)$result[]=$value;else$result[$key]=$value;}}return$result;}private
function
cbString($m){static$mapping=array('t'=>"\t",'n'=>"\n",'"'=>'"','\\'=>'\\','/'=>'/','_'=>"\xc2\xa0");$sq=$m[0];if(isset($mapping[$sq[1]])){return$mapping[$sq[1]];}elseif($sq[1]==='u'&&strlen($sq)===6){return
NetteX\StringUtils::chr(hexdec(substr($sq,2)));}elseif($sq[1]==='x'&&strlen($sq)===4){return
chr(hexdec(substr($sq,2)));}else{$this->error("Invalid escaping sequence $sq");}}private
function
error($message="Unexpected '%s'"){list(,$line,$col)=self::$tokenizer->getOffset($this->n);$token=isset(self::$tokenizer->tokens[$this->n])?str_replace("\n",'<new line>',NetteX\StringUtils::truncate(self::$tokenizer->tokens[$this->n],40)):'end';throw
new
NeonException(str_replace('%s',$token,$message)." on line $line, column $col.");}}class
NeonException
extends\Exception{}class
Paginator
extends
NetteX\Object{private$base=1;private$itemsPerPage=1;private$page;private$itemCount;function
setPage($page){$this->page=(int)$page;return$this;}function
getPage(){return$this->base+$this->getPageIndex();}function
getFirstPage(){return$this->base;}function
getLastPage(){return$this->itemCount===NULL?NULL:$this->base+max(0,$this->getPageCount()-1);}function
setBase($base){$this->base=(int)$base;return$this;}function
getBase(){return$this->base;}protected
function
getPageIndex(){$index=max(0,$this->page-$this->base);return$this->itemCount===NULL?$index:min($index,max(0,$this->getPageCount()-1));}function
isFirst(){return$this->getPageIndex()===0;}function
isLast(){return$this->itemCount===NULL?FALSE:$this->getPageIndex()>=$this->getPageCount()-1;}function
getPageCount(){return$this->itemCount===NULL?NULL:(int)ceil($this->itemCount/$this->itemsPerPage);}function
setItemsPerPage($itemsPerPage){$this->itemsPerPage=max(1,(int)$itemsPerPage);return$this;}function
getItemsPerPage(){return$this->itemsPerPage;}function
setItemCount($itemCount){$this->itemCount=($itemCount===FALSE||$itemCount===NULL)?NULL:max(0,(int)$itemCount);return$this;}function
getItemCount(){return$this->itemCount;}function
getOffset(){return$this->getPageIndex()*$this->itemsPerPage;}function
getCountdownOffset(){return$this->itemCount===NULL?NULL:max(0,$this->itemCount-($this->getPageIndex()+1)*$this->itemsPerPage);}function
getLength(){return$this->itemCount===NULL?$this->itemsPerPage:min($this->itemsPerPage,$this->itemCount-$this->getPageIndex()*$this->itemsPerPage);}}use
NetteX\StringUtils;class
Tokenizer
extends
NetteX\Object{public$tokens;public$position=0;public$ignored=array();private$input;private$re;private$types;private$current;function
__construct(array$patterns,$flags=''){$this->re='~('.implode(')|(',$patterns).')~A'.$flags;$keys=array_keys($patterns);$this->types=$keys===range(0,count($patterns)-1)?FALSE:$keys;}function
tokenize($input){$this->input=$input;if($this->types){$this->tokens=StringUtils::matchAll($input,$this->re);$len=0;$count=count($this->types);$line=1;foreach($this->tokens
as&$match){$type=NULL;for($i=1;$i<=$count;$i++){if(!isset($match[$i])){break;}elseif($match[$i]!=NULL){$type=$this->types[$i-1];break;}}$match=self::createToken($match[0],$type,$line);$len+=strlen($match['value']);$line+=substr_count($match['value'],"\n");}if($len!==strlen($input)){$errorOffset=$len;}}else{$this->tokens=StringUtils::split($input,$this->re,PREG_SPLIT_NO_EMPTY);if($this->tokens&&!StringUtils::match(end($this->tokens),$this->re)){$tmp=StringUtils::split($this->input,$this->re,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);list(,$errorOffset)=end($tmp);}}if(isset($errorOffset)){$line=$errorOffset?substr_count($this->input,"\n",0,$errorOffset)+1:1;$col=$errorOffset-strrpos(substr($this->input,0,$errorOffset),"\n")+1;$token=str_replace("\n",'\n',substr($input,$errorOffset,10));throw
new
TokenizerException("Unexpected '$token' on line $line, column $col.");}return$this->tokens;}static
function
createToken($value,$type=NULL,$line=NULL){return
array('value'=>$value,'type'=>$type,'line'=>$line);}function
getOffset($i){$tokens=StringUtils::split($this->input,$this->re,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);$offset=isset($tokens[$i])?$tokens[$i][1]:strlen($this->input);return
array($offset,($offset?substr_count($this->input,"\n",0,$offset)+1:1),$offset-strrpos(substr($this->input,0,$offset),"\n"));}function
fetch(){return$this->scan(func_get_args(),TRUE);}function
fetchAll(){return$this->scan(func_get_args(),FALSE);}function
fetchUntil($arg){return$this->scan(func_get_args(),FALSE,TRUE,TRUE);}function
isNext($arg){return(bool)$this->scan(func_get_args(),TRUE,FALSE);}function
isCurrent($arg){return
in_array($this->current,func_get_args(),TRUE);}private
function
scan($wanted,$first,$advance=TRUE,$neg=FALSE){$res=FALSE;$pos=$this->position;while(isset($this->tokens[$pos])){$token=$this->tokens[$pos++];$r=is_array($token)?$token['type']:$token;if(!$wanted||in_array($r,$wanted,TRUE)^$neg){if($advance){$this->position=$pos;$this->current=$r;}$res.=is_array($token)?$token['value']:$token;if($first){break;}}elseif(!in_array($r,$this->ignored,TRUE)){break;}}return$res;}}class
TokenizerException
extends\Exception{}}