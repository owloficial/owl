<?php
namespace Zeedhi\Report\Reader;

/**
 *
 * @author tuliopinto
 */
interface IReader {

    /**
     * Read report data and create a Report DTO.
     * 
     * @return \Zeedhi\Report\Report
     */
    public function read();

}