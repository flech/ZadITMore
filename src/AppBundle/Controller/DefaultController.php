<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Product;
use AppBundle\Entity\Log;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Session\Session;   
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    
    public function indexAction(Request $request)
    {
       
        $session = new Session();

        
        if ($session->isStarted()==true){
            $session->start();
        }

        $products = $this->getDoctrine()
                        ->getRepository('AppBundle:Product')->findAll();

        $prodId = $request->request->get('prod', 'noone');

        $inBasket[] = "Empty Basket, please select a product";
        

        if ($prodId != 'noone')
        {
            $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($prodId);
            $session->set($product->getId(),$product->getName());
            $log = new Log;
            $log->setProdName($product->getName());
            $log->setDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($log);
            $em->flush();
            echo "Dev : Log saved to database. Product name - ".$product->getName()." Current time - ".$log->getDate()->format('H:i:s \O\n Y-m-d');
            $inBasket = $session->all();
        }
        else 
        {
            if ($session->count() != 0)
            {
               $inBasket = $session->all();
               echo "Please select a Product before submitting !!!";
            }
        }

        return $this->render('default/index.html.twig', [
            'products' => $products,
            'basketProds' => $inBasket
        ]);
    }
    
        public function deleteBasketAction() {
            session_destroy();
            return $this->redirectToRoute('homepage');

    }
}
