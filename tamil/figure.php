<?php
							$m = new MongoClient();
							$db = $m -> map;
							$collection = $db -> reports;
							$cursor = $collection -> find(
								array(
									"constituency" => "Anna Nagar"
								)
							) -> sort (
								array(
									"votes" => (-1)
								)
							) -> limit(5);
							foreach ($cursor as $doc) {
								if ($doc["status"] == "open") {
									echo "
										<div class='trending-open-complaint' id='trending-complaint-block-" . $doc["_id"] . "' >\n" . 
									 		$doc["title"] . "\n". 
											"<i id='trending-plus-". $doc["_id"] . "' 
												onClick=openTrendingComplaint('" . $doc["_id"] . "') 
												class='fa fa-plus' style='color: #337AB7;' title='See More'>
											</i><br>\n" .
											"<div class='trending-complaint-info' id='trending-complaint-info-". $doc["_id"] . 
												"'>\n<br>".		
											 	$doc["description"] . "<br><br>\n<strong>Location:&nbsp;</strong>" . 
											 	$doc["location"] . "<br>\n<strong>Tagged at:&nbsp;</strong>" . 
											 	$doc["taggedAt"] . "<br>\n" . 
											    "<strong>Posted on:</strong>&nbsp;" . 
											    date('d-M-Y, H:i', $doc["time"] -> sec ) .
											    "<br>\n" .
											"</div>\n" .
										"</div>" .
										"<hr>" .
										"<br>\n";
								}	
							}				
						?>						