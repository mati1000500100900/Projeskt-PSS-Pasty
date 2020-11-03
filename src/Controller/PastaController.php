<?php

namespace App\Controller;

use App\Entity\Pasta;
use App\Entity\Picture;
use App\Entity\Tags;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PastaController extends AbstractController
{
    /**
     * @Route("/add",name="pasta_add")
     */
    public function addAction(Request $request){
        if($request->isMethod("post")){
            if(empty($request->get("content"))){
                $this->addFlash("error","Brak treści, napisz coś");
            }
            else if(in_array("ROLE_BANNED",$this->getUser()->getRoles())){
                $this->addFlash("error","Nie możesz nic dodać, masz bana");
            }
            else {
                $doc=$this->getDoctrine();
                $em=$doc->getManager();

                $pasta=new Pasta();
                $pasta->setTid();
                $pasta->setContent($request->get("content"));
                $pasta->setAuthor($this->getUser());
                $pasta->setDeleted(false);
                $pasta->setTimestamp(new \DateTime("now"));
                //pic rel
                if(!empty($request->get("imageName"))){
                    $image=$doc->getRepository(Picture::class)->findOneBy(["name"=>$request->get("imageName")]);
                    if(!empty($image)){
                        $pasta->setPicture($image);
                    }
                }
                //tags
                $tags=new ArrayCollection();
                $matches=[];
                $re = '/\#[a-z0-9]{3,32}/m';
                preg_match_all($re,$request->get("content"),$matches,PREG_SET_ORDER, 0);

                foreach ($matches as $match){
                    $tagStr=substr($match[0],1);
                    $tag=$doc->getRepository(Tags::class)->findOneBy(["name"=>$tagStr]);
                    if(empty($tag)){
                        $tag=new Tags();
                        $tag->setName($tagStr);
                        $em->persist($tag);
                        $em->flush();
                    }
                    if(!$tags->contains($tag)) {
                        $tags->add($tag);
                    }
                }
                $pasta->setTags($tags);
                try {
                    $em->persist($pasta);
                    $em->flush();
                }
                catch (\Exception $exception){
                    $this->addFlash("erreor","Bład bazy danych");
                    return $this->render('pasta/add.html.twig');
                }
                $this->addFlash("success","Dodano pomyślnie");
                return $this->redirectToRoute("homepage");
            }
        }
        return $this->render('pasta/add.html.twig');
    }
}