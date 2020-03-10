<?php
// api/src/Controller/BookController.php

namespace App\Controller;

use App\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{	
	public function __invoke(Review $data): Review
	{
		//$this->bookPublishingHandler->handle($data);
		
		return $data;
	}
}