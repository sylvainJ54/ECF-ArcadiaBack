<?php

namespace App\Controller;

use App\Entity\Habitat;
use OpenApi\Annotations as OA;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/habitat', name: 'app_api_habitat_')]
class HabitatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }
    #[Route(methods: 'POST')]

        /** @OA\Post(
     *     path="/api/habitat",
     *     summary="Création d'un nouvel habitat",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'habitat à créer",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom de l'habitat"),
     *             @OA\Property(property="description", type="string", example="description de l'habitat"),
     *             @OA\Property(property="comment", type="string", example="commentaires sur l'habitat"),
     *             @OA\Property(property="animal", type="string", example="animal de l'habitat"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="habitat créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom de l'habitat"),
     *             @OA\Property(property="description", type="string", example="description de l'habitat"),
     *             @OA\Property(property="comment", type="string", example="commentaire sur l'habitat"),
     *             @OA\Property(property="animal", type="string", example="animaux de l'habitat")
     *         )
     *     )
     * )
     */

    public function new(Request $request): JsonResponse
        {
            
            $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');

            $this->manager->persist($habitat);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize($habitat, 'json');
            $location = $this->urlGenerator->generate(
                'app_api_Habitat_show',
                ['id' => $habitat->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            
            return new JsonResponse($responseData, Response::HTTP_CREATED,["location" => $location], true);
        }
    
    #[Route('/{id}', name:'show', methods:'GET')]
        /** @OA\Get(
     *     path="/api/habitat/{id}",
     *     summary="Affichage de l'habitat",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Identifiant de l'habitat",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habitat trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *           @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom de l'habitat"),
     *             @OA\Property(property="description", type="string", example="description de l'habitat"),
     *             @OA\Property(property="comment", type="string", example="commentaire sur l'habitat"),
     *             @OA\Property(property="animal", type="string", example="animaux de l'habitat")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat non trouvé"
     *     )
     * )
     */

    public function show(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if ($habitat) {
            $responseData= $this->serializer->serialize($habitat, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(data:null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if ($habitat) {
            $habitat = $this->serializer->deserialize(
                $request->getContent(),
                type: Habitat::class,
                format: 'json',
                context: [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]);
            $this->manager->flush();

            return new JsonResponse(data:null,status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(data:null,status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'delete', methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if ($habitat) {
            $this->manager->remove($habitat);
            $this->manager->flush();
            return new JsonResponse (data:null, status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse (data:null, status: Response::HTTP_NOT_FOUND);
    }
}
