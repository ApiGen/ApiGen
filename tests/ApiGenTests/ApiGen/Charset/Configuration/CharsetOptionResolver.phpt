<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Charset;

use ApiGen\Charset\Configuration\CharsetOptionsResolver;
use ApiGen\Charset\Encoding;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../../bootstrap.php';


class CharsetOptionsResolverTest extends TestCase
{

	/**
	 * @var CharsetOptionsResolver
	 */
	private $charsetOptionsResolver;


	protected function setUp()
	{
		$container = createContainer();
		$this->charsetOptionsResolver = $container->getByType('ApiGen\Charset\Configuration\CharsetOptionsResolver');
	}


	public function testResolve()
	{
		$resolvedOptions = $this->charsetOptionsResolver->resolve([]);
		Assert::type('array', $resolvedOptions);
		Assert::false(empty($resolvedOptions));
	}


	public function testResolveWindows1250()
	{
		$resolvedOptions = $this->charsetOptionsResolver->resolve(['charsets' => [Encoding::WIN_1250]]);
		Assert::false(in_array(Encoding::WIN_1250, $resolvedOptions));
		Assert::true(in_array(Encoding::ISO_8859_1, $resolvedOptions));
		Assert::false(in_array(Encoding::ISO_8859_2, $resolvedOptions));
	}


	public function testUppercase()
	{
		$resolvedOptions = $this->charsetOptionsResolver->resolve(['charsets' => ['iso-8859-15']]);
		Assert::same(Encoding::ISO_8859_15, $resolvedOptions[1]);
	}


	public function testSupportedOnly()
	{
		$supportedEncodings = mb_list_encodings();
		$resolvedOptions = $this->charsetOptionsResolver->resolve(['charsets' => $supportedEncodings]);
		Assert::same(count($supportedEncodings), count($resolvedOptions));
	}


	public function testUtf8AlwaysFirst()
	{
		$resolvedOptions = $this->charsetOptionsResolver->resolve([]);
		Assert::same(Encoding::UTF_8, $resolvedOptions[0]);

		$resolvedOptions = $this->charsetOptionsResolver->resolve(['charsets' => [Encoding::ISO_8859_15]]);
		Assert::same(Encoding::UTF_8, $resolvedOptions[0]);
	}

}


(new CharsetOptionsResolverTest)->run();
