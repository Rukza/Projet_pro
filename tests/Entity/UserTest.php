<?php


use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase{

    public function testToGetUserId(){

        
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        
        $this->assertEquals($user->getId(),1);
    }

    public function testToGetUserFirstName(){
        
        $user = new App\Entity\User;
        $user->setFirstName('Billy');
        
        $this->assertEquals($user->getFirstName(),'Billy');
    }

    public function testToGetUserLastName(){
        
        $user = new App\Entity\User;
        $user->setLastName('Bob');
        
        $this->assertEquals($user->getLastName(),'Bob');
    }

    public function testToGetUserFullName(){
        
        $user = new App\Entity\User;
        $user->setFirstName('Billy');
        $user->setLastName('Bob');
        
        $this->assertEquals($user->getFullName(),'Billy Bob');
    }
}