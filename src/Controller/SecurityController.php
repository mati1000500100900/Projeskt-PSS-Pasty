<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils){
        $error = $authenticationUtils->getLastAuthenticationError();
        if (!empty($error)) $this->addFlash("error", "Błędne dane logowania");
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render("user/login.html.twig",[
            'last_username' => $lastUsername,
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request){

        if(!empty($request->isMethod("POST"))){
            $lastdata=[ "_username"=>$request->get("_username"),
                        "_email"=>$request->get("_email"),
                        "_password"=>$request->get("_password"),
                        "_password_confirm"=>$request->get("_password_confirm")
                ];
            if(empty($request->get("_username"))){
                $this->addFlash("error","Podaj nazwę użytkownika");
                return $this->render('user/register.html.twig',["lastdata"=>$lastdata]);
            }
            else if(empty($request->get("_email"))){
                $this->addFlash("error","Podaj adres email");
                return $this->render('user/register.html.twig',["lastdata"=>$lastdata]);
            }
            else if(empty($request->get("_password"))){
                $this->addFlash("error","Podaj hasło");
                return $this->render('user/register.html.twig',["lastdata"=>$lastdata]);
            }
            else if($request->get("_password")!=$request->get("_password_confirm")){
                $this->addFlash("error","Hasła różnią się");
                return $this->render('user/register.html.twig',["lastdata"=>$lastdata]);
            }
            $user=new User();
            $user->setUsername($request->get("_username"));
            $user->setEmail($request->get("_email"));
            $user->setPassword($request->get("_password"));
            $user->setRoles(1);

            $em=$this->getDoctrine()->getManager();
            try{
                $em->persist($user);
                $em->flush();
            }
            catch (\Exception $exception){
                $this->addFlash("error","Bład bazy danych");
                return $this->render('user/register.html.twig',["lastdata"=>$lastdata]);
            }

            $this->addFlash("success","Zarejestrowano pomyślnie, możesz się zalogować");
            return $this->redirectToRoute("login");

        }
        return $this->render("user/register.html.twig");
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}