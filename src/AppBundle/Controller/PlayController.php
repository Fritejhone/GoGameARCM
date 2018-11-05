<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Pusher\Pusher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Play controller.
 *
 * @Route("play")
 */
class PlayController extends Controller
{
    public $em;
    public $pusher;

    public function __construct(Pusher $pusher, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->pusher = $pusher;
    }

    /**
     * @Route("/{user}", name="play")
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Pusher\PusherException
     */
    public function playAction(Request $request, User $user)
    {
        //Gestion du rechargement de page
        //On regarde si le joueur a déjà une partie en cours
        if($user->getGame()){
            return $this->render('@App/play/play.html.twig', array('game' => $user->getGame(), 'user' => $user));
        }

        //On cherche s'il y a une partie en attente d'un second joueur
        $games = $this->em->getRepository(Game::class)->findByStatusAndNotMe($user);

        //Si il y a une partie en attente
        if($games)
        {
            //On prend la partie la plus ancienne (ordre de création par id directement trié par la requete)
            /** @var Game $game */
            $game = $games[0];

            //On retire le statut 'en attente du jeu'
            $game->setIsWaiting(0);

            $this->em->persist($game);
            $this->em->flush();

            //On ajoute à la liste des users le user actuel
            $user->setGame($game);

            $this->em->persist($user);
            $this->em->flush();

            //On notifie le user en attente de l'arrivée d'un adversaire
            $data['message'] = $user->getId();
            $this->pusher->trigger('game-' . $game->getId(), 'new_player', $data);

        }
        //Sinon, création d'une partie
        else{

            $game = new Game();
            $game->setIsWaiting(1);

            $this->em->persist($game);
            $this->em->flush();

            //On ajoute à la liste des users le user actuel
            $user->setGame($game);

            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->render('@App/play/play.html.twig', array('game' => $game, 'user' => $user));
    }

    /**
     * Retourne un joueur
     *
     * @Route("/newTurn", options={"expose"=true}, name="new_turn")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function newTurnAction(Request $request)
    {
        if (null !== $request->get('id') && null !== $request->get('color') && null !== $request->get('posX') && null !== $request->get('posY')) {
            //Update the game

        }
        return new JsonResponse(['message' => 'OK']);
    }

//    /**
//     * @Route("/pusher", name="pusher")
//     * @param PusherLogger $log
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
//     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
//     */
//    public function testPusher(PusherLogger $log)
//    {
//
//        /** @var Pusher $pusher */
//        $pusher = $this->container->get('lopi_pusher.pusher');
//        $pusher->set_logger($log);
//
//        $data['message'] = 'hello world';
//        dump($pusher->trigger('test_channel', 'my_event', $data));
//
//        return $this->render('@App/default/pusher.html.twig');
//
//
//    }

}
