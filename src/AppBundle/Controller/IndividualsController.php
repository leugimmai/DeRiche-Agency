<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Objective;
use AppBundle\Entity\Individual;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This is an important class and is used for all individual-related activity.
 * This has actions like, creation, deletion, listing, adjusting, etc.
 * @Route("/individuals", name="View Individuals")
 */
class IndividualsController extends Controller
{
    /**
     * This is the main action for the route and is just listing the individuals.
     * @Route("/", name="individuals")
     */
    public function indexAction()
    {
        // We get two separate lists.
        // One for active individuals, one for disabled/archived individuals.
        $activeIndividuals = array();
        $inactiveIndividuals = array();

        // We pull all individuals and separate them.
        $allIndividuals = $this->getDoctrine()
            ->getRepository(Individual::class)
            ->findAll();

        // The actual separation part.
        foreach ($allIndividuals as $individual) {
            if ($individual->getActive()) array_push($activeIndividuals, $individual);
            else                       array_push($inactiveIndividuals, $individual);
        }

        // We generate a class to send to Twig which will then separate it as needed.
        $individualsQuery = new \stdClass();
        $individualsQuery->active = $activeIndividuals;
        $individualsQuery->inactive = $inactiveIndividuals;

        return $this->render('individuals.html.twig', array(
            'individualsQuery' => $individualsQuery
        ));
    }

    /**
     * This is the viewing of the individual page. We send everything we can over to twig.
     * Twig does the heavy lifting for this class.
     * @Route("/individual/{id}/", name="View Individual")
     */
    public function viewIndividual($id)
    {
        // We find the individual ID and use it to serve the individual page.
        $individual = $this->getDoctrine()
            ->getRepository(Individual::class)
            ->find($id);
        // If there's no individual ID then we throw it away.
        if (!$individual) {
            throw $this->createNotFoundException(
                'No individual found for id ' . $id
            );
        }
        // Render if found.
        return $this->render('individual.html.twig', array('individual' => $individual));
    }

    /**
     * This is for adding an objective to a individual and is quite involved due to the ability
     * to add multiple at once.
     * @Route("/individual/{id}/objective/", name="Add Individual Objective")
     */
    public function addObjective(Request $request, Individual $individual)
    {
        // First we iterate through a ParameterPag for the request and find the objective words we need
        $objectives = [];
        // This is the list of objective words.
        $objwords = ['objectiveName', 'goalText', 'objectiveText', 'guidanceNotes', 'freqAmount', 'freqKind'];
        // Actual iteration process.
        foreach ($request->request->all() as $k => $o) {
            // We make sure that the KEY value starts with an objective word.
            $objword = substr($k, 0, -1);
            // We get the number of the objective for this individual that we want to edit.
            // This is reliant on the understanding that we append the objective variables in HTML
            // with the number of the objective.
            $objnum = intval(substr($k, -1));
            // Let's check if the first part of the key is what we want and the second part is an integer.
            if (in_array($objword, $objwords) && is_numeric(substr($k, -1))) {
                // Add to the objectives table that we'll iterate through and persist.
                // Notice how we use the objective number pulled above and start the array with that.
                // That is the actual key that we need to edit the right objective.
                $objectives[$objnum][$objword] = $o;
            }
        }

        // Iterate through the objective array we just created and persist them all in the database.
        foreach ($objectives as $obj) {
            // Create
            $objective = new Objective();
            $objective
                ->setIndividual($individual)
                ->setName($obj['objectiveName'])
                ->setGoalText($obj['goalText'])
                ->setObjectiveText($obj['objectiveText'])
                ->setGuidanceNotes($obj['guidanceNotes'])
                ->setFreqAmount($obj['freqAmount'])
                ->setFreqKind($obj['freqKind']);
            // This is where we add each objective and persist it.
            $individual->addObjective($objective);
            $em = $this->getDoctrine()->getManager();
            $em->persist($objective);
            $em->flush();
        }
        // Return them back to the individual page.
        return $this->redirect('../');
    }

    /**
     * Deleting a individual objective. Self explanatory.
     * @Route("/individual/{individual}/objective/{objective}/delete", name="Delete Individual Objective")
     */
    public function deleteObjective(EntityManagerInterface $em, Individual $individual, Objective $objective)
    {
        // If the objective is attached to the individual then we delete it.
        if ($individual !== $objective->getIndividual()) {
            throw new BadRequestHttpException("Note does not belong to individual specified.");
        }
        // Persist the delete.
        $em->remove($objective);
        $em->flush();
        // Redirect them back to the individual page.
        return $this->redirect('../../');
    }

    /**
     * This is for updating a specific objective. Much less complex than adding.
     * @Route("/individual/{individual}/objective/{objective}/patch", name="Update Individual Objective")
     */
    public function updateObjective(Request $request, Individual $individual, Objective $objective)
    {
        if ($individual !== $objective->getIndividual()) {
            throw new BadRequestHttpException("Note does not belong to individual specified.");
        }

        // Update the objective with the variables we pull from the request.
        $objective
            ->setName($request->get('objectiveName'))
            ->setGoalText($request->get('goalText'))
            ->setObjectiveText($request->get('objectiveText'))
            ->setGuidanceNotes($request->get('guidanceNotes'))
            ->setFreqAmount($request->get('freqAmount'))
            ->setFreqKind($request->get('freqKind'));
        // Persist the changes.
        $em = $this->getDoctrine()->getManager();
        $em->persist($objective);
        $em->flush();
        // Redirect them back to the individual page.
        return $this->redirect('../../');
    }


    /**
     * This is the actual creation of a individual.
     * @Route("/create/", name="Create Individual")
     */
    public function createIndividual(Request $request)
    {
        // Get all the variables we need.
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $medical_id = $request->get('medical_id');
        // Use ternary operator to check whether we got anything for these fields
        // as it's not required on the HTML end.
        $seizure_status = empty($request->get('seizure')) ? false : true;
        $bowel_status = empty($request->get('bowel')) ? false : true;

        // Generate the actual individual and fill in the variables.
        $individual = new Individual();
        $individual
            ->setFirstName($first_name)
            ->setLastName($last_name)
            ->setMedicalId($medical_id)
            ->setSeizure($seizure_status)
            ->setBowel($bowel_status);

        // Submit the individual to the database and persist.
        $em = $this->getDoctrine()->getManager();
        $em->persist($individual);
        $em->flush();

        // Handle objectives - Syed A. ~ May be a little complicated.
        // Let's iterate through the ParameterBag for the request. - More in depth explained above.
        $objectives = [];
        $objwords = ['objectiveName', 'goalText', 'objectiveText', 'guidanceNotes', 'freqAmount', 'freqKind'];
        foreach ($request->request->all() as $k => $o) {
            $objword = substr($k, 0, -1);
            $objnum = intval(substr($k, -1));
            // Let's check if the first part of the key is what we want and the second part is an integer.
            if (in_array($objword, $objwords) && is_numeric(substr($k, -1))) {
                // Add to the objectives table that we'll iterate through and persist.
                $objectives[$objnum][$objword] = $o;
            }
        }

        // Iterate through the objective array we just created and persist them in the database.
        foreach ($objectives as $obj) {
            // Create
            $objective = new Objective();
            $objective
                ->setIndividual($individual)
                ->setName($obj['objectiveName'])
                ->setGoalText($obj['goalText'])
                ->setObjectiveText($obj['objectiveText'])
                ->setGuidanceNotes($obj['guidanceNotes'])
                ->setFreqAmount($obj['freqAmount'])
                ->setFreqKind($obj['freqKind']);
            // Persist
            $individual->addObjective($objective);
            $em->persist($objective);
            $em->flush();
        }

        //Send them to the newly created individual's page
        return $this->redirectToRoute('View Individual', array('id' => $individual->getUuid()));
    }

    /**
     * @Route("/individual/{id}/update", name="Update Individual")
     */
    public function updateIndividual(Request $request, Individual $individual)
    {
        // Get all the variables we need.
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $medical_id = $request->get('medical_id');
        // Use ternary operator to check whether we got anything for these fields
        // as it's not required on the HTML end.
        $seizure_status = empty($request->get('seizure')) ? false : true;
        $bowel_status = empty($request->get('bowel')) ? false : true;

        // Only update if we got all three fields.
        if ($first_name && $last_name && $medical_id) {
            $individual
                ->setFirstName($first_name)
                ->setLastName($last_name)
                ->setMedicalId($medical_id)
                ->setSeizure($seizure_status)
                ->setBowel($bowel_status);
            // Submit the individual and persist.
            $em = $this->getDoctrine()->getManager();
            $em->persist($individual);
            $em->flush();
        }
        // Redirect them back to the individual page.
        return $this->render('individual.html.twig', array('individual' => $individual));
    }

    /**
     * Archive a individual, essentially disabling them.
     * @Route("/{id}/archive", name="Archive Individual")
     */
    public function archiveIndividual(Request $request, Individual $individual)
    {
        // Disable the user and update the database.
        $individual->setActive(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($individual);
        $em->flush();
        return $this->redirectToRoute('individuals');
    }

    /**
     * UnArchive a individual, essentially disabling them.
     * @Route("/{id}/unarchive", name="UnArchive Individual")
     */
    public function unArchiveIndividual(Request $request, Individual $individual)
    {
        // Enable the user and update the database.
        $individual->setActive(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($individual);
        $em->flush();
        return $this->redirectToRoute('individuals');
    }
}