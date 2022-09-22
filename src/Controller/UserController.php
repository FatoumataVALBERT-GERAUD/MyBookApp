<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * This controller allow us to edit user information
     *
     * @param User $chosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === chosenUser")]
    #[Route('/user/edit/{id}', name: 'user.edit', methods:['GET', 'POST'])]
    public function edit(User $chosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserType::class, $chosenUser);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($hasher->isPasswordValid($chosenUser, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Your account infos has been edited'
                );

                return $this->redirectToRoute('booklist.index');
            }else{
                $this->addFlash(
                    'warning',
                    'The password is incorrect'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This Controller allow us to edit user's password
     *
     * @param User $chosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === chosenUser")]
    #[Route('/user/edit-password/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(User $chosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($hasher->isPasswordValid($chosenUser, $form->getData()['plainPassword'])) {
                $chosenUser->setUpdatedAt(new \DateTimeImmutable());
                $chosenUser->setPlainPassword(
                        $form->getData()['newPassword']
                );

                $this->addFlash(
                    'success',
                    'The password has been edited'
                );

                $manager->persist($chosenUser);
                $manager->flush();

                return $this->redirectToRoute('booklist.index');
            }else{
                $this->addFlash(
                    'warning',
                    'The password is incorrect'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
