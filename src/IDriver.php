<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer;

use ReflectionClass;


interface IDriver
{

	/**
	 * Preforms serialization on entity
	 *
	 * @param $entity
	 * @return mixed
	 */
	function serialize($entity);

}
