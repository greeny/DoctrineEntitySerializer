<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer\Drivers;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use greeny\DoctrineEntitySerializer\IDriver;
use greeny\DoctrineEntitySerializer\Naming\Conventions\NoConvention;
use greeny\DoctrineEntitySerializer\Naming\NamingMapper;
use greeny\DoctrineEntitySerializer\SerializeException;
use greeny\DoctrineEntitySerializer\Serializer;
use greeny\DoctrineEntitySerializer\Utils\Converter;
use ReflectionClass;
use ReflectionProperty;


/**
 * Driver for serializing all non-static properties without any custom logic. Will fail if there is a recursion
 */
class SimpleSerializationDriver implements IDriver
{

	/**
	 * Property needs to have at least one of these annotations to be serialized
	 *
	 * @var array
	 */
	public static $mappedPropertyAnnotations = [
		ORM\Column::class,
		ORM\OneToMany::class,
		ORM\OneToOne::class,
		ORM\ManyToOne::class,
		ORM\ManyToMany::class,
	];


	/** @var Serializer */
	private $serializer;

	/** @var int */
	private $depth;

	/** @var Reader */
	private $reader;

	/** @var NamingMapper */
	private $namingMapper;



	public function __construct(Reader $reader, NamingMapper $namingMapper = NULL)
	{
		$this->reader = $reader;
		$this->namingMapper = $namingMapper ?: new NamingMapper(new NoConvention, new NoConvention);
	}


	/**
	 * @inheritdoc
	 */
	public function serialize($entity)
	{
		if (is_object($entity)) {
			return $this->serializeEntity($entity);
		} elseif (is_array($entity)) {
			return $this->serializeCollection($entity);
		}
		throw new SerializeException('Cannot serialize non-object and non-array.');
	}


	/**
	 * Serializes collection into array
	 *
	 * @param array $entities
	 * @param string|NULL $indexBy
	 * @param int $depth
	 * @return array
	 */
	private function serializeCollection(array $entities, $indexBy = NULL, $depth = 1)
	{
		$result = [];
		foreach ($entities as $entity) {
			$item = $this->serializeEntity($entity, $depth);
			if ($indexBy) {
				$result[$item[$indexBy]] = $item;
			} else {
				$result[] = $item;
			}
		}
		return $result;
	}


	/**
	 * Serializes entity into array
	 *
	 * @param object $entity
	 * @param int $depth
	 * @return array
	 * @throws SerializeException
	 */
	private function serializeEntity($entity, $depth = 1)
	{
		if (!is_object($entity)) {
			throw new SerializeException('Cannot serialize non-objects.');
		}
		$reflection = new ReflectionClass(get_class($entity));
		if (!$this->isEntity($reflection)) {
			throw new SerializeException('Cannot serialize non-entity class ' . $reflection->getName() . '.');
		}
		$result = [];
		/** @var ReflectionProperty[] $properties */
		$properties = [];
		foreach ($reflection->getProperties() as $property) {
			if (!$property->isStatic()) {
				$propertyAnnotations = $this->reader->getPropertyAnnotations($property);
				foreach ($propertyAnnotations as $propertyAnnotation) {
					if (in_array(get_class($propertyAnnotation), self::$mappedPropertyAnnotations, TRUE)) {
						$properties[] = $property;
						continue;
					}
				}
			}
		}

		foreach ($properties as $property) {
			$result[$this->getPropertyName($property)] = $this->serializeProperty($entity, $property);
		}

		return $result;
	}


	/**
	 * Checks if class is entity
	 *
	 * @param ReflectionClass $reflection
	 * @return bool
	 */
	private function isEntity(ReflectionClass $reflection)
	{
		$classAnnotations = $this->reader->getClassAnnotations($reflection);
		$isEntity = FALSE;
		foreach ($classAnnotations as $annotation) {
			if ($annotation instanceof ORM\Entity) {
				$isEntity = TRUE;
			}
		}
		return $isEntity;
	}


	/**
	 * Returns property name
	 *
	 * @param ReflectionProperty $reflection
	 * @return string
	 */
	private function getPropertyName(ReflectionProperty $reflection)
	{
		return $this->namingMapper->convert($reflection->getName());
	}


	/**
	 * Returns serialized value of property
	 *
	 * @param object $entity
	 * @param ReflectionProperty $reflection
	 * @return mixed
	 */
	private function serializeProperty($entity, ReflectionProperty $reflection)
	{
		$name = $reflection->getName();
		$value = $entity->$name;
		$type = $this->getPropertyType($value, $reflection);
		$return = Converter::convert($type, $value);
		if (is_array($return)) {
			return $this->serializeCollection($return, NULL, $this->depth + 1);
		} else {
			return $return;
		}
	}


	/**
	 * Resolves property type
	 *
	 * @param mixed $value
	 * @param ReflectionProperty $reflection
	 * @return string
	 */
	private function getPropertyType($value, ReflectionProperty $reflection)
	{
		/** @var ORM\Column $column */
		if ($column = $this->reader->getPropertyAnnotation($reflection, ORM\Column::class)) {
			if ($column->type) {
				return $column->type;
			}
		}

		if (is_null($value)) {
			return 'null';
		} elseif (is_numeric($value)) {
			return 'number';
		}  elseif ((substr($reflection->getName(), 0, 3) === 'get') || (substr($reflection->getName(), 0, 2) === 'is')) {
			return 'bool';
		} elseif (is_array($value) || (is_object($value) && $value instanceof Collection)) {
			return 'array';
		} elseif (is_string($value) || (is_object($value) && method_exists($value, '__toString'))) {
			return 'string';
		} else {
			return 'mixed';
		}
	}

}
