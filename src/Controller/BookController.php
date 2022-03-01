<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    /**
     * This controller display all books
     *
     * @param BookRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/book', name: 'book.index', methods: ['GET'])]
    //injection de dépendance
    public function index(BookRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $books = $paginator->paginate(
        $repository->findAll(),
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/
        );
        return $this->render('pages/book/index.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * This controller display a form that create a new book
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/book/new", 'book.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ): Response{
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();

            $manager->persist($book);
            $manager->flush();

            $this->addFlash(
                'success',
                'Your book has been created with success !'
            );

            return $this->redirectToRoute('book.index');
            
        }

        return $this->render('pages/book/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('book/edit/{id}', 'book.edit', methods: ['GET', 'POST'])]
    public function edit(
        Book $book,
        Request $request,
        EntityManagerInterface $manager
        ): Response{
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();

            $manager->persist($book);
            $manager->flush();

            $this->addFlash(
                'success',
                'Your book has been edited with success !'
            );

            return $this->redirectToRoute('book.index');
            
        }
        
        $form = $this->createForm(BookType::class, $book);

        return $this->render('pages/book/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/book/delete/{id}', 'book.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $manager,
        Book $book
        ): Response {
        $manager->remove($book);
        $manager->flush();

        $this->addFlash(
                'success',
                'Your book has been deleted with success !'
            );


        return $this->redirectToRoute('book.index');
    }
}
