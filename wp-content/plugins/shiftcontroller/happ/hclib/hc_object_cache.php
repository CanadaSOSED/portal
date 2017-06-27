<?php
class HC_Object_Cache
{
	private static $cache = NULL;

	public function __construct()
	{
		if( self::$cache === NULL )
		{
			self::$cache = new ArrayIterator;
		}
	}

	public function first_key( $class )
	{
		$return = NULL;
		if( self::$cache->offsetExists($class) )
		{
			self::$cache->offsetGet($class)->rewind();
			$return = self::$cache->offsetGet($class)->key();
		}
		return $return;
	}

	public function last_key( $class )
	{
		$return = NULL;
		if( self::$cache->offsetExists($class) )
		{
			$count = self::$cache->offsetGet($class)->count();
			self::$cache->offsetGet($class)->seek( $count - 1 );
			$return = self::$cache->offsetGet($class)->key();
		}
		return $return;
	}

	public function set( $class, $store, $key )
	{
		if( ! self::$cache->offsetExists($class) )
		{
			self::$cache->offsetSet( $class, new ArrayIterator );
		}

		if( ! self::$cache[$class]->offsetExists($key) )
		{
			self::$cache[$class]->offsetSet( $key, new ArrayIterator );
		}
		self::$cache[$class][$key]->append( $store );
	}

	public function get( $class, $key )
	{
		$return = new AppendIterator;

		if( ! is_array($key) )
		{
			$key = array( $key );
		}

		foreach( $key as $k )
		{
			if( self::$cache->offsetExists($class) )
			{
				if( self::$cache->offsetGet($class)->offsetExists($k) )
				{
					$return->append( self::$cache->offsetGet($class)->offsetGet($k) );
				}
			}
		}

		return $return;
	}
}