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
        return $this->json($msg);
    }

    /**
     * @route("message/", name="apiMessage", methods="POST");
     */
    public function apiMessage(Request $request){
        $email = $request->request->get('email');
        $content = $request->request->get('content');
        $date = new DateTime;
        $ip = 'xxx.xxx.xxx.xxx';

        $message = new Message();
        $message->setAuthor($email)->setContent($content)->setDate($date)->setIpadress($ip)->setStatut(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();
        $msg['success'] = true;
        return $this->json($msg);
    }
}