<?php
/**
 * Created by PhpStorm.
 * User: sahmed6
 * Date: 10/17/2017
 * Time: 6:38 PM
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Individual;
use AppBundle\Entity\User;
use AppBundle\Entity\Note;
use AppBundle\Entity\Comment;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase {
    public function createIndividual() {
        $individual = new Individual();
        $individual->setFirstName('John');
        $individual->setLastName('Adams');
        $individual->setMedicalId(random_int(1, 9000000));
        return $individual;
    }

    public function createStaff() {
        $staff = new User();
        $staff->setFirstName('John');
        $staff->setLastName('Oliver');
        $staff->setIsActive(true);
        $staff->setRoles(['ROLE_ADMIN']);
        return $staff;
    }

    public function createNote($individual, $staff) {
        $note = new Note();
        $note->setContent("This is a note for Today");
        $note->setModifiedAt(new \DateTime());
        $note->setSubmittedAt(new \DateTime());
        $note->setState(Note::AWAITING_APPROVAL);
        $note->setIndividual($individual);
        $note->setStaff($staff);
        $individual->addNote($note); // Attach the note to individual
        $staff->addAuthoredNote($note); // Attach the note to staff.
        $note->setComment("Test Comment - Assert Later");
        return $note;
    }

    public function testNote() {
        // Create the individual and assign it to a variable.
        $individual = $this->createIndividual();
        // Create the staff member and assign it to a variable.
        $staff = $this->createStaff();
        // Create the note and assign it to a variable.
        $note = $this->createNote($individual, $staff);

        // Now that we've created all the variables we need, let's actually test.
        
        // Make sure the note has the same staff member.
        $this->assertEquals($staff, $note->getStaff());
        // Make sure the note has the same individual.
        $this->assertEquals($individual, $note->getIndividual());
        // Make sure the individual has the note.
        $this->assertEquals($note, $individual->getNotes()[0]);
        // Make sure the staff member has the note.
        $this->assertEquals($note, $staff->getAuthoredNotes()[0]);
        // Make sure the note has the comment.
        $this->assertEquals("Test Comment - Assert Later", $note->getComment());
    }
}