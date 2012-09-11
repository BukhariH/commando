<?php
	/*
	# Copyright 2012 NodeSocket, LLC
	#
	# Licensed under the Apache License, Version 2.0 (the "License");
	# you may not use this file except in compliance with the License.
	# You may obtain a copy of the License at
	#
	# http://www.apache.org/licenses/LICENSE-2.0
	#
	# Unless required by applicable law or agreed to in writing, software
	# distributed under the License is distributed on an "AS IS" BASIS,
	# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	# See the License for the specific language governing permissions and
	# limitations under the License.
	*/
	
 	class Navigation {		
		private static $pages = array("execute" => "icon-cog",
				                      "recipes" => "icon-book",
				                      "files" => "icon-file",
				                      "servers" => "icon-hdd",
				                       "groups" => "icon-th",
				                     "settings" => "icon-check");
		
		private static $right = null;
				
		public static function right($html) {
			Navigation::$right = $html;
		}
		
		public static function render($current_page = "") {
			echo '<div class="navbar navbar-fixed-top">
			      <div class="navbar-inner">
			        <div class="container">
			          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			      	 	<span class="icon-bar"></span>
			      	 	<span class="icon-bar"></span>
			      	 	<span class="icon-bar"></span>
			          </a>
			          <a class="brand" href="/">Commando.io</a>
			          <div class="nav-collapse">
			            <ul class="nav">';
			            
			             	foreach(Navigation::$pages as $page => $icon) {
			             		if($page === strtolower($current_page)) {
			             			echo '<li class="active">';
			             		} else {
			             			echo '<li>';
			             		}
			             		
			             		echo '<a href="' . Links::render($page) . '"><i class="' . $icon . '"></i> ' . ucfirst($page) . '</a></li>';
			             	}     
			              
			       echo '</ul>';
			       
			       		if(!empty(Navigation::$right)) {
			       			echo '<ul class="nav pull-right">' .  Navigation::$right . '</ul>';
			       		}
			       
			    echo '</div>
			        </div>
			      </div>
			    </div>';
		}
	}
?>