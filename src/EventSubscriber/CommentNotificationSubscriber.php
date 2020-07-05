<?php

namespace App\EventSubscriber;

use Swift_Mailer;
use Swift_Message;
use App\Entity\Post;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Comment;
use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use App\Entity\Notification;
use Psr\Log\LoggerInterface;
use App\Events\CommentCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Notifies post's author about new comments.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class CommentNotificationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $translator;
    private $urlGenerator;
    private $sender;
    private $templating;
    private $notificationRepository;
    private $entityManager;
    private $logger;

    public function __construct(
        Swift_Mailer $mailer,
        Environment $templating,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        $sender
    )
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->sender = $sender;
        $this->templating = $templating;
        $this->notificationRepository = $entityManager->getRepository(Notification::class);
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CommentCreatedEvent::class => 'onCommentCreated',
        ];
    }

    /**
     * @param CommentCreatedEvent $event
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function onCommentCreated(CommentCreatedEvent $event): void
    {
        /** @var Comment $comment */
        $comment = $event->getComment();
        $post = $comment->getPost();
        $this->notifyAuthor($post, $comment);
        $this->notifyFollowers($post, $comment);
    }

    /**
     * @param Post $post
     * @param Comment $comment
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function notifyAuthor(Post $post, Comment $comment)
    {
        if ($comment->getAuthor()->getEmail() === $post->getAuthor()->getEmail()) {
            return;
        }
        $this->notify($post->getAuthor(), $post, $comment);
        $this->sendMail($post, $comment);
    }

    /**
     * @param Post $post
     * @param Comment $comment
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function notifyFollowers(Post $post, Comment $comment)
    {
        $comments = $post->getComments();
        $emails = [];
        $authorEmail = $post->getAuthor()->getEmail();
        $commentAuthorEmail = $comment->getAuthor()->getEmail();
        foreach ($comments as $item) {
            $email = $item->getAuthor()->getEmail();
            if (($email === $authorEmail) || ($commentAuthorEmail === $email)) {
                continue;
            }
            if (!in_array($email, $emails)) {
                $this->notify($item->getAuthor(), $post, $comment);
                $this->sendMail($post, $comment, $email);
                $emails[] = $item->getAuthor()->getEmail();
            }
        }
    }

    /**
     * @param Post $post
     * @param Comment $comment
     * @param null $receiverEmail
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function sendMail(Post $post, Comment $comment, $receiverEmail = null)
    {
        $subject = $comment->getSummary();
        $senderName = sprintf('%s  - %s', $comment->getAuthor()->getDisplayText(), $this->sender['name']);
        $to = $receiverEmail ?? $post->getAuthor()->getEmail();
        $body = $this->templating->render('email/comment.html.twig', [
            'post' => $post,
            'comment' => $comment,
            'isFollower' => !empty($receiverEmail),
            'to' => $to
        ]);
        $message = (new Swift_Message())
            ->setSubject($subject)
            ->setTo($to)
            ->setFrom($this->sender['email'], $senderName)
            ->setBody($body, 'text/html');
        try {
            $result = $this->mailer->send($message);
            $this->logger->info('Successfully send mail to: ' . $to);
        } catch (\Exception $exception) {
            $this->logger->error("Can not send email: " . $exception->getMessage());
        }
    }

    protected function notify(User $user, Post $post, Comment $comment)
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setPost($post);
        $notification->setComment($comment);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
