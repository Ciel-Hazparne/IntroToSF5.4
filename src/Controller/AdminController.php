<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/user', name: 'user_index')]
    public function userIndex(UserRepository $userRepository): Response
    {
        return $this->render("admin/userIndex.html.twig",[
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/user/edit/{id}', name: 'user_edit')]
    public function userEdit(Request $request, User $user, UserRepository $userRepository): RedirectResponse|Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/userEdit.html.twig', ['formUser' => $form->createView()]);
    }
}
