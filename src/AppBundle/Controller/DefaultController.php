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
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

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
