<?php

namespace App\Controller;

use App\Entity\Breed;
use OpenApi\Annotations as OA;
use App\Repository\BreedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/breed', name: 'app_api_breed_')]
class BreedController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private BreedRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }
    #[Route(methods: 'POST')]
    
        /** @OA\Post(
     *     path="/api/breed",
     *     summary="Création d'une nouvelle race d'animal",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la race d'animal à créer",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="label", type="string", example="Nom de la race de l'animal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="race d'animal créée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="label", type="string", example="Nom de l'animal"),
     *             @OA\Property(property="Vanimal", type="string", example="nom de l'animal"),
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
        {
            
            $breed = $this->serializer->deserialize($request->getContent(), Breed::class, 'json');

            $this->manager->persist($breed);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize($breed, 'json');
            $location = $this->urlGenerator->generate(
                'app_api_breed_show',
                ['id' => $breed->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            
            return new JsonResponse($responseData, Response::HTTP_CREATED,["location" => $location], true);
        }
    
    #[Route('/{id}', name:'show', methods:'GET')]
        /** @OA\Get(
     *     path="/api/breed/{id}",
     *     summary="Affichage d'une race ",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Identifiant de la race",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="race trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="label", type="string", example="Nom de la race"),
     *             @OA\Property(property="animal", type="string", example="Animal de la race"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal non trouvé"
     *     )
     * )
     */

    public function show(int $id): JsonResponse
    {
        $breed = $this->repository->findOneBy(['id' => $id]);
        if ($breed) {
            $responseData= $this->serializer->serialize($breed, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(data:null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $breed = $this->repository->findOneBy(['id' => $id]);
        if ($breed) {
            $breed = $this->serializer->deserialize(
                $request->getContent(),
                type: Breed::class,
                format: 'json',
                context: [AbstractNormalizer::OBJECT_TO_POPULATE => $breed]);
            $this->manager->flush();

            return new JsonResponse(data:null,status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(data:null,status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/', name:'delete', methods: 'DELETE')]
    public function delete(int $id): Response
    {
        $breed = $this->repository->findOneBy(['id' => $id]);
        if ($breed) {
            $this->manager->remove($breed);
            $this->manager->flush();
            return new JsonResponse (data:null, status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse (data:null, status: Response::HTTP_NOT_FOUND);
    }
}
