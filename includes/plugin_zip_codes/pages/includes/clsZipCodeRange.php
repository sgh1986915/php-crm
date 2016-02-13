<?php
/**
 * Class to find zip codes within an approximate
 * distance of another zip code. This can be useful
 * when trying to find retailers within a certain
 * number of miles to a customer.
 *
 * This cnation.lass makes some assumptions that I consider
 * pretty safe.  First it assumes there is a database
 * that houses all of the zip code information.
 * Secondly it assumes there is a way to validate a
 * zip code for a given country.  It makes one bad
 * assumption and that is that the world is flat. See
 * comments below for an explanation.
 *
include('include/clsZipCodeRange.php');
$objZipCodeRange = new ZipCodesRange($sSearchZipCode, $iRadius);
$sQuery = "$sSearchZipCode";

foreach ($objZipCodeRange->zipCodes as $key => $value){


}

 */
class ZipCodesRange
{
  /**
   * The conversion factor to go from miles to degrees.
   * @var float
   */
  var $milesToDegrees = .01445;
  /**
   * The zipcode we are starting from.
   * @var string
   */
  var $zipCode;
  /**
   * The maximum distance in miles to return results for.
   * @var float
   */
  var $range;
  /**
   * The country the zip code is in.
   * @var string Two character ISO code.
   */
  var $country;
  /**
   * The result of our search.
   * array(zip1 => distance, zip2 =>distance,...)
   * @var array
   */
  var $zipCodes = array ();
  /**
   * The database table to look for zipcodes in.
   * @var string
   */
  var $dbTable = 'zipcodes';
  /**
   * The name of the column containing the zip code.
   * @var string
   */
  var $dbZip = 'ZipCode';
  /**
   * The name of the column containing the longitude.
   * @var string
   */
  var $dbLon = 'Longitude';
  /**
   * The name of the column containing the latitude.
   * @var string
   */
  var $dbLat = 'Latitude';

  // Naveed;
  var $xLon = 1;
  var $xLat = 1;

  /**
   * Constructor. Calls initialization method.
   *
   * @access private
   * @param  string  $zipCode
   * @param  float   $range
   * @param  string  $country Optional. Defaults to US.
   * @return object
   */
  function ZipCodesRange($zipCode, $range, $country = 'US')
  {

    $this->_initialize($zipCode, $range, $country);
  }

  /**
   * Initialization method.
   * Checks data and sets member variables.
   *
   * @access private
   * @param  string  $zipCode
   * @param  float   $range
   * @param  string  $country Optional. Defaults to US.
   * @return boolean
   */
  function _initialize($zipCode, $range, $country) {

    // Check the country.
    if ($this->validateCountry($country)) {
      $this->country = $country;
    } else {
      trigger_error('Invalid country: ' . $country);
      return FALSE;
    }

    // Check the zipcode.
    if ($this->validateZipCode($zipCode, $country)) {
      $this->zipCode = $zipCode;
    } else {
      trigger_error('Invalid zip code: ' . $zipCode);
      return FALSE;
    }

    // We don't need a special method to check the range.
    if (is_numeric($range) && $range >= 0) {
      $this->range = $range;
    } else {
      trigger_error('Invalid range: ' . $range);
      return FALSE;
    }

    // Set up the zip codes.
    return $this->setZipCodesInRange();
  }

  /**
   * Get all of the zip codes from the database.
   * Currently this method is called on construction but
   * it doesn't have to be.
   *
   * @access public
   * @param  none
   * @return boolean
   */
  function setZipCodesInRange()
  {
    // First check that everything is set.
    if (!isset($this->zipCode) || !isset($this->range) || !isset($this->country)) {
      trigger_error('Cannot get zip codes. Class not initialized properly.');
      return FALSE;
    }

    // Get the max longitude and latitude of the starting point.
    $maxCoords = $this->getRangeBox();

    // The query.
    $query = 'SELECT City,State,County,' . $this->dbZip . ', ' . $this->dbLat . ', ';
    $query.=  $this->dbLon . ' ';
 	$query.=  $this->dbLon . ' ';
    $query.= 'FROM ' . $this->dbTable . ' ';
    $query.= ' WHERE ';
    $query.= '  (' . $this->dbLat . ' <= ' . $maxCoords['max_lat'] . ' ';
    $query.= '    AND ';
    $query.= '   ' . $this->dbLat . ' >= ' . $maxCoords['min_lat'] . ') ';
    $query.= '  AND ';
    $query.= '  (' . $this->dbLon . ' <= ' . $maxCoords['max_lon'] . ' ';
    $query.= '    AND ';
    $query.= '   ' . $this->dbLon . ' >= ' . $maxCoords['min_lon'] . ') ';

    // GAURAV JASSAL:
    // Query the database.
    //$db =& DB::connect(DSN);
    //$db->setFetchMode(DB_FETCHMODE_ASSOC);
    //$result = $db->query($query);
    global $objDatabase;
    $varResult = $objDatabase->Query($query);
	
	//echo $query;
	
    // Check for errors.
    //if (DB::isError($result)) {
    //  trigger_error('Database error: ' . $result->getMessage . ' ' . $query, E_USER_ERROR);
    //}

    // Process each row.

    for ($i=0; $i < $objDatabase->RowsNumber($varResult); $i++)
    {
        //while ($row = $result->fetchRow()) {

        // Get the distance form the origin (imperfect see below).
        // $distance = $this->calculateDistance($row[$this->dbLat], $row[$this->dbLon]);
        $distance = $this->calculateDistance($objDatabase->Result($varResult, $i, $this->dbLat), $objDatabase->Result($varResult, $i, $this->dbLon), $this->xLon, $this->xLat);
        // Double check that the distance is within the range.

        // Add the zip to the array
        if ($distance < $this->range){
			$this->zipCodes[$i]['ZIPCODE'] = $objDatabase->Result($varResult, $i, $this->dbZip);
			$this->zipCodes[$i]['DISTANCE'] = $distance;
			$this->zipCodes[$i]['CITY'] =$objDatabase->Result($varResult, $i, 'City');
			$this->zipCodes[$i]['STATE'] =$objDatabase->Result($varResult, $i, 'State');
			$this->zipCodes[$i]['COUNTY'] =$objDatabase->Result($varResult, $i, 'County');
            //$this->zipCodes[$objDatabase->Result($varResult, $i, $this->dbZip)] = $distance; // $this->zipCodes[$row[$this->dbZip]] = $distance;
		}
    }

    return TRUE;
  }

  /**
   * Return the array of results.
   *
   * @access public
   * @param  none
   * @return &array
   */
  function &getZipCodesInRange() {
    return $this->zipCodes;
  }

  /**
   * Calculate the distance from the coordinates are from the
   * origin zip code.
   *
   * The method is quite imperfect.  It assumes as flat Earth.
   * The values are quite accurate (depending on the conversion
   * factor used) for zip codes close  to the equator. I found
   * some crazy formula for calulating distance on a sphere
   * but I am not good enough at calculus to convert that into
   * working code.
   *
   * @access public
   * @param  float $lat       The latitude you want to know the distance to.
   * @param  float $lon       The longitude you want to know the distance to.
   * @param  float $zip       The zip code you want to know the distance from.
   * @param  int   $percision The number of decimals places in the distance.
   * @return float            The distance from the zip code to the coordinates.
   */
  function calculateDistance($lat, $lon, $starting_lon, $starting_lat, $zip = NULL, $percision = 2) {

    // Check the zip first.
    if (!isset ($zip)) {
      // Make it default to the origin zip code.
      // Could be used to calculate distances from other points.
      $zip = $this->zipCode;
    }
    // Get the coordinates of our starting zip code.

    // NAVEED:
    // list ($starting_lon, $starting_lat) = $this->getLonLat($zip);
    // Get Longitude and Latitude
    /*global $objDatabase;
    $query = 'SELECT ' . $this->dbLon . ', ' . $this->dbLat . ' ';
    $query.= 'FROM ' . $this->dbTable . ' ';
    $query.= 'WHERE ' . $this->dbZip . ' = \'' . addslashes($zip) . '\' ';
    $varResult = $objDatabase->Query($query);
    if ($objDatabase->RowsNumber($varResult) > 0)
    {
        $starting_lon = $objDatabase->Result($varResult, 0, "Longitude");
        $starting_lat = $objDatabase->Result($varResult, 0, "Latitude");
    }*/
    $starting_lon = $this->xLon;
    $starting_lat = $this->xLat;

    // Get the difference in miles for both coordinates.
    $diffLonMiles = ($starting_lon - $lon) / $this->milesToDegrees;
    $diffLatMiles = ($starting_lat - $lat) / $this->milesToDegrees;

    // Calculate the distance between two points.
    $distance = sqrt(($diffLonMiles * $diffLonMiles) + ($diffLatMiles * $diffLatMiles));

    // Return the distance rounded to the defined percision.
    return round($distance, $percision);
  }

  /** OBSOLETE BY NAVEED
   * Get the longitude and latitude for a single zip code.
   *
   * @access public
   * @param  string $zip  The zipcode to get the coordinates for.
   * @return array  Numerically index with longitude first.
   */
  function getLonLat($zip)
  {
    /*
    // Get the longitude and latitude for the zip code.
    $query = 'SELECT ' . $this->dbLon . ', ' . $this->dbLat . ' ';
    $query.= 'FROM ' . $this->dbTable . ' ';
    $query.= 'WHERE ' . $this->dbZip . ' = \'' . addslashes($zip) . '\' ';

    $db =& DB::connect(DSN);
    return $db->getRow($query);
    */
  }

  /**
   * Check to see if the country is valid.
   *
   * Not implemented in any useful manner.
   *
   * @access public
   * @param  string  $country The country to check.
   * @return boolean
   */
  function validateCountry($country) {

    return (strlen($country) == 2);
  }

  /**
   * Check to see if a zip code is valid.
   *
   * Not implemented in any useful manner.
   *
   * @access public
   * @param  string $zip     The code to validate.
   * @param  string $country The country the zip code is in.
   * @return boolean
   */
  function validateZipCode($zip, $country = NULL) {

    // Set the country if we need to.
    if (!isset($country)) {
      $country = $this->country;
    }

    // There should be a way to check the zip code for every
    // acceptabe country.
    return TRUE;
  }

  /**
   * Get the maximum and minimum longitude and latitude values
   * that our zip codes can be in.
   *
   * Not all zipcodes in this box will be with in the range.
   * The closest edge of this box is exactly range miles away
   * from the origin but the corners are sqrt(2(range^2)) miles
   * away. That is why we have to double check the ranges.
   *
   * @access public
   * @param  none
   * @return &array The edges of the box.
   */
  function &getRangeBox() {

    // Calculate the degree range using the mile range
    $degrees = $this->range * $this->milesToDegrees;

    // Get the coords for our starting zip code.
    global $objDatabase;
    $query = 'SELECT ' . $this->dbLon . ', ' . $this->dbLat . ' ';
    $query.= 'FROM ' . $this->dbTable . ' ';
    $query.= 'WHERE ' . $this->dbZip . ' = \'' . addslashes($this->zipCode) . '\' ';
    $varResult = $objDatabase->Query($query);
    if ($objDatabase->RowsNumber($varResult) > 0)
    {
        $starting_lon = $objDatabase->Result($varResult, 0, "Longitude");
        $starting_lat = $objDatabase->Result($varResult, 0, "Latitude");
    }
    else
    {
        $starting_lon = 0;
        $starting_lat = 0;
    }

    $this->xLon = $starting_lon;
    $this->xLat = $starting_lat;

    //list($starting_lon, $starting_lat) = $this->getLonLat($this->zipCode);

    // Set up an array to return.
    $ret_array = array ();

    // Lat/Lon ranges
    $ret_array['max_lat'] = $starting_lat + $degrees;
    $ret_array['max_lon'] = $starting_lon + $degrees;
    $ret_array['min_lat'] = $starting_lat - $degrees;
    $ret_array['min_lon'] = $starting_lon - $degrees;

    return $ret_array;
  }

  /**
   * Allow users to set the name of the database table holding
   * the information.
   *
   * @access public
   * @param  string $table The name of the db table.
   * @return void
   */
  function setTableName($table) {
    $this->dbTable = $name;
  }

  /**
   * Allow users to set the name of the column holding the
   * latitude value.
   *
   * @access public
   * @param  string $lat The name of the column.
   * @return void
   */
  function setLatColumn($lat) {
    $this->dbLat = $lat;
  }

  /**
   * Allow users to set the name of the column holding the
   * longitude value.
   *
   * @access public
   * @param  string $lon The name of the column.
   * @return void
   */
  function setLonColumn($lon) {
    $this->dbLon = $lon;
  }

  /**
   * Allow users to set the name of the column holding the
   * zip code value.
   *
   * @access public
   * @param  string $zips The name of the column.
   * @return void
   */
  function setZipColumn($zip) {
    $this->dbZip = $zip;
  }

  /**
   * Set a new origin and re-get the data.
   *
   * @access public
   * @param  string $zip The new origin.
   * @return void
   */
  function setNewOrigin($zip) {
    
    if ($this->validateZipCode($zip)) {
      $this->zipCode = $zip;
      $this->setZipCodesInRange();
    }
  }
  
  /**
   * Set a new range and re-get the data.
   *
   * @access public
   * @param  float  $range The new range.
   * @return void
   */
  function setNewRange($range) {
    
    if (is_numeric($range)) {
      $this->range = $range;
      $this->setZipCodesInRange();
    }
  }

  /**
   * Set a new country but don't re-get the data.
   * 
   * It isn't any good to check a zip code in two 
   * countries cause the rules for zip codes vary from
   * country to country.
   *
   * @access public
   * @param  string $country The new country.
   * @return void
   */
  function setNewCountry($coutry) {

    if ($this->validateCountry($country)) {
      $this->country = $country;
    }
  }

  /**
   * Allow users to set the converstion ratio.
   * Hopefully you are changing the percision
   * and not setting a bad value.
   *
   * @access public
   * @param  float  $rate The new value.
   * @return void
   */
  function setConversionRate($rate) {
    
    if (is_numeric($rate)) {
      $this->milesToDegrees = $rate;
    }
  }
}
/* Debugging lines
$zcr = new ZipCodesRange(10965, 10);
print_r($zcr);
*/
?>
