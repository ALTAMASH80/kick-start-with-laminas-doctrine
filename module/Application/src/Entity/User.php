<?php
namespace Application\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // this will be like an alias for Gedmo extensions annotations
use LmcUser\Entity\UserInterface;
use Application\Entity\HierarchicalRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LmcRbacMvc\Identity\IdentityInterface;

/**
 * User
 *
 * @ORM\Table(name="user", 
 * uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"}), 
 * @ORM\Index(name="users_oauth_login_idx_idx", columns={"oauth_uid", "oauth_provider", "state"}), 
 * @ORM\Index(name="user_email_idx_idx", columns={"email"})})
 * @ORM\Entity
 */
class User implements UserInterface, IdentityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=150, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=150, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=150, nullable=true)
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="displayname", type="string", length=250, nullable=true)
     */
    private $displayname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="admin_notes", type="string", length=255, nullable=true)
     */
    private $adminNotes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="remember_me", type="string", length=150, nullable=true)
     */
    private $rememberMe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="oauth_provider", type="string", length=150, nullable=true)
     */
    private $oauthProvider;

    /**
     * @var string|null
     *
     * @ORM\Column(name="oauth_uid", type="string", length=150, nullable=true)
     */
    private $oauthUid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codeconfirmation", type="string", length=150, nullable=true)
     */
    private $codeconfirmation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="passrecover", type="string", length=150, nullable=true)
     */
    private $passrecover;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_imported", type="boolean", nullable=true)
     */
    private $isImported = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="state", type="boolean", nullable=true, options={"default"="1"})
     */
    private $state = true;

    /**
     * @var int|null
     *
     * @ORM\Column(name="msg_sent", type="integer", nullable=true)
     */
    private $msgSent = '0';

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;
    
    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity=HierarchicalRole::class, mappedBy="user", cascade={"persist"})
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }
    
    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getPassword(){
        return $this->password;
    }

    public function setPassword($password){
        $this->password = $password;
        return $this;
    }

    public function getUsername(){
        return $this->username;
    }

    public function setUsername( $username){
        $this->username = $username;
        return $this;
    }

    public function getDisplayName(){
        return $this->displayname;
    }

    public function setDisplayName($displayname){
        $this->displayname = $displayname;
        return $this;
    }

    public function getState(){
        return $this->state;
    }

    public function setState($state){
        $this->state = $state;
        return $this;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }
    
    /**
     * Set the list of roles
     * @param Collection $roles
     */
    public function setRoles(Collection $roles)
    {
        $this->roles->clear();
        foreach ($roles as $role) {
            $this->roles[] = $role;
        }
    }
    
    /**
     * Add one role to roles list
     * @param \Application\Entity\HierarchicalRole $role
     */
    public function addRole(HierarchicalRole $role)
    {
        if ($this->roles->contains($role)) {
            return;
        }
        
        $this->roles->add($role);
        $role->addUser($this);
    }
    
    public function addRoles($roles): self
    {
        if (!$this->roles->contains($roles)) {
            $this->roles[] = $roles;
            $roles->addUser($this);
        }
        
        return $this;
    }
    
    public function removeRoles($roles): self
    {
        if ($this->roles->removeElement($roles)) {
            // set the owning side to null (unless already changed)
            if ($roles->getUser() === $this) {
                $roles->setUser(null);
            }
        }
        
        return $this;
    }
}
