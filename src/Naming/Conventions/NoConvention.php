<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer\Naming\Conventions;

use greeny\DoctrineEntitySerializer\Naming\IConvention;


class NoConvention implements IConvention
{

	/**
	 * Parses name into words
	 *
	 * @param string $name
	 * @return string[]
	 */
	public function getWords($name)
	{
		return [$name];
	}


	/**
	 * Joins words into name
	 *
	 * @param string[] $words
	 * @return string
	 */
	public function getName(array $words)
	{
		return implode('', $words);
	}
}
