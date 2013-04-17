<?php

namespace Grid\ApplicationLog\Controller\Plugin;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zork\Session\ContainerAwareTrait as SessionContainerAwareTrait;

/**
 * Dashboard plugin for ApplicationLog
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DashboardPlugin extends AbstractPlugin
{

    use SessionContainerAwareTrait;

    /**
     * Find last items count
     *
     * @var int
     */
    public $defaultLastItemCount = 5;

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function __invoke( $lastItemCount = null )
    {
        $controller = $this->getController();
        $service    = $controller->getServiceLocator();
        $model      = $service->get( 'Grid\ApplicationLog\Model\Log\Model' );

        $view = new ViewModel( array(
            'logEvents' => $model->findLast(
                $lastItemCount ?: $this->defaultLastItemCount
            ),
        ) );

        return $view->setTemplate( 'grid/application-log/admin/dashboard' );
    }

}
