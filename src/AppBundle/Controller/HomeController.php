<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * A simple home page.
 * @package AppBundle\Controller
 */
class HomeController extends Controller
{
    /**
     * A simple home page.
     * @Route("/", name="home page")
     */
    public function indexAction(Request $request)
    {
        return $this->render('home.html.twig');
    }
}