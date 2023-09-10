<?php

namespace Posts\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // this will be like an alias for Gedmo extensions annotations
use Doctrine\Common\Collections\ArrayCollection;
use Application\Entity\User as UserEntity;

/**
 * Post
 *
 * @Gedmo\Loggable
 * @ORM\Table(
 *      name                = "posts",
 *      indexes = {
 * @ORM\Index(name="title_idx",        columns={"title"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Posts\Repository\PostRepository")
 */
class Post
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     */
    protected $title;
    
    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="text")
     */
    protected $body;
    
    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="integer", options={"unsigned"=true, "default"="1"} )
     */
    protected $is_post;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;
    
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated_at;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="Categories", mappedBy="posts")
     */
//     protected $categories;

    /**
     * @var \Application\Entity\User
     *
     * @Gedmo\Versioned
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;
    
    /**
     * Constructor
     */
    public function __construct()
    {
//         $this->categories   = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set body
     *
     * @param string $body
     *
     * @return Post
     */
    public function setBody($body)
    {
        $this->body = (string)$body;
        
        return $this;
    }
    
    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Set is_post
     *
     * @param integer $is_post
     *
     * @return Post
     */
    public function setIsPost($is_post)
    {
        $this->is_post = $is_post;
        
        return $this;
    }
    
    /**
     * Get is_post
     *
     * @return integer
     */
    public function getIsPost()
    {
        return $this->is_post;
    }
    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = (string)$slug;
        
        return $this;
    }
    
    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }
    
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->created_at = $createdAt;
        return $this;
    }
    
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }
    
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updated_at = $updatedAt;
        return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    /**
     * Add categories
     *
     * @param \Doctrine\Common\Collections\ArrayCollection|\Articles\Entity\Category $categories
     *
     * @return self
     */
    public function addCategories(ArrayCollection $categories)
    {
        foreach ($categories as $category) {
            $this->addCategorie($category);
        }
        
        return $this;
    }
    
    /**
     * @param Category $category
     */
    public function addCategorie(Category $category)
    {
        if ($this->categories->contains($category)) {
            return;
        }
        
        $this->categories->add($category);
        $category->addPost($this);
    }
    
    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|Category $categories
     */
    public function removeCategories($categories)
    {
        if($categories instanceof Category){
            $this->removeCategorie($categories);
        }else{
            foreach ($categories as $post) {
                $this->removeCategorie($post);
            }
        }
        
        return $this;
    }
    
    /**
     * @param Category $category
     */
    public function removeCategorie(Category $category)
    {
        if (!$this->categories->contains($category)) {
            return;
        }
        
        $this->categories->removeElement($category);
        $category->removePost($this);
    }
    
    /**
     * Set author
     *
     * @param \Application\Entity\User $author
     *
     * @return Post
     */
    public function setAuthor(UserEntity $author = null)
    {
        $this->author = $author;
        
        return $this;
    }
    
    /**
     * Get author
     *
     * @return \LmcUserDoctrineORM\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
