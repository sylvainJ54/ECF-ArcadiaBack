<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/picture', name: 'app_api_picture_')]
class PictureController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private PictureRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
        {
            
            $picture = $this->serializer->deserialize($request->getContent(), Picture::class, 'json');

            $this->manager->persist($picture);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize($picture, 'json');
            $location = $this->urlGenerator->generate(
                'app_api_Picture_show',
                ['id' => $picture->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            
            return new JsonResponse($responseData, Response::HTTP_CREATED,["location" => $location], true);
        }
    
    #[Route('/', name:'show', methods:'GET')]
    public function show(int $id): JsonResponse
    {
        $picture = $this->repository->findOneBy(['id' => $id]);
        if ($picture) {
            $responseData= $this->serializer->serialize($picture, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(data:null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $picture = $this->repository->findOneBy(['id' => $id]);
        if ($picture) {
            $picture = $this->serializer->deserialize(
                $request->getContent(),
                type: Picture::class,
                format: 'json',
                context: [AbstractNormalizer::OBJECT_TO_POPULATE => $picture]);
            $this->manager->flush();

            return new JsonResponse(data:null,status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(data:null,status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'delete', methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $picture = $this->repository->findOneBy(['id' => $id]);
        if ($picture) {
            $this->manager->remove($picture);
            $this->manager->flush();
            return new JsonResponse (data:null, status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse (data:null, status: Response::HTTP_NOT_FOUND);
    }
}
