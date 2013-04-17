<?php

namespace Grid\ApplicationLog\Model\Log;

/**
 * Application\Model\Log\StructureInterface
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
interface StructureInterface
{

    /**
     * Get user-id of the log
     *
     * @return int|null
     */
    public function getLoggedUserId();

    /**
     * Get event-type of the log
     *
     * @return string|null
     */
    public function getEventType();

    /**
     * Get priority of the log
     *
     * @return int
     */
    public function getPriority();

}
