<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use AppBundle\Entity\Form;
use AppBundle\Entity\Types\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This is the main route for generating/viewing forms.
 * This is quite an important Class.
 * @Route("/form/", name="Form Creating Subsystem")
 */
class FormController extends Controller
{
    /**
     * This is the actual form creating function.
     * It takes a form type and a note ID. It then gets the form template and renders it.
     * @Route("{note}/create/{form_type}", name="forms_create")
     */
    public function renderForm(Note $note, $form_type)
    {
        // First we check if the form type exists. If it does not we serve an error.
        if (!FormType::isValueExist($form_type)) {
            throw new BadRequestHttpException("$form_type is not a valid form.");
        }
        // If it does then we pull it and render the template.
        return $this->render(FormType::getTwigTemplate($form_type),
            array('note' => $note, 'type' => $form_type, 'data' => array())
        );
    }

    /**
     * This is the actual submission of the form.
     * We get the form_type and the noteid and use that to attach the form to the note.
     * @Route("{note}/submit/{form_type}", name="Save form to note")
     */
    public function submitForm(Note $note, Request $request, $form_type)
    {
        // First we check if the form type exists. If it does not we serve an error.
        if (!FormType::isValueExist($form_type)) {
            throw new BadRequestHttpException("$form_type is not a valid form.");
        }

        // Iterate through a ParameterBag to get the variables we need.
        // The ones we need start with form_ and we store in formData
        $formData = array();
        foreach ($request->request->all() as $k => $o) {
            // Check if it starts with "form_" - if it does then it's part of the form data.
            if (0 === strpos($k, 'form_')) {
                // Add to the formData array so we can use it later.
                $formData[$k] = $o;
            }
        }

        // Create a new form using the data we have and persist it.
        $form = new Form();
        $form->setNote($note);
        $form->setType($form_type);
        // This is where we use the data we got from the parameter pag above.
        $form->setData($formData);
        $em = $this->getDoctrine()->getManager();
        $em->persist($form);
        $em->flush();
        // Forward the user back to the actual note creation page with the form attached.
        return $this->redirect('/note/create/' . $note->getIndividual()->getUuid());
    }

    /**
     * This is the actual rendering of the form for viewing, the difference between this and create
     * is that the input fields are generated with the "disabled" tag so they can't adjust the text.
     * @Route("view/{form}", name="View Form")
     */
    public function viewForm(Form $form)
    {
        return $this->render(FormType::getTwigTemplate($form->getType()),
            array('data' => $form->getData(), 'note' => $form->getNote(),
                'type' => $form->getType())
        );
    }

    /**
     * This is for deleting a form from the note/database.
     * This can ONLY be done when a note is in the draft state.
     * @Route("delete/{form}", name="Delete Form")
     */
    public function deleteForm(Form $form = null, Request $request)
    {
        // We get the note that's attached to the form.
        $n = $form->getNote();
        // We only delete forms from drafts.
        if ($form && $n->getState() == $n::DRAFT) {
            $em = $this->getDoctrine()->getManager();
            // We remove it from the database and persist.
            $em->remove($form);
            $em->flush();
        }

        // Send them back to where they come from.
        $referrer = $request->headers->get('referer');
        return $this->redirect($referrer);
    }
}