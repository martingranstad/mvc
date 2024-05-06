<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Book;
use App\Repository\BookRepository;

use Doctrine\Persistence\ManagerRegistry;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->redirectToRoute('show_books');
    }

    #[Route('/library/create_book', name: 'create_book', methods: ['GET'])]
    public function createBookForm(): Response
    {
        return $this->render('library/create_book.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    #[Route('/library/create_book_post', name: 'create_book_post', methods: ['POST'])]
    public function createBook(
        ManagerRegistry $doctrine,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();

        $book = new Book();
        $book->setTitel($request->request->get('titel'));
        $book->setISBN($request->request->get('isbn'));
        $book->setAuthor($request->request->get('author'));
        $book->setImage($request->request->get('image'));

        // tell Doctrine you want to (eventually) save the Product
        // (no queries yet)
        $entityManager->persist($book);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Saved new book with following [NAME, ISBN, AUTHOR, IMAGE]  ['.$book->getTitel().', '.$book->getISBN().', '.$book->getAuthor().', '.$book->getImage().']'
        );
        
        return $this->redirectToRoute('app_library');
    }

    #[Route('/library/show_books', name: 'show_books', methods: ['GET'])]
    public function showBooks(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $books = $entityManager->getRepository(Book::class)->findAll();

        return $this->render('library/show_books.html.twig', [
            'books' => $books,
        ]);
    }


    #[Route('/library/show_book/{isbn}', name: 'show_book', methods: ['GET'])]
    public function showBookByISBN(ManagerRegistry $doctrine, $isbn): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->findOneBy(['ISBN' => $isbn]);

        if (!$book) {
            return new Response('No book found for isbn: '.$isbn);
        }

        return $this->render('library/show_books.html.twig', [
            'books' => [$book],
        ]);
    }

    //Route to update a book
    #[Route('/library/update_book/{isbn}', name: 'update_book', methods: ['GET'])]
    public function updateBookForm(ManagerRegistry $doctrine, $isbn): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->findOneBy(['ISBN' => $isbn]);

        if (!$book) {
            return new Response('No book found for isbn: '.$isbn);
        }

        return $this->render('library/update_book.html.twig', [
            'book' => $book,
        ]);
    }

    //Post route to update book
    #[Route('/library/update_book_post', name: 'update_book_post', methods: ['POST'])]
    public function updateBook(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->findOneBy(['ISBN' => $request->request->get('isbn')]);

        if (!$book) {
            return new Response('No book found for isbn: '.$isbn);
        }

        $book->setTitel($request->request->get('titel'));
        $book->setAuthor($request->request->get('author'));
        $book->setImage($request->request->get('image'));

        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Updated book with following [NAME, AUTHOR, IMAGE]  ['.$book->getTitel().', '.$book->getAuthor().', '.$book->getImage().']'
        );

        return $this->redirectToRoute('app_library');
    }

    //Get route that displays the book thats about to be deleted and asks for confirmation
    #[Route('/library/delete_book/{isbn}', name: 'delete_book', methods: ['GET'])]
    public function deleteBookForm(ManagerRegistry $doctrine, $isbn): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->findOneBy(['ISBN' => $isbn]);

        if (!$book) {
            return new Response('No book found for isbn: '.$isbn);
        }

        return $this->render('library/delete_book.html.twig', [
            'book' => $book,
        ]);
    }

    //Post route to delete book
    #[Route('/library/delete_book_post', name: 'delete_book_post', methods: ['POST'])]
    public function deleteBook(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->findOneBy(['ISBN' => $request->request->get('isbn')]);

        if (!$book) {
            return new Response('No book found for isbn: '.$isbn);
        }

        $entityManager->remove($book);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Deleted book with following [NAME, AUTHOR, IMAGE]  ['.$book->getTitel().', '.$book->getAuthor().', '.$book->getImage().']'
        );

        return $this->redirectToRoute('app_library');
    }

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
