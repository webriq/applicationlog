<?php

namespace Grid\ApplicationLog\Controller;

use Zork\Data\Table;
use Zork\Data\Transform\Translate;
use Grid\Core\Controller\AbstractListExportController;

/**
 * AdminController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class AdminController extends AbstractListExportController
{

    /**
     * @var string
     */
    protected $exportFileName = 'log';

    /**
     * @var array
     */
    protected $exportFieldTypes = array(
        'priority'          => Table::INTEGER,
        'eventType'         => Table::STRING,
        'loggedUserName'    => Table::STRING,
        'timestamp'         => Table::DATETIME,
        'description'       => Table::STRING,
    );

    /**
     * @var array
     */
    protected $exportFieldNames = array(
        'priority'          => 'applicationLog.list.column.priority.title',
        'eventType'         => 'applicationLog.list.column.eventType.title',
        'loggedUserName'    => 'applicationLog.list.column.loggedUserName.title',
        'timestamp'         => 'applicationLog.list.column.timestamp.title',
        'description'       => 'applicationLog.list.column.extra.title',
    );

    /**
     * @return array
     */
    protected function getExportFieldTypes()
    {
        $translator = $this->getServiceLocator()
                           ->get( 'Zend\I18n\Translator\Translator' );

        return array_merge(
            parent::getExportFieldTypes(),
            array(
                'priority'  => new Translate( $translator, 'applicationLog.priority.', '', 'applicationLog' ),
                'eventType' => new Translate( $translator, 'applicationLog.eventType.', '', 'applicationLog' ),
            )
        );
    }

    /**
     * Define rights required to use this controller
     *
     * @var array
     */
    protected $aclRights = array(
        '' => array(
            'applicationLog' => 'view',
        ),
    );

    /**
     * Get paginator list
     *
     * @return \Zend\Paginator\Paginator
     */
    protected function getPaginator()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\ApplicationLog\Model\Log\Model' )
                    ->getPaginator();
    }

}
