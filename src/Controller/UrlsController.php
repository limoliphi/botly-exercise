<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class UrlsController extends AbstractController
{
    /**
     * @Route("/", name="app_urls_create")
     */
    public function create(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('original', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Enter the URL to shorten here'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'You need to enter an URL']),
                    new Url(['message' => 'The URL entered is invalid'])
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //valider les infos

            //vérifier que l'url entrée a déjà été raccourcie
            //preview de l'url raccourcie

            //sir l'url n'a pas déjà été raccourcie
            //alors on la raccourcit
            //et on retourne la version preview raccourcie
    }

        return $this->render('urls/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
