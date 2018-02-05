<?php
/**
 * Created by PhpStorm.
 * User: sahmed6
 * Date: 10/17/2017
 * Time: 6:38 PM
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Individual;
use AppBundle\Entity\Objective;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class IndividualTest extends TestCase {
    public function createIndividual() {
        $individual = new Individual();
        $individual->setFirstName('John');
        $individual->setLastName('Adams');
        $individual->setMedicalId(random_int(1, 9000000));
        return $individual;
    }

    public function createObjective($individual) {
        $objective = new Objective();
        $objective
            ->setIndividual($individual)
            ->setName('O1')
            ->setGoalText('GoalText')
            ->setObjectiveText('ObjectiveText')
            ->setGuidanceNotes('GuidanceNotes')
            ->setFreqAmount(2)
            ->setFreqKind('/Week');
        return $objective;
    }

    public function testIndividual() {
        // Create the individual and assign it to a variable.
        $individual = $this->createIndividual();
        // Create the objectives and attach it to the individual.
        $objective = $this->createObjective($individual);
        $individual->addObjective($objective);

        // Now that we've created all the variables we need, let's actually test.

        // Make sure the individual exists.
        $this->assertEquals($individual->getFirstName(), 'John');
        $this->assertEquals($individual->getLastName(), 'Adams');

        // Make sure the individual has the objective.
        $this->assertEquals($objective, $individual->getObjectives()[0]);
        // Make sure the objective is assigned to the individual.
        $this->assertEquals($objective->getIndividual(), $individual);
        // Make sure the objective has the right frequency.
        $this->assertEquals("/Week", $objective->getFreqKind());
    }
}