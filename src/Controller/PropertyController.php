<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Property;
use App\Form\ContactType;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Notification\ContactNotification;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PropertyController extends AbstractController
{
    private $repository;
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/properties", name="properties.index")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search), 
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('property/index.html.twig', [
            'controller_name' => 'PropertyController',
            'current_menu' => "properties",
            'properties' => $properties,
            'form'       => $form->createView()
        ]);
    }

    /**
     * @Route("/biens/{slug}-{id}", name="properties.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @return Response
     */
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response
    {
        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('properties.show', [
                'id'   => $property->getId(),
                'slug' => $property->getSlug()
            ]);
        }

        if($property->getSlug() !== $slug)
        {
            return $this->redirectToRoute('properties.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug(),
            ], 301); //redirection permanente
        }
        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => "properties",
            'form' => $form->createView()
        ]);
    }
}
