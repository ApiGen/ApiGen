<?php
/**
 *  @testCase
 */

namespace ApiGenTest\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGen\Templating\Filters;
use ApiGenTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class GroupsTest extends TestCase {
	const NAMESPACE_NAME = "Project";
	const PACKAGE_NAME = "Package-Group";
	
	private function prepareConfig( $groups = "" ) {
		$neonFile = new NeonFile( __DIR__ . '/apigen.neon' );
		$config = $neonFile->read();
		$config['source'] = array( PROJECT_DIR );
		$config['destination'] = API_DIR;
		
		if( $groups !== "" ) {
			$config['groups'] = $groups;
		}
			
		$neonFile->write( $config );
	}
	
	private function urlize( $format, $string ) {
		$string =  preg_replace( '~[^\w]~', '.', $string );
		return sprintf( $format, $string );
	}
	
	public function getLoopArgs() {
		return array(
			array( "Namespaces", "namespaces", "namespace-%s.html", self::NAMESPACE_NAME ),
			array( "Packages", "packages", "package-%s.html", self::PACKAGE_NAME ),
		);
	}
	
	/**
	 *  @dataProvider getLoopArgs
	 */
	public function testConfig( $header, $group, $format, $name ) {
		$this->prepareConfig( $group );
		passthru( APIGEN_BIN . ' generate' );
		$url = $this->urlize( $format, $name );
		$index_file = $this->getFileContentInOneLine( API_DIR . '/index.html' );
		
		Assert::true( file_exists( API_DIR . '/index.html' ));
		Assert::match(
			'%A%<h3>' . $header . '</h3>%A%',
			$index_file );
		Assert::match(
			'%A%<li><a href="' . $url . '">' . $name . '</a></li>%A%',
			$index_file );
		Assert::match(
			'%A%<td class="name"><a href="' . $url . '">' . $name . '</a></td>%A%',
			$index_file );
	}
}

\run( new GroupsTest );
