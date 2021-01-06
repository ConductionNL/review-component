<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Total;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;

class TotalSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        $organization = $event->getRequest()->get('organization', false);
        $resource = $event->getRequest()->get('resource', false);


        if (!$total instanceof Total) {
            return;
        }

        $rating = $this->em->getRepository("Review")->calculateRating($organization,$resource);
        $reviews = $this->em->getRepository("Review")->calculateReviews($organization,$resource);
        $likes = $this->em->getRepository("Like")->calculateLikes($organization,$resource);

        $total = New Total();
        $total->setOrganization($organization);
        $total->setResource($resource);
        $total->setReviews($reviews);
        $total->setLikes($likes);
        $total->setRating($rating);

        $event->setControllerResult($total);

        return $event;
    }

}
