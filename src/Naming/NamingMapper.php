<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer\Naming;

class NamingMapper
{

	/** @var IConvention */
	private $entitiesConvention;

	/** @var IConvention */
	private $serializationConvention;


	public function __construct(IConvention $entitiesConvention, IConvention $serializationConvention)
	{
		$this->entitiesConvention = $entitiesConvention;
		$this->serializationConvention = $serializationConvention;
	}


	/**
	 * @return IConvention
	 */
	public function getEntitiesConvention()
	{
		return $this->entitiesConvention;
	}


	/**
	 * @return IConvention
	 */
	public function getSerializationConvention()
	{
		return $this->serializationConvention;
	}


	/**
	 * Converts name from entity to serialization
	 *
	 * @param string $name
	 * @return string
	 */
	public function convert($name)
	{
		return $this->serializationConvention->getName($this->entitiesConvention->getWords($name));
	}

}
