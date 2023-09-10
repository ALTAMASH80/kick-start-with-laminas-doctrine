<?php

declare(strict_types=1);

namespace Category\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categories
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity
 */
class Categories
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
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="root_id", type="bigint", nullable=false)
     */
    private $rootId;

    /**
     * @var int
     *
     * @ORM\Column(name="ordsr", type="bigint", nullable=false)
     */
    private $ordsr;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true, options={"default"="1"})
     */
    private $isActive = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_validated", type="boolean", nullable=false)
     */
    private $isValidated = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="in_directory", type="boolean", nullable=false)
     */
    private $inDirectory = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="in_discussions", type="boolean", nullable=true, options={"default"="1"})
     */
    private $inDiscussions = true;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="in_groups", type="boolean", nullable=true, options={"default"="1"})
     */
    private $inGroups = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_waiting", type="boolean", nullable=false)
     */
    private $isWaiting = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="is_other", type="boolean", nullable=false)
     */
    private $isOther = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="seo_title", type="text", length=65535, nullable=true)
     */
    private $seoTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="seo_service_description", type="text", length=65535, nullable=true)
     */
    private $seoServiceDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="seo_slug", type="text", length=65535, nullable=true)
     */
    private $seoSlug;

    /**
     * @var int|null
     *
     * @ORM\Column(name="suggested_by", type="integer", nullable=true)
     */
    private $suggestedBy;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content_url", type="string", length=255, nullable=true)
     */
    private $contentUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content_type", type="string", length=50, nullable=true)
     */
    private $contentType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="content_id", type="integer", nullable=true)
     */
    private $contentId;
}
