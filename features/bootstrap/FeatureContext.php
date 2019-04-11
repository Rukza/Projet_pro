<?php

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Behat\Tester\Exception\PendingException;


/**
 * This context class contains the definitions of the steps used by the demo 
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 * 
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

     /**
     * @When a user sends a request to :path
     */
    public function aUserSendARequestTo(string $path)
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the status code should be :code
     */
    public function theResponseShouldBeReceived($code)
    {
        if ($this->response->getStatusCode() != $code) {
            throw new \RuntimeException('different status code');
        }
    }
    /**
     * @Then i should be redirected to :page
     */
    public function iShouldBeRedirectedTo($page)
    {
        if ($this->response->headers->get('location') != $page) {
            throw new \RuntimeException(sprintf('Wrong page %s', $page));
        }
    }

    /**
    * @When there is an registred user :email with password :password
    */
    public function thereIsAnRegistredUserWithPassword($email, $password)
    {   

        $user = new \App\Entity\User();
        $user->getEmail($email);
        $user->getPswd($password);
     
    }
   
}

