<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Table(name="app_users")
* @ORM\Entity
* @UniqueEntity(fields="email", message="Email already taken")
* @UniqueEntity(fields="name", message="name already taken")
*/
class User implements UserInterface
{
	/*
	 * @ORM\OneToMany(targetEntity="Todo",mappedBy="Userid")
	 */
	private $todos;
	public function __construct() {
		$this->todos = new ArrayCollection();
	}


	/**
	 * @ORM\Id;
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string", length=40)
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string", length=50)
	 */
	protected $role;

	/**
	 * @Assert\Length(max=4096)
	 */
	protected $plainPassword;

	/**
	 * @ORM\Column(type="string", length=64)
	 */
	protected $password;

	public function eraseCredentials()
	{
		return null;
	}

	public function getRole()
	{
		return $this->role;
	}

	public function setRole($role = null)
	{
		$this->role = $role;
	}

	public function getRoles()
	{
		return [$this->getRole()];
	}

	public function getId()
	{
		return $this->id;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getUsername()
	{
		return $this->email;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	public function setPlainPassword($plainPassword)
	{
		$this->plainPassword = $plainPassword;
	}

	public function getSalt()
	{
		return null;
	}
}
