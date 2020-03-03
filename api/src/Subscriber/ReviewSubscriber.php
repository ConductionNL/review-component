<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Twig_Environment as Environment;

use App\Entity\Template;

//use App\Service\MailService;
//use App\Service\MessageService;

class ReviewSubscriber implements EventSubscriberInterface
{
	private $params;
	private $em;
	private $templating;

	public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, Environment $twig)
    {
        $this->params = $params;
        $this->em = $em;
        $this->templating = $twig;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['template', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function template(GetResponseForControllerResultEvent $event)
    {
    	$result = $event->getControllerResult();
    	$method = $event->getRequest()->getMethod();
    	$route = $event->getRequest()->attributes->get('_route');
    	    	
    	var_dump($route);
    	
    	if ($route != 'api_templates_render_template_item' || $method != 'GET'){
    		return;
    	}
    	
    	$request = New Request();
    	
    	/*@todo onderstaande verhaal moet uiteraard wel worden gedocumenteerd in redoc */
    	$organisation = $request->request->get('organization');
    	
    	
    	return $result;
    }
}
