<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bird
 *
 * @ORM\Table(name="nao_bird")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BirdRepository")
 */
class Bird
{
    /**
     * @var int
     *
     * @ORM\Column(name="b_id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="b_regne", type="string", length=255)
     */
    private $regne;

    /**
     * @var string
     *
     * @ORM\Column(name="b_phylum", type="string", length=255)
     */
    private $phylum;

    /**
     * @var string
     *
     * @ORM\Column(name="b_classe", type="string", length=255)
     */
    private $classe;

    /**
     * @var string
     *
     * @ORM\Column(name="b_ordre", type="string", length=255)
     */
    private $ordre;

    /**
     * @var string
     *
     * @ORM\Column(name="b_famille", type="string", length=255)
     */
    private $famille;

    /**
     * @var int
     *
     * @ORM\Column(name="b_cd_taxsup", type="integer")
     */
    private $taxSup;

    /**
     * @var int
     *
     * @ORM\Column(name="b_cd_ref", type="integer")
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="b_rang", type="string", length=4)
     */
    private $rang;

    /**
     * @var string
     *
     * @ORM\Column(name="b_lb_nom", type="string", length=255)
     */
    private $nomScientif;

    /**
     * @var string
     *
     * @ORM\Column(name="b_lb_auteur", type="string", length=255)
     */
    private $auteur;

    /**
     * @var string
     *
     * @ORM\Column(name="b_nom_complet", type="string", length=255)
     */
    private $nomComplet;

    /**
     * @var string
     *
     * @ORM\Column(name="b_nom_valide", type="string", length=255)
     */
    private $nomValide;

    /**
     * @var string
     *
     * @ORM\Column(name="b_nom_vern", type="string", length=255)
     */
    private $nomCourant;

    /**
     * @var string
     *
     * @ORM\Column(name="b_nom_vern_eng", type="string", length=255)
     */
    private $nomCourantEn;

    /**
     * @var int
     *
     * @ORM\Column(name="b_habitat", type="smallint")
     */
    private $habitat;

    /**
     * @var string
     *
     * @ORM\Column(name="b_fr", type="string", length=1)
     */
    private $statutFR;

    /**
     * @var string
     *
     * @ORM\Column(name="b_gf", type="string", length=1)
     */
    private $statutGF;

    /**
     * @var string
     *
     * @ORM\Column(name="b_mar", type="string", length=1)
     */
    private $statutMAR;

    /**
     * @var string
     *
     * @ORM\Column(name="b_gua", type="string", length=1)
     */
    private $statutGUA;

    /**
     * @var string
     *
     * @ORM\Column(name="b_sm", type="string", length=1)
     */
    private $statutSM;

    /**
     * @var string
     *
     * @ORM\Column(name="b_sb", type="string", length=1)
     */
    private $statutSB;

    /**
     * @var string
     *
     * @ORM\Column(name="b_spm", type="string", length=1)
     */
    private $statutSPM;

    /**
     * @var string
     *
     * @ORM\Column(name="b_may", type="string", length=1)
     */
    private $statutMAY;

    /**
     * @var string
     *
     * @ORM\Column(name="b_epa", type="string", length=1)
     */
    private $statutEPA;

    /**
     * @var string
     *
     * @ORM\Column(name="b_reu", type="string", length=1)
     */
    private $statutREU;

    /**
     * @var string
     *
     * @ORM\Column(name="b_sa", type="string", length=1)
     */
    private $statutSA;

    /**
     * @var string
     *
     * @ORM\Column(name="b_ta", type="string", length=1)
     */
    private $statutTA;

    /**
     * @var string
     *
     * @ORM\Column(name="b_nc", type="string", length=1)
     */
    private $statutNC;

    /**
     * @var string
     *
     * @ORM\Column(name="b_wf", type="string", length=1)
     */
    private $statutWF;

    /**
     * @var string
     *
     * @ORM\Column(name="b_pf", type="string", length=1)
     */
    private $statutPF;

    /**
     * @var string
     *
     * @ORM\Column(name="b_cli", type="string", length=1)
     */
    private $statutCLI;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return Bird
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set regne
     *
     * @param string $regne
     *
     * @return Bird
     */
    public function setRegne($regne)
    {
        $this->regne = $regne;

        return $this;
    }

    /**
     * Get regne
     *
     * @return string
     */
    public function getRegne()
    {
        return $this->regne;
    }

    /**
     * Set phylum
     *
     * @param string $phylum
     *
     * @return Bird
     */
    public function setPhylum($phylum)
    {
        $this->phylum = $phylum;

        return $this;
    }

    /**
     * Get phylum
     *
     * @return string
     */
    public function getPhylum()
    {
        return $this->phylum;
    }

    /**
     * Set classe
     *
     * @param string $classe
     *
     * @return Bird
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe
     *
     * @return string
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set ordre
     *
     * @param string $ordre
     *
     * @return Bird
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return string
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set famille
     *
     * @param string $famille
     *
     * @return Bird
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * Get famille
     *
     * @return string
     */
    public function getFamille()
    {
        return $this->famille;
    }


    /**
     * Set rang
     *
     * @param string $rang
     *
     * @return Bird
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return string
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set nomScientif
     *
     * @param string $nomScientif
     *
     * @return Bird
     */
    public function setNomScientif($nomScientif)
    {
        $this->nomScientif = $nomScientif;

        return $this;
    }

    /**
     * Get nomScientif
     *
     * @return string
     */
    public function getNomScientif()
    {
        return $this->nomScientif;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     *
     * @return Bird
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set nomComplet
     *
     * @param string $nomComplet
     *
     * @return Bird
     */
    public function setNomComplet($nomComplet)
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    /**
     * Get nomComplet
     *
     * @return string
     */
    public function getNomComplet()
    {
        return $this->nomComplet;
    }

    /**
     * Set nomValide
     *
     * @param string $nomValide
     *
     * @return Bird
     */
    public function setNomValide($nomValide)
    {
        $this->nomValide = $nomValide;

        return $this;
    }

    /**
     * Get nomValide
     *
     * @return string
     */
    public function getNomValide()
    {
        return $this->nomValide;
    }

    /**
     * Set nomCourant
     *
     * @param string $nomCourant
     *
     * @return Bird
     */
    public function setNomCourant($nomCourant)
    {
        $this->nomCourant = $nomCourant;

        return $this;
    }

    /**
     * Get nomCourant
     *
     * @return string
     */
    public function getNomCourant()
    {
        return $this->nomCourant;
    }

    /**
     * Set nomCourantEn
     *
     * @param string $nomCourantEn
     *
     * @return Bird
     */
    public function setNomCourantEn($nomCourantEn)
    {
        $this->nomCourantEn = $nomCourantEn;

        return $this;
    }

    /**
     * Get nomCourantEn
     *
     * @return string
     */
    public function getNomCourantEn()
    {
        return $this->nomCourantEn;
    }

    /**
     * Set habitat
     *
     * @param integer $habitat
     *
     * @return Bird
     */
    public function setHabitat($habitat)
    {
        $this->habitat = $habitat;

        return $this;
    }

    /**
     * Get habitat
     *
     * @return int
     */
    public function getHabitat()
    {
        return $this->habitat;
    }

    /**
     * Set statutFR
     *
     * @param string $statutFR
     *
     * @return Bird
     */
    public function setStatutFR($statutFR)
    {
        $this->statutFR = $statutFR;

        return $this;
    }

    /**
     * Get statutFR
     *
     * @return string
     */
    public function getStatutFR()
    {
        return $this->statutFR;
    }

    /**
     * Set statutGF
     *
     * @param string $statutGF
     *
     * @return Bird
     */
    public function setStatutGF($statutGF)
    {
        $this->statutGF = $statutGF;

        return $this;
    }

    /**
     * Get statutGF
     *
     * @return string
     */
    public function getStatutGF()
    {
        return $this->statutGF;
    }

    /**
     * Set statutMAR
     *
     * @param string $statutMAR
     *
     * @return Bird
     */
    public function setStatutMAR($statutMAR)
    {
        $this->statutMAR = $statutMAR;

        return $this;
    }

    /**
     * Get statutMAR
     *
     * @return string
     */
    public function getStatutMAR()
    {
        return $this->statutMAR;
    }

    /**
     * Set statutGUA
     *
     * @param string $statutGUA
     *
     * @return Bird
     */
    public function setStatutGUA($statutGUA)
    {
        $this->statutGUA = $statutGUA;

        return $this;
    }

    /**
     * Get statutGUA
     *
     * @return string
     */
    public function getStatutGUA()
    {
        return $this->statutGUA;
    }

    /**
     * Set statutSM
     *
     * @param string $statutSM
     *
     * @return Bird
     */
    public function setStatutSM($statutSM)
    {
        $this->statutSM = $statutSM;

        return $this;
    }

    /**
     * Get statutSM
     *
     * @return string
     */
    public function getStatutSM()
    {
        return $this->statutSM;
    }

    /**
     * Set statutSB
     *
     * @param string $statutSB
     *
     * @return Bird
     */
    public function setStatutSB($statutSB)
    {
        $this->statutSB = $statutSB;

        return $this;
    }

    /**
     * Get statutSB
     *
     * @return string
     */
    public function getStatutSB()
    {
        return $this->statutSB;
    }

    /**
     * Set statutSPM
     *
     * @param string $statutSPM
     *
     * @return Bird
     */
    public function setStatutSPM($statutSPM)
    {
        $this->statutSPM = $statutSPM;

        return $this;
    }

    /**
     * Get statutSPM
     *
     * @return string
     */
    public function getStatutSPM()
    {
        return $this->statutSPM;
    }

    /**
     * Set statutMAY
     *
     * @param string $statutMAY
     *
     * @return Bird
     */
    public function setStatutMAY($statutMAY)
    {
        $this->statutMAY = $statutMAY;

        return $this;
    }

    /**
     * Get statutMAY
     *
     * @return string
     */
    public function getStatutMAY()
    {
        return $this->statutMAY;
    }

    /**
     * Set statutEPA
     *
     * @param string $statutEPA
     *
     * @return Bird
     */
    public function setStatutEPA($statutEPA)
    {
        $this->statutEPA = $statutEPA;

        return $this;
    }

    /**
     * Get statutEPA
     *
     * @return string
     */
    public function getStatutEPA()
    {
        return $this->statutEPA;
    }

    /**
     * Set statutREU
     *
     * @param string $statutREU
     *
     * @return Bird
     */
    public function setStatutREU($statutREU)
    {
        $this->statutREU = $statutREU;

        return $this;
    }

    /**
     * Get statutREU
     *
     * @return string
     */
    public function getStatutREU()
    {
        return $this->statutREU;
    }

    /**
     * Set statutSA
     *
     * @param string $statutSA
     *
     * @return Bird
     */
    public function setStatutSA($statutSA)
    {
        $this->statutSA = $statutSA;

        return $this;
    }

    /**
     * Get statutSA
     *
     * @return string
     */
    public function getStatutSA()
    {
        return $this->statutSA;
    }

    /**
     * Set statutTA
     *
     * @param string $statutTA
     *
     * @return Bird
     */
    public function setStatutTA($statutTA)
    {
        $this->statutTA = $statutTA;

        return $this;
    }

    /**
     * Get statutTA
     *
     * @return string
     */
    public function getStatutTA()
    {
        return $this->statutTA;
    }

    /**
     * Set statutNC
     *
     * @param string $statutNC
     *
     * @return Bird
     */
    public function setStatutNC($statutNC)
    {
        $this->statutNC = $statutNC;

        return $this;
    }

    /**
     * Get statutNC
     *
     * @return string
     */
    public function getStatutNC()
    {
        return $this->statutNC;
    }

    /**
     * Set statutWF
     *
     * @param string $statutWF
     *
     * @return Bird
     */
    public function setStatutWF($statutWF)
    {
        $this->statutWF = $statutWF;

        return $this;
    }

    /**
     * Get statutWF
     *
     * @return string
     */
    public function getStatutWF()
    {
        return $this->statutWF;
    }

    /**
     * Set statutPF
     *
     * @param string $statutPF
     *
     * @return Bird
     */
    public function setStatutPF($statutPF)
    {
        $this->statutPF = $statutPF;

        return $this;
    }

    /**
     * Get statutPF
     *
     * @return string
     */
    public function getStatutPF()
    {
        return $this->statutPF;
    }

    /**
     * Set statutCLI
     *
     * @param string $statutCLI
     *
     * @return Bird
     */
    public function setStatutCLI($statutCLI)
    {
        $this->statutCLI = $statutCLI;

        return $this;
    }

    /**
     * Get statutCLI
     *
     * @return string
     */
    public function getStatutCLI()
    {
        return $this->statutCLI;
    }

    /**
     * Set taxSup
     *
     * @param integer $taxSup
     *
     * @return Bird
     */
    public function setTaxSup($taxSup)
    {
        $this->taxSup = $taxSup;

        return $this;
    }

    /**
     * Get taxSup
     *
     * @return integer
     */
    public function getTaxSup()
    {
        return $this->taxSup;
    }

    /**
     * Set ref
     *
     * @param integer $ref
     *
     * @return Bird
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return integer
     */
    public function getRef()
    {
        return $this->ref;
    }
}
