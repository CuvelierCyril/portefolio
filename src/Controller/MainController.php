<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Message;
use \DateTime;

class MainController extends AbstractController{

    /**
     * @route("/", name="index")
     */
    public function index(){
        return $this->render('index.html.twig');
    }

    /**
     * @route("/mes-competences/", name="skills")
     */
    public function skills(){
        return $this->render('skills.html.twig');
    }

    /**
     * @route("/mon-experience/", name="experience")
     */
    public function experience(){
        return $this->render('experience.html.twig');
    }

    /**
     * @route("/me-contacter/", name="contact")
     */
    public function contact(Request $request){
        $ip = $request->server->get('REMOTE_ADDR');

        $repo = $this->getDoctrine()->getRepository(Message::class);
        $lastMessage = $repo->findLastByIpadress($ip);
        if ($lastMessage->getDate()->getTimestamp() + 21600 > time()){
            return $this->render('contact.html.twig', array('delay' => $lastMessage->getDate()->getTimestamp() + 21600 - time()));
        }

        return $this->render('contact.html.twig');
    }

    /**
     * @route("/admin/connexion/", name="admin-connexion")
     */
    public function adminConnexion(){
        if ($this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }
        return $this->render('admin-connexion.html.twig');
    }

    /**
     * @route("/admin/dashboard/", name="admin-dashboard")
     */
    public function adminDashboard(){
        if (!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }
        $date = new DateTime();
        $repo = $this->getDoctrine()->getRepository(Message::class);
        $todayMessages = $repo->findAllToday('%'.$date->format('Y-m-d').'%');
        return $this->render('admin-dashboard.html.twig', array('messages' => $todayMessages));
    }
    /**
     * @route("/admin/messages", name="admin-messages")
     */
    public function adminMessages(){
        if (!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }
        $repo = $this->getDoctrine()->getRepository(Message::class);
        $messages = $repo->findByDate();
        return $this->render('admin-messages.html.twig', array('messages' => $messages));
    }

    /**
     * @route("/admin/message/{nb}", name="admin-message")
     */
    public function adminMessage(int $nb){
        if (!$this->get('session')->has('account')){
            throw new AccessDeniedHttpException();
        }
        $repo = $this->getDoctrine()->getRepository(Message::class);
        $message = $repo->findOneById($nb);
        return $this->render('admin-message.html.twig', array('message' => $message));
    }
}