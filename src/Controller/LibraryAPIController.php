<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Book;
use App\Repository\BookRepository;

use Doctrine\Persistence\ManagerRegistry;

class LibraryAPIController extends AbstractController
{
    //API route that returns all books in JSON format
    #[Route('/api/library/books', name: 'api_books', methods: ['GET'])]
    public function showBooksApi(
        BookRepository $bookRepository
    ): Response {
        $books = $bookRepository
            ->findAll();

        return $this->json($books);
    }

    //API route that returns book by isbn in JSON format
    #[Route('/api/library/book/{isbn}', name: 'api_book', methods: ['GET'])]
    public function showBookApi(
        BookRepository $bookRepository,
        string $isbn
    ): Response {
        $book = $bookRepository->findOneBy(['ISBN' => $isbn]);
        return $this->json($book);
    }
}
