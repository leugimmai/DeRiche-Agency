<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Entity\Individual;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Main route for auditing tools, primarily, auditing by individual, daterange and doing a full backup.
 * @Route("/audit/")
 */
class AuditController extends Controller
{

    /**
     * Main index for the audit panel which just loads the template.
     * @Route(name="audit")
     */
    public function indexAction()
    {
        return $this->render('admin/audit.html.twig');
    }

    /**
     * Route for when the admin wants to audit by individual.
     * @Route("individual", name="Audit By Individual")
     */
    public function auditIndividual(Request $request)
    {
        // We get the name that the Admin wants to look up.
        $individualName = $request->get('name');
        // We then separate the name into two so we can search by first and last name.
        $names = explode(' ', $individualName, 2);
        $givenName = $names[0];
        $familyName = isset($names[1]) ? $names[1] : null;
        // The actual look up function.
        $individuals = $this->getDoctrine()
            ->getRepository(Individual::class)
            ->findBy([
                'firstName' => $givenName,
                'lastName' => $familyName
            ]);
        // We then make sure that only one individual has that name.
        $count = count($individuals);
        if ($count === 0) {
            // If a individual was not found with that name.
            throw $this->createNotFoundException('No individual found for ' . $individualName);
        } else {
            // If a individual was found then we render the template with the notes.
            return $this->render('admin/audit.html.twig', array('notes' => $individuals[0]->getNotes(),
                'individual' => $individuals[0]->getFirstName() . ' ' . $individuals[0]->getLastName()));
        }
    }

    /**
     * This is for when an admin wants all notes in a certain time period.
     * We use the Repository file for Notes and added a getBetweenDates function which makes this simpler.
     * @Route("dates", name="Audit By DateRange")
     */
    public function auditDates(Request $request)
    {
        // We get the variables and convert them into Datetime objects.
        $startDate = \DateTime::createFromFormat('m/d/Y', $request->get('start'));
        $endDate = \DateTime::createFromFormat('m/d/Y', $request->get('end'));
        // Find the notes between that date.
        $notes = $this->getDoctrine()
            ->getRepository(Note::class)
            ->getBetweenDates($startDate, $endDate);
        // Return the template
        return $this->render('admin/audit.html.twig', array('notes' => $notes));
    }

    /**
     * This is when the Administrator wants to do a full backup of the database.
     * We use shell_exec to pull a backup from the various databases.
     * @Route("db-backup", name="Audit - Full Backup")
     */
    public function dbBackup(EntityManagerInterface $em, Request $request)
    {
        // We pull the database values from Doctrine/Symfony that we'll need for the commands below.
        $dbuser = $em->getConnection()->getUsername();
        $dbpasswd = $em->getConnection()->getPassword();
        $database = $em->getConnection()->getDatabase();
        // This is primarily for the demo as we use SQLite in the backend.
        if (substr($database, -7) == ".sqlite") {
            $output = shell_exec("sqlite3 $database .dump");
            // We generate a response object and send it back to the enduser.
            $response = new Response();
            // This is a TEXT file that we name .sql because it is generating raw SQL in $output
            $response->headers->set('Content-Type', 'text/plain');
            $response->headers->set('Content-disposition', 'attachment;filename=full_backup.sql');
            $response->setContent($output);
            $response->setStatusCode(Response::HTTP_OK);
            $response->sendHeaders();
            $response->send();
        }
        // Actual MySQL Dump function - this uses the backend function.
        $output = shell_exec("mysqldump -u $dbuser --password=$dbpasswd $database | gzip --best");

        // We generate a response object and send it back to the enduser.
        $response = new Response();
        // This is a gzipped file that we name .sql because it is generating a gzipped out that we'll provide
        // to the enduser for download.
        $response->headers->set('Content-Type', 'application/x-gzip');
        $response->headers->set('Content-disposition', 'attachment;filename=full_backup.sql.gz');
        $response->setContent($output);
        $response->setStatusCode(Response::HTTP_OK);
        $response->sendHeaders();
        $response->send();
    }
}
