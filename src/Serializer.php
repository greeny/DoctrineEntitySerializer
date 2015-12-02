<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer;

use Doctrine\ORM\Mapping as ORM;


class Serializer
{

	/** @var IDriver */
	private $driver;


	public function __construct(IDriver $driver)
	{
		$this->driver = $driver;
	}


	/**
	 * @param object $entity
	 * @return array
	 * @throws SerializeException
	 */
	public function serialize($entity)
	{
		return $this->driver->serialize($entity);
	}

}
