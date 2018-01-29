<?php
	class WpFastestCacheRenderBlocking{
		private $html = "";
		private $except = "";
		private $tags = array();
		private $header_start_index = 0;
		private $js_tags_text = "";

		public function __construct($html){
			$this->html = $html;
			$this->set_header_start_index();
			$this->set_tags();
			$this->tags = $this->tags_reorder($this->tags);
		}

		public function set_header_start_index(){
			$head_tag = $this->find_tags("<head", ">");
			$this->header_start_index = isset($head_tag[0]) && isset($head_tag[0]["start"]) && $head_tag[0]["start"] ? $head_tag[0]["start"] : 0;
		}
		public function tags_reorder($tags){
			// <script>jQuery('head').append('<style>' + arr_splits[i] + '</style>');</script>
			// <script>document.getElementById("id").innerHTML='<div> <span> <!--[if !IE]>--> xxx <!--<![endif]--> </span></div>';</script>
			$list = array();

			for ($i=0; $i < count($tags); $i++) {
				for ($j=0; $j < count($tags); $j++) { 
					if($tags[$i]["start"] > $tags[$j]["start"]){
						if($tags[$i]["end"] < $tags[$j]["end"]){
							array_push($list, $i);
						}
					}
				}
			}

			foreach ($list as $key => $value) {
				unset($tags[$value]);
			}




		    $sorter = array();
		    $ret = array();

		    foreach ($tags as $ii => $va) {
		        $sorter[$ii] = $va['start'];
		    }

		    asort($sorter);

		    foreach ($sorter as $ii => $va) {
		        $ret[$ii] = $tags[$ii];
		    }

		    $tags = $ret;

		    return $tags;
		}

		public function set_except($tags){
			foreach ($tags as $key => $value) {
				$this->except = $value["text"].$this->except;
			}
		}

		public function set_tags(){
			$this->set_comments();
			$this->set_js();
			$this->set_css();
		}

		public function set_css(){
			$style_tags = $this->find_tags("<style", "</style>");

			foreach ($style_tags as $key => $value) {
				// <script>var xxx ={"id":"4", "html":"<style>\n\t\t\t.container{color:#CCCCCC;}\n\t\t<\/style>"};</script>
				if(!preg_match("/<\/script>/i", $value["text"])){
					array_push($this->tags, $value);
				}
			}

			
			
			$link_tags = $this->find_tags("<link", ">");

			foreach ($link_tags as $key => $value) {
				if(preg_match("/href\s*\=/i", $value["text"])){
					if(preg_match("/rel\s*\=\s*[\'\"]\s*stylesheet\s*[\'\"]/i", $value["text"])){
						array_push($this->tags, $value);
					}
				}
			}
		}

		public function set_js(){
			$script_tag = $this->find_tags("<script", "</script>");

			foreach ($script_tag as $key => $value) {
				if(preg_match("/google_ad_client/", $value["text"])){
					continue;
				}

				if(preg_match("/googlesyndication\.com/", $value["text"])){
					continue;
				}

				// if(preg_match("/srv\.sayyac\.net/", $value["text"])){
				// 	continue;
				// }

				if(preg_match("/app\.getresponse\.com/i", $value["text"])){
					continue;
				}

				if(preg_match("/adsbygoogle/i", $value["text"])){
					continue;
				}

				//<script type='text/javascript' src='http://partner.googleadservices.com/gampad/google_service.js'></script>
				if(preg_match("/partner\.googleadservices\.com\/gampad\/google_service\.js/i", $value["text"])){
					continue;
				}

				// <script type='text/javascript'>
				// GS_googleAddAdSenseService("ca-pub-1059380037");
				// GS_googleEnableAllServices();
				// </script>
				if(preg_match("/<script[^\>]*>\s*GS_googleAddAdSenseService\([\"\'][^\"\']+[\"\']\)\;\s*GS_googleEnableAllServices\(\)\;\s*<\/script>/i", $value["text"])){
					continue;
				}

				// <script type='text/javascript'>
				// GA_googleAddSlot("ca-pub-1059380037", "viajablog-300-250");
				// </script>
				if(preg_match("/<script[^\>]*>\s*GA_googleAddSlot\([^\)]+\)\;\s*<\/script>/i", $value["text"])){
					continue;
				}

				// <script type='text/javascript'>
				// GA_googleFetchAds();
				// </script>
				if(preg_match("/<script[^\>]*>\s*GA_googleFetchAds\(\)\;\s*<\/script>/i", $value["text"])){
					continue;
				}

				if(preg_match("/GoogleAnalyticsObject|\_gaq\.push\(\[\'\_setAccount/i", $value["text"])){
					continue;
				}

				if(preg_match("/smarticon\.geotrust\.com\/si\.js/i", $value["text"])){
					continue;
				}

				if(preg_match("/veedi\.com\/player\/embed\/veediEmbed\.js/i", $value["text"])){
					continue;
				}

				if(preg_match("/cdn\.ampproject\.org/i", $value["text"])){
					continue;
				}

				if(preg_match("/data-wpfc-render\=[\"\']false[\"\']/i", $value["text"])){
					continue;
				}

				if(preg_match("/adserver\.adtechjp\.com/i", $value["text"])){
					continue;
				}

				if(preg_match("/ib\.3lift\.com/i", $value["text"])){
					continue;
				}

				if(preg_match("/adtradradservices\.com/i", $value["text"])){
					continue;
				}

				if(preg_match("/static.clickpapa.com\/c\.js/i", $value["text"])){
					continue;
				}

				if(preg_match("/clickpapa_ad_client/i", $value["text"])){
					continue;
				}

				if(preg_match("/cts\.tradepub\.com/i", $value["text"])){
					continue;
				}

				if(preg_match("/_areklam_target|ad\.arklm\.com/i", $value["text"])){
					continue;
				}

				if(preg_match("/admatic\.com\.tr/i", $value["text"])){
					continue;
				}

				if(preg_match("/ca\.cubecdn\.net/i", $value["text"])){
					continue;
				}

				if(preg_match("/amazon-adsystem\.com\/widgets\/onejs/i", $value["text"])){
					//<script src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US"></script>
					continue;
				}

				if(preg_match("/amzn_assoc_placement/i", $value["text"])){
					// <script>amzn_assoc_placement="adunit0";
					// amzn_assoc_search_bar="false";
					// amzn_assoc_tracking_id="3d0f1f-20";
					// amzn_assoc_ad_mode="search";
					// amzn_assoc_ad_type="smart";
					// amzn_assoc_marketplace="amazon";
					// amzn_assoc_region="US";
					// amzn_assoc_title="";
					// amzn_assoc_default_search_phrase="Spray Paint ";
					// amzn_assoc_default_category="All";
					// amzn_assoc_linkid="949bfb847147d654e679d4876a8e2b77";</script>
					continue;
				}
				
				//<script type="text/javascript">document.write("<div data-role=\"amazonjs\" data-asin=\"4334035787\" data-locale=\"JP\" data-tmpl=\"\" data-img-size=\"\" class=\"asin_4334035787_JP_ amazonjs_item\"><div class=\"amazonjs_indicator\"><span class=\"amazonjs_indicator_img\"></span><a class=\"amazonjs_indicator_title\" href=\"https://www.amazon.co.jp/%E5%B8%8%E5%AF%BF/dp/4334035787?SubscriptionId=AKIAIQGSXT2U7QVCQGHA&tag=hiyokoweb06-22&linkCode=xm2&camp=2025&creative=165953&creativeASIN=4335787\">希望難民ご一行様　ピースボートと「承認の共同体」幻想 (光文社新書)</a><span class=\"amazonjs_indicator_footer\"></span></div></div>")</script>
				if(preg_match("/^<script[^\>]*>\s*document.write\([\"\']\s*<div/i", $value["text"])){
					if(preg_match("/\s*<\/div>[\"\']\)\s*<\/script>$/i", $value["text"])){
						if(preg_match("/amazonjs/i", $value["text"])){
							continue;
						}
					}
				}

				if(preg_match("/reklamstore/i", $value["text"])){
					if(preg_match("/reklamstore_region_id/i", $value["text"])){
						continue;
					}else if(preg_match("/reklamstore\.com\/reklamstore\.js/i", $value["text"])){
						continue;
					}
				}

				//<script>document.write ('<iframe id="g2324_1" src="http://site.com/index.php?display_gallery_iframe&amp;gal_id=2324_1&amp;gal_type=2&amp;gal_cap=OFF&amp;gal_page=false"></iframe>');</script>
				if(preg_match("/document\.write\s*\(/i", $value["text"])){
					if(preg_match("/<iframe/i", $value["text"])){
						continue;
					}
				}

				//Yandex.Metrika counter
				if(preg_match("/mc\.yandex\.ru\/metrika\/watch\.js/i", $value["text"])){
					if(preg_match("/yandex_metrika_callbacks/i", $value["text"])){
						continue;
					}
				}

				//<script type="text/javascript" src="https://seal.thawte.com/getthawteseal?host_name=www.site.co.za&amp;size=S&amp;lang=en"></script>
				if(preg_match("/seal\.thawte\.com/i", $value["text"])){
					continue;
				}

				//cdn.playwire.com/bolt/js/zeus/embed.js
				if(preg_match("/cdn\.playwire\.com\/bolt\/js\/zeus\/embed\.js/i", $value["text"])){
					continue;
				}
				
				//<script type= "text/javascript">var RecaptchaOptions = {custom_translations : { instructions_visual : "This is my text:" }};</script>
				if(preg_match("/var\s+RecaptchaOptions\s*=\s*\{/i", $value["text"])){
					continue;
				}

				//<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LfsMBcUAAATtSyDIUMNZa7E&amp;hl=en"></script>
				if(preg_match("/google\.com\/recaptcha\/api\/challenge/i", $value["text"])){
					continue;
				}

				// <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				// new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				// j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				// 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				// })(window,document,'script','dataLayer','GTM-5NRFSPW');</script>
				if(preg_match("/googletagmanager\.com\/gtm\.js/i", $value["text"]) && preg_match("/parentNode\.insertBefore/i", $value["text"])){
					continue;
				}

				//<script src="https://gist.github.com/name/0af5cfb23055f8d45f25328befd4d024.js"></script>
				if(preg_match("/gist\.github\.com\//i", $value["text"])){
					continue;
				}

				//<script async="asnyc" type="text/javascript" src="https://a-ssl.ligatus.com/?ids=91770&t=js&s=1"></script>
				if(preg_match("/a-ssl\.ligatus\.com/i", $value["text"])){
					continue;
				}

				//<script type="text/javascript" src="https://sealserver.trustwave.com/seal.js?style=invert&code=1fb5e"></script>
				if(preg_match("/sealserver\.trustwave\.com\/seal\.js/i", $value["text"])){
					continue;
				}

				//<script language="javascript" src="//inviocare.us13.list-manage.com/generate-js/?u=f3707cdf398370b05&fid=4301&show=10" type="text/javascript"></script>
				if(preg_match("/inviocare\.us13\.list\-manage\.com\/generate\-js/i", $value["text"])){
					continue;
				}

				//<script type="text/javascript" src="https://form.jotform.co/jsform/60138856"></script>
				if(preg_match("/jotform[^\/]+\/jsform\/\d+/i", $value["text"])){
					continue;
				}

				//<script type="application/json" class="wp-playlist-script">
				if(preg_match("/<script[^\>]+application\/json[^\>]+/i", $value["text"])){
					if(preg_match("/<script[^\>]+wp-playlist-script[^\>]+/i", $value["text"])){
						continue;
					}
				}

				// <script type='text/javascript' data-cfasync='false' src='//cdn.broadstreetads.com/init-2.min.js?ver=0b3c2484f1bad29996c60bc16fa8767f'></script>
				// <script data-cfasync="false">broadstreet.zone(46909, {responsive: true});</script>
				if(preg_match("/broadstreetads\.com\/init-2\.min\.js/i", $value["text"]) || preg_match("/broadstreet\.zone\(/i", $value["text"])){
					continue;
				}


				$this->js_tags_text = $this->js_tags_text.$value["text"];

				array_push($this->tags, $value);
			}
		}

		public function set_comments(){
			$comment_tags = $this->find_tags("<!--", "-->");

			$this->set_except($comment_tags);

			foreach ($comment_tags as $key => $value) {
				if(preg_match("/\<\!--\s*\[if/i", $value["text"])){
					array_push($this->tags, $value);
				}
			}
		}

		public function find_tags($start_string, $end_string, $html = false){
			$data = $html ? $html : $this->html;

			$list = array();
			$start_index = false;
			$end_index = false;

			for($i = 0; $i < strlen( $data ); $i++) {
			    if(substr($data, $i, strlen($start_string)) == $start_string){
			    	if(!$start_index && !$end_index){
			    		$start_index = $i;
			    	}
				}

				if($start_index && $i > $start_index){
					if(substr($data, $i, strlen($end_string)) == $end_string){
						$end_index = $i + strlen($end_string)-1;
						$text = substr($data, $start_index, ($end_index-$start_index + 1));
						
						if($html === false){
							if($start_index > $this->header_start_index){
								if($this->except){
									if(strpos($this->except, $text) === false){
										array_push($list, array("start" => $start_index, "end" => $end_index, "text" => $text));
									}
								}else{
									array_push($list, array("start" => $start_index, "end" => $end_index, "text" => $text));
								}
							}
						}else{
							array_push($list, array("start" => $start_index, "end" => $end_index, "text" => $text));
						}

						$start_index = false;
						$end_index = false;
					}
				}
			}

			return $list;
		}

		public function action($render_blocking_css = false, $make_defer = false){
			$wpemojiSettings = "";
			$google_fonts = "";
			$bootstrapcdn = "";
			$inline_js = "";
			$document_ready_js = "";
			$third_part_js = "";

			//to remove tags
			$this->tags = array_reverse($this->tags);
			foreach ($this->tags as $key => &$value) {
				if(preg_match("/\<\!--\s*\[if[^\>]+>/i", $value["text"])){
					if($arr = $this->split_html_condition($value["text"])){
						$style = "";
						$script = "";

						foreach ($arr as $arr_key => $arr_value) {
							if(preg_match("/\<\!--\s*\[if[^\>]+>(<link|<style)/i", $arr_value["text"])){
								$style = $style."\n".$arr_value["text"];
							}else if(preg_match("/\<\!--\s*\[if[^\>]+><script/i", $arr_value["text"])){
								$script = $script."\n".$arr_value["text"];
							}
						}
					}

					$value["text"] = $script;
					$this->html = substr_replace($this->html, $style, $value["start"], ($value["end"] - $value["start"] + 1));
				}else if(preg_match("/^<script/i", $value["text"])){
					$this->html = substr_replace($this->html, "", $value["start"], ($value["end"] - $value["start"] + 1));
				}else if(preg_match("/^<link[^\>]+(fonts|ajax)\.googleapis\.com[^\>]+>/", $value["text"])){
					if(isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheGoogleFonts) && $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheGoogleFonts){
						$this->html = substr_replace($this->html, "", $value["start"], ($value["end"] - $value["start"] + 1));

						$google_fonts = $value["text"]."\n".$google_fonts;
					}
				}else if(preg_match("/^<link[^\>]+(maxcdn)\.bootstrapcdn\.com[^\>]+>/", $value["text"])){
					$this->html = substr_replace($this->html, "", $value["start"], ($value["end"] - $value["start"] + 1));

					$bootstrapcdn = $value["text"]."\n".$bootstrapcdn;
				}
			}

			foreach ($this->tags as $key => &$value) {
				if($value["text"] && preg_match("/^<script/i", $value["text"])){
					if(preg_match("/gravatar\.com\/js\/gprofiles\.js/i", $value["text"]) || 
						preg_match("/s0\.wp\.com\/wp-content\/js\/devicepx-jetpack\.js/i", $value["text"])){
						//<script type='text/javascript' src='http://s.gravatar.com/js/gprofiles.js?ver=2017Janaa'></script>
						//<script type='text/javascript' src='http://s0.wp.com/wp-content/js/devicepx-jetpack.js?ver=201701'></script>
						
						if(!preg_match("/\sdefer\s/i", $value["text"])){
							$value["text"] = preg_replace("/<script\s+/", "<script defer ", $value["text"]);
						}

						if(!preg_match("/\sasync\s/i", $value["text"])){
							$value["text"] = preg_replace("/<script\s+/", "<script async ", $value["text"]);
						}

						unset($this->tags[$key]);
						$third_part_js = $value["text"]."\n".$third_part_js;
					}else if(preg_match("/if\(window\.attachEvent\)\{window\.attachEvent\(\'onload\'\,wpfdr\_/i", $value["text"])){
						//to remove jQuery(document) which contains window.attachEvent
						unset($this->tags[$key]);
						$document_ready_js = $value["text"]."\n".$document_ready_js;
					}else if(preg_match("/^<script[^\>]*>\s*jQuery\(\s*document\s*\)\.ready\(/i", $value["text"])){
						// <script>
						// 	jQuery(document).ready(function($){
						// 	});
						// </script>

						if(preg_match("/jQuery\(\s*window\s*\)\.load\(/i", $value["text"])){
							//jQuery(window).load(function(){
							continue;
						}

						if(preg_match("/\\$\(\s*window\s*\)\.load\(/i", $value["text"])){
							//$(window).load(function(){
							continue;
						}
						
						if(preg_match("/\}\s*\)\s*\;\s*<\/script>$/i", $value["text"])){
							//$onload = "if(window.attachEvent){window.attachEvent('onload',wpfdr_".$key.");}else{if(window.onload){var wpfdrcl_".$key."=window.onload;var wpfdrnl_".$key."=function(evt){wpfdrcl_".$key."(evt);wpfdr_".$key."(evt);};window.onload=wpfdrnl_".$key.";}else{window.onload=wpfdr_".$key.";}}";
							$onload = "if(window.attachEvent){window.attachEvent('onload',wpfdr_".$key.");}else if(window.addEventListener){window.addEventListener('load', wpfdr_".$key.",false);}";

							$value["text"] = preg_replace("/(<script[^\>]*>)/i", "$1".$onload."\nfunction wpfdr_".$key."(){\n", $value["text"]);
							$value["text"] = preg_replace("/\s*(<\/script>)/i", "\n}"."$1", $value["text"]);

							unset($this->tags[$key]);
							$document_ready_js = $value["text"]."\n".$document_ready_js;
						}
			    	}else if(preg_match("/^<script[^\>]*>\s*window\.\_wpemojiSettings/", $value["text"])){
						//to remove window._wpemojiSettings from tags
			    		unset($this->tags[$key]);
			    		$wpemojiSettings = $wpemojiSettings."\n".$value["text"];
			    	}else if(!preg_match("/^<script[^\>]+src=[\'\"][^\>]+>/", $value["text"])){
			    		//to remove inline js which do not contain any function
			    		//<script>var _wpcf7={"loaderUrl":"sample"};</script>
			    		$tmp = $value["text"];
			    		$tmp = preg_replace("/\s*\/\*(.+)\*\/\s*/", "", $tmp);

			    		//var themifyScript causes "fixed header" issue on thepurplepumpkinblog.co.uk
			    		if(preg_match("/var\sthemifyScript/i", $tmp)){
			    			continue;
			    		}

			    		//<script data-cfasync="false" type="text/javascript">var lsjQuery = jQuery;</script>
			    		if(preg_match("/data-cfasync\=[\"\']false[\"\']/i", $tmp)){
			    			continue;
			    		}

	    				if(preg_match("/(function|jQuery|if)\s*\([^\)\(]+\)/i", $tmp)){
	    					continue;
	    				}

	    				//var block_td_uid_2_58aab3b5b4eb1=new tdBlock()
	    				if(preg_match("/var\s+[^\=\;\s\"\']+\s*\=\s*new\s+[^\(\)]+\(/i", $tmp)){
	    					continue;
	    				}

	    				//<script>var lsjQuery = jQuery;</script>
						if(preg_match("/var\s+[^\=\s]+\s*\=\s*jQuery\s*\;/i", $value["text"])){
							continue;
						}

	    				//<script>Abtf.css();</script>
	    				//<script>dtGlobals.logoEnabled=1;</script>
	    				//if var does not exist
	    				if(!preg_match("/var\s*[a-z0-9_]+\s*\=\s*[^\;]+\s*\;/i", $tmp)){
	    					continue;
	    				}

	    				//$=jQuery.noConflict()
	    				if(preg_match("/\=\s*jQuery\.noConflict\(/i", $tmp)){
	    					continue;
	    				}


				    	unset($this->tags[$key]);
				    	$inline_js = $value["text"]."\n".$inline_js;

				    }
				}
		    }

		    //to add Google Fonts at the end of page before js sources
			if($google_fonts){
				//$google_fonts = $this->combine_google_fonts($google_fonts);

				if(isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheGoogleFonts) && $GLOBALS["wp_fastest_cache_options"]->wpFastestCacheGoogleFonts){
					$google_fonts = $this->async_google_fonts($google_fonts);

					$this->html = str_replace("</body>", $google_fonts."\n"."</body>", $this->html);
				}
			}

			//to add BootstrapCDN at the end of page before js sources
			if($bootstrapcdn){
				$this->html = str_replace("</body>", $bootstrapcdn."\n"."</body>", $this->html);
			}

		    //to add Inline Js before at the end of page before js sources
			if($inline_js){
				$this->html = str_replace("</body>", $inline_js."\n"."</body>", $this->html);
			}

			//to add third_part_js at the end of page
			if($third_part_js){
				$this->html = str_replace("</body>", $third_part_js."\n"."</body>", $this->html);
			}

			//to add defer and async attribute
			if($make_defer || !isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheCombineJsPowerFul)){
				$this->tags = $this->add_defer_attr($this->tags);
			}

			//to add tags into footer
			$this->tags = array_reverse($this->tags);
			foreach ($this->tags as $key => $value) {
				if(preg_match("/^<script/i", $value["text"])){
					$this->html = str_replace("</body>", $value["text"]."\n"."</body>", $this->html);
				}else if(preg_match("/\<\!--\s*\[if[^\>]+>/i", $value["text"])){
					$this->html = str_replace("</body>", $value["text"]."\n"."</body>", $this->html);
				}
			}

			//to add document_ready_js at the end of page
			if($document_ready_js){
				$this->html = str_replace("</body>", $document_ready_js."\n"."</body>", $this->html);
			}

			//to add wpemojiSettings at the end of page
			if($wpemojiSettings){
				$this->html = str_replace("</body>", $wpemojiSettings."\n"."</body>", $this->html);
			}

			return preg_replace("/^\s+/m", "", $this->html);
		}

		public function async_google_fonts($fonts){
			if(preg_match("/\shref\=/", $fonts)){
				$fonts = preg_replace("/\shref\=/", " wpfc-href=", $fonts);
				//$onload = "<script>if(window.attachEvent){window.attachEvent('onload',wpfdr_google);}else{if(window.onload){var wpfdrcl_google=window.onload;var wpfdrnl_google=function(evt){wpfdrcl_google(evt);wpfdr_google(evt);};window.onload=wpfdrnl_google;}else{window.onload=wpfdr_google;}};function wpfdr_google(){jQuery(\"link[wpfc-href]\").each(function(i,e){jQuery(e).attr(\"href\", jQuery(e).attr(\"wpfc-href\"));});}</script>";

				$onload = "<script>if(window.attachEvent){window.attachEvent('onload',wpfdr_google);}else if(window.addEventListener){window.addEventListener('load', wpfdr_google,false);}function wpfdr_google(){jQuery(\"link[wpfc-href]\").each(function(i,e){jQuery(e).attr(\"href\", jQuery(e).attr(\"wpfc-href\"));});}</script>";

				return $fonts."\n".$onload;
			}

			return $fonts;
		}

		public function combine_google_fonts($fonts){
			$family = "";
			$subset = "";

			preg_match_all("/fonts\.googleapis\.com\/css\?family\=([^\'\"]+)/si", $fonts, $arr);

			if(isset($arr[0])){
				foreach ($arr[0] as $key => $value) {
					//to remove special chars
					$value = htmlspecialchars_decode($value);

					$parts = parse_url($value);
					parse_str($parts['query'], $query);

					$family = $family ? $family."|".$query["family"] : $query["family"];

					if(isset($query["subset"]) && $query["subset"]){
						$subset = $subset ? $subset.",".$query["subset"] : $query["subset"];
					}
				}

				$family = str_replace(" ", "+", $family);
				$family = $subset ? $family."&subset=".$subset : $family;

				return "<!--\n".$fonts."\n-->\n"."<link rel='stylesheet' id='wpfc-google-combined' href='http://fonts.googleapis.com/css?family=".$family."' type='text/css' media='all'/>";
			}else{
				return $fonts;
			}
		}

		public function add_defer_attr($tags){
			$external_start = false;

			foreach ($tags as $key => &$value){

				if(preg_match("/^<script/i", $value["text"])){
    				if(preg_match("/var\s+[^\=\;\s\"\']+\s*\=\s*new\s+[^\(\)]+\(/i", $value["text"])){
						//var block_td_uid_2_58aab3b5b4eb1=new tdBlock()
    					break;
    				}else if(preg_match("/jQuery\(\s*window\s*\)\.load\(/i", $value["text"]) || preg_match("/\\$\(\s*window\s*\)\.load\(/i", $value["text"])){
						//jQuery(window).load(function(){
						//$(window).load(function(){
						break;
					}else if(preg_match("/^<script[^\>]*>\s*jQuery\([^\)\(]+\)\.[a-z]+\(/i", $value["text"]) && strpos($value["text"], "\n") === FALSE){
						//<script>jQuery("div").append("");</script>

						$value["text"] = $this->defer_load_inline_js($value["text"], $key."i");
					}else if(preg_match("/^<script[^\>]+src=[\'\"][^\>]+>/i", $value["text"])){
						$external_start = true;
						
						if(preg_match("/data-cfasync\=/i", $value["text"])){
							break;
						}

						if(!preg_match("/\s+defer\s+/i", $value["text"])){
							$value["text"] = preg_replace("/<script\s+/", "<script defer ", $value["text"]);
						}
					}else{
						//inline js

						// <script>(function(d, s, id){
						// var js, fjs=d.getElementsByTagName(s)[0];
						// if(d.getElementById(id)) return;
						// js=d.createElement(s); js.id=id;
						// js.async=true;
						// js.src="//connect.facebook.net/nl_NL/sdk.js#xfbml=1&version=v2.8&appId=1126044540802926";
						// fjs.parentNode.insertBefore(js, fjs);
						// }(document, 'script', 'facebook-jssdk'));</script>
						if(preg_match("/connect\.facebook\.net/i", $value["text"]) && preg_match("/parentNode\.insertBefore/i", $value["text"])){
							continue;
						}else{
							if($external_start){
								break;
							}
						}
					}
				}else if(preg_match("/^<(link|style)/i", $value["text"])){
					continue;
				}else if(preg_match("/<\!--\s*\[if/i", $value["text"])){
					preg_match_all("/<script[^\>]+src=[\'\"][^\>]+>/i", $value["text"], $src_number);
					preg_match_all("/<script[^\>]*/i", $value["text"], $script_tag_number);

					if(count($script_tag_number[0]) != count($src_number[0])){
						break;
					}

					if(preg_match("/<link|<style/i", $value["text"])){
						break;
					}

					if(!preg_match("/<script[^\>]+src=[\'\"][^\>]+>/i", $value["text"])){
						break;
					}

					if(preg_match("/data-cfasync\=/i", $value["text"])){
						break;
					}

					if(!preg_match("/<script[^\>]+defer[^\>]+>/i", $value["text"])){
						$value["text"] = preg_replace("/<script\s+/", "<script defer ", $value["text"]);
					}
				}else{
					break;
				}
			}

			return $tags;
		}

		public function defer_load_inline_js($script, $key){
			//$onload = "if(window.attachEvent){window.attachEvent('onload',wpfdr_".$key.");}else{if(window.onload){var wpfdrcl_".$key."=window.onload;var wpfdrnl_".$key."=function(evt){wpfdrcl_".$key."(evt);wpfdr_".$key."(evt);};window.onload=wpfdrnl_".$key.";}else{window.onload=wpfdr_".$key.";}}";

			$onload = "if(window.attachEvent){window.attachEvent('onload',wpfdr_".$key.");}else if(window.addEventListener){window.addEventListener('load', wpfdr_".$key.",false);}";
			
			$script = preg_replace("/(<script[^\>]*>)/i", "$1".$onload."\nfunction wpfdr_".$key."(){\n", $script);
			$script = preg_replace("/\s*(<\/script>)/i", "\n}"."$1", $script);

			return $script;
		}

		public function add_images_in_css_to_the_footer(){
			if(isset($GLOBALS["wp_fastest_cache"]->images_in_css["path"]) && $GLOBALS["wp_fastest_cache"]->images_in_css["path"]){
				$img_tag = "";
				$cachFilePath = WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/".md5($GLOBALS["wp_fastest_cache"]->images_in_css["path"]).".txt";
				
				if(isset($GLOBALS["wp_fastest_cache"]->images_in_css["images"])){
					foreach ($this->tags as $key => $value) {
						if(preg_match("/^<style/i", $value["text"])){
							$GLOBALS["wp_fastest_cache"]->set_images_in_css($value["text"]);
						}
					}
				}

				if(isset($GLOBALS["wp_fastest_cache"]->images_in_css["images"]) && isset($GLOBALS["wp_fastest_cache"]->images_in_css["images"][0])){
					foreach (array_unique($GLOBALS["wp_fastest_cache"]->images_in_css["images"]) as $key => $value) {
						$img_tag = $img_tag."\n".$this->get_html_image_style($value);
					}

					@file_put_contents($cachFilePath, $img_tag);

				}else if(file_exists($cachFilePath)){
					if(file_exists($cachFilePath)){
						$myfile = fopen($cachFilePath, "r") or die("Unable to open file!");
						$img_tag = fread($myfile, filesize($cachFilePath));
						fclose($myfile);
					}
				}

				$this->html = preg_replace("/<\/body>/i", $img_tag."\n"."</body>", $this->html);
			}else if(isset($GLOBALS["wp_fastest_cache"]->images_in_css["name"]) && $GLOBALS["wp_fastest_cache"]->images_in_css["name"]){
				// used to use $GLOBALS["wp_fastest_cache"]->images_in_css["path"]
				$img_tag = "";
				$cachFilePath = WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/".md5($GLOBALS["wp_fastest_cache"]->images_in_css["name"]).".txt";
				
				if(file_exists($cachFilePath)){
					if(file_exists($cachFilePath)){
						$myfile = fopen($cachFilePath, "r") or die("Unable to open file!");
						$img_tag = fread($myfile, filesize($cachFilePath));
						fclose($myfile);
					}
				}else{
					if(isset($GLOBALS["wp_fastest_cache"]->images_in_css["images"])){
						foreach ($this->tags as $key => $value) {
							if(preg_match("/^<style/i", $value["text"])){
								$GLOBALS["wp_fastest_cache"]->set_images_in_css($value["text"]);
							}
						}
					}

					if(isset($GLOBALS["wp_fastest_cache"]->images_in_css["images"]) && isset($GLOBALS["wp_fastest_cache"]->images_in_css["images"][0])){
						foreach (array_unique($GLOBALS["wp_fastest_cache"]->images_in_css["images"]) as $key => $value) {
							$img_tag = $img_tag."\n".$this->get_html_image_style($value);
						}

						@file_put_contents($cachFilePath, $img_tag);
					}
				}

				$this->html = preg_replace("/<\/body>/i", $img_tag."\n"."</body>", $this->html);
			}
		}

		public function get_html_image_style($value){
			if(preg_match("/\.gif|jpg|jpeg|png/i", $value)){
				if(preg_match("/url\(/i", $value)){
					return '<style>'.$value."</style>";
				}else{
					// old version... we need to remove it after some time
					$value = trim($value);
					$value = trim($value, "'");
					$value = trim($value, '"');
					$value = trim($value);
					return '<div style="display:none !important;"><img src="'.$value.'" /></div>';
				}
			}
		}

		public function split_html_condition($tag){
			if(substr_count($tag, '<!--') == substr_count($tag, '-->')){
				if(preg_match("/\<\!--\s*\[if[^\>]+>/i", $tag, $start_cond)){
					if(preg_match("/<\!\[endif\]-->/i", $tag, $end_cond)){
						$all = array();

						$script_tag = $this->find_tags("<script", "</script>", $tag);
						$style_tags = $this->find_tags("<style", "</style>", $tag);
						$link_tags = $this->find_tags("<link", ">", $tag);

						$all = array_merge($script_tag, $style_tags, $link_tags);

						$all = $this->tags_reorder($all);

						foreach ($all as $key => &$value) {
							$value["text"] = $start_cond[0].$value["text"].$end_cond[0];
						}

						return $all;
					}
				}
			}

			return false;
		}
	}
?>