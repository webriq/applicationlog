<?php

namespace Grid\ApplicationLog\Model\Log;

use Zork\Model\MapperAwareTrait;
use Zork\Model\MapperAwareInterface;

/**
 * \ApplicationLog\Model\Log\Model
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Model implements MapperAwareInterface
{

    use MapperAwareTrait;

    /**
     * Construct model
     *
     * @param \ApplicationLog\Model\Log\Mapper $applicationLogMapper
     * @param string $locale
     */
    public function __construct( Mapper $applicationLogMapper )
    {
        $this->setMapper( $applicationLogMapper );
    }

    /**
     * Create a structure
     *
     * @param array $data
     * @return \ApplicationLog\Model\Log\StructureInterface
     */
    public function create( $data )
    {
        return $this->getMapper()
                    ->create( $data );
    }

    /**
     * Find a structure
     *
     * @param int $id
     * @return \ApplicationLog\Model\Log\StructureInterface
     */
    public function find( $id )
    {
        return $this->getMapper()
                    ->find( $id );
    }

    /**
     * Get paginator for listing
     *
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->getMapper()
                    ->getPaginator();
    }

    /**
     * Find last items
     *
     * @param   int     $limit
     * @return  \Zend\Paginator\Paginator
     */
    public function findLast( $limit = 10 )
    {
        return $this->getMapper()
                    ->getPaginator( null, array( 'timestamp' => 'DESC' ) )
                    ->setCurrentPageNumber( 0 )
                    ->setItemCountPerPage( $limit );
    }

}
