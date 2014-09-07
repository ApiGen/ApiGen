<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\ConfigException;
use ApiGen\Generator;
use Nette;
use Nette\Utils\Validators;


class Validator extends Nette\Object
{

	/**
	 * @param array $config
	 */
	public function validateConfig($config)
	{
		Validators::assertField($config, 'source', 'array');
		Validators::assertField($config, 'destination', 'string:1..');
		Validators::assertField($config, 'extensions', 'array');
		Validators::assertField($config, 'exclude', 'list');
		Validators::assertField($config, 'skipDocPath', 'list');
		Validators::assertField($config, 'skipDocPrefix', 'list');
		Validators::assertField($config, 'charset', 'list');
		Validators::assertField($config, 'main', 'string');
		Validators::assertField($config, 'title', 'string');
		Validators::assertField($config, 'baseUrl', 'string');
		Validators::assertField($config, 'googleCseId', 'string');
		Validators::assertField($config, 'googleAnalytics', 'string');
		Validators::assertField($config, 'allowedHtml', 'list');
		Validators::assertField($config, 'autocomplete', 'list');
		Validators::assertField($config, 'accessLevels', 'list');
		Validators::assertField($config, 'internal', 'bool');
		Validators::assertField($config, 'php', 'bool');
		Validators::assertField($config, 'tree', 'bool');
		Validators::assertField($config, 'deprecated', 'bool');
		Validators::assertField($config, 'todo', 'bool');
		Validators::assertField($config, 'download', 'bool');
		Validators::assertField($config, 'report', 'string');
		Validators::assertField($config, 'wipeout', 'bool');
		Validators::assertField($config, 'debug', 'bool');

		foreach ($config['source'] as $source) {
			if ( ! file_exists($source)) {
				throw new ConfigException(sprintf('Source "%s" doesn\'t exist', $source));
			}
		}

		foreach ($config['extensions'] as $extension) {
			Validators::assert($extension, 'string', 'file extension');
		}
		if ( ! is_file($config['templateConfig'])) {
			throw new ConfigException(sprintf('Template config "%s" doesn\'t exist', $config['templateConfig']));
		}
	}


	/**
	 * @param array $config
	 */
	public function validateTemplateConfig($config)
	{
		return; // todo: fix
		foreach (array('main', 'optional') as $section) {
			foreach ($config['template']['templates'][$section] as $type => $config) {
				if ( ! isset($config['filename'])) {
					throw new ConfigException(sprintf('Filename for "%s" is not defined', $type));
				}


				if ( ! isset($config['template'])) {
					throw new ConfigException(sprintf('Template for "%s" is not defined', $type));
				}

				// todo: fix
//				if ( ! is_file(dirname($config['templateConfig']) . DIRECTORY_SEPARATOR . $config['template'])) {
//					throw new ConfigException(sprintf('Template for "%s" doesn\'t exist', $type));
//				}
			}
		}


	}

}
