<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\UrlFormType;
use App\Repository\UrlRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Str;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrlsController extends AbstractController
{
    private $urlRepository;

    public function __construct(UrlRepository $urlRepository)
    {
        $this->urlRepository = $urlRepository;
    }

    /**
     * @Route("/", name="app_home", methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UrlFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On recherche une url qui a comme original ce qui a été entré (donc déjà raccourci)
            $url = $this->urlRepository->findOneBy(['original' => $form['original']->getData()]);
            //si on ne trouve pas d'URL, on crée une nouvelle URL, et si on la trouve on passe à la suite
            if (! $url) {
                $url = $form->getData();
                //il faudra créer une méthode pour générer une chaîne de caractères aléatoire
                //car il y a une contrainte d'unicité
                $url->setShortened($this->getUniqueShortenedString());
                $em->persist($url);
                $em->flush();;
            }

            //On redirige l'utilisateur vers le preview
            return $this->redirectToRoute('app_urls_preview', ['shortened' => $url->getShortened()]);
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

    private function getUniqueShortenedString(): string
    {
        //ajout d'une classe utilitaire pour la génération de string alétoire
        $shortened = Str::random(6);

        if ($this->urlRepository->findOneBy(compact('shortened'))) {
            return $this->getUniqueShortenedString();
        }

        return $shortened;
    }
}
