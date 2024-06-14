<?php

namespace App\Controller;

use App\Entity\VeterinaryReport;
use App\Repository\VeterinaryReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/veterinaryReport', name: 'app_api_veterinaryReport_')]
class VeterinaryReportController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private VeterinaryReportRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
        {
            
            $veterinaryReport = $this->serializer->deserialize($request->getContent(), VeterinaryReport::class, 'json');

            $this->manager->persist($veterinaryReport);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize($veterinaryReport, 'json');
            $location = $this->urlGenerator->generate(
                'app_api_VeterinaryReport_show',
                ['id' => $veterinaryReport->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            
            return new JsonResponse($responseData, Response::HTTP_CREATED,["location" => $location], true);
        }
    
    #[Route('/', name:'show', methods:'GET')]
    public function show(int $id): JsonResponse
    {
        $veterinaryReport = $this->repository->findOneBy(['id' => $id]);
        if ($veterinaryReport) {
            $responseData= $this->serializer->serialize($veterinaryReport, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(data:null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $veterinaryReport = $this->repository->findOneBy(['id' => $id]);
        if ($veterinaryReport) {
            $veterinaryReport = $this->serializer->deserialize(
                $request->getContent(),
                type: VeterinaryReport::class,
                format: 'json',
                context: [AbstractNormalizer::OBJECT_TO_POPULATE => $veterinaryReport]);
            $this->manager->flush();

            return new JsonResponse(data:null,status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(data:null,status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'delete', methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $veterinaryReport = $this->repository->findOneBy(['id' => $id]);
        if ($veterinaryReport) {
            $this->manager->remove($veterinaryReport);
            $this->manager->flush();
            return new JsonResponse (data:null, status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse (data:null, status: Response::HTTP_NOT_FOUND);
    }
}
