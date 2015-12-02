<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer\Naming;

interface IConvention
{

	/**
	 * Parses name into words
	 *
	 * @param string $name
	 * @return string[]
	 */
	function getWords($name);


	/**
	 * Joins words into name
	 *
	 * @param string[] $words
	 * @return string
	 */
	function getName(array $words);

}
