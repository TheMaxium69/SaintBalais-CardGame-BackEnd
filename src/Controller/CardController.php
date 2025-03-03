<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\OpainedCard;
use App\Entity\OpenBooster;
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
//            $isSpam = false;
            $isSpam = $this->canOpenBooster($user);
            if($isSpam === false){


                /* GESTION BDD*/
                    $ip = $request->getClientIp();
                    if (!isset($ip)) {
                        $newIp = "0.0.0.0";
                    } else {
                        $newIp = $ip;
                    }
                    $booster = new OpenBooster();
                    $booster->setUserId($user);
                    $booster->setOpenAt(new \DateTimeImmutable());
                    $booster->setIp($newIp);
                    $this->entityManager->persist($booster);
                    $this->entityManager->flush();
                /* GESTION BDD*/



                /* renvoyé 6 carte donc au moin une antagoniste */
                

                /* et gerez les probabilité */




                return $this->json(['message' => 'good', 'result' => []], 200, [], ['groups' => 'card:read']);


            } else {

                return $this->json(['message' => 'Vous ne pouvez pas ouvrir un booster']);

            }


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







    function canOpenBooster($user)
    {

        $userIsLock = true;
        if ($user !== null) {

            $twoLatestBooster = $this->entityManager->getRepository(OpenBooster::class)->findTwoLatestBoosterByUser($user);

//            var_dump($twoLatestBooster);

            if ($twoLatestBooster !== null && count($twoLatestBooster) > 1) {

                $booster1 = false;
                $booster2 = false;

                $i = 0;
                foreach ($twoLatestBooster as $booster) {
                    $i++;

                    if ($booster && $booster->getOpenAt() && $booster->getOpenAt() >= (new \DateTimeImmutable())->sub(new \DateInterval('PT12H'))) {

                        /* LOCK - booster dans les 12 dernier heure */
                        if ($i === 1) {
                            $booster1 = false;
                        } else if ($i === 2) {
                            $booster2 = false;
                        }

                    } else {

                        /* BOOSTER DISPO + PLUS DE 12 HEURE */
                        if ($i === 1) {
                            $booster1 = true;
                        } else if ($i === 2) {
                            $booster2 = true;
                        }
                    }

                }

                /* AUCUN DES DEUX BOOSTER DANS LES 12 HEURES*/
                if ($booster1 || $booster2) {
                    $userIsLock = false;
                }

            } else {
                $userIsLock = false; /* notLock - aucun booster ou moin d'un 1 */
            }

        } else {
            $userIsLock = false;
        }


        if (!$userIsLock) {
            return false;
        }

        return true;

    }









}
