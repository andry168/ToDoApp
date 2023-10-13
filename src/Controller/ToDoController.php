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
use Knp\Component\Pager\PaginatorInterface;


class ToDoController extends AbstractController
{
    #[Route('/task/list', name: 'app_list')]
    public function view_list(TaskRepository $taskRepository ) : Response 
    {
        $tasks = $taskRepository->findAll();

        return $this->render('to_do/task_list.html.twig', ['tasks' => $tasks,]);     
    }

    #[Route('/task/view/{id}', name: 'app_view')]
    public function task_view(Task $task) : Response 
    {
        return $this->render('to_do/task_view.html.twig', ['task' => $task,]);
        
    }

    #[Route('/task/delete/{id}', name: 'app_delete')]
    public function task_delete(int $id, EntityManagerInterface $entityManager) : Response
    {
        $task = $entityManager->getRepository(Task::class)->find($id);

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_list');
    }

    #[Route('/task/create', name: 'app_task_add')]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, ['method'=>'POST']);
        $form->handleRequest($request);

        if(!($form->isSubmitted() && $form->isValid())){
            return $this->render('to_do/index.html.twig', ['task_form'=>$form->createView()]);
        }
        $task = $form->getData();
        $task->setCreatedAt(new \DateTime());
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_list');     
    }

    #[Route('/task/update/{id}', name: 'app_task_update')]
    public function editTask(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        $form = $this->createForm(TaskType::class, $task, [
            'method' => 'POST',
        ]);

        $form->handleRequest($request);
        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('to_do/index.html.twig', [
                'task_form' => $form->createView(),
            ]);
        }
        $task = $form->getData();
        $entityManager->persist($task);
        $entityManager->flush();
        return $this->redirectToRoute('app_task_add');
    }  
    
}
