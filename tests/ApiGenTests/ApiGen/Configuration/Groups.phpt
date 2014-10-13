<?php
/**
 *  @testCase
 */

namespace ApiGenTest\ApiGen\Configuration;

use ApiGen\Neon\NeonFile;
use ApiGenTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class GroupsTest extends TestCase {
	const NAMESPACE_NAME = "Project";
	const GROUP_NAME = "Package-Group";
	
	// public function testConfigDefault() {
		/// @todo
	// }
	
	// public function testConfigAuto() {
		// $this->prepareConfig();
		// passthru( APIGEN_BIN . ' generate' );
	// }
	
	public function testConfigNamespaces() {
		$this->prepareConfig( 'namespaces' );
		passthru( APIGEN_BIN . ' generate' );
		
		Assert::true( file_exists( API_DIR . '/index.html' ));
		$index_file = $this->getFileContentInOneLine( API_DIR . '/index.html' );
		Assert::match( '%A%<h3>Namespaces</h3>%A%', $index_file );
		Assert::match( '%A%<li><a href="namespace-' . str_replace( '-', '.', self::NAMESPACE_NAME ) . '.html">' . self::NAMESPACE_NAME . '</a></li>%A%', $index_file );
	}
	
	public function testConfigPackages() {
		$this->prepareConfig( 'packages' );
		passthru( APIGEN_BIN . ' generate' );
		
		Assert::true( file_exists( API_DIR . '/index.html' ));
		$index_file = $this->getFileContentInOneLine( API_DIR . '/index.html' );
		Assert::match( '%A%<h3>Packages</h3>%A%', $index_file );
		Assert::match( '%A%<li><a href="package-' . str_replace( '-', '.', self::GROUP_NAME ) . '.html">' . self::GROUP_NAME . '</a></li>%A%', $index_file );
	}
	
	private function prepareConfig( $groups = "" ) {
		$neonFile = new NeonFile( __DIR__ . '/apigen.neon' );
		$config = $neonFile->read();
		$config['source'] = array( PROJECT_DIR );
		$config['destination'] = API_DIR;
		
		if( $groups !== "" ) {
			$config['groups'] = $groups;
		}
			
		$neonFile->write($config);
	}
}

\run( new GroupsTest );
