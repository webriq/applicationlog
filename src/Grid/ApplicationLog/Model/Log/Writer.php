<?php

namespace Grid\ApplicationLog\Model\Log;

use Zork\Model\ModelAwareTrait;
use Zork\Model\ModelAwareInterface;
use Zend\Log\Writer\AbstractWriter;
use Zend\Authentication\AuthenticationService;
use Zork\Authentication\AuthenticationServiceAwareTrait;

/**
 * Writer
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Writer extends AbstractWriter implements ModelAwareInterface
{

    use ModelAwareTrait,
        AuthenticationServiceAwareTrait;

    /**
     * Constructor
     *
     * @param \ApplicationLog\Model\Log\Model $applicationLogModel
     */
    public function __construct( Model                  $applicationLogModel,
                                 AuthenticationService  $authenticationService )
    {
        $this->setModel( $applicationLogModel )
             ->setAuthenticationService( $authenticationService );
    }

    /**
     * Write a message to the log
     *
     * @param array $event log data event
     * @return void
     */
    protected function doWrite( array $event )
    {
        $auth           = $this->getAuthenticationService();
        $event['extra'] = empty( $event['extra'] )
                        ? array()
                        : (array) $event['extra'];

        $this->getModel()
             ->create( array(
                 'timestamp'    => $event['timestamp'],
                 'priority'     => $event['priority'],
                 'eventType'    => $event['message'],
                 'proxyData'    => $event['extra'],
                 'loggedUserId' => $auth->hasIdentity()
                                ? $auth->getIdentity()->id
                                : null,
             ) )
             ->save();
    }

}
