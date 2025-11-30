<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('dbConnector.php');
/**
 * Generates the HTML card for a single resource.
 *
 * @param array $resource The resource data array.
 * @return string The HTML markup.
 */
function generateResourceCard(array $resource): string {
    $name = htmlspecialchars($resource['name'] ?? 'Unknown Resource');
    $type = htmlspecialchars($resource['type_name'] ?? 'General');
    $capacity = htmlspecialchars($resource['capacity'] ?? 'N/A');
    $resource_id = htmlspecialchars($resource['resource_id'] ?? '0');
    $latitude = htmlspecialchars($resource['latitude'] ?? '0.0');
    $longitude = htmlspecialchars($resource['longitude'] ?? '0.0');

    return "
        <div data-id=\"$resource_id\" 
             data-lat=\"$latitude\" 
             data-lon=\"$longitude\" 
             class=\"resource-card p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-ashesi-light transition duration-150\"
             onclick=\"zoomToResource(this)\">
            <p class=\"font-semibold text-gray-800\">$name</p>
            <p class=\"text-sm text-gray-500\">$type | Capacity: $capacity</p>
        </div>
    ";
}
//get search term from query parameter
$searchTerm = $_GET['search'] ?? '';
//connect to database
$db = connectDB();
$resources = [];
if ($db) {
    //fetch filtered resources
    $resources = getFilteredResources($db, $searchTerm);
    $db->close();
}
//prepare the HTML output
$htmlOutput = '';
$resourcesData = [];
if (count($resources) > 0) {
    foreach ($resources as $resource) {
        $htmlOutput .= generateResourceCard($resource);
        
        //only include resources with valid coordinates for the map
        $lat = floatval($resource['latitude'] ?? 0);
        $lon = floatval($resource['longitude'] ?? 0);
        
        if ($lat != 0 && $lon != 0) {
            $resourcesData[] = [
                'id' => $resource['resource_id'],
                'name' => $resource['name'],
                'lat' => $lat,
                'lon' => $lon
            ];
        }
    }
} else {
    $htmlOutput = '<div class="text-sm text-center text-gray-500 p-4 border border-dashed rounded-lg">No resources matched your search query.</div>';
}
//return JSON response with both HTML and resource data
header('Content-Type: application/json');
echo json_encode([
    'html' => $htmlOutput,
    'resources' => $resourcesData
]);
?>
