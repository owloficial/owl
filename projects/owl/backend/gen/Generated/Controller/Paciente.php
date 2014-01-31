<?php
/**
 * Created by JetBrains PhpStorm.
 * User: francismararaujo
 * Date: 20/01/14
 * Time: 11:02
 * To change this template use File | Settings | File Templates.
 */

namespace Generated\Controller;

use Zeedhi\DTO\Response\Message;
use Zeedhi\DTO\Response\Method;

class Paciente extends \Zeedhi\Controller\Base{

    public function get(\Zeedhi\Request\FilterDataRequest $request, \Zeedhi\DTO\Response $response) {

        $cdfilial = 9999;
        $sql = "
            SELECT
                PACIENTE.CDPACIENTE,
                PACIENTE.NMPACIENTE,
                LEITO.CDLEITO,
                LEITO.CDANDAR,
                (CASE
                  WHEN DIAGNOST.NMDIAGNOST IS NULL
                  THEN 'PACIENTE NÃO POSSUI DIAGNÓSTICO.'
                  ELSE DIAGNOST.NMDIAGNOST
                END) AS NMDIAGNOST
            FROM PACIENTE
            JOIN INTERNAC
                ON PACIENTE.CDPACIENTE = INTERNAC.CDPACIENTE
                AND PACIENTE.CDFILIAL = INTERNAC.CDFILIAL
            JOIN LEITO
                ON LEITO.CDLEITO = INTERNAC.CDLEITO
                AND LEITO.CDFILIAL = INTERNAC.CDFILIAL
            LEFT JOIN DIAGINTER
                ON DIAGINTER.CDPACIENTE = PACIENTE.CDPACIENTE
                AND DIAGINTER.DTINTERNA = INTERNAC.DTINTERNA
            LEFT JOIN DIAGNOST
                ON DIAGINTER.CDDIAGNOST = DIAGNOST.CDDIAGNOST
                AND DIAGINTER.CDTABECDI = DIAGNOST.CDTABECDI
            WHERE PACIENTE.CDFILIAL = $cdfilial
        ";
        $data = $this->getApplication()->getDataSourceManager()->fetchAll($sql);
        $preparedData = $this->preparePatientsListDataSet($data);
        $response->addDataSet(new \Zeedhi\DTO\Response\DataSet('Paciente/paciente' , $preparedData));

    }

    public function preparePatientsListDataSet($dataset) {

        $preparedDataset = array();

        foreach($dataset as $patientsDataset) {

            $buffer = array();

            $buffer['NMPACIENTE'] = $patientsDataset['NMPACIENTE'];
            $buffer['CDANDAR'] = $patientsDataset['CDANDAR'];
            $buffer['CDLEITO'] = $patientsDataset['CDLEITO'];
            $buffer['NMDIAGNOST'] = $patientsDataset['NMDIAGNOST'];

            $preparedDataset[] = $buffer;

        }

        return $preparedDataset;

    }

}













//use Zeedhi\DTO\Response\Message;
//use Zeedhi\DTO\Response\Method;
//class Paciente extends PacienteBase {
//
//    private $patientDataSourceName = 'Paciente/paciente';
//    private $internacDataSourceName = 'Paciente/internac';
//
//    public function get(\Zeedhi\Request\FilterDataRequest $request, \Zeedhi\DTO\Response $response) {
//
//
//        $cdfilial = 9999;
//
//        $sql = "SELECT p.* FROM PACIENTE p
//                 WHERE p.CDFILIAL           = '$cdfilial'";
//
//        $data = $this->getApplication()->getDataSourceManager()->findBySQL($this->getDataSource($request, $this->patientDataSourceName), '\\Model\\Paciente', 'p', $sql);
//        $preparedData = $this->preparePatientsListDataSet($data);
//        $response->addDataSet(new \Zeedhi\DTO\Response\DataSet($this->patientDataSourceName, $preparedData));
//
//////        @todo - pegar valores da sessão;
//
////        $cdfilial = 9999;
////        $nrorg = 1;
////        $data = $this->getPatients($request, $cdfilial, $nrorg);
////        $preparedData = $this->preparePatientsListDataSet($data);
////        $response->addDataSet(new \Zeedhi\DTO\Response\DataSet($this->patientDataSourceName, $preparedData));
//
//    }
//
//    public function getPatients($request, $cdfilial, $nrorg) {
//
//        $filter = array(
//            array("name"=>"CDFILIAL", "operator"=>"=", "value"=> $cdfilial),
//            array("name"=>"NRORG", "operator"=>"=", "value"=> $nrorg)
//        );
//
//        $patients = $this->getApplication()->getDataSourceManager()->findBy(
//            $this->getDataSource($request, $this->patientDataSourceName),$filter, $request->getPage()
//        );
//
//        foreach($patients as $patient) {
//
//            $questionary = $this->getInternac($request, $patient['CDPACIENTE']);
//            $patients['INTERNAC'][$patient['CDPACIENTE']] = $questionary[0];
//
//        }
//
//        return $patients;
//
//    }
//
//    public function getInternac($request, $cdpaciente) {
//
//        $filter = array(
//            array("name"=>"CDPACIENTE", "operator"=>"=", "value"=> $cdpaciente),
//            array("name"=>"CDFILIAL", "operator"=>"=", "value"=> 9999)
//        );
//
//        return $this->getApplication()->getDataSourceManager()->findBy(
//            $this->getDataSource($request, $this->internacDataSourceName),$filter, $request->getPage()
//        );
//
//    }
//
//
//    public function preparePatientsListDataSet($dataset) {
//
//        $preparedDataset = array();
//
//        foreach($dataset as $patientsDataset) {
//
////
////            $buffer = array();
////
//            $buffer['nmpaciente'] = $patientsDataset['NMPACIENTE'];
////
//////            $buffer['answered_quest_id'] = $answeredQuestDataset['CDFILIAL'];
//////            $buffer['answered_quest_status'] = $answeredQuestDataset['NMPACIENTE'];
//////
////
//////            $internac = $answeredQuestDataset['INTERNAC'];
//////            $buffer['nmpaciente'] = $internac['CDLEITO'];
////
//////            $buffer['quest_exec_id'] = $internac['CDFILIAL'];
////////            $buffer['quest_exec_description'] = $internac['NMPACIENTE'];
//////            $buffer['quest_exec_start_date'] = $internac['NMPACIENTE'];
//////            $buffer['quest_exec_end_date'] = $internac['NMPACIENTE'];
////
//            $preparedDataset[] = $buffer;
////
//        }
//
//        return $preparedDataset;
//
//    }
//
//}