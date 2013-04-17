<?php

namespace Grid\ApplicationLog\Model\Log\Structure;

use Traversable;
use AppendIterator;
use Zork\Stdlib\DateTime;
use Zork\Factory\AdapterInterface;
use Zork\Model\MapperAwareInterface;
use Zork\Model\Exception\LogicException;
use Zend\View\Renderer\RendererInterface;
use Zork\Model\Structure\StructureAbstract;
use Grid\ApplicationLog\Model\Log\StructureInterface;

/**
 * ProxyAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class ProxyAbstract
       extends StructureAbstract
    implements AdapterInterface,
               StructureInterface,
               MapperAwareInterface
{

    /**
     * Log event-type
     *
     * @var string
     * @abstract
     */
    protected static $eventType;

    /**
     * Proxy base object
     *
     * @var \ApplicationLog\Model\Log\Structure\ProxyBase
     */
    private $proxyBase;

    /**
     * Constructor
     *
     * @param array $data
     * @throws \Zork\Model\Exception\LogicException if type does not match
     */
    public function __construct( $data = array() )
    {
        parent::__construct( $data );
        $proxyBase = $this->proxyBase;

        if ( empty( $proxyBase->eventType ) )
        {
            $proxyBase->eventType = static::$eventType;
        }
        else if ( ! empty( static::$eventType ) &&
                  static::$eventType !== $proxyBase->eventType )
        {
            throw new LogicException( 'Type does not match' );
        }
    }

    /**
     * Set option enhanced to be able to set id
     *
     * @param string $key
     * @param mixed $value
     * @return \Menu\Model\Menu\Structure\ProxyAbstract
     */
    public function setOption( $key, $value )
    {
        if ( 'id' == $key )
        {
            $this->proxyBase->setOption( $key, $value );
            return $this;
        }

        return parent::setOption( $key, $value );
    }

    /**
     * Set options
     *
     * @param mixed $options
     * @return \ApplicationLog\Model\Log\Structure\ProxyAbstract
     */
    public function setOptions( $options )
    {
        if ( $options instanceof Traversable )
        {
            $options = iterator_to_array( $options );
        }

        if ( isset( $options['proxyBase'] ) )
        {
            $this->setProxyBase( $options['proxyBase'] );
            unset( $options['proxyBase'] );
        }

        return parent::setOptions( $options );
    }

    /**
     * Get proxy base object
     *
     * @return \ApplicationLog\Model\Log\Structure\ProxyBase
     */
    public function setProxyBase( ProxyBase $proxyBase )
    {
        $this->proxyBase = $proxyBase;
        return $this;
    }

    /**
     * Get service-locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->proxyBase
                    ->getServiceLocator();
    }

    /**
     * Get the mapper object
     *
     * @return \ApplicationLog\Model\Log\Mapper
     */
    public function getMapper()
    {
        return $this->proxyBase
                    ->getMapper();
    }

    /**
     * Set the mapper object
     *
     * @param \ApplicationLog\Model\Log\Mapper $mapper
     * @return \ApplicationLog\Model\Log\Structure\ProxyAbsract
     */
    public function setMapper( $mapper = null )
    {
        $this->proxyBase
             ->setMapper( $mapper );

        return $this;
    }

    /**
     * Save me
     *
     * @return int Number of affected rows
     */
    public function save()
    {
        return $this->getMapper()
                    ->save( $this );
    }

    /**
     * Delete me
     *
     * @return int Number of affected rows
     */
    public function delete()
    {
        return $this->getMapper()
                    ->delete( $this );
    }

    /**
     * Get the proxy-base
     *
     * @return \ApplicationLog\Model\Log\Structure\ProxyBase
     */
    protected function & proxyBase()
    {
        return $this->proxyBase;
    }

    /**
     * Get ID of the log-event
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->proxyBase->id;
    }

    /**
     * Get timestamp of the log-event
     *
     * @return \Datetime|null
     */
    public function getTimestamp()
    {
        return $this->proxyBase->timestamp;
    }

    /**
     * Get user-id of the log-event
     *
     * @return int|null
     */
    public function getLoggedUserId()
    {
        return $this->proxyBase->loggedUserId;
    }

    /**
     * Get type of the log-event
     *
     * @return string|null
     */
    public function getEventType()
    {
        return $this->proxyBase->eventType;
    }

    /**
     * Get priority of the log-event
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->proxyBase->priority;
    }

    /**
     * Set timestamp of the log-event
     *
     * @return \DateTime|string|null
     */
    public function setTimestamp( $timestamp )
    {
        if ( empty( $timestamp ) )
        {
            $timestamp = null;
        }
        else if ( is_string( $timestamp ) )
        {
            $timestamp = new DateTime( $timestamp );
        }
        else if ( is_numeric( $timestamp ) )
        {
            $timestamp = new DateTime( '@' . $timestamp );
        }
        else
        {
            $timestamp = null;
        }

        $this->proxyBase->timestamp = $timestamp;
        return $this;
    }

    /**
     * Set user-id of the log-event
     *
     * @return int|null
     */
    public function setLoggedUserId( $userId )
    {
        $this->proxyBase->loggedUserId = ( (int) $userId ) ?: null;
        return $this;
    }

    /**
     * Set event-type of the log-event
     *
     * @return string|null
     * @thorws \Zork\Model\Exception\LogicException
     */
    public function setEventType( $eventType )
    {
        if ( empty( static::$eventType ) )
        {
            $this->proxyBase->eventType = $eventType;
        }
        elseif ( static::$eventType != $eventType )
        {
            throw new LogicException( 'Cannot alter type after creation' );
        }

        return $this;
    }

    /**
     * Set priority of the log-event
     *
     * @return int
     */
    public function setPriority( $severity )
    {
        $this->proxyBase->priority = (int) $severity;
        return $this;
    }

    /**
     * Returns the base iterator (only basic properties)
     *
     * @return \Zork\Model\Structure\StructureIterator
     */
    public function getBaseIterator()
    {
        return $this->proxyBase
                    ->getIterator();
    }

    /**
     * Returns the properties iterator (only additional properties)
     *
     * @return \Zork\Model\Structure\StructureIterator
     */
    public function getPropertiesIterator()
    {
        return parent::getIterator();
    }

    /**
     * Get iterator
     *
     * @return \AppendIterator
     */
    public function getIterator()
    {
        $result = new AppendIterator;
        $result->append( $this->getBaseIterator() );
        $result->append( $this->getPropertiesIterator() );
        return $result;
    }

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param array $options;
     * @return float
     */
    public static function acceptsOptions( array $options )
    {
        return isset( $options['eventType'] ) && $options['eventType'] === static::$eventType;
    }

    /**
     * Return a new instance of the adapter by $options
     *
     * @param array $options;
     * @return \ApplicationLog\Model\Log\Structure\ProxyAbstract
     */
    public static function factory( array $options = null )
    {
        return new static( $options );
    }

    /**
     * Get description for this log-event
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Render extra data for this log-event
     *
     * @return string
     */
    abstract public function render( RendererInterface $renderer );

}
