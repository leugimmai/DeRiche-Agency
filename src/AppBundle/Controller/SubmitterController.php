<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Individual;
use AppBundle\Entity\Note;
use AppBundle\Entity\Types\FormType;
use AppBundle\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the actual note creation system. This is the spine of the system.
 * @Route("/note", name="Note Creating Subsystem")
 */
class SubmitterController extends Controller
{
    /**
     * Main page which is essentially just a starting point.
     * It shows kicked back/draft notes and the search button.
     * @Route("/", name="note")
     */
    public function indexAction()
    {
        $draftNotes = [];
        $backNotes = [];

        if ($this->getUser()) {
            /** @var User $user */
            $user = $this->getUser();

            // Get the draft and kicked back notes.
            foreach ($user->getAuthoredNotes() as $note) {
                //Sort notes into groups based on state
                if ($note->getState() === $note::DRAFT) $draftNotes[] = $note;
                if ($note->getState() == $note::KICKED_BACK) $backNotes[] = $note;
            }
        }

        // Send it to the template as a $backNotes variable.
        return $this->render('notes/home.html.twig', array(
            'draftnotes' => $draftNotes,
            'backnotes' => $backNotes
        ));
    }

    /**
     * Review a specific note, fairly simple. We pull the note ID from the {id} and use it to render a template.
     * @Route("/render/", name="writers reviews a note.")
     */
    public function renderTest()
    {
        // Get all notes and render.
        $notes = $this->getDoctrine()
            ->getRepository(Note::class)
            ->findAll();
        return $this->render('writerreview.html.twig', array(
            'notes' => $notes
        ));
    }

    /**
     * Review a specific note, fairly simple. We pull the note ID from the {id} and use it to render a template.
     * @Route("/review/{id}", name="writer reviews a note.")
     */
    public function pre(Request $request, Note $note)
    {
        return $this->render('writerview.html.twig', array(
            'note' => $note
        ));
    }

    /**
     * Search for a individual for use and then forward a customer to the note creation system.
     * @Route("/findindividual/", name="Writer Find Individual")
     */
    public function findIndividual(Request $request)
    {
        // We get the name that the Author wants to look up.
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
                'lastName' => $familyName,
                'active' => true
            ]);

        // We then make sure that only one individual has that name.
        $count = count($individuals);
        if ($count === 0) {
            // If a individual was not found with that name.
            throw $this->createNotFoundException('No individual found for ' . $individualName);
        }
        if ($count === 1) {
            // If a individual was found then we push them to the note creation page.
            return $this->redirect('../create/' . $individuals[0]->getUuid());
        }
        if ($count > 1) {
            // If more than one individual was found then display an error.
            return $this->redirect('../../note/?error=Multiple');
        }
    }

    /**
     * The actual note creation page, this uses the individual ID rather than note ID.
     * That is the only odd part of this function as it should be using the draft ID and generating one
     * in the findindividual method.
     * @Route("/create/{id}", name="Writer Create Note")
     */
    public function create(Request $request, Individual $individual)
    {
        // Check if there's a note for this individual done today and whether it's a draft.
        $update = false;
        foreach ($individual->getNotes() as $n) {
            $ts = $n->getCreatedAt()->getTimeStamp();
            if (date('Y-m-d', strtotime("today")) == date('Y-m-d', $ts)) {
                // Verify that it's a draft before using it as the base.
                if ($n->getState() == $n::DRAFT || $n->getState() == $n::KICKED_BACK) {
                    $note = $n;
                    $update = true;
                }
            }
        }

        // Create a new Note if this is not a draft.
        if (!isset($note)) {
            $note = new Note();
            // Set some default values
            $note->setStaff($this->getUser());
            $note->setIndividual($individual);

            // Submit the draft so we can update it later
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();
        }

        // Create the form we show the user.
        $form = $this->createFormBuilder($note)
            ->add('content', TextareaType::class, array(
                'attr' => array('rows' => '25'),))
            ->add('signature', HiddenType::class)
            ->getForm();

        // Handle the form that we showed the user above.
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $note = $form->getData();
            $note->setState(20); // Send to reviewer.

            // Submit to the database. and persist.
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('home page');
        }

        // We get the objectives to send to the template
        $objArray = array();
        foreach ($individual->getObjectives()->toArray() as $item) {
            $objArray[$item->getFreqKind()][] = $item;
        }
        // We send a different render if this is an update because we need to include
        // the $note->getContent()
        if ($update) {
            return $this->render('notes/create.html.twig', array(
                'individual' => $individual,
                'form' => $form->createView(),
                'note' => $note,
                'content' => $note->getContent(),
                'objectives' => $objArray,
                'form_types' => FormType::getReadableValues()
            ));
        } else {
            return $this->render('notes/create.html.twig', array(
                'individual' => $individual,
                'form' => $form->createView(),
                'note' => $note,
                'objectives' => $objArray,
                'form_types' => FormType::getReadableValues()
            ));
        }
    }

    /**
     * This is for exporting a note and is fairly simple to understand.
     * @Route("/export/{id}", name="Writer exports a note.")
     */
    public function export(Request $request, Note $note)
    {
        // Let's generate the PDF using a library called Mpdf, you need to use the composer.json file to install it.
        $mpdf = new \Mpdf\Mpdf();
        // Set the header.
        $mpdf->SetHeader("Individual Note: " . $note->getIndividual()->getFirstName() . " " . $note->getIndividual()->getLastName());
        // Set the title of the note.
        $mpdf->SetTitle('Individual Note: ' . $note->getIndividual()->getFirstName() . " " . $note->getIndividual()->getLastName());
        // Write the basic note data.
        $mpdf->WriteHTML("<h3>Individual Name:</h3><p>" . $note->getIndividual()->getFirstName() . " " . $note->getIndividual()->getLastName() . "</p>");
        $mpdf->WriteHTML("<h3>Note Creation Date:</h3><p>" . $note->getCreatedAt()->format('m/d/Y') . "</p>");
        $mpdf->WriteHTML("<h3>Staff Member:</h3><p>" . $note->getStaff()->getFirstName() . " " . $note->getStaff()->getLastName() . "</p>");
        $mpdf->WriteHTML("<h3>Content:</h3><p>" . $note->getContent() . "</p>");
        $mpdf->WriteHTML('<h3>Signature:</h3><p><img src="data:image/jpg;base64, ' . $note->getSignature() . '"/></p>');
        // Iterate over the forms to attach them to the PDF as well.
        foreach($note->getForms() as $form) {
            // We add a page to the PDF and then write the form data to that page.
            $mpdf->AddPage();
            $mpdf->WriteHTML("<h1>Form: " . $form->getType() . "</h1>"); // TODO: Get actual text instead of enum.
            // TODO: Make it display actual form stuff somehow. Maybe hard code.
            // Iterate over the form data array and pull the fields we need.
            foreach($form->getData() as $k=>$v) {
                // Add a specific field for the signature as it needs to be an image, the rest are paragraphs.
                if($k == "form_signature_canvas") {
                    $mpdf->WriteHTML('<h3>'.$k.'</h3><p><img src="data:image/jpg;base64, ' . $v . '"/></p>');
                    continue;
                }
                $mpdf->WriteHTML("<h3>".$k."</h3><p>".$v."</p>");
            }
        }
        // Generate the output and send it back with a file name.
        // The filename would be Note_Oliver-10-22.pdf for example.
        $mpdf->Output('Note_'.$note->getIndividual()->getLastName().'-'.$note->getCreatedAt()->format("m-d").'.pdf', 'I');
    }

    /**
     * This is a method called by Javascript and constantly updates the note
     * so as to note lose content.
     * @Route("/updatedraft/{id}", name="Writer Update Draft")
     */
    public function updateDraft(Request $request, Note $note)
    {
        // This only handles the inbound request via JS, the state remains 10 until it's submitted.
        $content = $request->get('content');
        $note->setContent($content);

        // Submit to database and persist.
        $em = $this->getDoctrine()->getManager();
        $em->persist($note);
        $em->flush();

        // Empty response since this should only be called by JS.
        return new Response();
    }
}