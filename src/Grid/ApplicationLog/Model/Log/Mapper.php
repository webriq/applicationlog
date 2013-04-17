<?php

namespace Grid\ApplicationLog\Model\Log;

use Zend\Db\Sql;
use Zend\Stdlib\ArrayUtils;
use Zork\Db\Sql\Predicate\NotIn;
use Zork\Model\Mapper\DbAware\ReadWriteMapperAbstract;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Mapper
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Mapper extends ReadWriteMapperAbstract
          implements ServiceLocatorAwareInterface
{

    /**
     * Table name used in all queries
     *
     * @var string
     */
    protected static $tableName = 'application_log';

    /**
     * Table name used additinally in select queries
     *
     * @var string
     */
    protected static $propertyTableName = 'application_log_property';

    /**
     * Default column-conversion functions as types;
     * used in selected(), deselect()
     *
     * @var array
     */
    protected static $columns = array(
        'id'            => self::INT,
        'timestamp'     => self::DATETIME,
        'loggedUserId'  => self::INT,
        'eventType'     => self::STR,
        'priority'      => self::INT,
    );

    /**
     * Service-locator
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Structure factory for the mapper
     *
     * @var \ApplicationLog\Model\Log\StructureFactory
     */
    protected $structureFactory;

    /**
     * Get service-locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service-locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \ApplicationLog\Model\Log\Structure\ProxyBase
     */
    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get structure factory
     *
     * @return \ApplicationLog\Model\Log\StructureFactory
     */
    public function getStructureFactory()
    {
        return $this->structureFactory;
    }

    /**
     * Set structure factory
     *
     * @param \ApplicationLog\Model\Log\StructureFactory $structurePrototype
     * @return \ApplicationLog\Model\Log\Mapper
     */
    public function setStructureFactory( $structureFactory )
    {
        $this->structureFactory = $structureFactory;
        return $this;
    }

    /**
     * Contructor
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param \ApplicationLog\Model\Log\StructureFactory $paragraphStructureFactory
     * @param \ApplicationLog\Model\Log\Structure\ProxyBase $paragraphStructurePrototype
     */
    public function __construct( ServiceLocatorInterface $serviceLocator,
                                 StructureFactory $applicationLogStructureFactory,
                                 Structure\ProxyBase $applicationLogStructurePrototype = null )
    {
        parent::__construct( $applicationLogStructurePrototype ?: new Structure\ProxyBase );

        $this->setServiceLocator( $serviceLocator )
             ->setStructureFactory( $applicationLogStructureFactory );
    }

    /**
     * Create structure from plain data
     *
     * @param array $data
     * @return \ApplicationLog\Model\Log\StructureInterface
     */
    protected function createStructure( array $data )
    {
        if ( isset( $data['proxyData'] ) )
        {
            $proxyData = $data['proxyData'] ?: array();
            unset( $data['proxyData'] );
        }
        else
        {
            $proxyData = $data;
        }

        $proxyData['proxyBase'] = parent::createStructure( $data );
        $proxyData['eventType'] = $proxyData['proxyBase']->eventType;

        return $this->structureFactory
                    ->factory( $proxyData );
    }

    /**
     * Get select() default columns
     *
     * @return array
     */
    protected function getSelectColumns( $columns = null )
    {
        if ( null === $columns )
        {
            $proxyData = true;
        }
        elseif ( ( $index = array_search( 'proxyData', $columns ) ) )
        {
            $proxyData = true;
            unset( $columns[$index] );
        }
        else
        {
            $proxyData = false;
        }

        $columns = parent::getSelectColumns( $columns );

        if ( $proxyData )
        {
            $platform = $this->getDbAdapter()
                             ->getPlatform();

            $columns['proxyData'] = new Sql\Expression( '(' .
                $this->sql( $this->getTableInSchema(
                        static::$propertyTableName
                     ) )
                     ->select()
                     ->columns( array(
                         new Sql\Expression( 'TEXT( ARRAY_TO_JSON(
                             ARRAY_AGG( ? ORDER BY ? ASC )
                         ) )', array(
                             static::$propertyTableName,
                             'name',
                         ), array(
                             Sql\Expression::TYPE_IDENTIFIER,
                             Sql\Expression::TYPE_IDENTIFIER,
                         ) )
                     ) )
                     ->where( array(
                         new Sql\Predicate\Expression(
                             $platform->quoteIdentifierChain( array(
                                 static::$propertyTableName, 'logId'
                             ) ) .
                             ' = ' .
                             $platform->quoteIdentifierChain( array(
                                 static::$tableName, 'id'
                             ) )
                         )
                     ) )
                     ->getSqlString( $platform ) .
            ')' );
        }

        return $columns;
    }

    /**
     * Parse proxy-data
     *
     * Like:
     * <pre>
     * &lt;struct&gt;
     * [{"name":"{key}","value":"{value}"}]
     * &nbsp;...
     * &lt;/struct&gt;
     * </pre>
     *
     * @param string $data
     * @return array
     */
    protected function parseProxyData( & $data )
    {
        if ( empty( $data ) )
        {
            return array();
        }

        $result = array();
        foreach ( json_decode( $data, true ) as $field )
        {
            if ( empty( $field['name'] ) )
            {
                continue;
            }

            $name   = (string) $field['name'];
            $parts  = explode( '.', $name, 2 );
            $value  = isset( $field['value'] ) ? $field['value'] : null;

            if ( count( $parts ) > 1 )
            {
                list( $name, $sub ) = $parts;

                if ( isset( $result[$name] ) )
                {
                    if ( ! is_array( $result[$name] ) )
                    {
                        $result[$name] = (array) $result[$name];
                    }
                }
                else
                {
                    $result[$name] = array();
                }

                $result[$name][$sub] = $value;
            }
            else
            {
                $result[$name] = $value;
            }
        }

        foreach ( $result as & $value )
        {
            if ( is_array( $value ) )
            {
                uksort( $value, 'strnatcmp' );
            }
        }

        return $result;
    }

    /**
     * Transforms the selected data into the structure object
     *
     * @param array $data
     * @return \Zork\Model\Structure\StructureAbstract
     */
    public function selected( array $data )
    {
        if ( isset( $data['proxyData'] ) && is_string( $data['proxyData'] ) )
        {
            $data['proxyData'] = $this->parseProxyData( $data['proxyData'] );
        }

        return parent::selected( $data );
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $structure
     * @return object
     */
    public function hydrate( array $data, $structure )
    {
        if ( $structure instanceof Structure\ProxyBase )
        {
            if ( isset( $data['proxyData'] ) )
            {
                $proxyData = $data['proxyData'] ?: array();
                unset( $data['proxyData'] );

                if ( is_string( $proxyData ) )
                {
                    $proxyData = $this->parseProxyData( $proxyData );
                }

                if ( is_array( $proxyData ) )
                {
                    $proxyData = array_merge( $data, $proxyData );
                }

                foreach ( static::$columns as $column => $type )
                {
                    unset( $proxyData[$column] );
                }
            }
            else
            {
                $proxyData = $data;
            }

            $proxyData['proxyBase'] = parent::hydrate( $data, $structure );
            $proxyData['eventType'] = $proxyData['proxyBase']->eventType;

            return $this->structureFactory
                        ->factory( $proxyData );
        }

        return parent::hydrate( $data, $structure );
    }

    /**
     * Get paginator
     *
     * @param   mixed|null  $where
     * @param   mixed|null  $order
     * @param   mixed|null  $columns
     * @param   mixed|null  $joins
     * @param   mixed|null  $quantifier
     * @return  \Zend\Paginator\Paginator
     */
    public function getPaginator( $where        = null,
                                  $order        = null,
                                  $columns      = null,
                                  $joins        = null,
                                  $quantifier   = null )
    {
        $joins = array_merge( (array) $joins, array(
            'user' => array(
                'table'     => $this->getTableInSchema( 'user' ),
                'where'     => static::$tableName . '.loggedUserId = user.id',
                'columns'   => array(
                    'loggedUserName' => 'displayName',
                ),
                'type'      => Sql\Select::JOIN_LEFT,
            ),
        ) );

        return parent::getPaginator( $where, $order, $columns, $joins, $quantifier );
    }

    /**
     * Save a single property
     *
     * @param int $id
     * @param string $name
     * @param mixed $value
     * @return int
     */
    private function saveSingleProperty( $id, $name, $value )
    {
        $sql = $this->sql( $this->getTableInSchema(
            static::$propertyTableName
        ) );

        $update = $sql->update()
                      ->set( array(
                          'value'   => $value,
                      ) )
                      ->where( array(
                          'logId'   => $id,
                          'name'    => $name,
                      ) );

        $affected = $sql->prepareStatementForSqlObject( $update )
                        ->execute()
                        ->getAffectedRows();

        if ( $affected < 1 )
        {
            $insert = $sql->insert()
                          ->values( array(
                              'logId'   => $id,
                              'name'    => $name,
                              'value'   => $value,
                          ) );

            $affected = $sql->prepareStatementForSqlObject( $insert )
                            ->execute()
                            ->getAffectedRows();
        }

        return $affected;
    }

    /**
     * Save a property
     *
     * @param int $id
     * @param string $name
     * @param mixed $value
     * @return int
     */
    protected function saveProperty( $id, $name, $value )
    {
        $rows   = 0;
        $sql    = $this->sql( $this->getTableInSchema(
            static::$propertyTableName
        ) );

        $like = strtr( $name, array(
            '\\' => '\\\\',
            '%' => '\%',
            '_' => '\_',
        ) ) . '.%';

        if ( is_array( $value ) )
        {
            $nameLikeOrEq = new Sql\Predicate\PredicateSet( array(
                new Sql\Predicate\Like( 'name', $like ),
                new Sql\Predicate\Operator( 'name', Sql\Predicate\Operator::OP_EQ, $name )
            ), Sql\Predicate\PredicateSet::OP_OR );

            if ( empty( $value ) )
            {
                $delete = $sql->delete()
                              ->where( array(
                                  'logId'   => $id,
                                  $nameLikeOrEq,
                              ) );

                $rows += $sql->prepareStatementForSqlObject( $delete )
                             ->execute()
                             ->getAffectedRows();
            }
            else
            {
                $keys = array();

                foreach ( $value as $idx => $val )
                {
                    $keys[] = $key = $name . '.' . $idx;
                    $rows += $this->saveSingleProperty( $id, $key, $val );
                }

                $delete = $sql->delete()
                              ->where( array(
                                  'menuId'  => $id,
                                  $nameLikeOrEq,
                                  new NotIn( 'name', $keys ),
                              ) );

                $rows += $sql->prepareStatementForSqlObject( $delete )
                             ->execute()
                             ->getAffectedRows();
            }
        }
        else
        {
            $rows += $this->saveSingleProperty( $id, $name, $value );

            $delete = $sql->delete()
                          ->where( array(
                              'logId'   => $id,
                              new Sql\Predicate\Like( 'name', $like ),
                          ) );

            $rows += $sql->prepareStatementForSqlObject( $delete )
                         ->execute()
                         ->getAffectedRows();
        }

        return $rows;
    }

    /**
     * Save element structure to datasource
     *
     * @param \Menu\Model\Menu\Structure\ProxyAbstract $structure
     * @return int Number of affected rows
     */
    public function save( & $structure )
    {
        if ( ! $structure instanceof Structure\ProxyAbstract ||
             empty( $structure->eventType ) )
        {
            return 0;
        }

        $data   = ArrayUtils::iteratorToArray( $structure->getBaseIterator() );
        $result = parent::save( $data );

        if ( $result > 0 )
        {
            if ( empty( $structure->id ) )
            {
                $structure->setOption( 'id', $id = $data['id'] );
            }
            else
            {
                $id = $structure->id;
            }

            foreach ( $structure->getPropertiesIterator() as $property => $value )
            {
                $result += $this->saveProperty( $id, $property, $value );
            }
        }

        return $result;
    }

}
