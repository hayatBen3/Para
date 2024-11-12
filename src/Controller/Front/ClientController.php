<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/client')]
class ClientController extends AbstractController
{

    #[Route('/cree-compte', name: 'register_client', methods: ['GET', 'POST'])]
    public function registerClient(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $client = new Client();
        $user = new User();


        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $phone = $request->request->get('phone');
        $address = $request->request->get('address');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $errorMessage = '';


        if ($request->isMethod('POST') && $request->request->has('submit_button')) {

            // Check if the email already exists in the database
            $existingUserEmail = $entityManager->getRepository(User::class)->findOneByEmail($email);

            if ($existingUserEmail) {
                // Email already exists, return the form with an error message
                $errorMessage = 'This email is already in use. Please try with a different email.';
                return $this->render('front/client/new.html.twig', [
                    'error_message' => $errorMessage,
                    // Pass other necessary variables to your template if needed
                ]);
            }

            $client->setFirstName($firstName);
            $client->setLastName($lastName);
            $client->setPhone($phone);
            $client->setAddress($address);

            $user->setEmail($email);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            $user->setRoles(["ROLE_CLIENT"]);

            $entityManager->persist($user);
            $entityManager->flush();

            $client->setUser($user);
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('home');

        }

        return $this->render('front/client/new.html.twig', [
            'client' => $client,
            'error_message' => $errorMessage,

        ]);
    }




    #[Route('/modifier-profile', name: 'update_profile')]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager): Response
    {

        $client = $this->getUser()->getClient();

        $firstName = $request->request->get('firstName', $client->getFirstName());

        $lastName = $request->request->get('lastName', $client->getLastName());
        $phone = $request->request->get('phone', $client->getPhone());
        $address = $request->request->get('address', $client->getAddress());
        // $email = $request->request->get('email', $user->getEmail());
        // $password = $request->request->get('password');


        if ($request->isMethod('POST') && $request->request->has('submit_button')) {

            // Update client entity
            $client->setFirstName($firstName);
            $client->setLastName($lastName);
            $client->setPhone($phone);
            $client->setAddress($address);

            $entityManager->flush();

            $this->addFlash('success', 'Updated with success.');
            return $this->redirectToRoute('home');

        }

        return $this->render('front/client/edit.html.twig', [
            'client' => $client,
        ]);
    }





}
