<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}

// Open the XML
$handle = fopen($_SESSION["username"]."/".$_SESSION["sessionDate"]."/simulation-outputs2.xml", 'r');
// Get the nodestring incrementally from the xml file by defining a callback
// In this case using a anon function.

    print_r(getArrayFromXMLString($handle));
fclose($handle);


function nodeStringFromXMLFile($handle, $startNode, $endNode, $callback=null) {
    $cursorPos = 0;
    while(true) {
        // Find start position
         $startPos = getPos($handle, $startNode, $cursorPos);
		 print_r(startPos);

        // Find where the node ends
        $endPos = getPos($handle, $endNode, $startPos) + mb_strlen($endNode);
        // Jump back to the start position
        fseek($handle, $startPos);
        // Read the data
        $data = fread($handle, ($endPos-$startPos));
        // pass the $data into the callback
        $callback($data);
        // next iteration starts reading from here
        $cursorPos = ftell($handle);
    }
}

/**
 * This function will return the first string it could find in a resource that matches the $string.
 *
 * By using a $startFrom it recurses and seeks $chunk bytes at a time to avoid reading the
 * whole file at once.
 *
 * @param resource $handle - typically a file handle
 * @param string $string - what string to search for
 * @param int $startFrom - strpos to start searching from
 * @param int $chunk - chunk to read before rereading again
 * @return int|bool - Will return false if there are EOL or errors
 */
function getPos($handle, $string, $startFrom=0, $chunk=1024, $prev='') {
    // Set the file cursor on the startFrom position
    fseek($handle, $startFrom, SEEK_SET);
    // Read data
    $data = fread($handle, $chunk);
    // Try to find the search $string in this chunk
    $stringPos = mb_strpos($prev.$data, $string);
    // We found the string, return the position
    if($stringPos !== false ) {
        return $stringPos+$startFrom - mb_strlen($prev);
    }
    // We reached the end of the file
    if(feof($handle)) {
        return false;
    }
    // Recurse to read more data until we find the search $string it or run out of disk
    return getPos($handle, $string, $chunk+$startFrom, $chunk, $data);
}

/**
 * Turn a string version of XML and turn it into an array by using the
 * SimpleXML
 *
 * @param string $nodeAsString - a string representation of a XML node
 * @return array
 */
function getArrayFromXMLString($nodeAsString) {
    $simpleXML = simplexml_load_string($nodeAsString);
    if(libxml_get_errors()) {
        user_error('Libxml throws some errors.', implode(',', libxml_get_errors()));
    }
    return simplexml2array($simpleXML);
}

/**
 * Turns a SimpleXMLElement into an array
 *
 * @param SimpleXMLelem $xml
 * @return array
 */
function simplexml2array($xml) {
    if(is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
        $attributes = $xml->attributes();
        foreach($attributes as $k=>$v) {
            $a[$k] = (string) $v;
        }
        $x = $xml;
        $xml = get_object_vars($xml);
    }

    if(is_array($xml)) {
        if(count($xml) == 0) {
            return (string) $x;
        }
        $r = array();
        foreach($xml as $key=>$value) {
            $r[$key] = simplexml2array($value);
        }
        // Ignore attributes
        if (isset($a)) {
            $r['@attributes'] = $a;
        }
        return $r;
    }
    (string) $xml;
}

?>
