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
	
	($_SERVER['SCRIPT_NAME'] !== "/controller.php") ? require_once(__DIR__ . "/classes/Requires.php") : Links::$pretty = true;
	
	//Get recipes
	$recipes = array();
	$result = MySQLQueries::get_recipes();
	while($row = MySQLConnection::fetch_object($result)) {
		//Calculate Statistics
		$row->lines = (substr_count($row->content, "\n") + 1);
		$row->length = Functions::format_bytes(strlen($row->content));
		
		$recipes[$row->id] = $row;
	}
	
	//Get number of versions for all recipes
	$result = MySQLQueries::get_number_of_recipe_versions();
	while($row = MySQLConnection::fetch_object($result)) {
		$recipes[$row->id]->number_of_versions = $row->count;
	}
	
	$recipes = Functions::format_dates($recipes);
	
	Header::set_title("Commando.io - Recipes");
	Header::render();
	
	Navigation::render("recipes");
?>    
    <div class="container">
           
      <h1 class="header">Recipes</h1> 
      
	  <div class="row">
   	  	<div class="span12">
   	  		<div class="well">
   	  			<a href="<?php echo Links::render("add-recipe") ?>" class="btn btn-primary btn-large"><i class="icon-plus-sign icon-white"></i> Add Recipe</a>
   	  			<div style="float: right">	
					<div class="input-prepend" style="float: right">
						<span class="add-on">
							<i class="icon-search"></i>
						</span><input id="search-recipes" type="text" class="span3 tip" rel="tooltip" data-placement="top" data-original-title="Filter by ID, name or interpreter." maxlength="100" placeholder="Filter Recipes…" value=""<?php echo (count($recipes) === 0) ? ' disabled="disabled"' : null ?> />
					</div>
				</div>
				<div class="clear"></div>
   	  		</div>
      	</div>
      </div>
      
      <div class="row">
		<div class="span12">
			<div class="well">
				<div class="alert alert-info fade in" <?php if(count($recipes) > 0): ?>style="display: none;"<?php endif; ?>>
		  	  		<a class="close" data-dismiss="alert">&times;</a>
		  	  		<h4>Did you know?</h4>
		  	  		Recipes are containers of commands that are fully versioned. Recipes can be written in pure <i><strong>shell</strong></i>, <i><strong>bash</strong></i>, <i><strong>perl</strong></i>, <i><strong>python</strong></i>, or <i><strong>node.js</strong></i>.
	      		  </div>
			      <div id="no-recipes" class="alert alert-grey no-bottom-margin" <?php if(count($recipes) > 0): ?>style="display: none;"<?php endif; ?>>
			      	No recipes added. <a href="<?php echo Links::render("add-recipe") ?>">Add</a> a recipe now.
				  </div>
		      	  <?php if(count($recipes) > 0): ?>
			      	  <div id="table-container">
				      	  <div class="control-group">
				      	  	<div class="controls">
				      	 		<a id="delete-recipes" class="btn disabled"><i class="icon-remove"></i> Delete Selected</a>
				      	  	</div>
				      	  </div>
					      <table class="table table-striped table-hover table-bordered table-condensed">
					      	<thead>
					      		<tr>
					      			<th><input type="checkbox" id="recipe-delete-all-check" /></th>
					      			<th>Action</th>
					      			<th>ID</th>
					      			<th>Name</th>
					      			<th>Interpreter</th>
					      			<th>Versions</th>
					      			<th>Added</th>
					      			<th>Modified</th>
					      		</tr>
					      	</thead>
					      	<tbody>
				      			<?php foreach($recipes as $recipe): ?>	
				      				<tr id="<?php echo $recipe->id ?>" class="recipe">
					      				<td><input type="checkbox" class="recipe-delete-check" value="<?php echo $recipe->id ?>" /></td>
					      				<td><a href="<?php echo Links::render("download-recipe", array($recipe->id)) ?>" class="btn btn-mini"><i class="icon-download-alt"></i></a> <a href="<?php echo Links::render("view-recipe-raw", array($recipe->id)) ?>" class="btn btn-mini"><i class="icon-align-left"></i></a></td>
					      				<td><a class="btn btn-mini disabled expand-east expand-recipe-id"><?php echo Functions::add_ellipsis($recipe->id, 7) ?></a></td>
					      				<td><a href="<?php echo Links::render("view-recipe", array($recipe->id)) ?>" rel="tooltip" class="tip-delay" data-placement="top" data-original-title="<?php echo ($recipe->lines == 1 ? $recipe->lines . ' line' : $recipe->lines . ' lines') . " / " . $recipe->length ?>"><?php echo $recipe->name ?></a></td>
					      				<td><?php echo ucfirst($recipe->interpreter) ?></td>
					      				<td><span class="badge badge-info"><?php echo $recipe->number_of_versions ?></span></td>
					      				<td><?php echo $recipe->added ?></td>
					      				<td><?php echo $recipe->modified ?></td>
				      				</tr>
				      			<?php endforeach; ?>
					      	</tbody>
					      </table>
			      	  </div>
			      <?php endif; ?>
			</div>
	    </div>
	  </div>
<?php
	Footer::render(array("bootbox", "recipes"));
?>