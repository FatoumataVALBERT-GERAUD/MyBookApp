<?php

namespace App\Controller;

use App\Entity\BookList;
use App\Form\BookListType;
use App\Repository\BookListRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookListController extends AbstractController
{
    /**
     * This Controller display all booklists
     *
     * @param BookRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/booklist', name: 'booklist.index', methods: ['GET'])]
    public function index(BookListRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $booklists = $paginator->paginate(
        $repository->findBy(['user' => $this->getUser()]),
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/
        );

        return $this->render('pages/book_list/index.html.twig', [
            'booklists' => $booklists,
        ]);
    }

    /**
     * This Controller allow us to create a new booklist
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/booklist/create', name: 'booklist.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $booklist = new BookList();
        $form = $this->createForm(BookListType::class, $booklist);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $booklist = $form->getData();
            $booklist->setUser($this->getUser());

            $manager->persist($booklist);
            $manager->flush();

            $this->addFlash(
                'success',
                'Your book list has been added with success !'
            );

            return $this->redirectToRoute('booklist.index');
        }

        return $this->render('pages/book_list/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This Controller allow us to edit a book list
     *
     * @param BookList $booklist
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('booklist/edit/{id}', 'booklist.edit', methods: ['GET', 'POST'])]
    public function edit(
        BookList $booklist,
        Request $request,
        EntityManagerInterface $manager
        ): Response{
        $form = $this->createForm(BookListType::class, $booklist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $booklist = $form->getData();
            // getting the uploaded image
            // $covers = $form->get('covers')->getData();
            // // loop on the covers
            // foreach ($covers as $cover) {
            //     // file name generation
            //     $file = md5(uniqid()) . '.' . $cover->guessExtension();
            //     // copy the file in uploads folder
            //     $cover->move(
            //         $this->getParameter('covers_directory'),
            //         $file
            //     );
            //     // save the cover name in database
            //     $cov = new Covers();
            //     $cov->setName($file);
            //     $booklist->addCover($cov);
            // }

            $manager->persist($booklist);
            $manager->flush();

            $this->addFlash(
                'success',
                'Your book list has been edited with success !'
            );

            return $this->redirectToRoute('booklist.index');

        }

        $form = $this->createForm(BookListType::class, $booklist);

        return $this->render('pages/book_list/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

        /**
     * This controller allow us to delete a book list
     *
     * @param EntityManagerInterface $manager
     * @param BookList $booklist
     * @return Response
     */
    #[Route('/booklist/delete/{id}', 'booklist.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $manager,
        BookList $booklist
        ): Response {
        $manager->remove($booklist);
        $manager->flush();

        $this->addFlash(
                'success',
                'Your book list has been deleted with success !'
            );


        return $this->redirectToRoute('booklist.index');
    }

}
