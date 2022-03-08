<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends  AbstractController
{

    /**
     * @throws \Exception
     */
    public function number(): Response
    {
        $number = random_int(0, 10000);

        return $this->render('lucky/number.html.twig',[
            'lnumber'=>$number,
            'zmienna'=>"cos",
            "zmienna2"=>231
            ]);
    }
}