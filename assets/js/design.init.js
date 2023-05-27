/* Made by Han Lin Yap (aka Codler) http://www.zencodez.net/ 
 * Created: 2010-03-07
 * Last modified: 18th may 2010 v0.3
 * 8th april 2010 v0.2
 * 7th april 2010 v0.1 
 */
 
// gets key pressed event.
var global_key = new Array();
$(document).keyup(function(e) {
	code = e.keyCode ? e.keyCode : e.which;
	global_key[code] = false;
}).keydown(function(e) {
	code = e.keyCode ? e.keyCode : e.which;
	global_key[code] = true;
});

// Select elements
var select = {
	'tool' : {
		'frame'			: '#tool',
		'components'	: '.tool-component',
		'dock'			: '#tool-dock a',
		'draghandlers'	: '.draghandler',
		'layers'		: '.layer'
	},
	'component' : {
		'frames' : '.component',
		'id'	: function(id) {
			return this.frames + "[layer_id='" + id + "']";
		}
	},
	'canvas' : {
		'inner' : '#workspace',
		'outer' : '#worksheet'
	},
	'blog' : window.location.pathname.substr("/design/edit/".length).split("/")[0],
	'layout' : window.location.pathname.substr("/design/edit/".length).split("/")[1],
	'preview' : (window.location.pathname.indexOf('/design/preview') == 0)
}

// Settings
var components = {
	'blog_posts' : {
		'css' : {
			'background-color' 	: '#ffffff',
			'color' 			: '#000000',
			'height' 			: 200,
			'width' 			: 200
		},
		'text' : "Blogg inlägg",
		'description' : "Här kommer blogg inläggen att visas."
	},
	'list' : {
		'css' : {
			'background-color' 	: '#000000',
			'color' 			: '#ffffff',
			'height' 			: 100,
			'width' 			: 100
		},
		'text' : "Lista",
		'description' : "En lista med länkar."
	},
	'image' : {
		'css' : {
			'background-color' 	: '#fa615c',
			'color' 			: '#ffffff',
			'height' 			: 100,
			'width' 			: 100
		},
		'text' : "Bild",
		'description' : "Denna kan användas som symboler, bakgrund, dekorationer, vanlig bild."
	},
	'text' : {
		'css' : {
			'background-color' 	: '#b3e6cf',
			'color' 			: '#000000',
			'height' 			: 100,
			'width' 			: 100
		},
		'text' : "Text",
		'description' : "Enbart text."
	}
	,
	'grid' : {
		'css' : {
			'background-color' 	: '#cccccc',
			'color' 			: '#000000',
			'height' 			: 300,
			'width' 			: 200
		},
		'text' : "Låda",
		'description' : "Du kan lägga in andra verktyg in till lådan så det blir lättare att tex ändra storleken på flera objekt samtidigt."
	}
	
}
// Event
var event_maker = {
	'events' : {
		'click' : 'klick', 
		'dblclick' : 'dubbelklick', 
		'mouseover' : 'musen över', 
		'mouseout' : 'musen ut'
	},
	'effects' : {
		'hide' : 'göm',
		'show' : 'visa'
	},
	'duration' : {
		0 : 'instant',
		1000 : 'snabbt',
		2000 : 'söligt'
	}
}

var global_settings = {
	'view_content' : true,
	'component_zIndex' : 500,
	'inner_background' : 'http://',
	'outer_background' : 'http://'
}

var tool_settings = {
	'dock' : false,
	'component': {'height': 100, 'width': 100}
}

var handle_settings = {
	/* Tools */
	'tool': {
		'draggable' : {
			cancel		: select.tool.components + ', input',
			containment	: select.canvas.outer,
			handle		: select.tool.draghandlers + ', #tool-dock',
			snap		: select.canvas.inner,
			snapMode	: 'outer',
			snapTolerance: 10,
			start		: function(e) {
				$(this).data('started', true);
			},
			stop		: function(e) {
				$(this).data('started', false);
			}
		},
		
		'draghandler' : {
			'toggle' : function () {
				var item_content = $(this).next();
				if (!$(select.tool.frame).data('started'))
					item_content.toggle();
					
				if (item_content.is(':hidden')) {
					$(this).addClass('hidden');
				} else {
					$(this).removeClass('hidden');
				}
			}
		}
	},
	/* Component */
	'component' : {
		'resizable' : {
			handles : 'all',
			resize : function () {
				if ($(this).data('component_type') == 'grid') {
					$(select.component.frames, this).width($(this).width());
				}
			}
		},
		'mark' : function () {
			var component_item = $(select.component.id($(this).data('layer_id'))),
				search_in = $(select.canvas.inner);
			show_focus_frame(component_item, search_in);
		},
		'unmark' : function () {
			var component_item = $(select.component.id($(this).data('layer_id'))),
				search_in = $(select.canvas.inner);
			hide_focus_frame(component_item, search_in);
		}
	}
}

var tool = {
	'move' : {
		'fixed' : function () {
			$(select.tool.frame).draggable("destroy");
			tool_settings.dock = true;
			$(window).trigger('resize');
		},
		'free' : function () {
			$(select.tool.frame).draggable(handle_settings.tool.draggable).disableSelection();
			tool_settings.dock = false;
			$(window).trigger('resize');
		}
	}
}

$(document).ready(function () {	
	
	function init_handler() {
		/* Tools */
		// Set dock mode
		$(select.tool.dock).toggle(tool.move.fixed, tool.move.free);
		
		// Toggle section
		$(select.tool.draghandlers).mouseup(handle_settings.tool.draghandler.toggle);
		
		tool.move.free();
	}
	
	/* Tools - layer */
	
	// Mark selected component
	$(select.tool.layers).live('mouseover', handle_settings.component.mark);
	
	// Unmark component
	$(select.tool.layers).live('mouseout', handle_settings.component.unmark);

	// Select component
	$(select.tool.layers).live('click', function () {
		$(select.component.id($(this).data('layer_id'))).click();
	});
	
	// Change layer name
	$(select.tool.layers).live('dblclick', function () {
		var layer_name = prompt('Byt lager namn', $(this).text().substr(1));
		if (layer_name!=null && layer_name!="") {
			$(this).html("<a>X</a>"+layer_name);
		}
	});
	
	$(".layer a").live('click', function () {
		$(this).mouseout();
		$("#workspace .component[layer_id='"+$(this).parent().data('layer_id')+"']").remove();
		$(this).parent().remove();
	});
	
	// Sort layers
	$("#tool-section-layer .tool-section-content").sortable({
		placeholder: 'ui-state-highlight',
		update : function () {
			var index = 500 + $(".layer").length;
			$(".layer").each(function (i) {
				var item = $(".component[layer_id='"+$(this).data('layer_id')+"']");
				item.css('z-index', --index);
			});
		}
	});
	
	$(".component a").live('click', function (e) {
		// disable links in components
		e.preventDefault();
	});
	
	// select section
	$(".component").live('click', function () {
		var selected = $(this),
			component_type = $(this).data('component_type'),
			layer_item = $(".layer[layer_id='"+$(this).attr('layer_id')+"']");
		$("#tool-section-select").data('selected', $(this).attr('layer_id'));
		$("#tool-section-select").data('component_type', $(this).data('component_type'));
		
		if (global_key[16]==undefined) {
			global_key[16] = false;
		}
		if (global_key[17]==undefined) {
			global_key[17] = false;
		}
		
		// select layer
		// ctrl & shift
		if (global_key[17]==false&&global_key[16]==false) {
			$(".layer").removeClass('selected');
		}
		
		// shift
		if (global_key[16]==true&&$(".layer.selected").length>0) {
			var layer_pos = layer_item.parent().children().index(layer_item.get(0));
			var exist_layer_pos = layer_item.parent().children().index($('.layer.selected').get(0));
			$('.layer').each(function (i) {
				if (layer_pos < exist_layer_pos) {
					if (i > layer_pos && i < exist_layer_pos) {
						$(this).addClass('selected');
					}
				} else {
					if (i < layer_pos && i > exist_layer_pos) {
						$(this).addClass('selected');
					}
				}
			});
		}
		// ctrl
		if (global_key[17]==true&&layer_item.hasClass('selected')) {
			layer_item.removeClass('selected');
		} else {
			layer_item.addClass('selected');
		}
		
		var layer_name = layer_item.text().substr(1);
		//var html = "<p>Layer: " + layer_name + "</p>";
		var html = "";
			html += "<p>Background Text</p>";
		// background
			html += "<span class='input-color-box' style='background-color:"+$(this).data('css')['background-color']+";'></span><input type='text' id='background-color' class='input-color color {hash:true}' name='color' value='"+$(this).data('css')['background-color']+"' />";
		// text
			html += "<span class='input-color-box' style='background-color:"+$(this).data('css').color+";'></span><input type='text' id='text-color' class='input-color color {hash:true}' name='color' value='"+$(this).data('css').color+"' />";
		if (component_type != 'image' && component_type != 'grid') {
		// text size	
			html += "<p>Text storlek</p>";
		var font_size = ($(this).data('css')['font-size']) ? $(this).data('css')['font-size'].substr() : "12px";
			font_size = font_size.substr(0, font_size.length-2);
			html += "<input type='text' id='text-size' class='input-number' value='"+font_size+"' /> px";
		}
		// transparent
			html += "<br/><a id='background-transparent'>Transparent</a><br/>";
		// events
		if ($(".layer.selected").length == 1) {
			html += "<a id='settings-events'>Händelser</a><br/>";
		
		// grid - component
			if (component_type == 'grid') {
				var margin = $(this).data('grid-margin');
				if (margin == undefined) margin = 0;
				html += "<label for=\"grid-margin\">Marginal:</label><input class=\"input-number\" type=\"text\" id=\"grid-margin\" name=\"grid-margin\" value=\""+margin+"\" />";
			}
		}
		
		$("#tool-section-select .tool-section-content").html("");
		$(html).appendTo("#tool-section-select .tool-section-content");
		
		function colorpicker_background(hsb, hex, rgb) {
			$("#background-color").val("#" + hex);
			$("#background-color").prev().css('background-color', '#' + hex);
			
			//var id = $("#tool-section-select").data('selected');
			$('.layer.selected').each(function (i) {
				var id = $(this).data('layer_id');
				var data = $(select.component.id(id)).data('css');
				data['background-color'] = $("#background-color").val();
				$(select.component.id(id)).data('css', data);
				$(select.component.id(id)).css('background-color', $("#background-color").val());
			});
		}
		// background
		$("#background-color").ColorPicker({
			onChange: colorpicker_background,
			onSubmit: colorpicker_background
		})
		.ColorPickerSetColor($(this).data('css')['background-color'])
		.bind('keyup', function(){
			$(this).ColorPickerSetColor($(this).val());
		});
		
		function colorpicker_text(hsb, hex, rgb) {
				$("#text-color").val("#" + hex);
				$("#text-color").prev().css('background-color', '#' + hex);
				
				//var id = $("#tool-section-select").data('selected');
				$('.layer.selected').each(function (i) {
					var id = $(this).data('layer_id');
					var data = $(select.component.id(id)).data('css');
					data.color = $("#text-color").val();
					$(select.component.id(id)).data('css', data);
					$(select.component.id(id)).css('color', $("#text-color").val());
				});
			}
		
		// text
		$("#text-color").ColorPicker({
			onChange: colorpicker_text,
			onSubmit: colorpicker_text
		})
		.ColorPickerSetColor($(this).data('css').color)
		.bind('keyup', function(){
			$(this).ColorPickerSetColor($(this).val());
		});
	});
	
	// color box
	$(".input-color-box").live('click', function () {
		$(this).next().click();
	});
	
	// text size
	$("#text-size").live('blur', function () {
		var input = $(this);
		$('.layer.selected').each(function (i) {
			var id = $(this).data('layer_id');
			$(select.component.id(id)).css('font-size', input.val() + 'px');
			var css = $(select.component.id(id)).data('css');
			css['font-size'] = input.val() + 'px';
			$(select.component.id(id)).data('css', css);
		});
	});
	
	// transparent
	$("#background-transparent").live('click', function () {
		//var id = $("#tool-section-select").data('selected');
		$('.layer.selected').each(function (i) {
			var id = $(this).data('layer_id');
			$(select.component.id(id)).css('background-color', null);
			var css = $(select.component.id(id)).data('css');
			css['background-color'] = '';
			$(select.component.id(id)).data('css', css);
		});
	});
	
	// grid-margin
	$("#grid-margin").live('keyup', function () {
		var id = $('.layer.selected').data('layer_id');
		var selected = $(select.component.id(id));
		var old_margin = selected.data('grid-margin');
		if (old_margin == undefined) {
			old_margin = 0;
		}
		selected
			.css({
				'padding' : $(this).val()
			})
			.data('grid-margin', $(this).val())
			.height(selected.height() - ($(this).val() - old_margin) * 2)
			.width(selected.width() - ($(this).val() - old_margin) * 2)
			
		$(select.component.frames, selected).width(selected.width());
	});
	
	// events
	$("#settings-events").live('click', function () {
		$("#dialog-settings-events").dialog({
			autoOpen: false,
			modal: true,
			width: 600,
			open: function () {
				var dialog = $(this);
				// selection
				$("#events-event").html("");
				$.each(event_maker.events,function (key, val) {
					$("<option value='"+key+"'>"+val+"</option>").appendTo("#events-event");
				});
				$("#events-effect").html("");
				$.each(event_maker.effects,function (key, val) {
					$("<option value='"+key+"'>"+val+"</option>").appendTo("#events-effect");
				});
				$("#events-duration").html("");
				$.each(event_maker.duration,function (key, val) {
					$("<option value='"+key+"'>"+val+"</option>").appendTo("#events-duration");
				});
				$("#events-layer").html("");
				$('.layer').each(function (i) {
					$("<option value='"+$(this).data('layer_id')+"'>"+$(this).text().substr(1)+"</option>").appendTo("#events-layer");
				});
				// content
				$(this).find("tbody").html("");
				var id = $(".layer.selected").data('layer_id');
				var user_events = $(select.component.id(id)).data('user_events');
				if (user_events) {
					$.each(user_events, function (key, val) {
						var ev = val.event;
						var effect = val.effect;
						var duration = val.duration;
						var layer = val.layer;
						if ($(".layer[layer_id='"+layer+"']").length==0) return true;
						
						var html = "";
							html += "<tr><td>Vid <select class=\"events-event\">";
						$.each(event_maker.events,function (key, val) {
							if (key==ev) {
								html += "<option value='"+key+"' selected>"+val+"</option>";
							} else {
								html += "<option value='"+key+"'>"+val+"</option>";
							}
						});
							html += "</select></td><td><select class=\"events-effect\">";
						$.each(event_maker.effects,function (key, val) {
							if (key==effect) {
								html += "<option value='"+key+"' selected>"+val+"</option>";
							} else {
								html += "<option value='"+key+"'>"+val+"</option>";
							}
						});
							html += "</select></td><td><select class=\"events-duration\">";
						$.each(event_maker.duration,function (key, val) {
							if (key==duration) {
								html += "<option value='"+key+"' selected>"+val+"</option>";
							} else {
								html += "<option value='"+key+"'>"+val+"</option>";
							}
						});
							html += "</select></td><td><select class=\"events-layer\">";
						$('.layer').each(function (i) {
							if ($(this).data('layer_id')==layer) {
								html += "<option value='"+$(this).data('layer_id')+"' selected>"+$(this).text().substr(1)+"</option>";
							} else {
								html += "<option value='"+$(this).data('layer_id')+"'>"+$(this).text().substr(1)+"</option>";
							}
						});
							html += "</select></td><td><a class=\"delete\">Ta bort</a></td></tr>";
						$('tbody', dialog.get(0)).append(html);
					});
				}
			},
			buttons: {
				'Avbryt' : function() {
					$(this).dialog('close');
					$(this).dialog('destroy');
				}, 
				'Ändra' : function() {
					var data = {};
					$(this).find("tbody").find("tr").each(function (i) {
						var item = {
							'event' : $(this).find('.events-event').val(),
							'effect' : $(this).find('.events-effect').val(),
							'duration' : $(this).find('.events-duration').val(),
							'layer' : $(this).find('.events-layer').val()
						};
						data[i] = item;
					});
					
					var id = $('.layer.selected').data('layer_id');
					$(select.component.id(id)).data('user_events', data);
					
					$(this).dialog('close');
					$(this).dialog('destroy');
				}
			},
			close: function() {
			}
		});	
	
		$("#dialog-settings-events").dialog('open');
	});
	
	// Dialog
	$(".component").live('dblclick', function () {
		var id = $(this).attr('layer_id');
		var component_name = $(this).data('component_type');
		var selected = $(this);
		
		if ("text" == $(this).data('component_type')) {
		
			$("#dialog-component-text").dialog({
				autoOpen: false,
				modal: true,
				open: function () {
					selected.resizable("destroy");
					$(this).find('textarea').val(selected.text());
				},
				buttons: {
					'Avbryt' : function() {
						$(this).dialog('close');
					}, 
					'Ändra' : function() {
						selected.text($(this).find('textarea').val());
						$(this).dialog('close');
					}
				},
				close: function() {
					selected.resizable(handle_settings.component.resizable);
					$(this).find('textarea').val("");
				}
			});	
		
			$("#dialog-component-text").dialog('open');
		}
		if ("image" == $(this).data('component_type')) {
		
			$("#dialog-component-image").dialog({
				autoOpen: false,
				modal: true,
				open: function () {
					$(this).find("input[name=url]").val(selected.find('img').attr('src'));
					//$(this).find("input[name=url]").val(selected.css('background-image').substr(5,selected.css('background-image').length-7));
				},
				buttons: {
					'Avbryt' : function() {
						$(this).dialog('close');
						$(this).dialog('destroy');
					}, 
					'Ändra' : function() {
						var img = $("<img/>");
						img.load(function () {
							selected.width(this.width);
							selected.height(this.height);
							$(this).data('width', this.width);
							$(this).data('height', this.height);
							$(this).css({width: "100%", height: "100%"})
						})
						.attr('src','http://'+$(this).find("input[name=url]").val().replace('http://',''));
						selected.resizable('destroy').html(img).resizable(handle_settings.component.resizable);
						//selected.html('<img src="http://'+$(this).find("input[name=url]").val().replace('http://','')+'" />');
						//selected.css('background-image','url(http://'+$(this).find("input[name=url]").val().replace('http://','')+')');
						$(this).dialog('close');
						$(this).dialog('destroy');
					}
				},
				close: function() {
					$(this).find("input[name=url]").val("http://");
				}
			});	
		
			$("#dialog-component-image").dialog('open');
		}
		
		if ("list" == $(this).data('component_type')) {
		
			$("#dialog-component-list").dialog({
				autoOpen: false,
				modal: true,
				width: 300,
				open: function () {
					$(this).find("tbody").html("");
					selected.find('li').each(function (i) {
						$("#dialog-component-list tfoot a").click();
						$("#dialog-component-list tbody tr:last").find("td:first input").val($(this).find('a').attr('href'));
						$("#dialog-component-list tbody tr:last").find("td:last input").val($(this).text());
					});
				},
				buttons: {
					'Avbryt' : function() {
						$(this).dialog('close');
						$(this).dialog('destroy');
					}, 
					'Ändra' : function() {
						var html = "<ul>";
						$(this).find("tbody").find("tr").each(function (i, val) {
							html += "<li><a href='" + $(this).find("td:first input").val() + "'>" + $(this).find("td:last input").val() + "</a></li>";
						});
						html += "</ul>";
						selected.find('ul').sortable('destroy');
						selected.resizable('destroy').html(html).resizable(handle_settings.component.resizable);
						selected.find('ul').sortable({
							connectWith: '.component ul',
							placeholder: 'ui-state-highlight'
						});
						
						
						$(this).dialog('close');
						$(this).dialog('destroy');
					}
				},
				close: function() {
				}
			});	
		
			$("#dialog-component-list").dialog('open');
		}
	});
	
	// dialog list
	$("#dialog-component-list").find("tfoot a").click(function () {
		$('<tr><td><input type="text" id="url" name="url" value="http://" /></td><td><input type="text" id="namn" name="namn" value="" /></td></tr>')
		.appendTo("#dialog-component-list tbody");
	});
	
	// dialog events - add
	$("#dialog-settings-events").find("tfoot a").click(function () {
		var ev = $("#dialog-settings-events").find("#events-event").val();
		var effect = $("#dialog-settings-events").find("#events-effect").val();
		var duration = $("#dialog-settings-events").find("#events-duration").val();
		var layer = $("#dialog-settings-events").find("#events-layer").val();
	
		var html = "";
			html += "<tr><td>Vid <select class=\"events-event\">";
		$.each(event_maker.events,function (key, val) {
			if (key==ev) {
				html += "<option value='"+key+"' selected>"+val+"</option>";
			} else {
				html += "<option value='"+key+"'>"+val+"</option>";
			}
		});
			html += "</select></td><td><select class=\"events-effect\">";
		$.each(event_maker.effects,function (key, val) {
			if (key==effect) {
				html += "<option value='"+key+"' selected>"+val+"</option>";
			} else {
				html += "<option value='"+key+"'>"+val+"</option>";
			}
		});
			html += "</select></td><td><select class=\"events-duration\">";
		$.each(event_maker.duration,function (key, val) {
			if (key==duration) {
				html += "<option value='"+key+"' selected>"+val+"</option>";
			} else {
				html += "<option value='"+key+"'>"+val+"</option>";
			}
		});
			html += "</select></td><td><select class=\"events-layer\">";
		$('.layer').each(function (i) {
			if ($(this).data('layer_id')==layer) {
				html += "<option value='"+$(this).data('layer_id')+"' selected>"+$(this).text().substr(1)+"</option>";
			} else {
				html += "<option value='"+$(this).data('layer_id')+"'>"+$(this).text().substr(1)+"</option>";
			}
		});
			html += "</select></td><td><a class=\"delete\">Ta bort</a></td></tr>";
			
		$(html)
		.appendTo("#dialog-settings-events tbody");
	});
	// dialog events - delete
	$("#dialog-settings-events").find("tbody a").live('click', function () {
		$(this).parent().parent().remove();
	});
	
	/* worksheet */
	
	$("#worksheet").disableSelection();
	
	/* Move components with arrow */
	$(document).keydown(function (event) {
		var tag = event.target.nodeName;
		if (tag == 'INPUT' || tag == 'SELECT' || tag == 'TEXTAREA' ) {
			return true;
		}
	
		var top = 0, left = 0;
		
		// Key arrow left
		if (event.keyCode == 37) {
			left--;
		// Key arrow right
		} else if (event.keyCode == 39) {
			left++;
		}
		
		// Key arrow up
		if (event.keyCode == 38) {
			top--;
		// Key arrow down
		} else if (event.keyCode == 40) {
			top++;
		}
	
		$('.layer.selected').each(function (i) { 
			var id = $(this).data('layer_id');
			
			$(select.component.id(id)).css({
				left : function(index, value) {
					return parseInt(value.replace('px',''))+left;
				},
				top : function(index, value) {
					return parseInt(value.replace('px',''))+top;
				}
			});
		});
		
		return true;
	});
	
	$("#worksheet").mousedown(function(e) {
		if (global_key[32]) {
			e.preventDefault();
		}
	});
		
	$("#workspace").droppable({
		accept: '.tool-component, .component',
		drop: function(event, ui) {
			var item = ui.draggable;
			
			// from grid
			if (jQuery.data(item.get(0),'grid')) {
				item.data('grid', false)
					.css({
						position : 'absolute',
						minHeight : 'none',
						left : event.pageX - $("#workspace").offset().left - tool_settings.component.width / 2,
						top : event.pageY - $("#workspace").offset().top - tool_settings.component.height / 2
					}).resizable(handle_settings.component.resizable);
				$(this).append(item);
				return;
			}
			// from tool
			if (!jQuery.data(item.get(0),'tool')) return;
			
			var component_type = jQuery.data(item.get(0),'component_type');
			
			// add component with default settings
			add_component(component_type, false, event);

		},
		greedy: true
	});
	

	
	$("#workspace").mousedown(function(e) {
		if (e.target === e.currentTarget) {
			$(this).data('mousedown', true);
			e.preventDefault();
		}
	});
	
	$("#workspace").mousemove(function(e) {
		if ($(this).data('mousedown')) {
			if ($(this).data('prev_mouse_cords')) {
				currTop = $("#worksheet").scrollTop();
				currLeft = $("#worksheet").scrollLeft();
				
				prev_pos = $(this).data('prev_mouse_cords');
				$("#worksheet").scrollTop(currTop - (e.pageY - prev_pos.y));
				$("#worksheet").scrollLeft(currLeft - (e.pageX - prev_pos.x));
			}
			$(this).data('prev_mouse_cords',{x: e.pageX, y: e.pageY});
		}
	});

	$(document).mouseup(function() {
		$("#workspace").data('mousedown', false);
		$("#workspace").removeData('prev_mouse_cords');
	})
	

	
	/* Toolbar */
	
	$(".input-number").live('keydown', input_int);
	$("#worksheet_width, #worksheet_height").blur(recalc_workspace);
	
	/*
	$("#view_content").click(function () {
		if (global_settings.view_content) {
			$("#workspace .component").each(function () {
				jQuery.data($(this).get(0), 'component_data', $(this).html());
				$(this).resizable('destroy').html("").resizable(handle_settings.component.resizable);
			});
			global_settings.view_content = false;
		} else {
			$("#workspace .component").each(function () {
				var data = jQuery.data($(this).get(0), 'component_data');
				$(this).resizable('destroy').html(data).resizable(handle_settings.component.resizable);
			});
			global_settings.view_content = true;
		}
	});*/
	
	// unit
	$(".unit_text").live('mouseenter', function () {
		if ($(this).next().is('select')) {
			switch_display($(this),$(this).next());
		}
	});
	$(".unit").change(function () {
		if ($(this).prev().is('span.unit_text')) {
			$(this).prev().text($(this).val());
			switch_display($(this),$(this).prev());
			recalc_workspace();
		} else {
			$(this).before($("<span class='unit_text'>" + $(this).val() + "</span>"));
			switch_display($(this),$(this).prev());
		}
	});
	$(".unit").change();
	
	// reset
	$("#design-reset").click(function () {
		//$.cookie('zida-design-generator-save', '{}');
		//$.post("storage.php", { 'design-save': '{}' } );
		$(".layer , .component[layer_id]").remove();
	});
	
	// save
	$("#design-save").click(function () {
		var data = {'global_settings':global_settings};
		
		$($(".layer").get().reverse()).each(function (i) {
			var item = $(".component[layer_id='"+$(this).data('layer_id')+"']");
			var json_item = {
				'layer_name': $(this).text().substr(1),
				'layer_id': $(this).data('layer_id'),
				'component_type': item.data('component_type'),
				'css': {
					'background-color': item.data('css')['background-color'],
					'color': item.data('css').color,
					'height': item.css('height'),
					'width': item.css('width'),
					'top': item.css('top'),
					'left': item.css('left')
				}
			};
			
			if (item.data('user_events')) json_item['user_events'] = item.data('user_events');
			if (item.css('font-size')) json_item.css['font-size'] = item.css('font-size');
			if (item.css('padding')) json_item.css['padding'] = item.css('padding');
			
			if (item.data('component_type') == 'blog_posts') {
				json_item['css']['min-height'] = json_item['css']['height'];
				delete json_item['css']['height'];
			}
			
			if (item.data('component_type') == 'text') {
				json_item['text'] = item.text();
			}
			if (item.data('component_type') == 'image') {
				if (item.css('background-image') != undefined) {
					json_item['image'] = item.find("img").attr('src');
				}
			}
			if (item.data('component_type') == 'list') {
				var list = {}
					item.find("li").each(function (i) {
						list[i] = {
							'url' : $(this).find('a').attr('href'),
							'name' : $(this).text()
						}
					});
				
				json_item['list'] = list;
			}
			
			if (item.data('component_type') == 'grid') {
				var connectors = {}
					item.children(".component").each(function (i) {
						connectors[i] = $(this).data('layer_id');
					});
				
				json_item['grid'] = connectors;
			}
			
			data[i] = json_item;
		});
		/*
		if (window.localStorage) {
			window.localStorage.setItem('design-save', JSON.stringify(data));
			$("#notice span").text("Sparad!");
			$("#notice").fadeIn('fast').delay(5000).fadeOut('slow');
		} else {
		*/
			$.post("/storage/" + select.blog + "/" + select.layout + window.location.search, { 'design-save': JSON.stringify(data) }, function () {
				$("#notice span").text("Sparad!");
				$("#notice").fadeIn('fast').delay(5000).fadeOut('slow');
			});
		//}
		//$.cookie('zida-design-generator-save', JSON.stringify(data));
		return false;
	});
	
	// load
	$("#design-load").click(function (ev, type) {
		var data = {};
		if (type=='example') {
			data = {'example' : 'example'};
		}
		
		/*if (window.localStorage) {
			data = window.localStorage.getItem('design-save');
			if (data == "") {
				data = "{}";
			}
			load_design_by_json($.parseJSON(data));
			
			$("#notice span").text("Inladdad!");
			$("#notice").fadeIn('fast').delay(5000).fadeOut('slow');
		} else {	
		*/
		var url = (!select.preview) ? select.blog + "/" + select.layout : "";
		
			$.get("/storage/" + url + window.location.search,data, function (data) {
				if (data == "") {
					data = "{}";
				}
				load_design_by_json($.parseJSON(data));
				//console.log(data);
				
				$("#notice span").text("Inladdad!");
				$("#notice").fadeIn('fast').delay(5000).fadeOut('slow');
			});
		//}
		//var data = $.parseJSON($.cookie('zida-design-generator-save'));
		
		//load_design_by_json(data);

		return false;
	});
	
	// preview
	$("#design-preview").click(function () {
		$("#design-save").click();
		$(this).ajaxStop(function () {
			window.location.href = window.location.protocol + "//" + select.blog + "." + window.location.hostname //"preview.php" + window.location.search;
		});
		return false;
	});
	
	// load example
	$("#design-load-example").click(function () {
		$("#design-reset").click();
		$("#design-load").trigger('click',['example']);
		return false;
	});
	
	$("#settings-background").click(function () {
		$("#dialog-settings-background").dialog({
			autoOpen: false,
			modal: true,
			open: function () {
				$(this).find("#inner-background").val(global_settings.inner_background);
				$(this).find("#outer-background").val(global_settings.outer_background);
			},
			buttons: {
				'Avbryt' : function() {
					$(this).dialog('close');
					$(this).dialog('destroy');
				}, 
				'Ändra' : function() {
					global_settings.inner_background = $(this).find("#inner-background").val();
					global_settings.outer_background = $(this).find("#outer-background").val();
					
					$(this).dialog('close');
					$(this).dialog('destroy');
				}
			},
			close: function() {
				$(this).find("input[name=url]").val("http://");
			}
		});	
	
		$("#dialog-settings-background").dialog('open');
	});

	// init
	$(window).bind('resize', init);	
	init();
	init_handler();
	initializeComponents();
	$("#workspace").queue(function() {
		initializeScroll();
		$(this).dequeue();
	});
	
	// Bug fix - colorpicker
	$('.colorpicker_current_color').live('click',function () {
		$(this).parent().find('.colorpicker_submit').click();
	});
});