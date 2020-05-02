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

class SecretariatController extends AbstractController
{
    /**
     * @Route("/secretariat/", name="secretariat_index")
     */
    public function index()
    {
        return $this->render('Secretariat/index.html.twig');
    }

    /**
     * @Route("/secretariat/sympathisant_liste", name="secretariat_supporter_index")
     */
    public function supporterIndex()
    {
        $supporters = $this->getDoctrine()->getRepository(SecretariatSupporter::class)->findAll();

        return $this->render('Secretariat/supporter_index.html.twig', array('supporters' => $supporters));
    }

    /**
     * @Route("/secretariat/sympathisant_ajouter", name="secretariat_supporter_create")
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
     * @Route("/secretariat/sympathisant_modifier/{supporter_id<\d+>}", name="secretariat_supporter_update")
     * @param Request $request
     * @param int $supporter_id
     * @return RedirectResponse|Response
     */
    public function supporterUpdate(Request $request, int $supporter_id)
    {
        $supporter = $this->getDoctrine()->getRepository(SecretariatSupporter::class)->findOneBy(['secretariat_supporter_id' => $supporter_id]);

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
     * @Route("/secretariat/sympathisant_supprimer/{supporter_id<\d+>}", name="secretariat_supporter_delete")
     * @param Request $request
     * @param int $supporter_id
     * @return RedirectResponse|Response
     */
    public function supporterDelete(Request $request, int $supporter_id)
    {
        $supporter = $this->getDoctrine()->getRepository(SecretariatSupporter::class)->findOneBy(['secretariat_supporter_id' => $supporter_id]);

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
