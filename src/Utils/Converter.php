<?php
/**
 * @author Tomáš Blatný
 */

namespace greeny\DoctrineEntitySerializer\Utils;

use Doctrine\Common\Collections\Collection;
use Traversable;


final class Converter
{

	public static $convertMap = [
		'null' => [Converter::class, 'noop'],
		'int' => [Converter::class, 'toInt'],
		'integer' => [Converter::class, 'toInt'],
		'number' => [Converter::class, 'toFloat'],
		'float' => [Converter::class, 'toFloat'],
		'double' => [Converter::class, 'toFloat'],
		'bool' => [Converter::class, 'toBool'],
		'boolean' => [Converter::class, 'toBool'],
		'string' => [Converter::class, 'toString'],
		'char' => [Converter::class, 'toString'],
		'varchar' => [Converter::class, 'toString'],
		'text' => [Converter::class, 'toString'],
		'array' => [Converter::class, 'toArray'],
		'list' => [Converter::class, 'toArray'],
		'collection' => [Converter::class, 'toArray'],
		'mixed' => [Converter::class, 'noop'],
	];


	public static function convert($to, $var)
	{
		if (isset (self::$convertMap[$to])) {
			$callback = self::$convertMap[$to];
			return $callback($var);
		}
		throw new ConversionException('Cannot convert variable to ' . $to);
	}


	public static function toInt($val)
	{
		if (is_scalar($val)) {
			return (int) $val;
		} elseif (is_null($val)) {
			return NULL;
		}
		throw new ConversionException('Cannot convert non-scalar variable to int.');
	}


	public static function toFloat($val)
	{
		if (is_scalar($val)) {
			return (float) $val;
		} elseif (is_null($val)) {
			return NULL;
		}
		throw new ConversionException('Cannot convert non-scalar variable to int.');
	}


	public static function toBool($val)
	{
		return (bool) $val;
	}


	public static function toString($val)
	{
		if (is_scalar($val) || (is_object($val) && method_exists($val, '__toString'))) {
			return (string) $val;
		} elseif (is_null($val)) {
			return NULL;
		}
		throw new ConversionException('Cannot convert non-scalar variable to string.');
	}


	public static function toArray($val)
	{
		if (is_object($val) && $val instanceof Collection) {
			return $val->toArray();
		} elseif ($val instanceof Traversable) {
			return iterator_to_array($val);
		} elseif (is_array($val) || is_object($val)) {
			return (array) $val;
		}
		throw new ConversionException('Cannot convert scalar variable to array.');
	}


	public static function noop($val)
	{
		return $val;
	}

}
