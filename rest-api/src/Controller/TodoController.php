<?php

namespace App\Controller;

use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller for managing Todo tasks via RESTful API endpoints.
 */
class TodoController extends AbstractController
{
    /**
     * Create a new Todo task.
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    #[Route('/api/tasks', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate title
        if (empty($data['title'])) {
            return $this->json(['error' => 'Task title cannot be empty.'], 400);
        }

        // Validate description
        if (!isset($data['description'])) {
            return $this->json(['error' => 'Description cannot be empty.'], 400);
        }

        // Validate is_completed
        if (!isset($data['is_completed']) || !is_bool($data['is_completed'])) {
            return $this->json(['error' => 'is_completed must be a boolean.'], 400);
        }

        // Create and persist Todo entity
        $todo = new Todo();
        $todo->setTitle($data['title']);
        $todo->setDescription($data['description']);
        $todo->setIsCompleted($data['is_completed']);
        $todo->setCreatedAt(new \DateTime());
        $todo->setUpdatedAt(new \DateTime());

        $em->persist($todo);
        $em->flush();
        $id = $todo->getId();

        return $this->json(
            [
                'message' => sprintf('Task with Id %d successfully created.', $id),
                'title' => $todo->getTitle(),
                'description' => $todo->getDescription(),
                'is_completed' => $todo->isCompleted(),
                'created_at' => $todo->getCreatedAt(),
                'updated_at' => $todo->getUpdatedAt(),
            ],
            201,
            [],
            ['groups' => 'todo'] 
        );
    }

    /**
     * Retrieve all Todo tasks.
     *
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    #[Route('/api/tasks', name: 'get_all_todo', methods: ['GET'])]
    public function getAllTasks(EntityManagerInterface $em): JsonResponse
    {
        $todos = $em->getRepository(Todo::class)->findAll();

        $data = [];

        foreach ($todos as $todo) {
            $data[] = [
                'message'=> 'All tasks fetched successfully.',
                'id' => $todo->getId(),
                'title' => $todo->getTitle(),
                'description' => $todo->getDescription(),
                'is_completed' => $todo->isCompleted(),
                'created_at' => $todo->getCreatedAt(),
                'updated_at' => $todo->getUpdatedAt(),
            ];
        }

        return $this->json($data, 200, [], ['groups' => 'todo']);
    }

    /**
     * Retrieve a single Todo task by ID.
     *
     * @param int $id
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    #[Route('/api/tasks/{id}', name: 'api_todo_get_one', methods: ['GET'])]
    public function getOne(int $id, EntityManagerInterface $em): JsonResponse
    {
        $todo = $em->getRepository(Todo::class)->find($id);

        if (!$todo) {
            return $this->json(['error' => 'To-do not found'], 404);
        }

        $fetchedId = $todo->getId();

        return $this->json(
            [
                'message' => sprintf("Task with ID %d found.", $fetchedId),
                'id' => $fetchedId,
                'title' => $todo->getTitle(),
                'completed' => $todo->isCompleted(),
            ],
            200,
            [],
            ['groups' => 'todo']
        );
    }

    /**
     * Update a Todo task.
     *
     * @param Request $request
     * @param Todo|null $todo
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    #[Route('/api/tasks/{id}', methods: ['PUT'])]
    public function update(Request $request, ?Todo $todo, EntityManagerInterface $em): JsonResponse
    {
        if (!$todo) {
            return $this->json(['error' => 'Task with the given ID not found.'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'])) {
            return $this->json(['error' => 'Task title cannot be empty.'], 400);
        }
        $todo->setTitle($data['title']);

        if (!isset($data['description'])) {
            return $this->json(['error' => 'Task description cannot be empty.'], 400);
        }
        $todo->setDescription($data['description']);

        if (!isset($data['is_completed'])) {
            return $this->json(['error' => 'Task status (is_completed) cannot be empty.'], 400);
        }
        $todo->setIsCompleted($data['is_completed']);

        $todo->setUpdatedAt(new \DateTime());

        $em->flush();

       return $this->json([
    'message' => sprintf('Task with ID %d successfully updated.', $todo->getId()),
    'data' => [
        'id' => $todo->getId(),
        'title' => $todo->getTitle(),
        'description' => $todo->getDescription(),
        'is_completed' => $todo->isCompleted(),
        'created_at' => $todo->getCreatedAt(),
        'updated_at' => $todo->getUpdatedAt(),
    ]
], 200);
    }

    /**
     * Delete a Todo task.
     *
     * @param Todo|null $todo
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    #[Route('/api/tasks/{id}', methods: ['DELETE'])]
    public function delete(?Todo $todo, EntityManagerInterface $em): JsonResponse
    {
        if (!$todo) {
            return $this->json(['error' => 'Task not found.'], 404);
        }

        $deletedId = $todo->getId();
        $em->remove($todo);
        $em->flush();

        return new JsonResponse(sprintf("Task with ID %d successfully deleted.", $deletedId), 200);
    }
}