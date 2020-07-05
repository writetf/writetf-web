<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Events\CommentCreatedEvent;
use App\Repository\CommentRepository;
use App\Repository\CommunityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CommentController extends AbstractController
{

    /**
     * @Route("/comment/{postId}/new", methods={"POST", "GET"}, name="comment_new")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @ParamConverter("post", options={"mapping": {"postId": "id"}})
     *
     * NOTE: The ParamConverter mapping is required because the route parameter
     * (postSlug) doesn't match any of the Doctrine entity properties (slug).
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
     * @param Request $request
     * @param Post $post
     * @param CommunityRepository $communityRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param CommentRepository $commentRepository
     * @return Response
     * @throws \Exception
     */
    public function commentNew(
        Request $request,
        Post $post,
        CommunityRepository $communityRepository,
        EventDispatcherInterface $eventDispatcher,
        CommentRepository $commentRepository
    ): Response
    {
        if ($request->isMethod("GET")) {
            return $this->redirectToRoute('blog_post', ['id' => $post->getId()]);
        }
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $post->addComment($comment);
        $current = new DateTime();
        $post->setCommentedAt($current);
        $post->setLatestActivityAt($current);
        $commentRequest = $request->request->get('comment');


        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $parent = $commentRepository->find($commentRequest['parent']);
            if ($parent) {
                $comment->setParent($parent);
            }

            $em->persist($comment);
            $em->flush();

            // When an event is dispatched, Symfony notifies it to all the listeners
            // and subscribers registered to it. Listeners can modify the information
            // passed in the event and they can even modify the execution flow, so
            // there's no guarantee that the rest of this controller will be executed.
            // See https://symfony.com/doc/current/components/event_dispatcher.html
            $eventDispatcher->dispatch(new CommentCreatedEvent($comment));

            return $this->redirectToRoute('blog_post', ['id' => $post->getId()]);
        }

        return $this->render('blog/comment_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'reply' => false,
            'parent' => ''
        ]);
    }

    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     * @param Post $post
     * @param bool $reply
     * @param null $parent
     * @return Response
     */
    public function commentForm(Post $post, bool $reply = false, $parent = null): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('blog/_comment_form.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'reply' => $reply,
            'parent' => $parent
        ]);
    }
}
