<?php
namespace App\Controller;

use App\Entity\Article;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class IndexController extends AbstractController
{
      /**
     *@Route("/",name="article_list")
     */
  public function home()
  {
    //récupérer tous les articles de la table article de la BD
    // et les mettre dans le tableau $articles
    $articles= $this->getDoctrine()->getRepository(Article::class)->findAll();
    return  $this->render('articles/index.html.twig',['articles' => $articles]);  
  }

   /**
      * @Route("/article/save")
      */
     public function save() {
       $entityManager = $this->getDoctrine()->getManager();

       $article = new Article();
       $article->setNom('Article 3');
       $article->setPrix(3000);
      
       $entityManager->persist($article);
       $entityManager->flush();

       return new Response('Article enregisté avec id   '.$article->getId());
     }


    /**
     * @Route("/article/new", name="new_article")
     * Method({"GET", "POST"})
     */
    public function new(Request $request) {
        $article = new Article();
        $form = $this->createFormBuilder($article)
          ->add('nom', TextType::class)
          ->add('prix', TextType::class)
          ->add('save', SubmitType::class, array(
            'label' => 'Créer')
          )->getForm();
          
  
        $form->handleRequest($request);
  
        if($form->isSubmitted() && $form->isValid()) {
          $article = $form->getData();
  
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($article);
          $entityManager->flush();
  
          return $this->redirectToRoute('article_list');
        }
        return $this->render('articles/new.html.twig',['form' => $form->createView()]);
    }

      

      /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show($id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
  
        return $this->render('articles/show.html.twig', array('article' => $article));
      }


    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $article = new Article();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
  
        $form = $this->createFormBuilder($article)
          ->add('nom', TextType::class)
          ->add('prix', TextType::class)
          ->add('save', SubmitType::class, array(
            'label' => 'Modifier'         
          ))->getForm();
  
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
  
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->flush();
  
          return $this->redirectToRoute('article_list');
        }
  
        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
      }

   /**
     * @Route("/article/delete/{id}",name="delete_article")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
  
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
  
        $response = new Response();
        $response->send();

        return $this->redirectToRoute('article_list');
      }
  


}

