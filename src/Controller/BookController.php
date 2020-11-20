<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    /** @var BookRepository */
    private $bookRepository;

    public function __construct(
        BookRepository $bookRepository
    ) {
        $this->bookRepository = $bookRepository;
    }

    /**
     * @Route("/book", name="books", methods={"GET"})
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(): Response
    {
        $books = $this->bookRepository->findAll();
        return $this->render(
            'book/list.html.twig',
            [
                'controller_name' => 'BookController',
                'books' => $books,
            ]
        );
    }

    /**
     * @Route("/book/{id}", name="book", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function showById(int $id): Response
    {
        $book = $this->bookRepository->find($id);

        if (is_null($book)) {
            return $this->redirectToRoute('home');
        }

        return $this->render(
            'book/index.html.twig',
            [
                'controller_name' => 'BookController',
                'book' => $book,
            ]
        );
    }

    /**
     * @Route("/book/{author}/{title}", name="book-name",  methods={"GET"})
     */
    public function showByNames(?string $author = null, ?string $title = null): Response
    {
        $book = $this->bookRepository->findOneBy(['title' => $title, 'author' => $author]);

        if (is_null($book)) {
            return $this->redirectToRoute('home');
        }

        return $this->render(
            'book/index.html.twig',
            [
                'controller_name' => 'BookController',
                'book' => $book,
            ]
        );
    }

}
