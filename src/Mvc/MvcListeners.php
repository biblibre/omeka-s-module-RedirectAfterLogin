<?php

namespace RedirectAfterLogin\Mvc;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class MvcListeners extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'setRedirectUrl']
        );
    }

    public function setRedirectUrl(MvcEvent $event)
    {
        $services = $event->getApplication()->getServiceManager();
        $auth = $services->get('Omeka\AuthenticationService');

        if ($auth->hasIdentity()) {
            return;
        }

        $routeMatch = $event->getRouteMatch();
        if ('login' === $routeMatch->getMatchedRouteName()) {
            $redirect_url = $event->getRequest()->getQuery('redirect_url');
            if ($redirect_url && 0 === strpos($redirect_url, '/')) {
                $session = Container::getDefaultManager()->getStorage();
                $session->offsetSet('redirect_url', $redirect_url);
            }
        }
    }
}
