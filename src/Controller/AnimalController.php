<?php

namespace App\Controller;

use App\Entity\Animal;
use OpenApi\Annotations as OA;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/animal', name: 'app_api_animal_')]
class AnimalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AnimalRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }
    #[Route(methods: 'POST')]

    /** @OA\Post(
     *     path="/api/animal",
     *     summary="Création d'un nouvel animal",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'animal à créer",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom de l'animal"),
     *             @OA\Property(property="state", type="string", example="Etat de lanimal"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Animal créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom de l'animal"),
     *             @OA\Property(property="VeterinaryReport", type="string", example="Rapport du vétérinaire"),
     *         )
     *     )
     * )
     */

    public function new(Request $request): JsonResponse
        {
            
            $animal = $this->serializer->deserialize($request->getContent(), Animal::class, 'json');

            $this->manager->persist( $animal);
            $this->manager->flush();
            
            $responseData = $this->serializer->serialize( $animal, 'json');
            $location = $this->urlGenerator->generate(
                'app_api_animal_show',
                ['id' =>  $animal->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );
            
            return new JsonResponse($responseData, Response::HTTP_CREATED,["location" => $location], true);
        }
    
    #[Route('/{id}', name:'show', methods:'GET')]

    /** @OA\Get(
     *     path="/api/animal/{id}",
     *     summary="Affichage d'un animal",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Identifiant de l'animal",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom de l'animal"),
     *             @OA\Property(property="VeterinaryReport", type="string", example="Rapport du vétérinaire"),
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
        $animal = $this->repository->findOneBy(['id' => $id]);
        if ( $animal) {
            $responseData= $this->serializer->serialize( $animal, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(data:null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name:'edit', methods: 'PUT')]
    

    public function edit(int $id, Request $request): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);
        if ( $animal) {
            $animal = $this->serializer->deserialize(
                $request->getContent(),
                type: Animal::class,
                format: 'json',
                context: [AbstractNormalizer::OBJECT_TO_POPULATE =>  $animal]);
            $this->manager->flush();

            return new JsonResponse(data:null,status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(data:null,status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name:'delete', methods: 'DELETE')]

    public function delete(int $id): Response
    {
        $animal = $this->repository->findOneBy(['id' => $id]);
        if ($animal) {
            $this->manager->remove($animal);
            $this->manager->flush();
            return new JsonResponse (data:null, status: Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse (data:null, status: Response::HTTP_NOT_FOUND);
    }
}
