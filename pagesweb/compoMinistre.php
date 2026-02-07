<!-- Start Why choose -->
		<section class="why-choose section" >
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-12">
						<!-- Start Choose Left -->
						<div class="choose-left">
							<h3>Mot d'engagement du gouvernement congolais</h3>
							<p>Après 25 ans d’adoption et mise en oeuvre de la Résolution 1325 du Conseil de Sécurité des Nations Unies sur l’Agenda Femmes, la Paix et la Sécurité en octobre 2000,la République Démocratique du Congo notre pays a enregistré des progrès significatifs ence qui concerne l’intégration du genre dans lesdifférents processus et dialogues de paix ainsi que dans les initiatives de la reconstruction post-conflits. Nonobstant, les efforts à fournir sont encore immenses pour garantir la pleine participation des femmes et des filles dans les mécanismes de paix et de sécurité à l’échelle nationale, régionale et internationale.</p>
							<p>Mme Micheline OMBAE Ministre du Genre, Famille et Enfant </p>
							<div class="row">
								<div class="col-lg-6">
									
								</div>
								<div class="col-lg-6">
									
								</div>
							</div>
						</div>
						<!-- End Choose Left -->
					</div>
					<div class="col-lg-6 col-12">
						<!-- Start Choose Rights -->
						<div class="choose-right">
							<div class="video-image">
								<!-- Video Animation -->
								<div class="promo-video">
									<div class="waves-block">
										<div class="waves wave-1"></div>
										<div class="waves wave-2"></div>
										<div class="waves wave-3"></div>
									</div>
								</div>
								<!--/ End Video Animation -->
								<?php
								// Récupère l'ID de la dernière vidéo publiée pour un handle YouTube.
								function fetch_url($url){
									if (function_exists('curl_version')){
										$ch = curl_init($url);
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
										curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible)');
										$r = curl_exec($ch);
										curl_close($ch);
										return $r;
									}
									return @file_get_contents($url);
								}

								function get_latest_youtube_video_id_from_handle($handle){
									$handle = ltrim($handle, '@');
									$url = 'https://www.youtube.com/@'. $handle;
									$html = fetch_url($url);
									if (!$html) return null;

									// Try to find canonical channel URL with channel ID
									if (preg_match('/"channelId":"(UC[^"]+)"/i', $html, $m)){
										$channelId = $m[1];
										$feed = 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $channelId;
										$xml = @fetch_url($feed);
										if ($xml && ($doc = @simplexml_load_string($xml))){
											$entry = $doc->entry[0];
											if (!empty($entry->link) && isset($entry->link['href'])){
												$href = (string)$entry->link['href'];
												if (preg_match('/v=([A-Za-z0-9_-]+)/', $href, $mm)) return $mm[1];
												if (preg_match('/watch\/([A-Za-z0-9_-]+)/', $href, $mm2)) return $mm2[1];
											}
										}
									}

									// Fallback: parse first occurrence of /watch?v= in HTML
									if (preg_match('/\/watch\?v=([A-Za-z0-9_-]{8,})/i', $html, $mm)){
										return $mm[1];
									}
									// Another fallback: look for first /shorts/ id
									if (preg_match('/\/shorts\/([A-Za-z0-9_-]{8,})/i', $html, $mm2)){
										return $mm2[1];
									}
									return null;
								}

								$latestId = get_latest_youtube_video_id_from_handle('@Resolution1325RDC');
								if ($latestId){
									$embed = 'https://www.youtube.com/embed/' . htmlspecialchars($latestId) . '?rel=0&modestbranding=1';
								} else {
									// fallback: generic playlist/embed used previously
									$embed = 'https://www.youtube.com/embed/videoseries?si=MSZPE_M7NLh3PlOZ&amp;list=PLhlyAOe_x2Y3VSJiTJuoXpNIO6o9UVFfu';
								}
								?>

								<div class="video-embed-wrapper">
									<iframe src="<?= $embed ?>" title="Vidéo SN1325" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
								</div>
							</div>
						</div>
						<!-- End Choose Rights -->
					</div>
				</div>
			</div>
		</section>
		<!--/ End Why choose -->