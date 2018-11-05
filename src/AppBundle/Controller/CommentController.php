<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Game;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Pusher\Pusher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller.
 *
 * @Route("comment")
 */
class CommentController extends Controller
{
    public $pusher;
    public $em;


    public function __construct(Pusher $pusher, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->pusher = $pusher;
    }

    /**
     * Poste un commentaire dans une partie
     *
     * @Route("/post/{id}", options={"expose"=true}, name="comment_post")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function postAction(Request $request, Game $game)
    {

        if (!empty($request->get('content'))) {
            $comment = new Comment();

            $comment
                ->setContent($request->get('content'))
                ->setGame($game)
                ->setSender($this->getUser());

            //Envoi avec pusher pour temps réel
            $data['message'] = $game->getId();
            $this->pusher->trigger('game-' . $game->getId(), 'new_comment', $data);

            $this->em->persist($comment);
            $this->em->flush();

            return $this->render('@App/comment/single.html.twig', ['comment' => $comment]);
        }

        return new JsonResponse(['error' => 'Tous les champs sont obligatoires']);
    }
}
