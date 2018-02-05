<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This is the main route for the reviewing of notes.
 * There are also important methods in here like exporting, which are used in other controllers.
 * @Route("/reviewer", name="Reviewer Subsystem")
 */
class ReviewerController extends Controller
{
    /**
     * Generate a list of notes and send it on to Twig to render.
     * @Route("/", name="reviewer")
     */
    public function indexAction()
    {
        // Get all notes and render.
        $notes = $this->getDoctrine()
            ->getRepository(Note::class)
            ->findAll();
        return $this->render('reviewer/notes.html.twig', array(
            'notes' => $notes
        ));
    }

    /**
     * Review a specific note, fairly simple. We pull the note ID from the {id} and use it to render a template.
     * @Route("/review/{id}", name="Reviewer reviews a note.")
     */
    public function review(Request $request, Note $note)
    {
        return $this->render('reviewer/view.html.twig', array(
            'note' => $note
        ));
    }

    /**
     * Approve a specific note. We use the {id} variable to get the note ID we're approving.
     * @Route("/review/approve/{id}", name="Reviewer approves a note.")
     */
    public function approve(Request $request, Note $note)
    {
        // Approve the note and submit to database.
        $em = $this->getDoctrine()->getManager();
        $note->setState($note::ACCEPTED);
        $note->setSubmittedAt(new \DateTime());
        // Perist.
        $em->persist($note);
        $em->flush();
        // Render the note page again.
        return $this->render('reviewer/view.html.twig', array(
            'note' => $note
        ));
    }

    /**
     * This is a specific button on the HTML page for editing and then submitting.
     * The difference between this and the one above is *hint* the editing part.
     * @Route("/review/edit/{id}", name="Reviewer edits and approves a note.")
     */
    public function edit(Request $request, Note $note)
    {
        // Get edited content then submit to database.
        $em = $this->getDoctrine()->getManager();
        // Update the edited content.
        $note->setContent($request->get('content'));
        $note->setState($note::ACCEPTED);
        // Set the submitted date and persist.
        $note->setSubmittedAt(new \DateTime());
        $em->persist($note);
        $em->flush();
        // Render the note page again.
        return $this->render('reviewer/view.html.twig', array(
            'note' => $note
        ));
    }

    /**
     * Add a comment to a note for kicking back or simply for kicks.
     * @Route("/review/comment/{id}", name="Reviewer comments on a note.")
     */
    public function comment(Request $request, Note $note)
    {
        // Get the comment from the page and edit in the database.
        $em = $this->getDoctrine()->getManager();
        $note->setComment($request->get('comment'));
        // Figure out whether it's a comment submission or just a comment.
        // what this means is whether we need to kick it back to the customer
        // there are two options available to the enduser, regular submit and submit for correction.
        if ($request->get('correctsubmit') !== null) {
            $note->setState($note::KICKED_BACK);
        }
        // Persist to the database.
        $em->persist($note);
        $em->flush();
        // Redirect them back to the note page.
        return $this->redirect('../' . $note->getUuid());
    }

    /**
     * This is for exporting a note and is fairly simple to understand.
     * @Route("/export/{id}", name="Reviewer exports a note.")
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
}
