<?php

namespace App\Controller;

use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CdnController extends AbstractController
{
    /**
     * @Route("/api/upload", name="file_upload")
     * @param Request $request
     * @return JsonResponse
     */
    public function imageUploadAction(Request $request){
        if($request->isMethod("POST")){
            $doc=$this->getDoctrine();
            $em=$doc->getManager();

            $upl=$request->files->get("file");
            $base64 = 'data:' . $upl->getClientMimeType(). ';base64,' . base64_encode(file_get_contents($upl->getPathName()));
            // checking for duplicates
            $test=$doc->getRepository(Picture::class)->findOneBy(["name"=>md5($base64)]);
            if(!empty($test)){
                return new JsonResponse(["message"=>"success", "tid"=>$test->getName()]);
            }
            $pic = new Picture();
            $pic->setData($base64);
            $pic->setName(md5($base64));
            $em->persist($pic);
            $em->flush();
            return new JsonResponse(["message"=>"success", "tid"=>$pic->getName()]);
        }
        return new JsonResponse(["message"=>"error"]);
    }

    /**
     * @Route("/api/cdn/{tid}", name="file_view")
     * @param string $tid
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewImageAction($tid=""){
        if(!empty($tid)){
            $doc=$this->getDoctrine();
            $dbfile=$doc->getRepository(Picture::class)->findOneBy(["name"=>$tid]);
            if(empty($dbfile)) return new JsonResponse(["message"=>"no such file"]);

            $data = explode(',', $dbfile->getData());
            $mime = substr($data[0],strpos($data[0],":")+1,strpos($data[0],";")-5);

            $response = new Response();
            $response->setContent(base64_decode($data[1]));
            $response->headers->set("Content-Type",$mime);

            return $response;
        }
        return new JsonResponse(["message"=>"error"]);
    }

    public function base64ToImage($base64_string, $output_file) {


        return $output_file;
    }
}