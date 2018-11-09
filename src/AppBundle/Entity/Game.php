<?php
/**
 * Created by PhpStorm.
 * User: Adrien Ruisseau
 * Date: 18/09/2018
 * Time: 11:23
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Many Users have One Game.
     * @ORM\OneToMany(targetEntity="User", mappedBy="game", cascade={"remove"})
     */
    private $users;

    /**
     * Many Positions have One Game.
     * @ORM\OneToMany(targetEntity="Position", mappedBy="game", cascade={"remove"})
     */
    private $positions;

    /**
     * Many Comments have One Game.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="game", cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\Column(name="is_waiting", type="integer")
     *
     */
    protected $isWaiting;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->positions = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIsWaiting()
    {
        return $this->isWaiting;
    }

    /**
     * @param mixed $isWaiting
     */
    public function setIsWaiting($isWaiting)
    {
        $this->isWaiting = $isWaiting;
    }

    /**
     * Add user
     *
     * @param User $user
     *
     * @return Game
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add position
     *
     * @param Position $position
     *
     * @return Game
     */
    public function addPosition(Position $position)
    {
        $this->positions[] = $position;

        return $this;
    }

    /**
     * Remove position
     *
     * @param Position $position
     */
    public function removePosition(Position $position)
    {
        $this->positions->removeElement($position);
    }

    /**
     * Get positions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Game
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

}