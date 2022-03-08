<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Messenger\CommandBusInterface;
use App\Messenger\QueryBusInterface;
use App\UseCase\Post\Create\CreateCommand;
use App\UseCase\Post\Delete\DeleteCommand;
use App\UseCase\Post\Listing\Listing;
use App\UseCase\Post\Listing\ListingQuery;
use App\UseCase\Post\Read\ReadQuery;
use App\UseCase\Post\Update\UpdateCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/posts', name: 'post_')]
final class PostController extends AbstractController
{
    #[Route('', name: 'list', methods: [Request::METHOD_GET])]
    public function list(QueryBusInterface $queryBus, Request $request): Response
    {
        /** @var Listing $listing */
        $listing = $queryBus->fetch(new ListingQuery($request->query->getInt('page', 1)));
        return $this->render('post/list.html.twig', $listing->getData());
    }

    #[Route('/create', name: 'create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, CommandBusInterface $commandBus): Response
    {
        $form = $this->createForm(
            PostType::class,
            null,
            ['validation_groups' => ['Default', 'create']]
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $commandBus->dispatch(new CreateCommand($form->getData()));
            return $this->redirectToRoute('post_read', ['id' => $post->getId()]);
        }

        return $this->renderForm('post/create.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/read', name: 'read', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
    public function read(QueryBusInterface $queryBus, int $id): Response
    {
        return $this->render('post/read.html.twig', ['post' => $queryBus->fetch(new ReadQuery($id))]);
    }

    #[Route('/{id}/update', name: 'update', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(
        int $id,
        Request $request,
        QueryBusInterface $queryBus,
        CommandBusInterface $commandBus
    ): Response {
        /** @var Post $post */
        $post = $queryBus->fetch(new ReadQuery($id));

        $this->denyAccessUnlessGranted('edit', $post);

        $form = $this->createForm(PostType::class, $post)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandBus->dispatch(new UpdateCommand($post));
            return $this->redirectToRoute('post_read', ['id' => $post->getId()]);
        }

        return $this->renderForm('post/update.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: [Request::METHOD_POST])]
    public function delete(
        int $id,
        Request $request,
        QueryBusInterface $queryBus,
        CommandBusInterface $commandBus
    ): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete-post', $request->request->get('csrf_token'))) {
            throw new BadRequestHttpException('CSRF Token invalide.');
        }
        /** @var Post $post */
        $post = $queryBus->fetch(new ReadQuery($id));
        $this->denyAccessUnlessGranted('edit', $post);
        $commandBus->dispatch(new DeleteCommand($post));
        return $this->redirectToRoute('post_list');
    }
}
