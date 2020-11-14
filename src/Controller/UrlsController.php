<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url as UrlConstraints;

class UrlsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET|POST")
     * @Route("/", name="app_urls_create", methods="GET|POST")
     */
    public function create(Request $request, UrlRepository $urlRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('original', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Enter the URL to shorten here'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'You need to enter an URL']),
                    new UrlConstraints(['message' => 'The URL entered is invalid'])
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //vérifier que l'url entrée a déjà été raccourcie
            $url = $urlRepository->findOneBy(['original' => $form['original']->getData()]);

            if ($url) {
                return $this->redirectToRoute('app_urls_preview', ['shortened' => $url->getShortened()]);
            }

            //preview de l'url raccourcie

            //sir l'url n'a pas déjà été raccourcie
            //alors on la raccourcit
            //et on retourne la version preview raccourcie
    }

        return $this->render('urls/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/{shortened}/preview", name="app_urls_preview", methods="GET")
     */
    public function preview(Url $url): Response
    {
        return $this->render('urls/preview.html.twig', compact('url'));
    }

    /**
     * @Route ("/{shortened}", name="app_urls_show", methods="GET")
     */
    public function show(Url $url): Response
    {
        return $this->redirect($url->getOriginal());
    }
}
