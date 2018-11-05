<?php
/**
 * Created by PhpStorm.
 * User: Adrien Ruisseau
 * Date: 18/09/2018
 * Time: 16:38
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("user")
 */
class UserController extends Controller
{
    public $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Retourne un joueur
     *
     * @Route("/{id}", options={"expose"=true}, name="get_player")
     * @Method("GET")
     * @param User $user
     * @return JsonResponse
     */
    public function getPlayerAction(User $user)
    {
        return new JsonResponse(['id' => $user->getId(),'pseudo' => $user->getPseudo() ]);
    }

    /**
     * Retourne un joueur
     *
     * @Route("/opponent/{me}/{game}", options={"expose"=true}, name="get_opponent")
     * @Method("GET")
     * @param User $me
     * @param Game $game
     * @return JsonResponse
     */
    public function getOpponentAction(User $me, Game $game)
    {
        /** @var User $opponent */
        $opponent = $this->em->getRepository(User::class)->findOpponentByMeAndGame($me, $game);

        return new JsonResponse(['id' => $opponent->getId(),'pseudo' => $opponent->getPseudo()]);
    }

}