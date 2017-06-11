<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/1/2017
 * Time: 6:22 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class TodoController extends Controller {

	/**
	 * @Route("/", name="todo_list")
	 */
	public function listAction(Request $request)
	{
		$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBy(array('Userid'=>$this->get('security.token_storage')->getToken()->getUser()));
		return $this->render('todo/index.html.twig',array('todos'=> $todos));
	}


	/**
	 * @Route("/todos/create", name="todo_create")
	 */
	public function createAction(Request $request,UserInterface $user)
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
			$todo->setUserid($this->get('security.token_storage')->getToken()->getUser());
			$em = $this->getDoctrine()->getManager();
			$em->persist($todo);
			$em->flush();
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
	 * @Route("/todos/delete/{id}", name="todo_delete")
	 */
	public function deleteAction($id, Request $request)
	{
		$todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
		$em = $this->getDoctrine()->getManager();
		$em->remove($todo);
		$em->flush();
		$this->addFlash('notice','Todo deleted');
		return $this->redirectToRoute('todo_list');
	}


	/**
	 * @Route("/testuser", name="just_fake_user_add")
	 */
	public function registersAction(UserPasswordEncoderInterface $encoder)
	{
		$user = new User();
		$user->setName('test');
		$user->setEmail('test@test.com');
		$user->setPlainPassword('test');
		$encoder = $this->get('security.password_encoder');
		$user->setRole('ROLE_ADMIN');
		$password = $encoder->encodePassword($user, $user->getPlainPassword());
		$user->setPassword($password);

		$em = $this->getDoctrine()->getManager();
		$em->persist($user);
		$em->flush();
		// whatever *your* User object is
//		$user = new User();
//		$user->setUsername('test');
//		$user->setEmail('test@test.com');
//		$plainPassword = 'test';
//		$encoded = $encoder->encodePassword($user, $plainPassword);
//
//		$user->setPassword($encoded);
//		$em = $this->getDoctrine()->getManager();
//		$em->persist($user);
//		$em->flush();

		return $this->redirectToRoute('todo_list');

	}


	/**
	 * @Route("/login", name="login")
	 */
	public function loginAction(Request $request, AuthenticationUtils $authUtils)
	{
		$helper = $this->get('security.authentication_utils');

		return $this->render(
			'todo/login.html.twig',
			array(
				'last_username' => $helper->getLastUsername(),
				'error'         => $helper->getLastAuthenticationError(),
			)
		);
	}


	/**
	 * @Route("/login_check", name="security_login_check")
	 */
	public function loginCheckAction()
	{

	}

	/**
	 * @Route("/logout", name="logout")
	 */
	public function logoutAction(Request $request, AuthenticationUtils $authUtils)
	{
		// get the login error if there is one
		return null;
	}

	/**
	 * @Route("/admin", name="todo_admin")
	 */
	public function adminAction(Request $request, AuthenticationUtils $authUtils)
	{
		//get list of users, and display them to view their todos, modify their info, or delete them
		$todos = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
		return $this->render('todo/admin.html.twig',array('users'=> $todos));
	}
	/**
	 * @Route("/admin/add", name="todo_admin_add")
	 */
	public function adminAddAction(Request $request, AuthenticationUtils $authUtils)
	{
		$user = new User();
		$form = $this->createFormBuilder($user)
		             ->add('name',TextType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('role',ChoiceType::class, array('choices'=>array('User'=>'ROLE_USER','Admin'=>'ROLE_ADMIN'),'attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
					 ->add('email',EmailType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('password',PasswordType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->getForm();
		$form->handleRequest($request);
		if($form->isSubmitted()&& $form->isValid()){
			$name = $form['name']->getData();
			$email = $form['email']->getData();
			$password = $form['password']->getData();
			$role = $form['role']->getData();
			//if($password != ""){
			$user->setPlainPassword($password);
			$encoder = $this->get('security.password_encoder');
			$password = $encoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);
			//}
			$user->setName($name);
			$user->setEmail($email);
			$user->setRole($role);

			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			$this->addFlash('notice','User Added');
			return $this->redirectToRoute('todo_admin');
		}
		return $this->render('todo/adminAdd.html.twig',array('form'=>$form->createView()));
	}

	/**
	 * @Route("/admin/delete/{id}", name="todo_admin_delete")
	 */
	public function deleteUserAction($id, Request $request)
	{
		$todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findBy(array('id'=>$id));
		$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
		$em = $this->getDoctrine()->getManager();
		foreach ( $todos as $todo ) {$em->remove($todo);}
		$em->remove($user);
		$em->flush();
		$this->addFlash('notice','User deleted');
		return $this->redirectToRoute('todo_admin');
	}


	/**
	 * @Route("/register", name="register")
	 */
	public function registerAction(Request $request)
	{
		// Create a new blank user and process the form
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Encode the new users password
			$encoder = $this->get('security.password_encoder');
			$password = $encoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);

			// Set their role
			$user->setRole('ROLE_USER');

			// Save
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			return $this->redirectToRoute('login');
		}

		return $this->render('todo/register.html.twig', [
			'form' => $form->createView(),
		]);
	}


	/**
	 * @Route("/admin/edit/{id}", name="todo_admin_edit")
	 */
	public function editAdminAction($id, Request $request)
	{
		$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
		$pass = $user->getPassword();
		//die(var_dump($pass));

		$form = $this->createFormBuilder($user)
		             ->add('name',TextType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('role',ChoiceType::class, array('choices'=>array('User'=>'ROLE_USER','Admin'=>'ROLE_ADMIN'),'attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('email',EmailType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->add('password',PasswordType::class, array('attr'=>array('class'=>'form-control','style'=>'margin-bottom:15px')))
		             ->getForm();
		$form->handleRequest($request);
		//die(var_dump($id));
		if($form->isSubmitted()&& $form->isValid()){
			//die($pass);
			//$userdb = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
			//$pass = $userdb-> getPlainPassword();
			//die($pass);
			$name = $form['name']->getData();
			$email = $form['email']->getData();
			$password = $form['password']->getData();
			$role = $form['role']->getData();

			if($password != "PASS"){
				$user->setPlainPassword($password);
				$encoder = $this->get('security.password_encoder');
				$password = $encoder->encodePassword($user, $user->getPlainPassword());
				$user->setPassword($password);
			}else{
				$user->setPassword($pass);
			}

			$user->setName($name);
			$user->setEmail($email);
			$user->setRole($role);
			//die($pass);
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			$this->addFlash('notice','User Edited');
			return $this->redirectToRoute('todo_admin');
		}
		return $this->render('todo/adminEdit.html.twig',array('form'=>$form->createView()));
	}


}