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

var timer;
var editor;
var notes;

function isRecipeNameUnique(throttle, callback) {
	clearTimeout(timer);
	
	if($("#recipe-name").val().length === 0) {
		if(!$("#recipe-name").parents(".control-group").hasClass("error")) {
			$("#recipe-name").parents(".control-group").addClass("error");		
		}
		
		typeof callback === "function" ? callback.call(this, false) : null;
		return;
	}
	
	var wait_time = (throttle) ? 200 : 0;
	
	timer = setTimeout(function() {
		Request.ajax("/actions/check_recipe_name.php", {
			name: $("#recipe-name").val()
		}, function(response) {
			if(typeof response !== "undefined") {
				if(response.unique === true) {
					if($("#recipe-name").parents(".control-group").hasClass("error")) {
						$("#recipe-name").parents(".control-group").removeClass("error");
					}
	
					typeof this.callback === "function" ? this.callback.call(this, true) : null;
				} else {
					if(!$("#recipe-name").parents(".control-group").hasClass("error")) {
						$("#recipe-name").parents(".control-group").addClass("error");
					}
	
					typeof this.callback === "function" ? this.callback.call(this, false) : null;
				}
			}
		}.bind({ callback: this.callback }));
	}.bind({ callback: callback }), wait_time);
}

function validate_add_recipe() {	
	if($("#add-recipe-submit").hasClass("disabled")) {
		return;
	}
	
	editor.save();
	notes.save();
	
	clear_errors();
	
	$("#add-recipe-submit").addClass("disabled");
	
	isRecipeNameUnique(false, function(unique) {
		var ul = unique;
		var r = $("#recipe-editor");
		
		if($(r).val().length > 0) {
			$(r).next(".CodeMirror").css("border", "1px solid #dddddd");
			$(r).parents(".control-group").removeClass("error");
		} else {
			$(r).next(".CodeMirror").css("border", "1px solid #B94A48");
			$(r).parents(".control-group").addClass("error");
		}
		
		if(ul && $(r).val().length > 0) {
			$("#form-add-recipe").submit();
		} else {
			$("#add-recipe-submit").removeClass("disabled");
		}
	});
}

$(document).ready(function() {	
	notes = CodeMirror.fromTextArea(document.getElementById('recipe-notes'), {
		mode: 'markdown',
		lineNumbers: false,
		lineWrapping: true,
		matchBrackets: false,
		undoDepth: 250
	});

	editor = CodeMirror.fromTextArea(document.getElementById('recipe-editor'), {
		mode: 'shell',
		lineNumbers: true,
		lineWrapping: false,
		matchBrackets: true,
		undoDepth: 250
	});
	
	$(".CodeMirror").addClass("span9");
	
	$("#recipe-interpreter").chosen();
	$("#recipe-interpreter").trigger("liszt:updated");
	
	$("#recipe-notes").next().find(".CodeMirror-scroll").css("min-height", "83px");
	$("#recipe-notes").next().find(".CodeMirror-scroll").css("max-height", "152px");
	$("#recipe-notes").autosize();
	
	$("#recipe-name").bind("keyup input paste", function() {
		isRecipeNameUnique(true);
	});
	
	$("#recipe-name").upperCase();
	
	$("#recipe-interpreter").change(function() {
		//Bash uses "shell" mode
		if($("#recipe-interpreter").val() == "bash") {
			editor.setOption("mode", "shell");
		} else if($("#recipe-interpreter").val() == "node.js") {
			editor.setOption("mode", "javascript");
		} else {
			editor.setOption("mode", $("#recipe-interpreter").val());
		}
	});
});