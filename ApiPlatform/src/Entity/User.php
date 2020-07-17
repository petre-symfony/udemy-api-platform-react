<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *   itemOperations={
 *     "get" = {
 *       "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *       "normalization_context"={
 *         "groups"={"get"}
 *       }
 *     },
 *     "put" = {
 *       "security" = "is_granted('IS_AUTHENTICATED_FULLY') && object === user",
 *       "denormalization_context"={
 *         "groups"={"put"}
 *       },
 *       "normalization_context"={
 *         "groups"={"get"}
 *       }
 *     }
 *   },
 *   collectionOperations={
 *     "post" = {
 *       "denormalization_context"={
 *         "groups"={"post"}
 *       },
 *       "normalization_context"={
 *         "groups"={"get"}
 *       }
 *     }
 *   }
 * )
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
      * @Groups("get")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
      * @Groups({"get", "post", "get_comments_of_post_with_author"})
      * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
      * @Groups({"get", "put", "post", "get_comments_of_post_with_author"})
      * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"put", "post"})
     * @Assert\NotBlank()
     * @Assert\Regex(
     *   pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *   message="Password must be seven characters long and contain at least one digit, one upper case letter and one lower case lette"
     * )
     */
    private $plainPassword;

    /**
     * @Assert\NotBlank()
     * @Assert\Expression(
     *   "this.getPlainPassword() === this.getRetypedPassword()",
     *   message="Passwords does not match"
     * )
     * @Groups({"put", "post"})
     */
    private $retypedPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get","post"})
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
      * @Groups("get")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=BlogPost::class, mappedBy="author")
     * @Groups("get")
     */
    private $posts;

    public function __construct() {
        $this->comments = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string {
        return (string) $this->username;
    }

    public function setUsername(string $username): self {
        $this->username = $username;

        return $this;
    }

    public function getName(): ?string {
      return $this->name;
    }

    public function setName(string $name): self {
      $this->name = $name;

      return $this;
    }

    public function getEmail(): ?string {
      return $this->email;
    }

    public function setEmail(string $email): self {
      $this->email = $email;

      return $this;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string {
        return (string) $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword():?string {
      return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword(string $plainPassword): self {
      $this->plainPassword = $plainPassword;
      return $this;
    }

    /**
     * @return mixed
     */
    public function getRetypedPassword():?string {
      return $this->retypedPassword;
    }

    /**
     * @param mixed $retypedPassword
     */
    public function setRetypedPassword(string $retypedPassword): self {
      $this->retypedPassword = $retypedPassword;
      return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt() {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection {
        return $this->comments;
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getPosts(): Collection {
        return $this->posts;
    }
}
