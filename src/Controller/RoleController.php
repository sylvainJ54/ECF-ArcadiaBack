<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/role', name: 'app_api_role_')]
class RoleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RoleRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
        {
            
            $role = $this->serializer->deserialize($request->getContent(), Role::class, 'json');

            $this->manager->persist($role);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize($role, 'json');
            $location = $this->urlGenerator->generate(
                'app_api_Role_show',
                ['id' => $role->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            
            return new JsonResponse($responseData, Response::HTTP_CREATED,["location" => $location], true);
        }
    
    #[Route('/', name:'show', methods:'GET')]
    public function show(int $id): JsonResponse
    {
        $role = $this->repository->findOneBy(['id' => $id]);
        if ($role) {
            $responseData= $this->serializer->serialize($role, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(data:null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $role = $this->repository->findOneBy(['id' => $id]);
        if ($role) {
            $role = $this->serializer->deserialize(
                $request->getContent(),
                type: Role::class,
                format: 'json',
                context: [AbstractNormalizer::OBJECT_TO_POPULATE => $role]);
            $this->manager->flush();

            return new JsonResponse(data:null,status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(data:null,status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'delete', methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $role = $this->repository->findOneBy(['id' => $id]);
        if ($role) {
            $this->manager->remove($role);
            $this->manager->flush();
            return new JsonResponse (data:null, status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse (data:null, status: Response::HTTP_NOT_FOUND);
    }
}
