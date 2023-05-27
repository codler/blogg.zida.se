/* Made by Han Lin Yap (aka Codler) http://www.zencodez.net/ 
 * Created: 2010-03-07
 * Last modified: 8th april 2010 v0.2
 * 7th april 2010 v0.1 
 */

function init() {
	var viewport = 	{
		'height' 	: $(window).height(),
		'width' 	: $(window).width()
	};
	
	// Move tool into viewport if it comes outside when resizing
	if ($("#tool").offset().left + $("#tool").width() > viewport.width) {
		$("#tool").css({ 'left' : viewport.width - $("#tool").width() });	
	}
	
	if (tool_settings.dock) {
		w = 200;
		$("#tool").css({
			'border-width' : '0px',
			left : viewport.width - $("#tool").width()
		});		
	} else {
		w = 0;
		$("#tool").css({
			'border-width' : '3px'
		});		
	}
	
	$("#tool").css({
		maxHeight : viewport.height - $("nav").height() - ($("#tool").outerHeight() - $("#tool").innerHeight()),
		top : $("nav").height()
	});
	
	$("#worksheet").css({
		height 	: viewport.height - $("nav").height(),
		width 	: viewport.width - w,
		top 	: $("nav").height()
	});
		
	$("#workspace").css({top: 0, left: 0});
	
	// tooltips
	$('.input-number').easyTooltip({ content: 'Tips: Använd uppåt eller neråt pilen om du ska stega några pixlar!', clickRemove: true });
	$('.input-number').mousedown(function () { 
		$(this).unbind('mouseenter');
	});
	$('#tool-section-layer .draghandler').easyTooltip({ content: 'Tips: Markera flera lager en i taget (CTRL) eller ett område (SHIFT) för att flytta eller ändra flera objekt samtidigt!', clickRemove: true });
	
	
	//$(".unit").trigger('change');
	recalc_workspace();
}

function initializeComponents() {

	$.each(components,function(key,value) {
		var component = value;
		
		// Set element
		$("<div/>").attr({ 'id' : 'component_' + key })
			.addClass('tool-component')
			.text(component.text)
			.appendTo("#tool-section-component .tool-section-content")
		$('#component_' + key).easyTooltip({ content: component.description });
			
		// Set linkage
		jQuery.data($("#component_" + key).get(0), 'component_type', key);
		jQuery.data($("#component_" + key).get(0), 'tool', true);
	});
	
	$(".tool-component").draggable({
		appendTo	: '#worksheet',
		containment	: '#worksheet',	
		distance	: 50,
		helper		:'clone',
		opacity		: 0.7,
		snap		: '#workspace .component',
		snapMode	: 'inner'
	});
}

function initializeScroll() {
	measure_workspace();

	var workspace_offset = {
		top: $("#workspace").css('top').replace('px',''), 
		left: $("#workspace").css('left').replace('px','')
	};
	
	var top;
	var left;
	if (global_settings['worksheet_height'] > $("#worksheet").height()) {
		top = workspace_offset.top;
	} else {
		top = workspace_offset.top - ($("#worksheet").height() - global_settings['worksheet_height']) / 2;
	}
	
	if (global_settings['worksheet_width'] > $("#worksheet").width()) {
		left = workspace_offset.left;
	} else {
		left = workspace_offset.left - ($("#worksheet").width() - global_settings['worksheet_width']) / 2;
	}
	
	$("#worksheet").animate({ scrollTop : top, scrollLeft : left});
	
	//recalc_workspace();
}


	
function hide_focus_frame(item, relative) {
	relative = relative || false;
	if (!relative) {
		$("div[frame_id='"+item.data('layer_id')+"']").remove();
	} else {
		$("div[frame_id='"+item.data('layer_id')+"']", relative).remove();
	}
}
function show_focus_frame(item, relative, size) {
	size = size || 1;
	// Top
	$("<div/>").attr({'frame_id':item.data('layer_id')})
	.css({
		'background-color': 'red',
		'position': 'absolute',
		'height': size,
		'width'	: item.outerWidth(),
		'left'	: item.position().left,
		'top'	: item.position().top,
		'zIndex': 3000
	}).appendTo(relative);
	
	// Bottom
	$("<div/>").attr({'frame_id':item.data('layer_id')})
	.css({
		'background-color': 'red',
		'position': 'absolute',
		'height': size,
		'width'	: item.outerWidth(),
		'left'	: item.position().left,
		'top'	: item.position().top + item.outerHeight(),
		'zIndex': 3000
	}).appendTo(relative);
	
	// left
	$("<div/>").attr({'frame_id':item.data('layer_id')})
	.css({
		'background-color': 'red',
		'position': 'absolute',
		'height': item.outerHeight(),
		'width'	: size,
		'left'	: item.position().left,
		'top'	: item.position().top,
		'zIndex': 3000
	}).appendTo(relative);
	
	// right
	$("<div/>").attr({'frame_id':item.data('layer_id')})
	.css({
		'background-color': 'red',
		'position': 'absolute',
		'height': item.outerHeight(),
		'width'	: size,
		'left'	: item.position().left + item.outerWidth(),
		'top'	: item.position().top,
		'zIndex': 3000
	}).appendTo(relative);
}

function measure_workspace() {
	var height = $("#worksheet_height").val();
	var width = $("#worksheet_width").val();
	
	// unit
	if ($("#worksheet_height").parent().next().children('.unit_text').text() == '%') {
		height = height / 100 * $("#worksheet").height();
	}
	if ($("#worksheet_width").parent().next().children('.unit_text').text() == '%') {
		width = width / 100 * $("#worksheet").width();
	}
	
	global_settings['worksheet_height'] = height;
	global_settings['worksheet_width'] = width;
}

function recalc_workspace() {
	measure_workspace();
	
	var height = global_settings['worksheet_height'];
	var width = global_settings['worksheet_width'];
	
	var newtop = Math.max(Math.min(height - height / 2, 500), $("#worksheet").height() / 2 - height / 2);
	var newleft = Math.max(Math.min(width - width / 2, 500), $("#worksheet").width() / 2 - width / 2);
	var oldtop = $("#workspace").css('top').replace('px','');
	var oldleft = $("#workspace").css('left').replace('px', '');
	
	$("#worksheet").animate({ scrollTop : $("#worksheet").scrollTop() + (newtop - oldtop), scrollLeft : $("#worksheet").scrollLeft() + (newleft - oldleft)}, {queue:false});
	$("#workspace").animate({
		height 	: height,
		width	: width,
		top 	: Math.max(Math.min(height - height / 2, 500), $("#worksheet").height() / 2 - height / 2),
		left 	: Math.max(Math.min(width - width / 2, 500), $("#worksheet").width() / 2 - width / 2)
	})
	.queue(function() {
		$("#worksheet-size").css({
			top: Math.max(height * 2, $("#worksheet").height()-25),
			left: Math.max(width * 2, $("#worksheet").width()-25)
		});
		
		$(this).dequeue();
		
	});
}


function load_design_by_json(data) {
	var changed_id = {};
	var j = 0;
	$.each(data, function(key,val) {
		if (key == 'global_settings') {
			// toolbar
			$("#worksheet_height").val(val['worksheet_height']);
			$("#worksheet_width").val(val['worksheet_width']);		
			
			$("#inner-background").val(val['inner_background']);
			$("#outer-background").val(val['outer_background']);
			
			global_settings.inner_background = val['inner_background'];
			global_settings.outer_background = val['outer_background'];
			return;
		}
		// check if id exist before loaded
		$.each(changed_id, function(key2,val2) {
			if (val.layer_id == val2.old_id) {
				val.layer_id = val2.new_id;
			}
			if (val.user_events) {
				$.each(val.user_events, function(key3,val3) {
					if (val3.layer == val2.old_id) {
						val3.layer = val2.new_id;
					}
				});
			}
		});
		
		if ($(".layer[layer_id='"+val.layer_id+"']").length > 0) {
			var new_id = Math.floor(Math.random()*9999999) + 100000;
			changed_id[j] = {
				'old_id' : val.layer_id,
				'new_id' : new_id
			};
			val.layer_id = new_id;
			
			j++;
		}
		// check if id exist before loaded
		$.each(changed_id, function(key2,val2) {
			if (val.layer_id == val2.old_id) {
				val.layer_id = val2.new_id;
			}
			if (val.user_events) {
				$.each(val.user_events, function(key3,val3) {
					if (val3.layer == val2.old_id) {
						val3.layer = val2.new_id;
					}
				});
			}
		});
		
		if (val.user_events) {
			$.each(val.user_events, function(key2,val2) {
				if ($(".layer[layer_id='"+val2.layer+"']").length > 0) {
					var new_id = Math.floor(Math.random()*9999999) + 100000;
					changed_id[j] = {
						'old_id' : val2.layer,
						'new_id' : new_id
					};
					val2.layer = new_id;
					
					j++;
				}
			});
		}
		// add component with other settings
		add_component(val.component_type, val);
	});
	
	// append items to grid
	 $.each(data, function(key,val) {
		if (val.component_type == 'grid') {
			if (val.grid&&val.layer_id) {
				$.each(val.grid, function (key2, layer) {
					var target_id = $(select.component.id(layer));
					if (target_id.length > 0) {
						//$(select.component.id(val.layer_id)).trigger("drop", [{},{draggable : target_id}]);
						
						target_id
							.data('grid', true)
							.css({
								position : 'static',
								minHeight : 10
							}).resizable('destroy');
						$(select.component.id(val.layer_id)).append(target_id);
					}
				});
			}
		}
	});
	
	recalc_workspace();
}

// add selected component to workspace
function add_component(component_type, settings, event) {
	var settings = settings || false,
		event = event || false;
	// Get tool component settings.
	if (!settings) {
		var css = components[component_type].css;
		css['left'] = event.pageX - $("#workspace").offset().left - tool_settings.component.width / 2;
		css['top'] = event.pageY - $("#workspace").offset().top - tool_settings.component.height / 2;
		
		settings = {
			'component_type' : component_type,
			'layer_name' : components[component_type].text + "-" + (global_settings.component_zIndex-499),
			'css' : jQuery.extend(true, {}, css)
		}
	}
	
	var item = $("#component_"+settings.component_type).clone(),
		id = Math.floor(Math.random()*9999999) + 100000;
	if (settings.layer_id) {
		id = settings.layer_id;
	}
	item.removeAttr('id').addClass('component_'+settings.component_type);
	item.removeClass('tool-component').addClass('component');
	
	// Blog_posts - Component
	if (settings.component_type == 'blog_posts') {
		if (settings.css['min-height']) {
			settings['css']['height'] = (settings['css']['min-height'] == 'auto') ? '300px' : settings['css']['min-height'];
			delete settings['css']['min-height'];
		}
	}	
	
	// Set data
	item.data('component_type', settings.component_type);
	item.data('layer_id', id);
	item.data('css', settings.css);
	if (settings.user_events) {
		item.data('user_events', settings.user_events);
	}
	
	// Put to layer
	$("<div class=\"layer\"><a>X</a>"+settings.layer_name+"</div>")
		.attr('layer_id', id)
		.data('layer_id', id)
		.prependTo("#tool-section-layer .tool-section-content");
		
	
	// Text - Component
	if (settings.component_type == 'text') {
		if (settings.text) {
			item.text(settings.text);
		}
	}
	
	// Image - Component
	if (settings.component_type == 'image') {
		if (settings.image) {
			var img = $("<img/>");
			img.load(function () {
					item.height(this.height)
					item.width(this.width);

					$(this).data('height', this.height)
						.data('width', this.width)
						.css({width: "100%", height: "100%"});
				})
				.attr('src','http://'+settings.image.replace('http://',''));
			item.html(img);
		}
	}
	
	// list - Component
	if (settings.component_type == 'list') {
		if (settings.list) {
			item.data('list', settings.list);
			
			var html = "<ul>";
			$.each(settings.list, function (key, val) {
				html += "<li><a href='" + val['url'] + "'>" + val['name'] + "</a></li>";
			});
			html += "</ul>";
			item.html(html);
			
			item.find('ul').sortable({
				connectWith: '.component ul',
				placeholder: 'ui-state-highlight'
			});
		}
	}
	
	// grid - Component
	if (settings.component_type == 'grid') {
		item.html("").droppable({
			accept: '.component',
			drop: function(event, ui) {
				var component_item = ui.draggable;
				component_item.data('grid', true)
					.css({
						position : 'static',
						minHeight : 10,
						height : 'auto', 
						width : $(this).width()
					}).resizable('destroy');
				$(this).append(component_item);
			},
			greedy: true
		});
		
		/*.sortable({
			connectWith: '.component_grid',
			items: '> .component',			
			placeholder: 'ui-state-highlight'
		})*/
	}
	
	// Attach to workspace
	item
		.attr({'layer_id': id})
		.css(settings.css)
		.css({
			position : 'absolute',
			'z-index' : global_settings.component_zIndex++
		})
		.appendTo($("#workspace"))
		.animate({height: settings.css.height, width: settings.css.width})
		.resizable(handle_settings.component.resizable)
		.draggable({
			//connectToSortable: '.component_grid',
			cancel: 'ul',
			containment: '#worksheet',
			snap: true,
			snapTolerance: 10,
			distance: 40,
			opacity: 0.3,
			zIndex: 1500,
			start: function() { 
				$(this).click();
			}
		});
}