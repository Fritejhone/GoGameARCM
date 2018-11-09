<?php
/**
 * Created by PhpStorm.
 * User: Adrien Ruisseau
 * Date: 05/11/2018
 * Time: 18:10
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Position
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PositionRepository")
 * @ORM\Table(name="position")
 */
class Position
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
     * @ORM\Column(name="posX", type="string", length=255, nullable=false)
     */
    protected $posX;

    /**
     * @ORM\Column(name="posY", type="string", length=255, nullable=false)
     */
    protected $posY;

    /**
     * @ORM\Column(name="color", type="string", length=255, nullable=false)
     */
    protected $color;

    /**
     * Many Position have One User.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="positions")
     */
    protected $user;

    /**
     * Many Position have One Game.
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="positions")
     */
    private $game;

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
    public function getPosX()
    {
        return $this->posX;
    }

    /**
     * @param mixed $posX
     */
    public function setPosX($posX)
    {
        $this->posX = $posX;
    }

    /**
     * @return mixed
     */
    public function getPosY()
    {
        return $this->posY;
    }

    /**
     * @param mixed $posY
     */
    public function setPosY($posY)
    {
        $this->posY = $posY;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

}