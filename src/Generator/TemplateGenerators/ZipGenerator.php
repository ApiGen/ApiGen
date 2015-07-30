<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Generator\TemplateGenerators\ConditionalTemplateGeneratorInterface;
use ApiGen\Utils\ZipArchiveGenerator;

class ZipGenerator implements ConditionalTemplateGeneratorInterface
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ZipArchiveGenerator
     */
    private $zipArchiveGenerator;


    public function __construct(Configuration $configuration, ZipArchiveGenerator $zipArchiveGenerator)
    {
        $this->configuration = $configuration;
        $this->zipArchiveGenerator = $zipArchiveGenerator;
    }


    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $destination = $this->configuration->getOption(CO::DESTINATION);
        $zipFile = $destination . '/' . $this->configuration->getZipFileName();
        $this->zipArchiveGenerator->zipDirToFile($destination, $zipFile);
    }


    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return (bool) $this->configuration->getOption(CO::DOWNLOAD);
    }
}
