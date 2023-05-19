<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function testIsTrue()
    {
        $user = new User();
        $user->setEmail('a@ciel.eh')
            ->setUsername('a')
            ->setPassword('123456789123');
        $this->assertTrue($user->getEmail() === 'a@ciel.eh');
        $this->assertTrue($user->getUsername() ===  'a');
        $this->assertTrue($user->getPassword() ===  '123456789123');

    }

    public function testIsFalse()
    {
        $user = new User();
        $user->setEmail('a@ciel.eh')
            ->setUsername('a')
            ->setPassword('123456789123');
        $this->assertFalse($user->getEmail() ===  'false@ciel.eh');
        $this->assertFalse($user->getUsername() === 'false');
        $this->assertFalse($user->getPassword() ===  'false');

    }
    public function testIsEmpty()
    {
        $user = new User();

        $this->assertEmpty($user->getEmail() );
        $this->assertEmpty($user->getUsername() );
        $this->assertEmpty($user->getPassword() );

    }
}