<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoPageController extends AbstractController
{
    // ---------------------------
    // List all To-Dos
    // ---------------------------
    #[Route('/todos', name: 'todo_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): Response
    {
        $todos = $em->getRepository(Todo::class)->findAll();

        return $this->render('todo/list.html.twig', [
            'todos' => $todos,
        ]);
    }

    // ---------------------------
    // Show a single To-Do
    // ---------------------------
    #[Route('/todos/{id}', name: 'todo_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $todo = $em->getRepository(Todo::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException('Task not found');
        }

        return $this->render('todo/show.html.twig', [
            'todo' => $todo,
        ]);
    }

    // ---------------------------
    // Create a new To-Do
    // ---------------------------
    #[Route('/todos/new', name: 'todo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setCreatedAt(new \DateTime());
            $todo->setUpdatedAt(new \DateTime());

            $em->persist($todo);
            $em->flush();

            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ---------------------------
    // Edit a To-Do
    // ---------------------------
    #[Route('/todos/{id}/edit', name: 'todo_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $todo = $em->getRepository(Todo::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException('Task not found');
        }

        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setUpdatedAt(new \DateTime());
            $em->flush();

            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ---------------------------
    // Delete a To-Do
    // ---------------------------
    #[Route('/todos/{id}/delete', name: 'todo_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $todo = $em->getRepository(Todo::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException('Task not found');
        }

        // Optional: CSRF protection
        if ($this->isCsrfTokenValid('delete'.$todo->getId(), $request->request->get('_token'))) {
            $em->remove($todo);
            $em->flush();
        }

        return $this->redirectToRoute('todo_list');
    }
}