<?php

namespace App\Service;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class TaskService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createTask($title, $description, $date, $category): Task
        {
            $task = new Task();
            $task->setTitle($title);
            $task->setDescription($description);
            $task->setDueDate($date);
            $task->setCreatedAt(new \DateTime());
            $task->setCategory($category);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $task;
        }

    public function editTask($id, $title, $description, $date, $category): Task
        {
            $task = $this->entityManager->getRepository(Task::class)->find($id);

            $task->setTitle($title);
            $task->setDescription($description);
            $task->setDueDate($date);
            $task->setCategory($category);

            $this->entityManager->flush();

            return $task;
        }

    public function taskList()
        {  
            $taskRepository = $this->entityManager->getRepository(Task::class);
            $tasks = $taskRepository->findAll();

            return $tasks;
        }  


    public function deleteTask($id): void
        {
            $task = $this->entityManager->getRepository(Task::class)->find($id);

            $this->entityManager->remove($task);
            $this->entityManager->flush();
        }
}