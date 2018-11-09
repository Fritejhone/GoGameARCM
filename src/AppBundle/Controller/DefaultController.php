<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Services\PusherLogger;
use Doctrine\ORM\EntityManagerInterface;
use Pusher\Pusher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", options={"expose"=true}, name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $user->setPoints(0);

            //Création d'un USER dans la BDD
            $this->em->persist($user);

            $this->em->flush();

            return $this->redirectToRoute('play', array('user' => $user->getId()));
        }

        return $this->render('@App/default/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

//    /**
//     * @Route("/end", options={"expose"=true}, name="end_saloon")
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function endAction(Request $request)
//    {
//        return $this->render('@App/default/end.html.twig');
//    }


}
