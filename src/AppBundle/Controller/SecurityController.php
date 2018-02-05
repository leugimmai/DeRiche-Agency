<?php
/**
 * Created by PhpStorm.
 * User: sahmed6
 * Date: 10/3/2017
 * Time: 6:47 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Staff;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

/**
 * This is the actual controller for logging in as dictated by the documentation page here:
 * https://symfony.com/doc/current/security/form_login_setup.html
 * Class SecurityController
 * @package AppBundle\Controller
 */
class SecurityController extends Controller
{
    /**
     * Symfony handles the log in, this function just redirects the user after symfony authenticates them.
     * @Route("/login", name="Login Form")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        // Get the login error we need to display if there is one.
        $error = $authUtils->getLastAuthenticationError();

        // Get the last username that we need.
        $lastUsername = $authUtils->getLastUsername();

        // Render the actual login page.
        return $this->render('login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }
}