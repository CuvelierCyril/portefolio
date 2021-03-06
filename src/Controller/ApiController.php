<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Message;
use \DateTime;
use App\Service\Recaptcha;

/**
 * @route("/api/")
 */
class ApiController extends AbstractController{
     /**
     * @route("login/", name="apiLogin", methods="POST")
     */
    public function apiLogin(Request $request){
        if ($request->getMethod() == 'POST'){
            $email = $request->request->get('email');
            $pass = $request->request->get('password');
            $repo = $this->getDoctrine()->getRepository(User::class);
            $user = $repo->findOneByEmail($email);
            if ($user != null){
                if (password_verify($pass, $user->getPassword())){
                    $this->get('session')->set('account', $user);
                    $msg['redirect'] = true;
                } else {
                    $msg['password'] = true;
                }
            } else {
                $msg['email'] = true;
            }
        }
        return $this->json($msg);
    }

    /**
     * @route("message/", name="apiMessage", methods="POST");
     */
    public function apiMessage(Request $request){
        if ($request->getMethod() == 'POST'){
            $email = $request->request->get('email');
            $content = $request->request->get('content');
            $date = new DateTime;
            $ip = $request->server->get('REMOTE_ADDR');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $msg['email'] = true;
            }

            if(!preg_match('#^[a-zA-Z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._-\s !?,:\"\']{10,5000}$#', $content)){
                $msg['content'] = true;
            }
            if (!isset($msg)){
                $repo = $this->getDoctrine()->getRepository(Message::class);
                $lastMessage = $repo->findLastByIpadress($ip);
                if ($lastMessage->getDate()->getTimestamp() + 21600 > time()){
                    $msg['delay'] = true;
                } else {
                    $message = new Message();
                    $message->setAuthor($email)->setContent($content)->setDate($date)->setIpadress($ip)->setStatut(0);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($message);
                    $em->flush();
                    $msg['success'] = true;
                }
            }
        }
        return $this->json($msg);
    }

    /**
     * @route("message-statut/", name="apiMessageStatut", methods="POST")
     */
    public function apiMessageStatut(Request $request){
        if ($request->getMethod() == 'POST'){
            $id = $request->request->get('id');
            $statut = $request->request->get('statut');
            $statutPossible = array('0', '1', '2', '3');

            if (!in_array($statut, $statutPossible)){
                $msg['statut'] = true;
            } else {
                $repo = $this->getDoctrine()->getRepository(Message::class);
                $message = $repo->findOneById($id);
                $message->setStatut($statut);
                $em = $this->getDoctrine()->getManager();
                $em->merge($message);
                $em->flush();
                $msg['success'] = true;
            }
        }
        return $this->json($msg);
    }
}