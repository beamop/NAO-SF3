<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 17/03/2018
 * Time: 15:06
 */

namespace AppBundle\Service;

use AppBundle\Entity\Bird;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\ORM\EntityManagerInterface;

class ImportTaxRef
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function import() {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder($delimiter = ';', $enclosure = '"')]);

        $birdRepository = $this->em->getRepository('AppBundle:Bird');
        $nb = $birdRepository->count();
        //dump($nb);

        if ($nb > 0) {
            throw new \Exception('La table des références TaxRef n\'est pas vide !');
        }

        $csvFile = __DIR__ . '/../../../web/' . 'taxref11_aves.csv';
        $csvContents=file_get_contents($csvFile);
        $csvConverted = mb_convert_encoding($csvContents, "UTF-8", "Windows-1252");

        // decoding CSV contents
        $datas = $serializer->decode($csvConverted, 'csv');

        foreach($datas as $data) {
            $bird = new Bird();
            $bird->setId($data['CD_NOM']);
            $bird->setRegne($data['REGNE']);
            $bird->setPhylum($data['PHYLUM']);
            $bird->setClasse($data['CLASSE']);
            $bird->setOrdre($data['ORDRE']);
            $bird->setFamille($data['FAMILLE']);
            $bird->setTaxSup($data['CD_TAXSUP']);
            $bird->setRef($data['CD_REF']);
            $bird->setRang($data['RANG']);
            $bird->setNomScientif($data['LB_NOM']);
            $bird->setAuteur($data['LB_AUTEUR']);
            $bird->setNomComplet($data['NOM_COMPLET']);
            $bird->setNomValide($data['NOM_VALIDE']);
            $bird->setNomCourant($data['NOM_VERN']);
            $bird->setNomCourantEn($data['NOM_VERN_ENG']);
            $bird->setHabitat($data['HABITAT']);
            $bird->setStatutFR($data['FR']);
            $bird->setStatutGF($data['GF']);
            $bird->setStatutMAR($data['MAR']);
            $bird->setStatutGUA($data['GUA']);
            $bird->setStatutSM($data['SM']);
            $bird->setStatutSB($data['SB']);
            $bird->setStatutSPM($data['SPM']);
            $bird->setStatutMAY($data['MAY']);
            $bird->setStatutEPA($data['EPA']);
            $bird->setStatutREU($data['REU']);
            $bird->setStatutSA($data['SA']);
            $bird->setStatutTA($data['TA']);
            $bird->setStatutNC($data['NC']);
            $bird->setStatutWF($data['WF']);
            $bird->setStatutPF($data['PF']);
            $bird->setStatutCLI($data['CLI']);

            $this->em->persist($bird);
        }
        $this->em->flush();

        return "Fichier importé !";
    }

}