<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Main class for the Administrator Panel. We then extend the /admin/ route.
 * @Route("/admin/")
 */
class AdminController extends Controller
{

    /**
     * Main panel for admins. This is the initial route that the /admin link will touch.
     * It shows a panel of Create/View that Administrators can use for creating other users/admins.
     * @Route(name="admin")
     */
    public function indexAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // We generate a blank user to get the fields we need for the creation form for other admins.
        $user = new User();
        // The form is then generated.
        $form = $this->createForm(UserType::class, $user);
        // This is where we handle the submitted form.
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // We encode the password.
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            // We then submit the newly created user to the database.
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        // The index panel also needs a list of Admins as we provide this to them to take action on.
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/admin.html.twig', array(
            'users' => $users,
            'form' => $form->createView()
        ));
    }

    /**
     * This route is for archiving users that the Admin no longer wants to be able to login.
     * @Route("archive/{username}", name="Archive User")
     */
    public function archiveUser(User $user, EntityManagerInterface $em, Request $request)
    {
        // Prevent the user from archiving themselves.
        if ($user == $this->getUser())
            throw new BadRequestHttpException('You can not archive yourself!');

        // Change the user status and submit to DB.
        $user->setIsActive(false);
        $em->persist($user);
        $em->flush();

        //Get the page the browser was on before coming here and send em' out.
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * This is for us deleting the user. There are checks in here to make sure we don't
     * delete a user that has notes attached.
     * @Route("delete/{username}", name="Delete User")
     */
    public function deleteUser(User $user, EntityManagerInterface $em, Request $request)
    {
        // Prevent the user from deleting themselves.
        if ($user == $this->getUser())
            throw new BadRequestHttpException('You can not delete yourself!');

        // Prevent the deletion of a user that has created notes.
        if (!$user->getAuthoredNotes()->isEmpty())
            throw new ConflictHttpException('Can not delete a user that has authored notes!');

        // Prevent the deletion of a user that has reviewed notes.
        if (!$user->getReviewedNotes()->isEmpty())
            throw new ConflictHttpException('Can not delete a user that has reviewed notes!');

        // We then persist it in the database.
        $em->remove($user);
        $em->flush();

        //Get the page the browser was on before coming here and send em' out.
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * This is for modifying the secondary role/assigning a temporary role to a user.
     * This was requested by the customer - this allows you to change/add/delete a role.
     * @Route("secondaryrole/{username}", name="Update Secondary Role for User")
     */
    public function secondaryRole(User $user, EntityManagerInterface $em, Request $request)
    {
        // We get the role parameter from the request.
        $role = $request->get('role');

        // We then get the user's existing roles (By default, [0] is ROLE_USER, [1] is the primary role.
        // That leaves [2] as the secondary role, which is the only one we're interested in.
        $roles = $user->getRoles();
        $roles[2] = $role;
        // If there was no role submitted that means the "None" option was selected so let's delete the [2]' role.
        if ($role == "None") {
            unset($roles[2]);
        }
        // Then we set the roles and persist it in the DB.
        $user->setRoles($roles);
        $em->persist($user);
        $em->flush();

        //Get the page the browser was on and send em back.
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * An admin can use this to change the password for a user.
     * @Route("password/{username}", name="Update Password for User")
     */
    public function setPassword(User $user, EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Get the password and encode it.
        $password = $request->get('password');
        $password = $passwordEncoder->encodePassword($user, $password);
        // Now we set the password and send it to the DB.
        $user->setPassword($password);
        $em->persist($user);
        $em->flush();

        //Get the page the browser was on before coming here and send em' out.
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
