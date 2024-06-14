<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Repository\NoticeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/notice', name: 'app_api_notice_')]
class NoticeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private NoticeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
        {
            
            $notice = $this->serializer->deserialize($request->getContent(), Notice::class, 'json');

            $this->manager->persist($notice);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize($notice, 'json');
            $location = $this->urlGenerator->generate(
                'app_api_Notice_show',
                ['id' => $notice->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            
            return new JsonResponse($responseData, Response::HTTP_CREATED,["location" => $location], true);
        }
    
    #[Route('/', name:'show', methods:'GET')]
    public function show(int $id): JsonResponse
    {
        $notice = $this->repository->findOneBy(['id' => $id]);
        if ($notice) {
            $responseData= $this->serializer->serialize($notice, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(data:null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $notice = $this->repository->findOneBy(['id' => $id]);
        if ($notice) {
            $notice = $this->serializer->deserialize(
                $request->getContent(),
                type: Notice::class,
                format: 'json',
                context: [AbstractNormalizer::OBJECT_TO_POPULATE => $notice]);
            $this->manager->flush();

            return new JsonResponse(data:null,status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(data:null,status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'delete', methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $notice = $this->repository->findOneBy(['id' => $id]);
        if ($notice) {
            $this->manager->remove($notice);
            $this->manager->flush();
            return new JsonResponse (data:null, status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse (data:null, status: Response::HTTP_NOT_FOUND);
    }
}
