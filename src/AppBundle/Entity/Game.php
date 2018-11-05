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
     * @ORM\Column(name="is_waiting", type="integer")
     *
     */
    protected $isWaiting;

    //TODO : ajouter système de points, le positionnement

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
}