<?php

namespace App\Controller;

date_default_timezone_set('Europe/Bucharest');
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Form\TaskType;
use App\Service\TaskService;
use Doctrine\ORM\EntityManager;

class ToDoController extends AbstractController
{
    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route('/task/list', name: 'app_list')]
    public function view_list(): Response 
    {
        $tasks = $this->taskService->taskList();

        return $this->render('to_do/task_list.html.twig', ['tasks' => $tasks]);     
    }

    #[Route('/task/view/{id}', name: 'app_view')]
    public function task_view(Task $task) : Response 
    {
        return $this->render('to_do/task_view.html.twig', ['task' => $task,]);
        
    }

    #[Route('/task/delete/{id}', name: 'app_delete')]
    public function task_delete(int $id) : Response
    {
        $this->taskService->deleteTask($id);

        return $this->redirectToRoute('app_list');
    }

    #[Route('/task/create', name: 'app_task_add')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(TaskType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $title = $form->get('title')->getData();
            $description = $form->get('description')->getData();
            $duedate = $form->get('dueDate')->getData();
            $category = $form->get('category')->getData();

            $this->taskService->createTask($title, $description, $duedate, $category);

            return $this->redirectToRoute('app_list');
        }

        return $this->render('to_do/index.html.twig', [
            'task_form' => $form->createView(),
        ]);

        return $this->redirectToRoute('app_list');     
    }


    #[Route('/task/update/{id}', name: 'app_task_update')]
    public function update(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        $form = $this->createForm(TaskType::class,  $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $title = $form->get('title')->getData();
            $description = $form->get('description')->getData();
            $date = $form->get('dueDate')->getData();
            $category = $form->get('category')->getData();
            $this->taskService->editTask($id, $title, $description, $date, $category);

            return $this->redirectToRoute('app_list');
        }

        return $this->render('to_do/index.html.twig', ['task_form' => $form->createView()]);
    }
    }  
    
