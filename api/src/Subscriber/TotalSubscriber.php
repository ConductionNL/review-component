<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Total;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class TotalSubscriber implements EventSubscriberInterface
{
    private $em;
    private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['calculateTotal', EventPriorities::PRE_VALIDATE],
        ];
    }

    /*
     * This function hooks into the payment validition to make sure an payment acount is an acount
     *
     * It therby provides functionality for matching and creating acounts on the fly
     *
     * @parameter $event ViewEvent
     * @return Payment
     *
     */
    public function calculateTotal(ViewEvent $event)
    {
        $total = $event->getControllerResult();
        $author = $event->getRequest()->get('author', false);
        $organization = $event->getRequest()->get('organization', false);
        $resource = $event->getRequest()->get('resource', false);
        $route = $event->getRequest()->attributes->get('_route');
        $contentType = $event->getRequest()->headers->get('accept');
        if (!$contentType) {
            $contentType = $event->getRequest()->headers->get('Accept');
        }

        // api_totals_get_collection
        if ($route != "api_totals_get_collection") {
            return;
        }

        $rating = $this->em->getRepository("App\Entity\Review")->calculateRating($organization,$resource);
        $reviews = $this->em->getRepository("App\Entity\Review")->calculateReviews($organization,$resource);
        $likes = $this->em->getRepository("App\Entity\Like")->calculateLikes($organization,$resource);
        $liked = $this->em->getRepository("App\Entity\Like")->checkLiked($author,$resource, $organization);

        switch ($contentType) {
            case 'application/json':
                $renderType = 'json';
                break;
            case 'application/ld+json':
                $renderType = 'jsonld';
                break;
            case 'application/hal+json':
                $renderType = 'jsonhal';
                break;
            default:
                $contentType = 'application/json';
                $renderType = 'json';
        }

        $total = [
            'organization'    => $organization,
            'resource'   => $resource,
            'author'   => $author,
            'reviews' => $reviews,
            'likes'     => $likes,
            'liked'     => $liked,
            'rating'    => $rating,
        ];

        $response = $this->serializer->serialize(
            $total,
            $renderType,
            ['enable_max_depth'=> true]
        );

        $response = new Response(
            $response,
            Response::HTTP_OK,
            ['content-type' => $contentType]
        );

        $event->setResponse($response);

        return $event;
    }

}
