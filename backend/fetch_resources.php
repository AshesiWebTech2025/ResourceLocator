<?php
require_once('dbConnector.php');
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
$searchTerm = $_GET['search'] ?? '';
$db = connectDB();
$resources = [];
if ($db) {
    //fetch filtered resources
    $resources = getFilteredResources($db, $searchTerm);
    $db->close();
}

$output = '';

if (count($resources) > 0) {
    foreach ($resources as $resource) {
        $output .= generateResourceCard($resource);
    }
} else {
    $output = '<div class="text-sm text-center text-gray-500 p-4 border border-dashed rounded-lg">No resources matched your search query.</div>';
}

//reurn generated html
echo $output;
?>