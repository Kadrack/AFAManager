<?php
// src/Controller/SecretariatController.php
namespace App\Controller;

use App\Entity\SecretariatSupporter;

use App\Form\SecretariatType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secretariat", name="secretariat_")
 */
class SecretariatController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('Secretariat/index.html.twig');
    }

    /**
     * @Route("/sympathisant_liste", name="supporter_index")
     */
    public function supporterIndex()
    {
        $supporters = $this->getDoctrine()->getRepository(SecretariatSupporter::class)->findAll();

        return $this->render('Secretariat/supporter_index.html.twig', array('supporters' => $supporters));
    }

    /**
     * @Route("/sympathisant_ajouter", name="supporter_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function supporterAdd(Request $request)
    {
        $form = $this->createForm(SecretariatType::class, new SecretariatSupporter(), array('form' => 'supporter_create', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $address = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_supporter_index');
        }

        return $this->render('Secretariat/supporter_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/sympathisant_modifier/{supporter<\d+>}", name="supporter_update")
     * @param Request $request
     * @param SecretariatSupporter $supporter
     * @return RedirectResponse|Response
     */
    public function supporterUpdate(Request $request, SecretariatSupporter $supporter)
    {
        $form = $this->createForm(SecretariatType::class, $supporter, array('form' => 'supporter_update', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_supporter_index');
        }

        return $this->render('Secretariat/supporter_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/sympathisant_supprimer/{supporter<\d+>}", name="supporter_delete")
     * @param Request $request
     * @param SecretariatSupporter $supporter
     * @return RedirectResponse|Response
     */
    public function supporterDelete(Request $request, SecretariatSupporter $supporter)
    {
        $form = $this->createForm(SecretariatType::class, $supporter, array('form' => 'supporter_delete', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($supporter);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_supporter_index');
        }

        return $this->render('Secretariat/supporter_delete.html.twig', array('form' => $form->createView()));
    }
}
