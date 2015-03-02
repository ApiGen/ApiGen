<?php

namespace ApiGen\Tests\Charset;

use ApiGen\Charset\Configuration\CharsetOptionsResolver;
use ApiGen\Charset\Encoding;
use ApiGen\Tests\ContainerAwareTestCase;


class CharsetOptionsResolverTest extends ContainerAwareTestCase
{

	/**
	 * @var CharsetOptionsResolver
	 */
	private $charsetOptionsResolver;


	protected function setUp()
	{
		$this->charsetOptionsResolver = $this->container->getByType(CharsetOptionsResolver::class);
	}


	public function testResolve()
	{
		$resolvedOptions = $this->charsetOptionsResolver->resolve([]);
		$this->assertInternalType('array', $resolvedOptions);
		$this->assertNotEmpty($resolvedOptions);
	}


	public function testResolveWindows1250()
	{
		$resolvedOptions = $this->charsetOptionsResolver->resolve(['charsets' => [Encoding::WIN_1250]]);
		$this->assertNotContains(Encoding::WIN_1250, $resolvedOptions);
		$this->assertContains(Encoding::ISO_8859_1, $resolvedOptions);
		$this->assertNotContains('ISO-8859-2', $resolvedOptions);
	}


	public function testUppercase()
	{
		$resolvedOptions = $this->charsetOptionsResolver->resolve(['charsets' => ['iso-8859-15']]);
		$this->assertSame('ISO-8859-15', $resolvedOptions[1]);
	}


	public function testSupportedOnly()
	{
		$supportedEncodings = mb_list_encodings();
		$resolvedOptions = $this->charsetOptionsResolver->resolve(['charsets' => $supportedEncodings]);
		$this->assertSame(count($supportedEncodings), count($resolvedOptions));
	}


	public function testUtf8AlwaysFirst()
	{
		$resolvedOptions = $this->charsetOptionsResolver->resolve([]);
		$this->assertSame(Encoding::UTF_8, $resolvedOptions[0]);

		$resolvedOptions = $this->charsetOptionsResolver->resolve(['charsets' => ['ISO-8859-15']]);
		$this->assertSame(Encoding::UTF_8, $resolvedOptions[0]);
	}

}
