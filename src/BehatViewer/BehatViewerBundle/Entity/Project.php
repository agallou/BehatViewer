<?php

namespace BehatViewer\BehatViewerBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints,
    BehatViewer\BehatViewerBundle\Entity;

/**
 * BehatViewer\BehatViewerBundle\Entity\Project
 *
 * @ORM\Table(
 * 		name="project",
 * 		uniqueConstraints={
 * 			@ORM\UniqueConstraint(name="user__name", columns={"user_id", "name"}),
 * 			@ORM\UniqueConstraint(name="user__slug", columns={"user_id", "slug"})
 * 		}
 * )
 * @ORM\Entity(repositoryClass="BehatViewer\BehatViewerBundle\Entity\Repository\ProjectRepository")
 * @Constraints\UniqueEntity(fields={"name", "user"}, message="You already own a project with the same name")
 * @Constraints\UniqueEntity(fields={"slug", "user"}, message="You already own a project with the same identifier")
 */
class Project extends Base
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string $slug
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="slug", type="string", length=50)
     */
    private $slug;

    /**
     * @var string $base_url
     *
     * @Assert\NotBlank()
     * @Assert\Url()
     * @ORM\Column(name="base_url", type="string", length=255)
     */
    private $base_url;

    /**
     * @var string $output_path
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="output_path", type="string", length=255)
     */
    private $output_path;

    /**
     * @var string $root_path
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="root_path", type="string", length=255)
     */
    private $root_path;

    /**
     * @var string $test_command
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="test_command", type="text", length=65532)
     */
    private $test_command;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="project_type", options={"default": "public"})
     */
    private $type = 'public';

    /**
     * @ORM\OneToMany(targetEntity="Build", mappedBy="project", cascade={"remove","persist"})
     */
    private $builds;

    /**
     * @var \BehatViewer\BehatViewerBundle\Entity\User $user
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="projects", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Definition", mappedBy="project", cascade={"remove","persist"})
     */
    private $definitions;

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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param string $name
     */
    public function setUser(Entity\user $user)
    {
        $this->user = $user;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set base_url
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->base_url = $baseUrl;
    }

    /**
     * Get base_url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Set output_path
     *
     * @param string $outputPath
     */
    public function setOutputPath($outputPath)
    {
        $this->output_path = $outputPath;
    }

    /**
     * Get output_path
     *
     * @return string
     */
    public function getOutputPath()
    {
        return $this->output_path;
    }

    /**
     * Set source_path
     *
     * @param string $sourcePath
     */
    public function setRootPath($rootPath)
    {
        $this->root_path = $rootPath;
    }

    /**
     * Get source_path
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->root_path;
    }

    public function __construct()
    {
        $this->builds = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add builds
     *
     * @param \BehatViewer\BehatViewerBundle\Entity\Build $builds
     */
    public function addBuild(\BehatViewer\BehatViewerBundle\Entity\Build $builds)
    {
        $this->builds[] = $builds;
    }

    /**
     * Get builds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBuilds()
    {
        return $this->builds;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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

    /**
     * Add definitions
     *
     * @param \BehatViewer\BehatViewerBundle\Entity\Definition $definitions
     */
    public function addDefinition(\BehatViewer\BehatViewerBundle\Entity\Definition $definitions)
    {
        $this->definitions[] = $definitions;
    }

    /**
     * Get definitions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * Set test_command
     *
     * @param string $testCommand
     */
    public function setTestCommand($testCommand)
    {
        $this->test_command = $testCommand;
    }

    /**
     * Get test_command
     *
     * @return string
     */
    public function getTestCommand()
    {
        return $this->test_command;
    }

    public function getLastBuild()
    {
        $builds = $this->getBuilds();

        return $builds->last() ?: null;
    }

    /**
     * Get definitions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set test_command
     *
     * @param string $testCommand
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
