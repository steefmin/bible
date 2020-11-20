<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteGenerator extends Loader implements RouteLoaderInterface
{
    /** @var BookRepository */
    private $bookRepository;

    public function __construct(
        BookRepository $bookRepository
    ) {
        $this->bookRepository = $bookRepository;
    }

    public function getRoutes(): RouteCollection
    {
        $books = $this->bookRepository->findAll();

        $collection = new RouteCollection();

        foreach ($books as $book) {
            $name = sprintf('book-%s-%s', $book->getSpacelessAuthor(), $book->getSpacelessTitle());

            $route = new Route(sprintf('/book/%s/%s', $book->getAuthor(), $book->getTitle()), [
                '_controller' => 'App\Controller\BookController::showByNames',
                'author' => $book->getAuthor(),
                'title' => $book->getTitle(),
            ]);

            $collection->add($name, $route);
        }

        return $collection;
    }

    public function load($resource, string $type = null): RouteCollection
    {
        return $this->getRoutes();
    }

    public function supports($resource, string $type = null): bool
    {
        return $type === 'books';
    }
}
