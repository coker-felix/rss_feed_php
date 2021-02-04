//PODCAST RSS FEED READER
$feed = "YOUR RSS FEED LINK";

if (substr($feed, -4) == '.rss') {
  $result = simplexml_load_file($feed);

} else {
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $feed,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_USERAGENT => $useragent,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Accept: application/xml'
    ),
  ));
  $response = curl_exec($curl);
  try {
    $result = $response;
    $result = new SimpleXmlElement($result);
  } catch (Exception $e) {
    $data['status'] = 400;

  }
			
curl_close($curl);

		
$image = $result->channel->image->url;
$title = $result->channel->title;
$image_src = $result->channel->link;



  $items = $result->xpath("//item");

    foreach ($items as $item) 
  {

      $itunesSpace = $item->getNameSpaces(true);
      $nodes = $item->children($itunesSpace['itunes']);

      $new_data[] = [
        'title' => $item->title,
        'description' => (string) $item->description,
        'enclosure' => $item->enclosure['url'],
        'type' => $item->enclosure['type'],
        'guid' => $item->guid,

        'duration' => $nodes->duration,
        'keywords' => $nodes->keywords
        //you can add other tags
      ];

  }

usort($new_data, function ($feed1, $feed2) {
      return strtotime($feed2->pubDate) - strtotime($feed1->pubDate);
  });
