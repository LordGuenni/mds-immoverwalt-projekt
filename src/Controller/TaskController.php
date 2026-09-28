<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vorgaenge/aufgaben")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task_index")
     */
    public function index(Request $request, TaskRepository $repo): Response
    {
        $all = $request->query->get('filter') === 'all';

        return $this->render('task/index.html.twig', [
            'tasks' => $all ? $repo->findBy([], ['dueDate' => 'ASC']) : $repo->findOpen(),
            'all' => $all,
        ]);
    }

    /**
     * @Route("/neu", name="task_new", methods={"GET","POST"})
     */
    public function new(Request $request, TaskService $taskService): Response
    {
        $task = new Task();
        $task->setResponsible($this->getUser());
        if ($request->query->get('issue')) {
            $issue = $this->getDoctrine()->getRepository(Issue::class)->find($request->query->get('issue'));
            if ($issue) {
                $task->setIssue($issue);
                $task->setProperty($issue->getProperty());
                $task->setUnit($issue->getUnit());
            }
        }

        return $this->handleForm($request, $task, $taskService, 'Neue Aufgabe');
    }

    /**
     * @Route("/{id}/bearbeiten", name="task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task, TaskService $taskService): Response
    {
        return $this->handleForm($request, $task, $taskService, 'Aufgabe '.$task->getTaskNumber());
    }

    /**
     * @Route("/{id}/erledigt", name="task_done", methods={"POST"})
     */
    public function done(Task $task): Response
    {
        $task->setStatus('DONE');
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Aufgabe erledigt.');

        return $task->getIssue()
            ? $this->redirectToRoute('issue_show', ['id' => $task->getIssue()->getId()])
            : $this->redirectToRoute('task_index');
    }

    /**
     * Erledigte Aufgabe wieder öffnen - nur mit Begründung.
     *
     * @Route("/{id}/wiedereroeffnen", name="task_reopen", methods={"POST"})
     */
    public function reopen(Request $request, Task $task): Response
    {
        $reason = trim((string) $request->request->get('reason'));
        if ($task->getStatus() === 'DONE' && $reason === '') {
            $this->addFlash('error', 'Bitte eine Begründung angeben.');

            return $this->redirectToRoute('task_edit', ['id' => $task->getId()]);
        }

        $task->setStatus('OPEN');
        $task->setNotes(trim($task->getNotes()."\n[".date('d.m.Y').'] wieder eröffnet: '.$reason));
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('task_edit', ['id' => $task->getId()]);
    }

    private function handleForm(Request $request, Task $task, TaskService $taskService, string $title): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $taskService->save($task);
                $this->addFlash('success', 'Aufgabe gespeichert.');

                return $this->redirectToRoute('task_edit', ['id' => $task->getId()]);
            } catch (\RuntimeException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('task/edit.html.twig', [
            'title' => $title,
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }
}
