<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Covers;
use App\Form\BookType;
use Doctrine\ORM\EntityManager;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    /**
     * This controller display all  the books in the book list
     *
     * @param BookRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/book', name: 'book.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    //injection de dépendance
    public function index(BookRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $books = $paginator->paginate(
        $repository->findBy(['user' => $this->getUser()]),
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/
        );
        return $this->render('pages/book/index.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * This controller display a form that add a new book
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route("/book/new", 'book.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ): Response{
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // getting the uploaded image
            $covers = $form->get('covers')->getData();
            // loop on the covers
            foreach ($covers as $cover) {
                // file name generation
                $file = md5(uniqid()) . '.' . $cover->guessExtension();
                // copy the file in uploads folder
                $cover->move(
                    $this->getParameter('covers_directory'),
                    $file
                );
                // save the cover name in database
                $cov = new Covers();
                $cov->setName($file);
                $book->addCover($cov);
            }

            $book = $form->getData();
            $book->setUser($this->getUser());

            $manager->persist($book);
            $manager->flush();

            $this->addFlash(
                'success',
                'Your book has been added with success !'
            );

            return $this->redirectToRoute('book.index');

        }

        return $this->render('pages/book/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This Controller allow us to edit a book
     *
     * @param Book $book
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === book.getUser()")]
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
            // getting the uploaded image
            $covers = $form->get('covers')->getData();
            // loop on the covers
            foreach ($covers as $cover) {
                // file name generation
                $file = md5(uniqid()) . '.' . $cover->guessExtension();
                // copy the file in uploads folder
                $cover->move(
                    $this->getParameter('covers_directory'),
                    $file
                );
                // save the cover name in database
                $cov = new Covers();
                $cov->setName($file);
                $book->addCover($cov);
            }

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

    /**
     * This controller allow us to delete a book
     *
     * @param EntityManagerInterface $manager
     * @param Book $book
     * @return Response
     */
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
