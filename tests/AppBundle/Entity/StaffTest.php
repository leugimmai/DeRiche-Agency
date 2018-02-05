<?php
/**
 * Created by PhpStorm.
 * User: sahmed6
 * Date: 10/17/2017
 * Time: 6:38 PM
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class StaffTest extends TestCase
{
    public function createStaff()
    {
        $staff = new User();
        $staff->setFirstName('John');
        $staff->setLastName('Oliver');
        $staff->setIsActive(true);
        $staff->setRoles(['ROLE_ADMIN']);
        return $staff;
    }

    public function testStaff()
    {
        // Create the staff and assign it to a variable.
        $staff = $this->createStaff();

        // Now that we've created the variable we need, let's actually test.
        // Make sure the staff exists.
        $this->assertEquals($staff->getFirstName(), 'John');
        $this->assertEquals($staff->getLastName(), 'Oliver');

        // Get the staff member role, change it and then check again.
        $this->assertEquals($staff->getRoles()[0], 'ROLE_ADMIN');
        $staff->setRoles(['ROLE_WRITER']);
        $this->assertEquals($staff->getRoles()[0], 'ROLE_WRITER');
    }
}