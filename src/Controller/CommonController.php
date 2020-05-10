<?php
// src/Controller/CommonController.php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
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
