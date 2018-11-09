<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Game;
use AppBundle\Entity\User;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


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
     * @param User $user
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Pusher\PusherException
     */
    public function postAction(User $user, Request $request)
    {
        if (!empty($request->get('content'))) {
            $comment = new Comment();

            $comment
                ->setContent($request->get('content'))
                ->setGame($user->getGame())
                ->setSender($user);

            $this->em->persist($comment);
            $this->em->flush();

            //Envoi avec pusher pour temps réel
            $data['message'] = $comment->getId();
            $this->pusher->trigger('game-' . $user->getGame()->getId(), 'new_comment', $data);

            return $this->render('@App/comment/single.html.twig', ['comment' => $comment, 'user' => $user]);
        }

        return new JsonResponse(['error' => 'Tous les champs sont obligatoires']);
    }

    /**
     * Retourne le dernier commentaire d'une partie
     *
     * @Route("/last/{comment_id}/{user_id}", options={"expose"=true}, name="get_last_comment")
     * @ParamConverter("comment", options={"id" = "comment_id"})
     * @ParamConverter("user", options={"id" = "user_id"})
     * @Method("GET")
     * @param Comment $comment
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLastCommentAction(Comment $comment, User $user)
    {
        return $this->render('@App/comment/single.html.twig', ['comment' => $comment, 'user' => $user]);
    }
}
