<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     */
    public function create(Request $request, ManagerRegistry $doctrine)
    {
        // create new post with a title
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);      // 1:57:00
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            // entity manager
            $em = $doctrine->getManager();
            $em->persist($post);                        // https://stackoverflow.com/questions/1069992/jpa-entitymanager-why-use-persist-over-merge
            $em->flush();
            // remember to call flush() after a bunch of querries are ready to be sent
            return $this->redirect($this->generateUrl('post.index'));
        }


        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/show/{id}", name="show")
     * @param Post $post
     * @return Response
     */
    public function show(Post $post)
    {

        // create the view
        return $this->render('post/show.html.twig', [
            'post' =>$post
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Post $post
     * @param ManagerRegistry $doctrine
     */
    public function remove(Post $post, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();

        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Post was removed.');

        return $this->redirect($this->generateUrl('post.index'));
    }

}