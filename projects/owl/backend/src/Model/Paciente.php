<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Table(name="PACIENTE")
 * @Entity
 */
class Paciente
{
    /**
     * @var string
     *
     * @Column(name="CDFILIAL", type="string", nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $cdfilial;

    /**
     * @var string
     *
     * @Column(name="CDPACIENTE", type="string", length=4, nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $cdpaciente;

    /**
     * @var string
     *
     * @Column(name="NMPACIENTE", type="string", length=2, nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $nmpaciente;

    /**
     * @var string
     *
     * @Column(name="NMSOBRENOME", type="string", length=4, nullable=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $nmsobrenome;

    public function getCdfilial() {
        return $this->cdfilial;
    }

    public function setCdfilial($cdfilial) {
        $this->cdfilial = $cdfilial;
    }

    public function getCdpaciente() {
        return $this->cdpaciente;
    }

    public function setCdpaciente($cdpaciente) {
        $this->cdpaciente = $cdpaciente;
    }

    public function getNmpaciente() {
        return $this->nmpaciente;
    }

    public function setNmpaciente($nmpaciente) {
        $this->nmpaciente = $nmpaciente;
    }

    public function getNmsobrenome() {
        return $this->nmsobrenome;
    }

}
