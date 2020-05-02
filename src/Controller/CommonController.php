<?php
// src/Controller/CommonController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{
    /**
     * @Route("/", name="common_index")
     */    
    public function index()
    {
        return $this->render('Common/index.html.twig');
    }
}
