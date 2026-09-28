<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private $em;
    private $taskRepository;

    public function __construct(EntityManagerInterface $em, TaskRepository $taskRepository)
    {
        $this->em = $em;
        $this->taskRepository = $taskRepository;
    }

    public function save(Task $task): void
    {
        if (!$task->getTaskNumber()) {
            $task->setTaskNumber($this->taskRepository->nextNumber());
        }

        if ($task->getIssue() && !$task->getProperty()) {
            $task->setProperty($task->getIssue()->getProperty());
        }

        // Externer Dienstleister braucht einen Ansprechpartner
        if ($task->getServiceProvider()) {
            if (!$task->getContactPerson()) {
                $task->setContactPerson($task->getServiceProvider()->getContactPerson());
            }
            if (!$task->getContactPerson()) {
                throw new \RuntimeException('Bitte einen Ansprechpartner beim Dienstleister angeben.');
            }
            if ($task->getStatus() === 'OPEN') {
                $task->setStatus('ASSIGNED');
            }
        }

        $this->em->persist($task);
        $this->em->flush();
    }
}
