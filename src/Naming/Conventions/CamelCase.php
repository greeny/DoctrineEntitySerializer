<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer\Naming\Conventions;

use greeny\DoctrineEntitySerializer\Naming\IConvention;


class CamelCase implements IConvention
{

	/**
	 * @inheritdoc
	 */
	public function getWords($name)
	{
		return explode('.', preg_replace_callback('~[A-Z]~', function ($match) {
			return '.' . $match[0];
		}, $name));
	}


	/**
	 * @inheritdoc
	 */
	public function getName(array $words)
	{
		$isFirst = TRUE;
		$words = array_map(function ($word) use (&$isFirst) {
			if ($isFirst) {
				$isFirst = FALSE;
				return strtolower($word);
			}
			return ucfirst(strtolower($word));
		}, $words);
		return implode('', $words);
	}
}
