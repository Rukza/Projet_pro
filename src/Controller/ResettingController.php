<?php
namespace App\Controller;


use App\Entity\User;
use App\Form\ResettingType;
use App\Notification\Mailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResettingController extends Controller
{
            

/**
     * Permet de demander et de recevoir par mail une reinitialisation du mot de passe
     *
     * @route("/requete", name="requete_reset")
     */
    public  function requestpswd(Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator){

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ]
            ])
            ->getForm();
            $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()){
                        $em = $this->getDoctrine()->getManager();
                        $user = $em->getRepository(User::class)->findOneByEmail($form->getData()['email']);

                        if (!$user){
                            $request->getSession()->getFlashBag()->add('Warning', "Une erreur est survenu veuillez réitérer l'opération");
                            return $this->redirectToRoute("requete_reset");
                        }
                        $user->setToken($tokenGenerator->generateToken());
                        $user->setPasswordRequestedAt(new \Datetime());
                        $em->flush();

                        $bodyMail = $mailer->createBodyMail('emails/mailreset.html.twig', [
                            'user' => $user
                            ]);
                            $mailer->sendMessage('noreply@wristband.com', $user->getEmail(), 'renouvellement du mot de passe', $bodyMail);
                            $request->getSession()->getFlashBag()->add('success', "Si vous été inscrit sur notre site, un email va vous être envoyé afin que vous puissiez renouveller votre mot de passe. Le lien ne sera valide que 24h!!!");

                            return $this->redirectToRoute("account_login");
                    }
                    return $this->render('account/resettingpswd.html.twig',[
                        'form' => $form->createView()
                    ]);
    }
     // si supérieur à 10min, retourne false
    // sinon retourne false
    private function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null)
        {
            return false;        
        }
        
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();
        $daySeconds = 60 * 10;//to do passé a 24h la validation
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }

    /**
     * @Route("account/resettingpswd/{id}/{token}", name="reset")
     */
    public function resetting(User $user, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPswd());
            $user->setPswd($password);

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "Votre mot de passe a été renouvelé.");

            return $this->redirectToRoute('account_login');

        }

        return $this->render('account/resetedpswd.html.twig', [
            'form' => $form->createView()
        ]);
        
    }
}