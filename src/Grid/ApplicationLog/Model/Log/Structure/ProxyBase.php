<?php

namespace Grid\ApplicationLog\Model\Log\Structure;

use Zork\Model\Structure\MapperAwareAbstract;
use Grid\ApplicationLog\Model\Log\StructureInterface;

/**
 * Structure
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ProxyBase extends MapperAwareAbstract
             implements StructureInterface
{

    /**
     * ID of the menu
     *
     * @val int|null
     */
    protected $id;

    /**
     * Timestamp of the log-event
     *
     * @var \DateTime|null
     */
    public $timestamp;

    /**
     * User-id of the log-event
     *
     * @var int|null
     */
    public $loggedUserId;

    /**
     * Type of the log-event
     *
     * @var string|null
     */
    public $eventType;

    /**
     * Priority of the log-event
     *
     * @var int
     */
    public $priority;

    /**
     * Get ID of the log-event
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get timestamp of the log-event
     *
     * @return \DateTime|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get user-id of the log-event
     *
     * @return int|null
     */
    public function getLoggedUserId()
    {
        return $this->loggedUserId;
    }

    /**
     * Get type of the log-event
     *
     * @return string|null
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * Get priority of the log-event
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get service locator
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->getMapper()
                    ->getServiceLocator();
    }

}
