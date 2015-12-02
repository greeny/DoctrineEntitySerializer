<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer\Naming\Conventions;

use greeny\DoctrineEntitySerializer\Naming\IConvention;


class SnakeCase implements IConvention
{

	/**
	 * @inheritdoc
	 */
	public function getWords($name)
	{
		return explode('_', $name);
	}


	/**
	 * @inheritdoc
	 */
	public function getName(array $words)
	{
		return implode('_', array_map('strtolower', $words));
	}
}
