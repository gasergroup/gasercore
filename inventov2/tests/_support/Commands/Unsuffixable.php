<?php

namespace Tests\Support\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\GeneratorTrait;

class Unsuffixable extends BaseCommand
{
	use GeneratorTrait;

	/**
	 * The Command's Group
	 *
	 * @var string
	 */
	protected $group = 'Generators';

	/**
	 * The Command's Name
	 *
	 * @var string
	 */
	protected $name = 'make:foo';

	/**
	 * The Command's Description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * The Command's Usage
	 *
	 * @var string
	 */
	protected $usage = 'make:foo [arguments] [options]';

	/**
	 * The Command's Arguments
	 *
	 * @var array
	 */
	protected $arguments = [
		'name' => 'Class name',
	];

	/**
	 * The Command's Options
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Actually execute a command.
	 *
	 * @param array $params
	 */
	public function run(array $params)
	{
		$this->component = 'Command';
		$this->directory = 'Commands';
		$this->template  = 'command.tpl.php';

		$this->setEnabledSuffixing(false);
		$this->execute($params);
	}
}
