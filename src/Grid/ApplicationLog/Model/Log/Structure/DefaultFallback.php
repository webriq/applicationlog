<?php

namespace Grid\ApplicationLog\Model\Log\Structure;

use Zend\View\Renderer\RendererInterface;

/**
 * Default-fallback
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class DefaultFallback extends ProxyAbstract
{

    /**
     * Return true if and only if $options accepted by this adapter
     * If returns float as likelyhood the max of these will be used as adapter
     *
     * @param array $options;
     * @return float
     */
    public static function acceptsOptions( array $options )
    {
        return 0.01;
    }

    /**
     * Get description for this log-event
     *
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * Render extra data for this log-event
     *
     * @return string
     */
    public function render( RendererInterface $renderer )
    {
        return sprintf( $renderer->translate(
            'applicationLog.defaultFallback.message.%s',
            'applicationLog'
        ), $this->eventType );
    }

}
