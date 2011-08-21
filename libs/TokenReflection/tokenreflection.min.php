<?php
/**
 * PHP Token Reflection
 *
 * Version 1.0 beta 7
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this library in the file license.txt.
 *
 * @author Ondřej Nešpor <andrew@andrewsville.cz>
 * @author Jaroslav Hanslík <kukulich@kukulich.cz>
 */
 namespace TokenReflection\Broker{use TokenReflection;interface Backend{const TOKENIZED_CLASSES=1;const
INTERNAL_CLASSES=2;const NONEXISTENT_CLASSES=4;public function hasNamespace($namespaceName);public
function getNamespace($namespaceName);public function hasClass($className);public
function getClass($className);public function getClasses($type=Backend::TOKENIZED_CLASSES);public
function hasConstant($constantName);public function getConstant($constantName);public
function getConstants();public function hasFunction($functionName);public function
getFunction($functionName);public function getFunctions();public function isFileProcessed($fileName);public
function getFileTokens($fileName);public function addFile(TokenReflection\ReflectionFile$file);public
function setBroker(TokenReflection\Broker$broker);public function getBroker();public
function setStoringTokenStreams($store);public function getStoringTokenStreams();}}

 namespace TokenReflection{use TokenReflection\Broker,TokenReflection\Exception;use
RecursiveDirectoryIterator,RecursiveIteratorIterator;class Broker{const OPTION_SAVE_TOKEN_STREAM=0x0001;const
OPTION_PARSE_FUNCTION_BODY=0x0002;const OPTION_DEFAULT=0x0003;const CACHE_NAMESPACE='namespace';const
CACHE_CLASS='class';const CACHE_CONSTANT='constant';const CACHE_FUNCTION='function';private$backend;private$cache;private$options;public
function __construct(Broker\Backend$backend,$options=self::OPTION_DEFAULT){$this->cache=array(self::CACHE_NAMESPACE=>array(),self::CACHE_CLASS=>array(),self::CACHE_CONSTANT=>array(),self::CACHE_FUNCTION=>array());$this->options=$options;$this->backend=$backend->setBroker($this)->setStoringTokenStreams((bool)($options&self::OPTION_SAVE_TOKEN_STREAM));}public
function getOptions(){return$this->options;}public function isOptionSet($option){return
(bool)($this->options&$option);}public function processFile($fileName,$returnReflectionFile=false){try{if($this->backend->isFileProcessed($fileName)){$tokens=$this->backend->getFileTokens($fileName);}else{$tokens=new
Stream($fileName);}$reflectionFile=new ReflectionFile($tokens,$this);if(!$this->backend->isFileProcessed($fileName)){$this->backend->addFile($reflectionFile);foreach($this->cache
as$type=>$cached){if(!empty($cached)){$this->cache[$type]=array_filter($cached,function(IReflection$reflection){return$reflection->isTokenized();});}}}return$returnReflectionFile?$reflectionFile:true;}catch(Exception$e){throw
new Exception\Parse(sprintf('Could not process file %s.',$fileName),0,$e);}}public
function processPhar($fileName,$returnReflectionFile=false){try{if(!is_file($fileName)){throw
new Exception\Parse('File does not exist.',Exception\Parse::FILE_DOES_NOT_EXIST);}if(!extension_loaded('Phar')){throw
new Exception\Parse('The PHAR PHP extension is not loaded.',Exception\Parse::UNSUPPORTED);}$result=array();foreach(new
RecursiveIteratorIterator(new \Phar($fileName))as$entry){if($entry->isFile()){$result[$entry->getPathName()]=$this->processFile($entry->getPathName(),$returnReflectionFile);}}return$returnReflectionFile?$result:true;}catch(\Exception$e){throw
new Exception\Parse(sprintf('Could not process PHAR archive %s.',$fileName),0,$e);}}public
function processDirectory($path,$returnReflectionFile=false){try{$realPath=realpath($path);if(!is_dir($realPath)){throw
new Exception\Parse('Directory does not exist.',Exception\Parse::FILE_DOES_NOT_EXIST);}$result=array();foreach(new
RecursiveIteratorIterator(new RecursiveDirectoryIterator($realPath))as$entry){if($entry->isFile()){$result[$entry->getPathName()]=$this->processFile($entry->getPathName(),$returnReflectionFile);}}return$returnReflectionFile?$result:true;}catch(Exception$e){throw
new Exception\Parse(sprintf('Could not process directory %s.',$path),0,$e);}}public
function process($path,$returnReflectionFile=false){if(is_dir($path)){return$this->processDirectory($path,$returnReflectionFile);}elseif(is_file($path)){if(preg_match('~\\.phar$~i',$path)){try{return$this->processPhar($path,$returnReflectionFile);}catch(Exception\Parse$e){if(!($ex=$e->getPrevious())||!($ex
instanceof \UnexpectedValueException)){throw$e;}}}return$this->processFile($path,$returnReflectionFile);}else{throw
new Exception\Parse(sprintf('Could not process target %s; target does not exist.',$path));}}public
function hasNamespace($namespaceName){return isset($this->cache[self::CACHE_NAMESPACE][$namespaceName])||$this->backend->hasNamespace($namespaceName);}public
function getNamespace($namespaceName){$namespaceName=ltrim($namespaceName,'\\');if(isset($this->cache[self::CACHE_NAMESPACE][$namespaceName])){return$this->cache[self::CACHE_NAMESPACE][$namespaceName];}$namespace=$this->backend->getNamespace($namespaceName);if(null!==$namespace){$this->cache[self::CACHE_NAMESPACE][$namespaceName]=$namespaceName;}return$namespace;}public
function hasClass($className){return isset($this->cache[self::CACHE_CLASS][$className])||$this->backend->hasClass($className);}public
function getClass($className){$className=ltrim($className,'\\');if(isset($this->cache[self::CACHE_CLASS][$className])){return$this->cache[self::CACHE_CLASS][$className];}$this->cache[self::CACHE_CLASS][$className]=$this->backend->getClass($className);return$this->cache[self::CACHE_CLASS][$className];}public
function getClasses($types=Broker\Backend::TOKENIZED_CLASSES){return$this->backend->getClasses($types);}public
function hasConstant($constantName){return isset($this->cache[self::CACHE_CONSTANT][$constantName])||$this->backend->hasConstant($constantName);}public
function getConstant($constantName){$constantName=ltrim($constantName,'\\');if(isset($this->cache[self::CACHE_CONSTANT][$constantName])){return$this->cache[self::CACHE_CONSTANT][$constantName];}if($constant=$this->backend->getConstant($constantName)){$this->cache[self::CACHE_CONSTANT][$constantName]=$constant;}return$constant;}public
function getConstants(){return$this->backend->getConstants();}public function hasFunction($functionName){return
isset($this->cache[self::CACHE_FUNCTION][$functionName])||$this->backend->hasFunction($functionName);}public
function getFunction($functionName){$functionName=ltrim($functionName,'\\');if(isset($this->cache[self::CACHE_FUNCTION][$functionName])){return$this->cache[self::CACHE_FUNCTION][$functionName];}if($function=$this->backend->getFunction($functionName)){$this->cache[self::CACHE_FUNCTION][$functionName]=$function;}return$function;}public
function getFunctions(){return$this->backend->getFunctions();}public function getFileTokens($fileName){try{return$this->backend->getFileTokens($fileName);}catch(Exception$e){throw
new Exception\Runtime(sprintf('Could not retrieve token stream for file %s.',$fileName),0,$e);}}public
static function getRealPath($path){if(0===strpos($path,'phar://')){return is_file($path)||is_dir($path)?$path:false;}else{return
realpath($path);}}}}

 namespace TokenReflection{use Exception as InternalException;abstract class Exception
extends InternalException{const UNSUPPORTED=1;const DOES_NOT_EXIST=2;}}

 namespace TokenReflection{interface IReflection{public function getName();public
function isInternal();public function isUserDefined();public function isTokenized();public
function getBroker();public function __get($key);public function __isset($key);}}

 namespace TokenReflection{use TokenReflection\Exception;class ReflectionAnnotation{const
SHORT_DESCRIPTION=' short_description';const LONG_DESCRIPTION=' long_description';private$templates=array();private$annotations;private$docComment;private$reflection;public
function __construct(ReflectionBase$reflection,$docComment=false){$this->reflection=$reflection;$this->docComment=$docComment?:false;}public
function getDocComment(){return$this->docComment;}public function hasAnnotation($annotation){if(null===$this->annotations){$this->parse();}return
isset($this->annotations[$annotation]);}public function getAnnotation($annotation){if(null===$this->annotations){$this->parse();}return
isset($this->annotations[$annotation])?$this->annotations[$annotation]:null;}public
function getAnnotations(){if(null===$this->annotations){$this->parse();}return$this->annotations;}public
function setTemplates(array$templates){foreach($templates as$template){if(!$template
instanceof ReflectionAnnotation){throw new Exception\Runtime(sprintf('All templates have to be instances of \\TokenReflection\\ReflectionAnnotation; %s given.',is_object($template)?get_class($template):gettype($template)),Exception\Runtime::INVALID_ARGUMENT);}}$this->templates=$templates;return$this;}private
function parse(){$this->annotations=array();if(false!==$this->docComment){$name=self::SHORT_DESCRIPTION;$docblock=trim(preg_replace(array('~^'.preg_quote(ReflectionBase::DOCBLOCK_TEMPLATE_START,'~').'~','~^'.preg_quote(ReflectionBase::DOCBLOCK_TEMPLATE_END,'~').'$~','~^/\\*\\*~','~\\*/$~'),'',$this->docComment));foreach(explode("\n",$docblock)as$line){$line=preg_replace('~^\\*\\s?~','',trim($line));if(''===$line&&self::SHORT_DESCRIPTION===$name){$name=self::LONG_DESCRIPTION;continue;}if(preg_match('~^@([\\S]+)\\s*(.*)~',$line,$matches)){$name=$matches[1];$this->annotations[$name][]=$matches[2];continue;}if(self::SHORT_DESCRIPTION===$name||self::LONG_DESCRIPTION===$name){if(!isset($this->annotations[$name])){$this->annotations[$name]=$line;}else{$this->annotations[$name].="\n".$line;}}else{$this->annotations[$name][count($this->annotations[$name])-1].="\n".$line;}}array_walk_recursive($this->annotations,function(&$value){$value=str_replace('{@*}','*/',$value);$value=trim($value);});}$this->mergeTemplates();if($this->reflection
instanceof ReflectionClass||$this->reflection instanceof ReflectionMethod||$this->reflection
instanceof ReflectionProperty){$this->inheritAnnotations();}}private function mergeTemplates(){foreach($this->templates
as$index=>$template){if(0===$index&&$template->getDocComment()===$this->docComment){continue;}foreach($template->getAnnotations()as$name=>$value){if($name===self::LONG_DESCRIPTION){if(isset($this->annotations[self::LONG_DESCRIPTION])){$this->annotations[self::LONG_DESCRIPTION]=$value."\n".$this->annotations[self::LONG_DESCRIPTION];}else{$this->annotations[self::LONG_DESCRIPTION]=$value;}}elseif($name!==self::SHORT_DESCRIPTION){if(isset($this->annotations[$name])){$this->annotations[$name]=array_merge($this->annotations[$name],$value);}else{$this->annotations[$name]=$value;}}}}}private
function inheritAnnotations(){if($this->reflection instanceof ReflectionClass){$declaringClass=$this->reflection;}elseif($this->reflection
instanceof ReflectionMethod||$this->reflection instanceof ReflectionProperty){$declaringClass=$this->reflection->getDeclaringClass();}else{throw
new Exception\Parse(sprintf('Unsupported reflection type: "%s".',get_class($this->reflection)),Exception\Parse::UNSUPPORTED);}$parents=array_filter(array_merge(array($declaringClass->getParentClass()),$declaringClass->getOwnInterfaces()),function($class){return$class
instanceof ReflectionClass;});$parentDefinitions=array();if($this->reflection instanceof
ReflectionProperty){$name=$this->reflection->getName();foreach($parents as$parent){try{$parentDefinitions[]=$parent->getProperty($name);}catch(Exception\Runtime$e){if(Exception\Runtime::DOES_NOT_EXIST===$e->getCode()){continue;}throw$e;}}$parents=$parentDefinitions;}elseif($this->reflection
instanceof ReflectionMethod){$name=$this->reflection->getName();foreach($parents
as$parent){try{$parentDefinitions[]=$parent->getMethod($name);}catch(Exception\Runtime$e){if(Exception\Runtime::DOES_NOT_EXIST===$e->getCode()){continue;}throw$e;}}$parents=$parentDefinitions;}if(false===$this->docComment){foreach($parents
as$parent){$annotations=$parent->getAnnotations();if(!empty($annotations)){$this->annotations=$annotations;break;}}}else{if(isset($this->annotations[self::LONG_DESCRIPTION])&&false!==stripos($this->annotations[self::LONG_DESCRIPTION],'{@inheritdoc}')){foreach($parents
as$parent){if($parent->hasAnnotation(self::LONG_DESCRIPTION)){$this->annotations[self::LONG_DESCRIPTION]=str_ireplace('{@inheritdoc}',$parent->getAnnotation(self::LONG_DESCRIPTION),$this->annotations[self::LONG_DESCRIPTION]);break;}}$this->annotations[self::LONG_DESCRIPTION]=str_ireplace('{@inheritdoc}','',$this->annotations[self::LONG_DESCRIPTION]);}if(isset($this->annotations[self::SHORT_DESCRIPTION])&&false!==stripos($this->annotations[self::SHORT_DESCRIPTION],'{@inheritdoc}')){foreach($parents
as$parent){if($parent->hasAnnotation(self::SHORT_DESCRIPTION)){$this->annotations[self::SHORT_DESCRIPTION]=str_ireplace('{@inheritdoc}',$parent->getAnnotation(self::SHORT_DESCRIPTION),$this->annotations[self::SHORT_DESCRIPTION]);break;}}$this->annotations[self::SHORT_DESCRIPTION]=str_ireplace('{@inheritdoc}','',$this->annotations[self::SHORT_DESCRIPTION]);}}if($this->reflection
instanceof ReflectionProperty&&empty($this->annotations['var'])){foreach($parents
as$parent){if($parent->hasAnnotation('var')){$this->annotations['var']=$parent->getAnnotation('var');break;}}}if($this->reflection
instanceof ReflectionMethod){if(0!==$this->reflection->getNumberOfParameters()&&(empty($this->annotations['param'])||count($this->annotations['param'])<$this->reflection->getNumberOfParameters())){$params=isset($this->annotations['param'])?$this->annotations['param']:array();foreach($parents
as$parent){if($parent->hasAnnotation('param')){$parentParams=array_slice($parent->getAnnotation('param'),count($params));while(!empty($parentParams)){array_push($params,array_shift($parentParams));if(count($params)===$this->reflection->getNumberOfParameters()){break
2;}}}}if(!empty($params)){$this->annotations['param']=$params;}}foreach(array('return','throws')as$paramName){if(!isset($this->annotations[$paramName])){foreach($parents
as$parent){if($parent->hasAnnotation($paramName)){$this->annotations[$paramName]=$parent->getAnnotation($paramName);break;}}}}}}}}

 namespace TokenReflection{class Resolver{const CONSTANT_NOT_FOUND='~~NOT RESOLVED~~';final
public function __construct(){throw new \LogicException('Static class cannot be instantiated.');}final
public static function resolveClassFQN($className,array$aliases,$namespaceName=null){if($className{0}=='\\'){return
ltrim($className,'\\');}if(false===($position=strpos($className,'\\'))){if(isset($aliases[$className])){return$aliases[$className];}}else{$alias=substr($className,0,$position);if(isset($aliases[$alias])){return$aliases[$alias].'\\'.substr($className,$position+1);}}return
null===$namespaceName||''===$namespaceName||$namespaceName===ReflectionNamespace::NO_NAMESPACE_NAME?$className:$namespaceName.'\\'.$className;}final
public static function getValueDefinition(array$tokens,ReflectionBase$reflection){if($reflection
instanceof ReflectionConstant||$reflection instanceof ReflectionFunction){$namespace=$reflection->getNamespaceName();}elseif($reflection
instanceof ReflectionParameter){$namespace=$reflection->getDeclaringFunction()->getNamespaceName();}elseif($reflection
instanceof ReflectionProperty||$reflection instanceof ReflectionMethod){$namespace=$reflection->getDeclaringClass()->getNamespaceName();}else{throw
new Exception\Runtime(sprintf('Invalid reflection object given: "%s" ("%s")',get_class($reflection),$reflection->getName()),Exception\Runtime::INVALID_ARGUMENT);}$source=self::getSourceCode($tokens);$constants=self::findConstants($tokens,$reflection);if(!empty($constants)){$replacements=array();foreach($constants
as$constant){try{if(0===stripos($constant,'self::')||0===stripos($constant,'parent::')){if($reflection
instanceof ReflectionConstant){throw new Exception\Runtime('Constants cannot use self:: and parent:: references.',Exception\Runtime::INVALID_ARGUMENT);}elseif($reflection
instanceof ReflectionParameter&&null===$reflection->getDeclaringClassName()){throw
new Exception\Runtime('Function parameters cannot use self:: and parent:: references.',Exception\Runtime::INVALID_ARGUMENT);}if(0===stripos($constant,'self::')){$className=$reflection->getDeclaringClassName();}else{$declaringClass=$reflection->getDeclaringClass();$className=$declaringClass->getParentClassName()?:self::CONSTANT_NOT_FOUND;}$constantName=$className.substr($constant,strpos($constant,'::'));}else{$constantName=self::resolveClassFQN($constant,$reflection->getNamespaceAliases(),$namespace);if($cnt=strspn($constant,'\\')){$constantName=str_repeat('\\',$cnt).$constantName;}}$reflection=$reflection->getBroker()->getConstant($constantName);$value=$reflection->getValue();}catch(Exception\Runtime$e){$value=self::CONSTANT_NOT_FOUND;}$replacements[$constant]=var_export($value,true);}uksort($replacements,function($a,$b){$ca=strspn($a,'\\');$cb=strspn($b,'\\');return$ca===$cb?strcasecmp($b,$a):$cb-$ca;});$source=strtr($source,$replacements);}return
eval(sprintf('return %s;',$source));}final public static function getSourceCode(array$tokens){if(empty($tokens)){return
null;}$source='';foreach($tokens as$token){$source.=$token[1];}return$source;}final
public static function findConstants(array$tokens,ReflectionBase$reflection){static$accepted=array(T_DOUBLE_COLON=>true,T_STRING=>true,T_NS_SEPARATOR=>true);static$dontResolve=array('true'=>true,'false'=>true,'null'=>true);$tokens[]=array(-1);$constants=array();$constant='';foreach($tokens
as$token){if(isset($accepted[$token[0]])){$constant.=$token[1];}elseif(''!==$constant){if(!isset($dontResolve[strtolower($constant)])){$constants[$constant]=true;}$constant='';}}return
array_keys($constants);}}}

 namespace TokenReflection{use TokenReflection\Exception;use SeekableIterator,Countable,ArrayAccess,Serializable;class
Stream implements SeekableIterator,Countable,ArrayAccess,Serializable{private$fileName='unknown';private$tokens=array();private$position=0;private$count=0;public
function __construct($fileName){if(!extension_loaded('tokenizer')){throw new Exception\Parse('The tokenizer PHP extension is not loaded.',Exception\Parse::UNSUPPORTED);}$this->fileName=Broker::getRealPath($fileName);if(false===$this->fileName){throw
new Exception\Parse('File does not exist.',Exception\Parse::FILE_DOES_NOT_EXIST);}$contents=file_get_contents($this->fileName);if(false===$contents){throw
new Exception\Parse('File is not readable.',Exception\Parse::FILE_NOT_READABLE);}$stream=@token_get_all(str_replace(array("\r\n","\r"),"\n",$contents));static$checkLines=array(T_COMMENT=>true,T_WHITESPACE=>true,T_DOC_COMMENT=>true,T_INLINE_HTML=>true,T_ENCAPSED_AND_WHITESPACE=>true,T_CONSTANT_ENCAPSED_STRING=>true);foreach($stream
as$position=>$token){if(is_array($token)){$this->tokens[]=$token;}else{$previous=$this->tokens[$position-1];$line=$previous[2];if(isset($checkLines[$previous[0]])){$line+=substr_count($previous[1],"\n");}$this->tokens[]=array($token,$token,$line);}}$this->count=count($this->tokens);}public
function getFileName(){return$this->fileName;}public function getSource(){return$this->getSourcePart();}public
function getSourcePart($start=null,$end=null){$start=(int)$start;$end=null===$end?($this->count-1):(int)$end;$source='';for($i=$start;$i<=$end;$i++){$source.=$this->tokens[$i][1];}return$source;}public
function find($type){$actual=$this->position;while(isset($this->tokens[$this->position])){if($type===$this->tokens[$this->position][0]){return$this;}$this->position++;}$this->position=$actual;return
false;}public function findMatchingBracket(){static$brackets=array('('=>')','{'=>'}','['=>']',T_CURLY_OPEN=>'}',T_DOLLAR_OPEN_CURLY_BRACES=>'}');if(!$this->valid()){throw
new Exception\Runtime('Out of array.',Exception\Runtime::DOES_NOT_EXIST);}$position=$this->position;$bracket=$this->tokens[$this->position][0];if(!isset($brackets[$bracket])){throw
new Exception\Runtime(sprintf('There is no usable bracket at position "%d" in file "%s".',$position,$this->fileName),Exception\Runtime::DOES_NOT_EXIST);}$searching=$brackets[$bracket];$level=0;while(isset($this->tokens[$this->position])){$type=$this->tokens[$this->position][0];if($searching===$type){$level--;}elseif($bracket===$type||($searching==='}'&&('{'===$type||T_CURLY_OPEN===$type||T_DOLLAR_OPEN_CURLY_BRACES===$type))){$level++;}if(0===$level){return$this;}$this->position++;}throw
new Exception\Runtime(sprintf('Could not find the end bracket "%s" of the bracket at position "%d" in file "%s".',$searching,$position,$this->fileName),Exception\Runtime::DOES_NOT_EXIST);}public
function skipWhitespaces(){static$skipped=array(T_WHITESPACE=>true,T_COMMENT=>true);do{$this->position++;}while(isset($this->tokens[$this->position])&&isset($skipped[$this->tokens[$this->position][0]]));return$this;}public
function is($type,$position=-1){return$type===$this->getType($position);}public function
getType($position=-1){if(-1===$position){$position=$this->position;}return isset($this->tokens[$position])?$this->tokens[$position][0]:null;}public
function getTokenValue($position=-1){if(-1===$position){$position=$this->position;}return
isset($this->tokens[$position])?$this->tokens[$position][1]:null;}public function
getTokenName($position=-1){$type=$this->getType($position);return is_string($type)?$type:token_name($type);}public
function serialize(){return serialize(array($this->fileName,$this->tokens));}public
function unserialize($serialized){$data=@unserialize($serialized);if(false===$data){throw
new Exception\Runtime('Could not deserialize the serialized data.',Exception\Runtime::SERIALIZATION_ERROR);}if(2!==count($data)||!is_string($data[0])||!is_array($data[1])){throw
new Exception\Runtime('Invalid serialization data.',Exception\Runtime::SERIALIZATION_ERROR);}$this->fileName=$data[0];$this->tokens=$data[1];$this->count=count($this->tokens);$this->position=0;}public
function offsetExists($offset){return isset($this->tokens[$offset]);}public function
offsetUnset($offset){throw new Exception\Runtime('Removing of tokens from the stream is not supported.',Exception\Runtime::UNSUPPORTED);}public
function offsetGet($offset){return isset($this->tokens[$offset])?$this->tokens[$offset]:null;}public
function offsetSet($offset,$value){throw new Exception\Runtime('Setting token values is not supported.',Exception\Runtime::UNSUPPORTED);}public
function key(){return$this->position;}public function next(){$this->position++;return$this;}public
function rewind(){$this->position=0;return$this;}public function current(){return
isset($this->tokens[$this->position])?$this->tokens[$this->position]:null;}public
function valid(){return isset($this->tokens[$this->position]);}public function count(){return$this->count;}public
function seek($position){$this->position=(int)$position;return$this;}public function
__toString(){return$this->getSource();}}}

 namespace TokenReflection\Broker\Backend{use TokenReflection;use TokenReflection\Stream,TokenReflection\Exception,TokenReflection\Broker,TokenReflection\Php,TokenReflection\Dummy;class
Memory implements Broker\Backend{private$namespaces=array();private$allConstants;private$allClasses;private$allFunctions;private$tokenStreams=array();private$broker;private$storingTokenStreams;public
function hasNamespace($namespaceName){return isset($this->namespaces[ltrim($namespaceName,'\\')]);}public
function getNamespace($namespaceName){if(!isset($this->namespaces[TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME])){$this->namespaces[TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME]=new
TokenReflection\ReflectionNamespace(TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME,$this->broker);}$namespaceName=ltrim($namespaceName,'\\');if(!isset($this->namespaces[$namespaceName])){throw
new Exception\Runtime(sprintf('Namespace %s does not exist.',$namespaceName),TokenReflection\Exception::DOES_NOT_EXIST);}return$this->namespaces[$namespaceName];}public
function getNamespaces(){return$this->namespaces;}public function hasClass($className){$className=ltrim($className,'\\');if($pos=strrpos($className,'\\')){$namespace=substr($className,$pos);if(!isset($this->namespaces[$namespace])){return
false;}$namespace=$this->getNamespace($namespace);$className=substr($className,$pos+1);}else{$namespace=$this->getNamespace(TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME);}return$namespace->hasClass($className);}public
function getClass($className){static$declared=array();if(empty($declared)){$declared=array_flip(array_merge(get_declared_classes(),get_declared_interfaces()));}$className=ltrim($className,'\\');try{$ns=$this->getNamespace(($boundary=strrpos($className,'\\'))?substr($className,0,$boundary):TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME);return$ns->getClass($className);}catch(TokenReflection\Exception$e){if(isset($declared[$className])){$reflection=new
Php\ReflectionClass($className,$this->broker);if($reflection->isInternal()){return$reflection;}}return
new Dummy\ReflectionClass($className,$this->broker);}}public function getClasses($type=self::TOKENIZED_CLASSES){if(null===$this->allClasses){$this->allClasses=$this->parseClassLists();}$result=array();foreach($this->allClasses
as$classType=>$classes){if($type&$classType){$result=array_merge($result,$classes);}}return$result;}public
function hasConstant($constantName){$constantName=ltrim($constantName,'\\');if($pos=strpos($constantName,'::')){$className=substr($constantName,0,$pos);$constantName=substr($constantName,$pos+2);if(!$this->hasClass($className)){return
false;}$parent=$this->getClass($className);}else{if($pos=strrpos($constantName,'\\')){$namespace=substr($constantName,$pos);if(!$this->hasNamespace($namespace)){return
false;}$parent=$this->getNamespace($namespace);$constantName=substr($constantName,$pos+1);}else{$parent=$this->getNamespace(TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME);}}return$parent->hasConstant($constantName);}public
function getConstant($constantName){static$declared=array();if(empty($declared)){$declared=get_defined_constants();}if($boundary=strpos($constantName,'::')){$className=substr($constantName,0,$boundary);$constantName=substr($constantName,$boundary+2);try{return$this->getClass($className)->getConstantReflection($constantName);}catch(TokenReflection\Exception$e){throw
new Exception\Runtime(sprintf('Constant %s does not exist.',$constantName),0,$e);}}try{$constantName=ltrim($constantName,'\\');if($boundary=strrpos($constantName,'\\')){$ns=$this->getNamespace(substr($constantName,0,$boundary));$constantName=substr($constantName,$boundary+1);}else{$ns=$this->getNamespace(TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME);}return$ns->getConstant($constantName);}catch(TokenReflection\Exception$e){if(isset($declared[$constantName])){$reflection=new
Php\ReflectionConstant($constantName,$declared[$constantName],$this->broker);if($reflection->isInternal()){return$reflection;}}throw
new Exception\Runtime(sprintf('Constant %s does not exist.',$constantName),0,$e);}}public
function getConstants(){if(null===$this->allConstants){$this->allConstants=array();foreach($this->namespaces
as$namespace){foreach($namespace->getConstants()as$constant){$this->allConstants[$constant->getName()]=$constant;}}}return$this->allConstants;}public
function hasFunction($functionName){$functionName=ltrim($functionName,'\\');if($pos=strrpos($functionName,'\\')){$namespace=substr($functionName,$pos);if(!isset($this->namespaces[$namespace])){return
false;}$namespace=$this->getNamespace($namespace);$functionName=substr($functionName,$pos+1);}else{$namespace=$this->getNamespace(TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME);}return$namespace->hasFunction($functionName);}public
function getFunction($functionName){static$declared=array();if(empty($declared)){$functions=get_defined_functions();$declared=array_flip($functions['internal']);}$functionName=ltrim($functionName,'\\');try{$ns=$this->getNamespace(($boundary=strrpos($functionName,'\\'))?substr($functionName,0,$boundary):TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME);return$ns->getFunction($functionName);}catch(TokenReflection\Exception$e){if(isset($declared[$functionName])){return
new Php\ReflectionFunction($functionName,$this->broker);}throw new Exception\Runtime(sprintf('Function %s does not exist.',$functionName),0,$e);}}public
function getFunctions(){if(null===$this->allFunctions){$this->allFunctions=array();foreach($this->namespaces
as$namespace){foreach($namespace->getFunctions()as$function){$this->allFunctions[$function->getName()]=$function;}}}return$this->allFunctions;}public
function isFileProcessed($fileName){return isset($this->tokenStreams[Broker::getRealPath($fileName)]);}public
function getFileTokens($fileName){$realName=Broker::getRealPath($fileName);if(!isset($this->tokenStreams[$realName])){throw
new Exception\Runtime(sprintf('File "%s" was not processed yet.',$fileName),Exception\Runtime::DOES_NOT_EXIST);}return
true===$this->tokenStreams[$realName]?new Stream($realName):$this->tokenStreams[$realName];}public
function addFile(TokenReflection\ReflectionFile$file){foreach($file->getNamespaces()as$fileNamespace){$namespaceName=$fileNamespace->getName();if(!isset($this->namespaces[$namespaceName])){$this->namespaces[$namespaceName]=new
TokenReflection\ReflectionNamespace($namespaceName,$file->getBroker());}$this->namespaces[$namespaceName]->addFileNamespace($fileNamespace);}$this->tokenStreams[$file->getName()]=$this->storingTokenStreams?$file->getTokenStream():true;$this->allClasses=null;$this->allFunctions=null;$this->allConstants=null;return$this;}public
function setBroker(Broker$broker){$this->broker=$broker;return$this;}public function
getBroker(){return$this->broker;}public function setStoringTokenStreams($store){$this->storingTokenStreams=(bool)$store;return$this;}public
function getStoringTokenStreams(){return$this->storingTokenStreams;}protected function
parseClassLists(){$allClasses=array(self::TOKENIZED_CLASSES=>array(),self::INTERNAL_CLASSES=>array(),self::NONEXISTENT_CLASSES=>array());foreach($this->namespaces
as$namespace){foreach($namespace->getClasses()as$class){$allClasses[self::TOKENIZED_CLASSES][$class->getName()]=$class;}}foreach($allClasses[self::TOKENIZED_CLASSES]as$className=>$class){foreach(array_merge($class->getParentClasses(),$class->getInterfaces())as$parent){if($parent->isInternal()){$allClasses[self::INTERNAL_CLASSES][$parent->getName()]=$parent;}elseif(!$parent->isTokenized()){$allClasses[self::NONEXISTENT_CLASSES][$parent->getName()]=$parent;}}}return$allClasses;}}}

 namespace TokenReflection\Exception{use TokenReflection;class Parse extends TokenReflection\Exception{const
FILE_DOES_NOT_EXIST=10;const FILE_NOT_READABLE=11;const DIR_DOES_NOT_EXIST=12;const
INVALID_PARENT=13;const PARSE_ELEMENT_ERROR=14;const PARSE_CHILDREN_ERROR=15;}}

 namespace TokenReflection\Exception{use TokenReflection;class Runtime extends TokenReflection\Exception{const
INVALID_ARGUMENT=20;const NOT_ACCESSBILE=21;const ALREADY_EXISTS=22;const SERIALIZATION_ERROR=23;}}

 namespace TokenReflection{interface IReflectionClass extends IReflection{public function
getShortName();public function getNamespaceName();public function inNamespace();public
function getExtension();public function getExtensionName();public function getFileName();public
function getStartLine();public function getEndLine();public function getDocComment();public
function getModifiers();public function isAbstract();public function isFinal();public
function isInterface();public function isException();public function isCloneable();public
function isIterateable();public function isSubclassOf($class);public function getParentClass();public
function getParentClassName();public function getParentClasses();public function
getParentClassNameList();public function implementsInterface($interface);public function
getInterfaces();public function getInterfaceNames();public function getOwnInterfaces();public
function getOwnInterfaceNames();public function getConstructor();public function
getDestructor();public function hasMethod($name);public function getMethod($name);public
function getMethods($filter=null);public function hasOwnMethod($name);public function
getOwnMethods($filter=null);public function hasConstant($name);public function getConstant($name);public
function getConstantReflection($name);public function getConstants();public function
getConstantReflections();public function hasOwnConstant($name);public function getOwnConstants();public
function getOwnConstantReflections();public function hasProperty($name);public function
getProperty($name);public function getProperties($filter=null);public function hasOwnProperty($name);public
function getOwnProperties($filter=null);public function getDefaultProperties();public
function getStaticProperties();public function getStaticPropertyValue($name,$default=null);public
function getDirectSubclasses();public function getDirectSubclassNames();public function
getIndirectSubclasses();public function getIndirectSubclassNames();public function
getDirectImplementers();public function getDirectImplementerNames();public function
getIndirectImplementers();public function getIndirectImplementerNames();public function
isInstance($object);public function newInstance($args);public function newInstanceArgs(array$args=array());public
function setStaticPropertyValue($name,$value);public function __toString();public
function isComplete();}}

 namespace TokenReflection{interface IReflectionConstant extends IReflection{public
function getShortName();public function getDeclaringClass();public function getDeclaringClassName();public
function getNamespaceName();public function inNamespace();public function getFileName();public
function getStartLine();public function getEndLine();public function getDocComment();public
function getValue();public function getValueDefinition();public function __toString();}}

 namespace TokenReflection{interface IReflectionExtension extends IReflection{public
function getClass($name);public function getClasses();public function getClassNames();public
function getConstantReflection($name);public function getConstantReflections();public
function getConstant($name);public function getConstants();public function getFunction($name);public
function getFunctions();public function getFunctionNames();public function __toString();}}

 namespace TokenReflection{interface IReflectionFunctionBase extends IReflection{public
function getNamespaceName();public function inNamespace();public function getExtension();public
function getExtensionName();public function getFileName();public function getStartLine();public
function getEndLine();public function getDocComment();public function isClosure();public
function isDeprecated();public function returnsReference();public function getParameter($parameter);public
function getParameters();public function getNumberOfParameters();public function
getNumberOfRequiredParameters();public function getStaticVariables();}}

 namespace TokenReflection{interface IReflectionNamespace extends IReflection{public
function hasClass($className);public function getClass($className);public function
getClasses();public function getClassNames();public function getClassShortNames();public
function hasConstant($constantName);public function getConstant($constantName);public
function getConstants();public function getConstantNames();public function getConstantShortNames();public
function hasFunction($functionName);public function getFunction($functionName);public
function getFunctions();public function getFunctionNames();public function getFunctionShortNames();public
function __toString();}}

 namespace TokenReflection{interface IReflectionParameter extends IReflection{public
function getDeclaringClass();public function getDeclaringClassName();public function
getDeclaringFunction();public function getDeclaringFunctionName();public function
getStartLine();public function getEndLine();public function getDocComment();public
function getDefaultValue();public function getDefaultValueDefinition();public function
isDefaultValueAvailable();public function getPosition();public function isArray();public
function getClass();public function getClassName();public function allowsNull();public
function isOptional();public function isPassedByReference();public function __toString();}}

 namespace TokenReflection{interface IReflectionProperty extends IReflection{public
function getDeclaringClass();public function getDeclaringClassName();public function
getStartLine();public function getEndLine();public function getDocComment();public
function getDefaultValue();public function getDefaultValueDefinition();public function
getValue($object);public function getModifiers();public function isPrivate();public
function isProtected();public function isPublic();public function isStatic();public
function isDefault();public function setAccessible($accessible);public function setValue($object,$value);public
function __toString();}}

 namespace TokenReflection\Php{use TokenReflection;use Reflector;interface IReflection
extends TokenReflection\IReflection{public function getNamespaceAliases();public
static function create(Reflector$internalReflection,TokenReflection\Broker$broker);}}

 namespace TokenReflection{use TokenReflection\Exception;abstract class ReflectionBase
implements IReflection{const DOCBLOCK_TEMPLATE_START='/**#@+';const DOCBLOCK_TEMPLATE_END='/**#@-*/';private
static$methodCache=array();protected$name;private$fileName;private$startLine;private$endLine;protected$docComment;private$parsedDocComment;private$broker;private$startPosition;private$endPosition;protected$docblockTemplates=array();public
final function __construct(Stream$tokenStream,Broker$broker,IReflection$parent){if(0===$tokenStream->count()){throw
new Exception\Runtime('Reflection token stream must not be empty.',Exception\Runtime::INVALID_ARGUMENT);}$this->broker=$broker;$this->fileName=$tokenStream->getFileName();try{$this->processParent($parent)->parseStartLine($tokenStream)->parseDocComment($tokenStream,$parent)->parse($tokenStream,$parent);}catch(Exception$e){$message='Could not parse %s.';if(null!==$this->name){$message=sprintf($message,get_class($this).' '.$this->getName());}else{$message=sprintf($message,get_class($this));}throw
new Exception\Parse($message,Exception\Parse::PARSE_ELEMENT_ERROR,$e);}try{$this->parseChildren($tokenStream,$parent);}catch(Exception$e){throw
new Exception\Parse(sprintf('Could not parse %s %s child elements.',get_class($this),$this->getName()),Exception\Parse::PARSE_CHILDREN_ERROR,$e);}$this->parseEndLine($tokenStream);}public
function getName(){return$this->name;}public function getFileName(){return$this->fileName;}public
function getStartLine(){return$this->startLine;}public function getEndLine(){return$this->endLine;}public
function getExtension(){return null;}public function getExtensionName(){return false;}public
function getDocComment(){return$this->docComment->getDocComment();}final public function
hasAnnotation($name){return$this->docComment->hasAnnotation($name);}final public
function getAnnotation($name){return$this->docComment->getAnnotation($name);}final
public function getAnnotations(){return$this->docComment->getAnnotations();}public
function isInternal(){return false;}public function isUserDefined(){return true;}public
function isTokenized(){return true;}public function isDeprecated(){return$this->hasAnnotation('deprecated');}public
function getSource(){return$this->broker->getFileTokens($this->getFileName())->getSourcePart($this->startPosition,$this->endPosition);}public
function getStartPosition(){return$this->startPosition;}public function getEndPosition(){return$this->endPosition;}public
function getBroker(){return$this->broker;}abstract public function getNamespaceAliases();final
public function __get($key){return self::get($this,$key);}final public function __isset($key){return
self::exists($this,$key);}final public static function get(IReflection$object,$key){if(!empty($key)){$className=get_class($object);if(!isset(self::$methodCache[$className])){self::$methodCache[$className]=array_flip(get_class_methods($className));}$methods=self::$methodCache[$className];$key2=ucfirst($key);if(isset($methods['get'.$key2])){return$object->{'get'.$key2}();}elseif(isset($methods['is'.$key2])){return$object->{'is'.$key2}();}}throw
new Exception\Runtime(sprintf('Cannot read %s "%s" property "%s".',get_class($object),$object->getName(),$key),Exception\Runtime::DOES_NOT_EXIST);}final
public static function exists(IReflection$object,$key){try{self::get($object,$key);return
true;}catch(RuntimeException$e){return false;}}protected function getDocblockTemplates(){return$this->docblockTemplates;}protected
function processParent(IReflection$parent){return$this;}protected function parseDocComment(Stream$tokenStream,IReflection$parent){if($this
instanceof ReflectionParameter){$this->docComment=new ReflectionAnnotation($this);return$this;}$position=$tokenStream->key();if($tokenStream->is(T_DOC_COMMENT,$position-1)){$value=$tokenStream->getTokenValue($position-1);if(self::DOCBLOCK_TEMPLATE_END!==$value){$this->docComment=new
ReflectionAnnotation($this,$value);$this->startPosition--;}}elseif($tokenStream->is(T_DOC_COMMENT,$position-2)){$value=$tokenStream->getTokenValue($position-2);if(self::DOCBLOCK_TEMPLATE_END!==$value){$this->docComment=new
ReflectionAnnotation($this,$value);$this->startPosition-=2;}}elseif($tokenStream->is(T_COMMENT,$position-1)&&preg_match('~^'.preg_quote(self::DOCBLOCK_TEMPLATE_START,'~').'~',$tokenStream->getTokenValue($position-1))){$this->docComment=new
ReflectionAnnotation($this,$tokenStream->getTokenValue($position-1));$this->startPosition--;}elseif($tokenStream->is(T_COMMENT,$position-2)&&preg_match('~^'.preg_quote(self::DOCBLOCK_TEMPLATE_START,'~').'~',$tokenStream->getTokenValue($position-2))){$this->docComment=new
ReflectionAnnotation($this,$tokenStream->getTokenValue($position-2));$this->startPosition-=2;}if(null===$this->docComment){$this->docComment=new
ReflectionAnnotation($this);}if($parent instanceof ReflectionBase){$this->docComment->setTemplates($parent->getDocblockTemplates());}return$this;}private
final function parseStartLine(Stream$tokenStream){$token=$tokenStream->current();$this->startLine=$token[2];$this->startPosition=$tokenStream->key();return$this;}private
final function parseEndLine(Stream$tokenStream){$token=$tokenStream->current();$this->endLine=$token[2];$this->endPosition=$tokenStream->key();return$this;}abstract
protected function parse(Stream$tokenStream,IReflection$parent);abstract protected
function parseName(Stream$tokenStream);protected function parseChildren(Stream$tokenStream,IReflection$parent){return$this;}}}

 namespace TokenReflection{class ReflectionFile implements IReflection{private$namespaces=array();private$tokenStream=null;private$broker;public
function __construct(Stream$tokenStream,Broker$broker){$this->tokenStream=$tokenStream;$this->broker=$broker;$this->parse();}public
function getName(){return$this->tokenStream->getFileName();}public function isInternal(){return
false;}public function isUserDefined(){return true;}public function isTokenized(){return
true;}public function getNamespaces(){return$this->namespaces;}public function __toString(){throw
new Exception\Runtime('__toString is not supported.',Exception\Runtime::UNSUPPORTED);}public
static function export(Broker$broker,$argument,$return=false){throw new Exception\Runtime('Export is not supported.',Exception\Runtime::UNSUPPORTED);}public
function getSource(){return (string)$this->tokenStream;}public function getTokenStream(){return$this->tokenStream;}public
function getBroker(){return$this->broker;}final public function __get($key){return
ReflectionBase::get($this,$key);}final public function __isset($key){return ReflectionBase::exists($this,$key);}private
function parse(){if($this->tokenStream->count()<=1){return$this;}try{if(!$this->tokenStream->is(T_OPEN_TAG)){$this->namespaces[]=new
ReflectionFileNamespace($this->tokenStream,$this->broker,$this);}else{$this->tokenStream->skipWhitespaces();while(null!==($type=$this->tokenStream->getType())){switch($type){case
T_WHITESPACE:case T_DOC_COMMENT:case T_COMMENT:break;case T_DECLARE:$this->tokenStream->skipWhitespaces()->findMatchingBracket()->skipWhitespaces()->skipWhitespaces();break;case
T_NAMESPACE:break 2;default:$this->namespaces[]=new ReflectionFileNamespace($this->tokenStream,$this->broker,$this);return$this;}$this->tokenStream->skipWhitespaces();}while(null!==($type=$this->tokenStream->getType())){if(T_NAMESPACE===$type){$this->namespaces[]=new
ReflectionFileNamespace($this->tokenStream,$this->broker,$this);}else{$this->tokenStream->skipWhitespaces();}}}return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse file contents.',Exception\Parse::PARSE_CHILDREN_ERROR,$e);}}}}

 namespace TokenReflection{use TokenReflection\Exception;use ReflectionProperty as
InternalReflectionProperty,ReflectionClass as InternalReflectionClass;class ReflectionProperty
extends ReflectionBase implements IReflectionProperty{const ACCESS_LEVEL_CHANGED=0x800;private$declaringClassName;private$modifiers=0;private$modifiersComplete=false;private$defaultValue;private$defaultValueDefinition=array();private$accessible=false;public
function getDeclaringClass(){return$this->getBroker()->getClass($this->declaringClassName);}public
function getDeclaringClassName(){return$this->declaringClassName;}public function
getDefaultValue(){if(is_array($this->defaultValueDefinition)){$this->defaultValue=Resolver::getValueDefinition($this->defaultValueDefinition,$this);$this->defaultValueDefinition=Resolver::getSourceCode($this->defaultValueDefinition);}return$this->defaultValue;}public
function getDefaultValueDefinition(){return is_array($this->defaultValueDefinition)?Resolver::getSourceCode($this->defaultValueDefinition):$this->defaultValueDefinition;}public
function getValue($object){try{$declaringClass=$this->getDeclaringClass();if(!$declaringClass->isInstance($object)){throw
new Exception\Runtime(sprintf('Invalid class, "%s" expected "%s" given.',$declaringClass->getName(),get_class($object)),Exception\Runtime::INVALID_ARGUMENT);}if($this->isPublic()){return$object->{$this->name};}elseif($this->isAccessible()){$refClass=new
InternalReflectionClass($object);$refProperty=$refClass->getProperty($this->name);$refProperty->setAccessible(true);$value=$refProperty->getValue($object);$refProperty->setAccessible(false);return$value;}throw
new Exception\Runtime('Only public and accessible properties can return their values.',Exception\Runtime::NOT_ACCESSBILE);}catch(Exception\Runtime$e){throw
new Exception\Runtime(sprintf('Could not get value of property "%s::$%s".',$this->declaringClassName,$this->name),0,$e);}}public
function isDefault(){return true;}public function getModifiers(){if(false===$this->modifiersComplete){$declaringClass=$this->getDeclaringClass();$declaringClassParent=$declaringClass->getParentClass();if($declaringClassParent&&$declaringClassParent->hasProperty($this->name)){$property=$declaringClassParent->getProperty($this->name);if(($this->isPublic()&&!$property->isPublic())||($this->isProtected()&&$property->isPrivate())){$this->modifiers|=self::ACCESS_LEVEL_CHANGED;}}$this->modifiersComplete=($this->modifiers&self::ACCESS_LEVEL_CHANGED)||$declaringClass->isComplete();}return$this->modifiers;}public
function isPrivate(){return (bool)($this->modifiers&InternalReflectionProperty::IS_PRIVATE);}public
function isProtected(){return (bool)($this->modifiers&InternalReflectionProperty::IS_PROTECTED);}public
function isPublic(){return (bool)($this->modifiers&InternalReflectionProperty::IS_PUBLIC);}public
function isStatic(){return (bool)($this->modifiers&InternalReflectionProperty::IS_STATIC);}public
function __toString(){return sprintf("Property [ %s%s%s%s%s\$%s ]\n",$this->isStatic()?'':'<default> ',$this->isPublic()?'public ':'',$this->isPrivate()?'private ':'',$this->isProtected()?'protected ':'',$this->isStatic()?'static ':'',$this->getName());}public
static function export(Broker$broker,$class,$property,$return=false){$className=is_object($class)?get_class($class):$class;$propertyName=$property;$class=$broker->getClass($className);if($class
instanceof Dummy\ReflectionClass){throw new Exception\Runtime(sprintf('Class %s does not exist.',$className),Exception\Runtime::DOES_NOT_EXIST);}$property=$class->getProperty($propertyName);if($return){return$property->__toString();}echo$property->__toString();}public
function isAccessible(){return$this->accessible;}public function setAccessible($accessible){$this->accessible=(bool)$accessible;}public
function setDefaultValue($value){$this->defaultValue=$value;$this->defaultValueDefinition=var_export($value,true);}public
function setValue($object,$value){try{$declaringClass=$this->getDeclaringClass();if(!$declaringClass->isInstance($object)){throw
new Exception\Runtime(sprintf('Invalid class, "%s" expected "%s" given.',$declaringClass->getName(),get_class($object)),Exception\Runtime::INVALID_ARGUMENT);}if($this->isPublic()){$object->{$this->name}=$value;}elseif($this->isAccessible()){$refClass=new
InternalReflectionClass($object);$refProperty=$refClass->getProperty($this->name);$refProperty->setAccessible(true);$refProperty->setValue($object,$value);$refProperty->setAccessible(false);if($this->isStatic()){$this->setDefaultValue($value);}}else{throw
new Exception\Runtime('Only public and accessible properties can be set.',Exception\Runtime::NOT_ACCESSBILE);}}catch(Exception\Runtime$e){throw
new Exception\Runtime(sprintf('Could not set value of property "%s::$%s".',$this->declaringClassName,$this->name),0,$e);}}public
function getNamespaceAliases(){return$this->getDeclaringClass()->getNamespaceAliases();}protected
function processParent(IReflection$parent){if(!$parent instanceof ReflectionClass){throw
new Exception\Parse(sprintf('The parent object has to be an instance of TokenReflection\ReflectionClass, "%s" given.',get_class($parent)),Exception\Parse::INVALID_PARENT);}$this->declaringClassName=$parent->getName();return
parent::processParent($parent);}protected function parse(Stream$tokenStream,IReflection$parent){return$this->parseModifiers($tokenStream,$parent)->parseName($tokenStream)->parseDefaultValue($tokenStream);}private
function parseModifiers(Stream$tokenStream,ReflectionClass$class){while(true){switch($tokenStream->getType()){case
T_PUBLIC:case T_VAR:$this->modifiers|=InternalReflectionProperty::IS_PUBLIC;break;case
T_PROTECTED:$this->modifiers|=InternalReflectionProperty::IS_PROTECTED;break;case
T_PRIVATE:$this->modifiers|=InternalReflectionProperty::IS_PRIVATE;break;case T_STATIC:$this->modifiers|=InternalReflectionProperty::IS_STATIC;break;default:break
2;}$tokenStream->skipWhitespaces();}if(InternalReflectionProperty::IS_STATIC===$this->modifiers){$this->modifiers|=InternalReflectionProperty::IS_PUBLIC;}elseif(0===$this->modifiers){try{$parentProperties=$class->getOwnProperties();if(empty($parentProperties)){throw
new Exception\Parse('No access level defined and no previous defining class property present.',Exception\Parse::PARSE_ELEMENT_ERROR);}$sibling=array_pop($parentProperties);if($sibling->isPublic()){$this->modifiers=InternalReflectionProperty::IS_PUBLIC;}elseif($sibling->isPrivate()){$this->modifiers=InternalReflectionProperty::IS_PRIVATE;}elseif($sibling->isProtected()){$this->modifiers=InternalReflectionProperty::IS_PROTECTED;}else{throw
new Exception\Parse(sprintf('Property sibling "%s" has no access level defined.',$sibling->getName()),Exception\Parse::PARSE_ELEMENT_ERROR);}if($sibling->isStatic()){$this->modifiers|=InternalReflectionProperty::IS_STATIC;}}catch(Exception$e){throw
new Exception\Parse('Could not parse modifiers.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}return$this;}protected
function parseName(Stream$tokenStream){try{if(!$tokenStream->is(T_VARIABLE)){throw
new Exception\Parse('The property name could not be determined.',Exception\Parse::PARSE_ELEMENT_ERROR);}$this->name=substr($tokenStream->getTokenValue(),1);$tokenStream->skipWhitespaces();return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse property name.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}private
function parseDefaultValue(Stream$tokenStream){$type=$tokenStream->getType();if(';'===$type||','===$type){return$this;}if('='===$type){$tokenStream->skipWhitespaces();}try{$level=0;while(null!==($type=$tokenStream->getType())){switch($type){case
',':if(0!==$level){break;}case ';':break 2;case ')':case ']':case '}':$level--;break;case
'(':case '{':case '[':$level++;break;default:break;}$this->defaultValueDefinition[]=$tokenStream->current();$tokenStream->next();}if(','!==$type&&';'!==$type){throw
new Exception\Parse(sprintf('The property default value is not terminated properly. Expected "," or ";", "%s" found.',$tokenStream->getTokenName()),Exception\Parse::PARSE_ELEMENT_ERROR);}return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse property default value.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}}}

 namespace TokenReflection\Dummy{use TokenReflection;use TokenReflection\Broker,TokenReflection\IReflectionClass,TokenReflection\ReflectionBase;use
ReflectionClass as InternalReflectionClass,TokenReflection\Exception;class ReflectionClass
implements IReflectionClass{private$broker;private$name;public function __construct($className,Broker$broker){$this->name=$className;$this->broker=$broker;}public
function getName(){return$this->name;}public function getShortName(){$pos=strrpos($this->name,'\\');return
false===$pos?$this->name:substr($this->name,$pos+1);}public function getNamespaceName(){return
'';}public function inNamespace(){return false;}public function getExtension(){return
null;}public function getExtensionName(){return false;}public function getFileName(){return
null;}public function getStartLine(){return null;}public function getEndLine(){return
null;}public function getDocComment(){return false;}public function hasAnnotation($name){return
false;}public function getAnnotation($name){return null;}public function getAnnotations(){return
array();}public function getModifiers(){return 0;}public function isAbstract(){return
false;}public function isFinal(){return false;}public function isInterface(){return
false;}public function isException(){return false;}public function isInstantiable(){return
false;}public function isCloneable(){return false;}public function isIterateable(){return
false;}public function isInternal(){return false;}public function isUserDefined(){return
false;}public function isTokenized(){return false;}public function isSubclassOf($class){return
false;}public function getParentClass(){return false;}public function getParentClasses(){return
array();}public function getParentClassNameList(){return array();}public function
getParentClassName(){return null;}public function implementsInterface($interface){if(is_object($interface)){if(!$interface
instanceof IReflectionClass){throw new Exception\Runtime(sprintf('Parameter must be a string or an instance of class reflection, "%s" provided.',get_class($interface)),Exception\Runtime::INVALID_ARGUMENT);}$interfaceName=$interface->getName();if(!$interface->isInterface()){throw
new Exception\Runtime(sprintf('"%s" is not an interface.',$interfaceName),Exception\Runtime::INVALID_ARGUMENT);}}return
false;}public function getInterfaces(){return array();}public function getInterfaceNames(){return
array();}public function getOwnInterfaces(){return array();}public function getOwnInterfaceNames(){return
array();}public function getConstructor(){return null;}public function getDestructor(){return
null;}public function hasMethod($name){return false;}public function getMethod($name){throw
new Exception\Runtime(sprintf('There is no method "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function getMethods($filter=null){return array();}public function hasOwnMethod($name){return
false;}public function getOwnMethods($filter=null){return array();}public function
hasConstant($name){return false;}public function getConstant($name){throw new Exception\Runtime(sprintf('There is no constant "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function getConstantReflection($name){throw new Exception\Runtime(sprintf('There is no constant "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function getConstants(){return array();}public function getConstantReflections(){return
array();}public function hasOwnConstant($name){return false;}public function getOwnConstants(){return
array();}public function getOwnConstantReflections(){return array();}public function
getDefaultProperties(){return array();}public function hasProperty($name){return
false;}public function getProperties($filter=null){return array();}public function
getProperty($name){throw new Exception\Runtime(sprintf('There is no property "%s" in class "%s".',$name,$this->name),Exception::DOES_NOT_EXIST);}public
function hasOwnProperty($name){return false;}public function getOwnProperties($filter=null){return
array();}public function getStaticProperties(){return array();}public function getStaticPropertyValue($name,$default=null){throw
new Exception(sprintf('There is no static property "%s" in class "%s".',$name,$this->name),Exception::DOES_NOT_EXIST);}public
function getDirectSubclasses(){$that=$this->name;return array_filter($this->getBroker()->getClasses(Broker\Backend::INTERNAL_CLASSES|Broker\Backend::TOKENIZED_CLASSES),function(IReflectionClass$class)use($that){if(!$class->isSubclassOf($that)){return
false;}return null===$class->getParentClassName()||!$class->getParentClass()->isSubClassOf($that);});}public
function getDirectSubclassNames(){return array_keys($this->getDirectSubclasses());}public
function getIndirectSubclasses(){$that=$this->name;return array_filter($this->getBroker()->getClasses(Broker\Backend::INTERNAL_CLASSES|Broker\Backend::TOKENIZED_CLASSES),function(IReflectionClass$class)use($that){if(!$class->isSubclassOf($that)){return
false;}return null!==$class->getParentClassName()&&$class->getParentClass()->isSubClassOf($that);});}public
function getIndirectSubclassNames(){return array_keys($this->getIndirectSubclasses());}public
function getDirectImplementers(){if(!$this->isInterface()){return array();}$that=$this->name;return
array_filter($this->getBroker()->getClasses(Broker\Backend::INTERNAL_CLASSES|Broker\Backend::TOKENIZED_CLASSES),function(IReflectionClass$class)use($that){if(!$class->implementsInterface($that)){return
false;}return null===$class->getParentClassName()||!$class->getParentClass()->implementsInterface($that);});}public
function getDirectImplementerNames(){return array_keys($this->getDirectImplementers());}public
function getIndirectImplementers(){if(!$this->isInterface()){return array();}$that=$this->name;return
array_filter($this->getBroker()->getClasses(Broker\Backend::INTERNAL_CLASSES|Broker\Backend::TOKENIZED_CLASSES),function(IReflectionClass$class)use($that){if(!$class->implementsInterface($that)){return
false;}return null!==$class->getParentClassName()&&$class->getParentClass()->implementsInterface($this);});}public
function getIndirectImplementerNames(){return array_keys($this->getIndirectImplementers());}public
function isInstance($object){if(!is_object($object)){throw new Exception\Runtime(sprintf('Parameter must be a class instance, "%s" provided.',gettype($object)),Exception\Runtime::INVALID_ARGUMENT);}return$this->name===get_class($object)||is_subclass_of($object,$this->name);}public
function newInstance($args){return$this->newInstanceArgs(func_get_args());}public
function newInstanceArgs(array$args=array()){if(!class_exists($this->name,true)){throw
new Exception\Runtime(sprintf('Could not create an instance of class "%s"; class does not exist.',$this->name),Exception\Runtime::DOES_NOT_EXIST);}$reflection=new
InternalReflectionClass($this->name);return$reflection->newInstanceArgs($args);}public
function setStaticPropertyValue($name,$value){throw new Exception\Runtime(sprintf('There is no static property "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function __toString(){return sprintf("Class|Interface [ <user> class|interface %s ] {\n  %s%s%s%s%s\n}\n",$this->getName(),"\n\n  - Constants [0] {\n  }","\n\n  - Static properties [0] {\n  }","\n\n  - Static methods [0] {\n  }","\n\n  - Properties [0] {\n  }","\n\n  - Methods [0] {\n  }");}public
function getSource(){return '';}public function isComplete(){return false;}public
function getBroker(){return$this->broker;}final public function __get($key){return
ReflectionBase::get($this,$key);}final public function __isset($key){return ReflectionBase::exists($this,$key);}}}

 namespace TokenReflection{interface IReflectionFunction extends IReflectionFunctionBase{public
function isDisabled();public function invokeArgs(array$args);}}

 namespace TokenReflection{interface IReflectionMethod extends IReflectionFunctionBase{public
function getDeclaringClass();public function getDeclaringClassName();public function
getModifiers();public function isAbstract();public function isFinal();public function
isPrivate();public function isProtected();public function isPublic();public function
isStatic();public function is($filter=null);public function isConstructor();public
function isDestructor();public function getPrototype();public function invoke($object,$args);public
function invokeArgs($object,array$args);public function setAccessible($accessible);}}

 namespace TokenReflection\Php{use TokenReflection;use TokenReflection\Broker,TokenReflection\Exception;use
Reflector,ReflectionClass as InternalReflectionClass,ReflectionProperty as InternalReflectionProperty,ReflectionMethod
as InternalReflectionMethod;class ReflectionClass extends InternalReflectionClass
implements IReflection,TokenReflection\IReflectionClass{private$broker;private$interfaces;private$methods;private$constants;private$properties;public
function __construct($className,Broker$broker){parent::__construct($className);$this->broker=$broker;}public
function getExtension(){return ReflectionExtension::create(parent::getExtension(),$this->broker);}public
function hasAnnotation($name){return false;}public function getAnnotation($name){return
null;}public function getAnnotations(){return array();}public function isException(){return
'Exception'===$this->getName()||$this->isSubclassOf('Exception');}public function
isCloneable(){if($this->isInterface()||$this->isAbstract()){return false;}$methods=$this->getMethods();return
isset($methods['__clone'])?$methods['__clone']->isPublic():true;}public function
isTokenized(){return false;}public function isDeprecated(){return false;}public function
isSubclassOf($class){return in_array($class,$this->getParentClassNameList());}public
function getParentClass(){$parent=parent::getParentClass();return$parent?self::create($parent,$this->broker):null;}public
function getParentClassName(){$parent=$this->getParentClass();return$parent?$parent->getName():null;}public
function getParentClasses(){$broker=$this->broker;return array_map(function($className)use($broker){return$broker->getClass($className);},$this->getParentClassNameList());}public
function getParentClassNameList(){return class_parents($this->getName());}public
function implementsInterface($interface){if(is_object($interface)){if($interface
instanceof InternalReflectionClass||$interface instanceof IReflectionClass){$interfaceName=$interface->getName();}else{throw
new Exception\Runtime(sprintf('Parameter must be a string or an instance of class reflection, "%s" provided.',get_class($interface)),Exception\Runtime::INVALID_ARGUMENT);}}else{$interfaceName=$interface;}$interfaces=$this->getInterfaces();return
isset($interfaces[$interfaceName]);}public function getInterfaces(){if(null===$this->interfaces){$broker=$this->broker;$interfaceNames=$this->getInterfaceNames();if(empty($interfaceNames)){$this->interfaces=array();}else{$this->interfaces=array_combine($interfaceNames,array_map(function($interfaceName)use($broker){return$broker->getClass($interfaceName);},$interfaceNames));}}return$this->interfaces;}public
function getOwnInterfaces(){$parent=$this->getParentClass();return$parent?array_diff_key($this->getInterfaces(),$parent->getInterfaces()):$this->getInterfaces();}public
function getOwnInterfaceNames(){return array_keys($this->getOwnInterfaces());}public
function getConstructor(){return ReflectionMethod::create(parent::getConstructor(),$this->broker);}public
function getDestructor(){foreach($this->getMethods()as$method){if($method->isDestructor()){return$method;}}return
null;}public function getMethod($name){foreach($this->getMethods()as$method){if($method->getName()===$name){return$method;}}throw
new Exception\Runtime(sprintf('There is no method %s in class %s',$name,$this->name),Exception::DOES_NOT_EXIST);}public
function getMethods($filter=null){if(null===$this->methods){$broker=$this->broker;$this->methods=array_map(function(InternalReflectionMethod$method)use($broker){return
ReflectionMethod::create($method,$broker);},parent::getMethods());}if(null===$filter){return$this->methods;}return
array_filter($this->methods,function(ReflectionMethod$method)use($filter){return
(bool)($method->getModifiers()&$filter);});}public function hasOwnMethod($name){foreach($this->getOwnMethods()as$method){if($name===$method->getName()){return
true;}}return false;}public function getOwnMethods($filter=null){$me=$this->getName();return
array_filter($this->getMethods($filter),function(ReflectionMethod$method)use($me){return$method->getDeclaringClass()->getName()===$me;});}public
function getConstantReflection($name){if($this->hasConstant($name)){return new ReflectionConstant($name,$this->getConstant($name),$this->broker,$this);}throw
new Exception\Runtime(sprintf('Constant "%s" is not defined in class "%s"',$name,$this->getName()),Exception::DOES_NOT_EXIST);}public
function getConstantReflections(){if(null===$this->constants){$this->constants=array();foreach($this->getConstants()as$name=>$value){$this->constants[$name]=$this->getConstantReflection($name);}}return
array_values($this->constants);}public function hasOwnConstant($name){$constants=$this->getOwnConstants();return
isset($constants[$name]);}public function getOwnConstants(){return array_diff_assoc($this->getConstants(),$this->getParentClass()?$this->getParentClass()->getConstants():array());}public
function getOwnConstantReflections(){$constants=array();foreach($this->getOwnConstants()as$name=>$value){$constants[]=$this->getConstantReflection($name);}return$constants;}public
function getProperty($name){foreach($this->getProperties()as$property){if($name===$property->getName()){return$property;}}throw
new Exception\Runtime(sprintf('There is no property %s in class %s',$name,$this->getName()),Exception::DOES_NOT_EXIST);}public
function getProperties($filter=null){if(null===$this->properties){$broker=$this->broker;$this->properties=array_map(function(InternalReflectionProperty$property)use($broker){return
ReflectionProperty::create($property,$broker);},parent::getProperties());}if(null===$filter){return$this->properties;}return
array_filter($this->properties,function(ReflectionProperty$property)use($filter){return
(bool)($property->getModifiers()&$filter);});}public function hasOwnProperty($name){foreach($this->getOwnProperties()as$property){if($name===$property->getName()){return
true;}}return false;}public function getOwnProperties($filter=null){$me=$this->getName();return
array_filter($this->getProperties($filter),function(ReflectionProperty$property)use($me){return$property->getDeclaringClass()->getName()===$me;});}public
function getStaticProperties(){return$this->getProperties(InternalReflectionProperty::IS_STATIC);}public
function getDirectSubclasses(){$that=$this->name;return array_filter($this->getBroker()->getClasses(Broker\Backend::INTERNAL_CLASSES|Broker\Backend::TOKENIZED_CLASSES),function(IReflectionClass$class)use($that){if(!$class->isSubclassOf($that)){return
false;}return null===$class->getParentClassName()||!$class->getParentClass()->isSubClassOf($that);});}public
function getDirectSubclassNames(){return array_keys($this->getDirectSubclasses());}public
function getIndirectSubclasses(){$that=$this->name;return array_filter($this->getBroker()->getClasses(Broker\Backend::INTERNAL_CLASSES|Broker\Backend::TOKENIZED_CLASSES),function(IReflectionClass$class)use($that){if(!$class->isSubclassOf($that)){return
false;}return null!==$class->getParentClassName()&&$class->getParentClass()->isSubClassOf($that);});}public
function getIndirectSubclassNames(){return array_keys($this->getIndirectSubclasses());}public
function getDirectImplementers(){if(!$this->isInterface()){return array();}$that=$this->name;return
array_filter($this->getBroker()->getClasses(Broker\Backend::INTERNAL_CLASSES|Broker\Backend::TOKENIZED_CLASSES),function(IReflectionClass$class)use($that){if(!$class->implementsInterface($that)){return
false;}return null===$class->getParentClassName()||!$class->getParentClass()->implementsInterface($that);});}public
function getDirectImplementerNames(){return array_keys($this->getDirectImplementers());}public
function getIndirectImplementers(){if(!$this->isInterface()){return array();}$that=$this->name;return
array_filter($this->getBroker()->getClasses(Broker\Backend::INTERNAL_CLASSES|Broker\Backend::TOKENIZED_CLASSES),function(IReflectionClass$class)use($that){if(!$class->implementsInterface($that)){return
false;}return null!==$class->getParentClassName()&&$class->getParentClass()->implementsInterface($that);});}public
function getIndirectImplementerNames(){return array_keys($this->getIndirectImplementers());}public
function isComplete(){return true;}public function getNamespaceAliases(){return array();}public
function getBroker(){return$this->broker;}final public function __get($key){return
TokenReflection\ReflectionBase::get($this,$key);}final public function __isset($key){return
TokenReflection\ReflectionBase::exists($this,$key);}public static function create(Reflector$internalReflection,Broker$broker){if(!$internalReflection
instanceof InternalReflectionClass){throw new Exception\Runtime(sprintf('Invalid reflection instance provided (%s), ReflectionClass expected.',get_class($internalReflection)));}return$broker->getClass($internalReflection->getName());}}}

 namespace TokenReflection\Php{use TokenReflection;use TokenReflection\Broker,TokenReflection\Exception,Reflector;class
ReflectionConstant implements IReflection,TokenReflection\IReflectionConstant{private$name;private$declaringClassName;private$namespaceName;private$value;private$userDefined;private$broker;public
function __construct($name,$value,Broker$broker,ReflectionClass$parent=null){$this->name=$name;$this->value=$value;$this->broker=$broker;if(null!==$parent){$this->declaringClassName=$parent->getName();$this->userDefined=$parent->isUserDefined();}else{$declared=get_defined_constants(false);if(!isset($declared[$name])){$this->userDefined=true;}else{$declared=get_defined_constants(true);$this->userDefined=isset($declared['user'][$name]);}}}public
function getName(){return$this->name;}public function getShortName(){$name=$this->getName();if(null!==$this->namespaceName&&$this->namespaceName!==ReflectionNamespace::NO_NAMESPACE_NAME){$name=substr($name,strlen($this->namespaceName)+1);}return$name;}public
function getDeclaringClass(){if(null===$this->declaringClassName){return null;}return$this->getBroker()->getClass($this->declaringClassName);}public
function getDeclaringClassName(){return$this->declaringClassName;}public function
getNamespaceName(){return$this->namespaceName===TokenReflection\ReflectionNamespace::NO_NAMESPACE_NAME?'':$this->namespaceName;}public
function inNamespace(){return ''!==$this->getNamespaceName();}public function getFileName(){return
null;}public function getStartLine(){return null;}public function getEndLine(){return
null;}public function getDocComment(){return false;}public function hasAnnotation($name){return
false;}public function getAnnotation($name){return null;}public function getAnnotations(){return
array();}public function getValue(){return$this->value;}public function getValueDefinition(){return
var_export($this->value,true);}public function isInternal(){return!$this->userDefined;}public
function isUserDefined(){return$this->userDefined;}public function isTokenized(){return
false;}public function isDeprecated(){return false;}public function __toString(){return
sprintf("Constant [ %s %s ] { %s }\n",gettype($this->getValue()),$this->getName(),$this->getValue());}public
static function export(Broker$broker,$class,$constant,$return=false){$className=is_object($class)?get_class($class):$class;$constantName=$constant;if(null===$className){$constant=$broker->getConstant($constantName);if(null===$constant){throw
new Exception\Runtime(sprintf('Constant %s does not exist.',$constantName),Exception\Runtime::DOES_NOT_EXIST);}}else{$class=$broker->getClass($className);if($class
instanceof Dummy\ReflectionClass){throw new Exception\Runtime(sprintf('Class %s does not exist.',$className),Exception\Runtime::DOES_NOT_EXIST);}$constant=$class->getConstantReflection($constantName);}if($return){return$constant->__toString();}echo$constant->__toString();}public
function getBroker(){return$this->broker;}public function getNamespaceAliases(){return
array();}final public function __get($key){return TokenReflection\ReflectionBase::get($this,$key);}final
public function __isset($key){return TokenReflection\ReflectionBase::exists($this,$key);}public
static function create(Reflector$internalReflection,Broker$broker){return null;}}}

 namespace TokenReflection\Php{use TokenReflection;use TokenReflection\Broker,TokenReflection\Exception;use
Reflector,ReflectionExtension as InternalReflectionExtension;class ReflectionExtension
extends InternalReflectionExtension implements IReflection,TokenReflection\IReflectionExtension{private$classes;private$constants;private$functions;private$broker;public
function __construct($name,Broker$broker){parent::__construct($name);$this->broker=$broker;}public
function isInternal(){return true;}public function isUserDefined(){return false;}public
function isTokenized(){return false;}public function isDeprecated(){return false;}public
function getClass($name){$classes=$this->getClasses();return isset($classes[$name])?$classes[$name]:null;}public
function getClasses(){if(null===$this->classes){$broker=$this->broker;$this->classes=array_map(function($className)use($broker){return$broker->getClass($className);},$this->getClassNames());}return$this->classes;}public
function getConstant($name){$constants=$this->getConstants();return isset($constants[$name])?$constants[$name]:false;}public
function getConstantReflection($name){$constants=$this->getConstantReflections();return
isset($constants[$name])?$constants[$name]:null;}public function getConstantReflections(){if(null===$this->constants){$broker=$this->broker;$this->constants=array_map(function($constantName)use($broker){return$broker->getConstant($constantName);},array_keys($this->getConstants()));}return$this->constants;}public
function getFunction($name){$functions=$this->getFunctions();return isset($functions[$name])?$functions[$name]:null;}public
function getFunctions(){if(null===$this->functions){$broker=$this->broker;$this->classes=array_map(function($functionName)use($broker){return$broker->getFunction($functionName);},array_keys(parent::getFunctions()));}return$this->functions;}public
function getFunctionNames(){return array_keys($this->getFunctions());}public function
getBroker(){return$this->broker;}public function getNamespaceAliases(){return array();}final
public function __get($key){return TokenReflection\ReflectionBase::get($this,$key);}final
public function __isset($key){return TokenReflection\ReflectionBase::exists($this,$key);}public
static function create(Reflector$internalReflection,Broker$broker){static$cache=array();if(!$internalReflection
instanceof InternalReflectionExtension){throw new Exception\Runtime(sprintf('Invalid reflection instance provided: "%s", ReflectionExtension expected.',get_class($internalReflection)),Exception\Runtime::INVALID_ARGUMENT);}if(!isset($cache[$internalReflection->getName()])){$cache[$internalReflection->getName()]=new
self($internalReflection->getName(),$broker);}return$cache[$internalReflection->getName()];}}}

 namespace TokenReflection\Php{use TokenReflection;use TokenReflection\Broker,TokenReflection\Exception;use
Reflector,ReflectionParameter as InternalReflectionParameter,ReflectionFunctionAbstract
as InternalReflectionFunctionAbstract;class ReflectionParameter extends InternalReflectionParameter
implements IReflection,TokenReflection\IReflectionParameter{private$userDefined;private$broker;public
function __construct($function,$paramName,Broker$broker,InternalReflectionFunctionAbstract$parent){parent::__construct($function,$paramName);$this->broker=$broker;$this->userDefined=$parent->isUserDefined();}public
function getDeclaringClass(){$class=parent::getDeclaringClass();return$class?ReflectionClass::create($class,$this->broker):null;}public
function getDeclaringClassName(){$class=parent::getDeclaringClass();return$class?$class->getName():null;}public
function getDeclaringFunction(){$class=$this->getDeclaringClass();$function=parent::getDeclaringFunction();return$class?$class->getMethod($function->getName()):ReflectionFunction::create($function,$this->broker);}public
function getDeclaringFunctionName(){$function=parent::getDeclaringFunction();return$function?$function->getName():$function;}public
function getStartLine(){return null;}public function getEndLine(){return null;}public
function getDocComment(){return false;}public function getDefaultValueDefinition(){$value=$this->getDefaultValue();return
null===$value?null:var_export($value,true);}public function getClassName(){return$this->getClass()?$this->getClass()->getName():null;}public
function isInternal(){return!$this->userDefined;}public function isUserDefined(){return$this->userDefined;}public
function isTokenized(){return false;}public function isDeprecated(){return false;}public
function getBroker(){return$this->broker;}public function getNamespaceAliases(){return
array();}final public function __get($key){return TokenReflection\ReflectionBase::get($this,$key);}final
public function __isset($key){return TokenReflection\ReflectionBase::exists($this,$key);}public
static function create(Reflector$internalReflection,Broker$broker){static$cache=array();if(!$internalReflection
instanceof InternalReflectionParameter){throw new Exception\Runtime(sprintf('Invalid reflection instance provided: "%s", ReflectionParameter expected.',get_class($internalReflection)),Exception\Runtime::INVALID_ARGUMENT);}$class=$internalReflection->getDeclaringClass();$function=$internalReflection->getDeclaringFunction();$key=$class?$class->getName().'::':'';$key.=$function->getName().'('.$internalReflection->getName().')';if(!isset($cache[$key])){$cache[$key]=new
self($class?array($class->getName(),$function->getName()):$function->getName(),$internalReflection->getName(),$broker,$function);}return$cache[$key];}}}

 namespace TokenReflection\Php{use TokenReflection;use TokenReflection\Broker,TokenReflection\Exception;use
Reflector,ReflectionProperty as InternalReflectionProperty;class ReflectionProperty
extends InternalReflectionProperty implements IReflection,TokenReflection\IReflectionProperty{private$broker;public
function __construct($class,$propertyName,Broker$broker){parent::__construct($class,$propertyName);$this->broker=$broker;}public
function getDeclaringClass(){return ReflectionClass::create(parent::getDeclaringClass(),$this->broker);}public
function getDeclaringClassName(){return$this->getDeclaringClass()->getName();}public
function getStartLine(){return null;}public function getEndLine(){return null;}public
function getDocComment(){return false;}public function hasAnnotation($name){return
false;}public function getAnnotation($name){return null;}public function getAnnotations(){return
array();}public function getDefaultValue(){$values=$this->getDeclaringClass()->getDefaultProperties();return$values[$this->getName()];}public
function getDefaultValueDefinition(){$value=$this->getDefaultValue();return null===$value?null:var_export($value,true);}public
function isInternal(){return$this->getDeclaringClass()->isInternal();}public function
isUserDefined(){return$this->getDeclaringClass()->isUserDefined();}public function
isTokenized(){return false;}public function isDeprecated(){return false;}public function
getBroker(){return$this->broker;}public function getNamespaceAliases(){return array();}final
public function __get($key){return TokenReflection\ReflectionBase::get($this,$key);}final
public function __isset($key){return TokenReflection\ReflectionBase::exists($this,$key);}public
static function create(Reflector$internalReflection,Broker$broker){static$cache=array();if(!$internalReflection
instanceof InternalReflectionProperty){throw new Exception\Runtime(sprintf('Invalid reflection instance provided: "%s", ReflectionProperty expected.',get_class($internalReflection)),Exception\Runtime::INVALID_ARGUMENT);}$key=$internalReflection->getDeclaringClass()->getName().'::'.$internalReflection->getName();if(!isset($cache[$key])){$cache[$key]=new
self($internalReflection->getDeclaringClass()->getName(),$internalReflection->getName(),$broker);}return$cache[$key];}}}

 namespace TokenReflection{use TokenReflection\Exception;use ReflectionClass as InternalReflectionClass,ReflectionProperty
as InternalReflectionProperty;class ReflectionClass extends ReflectionBase implements
IReflectionClass{const IS_INTERFACE=0x80;const IMPLEMENTS_INTERFACES=0x80000;private$namespaceName;private$modifiers=0;private$modifiersComplete=false;private$parentClassName;private$interfaces=array();private$methods=array();private$constants=array();private$properties=array();private$definitionComplete=false;private$aliases=array();public
function getShortName(){$name=$this->getName();if($this->namespaceName!==ReflectionNamespace::NO_NAMESPACE_NAME){$name=substr($name,strlen($this->namespaceName)+1);}return$name;}public
function getNamespaceName(){return$this->namespaceName===ReflectionNamespace::NO_NAMESPACE_NAME?'':$this->namespaceName;}public
function inNamespace(){return null!==$this->namespaceName&&ReflectionNamespace::NO_NAMESPACE_NAME!==$this->namespaceName;}public
function getModifiers(){if(false===$this->modifiersComplete){if(($this->modifiers&InternalReflectionClass::IS_EXPLICIT_ABSTRACT)&&!($this->modifiers&InternalReflectionClass::IS_IMPLICIT_ABSTRACT)){foreach($this->getMethods()as$reflectionMethod){if($reflectionMethod->isAbstract()){$this->modifiers|=InternalReflectionClass::IS_IMPLICIT_ABSTRACT;}}if(!empty($this->interfaces)){$this->modifiers|=InternalReflectionClass::IS_IMPLICIT_ABSTRACT;}}if(!empty($this->interfaces)){$this->modifiers|=self::IMPLEMENTS_INTERFACES;}if($this->isInterface()&&!empty($this->methods)){$this->modifiers|=InternalReflectionClass::IS_IMPLICIT_ABSTRACT;}$this->modifiersComplete=true;foreach($this->getParentClasses()as$parentClass){if($parentClass
instanceof Dummy\ReflectionClass){$this->modifiersComplete=false;break;}}if($this->modifiersComplete){foreach($this->getInterfaces()as$interface){if($interface
instanceof Dummy\ReflectionClass){$this->modifiersComplete=false;break;}}}}return$this->modifiers;}public
function isAbstract(){if($this->modifiers&InternalReflectionClass::IS_EXPLICIT_ABSTRACT){return
true;}elseif($this->isInterface()&&!empty($this->methods)){return true;}return false;}public
function isFinal(){return (bool)($this->modifiers&InternalReflectionClass::IS_FINAL);}public
function isInterface(){return (bool)($this->modifiers&self::IS_INTERFACE);}public
function isException(){return 'Exception'===$this->name||$this->isSubclassOf('Exception');}public
function isInstantiable(){if($this->isInterface()||$this->isAbstract()){return false;}if(null===($constructor=$this->getConstructor())){return
true;}return$constructor->isPublic();}public function isCloneable(){if($this->isInterface()||$this->isAbstract()){return
false;}if($this->hasMethod('__clone')){return$this->getMethod('__clone')->isPublic();}return
true;}public function isIterateable(){return$this->implementsInterface('Traversable');}public
function isSubclassOf($class){if(is_object($class)){if(!$class instanceof InternalReflectionClass&&!$class
instanceof IReflectionClass){throw new Exception\Runtime(sprintf('Parameter must be a string or an instance of class reflection, "%s" provided.',get_class($class)),Exception\Runtime::INVALID_ARGUMENT);}$class=$class->getName();}if($class===$this->parentClassName){return
true;}$parent=$this->getParentClass();return false===$parent?false:$parent->isSubclassOf($class);}public
function getParentClass(){$className=$this->getParentClassName();if(null===$className){return
false;}return$this->getBroker()->getClass($className);}public function getParentClassName(){return$this->parentClassName;}public
function getParentClasses(){$parent=$this->getParentClass();if(false===$parent){return
array();}return array_merge(array($parent->getName()=>$parent),$parent->getParentClasses());}public
function getParentClassNameList(){$parent=$this->getParentClass();if(false===$parent){return
array();}return array_merge(array($parent->getName()),$parent->getParentClassNameList());}public
function implementsInterface($interface){if(is_object($interface)){if(!$interface
instanceof InternalReflectionClass&&!$interface instanceof IReflectionClass){throw
new Exception\Runtime(sprintf('Parameter must be a string or an instance of class reflection, "%s" provided.',get_class($interface)),Exception\Runtime::INVALID_ARGUMENT);}$interfaceName=$interface->getName();if(!$interface->isInterface()){throw
new Exception\Runtime(sprintf('"%s" is not an interface.',$interfaceName),Exception\Runtime::INVALID_ARGUMENT);}}else{$reflection=$this->getBroker()->getClass($interface);if(!$reflection->isInterface()){throw
new Exception\Runtime(sprintf('"%s" is not an interface.',$interface),Exception\Runtime::INVALID_ARGUMENT);}$interfaceName=$interface;}return
in_array($interfaceName,$this->getInterfaceNames());}public function getInterfaces(){$interfaceNames=$this->getInterfaceNames();if(empty($interfaceNames)){return
array();}$broker=$this->getBroker();return array_combine($interfaceNames,array_map(function($interfaceName)use($broker){return$broker->getClass($interfaceName);},$interfaceNames));}public
function getInterfaceNames(){$parentClass=$this->getParentClass();$names=false!==$parentClass?array_reverse(array_flip($parentClass->getInterfaceNames())):array();foreach($this->interfaces
as$interfaceName){$names[$interfaceName]=true;foreach(array_reverse($this->getBroker()->getClass($interfaceName)->getInterfaceNames())as$parentInterfaceName){$names[$parentInterfaceName]=true;}}return
array_keys($names);}public function getOwnInterfaces(){$interfaceNames=$this->getOwnInterfaceNames();if(empty($interfaceNames)){return
array();}$broker=$this->getBroker();return array_combine($interfaceNames,array_map(function($interfaceName)use($broker){return$broker->getClass($interfaceName);},$interfaceNames));}public
function getOwnInterfaceNames(){return$this->interfaces;}public function getConstructor(){foreach($this->getMethods()as$method){if($method->isConstructor()){return$method;}}return
null;}public function getDestructor(){foreach($this->getMethods()as$method){if($method->isDestructor()){return$method;}}return
null;}public function hasMethod($name){foreach($this->getMethods()as$method){if($name===$method->getName()){return
true;}}return false;}public function getMethod($name){if(isset($this->methods[$name])){return$this->methods[$name];}foreach($this->getMethods()as$method){if($name===$method->getName()){return$method;}}throw
new Exception\Runtime(sprintf('There is no method "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function getMethods($filter=null){$methods=$this->methods;if(null!==$this->parentClassName){foreach($this->getParentClass()->getMethods(null)as$parentMethod){if(!isset($methods[$parentMethod->getName()])){$methods[$parentMethod->getName()]=$parentMethod;}}}foreach($this->getOwnInterfaces()as$interface){foreach($interface->getMethods(null)as$parentMethod){if(!isset($methods[$parentMethod->getName()])){$methods[$parentMethod->getName()]=$parentMethod;}}}if(null!==$filter){$methods=array_filter($methods,function(IReflectionMethod$method)use($filter){return$method->is($filter);});}return
array_values($methods);}public function hasOwnMethod($name){return isset($this->methods[$name]);}public
function getOwnMethods($filter=null){$methods=$this->methods;if(null!==$filter){$methods=array_filter($methods,function(ReflectionMethod$method)use($filter){return$method->is($filter);});}return
array_values($methods);}public function hasConstant($name){if(isset($this->constants[$name])){return
true;}foreach($this->getConstantReflections()as$constant){if($name===$constant->getName()){return
true;}}return false;}public function getConstant($name){try{return$this->getConstantReflection($name)->getValue();}catch(Exception\Runtime$e){if($e->getCode()===Exception\Runtime::DOES_NOT_EXIST){return
false;}throw$e;}}public function getConstantReflection($name){if(isset($this->constants[$name])){return$this->constants[$name];}foreach($this->getConstantReflections()as$constant){if($name===$constant->getName()){return$constant;}}throw
new Exception\Runtime(sprintf('There is no constant "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function getConstants(){$constants=array();foreach($this->getConstantReflections()as$constant){$constants[$constant->getName()]=$constant->getValue();}return$constants;}public
function getConstantReflections(){if(null===$this->parentClassName){return array_values($this->constants);}else{$reflections=array_values($this->constants);if(null!==$this->parentClassName){$reflections=array_merge($reflections,$this->getParentClass()->getConstantReflections());}foreach($this->getOwnInterfaces()as$interface){$reflections=array_merge($reflections,$interface->getConstantReflections());}return$reflections;}}public
function hasOwnConstant($name){return isset($this->constants[$name]);}public function
getOwnConstants(){return array_map(function(ReflectionConstant$constant){return$constant->getValue();},$this->constants);}public
function getOwnConstantReflections(){return array_values($this->constants);}public
function hasProperty($name){foreach($this->getProperties()as$property){if($name===$property->getName()){return
true;}}return false;}public function getProperty($name){if(isset($this->properties[$name])){return$this->properties[$name];}foreach($this->getProperties()as$property){if($name===$property->getName()){return$property;}}throw
new Exception\Runtime(sprintf('There is no property "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function getProperties($filter=null){$properties=$this->properties;if(null!==$this->parentClassName){foreach($this->getParentClass()->getProperties(null)as$parentProperty){if(!isset($properties[$parentProperty->getName()])){$properties[$parentProperty->getName()]=$parentProperty;}}}if(null!==$filter){$properties=array_filter($properties,function(IReflectionProperty$property)use($filter){return
(bool)($property->getModifiers()&$filter);});}return array_values($properties);}public
function hasOwnProperty($name){return isset($this->properties[$name]);}public function
getOwnProperties($filter=null){$properties=$this->properties;if(null!==$filter){$properties=array_filter($properties,function(ReflectionProperty$property)use($filter){return
(bool)($property->getModifiers()&$filter);});}return array_values($properties);}public
function getDefaultProperties(){static$accessLevels=array(InternalReflectionProperty::IS_PUBLIC,InternalReflectionProperty::IS_PROTECTED,InternalReflectionProperty::IS_PRIVATE);$defaults=array();$properties=$this->getProperties();foreach(array(true,false)as$static){foreach($properties
as$property){foreach($accessLevels as$level){if($property->isStatic()===$static&&($property->getModifiers()&$level)){$defaults[$property->getName()]=$property->getDefaultValue();}}}}return$defaults;}public
function getStaticProperties(){$defaults=array();foreach($this->getProperties(InternalReflectionProperty::IS_STATIC)as$property){if($property
instanceof ReflectionProperty){$defaults[$property->getName()]=$property->getDefaultValue();}}return$defaults;}public
function getStaticPropertyValue($name,$default=null){if($this->hasProperty($name)&&($property=$this->getProperty($name))&&$property->isStatic()){if(!$property->isPublic()&&!$property->isAccessible()){throw
new Exception\Runtime(sprintf('Static property "%s" in class "%s" is not accessible.',$name,$this->name),Exception\Runtime::NOT_ACCESSBILE);}return$property->getDefaultValue();}throw
new Exception\Runtime(sprintf('There is no static property "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function getDirectSubclasses(){$that=$this->name;return array_filter($this->getBroker()->getClasses(),function(ReflectionClass$class)use($that){if(!$class->isSubclassOf($that)){return
false;}return null===$class->getParentClassName()||!$class->getParentClass()->isSubClassOf($that);});}public
function getDirectSubclassNames(){return array_keys($this->getDirectSubclasses());}public
function getIndirectSubclasses(){$that=$this->name;return array_filter($this->getBroker()->getClasses(),function(ReflectionClass$class)use($that){if(!$class->isSubclassOf($that)){return
false;}return null!==$class->getParentClassName()&&$class->getParentClass()->isSubClassOf($that);});}public
function getIndirectSubclassNames(){return array_keys($this->getIndirectSubclasses());}public
function getDirectImplementers(){if(!$this->isInterface()){return array();}$that=$this->name;return
array_filter($this->getBroker()->getClasses(),function(ReflectionClass$class)use($that){if($class->isInterface()||!$class->implementsInterface($that)){return
false;}return null===$class->getParentClassName()||!$class->getParentClass()->implementsInterface($that);});}public
function getDirectImplementerNames(){return array_keys($this->getDirectImplementers());}public
function getIndirectImplementers(){if(!$this->isInterface()){return array();}$that=$this->name;return
array_filter($this->getBroker()->getClasses(),function(ReflectionClass$class)use($that){if($class->isInterface()||!$class->implementsInterface($that)){return
false;}return null!==$class->getParentClassName()&&$class->getParentClass()->implementsInterface($that);});}public
function getIndirectImplementerNames(){return array_keys($this->getIndirectImplementers());}public
function isInstance($object){if(!is_object($object)){throw new Exception\Runtime(sprintf('Parameter must be a class instance, "%s" provided.',gettype($object)),Exception\Runtime::INVALID_ARGUMENT);}return$this->name===get_class($object)||is_subclass_of($object,$this->name);}public
function newInstance($args){return$this->newInstanceArgs(func_get_args());}public
function newInstanceArgs(array$args=array()){if(!class_exists($this->name,true)){throw
new Exception\Runtime(sprintf('Could not create an instance of class "%s"; class does not exist.',$this->name),Exception\Runtime::DOES_NOT_EXIST);}$reflection=new
InternalReflectionClass($this->name);return$reflection->newInstanceArgs($args);}public
function setStaticPropertyValue($name,$value){if($this->hasProperty($name)&&($property=$this->getProperty($name))&&$property->isStatic()){if(!$property->isPublic()&&!$property->isAccessible()){throw
new Exception\Runtime(sprintf('Static property "%s" in class "%s" is not accessible.',$name,$this->name),Exception\Runtime::NOT_ACCESSBILE);}$property->setDefaultValue($value);return;}throw
new Exception\Runtime(sprintf('There is no static property "%s" in class "%s".',$name,$this->name),Exception\Runtime::DOES_NOT_EXIST);}public
function __toString(){$implements='';$interfaceNames=$this->getInterfaceNames();if(count($interfaceNames)>0){$implements=sprintf(' %s %s',$this->isInterface()?'extends':'implements',implode(', ',$interfaceNames));}$buffer='';$count=0;foreach($this->getConstantReflections()as$constant){$buffer.='    '.$constant->__toString();$count++;}$constants=sprintf("\n\n  - Constants [%d] {\n%s  }",$count,$buffer);$sBuffer='';$sCount=0;$buffer='';$count=0;foreach($this->getProperties()as$property){$string='    '.preg_replace('~\n(?!$)~',"\n    ",$property->__toString());if($property->isStatic()){$sBuffer.=$string;$sCount++;}else{$buffer.=$string;$count++;}}$staticProperties=sprintf("\n\n  - Static properties [%d] {\n%s  }",$sCount,$sBuffer);$properties=sprintf("\n\n  - Properties [%d] {\n%s  }",$count,$buffer);$sBuffer='';$sCount=0;$buffer='';$count=0;foreach($this->getMethods()as$method){if($method->getDeclaringClassName()!==$this->getName()&&$method->isPrivate()){continue;}$string="\n    ".preg_replace('~\n(?!$|\n|\s*\*)~',"\n    ",$method->__toString());if($method->getDeclaringClassName()!==$this->getName()){$string=preg_replace(array('~Method [ <[\w:]+~','~, overwrites[^,]+~'),array('\0, inherits '.$method->getDeclaringClassName(),''),$string);}if($method->isStatic()){$sBuffer.=$string;$sCount++;}else{$buffer.=$string;$count++;}}$staticMethods=sprintf("\n\n  - Static methods [%d] {\n%s  }",$sCount,ltrim($sBuffer,"\n"));$methods=sprintf("\n\n  - Methods [%d] {\n%s  }",$count,ltrim($buffer,"\n"));return
sprintf("%s%s [ <user>%s %s%s%s %s%s%s ] {\n  @@ %s %d-%d%s%s%s%s%s\n}\n",$this->getDocComment()?$this->getDocComment()."\n":'',$this->isInterface()?'Interface':'Class',$this->isIterateable()?' <iterateable>':'',$this->isAbstract()&&!$this->isInterface()?'abstract ':'',$this->isFinal()?'final ':'',$this->isInterface()?'interface':'class',$this->getName(),null!==$this->getParentClassName()?' extends '.$this->getParentClassName():'',$implements,$this->getFileName(),$this->getStartLine(),$this->getEndLine(),$constants,$staticProperties,$staticMethods,$properties,$methods);}public
static function export(Broker$broker,$className,$return=false){if(is_object($className)){$className=get_class($className);}$class=$broker->getClass($className);if($class
instanceof Dummy\ReflectionClass){throw new Exception\Runtime(sprintf('Class %s does not exist.',$className),Exception\Runtime::DOES_NOT_EXIST);}if($return){return$class->__toString();}echo$class->__toString();}public
function isComplete(){if(!$this->definitionComplete){if(null!==$this->parentClassName&&!$this->getParentClass()->isComplete()){return
false;}foreach($this->getOwnInterfaces()as$interface){if(!$interface->isComplete()){return
false;}}$this->definitionComplete=true;}return$this->definitionComplete;}public function
getNamespaceAliases(){return$this->aliases;}protected function processParent(IReflection$parent){if(!$parent
instanceof ReflectionFileNamespace){throw new Exception\Parse(sprintf('The parent object has to be an instance of TokenReflection\ReflectionFileNamespace, "%s" given.',get_class($parent)),Exception\Parse::INVALID_PARENT);}$this->namespaceName=$parent->getName();$this->aliases=$parent->getNamespaceAliases();return
parent::processParent($parent);}protected function parse(Stream$tokenStream,IReflection$parent){return$this->parseModifiers($tokenStream)->parseName($tokenStream)->parseParent($tokenStream,$parent)->parseInterfaces($tokenStream,$parent);}private
function parseModifiers(Stream$tokenStream){try{while(true){switch($tokenStream->getType()){case
null:break 2;case T_ABSTRACT:$this->modifiers=InternalReflectionClass::IS_EXPLICIT_ABSTRACT;break;case
T_FINAL:$this->modifiers=InternalReflectionClass::IS_FINAL;break;case T_INTERFACE:$this->modifiers=self::IS_INTERFACE;case
T_CLASS:$tokenStream->skipWhitespaces();break 2;default:break;}$tokenStream->skipWhitespaces();}return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse class modifiers.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}protected
function parseName(Stream$tokenStream){try{if(!$tokenStream->is(T_STRING)){throw
new Exception\Parse(sprintf('Invalid token found: "%s".',$tokenStream->getTokenName()),Exception\Parse::PARSE_ELEMENT_ERROR);}if($this->namespaceName===ReflectionNamespace::NO_NAMESPACE_NAME){$this->name=$tokenStream->getTokenValue();}else{$this->name=$this->namespaceName.'\\'.$tokenStream->getTokenValue();}$tokenStream->skipWhitespaces();return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse class name.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}private
function parseParent(Stream$tokenStream,ReflectionBase$parent=null){if(!$tokenStream->is(T_EXTENDS)){return$this;}try{while(true){$tokenStream->skipWhitespaces();$parentClassName='';while(true){switch($tokenStream->getType()){case
T_STRING:case T_NS_SEPARATOR:$parentClassName.=$tokenStream->getTokenValue();break;default:break
2;}$tokenStream->skipWhitespaces();}$parentClassName=Resolver::resolveClassFQN($parentClassName,$this->aliases,$this->namespaceName);if($this->isInterface()){$this->interfaces[]=$parentClassName;if(','===$tokenStream->getTokenValue()){continue;}}else{$this->parentClassName=$parentClassName;}break;}return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse parent class name.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}private
function parseInterfaces(Stream$tokenStream,ReflectionBase$parent=null){if(!$tokenStream->is(T_IMPLEMENTS)){return$this;}if($this->isInterface()){throw
new Exception\Parse(sprintf('Interfaces ("%s") cannot implement interfaces.',$this->name),Exception\Parse::PARSE_ELEMENT_ERROR);}try{while(true){$interfaceName='';$tokenStream->skipWhitespaces();while(true){switch($tokenStream->getType()){case
T_STRING:case T_NS_SEPARATOR:$interfaceName.=$tokenStream->getTokenValue();break;default:break
2;}$tokenStream->skipWhitespaces();}$this->interfaces[]=Resolver::resolveClassFQN($interfaceName,$this->aliases,$this->namespaceName);$type=$tokenStream->getType();if('{'===$type){break;}elseif(','!==$type){throw
new Exception\Parse(sprintf('Invalid token found: "%s", expected "{" or ";".',$tokenStream->getTokenName()),Exception\Parse::PARSE_ELEMENT_ERROR);}}return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse implemented interfaces.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}protected
function parseChildren(Stream$tokenStream,IReflection$parent){while(true){switch($type=$tokenStream->getType()){case
null:break 2;case T_COMMENT:case T_DOC_COMMENT:$docblock=$tokenStream->getTokenValue();if(preg_match('~^'.preg_quote(self::DOCBLOCK_TEMPLATE_START,'~').'~',$docblock)){array_unshift($this->docblockTemplates,new
ReflectionAnnotation($this,$docblock));}elseif(self::DOCBLOCK_TEMPLATE_END===$docblock){array_shift($this->docblockTemplates);}$tokenStream->next();break;case
'}':break 2;case T_PUBLIC:case T_PRIVATE:case T_PROTECTED:case T_STATIC:case T_VAR:case
T_VARIABLE:static$searching=array(T_VARIABLE=>true,T_FUNCTION=>true);if(T_VAR!==$tokenStream->getType()){$position=$tokenStream->key();while(null!==($type=$tokenStream->getType($position))&&!isset($searching[$type])){$position++;}}if(T_VARIABLE===$type||T_VAR===$type){$property=new
ReflectionProperty($tokenStream,$this->getBroker(),$this);$this->properties[$property->getName()]=$property;$tokenStream->next();break;}case
T_FINAL:case T_ABSTRACT:case T_FUNCTION:$method=new ReflectionMethod($tokenStream,$this->getBroker(),$this);$this->methods[$method->getName()]=$method;$tokenStream->next();break;case
T_CONST:$tokenStream->skipWhitespaces();while($tokenStream->is(T_STRING)){$constant=new
ReflectionConstant($tokenStream,$this->getBroker(),$this);$this->constants[$constant->getName()]=$constant;if($tokenStream->is(',')){$tokenStream->skipWhitespaces();}else{$tokenStream->next();}}break;default:$tokenStream->next();break;}}return$this;}}}

 namespace TokenReflection{class ReflectionConstant extends ReflectionBase implements
IReflectionConstant{private$declaringClassName;private$namespaceName;private$value;private$valueDefinition=array();private$aliases=array();public
function getShortName(){$name=$this->getName();if(null!==$this->namespaceName&&$this->namespaceName!==ReflectionNamespace::NO_NAMESPACE_NAME){$name=substr($name,strlen($this->namespaceName)+1);}return$name;}public
function getDeclaringClassName(){return$this->declaringClassName;}public function
getDeclaringClass(){if(null===$this->declaringClassName){return null;}return$this->getBroker()->getClass($this->declaringClassName);}public
function getNamespaceName(){return null===$this->namespaceName||$this->namespaceName===ReflectionNamespace::NO_NAMESPACE_NAME?'':$this->namespaceName;}public
function inNamespace(){return ''!==$this->getNamespaceName();}public function getValue(){if(is_array($this->valueDefinition)){$this->value=Resolver::getValueDefinition($this->valueDefinition,$this);$this->valueDefinition=Resolver::getSourceCode($this->valueDefinition);}return$this->value;}public
function getValueDefinition(){return is_array($this->valueDefinition)?Resolver::getSourceCode($this->valueDefinition):$this->valueDefinition;}public
function getOriginalValueDefinition(){return$this->valueDefinition;}public function
__toString(){return sprintf("Constant [ %s %s ] { %s }\n",strtolower(gettype($this->getValue())),$this->getName(),$this->getValue());}public
static function export(Broker$broker,$class,$constant,$return=false){$className=is_object($class)?get_class($class):$class;$constantName=$constant;if(null===$className){$constant=$broker->getConstant($constantName);if(null===$constant){throw
new Exception\Runtime(sprintf('Constant %s does not exist.',$constantName),Exception\Runtime::DOES_NOT_EXIST);}}else{$class=$broker->getClass($className);if($class
instanceof Dummy\ReflectionClass){throw new Exception\Runtime(sprintf('Class %s does not exist.',$className),Exception\Runtime::DOES_NOT_EXIST);}$constant=$class->getConstantReflection($constantName);}if($return){return$constant->__toString();}echo$constant->__toString();}public
function getNamespaceAliases(){return null===$this->declaringClassName?$this->aliases:$this->getDeclaringClass()->getNamespaceAliases();}protected
function processParent(IReflection$parent){if($parent instanceof ReflectionFileNamespace){$this->namespaceName=$parent->getName();$this->aliases=$parent->getNamespaceAliases();}elseif($parent
instanceof ReflectionClass){$this->declaringClassName=$parent->getName();}else{throw
new Exception\Parse(sprintf('The parent object has to be an instance of TokenReflection\ReflectionFileNamespace or TokenReflection\ReflectionClass, "%s" given.',get_class($parent)),Exception\Parse::INVALID_PARENT);}return
parent::processParent($parent);}protected function parseDocComment(Stream$tokenStream,IReflection$parent){$position=$tokenStream->key()-1;while($position>0&&!$tokenStream->is(T_CONST,$position)){$position--;}$actual=$tokenStream->key();parent::parseDocComment($tokenStream->seek($position),$parent);$tokenStream->seek($actual);return$this;}protected
function parse(Stream$tokenStream,IReflection$parent){return$this->parseName($tokenStream)->parseValue($tokenStream,$parent);}protected
function parseName(Stream$tokenStream){try{if($tokenStream->is(T_CONST)){$tokenStream->skipWhitespaces();}if(!$tokenStream->is(T_STRING)){throw
new Exception\Parse('The constant name could not be determined.',Exception\Parse::PARSE_ELEMENT_ERROR);}if(null===$this->namespaceName||$this->namespaceName===ReflectionNamespace::NO_NAMESPACE_NAME){$this->name=$tokenStream->getTokenValue();}else{$this->name=$this->namespaceName.'\\'.$tokenStream->getTokenValue();}$tokenStream->skipWhitespaces();return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse constant name.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}private
function parseValue(Stream$tokenStream,IReflection$parent){try{if(!$tokenStream->is('=')){throw
new Exception\Parse('Could not find the definition start.',Exception\Parse::PARSE_ELEMENT_ERROR);}$tokenStream->skipWhitespaces();static$acceptedTokens=array('-'=>true,'+'=>true,T_STRING=>true,T_NS_SEPARATOR=>true,T_CONSTANT_ENCAPSED_STRING=>true,T_DNUMBER=>true,T_LNUMBER=>true,T_DOUBLE_COLON=>true);while(null!==($type=$tokenStream->getType())&&isset($acceptedTokens[$type])){$this->valueDefinition[]=$tokenStream->current();$tokenStream->next();}if(empty($this->valueDefinition)){throw
new Exception\Parse('Value definition is empty.',Exception\Parse::PARSE_ELEMENT_ERROR);}$value=$tokenStream->getTokenValue();if(null===$type||(','!==$value&&';'!==$value)){throw
new Exception\Parse(sprintf('Invalid value definition: "%s".',$this->valueDefinition),Exception\Parse::PARSE_ELEMENT_ERROR);}return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse constant value.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}}}

 namespace TokenReflection{use TokenReflection\Exception;class ReflectionFileNamespace
extends ReflectionBase{private$classes=array();private$constants=array();private$functions=array();private$aliases=array();public
function getClasses(){return$this->classes;}public function getConstants(){return$this->constants;}public
function getFunctions(){return$this->functions;}public function getNamespaceAliases(){return$this->aliases;}protected
function processParent(IReflection$parent){if(!$parent instanceof ReflectionFile){throw
new Exception\Parse(sprintf('The parent object has to be an instance of TokenReflection\ReflectionFile, "%s" given.',get_class($parent)),Exception\Parse::INVALID_PARENT);}return
parent::processParent($parent);}protected function parse(Stream$tokenStream,IReflection$parent){return$this->parseName($tokenStream);}protected
function parseName(Stream$tokenStream){if(!$tokenStream->is(T_NAMESPACE)){$this->name=ReflectionNamespace::NO_NAMESPACE_NAME;return$this;}try{$tokenStream->skipWhitespaces();$name='';while(true){switch($tokenStream->getType()){case
T_STRING:case T_NS_SEPARATOR:$name.=$tokenStream->getTokenValue();break;default:break
2;}$tokenStream->skipWhitespaces();}$name=ltrim($name,'\\');if(empty($name)){$this->name=ReflectionNamespace::NO_NAMESPACE_NAME;}else{$this->name=$name;}if(!$tokenStream->is(';')&&!$tokenStream->is('{')){throw
new Exception\Parse(sprintf('Invalid namespace name end: "%s", expecting ";" or "{".',$tokenStream->getTokenName()),Exception\Parse::PARSE_ELEMENT_ERROR);}$tokenStream->skipWhitespaces();return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse namespace name.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}protected
function parseChildren(Stream$tokenStream,IReflection$parent){static$skipped=array(T_WHITESPACE=>true,T_COMMENT=>true,T_DOC_COMMENT=>true);while(true){switch($tokenStream->getType()){case
T_USE:while(true){$namespaceName='';$alias=null;$tokenStream->skipWhitespaces();while(true){switch($tokenStream->getType()){case
T_STRING:case T_NS_SEPARATOR:$namespaceName.=$tokenStream->getTokenValue();break;default:break
2;}$tokenStream->skipWhitespaces();}$namespaceName=ltrim($namespaceName,'\\');if(empty($namespaceName)){throw
new Exception\Parse('Imported namespace name could not be determined.',Exception\Parse::PARSE_ELEMENT_ERROR);}elseif('\\'===substr($namespaceName,-1)){throw
new Exception\Parse(sprintf('Invalid namespace name "%s".',$namespaceName),Exception\Parse::PARSE_ELEMENT_ERROR);}if($tokenStream->is(T_AS)){$tokenStream->skipWhitespaces();if(!$tokenStream->is(T_STRING)){throw
new Exception\Parse(sprintf('The imported namespace "%s" seems aliased but the alias name could not be determined.',$namespaceName),Exception\Parse::PARSE_ELEMENT_ERROR);}$alias=$tokenStream->getTokenValue();$tokenStream->skipWhitespaces();}else{if(false!==($pos=strrpos($namespaceName,'\\'))){$alias=substr($namespaceName,$pos+1);}else{$alias=$namespaceName;}}if(isset($aliases[$alias])){throw
new Exception\Parse(sprintf('Namespace alias "%s" already defined.',$alias),Exception\Parse::PARSE_ELEMENT_ERROR);}$this->aliases[$alias]=$namespaceName;$type=$tokenStream->getType();if(';'===$type){$tokenStream->skipWhitespaces();break
2;}elseif(','===$type){continue;}throw new Exception\Parse(sprintf('Unexpected token found: "%s".',$tokenStream->getTokenName()),Exception\Parse::PARSE_ELEMENT_ERROR);}case
T_COMMENT:case T_DOC_COMMENT:$docblock=$tokenStream->getTokenValue();if(preg_match('~^'.preg_quote(self::DOCBLOCK_TEMPLATE_START,'~').'~',$docblock)){array_unshift($this->docblockTemplates,new
ReflectionAnnotation($this,$docblock));}elseif(self::DOCBLOCK_TEMPLATE_END===$docblock){array_shift($this->docblockTemplates);}$tokenStream->next();break;case
'{':$tokenStream->findMatchingBracket()->next();break;case '}':case null:case T_NAMESPACE:break
2;case T_ABSTRACT:case T_FINAL:case T_CLASS:case T_INTERFACE:$class=new ReflectionClass($tokenStream,$this->getBroker(),$this);$this->classes[$class->getName()]=$class;$tokenStream->next();break;case
T_CONST:$tokenStream->skipWhitespaces();while($tokenStream->is(T_STRING)){$constant=new
ReflectionConstant($tokenStream,$this->getBroker(),$this);$this->constants[$constant->getName()]=$constant;if($tokenStream->is(',')){$tokenStream->skipWhitespaces();}else{$tokenStream->next();}}break;case
T_FUNCTION:$position=$tokenStream->key()+1;while(isset($skipped[$type=$tokenStream->getType($position)])){$position++;}if('('===$type){$tokenStream->seek($position)->findMatchingBracket()->skipWhiteSpaces();if($tokenStream->is(T_USE)){$tokenStream->skipWhitespaces()->findMatchingBracket()->skipWhitespaces();}$tokenStream->findMatchingBracket()->next();continue;}$function=new
ReflectionFunction($tokenStream,$this->getBroker(),$this);$this->functions[$function->getName()]=$function;$tokenStream->next();break;default:$tokenStream->next();break;}}return$this;}}}

 namespace TokenReflection{use TokenReflection\Exception;abstract class ReflectionFunctionBase
extends ReflectionBase implements IReflectionFunctionBase{protected$namespaceName;protected$modifiers=0;private$returnsReference=false;private$parameters=array();private$staticVariables=array();private$staticVariablesDefinition=array();public
function getName(){if(null!==$this->namespaceName&&ReflectionNamespace::NO_NAMESPACE_NAME!==$this->namespaceName){return$this->namespaceName.'\\'.$this->name;}return$this->name;}public
function getShortName(){return$this->name;}public function getNamespaceName(){return
null===$this->namespaceName||$this->namespaceName===ReflectionNamespace::NO_NAMESPACE_NAME?'':$this->namespaceName;}public
function inNamespace(){return ''!==$this->getNamespaceName();}public function getModifiers(){return$this->modifiers;}public
function isClosure(){return false;}public function returnsReference(){return$this->returnsReference;}public
function getParameter($parameter){if(is_numeric($parameter)){if(!isset($this->parameters[$parameter])){throw
new Exception\Runtime(sprintf('There is no parameter at position "%d" in function/method "%s".',$parameter,$this->getName()),Exception\Runtime::DOES_NOT_EXIST);}return$this->parameters[$parameter];}else{foreach($this->parameters
as$reflection){if($reflection->getName()===$parameter){return$reflection;}}throw
new Exception\Runtime(sprintf('There is no parameter "%s" in function/method "%s".',$parameter,$this->getName()),Exception\Runtime::DOES_NOT_EXIST);}}public
function getParameters(){return$this->parameters;}public function getNumberOfParameters(){return
count($this->parameters);}public function getNumberOfRequiredParameters(){$count=0;array_walk($this->parameters,function(ReflectionParameter$parameter)use(&$count){if(!$parameter->isOptional()){$count++;}});return$count;}public
function getStaticVariables(){if(empty($this->staticVariables)&&!empty($this->staticVariablesDefinition)){foreach($this->staticVariablesDefinition
as$variableName=>$variableDefinition){$this->staticVariables[$variableName]=Resolver::getValueDefinition($variableDefinition,$this);}}return$this->staticVariables;}final
protected function parseReturnsReference(Stream$tokenStream){try{if(!$tokenStream->is(T_FUNCTION)){throw
new Exception\Parse('Could not find the function keyword.',Exception\Parse::PARSE_ELEMENT_ERROR);}$tokenStream->skipWhitespaces();$type=$tokenStream->getType();if('&'===$type){$this->returnsReference=true;$tokenStream->skipWhitespaces();}elseif(T_STRING!==$type){throw
new Exception\Parse(sprintf('Invalid token found: "%s".',$tokenStream->getTokenName()),Exception\Parse::PARSE_ELEMENT_ERROR);}return$this;}catch(Exception\Parse$e){throw
new Exception\Parse('Could not determine if the function\method returns its value by reference.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}protected
function parseName(Stream$tokenStream){try{if(!$tokenStream->is(T_STRING)){throw
new Exception\Parse(sprintf('Invalid token found: "%s".',$tokenStream->getTokenName()),Exception\Parse::PARSE_ELEMENT_ERROR);}$this->name=$tokenStream->getTokenValue();$tokenStream->skipWhitespaces();return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse function/method name.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}final
protected function parseChildren(Stream$tokenStream,IReflection$parent){return$this->parseParameters($tokenStream)->parseStaticVariables($tokenStream);}final
protected function parseParameters(Stream$tokenStream){try{if(!$tokenStream->is('(')){throw
new Exception\Parse('Could find the start token.',Exception\Parse::PARSE_CHILDREN_ERROR);}static$accepted=array(T_NS_SEPARATOR=>true,T_STRING=>true,T_ARRAY=>true,T_VARIABLE=>true,'&'=>true);$tokenStream->skipWhitespaces();while(null!==($type=$tokenStream->getType())&&')'!==$type){if(isset($accepted[$type])){$parameter=new
ReflectionParameter($tokenStream,$this->getBroker(),$this);$this->parameters[]=$parameter;}if($tokenStream->is(')')){break;}$tokenStream->skipWhitespaces();}$tokenStream->skipWhitespaces();return$this;}catch(Exception$e){throw
new Exception\Parse(sprintf('Could not parse function/method "%s" parameters.',$this->name),Exception\Parse::PARSE_CHILDREN_ERROR,$e);}}final
protected function parseStaticVariables(Stream$tokenStream){try{$type=$tokenStream->getType();if('{'===$type){if($this->getBroker()->isOptionSet(Broker::OPTION_PARSE_FUNCTION_BODY)){$tokenStream->skipWhitespaces();while('}'!==($type=$tokenStream->getType())){switch($type){case
T_STATIC:$type=$tokenStream->skipWhitespaces()->getType();if(T_VARIABLE!==$type){break;}while(T_VARIABLE===$type){$variableName=$tokenStream->getTokenValue();$variableDefinition=array();$type=$tokenStream->skipWhitespaces()->getType();if('='===$type){$type=$tokenStream->skipWhitespaces()->getType();$level=0;while($tokenStream->valid()){switch($type){case
'(':case '[':case '{':case T_CURLY_OPEN:case T_DOLLAR_OPEN_CURLY_BRACES:$level++;break;case
')':case ']':case '}':$level--;break;case ';':case ',':if(0===$level){break 2;}}$variableDefinition[]=$tokenStream->current();$type=$tokenStream->skipWhitespaces()->getType();}if(!$tokenStream->valid()){throw
new Exception\Parse('Invalid end of token stream.',Exception\Parse::PARSE_CHILDREN_ERROR);}}$this->staticVariablesDefinition[substr($variableName,1)]=$variableDefinition;if(','===$type){$type=$tokenStream->skipWhitespaces()->getType();}else{break;}}break;case
T_FUNCTION:if(!$tokenStream->find('{')){throw new Exception\Parse('Could not find beginning of the anonymous function.',Exception\Parse::PARSE_CHILDREN_ERROR);}case
'{':case '[':case '(':case T_CURLY_OPEN:case T_DOLLAR_OPEN_CURLY_BRACES:$tokenStream->findMatchingBracket()->skipWhitespaces();break;default:$tokenStream->skipWhitespaces();}}}else{$tokenStream->findMatchingBracket();}}elseif(';'!==$type){throw
new Exception\Parse(sprintf('Invalid token found: "%s".',$tokenStream->getTokenName()),Exception\Parse::PARSE_CHILDREN_ERROR);}return$this;}catch(Exception$e){throw
new Exception\Parse(sprintf('Could not parse function/method "%s" static variables.',$this->name),Exception\Parse::PARSE_CHILDREN_ERROR,$e);}}}}

 namespace TokenReflection{use TokenReflection\Exception;class ReflectionNamespace
implements IReflectionNamespace{const NO_NAMESPACE_NAME='no-namespace';private$name;private$classes=array();private$constants=array();private$functions=array();private$broker;public
function __construct($name,Broker$broker){$this->name=$name;$this->broker=$broker;}public
function getName(){return$this->name;}public function isInternal(){return false;}public
function isUserDefined(){return true;}public function isTokenized(){return true;}public
function hasClass($className){$className=ltrim($className,'\\');if(false===strpos($className,'\\')&&self::NO_NAMESPACE_NAME!==$this->getName()){$className=$this->getName().'\\'.$className;}return
isset($this->classes[$className]);}public function getClass($className){$className=ltrim($className,'\\');if(false===strpos($className,'\\')&&self::NO_NAMESPACE_NAME!==$this->getName()){$className=$this->getName().'\\'.$className;}if(!isset($this->classes[$className])){throw
new Exception\Runtime(sprintf('Class "%s" does not exist.',$className),Exception\Runtime::DOES_NOT_EXIST);}return$this->classes[$className];}public
function getClasses(){return$this->classes;}public function getClassNames(){return
array_keys($this->classes);}public function getClassShortNames(){return array_map(function(IReflectionClass$class){return$class->getShortName();},$this->classes);}public
function hasConstant($constantName){$constantName=ltrim($constantName,'\\');if(false===strpos($constantName,'\\')&&self::NO_NAMESPACE_NAME!==$this->getName()){$constantName=$this->getName().'\\'.$constantName;}return
isset($this->constants[$constantName]);}public function getConstant($constantName){$constantName=ltrim($constantName,'\\');if(false===strpos($constantName,'\\')&&self::NO_NAMESPACE_NAME!==$this->getName()){$constantName=$this->getName().'\\'.$constantName;}if(!isset($this->constants[$constantName])){throw
new Exception\Runtime(sprintf('Constant "%s" does not exist.',$constantName),Exception\Runtime::DOES_NOT_EXIST);}return$this->constants[$constantName];}public
function getConstants(){return$this->constants;}public function getConstantNames(){return
array_keys($this->constants);}public function getConstantShortNames(){return array_map(function(IReflectionConstant$constant){return$constant->getShortName();},$this->constants);}public
function hasFunction($functionName){$functionName=ltrim($functionName,'\\');if(false===strpos($functionName,'\\')&&self::NO_NAMESPACE_NAME!==$this->getName()){$functionName=$this->getName().'\\'.$functionName;}return
isset($this->functions[$functionName]);}public function getFunction($functionName){$functionName=ltrim($functionName,'\\');if(false===strpos($functionName,'\\')&&self::NO_NAMESPACE_NAME!==$this->getName()){$functionName=$this->getName().'\\'.$functionName;}if(!isset($this->functions[$functionName])){throw
new Exception\Runtime(sprintf('Function "%s" does not exist.',$functionName),Exception\Runtime::DOES_NOT_EXIST);}return$this->functions[$functionName];}public
function getFunctions(){return$this->functions;}public function getFunctionNames(){return
array_keys($this->functions);}public function getFunctionShortNames(){return array_map(function(IReflectionFunction$function){return$function->getShortName();},$this->functions);}public
function __toString(){$buffer='';$count=0;foreach($this->getClasses()as$class){$string="\n    ".trim(str_replace("\n","\n    ",$class->__toString()),' ');$string=str_replace("    \n      - Parameters","\n      - Parameters",$string);$buffer.=$string;$count++;}$classes=sprintf("\n\n  - Classes [%d] {\n%s  }",$count,ltrim($buffer,"\n"));$buffer='';$count=0;foreach($this->getConstants()as$constant){$buffer.='    '.$constant->__toString();$count++;}$constants=sprintf("\n\n  - Constants [%d] {\n%s  }",$count,$buffer);$buffer='';$count=0;foreach($this->getFunctions()as$function){$string="\n    ".trim(str_replace("\n","\n    ",$function->__toString()),' ');$string=str_replace("    \n      - Parameters","\n      - Parameters",$string);$buffer.=$string;$count++;}$functions=sprintf("\n\n  - Functions [%d] {\n%s  }",$count,ltrim($buffer,"\n"));return
sprintf("Namespace [ <user> namespace %s ] {  %s%s%s\n}\n",$this->getName(),$classes,$constants,$functions);}public
static function export(Broker$broker,$namespace,$return=false){$namespaceName=$namespace;$namespace=$broker->getNamespace($namespaceName);if(null===$namespace){throw
new Exception\Runtime(sprintf('Namespace %s does not exist.',$namespaceName),Exception\Runtime::DOES_NOT_EXIST);}if($return){return$namespace->__toString();}echo$namespace->__toString();}public
function addFileNamespace(ReflectionFileNamespace$namespace){$classes=$namespace->getClasses();foreach($this->classes
as$className=>$reflection){if(isset($classes[$className])){throw new Exception\Runtime(sprintf('Class "%s" is already defined; in file "%s".',$className,$reflection->getFileName()),Exception\Runtime::ALREADY_EXISTS);}}$this->classes=array_merge($this->classes,$classes);$functions=$namespace->getFunctions();foreach($this->functions
as$functionName=>$reflection){if(isset($functions[$functionName])){throw new Exception\Runtime(sprintf('Function "%s" is already defined; in file "%s".',$functionName,$reflection->getFileName()),Exception\Runtime::ALREADY_EXISTS);}}$this->functions=array_merge($this->functions,$functions);$constants=$namespace->getConstants();foreach($this->constants
as$constantName=>$reflection){if(isset($constants[$constantName])){throw new Exception\Runtime(sprintf('Constant "%s" is already defined; in file "%s".',$constantName,$reflection->getFileName()),Exception\Runtime::ALREADY_EXISTS);}}$this->constants=array_merge($this->constants,$constants);}public
function getSource(){throw new Exception\Runtime('Cannot export source code of a namespace.',Exception\Runtime::UNSUPPORTED);}public
function getBroker(){return$this->broker;}final public function __get($key){return
ReflectionBase::get($this,$key);}final public function __isset($key){return ReflectionBase::exists($this,$key);}}}

 namespace TokenReflection{use TokenReflection\Exception;use ReflectionParameter as
InternalReflectionParameter;class ReflectionParameter extends ReflectionBase implements
IReflectionParameter{const ARRAY_TYPE_HINT='array';private$declaringClassName;private$declaringFunctionName;private$defaultValue;private$defaultValueDefinition=array();private$typeHint;private$originalTypeHint;private$position;private$isOptional;private$passedByReference=false;public
function getDeclaringClass(){return null===$this->declaringClassName?null:$this->getBroker()->getClass($this->declaringClassName);}public
function getDeclaringClassName(){return$this->declaringClassName;}public function
getDeclaringFunction(){if(null!==$this->declaringClassName){$class=$this->getBroker()->getClass($this->declaringClassName);if(null!==$class){return$class->getMethod($this->declaringFunctionName);}}else{return$this->getBroker()->getFunction($this->declaringFunctionName);}}public
function getDeclaringFunctionName(){return$this->declaringFunctionName;}public function
getDefaultValue(){if(!$this->isOptional()){throw new Exception\Runtime(sprintf('Property "%s" is not optional.',$this->name),Exception\Runtime::UNSUPPORTED);}if(is_array($this->defaultValueDefinition)){if(0===count($this->defaultValueDefinition)){throw
new Exception\Runtime(sprintf('Property "%s" has no default value.',$this->name),Exception\Runtime::DOES_NOT_EXIST);}$this->defaultValue=Resolver::getValueDefinition($this->defaultValueDefinition,$this);$this->defaultValueDefinition=Resolver::getSourceCode($this->defaultValueDefinition);}return$this->defaultValue;}public
function getDefaultValueDefinition(){return is_array($this->defaultValueDefinition)?Resolver::getSourceCode($this->defaultValueDefinition):$this->defaultValueDefinition;}public
function isDefaultValueAvailable(){return null!==$this->getDefaultValueDefinition();}public
function getPosition(){return$this->position;}public function isArray(){return$this->typeHint===self::ARRAY_TYPE_HINT;}public
function getOriginalTypeHint(){return!$this->isArray()?ltrim($this->originalTypeHint,'\\'):null;}public
function getClass(){$name=$this->getClassName();if(null===$name){return null;}return$this->getBroker()->getClass($name);}public
function getClassName(){if($this->isArray()){return null;}try{if(null===$this->typeHint&&null!==$this->originalTypeHint){if(null!==$this->declaringClassName){$parent=$this->getDeclaringClass();if(null===$parent){throw
new Exception\Runtime(sprintf('Could not load class "%s" reflection.',$this->declaringClassName),Exception\Runtime::DOES_NOT_EXIST);}}else{$parent=$this->getDeclaringFunction();if(null===$parent||!$parent->isTokenized()){throw
new Exception\Runtime(sprintf('Could not load function "%s" reflection.',$this->declaringFunctionName),Exception\Runtime::DOES_NOT_EXIST);}}$lTypeHint=strtolower($this->originalTypeHint);if('parent'===$lTypeHint||'self'===$lTypeHint){if(null===$this->declaringClassName){throw
new Exception\Runtime('Parameter type hint cannot be "self" nor "parent" when not a method.',Exception::UNSUPPORTED);}if('parent'===$lTypeHint){if($parent->isInterface()||null===$parent->getParentClassName()){throw
new Exception\Runtime(sprintf('Class "%s" has no parent.',$this->declaringClassName),Exception::DOES_NOT_EXIST);}$this->typeHint=$parent->getParentClassName();}else{$this->typeHint=$this->declaringClassName;}}else{$this->typeHint=ltrim(Resolver::resolveClassFQN($this->originalTypeHint,$parent->getNamespaceAliases(),$parent->getNamespaceName()),'\\');}}return$this->typeHint;}catch(Exception\Runtime$e){throw
new Exception\Runtime('Could not determine the class type hint FQN.',0,$e);}}public
function allowsNull(){if($this->isArray()){return 'null'===strtolower($this->getDefaultValueDefinition());}return
null===$this->originalTypeHint||!empty($this->defaultValueDefinition);}public function
isOptional(){try{if(null===$this->isOptional){$function=$this->getDeclaringFunction();if(null===$function){throw
new Exception\Runtime(sprintf('Could not get the declaring function "%s" reflection.',$this->declaringFunctionName),Exception\Runtime::DOES_NOT_EXIST);}$this->isOptional=true;foreach(array_slice($function->getParameters(),$this->position)as$reflectionParameter){if(!$reflectionParameter->isDefaultValueAvailable()){$this->isOptional=false;break;}}}return$this->isOptional;}catch(Exception\Runtime$e){throw
new Exception\Runtime(sprintf('Could not determine if parameter "%s" is optional.',$this->name),0,$e);}}public
function isPassedByReference(){return$this->passedByReference;}public function __toString(){if($this->getClass()){$hint=$this->getClassName();if($this->allowsNull()){$hint.=' or NULL';}}elseif($this->isArray()){$hint='array';if($this->allowsNull()){$hint.=' or NULL';}}else{$hint='';}if($this->isDefaultValueAvailable()){$default=' = ';if(is_null($this->getDefaultValue())){$default.='NULL';}elseif(is_array($this->getDefaultValue())){$default.='Array';}elseif(is_bool($this->getDefaultValue())){$default.=$this->getDefaultValue()?'true':'false';}elseif(is_string($this->getDefaultValue())){$default.=sprintf("'%s'",str_replace("'","\\'",$this->getDefaultValue()));}else{$default.=$this->getDefaultValue();}}else{$default='';}return
sprintf('Parameter #%d [ <%s> %s%s$%s%s ]',$this->getPosition(),$this->isOptional()?'optional':'required',$hint?$hint.' ':'',$this->isPassedByReference()?'&':'',$this->getName(),$default);}public
static function export(Broker$broker,$function,$parameter,$return=false){$functionName=$function;$parameterName=$parameter;$function=$broker->getFunction($functionName);if(null===$function){throw
new Exception\Runtime(sprintf('Function %s() does not exist.',$functionName),Exception\Runtime::DOES_NOT_EXIST);}$parameter=$function->getParameter($parameterName);if($return){return$parameter->__toString();}echo$parameter->__toString();}public
function getNamespaceAliases(){return$this->getDeclaringFunction()->getNamespaceAliases();}protected
function processParent(IReflection$parent){if(!$parent instanceof ReflectionFunctionBase){throw
new Exception\Parse(sprintf('The parent object has to be an instance of TokenReflection\ReflectionFunctionBase, "%s" given.',get_class($parent)),Exception\Parse::INVALID_PARENT);}$this->declaringFunctionName=$parent->getName();$this->position=count($parent->getParameters());if($parent
instanceof ReflectionMethod){$this->declaringClassName=$parent->getDeclaringClassName();}return
parent::processParent($parent);}protected function parse(Stream$tokenStream,IReflection$parent){return$this->parseTypeHint($tokenStream)->parsePassedByReference($tokenStream)->parseName($tokenStream)->parseDefaultValue($tokenStream);}private
function parseTypeHint(Stream$tokenStream){try{$type=$tokenStream->getType();if(T_ARRAY===$type){$this->typeHint=self::ARRAY_TYPE_HINT;$this->originalTypeHint=self::ARRAY_TYPE_HINT;$tokenStream->skipWhitespaces();}elseif(T_STRING===$type||T_NS_SEPARATOR===$type){$className='';do{$className.=$tokenStream->getTokenValue();$tokenStream->skipWhitespaces();$type=$tokenStream->getType();}while(T_STRING===$type||T_NS_SEPARATOR===$type);if(''===ltrim($className,'\\')){throw
new Exception\Parse(sprintf('Invalid class name definition: "%s".',$className),Exception\Parse::PARSE_ELEMENT_ERROR);}$this->originalTypeHint=$className;}return$this;}catch(Exception\Parse$e){throw
new Exception\Parse('Could not parse the value constaint class name.',0,$e);}}private
function parsePassedByReference(Stream$tokenStream){if($tokenStream->is('&')){$this->passedByReference=true;$tokenStream->skipWhitespaces();}return$this;}protected
function parseName(Stream$tokenStream){try{if(!$tokenStream->is(T_VARIABLE)){throw
new Exception\Parse('The parameter name could not be determined.',Exception\Parse::PARSE_ELEMENT_ERROR);}$this->name=substr($tokenStream->getTokenValue(),1);$tokenStream->skipWhitespaces();return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse parameter name.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}private
function parseDefaultValue(Stream$tokenStream){try{if($tokenStream->is('=')){$tokenStream->skipWhitespaces();$level=0;while(null!==($type=$tokenStream->getType())){switch($type){case
')':if(0===$level){break 2;}case '}':case ']':$level--;break;case '(':case '{':case
'[':$level++;break;case ',':if(0===$level){break 2;}break;default:break;}$this->defaultValueDefinition[]=$tokenStream->current();$tokenStream->next();}if(')'!==$type&&','!==$type){throw
new Exception\Parse(sprintf('The property default value is not terminated properly. Expected "," or ")", "%s" found.',$tokenStream->getTokenName()),Exception\Parse::PARSE_ELEMENT_ERROR);}}return$this;}catch(Exception\Parse$e){throw
new Exception\Parse('Could not parse the default value.',0,$e);}}}}

 namespace TokenReflection\Php{use TokenReflection;use TokenReflection\Broker,TokenReflection\Exception;use
Reflector,ReflectionFunction as InternalReflectionFunction,ReflectionParameter as
InternalReflectionParameter;class ReflectionFunction extends InternalReflectionFunction
implements IReflection,TokenReflection\IReflectionFunction{private$parameters;private$broker;public
function __construct($functionName,Broker$broker){parent::__construct($functionName);$this->broker=$broker;}public
function getExtension(){return ReflectionExtension::create(parent::getExtension(),$this->broker);}public
function hasAnnotation($name){return false;}public function getAnnotation($name){return
null;}public function getAnnotations(){return array();}public function isTokenized(){return
false;}public function getParameter($parameter){$parameters=$this->getParameters();if(is_numeric($parameter)){if(!isset($parameters[$parameter])){throw
new Exception\Runtime(sprintf('There is no parameter at position "%d" in function "%s".',$parameter,$this->getName()),Exception\Runtime::DOES_NOT_EXIST);}return$parameters[$parameter];}else{foreach($parameters
as$reflection){if($reflection->getName()===$parameter){return$reflection;}}throw
new Exception\Runtime(sprintf('There is no parameter "%s" in function "%s".',$parameter,$this->getName()),Exception\Runtime::DOES_NOT_EXIST);}}public
function getParameters(){if(null===$this->parameters){$broker=$this->broker;$parent=$this;$this->parameters=array_map(function(InternalReflectionParameter$parameter)use($broker,$parent){return
ReflectionParameter::create($parameter,$broker,$parent);},parent::getParameters());}return$this->parameters;}public
function getBroker(){return$this->broker;}public function getNamespaceAliases(){return
array();}final public function __get($key){return TokenReflection\ReflectionBase::get($this,$key);}final
public function __isset($key){return TokenReflection\ReflectionBase::exists($this,$key);}public
static function create(Reflector$internalReflection,Broker$broker){if(!$internalReflection
instanceof InternalReflectionFunction){throw new Exception\Runtime(sprintf('Invalid reflection instance provided: "%s", ReflectionFunction expected.',get_class($internalReflection)),Exception\Runtime::INVALID_ARGUMENT);}return$broker->getFunction($internalReflection->getName());}}}

 namespace TokenReflection\Php{use TokenReflection;use TokenReflection\Broker,TokenReflection\Exception;use
Reflector,ReflectionMethod as InternalReflectionMethod,ReflectionParameter as InternalReflectionParameter;class
ReflectionMethod extends InternalReflectionMethod implements IReflection,TokenReflection\IReflectionMethod{private$parameters;private$broker;public
function __construct($class,$methodName,Broker$broker){parent::__construct($class,$methodName);$this->broker=$broker;}public
function getDeclaringClass(){return ReflectionClass::create(parent::getDeclaringClass(),$this->broker);}public
function getDeclaringClassName(){return$this->getDeclaringClass()->getName();}public
function hasAnnotation($name){return false;}public function getAnnotation($name){return
null;}public function getAnnotations(){return array();}public function isTokenized(){return
false;}public function getPrototype(){return self::create(parent::getPrototype(),$this->broker);}public
function getParameter($parameter){$parameters=$this->getParameters();if(is_numeric($parameter)){if(!isset($parameters[$parameter])){throw
new Exception\Runtime(sprintf('There is no parameter at position "%d" in method "%s".',$parameter,$this->getName()),Exception\Runtime::DOES_NOT_EXIST);}return$parameters[$parameter];}else{foreach($parameters
as$reflection){if($reflection->getName()===$parameter){return$reflection;}}throw
new Exception\Runtime(sprintf('There is no parameter "%s" in method "%s".',$parameter,$this->getName()),Exception\Runtime::DOES_NOT_EXIST);}}public
function getParameters(){if(null===$this->parameters){$broker=$this->broker;$parent=$this;$this->parameters=array_map(function(InternalReflectionParameter$parameter)use($broker,$parent){return
ReflectionParameter::create($parameter,$broker,$parent);},parent::getParameters());}return$this->parameters;}public
function setAccessible($accessible){if(PHP_VERSION_ID<50302){throw new Exception\Runtime(sprintf('Method setAccessible was introduced the internal reflection in PHP 5.3.2, you are using %s.',PHP_VERSION),Exception\Runtime::UNSUPPORTED);}parent::setAccessible($accessible);}public
function is($filter=null){return null===$filter||($this->getModifiers()&$filter);}public
function getBroker(){return$this->broker;}public function getNamespaceAliases(){return
array();}final public function __get($key){return TokenReflection\ReflectionBase::get($this,$key);}final
public function __isset($key){return TokenReflection\ReflectionBase::exists($this,$key);}public
static function create(Reflector$internalReflection,Broker$broker){static$cache=array();if(!$internalReflection
instanceof InternalReflectionMethod){throw new Exception\Runtime(sprintf('Invalid reflection instance provided: "%s", ReflectionMethod expected.',get_class($internalReflection)),Exception\Runtime::INVALID_ARGUMENT);}$key=$internalReflection->getDeclaringClass()->getName().'::'.$internalReflection->getName();if(!isset($cache[$key])){$cache[$key]=new
self($internalReflection->getDeclaringClass()->getName(),$internalReflection->getName(),$broker);}return$cache[$key];}}}

 namespace TokenReflection{use TokenReflection\Exception;use ReflectionFunction as
InternalReflectionFunction;class ReflectionFunction extends ReflectionFunctionBase
implements IReflectionFunction{private$aliases=array();public function isDisabled(){return$this->hasAnnotation('disabled');}public
function __toString(){$parameters='';if($this->getNumberOfParameters()>0){$buffer='';foreach($this->getParameters()as$parameter){$buffer.="\n    ".$parameter->__toString();}$parameters=sprintf("\n\n  - Parameters [%d] {%s\n  }",$this->getNumberOfParameters(),$buffer);}return
sprintf("%sFunction [ <user> function %s%s ] {\n  @@ %s %d - %d%s\n}\n",$this->getDocComment()?$this->getDocComment()."\n":'',$this->returnsReference()?'&':'',$this->getName(),$this->getFileName(),$this->getStartLine(),$this->getEndLine(),$parameters);}public
static function export(Broker$broker,$function,$return=false){$functionName=$function;$function=$broker->getFunction($functionName);if(null===$function){throw
new Exception\Runtime(sprintf('Function %s() does not exist.',$functionName),Exception\Runtime::DOES_NOT_EXIST);}if($return){return$function->__toString();}echo$function->__toString();}public
function invoke(){return$this->invokeArgs(func_get_args());}public function invokeArgs(array$args=array()){if(!function_exists($this->getName())){throw
new Exception\Runtime(sprintf('Could not invoke function "%s"; function is not defined.',$this->name),Exception\Runtime::DOES_NOT_EXIST);}return
call_user_func_array($this->getName(),$args);}public function getNamespaceAliases(){return$this->aliases;}protected
function processParent(IReflection$parent){if(!$parent instanceof ReflectionFileNamespace){throw
new Exception\Parse(sprintf('The parent object has to be an instance of TokenReflection\ReflectionFileNamespace, "%s" given.',get_class($parent)),Exception\Parse::INVALID_PARENT);}$this->namespaceName=$parent->getName();$this->aliases=$parent->getNamespaceAliases();return
parent::processParent($parent);}protected function parse(Stream$tokenStream,IReflection$parent){return$this->parseReturnsReference($tokenStream)->parseName($tokenStream);}}}

 namespace TokenReflection{use TokenReflection\Exception;use ReflectionMethod as InternalReflectionMethod,ReflectionClass
as InternalReflectionClass;class ReflectionMethod extends ReflectionFunctionBase
implements IReflectionMethod{const IS_IMPLEMENTED_ABSTRACT=0x08;const ACCESS_LEVEL_CHANGED=0x800;const
IS_CONSTRUCTOR=0x2000;const IS_DESTRUCTOR=0x4000;const IS_CLONE=0x8000;const IS_ALLOWED_STATIC=0x10000;private$declaringClassName;private$prototype;private$accessible=false;private$modifiersComplete=false;public
function getDeclaringClass(){return null===$this->declaringClassName?null:$this->getBroker()->getClass($this->declaringClassName);}public
function getDeclaringClassName(){return$this->declaringClassName;}public function
getModifiers(){if(!$this->modifiersComplete&&!($this->modifiers&(self::ACCESS_LEVEL_CHANGED|self::IS_IMPLEMENTED_ABSTRACT))){$declaringClass=$this->getDeclaringClass();$parentClass=$declaringClass->getParentClass();if(false!==$parentClass&&$parentClass->hasMethod($this->name)){$parentClassMethod=$parentClass->getMethod($this->name);if(($this->isPublic()||$this->isProtected())&&$parentClassMethod->is(self::ACCESS_LEVEL_CHANGED|InternalReflectionMethod::IS_PRIVATE)){$this->modifiers|=self::ACCESS_LEVEL_CHANGED;}if($parentClassMethod->isAbstract()&&!$this->isAbstract()){$this->modifiers|=self::IS_IMPLEMENTED_ABSTRACT;}}else{foreach($declaringClass->getInterfaces()as$interface){if($interface->hasOwnMethod($this->name)){$this->modifiers|=self::IS_IMPLEMENTED_ABSTRACT;break;}}}$this->modifiersComplete=$this->isComplete()||(($this->modifiers&self::IS_IMPLEMENTED_ABSTRACT)&&($this->modifiers&self::ACCESS_LEVEL_CHANGED));}return$this->modifiers;}public
function isAbstract(){return (bool)($this->modifiers&InternalReflectionMethod::IS_ABSTRACT);}public
function isFinal(){return (bool)($this->modifiers&InternalReflectionMethod::IS_FINAL);}public
function isPrivate(){return (bool)($this->modifiers&InternalReflectionMethod::IS_PRIVATE);}public
function isProtected(){return (bool)($this->modifiers&InternalReflectionMethod::IS_PROTECTED);}public
function isPublic(){return (bool)($this->modifiers&InternalReflectionMethod::IS_PUBLIC);}public
function isStatic(){return (bool)($this->modifiers&InternalReflectionMethod::IS_STATIC);}public
function is($filter=null){static$computedModifiers=0x808;if(null===$filter||($this->modifiers&$filter)){return
true;}elseif(($filter&$computedModifiers)&&!$this->modifiersComplete){return (bool)($this->getModifiers()&$filter);}return
false;}public function isConstructor(){return (bool)($this->modifiers&self::IS_CONSTRUCTOR);}public
function isDestructor(){return (bool)($this->modifiers&self::IS_DESTRUCTOR);}public
function getPrototype(){if(null===$this->prototype){$prototype=null;$declaring=$this->getDeclaringClass();if(($parent=$declaring->getParentClass())&&$parent->hasMethod($this->name)){$method=$parent->getMethod($this->name);if(!$method->isPrivate()){try{$prototype=$method->getPrototype();}catch(\Exception$e){$prototype=$method;}}}if(null===$prototype){foreach($declaring->getOwnInterfaces()as$interface){if($interface->hasMethod($this->name)){$prototype=$interface->getMethod($this->name);break;}}}$this->prototype=$prototype?:($this->isComplete()?false:null);}if(empty($this->prototype)){throw
new Exception\Runtime(sprintf('Method "%s::%s()" has no prototype.',$this->declaringClassName,$this->name),Exception\Runtime::DOES_NOT_EXIST);}return$this->prototype;}public
function __toString(){$internal='';$overwrite='';$prototype='';$declaringClassParent=$this->getDeclaringClass()->getParentClass();try{$prototype=', prototype '.$this->getPrototype()->getDeclaringClassName();}catch(Exception$e){if($declaringClassParent&&$declaringClassParent->isInternal()){$internal='internal:'.$parentClass->getExtensionName();}}if($declaringClassParent&&$declaringClassParent->hasMethod($this->name)){$parentMethod=$declaringClassParent->getMethod($this->name);$overwrite=', overwrites '.$parentMethod->getDeclaringClassName();}if($this->isConstructor()){$cdtor=', ctor';}elseif($this->isDestructor()){$cdtor=', dtor';}else{$cdtor='';}$parameters='';if($this->getNumberOfParameters()>0){$buffer='';foreach($this->getParameters()as$parameter){$buffer.="\n    ".$parameter->__toString();}$parameters=sprintf("\n\n  - Parameters [%d] {%s\n  }",$this->getNumberOfParameters(),$buffer);}return
sprintf("%sMethod [ <%s%s%s%s> %s%s%s%s%s%s method %s%s ] {\n  @@ %s %d - %d%s\n}\n",$this->getDocComment()?$this->getDocComment()."\n":'',!empty($internal)?$internal:'user',$overwrite,$prototype,$cdtor,$this->isAbstract()?'abstract ':'',$this->isFinal()?'final ':'',$this->isStatic()?'static ':'',$this->isPublic()?'public':'',$this->isPrivate()?'private':'',$this->isProtected()?'protected':'',$this->returnsReference()?'&':'',$this->getName(),$this->getFileName(),$this->getStartLine(),$this->getEndLine(),$parameters);}public
static function export(Broker$broker,$class,$method,$return=false){$className=is_object($class)?get_class($class):$class;$methodName=$method;$class=$broker->getClass($className);if($class
instanceof Dummy\ReflectionClass){throw new Exception\Runtime(sprintf('Class %s does not exist.',$className),Exception\Runtime::DOES_NOT_EXIST);}$method=$class->getMethod($methodName);if($return){return$method->__toString();}echo$method->__toString();}public
function invoke($object,$args){$params=func_get_args();return$this->invokeArgs(array_shift($params),$params);}public
function invokeArgs($object,array$args=array()){try{$declaringClass=$this->getDeclaringClass();if(!$declaringClass->isInstance($object)){throw
new Exception\Runtime(sprintf('Invalid class, "%s" expected "%s" given.',$declaringClass->getName(),get_class($object)),Exception\Runtime::INVALID_ARGUMENT);}if($this->isPublic()){return
call_user_func_array(array($object,$this->getName()),$args);}elseif($this->isAccessible()){$refClass=new
InternalReflectionClass($object);$refMethod=$refClass->getMethod($this->name);$refMethod->setAccessible(true);$value=$refMethod->invokeArgs($object,$args);$refMethod->setAccessible(false);return$value;}throw
new Exception\Runtime('Only public methods can be invoked.',Exception\Runtime::NOT_ACCESSBILE);}catch(Exception\Runtime$e){throw
new Exception\Runtime(sprintf('Could not invoke method "%s::%s()".',$this->declaringClassName,$this->name),0,$e);}}public
function isAccessible(){return$this->accessible;}public function setAccessible($accessible){$this->accessible=(bool)$accessible;}private
function isComplete(){return$this->getDeclaringClass()->isComplete();}public function
getNamespaceAliases(){return$this->getDeclaringClass()->getNamespaceAliases();}protected
function processParent(IReflection$parent){if(!$parent instanceof ReflectionClass){throw
new Exception\Parse(sprintf('The parent object has to be an instance of TokenReflection\ReflectionClass, "%s" given.',get_class($parent)),Exception\Parse::INVALID_PARENT);}$this->declaringClassName=$parent->getName();return
parent::processParent($parent);}protected function parse(Stream$tokenStream,IReflection$parent){return$this->parseBaseModifiers($tokenStream)->parseReturnsReference($tokenStream)->parseName($tokenStream)->parseInternalModifiers($parent);}private
function parseBaseModifiers(Stream$tokenStream){try{while(true){switch($tokenStream->getType()){case
T_ABSTRACT:$this->modifiers|=InternalReflectionMethod::IS_ABSTRACT;break;case T_FINAL:$this->modifiers|=InternalReflectionMethod::IS_FINAL;break;case
T_PUBLIC:$this->modifiers|=InternalReflectionMethod::IS_PUBLIC;break;case T_PRIVATE:$this->modifiers|=InternalReflectionMethod::IS_PRIVATE;break;case
T_PROTECTED:$this->modifiers|=InternalReflectionMethod::IS_PROTECTED;break;case T_STATIC:$this->modifiers|=InternalReflectionMethod::IS_STATIC;break;case
T_FUNCTION:case null:break 2;default:break;}$tokenStream->skipWhitespaces();}if(!($this->modifiers&(InternalReflectionMethod::IS_PRIVATE|InternalReflectionMethod::IS_PROTECTED))){$this->modifiers|=InternalReflectionMethod::IS_PUBLIC;}return$this;}catch(Exception$e){throw
new Exception\Parse('Could not parse basic modifiers.',Exception\Parse::PARSE_ELEMENT_ERROR,$e);}}private
function parseInternalModifiers(ReflectionClass$class){$name=strtolower($this->name);if('__construct'===$name||((!$class->inNamespace()||PHP_VERSION_ID<50303)&&strtolower($class->getShortName())===$name)){$this->modifiers|=self::IS_CONSTRUCTOR;}elseif('__destruct'===$name){$this->modifiers|=self::IS_DESTRUCTOR;}elseif('__clone'===$name){$this->modifiers|=self::IS_CLONE;}if($class->isInterface()){$this->modifiers|=InternalReflectionMethod::IS_ABSTRACT;}else{static$notAllowed=array('__clone'=>true,'__tostring'=>true,'__get'=>true,'__set'=>true,'__isset'=>true,'__unset'=>true);if(!$this->isStatic()&&!$this->isConstructor()&&!$this->isDestructor()&&!isset($notAllowed[$name])){$this->modifiers|=self::IS_ALLOWED_STATIC;}}return$this;}}}

