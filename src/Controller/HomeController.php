<?php
namespace App\Controller;

use App\Repository\BookListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index', methods: ['GET'])]
    public function index(BookListRepository $bookListRepository): Response
    {
        return $this->render('pages/home.html.twig', [
            'booklists' => $bookListRepository->findPublicBooklist(3),
        ]);
    }
}