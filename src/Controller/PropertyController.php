<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Property;
use App\Form\BuildingType;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use App\Service\AppointmentService;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Objekte und Gebäude
 *
 * @Route("/stammdaten/objekte")
 */
class PropertyController extends AbstractController
{
    /**
     * @Route("/", name="property_index")
     */
    public function index(PropertyRepository $repo): Response
    {
        return $this->render('property/index.html.twig', ['properties' => $repo->findBy([], ['name' => 'ASC'])]);
    }

    /**
     * @Route("/neu", name="property_new", methods={"GET","POST"})
     */
    public function new(Request $request, PropertyService $propertyService): Response
    {
        $property = new Property();
        $property->setPropertyNumber($propertyService->nextPropertyNumber());

        return $this->handleForm($request, $property, 'Neues Objekt');
    }

    /**
     * @Route("/{id}", name="property_show", requirements={"id"="\d+"})
     */
    public function show(Property $property, PropertyService $propertyService, AppointmentService $appointments): Response
    {
        return $this->render('property/show.html.twig', [
            'property' => $property,
            'stats' => $propertyService->getStatistics($property),
            'appointments' => $appointments->findFor('PROPERTY', $property->getId()),
        ]);
    }

    /**
     * @Route("/{id}/bearbeiten", name="property_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Property $property): Response
    {
        return $this->handleForm($request, $property, 'Objekt bearbeiten');
    }

    /**
     * @Route("/{id}/gebaeude/neu", name="building_new", methods={"GET","POST"})
     */
    public function newBuilding(Request $request, Property $property): Response
    {
        $building = new Building();
        $building->setProperty($property);
        $building->setBuildingNumber(sprintf('%s-G%d', $property->getPropertyNumber(), $property->getBuildings()->count() + 1));
        $building->setAddress($property->getStreet().' '.$property->getHouseNumber());

        return $this->handleBuildingForm($request, $building, 'Neues Gebäude');
    }

    /**
     * @Route("/gebaeude/{id}/bearbeiten", name="building_edit", methods={"GET","POST"})
     */
    public function editBuilding(Request $request, Building $building): Response
    {
        return $this->handleBuildingForm($request, $building, 'Gebäude bearbeiten');
    }

    private function handleForm(Request $request, Property $property, string $title): Response
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($property);
            $em->flush();
            $this->addFlash('success', 'Objekt gespeichert.');

            return $this->redirectToRoute('property_show', ['id' => $property->getId()]);
        }

        return $this->render('form_page.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'back' => $this->generateUrl('property_index'),
            'section' => 'stammdaten',
        ]);
    }

    private function handleBuildingForm(Request $request, Building $building, string $title): Response
    {
        $form = $this->createForm(BuildingType::class, $building);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($building);
            $em->flush();
            $this->addFlash('success', 'Gebäude gespeichert.');

            return $this->redirectToRoute('property_show', ['id' => $building->getProperty()->getId()]);
        }

        return $this->render('form_page.html.twig', [
            'title' => $title.' - '.$building->getProperty()->getName(),
            'form' => $form->createView(),
            'back' => $this->generateUrl('property_show', ['id' => $building->getProperty()->getId()]),
            'section' => 'stammdaten',
        ]);
    }
}
