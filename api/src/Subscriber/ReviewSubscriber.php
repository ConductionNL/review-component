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

//use App\Service\MailService;
//use App\Service\MessageService;

class ReviewSubscriber implements EventSubscriberInterface
{
	private $params;
	private $em;
	private $serializer;

	public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->params = $params;
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['totals', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function totals(GetResponseForControllerResultEvent $event)
    {
    	$result = $event->getControllerResult();
    	$method = $event->getRequest()->getMethod();
    	$route = $event->getRequest()->attributes->get('_route');
    	    	
    	if ($route != 'api_reviews_item_total_collection' || $method != 'GET'){
    		return;
    	}
    	$request = New Request();
    	
    	/*@todo onderstaande verhaal moet uiteraard wel worden gedocumenteerd in redoc */
    	$organisation = $request->request->get('organization');
    	
    	
    	// Let then create the responce
    	$response = [];
    	$response['organisation'] = $organisation;
    	$response['rating'] = 3;
    	$response['likes'] = 7500;
    	
    	$json = $this->serializer->serialize(
    			$response,
    			'jsonhal', ['enable_max_depth' => true]
    			);
    	
    	$response = new Response(
    			$json,
    			Response::HTTP_OK,
    			['content-type' => 'application/json+hal']
    			);
    	
    	$event->setResponse($response);
    }
}
