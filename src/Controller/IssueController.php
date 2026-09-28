<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Entity\IssueStatusChange;
use App\Entity\RentalUnit;
use App\Form\IssueType;
use App\Repository\IssueRepository;
use App\Service\IssueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Meldungen zur Mietsache
 *
 * @Route("/vorgaenge/meldungen")
 */
class IssueController extends AbstractController
{
    private $issueService;

    public function __construct(IssueService $issueService)
    {
        $this->issueService = $issueService;
    }

    /**
     * @Route("/", name="issue_index")
     */
    public function index(Request $request, IssueRepository $repo): Response
    {
        $filter = $request->query->get('filter', 'open');

        return $this->render('issue/index.html.twig', [
            'issues' => $repo->findByStatusFilter($filter === 'all' ? null : $filter),
            'filter' => $filter,
            'statuses' => Issue::STATUSES,
        ]);
    }

    /**
     * @Route("/neu", name="issue_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $issue = new Issue();
        if ($request->query->get('unit')) {
            $unit = $this->getDoctrine()->getRepository(RentalUnit::class)->find($request->query->get('unit'));
            if ($unit) {
                $issue->setUnit($unit);
                $issue->setProperty($unit->getBuilding()->getProperty());
            }
        }

        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->issueService->createIssue($issue, $this->getUser());
            $this->addFlash('success', 'Meldung '.$issue->getIssueNumber().' erfasst.');

            return $this->redirectToRoute('issue_show', ['id' => $issue->getId()]);
        }

        return $this->render('form_page.html.twig', [
            'title' => 'Neue Meldung erfassen',
            'form' => $form->createView(),
            'back' => $this->generateUrl('issue_index'),
            'section' => 'vorgaenge',
        ]);
    }

    /**
     * @Route("/{id}", name="issue_show", requirements={"id"="\d+"})
     */
    public function show(Issue $issue): Response
    {
        return $this->render('issue/show.html.twig', [
            'issue' => $issue,
            'statuses' => Issue::STATUSES,
            'tenancy' => $this->issueService->getCurrentTenancy($issue),
            'appointments' => $this->issueService->getRelatedAppointments($issue),
        ]);
    }

    /**
     * @Route("/{id}/bearbeiten", name="issue_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Issue $issue): Response
    {
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Meldung gespeichert.');

            return $this->redirectToRoute('issue_show', ['id' => $issue->getId()]);
        }

        return $this->render('form_page.html.twig', [
            'title' => 'Meldung '.$issue->getIssueNumber().' bearbeiten',
            'form' => $form->createView(),
            'back' => $this->generateUrl('issue_show', ['id' => $issue->getId()]),
            'section' => 'vorgaenge',
        ]);
    }

    /**
     * Status ändern (mit Historie)
     *
     * @Route("/{id}/status", name="issue_status", methods={"POST"})
     */
    public function status(Request $request, Issue $issue): Response
    {
        $old = $issue->getStatus();
        $new = $request->request->get('status');

        if ($new && $new !== $old) {
            $issue->setStatus($new);
            $em = $this->getDoctrine()->getManager();
            $em->persist(new IssueStatusChange($issue, $old, $new, $this->getUser()->getUsername(), $request->request->get('comment')));
            $em->flush();
            $this->addFlash('success', 'Status geändert.');
        }

        return $this->redirectToRoute('issue_show', ['id' => $issue->getId()]);
    }

    /**
     * @Route("/{id}/schliessen", name="issue_close", methods={"POST"})
     */
    public function close(Issue $issue): Response
    {
        try {
            $this->issueService->closeIssue($issue);
            $this->addFlash('success', 'Meldung geschlossen.');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('issue_show', ['id' => $issue->getId()]);
    }

    /**
     * Sammeländerung aus der Liste
     *
     * @Route("/sammelaenderung", name="issue_bulk", methods={"POST"})
     */
    public function bulk(Request $request, IssueRepository $repo): Response
    {
        $ids = (array) $request->request->get('ids', []);
        $status = $request->request->get('status');
        if ($status && count($ids) > 0) {
            foreach ($repo->findBy(['id' => $ids]) as $issue) {
                $issue->setStatus($status);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', count($ids).' Meldung(en) geändert.');
        }

        return $this->redirectToRoute('issue_index');
    }

    /**
     * @Route("/{id}/aufgabe", name="issue_add_task", methods={"POST"})
     */
    public function addTask(Request $request, Issue $issue): Response
    {
        $title = trim((string) $request->request->get('title'));
        if ($title === '') {
            $this->addFlash('error', 'Bitte einen Titel für die Aufgabe angeben.');

            return $this->redirectToRoute('issue_show', ['id' => $issue->getId()]);
        }

        $task = $this->issueService->createTaskFromIssue($issue, $title, $this->getUser());
        $this->addFlash('success', 'Aufgabe '.$task->getTaskNumber().' angelegt.');

        return $this->redirectToRoute('task_edit', ['id' => $task->getId()]);
    }

    /**
     * Mitteilung an den Mieter (Druckansicht)
     *
     * @Route("/{id}/druck", name="issue_print")
     */
    public function print(Issue $issue): Response
    {
        return $this->render('issue/print.html.twig', ['issue' => $issue]);
    }
}
