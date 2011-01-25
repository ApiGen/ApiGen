<?php
/* --------------------------------------------------------------- *
 *        WARNING: ALL CHANGES IN THIS FILE WILL BE LOST
 *
 *   Source language file: W:\fshl/lang/PHP_lang.php
 *       Language version: 1.29 (Sign:SHL)
 *
 *            Target file: W:\fshl/fshl_cache/PHP_lang.php
 *             Build date: Mon 24.1.2011 18:17:43
 *
 *      Generator version: 0.4.11
 * --------------------------------------------------------------- */
class PHP_lang
{
var $trans,$flags,$data,$delim,$class,$keywords;
var $version,$signature,$initial_state,$ret,$quit;
var $pt,$pti,$generator_version;
var $names;

function PHP_lang () {
	$this->version=1.29;
	$this->signature="SHL";
	$this->generator_version="0.4.11";
	$this->initial_state=0;
	$this->trans=array(0=>array(0=>array(0=>0,1=>0),1=>array(0=>5,1=>0),2=>array(0=>2,1=>-1),3=>array(0=>8,1=>0),4=>array(0=>7,1=>0),5=>array(0=>4,1=>0),6=>array(0=>9,1=>0),7=>array(0=>13,1=>0),8=>array(0=>3,1=>0),9=>array(0=>1,1=>-1),10=>array(0=>4,1=>0)),1=>array(0=>array(0=>12,1=>0),1=>array(0=>12,1=>0)),2=>array(0=>array(0=>12,1=>1)),3=>array(0=>array(0=>3,1=>0),1=>array(0=>12,1=>0)),4=>array(0=>array(0=>12,1=>0),1=>array(0=>4,1=>0),2=>array(0=>12,1=>-1)),5=>array(0=>array(0=>5,1=>0),1=>array(0=>5,1=>0),2=>array(0=>5,1=>0),3=>array(0=>12,1=>1)),6=>array(0=>array(0=>12,1=>0),1=>array(0=>12,1=>0)),7=>array(0=>array(0=>12,1=>0),1=>array(0=>7,1=>0),2=>array(0=>7,1=>0),3=>array(0=>5,1=>0),4=>array(0=>6,1=>0),5=>array(0=>7,1=>0)),8=>array(0=>array(0=>12,1=>0),1=>array(0=>8,1=>0),2=>array(0=>8,1=>0),3=>array(0=>8,1=>0)),9=>array(0=>array(0=>11,1=>0),1=>array(0=>12,1=>1),2=>array(0=>10,1=>0)),10=>array(0=>array(0=>12,1=>1)),11=>array(0=>array(0=>12,1=>1)),13=>null);
	$this->flags=array(0=>0,1=>4,2=>5,3=>4,4=>4,5=>4,6=>4,7=>4,8=>4,9=>4,10=>0,11=>0,13=>8);
	$this->delim=array(0=>array(0=>"_COUNTAB",1=>"\$",2=>"ALPHA",3=>"'",4=>"\"",5=>"//",6=>"NUMBER",7=>"?>",8=>"/*",9=>"<?",10=>"#"),1=>array(0=>"<?php",1=>"<?"),2=>array(0=>"!SAFECHAR"),3=>array(0=>"_COUNTAB",1=>"*/"),4=>array(0=>"\x0a",1=>"_COUNTAB",2=>"?>"),5=>array(0=>"\$",1=>"{",2=>"}",3=>"!SAFECHAR"),6=>array(0=>"}",1=>"SPACE"),7=>array(0=>"\"",1=>"\\\\",2=>"\\\"",3=>"\$",4=>"{\$",5=>"_COUNTAB"),8=>array(0=>"'",1=>"\\\\",2=>"\\'",3=>"_COUNTAB"),9=>array(0=>"x",1=>"!NUMBER",2=>"NUMBER"),10=>array(0=>"!NUMBER"),11=>array(0=>"!HEXNUM"),13=>null);
	$this->ret=12;
	$this->quit=13;
	$this->names=array(0=>"OUT",1=>"DUMMY_PHP",2=>"FUNCTION",3=>"COMMENT",4=>"COMMENT1",5=>"VAR",6=>"VAR_STR",7=>"QUOTE",8=>"QUOTE1",9=>"NUM",10=>"DEC_NUM",11=>"HEX_NUM",12=>"_RET",13=>"_QUIT");
	$this->data=array(0=>null,1=>null,2=>null,3=>null,4=>null,5=>null,6=>null,7=>null,8=>null,9=>null,10=>null,11=>null,13=>"");
	$this->class=array(0=>null,1=>"xlang",2=>null,3=>"php-comment",4=>"php-comment",5=>"php-var",6=>"php-var",7=>"php-quote",8=>"php-quote",9=>"php-num",10=>"php-num",11=>"php-num",13=>"xlang");
	$this->keywords=array(0=>"php-keyword",1=>array("abstract"=>1,"and"=>1,"array"=>1,"break"=>1,"case"=>1,"catch"=>1,"class"=>1,"clone"=>1,"const"=>1,"continue"=>1,"declare"=>1,"default"=>1,"do"=>1,"else"=>1,"elseif"=>1,"enddeclare"=>1,"endfor"=>1,"endforeach"=>1,"endif"=>1,"endswitch"=>1,"endwhile"=>1,"extends"=>1,"final"=>1,"for"=>1,"foreach"=>1,"function"=>1,"global"=>1,"goto"=>1,"if"=>1,"implements"=>1,"interface"=>1,"instanceof"=>1,"namespace"=>1,"new"=>1,"or"=>1,"private"=>1,"protected"=>1,"public"=>1,"static"=>1,"switch"=>1,"throw"=>1,"try"=>1,"use"=>1,"var"=>1,"while"=>1,"xor"=>1,"__CLASS__"=>1,"__DIR__"=>1,"__FILE__"=>1,"__FUNCTION__"=>1,"__METHOD__"=>1,"__NAMESPACE__"=>1,"die"=>1,"echo"=>1,"empty"=>1,"exit"=>1,"eval"=>1,"include"=>1,"include_once"=>1,"isset"=>1,"list"=>1,"require"=>1,"require_once"=>1,"return"=>1,"print"=>1,"unset"=>1,"true"=>1,"false"=>1,"null"=>1,"zend_version"=>2,"func_num_args"=>2,"func_get_arg"=>2,"func_get_args"=>2,"strlen"=>2,"strcmp"=>2,"strncmp"=>2,"strcasecmp"=>2,"strncasecmp"=>2,"each"=>2,"error_reporting"=>2,"define"=>2,"defined"=>2,"get_class"=>2,"get_called_class"=>2,"get_parent_class"=>2,"method_exists"=>2,"property_exists"=>2,"class_exists"=>2,"interface_exists"=>2,"function_exists"=>2,"class_alias"=>2,"get_included_files"=>2,"get_required_files"=>2,"is_subclass_of"=>2,"is_a"=>2,"get_class_vars"=>2,"get_object_vars"=>2,"get_class_methods"=>2,"trigger_error"=>2,"user_error"=>2,"set_error_handler"=>2,"restore_error_handler"=>2,"set_exception_handler"=>2,"restore_exception_handler"=>2,"get_declared_classes"=>2,"get_declared_interfaces"=>2,"get_defined_functions"=>2,"get_defined_vars"=>2,"create_function"=>2,"get_resource_type"=>2,"get_loaded_extensions"=>2,"extension_loaded"=>2,"get_extension_funcs"=>2,"get_defined_constants"=>2,"debug_backtrace"=>2,"debug_print_backtrace"=>2,"gc_collect_cycles"=>2,"gc_enabled"=>2,"gc_enable"=>2,"gc_disable"=>2,"bcadd"=>2,"bcsub"=>2,"bcmul"=>2,"bcdiv"=>2,"bcmod"=>2,"bcpow"=>2,"bcsqrt"=>2,"bcscale"=>2,"bccomp"=>2,"bcpowmod"=>2,"jdtogregorian"=>2,"gregoriantojd"=>2,"jdtojulian"=>2,"juliantojd"=>2,"jdtojewish"=>2,"jewishtojd"=>2,"jdtofrench"=>2,"frenchtojd"=>2,"jddayofweek"=>2,"jdmonthname"=>2,"easter_date"=>2,"easter_days"=>2,"unixtojd"=>2,"jdtounix"=>2,"cal_to_jd"=>2,"cal_from_jd"=>2,"cal_days_in_month"=>2,"cal_info"=>2,"variant_set"=>2,"variant_add"=>2,"variant_cat"=>2,"variant_sub"=>2,"variant_mul"=>2,"variant_and"=>2,"variant_div"=>2,"variant_eqv"=>2,"variant_idiv"=>2,"variant_imp"=>2,"variant_mod"=>2,"variant_or"=>2,"variant_pow"=>2,"variant_xor"=>2,"variant_abs"=>2,"variant_fix"=>2,"variant_int"=>2,"variant_neg"=>2,"variant_not"=>2,"variant_round"=>2,"variant_cmp"=>2,"variant_date_to_timestamp"=>2,"variant_date_from_timestamp"=>2,"variant_get_type"=>2,"variant_set_type"=>2,"variant_cast"=>2,"com_create_guid"=>2,"com_event_sink"=>2,"com_print_typeinfo"=>2,"com_message_pump"=>2,"com_load_typelib"=>2,"com_get_active_object"=>2,"ctype_alnum"=>2,"ctype_alpha"=>2,"ctype_cntrl"=>2,"ctype_digit"=>2,"ctype_lower"=>2,"ctype_graph"=>2,"ctype_print"=>2,"ctype_punct"=>2,"ctype_space"=>2,"ctype_upper"=>2,"ctype_xdigit"=>2,"strtotime"=>2,"date"=>2,"idate"=>2,"gmdate"=>2,"mktime"=>2,"gmmktime"=>2,"checkdate"=>2,"strftime"=>2,"gmstrftime"=>2,"time"=>2,"localtime"=>2,"getdate"=>2,"date_create"=>2,"date_create_from_format"=>2,"date_parse"=>2,"date_parse_from_format"=>2,"date_get_last_errors"=>2,"date_format"=>2,"date_modify"=>2,"date_add"=>2,"date_sub"=>2,"date_timezone_get"=>2,"date_timezone_set"=>2,"date_offset_get"=>2,"date_diff"=>2,"date_time_set"=>2,"date_date_set"=>2,"date_isodate_set"=>2,"date_timestamp_set"=>2,"date_timestamp_get"=>2,"timezone_open"=>2,"timezone_name_get"=>2,"timezone_name_from_abbr"=>2,"timezone_offset_get"=>2,"timezone_transitions_get"=>2,"timezone_location_get"=>2,"timezone_identifiers_list"=>2,"timezone_abbreviations_list"=>2,"timezone_version_get"=>2,"date_interval_create_from_date_string"=>2,"date_interval_format"=>2,"date_default_timezone_set"=>2,"date_default_timezone_get"=>2,"date_sunrise"=>2,"date_sunset"=>2,"date_sun_info"=>2,"ereg"=>2,"ereg_replace"=>2,"eregi"=>2,"eregi_replace"=>2,"split"=>2,"spliti"=>2,"sql_regcase"=>2,"filter_input"=>2,"filter_var"=>2,"filter_input_array"=>2,"filter_var_array"=>2,"filter_list"=>2,"filter_has_var"=>2,"filter_id"=>2,"ftp_connect"=>2,"ftp_login"=>2,"ftp_pwd"=>2,"ftp_cdup"=>2,"ftp_chdir"=>2,"ftp_exec"=>2,"ftp_raw"=>2,"ftp_mkdir"=>2,"ftp_rmdir"=>2,"ftp_chmod"=>2,"ftp_alloc"=>2,"ftp_nlist"=>2,"ftp_rawlist"=>2,"ftp_systype"=>2,"ftp_pasv"=>2,"ftp_get"=>2,"ftp_fget"=>2,"ftp_put"=>2,"ftp_fput"=>2,"ftp_size"=>2,"ftp_mdtm"=>2,"ftp_rename"=>2,"ftp_delete"=>2,"ftp_site"=>2,"ftp_close"=>2,"ftp_set_option"=>2,"ftp_get_option"=>2,"ftp_nb_fget"=>2,"ftp_nb_get"=>2,"ftp_nb_continue"=>2,"ftp_nb_put"=>2,"ftp_nb_fput"=>2,"ftp_quit"=>2,"hash"=>2,"hash_file"=>2,"hash_hmac"=>2,"hash_hmac_file"=>2,"hash_init"=>2,"hash_update"=>2,"hash_update_stream"=>2,"hash_update_file"=>2,"hash_final"=>2,"hash_copy"=>2,"hash_algos"=>2,"mhash_keygen_s2k"=>2,"mhash_get_block_size"=>2,"mhash_get_hash_name"=>2,"mhash_count"=>2,"mhash"=>2,"iconv"=>2,"ob_iconv_handler"=>2,"iconv_get_encoding"=>2,"iconv_set_encoding"=>2,"iconv_strlen"=>2,"iconv_substr"=>2,"iconv_strpos"=>2,"iconv_strrpos"=>2,"iconv_mime_encode"=>2,"iconv_mime_decode"=>2,"iconv_mime_decode_headers"=>2,"json_encode"=>2,"json_decode"=>2,"json_last_error"=>2,"mcrypt_ecb"=>2,"mcrypt_cbc"=>2,"mcrypt_cfb"=>2,"mcrypt_ofb"=>2,"mcrypt_get_key_size"=>2,"mcrypt_get_block_size"=>2,"mcrypt_get_cipher_name"=>2,"mcrypt_create_iv"=>2,"mcrypt_list_algorithms"=>2,"mcrypt_list_modes"=>2,"mcrypt_get_iv_size"=>2,"mcrypt_encrypt"=>2,"mcrypt_decrypt"=>2,"mcrypt_module_open"=>2,"mcrypt_generic_init"=>2,"mcrypt_generic"=>2,"mdecrypt_generic"=>2,"mcrypt_generic_end"=>2,"mcrypt_generic_deinit"=>2,"mcrypt_enc_self_test"=>2,"mcrypt_enc_is_block_algorithm_mode"=>2,"mcrypt_enc_is_block_algorithm"=>2,"mcrypt_enc_is_block_mode"=>2,"mcrypt_enc_get_block_size"=>2,"mcrypt_enc_get_key_size"=>2,"mcrypt_enc_get_supported_key_sizes"=>2,"mcrypt_enc_get_iv_size"=>2,"mcrypt_enc_get_algorithms_name"=>2,"mcrypt_enc_get_modes_name"=>2,"mcrypt_module_self_test"=>2,"mcrypt_module_is_block_algorithm_mode"=>2,"mcrypt_module_is_block_algorithm"=>2,"mcrypt_module_is_block_mode"=>2,"mcrypt_module_get_algo_block_size"=>2,"mcrypt_module_get_algo_key_size"=>2,"mcrypt_module_get_supported_key_sizes"=>2,"mcrypt_module_close"=>2,"odbc_autocommit"=>2,"odbc_binmode"=>2,"odbc_close"=>2,"odbc_close_all"=>2,"odbc_columns"=>2,"odbc_commit"=>2,"odbc_connect"=>2,"odbc_cursor"=>2,"odbc_data_source"=>2,"odbc_execute"=>2,"odbc_error"=>2,"odbc_errormsg"=>2,"odbc_exec"=>2,"odbc_fetch_array"=>2,"odbc_fetch_object"=>2,"odbc_fetch_row"=>2,"odbc_fetch_into"=>2,"odbc_field_len"=>2,"odbc_field_scale"=>2,"odbc_field_name"=>2,"odbc_field_type"=>2,"odbc_field_num"=>2,"odbc_free_result"=>2,"odbc_gettypeinfo"=>2,"odbc_longreadlen"=>2,"odbc_next_result"=>2,"odbc_num_fields"=>2,"odbc_num_rows"=>2,"odbc_pconnect"=>2,"odbc_prepare"=>2,"odbc_result"=>2,"odbc_result_all"=>2,"odbc_rollback"=>2,"odbc_setoption"=>2,"odbc_specialcolumns"=>2,"odbc_statistics"=>2,"odbc_tables"=>2,"odbc_primarykeys"=>2,"odbc_columnprivileges"=>2,"odbc_tableprivileges"=>2,"odbc_foreignkeys"=>2,"odbc_procedures"=>2,"odbc_procedurecolumns"=>2,"odbc_do"=>2,"odbc_field_precision"=>2,"preg_match"=>2,"preg_match_all"=>2,"preg_replace"=>2,"preg_replace_callback"=>2,"preg_filter"=>2,"preg_split"=>2,"preg_quote"=>2,"preg_grep"=>2,"preg_last_error"=>2,"session_name"=>2,"session_module_name"=>2,"session_save_path"=>2,"session_id"=>2,"session_regenerate_id"=>2,"session_decode"=>2,"session_register"=>2,"session_unregister"=>2,"session_is_registered"=>2,"session_encode"=>2,"session_start"=>2,"session_destroy"=>2,"session_unset"=>2,"session_set_save_handler"=>2,"session_cache_limiter"=>2,"session_cache_expire"=>2,"session_set_cookie_params"=>2,"session_get_cookie_params"=>2,"session_write_close"=>2,"session_commit"=>2,"spl_classes"=>2,"spl_autoload"=>2,"spl_autoload_extensions"=>2,"spl_autoload_register"=>2,"spl_autoload_unregister"=>2,"spl_autoload_functions"=>2,"spl_autoload_call"=>2,"class_parents"=>2,"class_implements"=>2,"spl_object_hash"=>2,"iterator_to_array"=>2,"iterator_count"=>2,"iterator_apply"=>2,"constant"=>2,"bin2hex"=>2,"sleep"=>2,"usleep"=>2,"time_nanosleep"=>2,"time_sleep_until"=>2,"flush"=>2,"wordwrap"=>2,"htmlspecialchars"=>2,"htmlentities"=>2,"html_entity_decode"=>2,"htmlspecialchars_decode"=>2,"get_html_translation_table"=>2,"sha1"=>2,"sha1_file"=>2,"md5"=>2,"md5_file"=>2,"crc32"=>2,"iptcparse"=>2,"iptcembed"=>2,"getimagesize"=>2,"image_type_to_mime_type"=>2,"image_type_to_extension"=>2,"phpinfo"=>2,"phpversion"=>2,"phpcredits"=>2,"php_logo_guid"=>2,"php_real_logo_guid"=>2,"php_egg_logo_guid"=>2,"zend_logo_guid"=>2,"php_sapi_name"=>2,"php_uname"=>2,"php_ini_scanned_files"=>2,"php_ini_loaded_file"=>2,"strnatcmp"=>2,"strnatcasecmp"=>2,"substr_count"=>2,"strspn"=>2,"strcspn"=>2,"strtok"=>2,"strtoupper"=>2,"strtolower"=>2,"strpos"=>2,"stripos"=>2,"strrpos"=>2,"strripos"=>2,"strrev"=>2,"hebrev"=>2,"hebrevc"=>2,"nl2br"=>2,"basename"=>2,"dirname"=>2,"pathinfo"=>2,"stripslashes"=>2,"stripcslashes"=>2,"strstr"=>2,"stristr"=>2,"strrchr"=>2,"str_shuffle"=>2,"str_word_count"=>2,"str_split"=>2,"strpbrk"=>2,"substr_compare"=>2,"strcoll"=>2,"substr"=>2,"substr_replace"=>2,"quotemeta"=>2,"ucfirst"=>2,"lcfirst"=>2,"ucwords"=>2,"strtr"=>2,"addslashes"=>2,"addcslashes"=>2,"rtrim"=>2,"str_replace"=>2,"str_ireplace"=>2,"str_repeat"=>2,"count_chars"=>2,"chunk_split"=>2,"trim"=>2,"ltrim"=>2,"strip_tags"=>2,"similar_text"=>2,"explode"=>2,"implode"=>2,"join"=>2,"setlocale"=>2,"localeconv"=>2,"soundex"=>2,"levenshtein"=>2,"chr"=>2,"ord"=>2,"parse_str"=>2,"str_getcsv"=>2,"str_pad"=>2,"chop"=>2,"strchr"=>2,"sprintf"=>2,"printf"=>2,"vprintf"=>2,"vsprintf"=>2,"fprintf"=>2,"vfprintf"=>2,"sscanf"=>2,"fscanf"=>2,"parse_url"=>2,"urlencode"=>2,"urldecode"=>2,"rawurlencode"=>2,"rawurldecode"=>2,"http_build_query"=>2,"readlink"=>2,"linkinfo"=>2,"symlink"=>2,"link"=>2,"unlink"=>2,"exec"=>2,"system"=>2,"escapeshellcmd"=>2,"escapeshellarg"=>2,"passthru"=>2,"shell_exec"=>2,"proc_open"=>2,"proc_close"=>2,"proc_terminate"=>2,"proc_get_status"=>2,"rand"=>2,"srand"=>2,"getrandmax"=>2,"mt_rand"=>2,"mt_srand"=>2,"mt_getrandmax"=>2,"getservbyname"=>2,"getservbyport"=>2,"getprotobyname"=>2,"getprotobynumber"=>2,"getmyuid"=>2,"getmygid"=>2,"getmypid"=>2,"getmyinode"=>2,"getlastmod"=>2,"base64_decode"=>2,"base64_encode"=>2,"convert_uuencode"=>2,"convert_uudecode"=>2,"abs"=>2,"ceil"=>2,"floor"=>2,"round"=>2,"sin"=>2,"cos"=>2,"tan"=>2,"asin"=>2,"acos"=>2,"atan"=>2,"atanh"=>2,"atan2"=>2,"sinh"=>2,"cosh"=>2,"tanh"=>2,"asinh"=>2,"acosh"=>2,"expm1"=>2,"log1p"=>2,"pi"=>2,"is_finite"=>2,"is_nan"=>2,"is_infinite"=>2,"pow"=>2,"exp"=>2,"log"=>2,"log10"=>2,"sqrt"=>2,"hypot"=>2,"deg2rad"=>2,"rad2deg"=>2,"bindec"=>2,"hexdec"=>2,"octdec"=>2,"decbin"=>2,"decoct"=>2,"dechex"=>2,"base_convert"=>2,"number_format"=>2,"fmod"=>2,"inet_ntop"=>2,"inet_pton"=>2,"ip2long"=>2,"long2ip"=>2,"getenv"=>2,"putenv"=>2,"getopt"=>2,"microtime"=>2,"gettimeofday"=>2,"uniqid"=>2,"quoted_printable_decode"=>2,"quoted_printable_encode"=>2,"convert_cyr_string"=>2,"get_current_user"=>2,"set_time_limit"=>2,"get_cfg_var"=>2,"magic_quotes_runtime"=>2,"set_magic_quotes_runtime"=>2,"get_magic_quotes_gpc"=>2,"get_magic_quotes_runtime"=>2,"import_request_variables"=>2,"error_log"=>2,"error_get_last"=>2,"call_user_func"=>2,"call_user_func_array"=>2,"call_user_method"=>2,"call_user_method_array"=>2,"forward_static_call"=>2,"forward_static_call_array"=>2,"serialize"=>2,"unserialize"=>2,"var_dump"=>2,"var_export"=>2,"debug_zval_dump"=>2,"print_r"=>2,"memory_get_usage"=>2,"memory_get_peak_usage"=>2,"register_shutdown_function"=>2,"register_tick_function"=>2,"unregister_tick_function"=>2,"highlight_file"=>2,"show_source"=>2,"highlight_string"=>2,"php_strip_whitespace"=>2,"ini_get"=>2,"ini_get_all"=>2,"ini_set"=>2,"ini_alter"=>2,"ini_restore"=>2,"get_include_path"=>2,"set_include_path"=>2,"restore_include_path"=>2,"setcookie"=>2,"setrawcookie"=>2,"header"=>2,"header_remove"=>2,"headers_sent"=>2,"headers_list"=>2,"connection_aborted"=>2,"connection_status"=>2,"ignore_user_abort"=>2,"parse_ini_file"=>2,"parse_ini_string"=>2,"is_uploaded_file"=>2,"move_uploaded_file"=>2,"gethostbyaddr"=>2,"gethostbyname"=>2,"gethostbynamel"=>2,"gethostname"=>2,"dns_check_record"=>2,"checkdnsrr"=>2,"dns_get_mx"=>2,"getmxrr"=>2,"dns_get_record"=>2,"intval"=>2,"floatval"=>2,"doubleval"=>2,"strval"=>2,"gettype"=>2,"settype"=>2,"is_null"=>2,"is_resource"=>2,"is_bool"=>2,"is_long"=>2,"is_float"=>2,"is_int"=>2,"is_integer"=>2,"is_double"=>2,"is_real"=>2,"is_numeric"=>2,"is_string"=>2,"is_array"=>2,"is_object"=>2,"is_scalar"=>2,"is_callable"=>2,"pclose"=>2,"popen"=>2,"readfile"=>2,"rewind"=>2,"rmdir"=>2,"umask"=>2,"fclose"=>2,"feof"=>2,"fgetc"=>2,"fgets"=>2,"fgetss"=>2,"fread"=>2,"fopen"=>2,"fpassthru"=>2,"ftruncate"=>2,"fstat"=>2,"fseek"=>2,"ftell"=>2,"fflush"=>2,"fwrite"=>2,"fputs"=>2,"mkdir"=>2,"rename"=>2,"copy"=>2,"tempnam"=>2,"tmpfile"=>2,"file"=>2,"file_get_contents"=>2,"file_put_contents"=>2,"stream_select"=>2,"stream_context_create"=>2,"stream_context_set_params"=>2,"stream_context_get_params"=>2,"stream_context_set_option"=>2,"stream_context_get_options"=>2,"stream_context_get_default"=>2,"stream_context_set_default"=>2,"stream_filter_prepend"=>2,"stream_filter_append"=>2,"stream_filter_remove"=>2,"stream_socket_client"=>2,"stream_socket_server"=>2,"stream_socket_accept"=>2,"stream_socket_get_name"=>2,"stream_socket_recvfrom"=>2,"stream_socket_sendto"=>2,"stream_socket_enable_crypto"=>2,"stream_socket_shutdown"=>2,"stream_socket_pair"=>2,"stream_copy_to_stream"=>2,"stream_get_contents"=>2,"stream_supports_lock"=>2,"fgetcsv"=>2,"fputcsv"=>2,"flock"=>2,"get_meta_tags"=>2,"stream_set_read_buffer"=>2,"stream_set_write_buffer"=>2,"set_file_buffer"=>2,"set_socket_blocking"=>2,"stream_set_blocking"=>2,"socket_set_blocking"=>2,"stream_get_meta_data"=>2,"stream_get_line"=>2,"stream_wrapper_register"=>2,"stream_register_wrapper"=>2,"stream_wrapper_unregister"=>2,"stream_wrapper_restore"=>2,"stream_get_wrappers"=>2,"stream_get_transports"=>2,"stream_resolve_include_path"=>2,"stream_is_local"=>2,"get_headers"=>2,"stream_set_timeout"=>2,"socket_set_timeout"=>2,"socket_get_status"=>2,"realpath"=>2,"fnmatch"=>2,"fsockopen"=>2,"pfsockopen"=>2,"pack"=>2,"unpack"=>2,"get_browser"=>2,"crypt"=>2,"opendir"=>2,"closedir"=>2,"chdir"=>2,"getcwd"=>2,"rewinddir"=>2,"readdir"=>2,"dir"=>2,"scandir"=>2,"glob"=>2,"fileatime"=>2,"filectime"=>2,"filegroup"=>2,"fileinode"=>2,"filemtime"=>2,"fileowner"=>2,"fileperms"=>2,"filesize"=>2,"filetype"=>2,"file_exists"=>2,"is_writable"=>2,"is_writeable"=>2,"is_readable"=>2,"is_executable"=>2,"is_file"=>2,"is_dir"=>2,"is_link"=>2,"stat"=>2,"lstat"=>2,"chown"=>2,"chgrp"=>2,"chmod"=>2,"touch"=>2,"clearstatcache"=>2,"disk_total_space"=>2,"disk_free_space"=>2,"diskfreespace"=>2,"realpath_cache_size"=>2,"realpath_cache_get"=>2,"mail"=>2,"ezmlm_hash"=>2,"openlog"=>2,"syslog"=>2,"closelog"=>2,"define_syslog_variables"=>2,"lcg_value"=>2,"metaphone"=>2,"ob_start"=>2,"ob_flush"=>2,"ob_clean"=>2,"ob_end_flush"=>2,"ob_end_clean"=>2,"ob_get_flush"=>2,"ob_get_clean"=>2,"ob_get_length"=>2,"ob_get_level"=>2,"ob_get_status"=>2,"ob_get_contents"=>2,"ob_implicit_flush"=>2,"ob_list_handlers"=>2,"ksort"=>2,"krsort"=>2,"natsort"=>2,"natcasesort"=>2,"asort"=>2,"arsort"=>2,"sort"=>2,"rsort"=>2,"usort"=>2,"uasort"=>2,"uksort"=>2,"shuffle"=>2,"array_walk"=>2,"array_walk_recursive"=>2,"count"=>2,"end"=>2,"prev"=>2,"next"=>2,"reset"=>2,"current"=>2,"key"=>2,"min"=>2,"max"=>2,"in_array"=>2,"array_search"=>2,"extract"=>2,"compact"=>2,"array_fill"=>2,"array_fill_keys"=>2,"range"=>2,"array_multisort"=>2,"array_push"=>2,"array_pop"=>2,"array_shift"=>2,"array_unshift"=>2,"array_splice"=>2,"array_slice"=>2,"array_merge"=>2,"array_merge_recursive"=>2,"array_replace"=>2,"array_replace_recursive"=>2,"array_keys"=>2,"array_values"=>2,"array_count_values"=>2,"array_reverse"=>2,"array_reduce"=>2,"array_pad"=>2,"array_flip"=>2,"array_change_key_case"=>2,"array_rand"=>2,"array_unique"=>2,"array_intersect"=>2,"array_intersect_key"=>2,"array_intersect_ukey"=>2,"array_uintersect"=>2,"array_intersect_assoc"=>2,"array_uintersect_assoc"=>2,"array_intersect_uassoc"=>2,"array_uintersect_uassoc"=>2,"array_diff"=>2,"array_diff_key"=>2,"array_diff_ukey"=>2,"array_udiff"=>2,"array_diff_assoc"=>2,"array_udiff_assoc"=>2,"array_diff_uassoc"=>2,"array_udiff_uassoc"=>2,"array_sum"=>2,"array_product"=>2,"array_filter"=>2,"array_map"=>2,"array_chunk"=>2,"array_combine"=>2,"array_key_exists"=>2,"pos"=>2,"sizeof"=>2,"key_exists"=>2,"assert"=>2,"assert_options"=>2,"version_compare"=>2,"str_rot13"=>2,"stream_get_filters"=>2,"stream_filter_register"=>2,"stream_bucket_make_writeable"=>2,"stream_bucket_prepend"=>2,"stream_bucket_append"=>2,"stream_bucket_new"=>2,"output_add_rewrite_var"=>2,"output_reset_rewrite_vars"=>2,"sys_get_temp_dir"=>2,"token_get_all"=>2,"token_name"=>2,"zip_open"=>2,"zip_close"=>2,"zip_read"=>2,"zip_entry_open"=>2,"zip_entry_close"=>2,"zip_entry_read"=>2,"zip_entry_filesize"=>2,"zip_entry_name"=>2,"zip_entry_compressedsize"=>2,"zip_entry_compressionmethod"=>2,"readgzfile"=>2,"gzrewind"=>2,"gzclose"=>2,"gzeof"=>2,"gzgetc"=>2,"gzgets"=>2,"gzgetss"=>2,"gzread"=>2,"gzopen"=>2,"gzpassthru"=>2,"gzseek"=>2,"gztell"=>2,"gzwrite"=>2,"gzputs"=>2,"gzfile"=>2,"gzcompress"=>2,"gzuncompress"=>2,"gzdeflate"=>2,"gzinflate"=>2,"gzencode"=>2,"ob_gzhandler"=>2,"zlib_get_coding_type"=>2,"libxml_set_streams_context"=>2,"libxml_use_internal_errors"=>2,"libxml_get_last_error"=>2,"libxml_clear_errors"=>2,"libxml_get_errors"=>2,"libxml_disable_entity_loader"=>2,"dom_import_simplexml"=>2,"pdo_drivers"=>2,"simplexml_load_file"=>2,"simplexml_load_string"=>2,"simplexml_import_dom"=>2,"wddx_serialize_value"=>2,"wddx_serialize_vars"=>2,"wddx_packet_start"=>2,"wddx_packet_end"=>2,"wddx_add_vars"=>2,"wddx_deserialize"=>2,"xml_parser_create"=>2,"xml_parser_create_ns"=>2,"xml_set_object"=>2,"xml_set_element_handler"=>2,"xml_set_character_data_handler"=>2,"xml_set_processing_instruction_handler"=>2,"xml_set_default_handler"=>2,"xml_set_unparsed_entity_decl_handler"=>2,"xml_set_notation_decl_handler"=>2,"xml_set_external_entity_ref_handler"=>2,"xml_set_start_namespace_decl_handler"=>2,"xml_set_end_namespace_decl_handler"=>2,"xml_parse"=>2,"xml_parse_into_struct"=>2,"xml_get_error_code"=>2,"xml_error_string"=>2,"xml_get_current_line_number"=>2,"xml_get_current_column_number"=>2,"xml_get_current_byte_index"=>2,"xml_parser_free"=>2,"xml_parser_set_option"=>2,"xml_parser_get_option"=>2,"utf8_encode"=>2,"utf8_decode"=>2,"xmlwriter_open_uri"=>2,"xmlwriter_open_memory"=>2,"xmlwriter_set_indent"=>2,"xmlwriter_set_indent_string"=>2,"xmlwriter_start_comment"=>2,"xmlwriter_end_comment"=>2,"xmlwriter_start_attribute"=>2,"xmlwriter_end_attribute"=>2,"xmlwriter_write_attribute"=>2,"xmlwriter_start_attribute_ns"=>2,"xmlwriter_write_attribute_ns"=>2,"xmlwriter_start_element"=>2,"xmlwriter_end_element"=>2,"xmlwriter_full_end_element"=>2,"xmlwriter_start_element_ns"=>2,"xmlwriter_write_element"=>2,"xmlwriter_write_element_ns"=>2,"xmlwriter_start_pi"=>2,"xmlwriter_end_pi"=>2,"xmlwriter_write_pi"=>2,"xmlwriter_start_cdata"=>2,"xmlwriter_end_cdata"=>2,"xmlwriter_write_cdata"=>2,"xmlwriter_text"=>2,"xmlwriter_write_raw"=>2,"xmlwriter_start_document"=>2,"xmlwriter_end_document"=>2,"xmlwriter_write_comment"=>2,"xmlwriter_start_dtd"=>2,"xmlwriter_end_dtd"=>2,"xmlwriter_write_dtd"=>2,"xmlwriter_start_dtd_element"=>2,"xmlwriter_end_dtd_element"=>2,"xmlwriter_write_dtd_element"=>2,"xmlwriter_start_dtd_attlist"=>2,"xmlwriter_end_dtd_attlist"=>2,"xmlwriter_write_dtd_attlist"=>2,"xmlwriter_start_dtd_entity"=>2,"xmlwriter_end_dtd_entity"=>2,"xmlwriter_write_dtd_entity"=>2,"xmlwriter_output_memory"=>2,"xmlwriter_flush"=>2,"apache_lookup_uri"=>2,"virtual"=>2,"apache_request_headers"=>2,"apache_response_headers"=>2,"apache_setenv"=>2,"apache_getenv"=>2,"apache_note"=>2,"apache_get_version"=>2,"apache_get_modules"=>2,"getallheaders"=>2,"mb_convert_case"=>2,"mb_strtoupper"=>2,"mb_strtolower"=>2,"mb_language"=>2,"mb_internal_encoding"=>2,"mb_http_input"=>2,"mb_http_output"=>2,"mb_detect_order"=>2,"mb_substitute_character"=>2,"mb_parse_str"=>2,"mb_output_handler"=>2,"mb_preferred_mime_name"=>2,"mb_strlen"=>2,"mb_strpos"=>2,"mb_strrpos"=>2,"mb_stripos"=>2,"mb_strripos"=>2,"mb_strstr"=>2,"mb_strrchr"=>2,"mb_stristr"=>2,"mb_strrichr"=>2,"mb_substr_count"=>2,"mb_substr"=>2,"mb_strcut"=>2,"mb_strwidth"=>2,"mb_strimwidth"=>2,"mb_convert_encoding"=>2,"mb_detect_encoding"=>2,"mb_list_encodings"=>2,"mb_encoding_aliases"=>2,"mb_convert_kana"=>2,"mb_encode_mimeheader"=>2,"mb_decode_mimeheader"=>2,"mb_convert_variables"=>2,"mb_encode_numericentity"=>2,"mb_decode_numericentity"=>2,"mb_send_mail"=>2,"mb_get_info"=>2,"mb_check_encoding"=>2,"mb_regex_encoding"=>2,"mb_regex_set_options"=>2,"mb_ereg"=>2,"mb_eregi"=>2,"mb_ereg_replace"=>2,"mb_eregi_replace"=>2,"mb_split"=>2,"mb_ereg_match"=>2,"mb_ereg_search"=>2,"mb_ereg_search_pos"=>2,"mb_ereg_search_regs"=>2,"mb_ereg_search_init"=>2,"mb_ereg_search_getregs"=>2,"mb_ereg_search_getpos"=>2,"mb_ereg_search_setpos"=>2,"mbregex_encoding"=>2,"mbereg"=>2,"mberegi"=>2,"mbereg_replace"=>2,"mberegi_replace"=>2,"mbsplit"=>2,"mbereg_match"=>2,"mbereg_search"=>2,"mbereg_search_pos"=>2,"mbereg_search_regs"=>2,"mbereg_search_init"=>2,"mbereg_search_getregs"=>2,"mbereg_search_getpos"=>2,"mbereg_search_setpos"=>2,"finfo_open"=>2,"finfo_close"=>2,"finfo_set_flags"=>2,"finfo_file"=>2,"finfo_buffer"=>2,"mime_content_type"=>2,"gd_info"=>2,"imagearc"=>2,"imageellipse"=>2,"imagechar"=>2,"imagecharup"=>2,"imagecolorat"=>2,"imagecolorallocate"=>2,"imagepalettecopy"=>2,"imagecreatefromstring"=>2,"imagecolorclosest"=>2,"imagecolorclosesthwb"=>2,"imagecolordeallocate"=>2,"imagecolorresolve"=>2,"imagecolorexact"=>2,"imagecolorset"=>2,"imagecolortransparent"=>2,"imagecolorstotal"=>2,"imagecolorsforindex"=>2,"imagecopy"=>2,"imagecopymerge"=>2,"imagecopymergegray"=>2,"imagecopyresized"=>2,"imagecreate"=>2,"imagecreatetruecolor"=>2,"imageistruecolor"=>2,"imagetruecolortopalette"=>2,"imagesetthickness"=>2,"imagefilledarc"=>2,"imagefilledellipse"=>2,"imagealphablending"=>2,"imagesavealpha"=>2,"imagecolorallocatealpha"=>2,"imagecolorresolvealpha"=>2,"imagecolorclosestalpha"=>2,"imagecolorexactalpha"=>2,"imagecopyresampled"=>2,"imagegrabwindow"=>2,"imagegrabscreen"=>2,"imagerotate"=>2,"imageantialias"=>2,"imagesettile"=>2,"imagesetbrush"=>2,"imagesetstyle"=>2,"imagecreatefrompng"=>2,"imagecreatefromgif"=>2,"imagecreatefromjpeg"=>2,"imagecreatefromwbmp"=>2,"imagecreatefromxbm"=>2,"imagecreatefromgd"=>2,"imagecreatefromgd2"=>2,"imagecreatefromgd2part"=>2,"imagepng"=>2,"imagegif"=>2,"imagejpeg"=>2,"imagewbmp"=>2,"imagegd"=>2,"imagegd2"=>2,"imagedestroy"=>2,"imagegammacorrect"=>2,"imagefill"=>2,"imagefilledpolygon"=>2,"imagefilledrectangle"=>2,"imagefilltoborder"=>2,"imagefontwidth"=>2,"imagefontheight"=>2,"imageinterlace"=>2,"imageline"=>2,"imageloadfont"=>2,"imagepolygon"=>2,"imagerectangle"=>2,"imagesetpixel"=>2,"imagestring"=>2,"imagestringup"=>2,"imagesx"=>2,"imagesy"=>2,"imagedashedline"=>2,"imagettfbbox"=>2,"imagettftext"=>2,"imageftbbox"=>2,"imagefttext"=>2,"imagetypes"=>2,"jpeg2wbmp"=>2,"png2wbmp"=>2,"image2wbmp"=>2,"imagelayereffect"=>2,"imagexbm"=>2,"imagecolormatch"=>2,"imagefilter"=>2,"imageconvolution"=>2,"mysql_connect"=>2,"mysql_pconnect"=>2,"mysql_close"=>2,"mysql_select_db"=>2,"mysql_query"=>2,"mysql_unbuffered_query"=>2,"mysql_db_query"=>2,"mysql_list_dbs"=>2,"mysql_list_tables"=>2,"mysql_list_fields"=>2,"mysql_list_processes"=>2,"mysql_error"=>2,"mysql_errno"=>2,"mysql_affected_rows"=>2,"mysql_insert_id"=>2,"mysql_result"=>2,"mysql_num_rows"=>2,"mysql_num_fields"=>2,"mysql_fetch_row"=>2,"mysql_fetch_array"=>2,"mysql_fetch_assoc"=>2,"mysql_fetch_object"=>2,"mysql_data_seek"=>2,"mysql_fetch_lengths"=>2,"mysql_fetch_field"=>2,"mysql_field_seek"=>2,"mysql_free_result"=>2,"mysql_field_name"=>2,"mysql_field_table"=>2,"mysql_field_len"=>2,"mysql_field_type"=>2,"mysql_field_flags"=>2,"mysql_escape_string"=>2,"mysql_real_escape_string"=>2,"mysql_stat"=>2,"mysql_thread_id"=>2,"mysql_client_encoding"=>2,"mysql_ping"=>2,"mysql_get_client_info"=>2,"mysql_get_host_info"=>2,"mysql_get_proto_info"=>2,"mysql_get_server_info"=>2,"mysql_info"=>2,"mysql_set_charset"=>2,"mysql"=>2,"mysql_fieldname"=>2,"mysql_fieldtable"=>2,"mysql_fieldlen"=>2,"mysql_fieldtype"=>2,"mysql_fieldflags"=>2,"mysql_selectdb"=>2,"mysql_freeresult"=>2,"mysql_numfields"=>2,"mysql_numrows"=>2,"mysql_listdbs"=>2,"mysql_listtables"=>2,"mysql_listfields"=>2,"mysql_db_name"=>2,"mysql_dbname"=>2,"mysql_tablename"=>2,"mysql_table_name"=>2,"openssl_pkey_free"=>2,"openssl_pkey_new"=>2,"openssl_pkey_export"=>2,"openssl_pkey_export_to_file"=>2,"openssl_pkey_get_private"=>2,"openssl_pkey_get_public"=>2,"openssl_pkey_get_details"=>2,"openssl_free_key"=>2,"openssl_get_privatekey"=>2,"openssl_get_publickey"=>2,"openssl_x509_read"=>2,"openssl_x509_free"=>2,"openssl_x509_parse"=>2,"openssl_x509_checkpurpose"=>2,"openssl_x509_check_private_key"=>2,"openssl_x509_export"=>2,"openssl_x509_export_to_file"=>2,"openssl_pkcs12_export"=>2,"openssl_pkcs12_export_to_file"=>2,"openssl_pkcs12_read"=>2,"openssl_csr_new"=>2,"openssl_csr_export"=>2,"openssl_csr_export_to_file"=>2,"openssl_csr_sign"=>2,"openssl_csr_get_subject"=>2,"openssl_csr_get_public_key"=>2,"openssl_digest"=>2,"openssl_encrypt"=>2,"openssl_decrypt"=>2,"openssl_cipher_iv_length"=>2,"openssl_sign"=>2,"openssl_verify"=>2,"openssl_seal"=>2,"openssl_open"=>2,"openssl_pkcs7_verify"=>2,"openssl_pkcs7_decrypt"=>2,"openssl_pkcs7_sign"=>2,"openssl_pkcs7_encrypt"=>2,"openssl_private_encrypt"=>2,"openssl_private_decrypt"=>2,"openssl_public_encrypt"=>2,"openssl_public_decrypt"=>2,"openssl_get_md_methods"=>2,"openssl_get_cipher_methods"=>2,"openssl_dh_compute_key"=>2,"openssl_random_pseudo_bytes"=>2,"openssl_error_string"=>2,"sqlite_open"=>2,"sqlite_popen"=>2,"sqlite_close"=>2,"sqlite_query"=>2,"sqlite_exec"=>2,"sqlite_array_query"=>2,"sqlite_single_query"=>2,"sqlite_fetch_array"=>2,"sqlite_fetch_object"=>2,"sqlite_fetch_single"=>2,"sqlite_fetch_string"=>2,"sqlite_fetch_all"=>2,"sqlite_current"=>2,"sqlite_column"=>2,"sqlite_libversion"=>2,"sqlite_libencoding"=>2,"sqlite_changes"=>2,"sqlite_last_insert_rowid"=>2,"sqlite_num_rows"=>2,"sqlite_num_fields"=>2,"sqlite_field_name"=>2,"sqlite_seek"=>2,"sqlite_rewind"=>2,"sqlite_next"=>2,"sqlite_prev"=>2,"sqlite_valid"=>2,"sqlite_has_more"=>2,"sqlite_has_prev"=>2,"sqlite_escape_string"=>2,"sqlite_busy_timeout"=>2,"sqlite_last_error"=>2,"sqlite_error_string"=>2,"sqlite_unbuffered_query"=>2,"sqlite_create_aggregate"=>2,"sqlite_create_function"=>2,"sqlite_factory"=>2,"sqlite_udf_encode_binary"=>2,"sqlite_udf_decode_binary"=>2,"sqlite_fetch_column_types"=>2,"tidy_getopt"=>2,"tidy_parse_string"=>2,"tidy_parse_file"=>2,"tidy_get_output"=>2,"tidy_get_error_buffer"=>2,"tidy_clean_repair"=>2,"tidy_repair_string"=>2,"tidy_repair_file"=>2,"tidy_diagnose"=>2,"tidy_get_release"=>2,"tidy_get_config"=>2,"tidy_get_status"=>2,"tidy_get_html_ver"=>2,"tidy_is_xhtml"=>2,"tidy_is_xml"=>2,"tidy_error_count"=>2,"tidy_warning_count"=>2,"tidy_access_count"=>2,"tidy_config_count"=>2,"tidy_get_root"=>2,"tidy_get_head"=>2,"tidy_get_html"=>2,"tidy_get_body"=>2,"ob_tidyhandler"=>2,"debugbreak"=>2,"outputdebugstring"=>2,"dbg_get_loaded_zendextensions"=>2,"dbg_get_profiler_results"=>2,"dbg_get_all_module_names"=>2,"dbg_get_module_name"=>2,"dbg_get_all_contexts"=>2,"dbg_get_context_name"=>2,"dbg_get_all_source_lines"=>2,"dbg_get_source_context"=>2),2=>false);
}

// OUT
function getw0 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if(($c1=="\t"||$c1=="\n")){
			return array(0,$c1,$o,1,$i-$start);
		}
		if($c1=="\$"){
			return array(1,"\$",$o,1,$i-$start);
		}
		if(ctype_alpha($c1)){
			return array(2,$c1,$o,1,$i-$start);
		}
		if($c1=="'"){
			return array(3,"'",$o,1,$i-$start);
		}
		if($c1=="\""){
			return array(4,"\"",$o,1,$i-$start);
		}
		if($c2=="//"){
			return array(5,"//",$o,2,$i-$start);
		}
		if(ctype_digit($c1)){
			return array(6,$c1,$o,1,$i-$start);
		}
		if($c2=="?>"){
			return array(7,"?>",$o,2,$i-$start);
		}
		if($c2=="/*"){
			return array(8,"/*",$o,2,$i-$start);
		}
		if($c2=="<?"){
			return array(9,"<?",$o,2,$i-$start);
		}
		if($c1=="#"){
			return array(10,"#",$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// DUMMY_PHP
function getw1 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p++];
		$c3=$c2.$s[$p++];
		$c4=$c3.$s[$p++];
		$c5=$c4.$s[$p];
		if($c5=="<?php"){
			return array(0,"<?php",$o,5,$i-$start);
		}
		if($c2=="<?"){
			return array(1,"<?",$o,2,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// FUNCTION
function getw2 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if(!($c1=='_'||ctype_alnum($c1))){
			return array(0,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// COMMENT
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
		if($c2=="*/"){
			return array(1,"*/",$o,2,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// COMMENT1
function getw4 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if($c1=="\x0a"){
			return array(0,"\x0a",$o,1,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(1,$c1,$o,1,$i-$start);
		}
		if($c2=="?>"){
			return array(2,"?>",$o,2,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// VAR
function getw5 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1=="\$"){
			return array(0,"\$",$o,1,$i-$start);
		}
		if($c1=="{"){
			return array(1,"{",$o,1,$i-$start);
		}
		if($c1=="}"){
			return array(2,"}",$o,1,$i-$start);
		}
		if(!($c1=='_'||ctype_alnum($c1))){
			return array(3,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// VAR_STR
function getw6 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1=="}"){
			return array(0,"}",$o,1,$i-$start);
		}
		if(ctype_space($c1)){
			return array(1,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// QUOTE
function getw7 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$p=$i;
		$c1=$s[$p++];
		$c2=$c1.$s[$p];
		if($c1=="\""){
			return array(0,"\"",$o,1,$i-$start);
		}
		if($c2=="\\\\"){
			return array(1,"\\\\",$o,2,$i-$start);
		}
		if($c2=="\\\""){
			return array(2,"\\\"",$o,2,$i-$start);
		}
		if($c1=="\$"){
			return array(3,"\$",$o,1,$i-$start);
		}
		if($c2=="{\$"){
			return array(4,"{\$",$o,2,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(5,$c1,$o,1,$i-$start);
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
		$c2=$c1.$s[$p];
		if($c1=="'"){
			return array(0,"'",$o,1,$i-$start);
		}
		if($c2=="\\\\"){
			return array(1,"\\\\",$o,2,$i-$start);
		}
		if($c2=="\\'"){
			return array(2,"\\'",$o,2,$i-$start);
		}
		if(($c1=="\t"||$c1=="\n")){
			return array(3,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// NUM
function getw9 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if($c1=="x"){
			return array(0,"x",$o,1,$i-$start);
		}
		if(!ctype_digit($c1)){
			return array(1,$c1,$o,1,$i-$start);
		}
		if(ctype_digit($c1)){
			return array(2,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// DEC_NUM
function getw10 (&$s, $i, $l) {
	$o = false;
	$start = $i;
	while($i<$l) {
		$c1=$s[$i];
		if(!ctype_digit($c1)){
			return array(0,$c1,$o,1,$i-$start);
		}
		$o.=$c1;
		$i++;
	}
	return array(-1,-1,$o,-1,-1);
}

// HEX_NUM
function getw11 (&$s, $i, $l) {
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

}
