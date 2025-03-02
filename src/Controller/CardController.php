<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\OpainedCard;
use App\Entity\User;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CardController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CardRepository $cardRepository
    ) {}

    #[Route('/card/{id}', name: 'one_card', methods: ['GET'])]
    public function getOneCard(Request $request, int $id): JsonResponse
    {
        $card = $this->entityManager->getRepository(Card::class)->find($id);

        if(!$card) {
            return $this->json(['error' => 'Card not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            return $this->json(['message' => 'good', 'result' => $card], 200, [], ['groups' => 'card:read']);
        }

        return $this->json(['message' => 'Token invalide']);
    }

    #[Route('/allCardByUser', name: 'all_card_by_user', methods: ['GET'])]
    public function getAllCardByUser(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            $cardAll = $this->cardRepository->findAll();

            $result = [];

            foreach ($cardAll as $card) {
                $obtainedCards = $this->entityManager->getRepository(OpainedCard::class)
                ->findBy(['user' => $user, 'card' => $card]);

                $nbObtained = count($obtainedCards);
                $isObtained = $nbObtained > 0;

                $result[] = [
                    'id' => $card->getId(),
                    'name' => $card->getName(),
                    'content' => $card->getContent(),
                    'isObtained' => $isObtained,
                    'nbObtained' => $nbObtained,
                    'type' => $card->getType(),
                    'rarity' => $card->getRarity(),
                    'version' => $card->getVersion(),
                    'cardFront' => $card->getCardFront(),
                    'cardBack' => $card->getCardBack()
                ];
            }

            return $this->json(['message' => 'good', 'result' => $result], 200, [], ['groups' => 'card:read']);
        }

        return $this->json(['message' => 'Token invalide']);
    }
    
    #[Route('/openBooster', name: 'open_booster', methods: ['GET'])]
    public function openBooster(Request $request): Response
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'token is failed']);
            }



            /* Verifier si je peut ouvrir un booster */

            /* renvoyé 6 carte donc au moin une antagoniste */

            /* et gerez les probabilité */











        }

        return $this->json(['message' => 'Token invalide']);


    }

    #[Route('/getTimeOpenBooster', name: 'get_time_open_booster')]
    public function getTimeOpenBooster(): Response
    {
        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'token is failed']);
            }



            /* Verifier si je peut ouvrir un booster */


            /* renvoyé si le temps si je ne peut pas */








        }

        return $this->json(['message' => 'Token invalide']);


    }
}
