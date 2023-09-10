<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Permissions\Rbac\RoleInterface;
use LmcRbacMvc\Permission\PermissionInterface;
use Application\Entity\User;
use LmcUser\Entity\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */
class HierarchicalRole implements RoleInterface
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=48, unique=true)
     */
    protected $name;

    /**
     * @var RoleInterface[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="HierarchicalRole")
     */
    protected $children = [];

    /**
     * @var PermissionInterface[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity=Permission::class, indexBy="name", fetch="EAGER")
     */
    protected $permissions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity=User::class, cascade={"persist"})
     */
    protected $user;

    /**
     * Init the Doctrine collection
     */
    public function __construct()
    {
        $this->children    = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->user        = new ArrayCollection();
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the role identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the role name
     *
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Get the role name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function addChild(RoleInterface $child): void
    {
        $this->children[] = $child;
    }

    /**
     * {@inheritDoc}
     */
    public function addPermission($permission): void
    {
        if (is_string($permission)) {
            $permission = new Permission($permission);
        }

        $this->permissions[(string) $permission] = $permission;
    }

    /**
     * {@inheritDoc}
     */
    public function hasPermission($permission): bool
    {
        // This can be a performance problem if your role has a lot of permissions. Please refer
        // to the cookbook to an elegant way to solve this issue

        return isset($this->permissions[(string) $permission]);
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren(): iterable
    {
        return $this->children;
    }

    /**
     * {@inheritDoc}
     */
    public function hasChildren() : bool
    {
        return !$this->children->isEmpty();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getParents(): iterable
    {
        return [];
    }
    
    /**
     * {@inheritDoc}
     */
    public function addParent($parent): void
    {
        return;
    }

    /**
     * Add posts
     *
     * @param \Doctrine\Common\Collections\Collection|\Application\Entity\User $users
     *
     * @return self
     */
    public function addUsers(ArrayCollection $users)
    {
        foreach ($users as $user) {
            $this->addUser($user);
        }
        
        return $this;
    }
    
    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user)
    {
        if ($this->user->contains($user)) {
            return;
        }
        
        $this->user->add($user);
        $user->addRole($this);
    }
    
    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $posts
     */
    public function removeUsers(ArrayCollection $posts)
    {
        foreach ($posts as $post) {
            $this->removeUser($post);
        }
        
        return $this;
    }
    
    /**
     * @param UserInterFace $user
     */
    public function removeUser(User $user)
    {
        if (!$this->user->contains($user)) {
            return;
        }
        
        $this->user->removeElement($user);
        $user->removeRole($this);
    }
}
