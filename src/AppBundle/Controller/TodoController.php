<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/1/2017
 * Time: 6:22 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;


class TodoController extends Controller {

	/**
	 * @Route("/", name="todo_list")
	 */
	public function listAction(Request $request)
	{
		$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findAll();
		return $this->render('todo/index.html.twig',array('todos'=> $todos));
	}


	/**
	 * @Route("/todos/create", name="todo_create")
	 */
	public function createAction(Request $request)
	{
		$todo = new Todo();
		$form = $this->createFormBuilder($todo)
			->add('name',TextType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
			->add('category',TextType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
			->add('description',TextareaType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
			->add('priority',ChoiceType::class, array('choices'=>array('Low'=>'Low','Normal'=>'Normal','High'=>'High'),'attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
			->add('due_date',DateTimeType::class, array('attr'=>array('class'=>'formcontrol','style'=>'margin-bottom:15px')))
			->add('submit',SubmitType::class, array('attr'=>array('class'=>'btn btn-primary','style'=>'margin-bottom:15px')))
			->getForm();

		$form->handleRequest($request);
		if($form->isSubmitted()&& $form->isValid()){
			$name = $form['name']->getData();
			$category = $form['category']->getData();
			$desc = $form['description']->getData();
			$priority = $form['priority']->getData();
			$due_date = $form['due_date']->getData();
			$now = new\DateTime('now');
			$todo->setCategory($category);
			$todo->setCreateDate($now);
			$todo->setDueDate($due_date);
			$todo->setName($name);
			$todo->setPriority($priority);
			$em = $this->getDoctrine()->getManager();
			$em->persist($todo);
			$em->flush();
			$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findAll();
			$this->addFlash('notice','Todo Added');
			return $this->redirectToRoute('todo_list');
		}
		return $this->render('todo/create.html.twig',array('form'=>$form->createView()));
	}


	/**
	 * @Route("/todos/edit/{id}", name="todo_edit")
	 */
	public function editAction($id, Request $request)
	{
		$todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);

		$form = $this->createFormBuilder($todo)
		             ->add('name',TextType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('category',TextType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('description',TextareaType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('priority',ChoiceType::class, array('choices'=>array('Low'=>'Low','Normal'=>'Normal','High'=>'High'),'attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('due_date',DateTimeType::class, array('attr'=>array('class'=>'formcontrol','style'=>'margin-bottom:15px')))
		             ->add('submit',SubmitType::class, array('attr'=>array('class'=>'btn btn-primary','style'=>'margin-bottom:15px')))
		             ->getForm();
		$form->handleRequest($request);
		if($form->isSubmitted()&& $form->isValid()){
			$name = $form['name']->getData();
			$category = $form['category']->getData();
			$desc = $form['description']->getData();
			$priority = $form['priority']->getData();
			$due_date = $form['due_date']->getData();
			$now = new\DateTime('now');
			$todo->setCategory($category);
			$todo->setCreateDate($now);
			$todo->setDueDate($due_date);
			$todo->setName($name);
			$todo->setPriority($priority);
			$em = $this->getDoctrine()->getManager();
			$em->persist($todo);
			$em->flush();
			$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findAll();
			$this->addFlash('notice','Todo Edited');
			return $this->redirectToRoute('todo_list');
		}
		return $this->render('todo/edit.html.twig',array('form'=>$form->createView()));	}


	/**
	 * @Route("/todos/details/{id}", name="todo_details")
	 */
	public function detailsAction($id, Request $request)
	{
		$todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
		return $this->render('todo/details.html.twig',array('todo'=> $todo));
	}

	/**
	 * @Route("/todos/delete/{id}", name="todo_details")
	 */
	public function deleteAction($id, Request $request)
	{
		$todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
		$em = $this->getDoctrine()->getManager();
		$em->remove($todo);
		//$em->flush();
		$this->addFlash('notice','Todo deleted');
		return $this->redirectToRoute('todo_list');
	}

}