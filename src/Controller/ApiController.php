<?php

namespace App\Controller;

use App\Entity\Pasta;
use App\Entity\Report;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/posts/{order}/{page}/{tag}/{author}/{search}", name="api_get_posts")
     */
    public function getAllPostsAction($order="new",$page=0,$tag="",$author="",$search="",SerializerInterface $serializer){
        $doc=$this->getDoctrine();
        $activeUser=$this->getUser() ? $this->getUser()->getUsername(): "anon";
        dump($activeUser);
        $posts=$doc->getRepository(Pasta::class)->findOrdered($activeUser,$order,$page,$tag,$author,$search);

        return new JsonResponse(["valid"=>!empty($posts),"posts"=>$serializer->normalize($posts,"json",[AbstractNormalizer::ATTRIBUTES => ["content",'timestamp',"likesCount","tid","likes"=>["username"],"author" => ["username"],"picture"=>["name"],"long"]
        ])]);
    }
    /**
     * @Route("/api/like/{tid}", name="api_like_post")
     */
    public function likeAction($tid=""){
        $doc=$this->getDoctrine();
        $em=$doc->getManager();
        $post=$doc->getRepository(Pasta::class)->findOneBy(["tid"=>$tid]);
        if(empty($post)){
            return new JsonResponse("Nie ma takiego wpisu");
        }
        else if(empty($this->getUser())){
            return new JsonResponse("Musisz być zalogownay");
        }
        else if($post->getLikes()->contains($this->getUser())){
            return new JsonResponse("Już lubisz ten wpis");
        }
        else if($post->getAuthor()->getUsername()==$this->getUser()->getUsername()){
            return new JsonResponse("Nie można polubić swojego wpisu");
        }
        else{
            $likes=$post->getLikes();
            $likes->add($this->getUser());
            $post->setLikes($likes);
            try{
                $em->persist($post);
                $em->flush();
            }
            catch (\Exception $exception){
                return new JsonResponse("database error");
            }
        }
        return new JsonResponse("liked");
    }


    /**
     * @Route("/api/dislike/{tid}", name="api_dislike_post")
     */
    public function disLikeAction($tid=""){
        $doc=$this->getDoctrine();
        $em=$doc->getManager();
        $post=$doc->getRepository(Pasta::class)->findOneBy(["tid"=>$tid]);
        if(empty($post)){
            return new JsonResponse("Nie ma takiego wpisu");
        }
        else if(empty($this->getUser())){
            return new JsonResponse("Musisz być zalogownay");
        }
        else if($post->getLikes()->contains($this->getUser())){

            $likes=$post->getLikes();
            $likes->removeElement($this->getUser());
            $post->setLikes($likes);
            try{
                $em->persist($post);
                $em->flush();
            }
            catch (\Exception $exception){
                return new JsonResponse("database error");
            }
        }
        else{
            return new JsonResponse("Nie lubisz tego wpisu");
        }
        return new JsonResponse("disliked");
    }

    /**
     * @Route("/api/duplicates", name="api_check_duplicates")
     */
    public function serachDuplicatesAction(Request $request){
        if($request->isMethod('post')){
            $doc=$this->getDoctrine();
            $stripped=preg_replace("/[^a-z0-9.]+/i", "", $request->get('text'));

            $similar=[];
            for($i=0;$i<25;$i++){
                $index=rand(1,strlen($stripped)-16);
                $res=$doc->getRepository(Pasta::class)->findAllContaining(substr($stripped,$index,16));
                foreach ($res as $pasta){
                    array_push($similar,$pasta->getTid());
                }
            }
            $vals=array_count_values($similar);
            rsort($vals);
            if(!empty($vals)) $percent=$vals[0]/25;
            else $percent=0;

            return new JsonResponse(["sure"=>$percent]);
        }
        return new JsonResponse("wrong method");
    }
    /**
     * @Route("/api/delete/{tid}", name="api_delete_post")
     */
    public function deletePostAction($tid){
        $doc=$this->getDoctrine();
        $post=$doc->getRepository(Pasta::class)->findOneBy(["tid"=>$tid]);
        if(empty($post)){
            return new JsonResponse("Nie ma takiej pasty");
        }
        else if($post->getAuthor()!=$this->getUser()){
            return new JsonResponse("To nie jest twój wpis");
        }
        else{
            $em=$doc->getManager();
            $post->delete();
            $em->persist($post);
            $em->flush();
            return new JsonResponse("deleted");
        }
    }
    /**
     * @Route("/api/report", name="api_report_post")
     */
    public function reportPostAction(Request $request){
        $doc=$this->getDoctrine();
        $post=$doc->getRepository(Pasta::class)->findOneBy(["tid"=>$request->get("tid")]);
        if(empty($post)){
            return new JsonResponse("Nie ma takiej pasty");
        }
        else if(empty($this->getUser())){
            return new JsonResponse("Zaloguj się, żeby zgłosić");
        }
        else{
            $report=new Report();
            $report->setPost($post);
            $report->setReason($request->get("reason"));
            $report->setReporter($this->getUser());
            $report->setManaged(false);
            $em=$doc->getManager();
            try{
                $em->persist($report);
                $em->flush();
            }
            catch (\Exception $exception){
                return new JsonResponse("Błąd bazy danych");
            }
        }
        return new JsonResponse("reported");
    }
}