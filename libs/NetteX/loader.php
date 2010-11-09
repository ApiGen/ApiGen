<?php //netteloader=NetteX\IComponent

namespace {/**
 * NetteX Framework (version 2.0-dev 45c4669 released on 2010-11-09)
 *
 * Copyright (c) 2004, 2010 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "NetteX license", and/or
 * GPL license. For more information please see http://nette.org
 */

error_reporting(E_ALL|E_STRICT);@set_magic_quotes_runtime(FALSE);iconv_set_encoding('internal_encoding','UTF-8');extension_loaded('mbstring')&&mb_internal_encoding('UTF-8');@header('X-Powered-By: NetteX Framework');define('NETTEX',TRUE);define('NETTEX_DIR',__DIR__);define('NETTEX_VERSION_ID',20000);define('NETTEX_PACKAGE','5.3');function
callback($callback,$m=NULL){return($m===NULL&&$callback
instanceof
NetteX\Callback)?$callback:new
NetteX\Callback($callback,$m);}function
dump($var){foreach(func_get_args()as$arg)NetteX\Debug::dump($arg);return$var;}}namespace NetteX{use
NetteX;interface
IComponent{const
NAME_SEPARATOR='-';function
getName();function
getParent();function
setParent(IComponentContainer$parent=NULL,$name=NULL);}interface
IComponentContainer
extends
IComponent{function
addComponent(IComponent$component,$name);function
removeComponent(IComponent$component);function
getComponent($name);function
getComponents($deep=FALSE,$filterType=NULL);}}namespace NetteX\Application{use
NetteX;interface
ISignalReceiver{function
signalReceived($signal);}interface
IStatePersistent{function
loadState(array$params);function
saveState(array&$params);}interface
IRenderable{function
invalidateControl();function
isControlInvalid();}interface
IPartiallyRenderable
extends
IRenderable{}interface
IPresenter{function
run(PresenterRequest$request);}interface
IPresenterLoader{function
getPresenterClass(&$name);}interface
IPresenterResponse{function
send();}interface
IRouter{const
ONE_WAY=1;const
SECURED=2;function
match(NetteX\Web\IHttpRequest$httpRequest);function
constructUrl(PresenterRequest$appRequest,NetteX\Web\Uri$refUri);}}namespace NetteX{use
NetteX;interface
IFreezable{function
freeze();function
isFrozen();}interface
IDebugPanel{function
getTab();function
getPanel();function
getId();}}namespace NetteX\Caching{use
NetteX;interface
ICacheStorage{function
read($key);function
write($key,$data,array$dependencies);function
remove($key);function
clean(array$conds);}interface
ICacheJournal{function
write($key,array$dependencies);function
clean(array$conditions);}}namespace NetteX\Config{use
NetteX;interface
IConfigAdapter{static
function
load($file,$section=NULL);static
function
save($config,$file,$section=NULL);}}namespace NetteX\Forms{use
NetteX;interface
IFormControl{function
loadHttpData();function
setValue($value);function
getValue();function
getRules();function
getErrors();function
isDisabled();function
translate($s,$count=NULL);}interface
ISubmitterControl
extends
IFormControl{function
isSubmittedBy();function
getValidationScope();}interface
IFormRenderer{function
render(Form$form);}}namespace NetteX\Mail{use
NetteX;interface
IMailer{function
send(Mail$mail);}}namespace NetteX\Reflection{use
NetteX;interface
IAnnotation{function
__construct(array$values);}}namespace NetteX\Security{use
NetteX;interface
IAuthenticator{const
USERNAME=0;const
PASSWORD=1;const
IDENTITY_NOT_FOUND=1;const
INVALID_CREDENTIAL=2;const
FAILURE=3;const
NOT_APPROVED=4;function
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
getRoleId();}}namespace NetteX\Templates{use
NetteX;interface
ITemplate{function
render();}interface
IFileTemplate
extends
ITemplate{function
setFile($file);function
getFile();}}namespace NetteX{use
NetteX;interface
IContext{function
addService($name,$service,$singleton=TRUE,array$options=NULL);function
getService($name,array$options=NULL);function
removeService($name);function
hasService($name);}interface
ITranslator{function
translate($message,$count=NULL);}}namespace NetteX\Web{use
NetteX;interface
IHttpRequest{const
GET='GET',POST='POST',HEAD='HEAD',PUT='PUT',DELETE='DELETE';function
getUri();function
getQuery($key=NULL,$default=NULL);function
getPost($key=NULL,$default=NULL);function
getPostRaw();function
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
IHttpResponse{const
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
getAuthorizationHandler();}}namespace {class
XArgumentOutOfRangeException
extends
InvalidArgumentException{}class
XInvalidStateException
extends
RuntimeException{}class
XNotImplementedException
extends
LogicException{}class
XNotSupportedException
extends
LogicException{}class
XDeprecatedException
extends
XNotSupportedException{}class
XMemberAccessException
extends
LogicException{}class
XIOException
extends
RuntimeException{}class
XFileNotFoundException
extends
XIOException{}class
XDirectoryNotFoundException
extends
XIOException{}class
XFatalErrorException
extends
ErrorException{function
__construct($message,$code,$severity,$file,$line,$context){parent::__construct($message,$code,$severity,$file,$line);$this->context=$context;}}}namespace NetteX{use
NetteX;final
class
Framework{const
NAME='NetteX Framework';const
VERSION='2.0-dev';const
REVISION='45c4669 released on 2010-11-09';public
static$iAmUsingBadHost=FALSE;final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}}abstract
class
Object{static
function
getReflection(){return
new
NetteX\Reflection\ClassReflection(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}static
function
__callStatic($name,$args){$class=get_called_class();throw
new\XMemberAccessException("Call to undefined static method $class::$name().");}static
function
extensionMethod($name,$callback=NULL){if(strpos($name,'::')===FALSE){$class=get_called_class();}else{list($class,$name)=explode('::',$name);}$class=new
NetteX\Reflection\ClassReflection($class);if($callback===NULL){return$class->getExtensionMethod($name);}else{$class->setExtensionMethod($name,$callback);}}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){throw
new\XMemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");}}final
class
ObjectMixin{private
static$methods;final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
call($_this,$name,$args){$class=new
NetteX\Reflection\ClassReflection($_this);if($name===''){throw
new\XMemberAccessException("Call to class '$class->name' method without name.");}if($class->hasEventProperty($name)){if(is_array($list=$_this->$name)||$list
instanceof\Traversable){foreach($list
as$handler){callback($handler)->invokeArgs($args);}}return
NULL;}if($cb=$class->getExtensionMethod($name)){array_unshift($args,$_this);return$cb->invokeArgs($args);}throw
new\XMemberAccessException("Call to undefined method $class->name::$name().");}static
function&get($_this,$name){$class=get_class($_this);if($name===''){throw
new\XMemberAccessException("Cannot read a class '$class' property without name.");}if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";$m='get'.$name;if(isset(self::$methods[$class][$m])){$val=$_this->$m();return$val;}$m='is'.$name;if(isset(self::$methods[$class][$m])){$val=$_this->$m();return$val;}$name=func_get_arg(1);throw
new\XMemberAccessException("Cannot read an undeclared property $class::\$$name.");}static
function
set($_this,$name,$value){$class=get_class($_this);if($name===''){throw
new\XMemberAccessException("Cannot write to a class '$class' property without name.");}if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";if(isset(self::$methods[$class]['get'.$name])||isset(self::$methods[$class]['is'.$name])){$m='set'.$name;if(isset(self::$methods[$class][$m])){$_this->$m($value);return;}else{$name=func_get_arg(1);throw
new\XMemberAccessException("Cannot write to a read-only property $class::\$$name.");}}$name=func_get_arg(1);throw
new\XMemberAccessException("Cannot write to an undeclared property $class::\$$name.");}static
function
has($_this,$name){if($name===''){return
FALSE;}$class=get_class($_this);if(!isset(self::$methods[$class])){self::$methods[$class]=array_flip(get_class_methods($class));}$name[0]=$name[0]&"\xDF";return
isset(self::$methods[$class]['get'.$name])||isset(self::$methods[$class]['is'.$name]);}}final
class
Callback
extends
Object{private$cb;function
__construct($t,$m=NULL){if($m===NULL){$this->cb=$t;}else{$this->cb=array($t,$m);}if(!is_callable($this->cb,TRUE)){throw
new\InvalidArgumentException("Invalid callback.");}}function
__invoke(){if(!is_callable($this->cb)){throw
new\XInvalidStateException("Callback '$this' is not callable.");}$args=func_get_args();return
call_user_func_array($this->cb,$args);}function
invoke(){if(!is_callable($this->cb)){throw
new\XInvalidStateException("Callback '$this' is not callable.");}$args=func_get_args();return
call_user_func_array($this->cb,$args);}function
invokeArgs(array$args){if(!is_callable($this->cb)){throw
new\XInvalidStateException("Callback '$this' is not callable.");}return
call_user_func_array($this->cb,$args);}function
isCallable(){return
is_callable($this->cb);}function
getNative(){return$this->cb;}function
__toString(){is_callable($this->cb,TRUE,$textual);return$textual;}}}namespace NetteX\Loaders{use
NetteX;final
class
LimitedScope{private
static$vars;final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
evaluate(){if(func_num_args()>1){self::$vars=func_get_arg(1);extract(self::$vars);}return
eval('?>'.func_get_arg(0));}static
function
load(){if(func_num_args()>1){self::$vars=func_get_arg(1);extract(self::$vars);}return include func_get_arg(0);}}abstract
class
AutoLoader
extends
NetteX\Object{static
private$loaders=array();public
static$count=0;final
static
function
load($type){foreach(func_get_args()as$type){if(!class_exists($type)){throw
new\XInvalidStateException("Unable to load class or interface '$type'.");}}}final
static
function
getLoaders(){return
array_values(self::$loaders);}function
register(){if(!function_exists('spl_autoload_register')){throw
new\RuntimeException('spl_autoload does not exist in this PHP installation.');}spl_autoload_register(array($this,'tryLoad'));self::$loaders[spl_object_hash($this)]=$this;}function
unregister(){unset(self::$loaders[spl_object_hash($this)]);return
spl_autoload_unregister(array($this,'tryLoad'));}abstract
function
tryLoad($type);}}namespace NetteX{use
NetteX;abstract
class
Component
extends
Object
implements
IComponent{private$parent;private$name;private$monitors=array();function
__construct(IComponentContainer$parent=NULL,$name=NULL){if($parent!==NULL){$parent->addComponent($this,$name);}elseif(is_string($name)){$this->name=$name;}}function
lookup($type,$need=TRUE){if(!isset($this->monitors[$type])){$obj=$this->parent;$path=self::NAME_SEPARATOR.$this->name;$depth=1;while($obj!==NULL){if($obj
instanceof$type)break;$path=self::NAME_SEPARATOR.$obj->getName().$path;$depth++;$obj=$obj->getParent();if($obj===$this)$obj=NULL;}if($obj){$this->monitors[$type]=array($obj,$depth,substr($path,1),FALSE);}else{$this->monitors[$type]=array(NULL,NULL,NULL,FALSE);}}if($need&&$this->monitors[$type][0]===NULL){throw
new\XInvalidStateException("Component '$this->name' is not attached to '$type'.");}return$this->monitors[$type][0];}function
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
setParent(IComponentContainer$parent=NULL,$name=NULL){if($parent===NULL&&$this->parent===NULL&&$name!==NULL){$this->name=$name;return$this;}elseif($parent===$this->parent&&$name===NULL){return$this;}if($this->parent!==NULL&&$parent!==NULL){throw
new\XInvalidStateException("Component '$this->name' already has a parent.");}if($parent===NULL){$this->refreshMonitors(0);$this->parent=NULL;}else{$this->validateParent($parent);$this->parent=$parent;if($name!==NULL)$this->name=$name;$tmp=array();$this->refreshMonitors(0,$tmp);}return$this;}protected
function
validateParent(IComponentContainer$parent){}private
function
refreshMonitors($depth,&$missing=NULL,&$listeners=array()){if($this
instanceof
IComponentContainer){foreach($this->getComponents()as$component){if($component
instanceof
Component){$component->refreshMonitors($depth+1,$missing,$listeners);}}}if($missing===NULL){foreach($this->monitors
as$type=>$rec){if(isset($rec[1])&&$rec[1]>$depth){if($rec[3]){$this->monitors[$type]=array(NULL,NULL,NULL,TRUE);$listeners[]=array($this,$rec[0]);}else{unset($this->monitors[$type]);}}}}else{foreach($this->monitors
as$type=>$rec){if(isset($rec[0])){continue;}elseif(!$rec[3]){unset($this->monitors[$type]);}elseif(isset($missing[$type])){$this->monitors[$type]=array(NULL,NULL,NULL,TRUE);}else{$this->monitors[$type]=NULL;if($obj=$this->lookup($type,FALSE)){$listeners[]=array($this,$obj);}else{$missing[$type]=TRUE;}$this->monitors[$type][3]=TRUE;}}}if($depth===0){$method=$missing===NULL?'detached':'attached';foreach($listeners
as$item){$item[0]->$method($item[1]);}}}function
__clone(){if($this->parent===NULL){return;}elseif($this->parent
instanceof
ComponentContainer){$this->parent=$this->parent->_isCloning();if($this->parent===NULL){$this->refreshMonitors(0);}}else{$this->parent=NULL;$this->refreshMonitors(0);}}final
function
__wakeup(){throw
new\XNotImplementedException;}}class
ComponentContainer
extends
Component
implements
IComponentContainer{private$components=array();private$cloning;function
addComponent(IComponent$component,$name,$insertBefore=NULL){if($name===NULL){$name=$component->getName();}if(is_int($name)){$name=(string)$name;}elseif(!is_string($name)){throw
new\InvalidArgumentException("Component name must be integer or string, ".gettype($name)." given.");}elseif(!preg_match('#^[a-zA-Z0-9_]+$#',$name)){throw
new\InvalidArgumentException("Component name must be non-empty alphanumeric string, '$name' given.");}if(isset($this->components[$name])){throw
new\XInvalidStateException("Component with name '$name' already exists.");}$obj=$this;do{if($obj===$component){throw
new\XInvalidStateException("Circular reference detected while adding component '$name'.");}$obj=$obj->getParent();}while($obj!==NULL);$this->validateChildComponent($component);try{if(isset($this->components[$insertBefore])){$tmp=array();foreach($this->components
as$k=>$v){if($k===$insertBefore)$tmp[$name]=$component;$tmp[$k]=$v;}$this->components=$tmp;}else{$this->components[$name]=$component;}$component->setParent($this,$name);}catch(\Exception$e){unset($this->components[$name]);throw$e;}}function
removeComponent(IComponent$component){$name=$component->getName();if(!isset($this->components[$name])||$this->components[$name]!==$component){throw
new\InvalidArgumentException("Component named '$name' is not located in this container.");}unset($this->components[$name]);$component->setParent(NULL);}final
function
getComponent($name,$need=TRUE){if(is_int($name)){$name=(string)$name;}elseif(!is_string($name)){throw
new\InvalidArgumentException("Component name must be integer or string, ".gettype($name)." given.");}else{$a=strpos($name,self::NAME_SEPARATOR);if($a!==FALSE){$ext=(string)substr($name,$a+1);$name=substr($name,0,$a);}if($name===''){throw
new\InvalidArgumentException("Component or subcomponent name must not be empty string.");}}if(!isset($this->components[$name])){$component=$this->createComponent($name);if($component
instanceof
IComponent&&$component->getParent()===NULL){$this->addComponent($component,$name);}}if(isset($this->components[$name])){if(!isset($ext)){return$this->components[$name];}elseif($this->components[$name]instanceof
IComponentContainer){return$this->components[$name]->getComponent($ext,$need);}elseif($need){throw
new\InvalidArgumentException("Component with name '$name' is not container and cannot have '$ext' component.");}}elseif($need){throw
new\InvalidArgumentException("Component with name '$name' does not exist.");}}protected
function
createComponent($name){$ucname=ucfirst($name);$method='createComponent'.$ucname;if($ucname!==$name&&method_exists($this,$method)&&$this->getReflection()->getMethod($method)->getName()===$method){return$this->$method($name);}}final
function
getComponents($deep=FALSE,$filterType=NULL){$iterator=new
RecursiveComponentIterator($this->components);if($deep){$deep=$deep>0?\RecursiveIteratorIterator::SELF_FIRST:\RecursiveIteratorIterator::CHILD_FIRST;$iterator=new\RecursiveIteratorIterator($iterator,$deep);}if($filterType){$iterator=new
InstanceFilterIterator($iterator,$filterType);}return$iterator;}protected
function
validateChildComponent(IComponent$child){}function
__clone(){if($this->components){$oldMyself=reset($this->components)->getParent();$oldMyself->cloning=$this;foreach($this->components
as$name=>$component){$this->components[$name]=clone$component;}$oldMyself->cloning=NULL;}parent::__clone();}function
_isCloning(){return$this->cloning;}}class
RecursiveComponentIterator
extends\RecursiveArrayIterator
implements\Countable{function
hasChildren(){return$this->current()instanceof
IComponentContainer;}function
getChildren(){return$this->current()->getComponents();}function
count(){return
iterator_count($this);}}}namespace NetteX\Forms{use
NetteX;class
FormContainer
extends
NetteX\ComponentContainer
implements\ArrayAccess{public$onValidate;protected$currentGroup;protected$valid;function
setDefaults($values,$erase=FALSE){$form=$this->getForm(FALSE);if(!$form||!$form->isAnchored()||!$form->isSubmitted()){$this->setValues($values,$erase);}return$this;}function
setValues($values,$erase=FALSE){if($values
instanceof\Traversable){$values=iterator_to_array($values);}elseif(!is_array($values)){throw
new\InvalidArgumentException("First parameter must be an array, ".gettype($values)." given.");}$cursor=&$values;$iterator=$this->getComponents(TRUE);foreach($iterator
as$name=>$control){$sub=$iterator->getSubIterator();if(!isset($sub->cursor)){$sub->cursor=&$cursor;}if($control
instanceof
IFormControl){if((is_array($sub->cursor)||$sub->cursor
instanceof\ArrayAccess)&&array_key_exists($name,$sub->cursor)){$control->setValue($sub->cursor[$name]);}elseif($erase){$control->setValue(NULL);}}if($control
instanceof
FormContainer){if((is_array($sub->cursor)||$sub->cursor
instanceof\ArrayAccess)&&isset($sub->cursor[$name])){$cursor=&$sub->cursor[$name];}else{unset($cursor);$cursor=NULL;}}}return$this;}function
getValues(){$values=array();$cursor=&$values;$iterator=$this->getComponents(TRUE);foreach($iterator
as$name=>$control){$sub=$iterator->getSubIterator();if(!isset($sub->cursor)){$sub->cursor=&$cursor;}if($control
instanceof
IFormControl&&!$control->isDisabled()&&!($control
instanceof
ISubmitterControl)){$sub->cursor[$name]=$control->getValue();}if($control
instanceof
FormContainer){$cursor=&$sub->cursor[$name];$cursor=array();}}return$values;}function
isValid(){if($this->valid===NULL){$this->validate();}return$this->valid;}function
validate(){$this->valid=TRUE;$this->onValidate($this);foreach($this->getControls()as$control){if(!$control->getRules()->validate()){$this->valid=FALSE;}}}function
setCurrentGroup(FormGroup$group=NULL){$this->currentGroup=$group;return$this;}function
getCurrentGroup(){return$this->currentGroup;}function
addComponent(NetteX\IComponent$component,$name,$insertBefore=NULL){parent::addComponent($component,$name,$insertBefore);if($this->currentGroup!==NULL&&$component
instanceof
IFormControl){$this->currentGroup->add($component);}}function
getControls(){return$this->getComponents(TRUE,'NetteX\Forms\IFormControl');}function
getForm($need=TRUE){return$this->lookup('NetteX\Forms\Form',$need);}function
addText($name,$label=NULL,$cols=NULL,$maxLength=NULL){return$this[$name]=new
TextInput($label,$cols,$maxLength);}function
addPassword($name,$label=NULL,$cols=NULL,$maxLength=NULL){$control=new
TextInput($label,$cols,$maxLength);$control->setType('password');return$this[$name]=$control;}function
addTextArea($name,$label=NULL,$cols=40,$rows=10){return$this[$name]=new
TextArea($label,$cols,$rows);}function
addFile($name,$label=NULL){return$this[$name]=new
FileUpload($label);}function
addHidden($name,$default=NULL){$control=new
HiddenField;$control->setDefaultValue($default);return$this[$name]=$control;}function
addCheckbox($name,$caption=NULL){return$this[$name]=new
Checkbox($caption);}function
addRadioList($name,$label=NULL,array$items=NULL){return$this[$name]=new
RadioList($label,$items);}function
addSelect($name,$label=NULL,array$items=NULL,$size=NULL){return$this[$name]=new
SelectBox($label,$items,$size);}function
addMultiSelect($name,$label=NULL,array$items=NULL,$size=NULL){return$this[$name]=new
MultiSelectBox($label,$items,$size);}function
addSubmit($name,$caption=NULL){return$this[$name]=new
SubmitButton($caption);}function
addButton($name,$caption){return$this[$name]=new
Button($caption);}function
addImage($name,$src=NULL,$alt=NULL){return$this[$name]=new
ImageButton($src,$alt);}function
addContainer($name){$control=new
FormContainer;$control->currentGroup=$this->currentGroup;return$this[$name]=$control;}final
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
new\XNotImplementedException('Form cloning is not supported yet.');}}class
Form
extends
FormContainer{const
EQUAL=':equal';const
IS_IN=':equal';const
FILLED=':filled';const
VALID=':valid';const
PROTECTION='NetteX\Forms\HiddenField::validateEqual';const
SUBMITTED=':submitted';const
MIN_LENGTH=':minLength';const
MAX_LENGTH=':maxLength';const
LENGTH=':length';const
EMAIL=':email';const
URL=':url';const
REGEXP=':regexp';const
PATTERN=':pattern';const
INTEGER=':integer';const
NUMERIC=':integer';const
FLOAT=':float';const
RANGE=':range';const
MAX_FILE_SIZE=':fileSize';const
MIME_TYPE=':mimeType';const
IMAGE=':image';const
GET='get';const
POST='post';const
TRACKER_ID='_form_';const
PROTECTOR_ID='_token_';public$onSubmit;public$onInvalidSubmit;private$submittedBy;private$httpData;private$element;private$renderer;private$translator;private$groups=array();private$errors=array();function
__construct($name=NULL){$this->element=NetteX\Web\Html::el('form');$this->element->action='';$this->element->method=self::POST;$this->element->id='frm-'.$name;$this->monitor(__CLASS__);if($name!==NULL){$tracker=new
HiddenField($name);$tracker->unmonitor(__CLASS__);$this[self::TRACKER_ID]=$tracker;}parent::__construct(NULL,$name);}protected
function
attached($obj){if($obj
instanceof
self){throw
new\XInvalidStateException('Nested forms are forbidden.');}}final
function
getForm($need=TRUE){return$this;}function
setAction($url){$this->element->action=$url;return$this;}function
getAction(){return$this->element->action;}function
setMethod($method){if($this->httpData!==NULL){throw
new\XInvalidStateException(__METHOD__.'() must be called until the form is empty.');}$this->element->method=strtolower($method);return$this;}function
getMethod(){return$this->element->method;}function
addProtection($message=NULL,$timeout=NULL){$session=$this->getSession()->getNamespace('NetteX.Forms.Form/CSRF');$key="key$timeout";if(isset($session->$key)){$token=$session->$key;}else{$session->$key=$token=md5(uniqid('',TRUE));}$session->setExpiration($timeout,$key);$this[self::PROTECTOR_ID]=new
HiddenField($token);$this[self::PROTECTOR_ID]->addRule(self::PROTECTION,$message,$token);}function
addGroup($caption=NULL,$setAsCurrent=TRUE){$group=new
FormGroup;$group->setOption('label',$caption);$group->setOption('visual',TRUE);if($setAsCurrent){$this->setCurrentGroup($group);}if(isset($this->groups[$caption])){return$this->groups[]=$group;}else{return$this->groups[$caption]=$group;}}function
removeGroup($name){if(is_string($name)&&isset($this->groups[$name])){$group=$this->groups[$name];}elseif($name
instanceof
FormGroup&&in_array($name,$this->groups,TRUE)){$group=$name;$name=array_search($group,$this->groups,TRUE);}else{throw
new\InvalidArgumentException("Group not found in form '$this->name'");}foreach($group->getControls()as$control){$this->removeComponent($control);}unset($this->groups[$name]);}function
getGroups(){return$this->groups;}function
getGroup($name){return
isset($this->groups[$name])?$this->groups[$name]:NULL;}function
setTranslator(NetteX\ITranslator$translator=NULL){$this->translator=$translator;return$this;}final
function
getTranslator(){return$this->translator;}function
isAnchored(){return
TRUE;}final
function
isSubmitted(){if($this->submittedBy===NULL){$this->getHttpData();$this->submittedBy=!empty($this->httpData);}return$this->submittedBy;}function
setSubmittedBy(ISubmitterControl$by=NULL){$this->submittedBy=$by===NULL?FALSE:$by;return$this;}final
function
getHttpData(){if($this->httpData===NULL){if(!$this->isAnchored()){throw
new\XInvalidStateException('Form is not anchored and therefore can not determine whether it was submitted.');}$this->httpData=(array)$this->receiveHttpData();}return$this->httpData;}function
fireEvents(){if(!$this->isSubmitted()){return;}elseif($this->submittedBy
instanceof
ISubmitterControl){if(!$this->submittedBy->getValidationScope()||$this->isValid()){$this->submittedBy->click();$this->onSubmit($this);}else{$this->submittedBy->onInvalidClick($this->submittedBy);$this->onInvalidSubmit($this);}}elseif($this->isValid()){$this->onSubmit($this);}else{$this->onInvalidSubmit($this);}}protected
function
receiveHttpData(){$httpRequest=$this->getHttpRequest();if(strcasecmp($this->getMethod(),$httpRequest->getMethod())){return;}$httpRequest->setEncoding('utf-8');if($httpRequest->isMethod('post')){$data=NetteX\ArrayTools::mergeTree($httpRequest->getPost(),$httpRequest->getFiles());}else{$data=$httpRequest->getQuery();}if($tracker=$this->getComponent(self::TRACKER_ID,FALSE)){if(!isset($data[self::TRACKER_ID])||$data[self::TRACKER_ID]!==$tracker->getValue()){return;}}return$data;}function
getValues(){$values=parent::getValues();unset($values[self::TRACKER_ID],$values[self::PROTECTOR_ID]);return$values;}function
addError($message){$this->valid=FALSE;if($message!==NULL&&!in_array($message,$this->errors,TRUE)){$this->errors[]=$message;}}function
getErrors(){return$this->errors;}function
hasErrors(){return(bool)$this->getErrors();}function
cleanErrors(){$this->errors=array();$this->valid=NULL;}function
getElementPrototype(){return$this->element;}function
setRenderer(IFormRenderer$renderer){$this->renderer=$renderer;return$this;}final
function
getRenderer(){if($this->renderer===NULL){$this->renderer=new
ConventionalRenderer;}return$this->renderer;}function
render(){$args=func_get_args();array_unshift($args,$this);echo
call_user_func_array(array($this->getRenderer(),'render'),$args);}function
__toString(){try{return$this->getRenderer()->render($this);}catch(\Exception$e){if(func_get_args()&&func_get_arg(0)){throw$e;}else{NetteX\Debug::toStringException($e);}}}protected
function
getHttpRequest(){return
class_exists('NetteX\Environment')?NetteX\Environment::getHttpRequest():new
NetteX\Web\HttpRequest;}protected
function
getSession(){return
NetteX\Environment::getSession();}}}namespace NetteX\Application{use
NetteX;class
AppForm
extends
NetteX\Forms\Form
implements
ISignalReceiver{function
__construct(NetteX\IComponentContainer$parent=NULL,$name=NULL){parent::__construct();$this->monitor('NetteX\Application\Presenter');if($parent!==NULL){$parent->addComponent($this,$name);}}function
getPresenter($need=TRUE){return$this->lookup('NetteX\Application\Presenter',$need);}protected
function
attached($presenter){if($presenter
instanceof
Presenter){$name=$this->lookupPath('NetteX\Application\Presenter');if(!isset($this->getElementPrototype()->id)){$this->getElementPrototype()->id='frm-'.$name;}$this->setAction(new
Link($presenter,$name.self::NAME_SEPARATOR.'submit!',array()));if($this->isSubmitted()){foreach($this->getControls()as$control){$control->loadHttpData();}}}parent::attached($presenter);}function
isAnchored(){return(bool)$this->getPresenter(FALSE);}protected
function
receiveHttpData(){$presenter=$this->getPresenter();if(!$presenter->isSignalReceiver($this,'submit')){return;}$isPost=$this->getMethod()===self::POST;$request=$presenter->getRequest();if($request->isMethod('forward')||$request->isMethod('post')!==$isPost){return;}if($isPost){return
NetteX\ArrayTools::mergeTree($request->getPost(),$request->getFiles());}else{return$request->getParams();}}function
signalReceived($signal){if($signal==='submit'){$this->fireEvents();}else{throw
new
BadSignalException("Missing handler for signal '$signal' in {$this->reflection->name}.");}}}class
Application
extends
NetteX\Object{public
static$maxLoop=20;public$catchExceptions;public$errorPresenter;public$onStartup;public$onShutdown;public$onRequest;public$onResponse;public$onError;public$allowedMethods=array('GET','POST','HEAD','PUT','DELETE');private$requests=array();private$presenter;private$context;function
run(){$httpRequest=$this->getHttpRequest();$httpResponse=$this->getHttpResponse();$httpRequest->setEncoding('UTF-8');$session=$this->getSession();if(!$session->isStarted()&&$session->exists()){$session->start();}if($this->allowedMethods){$method=$httpRequest->getMethod();if(!in_array($method,$this->allowedMethods,TRUE)){$httpResponse->setCode(NetteX\Web\IHttpResponse::S501_NOT_IMPLEMENTED);$httpResponse->setHeader('Allow',implode(',',$this->allowedMethods));echo'<h1>Method '.htmlSpecialChars($method).' is not implemented</h1>';return;}}$request=NULL;$repeatedError=FALSE;do{try{if(count($this->requests)>self::$maxLoop){throw
new
ApplicationException('Too many loops detected in application life cycle.');}if(!$request){$this->onStartup($this);$router=$this->getRouter();NetteX\Debug::addPanel(new
RoutingDebugger($router,$httpRequest));$request=$router->match($httpRequest);if(!($request
instanceof
PresenterRequest)){$request=NULL;throw
new
BadRequestException('No route for HTTP request.');}if(strcasecmp($request->getPresenterName(),$this->errorPresenter)===0){throw
new
BadRequestException('Invalid request. Presenter is not achievable.');}}$this->requests[]=$request;$this->onRequest($this,$request);$presenter=$request->getPresenterName();try{$class=$this->getPresenterLoader()->getPresenterClass($presenter);$request->setPresenterName($presenter);}catch(InvalidPresenterException$e){throw
new
BadRequestException($e->getMessage(),404,$e);}$request->freeze();$this->presenter=new$class;$response=$this->presenter->run($request);$this->onResponse($this,$response);if($response
instanceof
ForwardingResponse){$request=$response->getRequest();continue;}elseif($response
instanceof
IPresenterResponse){$response->send();}break;}catch(\Exception$e){$this->onError($this,$e);if(!$this->catchExceptions){$this->onShutdown($this,$e);throw$e;}if($repeatedError){$e=new
ApplicationException('An error occured while executing error-presenter',0,$e);}if(!$httpResponse->isSent()){$httpResponse->setCode($e
instanceof
BadRequestException?$e->getCode():500);}if(!$repeatedError&&$this->errorPresenter){$repeatedError=TRUE;if($this->presenter){try{$this->presenter->forward(":$this->errorPresenter:",array('exception'=>$e));}catch(AbortException$foo){$request=$this->presenter->getLastCreatedRequest();}}else{$request=new
PresenterRequest($this->errorPresenter,PresenterRequest::FORWARD,array('exception'=>$e));}}else{if($e
instanceof
BadRequestException){$code=$e->getCode();}else{$code=500;NetteX\Debug::log($e,NetteX\Debug::ERROR);}echo"<!DOCTYPE html><meta name=robots content=noindex><meta name=generator content='NetteX Framework'>\n\n";echo"<style>body{color:#333;background:white;width:500px;margin:100px auto}h1{font:bold 47px/1.5 sans-serif;margin:.6em 0}p{font:21px/1.5 Georgia,serif;margin:1.5em 0}small{font-size:70%;color:gray}</style>\n\n";static$messages=array(0=>array('Oops...','Your browser sent a request that this server could not understand or process.'),403=>array('Access Denied','You do not have permission to view this page. Please try contact the web site administrator if you believe you should be able to view this page.'),404=>array('Page Not Found','The page you requested could not be found. It is possible that the address is incorrect, or that the page no longer exists. Please use a search engine to find what you are looking for.'),405=>array('Method Not Allowed','The requested method is not allowed for the URL.'),410=>array('Page Not Found','The page you requested has been taken off the site. We apologize for the inconvenience.'),500=>array('Server Error','We\'re sorry! The server encountered an internal error and was unable to complete your request. Please try again later.'));$message=isset($messages[$code])?$messages[$code]:$messages[0];echo"<title>$message[0]</title>\n\n<h1>$message[0]</h1>\n\n<p>$message[1]</p>\n\n";if($code)echo"<p><small>error $code</small></p>";break;}}}while(1);$this->onShutdown($this,isset($e)?$e:NULL);}final
function
getRequests(){return$this->requests;}final
function
getPresenter(){return$this->presenter;}function
setContext(NetteX\IContext$context){$this->context=$context;return$this;}final
function
getContext(){return$this->context;}final
function
getService($name,array$options=NULL){return$this->context->getService($name,$options);}function
getRouter(){return$this->context->getService('NetteX\\Application\\IRouter');}function
setRouter(IRouter$router){$this->context->addService('NetteX\\Application\\IRouter',$router);return$this;}function
getPresenterLoader(){return$this->context->getService('NetteX\\Application\\IPresenterLoader');}protected
function
getHttpRequest(){return$this->context->getService('NetteX\\Web\\IHttpRequest');}protected
function
getHttpResponse(){return$this->context->getService('NetteX\\Web\\IHttpResponse');}protected
function
getSession($namespace=NULL){$handler=$this->context->getService('NetteX\\Web\\Session');return$namespace===NULL?$handler:$handler->getNamespace($namespace);}function
storeRequest($expiration='+ 10 minutes'){$session=$this->getSession('NetteX.Application/requests');do{$key=substr(md5(lcg_value()),0,4);}while(isset($session[$key]));$session[$key]=end($this->requests);$session->setExpiration($expiration,$key);return$key;}function
restoreRequest($key){$session=$this->getSession('NetteX.Application/requests');if(isset($session[$key])){$request=clone$session[$key];unset($session[$key]);$request->setFlag(PresenterRequest::RESTORED,TRUE);$this->presenter->sendResponse(new
ForwardingResponse($request));}}}abstract
class
PresenterComponent
extends
NetteX\ComponentContainer
implements
ISignalReceiver,IStatePersistent,\ArrayAccess{protected$params=array();function
__construct(NetteX\IComponentContainer$parent=NULL,$name=NULL){$this->monitor('NetteX\Application\Presenter');parent::__construct($parent,$name);}function
getPresenter($need=TRUE){return$this->lookup('NetteX\Application\Presenter',$need);}function
getUniqueId(){return$this->lookupPath('NetteX\Application\Presenter',TRUE);}protected
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
new\XInvalidStateException("Persistent parameter must be scalar or array, {$this->reflection->name}::\$$nm is ".gettype($val));}else{if(isset($meta['def'])){settype($val,gettype($meta['def']));if($val===$meta['def'])$val=NULL;}else{if((string)$val==='')$val=NULL;}$params[$nm]=$val;}}}final
function
getParam($name=NULL,$default=NULL){if(func_num_args()===0){return$this->params;}elseif(isset($this->params[$name])){return$this->params[$name];}else{return$default;}}final
function
getParamId($name){$uid=$this->getUniqueId();return$uid===''?$name:$uid.self::NAME_SEPARATOR.$name;}static
function
getPersistentParams(){$rc=new
NetteX\Reflection\ClassReflection(get_called_class());$params=array();foreach($rc->getProperties(\ReflectionProperty::IS_PUBLIC)as$rp){if(!$rp->isStatic()&&$rp->hasAnnotation('persistent')){$params[]=$rp->getName();}}return$params;}function
signalReceived($signal){if(!$this->tryCall($this->formatSignalMethod($signal),$this->params)){throw
new
BadSignalException("There is no handler for signal '$signal' in class {$this->reflection->name}.");}}function
formatSignalMethod($signal){return$signal==NULL?NULL:'handle'.$signal;}function
link($destination,$args=array()){if(!is_array($args)){$args=func_get_args();array_shift($args);}try{return$this->getPresenter()->createRequest($this,$destination,$args,'link');}catch(InvalidLinkException$e){return$this->getPresenter()->handleInvalidLink($e);}}function
lazyLink($destination,$args=array()){if(!is_array($args)){$args=func_get_args();array_shift($args);}return
new
Link($this,$destination,$args);}function
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
getTemplate(){if($this->template===NULL){$value=$this->createTemplate();if(!($value
instanceof
NetteX\Templates\ITemplate||$value===NULL)){$class=get_class($value);throw
new\UnexpectedValueException("Object returned by {$this->reflection->name}::createTemplate() must be instance of NetteX\\Templates\\ITemplate, '$class' given.");}$this->template=$value;}return$this->template;}protected
function
createTemplate(){$template=new
NetteX\Templates\FileTemplate;$presenter=$this->getPresenter(FALSE);$template->onPrepareFilters[]=callback($this,'templatePrepareFilters');$template->control=$this;$template->presenter=$presenter;$template->user=NetteX\Environment::getUser();$template->baseUri=rtrim(NetteX\Environment::getVariable('baseUri',NULL),'/');$template->basePath=preg_replace('#https?://[^/]+#A','',$template->baseUri);if($presenter!==NULL&&$presenter->hasFlashSession()){$id=$this->getParamId('flash');$template->flashes=$presenter->getFlashSession()->$id;}if(!isset($template->flashes)||!is_array($template->flashes)){$template->flashes=array();}$template->registerHelper('escape','NetteX\Templates\TemplateHelpers::escapeHtml');$template->registerHelper('escapeUrl','rawurlencode');$template->registerHelper('stripTags','strip_tags');$template->registerHelper('nl2br','nl2br');$template->registerHelper('substr','iconv_substr');$template->registerHelper('repeat','str_repeat');$template->registerHelper('replaceRE','NetteX\String::replace');$template->registerHelper('implode','implode');$template->registerHelper('number','number_format');$template->registerHelperLoader('NetteX\Templates\TemplateHelpers::loader');return$template;}function
templatePrepareFilters($template){$template->registerFilter(new
NetteX\Templates\LatteFilter);}function
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
getSnippetId($name=NULL){return'snippet-'.$this->getUniqueId().'-'.$name;}}class
AbortException
extends\Exception{}class
ApplicationException
extends\Exception{}class
BadRequestException
extends\Exception{protected$defaultCode=404;function
__construct($message='',$code=0,\Exception$previous=NULL){if($code<200||$code>504){$code=$this->defaultCode;}{parent::__construct($message,$code,$previous);}}}class
BadSignalException
extends
BadRequestException{protected$defaultCode=403;}class
ForbiddenRequestException
extends
BadRequestException{protected$defaultCode=403;}class
InvalidLinkException
extends\Exception{}class
InvalidPresenterException
extends
InvalidLinkException{}class
Link
extends
NetteX\Object{private$component;private$destination;private$params;function
__construct(PresenterComponent$component,$destination,array$params){$this->component=$component;$this->destination=$destination;$this->params=$params;}function
getDestination(){return$this->destination;}function
setParam($key,$value){$this->params[$key]=$value;return$this;}function
getParam($key){return
isset($this->params[$key])?$this->params[$key]:NULL;}function
getParams(){return$this->params;}function
__toString(){try{return$this->component->link($this->destination,$this->params);}catch(\Exception$e){NetteX\Debug::toStringException($e);}}}use
NetteX\Environment;abstract
class
Presenter
extends
Control
implements
IPresenter{const
INVALID_LINK_SILENT=1;const
INVALID_LINK_WARNING=2;const
INVALID_LINK_EXCEPTION=3;const
SIGNAL_KEY='do';const
ACTION_KEY='action';const
FLASH_KEY='_fid';public
static$defaultAction='default';public
static$invalidLinkMode;public$onShutdown;private$request;private$response;public$autoCanonicalize=TRUE;public$absoluteUrls=FALSE;private$globalParams;private$globalState;private$globalStateSinces;private$action;private$view;private$layout;private$payload;private$signalReceiver;private$signal;private$ajaxMode;private$startupCheck;private$lastCreatedRequest;private$lastCreatedRequestFlag;final
function
getRequest(){return$this->request;}final
function
getPresenter($need=TRUE){return$this;}final
function
getUniqueId(){return'';}function
run(PresenterRequest$request){try{$this->request=$request;$this->payload=(object)NULL;$this->setParent($this->getParent(),$request->getPresenterName());$this->initGlobalParams();$this->startup();if(!$this->startupCheck){$class=$this->reflection->getMethod('startup')->getDeclaringClass()->getName();throw
new\XInvalidStateException("Method $class::startup() or its descendant doesn't call parent::startup().");}$this->tryCall($this->formatActionMethod($this->getAction()),$this->params);if($this->autoCanonicalize){$this->canonicalize();}if($this->getHttpRequest()->isMethod('head')){$this->terminate();}$this->processSignal();$this->beforeRender();$this->tryCall($this->formatRenderMethod($this->getView()),$this->params);$this->afterRender();$this->saveGlobalState();if($this->isAjax()){$this->payload->state=$this->getGlobalState();}$this->sendTemplate();}catch(AbortException$e){}{if($this->isAjax())try{$hasPayload=(array)$this->payload;unset($hasPayload['state']);if($this->response
instanceof
RenderResponse&&($this->isControlInvalid()||$hasPayload)){$this->response->send();$this->sendPayload();}elseif(!$this->response&&$hasPayload){$this->sendPayload();}}catch(AbortException$e){}if($this->hasFlashSession()){$this->getFlashSession()->setExpiration($this->response
instanceof
RedirectingResponse?'+ 30 seconds':'+ 3 seconds');}$this->onShutdown($this,$this->response);$this->shutdown($this->response);return$this->response;}}protected
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
NetteX\Component){$component=$component===$this?'':$component->lookupPath(__CLASS__,TRUE);}if($this->signal===NULL){return
FALSE;}elseif($signal===TRUE){return$component===''||strncmp($this->signalReceiver.'-',$component.'-',strlen($component)+1)===0;}elseif($signal===NULL){return$this->signalReceiver===$component;}else{return$this->signalReceiver===$component&&strcasecmp($signal,$this->signal)===0;}}final
function
getAction($fullyQualified=FALSE){return$fullyQualified?':'.$this->getName().':'.$this->action:$this->action;}function
changeAction($action){if(NetteX\String::match($action,"#^[a-zA-Z0-9][a-zA-Z0-9_\x7f-\xff]*$#")){$this->action=$action;$this->view=$action;}else{throw
new
BadRequestException("Action name '$action' is not alphanumeric string.");}}final
function
getView(){return$this->view;}function
setView($view){$this->view=(string)$view;return$this;}final
function
getLayout(){return$this->layout;}function
setLayout($layout){$this->layout=$layout===FALSE?FALSE:(string)$layout;return$this;}function
sendTemplate(){$template=$this->getTemplate();if(!$template)return;if($template
instanceof
NetteX\Templates\IFileTemplate&&!$template->getFile()){$files=$this->formatTemplateFiles($this->getName(),$this->view);foreach($files
as$file){if(is_file($file)){$template->setFile($file);break;}}if(!$template->getFile()){$file=str_replace(Environment::getVariable('appDir'),"\xE2\x80\xA6",reset($files));throw
new
BadRequestException("Page not found. Missing template '$file'.");}if($this->layout!==FALSE){$files=$this->formatLayoutTemplateFiles($this->getName(),$this->layout?$this->layout:'layout');foreach($files
as$file){if(is_file($file)){$template->layout=$file;$template->_extends=$file;break;}}if(empty($template->layout)&&$this->layout!==NULL){$file=str_replace(Environment::getVariable('appDir'),"\xE2\x80\xA6",reset($files));throw
new\XFileNotFoundException("Layout not found. Missing template '$file'.");}}}$this->sendResponse(new
RenderResponse($template));}function
formatLayoutTemplateFiles($presenter,$layout){$appDir=Environment::getVariable('appDir');$path='/'.str_replace(':','Module/',$presenter);$pathP=substr_replace($path,'/templates',strrpos($path,'/'),0);$list=array("$appDir$pathP/@$layout.phtml","$appDir$pathP.@$layout.phtml");while(($path=substr($path,0,strrpos($path,'/')))!==FALSE){$list[]="$appDir$path/templates/@$layout.phtml";}return$list;}function
formatTemplateFiles($presenter,$view){$appDir=Environment::getVariable('appDir');$path='/'.str_replace(':','Module/',$presenter);$pathP=substr_replace($path,'/templates',strrpos($path,'/'),0);$path=substr_replace($path,'/templates',strrpos($path,'/'));return
array("$appDir$pathP/$view.phtml","$appDir$pathP.$view.phtml","$appDir$path/@global.$view.phtml");}protected
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
JsonResponse($this->payload));}function
sendResponse(IPresenterResponse$response){$this->response=$response;$this->terminate();}function
terminate(){if(func_num_args()!==0){trigger_error(__METHOD__.' is not intended to send a PresenterResponse; use sendResponse() instead.',E_USER_WARNING);$this->sendResponse(func_get_arg(0));}throw
new
AbortException();}function
forward($destination,$args=array()){if($destination
instanceof
PresenterRequest){$this->sendResponse(new
ForwardingResponse($destination));}elseif(!is_array($args)){$args=func_get_args();array_shift($args);}$this->createRequest($this,$destination,$args,'forward');$this->sendResponse(new
ForwardingResponse($this->lastCreatedRequest));}function
redirectUri($uri,$code=NULL){if($this->isAjax()){$this->payload->redirect=(string)$uri;$this->sendPayload();}elseif(!$code){$code=$this->getHttpRequest()->isMethod('post')?NetteX\Web\IHttpResponse::S303_POST_GET:NetteX\Web\IHttpResponse::S302_FOUND;}$this->sendResponse(new
RedirectingResponse($uri,$code));}function
backlink(){return$this->getAction(TRUE);}function
getLastCreatedRequest(){return$this->lastCreatedRequest;}function
getLastCreatedRequestFlag($flag){return!empty($this->lastCreatedRequestFlag[$flag]);}function
canonicalize(){if(!$this->isAjax()&&($this->request->isMethod('get')||$this->request->isMethod('head'))){$uri=$this->createRequest($this,$this->action,$this->getGlobalState()+$this->request->params,'redirectX');if($uri!==NULL&&!$this->getHttpRequest()->getUri()->isEqual($uri)){$this->sendResponse(new
RedirectingResponse($uri,NetteX\Web\IHttpResponse::S301_MOVED_PERMANENTLY));}}}function
lastModified($lastModified,$etag=NULL,$expire=NULL){if(!Environment::isProduction()){return;}if($expire!==NULL){$this->getHttpResponse()->setExpiration($expire);}if(!$this->getHttpContext()->isModified($lastModified,$etag)){$this->terminate();}}final
protected
function
createRequest($component,$destination,array$args,$mode){static$presenterLoader,$router,$httpRequest;if($presenterLoader===NULL){$presenterLoader=$this->getApplication()->getPresenterLoader();$router=$this->getApplication()->getRouter();$httpRequest=$this->getHttpRequest();}$this->lastCreatedRequest=$this->lastCreatedRequestFlag=NULL;$a=strpos($destination,'#');if($a===FALSE){$fragment='';}else{$fragment=substr($destination,$a);$destination=substr($destination,0,$a);}$a=strpos($destination,'?');if($a!==FALSE){parse_str(substr($destination,$a+1),$args);$destination=substr($destination,0,$a);}$a=strpos($destination,'//');if($a===FALSE){$scheme=FALSE;}else{$scheme=substr($destination,0,$a);$destination=substr($destination,$a+2);}if(!($component
instanceof
Presenter)||substr($destination,-1)==='!'){$signal=rtrim($destination,'!');$a=strrpos($signal,':');if($a!==FALSE){$component=$component->getComponent(strtr(substr($signal,0,$a),':','-'));$signal=(string)substr($signal,$a+1);}if($signal==NULL){throw
new
InvalidLinkException("Signal must be non-empty string.");}$destination='this';}if($destination==NULL){throw
new
InvalidLinkException("Destination must be non-empty string.");}$current=FALSE;$a=strrpos($destination,':');if($a===FALSE){$action=$destination==='this'?$this->action:$destination;$presenter=$this->getName();$presenterClass=get_class($this);}else{$action=(string)substr($destination,$a+1);if($destination[0]===':'){if($a<2){throw
new
InvalidLinkException("Missing presenter name in '$destination'.");}$presenter=substr($destination,1,$a-1);}else{$presenter=$this->getName();$b=strrpos($presenter,':');if($b===FALSE){$presenter=substr($destination,0,$a);}else{$presenter=substr($presenter,0,$b+1).substr($destination,0,$a);}}$presenterClass=$presenterLoader->getPresenterClass($presenter);}if(isset($signal)){$reflection=new
PresenterComponentReflection(get_class($component));if($signal==='this'){$signal='';if(array_key_exists(0,$args)){throw
new
InvalidLinkException("Unable to pass parameters to 'this!' signal.");}}elseif(strpos($signal,self::NAME_SEPARATOR)===FALSE){$method=$component->formatSignalMethod($signal);if(!$reflection->hasCallableMethod($method)){throw
new
InvalidLinkException("Unknown signal '$signal', missing handler {$reflection->name}::$method()");}if($args){self::argsToParams(get_class($component),$method,$args);}}if($args&&array_intersect_key($args,$reflection->getPersistentParams())){$component->saveState($args);}if($args&&$component!==$this){$prefix=$component->getUniqueId().self::NAME_SEPARATOR;foreach($args
as$key=>$val){unset($args[$key]);$args[$prefix.$key]=$val;}}}if(is_subclass_of($presenterClass,__CLASS__)){if($action===''){$action=$presenterClass::$defaultAction;}$current=($action==='*'||$action===$this->action)&&$presenterClass===get_class($this);$reflection=new
PresenterComponentReflection($presenterClass);if($args||$destination==='this'){$method=$presenterClass::formatActionMethod($action);if(!$reflection->hasCallableMethod($method)){$method=$presenterClass::formatRenderMethod($action);if(!$reflection->hasCallableMethod($method)){$method=NULL;}}if($method===NULL){if(array_key_exists(0,$args)){throw
new
InvalidLinkException("Unable to pass parameters to action '$presenter:$action', missing corresponding method.");}}elseif($destination==='this'){self::argsToParams($presenterClass,$method,$args,$this->params);}else{self::argsToParams($presenterClass,$method,$args);}}if($args&&array_intersect_key($args,$reflection->getPersistentParams())){$this->saveState($args,$reflection);}$globalState=$this->getGlobalState($destination==='this'?NULL:$presenterClass);if($current&&$args){$tmp=$globalState+$this->params;foreach($args
as$key=>$val){if((string)$val!==(isset($tmp[$key])?(string)$tmp[$key]:'')){$current=FALSE;break;}}}$args+=$globalState;}$args[self::ACTION_KEY]=$action;if(!empty($signal)){$args[self::SIGNAL_KEY]=$component->getParamId($signal);$current=$current&&$args[self::SIGNAL_KEY]===$this->getParam(self::SIGNAL_KEY);}if(($mode==='redirect'||$mode==='forward')&&$this->hasFlashSession()){$args[self::FLASH_KEY]=$this->getParam(self::FLASH_KEY);}$this->lastCreatedRequest=new
PresenterRequest($presenter,PresenterRequest::FORWARD,$args,array(),array());$this->lastCreatedRequestFlag=array('current'=>$current);if($mode==='forward')return;$uri=$router->constructUrl($this->lastCreatedRequest,$httpRequest->getUri());if($uri===NULL){unset($args[self::ACTION_KEY]);$params=urldecode(http_build_query($args,NULL,', '));throw
new
InvalidLinkException("No route for $presenter:$action($params)");}if($mode==='link'&&$scheme===FALSE&&!$this->absoluteUrls){$hostUri=$httpRequest->getUri()->getHostUri();if(strncmp($uri,$hostUri,strlen($hostUri))===0){$uri=substr($uri,strlen($hostUri));}}return$uri.$fragment;}private
static
function
argsToParams($class,$method,&$args,$supplemental=array()){static$cache;$params=&$cache[strtolower($class.':'.$method)];if($params===NULL){$params=NetteX\Reflection\MethodReflection::from($class,$method)->getDefaultParameters();}$i=0;foreach($params
as$name=>$def){if(array_key_exists($i,$args)){$args[$name]=$args[$i];unset($args[$i]);$i++;}elseif(array_key_exists($name,$args)){}elseif(array_key_exists($name,$supplemental)){$args[$name]=$supplemental[$name];}else{continue;}if($def===NULL){if((string)$args[$name]==='')$args[$name]=NULL;}else{settype($args[$name],gettype($def));if($args[$name]===$def)$args[$name]=NULL;}}if(array_key_exists($i,$args)){$method=NetteX\Reflection\MethodReflection::from($class,$method)->getName();throw
new
InvalidLinkException("Passed more parameters than method $class::$method() expects.");}}protected
function
handleInvalidLink($e){if(self::$invalidLinkMode===NULL){self::$invalidLinkMode=Environment::isProduction()?self::INVALID_LINK_SILENT:self::INVALID_LINK_WARNING;}if(self::$invalidLinkMode===self::INVALID_LINK_SILENT){return'#';}elseif(self::$invalidLinkMode===self::INVALID_LINK_WARNING){return'error: '.$e->getMessage();}else{throw$e;}}static
function
getPersistentComponents(){return(array)NetteX\Reflection\ClassReflection::from(get_called_class())->getAnnotation('persistent');}private
function
getGlobalState($forClass=NULL){$sinces=&$this->globalStateSinces;if($this->globalState===NULL){$state=array();foreach($this->globalParams
as$id=>$params){$prefix=$id.self::NAME_SEPARATOR;foreach($params
as$key=>$val){$state[$prefix.$key]=$val;}}$this->saveState($state,$forClass?new
PresenterComponentReflection($forClass):NULL);if($sinces===NULL){$sinces=array();foreach($this->getReflection()->getPersistentParams()as$nm=>$meta){$sinces[$nm]=$meta['since'];}}$components=$this->getReflection()->getPersistentComponents();$iterator=$this->getComponents(TRUE,'NetteX\Application\IStatePersistent');foreach($iterator
as$name=>$component){if($iterator->getDepth()===0){$since=isset($components[$name]['since'])?$components[$name]['since']:FALSE;}$prefix=$component->getUniqueId().self::NAME_SEPARATOR;$params=array();$component->saveState($params);foreach($params
as$key=>$val){$state[$prefix.$key]=$val;$sinces[$prefix.$key]=$since;}}}else{$state=$this->globalState;}if($forClass!==NULL){$since=NULL;foreach($state
as$key=>$foo){if(!isset($sinces[$key])){$x=strpos($key,self::NAME_SEPARATOR);$x=$x===FALSE?$key:substr($key,0,$x);$sinces[$key]=isset($sinces[$x])?$sinces[$x]:FALSE;}if($since!==$sinces[$key]){$since=$sinces[$key];$ok=$since&&(is_subclass_of($forClass,$since)||$forClass===$since);}if(!$ok){unset($state[$key]);}}}return$state;}protected
function
saveGlobalState(){foreach($this->globalParams
as$id=>$foo){$this->getComponent($id,FALSE);}$this->globalParams=array();$this->globalState=$this->getGlobalState();}private
function
initGlobalParams(){$this->globalParams=array();$selfParams=array();$params=$this->request->getParams();if($this->isAjax()){$params=$this->request->getPost()+$params;}foreach($params
as$key=>$value){$a=strlen($key)>2?strrpos($key,self::NAME_SEPARATOR,-2):FALSE;if($a===FALSE){$selfParams[$key]=$value;}else{$this->globalParams[substr($key,0,$a)][substr($key,$a+1)]=$value;}}$this->changeAction(isset($selfParams[self::ACTION_KEY])?$selfParams[self::ACTION_KEY]:self::$defaultAction);$this->signalReceiver=$this->getUniqueId();if(!empty($selfParams[self::SIGNAL_KEY])){$param=$selfParams[self::SIGNAL_KEY];$pos=strrpos($param,'-');if($pos){$this->signalReceiver=substr($param,0,$pos);$this->signal=substr($param,$pos+1);}else{$this->signalReceiver=$this->getUniqueId();$this->signal=$param;}if($this->signal==NULL){$this->signal=NULL;}}$this->loadState($selfParams);}final
function
popGlobalParams($id){if(isset($this->globalParams[$id])){$res=$this->globalParams[$id];unset($this->globalParams[$id]);return$res;}else{return
array();}}function
hasFlashSession(){return!empty($this->params[self::FLASH_KEY])&&$this->getSession()->hasNamespace('NetteX.Application.Flash/'.$this->params[self::FLASH_KEY]);}function
getFlashSession(){if(empty($this->params[self::FLASH_KEY])){$this->params[self::FLASH_KEY]=substr(md5(lcg_value()),0,4);}return$this->getSession('NetteX.Application.Flash/'.$this->params[self::FLASH_KEY]);}protected
function
getHttpRequest(){return
Environment::getHttpRequest();}protected
function
getHttpResponse(){return
Environment::getHttpResponse();}protected
function
getHttpContext(){return
Environment::getHttpContext();}function
getApplication(){return
Environment::getApplication();}function
getSession($namespace=NULL){return
Environment::getSession($namespace);}function
getUser(){return
Environment::getUser();}}}namespace NetteX\Reflection{use
NetteX;use
NetteX\ObjectMixin;class
ClassReflection
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
getConstructor(){return($ref=parent::getConstructor())?MethodReflection::import($ref):NULL;}function
getExtension(){return($name=$this->getExtensionName())?new
ExtensionReflection($name):NULL;}function
getInterfaces(){$res=array();foreach(parent::getInterfaceNames()as$val){$res[$val]=new
static($val);}return$res;}function
getMethod($name){return
new
MethodReflection($this->getName(),$name);}function
getMethods($filter=-1){foreach($res=parent::getMethods($filter)as$key=>$val){$res[$key]=new
MethodReflection($this->getName(),$val->getName());}return$res;}function
getParentClass(){return($ref=parent::getParentClass())?new
static($ref->getName()):NULL;}function
getProperties($filter=-1){foreach($res=parent::getProperties($filter)as$key=>$val){$res[$key]=new
PropertyReflection($this->getName(),$val->getName());}return$res;}function
getProperty($name){return
new
PropertyReflection($this->getName(),$name);}function
hasAnnotation($name){$res=AnnotationsParser::getAll($this);return!empty($res[$name]);}function
getAnnotation($name){$res=AnnotationsParser::getAll($this);return
isset($res[$name])?end($res[$name]):NULL;}function
getAnnotations(){return
AnnotationsParser::getAll($this);}static
function
getReflection(){return
new
NetteX\Reflection\ClassReflection(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){throw
new\XMemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");}}}namespace NetteX\Application{use
NetteX;class
PresenterComponentReflection
extends
NetteX\Reflection\ClassReflection{private
static$ppCache=array();private
static$pcCache=array();private
static$mcCache=array();function
getPersistentParams($class=NULL){$class=$class===NULL?$this->getName():$class;$params=&self::$ppCache[$class];if($params!==NULL)return$params;$params=array();if(is_subclass_of($class,'NetteX\Application\PresenterComponent')){$defaults=get_class_vars($class);foreach(call_user_func(array($class,'getPersistentParams'),$class)as$name=>$meta){if(is_string($meta))$name=$meta;$params[$name]=array('def'=>$defaults[$name],'since'=>$class);}$params=$this->getPersistentParams(get_parent_class($class))+$params;}return$params;}function
getPersistentComponents(){$class=$this->getName();$components=&self::$pcCache[$class];if($components!==NULL)return$components;$components=array();if(is_subclass_of($class,'NetteX\Application\Presenter')){foreach(call_user_func(array($class,'getPersistentComponents'),$class)as$name=>$meta){if(is_string($meta))$name=$meta;$components[$name]=array('since'=>$class);}$components=self::getPersistentComponents(get_parent_class($class))+$components;}return$components;}function
hasCallableMethod($method){$class=$this->getName();$cache=&self::$mcCache[strtolower($class.':'.$method)];if($cache===NULL)try{$cache=FALSE;$rm=NetteX\Reflection\MethodReflection::from($class,$method);$cache=$this->isInstantiable()&&$rm->isPublic()&&!$rm->isAbstract()&&!$rm->isStatic();}catch(\ReflectionException$e){}return$cache;}}class
PresenterLoader
implements
IPresenterLoader{public$caseSensitive=FALSE;private$baseDir;private$cache=array();function
__construct($baseDir){$this->baseDir=$baseDir;}function
getPresenterClass(&$name){if(isset($this->cache[$name])){list($class,$name)=$this->cache[$name];return$class;}if(!is_string($name)||!NetteX\String::match($name,"#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*$#")){throw
new
InvalidPresenterException("Presenter name must be alphanumeric string, '$name' is invalid.");}$class=$this->formatPresenterClass($name);if(!class_exists($class)){$file=$this->formatPresenterFile($name);if(is_file($file)&&is_readable($file)){NetteX\Loaders\LimitedScope::load($file);}if(!class_exists($class)){throw
new
InvalidPresenterException("Cannot load presenter '$name', class '$class' was not found in '$file'.");}}$reflection=new
NetteX\Reflection\ClassReflection($class);$class=$reflection->getName();if(!$reflection->implementsInterface('NetteX\Application\IPresenter')){throw
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
new\XInvalidStateException("Cannot modify a frozen object {$this->reflection->name}.");}}}}namespace NetteX\Application{use
NetteX;final
class
PresenterRequest
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
hasFlag($flag){return!empty($this->flags[$flag]);}}class
DownloadResponse
extends
NetteX\Object
implements
IPresenterResponse{private$file;private$contentType;private$name;function
__construct($file,$name=NULL,$contentType=NULL){if(!is_file($file)){throw
new
BadRequestException("File '$file' doesn't exist.");}$this->file=$file;$this->name=$name?$name:basename($file);$this->contentType=$contentType?$contentType:'application/octet-stream';}final
function
getFile(){return$this->file;}final
function
getName(){return$this->name;}final
function
getContentType(){return$this->contentType;}function
send(){NetteX\Environment::getHttpResponse()->setContentType($this->contentType);NetteX\Environment::getHttpResponse()->setHeader('Content-Disposition','attachment; filename="'.$this->name.'"');readfile($this->file);}}class
ForwardingResponse
extends
NetteX\Object
implements
IPresenterResponse{private$request;function
__construct(PresenterRequest$request){$this->request=$request;}final
function
getRequest(){return$this->request;}function
send(){}}class
JsonResponse
extends
NetteX\Object
implements
IPresenterResponse{private$payload;private$contentType;function
__construct($payload,$contentType=NULL){if(!is_array($payload)&&!($payload
instanceof\stdClass)){throw
new\InvalidArgumentException("Payload must be array or anonymous class, ".gettype($payload)." given.");}$this->payload=$payload;$this->contentType=$contentType?$contentType:'application/json';}final
function
getPayload(){return$this->payload;}function
send(){NetteX\Environment::getHttpResponse()->setContentType($this->contentType);NetteX\Environment::getHttpResponse()->setExpiration(FALSE);echo
NetteX\Json::encode($this->payload);}}class
RedirectingResponse
extends
NetteX\Object
implements
IPresenterResponse{private$uri;private$code;function
__construct($uri,$code=NetteX\Web\IHttpResponse::S302_FOUND){$this->uri=(string)$uri;$this->code=(int)$code;}final
function
getUri(){return$this->uri;}final
function
getCode(){return$this->code;}function
send(){NetteX\Environment::getHttpResponse()->redirect($this->uri,$this->code);}}class
RenderResponse
extends
NetteX\Object
implements
IPresenterResponse{private$source;function
__construct($source){$this->source=$source;}final
function
getSource(){return$this->source;}function
send(){if($this->source
instanceof
NetteX\Templates\ITemplate){$this->source->render();}else{echo$this->source;}}}class
CliRouter
extends
NetteX\Object
implements
IRouter{const
PRESENTER_KEY='action';private$defaults;function
__construct($defaults=array()){$this->defaults=$defaults;}function
match(NetteX\Web\IHttpRequest$httpRequest){if(empty($_SERVER['argv'])||!is_array($_SERVER['argv'])){return
NULL;}$names=array(self::PRESENTER_KEY);$params=$this->defaults;$args=$_SERVER['argv'];array_shift($args);$args[]='--';foreach($args
as$arg){$opt=preg_replace('#/|-+#A','',$arg);if($opt===$arg){if(isset($flag)||$flag=array_shift($names)){$params[$flag]=$arg;}else{$params[]=$arg;}$flag=NULL;continue;}if(isset($flag)){$params[$flag]=TRUE;$flag=NULL;}if($opt!==''){$pair=explode('=',$opt,2);if(isset($pair[1])){$params[$pair[0]]=$pair[1];}else{$flag=$pair[0];}}}if(!isset($params[self::PRESENTER_KEY])){throw
new\XInvalidStateException('Missing presenter & action in route definition.');}$presenter=$params[self::PRESENTER_KEY];if($a=strrpos($presenter,':')){$params[self::PRESENTER_KEY]=substr($presenter,$a+1);$presenter=substr($presenter,0,$a);}return
new
PresenterRequest($presenter,'CLI',$params);}function
constructUrl(PresenterRequest$appRequest,NetteX\Web\Uri$refUri){return
NULL;}function
getDefaults(){return$this->defaults;}}}namespace NetteX{use
NetteX;class
ArrayList
implements\ArrayAccess,\Countable,\IteratorAggregate{private$list=array();function
getIterator(){return
new\ArrayIterator($this->list);}function
count(){return
count($this->list);}function
offsetSet($index,$value){if($index===NULL){$this->list[]=$value;}elseif($index<0||$index>=count($this->list)){throw
new\OutOfRangeException("Offset invalid or out of range");}else{$this->list[(int)$index]=$value;}}function
offsetGet($index){if($index<0||$index>=count($this->list)){throw
new\OutOfRangeException("Offset invalid or out of range");}return$this->list[(int)$index];}function
offsetExists($index){return$index>=0&&$index<count($this->list);}function
offsetUnset($index){if($index<0||$index>=count($this->list)){throw
new\OutOfRangeException("Offset invalid or out of range");}array_splice($this->list,(int)$index,1);}}}namespace NetteX\Application{use
NetteX;class
MultiRouter
extends
NetteX\ArrayList
implements
IRouter{private$cachedRoutes;function
match(NetteX\Web\IHttpRequest$httpRequest){foreach($this
as$route){$appRequest=$route->match($httpRequest);if($appRequest!==NULL){return$appRequest;}}return
NULL;}function
constructUrl(PresenterRequest$appRequest,NetteX\Web\Uri$refUri){if($this->cachedRoutes===NULL){$routes=array();$routes['*']=array();foreach($this
as$route){$presenter=$route
instanceof
Route?$route->getTargetPresenter():NULL;if($presenter===FALSE)continue;if(is_string($presenter)){$presenter=strtolower($presenter);if(!isset($routes[$presenter])){$routes[$presenter]=$routes['*'];}$routes[$presenter][]=$route;}else{foreach($routes
as$id=>$foo){$routes[$id][]=$route;}}}$this->cachedRoutes=$routes;}$presenter=strtolower($appRequest->getPresenterName());if(!isset($this->cachedRoutes[$presenter]))$presenter='*';foreach($this->cachedRoutes[$presenter]as$route){$uri=$route->constructUrl($appRequest,$refUri);if($uri!==NULL){return$uri;}}return
NULL;}function
offsetSet($index,$route){if(!($route
instanceof
IRouter)){throw
new\InvalidArgumentException("Argument must be IRouter descendant.");}parent::offsetSet($index,$route);}}use
NetteX\String;class
Route
extends
NetteX\Object
implements
IRouter{const
PRESENTER_KEY='presenter';const
MODULE_KEY='module';const
CASE_SENSITIVE=256;const
HOST=1;const
PATH=2;const
RELATIVE=3;const
VALUE='value';const
PATTERN='pattern';const
FILTER_IN='filterIn';const
FILTER_OUT='filterOut';const
FILTER_TABLE='filterTable';const
OPTIONAL=0;const
PATH_OPTIONAL=1;const
CONSTANT=2;public
static$defaultFlags=0;public
static$styles=array('#'=>array(self::PATTERN=>'[^/]+',self::FILTER_IN=>'rawurldecode',self::FILTER_OUT=>'rawurlencode'),'?#'=>array(),'module'=>array(self::PATTERN=>'[a-z][a-z0-9.-]*',self::FILTER_IN=>array(__CLASS__,'path2presenter'),self::FILTER_OUT=>array(__CLASS__,'presenter2path')),'presenter'=>array(self::PATTERN=>'[a-z][a-z0-9.-]*',self::FILTER_IN=>array(__CLASS__,'path2presenter'),self::FILTER_OUT=>array(__CLASS__,'presenter2path')),'action'=>array(self::PATTERN=>'[a-z][a-z0-9-]*',self::FILTER_IN=>array(__CLASS__,'path2action'),self::FILTER_OUT=>array(__CLASS__,'action2path')),'?module'=>array(),'?presenter'=>array(),'?action'=>array());private$mask;private$sequence;private$re;private$metadata=array();private$xlat;private$type;private$flags;function
__construct($mask,array$metadata=array(),$flags=0){$this->flags=$flags|self::$defaultFlags;$this->setMask($mask,$metadata);}function
match(NetteX\Web\IHttpRequest$httpRequest){$uri=$httpRequest->getUri();if($this->type===self::HOST){$path='//'.$uri->getHost().$uri->getPath();}elseif($this->type===self::RELATIVE){$basePath=$uri->getBasePath();if(strncmp($uri->getPath(),$basePath,strlen($basePath))!==0){return
NULL;}$path=(string)substr($uri->getPath(),strlen($basePath));}else{$path=$uri->getPath();}if($path!==''){$path=rtrim($path,'/').'/';}if(!$matches=String::match($path,$this->re)){return
NULL;}$params=array();foreach($matches
as$k=>$v){if(is_string($k)&&$v!==''){$params[str_replace('___','-',$k)]=$v;}}foreach($this->metadata
as$name=>$meta){if(isset($params[$name])){}elseif(isset($meta['fixity'])&&$meta['fixity']!==self::OPTIONAL){$params[$name]=NULL;}}if($this->xlat){$params+=self::renameKeys($httpRequest->getQuery(),array_flip($this->xlat));}else{$params+=$httpRequest->getQuery();}foreach($this->metadata
as$name=>$meta){if(isset($params[$name])){if(!is_scalar($params[$name])){}elseif(isset($meta[self::FILTER_TABLE][$params[$name]])){$params[$name]=$meta[self::FILTER_TABLE][$params[$name]];}elseif(isset($meta[self::FILTER_IN])){$params[$name]=call_user_func($meta[self::FILTER_IN],(string)$params[$name]);if($params[$name]===NULL&&!isset($meta['fixity'])){return
NULL;}}}elseif(isset($meta['fixity'])){$params[$name]=$meta[self::VALUE];}}if(!isset($params[self::PRESENTER_KEY])){throw
new\XInvalidStateException('Missing presenter in route definition.');}if(isset($this->metadata[self::MODULE_KEY])){if(!isset($params[self::MODULE_KEY])){throw
new\XInvalidStateException('Missing module in route definition.');}$presenter=$params[self::MODULE_KEY].':'.$params[self::PRESENTER_KEY];unset($params[self::MODULE_KEY],$params[self::PRESENTER_KEY]);}else{$presenter=$params[self::PRESENTER_KEY];unset($params[self::PRESENTER_KEY]);}return
new
PresenterRequest($presenter,$httpRequest->getMethod(),$params,$httpRequest->getPost(),$httpRequest->getFiles(),array(PresenterRequest::SECURED=>$httpRequest->isSecured()));}function
constructUrl(PresenterRequest$appRequest,NetteX\Web\Uri$refUri){if($this->flags&self::ONE_WAY){return
NULL;}$params=$appRequest->getParams();$metadata=$this->metadata;$presenter=$appRequest->getPresenterName();$params[self::PRESENTER_KEY]=$presenter;if(isset($metadata[self::MODULE_KEY])){$module=$metadata[self::MODULE_KEY];if(isset($module['fixity'])&&strncasecmp($presenter,$module[self::VALUE].':',strlen($module[self::VALUE])+1)===0){$a=strlen($module[self::VALUE]);}else{$a=strrpos($presenter,':');}if($a===FALSE){$params[self::MODULE_KEY]='';}else{$params[self::MODULE_KEY]=substr($presenter,0,$a);$params[self::PRESENTER_KEY]=substr($presenter,$a+1);}}foreach($metadata
as$name=>$meta){if(!isset($params[$name]))continue;if(isset($meta['fixity'])){if(is_scalar($params[$name])&&strcasecmp($params[$name],$meta[self::VALUE])===0){unset($params[$name]);continue;}elseif($meta['fixity']===self::CONSTANT){return
NULL;}}if(!is_scalar($params[$name])){}elseif(isset($meta['filterTable2'][$params[$name]])){$params[$name]=$meta['filterTable2'][$params[$name]];}elseif(isset($meta[self::FILTER_OUT])){$params[$name]=call_user_func($meta[self::FILTER_OUT],$params[$name]);}if(isset($meta[self::PATTERN])&&!preg_match($meta[self::PATTERN],rawurldecode($params[$name]))){return
NULL;}}$sequence=$this->sequence;$brackets=array();$required=0;$uri='';$i=count($sequence)-1;do{$uri=$sequence[$i].$uri;if($i===0)break;$i--;$name=$sequence[$i];$i--;if($name===']'){$brackets[]=$uri;}elseif($name[0]==='['){$tmp=array_pop($brackets);if($required<count($brackets)+1){if($name!=='[!'){$uri=$tmp;}}else{$required=count($brackets);}}elseif($name[0]==='?'){continue;}elseif(isset($params[$name])&&$params[$name]!=''){$required=count($brackets);$uri=$params[$name].$uri;unset($params[$name]);}elseif(isset($metadata[$name]['fixity'])){$uri=$metadata[$name]['defOut'].$uri;}else{return
NULL;}}while(TRUE);if($this->xlat){$params=self::renameKeys($params,$this->xlat);}$sep=ini_get('arg_separator.input');$query=http_build_query($params,'',$sep?$sep[0]:'&');if($query!='')$uri.='?'.$query;if($this->type===self::RELATIVE){$uri='//'.$refUri->getAuthority().$refUri->getBasePath().$uri;}elseif($this->type===self::PATH){$uri='//'.$refUri->getAuthority().$uri;}if(strpos($uri,'//',2)!==FALSE){return
NULL;}$uri=($this->flags&self::SECURED?'https:':'http:').$uri;return$uri;}private
function
setMask($mask,array$metadata){$this->mask=$mask;if(substr($mask,0,2)==='//'){$this->type=self::HOST;}elseif(substr($mask,0,1)==='/'){$this->type=self::PATH;}else{$this->type=self::RELATIVE;}foreach($metadata
as$name=>$meta){if(!is_array($meta)){$metadata[$name]=array(self::VALUE=>$meta,'fixity'=>self::CONSTANT);}elseif(array_key_exists(self::VALUE,$meta)){$metadata[$name]['fixity']=self::CONSTANT;}}$parts=String::split($mask,'/<([^># ]+) *([^>#]*)(#?[^>\[\]]*)>|(\[!?|\]|\s*\?.*)/');$this->xlat=array();$i=count($parts)-1;if(isset($parts[$i-1])&&substr(ltrim($parts[$i-1]),0,1)==='?'){$matches=String::matchAll($parts[$i-1],'/(?:([a-zA-Z0-9_.-]+)=)?<([^># ]+) *([^>#]*)(#?[^>]*)>/');foreach($matches
as$match){list(,$param,$name,$pattern,$class)=$match;if($class!==''){if(!isset(self::$styles[$class])){throw
new\XInvalidStateException("Parameter '$name' has '$class' flag, but Route::\$styles['$class'] is not set.");}$meta=self::$styles[$class];}elseif(isset(self::$styles['?'.$name])){$meta=self::$styles['?'.$name];}else{$meta=self::$styles['?#'];}if(isset($metadata[$name])){$meta=$metadata[$name]+$meta;}if(array_key_exists(self::VALUE,$meta)){$meta['fixity']=self::OPTIONAL;}unset($meta['pattern']);$meta['filterTable2']=empty($meta[self::FILTER_TABLE])?NULL:array_flip($meta[self::FILTER_TABLE]);$metadata[$name]=$meta;if($param!==''){$this->xlat[$name]=$param;}}$i-=5;}$brackets=0;$re='';$sequence=array();$autoOptional=array(0,0);do{array_unshift($sequence,$parts[$i]);$re=preg_quote($parts[$i],'#').$re;if($i===0)break;$i--;$part=$parts[$i];if($part==='['||$part===']'||$part==='[!'){$brackets+=$part[0]==='['?-1:1;if($brackets<0){throw
new\InvalidArgumentException("Unexpected '$part' in mask '$mask'.");}array_unshift($sequence,$part);$re=($part[0]==='['?'(?:':')?').$re;$i-=4;continue;}$class=$parts[$i];$i--;$pattern=trim($parts[$i]);$i--;$name=$parts[$i];$i--;array_unshift($sequence,$name);if($name[0]==='?'){$re='(?:'.preg_quote(substr($name,1),'#').'|'.$pattern.')'.$re;$sequence[1]=substr($name,1).$sequence[1];continue;}if(preg_match('#[^a-z0-9_-]#i',$name)){throw
new\InvalidArgumentException("Parameter name must be alphanumeric string due to limitations of PCRE, '$name' given.");}if($class!==''){if(!isset(self::$styles[$class])){throw
new\XInvalidStateException("Parameter '$name' has '$class' flag, but Route::\$styles['$class'] is not set.");}$meta=self::$styles[$class];}elseif(isset(self::$styles[$name])){$meta=self::$styles[$name];}else{$meta=self::$styles['#'];}if(isset($metadata[$name])){$meta=$metadata[$name]+$meta;}if($pattern==''&&isset($meta[self::PATTERN])){$pattern=$meta[self::PATTERN];}$meta['filterTable2']=empty($meta[self::FILTER_TABLE])?NULL:array_flip($meta[self::FILTER_TABLE]);if(array_key_exists(self::VALUE,$meta)){if(isset($meta['filterTable2'][$meta[self::VALUE]])){$meta['defOut']=$meta['filterTable2'][$meta[self::VALUE]];}elseif(isset($meta[self::FILTER_OUT])){$meta['defOut']=call_user_func($meta[self::FILTER_OUT],$meta[self::VALUE]);}else{$meta['defOut']=$meta[self::VALUE];}}$meta[self::PATTERN]="#(?:$pattern)$#A".($this->flags&self::CASE_SENSITIVE?'':'iu');$re='(?P<'.str_replace('-','___',$name).'>'.$pattern.')'.$re;if($brackets){if(!isset($meta[self::VALUE])){$meta[self::VALUE]=$meta['defOut']=NULL;}$meta['fixity']=self::PATH_OPTIONAL;}elseif(isset($meta['fixity'])){$re='(?:'.substr_replace($re,')?',strlen($re)-$autoOptional[0],0);array_splice($sequence,count($sequence)-$autoOptional[1],0,array(']',''));array_unshift($sequence,'[','');$meta['fixity']=self::PATH_OPTIONAL;}else{$autoOptional=array(strlen($re),count($sequence));}$metadata[$name]=$meta;}while(TRUE);if($brackets){throw
new\InvalidArgumentException("Missing closing ']' in mask '$mask'.");}$this->re='#'.$re.'/?$#A'.($this->flags&self::CASE_SENSITIVE?'':'iu');$this->metadata=$metadata;$this->sequence=$sequence;}function
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
new\InvalidArgumentException("Style '$style' already exists.");}if($parent!==NULL){if(!isset(self::$styles[$parent])){throw
new\InvalidArgumentException("Parent style '$parent' doesn't exist.");}self::$styles[$style]=self::$styles[$parent];}else{self::$styles[$style]=array();}}static
function
setStyleProperty($style,$key,$value){if(!isset(self::$styles[$style])){throw
new\InvalidArgumentException("Style '$style' doesn't exist.");}self::$styles[$style][$key]=$value;}}class
SimpleRouter
extends
NetteX\Object
implements
IRouter{const
PRESENTER_KEY='presenter';const
MODULE_KEY='module';private$module='';private$defaults;private$flags;function
__construct($defaults=array(),$flags=0){if(is_string($defaults)){$a=strrpos($defaults,':');$defaults=array(self::PRESENTER_KEY=>substr($defaults,0,$a),'action'=>substr($defaults,$a+1));}if(isset($defaults[self::MODULE_KEY])){$this->module=$defaults[self::MODULE_KEY].':';unset($defaults[self::MODULE_KEY]);}$this->defaults=$defaults;$this->flags=$flags;}function
match(NetteX\Web\IHttpRequest$httpRequest){if($httpRequest->getUri()->getPathInfo()!==''){return
NULL;}$params=$httpRequest->getQuery();$params+=$this->defaults;if(!isset($params[self::PRESENTER_KEY])){throw
new\XInvalidStateException('Missing presenter.');}$presenter=$this->module.$params[self::PRESENTER_KEY];unset($params[self::PRESENTER_KEY]);return
new
PresenterRequest($presenter,$httpRequest->getMethod(),$params,$httpRequest->getPost(),$httpRequest->getFiles(),array(PresenterRequest::SECURED=>$httpRequest->isSecured()));}function
constructUrl(PresenterRequest$appRequest,NetteX\Web\Uri$refUri){$params=$appRequest->getParams();$presenter=$appRequest->getPresenterName();if(strncasecmp($presenter,$this->module,strlen($this->module))===0){$params[self::PRESENTER_KEY]=substr($presenter,strlen($this->module));}else{return
NULL;}foreach($this->defaults
as$key=>$value){if(isset($params[$key])&&$params[$key]==$value){unset($params[$key]);}}$uri=($this->flags&self::SECURED?'https://':'http://').$refUri->getAuthority().$refUri->getPath();$sep=ini_get('arg_separator.input');$query=http_build_query($params,'',$sep?$sep[0]:'&');if($query!=''){$uri.='?'.$query;}return$uri;}function
getDefaults(){return$this->defaults;}}}namespace NetteX{class
DebugPanel
extends
Object
implements
IDebugPanel{private$id;private$tabCb;private$panelCb;function
__construct($id,$tabCb,$panelCb){$this->id=$id;$this->tabCb=$tabCb;$this->panelCb=$panelCb;}function
getId(){return$this->id;}function
getTab(){ob_start();call_user_func($this->tabCb,$this->id);return
ob_get_clean();}function
getPanel(){ob_start();call_user_func($this->panelCb,$this->id);return
ob_get_clean();}}}namespace NetteX\Application{use
NetteX;class
RoutingDebugger
extends
NetteX\DebugPanel{private$router;private$httpRequest;private$routers;private$request;function
__construct(IRouter$router,NetteX\Web\IHttpRequest$httpRequest){$this->router=$router;$this->httpRequest=$httpRequest;$this->routers=new\ArrayObject;parent::__construct('RoutingDebugger',array($this,'renderTab'),array($this,'renderPanel'));}function
renderTab(){$this->analyse($this->router);?>
<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJHSURBVDjLlZPNi81hFMc/z7137p1mTCFvNZfGSzLIWNjZKRvFRoqNhRCSYm8xS3+AxRRZ2JAFJWJHSQqTQkbEzYwIM+6Yid/znJfH4prLXShOnb6r8/nWOd8Tcs78bz0/f+KMu50y05nK/wy+uHDylbutqS5extvGcxaWqtoGDA8PZ3dnrs2srQc2Zko41UXLmLdyDW5OfvsUkUgbYGbU63UAQggdmvMzFmzZCgTi7CQmkZwdEaX0JwDgTnGbTCaE0G4zw80omhPI92lcEtkNkdgJCCHwJX7mZvNaB0A14SaYJlwTrpHsTkoFlV1nt2c3x5YYo1/vM9A/gKpxdfwyu/v3teCayKq4JEwT5EB2R6WgYmrs2bYbcUNNUVfEhIfFYy69uci+1fuRX84mkawFSxd/4nVWUopUVIykwlQxRTJBTIDA4Pp1jBZPuNW4wUAPmCqWIn29X1k4f5Ku8g9mpKCkakRLVEs1auVuauVuyqHMo8ejNCe+sWPVTkQKXCMmkeZUmUZjETF1tc6ooly+fgUVw9So1/tRN6YnZji46QghBFKKuAouERNhMlbAHZFE6e7pB+He8MMw+GGI4xtOMf1+lsl3TQ4NHf19BSlaO1DB9BfMHdX0O0iqSgiBbJkjm491hClJbA1LxCURgpPzXwAHhg63necAIi3XngXLcRU0fof8ETMljIyM5LGxMcbHxzvy/6fuXdWgt6+PWncv1e4euqo1ZmabvHs5+jn8yzufO7hiiZmuNpNBM13rbvVSpbrXJE7/BMkHtU9jFIC/AAAAAElFTkSuQmCC"
/><?php if(empty($this->request)):?>no route<?php else:echo$this->request->getPresenterName().':'.(isset($this->request->params[Presenter::ACTION_KEY])?$this->request->params[Presenter::ACTION_KEY]:Presenter::$defaultAction);endif?>
<?php }function
renderPanel(){?>
<style>#nette-debug-RoutingDebugger table{font:9pt/1.5 Consolas,monospace}#nette-debug-RoutingDebugger .yes td{color:green}#nette-debug-RoutingDebugger .may td{color:#67F}#nette-debug-RoutingDebugger pre,#nette-debug-RoutingDebugger code{display:inline}</style>

<h1>
<?php if(empty($this->request)):?>
	no route
<?php else:?>
	<?php echo$this->request->getPresenterName().':'.(isset($this->request->params[Presenter::ACTION_KEY])?$this->request->params[Presenter::ACTION_KEY]:Presenter::$defaultAction)?>
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
		<?php unset($params[Presenter::ACTION_KEY])?>
		<?php foreach($params
as$key=>$value):?>
		<tr>
			<td><code><?php echo
htmlSpecialChars($key)?></code></td>
			<td><?php if(is_string($value)):?><code><?php echo
htmlSpecialChars($value)?></code><?php else:echo
NetteX\Debug::dump($value,TRUE);endif?></td>
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

		<td><code><?php echo
htmlSpecialChars($router['class'])?></code></td>

		<td><code><strong><?php echo
htmlSpecialChars($router['mask'])?></strong></code></td>

		<td><code>
		<?php foreach($router['defaults']as$key=>$value):?>
			<?php echo
htmlSpecialChars($key),"&nbsp;=&nbsp;",is_string($value)?htmlSpecialChars($value):str_replace("\n</pre",'</pre',NetteX\Debug::dump($value,TRUE))?><br />
		<?php endforeach?>
		</code></td>

		<td><?php if($router['request']):?><code>
		<?php $params=$router['request']->getParams();?>
		<strong><?php echo
htmlSpecialChars($router['request']->getPresenterName().':'.(isset($params[Presenter::ACTION_KEY])?$params[Presenter::ACTION_KEY]:Presenter::$defaultAction))?></strong><br />
		<?php unset($params[Presenter::ACTION_KEY])?>
		<?php foreach($params
as$key=>$value):?>
			<?php echo
htmlSpecialChars($key),"&nbsp;=&nbsp;",is_string($value)?htmlSpecialChars($value):str_replace("\n</pre",'</pre',NetteX\Debug::dump($value,TRUE))?><br />
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
MultiRouter){foreach($router
as$subRouter){$this->analyse($subRouter);}return;}$request=$router->match($this->httpRequest);$matched=$request===NULL?'no':'may';if($request!==NULL&&empty($this->request)){$this->request=$request;$matched='yes';}$this->routers[]=array('matched'=>$matched,'class'=>get_class($router),'defaults'=>$router
instanceof
Route||$router
instanceof
SimpleRouter?$router->getDefaults():array(),'mask'=>$router
instanceof
Route?$router->getMask():NULL,'request'=>$request);}}}namespace NetteX\Caching{use
NetteX;class
Cache
extends
NetteX\Object
implements\ArrayAccess{const
PRIORITY='priority';const
EXPIRATION='expire';const
EXPIRE='expire';const
SLIDING='sliding';const
TAGS='tags';const
FILES='files';const
ITEMS='items';const
CONSTS='consts';const
CALLBACKS='callbacks';const
ALL='all';const
NAMESPACE_SEPARATOR="\x00";private$storage;private$namespace;private$key;private$data;function
__construct(ICacheStorage$storage,$namespace=NULL){$this->storage=$storage;$this->namespace=(string)$namespace;if(strpos($this->namespace,self::NAMESPACE_SEPARATOR)!==FALSE){throw
new\InvalidArgumentException("Namespace name contains forbidden NUL character.");}}function
getStorage(){return$this->storage;}function
getNamespace(){return$this->namespace;}function
release(){$this->key=$this->data=NULL;}function
save($key,$data,array$dp=NULL){if(!is_string($key)&&!is_int($key)){throw
new\InvalidArgumentException("Cache key name must be string or integer, ".gettype($key)." given.");}$this->key=(string)$key;$key=$this->namespace.self::NAMESPACE_SEPARATOR.$key;if(isset($dp[Cache::EXPIRATION])){$dp[Cache::EXPIRATION]=NetteX\Tools::createDateTime($dp[Cache::EXPIRATION])->format('U')-time();}if(isset($dp[self::FILES])){foreach((array)$dp[self::FILES]as$item){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkFile'),$item,@filemtime($item));}unset($dp[self::FILES]);}if(isset($dp[self::ITEMS])){$dp[self::ITEMS]=(array)$dp[self::ITEMS];foreach($dp[self::ITEMS]as$k=>$item){$dp[self::ITEMS][$k]=$this->namespace.self::NAMESPACE_SEPARATOR.$item;}}if(isset($dp[self::CONSTS])){foreach((array)$dp[self::CONSTS]as$item){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkConst'),$item,constant($item));}unset($dp[self::CONSTS]);}if($data
instanceof
NetteX\Callback||$data
instanceof\Closure){NetteX\Tools::enterCriticalSection();$data=$data->__invoke();NetteX\Tools::leaveCriticalSection();}if(is_object($data)){$dp[self::CALLBACKS][]=array(array(__CLASS__,'checkSerializationVersion'),get_class($data),NetteX\Reflection\ClassReflection::from($data)->getAnnotation('serializationVersion'));}$this->data=$data;if($data===NULL){$this->storage->remove($key);}else{$this->storage->write($key,$data,(array)$dp);}return$data;}function
clean(array$conds=NULL){$this->release();$this->storage->clean((array)$conds);}function
offsetSet($key,$data){$this->save($key,$data);}function
offsetGet($key){if(!is_string($key)&&!is_int($key)){throw
new\InvalidArgumentException("Cache key name must be string or integer, ".gettype($key)." given.");}$key=(string)$key;if($this->key===$key){return$this->data;}$this->key=$key;$this->data=$this->storage->read($this->namespace.self::NAMESPACE_SEPARATOR.$key);return$this->data;}function
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
NetteX\Reflection\ClassReflection::from($class)->getAnnotation('serializationVersion')===$value;}}class
DummyStorage
extends
NetteX\Object
implements
ICacheStorage{function
read($key){}function
write($key,$data,array$dp){}function
remove($key){}function
clean(array$conds){}}class
FileJournal
extends
NetteX\Object
implements
ICacheJournal{const
MAGIC=0x666a3030,FILE='fj',EXTNEW='.new',EXTLOG='.log',EXTLOGNEW='.log.new',LOGMAXSIZE=65536,INT32=4,TAGS=0x74616773,PRIORITY=0x7072696f,ENTRIES=0x656e7473,DELETE='d',ADD='a',CLEAN='c';private
static$ops=array(self::ADD=>self::DELETE,self::DELETE=>self::ADD);private$file;private$handle;private$mtime=0;private$sections=array();private$logHandle;private$isLogNew=FALSE;private$logMerge=array();private$logMergeP=0;function
__construct($dir){$this->file=$dir.'/'.self::FILE;$this->open();}function
__destruct(){if($this->handle){fclose($this->handle);}if($this->logHandle){fclose($this->logHandle);}}private
function
reload(){if(($mtime=@filemtime($this->file))===FALSE){$mtime=0;}if($this->mtime<$mtime){fclose($this->handle);fclose($this->logHandle);$this->handle=$this->logHandle=NULL;$this->open();}$this->logMerge=$this->mergeLogFile($this->logHandle,$this->logMergeP,$this->logMerge);}private
function
open(){$this->handle=$this->logHandle=NULL;$this->mtime=$this->logMergeP=0;$this->sections=$this->logMerge=array();clearstatcache();if(($this->mtime=@filemtime($this->file))===FALSE){$this->mtime=0;}$tries=3;do{if(!$tries--){throw
new\XInvalidStateException("Cannot open journal file '$this->file'.");}if(!($this->handle=@fopen($this->file,'rb'))){$this->handle=NULL;}else{list(,$magic,$sectionCount)=unpack('N2',fread($this->handle,2*self::INT32));if($magic!==self::MAGIC){fclose($this->handle);throw
new\XInvalidStateException("Malformed journal file '$this->file'.");}for($i=0;$i<$sectionCount;++$i){list(,$name,$offset,$keyLength,$keyCount)=unpack('N4',fread($this->handle,4*self::INT32));$this->sections[$name]=(object)array('offset'=>$offset,'keyLength'=>$keyLength,'keyCount'=>$keyCount);}}clearstatcache();if(($mtime=@filemtime($this->file))===FALSE){$mtime=0;}}while($this->mtime<$mtime);if(!($this->logHandle=@fopen($logfile=$this->file.self::EXTLOG,'a+b'))){throw
new\XInvalidStateException("Cannot open logfile '$logfile' for journal.");}$doMergeFirst=FALSE;$openNewLog=FALSE;$reopen=FALSE;if(flock($this->logHandle,LOCK_SH|LOCK_NB)){if(file_exists($logfile=$this->file.self::EXTLOGNEW)){if(($logmtime=@filemtime($this->file.self::EXTLOG))===FALSE){throw
new\XInvalidStateException("Cannot determine modification time of logfile '$this->file".self::EXTLOG."'.");}if($logmtime<$this->mtime){fclose($this->logHandle);if(!@rename($this->file.self::EXTLOGNEW,$this->file.self::EXTLOG)){clearstatcache();if(!file_exists($this->file.self::EXTLOGNEW)){$reopen=TRUE;}else{$openNewLog=TRUE;}}else{$reopen=TRUE;}}else{if(!$this->rebuild()){$doMergeFirst=TRUE;$openNewLog=TRUE;}}}}else{$doMergeFirst=TRUE;$openNewLog=TRUE;}if($reopen&&$openNewLog){throw
new\LogicException('Something bad with algorithm.');}if($doMergeFirst){$this->logMerge=$this->mergeLogFile($this->logHandle,0);}if($reopen){fclose($this->logHandle);if(!($this->logHandle=@fopen($logfile=$this->file.self::EXTLOG,'a+b'))){throw
new\XInvalidStateException("Cannot open logfile '$logfile'.");}if(!flock($this->logHandle,LOCK_SH)){throw
new\XInvalidStateException('Cannot acquite shared lock on log.');}}if($openNewLog){fclose($this->logHandle);if(!($this->logHandle=@fopen($logfile=$this->file.self::EXTLOGNEW,'a+b'))){throw
new\XInvalidStateException("Cannot open logfile '$logfile'.");}$this->isLogNew=TRUE;}$this->logMerge=$this->mergeLogFile($this->logHandle,0,$this->logMerge);$this->logMergeP=ftell($this->logHandle);if($this->logMergeP===0){if(!flock($this->logHandle,LOCK_EX)){throw
new\XInvalidStateException('Cannot acquite exclusive lock on log.');}$data=serialize(array());$data=pack('N',strlen($data)).$data;$written=fwrite($this->logHandle,$data);if($written===FALSE||$written!==strlen($data)){throw
new\XInvalidStateException('Cannot write empty packet to log.');}if(!flock($this->logHandle,LOCK_SH)){throw
new\XInvalidStateException('Cannot acquite shared lock on log.');}}}function
write($key,array$dependencies){$log=array();$delete=$this->get(self::ENTRIES,$key);if($delete!==NULL&&isset($delete[$key])){foreach($delete[$key]as$id){list($sectionName,$k)=explode(':',$id,2);$sectionName=intval($sectionName);if(!isset($log[$sectionName])){$log[$sectionName]=array();}if(!isset($log[$sectionName][self::DELETE])){$log[$sectionName][self::DELETE]=array();}$log[$sectionName][self::DELETE][$k][]=$key;}}if(!empty($dependencies[Cache::TAGS])){if(!isset($log[self::TAGS])){$log[self::TAGS]=array();}if(!isset($log[self::TAGS][self::ADD])){$log[self::TAGS][self::ADD]=array();}foreach((array)$dependencies[Cache::TAGS]as$tag){$log[self::TAGS][self::ADD][$tag]=(array)$key;}}if(!empty($dependencies[Cache::PRIORITY])){if(!isset($log[self::PRIORITY])){$log[self::PRIORITY]=array();}if(!isset($log[self::PRIORITY][self::ADD])){$log[self::PRIORITY][self::ADD]=array();}$log[self::PRIORITY][self::ADD][sprintf('%010u',(int)$dependencies[Cache::PRIORITY])]=(array)$key;}if(empty($log)){return;}$entriesSection=array(self::ADD=>array());foreach($log
as$sectionName=>$section){if(!isset($section[self::ADD])){continue;}foreach($section[self::ADD]as$k=>$_){$entriesSection[self::ADD][$key][]="$sectionName:$k";}}$entriesSection[self::ADD][$key][]=self::ENTRIES.':'.$key;$log[self::ENTRIES]=$entriesSection;$this->log($log);}private
function
log(array$data){$data=$this->mergeLogRecords(array(),$data);$data=serialize($data);$data=pack('N',strlen($data)).$data;$written=fwrite($this->logHandle,$data);if($written===FALSE||$written!==strlen($data)){throw
new\XInvalidStateException('Cannot write to log.');}if(!$this->isLogNew){fseek($this->logHandle,0,SEEK_END);$size=ftell($this->logHandle);if($size>self::LOGMAXSIZE){$this->rebuild();}}return
TRUE;}private
function
rebuild(){if(!flock($this->logHandle,LOCK_EX|LOCK_NB)){return
TRUE;}if(!($newhandle=@fopen($this->file.self::EXTNEW,'wb'))){flock($this->logHandle,LOCK_UN);return
FALSE;}$merged=$this->mergeLogFile($this->logHandle);$sections=array_unique(array_merge(array_keys($this->sections),array_keys($merged)),SORT_NUMERIC);sort($sections);$offset=4096;$newsections=array();foreach($sections
as$section){$maxKeyLength=0;$keyCount=0;if(isset($this->sections[$section])){$maxKeyLength=$this->sections[$section]->keyLength;$keyCount=$this->sections[$section]->keyCount;}if(isset($merged[$section][self::ADD])){foreach($merged[$section][self::ADD]as$k=>$_){if(($len=strlen((string)$k))>$maxKeyLength){$maxKeyLength=$len;}$keyCount++;}}$newsections[$section]=(object)array('keyLength'=>$maxKeyLength,'keyCount'=>$keyCount,'offset'=>$offset);$offset+=$keyCount*($maxKeyLength+2*self::INT32);}$dataOffset=$offset;$dataWrite=array();$clean=isset($merged[self::CLEAN]);unset($merged[self::CLEAN]);foreach($sections
as$section){fseek($newhandle,$newsections[$section]->offset,SEEK_SET);$pack='a'.$newsections[$section]->keyLength.'NN';$realKeyCount=0;foreach(self::$ops
as$op){if(isset($merged[$section][$op])){reset($merged[$section][$op]);}}if($this->handle&&isset($this->sections[$section])&&!$clean){$unpack='a'.$this->sections[$section]->keyLength.'key/NvalueOffset/NvalueLength';$recordSize=$this->sections[$section]->keyLength+2*self::INT32;$batchSize=intval(65536/$recordSize);$i=0;while($i<$this->sections[$section]->keyCount){fseek($this->handle,$this->sections[$section]->offset+$i*$recordSize,SEEK_SET);$size=min($batchSize,$this->sections[$section]->keyCount-$i);$data=stream_get_contents($this->handle,$size*$recordSize);if(!($data!==FALSE&&strlen($data)===$size*$recordSize)){flock($this->logHandle,LOCK_UN);fclose($newhandle);return
FALSE;}for($j=0;$j<$size&&$i<$this->sections[$section]->keyCount;++$j,++$i){$record=(object)unpack($unpack,substr($data,$j*$recordSize,$recordSize));$value=NULL;if(isset($merged[$section][self::DELETE])){while(current($merged[$section][self::DELETE])&&strcmp(key($merged[$section][self::DELETE]),$record->key)<0){next($merged[$section][self::DELETE]);}if(strcmp(key($merged[$section][self::DELETE]),$record->key)===0){fseek($this->handle,$record->valueOffset,SEEK_SET);$value=@unserialize(fread($this->handle,$record->valueLength));if($value===FALSE){flock($this->logHandle,LOCK_UN);fclose($newhandle);return
FALSE;}$value=array_flip($value);foreach(current($merged[$section][self::DELETE])as$delete){unset($value[$delete]);}$value=array_keys($value);next($merged[$section][self::DELETE]);}}if(isset($merged[$section][self::ADD])){while(current($merged[$section][self::ADD])&&strcmp(key($merged[$section][self::ADD]),$record->key)<0){$dataWrite[]=($serialized=serialize(current($merged[$section][self::ADD])));$packed=pack($pack,key($merged[$section][self::ADD]),$dataOffset,strlen($serialized));if(!$this->writeAll($newhandle,$packed)){flock($this->logHandle,LOCK_UN);fclose($newhandle);return
FALSE;}$realKeyCount++;$dataOffset+=strlen($serialized);next($merged[$section][self::ADD]);}if(strcmp(key($merged[$section][self::ADD]),$record->key)===0){if($value===NULL){$value=$this->loadValue($this->handle,$record->valueOffset,$record->valueLength);}if($value===NULL){flock($this->logHandle,LOCK_UN);fclose($newhandle);return
FALSE;}$value=array_unique(array_merge($value,current($merged[$section][self::ADD])));sort($value);next($merged[$section][self::ADD]);}}if(is_array($value)&&!empty($value)||$value===NULL){if($value!==NULL){$dataWrite[]=($serialized=serialize($value));$newValueLength=strlen($serialized);}else{$dataWrite[]=array($record->valueOffset,$record->valueLength);$newValueLength=$record->valueLength;}if(!$this->writeAll($newhandle,pack($pack,$record->key,$dataOffset,$newValueLength))){flock($this->logHandle,LOCK_UN);fclose($newhandle);return
FALSE;}$realKeyCount++;$dataOffset+=$newValueLength;}}}}while(isset($merged[$section][self::ADD])&&current($merged[$section][self::ADD])){$dataWrite[]=($serialized=serialize(current($merged[$section][self::ADD])));$valueLength=strlen($serialized);$packed=pack($pack,key($merged[$section][self::ADD]),$dataOffset,$valueLength);if(!$this->writeAll($newhandle,$packed)){flock($this->logHandle,LOCK_UN);fclose($newhandle);return
FALSE;}$realKeyCount++;$dataOffset+=$valueLength;next($merged[$section][self::ADD]);}$newsections[$section]->keyCount=$realKeyCount;if($realKeyCount<1){unset($newsections[$section]);}}fseek($newhandle,0,SEEK_SET);$data=pack('NN',self::MAGIC,count($newsections));foreach($newsections
as$name=>$section){$data.=pack('NNNN',$name,$section->offset,$section->keyLength,$section->keyCount);}if(!$this->writeAll($newhandle,$data)){flock($this->logHandle,LOCK_UN);fclose($newhandle);return
FALSE;}fseek($newhandle,$offset,SEEK_SET);reset($dataWrite);while(!empty($dataWrite)){$data=array_shift($dataWrite);if(is_string($data)){while(is_string(current($dataWrite))){$data.=array_shift($dataWrite);}if(!$this->writeAll($newhandle,$data)){flock($this->logHandle,LOCK_UN);fclose($newhandle);return
FALSE;}}else{if(!is_array($data)){throw
new\LogicException('Something bad with algorithm, it has to be array.');}list($readOffset,$readLength)=$data;while(!empty($dataWrite)&&is_array(current($dataWrite))){list($nextReadOffset,$nextReadLength)=current($dataWrite);if($readOffset+$readLength!==$nextReadOffset){break;}$readLength+=$nextReadLength;array_shift($dataWrite);}fseek($this->handle,$readOffset,SEEK_SET);while(($readLength-=stream_copy_to_stream($this->handle,$newhandle,$readLength))>0);}}fflush($newhandle);fclose($newhandle);$newhandle=NULL;if($this->handle){fclose($this->handle);$this->handle=NULL;}if(!@rename($this->file.self::EXTNEW,$this->file)){flock($this->logHandle,LOCK_UN);return
FALSE;}ftruncate($this->logHandle,4+strlen(serialize(array())));flock($this->logHandle,LOCK_UN);fclose($this->logHandle);if(!@rename($this->file.self::EXTLOGNEW,$this->file.self::EXTLOG)&&file_exists($this->file.self::EXTLOGNEW)){$this->isLogNew=TRUE;$logfile=$this->file.self::EXTLOGNEW;}else{$logfile=$this->file.self::EXTLOG;}if(!($this->logHandle=@fopen($logfile,'a+b'))){throw
new\XInvalidStateException("Cannot reopen logfile '$logfile'.");}$this->logMerge=array();$this->logMergeP=0;if(!($this->handle=@fopen($this->file,'rb'))){throw
new\XInvalidStateException("Cannot reopen file '$this->file'.");}clearstatcache();$this->mtime=(int)@filemtime($this->file);$this->sections=$newsections;return
TRUE;}private
function
writeAll($handle,$data){$bytesLeft=strlen($data);while($bytesLeft>0){$written=fwrite($handle,substr($data,strlen($data)-$bytesLeft));if($written===FALSE){return
FALSE;}$bytesLeft-=$written;}return
TRUE;}private
function
loadValue($handle,$offset,$length){fseek($handle,$offset,SEEK_SET);$data='';$bytesLeft=$length;while($bytesLeft>0){$read=fread($handle,$bytesLeft);if($read===FALSE){return
NULL;}$data.=$read;$bytesLeft-=strlen($read);}$value=@unserialize($data);if($value===FALSE){return
NULL;}return$value;}private
function
mergeLogFile($handle,$startp=0,$merged=array()){fseek($handle,$startp,SEEK_SET);while(!feof($handle)&&strlen($data=fread($handle,self::INT32))===self::INT32){list(,$size)=unpack('N',$data);$data=@unserialize(fread($handle,$size));if($data===FALSE){continue;}$merged=$this->mergeLogRecords($merged,$data);}ksort($merged);return$merged;}private
function
mergeLogRecords(array$a,array$b){$clean=isset($a[self::CLEAN]);unset($a[self::CLEAN],$b[self::CLEAN]);if(isset($b[self::CLEAN])){return$b;}foreach($b
as$section=>$data){if(!isset($a[$section])){$a[$section]=array();}foreach(self::$ops
as$op){if(!isset($data[$op])){continue;}if(!isset($a[$section][$op])){$a[$section][$op]=array();}foreach($data[$op]as$k=>$v){if(!isset($a[$section][$op][$k])){$a[$section][$op][$k]=array();}$a[$section][$op][$k]=array_unique(array_merge($a[$section][$op][$k],$v));if(isset($a[$section][self::$ops[$op]][$k])){$a[$section][self::$ops[$op]][$k]=array_flip($a[$section][self::$ops[$op]][$k]);foreach($v
as$unsetk){unset($a[$section][self::$ops[$op]][$k][$unsetk]);}$a[$section][self::$ops[$op]][$k]=array_keys($a[$section][self::$ops[$op]][$k]);}}}foreach(self::$ops
as$op){if(!isset($a[$section][$op])){continue;}foreach($a[$section][$op]as$k=>$v){if(empty($v)){unset($a[$section][$op][$k]);continue;}sort($a[$section][$op][$k]);}if(empty($a[$section][$op])){unset($a[$section][$op]);continue;}ksort($a[$section][$op]);}}if($clean){$a[self::CLEAN]=TRUE;}return$a;}function
clean(array$conditions){if(!empty($conditions[Cache::ALL])){$this->log(array(self::CLEAN=>TRUE));return
NULL;}else{$log=array();$entries=array();if(!empty($conditions[Cache::TAGS])){$tagEntries=array();foreach((array)$conditions[Cache::TAGS]as$tag){$tagEntries=array_merge($tagEntries,$tagEntry=$this->get(self::TAGS,$tag));if(isset($tagEntry[$tag])){foreach($tagEntry[$tag]as$entry){$entries[]=$entry;}}}if(!empty($tagEntries)){if(!isset($log[self::TAGS])){$log[self::TAGS]=array();}$log[self::TAGS][self::DELETE]=$tagEntries;}}if(isset($conditions[Cache::PRIORITY])){$priorityEntries=$this->getLte(self::PRIORITY,sprintf('%010u',(int)$conditions[Cache::PRIORITY]));foreach($priorityEntries
as$priorityEntry){foreach($priorityEntry
as$entry){$entries[]=$entry;}}if(!empty($priorityEntries)){if(!isset($log[self::PRIORITY])){$log[self::PRIORITY]=array();}$log[self::PRIORITY][self::DELETE]=$priorityEntries;}}if(!empty($log)){if(!$this->log($log)){return
array();}}return
array_values(array_unique($entries));}}private
function
get($section,$key){$this->reload();$ret=$this->logMerge;if(!isset($ret[self::CLEAN])){list($offset,$record)=$this->lowerBound($section,$key);if($offset!==-1&&$record->key===$key&&!isset($ret[self::CLEAN])){$entries=$this->loadValue($this->handle,$record->valueOffset,$record->valueLength);$ret=$this->mergeLogRecords(array($section=>array(self::ADD=>array($key=>$entries))),$ret);}}return
isset($ret[$section][self::ADD][$key])?array($key=>$ret[$section][self::ADD][$key]):array();}private
function
getLte($section,$key){$this->reload();$ret=array();if(!isset($this->logMerge[self::CLEAN])){list($offset,$record)=$this->lowerBound($section,$key);if($offset!==-1){$unpack='a'.$this->sections[$section]->keyLength.'key/NvalueOffset/NvalueLength';$recordSize=$this->sections[$section]->keyLength+2*self::INT32;$batchSize=intval(65536/$recordSize);$i=0;$count=($offset-$this->sections[$section]->offset)/$recordSize;if($record->key===$key){$count+=1;}while($i<$count){fseek($this->handle,$this->sections[$section]->offset+$i*$recordSize,SEEK_SET);$size=min($batchSize,$count-$i);$data=stream_get_contents($this->handle,$size*$recordSize);if(!($data!==FALSE&&strlen($data)===$size*$recordSize)){return
NULL;}for($j=0;$j<$size&&$i<$count;++$j,++$i){$record=(object)unpack($unpack,substr($data,$j*$recordSize,$recordSize));$ret[$record->key]=$this->loadValue($this->handle,$record->valueOffset,$record->valueLength);if($ret[$record->key]===NULL){unset($ret[$record->key]);}}}}}if(isset($this->logMerge[$section][self::DELETE])){$ret=$this->mergeLogRecords(array($section=>array(self::DELETE=>$this->logMerge[$section][self::DELETE])),array($section=>array(self::ADD=>$ret)));if(!isset($ret[$section][self::ADD])){$ret=array();}else{$ret=$ret[$section][self::ADD];}}if(isset($this->logMerge[$section][self::ADD])){foreach($this->logMerge[$section][self::ADD]as$k=>$v){if(strcmp($k,$key)>0){continue;}if(!isset($ret[$k])){$ret[$k]=array();}$ret[$k]=array_values(array_unique(array_merge($ret[$k],$v)));}}return$ret;}private
function
lowerBound($section,$key){if(!isset($this->sections[$section])){return
array(-1,NULL);}$l=0;$r=$this->sections[$section]->keyCount;$unpack='a'.$this->sections[$section]->keyLength.'key/NvalueOffset/NvalueLength';$recordSize=$this->sections[$section]->keyLength+2*self::INT32;while($l<$r){$m=intval(($l+$r)/2);fseek($this->handle,$this->sections[$section]->offset+$m*$recordSize);$data=stream_get_contents($this->handle,$recordSize);if(!($data!==FALSE&&strlen($data)===$recordSize)){return
array(-1,NULL);}$record=(object)unpack($unpack,$data);if(strcmp($record->key,$key)<0){$l=$m+1;}else{$r=$m;}}fseek($this->handle,$this->sections[$section]->offset+$l*$recordSize);$data=stream_get_contents($this->handle,$recordSize);if(!($data!==FALSE&&strlen($data)===$recordSize)){return
array(-1,NULL);}$record=(object)unpack($unpack,$data);return
array($this->sections[$section]->offset+$l*$recordSize,$record);}}class
FileStorage
extends
NetteX\Object
implements
ICacheStorage{const
META_HEADER_LEN=28;const
META_TIME='time';const
META_SERIALIZED='serialized';const
META_EXPIRE='expire';const
META_DELTA='delta';const
META_ITEMS='di';const
META_CALLBACKS='callbacks';const
FILE='file';const
HANDLE='handle';public
static$gcProbability=0.001;public
static$useDirectories;private$dir;private$useDirs;private$context;function
__construct($dir,NetteX\Context$context=NULL){if(self::$useDirectories===NULL){$uniq=uniqid('_',TRUE);umask(0000);if(!@mkdir("$dir/$uniq",0777)){throw
new\XInvalidStateException("Unable to write to directory '$dir'. Make this directory writable.");}self::$useDirectories=!ini_get('safe_mode');if(!self::$useDirectories&&@file_put_contents("$dir/$uniq/_",'')!==FALSE){self::$useDirectories=TRUE;unlink("$dir/$uniq/_");}@rmdir("$dir/$uniq");}$this->dir=$dir;$this->useDirs=(bool)self::$useDirectories;$this->context=$context;if(mt_rand()/mt_getrandmax()<self::$gcProbability){$this->clean(array());}}function
read($key){$meta=$this->readMeta($this->getCacheFile($key),LOCK_SH);if($meta&&$this->verify($meta)){return$this->readData($meta);}else{return
NULL;}}private
function
verify($meta){do{if(!empty($meta[self::META_DELTA])){if(filemtime($meta[self::FILE])+$meta[self::META_DELTA]<time())break;touch($meta[self::FILE]);}elseif(!empty($meta[self::META_EXPIRE])&&$meta[self::META_EXPIRE]<time()){break;}if(!empty($meta[self::META_CALLBACKS])&&!Cache::checkCallbacks($meta[self::META_CALLBACKS])){break;}if(!empty($meta[self::META_ITEMS])){foreach($meta[self::META_ITEMS]as$depFile=>$time){$m=$this->readMeta($depFile,LOCK_SH);if($m[self::META_TIME]!==$time)break
2;if($m&&!$this->verify($m))break
2;}}return
TRUE;}while(FALSE);$this->delete($meta[self::FILE],$meta[self::HANDLE]);return
FALSE;}function
write($key,$data,array$dp){$meta=array(self::META_TIME=>microtime());if(isset($dp[Cache::EXPIRATION])){if(empty($dp[Cache::SLIDING])){$meta[self::META_EXPIRE]=$dp[Cache::EXPIRATION]+time();}else{$meta[self::META_DELTA]=(int)$dp[Cache::EXPIRATION];}}if(isset($dp[Cache::ITEMS])){foreach((array)$dp[Cache::ITEMS]as$item){$depFile=$this->getCacheFile($item);$m=$this->readMeta($depFile,LOCK_SH);$meta[self::META_ITEMS][$depFile]=$m[self::META_TIME];unset($m);}}if(isset($dp[Cache::CALLBACKS])){$meta[self::META_CALLBACKS]=$dp[Cache::CALLBACKS];}$cacheFile=$this->getCacheFile($key);if($this->useDirs&&!is_dir($dir=dirname($cacheFile))){umask(0000);if(!mkdir($dir,0777)){return;}}$handle=@fopen($cacheFile,'r+b');if(!$handle){$handle=fopen($cacheFile,'wb');if(!$handle){return;}}if(isset($dp[Cache::TAGS])||isset($dp[Cache::PRIORITY])){if(!$this->context){throw
new\XInvalidStateException('CacheJournal has not been provided.');}$this->getJournal()->write($cacheFile,$dp);}flock($handle,LOCK_EX);ftruncate($handle,0);if(!is_string($data)){$data=serialize($data);$meta[self::META_SERIALIZED]=TRUE;}$head=serialize($meta).'?>';$head='<?php //netteCache[01]'.str_pad((string)strlen($head),6,'0',STR_PAD_LEFT).$head;$headLen=strlen($head);$dataLen=strlen($data);do{if(fwrite($handle,str_repeat("\x00",$headLen),$headLen)!==$headLen){break;}if(fwrite($handle,$data,$dataLen)!==$dataLen){break;}fseek($handle,0);if(fwrite($handle,$head,$headLen)!==$headLen){break;}fclose($handle);return
TRUE;}while(FALSE);$this->delete($cacheFile,$handle);}function
remove($key){$this->delete($this->getCacheFile($key));}function
clean(array$conds){$all=!empty($conds[Cache::ALL]);$collector=empty($conds);if($all||$collector){$now=time();foreach(NetteX\Finder::find('*')->from($this->dir)->childFirst()as$entry){$path=(string)$entry;if($entry->isDir()){@rmdir($path);continue;}if($all){$this->delete($path);}else{$meta=$this->readMeta($path,LOCK_SH);if(!$meta)continue;if(!empty($meta[self::META_EXPIRE])&&$meta[self::META_EXPIRE]<$now){$this->delete($path,$meta[self::HANDLE]);continue;}fclose($meta[self::HANDLE]);}}if($this->context){$this->getJournal()->clean($conds);}return;}if($this->context){foreach($this->getJournal()->clean($conds)as$file){$this->delete($file);}}}protected
function
readMeta($file,$lock){$handle=@fopen($file,'r+b');if(!$handle)return
NULL;flock($handle,$lock);$head=stream_get_contents($handle,self::META_HEADER_LEN);if($head&&strlen($head)===self::META_HEADER_LEN){$size=(int)substr($head,-6);$meta=stream_get_contents($handle,$size,self::META_HEADER_LEN);$meta=@unserialize($meta);if(is_array($meta)){fseek($handle,$size+self::META_HEADER_LEN);$meta[self::FILE]=$file;$meta[self::HANDLE]=$handle;return$meta;}}fclose($handle);return
NULL;}protected
function
readData($meta){$data=stream_get_contents($meta[self::HANDLE]);fclose($meta[self::HANDLE]);if(empty($meta[self::META_SERIALIZED])){return$data;}else{return@unserialize($data);}}protected
function
getCacheFile($key){if($this->useDirs){$key=explode(Cache::NAMESPACE_SEPARATOR,$key,2);return$this->dir.'/'.(isset($key[1])?urlencode($key[0]).'/_'.urlencode($key[1]):'_'.urlencode($key[0]));}else{return$this->dir.'/_'.urlencode($key);}}private
static
function
delete($file,$handle=NULL){if(@unlink($file)){if($handle)fclose($handle);return;}if(!$handle){$handle=@fopen($file,'r+');}if($handle){flock($handle,LOCK_EX);ftruncate($handle,0);fclose($handle);@unlink($file);}}protected
function
getJournal(){return$this->context->getService('NetteX\\Caching\\ICacheJournal');}}class
MemcachedStorage
extends
NetteX\Object
implements
ICacheStorage{const
META_CALLBACKS='callbacks';const
META_DATA='data';const
META_DELTA='delta';private$memcache;private$prefix;private$context;static
function
isAvailable(){return
extension_loaded('memcache');}function
__construct($host='localhost',$port=11211,$prefix='',NetteX\Context$context=NULL){if(!self::isAvailable()){throw
new\XNotSupportedException("PHP extension 'memcache' is not loaded.");}$this->prefix=$prefix;$this->context=$context;$this->memcache=new\Memcache;NetteX\Debug::tryError();$this->memcache->connect($host,$port);if(NetteX\Debug::catchError($e)){throw
new\XInvalidStateException($e->getMessage());}}function
read($key){$key=$this->prefix.$key;$meta=$this->memcache->get($key);if(!$meta)return
NULL;if(!empty($meta[self::META_CALLBACKS])&&!Cache::checkCallbacks($meta[self::META_CALLBACKS])){$this->memcache->delete($key,0);return
NULL;}if(!empty($meta[self::META_DELTA])){$this->memcache->replace($key,$meta,0,$meta[self::META_DELTA]+time());}return$meta[self::META_DATA];}function
write($key,$data,array$dp){if(isset($dp[Cache::ITEMS])){throw
new\XNotSupportedException('Dependent items are not supported by MemcachedStorage.');}$meta=array(self::META_DATA=>$data);$expire=0;if(isset($dp[Cache::EXPIRATION])){$expire=(int)$dp[Cache::EXPIRATION];if(!empty($dp[Cache::SLIDING])){$meta[self::META_DELTA]=$expire;}}if(isset($dp[Cache::CALLBACKS])){$meta[self::META_CALLBACKS]=$dp[Cache::CALLBACKS];}if(isset($dp[Cache::TAGS])||isset($dp[Cache::PRIORITY])){if(!$this->context){throw
new\XInvalidStateException('CacheJournal has not been provided.');}$this->getJournal()->write($this->prefix.$key,$dp);}$this->memcache->set($this->prefix.$key,$meta,0,$expire);}function
remove($key){$this->memcache->delete($this->prefix.$key,0);}function
clean(array$conds){if(!empty($conds[Cache::ALL])){$this->memcache->flush();}elseif($this->context){foreach($this->getJournal()->clean($conds)as$entry){$this->memcache->delete($entry,0);}}}protected
function
getJournal(){return$this->context->getService('NetteX\\Caching\\ICacheJournal');}}class
MemoryStorage
extends
NetteX\Object
implements
ICacheStorage{private$data=array();function
read($key){return
isset($this->data[$key])?$this->data[$key]:NULL;}function
write($key,$data,array$dp){$this->data[$key]=$data;}function
remove($key){unset($this->data[$key]);}function
clean(array$conds){if(!empty($conds[Cache::ALL])){$this->data=array();}}}class
SqliteJournal
extends
NetteX\Object
implements
ICacheJournal{private$database;static
function
isAvailable(){return
extension_loaded('sqlite');}function
__construct($file){if(!self::isAvailable()){throw
new\XNotSupportedException("SQLite or SQLite3 extension is required for storing tags and priorities.");}$this->database=extension_loaded('sqlite')?new
SQLiteMimic($file):new\SQLite3($file);@$this->database->exec('CREATE TABLE cache (entry VARCHAR NOT NULL, priority INTEGER, tag VARCHAR); '.'CREATE INDEX IDX_ENTRY ON cache (entry); '.'CREATE INDEX IDX_PRI ON cache (priority); '.'CREATE INDEX IDX_TAG ON cache (tag);');}function
write($key,array$dependencies){$entry=$this->database->escapeString($key);$query='';if(!empty($dependencies[Cache::TAGS])){foreach((array)$dependencies[Cache::TAGS]as$tag){$query.="INSERT INTO cache (entry, tag) VALUES ('$entry', '".$this->database->escapeString($tag)."'); ";}}if(!empty($dependencies[Cache::PRIORITY])){$query.="INSERT INTO cache (entry, priority) VALUES ('$entry', '".((int)$dependencies[Cache::PRIORITY])."'); ";}if(!$this->database->exec("BEGIN; DELETE FROM cache WHERE entry = '$entry'; $query COMMIT;")){$this->database->exec('ROLLBACK');return
FALSE;}return
TRUE;}function
clean(array$conditions){if(!empty($conditions[Cache::ALL])){$this->database->exec('DELETE FROM CACHE;');return;}$query=array();if(!empty($conditions[Cache::TAGS])){$tags=array();foreach((array)$conditions[Cache::TAGS]as$tag){$tags[]="'".$this->database->escapeString($tag)."'";}$query[]='tag IN('.implode(', ',$tags).')';}if(isset($conditions[Cache::PRIORITY])){$query[]='priority <= '.((int)$conditions[Cache::PRIORITY]);}$entries=array();if(!empty($query)){$query=implode(' OR ',$query);$result=$this->database->query("SELECT entry FROM cache WHERE $query");if($result
instanceof\SQLiteResult){while($entry=$result->fetchSingle())$entries[]=$entry;}else{while($entry=$result->fetchArray(SQLITE3_NUM))$entries[]=$entry[0];}$this->database->exec("DELETE FROM cache WHERE $query");}return$entries;}}if(class_exists('SQLiteDatabase')){class
SQLiteMimic
extends\SQLiteDatabase{function
exec($sql){return$this->queryExec($sql);}function
escapeString($s){return
sqlite_escape_string($s);}}}}namespace NetteX\Config{use
NetteX;class
Config
implements\ArrayAccess,\IteratorAggregate{private
static$extensions=array('ini'=>'NetteX\Config\ConfigAdapterIni');static
function
registerExtension($extension,$class){if(!class_exists($class)){throw
new\InvalidArgumentException("Class '$class' was not found.");}if(!NetteX\Reflection\ClassReflection::from($class)->implementsInterface('NetteX\Config\IConfigAdapter')){throw
new\InvalidArgumentException("Configuration adapter '$class' is not NetteX\\Config\\IConfigAdapter implementor.");}self::$extensions[strtolower($extension)]=$class;}static
function
fromFile($file,$section=NULL){$extension=strtolower(pathinfo($file,PATHINFO_EXTENSION));if(isset(self::$extensions[$extension])){$arr=call_user_func(array(self::$extensions[$extension],'load'),$file,$section);return
new
static($arr);}else{throw
new\InvalidArgumentException("Unknown file extension '$file'.");}}function
__construct($arr=NULL){foreach((array)$arr
as$k=>$v){$this->$k=is_array($v)?new
static($v):$v;}}function
save($file,$section=NULL){$extension=strtolower(pathinfo($file,PATHINFO_EXTENSION));if(isset(self::$extensions[$extension])){return
call_user_func(array(self::$extensions[$extension],'save'),$this,$file,$section);}else{throw
new\InvalidArgumentException("Unknown file extension '$file'.");}}function
__set($key,$value){if(!is_scalar($key)){throw
new\InvalidArgumentException("Key must be either a string or an integer.");}elseif($value===NULL){unset($this->$key);}else{$this->$key=$value;}}function&__get($key){if(!is_scalar($key)){throw
new\InvalidArgumentException("Key must be either a string or an integer.");}return$this->$key;}function
__isset($key){return
FALSE;}function
__unset($key){}function
offsetSet($key,$value){$this->__set($key,$value);}function
offsetGet($key){if(!is_scalar($key)){throw
new\InvalidArgumentException("Key must be either a string or an integer.");}elseif(!isset($this->$key)){return
NULL;}return$this->$key;}function
offsetExists($key){if(!is_scalar($key)){throw
new\InvalidArgumentException("Key must be either a string or an integer.");}return
isset($this->$key);}function
offsetUnset($key){if(!is_scalar($key)){throw
new\InvalidArgumentException("Key must be either a string or an integer.");}unset($this->$key);}function
getIterator(){return
new
NetteX\GenericRecursiveIterator(new\ArrayIterator($this));}function
toArray(){$arr=array();foreach($this
as$k=>$v){$arr[$k]=$v
instanceof
self?$v->toArray():$v;}return$arr;}}final
class
ConfigAdapterIni
implements
IConfigAdapter{public
static$keySeparator='.';public
static$sectionSeparator=' < ';public
static$rawSection='!';final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
load($file,$section=NULL){if(!is_file($file)||!is_readable($file)){throw
new\XFileNotFoundException("File '$file' is missing or is not readable.");}NetteX\Debug::tryError();$ini=parse_ini_file($file,TRUE);if(NetteX\Debug::catchError($e)){throw$e;}$separator=trim(self::$sectionSeparator);$data=array();foreach($ini
as$secName=>$secData){if(is_array($secData)){if(substr($secName,-1)===self::$rawSection){$secName=substr($secName,0,-1);}elseif(self::$keySeparator){$tmp=array();foreach($secData
as$key=>$val){$cursor=&$tmp;foreach(explode(self::$keySeparator,$key)as$part){if(!isset($cursor[$part])||is_array($cursor[$part])){$cursor=&$cursor[$part];}else{throw
new\XInvalidStateException("Invalid key '$key' in section [$secName] in '$file'.");}}$cursor=$val;}$secData=$tmp;}$parts=$separator?explode($separator,strtr($secName,':',$separator)):array($secName);if(count($parts)>1){$parent=trim($parts[1]);$cursor=&$data;foreach(self::$keySeparator?explode(self::$keySeparator,$parent):array($parent)as$part){if(isset($cursor[$part])&&is_array($cursor[$part])){$cursor=&$cursor[$part];}else{throw
new\XInvalidStateException("Missing parent section [$parent] in '$file'.");}}$secData=NetteX\ArrayTools::mergeTree($secData,$cursor);}$secName=trim($parts[0]);if($secName===''){throw
new\XInvalidStateException("Invalid empty section name in '$file'.");}}if(self::$keySeparator){$cursor=&$data;foreach(explode(self::$keySeparator,$secName)as$part){if(!isset($cursor[$part])||is_array($cursor[$part])){$cursor=&$cursor[$part];}else{throw
new\XInvalidStateException("Invalid section [$secName] in '$file'.");}}}else{$cursor=&$data[$secName];}if(is_array($secData)&&is_array($cursor)){$secData=NetteX\ArrayTools::mergeTree($secData,$cursor);}$cursor=$secData;}if($section===NULL){return$data;}elseif(!isset($data[$section])||!is_array($data[$section])){throw
new\XInvalidStateException("There is not section [$section] in '$file'.");}else{return$data[$section];}}static
function
save($config,$file,$section=NULL){$output=array();$output[]='; generated by NetteX';$output[]='';if($section===NULL){foreach($config
as$secName=>$secData){if(!(is_array($secData)||$secData
instanceof\Traversable)){throw
new\XInvalidStateException("Invalid section '$section'.");}$output[]="[$secName]";self::build($secData,$output,'');$output[]='';}}else{$output[]="[$section]";self::build($config,$output,'');$output[]='';}if(!file_put_contents($file,implode(PHP_EOL,$output))){throw
new\XIOException("Cannot write file '$file'.");}}private
static
function
build($input,&$output,$prefix){foreach($input
as$key=>$val){if(is_array($val)||$val
instanceof\Traversable){self::build($val,$output,$prefix.$key.self::$keySeparator);}elseif(is_bool($val)){$output[]="$prefix$key = ".($val?'true':'false');}elseif(is_numeric($val)){$output[]="$prefix$key = $val";}elseif(is_string($val)){$output[]="$prefix$key = \"$val\"";}else{throw
new\InvalidArgumentException("The '$prefix$key' item must be scalar or array, ".gettype($val)." given.");}}}}}namespace NetteX{use
NetteX;use
NetteX\Environment;final
class
Debug{public
static$productionMode;public
static$consoleMode;public
static$time;private
static$firebugDetected;private
static$ajaxDetected;public
static$source;public
static$maxDepth=3;public
static$maxLen=150;public
static$showLocation=FALSE;const
DEVELOPMENT=FALSE;const
PRODUCTION=TRUE;const
DETECT=NULL;public
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
static$dumps;private
static$errors;const
DEBUG='debug';const
INFO='info';const
WARNING='warning';const
ERROR='error';const
CRITICAL='critical';final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
_init(){self::$time=microtime(TRUE);self::$consoleMode=PHP_SAPI==='cli';self::$productionMode=self::DETECT;if(self::$consoleMode){self::$source=empty($_SERVER['argv'])?'cli':'cli: '.$_SERVER['argv'][0];}else{self::$firebugDetected=isset($_SERVER['HTTP_X_FIRELOGGER']);self::$ajaxDetected=isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&$_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';if(isset($_SERVER['REQUEST_URI'])){self::$source=(isset($_SERVER['HTTPS'])&&strcasecmp($_SERVER['HTTPS'],'off')?'https://':'http://').(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'')).$_SERVER['REQUEST_URI'];}}$tab=array(__CLASS__,'renderTab');$panel=array(__CLASS__,'renderPanel');self::addPanel(new
DebugPanel('time',$tab,$panel));self::addPanel(new
DebugPanel('memory',$tab,$panel));self::addPanel(new
DebugPanel('errors',$tab,$panel));self::addPanel(new
DebugPanel('dumps',$tab,$panel));}static
function
dump($var,$return=FALSE){if(!$return&&self::$productionMode){return$var;}$output="<pre class=\"nette-dump\">".self::_dump($var,0)."</pre>\n";if(!$return&&self::$showLocation){$trace=debug_backtrace();$i=isset($trace[1]['class'])&&$trace[1]['class']===__CLASS__?1:0;if(isset($trace[$i]['file'],$trace[$i]['line'])){$output=substr_replace($output,' <small>'.htmlspecialchars("in file {$trace[$i]['file']} on line {$trace[$i]['line']}",ENT_NOQUOTES).'</small>',-8,0);}}if(self::$consoleMode){$output=htmlspecialchars_decode(strip_tags($output),ENT_NOQUOTES);}if($return){return$output;}else{echo$output;return$var;}}static
function
barDump($var,$title=NULL){if(!self::$productionMode){$dump=array();foreach((is_array($var)?$var:array(''=>$var))as$key=>$val){$dump[$key]=self::_dump($val,0);}self::$dumps[]=array('title'=>$title,'dump'=>$dump);}return$var;}private
static
function
_dump(&$var,$level){static$tableUtf,$tableBin,$reBinary='#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u';if($tableUtf===NULL){foreach(range("\x00","\xFF")as$ch){if(ord($ch)<32&&strpos("\r\n\t",$ch)===FALSE)$tableUtf[$ch]=$tableBin[$ch]='\\x'.str_pad(dechex(ord($ch)),2,'0',STR_PAD_LEFT);elseif(ord($ch)<127)$tableUtf[$ch]=$tableBin[$ch]=$ch;else{$tableUtf[$ch]=$ch;$tableBin[$ch]='\\x'.dechex(ord($ch));}}$tableBin["\\"]='\\\\';$tableBin["\r"]='\\r';$tableBin["\n"]='\\n';$tableBin["\t"]='\\t';$tableUtf['\\x']=$tableBin['\\x']='\\\\x';}if(is_bool($var)){return($var?'TRUE':'FALSE')."\n";}elseif($var===NULL){return"NULL\n";}elseif(is_int($var)){return"$var\n";}elseif(is_float($var)){$var=(string)$var;if(strpos($var,'.')===FALSE)$var.='.0';return"$var\n";}elseif(is_string($var)){if(self::$maxLen&&strlen($var)>self::$maxLen){$s=htmlSpecialChars(substr($var,0,self::$maxLen),ENT_NOQUOTES).' ... ';}else{$s=htmlSpecialChars($var,ENT_NOQUOTES);}$s=strtr($s,preg_match($reBinary,$s)||preg_last_error()?$tableBin:$tableUtf);$len=strlen($var);return"\"$s\"".($len>1?" ($len)":"")."\n";}elseif(is_array($var)){$s="<span>array</span>(".count($var).") ";$space=str_repeat($space1='   ',$level);$brackets=range(0,count($var)-1)===array_keys($var)?"[]":"{}";static$marker;if($marker===NULL)$marker=uniqid("\x00",TRUE);if(empty($var)){}elseif(isset($var[$marker])){$brackets=$var[$marker];$s.="$brackets[0] *RECURSION* $brackets[1]";}elseif($level<self::$maxDepth||!self::$maxDepth){$s.="<code>$brackets[0]\n";$var[$marker]=$brackets;foreach($var
as$k=>&$v){if($k===$marker)continue;$k=is_int($k)?$k:'"'.strtr($k,preg_match($reBinary,$k)||preg_last_error()?$tableBin:$tableUtf).'"';$s.="$space$space1$k => ".self::_dump($v,$level+1);}unset($var[$marker]);$s.="$space$brackets[1]</code>";}else{$s.="$brackets[0] ... $brackets[1]";}return$s."\n";}elseif(is_object($var)){$arr=(array)$var;$s="<span>".get_class($var)."</span>(".count($arr).") ";$space=str_repeat($space1='   ',$level);static$list=array();if(empty($arr)){}elseif(in_array($var,$list,TRUE)){$s.="{ *RECURSION* }";}elseif($level<self::$maxDepth||!self::$maxDepth){$s.="<code>{\n";$list[]=$var;foreach($arr
as$k=>&$v){$m='';if($k[0]==="\x00"){$m=$k[1]==='*'?' <span>protected</span>':' <span>private</span>';$k=substr($k,strrpos($k,"\x00")+1);}$k=strtr($k,preg_match($reBinary,$k)||preg_last_error()?$tableBin:$tableUtf);$s.="$space$space1\"$k\"$m => ".self::_dump($v,$level+1);}array_pop($list);$s.="$space}</code>";}else{$s.="{ ... }";}return$s."\n";}elseif(is_resource($var)){return"<span>".get_resource_type($var)." resource</span>\n";}else{return"<span>unknown type</span>\n";}}static
function
timer($name=NULL){static$time=array();$now=microtime(TRUE);$delta=isset($time[$name])?$now-$time[$name]:0;$time[$name]=$now;return$delta;}static
function
enable($mode=NULL,$logDirectory=NULL,$email=NULL){error_reporting(E_ALL|E_STRICT);if(is_bool($mode)){self::$productionMode=$mode;}elseif(is_string($mode)){$mode=preg_split('#[,\s]+#',"$mode 127.0.0.1 ::1");}if(is_array($mode)){self::$productionMode=!isset($_SERVER['REMOTE_ADDR'])||!in_array($_SERVER['REMOTE_ADDR'],$mode,TRUE);}if(self::$productionMode===self::DETECT){if(class_exists('NetteX\Environment')){self::$productionMode=Environment::isProduction();}elseif(isset($_SERVER['SERVER_ADDR'])||isset($_SERVER['LOCAL_ADDR'])){$addrs=array();if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){$addrs=preg_split('#,\s*#',$_SERVER['HTTP_X_FORWARDED_FOR']);}if(isset($_SERVER['REMOTE_ADDR'])){$addrs[]=$_SERVER['REMOTE_ADDR'];}$addrs[]=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['LOCAL_ADDR'];self::$productionMode=FALSE;foreach($addrs
as$addr){$oct=explode('.',$addr);if($addr!=='::1'&&(count($oct)!==4||($oct[0]!=='10'&&$oct[0]!=='127'&&($oct[0]!=='172'||$oct[1]<16||$oct[1]>31)&&($oct[0]!=='169'||$oct[1]!=='254')&&($oct[0]!=='192'||$oct[1]!=='168')))){self::$productionMode=TRUE;break;}}}else{self::$productionMode=!self::$consoleMode;}}if(is_string($logDirectory)||$logDirectory===FALSE){self::$logDirectory=$logDirectory;}else{self::$logDirectory=defined('APP_DIR')?APP_DIR.'/../log':getcwd().'/log';}if(self::$logDirectory){ini_set('error_log',self::$logDirectory.'/php_error.log');}if(function_exists('ini_set')){ini_set('display_errors',!self::$productionMode);ini_set('html_errors',FALSE);ini_set('log_errors',FALSE);}elseif(ini_get('display_errors')!=!self::$productionMode&&ini_get('display_errors')!==(self::$productionMode?'stderr':'stdout')){throw
new\XNotSupportedException('Function ini_set() must be enabled.');}if($email){if(!is_string($email)){throw
new\InvalidArgumentException('E-mail address must be a string.');}self::$email=$email;}if(!defined('E_DEPRECATED')){define('E_DEPRECATED',8192);}if(!defined('E_USER_DEPRECATED')){define('E_USER_DEPRECATED',16384);}if(!self::$enabled){register_shutdown_function(array(__CLASS__,'_shutdownHandler'));set_exception_handler(array(__CLASS__,'_exceptionHandler'));set_error_handler(array(__CLASS__,'_errorHandler'));self::$enabled=TRUE;}}static
function
isEnabled(){return
self::$enabled;}static
function
log($message,$priority=self::INFO){if(self::$logDirectory===FALSE){return;}elseif(!self::$logDirectory){throw
new\XInvalidStateException('Logging directory is not specified in NetteX\Debug::$logDirectory.');}elseif(!is_dir(self::$logDirectory)){throw
new\XDirectoryNotFoundException("Directory '".self::$logDirectory."' is not found or is not directory.");}if($message
instanceof\Exception){$exception=$message;$message="PHP Fatal error: ".($message
instanceof\XFatalErrorException?$exception->getMessage():"Uncaught exception ".get_class($exception)." with message '".$exception->getMessage()."'")." in ".$exception->getFile().":".$exception->getLine();}error_log(@date('[Y-m-d H-i-s] ').trim($message).(self::$source?'  @  '.self::$source:'').PHP_EOL,3,self::$logDirectory.'/'.strtolower($priority).'.log');if(($priority===self::ERROR||$priority===self::CRITICAL)&&self::$email&&@filemtime(self::$logDirectory.'/email-sent')+self::$emailSnooze<time()&&@file_put_contents(self::$logDirectory.'/email-sent','sent')){call_user_func(self::$mailer,$message);}if(isset($exception)){$hash=md5($exception);foreach(new\DirectoryIterator(self::$logDirectory)as$entry){if(strpos($entry,$hash)){$skip=TRUE;break;}}if(empty($skip)&&$logHandle=@fopen(self::$logDirectory."/exception ".@date('Y-m-d H-i-s')." $hash.html",'w')){ob_start();ob_start(function($buffer)use($logHandle){fwrite($logHandle,$buffer);},1);self::paintBlueScreen($exception);ob_end_flush();ob_end_clean();fclose($logHandle);}}}static
function
_shutdownHandler(){static$types=array(E_ERROR=>1,E_CORE_ERROR=>1,E_COMPILE_ERROR=>1,E_PARSE=>1);$error=error_get_last();if(isset($types[$error['type']])){self::_exceptionHandler(new\XFatalErrorException($error['message'],0,$error['type'],$error['file'],$error['line'],NULL));return;}if(self::$showBar&&!self::$productionMode&&!self::$ajaxDetected&&!self::$consoleMode&&(!preg_match('#^Content-Type: (?!text/html)#im',implode("\n",headers_list())))){self::paintDebugBar();}}static
function
_exceptionHandler(\Exception$exception){if(!headers_sent()){header('HTTP/1.1 500 Internal Server Error');}$htmlMode=!self::$ajaxDetected&&!preg_match('#^Content-Type: (?!text/html)#im',implode("\n",headers_list()));try{if(self::$productionMode){self::log($exception,self::ERROR);if(self::$consoleMode){echo"ERROR: the server encountered an internal error and was unable to complete your request.\n";}elseif($htmlMode){echo"<!DOCTYPE html><meta name=robots content=noindex><meta name=generator content='NetteX Framework'>\n\n";echo"<style>body{color:#333;background:white;width:500px;margin:100px auto}h1{font:bold 47px/1.5 sans-serif;margin:.6em 0}p{font:21px/1.5 Georgia,serif;margin:1.5em 0}small{font-size:70%;color:gray}</style>\n\n";echo"<title>Server Error</title>\n\n<h1>Server Error</h1>\n\n<p>We're sorry! The server encountered an internal error and was unable to complete your request. Please try again later.</p>\n\n<p><small>error 500</small></p>";}}else{if(self::$consoleMode){echo"$exception\n";}elseif($htmlMode){self::paintBlueScreen($exception);}elseif(!self::fireLog($exception,self::ERROR)){self::log($exception);}}foreach(self::$onFatalError
as$handler){call_user_func($handler,$exception);}}catch(\Exception$e){echo"\nNetteX\\Debug FATAL ERROR: thrown ",get_class($e),': ',$e->getMessage(),"\nwhile processing ",get_class($exception),': ',$exception->getMessage(),"\n";exit;}}static
function
_errorHandler($severity,$message,$file,$line,$context){if(self::$lastError!==FALSE){self::$lastError=new\ErrorException($message,0,$severity,$file,$line);return
NULL;}if(self::$scream){error_reporting(E_ALL|E_STRICT);}if($severity===E_RECOVERABLE_ERROR||$severity===E_USER_ERROR){throw
new\XFatalErrorException($message,0,$severity,$file,$line,$context);}elseif(($severity&error_reporting())!==$severity){return
FALSE;}elseif(self::$strictMode&&!self::$productionMode){self::_exceptionHandler(new\XFatalErrorException($message,0,$severity,$file,$line,$context));exit;}static$types=array(E_WARNING=>'Warning',E_COMPILE_WARNING=>'Warning',E_USER_WARNING=>'Warning',E_NOTICE=>'Notice',E_USER_NOTICE=>'Notice',E_STRICT=>'Strict standards',E_DEPRECATED=>'Deprecated',E_USER_DEPRECATED=>'Deprecated');$message='PHP '.(isset($types[$severity])?$types[$severity]:'Unknown error').": $message";$count=&self::$errors["$message|$file|$line"];if($count++){return
NULL;}elseif(self::$productionMode){self::log("$message in $file:$line",self::ERROR);return
NULL;}else{$ok=self::fireLog(new\ErrorException($message,0,$severity,$file,$line),self::WARNING);return
self::$consoleMode||(!self::$showBar&&!$ok)?FALSE:NULL;}return
FALSE;}static
function
processException(\Exception$exception){trigger_error(__METHOD__.'() is deprecated; use '.__CLASS__.'::log($exception, Debug::ERROR) instead.',E_USER_WARNING);self::log($exception,self::ERROR);}static
function
toStringException(\Exception$exception){if(self::$enabled){self::_exceptionHandler($exception);}else{trigger_error($exception->getMessage(),E_USER_ERROR);}exit;}static
function
paintBlueScreen(\Exception$exception){if(class_exists('NetteX\Environment',FALSE)){$application=Environment::getContext()->hasService('NetteX\\Application\\Application',TRUE)?Environment::getContext()->getService('NetteX\\Application\\Application'):NULL;}if(!function_exists('NetteX\_netteDebugPrintCode')){function
_netteDebugPrintCode($file,$line,$count=15){if(function_exists('ini_set')){ini_set('highlight.comment','#999; font-style: italic');ini_set('highlight.default','#000');ini_set('highlight.html','#06B');ini_set('highlight.keyword','#D24; font-weight: bold');ini_set('highlight.string','#080');}$start=max(1,$line-floor($count/2));$source=@file_get_contents($file);if(!$source)return;$source=explode("\n",highlight_string($source,TRUE));$spans=1;echo$source[0];$source=explode('<br />',$source[1]);array_unshift($source,NULL);$i=$start;while(--$i>=1){if(preg_match('#.*(</?span[^>]*>)#',$source[$i],$m)){if($m[1]!=='</span>'){$spans++;echo$m[1];}break;}}$source=array_slice($source,$start,$count,TRUE);end($source);$numWidth=strlen((string)key($source));foreach($source
as$n=>$s){$spans+=substr_count($s,'<span')-substr_count($s,'</span');$s=str_replace(array("\r","\n"),array('',''),$s);preg_match_all('#<[^>]+>#',$s,$tags);if($n===$line){printf("<span class='highlight'>%{$numWidth}s:    %s\n</span>%s",$n,strip_tags($s),implode('',$tags[0]));}else{printf("<span class='line'>%{$numWidth}s:</span>    %s\n",$n,$s);}}echo
str_repeat('</span>',$spans),'</code>';}function
_netteDump($dump){return'<pre class="nette-dump">'.preg_replace_callback('#^( *)((?>[^(]{1,200}))\((\d+)\) <code>#m',function($m){return"$m[1]<a href='#' onclick='return !netteToggle(this)'>$m[2]($m[3]) ".(trim($m[1])||$m[3]<7?'<abbr>&#x25bc;</abbr> </a><code>':'<abbr>&#x25ba;</abbr> </a><code class="collapsed">');},$dump).'</pre>';}function
_netteOpenPanel($name,$collapsed){static$id;$id++;?>
	<div class="panel">
		<h2><a href="#" onclick="return !netteToggle(this, 'netteBsPnl<?php echo$id?>')"><?php echo
htmlSpecialChars($name)?> <abbr><?php echo$collapsed?'&#x25ba;':'&#x25bc;'?></abbr></a></h2>

		<div id="netteBsPnl<?php echo$id?>" class="<?php echo$collapsed?'collapsed ':''?>inner">
	<?php
}function
_netteClosePanel(){?>
		</div>
	</div>
	<?php
}}static$errorTypes=array(E_ERROR=>'Fatal Error',E_USER_ERROR=>'User Error',E_RECOVERABLE_ERROR=>'Recoverable Error',E_CORE_ERROR=>'Core Error',E_COMPILE_ERROR=>'Compile Error',E_PARSE=>'Parse Error',E_WARNING=>'Warning',E_CORE_WARNING=>'Core Warning',E_COMPILE_WARNING=>'Compile Warning',E_USER_WARNING=>'User Warning',E_NOTICE=>'Notice',E_USER_NOTICE=>'User Notice',E_STRICT=>'Strict',E_DEPRECATED=>'Deprecated',E_USER_DEPRECATED=>'User Deprecated');$title=($exception
instanceof\XFatalErrorException&&isset($errorTypes[$exception->getSeverity()]))?$errorTypes[$exception->getSeverity()]:get_class($exception);$expandPath=NETTEX_DIR.DIRECTORY_SEPARATOR;if(headers_sent()){echo'</pre></xmp></table>';}?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="robots" content="noindex,noarchive">
	<meta name="generator" content="NetteX Framework">

	<title><?php echo
htmlspecialchars($title)?></title><!-- <?php
$ex=$exception;echo$ex->getMessage(),($ex->getCode()?' #'.$ex->getCode():'');while((method_exists($ex,'getPrevious')&&$ex=$ex->getPrevious())||(isset($ex->previous)&&$ex=$ex->previous))echo'; caused by ',get_class($ex),' ',$ex->getMessage(),($ex->getCode()?' #'.$ex->getCode():'');?> -->

	<style type="text/css" class="nette">body{margin:0 0 2em;padding:0}#netteBluescreen{font:9pt/1.5 Verdana,sans-serif;background:white;color:#333;position:absolute;left:0;top:0;width:100%;z-index:23178;text-align:left}#netteBluescreen *{font:inherit;color:inherit;background:transparent;border:none;margin:0;padding:0;text-align:inherit;text-indent:0}#netteBluescreen b{font-weight:bold}#netteBluescreen i{font-style:italic}#netteBluescreenIcon{position:absolute;right:.5em;top:.5em;z-index:23179;text-decoration:none;background:#CD1818;padding:3px}#netteBluescreenError{background:#CD1818;color:white;font:13pt/1.5 Verdana,sans-serif!important;display:block}#netteBluescreen h1{font-size:18pt;font-weight:normal;text-shadow:1px 1px 0 rgba(0,0,0,.4);margin:.7em 0}#netteBluescreen h2{font:14pt/1.5 sans-serif!important;color:#888;margin:.6em 0}#netteBluescreen a{text-decoration:none;color:#328ADC;padding:2px 4px;margin:-2px -4px}#netteBluescreen a abbr{font-family:sans-serif;color:#BBB}#netteBluescreen h3{font:bold 10pt/1.5 Verdana,sans-serif!important;margin:1em 0;padding:0}#netteBluescreen p,#netteBluescreen pre{margin:.8em 0}#netteBluescreen pre,#netteBluescreen code,#netteBluescreen table{font:9pt/1.5 Consolas,monospace!important}#netteBluescreen pre,#netteBluescreen table{background:#FDF5CE;padding:.4em .7em;border:1px dotted silver;overflow:auto}#netteBluescreen table pre{padding:0;margin:0;border:none}#netteBluescreen pre.nette-dump span{color:#C22}#netteBluescreen pre.nette-dump a{color:#333}#netteBluescreen div.panel{padding:1px 25px}#netteBluescreen div.inner{background:#F4F3F1;padding:.1em 1em 1em;border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px}#netteBluescreen table{border-collapse:collapse;width:100%}#netteBluescreen .outer{overflow:auto}#netteBluescreen td,#netteBluescreen th{vertical-align:top;text-align:left;padding:2px 6px;border:1px solid #e6dfbf}#netteBluescreen th{width:10%;font-weight:bold}#netteBluescreen tr:nth-child(2n),#netteBluescreen tr:nth-child(2n) pre{background-color:#F7F0CB}#netteBluescreen ol{margin:1em 0;padding-left:2.5em}#netteBluescreen ul{font:7pt/1.5 Verdana,sans-serif!important;padding:2em 4em;margin:1em 0 0;color:#777;background:#F6F5F3 url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFEAAAAjCAMAAADbuxbOAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAADBQTFRF/fz24d7Y7Onj5uLd9vPu3drUzMvG09LN39zW8e7o2NbQ3NnT29jS0M7J1tXQAAAApvmsFgAAABB0Uk5T////////////////////AOAjXRkAAAKlSURBVHja7FbbsqQgDAwENEgc//9vN+SCWDtbtXPmZR/Wc6o02mlC58LA9ckFAOszvMV8xNgyUjyXhojfMVKvRL0ZHavxXYy5JrmchMdzou8YlTClxajtK8ZGGpWRoBr1+gFjKfHkJPbizabLgzE3pH7Iu4K980xgFvlrVzMZoVBWhtvouCDdcTDmTgMCJdVxJ9MKO6XxnliM7hxi5lbj2ZVM4l8DqYyKoNLYcfqBB1/LpHYxEcfVG6ZpMDgyFUVWY/Q1sSYPpIdSAKWqLWL0XqWiMWc4hpH0OQOMOAgdycY4N9Sb7wWANQs3rsDSdLAYiuxi5siVfOhBWIrtH0G3kNaF/8Q4kCPE1kMucG/ZMUBUCOgiKJkPuWWTLGVgLGpwns1DraUayCtoBqERyaYtVsm85NActRooezvSLO/sKZP/nq8n4+xcyjNsRu8zW6KWpdb7wjiQd4WrtFZYFiKHENSmWp6xshh96c2RQ+c7Lt+qbijyEjHWUJ/pZsy8MGIUuzNiPySK2Gqoh6ZTRF6ko6q3nVTkaA//itIrDpW6l3SLo8juOmqMXkYknu5FdQxWbhCfKHEGDhxxyTVaXJF3ZjSl3jMksjSOOKmne9pI+mcG5QvaUJhI9HpkmRo2NpCrDJvsktRhRE2MM6F2n7dt4OaMUq8bCctk0+PoMRzL+1l5PZ2eyM/Owr86gf8z/tOM53lom5+nVcFuB+eJVzlXwAYy9TZ9s537tfqcsJWbEU4nBngZo6FfO9T9CdhfBtmk2dLiAy8uS4zwOpMx2HqYbTC+amNeAYTpsP4SIgvWfUBWXxn3CMHW3ffd7k3+YIkx7w0t/CVGvcPejoeOlzOWzeGbawOHqXQGUTMZRcfj4XPCgW9y/fuvVn8zD9P1QHzv80uAAQA0i3Jer7Jr7gAAAABJRU5ErkJggg==') 99% 10px no-repeat;border-top:1px solid #DDD}#netteBluescreen .highlight{background:#CD1818;color:white;font-weight:bold;font-style:normal;display:block;padding:0 .4em;margin:0 -.4em}#netteBluescreen .line{color:#9F9C7F;font-weight:normal;font-style:normal}#netteBluescreen a[href^=editor\:]{color:inherit;border-bottom:1px dotted #328ADC}</style>


	<script type="text/javascript">/*<![CDATA[*/function netteToggle(c,e){for(var b=c.getElementsByTagName("abbr")[0],a=e?document.getElementById(e):c.nextSibling;a.nodeType!==1;)a=a.nextSibling;var d=a.currentStyle?a.currentStyle.display=="none":getComputedStyle(a,null).display=="none";try{b.innerHTML=String.fromCharCode(d?9660:9658)}catch(f){}a.style.display=d?a.tagName.toLowerCase()==="code"?"inline":"block":"none";if(c.id==="netteBluescreenIcon"){b=0;for(a=document.styleSheets;b<a.length;b++)if((a[b].owningElement||a[b].ownerNode).className!==
"nette")a[b].disabled=d?true:a[b].oldDisabled}return true};/*]]>*/</script>
</head>



<body>
<div id="netteBluescreen">
	<a id="netteBluescreenIcon" href="#" onclick="return !netteToggle(this)"><abbr>&#x25bc;</abbr></a

	><div>
		<div id="netteBluescreenError" class="panel">
			<h1><?php echo
htmlspecialchars($title),($exception->getCode()?' #'.$exception->getCode():'')?></h1>

			<p><?php echo
htmlspecialchars($exception->getMessage())?></p>
		</div>



		<?php $ex=$exception;$level=0;?>
		<?php do{?>

			<?php if($level++):?>
				<?php _netteOpenPanel('Caused by',$level>2)?>
				<div class="panel">
					<h1><?php echo
htmlspecialchars(get_class($ex)),($ex->getCode()?' #'.$ex->getCode():'')?></h1>

					<p><b><?php echo
htmlspecialchars($ex->getMessage())?></b></p>
				</div>
			<?php endif?>

			<?php $stack=$ex->getTrace();$expanded=NULL?>
			<?php if(strpos($ex->getFile(),$expandPath)===0){foreach($stack
as$key=>$row){if(isset($row['file'])&&strpos($row['file'],$expandPath)!==0){$expanded=$key;break;}}}?>
			<?php if(is_file($ex->getFile())):?>
			<?php _netteOpenPanel('Source file',$expanded!==NULL)?>
				<p><b>File:</b> <?php if(self::$editor)echo'<a href="',htmlspecialchars(strtr(self::$editor,array('%file'=>rawurlencode($ex->getFile()),'%line'=>$ex->getLine()))),'">'?>
				<?php echo
htmlspecialchars($ex->getFile()),(self::$editor?'</a>':'')?> &nbsp; <b>Line:</b> <?php echo$ex->getLine()?></p>
				<pre><?php _netteDebugPrintCode($ex->getFile(),$ex->getLine())?></pre>
			<?php _netteClosePanel()?>
			<?php endif?>



			<?php if(isset($stack[0]['class'])&&$stack[0]['class']==='NetteX\Debug'&&($stack[0]['function']==='_shutdownHandler'||$stack[0]['function']==='_errorHandler'))unset($stack[0])?>
			<?php if($stack):?>
			<?php _netteOpenPanel('Call stack',FALSE)?>
				<ol>
					<?php foreach($stack
as$key=>$row):?>
					<li><p>

					<?php if(isset($row['file'])&&is_file($row['file'])):?>
						<?php echo
self::$editor?'<a href="'.htmlspecialchars(strtr(self::$editor,array('%file'=>rawurlencode($row['file']),'%line'=>$row['line']))).'"':'<span';?> title="<?php echo
htmlSpecialChars($row['file'])?>">
						<?php echo
htmlSpecialChars(basename(dirname($row['file']))),'/<b>',htmlSpecialChars(basename($row['file'])),'</b>',(self::$editor?'</a>':'</span>'),' (',$row['line'],')'?>
					<?php else:?>
						<i>inner-code</i><?php if(isset($row['line']))echo' (',$row['line'],')'?>
					<?php endif?>

					<?php if(isset($row['file'])&&is_file($row['file'])):?><a href="#" onclick="return !netteToggle(this, 'netteBsSrc<?php echo"$level-$key"?>')">source <abbr>&#x25ba;</abbr></a>&nbsp; <?php endif?>

					<?php if(isset($row['class']))echo$row['class'].$row['type']?>
					<?php echo$row['function']?>

					(<?php if(!empty($row['args'])):?><a href="#" onclick="return !netteToggle(this, 'netteBsArgs<?php echo"$level-$key"?>')">arguments <abbr>&#x25ba;</abbr></a><?php endif?>)
					</p>

					<?php if(!empty($row['args'])):?>
						<div class="collapsed outer" id="netteBsArgs<?php echo"$level-$key"?>">
						<table>
						<?php

try{$r=isset($row['class'])?new\ReflectionMethod($row['class'],$row['function']):new\ReflectionFunction($row['function']);$params=$r->getParameters();}catch(\Exception$e){$params=array();}foreach($row['args']as$k=>$v){echo'<tr><th>',(isset($params[$k])?'$'.$params[$k]->name:"#$k"),'</th><td>';echo
_netteDump(self::_dump($v,0));echo"</td></tr>\n";}?>
						</table>
						</div>
					<?php endif?>


					<?php if(isset($row['file'])&&is_file($row['file'])):?>
						<pre <?php if($expanded!==$key)echo'class="collapsed"';?> id="netteBsSrc<?php echo"$level-$key"?>"><?php _netteDebugPrintCode($row['file'],$row['line'])?></pre>
					<?php endif?>

					</li>
					<?php endforeach?>
				</ol>
			<?php _netteClosePanel()?>
			<?php endif?>



			<?php if($ex
instanceof
IDebugPanel&&($tab=$ex->getTab())&&($panel=$ex->getPanel())):?>
			<?php _netteOpenPanel($tab,FALSE)?>
				<?php echo$panel?>
			<?php _netteClosePanel()?>
			<?php endif?>



			<?php if(isset($ex->context)&&is_array($ex->context)):?>
			<?php _netteOpenPanel('Variables',TRUE)?>
			<div class="outer">
			<table>
			<?php

foreach($ex->context
as$k=>$v){echo'<tr><th>$',htmlspecialchars($k),'</th><td>',_netteDump(self::_dump($v,0)),"</td></tr>\n";}?>
			</table>
			</div>
			<?php _netteClosePanel()?>
			<?php endif?>

		<?php }while((method_exists($ex,'getPrevious')&&$ex=$ex->getPrevious())||(isset($ex->previous)&&$ex=$ex->previous));?>
		<?php while(--$level)_netteClosePanel()?>



		<?php if(!empty($application)):?>
		<?php _netteOpenPanel('NetteX Application',TRUE)?>
			<h3>Requests</h3>
			<?php $tmp=$application->getRequests();echo
_netteDump(self::_dump($tmp,0))?>

			<h3>Presenter</h3>
			<?php $tmp=$application->getPresenter();echo
_netteDump(self::_dump($tmp,0))?>
		<?php _netteClosePanel()?>
		<?php endif?>



		<?php _netteOpenPanel('Environment',TRUE)?>
			<?php
$list=get_defined_constants(TRUE);if(!empty($list['user'])):?>
			<h3><a href="#" onclick="return !netteToggle(this, 'netteBsPnl-env-const')">Constants <abbr>&#x25bc;</abbr></a></h3>
			<div class="outer">
			<table id="netteBsPnl-env-const">
			<?php

foreach($list['user']as$k=>$v){echo'<tr><th>',htmlspecialchars($k),'</th>';echo'<td>',_netteDump(self::_dump($v,0)),"</td></tr>\n";}?>
			</table>
			</div>
			<?php endif?>


			<h3><a href="#" onclick="return !netteToggle(this, 'netteBsPnl-env-files')">Included files <abbr>&#x25ba;</abbr></a> (<?php echo
count(get_included_files())?>)</h3>
			<div class="outer">
			<table id="netteBsPnl-env-files" class="collapsed">
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
as$k=>$v)echo'<tr><th>',htmlspecialchars($k),'</th><td>',_netteDump(self::_dump($v,0)),"</td></tr>\n";?>
			</table>
			</div>
			<?php endif?>
		<?php _netteClosePanel()?>



		<?php _netteOpenPanel('HTTP request',TRUE)?>
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

foreach($GLOBALS[$name]as$k=>$v)echo'<tr><th>',htmlspecialchars($k),'</th><td>',_netteDump(self::_dump($v,0)),"</td></tr>\n";?>
			</table>
			</div>
			<?php endif?>
			<?php endforeach?>
		<?php _netteClosePanel()?>



		<?php _netteOpenPanel('HTTP response',TRUE)?>
			<h3>Headers</h3>
			<?php if(headers_list()):?>
			<pre><?php

foreach(headers_list()as$s)echo
htmlspecialchars($s),'<br>';?></pre>
			<?php else:?>
			<p><i>no headers</i></p>
			<?php endif?>
		<?php _netteClosePanel()?>


		<ul>
			<li>Report generated at <?php echo@date('Y/m/d H:i:s',self::$time)?></li>
			<?php if(preg_match('#^https?://#',self::$source)):?>
				<li><a href="<?php echo
htmlSpecialChars(self::$source)?>"><?php echo
htmlSpecialChars(self::$source)?></a></li>
			<?php elseif(self::$source):?>
				<li><?php echo
htmlSpecialChars(self::$source)?></li>
			<?php endif?>
			<li>PHP <?php echo
htmlSpecialChars(PHP_VERSION)?></li>
			<?php if(isset($_SERVER['SERVER_SOFTWARE'])):?><li><?php echo
htmlSpecialChars($_SERVER['SERVER_SOFTWARE'])?></li><?php endif?>
			<?php if(class_exists('NetteX\Framework')):?><li><?php echo
htmlSpecialChars('NetteX Framework '.Framework::VERSION)?> <i>(revision <?php echo
htmlSpecialChars(Framework::REVISION)?>)</i></li><?php endif?>
		</ul>
	</div>
</div>

<script type="text/javascript">/*<![CDATA[*/document.body.appendChild(document.getElementById("netteBluescreen"));document.onkeyup=function(a){a=a||window.event;if(a.keyCode==27)return document.getElementById("netteBluescreenIcon").onclick()};
for(var i=0,styles=document.styleSheets;i<styles.length;i++)if((styles[i].owningElement||styles[i].ownerNode).className!=="nette"){styles[i].oldDisabled=styles[i].disabled;styles[i].disabled=true}else styles[i].addRule?styles[i].addRule(".collapsed","display: none"):styles[i].insertRule(".collapsed { display: none }",0);/*]]>*/</script>
</body>
</html><?php }static
function
paintDebugBar(){$panels=array();foreach(self::$panels
as$panel){$panels[]=array('id'=>preg_replace('#[^a-z0-9]+#i','-',$panel->getId()),'tab'=>$tab=(string)$panel->getTab(),'panel'=>$tab?(string)$panel->getPanel():NULL);}?>




<!-- NetteX Debug Bar -->

<?php ob_start()?>
&nbsp;

<style id="nette-debug-style" class="nette">#nette-debug{display:none}body#nette-debug{margin:5px 5px 0;display:block}#nette-debug *{font:inherit;color:inherit;background:transparent;margin:0;padding:0;border:none;text-align:inherit;list-style:inherit}#nette-debug .nette-fixed-coords{position:fixed;_position:absolute;right:0;bottom:0}#nette-debug a{color:#125EAE;text-decoration:none}#nette-debug .nette-panel a{color:#125EAE;text-decoration:none}#nette-debug a:hover,#nette-debug a:active,#nette-debug a:focus{background-color:#125EAE;color:white}#nette-debug .nette-panel h2,#nette-debug .nette-panel h3,#nette-debug .nette-panel p{margin:.4em 0}#nette-debug .nette-panel table{border-collapse:collapse;background:#FDF5CE}#nette-debug .nette-panel tr:nth-child(2n) td{background:#F7F0CB}#nette-debug .nette-panel td,#nette-debug .nette-panel th{border:1px solid #E6DFBF;padding:2px 5px;vertical-align:top;text-align:left}#nette-debug .nette-panel th{background:#F4F3F1;color:#655E5E;font-size:90%;font-weight:bold}#nette-debug .nette-panel pre,#nette-debug .nette-panel code{font:9pt/1.5 Consolas,monospace}#nette-debug table .nette-right{text-align:right}.nette-hidden{display:none}#nette-debug-bar{font:normal normal 12px/21px Tahoma,sans-serif;color:#333;border:1px solid #c9c9c9;background:#EDEAE0 url('data:image/png;base64,R0lGODlhAQAVALMAAOTh1/Px6eHe1fHv5e/s4vLw6Ofk2u3q4PPw6PPx6PDt5PLw5+Dd1OXi2Ojm3Orn3iH5BAAAAAAALAAAAAABABUAAAQPMISEyhpYkfOcaQAgCEwEADs=') repeat-x bottom;position:relative;height:1.75em;min-height:21px;_float:left;min-width:50px;white-space:nowrap;z-index:23181;opacity:.9;border-radius:3px;-moz-border-radius:3px;box-shadow:1px 1px 10px rgba(0,0,0,.15);-moz-box-shadow:1px 1px 10px rgba(0,0,0,.15);-webkit-box-shadow:1px 1px 10px rgba(0,0,0,.15)}#nette-debug-bar:hover{opacity:1}#nette-debug-bar ul{list-style:none none;margin-left:4px}#nette-debug-bar li{float:left}#nette-debug-bar img{vertical-align:middle;position:relative;top:-1px;margin-right:3px}#nette-debug-bar li a{color:#000;display:block;padding:0 4px}#nette-debug-bar li a:hover{color:black;background:#c3c1b8}#nette-debug-bar li .nette-warning{color:#D32B2B;font-weight:bold}#nette-debug-bar li>span{padding:0 4px}#nette-debug-logo{background:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAPCAYAAABwfkanAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABiFJREFUSMe1VglPlGcQ5i+1xjZNqxREtGq8ahCPWsVGvEDBA1BBRQFBDjkE5BYUzwpovRBUREBEBbl3OVaWPfj2vi82eTrvbFHamLRJ4yYTvm+u95mZZ96PoKAv+LOatXBYZ+Bx6uFy6DGnt1m0EOKwSmQzwmHTgX5B/1W+yM9GYJ02CX6/B/5ZF+w2A4x6FYGTYDVp4PdY2Tbrs5N+mnRa2Km4/wV6rhPzQQj5fDc1mJM5nd0iYdZtQWtrCxobGnDpUiledTynbuvg99mgUMhw924Trl2rR01NNSTNJE9iDpTV8innv4K2kZPLroPXbYLHZeSu2K1aeF0muJ2GvwGzmNSwU2E+svm8ZrgdBliMaha/34Vx+RAKCgpwpa4OdbW1UE/L2cc/68WtWzdRVlaG6uoqtD1/BA/pA1MIxLvtes7pc5vhoDOE/rOgbVSdf9aJWa8dBp0Kyg+jdLiTx2vQKWEyqGmcNkqg4iTC1+dzQatWkK+cJqPD7KyFaKEjvRuNjY24fLkGdXW1ePjwAeX4QHonDNI0A75+/RpqqqshH+6F2UAUMaupYXouykV0mp6SQ60coxgL8Z4aMg/4x675/V60v3jKB+Xl5WJibIC4KPEIS0qKqWv5GOh7BZ/HSIk9kA33o7y8DOfPZ6GQOipkXDZAHXKxr4ipqqpkKS6+iIrycgz2dyMnJxtVlZUsotNZWZmor79KBbvgpdjm5sfIzc1hv4L8fKJPDTfJZZc+gRYKr8sAEy2DcBRdEEk62ltx9uwZ5qNILoDU1l6mbrvx5EkzUlKSuTiR7PHjR3x4fv4FyIbeIic7G5WVFUyN+qtX+Lnt2SPcvn2LfURjhF7kE4WPDr+Bx+NEUVEhkpNPoImm5CSOl5aUIC3tLOMR59gtAY4HidGIzj14cB8ZGRkM8kJeHk6cOI4xWR8vSl5uLlJTT6O74xnT5lB8PM6cSYXVqILb5UBWZiYSExMYkE4zzjqX00QHG+h9AjPqMei0k3ywy2khMdNiq6BVCf04T6ekuBgJCUdRUVHOBQwPvkNSUiLjaGi4Q/5qFgYtHgTXRJdTT59GenoaA5gY64deq0Bc3EGuNj4+DnppEheLijhZRkY6SktLsGPHdi6irOwSFTRAgO04deokTSIFsbExuHfvLnFSx8DevelAfFwcA0lJTqZi5PDS9aci/sbE7Oe4wsICbtD27b/ye1NTI3FeSX4W2gdFALRD3A4eM44ePcKViuD79/8gnZP5Kg4+cCAW2dnnqUM2Lujw4UM4ePAA2ztfPsHIYA/sdOt43A50d7UFCjkUj+joXVBMDJDeDhcVk08cjd61C3v37uFYp8PKXX3X8xJRUTtw7FgSn3Xzxg10d7ZCqRjkM+02C7pettDNogqAFjzxuI3YHR2Nffv2coXy0V44HGZERm7kJNu2/cK8bW9rwbp1axnMnj27uUijQQOb1QyTcYZ3YMOGn/Hbzp1crAAvaDfY38O5hW3//n0ce+TIYWiUcub1xo0R2Lp1y8cYsUMWM125VhPe93Zj7do1vEPi26GfUdBFbhK8tGHrli1YsWwpgoOD0dXRQqAtXMCy8DBs3rwJoSGLsWrVclylBdoUGYlVK1dg9eqVCFsSSs8/4btvvmUwEnE0KTERISE/IiIiAsGLF2HhwgU8qbc97QgPX8qFr1mzGgu+/opzdL5o5l1aEhqC9evXYWlYKFYsD6e/YVj0w/dMGZVyBDMqeaDTRuKpkxYjIz2dOyeup6H3r2kkOuJ1H3N5Z1QUzp3LQF9vJ4xGLQYHXiM9LY0pEhsTg+PHj9HNcJu4OcL3uaQZY86LiZw8mcJTkmhBTUYJbU8fcoygobgWR4Z6iKtTPLE7d35HYkICT1dIZuY59HQ9412StBPQTMvw8Z6WaMNFxy3Gab4TeQT0M9IHwUT/G0i0MGIJ9CTiJjBIH+iQaQbC7+QnfEXiQL6xgF09TjETHCt8RbeMuil+D8RNsV1LHdQoZfR/iJJzCZuYmEE/Bd3MJNs/+0UURgFWJJ//aQ8k+CsxVTqnVytHObkQrUoG8T4/bs4u4ubbxLPwFzYNPc8HI2zijLm84l39Dx8hfwJenFezFBKKQwAAAABJRU5ErkJggg==') 0 50% no-repeat;min-width:45px;cursor:move}#nette-debug-logo span{display:none}#nette-debug-bar-bgl,#nette-debug-bar-bgx,#nette-debug-bar-bgr{position:absolute;z-index:-1;top:-7px;height:37px}#nette-debug .nette-panel{font:normal normal 12px/1.5 sans-serif;background:white;color:#333}#nette-debug h1{font:normal normal 23px/1.4 Tahoma,sans-serif;color:#575753;background:#EDEAE0;margin:-5px -5px 5px;padding:0 25px 5px 5px}#nette-debug .nette-mode-peek .nette-inner,#nette-debug .nette-mode-float .nette-inner{max-width:700px;max-height:500px;overflow:auto}#nette-debug .nette-panel .nette-icons{display:none}#nette-debug .nette-mode-peek{display:none;position:relative;z-index:23180;padding:5px;min-width:150px;min-height:50px;border:5px solid #EDEAE0;border-radius:5px;-moz-border-radius:5px}#nette-debug .nette-mode-peek h1{cursor:move}#nette-debug .nette-mode-float{position:relative;z-index:23179;padding:5px;min-width:150px;min-height:50px;border:5px solid #EDEAE0;border-radius:5px;-moz-border-radius:5px;opacity:.9;box-shadow:1px 1px 6px #666;-moz-box-shadow:1px 1px 6px rgba(0,0,0,.45);-webkit-box-shadow:1px 1px 6px #666}#nette-debug .nette-focused{z-index:23180;opacity:1}#nette-debug .nette-mode-float h1{cursor:move}#nette-debug .nette-mode-float .nette-icons{display:block;position:absolute;top:0;right:0;font-size:18px}#nette-debug .nette-icons a{color:#575753}#nette-debug .nette-icons a:hover{color:white}</style>

<!--[if lt IE 8]><style class="nette">#nette-debug-bar img{display:none}#nette-debug-bar li{border-left:1px solid #DCD7C8;padding:0 3px}#nette-debug-logo span{background:#edeae0;display:inline}</style><![endif]-->


<script id="nette-debug-script">/*<![CDATA[*/var NetteX=NetteX||{};
(function(){NetteX.Class=function(a){var b=a.constructor||function(){},c;delete a.constructor;if(a.Extends){var d=function(){this.constructor=b};d.prototype=a.Extends.prototype;b.prototype=new d;delete a.Extends}if(a.Static){for(c in a.Static)b[c]=a.Static[c];delete a.Static}for(c in a)b.prototype[c]=a[c];return b};NetteX.Q=NetteX.Class({Static:{factory:function(a){return new NetteX.Q(a)},implement:function(a){var b,c=NetteX.Q.implement,d=NetteX.Q.prototype;for(b in a){c[b]=a[b];d[b]=function(f){return function(){return this.each(c[f],
arguments)}}(b)}}},constructor:function(a){if(typeof a==="string")a=this._find(document,a);else if(!a||a.nodeType||a.length===void 0||a===window)a=[a];for(var b=0,c=a.length;b<c;b++)if(a[b])this[this.length++]=a[b]},length:0,find:function(a){return new NetteX.Q(this._find(this[0],a))},_find:function(a,b){if(!a||!b)return[];else if(document.querySelectorAll)return a.querySelectorAll(b);else if(b.charAt(0)==="#")return[document.getElementById(b.substring(1))];else{b=b.split(".");var c=a.getElementsByTagName(b[0]||
"*");if(b[1]){for(var d=[],f=RegExp("(^|\\s)"+b[1]+"(\\s|$)"),i=0,k=c.length;i<k;i++)f.test(c[i].className)&&d.push(c[i]);return d}else return c}},dom:function(){return this[0]},each:function(a,b){for(var c=0,d;c<this.length;c++)if((d=a.apply(this[c],b||[]))!==void 0)return d;return this}});var h=NetteX.Q.factory,e=NetteX.Q.implement;e({bind:function(a,b){if(document.addEventListener&&(a==="mouseenter"||a==="mouseleave")){var c=b;a=a==="mouseenter"?"mouseover":"mouseout";b=function(g){for(var j=g.relatedTarget;j;j=
j.parentNode)if(j===this)return;c.call(this,g)}}var d=e.data.call(this);d=d.events=d.events||{};if(!d[a]){var f=this,i=d[a]=[],k=e.bind.genericHandler=function(g){if(!g.target)g.target=g.srcElement;if(!g.preventDefault)g.preventDefault=function(){g.returnValue=false};if(!g.stopPropagation)g.stopPropagation=function(){g.cancelBubble=true};g.stopImmediatePropagation=function(){this.stopPropagation();j=i.length};for(var j=0;j<i.length;j++)i[j].call(f,g)};if(document.addEventListener)this.addEventListener(a,
k,false);else document.attachEvent&&this.attachEvent("on"+a,k)}d[a].push(b)},addClass:function(a){this.className=this.className.replace(/^|\s+|$/g," ").replace(" "+a+" "," ")+" "+a},removeClass:function(a){this.className=this.className.replace(/^|\s+|$/g," ").replace(" "+a+" "," ")},hasClass:function(a){return this.className.replace(/^|\s+|$/g," ").indexOf(" "+a+" ")>-1},show:function(){var a=e.show.display=e.show.display||{},b=this.tagName;if(!a[b]){var c=document.body.appendChild(document.createElement(b));
a[b]=e.css.call(c,"display")}this.style.display=a[b]},hide:function(){this.style.display="none"},css:function(a){return this.currentStyle?this.currentStyle[a]:window.getComputedStyle?document.defaultView.getComputedStyle(this,null).getPropertyValue(a):void 0},data:function(){return this.nette=this.nette||{}},val:function(){if(!this.nodeName){for(var a=0,b=this.length;a<b;a++)if(this[a].checked)return this[a].value;return null}if(this.nodeName.toLowerCase()==="select"){a=this.selectedIndex;var c=this.options;
if(a<0)return null;else if(this.type==="select-one")return c[a].value;a=0;var d=[];for(b=c.length;a<b;a++)c[a].selected&&d.push(c[a].value);return d}if(this.type==="checkbox")return this.checked;return this.value.replace(/^\s+|\s+$/g,"")},_trav:function(a,b,c){for(b=b.split(".");a&&!(a.nodeType===1&&(!b[0]||a.tagName.toLowerCase()===b[0])&&(!b[1]||e.hasClass.call(a,b[1])));)a=a[c];return h(a)},closest:function(a){return e._trav(this,a,"parentNode")},prev:function(a){return e._trav(this.previousSibling,
a,"previousSibling")},next:function(a){return e._trav(this.nextSibling,a,"nextSibling")},offset:function(a){for(var b=this,c=a?{left:-a.left||0,top:-a.top||0}:e.position.call(b);b=b.offsetParent;){c.left+=b.offsetLeft;c.top+=b.offsetTop}if(a)e.position.call(this,{left:-c.left,top:-c.top});else return c},position:function(a){if(a){this.nette&&this.nette.onmove&&this.nette.onmove.call(this,a);this.style.left=(a.left||0)+"px";this.style.top=(a.top||0)+"px"}else return{left:this.offsetLeft,top:this.offsetTop,
width:this.offsetWidth,height:this.offsetHeight}},draggable:function(a){var b=h(this),c=document.documentElement,d;a=a||{};h(a.handle||this).bind("mousedown",function(f){f.preventDefault();f.stopPropagation();if(e.draggable.binded)return c.onmouseup(f);var i=b[0].offsetLeft-f.clientX,k=b[0].offsetTop-f.clientY;e.draggable.binded=true;d=false;c.onmousemove=function(g){g=g||event;if(!d){a.draggedClass&&b.addClass(a.draggedClass);a.start&&a.start(g,b);d=true}b.position({left:g.clientX+i,top:g.clientY+
k});return false};c.onmouseup=function(g){if(d){a.draggedClass&&b.removeClass(a.draggedClass);a.stop&&a.stop(g||event,b)}e.draggable.binded=c.onmousemove=c.onmouseup=null;return false}}).bind("click",function(f){if(d){f.stopImmediatePropagation();preventClick=false}})}})})();
(function(){NetteX.Debug={};var h=NetteX.Q.factory,e=NetteX.Debug.Panel=NetteX.Class({Extends:NetteX.Q,Static:{PEEK:"nette-mode-peek",FLOAT:"nette-mode-float",WINDOW:"nette-mode-window",FOCUSED:"nette-focused",factory:function(a){return new e(a)},_toggle:function(a){var b=a.rel;b=b.charAt(0)==="#"?h(b):h(a)[b.charAt(0)==="<"?"prev":"next"](b.substring(1));if(b.css("display")==="none"){b.show();a.innerHTML=a.innerHTML.replace("\u25ba","\u25bc")}else{b.hide();a.innerHTML=a.innerHTML.replace("\u25bc","\u25ba")}}},
constructor:function(a){NetteX.Q.call(this,"#nette-debug-panel-"+a.replace("nette-debug-panel-",""))},reposition:function(){if(this.hasClass(e.WINDOW))window.resizeBy(document.documentElement.scrollWidth-document.documentElement.clientWidth,document.documentElement.scrollHeight-document.documentElement.clientHeight);else{this.position(this.position());if(this.position().width)document.cookie=this.dom().id+"="+this.position().left+":"+this.position().top+"; path=/"}},focus:function(){if(this.hasClass(e.WINDOW))this.data().win.focus();
else{clearTimeout(this.data().blurTimeout);this.addClass(e.FOCUSED).show()}},blur:function(){this.removeClass(e.FOCUSED);if(this.hasClass(e.PEEK)){var a=this;this.data().blurTimeout=setTimeout(function(){a.hide()},50)}},toFloat:function(){this.removeClass(e.WINDOW).removeClass(e.PEEK).addClass(e.FLOAT).show().reposition();return this},toPeek:function(){this.removeClass(e.WINDOW).removeClass(e.FLOAT).addClass(e.PEEK).hide();document.cookie=this.dom().id+"=; path=/"},toWindow:function(){var a=this,
b,c;c=this.offset();var d=this.dom().id;c.left+=typeof window.screenLeft==="number"?window.screenLeft:window.screenX+10;c.top+=typeof window.screenTop==="number"?window.screenTop:window.screenY+50;if(b=window.open("",d.replace(/-/g,"_"),"left="+c.left+",top="+c.top+",width="+c.width+",height="+(c.height+15)+",resizable=yes,scrollbars=yes")){c=b.document;c.write('<!DOCTYPE html><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>'+h("#nette-debug-style").dom().innerHTML+"</style><script>"+
h("#nette-debug-script").dom().innerHTML+'<\/script><body id="nette-debug">');c.body.innerHTML='<div class="nette-panel nette-mode-window" id="'+d+'">'+this.dom().innerHTML+"</div>";b.NetteX.Debug.Panel.factory(d).initToggler().reposition();c.title=a.find("h1").dom().innerHTML;h([b]).bind("unload",function(){a.toPeek();b.close()});h(c).bind("keyup",function(f){f.keyCode===27&&b.close()});document.cookie=d+"=window; path=/";this.hide().removeClass(e.FLOAT).removeClass(e.PEEK).addClass(e.WINDOW).data().win=
b}},init:function(){var a=this,b;a.data().onmove=function(c){var d=document,f=window.innerWidth||d.documentElement.clientWidth||d.body.clientWidth;d=window.innerHeight||d.documentElement.clientHeight||d.body.clientHeight;c.left=Math.max(Math.min(c.left,0.8*this.offsetWidth),0.2*this.offsetWidth-f);c.top=Math.max(Math.min(c.top,0.8*this.offsetHeight),this.offsetHeight-d)};h(window).bind("resize",function(){a.reposition()});a.draggable({handle:a.find("h1"),stop:function(){a.toFloat()}}).bind("mouseenter",
function(){a.focus()}).bind("mouseleave",function(){a.blur()});this.initToggler();a.find(".nette-icons").find("a").bind("click",function(c){this.rel==="close"?a.toPeek():a.toWindow();c.preventDefault()});if(b=document.cookie.match(RegExp(a.dom().id+"=(window|(-?[0-9]+):(-?[0-9]+))")))b[2]?a.toFloat().position({left:b[2],top:b[3]}):a.toWindow();else a.addClass(e.PEEK)},initToggler:function(){var a=this;this.bind("click",function(b){var c=h(b.target).closest("a"),d=c.dom();if(d&&c.hasClass("nette-toggler")){e._toggle(d);
b.preventDefault();a.reposition()}});return this}});NetteX.Debug.Bar=NetteX.Class({Extends:NetteX.Q,constructor:function(){NetteX.Q.call(this,"#nette-debug-bar")},init:function(){var a=this,b;a.data().onmove=function(c){var d=document,f=window.innerWidth||d.documentElement.clientWidth||d.body.clientWidth;d=window.innerHeight||d.documentElement.clientHeight||d.body.clientHeight;c.left=Math.max(Math.min(c.left,0),this.offsetWidth-f);c.top=Math.max(Math.min(c.top,0),this.offsetHeight-d)};h(window).bind("resize",
function(){a.position(a.position())});a.draggable({draggedClass:"nette-dragged",stop:function(){document.cookie=a.dom().id+"="+a.position().left+":"+a.position().top+"; path=/"}});a.find("a").bind("click",function(c){if(this.rel==="close"){h("#nette-debug").hide();window.opera&&h("body").show()}else if(this.rel){var d=e.factory(this.rel);if(c.shiftKey)d.toFloat().toWindow();else if(d.hasClass(e.FLOAT)){var f=h(this).offset();d.offset({left:f.left-d.position().width+f.width+4,top:f.top-d.position().height-
4}).toPeek()}else d.toFloat().position({left:d.position().left-Math.round(Math.random()*100)-20,top:d.position().top-Math.round(Math.random()*100)-20}).reposition()}c.preventDefault()}).bind("mouseenter",function(){if(!(!this.rel||this.rel==="close"||a.hasClass("nette-dragged"))){var c=e.factory(this.rel);c.focus();if(c.hasClass(e.PEEK)){var d=h(this).offset();c.offset({left:d.left-c.position().width+d.width+4,top:d.top-c.position().height-4})}}}).bind("mouseleave",function(){!this.rel||this.rel===
"close"||a.hasClass("nette-dragged")||e.factory(this.rel).blur()});if(b=document.cookie.match(RegExp(a.dom().id+"=(-?[0-9]+):(-?[0-9]+)")))a.position({left:b[1],top:b[2]});a.find("a").each(function(){!this.rel||this.rel==="close"||e.factory(this.rel).init()})}})})();/*]]>*/</script>


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
			<li id="nette-debug-logo">&nbsp;<span>NetteX Framework</span></li>
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
tryError(){if(!self::$enabled&&self::$lastError===FALSE){set_error_handler(array(__CLASS__,'_errorHandler'));}self::$lastError=NULL;}static
function
catchError(&$error){if(!self::$enabled&&self::$lastError!==FALSE){restore_error_handler();}$error=self::$lastError;self::$lastError=FALSE;return(bool)$error;}private
static
function
defaultMailer($message){$host=isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'');$parts=str_replace(array("\r\n","\n"),array("\n",PHP_EOL),array('headers'=>"From: noreply@$host\nX-Mailer: NetteX Framework\n",'subject'=>"PHP: An error occurred on the server $host",'body'=>"[".@date('Y-m-d H:i:s')."] $message"));mail(self::$email,$parts['subject'],$parts['body'],$parts['headers']);}static
function
addPanel(IDebugPanel$panel){self::$panels[]=$panel;}static
function
renderTab($id){switch($id){case'time':?>
<span title="Execution time"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJ6SURBVDjLjZO7T1NhGMY7Mji6uJgYt8bElTjof6CDg4sMSqIxJsRGB5F4TwQSIg1QKC0KWmkZEEsKtEcSxF5ohV5pKSicXqX3aqGn957z+PUEGopiGJ583/A+v3znvPkJAAjWR0VNJG0kGhKahCFhXcN3YBFfx8Kry6ym4xIzce88/fbWGY2k5WRb77UTTbWuYA9gDGg7EVmSIOF4g5T7HZKuMcSW5djWDyL0uRf0dCc8inYYxTcw9fAiCMBYB3gVj1z7gLhNTjKCqHkYP79KENC9Bq3uxrrqORzy+9D3tPAAccspVx1gWg0KbaZFbGllWFM+xrKkFQudV0CeDfJsjN4+C2nracjunoPq5VXIBrowMK4V1gG1LGyWdbZwCalsBYUyh2KFQzpXxVqkAGswD3+qBDpZwow9iYE5v26/VwfUQnnznyhvjguQYabIIpKpYD1ahI8UTT92MUSFuP5Z/9TBTgOgFrVjp3nakaG/0VmEfpX58pwzjUEquNk362s+PP8XYD/KpYTBHmRg9Wch0QX1R80dCZhYipudYQY2Auib8RmODVCa4hfUK4ngaiiLNFNFdKeCWWscXZMbWy9Unv9/gsIQU09a4pwvUeA3Uapy2C2wCKXL0DqTePLexbWPOv79E8f0UWrencZ2poxciUWZlKssB4bcHeE83NsFuMgpo2iIpMuNa1TNu4XjhggWvb+R2K3wZdLlAZl8Fd9jRb5sD+Xx0RJBx5gdom6VsMEFDyWF0WyCeSOFcDKPnRxZYTQL5Rc/nn1w4oFsBaIhC3r6FRh5erPRhYMyHdeFw4C6zkRhmijM7CnMu0AUZonCDCnRJBqSus5/ABD6Ba5CkQS8AAAAAElFTkSuQmCC"
/><?php echo
number_format((microtime(TRUE)-self::$time)*1000,1,'.',' ')?>ms</span>
<?php
return;case'memory':?>
<span title="The peak of allocated memory"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAGvSURBVDjLpZO7alZREEbXiSdqJJDKYJNCkPBXYq12prHwBezSCpaidnY+graCYO0DpLRTQcR3EFLl8p+9525xgkRIJJApB2bN+gZmqCouU+NZzVef9isyUYeIRD0RTz482xouBBBNHi5u4JlkgUfx+evhxQ2aJRrJ/oFjUWysXeG45cUBy+aoJ90Sj0LGFY6anw2o1y/mK2ZS5pQ50+2XiBbdCvPk+mpw2OM/Bo92IJMhgiGCox+JeNEksIC11eLwvAhlzuAO37+BG9y9x3FTuiWTzhH61QFvdg5AdAZIB3Mw50AKsaRJYlGsX0tymTzf2y1TR9WwbogYY3ZhxR26gBmocrxMuhZNE435FtmSx1tP8QgiHEvj45d3jNlONouAKrjjzWaDv4CkmmNu/Pz9CzVh++Yd2rIz5tTnwdZmAzNymXT9F5AtMFeaTogJYkJfdsaaGpyO4E62pJ0yUCtKQFxo0hAT1JU2CWNOJ5vvP4AIcKeao17c2ljFE8SKEkVdWWxu42GYK9KE4c3O20pzSpyyoCx4v/6ECkCTCqccKorNxR5uSXgQnmQkw2Xf+Q+0iqQ9Ap64TwAAAABJRU5ErkJggg=="
/><?php echo
function_exists('memory_get_peak_usage')?number_format(memory_get_peak_usage()/1000,1,'.',' '):'n/a';?> kB</span>
<?php
return;case'dumps':if(!Debug::$dumps)return;?>
<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIASURBVDjLpVPPaxNREJ6Vt01caH4oWk1T0ZKlGIo9RG+BUsEK4kEP/Q8qPXnpqRdPBf8A8Wahhx7FQ0GF9FJ6UksqwfTSBDGyB5HkkphC9tfb7jfbtyQQTx142byZ75v5ZnZWC4KALmICPy+2DkvKIX2f/POz83LxCL7nrz+WPNcll49DrhM9v7xdO9JW330DuXrrqkFSgig5iR2Cfv3t3gNxOnv5BwU+eZ5HuON5/PMPJZKJ+yKQfpW0S7TxdC6WJaWkyvff1LDaFRAeLZj05MHsiPTS6hua0PUqtwC5sHq9zv9RYWl+nu5cETcnJ1M0M5WlWq3GsX6/T+VymRzHDluZiGYAAsw0TQahV8uyyGq1qFgskm0bHIO/1+sx1rFtchJhArwEyIQ1Gg2WD2A6nWawHQJVDIWgIJfLhQowTIeE9D0mKAU8qPC0220afsWFQoH93W6X7yCDJ+DEBeBmsxnPIJVKxWQVUwry+XyUwBlKMKwA8jqdDhOVCqVAzQDVvXAXhOdGBFgymYwrGoZBmUyGjxCCdF0fSahaFdgoTHRxfTveMCXvWfkuE3Y+f40qhgT/nMitupzApdvT18bu+YeDQwY9Xl4aG9/d/URiMBhQq/dvZMeVghtT17lSZW9/rAKsvPa/r9Fc2dw+Pe0/xI6kM9mT5vtXy+Nw2kU/5zOGRpvuMIu0YAAAAABJRU5ErkJggg==" />variables
<?php
return;case'errors':if(!Debug::$errors)return;?>
<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIsSURBVDjLpVNLSJQBEP7+h6uu62vLVAJDW1KQTMrINQ1vPQzq1GOpa9EppGOHLh0kCEKL7JBEhVCHihAsESyJiE4FWShGRmauu7KYiv6Pma+DGoFrBQ7MzGFmPr5vmDFIYj1mr1WYfrHPovA9VVOqbC7e/1rS9ZlrAVDYHig5WB0oPtBI0TNrUiC5yhP9jeF4X8NPcWfopoY48XT39PjjXeF0vWkZqOjd7LJYrmGasHPCCJbHwhS9/F8M4s8baid764Xi0Ilfp5voorpJfn2wwx/r3l77TwZUvR+qajXVn8PnvocYfXYH6k2ioOaCpaIdf11ivDcayyiMVudsOYqFb60gARJYHG9DbqQFmSVNjaO3K2NpAeK90ZCqtgcrjkP9aUCXp0moetDFEeRXnYCKXhm+uTW0CkBFu4JlxzZkFlbASz4CQGQVBFeEwZm8geyiMuRVntzsL3oXV+YMkvjRsydC1U+lhwZsWXgHb+oWVAEzIwvzyVlk5igsi7DymmHlHsFQR50rjl+981Jy1Fw6Gu0ObTtnU+cgs28AKgDiy+Awpj5OACBAhZ/qh2HOo6i+NeA73jUAML4/qWux8mt6NjW1w599CS9xb0mSEqQBEDAtwqALUmBaG5FV3oYPnTHMjAwetlWksyByaukxQg2wQ9FlccaK/OXA3/uAEUDp3rNIDQ1ctSk6kHh1/jRFoaL4M4snEMeD73gQx4M4PsT1IZ5AfYH68tZY7zv/ApRMY9mnuVMvAAAAAElFTkSuQmCC"
/><span class="nette-warning"><?php echo
array_sum(self::$errors)?> errors</span>
<?php }}static
function
renderPanel($id){switch($id){case'dumps':if(!function_exists('NetteX\_netteDumpCb2')){function
_netteDumpCb2($m){return"$m[1]<a href='#' class='nette-toggler'>$m[2]($m[3]) ".($m[3]<7?'<abbr>&#x25bc;</abbr> </a><code>':'<abbr>&#x25ba;</abbr> </a><code class="nette-hidden">');}}?>
<style>#nette-debug-dumps h2{font:11pt/1.5 sans-serif;margin:0;padding:2px 8px;background:#3484d2;color:white}#nette-debug-dumps table{width:100%}#nette-debug #nette-debug-dumps a{color:#333;background:transparent}#nette-debug-dumps a abbr{font-family:sans-serif;color:#999}#nette-debug-dumps pre.nette-dump span{color:#c16549}</style>


<h1>Dumped variables</h1>

<div class="nette-inner">
<?php foreach(self::$dumps
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
		<td><pre class="nette-dump"><?php echo
preg_replace_callback('#^( *)((?>[^(]{1,200}))\((\d+)\) <code>#m','NetteX\_netteDumpCb2',$dump)?></pre></td>
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
<?php foreach(self::$errors
as$item=>$count):list($message,$file,$line)=explode('|',$item)?>
<tr class="<?php echo$i++%
2?'nette-alt':''?>">
	<td class="nette-right"><?php echo$count?"$count\xC3\x97":''?></td>
	<td><pre><?php echo
htmlspecialchars($message),' in ',(self::$editor?'<a href="'.htmlspecialchars(strtr(self::$editor,array('%file'=>rawurlencode($file),'%line'=>$line))).'">':''),htmlspecialchars(($relative?str_replace($relative,"...",$file):$file)),':',$line,(self::$editor?'</a>':'')?></pre></td>
</tr>
<?php endforeach?>
</table>
</div><?php }}static
function
fireLog($message){if(self::$productionMode){return;}elseif(!self::$firebugDetected||headers_sent()){return
FALSE;}static$payload=array('logs'=>array());$item=array('name'=>'PHP','level'=>'debug','order'=>count($payload['logs']),'time'=>str_pad(number_format((microtime(TRUE)-self::$time)*1000,1,'.',' '),8,'0',STR_PAD_LEFT).' ms','template'=>'','message'=>'','style'=>'background:#767ab6');$args=func_get_args();if(isset($args[0])&&is_string($args[0])){$item['template']=array_shift($args);}if(isset($args[0])&&$args[0]instanceof\Exception){$e=array_shift($args);$trace=$e->getTrace();if(isset($trace[0]['class'])&&$trace[0]['class']===__CLASS__&&($trace[0]['function']==='_shutdownHandler'||$trace[0]['function']==='_errorHandler')){unset($trace[0]);}$item['exc_info']=array($e->getMessage(),$e->getFile(),array());$item['exc_frames']=array();foreach($trace
as$frame){$frame+=array('file'=>null,'line'=>null,'class'=>null,'type'=>null,'function'=>null,'object'=>null,'args'=>null);$item['exc_info'][2][]=array($frame['file'],$frame['line'],"$frame[class]$frame[type]$frame[function]",$frame['object']);$item['exc_frames'][]=$frame['args'];};$file=str_replace(dirname(dirname(dirname($e->getFile()))),"\xE2\x80\xA6",$e->getFile());$item['template']=($e
instanceof\ErrorException?'':get_class($e).': ').$e->getMessage().($e->getCode()?' #'.$e->getCode():'').' in '.$file.':'.$e->getLine();array_unshift($trace,array('file'=>$e->getFile(),'line'=>$e->getLine()));}else{$trace=debug_backtrace();if(isset($trace[0]['class'])&&$trace[0]['class']===__CLASS__&&($trace[0]['function']==='_shutdownHandler'||$trace[0]['function']==='_errorHandler')){unset($trace[0]);}}if(isset($args[0])&&in_array($args[0],array(self::DEBUG,self::INFO,self::WARNING,self::ERROR,self::CRITICAL),TRUE)){$item['level']=array_shift($args);}$item['args']=$args;foreach($trace
as$frame){if(isset($frame['file'])&&is_file($frame['file'])){$item['pathname']=$frame['file'];$item['lineno']=$frame['line'];break;}}$payload['logs'][]=$item;foreach(str_split(base64_encode(@json_encode($payload)),4990)as$k=>$v){header("FireLogger-de11e-$k:$v");}return
TRUE;}private
static
function
fireDump(&$var,$level=0){if(is_bool($var)||is_null($var)||is_int($var)||is_float($var)){return$var;}elseif(is_string($var)){if(self::$maxLen&&strlen($var)>self::$maxLen){$var=substr($var,0,self::$maxLen)." \xE2\x80\xA6 ";}return@iconv('UTF-16','UTF-8//IGNORE',iconv('UTF-8','UTF-16//IGNORE',$var));}elseif(is_array($var)){static$marker;if($marker===NULL)$marker=uniqid("\x00",TRUE);if(isset($var[$marker])){return"\xE2\x80\xA6RECURSION\xE2\x80\xA6";}elseif($level<self::$maxDepth||!self::$maxDepth){$var[$marker]=TRUE;$res=array();foreach($var
as$k=>&$v){if($k!==$marker)$res[self::fireDump($k)]=self::fireDump($v,$level+1);}unset($var[$marker]);return$res;}else{return" \xE2\x80\xA6 ";}}elseif(is_object($var)){$arr=(array)$var;static$list=array();if(in_array($var,$list,TRUE)){return"\xE2\x80\xA6RECURSION\xE2\x80\xA6";}elseif($level<self::$maxDepth||!self::$maxDepth){$list[]=$var;$res=array(" \xC2\xBBclass\xC2\xAB"=>get_class($var));foreach($arr
as$k=>&$v){if($k[0]==="\x00"){$k=substr($k,strrpos($k,"\x00")+1);}$res[self::fireDump($k)]=self::fireDump($v,$level+1);}array_pop($list);return$res;}else{return" \xE2\x80\xA6 ";}}elseif(is_resource($var)){return"resource ".get_resource_type($var);}else{return"unknown type";}}}Debug::_init();use
NetteX\Config\Config;class
Configurator
extends
Object{public$defaultConfigFile='%appDir%/config.ini';public$defaultServices=array('NetteX\\Application\\Application'=>array(__CLASS__,'createApplication'),'NetteX\\Web\\HttpContext'=>'NetteX\Web\HttpContext','NetteX\\Web\\IHttpRequest'=>'NetteX\Web\HttpRequest','NetteX\\Web\\IHttpResponse'=>'NetteX\Web\HttpResponse','NetteX\\Web\\IUser'=>'NetteX\Web\User','NetteX\\Caching\\ICacheStorage'=>array(__CLASS__,'createCacheStorage'),'NetteX\\Caching\\ICacheJournal'=>array(__CLASS__,'createCacheJournal'),'NetteX\\Web\\Session'=>'NetteX\Web\Session','NetteX\\Loaders\\RobotLoader'=>array(__CLASS__,'createRobotLoader'));function
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
Config){$config=$file;$file=NULL;}else{if($file===NULL){$file=$this->defaultConfigFile;}$file=Environment::expand($file);$config=Config::fromFile($file,$name);}if($config->variable
instanceof
Config){foreach($config->variable
as$key=>$value){Environment::setVariable($key,$value);}}$iterator=new\RecursiveIteratorIterator($config);foreach($iterator
as$key=>$value){$tmp=$iterator->getDepth()?$iterator->getSubIterator($iterator->getDepth()-1)->current():$config;$tmp[$key]=Environment::expand($value);}$runServices=array();$context=Environment::getContext();if($config->service
instanceof
Config){foreach($config->service
as$key=>$value){$key=strtr($key,'-','\\');if(is_string($value)){$context->removeService($key);$context->addService($key,$value);}else{if($value->factory){$context->removeService($key);$context->addService($key,$value->factory,isset($value->singleton)?$value->singleton:TRUE,(array)$value->option);}elseif(isset($this->defaultServices[$key])){$context->removeService($key);$context->addService($key,$this->defaultServices[$key],isset($value->singleton)?$value->singleton:TRUE,(array)$value->option);}if($value->run){$runServices[]=$key;}}}}if(!$config->php){$config->php=$config->set;unset($config->set);}if($config->php
instanceof
Config){if(PATH_SEPARATOR!==';'&&isset($config->php->include_path)){$config->php->include_path=str_replace(';',PATH_SEPARATOR,$config->php->include_path);}foreach(clone$config->php
as$key=>$value){if($value
instanceof
Config){unset($config->php->$key);foreach($value
as$k=>$v){$config->php->{"$key.$k"}=$v;}}}foreach($config->php
as$key=>$value){$key=strtr($key,'-','.');if(!is_scalar($value)){throw
new\XInvalidStateException("Configuration value for directive '$key' is not scalar.");}if($key==='date.timezone'){date_default_timezone_set($value);}if(function_exists('ini_set')){ini_set($key,$value);}else{switch($key){case'include_path':set_include_path($value);break;case'iconv.internal_encoding':iconv_set_encoding('internal_encoding',$value);break;case'mbstring.internal_encoding':mb_internal_encoding($value);break;case'date.timezone':date_default_timezone_set($value);break;case'error_reporting':error_reporting($value);break;case'ignore_user_abort':ignore_user_abort($value);break;case'max_execution_time':set_time_limit($value);break;default:if(ini_get($key)!=$value){throw
new\XNotSupportedException('Required function ini_set() is disabled.');}}}}}if($config->const
instanceof
Config){foreach($config->const
as$key=>$value){define($key,$value);}}if(isset($config->mode)){foreach($config->mode
as$mode=>$state){Environment::setMode($mode,$state);}}foreach($runServices
as$name){$context->getService($name);}return$config;}function
createContext(){$context=new
Context;foreach($this->defaultServices
as$name=>$service){$context->addService($name,$service);}return$context;}static
function
createApplication(){if(Environment::getVariable('baseUri',NULL)===NULL){Environment::setVariable('baseUri',Environment::getHttpRequest()->getUri()->getBaseUri());}$context=clone
Environment::getContext();$context->addService('NetteX\\Application\\IRouter','NetteX\Application\MultiRouter');if(!$context->hasService('NetteX\\Application\\IPresenterLoader')){$context->addService('NetteX\\Application\\IPresenterLoader',function(){return
new
NetteX\Application\PresenterLoader(Environment::getVariable('appDir'));});}$application=new
NetteX\Application\Application;$application->setContext($context);$application->catchExceptions=Environment::isProduction();return$application;}static
function
createCacheStorage(){$context=new
Context;$context->addService('NetteX\\Caching\\ICacheJournal',array(__CLASS__,'createCacheJournal'));$dir=Environment::getVariable('tempDir').'/cache';umask(0000);@mkdir($dir,0777);return
new
NetteX\Caching\FileStorage($dir,$context);}static
function
createCacheJournal(){{return
new
NetteX\Caching\FileJournal(Environment::getVariable('tempDir').'/cache');}}static
function
createRobotLoader($options){$loader=new
NetteX\Loaders\RobotLoader;$loader->autoRebuild=isset($options['autoRebuild'])?$options['autoRebuild']:!Environment::isProduction();$loader->setCacheStorage(Environment::getService('NetteX\\Caching\\ICacheStorage'));if(isset($options['directory'])){$loader->addDirectory($options['directory']);}else{foreach(array('appDir','libsDir')as$var){if($dir=Environment::getVariable($var,NULL)){$loader->addDirectory($dir);}}}$loader->register();return$loader;}}final
class
Environment{const
DEVELOPMENT='development';const
PRODUCTION='production';const
CONSOLE='console';private
static$configurator;private
static$modes=array();private
static$config;private
static$context;private
static$vars=array();private
static$aliases=array('getHttpContext'=>'NetteX\\Web\\HttpContext','getHttpRequest'=>'NetteX\\Web\\IHttpRequest','getHttpResponse'=>'NetteX\\Web\\IHttpResponse','getApplication'=>'NetteX\\Application\\Application','getUser'=>'NetteX\\Web\\IUser','getRobotLoader'=>'NetteX\\Loaders\\RobotLoader');final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
setConfigurator(Configurator$configurator){self::$configurator=$configurator;}static
function
getConfigurator(){if(self::$configurator===NULL){self::$configurator=new
Configurator;}return
self::$configurator;}static
function
setName($name){if(!isset(self::$vars['environment'])){self::setVariable('environment',$name,FALSE);}else{throw
new\XInvalidStateException('Environment name has already been set.');}}static
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
new\XInvalidStateException("Unknown environment variable '$name'.");}}}static
function
getVariables(){$res=array();foreach(self::$vars
as$name=>$foo){$res[$name]=self::getVariable($name);}return$res;}static
function
expand($var){static$livelock;if(is_string($var)&&strpos($var,'%')!==FALSE){return@preg_replace_callback('#%([a-z0-9_-]*)%#i',function($m)use(&$livelock){list(,$var)=$m;if($var==='')return'%';if(isset($livelock[$var])){throw
new\XInvalidStateException("Circular reference detected for variables: ".implode(', ',array_keys($livelock)).".");}try{$livelock[$var]=TRUE;$val=Environment::getVariable($var);unset($livelock[$var]);}catch(\Exception$e){$livelock=array();throw$e;}if(!is_scalar($val)){throw
new\XInvalidStateException("Environment variable '$var' is not scalar.");}return$val;},$var);}return$var;}static
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
new\XMemberAccessException("Call to undefined static method NetteX\\Environment::$name().");}}static
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
NetteX\Caching\Cache(self::getService('NetteX\\Caching\\ICacheStorage'),$namespace);}static
function
getSession($namespace=NULL){$handler=self::getService('NetteX\\Web\\Session');return$namespace===NULL?$handler:$handler->getNamespace($namespace);}static
function
loadConfig($file=NULL){return
self::$config=self::getConfigurator()->loadConfig($file);}static
function
getConfig($key=NULL,$default=NULL){if(func_num_args()){return
isset(self::$config[$key])?self::$config[$key]:$default;}else{return
self::$config;}}}}namespace NetteX\Forms{use
NetteX;use
NetteX\Web\Html;abstract
class
FormControl
extends
NetteX\Component
implements
IFormControl{public
static$idMask='frm%s-%s';public$caption;protected$value;protected$control;protected$label;private$errors=array();private$disabled=FALSE;private$htmlId;private$htmlName;private$rules;private$translator=TRUE;private$options=array();function
__construct($caption=NULL){$this->monitor('NetteX\Forms\Form');parent::__construct();$this->control=Html::el('input');$this->label=Html::el('label');$this->caption=$caption;$this->rules=new
Rules($this);}protected
function
attached($form){if(!$this->disabled&&$form
instanceof
Form&&$form->isAnchored()&&$form->isSubmitted()){$this->htmlName=NULL;$this->loadHttpData();}}function
getForm($need=TRUE){return$this->lookup('NetteX\Forms\Form',$need);}function
getHtmlName(){if($this->htmlName===NULL){$s='';$name=$this->getName();$obj=$this->lookup('NetteX\Forms\FormContainer',TRUE);while(!($obj
instanceof
Form)){$s="[$name]$s";$name=$obj->getName();$obj=$obj->lookup('NetteX\Forms\FormContainer',TRUE);}$name.=$s;if(is_numeric($name)||in_array($name,array('attributes','children','elements','focus','length','reset','style','submit','onsubmit'))){$name.='_';}$this->htmlName=$name;}return$this->htmlName;}function
setHtmlId($id){$this->htmlId=$id;return$this;}function
getHtmlId(){if($this->htmlId===FALSE){return
NULL;}elseif($this->htmlId===NULL){$this->htmlId=sprintf(self::$idMask,$this->getForm()->getName(),$this->getHtmlName());$this->htmlId=str_replace(array('[]','[',']'),array('','-',''),$this->htmlId);}return$this->htmlId;}function
setAttribute($name,$value=TRUE){$this->control->$name=$value;return$this;}function
setOption($key,$value){if($value===NULL){unset($this->options[$key]);}else{$this->options[$key]=$value;}return$this;}final
function
getOption($key,$default=NULL){return
isset($this->options[$key])?$this->options[$key]:$default;}final
function
getOptions(){return$this->options;}function
setTranslator(NetteX\ITranslator$translator=NULL){$this->translator=$translator;return$this;}final
function
getTranslator(){if($this->translator===TRUE){return$this->getForm(FALSE)?$this->getForm()->getTranslator():NULL;}return$this->translator;}function
translate($s,$count=NULL){$translator=$this->getTranslator();return$translator===NULL||$s==NULL?$s:$translator->translate($s,$count);}function
setValue($value){$this->value=$value;return$this;}function
getValue(){return$this->value;}function
isFilled(){return(string)$this->getValue()!=='';}function
setDefaultValue($value){$form=$this->getForm(FALSE);if(!$form||!$form->isAnchored()||!$form->isSubmitted()){$this->setValue($value);}return$this;}function
loadHttpData(){$path=explode('[',strtr(str_replace(array('[]',']'),'',$this->getHtmlName()),'.','_'));$this->setValue(NetteX\ArrayTools::get($this->getForm()->getHttpData(),$path));}function
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
addConditionOn(IFormControl$control,$operation,$value=NULL){return$this->rules->addConditionOn($control,$operation,$value);}final
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
as$rule){if(!is_string($rule->operation)){continue;}elseif($rule->type===Rule::VALIDATOR){$item=array('op'=>($rule->isNegative?'~':'').$rule->operation,'msg'=>$rules->formatMessage($rule,FALSE));}elseif($rule->type===Rule::CONDITION){$item=array('op'=>($rule->isNegative?'~':'').$rule->operation,'rules'=>self::exportRules($rule->subRules),'control'=>$rule->control->getHtmlName());if($rule->subRules->getToggles()){$item['toggle']=$rule->subRules->getToggles();}}if(is_array($rule->arg)){foreach($rule->arg
as$key=>$value){$item['arg'][$key]=$value
instanceof
IFormControl?(object)array('control'=>$value->getHtmlName()):$value;}}elseif($rule->arg!==NULL){$item['arg']=$rule->arg
instanceof
IFormControl?(object)array('control'=>$rule->arg->getHtmlName()):$rule->arg;}$payload[]=$item;}return$payload;}static
function
validateEqual(IFormControl$control,$arg){$value=$control->getValue();foreach((is_array($value)?$value:array($value))as$val){foreach((is_array($arg)?$arg:array($arg))as$item){if((string)$val===(string)($item
instanceof
IFormControl?$item->value:$item)){return
TRUE;}}}return
FALSE;}static
function
validateFilled(IFormControl$control){return$control->isFilled();}static
function
validateValid(IFormControl$control){return$control->rules->validate(TRUE);}function
addError($message){if(!in_array($message,$this->errors,TRUE)){$this->errors[]=$message;}$this->getForm()->addError($message);}function
getErrors(){return$this->errors;}function
hasErrors(){return(bool)$this->errors;}function
cleanErrors(){$this->errors=array();}}class
Button
extends
FormControl{function
__construct($caption=NULL){parent::__construct($caption);$this->control->type='button';}function
getLabel($caption=NULL){return
NULL;}function
getControl($caption=NULL){$control=parent::getControl();$control->value=$this->translate($caption===NULL?$this->caption:$caption);return$control;}}class
Checkbox
extends
FormControl{function
__construct($label=NULL){parent::__construct($label);$this->control->type='checkbox';$this->value=FALSE;}function
setValue($value){$this->value=is_scalar($value)?(bool)$value:FALSE;return$this;}function
getControl(){return
parent::getControl()->checked($this->value);}}use
NetteX\Web\HttpUploadedFile;class
FileUpload
extends
FormControl{function
__construct($label=NULL){parent::__construct($label);$this->control->type='file';}protected
function
attached($form){if($form
instanceof
Form){if($form->getMethod()!==Form::POST){throw
new\XInvalidStateException('File upload requires method POST.');}$form->getElementPrototype()->enctype='multipart/form-data';}parent::attached($form);}function
setValue($value){if(is_array($value)){$this->value=new
HttpUploadedFile($value);}elseif($value
instanceof
HttpUploadedFile){$this->value=$value;}else{$this->value=new
HttpUploadedFile(NULL);}return$this;}function
isFilled(){return$this->value
instanceof
HttpUploadedFile&&$this->value->isOK();}static
function
validateFileSize(FileUpload$control,$limit){$file=$control->getValue();return$file
instanceof
HttpUploadedFile&&$file->getSize()<=$limit;}static
function
validateMimeType(FileUpload$control,$mimeType){$file=$control->getValue();if($file
instanceof
HttpUploadedFile){$type=strtolower($file->getContentType());$mimeTypes=is_array($mimeType)?$mimeType:explode(',',$mimeType);if(in_array($type,$mimeTypes,TRUE)){return
TRUE;}if(in_array(preg_replace('#/.*#','/*',$type),$mimeTypes,TRUE)){return
TRUE;}}return
FALSE;}static
function
validateImage(FileUpload$control){$file=$control->getValue();return$file
instanceof
HttpUploadedFile&&$file->isImage();}}class
HiddenField
extends
FormControl{private$forcedValue;function
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
ISubmitterControl{public$onClick;public$onInvalidClick;private$validationScope=TRUE;function
__construct($caption=NULL){parent::__construct($caption);$this->control->type='submit';}function
setValue($value){$this->value=is_scalar($value)&&(bool)$value;$form=$this->getForm();if($this->value||!is_object($form->isSubmitted())){$this->value=TRUE;$form->setSubmittedBy($this);}return$this;}function
isSubmittedBy(){return$this->getForm()->isSubmitted()===$this;}function
setValidationScope($scope){$this->validationScope=(bool)$scope;$this->control->formnovalidate=!$this->validationScope;return$this;}final
function
getValidationScope(){return$this->validationScope;}function
click(){$this->onClick($this);}static
function
validateSubmitted(ISubmitterControl$control){return$control->isSubmittedBy();}}class
ImageButton
extends
SubmitButton{function
__construct($src=NULL,$alt=NULL){parent::__construct();$this->control->type='image';$this->control->src=$src;$this->control->alt=$alt;}function
getHtmlName(){$name=parent::getHtmlName();return
strpos($name,'[')===FALSE?$name:$name.'[]';}function
loadHttpData(){$path=$this->getHtmlName();$path=explode('[',strtr(str_replace(']','',strpos($path,'[')===FALSE?$path.'.x':substr($path,0,-2)),'.','_'));$this->setValue(NetteX\ArrayTools::get($this->getForm()->getHttpData(),$path)!==NULL);}}class
SelectBox
extends
FormControl{private$items=array();protected$allowed=array();private$skipFirst=FALSE;private$useKeys=TRUE;function
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
new\InvalidArgumentException("All items must be scalar.");}$key2=$value2;}if(isset($this->allowed[$key2])){throw
new\InvalidArgumentException("Items contain duplication for key '$key2'.");}$this->allowed[$key2]=$value2;}}return$this;}final
function
getItems(){return$this->items;}function
getSelectedItem(){if(!$this->useKeys){return$this->getValue();}else{$value=$this->getValue();return$value===NULL?NULL:$this->allowed[$value];}}function
getControl(){$control=parent::getControl();if($this->skipFirst){reset($this->items);$control->data('nette-empty-value',$this->useKeys?key($this->items):current($this->items));}$selected=$this->getValue();$selected=is_array($selected)?array_flip($selected):array($selected=>TRUE);$option=NetteX\Web\Html::el('option');foreach($this->items
as$key=>$value){if(!is_array($value)){$value=array($key=>$value);$dest=$control;}else{$dest=$control->create('optgroup')->label($key);}foreach($value
as$key2=>$value2){if($value2
instanceof
NetteX\Web\Html){$dest->add((string)$value2->selected(isset($selected[$key2])));}else{$key2=$this->useKeys?$key2:$value2;$value2=$this->translate($value2);$dest->add((string)$option->value($key2===$value2?NULL:$key2)->selected(isset($selected[$key2]))->setText($value2));}}}return$control;}}class
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
FormControl{protected$separator;protected$container;protected$items=array();function
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
Html){$label->setHtml($val);}else{$label->setText($this->translate($val));}if($key!==NULL){return(string)$control.(string)$label;}$container->add((string)$control.(string)$label.$separator);$control->data('nette-rules',NULL);}return$container;}function
getLabel($caption=NULL){$label=parent::getLabel($caption);$label->for=NULL;return$label;}}use
NetteX\String;abstract
class
TextBase
extends
FormControl{protected$emptyValue='';protected$filters=array();function
setValue($value){$this->value=is_scalar($value)?(string)$value:'';return$this;}function
getValue(){$value=$this->value;foreach($this->filters
as$filter){$value=(string)$filter($value);}return$value===$this->translate($this->emptyValue)?'':$value;}function
setEmptyValue($value){$this->emptyValue=(string)$value;return$this;}final
function
getEmptyValue(){return$this->emptyValue;}function
addFilter($filter){$this->filters[]=callback($filter);return$this;}function
getControl(){$control=parent::getControl();foreach($this->getRules()as$rule){if($rule->type===Rule::VALIDATOR&&!$rule->isNegative&&($rule->operation===Form::LENGTH||$rule->operation===Form::MAX_LENGTH)){$control->maxlength=is_array($rule->arg)?$rule->arg[1]:$rule->arg;}}if($this->emptyValue!==''){$control->data('nette-empty-value',$this->translate($this->emptyValue));}return$control;}function
addRule($operation,$message=NULL,$arg=NULL){if($operation===Form::FLOAT){$this->addFilter(callback(__CLASS__,'filterFloat'));}return
parent::addRule($operation,$message,$arg);}static
function
validateMinLength(TextBase$control,$length){return
String::length($control->getValue())>=$length;}static
function
validateMaxLength(TextBase$control,$length){return
String::length($control->getValue())<=$length;}static
function
validateLength(TextBase$control,$range){if(!is_array($range)){$range=array($range,$range);}$len=String::length($control->getValue());return($range[0]===NULL||$len>=$range[0])&&($range[1]===NULL||$len<=$range[1]);}static
function
validateEmail(TextBase$control){$atom="[-a-z0-9!#$%&'*+/=?^_`{|}~]";$localPart="(?:\"(?:[ !\\x23-\\x5B\\x5D-\\x7E]*|\\\\[ -~])+\"|$atom+(?:\\.$atom+)*)";$chars="a-z0-9\x80-\xFF";$domain="[$chars](?:[-$chars]{0,61}[$chars])";return(bool)String::match($control->getValue(),"(^$localPart@(?:$domain?\\.)+[-$chars]{2,19}\\z)i");}static
function
validateUrl(TextBase$control){$chars="a-z0-9\x80-\xFF";return(bool)String::match($control->getValue(),"#^(?:https?://|)(?:[$chars](?:[-$chars]{0,61}[$chars])?\\.)+[-$chars]{2,19}(/\S*)?$#i");}static
function
validateRegexp(TextBase$control,$regexp){return(bool)String::match($control->getValue(),$regexp);}static
function
validatePattern(TextBase$control,$pattern){return(bool)String::match($control->getValue(),"\x01^($pattern)$\x01u");}static
function
validateInteger(TextBase$control){return(bool)String::match($control->getValue(),'/^-?[0-9]+$/');}static
function
validateFloat(TextBase$control){return(bool)String::match($control->getValue(),'/^-?[0-9]*[.,]?[0-9]+$/');}static
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
sanitize($value){if($this->control->maxlength&&NetteX\String::length($value)>$this->control->maxlength){$value=iconv_substr($value,0,$this->control->maxlength,'UTF-8');}return
NetteX\String::trim(strtr($value,"\r\n",'  '));}function
setType($type){$this->control->type=$type;return$this;}function
setPasswordMode($mode=TRUE){$this->control->type=$mode?'password':'text';return$this;}function
getControl(){$control=parent::getControl();foreach($this->getRules()as$rule){if($rule->isNegative||$rule->type!==Rule::VALIDATOR){}elseif($rule->operation===Form::RANGE&&$control->type!=='text'){list($control->min,$control->max)=$rule->arg;}elseif($rule->operation===Form::PATTERN){$control->pattern=$rule->arg;}}if($control->type!=='password'){$control->value=$this->getValue()===''?$this->translate($this->emptyValue):$this->value;}return$control;}}class
FormGroup
extends
NetteX\Object{protected$controls;private$options=array();function
__construct(){$this->controls=new\SplObjectStorage;}function
add(){foreach(func_get_args()as$num=>$item){if($item
instanceof
IFormControl){$this->controls->attach($item);}elseif($item
instanceof\Traversable||is_array($item)){foreach($item
as$control){$this->controls->attach($control);}}else{throw
new\InvalidArgumentException("Only IFormControl items are allowed, the #$num parameter is invalid.");}}return$this;}function
getControls(){return
iterator_to_array($this->controls);}function
setOption($key,$value){if($value===NULL){unset($this->options[$key]);}else{$this->options[$key]=$value;}return$this;}final
function
getOption($key,$default=NULL){return
isset($this->options[$key])?$this->options[$key]:$default;}final
function
getOptions(){return$this->options;}}class
ConventionalRenderer
extends
NetteX\Object
implements
IFormRenderer{public$wrappers=array('form'=>array('container'=>NULL,'errors'=>TRUE),'error'=>array('container'=>'ul class=error','item'=>'li'),'group'=>array('container'=>'fieldset','label'=>'legend','description'=>'p'),'controls'=>array('container'=>'table'),'pair'=>array('container'=>'tr','.required'=>'required','.optional'=>NULL,'.odd'=>NULL),'control'=>array('container'=>'td','.odd'=>NULL,'errors'=>FALSE,'description'=>'small','requiredsuffix'=>'','.required'=>'required','.text'=>'text','.password'=>'text','.file'=>'text','.submit'=>'button','.image'=>'imagebutton','.button'=>'button'),'label'=>array('container'=>'th','suffix'=>NULL,'requiredsuffix'=>''),'hidden'=>array('container'=>'div'));protected$form;protected$counter;function
render(Form$form,$mode=NULL){if($this->form!==$form){$this->form=$form;$this->init();}$s='';if(!$mode||$mode==='begin'){$s.=$this->renderBegin();}if((!$mode&&$this->getValue('form errors'))||$mode==='errors'){$s.=$this->renderErrors();}if(!$mode||$mode==='body'){$s.=$this->renderBody();}if(!$mode||$mode==='end'){$s.=$this->renderEnd();}return$s;}function
setClientScript(){trigger_error(__METHOD__.'() is deprecated; use unobstructive JavaScript instead.',E_USER_WARNING);return$this;}protected
function
init(){$wrapper=&$this->wrappers['control'];foreach($this->form->getControls()as$control){if($control->isRequired()&&isset($wrapper['.required'])){$control->getLabelPrototype()->class($wrapper['.required'],TRUE);}$el=$control->getControlPrototype();if($el->getName()==='input'&&isset($wrapper['.'.$el->type])){$el->class($wrapper['.'.$el->type],TRUE);}}}function
renderBegin(){$this->counter=0;foreach($this->form->getControls()as$control){$control->setOption('rendered',FALSE);}if(strcasecmp($this->form->getMethod(),'get')===0){$el=clone$this->form->getElementPrototype();$uri=explode('?',(string)$el->action,2);$el->action=$uri[0];$s='';if(isset($uri[1])){foreach(preg_split('#[;&]#',$uri[1])as$param){$parts=explode('=',$param,2);$name=urldecode($parts[0]);if(!isset($this->form[$name])){$s.=Html::el('input',array('type'=>'hidden','name'=>$name,'value'=>urldecode($parts[1])));}}$s="\n\t".$this->getWrapper('hidden container')->setHtml($s);}return$el->startTag().$s;}else{return$this->form->getElementPrototype()->startTag();}}function
renderEnd(){$s='';foreach($this->form->getControls()as$control){if($control
instanceof
HiddenField&&!$control->getOption('rendered')){$s.=(string)$control->getControl();}}if($s){$s=$this->getWrapper('hidden container')->setHtml($s)."\n";}return$s.$this->form->getElementPrototype()->endTag()."\n";}function
renderErrors(IFormControl$control=NULL){$errors=$control===NULL?$this->form->getErrors():$control->getErrors();if(count($errors)){$ul=$this->getWrapper('error container');$li=$this->getWrapper('error item');foreach($errors
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
FormContainer||$parent
instanceof
FormGroup)){throw
new\InvalidArgumentException("Argument must be FormContainer or FormGroup instance.");}$container=$this->getWrapper('controls container');$buttons=NULL;foreach($parent->getControls()as$control){if($control->getOption('rendered')||$control
instanceof
HiddenField||$control->getForm(FALSE)!==$this->form){}elseif($control
instanceof
Button){$buttons[]=$control;}else{if($buttons){$container->add($this->renderPairMulti($buttons));$buttons=NULL;}$container->add($this->renderPair($control));}}if($buttons){$container->add($this->renderPairMulti($buttons));}$s='';if(count($container)){$s.="\n".$container."\n";}return$s;}function
renderPair(IFormControl$control){$pair=$this->getWrapper('pair container');$pair->add($this->renderLabel($control));$pair->add($this->renderControl($control));$pair->class($this->getValue($control->isRequired()?'pair .required':'pair .optional'),TRUE);$pair->class($control->getOption('class'),TRUE);if(++$this->counter
%
2)$pair->class($this->getValue('pair .odd'),TRUE);$pair->id=$control->getOption('id');return$pair->render(0);}function
renderPairMulti(array$controls){$s=array();foreach($controls
as$control){if(!($control
instanceof
IFormControl)){throw
new\InvalidArgumentException("Argument must be array of IFormControl instances.");}$s[]=(string)$control->getControl();}$pair=$this->getWrapper('pair container');$pair->add($this->renderLabel($control));$pair->add($this->getWrapper('control container')->setHtml(implode(" ",$s)));return$pair->render(0);}function
renderLabel(IFormControl$control){$head=$this->getWrapper('label container');if($control
instanceof
Checkbox||$control
instanceof
Button){return$head->setHtml(($head->getName()==='td'||$head->getName()==='th')?'&nbsp;':'');}else{$label=$control->getLabel();$suffix=$this->getValue('label suffix').($control->isRequired()?$this->getValue('label requiredsuffix'):'');if($label
instanceof
Html){$label->setHtml($label->getHtml().$suffix);$suffix='';}return$head->setHtml((string)$label.$suffix);}}function
renderControl(IFormControl$control){$body=$this->getWrapper('control container');if($this->counter
%
2)$body->class($this->getValue('control .odd'),TRUE);$description=$control->getOption('description');if($description
instanceof
Html){$description=' '.$control->getOption('description');}elseif(is_string($description)){$description=' '.$this->getWrapper('control description')->setText($control->translate($description));}else{$description='';}if($control->isRequired()){$description=$this->getValue('control requiredsuffix').$description;}if($this->getValue('control errors')){$description.=$this->renderErrors($control);}if($control
instanceof
Checkbox||$control
instanceof
Button){return$body->setHtml((string)$control->getControl().(string)$control->getLabel().$description);}else{return$body->setHtml((string)$control->getControl().$description);}}protected
function
getWrapper($name){$data=$this->getValue($name);return$data
instanceof
Html?clone$data:Html::el($data);}protected
function
getValue($name){$name=explode(' ',$name);$data=&$this->wrappers[$name[0]][$name[1]];return$data;}}final
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
static$defaultMessages=array(Form::PROTECTION=>'Security token did not match. Possible CSRF attack.');private$rules=array();private$parent;private$toggles=array();private$control;function
__construct(IFormControl$control){$this->control=$control;}function
addRule($operation,$message=NULL,$arg=NULL){$rule=new
Rule;$rule->control=$this->control;$rule->operation=$operation;$this->adjustOperation($rule);$rule->arg=$arg;$rule->type=Rule::VALIDATOR;if($message===NULL&&is_string($rule->operation)&&isset(self::$defaultMessages[$rule->operation])){$rule->message=self::$defaultMessages[$rule->operation];}else{$rule->message=$message;}$this->rules[]=$rule;return$this;}function
addCondition($operation,$arg=NULL){return$this->addConditionOn($this->control,$operation,$arg);}function
addConditionOn(IFormControl$control,$operation,$arg=NULL){$rule=new
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
new\InvalidArgumentException("Unknown operation$operation for control '{$rule->control->name}'.");}}private
function
getCallback($rule){$op=$rule->operation;if(is_string($op)&&strncmp($op,':',1)===0){return
callback(get_class($rule->control),self::VALIDATE_PREFIX.ltrim($op,':'));}else{return
callback($op);}}static
function
formatMessage($rule,$withValue){$message=$rule->message;if(!isset($message)){$message=self::$defaultMessages[$rule->operation];}if($translator=$rule->control->getForm()->getTranslator()){$message=$translator->translate($message,is_int($rule->arg)?$rule->arg:NULL);}$message=vsprintf(preg_replace('#%(name|label|value)#','%$0',$message),(array)$rule->arg);$message=str_replace('%name',$rule->control->getName(),$message);$message=str_replace('%label',$rule->control->translate($rule->control->caption),$message);if($withValue&&strpos($message,'%value')!==FALSE){$message=str_replace('%value',$rule->control->getValue(),$message);}return$message;}}}namespace NetteX\Loaders{use
NetteX;use
NetteX\String;use
NetteX\Caching\Cache;class
RobotLoader
extends
AutoLoader{public$scanDirs;public$ignoreDirs='.*, *.old, *.bak, *.tmp, temp';public$acceptFiles='*.php, *.php5';public$autoRebuild=TRUE;private$list=array();private$files;private$rebuilt=FALSE;private$cacheStorage;function
__construct(){if(!extension_loaded('tokenizer')){throw
new\Exception("PHP extension Tokenizer is not loaded.");}}function
register(){$cache=$this->getCache();$key=$this->getKey();if(isset($cache[$key])){$this->list=$cache[$key];}else{$this->rebuild();}if(isset($this->list[strtolower(__CLASS__)])&&class_exists('NetteX\Loaders\NetteXLoader',FALSE)){NetteXLoader::getInstance()->unregister();}parent::register();}function
tryLoad($type){$type=ltrim(strtolower($type),'\\');if(isset($this->list[$type][0])&&!is_file($this->list[$type][0])){unset($this->list[$type]);}if(!isset($this->list[$type])){$trace=debug_backtrace();$initiator=&$trace[2]['function'];if($initiator==='class_exists'||$initiator==='interface_exists'){$this->list[$type]=FALSE;if($this->autoRebuild&&$this->rebuilt){$this->getCache()->save($this->getKey(),$this->list,array(Cache::CONSTS=>'NetteX\Framework::REVISION'));}}if($this->autoRebuild&&!$this->rebuilt){$this->rebuild();}}if(isset($this->list[$type][0])){LimitedScope::load($this->list[$type][0]);self::$count++;}}function
rebuild(){$this->getCache()->save($this->getKey(),callback($this,'_rebuildCallback'),array(Cache::CONSTS=>'NetteX\Framework::REVISION'));$this->rebuilt=TRUE;}function
_rebuildCallback(){foreach($this->list
as$pair){if($pair)$this->files[$pair[0]]=$pair[1];}foreach(array_unique($this->scanDirs)as$dir){$this->scanDirectory($dir);}$this->files=NULL;return$this->list;}function
getIndexedClasses(){$res=array();foreach($this->list
as$class=>$pair){if($pair)$res[$pair[2]]=$pair[0];}return$res;}function
addDirectory($path){foreach((array)$path
as$val){$real=realpath($val);if($real===FALSE){throw
new\XDirectoryNotFoundException("Directory '$val' not found.");}$this->scanDirs[]=$real;}}private
function
addClass($class,$file,$time){$lClass=strtolower($class);if(isset($this->list[$lClass][0])&&$this->list[$lClass][0]!==$file&&is_file($this->list[$lClass][0])){$e=new\XInvalidStateException("Ambiguous class '$class' resolution; defined in $file and in ".$this->list[$lClass][0].".");{throw$e;}}$this->list[$lClass]=array($file,$time,$class);}private
function
scanDirectory($dir){if(is_dir($dir)){$disallow=array();$iterator=NetteX\Finder::findFiles(String::split($this->acceptFiles,'#[,\s]+#'))->filter(function($file)use(&$disallow){return!isset($disallow[$file->getPathname()]);})->from($dir)->exclude(String::split($this->ignoreDirs,'#[,\s]+#'))->filter($filter=function($dir)use(&$disallow){$path=$dir->getPathname();if(is_file("$path/netterobots.txt")){foreach(file("$path/netterobots.txt")as$s){if($matches=String::match($s,'#^disallow\\s*:\\s*(\\S+)#i')){$disallow[$path.str_replace('/',DIRECTORY_SEPARATOR,rtrim('/'.ltrim($matches[1],'/'),'/'))]=TRUE;}}}return!isset($disallow[$path]);});$filter(new\SplFileInfo($dir));}else{$iterator=new\ArrayIterator(array(new\SplFileInfo($dir)));}foreach($iterator
as$entry){$path=$entry->getPathname();if(!isset($this->files[$path])||$this->files[$path]!==$entry->getMTime()){$this->scanScript($path);}}}private
function
scanScript($file){$T_NAMESPACE=PHP_VERSION_ID<50300?-1:T_NAMESPACE;$T_NS_SEPARATOR=PHP_VERSION_ID<50300?-1:T_NS_SEPARATOR;$expected=FALSE;$namespace='';$level=$minLevel=0;$time=filemtime($file);$s=file_get_contents($file);if($matches=String::match($s,'#//nette'.'loader=(\S*)#')){foreach(explode(',',$matches[1])as$name){$this->addClass($name,$file,$time);}return;}foreach(token_get_all($s)as$token){if(is_array($token)){switch($token[0]){case
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
setCacheStorage(NetteX\Caching\ICacheStorage$storage){$this->cacheStorage=$storage;return$this;}function
getCacheStorage(){return$this->cacheStorage;}protected
function
getCache(){if(!$this->cacheStorage){trigger_error('Missing cache storage.',E_USER_WARNING);$this->cacheStorage=new
NetteX\Caching\DummyStorage;}return
new
Cache($this->cacheStorage,'NetteX.RobotLoader');}protected
function
getKey(){return
md5("v2|$this->ignoreDirs|$this->acceptFiles|".implode('|',$this->scanDirs));}}}namespace NetteX\Mail{use
NetteX;class
MailMimePart
extends
NetteX\Object{const
ENCODING_BASE64='base64';const
ENCODING_7BIT='7bit';const
ENCODING_8BIT='8bit';const
ENCODING_QUOTED_PRINTABLE='quoted-printable';const
EOL="\r\n";const
LINE_LENGTH=76;private$headers=array();private$parts=array();private$body;function
setHeader($name,$value,$append=FALSE){if(!$name||preg_match('#[^a-z0-9-]#i',$name)){throw
new\InvalidArgumentException("Header name must be non-empty alphanumeric string, '$name' given.");}if($value==NULL){if(!$append){unset($this->headers[$name]);}}elseif(is_array($value)){$tmp=&$this->headers[$name];if(!$append||!is_array($tmp)){$tmp=array();}foreach($value
as$email=>$name){if($name!==NULL&&!NetteX\String::checkEncoding($name)){throw
new\InvalidArgumentException("Name is not valid UTF-8 string.");}if(!preg_match('#^[^@",\s]+@[^@",\s]+\.[a-z]{2,10}$#i',$email)){throw
new\InvalidArgumentException("Email address '$email' is not valid.");}if(preg_match('#[\r\n]#',$name)){throw
new\InvalidArgumentException("Name must not contain line separator.");}$tmp[$email]=$name;}}else{$value=(string)$value;if(!NetteX\String::checkEncoding($value)){throw
new\InvalidArgumentException("Header is not valid UTF-8 string.");}$this->headers[$name]=preg_replace('#[\r\n]+#',' ',$value);}return$this;}function
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
addPart(MailMimePart$part=NULL){return$this->parts[]=$part===NULL?new
self:$part;}function
setBody($body){$this->body=$body;return$this;}function
getBody(){return$this->body;}function
generateMessage(){$output='';$boundary='--------'.md5(uniqid('',TRUE));foreach($this->headers
as$name=>$value){$output.=$name.': '.$this->getEncodedHeader($name);if($this->parts&&$name==='Content-Type'){$output.=';'.self::EOL."\tboundary=\"$boundary\"";}$output.=self::EOL;}$output.=self::EOL;$body=(string)$this->body;if($body!==''){switch($this->getEncoding()){case
self::ENCODING_QUOTED_PRINTABLE:$output.=function_exists('quoted_printable_encode')?quoted_printable_encode($body):self::encodeQuotedPrintable($body);break;case
self::ENCODING_BASE64:$output.=rtrim(chunk_split(base64_encode($body),self::LINE_LENGTH,self::EOL));break;case
self::ENCODING_7BIT:$body=preg_replace('#[\x80-\xFF]+#','',$body);case
self::ENCODING_8BIT:$body=str_replace(array("\x00","\r"),'',$body);$body=str_replace("\n",self::EOL,$body);$output.=$body;break;default:throw
new\XInvalidStateException('Unknown encoding.');}}if($this->parts){if(substr($output,-strlen(self::EOL))!==self::EOL)$output.=self::EOL;foreach($this->parts
as$part){$output.='--'.$boundary.self::EOL.$part->generateMessage().self::EOL;}$output.='--'.$boundary.'--';}return$output;}private
static
function
encodeHeader($s,&$offset=0){if(strspn($s,"!\"#$%&\'()*+,-./0123456789:;<>@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^`abcdefghijklmnopqrstuvwxyz{|}=? _\r\n\t")===strlen($s)&&($offset+strlen($s)<=self::LINE_LENGTH)){$offset+=strlen($s);return$s;}$o=str_replace("\n ","\n\t",substr(iconv_mime_encode(str_repeat(' ',$offset),$s,array('scheme'=>'B','input-charset'=>'UTF-8','output-charset'=>'UTF-8')),$offset+2));$offset=strlen($o)-strrpos($o,"\n");return$o;}}use
NetteX\String;class
Mail
extends
MailMimePart{const
HIGH=1;const
NORMAL=3;const
LOW=5;public
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
addEmbeddedFile($file,$content=NULL,$contentType=NULL){return$this->inlines[$file]=$this->createAttachment($file,$content,$contentType,'inline')->setHeader('Content-ID','<'.md5(uniqid('',TRUE)).'>');}function
addAttachment($file,$content=NULL,$contentType=NULL){return$this->attachments[]=$this->createAttachment($file,$content,$contentType,'attachment');}private
function
createAttachment($file,$content,$contentType,$disposition){$part=new
MailMimePart;if($content===NULL){if(!is_file($file)){throw
new\XFileNotFoundException("File '$file' not found.");}if(!$contentType&&$info=getimagesize($file)){$contentType=$info['mime'];}$part->setBody(file_get_contents($file));}else{$part->setBody((string)$content);}$part->setContentType($contentType?$contentType:'application/octet-stream');$part->setEncoding(preg_match('#(multipart|message)/#A',$contentType)?self::ENCODING_8BIT:self::ENCODING_BASE64);$part->setHeader('Content-Disposition',$disposition.'; filename="'.String::fixEncoding(basename($file)).'"');return$part;}function
send(){$this->getMailer()->send($this->build());}function
setMailer(IMailer$mailer){$this->mailer=$mailer;return$this;}function
getMailer(){if($this->mailer===NULL){$this->mailer=is_object(self::$defaultMailer)?self::$defaultMailer:new
self::$defaultMailer;}return$this->mailer;}protected
function
build(){$mail=clone$this;$hostname=isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost');$mail->setHeader('Message-ID','<'.md5(uniqid('',TRUE))."@$hostname>");$mail->buildHtml();$mail->buildText();$cursor=$mail;if($mail->attachments){$tmp=$cursor->setContentType('multipart/mixed');$cursor=$cursor->addPart();foreach($mail->attachments
as$value){$tmp->addPart($value);}}if($mail->html!=NULL){$tmp=$cursor->setContentType('multipart/alternative');$cursor=$cursor->addPart();$alt=$tmp->addPart();if($mail->inlines){$tmp=$alt->setContentType('multipart/related');$alt=$alt->addPart();foreach($mail->inlines
as$name=>$value){$tmp->addPart($value);}}$alt->setContentType('text/html','UTF-8')->setEncoding(preg_match('#[\x80-\xFF]#',$mail->html)?self::ENCODING_8BIT:self::ENCODING_7BIT)->setBody($mail->html);}$text=$mail->getBody();$mail->setBody(NULL);$cursor->setContentType('text/plain','UTF-8')->setEncoding(preg_match('#[\x80-\xFF]#',$text)?self::ENCODING_8BIT:self::ENCODING_7BIT)->setBody($text);return$mail;}protected
function
buildHtml(){if($this->html
instanceof
NetteX\Templates\ITemplate){$this->html->mail=$this;if($this->basePath===NULL&&$this->html
instanceof
NetteX\Templates\IFileTemplate){$this->basePath=dirname($this->html->getFile());}$this->html=$this->html->__toString(TRUE);}if($this->basePath!==FALSE){$cids=array();$matches=String::matchAll($this->html,'#(src\s*=\s*|background\s*=\s*|url\()(["\'])(?![a-z]+:|[/\\#])(.+?)\\2#i',PREG_OFFSET_CAPTURE);foreach(array_reverse($matches)as$m){$file=rtrim($this->basePath,'/\\').'/'.$m[3][0];$cid=isset($cids[$file])?$cids[$file]:$cids[$file]=substr($this->addEmbeddedFile($file)->getHeader("Content-ID"),1,-1);$this->html=substr_replace($this->html,"{$m[1][0]}{$m[2][0]}cid:$cid{$m[2][0]}",$m[0][1],strlen($m[0][0]));}}if(!$this->getSubject()&&$matches=String::match($this->html,'#<title>(.+?)</title>#is')){$this->setSubject(html_entity_decode($matches[1],ENT_QUOTES,'UTF-8'));}}protected
function
buildText(){$text=$this->getBody();if($text
instanceof
NetteX\Templates\ITemplate){$text->mail=$this;$this->setBody($text->__toString(TRUE));}elseif($text==NULL&&$this->html!=NULL){$text=String::replace($this->html,array('#<(style|script|head).*</\\1>#Uis'=>'','#<t[dh][ >]#i'=>" $0",'#[ \t\r\n]+#'=>' ','#<(/?p|/?h\d|li|br|/tr)[ >/]#i'=>"\n$0"));$text=html_entity_decode(strip_tags($text),ENT_QUOTES,'UTF-8');$this->setBody(trim($text));}}}class
SendmailMailer
extends
NetteX\Object
implements
IMailer{function
send(Mail$mail){$tmp=clone$mail;$tmp->setHeader('Subject',NULL);$tmp->setHeader('To',NULL);$parts=explode(Mail::EOL.Mail::EOL,$tmp->generateMessage(),2);NetteX\Debug::tryError();$res=mail(str_replace(Mail::EOL,PHP_EOL,$mail->getEncodedHeader('To')),str_replace(Mail::EOL,PHP_EOL,$mail->getEncodedHeader('Subject')),str_replace(Mail::EOL,PHP_EOL,$parts[1]),str_replace(Mail::EOL,PHP_EOL,$parts[0]));if(NetteX\Debug::catchError($e)){throw
new\XInvalidStateException($e->getMessage());}elseif(!$res){throw
new\XInvalidStateException('Unable to send email.');}}}class
SmtpMailer
extends
NetteX\Object
implements
IMailer{private$connection;private$host;private$port;private$username;private$password;private$secure;private$timeout;function
__construct(array$options=array()){if($options['host']){$this->host=$options['host'];$this->port=isset($options['port'])?(int)$options['port']:NULL;}else{$this->host=ini_get('SMTP');$this->port=(int)ini_get('smtp_port');}$this->username=isset($options['username'])?$options['username']:'';$this->password=isset($options['password'])?$options['password']:'';$this->secure=isset($options['secure'])?$options['secure']:'';$this->timeout=isset($options['timeout'])?(int)$options['timeout']:20;if(!$this->port){$this->port=$this->secure==='ssl'?465:25;}}function
send(Mail$mail){$data=$mail->generateMessage();$this->connect();$from=$mail->getHeader('From');if($from){$from=array_keys($from);$this->write("MAIL FROM:<$from[0]>",250);}$recipients=array_merge((array)$mail->getHeader('To'),(array)$mail->getHeader('Cc'),(array)$mail->getHeader('Bcc'));foreach($recipients
as$email=>$name){$this->write("RCPT TO:<$email>",array(250,251));}$this->write('DATA',354);$data=preg_replace('#^\.#m','..',$data);$this->write($data);$this->write('.',250);$this->write('QUIT',221);$this->disconnect();}private
function
connect(){$this->connection=@fsockopen(($this->secure==='ssl'?'ssl://':'').$this->host,$this->port,$errno,$error,$this->timeout);if(!$this->connection){throw
new
SmtpException($error,$errno);}stream_set_timeout($this->connection,$this->timeout,0);$this->read();$self=isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost';$this->write("EHLO $self");if((int)$this->read()!==250){$this->write("HELO $self",250);}if($this->secure==='tls'){$this->write('STARTTLS',220);if(!stream_socket_enable_crypto($this->connection,TRUE,STREAM_CRYPTO_METHOD_TLS_CLIENT)){throw
new
SmtpException('Unable to connect via TLS.');}}if($this->username!=NULL&&$this->password!=NULL){$this->write('AUTH LOGIN',334);$this->write(base64_encode($this->username),334,'username');$this->write(base64_encode($this->password),235,'password');}}private
function
disconnect(){fclose($this->connection);$this->connection=NULL;}private
function
write($line,$expectedCode=NULL,$message=NULL){fwrite($this->connection,$line.Mail::EOL);if($expectedCode&&!in_array((int)$this->read(),(array)$expectedCode)){throw
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
NetteX\String;/**
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
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
getAll(\Reflector$r){if($r
instanceof\ReflectionClass){$type=$r->getName();$member='';}elseif($r
instanceof\ReflectionMethod){$type=$r->getDeclaringClass()->getName();$member=$r->getName();}else{$type=$r->getDeclaringClass()->getName();$member='$'.$r->getName();}if(!self::$useReflection){$file=$r
instanceof\ReflectionClass?$r->getFileName():$r->getDeclaringClass()->getFileName();if($file&&isset(self::$timestamps[$file])&&self::$timestamps[$file]!==filemtime($file)){unset(self::$cache[$type]);}unset(self::$timestamps[$file]);}if(isset(self::$cache[$type][$member])){return
self::$cache[$type][$member];}if(self::$useReflection===NULL){self::$useReflection=(bool)NetteX\Reflection\ClassReflection::from(__CLASS__)->getDocComment();}if(self::$useReflection){return
self::$cache[$type][$member]=self::parseComment($r->getDocComment());}else{if(self::$cache===NULL){self::$cache=(array)self::getCache()->offsetGet('list');self::$timestamps=isset(self::$cache['*'])?self::$cache['*']:array();}if(!isset(self::$cache[$type])&&$file){self::$cache['*'][$file]=filemtime($file);self::parseScript($file);self::getCache()->save('list',self::$cache);}if(isset(self::$cache[$type][$member])){return
self::$cache[$type][$member];}else{return
self::$cache[$type][$member]=array();}}}private
static
function
parseComment($comment){static$tokens=array('true'=>TRUE,'false'=>FALSE,'null'=>NULL,''=>TRUE);$matches=String::matchAll(trim($comment,'/*'),'~
				(?<=\s)@('.self::RE_IDENTIFIER.')[ \t]*      ##  annotation
				(
					\((?>'.self::RE_STRING.'|[^\'")@]+)+\)|  ##  (value)
					[^(@\r\n][^@\r\n]*|)                     ##  value
			~xi');$res=array();foreach($matches
as$match){list(,$name,$value)=$match;if(substr($value,0,1)==='('){$items=array();$key='';$val=TRUE;$value[0]=',';while($m=String::match($value,'#\s*,\s*(?>('.self::RE_IDENTIFIER.')\s*=\s*)?('.self::RE_STRING.'|[^\'"),\s][^\'"),]*)#A')){$value=substr($value,strlen($m[0]));list(,$key,$val)=$m;if($val[0]==="'"||$val[0]==='"'){$val=substr($val,1,-1);}elseif(is_numeric($val)){$val=1*$val;}else{$lval=strtolower($val);$val=array_key_exists($lval,$tokens)?$tokens[$lval]:$val;}if($key===''){$items[]=$val;}else{$items[$key]=$val;}}$value=count($items)<2&&$key===''?$val:$items;}else{$value=trim($value);if(is_numeric($value)){$value=1*$value;}else{$lval=strtolower($value);$value=array_key_exists($lval,$tokens)?$tokens[$lval]:$value;}}$class=$name.'Annotation';if(class_exists($class)){$res[$name][]=new$class(is_array($value)?$value:array('value'=>$value));}else{$res[$name][]=is_array($value)?new\ArrayObject($value,\ArrayObject::ARRAY_AS_PROPS):$value;}}return$res;}private
static
function
parseScript($file){$T_NAMESPACE=PHP_VERSION_ID<50300?-1:T_NAMESPACE;$T_NS_SEPARATOR=PHP_VERSION_ID<50300?-1:T_NS_SEPARATOR;$s=file_get_contents($file);if(String::match($s,'#//nette'.'loader=(\S*)#')){return;}$expected=$namespace=$class=$docComment=NULL;$level=$classLevel=0;foreach(token_get_all($s)as$token){if(is_array($token)){switch($token[0]){case
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
NetteX\ObjectMixin;use
NetteX\Annotations;class
ExtensionReflection
extends\ReflectionExtension{function
__toString(){return'Extension '.$this->getName();}function
getClasses(){$res=array();foreach(parent::getClassNames()as$val){$res[$val]=new
ClassReflection($val);}return$res;}function
getFunctions(){foreach($res=parent::getFunctions()as$key=>$val){$res[$key]=new
FunctionReflection($key);}return$res;}static
function
getReflection(){return
new
NetteX\Reflection\ClassReflection(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){throw
new\XMemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");}}class
FunctionReflection
extends\ReflectionFunction{private$value;function
__construct($name){parent::__construct($this->value=$name);}function
__toString(){return'Function '.$this->getName().'()';}function
getClosure(){return$this->isClosure()?$this->value:NULL;}function
getExtension(){return($name=$this->getExtensionName())?new
ExtensionReflection($name):NULL;}function
getParameters(){foreach($res=parent::getParameters()as$key=>$val){$res[$key]=new
ParameterReflection($this->value,$val->getName());}return$res;}static
function
getReflection(){return
new
NetteX\Reflection\ClassReflection(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){throw
new\XMemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");}}class
MethodReflection
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
ClassReflection(parent::getDeclaringClass()->getName());}function
getPrototype(){$prototype=parent::getPrototype();return
new
MethodReflection($prototype->getDeclaringClass()->getName(),$prototype->getName());}function
getExtension(){return($name=$this->getExtensionName())?new
ExtensionReflection($name):NULL;}function
getParameters(){$me=array(parent::getDeclaringClass()->getName(),$this->getName());foreach($res=parent::getParameters()as$key=>$val){$res[$key]=new
ParameterReflection($me,$val->getName());}return$res;}function
hasAnnotation($name){$res=AnnotationsParser::getAll($this);return!empty($res[$name]);}function
getAnnotation($name){$res=AnnotationsParser::getAll($this);return
isset($res[$name])?end($res[$name]):NULL;}function
getAnnotations(){return
AnnotationsParser::getAll($this);}static
function
getReflection(){return
new
NetteX\Reflection\ClassReflection(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){throw
new\XMemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");}}class
ParameterReflection
extends\ReflectionParameter{private$function;function
__construct($function,$parameter){parent::__construct($this->function=$function,$parameter);}function
getClass(){return($ref=parent::getClass())?new
ClassReflection($ref->getName()):NULL;}function
getClassName(){return($tmp=NetteX\String::match($this,'#>\s+([a-z0-9_\\\\]+)#i'))?$tmp[1]:NULL;}function
getDeclaringClass(){return($ref=parent::getDeclaringClass())?new
ClassReflection($ref->getName()):NULL;}function
getDeclaringFunction(){return
is_array($this->function)?new
MethodReflection($this->function[0],$this->function[1]):new
FunctionReflection($this->function);}static
function
getReflection(){return
new
NetteX\Reflection\ClassReflection(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){throw
new\XMemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");}}class
PropertyReflection
extends\ReflectionProperty{function
__toString(){return'Property '.parent::getDeclaringClass()->getName().'::$'.$this->getName();}function
getDeclaringClass(){return
new
ClassReflection(parent::getDeclaringClass()->getName());}function
hasAnnotation($name){$res=AnnotationsParser::getAll($this);return!empty($res[$name]);}function
getAnnotation($name){$res=AnnotationsParser::getAll($this);return
isset($res[$name])?end($res[$name]):NULL;}function
getAnnotations(){return
AnnotationsParser::getAll($this);}static
function
getReflection(){return
new
NetteX\Reflection\ClassReflection(get_called_class());}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){throw
new\XMemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");}}}namespace NetteX\Security{use
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
__unset($name){throw
new\XMemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");}}class
Permission
extends
NetteX\Object
implements
IAuthorizator{private$roles=array();private$resources=array();private$rules=array('allResources'=>array('allRoles'=>array('allPrivileges'=>array('type'=>self::DENY,'assert'=>NULL),'byPrivilege'=>array()),'byRole'=>array()),'byResource'=>array());private$queriedRole,$queriedResource;function
addRole($role,$parents=NULL){$this->checkRole($role,FALSE);if(isset($this->roles[$role])){throw
new\XInvalidStateException("Role '$role' already exists in the list.");}$roleParents=array();if($parents!==NULL){if(!is_array($parents)){$parents=array($parents);}foreach($parents
as$parent){$this->checkRole($parent);$roleParents[$parent]=TRUE;$this->roles[$parent]['children'][$role]=TRUE;}}$this->roles[$role]=array('parents'=>$roleParents,'children'=>array());return$this;}function
hasRole($role){$this->checkRole($role,FALSE);return
isset($this->roles[$role]);}private
function
checkRole($role,$need=TRUE){if(!is_string($role)||$role===''){throw
new\InvalidArgumentException("Role must be a non-empty string.");}elseif($need&&!isset($this->roles[$role])){throw
new\XInvalidStateException("Role '$role' does not exist.");}}function
getRoleParents($role){$this->checkRole($role);return
array_keys($this->roles[$role]['parents']);}function
roleInheritsFrom($role,$inherit,$onlyParents=FALSE){$this->checkRole($role);$this->checkRole($inherit);$inherits=isset($this->roles[$role]['parents'][$inherit]);if($inherits||$onlyParents){return$inherits;}foreach($this->roles[$role]['parents']as$parent=>$foo){if($this->roleInheritsFrom($parent,$inherit)){return
TRUE;}}return
FALSE;}function
removeRole($role){$this->checkRole($role);foreach($this->roles[$role]['children']as$child=>$foo)unset($this->roles[$child]['parents'][$role]);foreach($this->roles[$role]['parents']as$parent=>$foo)unset($this->roles[$parent]['children'][$role]);unset($this->roles[$role]);foreach($this->rules['allResources']['byRole']as$roleCurrent=>$rules){if($role===$roleCurrent){unset($this->rules['allResources']['byRole'][$roleCurrent]);}}foreach($this->rules['byResource']as$resourceCurrent=>$visitor){if(isset($visitor['byRole'])){foreach($visitor['byRole']as$roleCurrent=>$rules){if($role===$roleCurrent){unset($this->rules['byResource'][$resourceCurrent]['byRole'][$roleCurrent]);}}}}return$this;}function
removeAllRoles(){$this->roles=array();foreach($this->rules['allResources']['byRole']as$roleCurrent=>$rules)unset($this->rules['allResources']['byRole'][$roleCurrent]);foreach($this->rules['byResource']as$resourceCurrent=>$visitor){foreach($visitor['byRole']as$roleCurrent=>$rules){unset($this->rules['byResource'][$resourceCurrent]['byRole'][$roleCurrent]);}}return$this;}function
addResource($resource,$parent=NULL){$this->checkResource($resource,FALSE);if(isset($this->resources[$resource])){throw
new\XInvalidStateException("Resource '$resource' already exists in the list.");}if($parent!==NULL){$this->checkResource($parent);$this->resources[$parent]['children'][$resource]=TRUE;}$this->resources[$resource]=array('parent'=>$parent,'children'=>array());return$this;}function
hasResource($resource){$this->checkResource($resource,FALSE);return
isset($this->resources[$resource]);}private
function
checkResource($resource,$need=TRUE){if(!is_string($resource)||$resource===''){throw
new\InvalidArgumentException("Resource must be a non-empty string.");}elseif($need&&!isset($this->resources[$resource])){throw
new\XInvalidStateException("Resource '$resource' does not exist.");}}function
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
AuthenticationException("User '$username' not found.",self::IDENTITY_NOT_FOUND);}}}namespace NetteX\Templates{use
NetteX;abstract
class
Template
extends
NetteX\Object
implements
ITemplate{public$warnOnUndefined=TRUE;public$onPrepareFilters=array();private$params=array();private$filters=array();private$helpers=array();private$helperLoaders=array();function
registerFilter($callback){$callback=callback($callback);if(in_array($callback,$this->filters)){throw
new\XInvalidStateException("Filter '$callback' was registered twice.");}$this->filters[]=$callback;}final
function
getFilters(){return$this->filters;}function
render(){}function
save($file){if(file_put_contents($file,$this->__toString(TRUE))===FALSE){throw
new\XIOException("Unable to save file '$file'.");}}function
__toString(){ob_start();try{$this->render();return
ob_get_clean();}catch(\Exception$e){ob_end_clean();if(func_num_args()&&func_get_arg(0)){throw$e;}else{NetteX\Debug::toStringException($e);}}}protected
function
compile($content,$label=NULL){if(!$this->filters){$this->onPrepareFilters($this);}try{foreach($this->filters
as$filter){$content=self::extractPhp($content,$blocks);$content=$filter($content);$content=strtr($content,$blocks);}}catch(\Exception$e){throw
new\XInvalidStateException("Filter $filter: ".$e->getMessage().($label?" (in $label)":''),0,$e);}if($label){$content="<?php\n// $label\n//\n?>$content";}return
self::optimizePhp($content);}function
registerHelper($name,$callback){$this->helpers[strtolower($name)]=callback($callback);}function
registerHelperLoader($callback){$this->helperLoaders[]=callback($callback);}final
function
getHelpers(){return$this->helpers;}function
__call($name,$args){$lname=strtolower($name);if(!isset($this->helpers[$lname])){foreach($this->helperLoaders
as$loader){$helper=$loader($lname);if($helper){$this->registerHelper($lname,$helper);return$this->helpers[$lname]->invokeArgs($args);}}return
parent::__call($name,$args);}return$this->helpers[$lname]->invokeArgs($args);}function
setTranslator(NetteX\ITranslator$translator=NULL){$this->registerHelper('translate',$translator===NULL?NULL:array($translator,'translate'));return$this;}function
add($name,$value){if(array_key_exists($name,$this->params)){throw
new\XInvalidStateException("The variable '$name' already exists.");}$this->params[$name]=$value;}function
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
as$key=>$token){if(is_array($token)){if($token[0]===T_INLINE_HTML){$lastChar='';$res.=$token[1];}elseif($token[0]===T_CLOSE_TAG){$next=isset($tokens[$key+1])?$tokens[$key+1]:NULL;if(substr($res,-1)!=='<'&&preg_match('#^<\?php\s*$#',$php)){$php='';}elseif(is_array($next)&&$next[0]===T_OPEN_TAG){if($lastChar!==';'&&$lastChar!=='{'&&$lastChar!=='}'&&$lastChar!==':'&&$lastChar!=='/')$php.=$lastChar=';';if(substr($next[1],-1)==="\n")$php.="\n";$tokens->next();}elseif($next){$res.=preg_replace('#;?(\s)*$#','$1',$php).$token[1];$php='';}else{if($lastChar!=='}'&&$lastChar!==';')$php.=';';}}elseif($token[0]===T_ELSE||$token[0]===T_ELSEIF){if($tokens[$key+1]===':'&&$lastChar==='}')$php.=';';$lastChar='';$php.=$token[1];}else{if(!in_array($token[0],array(T_WHITESPACE,T_COMMENT,T_DOC_COMMENT,T_OPEN_TAG)))$lastChar='';$php.=$token[1];}}else{$php.=$lastChar=$token;}}return$res.$php;}}use
NetteX\Environment;use
NetteX\Caching\Cache;use
NetteX\Loaders\LimitedScope;class
FileTemplate
extends
Template
implements
IFileTemplate{public
static$cacheExpire=NULL;private
static$cacheStorage;private$file;function
__construct($file=NULL){if($file!==NULL){$this->setFile($file);}}function
setFile($file){if(!is_file($file)){throw
new\XFileNotFoundException("Missing template file '$file'.");}$this->file=$file;return$this;}function
getFile(){return$this->file;}function
render(){if($this->file==NULL){throw
new\XInvalidStateException("Template file name was not specified.");}$this->__set('template',$this);$shortName=str_replace(dirname(dirname($this->file)),'',$this->file);$cache=new
Cache($this->getCacheStorage(),'NetteX.FileTemplate');$key=trim(strtr($shortName,'\\/@','.._'),'.').'-'.md5($this->file);$cached=$content=$cache[$key];if($content===NULL){if(!$this->getFilters()){$this->onPrepareFilters($this);}if(!$this->getFilters()){LimitedScope::load($this->file,$this->getParams());return;}$content=$this->compile(file_get_contents($this->file),"file \xE2\x80\xA6$shortName");$cache->save($key,$content,array(Cache::FILES=>$this->file,Cache::EXPIRATION=>self::$cacheExpire,Cache::CONSTS=>'NetteX\Framework::REVISION'));$cache->release();$cached=$cache[$key];}if($cached!==NULL&&self::$cacheStorage
instanceof
TemplateCacheStorage){LimitedScope::load($cached['file'],$this->getParams());fclose($cached['handle']);}else{LimitedScope::evaluate($content,$this->getParams());}}static
function
setCacheStorage(NetteX\Caching\ICacheStorage$storage){self::$cacheStorage=$storage;}static
function
getCacheStorage(){if(self::$cacheStorage===NULL){$dir=Environment::getVariable('tempDir').'/cache';umask(0000);@mkdir($dir,0777);self::$cacheStorage=new
TemplateCacheStorage($dir);}return
self::$cacheStorage;}}class
CachingHelper
extends
NetteX\Object{private$frame;private$key;static
function
create($key,&$parents,$args=NULL){if($args){$key.=md5(serialize(array_intersect_key($args,range(0,count($args)))));if(array_key_exists('if',$args)&&!$args['if']){return$parents[]=new
self;}}if($parents){end($parents)->frame[Cache::ITEMS][]=$key;}$cache=self::getCache();if(isset($cache[$key])){echo$cache[$key];return
FALSE;}else{$obj=new
self;$obj->key=$key;$obj->frame=array(Cache::TAGS=>isset($args['tags'])?$args['tags']:NULL,Cache::EXPIRATION=>isset($args['expire'])?$args['expire']:'+ 7 days');ob_start();return$parents[]=$obj;}}function
save(){if($this->key!==NULL){$this->getCache()->save($this->key,ob_get_flush(),$this->frame);}$this->key=$this->frame=NULL;}function
addFile($file){$this->frame[Cache::FILES][]=$file;}protected
static
function
getCache(){return
Environment::getCache('NetteX.Template.Cache');}}use
NetteX\String;use
NetteX\Tokenizer;class
LatteFilter
extends
NetteX\Object{const
RE_STRING='\'(?:\\\\.|[^\'\\\\])*\'|"(?:\\\\.|[^"\\\\])*"';const
HTML_PREFIX='n:';private$handler;private$macroRe;private$input,$output;private$offset;private$quote;private$tags;public$context,$escape;const
CONTEXT_TEXT='text';const
CONTEXT_CDATA='cdata';const
CONTEXT_TAG='tag';const
CONTEXT_ATTRIBUTE='attribute';const
CONTEXT_NONE='none';const
CONTEXT_COMMENT='comment';function
setHandler($handler){$this->handler=$handler;return$this;}function
getHandler(){if($this->handler===NULL){$this->handler=new
LatteMacros;}return$this->handler;}function
__invoke($s){if(!$this->macroRe){$this->setDelimiters('\\{(?![\\s\'"{}])','\\}');}$this->context=LatteFilter::CONTEXT_NONE;$this->escape='$template->escape';$this->getHandler()->initialize($this,$s);$s=$this->parse("\n".$s);$this->getHandler()->finalize($s);return$s;}private
function
parse($s){$this->input=&$s;$this->offset=0;$this->output='';$this->tags=array();$len=strlen($s);while($this->offset<$len){$matches=$this->{"context$this->context"}();if(!$matches){break;}elseif(!empty($matches['macro'])){$code=$this->handler->macro($matches['macro']);if($code===FALSE){throw
new\XInvalidStateException("Unknown macro {{$matches['macro']}} on line $this->line.");}$nl=isset($matches['newline'])?"\n":'';if($nl&&$matches['indent']&&strncmp($code,'<?php echo ',11)){$this->output.="\n".$code;}else{$this->output.=$matches['indent'].$code.(substr($code,-2)==='?>'?$nl:'');}}else{$this->output.=$matches[0];}}foreach($this->tags
as$tag){if(!$tag->isMacro&&!empty($tag->attrs)){throw
new\XInvalidStateException("Missing end tag </$tag->name> for macro-attribute ".self::HTML_PREFIX.implode(' and '.self::HTML_PREFIX,array_keys($tag->attrs)).".");}}return$this->output.substr($this->input,$this->offset);}private
function
contextText(){$matches=$this->match('~
			(?:\n[ \t]*)?<(?P<closing>/?)(?P<tag>[a-z0-9:]+)|  ##  begin of HTML tag <tag </tag - ignores <!DOCTYPE
			<(?P<comment>!--)|           ##  begin of HTML comment <!--
			'.$this->macroRe.'           ##  curly tag
		~xsi');if(!$matches||!empty($matches['macro'])){}elseif(!empty($matches['comment'])){$this->context=self::CONTEXT_COMMENT;$this->escape='NetteX\Templates\TemplateHelpers::escapeHtmlComment';}elseif(empty($matches['closing'])){$tag=$this->tags[]=(object)NULL;$tag->name=$matches['tag'];$tag->closing=FALSE;$tag->isMacro=String::startsWith($tag->name,self::HTML_PREFIX);$tag->attrs=array();$tag->pos=strlen($this->output);$this->context=self::CONTEXT_TAG;$this->escape='NetteX\Templates\TemplateHelpers::escapeHtml';}else{do{$tag=array_pop($this->tags);if(!$tag){$tag=(object)NULL;$tag->name=$matches['tag'];$tag->isMacro=String::startsWith($tag->name,self::HTML_PREFIX);}}while(strcasecmp($tag->name,$matches['tag']));$this->tags[]=$tag;$tag->closing=TRUE;$tag->pos=strlen($this->output);$this->context=self::CONTEXT_TAG;$this->escape='NetteX\Templates\TemplateHelpers::escapeHtml';}return$matches;}private
function
contextCData(){$tag=end($this->tags);$matches=$this->match('~
			</'.$tag->name.'(?![a-z0-9:])| ##  end HTML tag </tag
			'.$this->macroRe.'           ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])){$tag->closing=TRUE;$tag->pos=strlen($this->output);$this->context=self::CONTEXT_TAG;$this->escape='NetteX\Templates\TemplateHelpers::escapeHtml';}return$matches;}private
function
contextTag(){$matches=$this->match('~
			(?P<end>\ ?/?>)(?P<tagnewline>[\ \t]*(?=\r|\n))?|  ##  end of HTML tag
			'.$this->macroRe.'|          ##  curly tag
			\s*(?P<attr>[^\s/>={]+)(?:\s*=\s*(?P<value>["\']|[^\s/>{]+))? ## begin of HTML attribute
		~xsi');if(!$matches||!empty($matches['macro'])){}elseif(!empty($matches['end'])){$tag=end($this->tags);$isEmpty=!$tag->closing&&(strpos($matches['end'],'/')!==FALSE||isset(NetteX\Web\Html::$emptyElements[strtolower($tag->name)]));if($isEmpty){$matches[0]=(NetteX\Web\Html::$xhtml?' />':'>').(isset($matches['tagnewline'])?$matches['tagnewline']:'');}if($tag->isMacro||!empty($tag->attrs)){if($tag->isMacro){$code=$this->handler->tagMacro(substr($tag->name,strlen(self::HTML_PREFIX)),$tag->attrs,$tag->closing);if($code===FALSE){throw
new\XInvalidStateException("Unknown tag-macro <$tag->name> on line $this->line.");}if($isEmpty){$code.=$this->handler->tagMacro(substr($tag->name,strlen(self::HTML_PREFIX)),$tag->attrs,TRUE);}}else{$code=substr($this->output,$tag->pos).$matches[0].(isset($matches['tagnewline'])?"\n":'');$code=$this->handler->attrsMacro($code,$tag->attrs,$tag->closing);if($code===FALSE){throw
new\XInvalidStateException("Unknown macro-attribute ".self::HTML_PREFIX.implode(' or '.self::HTML_PREFIX,array_keys($tag->attrs))." on line $this->line.");}if($isEmpty){$code=$this->handler->attrsMacro($code,$tag->attrs,TRUE);}}$this->output=substr_replace($this->output,$code,$tag->pos);$matches[0]='';}if($isEmpty){$tag->closing=TRUE;}if(!$tag->closing&&(strcasecmp($tag->name,'script')===0||strcasecmp($tag->name,'style')===0)){$this->context=self::CONTEXT_CDATA;$this->escape=strcasecmp($tag->name,'style')?'NetteX\Templates\TemplateHelpers::escapeJs':'NetteX\Templates\TemplateHelpers::escapeCss';}else{$this->context=self::CONTEXT_TEXT;$this->escape='NetteX\Templates\TemplateHelpers::escapeHtml';if($tag->closing)array_pop($this->tags);}}else{$name=$matches['attr'];$value=isset($matches['value'])?$matches['value']:'';if($isSpecial=String::startsWith($name,self::HTML_PREFIX)){$name=substr($name,strlen(self::HTML_PREFIX));}$tag=end($this->tags);if($isSpecial||$tag->isMacro){if($value==='"'||$value==="'"){if($matches=$this->match('~(.*?)'.$value.'~xsi')){$value=$matches[1];}}$tag->attrs[$name]=$value;$matches[0]='';}elseif($value==='"'||$value==="'"){$this->context=self::CONTEXT_ATTRIBUTE;$this->quote=$value;$this->escape=strncasecmp($name,'on',2)?(strcasecmp($name,'style')?'NetteX\Templates\TemplateHelpers::escapeHtml':'NetteX\Templates\TemplateHelpers::escapeHtmlCss'):'NetteX\Templates\TemplateHelpers::escapeHtmlJs';}}return$matches;}private
function
contextAttribute(){$matches=$this->match('~
			('.$this->quote.')|      ##  1) end of HTML attribute
			'.$this->macroRe.'           ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])){$this->context=self::CONTEXT_TAG;$this->escape='NetteX\Templates\TemplateHelpers::escapeHtml';}return$matches;}private
function
contextComment(){$matches=$this->match('~
			(--\s*>)|                    ##  1) end of HTML comment
			'.$this->macroRe.'           ##  curly tag
		~xsi');if($matches&&empty($matches['macro'])){$this->context=self::CONTEXT_TEXT;$this->escape='NetteX\Templates\TemplateHelpers::escapeHtml';}return$matches;}private
function
contextNone(){$matches=$this->match('~
			'.$this->macroRe.'           ##  curly tag
		~xsi');return$matches;}private
function
match($re){if($matches=String::match($this->input,$re,PREG_OFFSET_CAPTURE,$this->offset)){$this->output.=substr($this->input,$this->offset,$matches[0][1]-$this->offset);$this->offset=$matches[0][1]+strlen($matches[0][0]);foreach($matches
as$k=>$v)$matches[$k]=$v[0];}return$matches;}function
getLine(){return
substr_count($this->input,"\n",0,$this->offset);}function
setDelimiters($left,$right){$this->macroRe='
			(?P<indent>\n[\ \t]*)?
			'.$left.'
				(?P<macro>(?:'.self::RE_STRING.'|[^\'"]+?)*?)
			'.$right.'
			(?P<newline>[\ \t]*(?=\r|\n))?
		';return$this;}static
function
formatModifiers($var,$modifiers){trigger_error(__METHOD__.'() is deprecated; use LatteMacros::formatModifiers() instead.',E_USER_WARNING);return
LatteMacros::formatModifiers($var,$modifiers);}static
function
fetchToken(&$s){trigger_error(__METHOD__.'() is deprecated; use LatteMacros::fetchToken() instead.',E_USER_WARNING);return
LatteMacros::fetchToken($s);}static
function
formatArray($input,$prefix=''){trigger_error(__METHOD__.'() is deprecated; use LatteMacros::formatArray() instead.',E_USER_WARNING);return
LatteMacros::formatArray($input,$prefix);}static
function
formatString($s){trigger_error(__METHOD__.'() is deprecated; use LatteMacros::formatString() instead.',E_USER_WARNING);return
LatteMacros::formatString($s);}}class
LatteMacros
extends
NetteX\Object{public
static$defaultMacros=array('syntax'=>'%:macroSyntax%','/syntax'=>'%:macroSyntax%','block'=>'<?php %:macroBlock% ?>','/block'=>'<?php %:macroBlockEnd% ?>','capture'=>'<?php %:macroCapture% ?>','/capture'=>'<?php %:macroCaptureEnd% ?>','snippet'=>'<?php %:macroSnippet% ?>','/snippet'=>'<?php %:macroSnippetEnd% ?>','cache'=>'<?php %:macroCache% ?>','/cache'=>'<?php array_pop($_l->g->caches)->save(); } ?>','if'=>'<?php if (%%): ?>','elseif'=>'<?php elseif (%%): ?>','else'=>'<?php else: ?>','/if'=>'<?php endif ?>','ifset'=>'<?php if (isset(%%)): ?>','/ifset'=>'<?php endif ?>','elseifset'=>'<?php elseif (isset(%%)): ?>','foreach'=>'<?php foreach (%:macroForeach%): ?>','/foreach'=>'<?php endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>','for'=>'<?php for (%%): ?>','/for'=>'<?php endfor ?>','while'=>'<?php while (%%): ?>','/while'=>'<?php endwhile ?>','continueIf'=>'<?php if (%%) continue ?>','breakIf'=>'<?php if (%%) break ?>','first'=>'<?php if ($iterator->isFirst(%%)): ?>','/first'=>'<?php endif ?>','last'=>'<?php if ($iterator->isLast(%%)): ?>','/last'=>'<?php endif ?>','sep'=>'<?php if (!$iterator->isLast(%%)): ?>','/sep'=>'<?php endif ?>','include'=>'<?php %:macroInclude% ?>','extends'=>'<?php %:macroExtends% ?>','layout'=>'<?php %:macroExtends% ?>','plink'=>'<?php echo %:escape%(%:macroPlink%) ?>','link'=>'<?php echo %:escape%(%:macroLink%) ?>','ifCurrent'=>'<?php %:macroIfCurrent% ?>','widget'=>'<?php %:macroControl% ?>','control'=>'<?php %:macroControl% ?>','@href'=>' href="<?php echo %:escape%(%:macroLink%) ?>"','@class'=>'<?php if ($_l->tmp = trim(implode(" ", array_unique(%:formatArray%)))) echo \' class="\' . %:escape%($_l->tmp) . \'"\' ?>','@attr'=>'<?php if (($_l->tmp = (string) (%%)) !== \'\') echo \' @@="\' . %:escape%($_l->tmp) . \'"\' ?>','attr'=>'<?php echo NetteX\Web\Html::el(NULL)->%:macroAttr%attributes() ?>','contentType'=>'<?php %:macroContentType% ?>','status'=>'<?php NetteX\Environment::getHttpResponse()->setCode(%%) ?>','var'=>'<?php %:macroVar% ?>','assign'=>'<?php %:macroVar% ?>','default'=>'<?php %:macroDefault% ?>','dump'=>'<?php %:macroDump% ?>','debugbreak'=>'<?php %:macroDebugbreak% ?>','l'=>'{','r'=>'}','!_'=>'<?php echo %:macroTranslate% ?>','_'=>'<?php echo %:escape%(%:macroTranslate%) ?>','!='=>'<?php echo %:macroModifiers% ?>','='=>'<?php echo %:escape%(%:macroModifiers%) ?>','!$'=>'<?php echo %:macroDollar% ?>','$'=>'<?php echo %:escape%(%:macroDollar%) ?>','?'=>'<?php %:macroModifiers% ?>');const
RE_IDENTIFIER='[_a-zA-Z\x7F-\xFF][_a-zA-Z0-9\x7F-\xFF]*';const
T_WHITESPACE=T_WHITESPACE;const
T_COMMENT=T_COMMENT;const
T_SYMBOL=-1;const
T_NUMBER=-2;const
T_VARIABLE=-3;public$macros;private$tokenizer;private$filter;private$blocks=array();private$namedBlocks=array();private$extends;private$uniq;private$cacheCounter;const
BLOCK_NAMED=1;const
BLOCK_CAPTURE=2;const
BLOCK_ANONYMOUS=3;function
__construct(){$this->macros=self::$defaultMacros;$this->tokenizer=new
NetteX\Tokenizer(array(self::T_WHITESPACE=>'\s+',self::T_COMMENT=>'/\*.*?\*/',LatteFilter::RE_STRING,'true|false|null|and|or|xor|clone|new|instanceof|\([a-z]+\)',self::T_VARIABLE=>'\$[_a-z0-9\x7F-\xFF]+',self::T_NUMBER=>'[+-]?[0-9]+(?:\.[0-9]+)?(?:e[0-9]+)?',self::T_SYMBOL=>'[_a-z0-9\x7F-\xFF]+(?:-[_a-z0-9\x7F-\xFF]+)*','::|=>|[^"\']'),'i');}function
initialize($filter,&$s){$this->filter=$filter;$this->blocks=array();$this->namedBlocks=array();$this->extends=NULL;$this->uniq=substr(md5(uniqid('',TRUE)),0,10);$this->cacheCounter=0;$filter->context=LatteFilter::CONTEXT_TEXT;$filter->escape='NetteX\Templates\TemplateHelpers::escapeHtml';$s=String::replace($s,'#\\{\\*.*?\\*\\}[\r\n]*#s','');}function
finalize(&$s){if(count($this->blocks)===1){$s.=$this->macro('/block');}elseif($this->blocks){throw
new\XInvalidStateException("There are unclosed blocks.");}if($this->namedBlocks||$this->extends){$s='<?php
if ($_l->extends) {
	ob_start();
} elseif (isset($presenter, $control) && $presenter->isAjax()) {
	return NetteX\Templates\LatteMacros::renderSnippets($control, $_l, get_defined_vars());
}
?>'.$s.'<?php
if ($_l->extends) {
	ob_end_clean();
	NetteX\Templates\LatteMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render();
}
';}else{$s='<?php
if (isset($presenter, $control) && $presenter->isAjax()) {
	return NetteX\Templates\LatteMacros::renderSnippets($control, $_l, get_defined_vars());
}
?>'.$s;}if($this->namedBlocks){$uniq=$this->uniq;foreach(array_reverse($this->namedBlocks,TRUE)as$name=>$foo){$code=&$this->namedBlocks[$name];$namere=preg_quote($name,'#');$s=String::replace($s,"#{block $namere} \?>(.*)<\?php {/block $namere}#sU",function($matches)use($name,&$code,$uniq){list(,$content)=$matches;$func='_lb'.substr(md5($uniq.$name),0,10).'_'.preg_replace('#[^a-z0-9_]#i','_',$name);$code="//\n// block $name\n//\n"."if (!function_exists(\$_l->blocks[".var_export($name,TRUE)."][] = '$func')) { "."function $func(\$_l, \$_args) { extract(\$_args)\n?>$content<?php\n}}";return'';});}$s="<?php\n\n".implode("\n\n\n",$this->namedBlocks)."\n\n//\n// end of blocks\n//\n?>".$s;}$s="<?php\n".'$_l = NetteX\Templates\LatteMacros::initRuntime($template, '.var_export($this->extends,TRUE).', '.var_export($this->uniq,TRUE).'); unset($_extends);'."\n?>".$s;}function
macro($macro,$content='',$modifiers=''){if(func_num_args()===1){list(,$macro,$content,$modifiers)=String::match($macro,'#^(/?[a-z0-9.:]+)?(.*?)(\\|[a-z](?:'.LatteFilter::RE_STRING.'|[^\'"]+)*)?$()#is');$content=trim($content);}if($macro===''){$macro=substr($content,0,2);if(!isset($this->macros[$macro])){$macro=substr($content,0,1);if(!isset($this->macros[$macro])){return
FALSE;}}$content=substr($content,strlen($macro));}elseif(!isset($this->macros[$macro])){return
FALSE;}$This=$this;return
String::replace($this->macros[$macro],'#%(.*?)%#',function($m)use($This,$content,$modifiers){if($m[1]){return
callback($m[1][0]===':'?array($This,substr($m[1],1)):$m[1])->invoke($content,$modifiers);}else{return$This->formatMacroArgs($content,'#');}});}function
tagMacro($name,$attrs,$closing){$knownTags=array('include'=>'block','for'=>'each','block'=>'name','if'=>'cond','elseif'=>'cond');return$this->macro($closing?"/$name":$name,isset($knownTags[$name],$attrs[$knownTags[$name]])?$attrs[$knownTags[$name]]:preg_replace("#'([^\\'$]+)'#",'$1',substr(var_export($attrs,TRUE),8,-1)),isset($attrs['modifiers'])?$attrs['modifiers']:'');}function
attrsMacro($code,$attrs,$closing){foreach($attrs
as$name=>$content){if(substr($name,0,5)==='attr-'){if(!$closing){$pos=strrpos($code,'>');if($code[$pos-1]==='/')$pos--;$code=substr_replace($code,str_replace('@@',substr($name,5),$this->macro("@attr",$content)),$pos,0);}unset($attrs[$name]);}}$left=$right='';foreach($this->macros
as$name=>$foo){if($name[0]==='@'){$name=substr($name,1);if(isset($attrs[$name])){if(!$closing){$pos=strrpos($code,'>');if($code[$pos-1]==='/')$pos--;$code=substr_replace($code,$this->macro("@$name",$attrs[$name]),$pos,0);}unset($attrs[$name]);}}if(!isset($this->macros["/$name"])){continue;}$macro=$closing?"/$name":$name;if(isset($attrs[$name])){if($closing){$right.=$this->macro($macro);}else{$left=$this->macro($macro,$attrs[$name]).$left;}}$innerName="inner-$name";if(isset($attrs[$innerName])){if($closing){$left.=$this->macro($macro);}else{$right=$this->macro($macro,$attrs[$innerName]).$right;}}$tagName="tag-$name";if(isset($attrs[$tagName])){$left=$this->macro($name,$attrs[$tagName]).$left;$right.=$this->macro("/$name");}unset($attrs[$name],$attrs[$innerName],$attrs[$tagName]);}return$attrs?FALSE:$left.$code.$right;}function
macroDollar($var,$modifiers){return$this->formatModifiers($this->formatMacroArgs('$'.$var),$modifiers);}function
macroTranslate($var,$modifiers){return$this->formatModifiers($this->formatMacroArgs($var),'|translate'.$modifiers);}function
macroSyntax($var){switch($var){case'':case'latte':$this->filter->setDelimiters('\\{(?![\\s\'"{}])','\\}');break;case'double':$this->filter->setDelimiters('\\{\\{(?![\\s\'"{}])','\\}\\}');break;case'asp':$this->filter->setDelimiters('<%\s*','\s*%>');break;case'python':$this->filter->setDelimiters('\\{[{%]\s*','\s*[%}]\\}');break;case'off':$this->filter->setDelimiters('[^\x00-\xFF]','');break;default:throw
new\XInvalidStateException("Unknown syntax '$var' on line {$this->filter->line}.");}}function
macroInclude($content,$modifiers,$isDefinition=FALSE){$destination=$this->fetchToken($content);$params=$this->formatArray($content).($content?' + ':'');if($destination===NULL){throw
new\XInvalidStateException("Missing destination in {include} on line {$this->filter->line}.");}elseif($destination[0]==='#'){$destination=ltrim($destination,'#');if(!String::match($destination,'#^'.self::RE_IDENTIFIER.'$#')){throw
new\XInvalidStateException("Included block name must be alphanumeric string, '$destination' given on line {$this->filter->line}.");}$parent=$destination==='parent';if($destination==='parent'||$destination==='this'){$item=end($this->blocks);while($item&&$item[0]!==self::BLOCK_NAMED)$item=prev($this->blocks);if(!$item){throw
new\XInvalidStateException("Cannot include $destination block outside of any block on line {$this->filter->line}.");}$destination=$item[1];}$name=var_export($destination,TRUE);$params.=$isDefinition?'get_defined_vars()':'$template->getParams()';$cmd=isset($this->namedBlocks[$destination])&&!$parent?"call_user_func(reset(\$_l->blocks[$name]), \$_l, $params)":'NetteX\Templates\LatteMacros::callBlock'.($parent?'Parent':'')."(\$_l, $name, $params)";return$modifiers?"ob_start(); $cmd; echo ".$this->formatModifiers('ob_get_clean()',$modifiers):$cmd;}else{$destination=$this->formatString($destination);$cmd='NetteX\Templates\LatteMacros::includeTemplate('.$destination.', '.$params.'$template->getParams(), $_l->templates['.var_export($this->uniq,TRUE).'])';return$modifiers?'echo '.$this->formatModifiers($cmd.'->__toString(TRUE)',$modifiers):$cmd.'->render()';}}function
macroExtends($content){$destination=$this->fetchToken($content);if($destination===NULL){throw
new\XInvalidStateException("Missing destination in {extends} on line {$this->filter->line}.");}if(!empty($this->blocks)){throw
new\XInvalidStateException("{extends} must be placed outside any block; on line {$this->filter->line}.");}if($this->extends!==NULL){throw
new\XInvalidStateException("Multiple {extends} declarations are not allowed; on line {$this->filter->line}.");}$this->extends=$destination!=='none';return$this->extends?'$_l->extends = '.($destination==='auto'?'$layout':$this->formatString($destination)):'';}function
macroBlock($content,$modifiers){$name=$this->fetchToken($content);if($name===NULL){$this->blocks[]=array(self::BLOCK_ANONYMOUS,NULL,$modifiers);return$modifiers===''?'':'ob_start()';}else{$name=ltrim($name,'#');if(!String::match($name,'#^'.self::RE_IDENTIFIER.'$#')){throw
new\XInvalidStateException("Block name must be alphanumeric string, '$name' given on line {$this->filter->line}.");}elseif(isset($this->namedBlocks[$name])){throw
new\XInvalidStateException("Cannot redeclare block '$name'; on line {$this->filter->line}.");}$top=empty($this->blocks);$this->namedBlocks[$name]=$name;$this->blocks[]=array(self::BLOCK_NAMED,$name,'');if($name[0]==='_'){$tag=$this->fetchToken($content);$tag=trim($tag,'<>');$namePhp=var_export(substr($name,1),TRUE);if(!$tag)$tag='div';return"?><$tag id=\"<?php echo \$control->getSnippetId($namePhp) ?>\"><?php ".$this->macroInclude('#'.$name,$modifiers)." ?></$tag><?php {block $name}";}elseif(!$top){return$this->macroInclude('#'.$name,$modifiers,TRUE)."{block $name}";}elseif($this->extends){return"{block $name}";}else{return'if (!$_l->extends) { '.$this->macroInclude('#'.$name,$modifiers,TRUE)."; } {block $name}";}}}function
macroBlockEnd($content){list($type,$name,$modifiers)=array_pop($this->blocks);if($type===self::BLOCK_CAPTURE){$this->blocks[]=array($type,$name,$modifiers);return$this->macroCaptureEnd($content);}if(($type!==self::BLOCK_NAMED&&$type!==self::BLOCK_ANONYMOUS)||($content&&$content!==$name)){throw
new\XInvalidStateException("Tag {/block $content} was not expected here on line {$this->filter->line}.");}elseif($type===self::BLOCK_NAMED){return"{/block $name}";}else{return$modifiers===''?'':'echo '.$this->formatModifiers('ob_get_clean()',$modifiers);}}function
macroSnippet($content){return$this->macroBlock('_'.$content,'');}function
macroSnippetEnd($content){return$this->macroBlockEnd('','');}function
macroCapture($content,$modifiers){$name=$this->fetchToken($content);if(substr($name,0,1)!=='$'){throw
new\XInvalidStateException("Invalid capture block parameter '$name' on line {$this->filter->line}.");}$this->blocks[]=array(self::BLOCK_CAPTURE,$name,$modifiers);return'ob_start()';}function
macroCaptureEnd($content){list($type,$name,$modifiers)=array_pop($this->blocks);if($type!==self::BLOCK_CAPTURE||($content&&$content!==$name)){throw
new\XInvalidStateException("Tag {/capture $content} was not expected here on line {$this->filter->line}.");}return$name.'='.$this->formatModifiers('ob_get_clean()',$modifiers);}function
macroCache($content){return'if (NetteX\Templates\CachingHelper::create('.var_export($this->uniq.':'.$this->cacheCounter++,TRUE).', $_l->g->caches'.$this->formatArray($content,', ').')) {';}function
macroForeach($content){return'$iterator = $_l->its[] = new NetteX\SmartCachingIterator('.preg_replace('# +as +#i',') as ',$this->formatMacroArgs($content),1);}function
macroAttr($content){return
String::replace($content.' ','#\)\s+#',')->');}function
macroContentType($content){if(strpos($content,'html')!==FALSE){$this->filter->escape='NetteX\Templates\TemplateHelpers::escapeHtml';$this->filter->context=LatteFilter::CONTEXT_TEXT;}elseif(strpos($content,'xml')!==FALSE){$this->filter->escape='NetteX\Templates\TemplateHelpers::escapeXml';$this->filter->context=LatteFilter::CONTEXT_NONE;}elseif(strpos($content,'javascript')!==FALSE){$this->filter->escape='NetteX\Templates\TemplateHelpers::escapeJs';$this->filter->context=LatteFilter::CONTEXT_NONE;}elseif(strpos($content,'css')!==FALSE){$this->filter->escape='NetteX\Templates\TemplateHelpers::escapeCss';$this->filter->context=LatteFilter::CONTEXT_NONE;}elseif(strpos($content,'plain')!==FALSE){$this->filter->escape='';$this->filter->context=LatteFilter::CONTEXT_NONE;}else{$this->filter->escape='$template->escape';$this->filter->context=LatteFilter::CONTEXT_NONE;}return
strpos($content,'/')?'NetteX\Environment::getHttpResponse()->setHeader("Content-Type", "'.$content.'")':'';}function
macroDump($content){return'NetteX\Debug::barDump('.($content?'array('.var_export($this->formatMacroArgs($content),TRUE)." => $content)":'get_defined_vars()').', "Template " . str_replace(dirname(dirname($template->getFile())), "\xE2\x80\xA6", $template->getFile()))';}function
macroDebugbreak(){return'if (function_exists("debugbreak")) debugbreak(); elseif (function_exists("xdebug_break")) xdebug_break()';}function
macroControl($content){$pair=$this->fetchToken($content);if($pair===NULL){throw
new\XInvalidStateException("Missing control name in {control} on line {$this->filter->line}.");}$pair=explode(':',$pair,2);$name=$this->formatString($pair[0]);$method=isset($pair[1])?ucfirst($pair[1]):'';$method=String::match($method,'#^('.self::RE_IDENTIFIER.'|)$#')?"render$method":"{\"render$method\"}";$param=$this->formatArray($content);if(strpos($content,'=>')===FALSE)$param=substr($param,6,-1);return($name[0]==='$'?"if (is_object($name)) {$name}->$method($param); else ":'')."\$control->getWidget($name)->$method($param)";}function
macroLink($content,$modifiers){return$this->formatModifiers('$control->link('.$this->formatLink($content).')',$modifiers);}function
macroPlink($content,$modifiers){return$this->formatModifiers('$presenter->link('.$this->formatLink($content).')',$modifiers);}function
macroIfCurrent($content){return($content?'try { $presenter->link('.$this->formatLink($content).'); } catch (NetteX\Application\InvalidLinkException $e) {}':'').'; if ($presenter->getLastCreatedRequestFlag("current")):';}private
function
formatLink($content){return$this->formatString($this->fetchToken($content)).$this->formatArray($content,', ');}function
macroVar($content,$modifiers,$extract=FALSE){$out='';$var=TRUE;foreach($this->parseMacro($content)as$rec){list($token,$name,$depth)=$rec;if($var&&($name===self::T_SYMBOL||$name===self::T_VARIABLE)){if($extract){$token="'".trim($token,"'$")."'";}else{$token='$'.trim($token,"'$");}}elseif(($token==='='||$token==='=>')&&$depth===0){$token=$extract?'=>':'=';$var=FALSE;}elseif($token===','&&$depth===0){$token=$extract?',':';';$var=TRUE;}$out.=$token;}return$out;}function
macroDefault($content){return'extract(array('.$this->macroVar($content,'',TRUE).'), EXTR_SKIP)';}function
macroModifiers($content,$modifiers){return$this->formatModifiers($this->formatMacroArgs($content),$modifiers);}function
escape($content){return$this->filter->escape;}function
formatModifiers($var,$modifiers){if(!$modifiers)return$var;$inside=FALSE;foreach($this->parseMacro(ltrim($modifiers,'|'))as$rec){list($token,$name)=$rec;if($name===self::T_WHITESPACE){$var=rtrim($var).' ';}elseif(!$inside){if($name===self::T_SYMBOL){$var="\$template->".trim($token,"'")."($var";$inside=TRUE;}else{throw
new\XInvalidStateException("Modifier name must be alphanumeric string, '$token' given.");}}else{if($token===':'||$token===','){$var=$var.', ';}elseif($token==='|'){$var=$var.')';$inside=FALSE;}else{$var.=$token;}}}return$inside?"$var)":$var;}function
fetchToken(&$s){if($matches=String::match($s,'#^((?>'.LatteFilter::RE_STRING.'|[^\'"\s,]+)+)\s*,?\s*(.*)$#')){$s=$matches[2];return$matches[1];}return
NULL;}function
formatMacroArgs($input){$out='';foreach($this->parseMacro($input)as$token){$out.=$token[0];}return$out;}function
formatArray($input,$prefix=''){$tokens=$this->parseMacro($input);if(!$tokens){return'';}$out='';$expand=NULL;$tokens[]=NULL;foreach($tokens
as$rec){list($token,$name,$depth)=$rec;if($token==='(expand)'&&$depth===0){$expand=TRUE;$token='),';}elseif($expand&&($token===','||$token===NULL)&&!$depth){$expand=FALSE;$token=', array(';}$out.=$token;}return$prefix.($expand===NULL?"array($out)":"array_merge(array($out))");}function
formatString($s){static$keywords=array('true'=>1,'false'=>1,'null'=>1);return(is_numeric($s)||strspn($s,'\'"$')||isset($keywords[strtolower($s)]))?$s:'"'.$s.'"';}private
function
parseMacro($input){$this->tokenizer->tokenize($input);$this->tokenizer->tokens[]=NULL;$inTernary=$lastSymbol=$prev=NULL;$tokens=$arrays=array();$n=-1;while(++$n<count($this->tokenizer->tokens)){list($token,$name)=$current=$this->tokenizer->tokens[$n];$depth=count($arrays);if($name===self::T_COMMENT){continue;}elseif($name===self::T_WHITESPACE){$current[2]=$depth;$tokens[]=$current;continue;}elseif($name===self::T_SYMBOL&&in_array($prev[0],array(',','(','[','=','=>',':','?',NULL),TRUE)){$lastSymbol=count($tokens);}elseif(is_int($lastSymbol)&&in_array($token,array(',',')',']','=','=>',':','|',NULL),TRUE)){$tokens[$lastSymbol][0]="'".$tokens[$lastSymbol][0]."'";$lastSymbol=NULL;}else{$lastSymbol=NULL;}if($token==='?'){$inTernary=$depth;}elseif($token===':'){$inTernary=NULL;}elseif($inTernary===$depth&&($token===','||$token===')'||$token===']'||$token===NULL)){$tokens[]=array(':',NULL,$depth);$tokens[]=array('null',NULL,$depth);$inTernary=NULL;}if($token==='['){if($arrays[]=$prev[0]!==']'&&$prev[1]!==self::T_SYMBOL&&$prev[1]!==self::T_VARIABLE){$tokens[]=array('array',NULL,$depth);$current=array('(',NULL);}}elseif($token===']'){if(array_pop($arrays)===TRUE){$current=array(')',NULL);}}elseif($token==='('){$arrays[]='(';}elseif($token===')'){array_pop($arrays);}if($current){$current[2]=$depth;$tokens[]=$prev=$current;}}return$tokens;}static
function
callBlock($context,$name,$params){if(empty($context->blocks[$name])){throw
new\XInvalidStateException("Cannot include undefined block '$name'.");}$block=reset($context->blocks[$name]);$block($context,$params);}static
function
callBlockParent($context,$name,$params){if(empty($context->blocks[$name])||($block=next($context->blocks[$name]))===FALSE){throw
new\XInvalidStateException("Cannot include undefined parent block '$name'.");}$block($context,$params);}static
function
includeTemplate($destination,$params,$template){if($destination
instanceof
ITemplate){$tpl=$destination;}elseif($destination==NULL){throw
new\InvalidArgumentException("Template file name was not specified.");}else{$tpl=clone$template;if($template
instanceof
IFileTemplate){if(substr($destination,0,1)!=='/'&&substr($destination,1,1)!==':'){$destination=dirname($template->getFile()).'/'.$destination;}$tpl->setFile($destination);}}$tpl->setParams($params);return$tpl;}static
function
initRuntime($template,$extends,$realFile){$local=(object)NULL;if(isset($template->_l)){$local->blocks=&$template->_l->blocks;$local->templates=&$template->_l->templates;}$local->templates[$realFile]=$template;$local->extends=is_bool($extends)?$extends:(empty($template->_extends)?FALSE:$template->_extends);unset($template->_l,$template->_extends);if(!isset($template->_g)){$template->_g=(object)NULL;}$local->g=$template->_g;if(!empty($local->g->caches)){end($local->g->caches)->addFile($template->getFile());}return$local;}static
function
renderSnippets($control,$local,$params){$payload=$control->getPresenter()->getPayload();if(isset($local->blocks)){foreach($local->blocks
as$name=>$function){if($name[0]!=='_'||!$control->isControlInvalid(substr($name,1)))continue;ob_start();$function=reset($function);$function($local,$params);$payload->snippets[$control->getSnippetId(substr($name,1))]=ob_get_clean();}}if($control
instanceof
NetteX\Application\Control){foreach($control->getComponents(FALSE,'NetteX\Application\Control')as$child){if($child->isControlInvalid()){$child->render();}}}}}final
class
TemplateFilters{final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
removePhp($s){return
String::replace($s,'#\x01@php:p\d+@\x02#','<?php ?>');}static
function
relativeLinks($s){return
String::replace($s,'#(src|href|action)\s*=\s*(["\'])(?![a-z]+:|[\x01/\\#])#','$1=$2<?php echo \\$baseUri ?>');}static
function
netteLinks($s){return
String::replace($s,'#(src|href|action)\s*=\s*(["\'])(nette:.*?)([\#"\'])#',function($m){list(,$attr,$quote,$uri,$fragment)=$m;$parts=parse_url($uri);if(isset($parts['scheme'])&&$parts['scheme']==='nette'){return$attr.'='.$quote.'<?php echo $template->escape($control->'."link('".(isset($parts['path'])?$parts['path']:'this!').(isset($parts['query'])?'?'.$parts['query']:'').'\'))?>'.$fragment;}else{return$m[0];}});}public
static$texy;static
function
texyElements($s){return
String::replace($s,'#<texy([^>]*)>(.*?)</texy>#s',function($m){list(,$mAttrs,$mContent)=$m;$attrs=array();if($mAttrs){foreach(String::matchAll($mAttrs,'#([a-z0-9:-]+)\s*(?:=\s*(\'[^\']*\'|"[^"]*"|[^\'"\s]+))?()#isu')as$m){$key=strtolower($m[1]);$val=$m[2];if($val==NULL)$attrs[$key]=TRUE;elseif($val{0}==='\''||$val{0}==='"')$attrs[$key]=html_entity_decode(substr($val,1,-1),ENT_QUOTES,'UTF-8');else$attrs[$key]=html_entity_decode($val,ENT_QUOTES,'UTF-8');}}return
TemplateFilters::$texy->process($m[2]);});}}use
NetteX\Forms\Form;use
NetteX\Web\Html;final
class
TemplateHelpers{public
static$dateFormat='%x';final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
loader($helper){$callback=callback('NetteX\Templates\TemplateHelpers',$helper);if($callback->isCallable()){return$callback;}$callback=callback('NetteX\String',$helper);if($callback->isCallable()){return$callback;}}static
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
addcslashes($s,"\x00..\x2C./:;<=>?@[\\]^`{|}~");}static
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
str_replace(']]>',']]\x3E',NetteX\Json::encode($s));}static
function
escapeHtmlJs($s){return
htmlSpecialChars(self::escapeJs($s),ENT_QUOTES);}static
function
strip($s){return
String::replace($s,'#(</textarea|</pre|</script|^).*?(?=<textarea|<pre|<script|$)#si',function($m){return
trim(preg_replace("#[ \t\r\n]+#"," ",$m[0]));});}static
function
indent($s,$level=1,$chars="\t"){if($level>=1){$s=String::replace($s,'#<(textarea|pre).*?</\\1#si',function($m){return
strtr($m[0]," \t\r\n","\x1F\x1E\x1D\x1A");});$s=String::indent($s,$level,$chars);$s=strtr($s,"\x1F\x1E\x1D\x1A"," \t\r\n");}return$s;}static
function
date($time,$format=NULL){if($time==NULL){return
NULL;}if(!isset($format)){$format=self::$dateFormat;}$time=NetteX\Tools::createDateTime($time);return
strpos($format,'%')===FALSE?$time->format($format):strftime($format,$time->format('U'));}static
function
bytes($bytes,$precision=2){$bytes=round($bytes);$units=array('B','kB','MB','GB','TB','PB');foreach($units
as$unit){if(abs($bytes)<1024||$unit===end($units))break;$bytes=$bytes/1024;}return
round($bytes,$precision).' '.$unit;}static
function
length($var){return
is_string($var)?String::length($var):count($var);}static
function
replace($subject,$search,$replacement=''){return
str_replace($search,$replacement,$subject);}static
function
null($value){return'';}}class
TemplateCacheStorage
extends
NetteX\Caching\FileStorage{protected
function
readData($meta){return
array('file'=>$meta[self::FILE],'handle'=>$meta[self::HANDLE]);}protected
function
getCacheFile($key){return
parent::getCacheFile($key).'.php';}}}namespace NetteX{use
NetteX;final
class
ArrayTools{final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
get(array$arr,$key,$default=NULL){foreach(is_array($key)?$key:array($key)as$k){if(is_array($arr)&&array_key_exists($k,$arr)){$arr=$arr[$k];}else{return$default;}}return$arr;}static
function&getRef(&$arr,$key){foreach(is_array($key)?$key:array($key)as$k){if(is_array($arr)||$arr===NULL){$arr=&$arr[$k];}else{throw
new\InvalidArgumentException('Traversed item is not an array.');}}return$arr;}static
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
grep(array$arr,$pattern,$flags=0){Debug::tryError();$res=preg_grep($pattern,$arr,$flags);String::catchPregError($pattern);return$res;}}class
Context
extends
FreezableObject
implements
IContext{private$registry=array();private$factories=array();function
addService($name,$service,$singleton=TRUE,array$options=NULL){$this->updating();if(!is_string($name)||$name===''){throw
new\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}$lower=strtolower($name);if(isset($this->registry[$lower])){throw
new
AmbiguousServiceException("Service named '$name' has already been registered.");}if(is_object($service)&&!($service
instanceof\Closure||$service
instanceof
Callback)){if(!$singleton||$options){throw
new\InvalidArgumentException("Service named '$name' is an instantiated object and must therefore be singleton without options.");}$this->registry[$lower]=$service;}else{if(!$service){throw
new\InvalidArgumentException("Service named '$name' is empty.");}$this->factories[$lower]=array($service,$singleton,$options);}}function
removeService($name){$this->updating();if(!is_string($name)||$name===''){throw
new\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}$lower=strtolower($name);unset($this->registry[$lower],$this->factories[$lower]);}function
getService($name,array$options=NULL){if(!is_string($name)||$name===''){throw
new\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}$lower=strtolower($name);if(isset($this->registry[$lower])){if($options){throw
new\InvalidArgumentException("Service named '$name' is singleton and therefore can not have options.");}return$this->registry[$lower];}elseif(isset($this->factories[$lower])){list($factory,$singleton,$defOptions)=$this->factories[$lower];if($singleton&&$options){throw
new\InvalidArgumentException("Service named '$name' is singleton and therefore can not have options.");}elseif($defOptions){$options=$options?$options+$defOptions:$defOptions;}if(is_string($factory)&&strpos($factory,':')===FALSE){if(!class_exists($factory)){throw
new
AmbiguousServiceException("Cannot instantiate service '$name', class '$factory' not found.");}$service=new$factory;if($options&&method_exists($service,'setOptions')){$service->setOptions($options);}}else{$factory=callback($factory);if(!$factory->isCallable()){throw
new\XInvalidStateException("Cannot instantiate service '$name', handler '$factory' is not callable.");}$service=$factory($options);if(!is_object($service)){throw
new
AmbiguousServiceException("Cannot instantiate service '$name', value returned by '$factory' is not object.");}}if($singleton){$this->registry[$lower]=$service;unset($this->factories[$lower]);}return$service;}else{throw
new\XInvalidStateException("Service '$name' not found.");}}function
hasService($name,$created=FALSE){if(!is_string($name)||$name===''){throw
new\InvalidArgumentException("Service name must be a non-empty string, ".gettype($name)." given.");}$lower=strtolower($name);return
isset($this->registry[$lower])||(!$created&&isset($this->factories[$lower]));}}class
AmbiguousServiceException
extends\Exception{}use
RecursiveIteratorIterator;class
Finder
extends
Object
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
new\XInvalidStateException('Directory to search has already been specified.');}if(!is_array($path)){$path=func_get_args();}$this->paths=$path;$this->cursor=&$this->exclude;return$this;}function
childFirst(){$this->order=RecursiveIteratorIterator::CHILD_FIRST;return$this;}private
static
function
buildPattern($masks){$pattern=array();foreach($masks
as$mask){$mask=rtrim(strtr($mask,'\\','/'),'/');$prefix='';if($mask===''){continue;}elseif($mask==='*'){return
NULL;}elseif($mask[0]==='/'){$mask=ltrim($mask,'/');$prefix='(?<=^/)';}$pattern[]=$prefix.strtr(preg_quote($mask,'#'),array('\*\*'=>'.*','\*'=>'[^/]*','\?'=>'[^/]','\[\!'=>'[^','\['=>'[','\]'=>']','\-'=>'-'));}return$pattern?'#/('.implode('|',$pattern).')$#i':NULL;}function
getIterator(){if(!$this->paths){throw
new\XInvalidStateException('Call in() or from() to specify directory to search.');}elseif(count($this->paths)===1){return$this->buildIterator($this->paths[0]);}else{$iterator=new\AppendIterator();foreach($this->paths
as$path){$iterator->append($this->buildIterator($path));}return$iterator;}}private
function
buildIterator($path){if(PHP_VERSION_ID<50301){$iterator=new
RecursiveDirectoryIteratorFixed($path);}else{$iterator=new\RecursiveDirectoryIterator($path,\RecursiveDirectoryIterator::FOLLOW_SYMLINKS);}if($this->exclude){$filters=$this->exclude;$iterator=new
RecursiveCallbackFilterIterator($iterator,function($file)use($filters){if(!$file->isFile()){foreach($filters
as$filter){if(!call_user_func($filter,$file)){return
FALSE;}}}return
TRUE;});}if($this->maxDepth!==0){$iterator=new
RecursiveIteratorIterator($iterator,$this->order);$iterator->setMaxDepth($this->maxDepth);}if($this->groups){$groups=$this->groups;$iterator=new
CallbackFilterIterator($iterator,function($file)use($groups){foreach($groups
as$filters){foreach($filters
as$filter){if(!call_user_func($filter,$file)){continue
2;}}return
TRUE;}return
FALSE;});}return$iterator;}function
exclude($masks){if(!is_array($masks)){$masks=func_get_args();}$pattern=self::buildPattern($masks);if($pattern){$this->filter(function($file)use($pattern){return!preg_match($pattern,'/'.strtr($file->getSubPathName(),'\\','/'));});}return$this;}function
filter($callback){$this->cursor[]=$callback;return$this;}function
limitDepth($depth){$this->maxDepth=$depth;return$this;}function
size($operator,$size=NULL){if(func_num_args()===1){if(!preg_match('#^(?:([=<>!]=?|<>)\s*)?((?:\d*\.)?\d+)\s*(K|M|G|)B?$#i',$operator,$matches)){throw
new\InvalidArgumentException('Invalid size predicate format.');}list(,$operator,$size,$unit)=$matches;static$units=array(''=>1,'k'=>1e3,'m'=>1e6,'g'=>1e9);$size*=$units[strtolower($unit)];$operator=$operator?$operator:'=';}return$this->filter(function($file)use($operator,$size){return
Tools::compare($file->getSize(),$operator,$size);});}function
date($operator,$date=NULL){if(func_num_args()===1){if(!preg_match('#^(?:([=<>!]=?|<>)\s*)?(.+)$#i',$operator,$matches)){throw
new\InvalidArgumentException('Invalid date predicate format.');}list(,$operator,$date)=$matches;$operator=$operator?$operator:'=';}$date=Tools::createDateTime($date)->format('U');return$this->filter(function($file)use($operator,$date){return
Tools::compare($file->getMTime(),$operator,$date);});}}if(PHP_VERSION_ID<50301){class
RecursiveDirectoryIteratorFixed
extends\RecursiveDirectoryIterator{function
hasChildren(){return
parent::hasChildren(TRUE);}}}class
Image
extends
Object{const
ENLARGE=1;const
STRETCH=2;const
FIT=0;const
FILL=4;const
JPEG=IMAGETYPE_JPEG;const
PNG=IMAGETYPE_PNG;const
GIF=IMAGETYPE_GIF;const
EMPTY_GIF="GIF89a\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;";public
static$useImageMagick=FALSE;private$image;static
function
rgb($red,$green,$blue,$transparency=0){return
array('red'=>max(0,min(255,(int)$red)),'green'=>max(0,min(255,(int)$green)),'blue'=>max(0,min(255,(int)$blue)),'alpha'=>max(0,min(127,(int)$transparency)));}static
function
fromFile($file,&$format=NULL){if(!extension_loaded('gd')){throw
new\Exception("PHP extension GD is not loaded.");}$info=@getimagesize($file);if(self::$useImageMagick&&(empty($info)||$info[0]*$info[1]>9e5)){return
new
ImageMagick($file,$format);}switch($format=$info[2]){case
self::JPEG:return
new
static(imagecreatefromjpeg($file));case
self::PNG:return
new
static(imagecreatefrompng($file));case
self::GIF:return
new
static(imagecreatefromgif($file));default:if(self::$useImageMagick){return
new
ImageMagick($file,$format);}throw
new\Exception("Unknown image type or file '$file' not found.");}}static
function
getFormatFromString($s){if(strncmp($s,"\xff\xd8",2)===0){return
self::JPEG;}if(strncmp($s,"\x89PNG",4)===0){return
self::PNG;}if(strncmp($s,"GIF",3)===0){return
self::GIF;}return
NULL;}static
function
fromString($s,&$format=NULL){if(!extension_loaded('gd')){throw
new\Exception("PHP extension GD is not loaded.");}$format=static::getFormatFromString($s);return
new
static(imagecreatefromstring($s));}static
function
fromBlank($width,$height,$color=NULL){if(!extension_loaded('gd')){throw
new\Exception("PHP extension GD is not loaded.");}$width=(int)$width;$height=(int)$height;if($width<1||$height<1){throw
new\InvalidArgumentException('Image width and height must be greater than zero.');}$image=imagecreatetruecolor($width,$height);if(is_array($color)){$color+=array('alpha'=>0);$color=imagecolorallocatealpha($image,$color['red'],$color['green'],$color['blue'],$color['alpha']);imagealphablending($image,FALSE);imagefilledrectangle($image,0,0,$width-1,$height-1,$color);imagealphablending($image,TRUE);}return
new
static($image);}function
__construct($image){$this->setImageResource($image);}function
getWidth(){return
imagesx($this->image);}function
getHeight(){return
imagesy($this->image);}protected
function
setImageResource($image){if(!is_resource($image)||get_resource_type($image)!=='gd'){throw
new\InvalidArgumentException('Image is not valid.');}$this->image=$image;return$this;}function
getImageResource(){return$this->image;}function
resize($width,$height,$flags=self::FIT){list($newWidth,$newHeight)=self::calculateSize($this->getWidth(),$this->getHeight(),$width,$height,$flags);if($newWidth!==$this->getWidth()||$newHeight!==$this->getHeight()){$newImage=self::fromBlank($newWidth,$newHeight,self::RGB(0,0,0,127))->getImageResource();imagecopyresampled($newImage,$this->getImageResource(),0,0,0,0,$newWidth,$newHeight,$this->getWidth(),$this->getHeight());$this->image=$newImage;}if($width<0||$height<0){$newImage=self::fromBlank($newWidth,$newHeight,self::RGB(0,0,0,127))->getImageResource();imagecopyresampled($newImage,$this->getImageResource(),0,0,$width<0?$newWidth-1:0,$height<0?$newHeight-1:0,$newWidth,$newHeight,$width<0?-$newWidth:$newWidth,$height<0?-$newHeight:$newHeight);$this->image=$newImage;}return$this;}static
function
calculateSize($srcWidth,$srcHeight,$newWidth,$newHeight,$flags=self::FIT){if(substr($newWidth,-1)==='%'){$newWidth=round($srcWidth/100*abs($newWidth));$flags|=self::ENLARGE;$percents=TRUE;}else{$newWidth=(int)abs($newWidth);}if(substr($newHeight,-1)==='%'){$newHeight=round($srcHeight/100*abs($newHeight));$flags|=empty($percents)?self::ENLARGE:self::STRETCH;}else{$newHeight=(int)abs($newHeight);}if($flags&self::STRETCH){if(empty($newWidth)||empty($newHeight)){throw
new\InvalidArgumentException('For stretching must be both width and height specified.');}if(($flags&self::ENLARGE)===0){$newWidth=round($srcWidth*min(1,$newWidth/$srcWidth));$newHeight=round($srcHeight*min(1,$newHeight/$srcHeight));}}else{if(empty($newWidth)&&empty($newHeight)){throw
new\InvalidArgumentException('At least width or height must be specified.');}$scale=array();if($newWidth>0){$scale[]=$newWidth/$srcWidth;}if($newHeight>0){$scale[]=$newHeight/$srcHeight;}if($flags&self::FILL){$scale=array(max($scale));}if(($flags&self::ENLARGE)===0){$scale[]=1;}$scale=min($scale);$newWidth=round($srcWidth*$scale);$newHeight=round($srcHeight*$scale);}return
array((int)$newWidth,(int)$newHeight);}function
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
new\Exception("Unsupported image type.");}}function
toString($type=self::JPEG,$quality=NULL){ob_start();$this->save(NULL,$quality,$type);return
ob_get_clean();}function
__toString(){try{return$this->toString();}catch(\Exception$e){Debug::toStringException($e);}}function
send($type=self::JPEG,$quality=NULL){if($type!==self::GIF&&$type!==self::PNG&&$type!==self::JPEG){throw
new\Exception("Unsupported image type.");}header('Content-Type: '.image_type_to_mime_type($type));return$this->save(NULL,$quality,$type);}function
__call($name,$args){$function='image'.$name;if(function_exists($function)){foreach($args
as$key=>$value){if($value
instanceof
self){$args[$key]=$value->getImageResource();}elseif(is_array($value)&&isset($value['red'])){$args[$key]=imagecolorallocatealpha($this->getImageResource(),$value['red'],$value['green'],$value['blue'],$value['alpha']);}}array_unshift($args,$this->getImageResource());$res=call_user_func_array($function,$args);return
is_resource($res)&&get_resource_type($res)==='gd'?$this->setImageResource($res):$res;}return
parent::__call($name,$args);}}class
ImageMagick
extends
Image{public
static$path='';public
static$tempDir;private$file;private$isTemporary=FALSE;private$width;private$height;function
__construct($file,&$format=NULL){if(!is_file($file)){throw
new\InvalidArgumentException("File '$file' not found.");}$format=$this->setFile(realpath($file));if($format==='JPEG')$format=self::JPEG;elseif($format==='PNG')$format=self::PNG;elseif($format==='GIF')$format=self::GIF;}function
getWidth(){return$this->file===NULL?parent::getWidth():$this->width;}function
getHeight(){return$this->file===NULL?parent::getHeight():$this->height;}function
getImageResource(){if($this->file!==NULL){if(!$this->isTemporary){$this->execute("convert -strip %input %output",self::PNG);}$this->setImageResource(imagecreatefrompng($this->file));if($this->isTemporary){unlink($this->file);}$this->file=NULL;}return
parent::getImageResource();}function
resize($width,$height,$flags=self::FIT){if($this->file===NULL){return
parent::resize($width,$height,$flags);}$mirror='';if($width<0)$mirror.=' -flop';if($height<0)$mirror.=' -flip';list($newWidth,$newHeight)=self::calculateSize($this->getWidth(),$this->getHeight(),$width,$height,$flags);$this->execute("convert -resize {$newWidth}x{$newHeight}! {$mirror} -strip %input %output",self::PNG);return$this;}function
crop($left,$top,$width,$height){if($this->file===NULL){return
parent::crop($left,$top,$width,$height);}list($left,$top,$width,$height)=self::calculateCutout($this->getWidth(),$this->getHeight(),$left,$top,$width,$height);$this->execute("convert -crop {$width}x{$height}+{$left}+{$top} -strip %input %output",self::PNG);return$this;}function
save($file=NULL,$quality=NULL,$type=NULL){if($this->file===NULL){return
parent::save($file,$quality,$type);}$quality=$quality===NULL?'':'-quality '.max(0,min(100,(int)$quality));if($file===NULL){$this->execute("convert $quality -strip %input %output",$type===NULL?self::PNG:$type);readfile($this->file);}else{$this->execute("convert $quality -strip %input %output",(string)$file);}return
TRUE;}private
function
setFile($file){$this->file=$file;$res=$this->execute('identify -format "%w,%h,%m" '.escapeshellarg($this->file));if(!$res){throw
new\Exception("Unknown image type in file '$file' or ImageMagick not available.");}list($this->width,$this->height,$format)=explode(',',$res,3);return$format;}private
function
execute($command,$output=NULL){$command=str_replace('%input',escapeshellarg($this->file),$command);if($output){$newFile=is_string($output)?$output:(self::$tempDir?self::$tempDir:dirname($this->file)).'/'.uniqid('_tempimage',TRUE).image_type_to_extension($output);$command=str_replace('%output',escapeshellarg($newFile),$command);}$lines=array();exec(self::$path.$command,$lines,$status);if($output){if($status!=0){throw
new\Exception("Unknown error while calling ImageMagick.");}if($this->isTemporary){unlink($this->file);}$this->setFile($newFile);$this->isTemporary=!is_string($output);}return$lines?$lines[0]:FALSE;}function
__destruct(){if($this->file!==NULL&&$this->isTemporary){unlink($this->file);}}}class
CallbackFilterIterator
extends\FilterIterator{private$callback;function
__construct(\Iterator$iterator,$callback){parent::__construct($iterator);$this->callback=$callback;}function
accept(){return
call_user_func($this->callback,$this);}}class
RecursiveCallbackFilterIterator
extends\FilterIterator
implements\RecursiveIterator{private$callback;private$childrenCallback;function
__construct(\RecursiveIterator$iterator,$callback,$childrenCallback=NULL){parent::__construct($iterator);$this->callback=$callback;$this->childrenCallback=$childrenCallback;}function
accept(){return$this->callback===NULL||call_user_func($this->callback,$this);}function
hasChildren(){return$this->getInnerIterator()->hasChildren()&&($this->childrenCallback===NULL||call_user_func($this->childrenCallback,$this));}function
getChildren(){return
new
self($this->getInnerIterator()->getChildren(),$this->callback,$this->childrenCallback);}}class
GenericRecursiveIterator
extends\IteratorIterator
implements\RecursiveIterator,\Countable{function
hasChildren(){$obj=$this->current();return($obj
instanceof\IteratorAggregate&&$obj->getIterator()instanceof\RecursiveIterator)||$obj
instanceof\RecursiveIterator;}function
getChildren(){$obj=$this->current();return$obj
instanceof\IteratorAggregate?$obj->getIterator():$obj;}function
count(){return
iterator_count($this);}}class
InstanceFilterIterator
extends\FilterIterator
implements\Countable{private$type;function
__construct(\Iterator$iterator,$type){$this->type=$type;parent::__construct($iterator);}function
accept(){return$this->current()instanceof$this->type;}function
count(){return
iterator_count($this);}}class
SmartCachingIterator
extends\CachingIterator
implements\Countable{private$counter=0;function
__construct($iterator){if(is_array($iterator)||$iterator
instanceof\stdClass){$iterator=new\ArrayIterator($iterator);}elseif($iterator
instanceof\Traversable){if($iterator
instanceof\IteratorAggregate){$iterator=$iterator->getIterator();}elseif(!($iterator
instanceof\Iterator)){$iterator=new\IteratorIterator($iterator);}}else{throw
new\InvalidArgumentException("Invalid argument passed to foreach resp. ".__CLASS__."; array or Traversable expected, ".(is_object($iterator)?get_class($iterator):gettype($iterator))." given.");}parent::__construct($iterator,0);}function
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
new\XNotSupportedException('Iterator is not countable.');}}function
next(){parent::next();if(parent::valid()){$this->counter++;}}function
rewind(){parent::rewind();$this->counter=parent::valid()?1:0;}function
getNextKey(){return$this->getInnerIterator()->key();}function
getNextValue(){return$this->getInnerIterator()->current();}function
__call($name,$args){return
ObjectMixin::call($this,$name,$args);}function&__get($name){return
ObjectMixin::get($this,$name);}function
__set($name,$value){return
ObjectMixin::set($this,$name,$value);}function
__isset($name){return
ObjectMixin::has($this,$name);}function
__unset($name){$class=get_class($this);throw
new\XMemberAccessException("Cannot unset the property $class::\$$name.");}}final
class
Json{const
FORCE_ARRAY=1;private
static$messages=array(JSON_ERROR_DEPTH=>'The maximum stack depth has been exceeded',JSON_ERROR_STATE_MISMATCH=>'Syntax error, malformed JSON',JSON_ERROR_CTRL_CHAR=>'Unexpected control character found',JSON_ERROR_SYNTAX=>'Syntax error, malformed JSON');final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
encode($value){Debug::tryError();if(function_exists('ini_set')){$old=ini_set('display_errors',0);$json=json_encode($value);ini_set('display_errors',$old);}else{$json=json_encode($value);}if(Debug::catchError($e)){throw
new
JsonException($e->getMessage());}return$json;}static
function
decode($json,$options=0){$json=(string)$json;$value=json_decode($json,(bool)($options&self::FORCE_ARRAY));if($value===NULL&&$json!==''&&strcasecmp($json,'null')){$error=PHP_VERSION_ID>=50300?json_last_error():0;throw
new
JsonException(isset(self::$messages[$error])?self::$messages[$error]:'Unknown error',$error);}return$value;}}class
JsonException
extends\Exception{}class
NeonParser
extends
Object{private
static$patterns=array('\'[^\'\n]*\'|"(?:\\\\.|[^"\\\\\n])*"','@[a-zA-Z_0-9\\\\]+','[:-](?=\s|$)|[,=[\]{}()]','?:#.*','\n *','[^#"\',:=@[\]{}()<>\s](?:[^#,:=\]})>\n]+|:(?!\s)|(?<!\s)#)*(?<!\s)','?: +');private
static$tokenizer;private
static$brackets=array('['=>']','{'=>'}','('=>')');private$n;function
parse($input){if(!self::$tokenizer){self::$tokenizer=new
Tokenizer(self::$patterns,'mi');}$input=str_replace("\r",'',$input);$input=strtr($input,"\t",' ');$input="\n".$input."\n";self::$tokenizer->tokenize($input);$this->n=0;$res=$this->_parse();while(isset(self::$tokenizer->tokens[$this->n])){if(self::$tokenizer->tokens[$this->n][0]==="\n"){$this->n++;}else{$this->error();}}return$res;}private
function
_parse($indent=NULL,$endBracket=NULL){$inlineParser=$endBracket!==NULL;$result=$inlineParser||$indent?array():NULL;$value=$key=$object=NULL;$hasValue=$hasKey=FALSE;$tokens=self::$tokenizer->tokens;$n=&$this->n;$count=count($tokens);for(;$n<$count;$n++){$t=$tokens[$n];if($t===','){if(!$hasValue||!$inlineParser){$this->error();}if($hasKey)$result[$key]=$value;else$result[]=$value;$hasKey=$hasValue=FALSE;}elseif($t===':'||$t==='='){if($hasKey||!$hasValue){$this->error();}$key=(string)$value;$hasKey=TRUE;$hasValue=FALSE;}elseif($t==='-'){if($hasKey||$hasValue||$inlineParser){$this->error();}$key=NULL;$hasKey=TRUE;}elseif(isset(self::$brackets[$t])){if($hasValue){$this->error();}$hasValue=TRUE;$value=$this->_parse(NULL,self::$brackets[$tokens[$n++]]);}elseif($t===']'||$t==='}'||$t===')'){if($t!==$endBracket){$this->error();}if($hasValue){if($hasKey)$result[$key]=$value;else$result[]=$value;}elseif($hasKey){$this->error();}return$result;}elseif($t[0]==='@'){$object=$t;}elseif($t[0]==="\n"){if($inlineParser){if($hasValue){if($hasKey)$result[$key]=$value;else$result[]=$value;$hasKey=$hasValue=FALSE;}}else{while(isset($tokens[$n+1])&&$tokens[$n+1][0]==="\n")$n++;$newIndent=strlen($tokens[$n])-1;if($indent===NULL){$indent=$newIndent;}if($newIndent>$indent){if($hasValue||!$hasKey){$this->error();}elseif($key===NULL){$result[]=$this->_parse($newIndent);}else{$result[$key]=$this->_parse($newIndent);}$newIndent=strlen($tokens[$n])-1;$hasKey=FALSE;}else{if($hasValue&&!$hasKey){if($result===NULL)return$value;$this->error();}elseif($hasKey){$value=$hasValue?$value:NULL;if($key===NULL)$result[]=$value;else$result[$key]=$value;$hasKey=$hasValue=FALSE;}}if($newIndent<$indent||!isset($tokens[$n+1])){return$result;}}}else{if($hasValue){$this->error();}if($t[0]==='"'){$value=json_decode($t);if($value===NULL){$this->error();}}elseif($t[0]==="'"){$value=substr($t,1,-1);}elseif($t==='true'||$t==='yes'||$t==='TRUE'||$t==='YES'){$value=TRUE;}elseif($t==='false'||$t==='no'||$t==='FALSE'||$t==='NO'){$value=FALSE;}elseif($t==='null'||$t==='NULL'){$value=NULL;}elseif(is_numeric($t)){$value=$t*1;}else{$value=$t;}$hasValue=TRUE;}}throw
new
NeonException('Unexpected end of file.');}private
function
error(){list(,$line,$col)=self::$tokenizer->getOffset($this->n);throw
new
NeonException("Unexpected '".str_replace("\n",'\n',substr(self::$tokenizer->tokens[$this->n],0,10))."' on line ".($line-1).", column $col.");}}class
NeonException
extends\Exception{}class
Paginator
extends
Object{private$base=1;private$itemsPerPage=1;private$page;private$itemCount=0;function
setPage($page){$this->page=(int)$page;return$this;}function
getPage(){return$this->base+$this->getPageIndex();}function
getFirstPage(){return$this->base;}function
getLastPage(){return$this->base+max(0,$this->getPageCount()-1);}function
setBase($base){$this->base=(int)$base;return$this;}function
getBase(){return$this->base;}protected
function
getPageIndex(){return
min(max(0,$this->page-$this->base),max(0,$this->getPageCount()-1));}function
isFirst(){return$this->getPageIndex()===0;}function
isLast(){return$this->getPageIndex()>=$this->getPageCount()-1;}function
getPageCount(){return(int)ceil($this->itemCount/$this->itemsPerPage);}function
setItemsPerPage($itemsPerPage){$this->itemsPerPage=max(1,(int)$itemsPerPage);return$this;}function
getItemsPerPage(){return$this->itemsPerPage;}function
setItemCount($itemCount){$this->itemCount=$itemCount===FALSE?PHP_INT_MAX:max(0,(int)$itemCount);return$this;}function
getItemCount(){return$this->itemCount;}function
getOffset(){return$this->getPageIndex()*$this->itemsPerPage;}function
getCountdownOffset(){return
max(0,$this->itemCount-($this->getPageIndex()+1)*$this->itemsPerPage);}function
getLength(){return
min($this->itemsPerPage,$this->itemCount-$this->getPageIndex()*$this->itemsPerPage);}}final
class
SafeStream{const
PROTOCOL='safe';private$handle;private$filePath;private$tempFile;private$startPos=0;private$writeError=FALSE;static
function
register(){return
stream_wrapper_register(self::PROTOCOL,__CLASS__);}function
stream_open($path,$mode,$options,&$opened_path){$path=substr($path,strlen(self::PROTOCOL)+3);$flag=trim($mode,'rwax+');$mode=trim($mode,'tb');$use_path=(bool)(STREAM_USE_PATH&$options);$append=FALSE;switch($mode){case'r':case'r+':$handle=@fopen($path,$mode.$flag,$use_path);if(!$handle)return
FALSE;if(flock($handle,$mode=='r'?LOCK_SH:LOCK_EX)){$this->handle=$handle;return
TRUE;}fclose($handle);return
FALSE;case'a':case'a+':$append=TRUE;case'w':case'w+':$handle=@fopen($path,'r+'.$flag,$use_path);if($handle){if(flock($handle,LOCK_EX)){if($append){fseek($handle,0,SEEK_END);$this->startPos=ftell($handle);}else{ftruncate($handle,0);}$this->handle=$handle;return
TRUE;}fclose($handle);}$mode{0}='x';case'x':case'x+':if(file_exists($path))return
FALSE;$tmp='~~'.time().'.tmp';$handle=@fopen($path.$tmp,$mode.$flag,$use_path);if($handle){if(flock($handle,LOCK_EX)){$this->handle=$handle;if(!@rename($path.$tmp,$path)){$this->tempFile=realpath($path.$tmp);$this->filePath=substr($this->tempFile,0,-strlen($tmp));}return
TRUE;}fclose($handle);unlink($path.$tmp);}return
FALSE;default:trigger_error("Unsupported mode $mode",E_USER_WARNING);return
FALSE;}}function
stream_close(){if($this->writeError){ftruncate($this->handle,$this->startPos);}fclose($this->handle);if($this->tempFile){if(!@rename($this->tempFile,$this->filePath)){unlink($this->tempFile);}}}function
stream_read($length){return
fread($this->handle,$length);}function
stream_write($data){$len=strlen($data);$res=fwrite($this->handle,$data,$len);if($res!==$len){$this->writeError=TRUE;}return$res;}function
stream_tell(){return
ftell($this->handle);}function
stream_eof(){return
feof($this->handle);}function
stream_seek($offset,$whence){return
fseek($this->handle,$offset,$whence)===0;}function
stream_stat(){return
fstat($this->handle);}function
url_stat($path,$flags){$path=substr($path,strlen(self::PROTOCOL)+3);return($flags&STREAM_URL_STAT_LINK)?@lstat($path):@stat($path);}function
unlink($path){$path=substr($path,strlen(self::PROTOCOL)+3);return
unlink($path);}}final
class
String{final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
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
toAscii($s){$s=preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u','',$s);$s=strtr($s,'`\'"^~',"\x01\x02\x03\x04\x05");if(ICONV_IMPL==='glibc'){$s=@iconv('UTF-8','WINDOWS-1250//TRANSLIT',$s);$s=strtr($s,"\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2"."\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe","ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt");}else{$s=@iconv('UTF-8','ASCII//TRANSLIT',$s);}$s=str_replace(array('`',"'",'"','^','~'),'',$s);return
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
split($subject,$pattern,$flags=0){Debug::tryError();$res=preg_split($pattern,$subject,-1,$flags|PREG_SPLIT_DELIM_CAPTURE);self::catchPregError($pattern);return$res;}static
function
match($subject,$pattern,$flags=0,$offset=0){Debug::tryError();$res=preg_match($pattern,$subject,$m,$flags,$offset);self::catchPregError($pattern);if($res){return$m;}}static
function
matchAll($subject,$pattern,$flags=0,$offset=0){Debug::tryError();$res=preg_match_all($pattern,$subject,$m,($flags&PREG_PATTERN_ORDER)?$flags:($flags|PREG_SET_ORDER),$offset);self::catchPregError($pattern);return$m;}static
function
replace($subject,$pattern,$replacement=NULL,$limit=-1){Debug::tryError();if(is_object($replacement)||is_array($replacement)){if($replacement
instanceof
Callback){$replacement=$replacement->getNative();}if(!is_callable($replacement,FALSE,$textual)){Debug::catchError($foo);throw
new\XInvalidStateException("Callback '$textual' is not callable.");}$res=preg_replace_callback($pattern,$replacement,$subject,$limit);if(Debug::catchError($e)){$trace=$e->getTrace();if(isset($trace[2]['class'])&&$trace[2]['class']===__CLASS__){throw
new
RegexpException($e->getMessage()." in pattern: $pattern");}}}elseif(is_array($pattern)){$res=preg_replace(array_keys($pattern),array_values($pattern),$subject,$limit);}else{$res=preg_replace($pattern,$replacement,$subject,$limit);}self::catchPregError($pattern);return$res;}static
function
catchPregError($pattern){if(Debug::catchError($e)){throw
new
RegexpException($e->getMessage()." in pattern: $pattern");}elseif(preg_last_error()){static$messages=array(PREG_INTERNAL_ERROR=>'Internal error',PREG_BACKTRACK_LIMIT_ERROR=>'Backtrack limit was exhausted',PREG_RECURSION_LIMIT_ERROR=>'Recursion limit was exhausted',PREG_BAD_UTF8_ERROR=>'Malformed UTF-8 data',5=>'Offset didn\'t correspond to the begin of a valid UTF-8 code point');$code=preg_last_error();throw
new
RegexpException((isset($messages[$code])?$messages[$code]:'Unknown error')." (pattern: $pattern)",$code);}}}class
RegexpException
extends\Exception{}class
Tokenizer
extends
Object{private$input;public$tokens;private$re;private$names;function
__construct(array$patterns,$flags=''){$this->re='~('.implode(')|(',$patterns).')~A'.$flags;$keys=array_keys($patterns);$this->names=$keys===range(0,count($patterns)-1)?FALSE:$keys;}function
tokenize($input){$this->input=$input;if($this->names){$this->tokens=String::matchAll($input,$this->re);$len=0;foreach($this->tokens
as&$match){$name=NULL;for($i=1;$i<count($this->names);$i++){if(!isset($match[$i])){break;}elseif($match[$i]!=NULL){$name=$this->names[$i-1];break;}}$match=array($match[0],$name);$len+=strlen($match[0]);}if($len!==strlen($input)){$errorOffset=$len;}}else{$this->tokens=String::split($input,$this->re,PREG_SPLIT_NO_EMPTY);if($this->tokens&&!String::match(end($this->tokens),$this->re)){$tmp=String::split($this->input,$this->re,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);list(,$errorOffset)=end($tmp);}}if(isset($errorOffset)){$line=$errorOffset?substr_count($this->input,"\n",0,$errorOffset)+1:1;$col=$errorOffset-strrpos(substr($this->input,0,$errorOffset),"\n")+1;$token=str_replace("\n",'\n',substr($input,$errorOffset,10));throw
new
TokenizerException("Unexpected '$token' on line $line, column $col.");}return$this->tokens;}function
getOffset($i){$tokens=String::split($this->input,$this->re,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);list(,$offset)=$tokens[$i];return
array($offset,($offset?substr_count($this->input,"\n",0,$offset)+1:1),$offset-strrpos(substr($this->input,0,$offset),"\n"));}}class
TokenizerException
extends\Exception{}final
class
Tools{const
MINUTE=60;const
HOUR=3600;const
DAY=86400;const
WEEK=604800;const
MONTH=2629800;const
YEAR=31557600;private
static$criticalSections;final
function
__construct(){throw
new\LogicException("Cannot instantiate static class ".get_class($this));}static
function
createDateTime($time){if($time
instanceof\DateTime){return
clone$time;}elseif(is_numeric($time)){if($time<=self::YEAR){$time+=time();}return
new\DateTime(date('Y-m-d H:i:s',$time));}else{return
new\DateTime($time);}}static
function
iniFlag($var){$status=strtolower(ini_get($var));return$status==='on'||$status==='true'||$status==='yes'||(int)$status;}static
function
defaultize(&$var,$default){if($var===NULL)$var=$default;}static
function
compare($l,$operator,$r){switch($operator){case'>':return$l>$r;case'>=':return$l>=$r;case'<':return$l<$r;case'<=':return$l<=$r;case'=':case'==':return$l==$r;case'!':case'!=':case'<>':return$l!=$r;}throw
new\InvalidArgumentException("Unknown operator $operator.");}static
function
detectMimeType($file){if(!is_file($file)){throw
new\XFileNotFoundException("File '$file' not found.");}$info=@getimagesize($file);if(isset($info['mime'])){return$info['mime'];}elseif(extension_loaded('fileinfo')){$type=preg_replace('#[\s;].*$#','',finfo_file(finfo_open(FILEINFO_MIME),$file));}elseif(function_exists('mime_content_type')){$type=mime_content_type($file);}return
isset($type)&&preg_match('#^\S+/\S+$#',$type)?$type:'application/octet-stream';}static
function
enterCriticalSection(){if(self::$criticalSections){throw
new\XInvalidStateException('Critical section has already been entered.');}$handle=fopen(NETTEX_DIR.'/lockfile','r')?:fopen(NETTEX_DIR.'/lockfile','w');if(!$handle){throw
new\XInvalidStateException("Unable initialize critical section (missing file '".NETTEX_DIR."/lockfile').");}flock(self::$criticalSections=$handle,LOCK_EX);}static
function
leaveCriticalSection(){if(!self::$criticalSections){throw
new\XInvalidStateException('Critical section has not been initialized.');}fclose(self::$criticalSections);self::$criticalSections=NULL;}}}namespace NetteX\Web{use
NetteX;class
Html
extends
NetteX\Object
implements\ArrayAccess,\Countable,\IteratorAggregate{private$name;private$isEmpty;public$attrs=array();protected$children=array();public
static$xhtml=TRUE;public
static$emptyElements=array('img'=>1,'hr'=>1,'br'=>1,'input'=>1,'meta'=>1,'area'=>1,'command'=>1,'keygen'=>1,'source'=>1,'base'=>1,'col'=>1,'link'=>1,'param'=>1,'basefont'=>1,'frame'=>1,'isindex'=>1,'wbr'=>1,'embed'=>1);static
function
el($name=NULL,$attrs=NULL){$el=new
static;$parts=explode(' ',$name,2);$el->setName($parts[0]);if(is_array($attrs)){$el->attrs=$attrs;}elseif($attrs!==NULL){$el->setText($attrs);}if(isset($parts[1])){foreach(NetteX\String::matchAll($parts[1].' ','#([a-z0-9:-]+)(?:=(["\'])?(.*?)(?(2)\\2|\s))?#i')as$m){$el->attrs[$m[1]]=isset($m[3])?$m[3]:TRUE;}}return$el;}final
function
setName($name,$isEmpty=NULL){if($name!==NULL&&!is_string($name)){throw
new\InvalidArgumentException("Name must be string or NULL, ".gettype($name)." given.");}$this->name=$name;$this->isEmpty=$isEmpty===NULL?isset(self::$emptyElements[$name]):(bool)$isEmpty;return$this;}final
function
getName(){return$this->name;}final
function
isEmpty(){return$this->isEmpty;}final
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
new\InvalidArgumentException("Textual content must be a scalar, ".gettype($html)." given.");}else{$html=(string)$html;}$this->removeChildren();$this->children[]=$html;return$this;}final
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
new\InvalidArgumentException("Child node must be scalar or Html object, ".(is_object($child)?get_class($child):gettype($child))." given.");}return$this;}final
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
NetteX\GenericRecursiveIterator(new\ArrayIterator($this->children)),$deep);}else{return
new
NetteX\GenericRecursiveIterator(new\ArrayIterator($this->children));}}final
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
as$key=>$value){if(is_object($value)){$this->children[$key]=clone$value;}}}}class
HttpContext
extends
NetteX\Object{function
isModified($lastModified=NULL,$etag=NULL){$response=$this->getResponse();$request=$this->getRequest();if($lastModified){$response->setHeader('Last-Modified',$response->date($lastModified));}if($etag){$response->setHeader('ETag','"'.addslashes($etag).'"');}$ifNoneMatch=$request->getHeader('If-None-Match');if($ifNoneMatch==='*'){$match=TRUE;}elseif($ifNoneMatch!==NULL){$etag=$response->getHeader('ETag');if($etag==NULL||strpos(' '.strtr($ifNoneMatch,",\t",'  '),' '.$etag)===FALSE){return
TRUE;}else{$match=TRUE;}}$ifModifiedSince=$request->getHeader('If-Modified-Since');if($ifModifiedSince!==NULL){$lastModified=$response->getHeader('Last-Modified');if($lastModified!=NULL&&strtotime($lastModified)<=strtotime($ifModifiedSince)){$match=TRUE;}else{return
TRUE;}}if(empty($match)){return
TRUE;}$response->setCode(IHttpResponse::S304_NOT_MODIFIED);return
FALSE;}function
getRequest(){return
NetteX\Environment::getHttpRequest();}function
getResponse(){return
NetteX\Environment::getHttpResponse();}}use
NetteX\String;class
HttpRequest
extends
NetteX\Object
implements
IHttpRequest{protected$query;protected$post;protected$files;protected$cookies;protected$uri;protected$originalUri;protected$headers;protected$uriFilter=array(PHP_URL_PATH=>array('#/{2,}#'=>'/'),0=>array());protected$encoding;final
function
getUri(){if($this->uri===NULL){$this->detectUri();}return$this->uri;}function
setUri(UriScript$uri){$this->uri=clone$uri;$this->query=NULL;$this->uri->canonicalize();$this->uri->freeze();return$this;}final
function
getOriginalUri(){if($this->originalUri===NULL){$this->detectUri();}return$this->originalUri;}function
addUriFilter($pattern,$replacement='',$component=NULL){$pattern='#'.$pattern.'#';$component=$component===PHP_URL_PATH?PHP_URL_PATH:0;if($replacement===NULL){unset($this->uriFilter[$component][$pattern]);}else{$this->uriFilter[$component][$pattern]=$replacement;}$this->uri=NULL;}final
function
getUriFilters(){return$this->uriFilter;}protected
function
detectUri(){$uri=$this->uri=new
UriScript;$uri->scheme=$this->isSecured()?'https':'http';$uri->user=isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:'';$uri->password=isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:'';if(isset($_SERVER['HTTP_HOST'])){$pair=explode(':',$_SERVER['HTTP_HOST']);}elseif(isset($_SERVER['SERVER_NAME'])){$pair=explode(':',$_SERVER['SERVER_NAME']);}else{$pair=array('');}$uri->host=preg_match('#^[-.a-z0-9]+$#',$pair[0])?$pair[0]:'';if(isset($pair[1])){$uri->port=(int)$pair[1];}elseif(isset($_SERVER['SERVER_PORT'])){$uri->port=(int)$_SERVER['SERVER_PORT'];}if(isset($_SERVER['REQUEST_URI'])){$requestUri=$_SERVER['REQUEST_URI'];}elseif(isset($_SERVER['ORIG_PATH_INFO'])){$requestUri=$_SERVER['ORIG_PATH_INFO'];if(isset($_SERVER['QUERY_STRING'])&&$_SERVER['QUERY_STRING']!=''){$requestUri.='?'.$_SERVER['QUERY_STRING'];}}else{$requestUri='';}$tmp=explode('?',$requestUri,2);$this->originalUri=new
Uri($uri);$this->originalUri->path=$tmp[0];$this->originalUri->query=isset($tmp[1])?$tmp[1]:'';$this->originalUri->freeze();$requestUri=String::replace($requestUri,$this->uriFilter[0]);$tmp=explode('?',$requestUri,2);$uri->path=String::replace($tmp[0],$this->uriFilter[PHP_URL_PATH]);$uri->query=isset($tmp[1])?$tmp[1]:'';$uri->canonicalize();$uri->path=String::fixEncoding($uri->path);$uri->scriptPath='/';if(isset($_SERVER['SCRIPT_NAME'])){$script=$_SERVER['SCRIPT_NAME'];if(strncmp($uri->path.'/',$script.'/',strlen($script)+1)===0){$uri->scriptPath=$script;}elseif(strncmp($uri->path,$script,strrpos($script,'/')+1)===0){$uri->scriptPath=substr($script,0,strrpos($script,'/')+1);}}$uri->freeze();}final
function
getQuery($key=NULL,$default=NULL){if($this->query===NULL){$this->initialize();}if(func_num_args()===0){return$this->query;}elseif(isset($this->query[$key])){return$this->query[$key];}else{return$default;}}final
function
getPost($key=NULL,$default=NULL){if($this->post===NULL){$this->initialize();}if(func_num_args()===0){return$this->post;}elseif(isset($this->post[$key])){return$this->post[$key];}else{return$default;}}function
getPostRaw(){return
file_get_contents('php://input');}final
function
getFile($key){if($this->files===NULL){$this->initialize();}$args=func_get_args();return
NetteX\ArrayTools::get($this->files,$args);}final
function
getFiles(){if($this->files===NULL){$this->initialize();}return$this->files;}final
function
getCookie($key,$default=NULL){if($this->cookies===NULL){$this->initialize();}if(func_num_args()===0){return$this->cookies;}elseif(isset($this->cookies[$key])){return$this->cookies[$key];}else{return$default;}}final
function
getCookies(){if($this->cookies===NULL){$this->initialize();}return$this->cookies;}function
setEncoding($encoding){if(strcasecmp($encoding,$this->encoding)){$this->encoding=$encoding;$this->query=$this->post=$this->cookies=$this->files=NULL;}return$this;}function
initialize(){$filter=(!in_array(ini_get("filter.default"),array("","unsafe_raw"))||ini_get("filter.default_flags"));parse_str($this->getUri()->query,$this->query);if(!$this->query){$this->query=$filter?filter_input_array(INPUT_GET,FILTER_UNSAFE_RAW):(empty($_GET)?array():$_GET);}$this->post=$filter?filter_input_array(INPUT_POST,FILTER_UNSAFE_RAW):(empty($_POST)?array():$_POST);$this->cookies=$filter?filter_input_array(INPUT_COOKIE,FILTER_UNSAFE_RAW):(empty($_COOKIE)?array():$_COOKIE);$gpc=(bool)get_magic_quotes_gpc();$enc=(bool)$this->encoding;$old=error_reporting(error_reporting()^E_NOTICE);$nonChars='#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u';if($gpc||$enc){$utf=strcasecmp($this->encoding,'UTF-8')===0;$list=array(&$this->query,&$this->post,&$this->cookies);while(list($key,$val)=each($list)){foreach($val
as$k=>$v){unset($list[$key][$k]);if($gpc){$k=stripslashes($k);}if($enc&&is_string($k)&&(preg_match($nonChars,$k)||preg_last_error())){}elseif(is_array($v)){$list[$key][$k]=$v;$list[]=&$list[$key][$k];}else{if($gpc&&!$filter){$v=stripSlashes($v);}if($enc){if($utf){$v=String::fixEncoding($v);}else{if(!String::checkEncoding($v)){$v=iconv($this->encoding,'UTF-8//IGNORE',$v);}$v=html_entity_decode($v,ENT_QUOTES,'UTF-8');}$v=preg_replace($nonChars,'',$v);}$list[$key][$k]=$v;}}}unset($list,$key,$val,$k,$v);}$this->files=array();$list=array();if(!empty($_FILES)){foreach($_FILES
as$k=>$v){if($enc&&is_string($k)&&(preg_match($nonChars,$k)||preg_last_error()))continue;$v['@']=&$this->files[$k];$list[]=$v;}}while(list(,$v)=each($list)){if(!isset($v['name'])){continue;}elseif(!is_array($v['name'])){if($gpc){$v['name']=stripSlashes($v['name']);}if($enc){$v['name']=preg_replace($nonChars,'',String::fixEncoding($v['name']));}$v['@']=new
HttpUploadedFile($v);continue;}foreach($v['name']as$k=>$foo){if($enc&&is_string($k)&&(preg_match($nonChars,$k)||preg_last_error()))continue;$list[]=array('name'=>$v['name'][$k],'type'=>$v['type'][$k],'size'=>$v['size'][$k],'tmp_name'=>$v['tmp_name'][$k],'error'=>$v['error'][$k],'@'=>&$v['@'][$k]);}}error_reporting($old);}function
getMethod(){return
isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:NULL;}function
isMethod($method){return
isset($_SERVER['REQUEST_METHOD'])?strcasecmp($_SERVER['REQUEST_METHOD'],$method)===0:FALSE;}function
isPost(){return$this->isMethod('POST');}final
function
getHeader($header,$default=NULL){$header=strtolower($header);$headers=$this->getHeaders();if(isset($headers[$header])){return$headers[$header];}else{return$default;}}function
getHeaders(){if($this->headers===NULL){if(function_exists('apache_request_headers')){$this->headers=array_change_key_case(apache_request_headers(),CASE_LOWER);}else{$this->headers=array();foreach($_SERVER
as$k=>$v){if(strncmp($k,'HTTP_',5)==0){$k=substr($k,5);}elseif(strncmp($k,'CONTENT_',8)){continue;}$this->headers[strtr(strtolower($k),'_','-')]=$v;}}}return$this->headers;}final
function
getReferer(){$uri=self::getHeader('referer');return$uri?new
Uri($uri):NULL;}function
isSecured(){return
isset($_SERVER['HTTPS'])&&strcasecmp($_SERVER['HTTPS'],'off');}function
isAjax(){return$this->getHeader('X-Requested-With')==='XMLHttpRequest';}function
getRemoteAddress(){return
isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:NULL;}function
getRemoteHost(){if(!isset($_SERVER['REMOTE_HOST'])){if(!isset($_SERVER['REMOTE_ADDR'])){return
NULL;}$_SERVER['REMOTE_HOST']=getHostByAddr($_SERVER['REMOTE_ADDR']);}return$_SERVER['REMOTE_HOST'];}function
detectLanguage(array$langs){$header=$this->getHeader('accept-language');if(!$header)return
NULL;$s=strtolower($header);$s=strtr($s,'_','-');rsort($langs);preg_match_all('#('.implode('|',$langs).')(?:-[^\s,;=]+)?\s*(?:;\s*q=([0-9.]+))?#',$s,$matches);if(!$matches[0]){return
NULL;}$max=0;$lang=NULL;foreach($matches[1]as$key=>$value){$q=$matches[2][$key]===''?1.0:(float)$matches[2][$key];if($q>$max){$max=$q;$lang=$value;}}return$lang;}}final
class
HttpResponse
extends
NetteX\Object
implements
IHttpResponse{private
static$fixIE=TRUE;public$cookieDomain='';public$cookiePath='/';public$cookieSecure=FALSE;public$cookieHttpOnly=TRUE;private$code=self::S200_OK;function
setCode($code){$code=(int)$code;static$allowed=array(200=>1,201=>1,202=>1,203=>1,204=>1,205=>1,206=>1,300=>1,301=>1,302=>1,303=>1,304=>1,307=>1,400=>1,401=>1,403=>1,404=>1,406=>1,408=>1,410=>1,412=>1,415=>1,416=>1,500=>1,501=>1,503=>1,505=>1);if(!isset($allowed[$code])){throw
new\InvalidArgumentException("Bad HTTP response '$code'.");}elseif(headers_sent($file,$line)){throw
new\XInvalidStateException("Cannot set HTTP code after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}else{$this->code=$code;$protocol=isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:'HTTP/1.1';header($protocol.' '.$code,TRUE,$code);}return$this;}function
getCode(){return$this->code;}function
setHeader($name,$value){if(headers_sent($file,$line)){throw
new\XInvalidStateException("Cannot send header after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}if($value===NULL&&function_exists('header_remove')){header_remove($name);}else{header($name.': '.$value,TRUE,$this->code);}return$this;}function
addHeader($name,$value){if(headers_sent($file,$line)){throw
new\XInvalidStateException("Cannot send header after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}header($name.': '.$value,FALSE,$this->code);}function
setContentType($type,$charset=NULL){$this->setHeader('Content-Type',$type.($charset?'; charset='.$charset:''));return$this;}function
redirect($url,$code=self::S302_FOUND){if(isset($_SERVER['SERVER_SOFTWARE'])&&preg_match('#^Microsoft-IIS/[1-5]#',$_SERVER['SERVER_SOFTWARE'])&&$this->getHeader('Set-Cookie')!==NULL){$this->setHeader('Refresh',"0;url=$url");return;}$this->setCode($code);$this->setHeader('Location',$url);echo"<h1>Redirect</h1>\n\n<p><a href=\"".htmlSpecialChars($url)."\">Please click here to continue</a>.</p>";}function
setExpiration($time){if(!$time){$this->setHeader('Cache-Control','s-maxage=0, max-age=0, must-revalidate');$this->setHeader('Expires','Mon, 23 Jan 1978 10:00:00 GMT');return$this;}$time=NetteX\Tools::createDateTime($time);$this->setHeader('Cache-Control','max-age='.($time->format('U')-time()));$this->setHeader('Expires',self::date($time));return$this;}function
isSent(){return
headers_sent();}function
getHeader($header,$default=NULL){$header.=':';$len=strlen($header);foreach(headers_list()as$item){if(strncasecmp($item,$header,$len)===0){return
ltrim(substr($item,$len));}}return$default;}function
getHeaders(){$headers=array();foreach(headers_list()as$header){$a=strpos($header,':');$headers[substr($header,0,$a)]=(string)substr($header,$a+2);}return$headers;}static
function
date($time=NULL){$time=NetteX\Tools::createDateTime($time);$time->setTimezone(new\DateTimeZone('GMT'));return$time->format('D, d M Y H:i:s \G\M\T');}function
__destruct(){if(self::$fixIE){if(!isset($_SERVER['HTTP_USER_AGENT'])||strpos($_SERVER['HTTP_USER_AGENT'],'MSIE ')===FALSE)return;if(!in_array($this->code,array(400,403,404,405,406,408,409,410,500,501,505),TRUE))return;if($this->getHeader('Content-Type','text/html')!=='text/html')return;$s=" \t\r\n";for($i=2e3;$i;$i--)echo$s{rand(0,3)};self::$fixIE=FALSE;}}function
setCookie($name,$value,$time,$path=NULL,$domain=NULL,$secure=NULL,$httpOnly=NULL){if(headers_sent($file,$line)){throw
new\XInvalidStateException("Cannot set cookie after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}setcookie($name,$value,$time?NetteX\Tools::createDateTime($time)->format('U'):0,$path===NULL?$this->cookiePath:(string)$path,$domain===NULL?$this->cookieDomain:(string)$domain,$secure===NULL?$this->cookieSecure:(bool)$secure,$httpOnly===NULL?$this->cookieHttpOnly:(bool)$httpOnly);return$this;}function
deleteCookie($name,$path=NULL,$domain=NULL,$secure=NULL){if(headers_sent($file,$line)){throw
new\XInvalidStateException("Cannot delete cookie after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}setcookie($name,FALSE,254400000,$path===NULL?$this->cookiePath:(string)$path,$domain===NULL?$this->cookieDomain:(string)$domain,$secure===NULL?$this->cookieSecure:(bool)$secure,TRUE);}}class
HttpUploadedFile
extends
NetteX\Object{private$name;private$type;private$size;private$tmpName;private$error;function
__construct($value){foreach(array('name','type','size','tmp_name','error')as$key){if(!isset($value[$key])||!is_scalar($value[$key])){$this->error=UPLOAD_ERR_NO_FILE;return;}}$this->name=$value['name'];$this->size=$value['size'];$this->tmpName=$value['tmp_name'];$this->error=$value['error'];}function
getName(){return$this->name;}function
getContentType(){if($this->isOk()&&$this->type===NULL){$this->type=NetteX\Tools::detectMimeType($this->tmpName);}return$this->type;}function
getSize(){return$this->size;}function
getTemporaryFile(){return$this->tmpName;}function
__toString(){return$this->tmpName;}function
getError(){return$this->error;}function
isOk(){return$this->error===UPLOAD_ERR_OK;}function
move($dest){$dir=dirname($dest);if(@mkdir($dir,0755,TRUE)){chmod($dir,0755);}$func=is_uploaded_file($this->tmpName)?'move_uploaded_file':'rename';if(!$func($this->tmpName,$dest)){throw
new\XInvalidStateException("Unable to move uploaded file '$this->tmpName' to '$dest'.");}chmod($dest,0644);$this->tmpName=$dest;return$this;}function
isImage(){return
in_array($this->getContentType(),array('image/gif','image/png','image/jpeg'),TRUE);}function
toImage(){return
NetteX\Image::fromFile($this->tmpName);}function
getImageSize(){return$this->isOk()?@getimagesize($this->tmpName):NULL;}function
getContents(){return$this->isOk()?file_get_contents($this->tmpName):NULL;}}class
Session
extends
NetteX\Object{const
DEFAULT_FILE_LIFETIME=10800;private$regenerationNeeded;private
static$started;private$options=array('referer_check'=>'','use_cookies'=>1,'use_only_cookies'=>1,'use_trans_sid'=>0,'cookie_lifetime'=>0,'cookie_path'=>'/','cookie_domain'=>'','cookie_secure'=>FALSE,'cookie_httponly'=>TRUE,'gc_maxlifetime'=>self::DEFAULT_FILE_LIFETIME,'cache_limiter'=>NULL,'cache_expire'=>NULL,'hash_function'=>NULL,'hash_bits_per_character'=>NULL);function
start(){if(self::$started){return;}elseif(self::$started===NULL&&defined('SID')){throw
new\XInvalidStateException('A session had already been started by session.auto-start or session_start().');}$this->configure($this->options);NetteX\Debug::tryError();session_start();if(NetteX\Debug::catchError($e)){@session_write_close();throw
new\XInvalidStateException($e->getMessage());}self::$started=TRUE;if($this->regenerationNeeded){session_regenerate_id(TRUE);$this->regenerationNeeded=FALSE;}unset($_SESSION['__NT'],$_SESSION['__NS'],$_SESSION['__NM']);$nf=&$_SESSION['__NF'];if(empty($nf)){$nf=array('C'=>0);}else{$nf['C']++;}$browserKey=$this->getHttpRequest()->getCookie('nette-browser');if(!$browserKey){$browserKey=(string)lcg_value();}$browserClosed=!isset($nf['B'])||$nf['B']!==$browserKey;$nf['B']=$browserKey;$this->sendCookie();if(isset($nf['META'])){$now=time();foreach($nf['META']as$namespace=>$metadata){if(is_array($metadata)){foreach($metadata
as$variable=>$value){if((!empty($value['B'])&&$browserClosed)||(!empty($value['T'])&&$now>$value['T'])||($variable!==''&&is_object($nf['DATA'][$namespace][$variable])&&(isset($value['V'])?$value['V']:NULL)!==NetteX\Reflection\ClassReflection::from($nf['DATA'][$namespace][$variable])->getAnnotation('serializationVersion'))){if($variable===''){unset($nf['META'][$namespace],$nf['DATA'][$namespace]);continue
2;}unset($nf['META'][$namespace][$variable],$nf['DATA'][$namespace][$variable]);}}}}}register_shutdown_function(array($this,'clean'));}function
isStarted(){return(bool)self::$started;}function
close(){if(self::$started){$this->clean();session_write_close();self::$started=FALSE;}}function
destroy(){if(!self::$started){throw
new\XInvalidStateException('Session is not started.');}session_destroy();$_SESSION=NULL;self::$started=FALSE;if(!$this->getHttpResponse()->isSent()){$params=session_get_cookie_params();$this->getHttpResponse()->deleteCookie(session_name(),$params['path'],$params['domain'],$params['secure']);}}function
exists(){return
self::$started||$this->getHttpRequest()->getCookie(session_name())!==NULL;}function
regenerateId(){if(self::$started){if(headers_sent($file,$line)){throw
new\XInvalidStateException("Cannot regenerate session ID after HTTP headers have been sent".($file?" (output started at $file:$line).":"."));}session_regenerate_id(TRUE);}else{$this->regenerationNeeded=TRUE;}}function
getId(){return
session_id();}function
setName($name){if(!is_string($name)||!preg_match('#[^0-9.][^.]*$#A',$name)){throw
new\InvalidArgumentException('Session name must be a string and cannot contain dot.');}session_name($name);return$this->setOptions(array('name'=>$name));}function
getName(){return
session_name();}function
getNamespace($namespace,$class='NetteX\Web\SessionNamespace'){if(!is_string($namespace)||$namespace===''){throw
new\InvalidArgumentException('Session namespace must be a non-empty string.');}if(!self::$started){$this->start();}return
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
new\XInvalidStateException("Unable to set '$key' when session has been started.");}$key="session_$key";$key($value);}elseif(strncmp($key,'cookie_',7)===0){if(!isset($cookie)){$cookie=session_get_cookie_params();}$cookie[substr($key,7)]=$value;}elseif(!function_exists('ini_set')){if(ini_get($key)!=$value&&!NetteX\Framework::$iAmUsingBadHost){throw
new\XNotSupportedException('Required function ini_set() is disabled.');}}else{if(self::$started){throw
new\XInvalidStateException("Unable to set '$key' when session has been started.");}ini_set("session.$key",$value);}}if(isset($cookie)){session_set_cookie_params($cookie['lifetime'],$cookie['path'],$cookie['domain'],$cookie['secure'],$cookie['httponly']);if(self::$started){$this->sendCookie();}}}function
setExpiration($time){if(empty($time)){return$this->setOptions(array('gc_maxlifetime'=>self::DEFAULT_FILE_LIFETIME,'cookie_lifetime'=>0));}else{$time=NetteX\Tools::createDateTime($time)->format('U');return$this->setOptions(array('gc_maxlifetime'=>$time,'cookie_lifetime'=>$time));}}function
setCookieParams($path,$domain=NULL,$secure=NULL){return$this->setOptions(array('cookie_path'=>$path,'cookie_domain'=>$domain,'cookie_secure'=>$secure));}function
getCookieParams(){return
session_get_cookie_params();}function
setSavePath($path){return$this->setOptions(array('save_path'=>$path));}private
function
sendCookie(){$cookie=$this->getCookieParams();$this->getHttpResponse()->setCookie(session_name(),session_id(),$cookie['lifetime'],$cookie['path'],$cookie['domain'],$cookie['secure'],$cookie['httponly']);$this->getHttpResponse()->setCookie('nette-browser',$_SESSION['__NF']['B'],HttpResponse::BROWSER,$cookie['path'],$cookie['domain'],$cookie['secure'],$cookie['httponly']);}protected
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
__set($name,$value){$this->data[$name]=$value;if(is_object($value)){$this->meta[$name]['V']=NetteX\Reflection\ClassReflection::from($value)->getAnnotation('serializationVersion');}}function&__get($name){if($this->warnOnUndefined&&!array_key_exists($name,$this->data)){trigger_error("The variable '$name' does not exist in session namespace",E_USER_NOTICE);}return$this->data[$name];}function
__isset($name){return
isset($this->data[$name]);}function
__unset($name){unset($this->data[$name],$this->meta[$name]);}function
offsetSet($name,$value){$this->__set($name,$value);}function
offsetGet($name){return$this->__get($name);}function
offsetExists($name){return$this->__isset($name);}function
offsetUnset($name){$this->__unset($name);}function
setExpiration($time,$variables=NULL){if(empty($time)){$time=NULL;$whenBrowserIsClosed=TRUE;}else{$time=NetteX\Tools::createDateTime($time)->format('U');$whenBrowserIsClosed=FALSE;}if($variables===NULL){$this->meta['']['T']=$time;$this->meta['']['B']=$whenBrowserIsClosed;}elseif(is_array($variables)){foreach($variables
as$variable){$this->meta[$variable]['T']=$time;$this->meta[$variable]['B']=$whenBrowserIsClosed;}}else{$this->meta[$variables]['T']=$time;$this->meta[$variables]['B']=$whenBrowserIsClosed;}return$this;}function
removeExpiration($variables=NULL){if($variables===NULL){unset($this->meta['']['T'],$this->meta['']['B']);}elseif(is_array($variables)){foreach($variables
as$variable){unset($this->meta[$variable]['T'],$this->meta[$variable]['B']);}}else{unset($this->meta[$variables]['T'],$this->meta[$variable]['B']);}}function
remove(){$this->data=NULL;$this->meta=NULL;}}class
Uri
extends
NetteX\FreezableObject{public
static$defaultPorts=array('http'=>80,'https'=>443,'ftp'=>21,'news'=>119,'nntp'=>119);private$scheme='';private$user='';private$pass='';private$host='';private$port=NULL;private$path='';private$query='';private$fragment='';function
__construct($uri=NULL){if(is_string($uri)){$parts=@parse_url($uri);if($parts===FALSE){throw
new\InvalidArgumentException("Malformed or unsupported URI '$uri'.");}foreach($parts
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
UriScript
extends
Uri{private$scriptPath='/';function
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
MANUAL=1;const
INACTIVITY=2;const
BROWSER_CLOSED=3;public$guestRole='guest';public$authenticatedRole='authenticated';public$onLoggedIn;public$onLoggedOut;private$authenticationHandler;private$authorizationHandler;private$namespace='';private$session;function
login($username=NULL,$password=NULL){$handler=$this->getAuthenticationHandler();if($handler===NULL){throw
new\XInvalidStateException('Authentication handler has not been set.');}$this->logout(TRUE);$credentials=func_get_args();$this->setIdentity($handler->authenticate($credentials));$this->setAuthenticated(TRUE);$this->onLoggedIn($this);}final
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
setExpiration($time,$whenBrowserIsClosed=TRUE,$clearIdentity=FALSE){$session=$this->getSessionNamespace(TRUE);if($time){$time=NetteX\Tools::createDateTime($time)->format('U');$session->expireTime=$time;$session->expireDelta=$time-time();}else{unset($session->expireTime,$session->expireDelta);}$session->expireIdentity=(bool)$clearIdentity;$session->expireBrowser=(bool)$whenBrowserIsClosed;$session->browserCheck=TRUE;$session->setExpiration(0,'browserCheck');return$this;}final
function
getLogoutReason(){$session=$this->getSessionNamespace(FALSE);return$session?$session->reason:NULL;}protected
function
getSessionNamespace($need){if($this->session!==NULL){return$this->session;}$sessionHandler=$this->getSession();if(!$need&&!$sessionHandler->exists()){return
NULL;}$this->session=$session=$sessionHandler->getNamespace('NetteX.Web.User/'.$this->namespace);if(!($session->identity
instanceof
IIdentity)||!is_bool($session->authenticated)){$session->remove();}if($session->authenticated&&$session->expireBrowser&&!$session->browserCheck){$session->reason=self::BROWSER_CLOSED;$session->authenticated=FALSE;$this->onLoggedOut($this);if($session->expireIdentity){unset($session->identity);}}if($session->authenticated&&$session->expireDelta>0){if($session->expireTime<time()){$session->reason=self::INACTIVITY;$session->authenticated=FALSE;$this->onLoggedOut($this);if($session->expireIdentity){unset($session->identity);}}$session->expireTime=time()+$session->expireDelta;}if(!$session->authenticated){unset($session->expireTime,$session->expireDelta,$session->expireIdentity,$session->expireBrowser,$session->browserCheck,$session->authTime);}return$this->session;}protected
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
new\XInvalidStateException("Authorization handler has not been set.");}foreach($this->getRoles()as$role){if($handler->isAllowed($role,$resource,$privilege))return
TRUE;}return
FALSE;}function
setAuthorizationHandler(IAuthorizator$handler){$this->authorizationHandler=$handler;return$this;}final
function
getAuthorizationHandler(){if($this->authorizationHandler===NULL){$this->authorizationHandler=Environment::getService('NetteX\\Security\\IAuthorizator');}return$this->authorizationHandler;}protected
function
getSession(){return
Environment::getSession();}}}