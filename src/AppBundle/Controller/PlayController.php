<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\Position;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Pusher\Pusher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Route("/{user}", name="play", options={"expose"=true})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Pusher\PusherException
     */
    public function playAction(Request $request, User $user)
    {

        //Gestion du rechargement de page
        //On regarde si le joueur a déjà une partie en cours
        if($user->getGame() && $user->getGame()->getIsWaiting() === 0){

            //Récupération du tour en cours et de ma couleur

            //Il y a pas eu de coups de joué et j'ai l'id le plus petit ==> premier à jouer et couleur noire
            if($user->getGame()->getPositions()->isEmpty()){

                foreach($user->getGame()->getUsers()->getIterator() as $i => $item) {
                    //do things with $item
                    if($item !== $user){
                        if($item->getId() > $user->getId()){
                            $myTurn = 1;
                            $myColor = 0;
                        }
                        else{
                            $myTurn = 0;
                            $myColor = 1;
                        }
                    }
                }
            }
            //Il y a eu des coups de joués, je regarde qui a joué le dernier et la couleur et l'id ==> si c'est moi je récupère ma couleur et ce n'est pas mon tour
            else{
                /** @var Position $pos */
                $pos = $user->getGame()->getPositions()->last();
                if($pos->getUser() === $user){
                    $myTurn = 0;
                    $myColor = $pos->getColor();
                }
                //Sinon, je suis l'inverse de la couleur et c'est mon tour
                else{
                    $myTurn = 1;
                    $myColor = $pos->getColor() == 1 ? 0 : 1;
                }
            }

            return $this->render('@App/play/play.html.twig', array('game' => $user->getGame(), 'user' => $user, 'myColor' => $myColor, 'myTurn' => $myTurn));
        }
        else if ($user->getGame() && $user->getGame()->getIsWaiting() === 1){
            return $this->render('@App/play/wait.html.twig', array('user' => $user, 'game' => $user->getGame()));
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

            return $this->render('@App/play/play.html.twig', array('game' => $game, 'user' => $user, 'myColor' => 1, 'myTurn' => 0));

        }
        //Sinon, création d'une partie et mise en attente
        else{

            $game = new Game();
            $game->setIsWaiting(1);

            $this->em->persist($game);
            $this->em->flush();

            //On ajoute à la liste des users le user actuel
            $user->setGame($game);

            $this->em->persist($user);
            $this->em->flush();

            return $this->render('@App/play/wait.html.twig', array('user' => $user, 'game' => $game));

        }
    }

    /**
 * Retourne un joueur
 *
 * @Route("/newTurn/{id}", options={"expose"=true}, name="new_turn")
 * @Method("POST")
 * @param Request $request
 * @return JsonResponse
 * @throws \Pusher\PusherException
 */
    public function newTurnAction(User $user, Request $request)
    {
        if (null !== $request->get('color') && null !== $request->get('posX') && null !== $request->get('posY')) {

            /** @var Game $game */
            $game = $user->getGame();

            $pos = new Position();
            $pos->setGame($game);
            $pos->setUser($user);
            $pos->setPosX($request->get('posX'));
            $pos->setPosY($request->get('posY'));
            $pos->setColor($request->get('color'));
            //Update the game --> Insert a new pos in BDD

            $this->em->persist($pos);

            $game->addPosition($pos);

            $this->em->persist($game);
            $this->em->flush();

            //Trigger pusher
            $data['message'] = $pos->getId();
            $this->pusher->trigger('game-' . $game->getId(), 'new_turn', $data);

        }
        return new JsonResponse(['message' => 'OK']);
    }

    /**
     * @Route("/remove/{id}", options={"expose"=true}, name="remove_pion")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws \Pusher\PusherException
     */
    public function removePionAction(User $user, Request $request)
    {
        if (null !== $request->get('posX') && null !== $request->get('posY')) {

            /** @var Position $pos */
            $pos = $this->em->getRepository(Position::class)->findByCoordinates($request->get('posX'), $request->get('posY'), $user->getGame());

            //Trigger pusher
            $data['posX'] = $pos->getPosY();
            $data['posY'] = $pos->getPosX();
            $this->pusher->trigger('game-' . $user->getGame()->getId(), 'remove_pion', $data);

            $this->em->remove($pos);
            $this->em->flush();

            //On rajoute un point au user
            $user->setPoints($user->getPoints() + 1);
            $this->em->persist($user);
            $this->em->flush();

//            //Si on dépasse 10 points on déclenche la fin de partie
//            if ($user->getPoints() >= 10){
//                $data['data'] = $user->getId();
//                $this->pusher->trigger('game-' . $user->getGame()->getId(), 'end_game', $data);
//            }
        }
        return new JsonResponse(['message' => 'OK']);
    }

    /**
     * Retourne un coup
     *
     * @Route("/getTurn/{id}", options={"expose"=true}, name="get_turn")
     * @Method("GET")
     * @param Position $position
     * @ParamConverter("position", class="AppBundle:Position")
     * @return JsonResponse
     */
    public function GetTurnAction(Position $position)
    {
        return new JsonResponse(['id' => $position->getId(), 'posX' => $position->getPosX(), 'posY' => $position->getPosY(), 'color' => $position->getColor()]);
    }
}
