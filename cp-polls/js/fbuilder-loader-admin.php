<?php 
header('Content-Type: application/x-javascript; charset=UTF-8'); 
?>

fbuilderjQuery = (typeof fbuilderjQuery != 'undefined' ) ? fbuilderjQuery : jQuery;
fbuilderjQuery(function(){
(function($) {
	// Namespace of fbuilder
	$.fbuilder = $.fbuilder || {};
	$.fbuilder[ 'objName' ] = 'fbuilderjQuery';
	
	$.fbuilder[ 'typeList' ] = [];
	$.fbuilder[ 'categoryList' ] = [];
	$.fbuilder[ 'controls' ] = {};
	$.fbuilder[ 'htmlEncode' ] = function(value)
	{
		value = $('<div/>').text(value).html()
		value = value.replace(/"/g, "&quot;");
		return value;
	};
	
	$.fbuilder[ 'escape_symbol' ] = function( value ) // Escape the symbols used in regulars expressions
	{
		return value.replace(/([\^\$\-\.\,\[\]\(\)\/\\\*\?\+\!\{\}])/g, "\\$1");
	};
				
	$.fbuilder[ 'parseVal' ] = function( value, thousandSeparator, decimalSymbol )
	{
		if( value == '' ) return 0;
		value += '';
		
		thousandSeparator = new RegExp( $.fbuilder.escape_symbol( ( typeof thousandSeparator == 'undefined' ) ? ',' : thousandSeparator ), 'g' );
		decimalSymbol = new RegExp( $.fbuilder.escape_symbol( ( typeof decimalSymbol == 'undefined' ) ? '.' : decimalSymbol ), 'g' );
		
		var t = value.replace( thousandSeparator, '' ).replace( decimalSymbol, '.' ).replace( /\s/g, '' ),
			p = /[+-]?((\d+(\.\d+)?)|(\.\d+))/.exec( t );
			
		return ( p ) ? p[0]*1 : '"' + value.replace(/'/g, "\\'").replace( /\$/g, '') + '"';
	};
	
	// fbuilder plugin
	$.fn.fbuilder = function(options){
		var opt = $.extend({},
				{
	   				pub:false,
					identifier:"",
					title:""
				},
				options, true),
			typeList = 	$.fbuilder.typeList,
			categoryList = $.fbuilder.categoryList;
			
		$.fbuilder[ 'getNameByIdFromType' ] = function( id )
			{
				for ( var i = 0, h = typeList.length; i < h; i++ )
				{
					if ( typeList[i].id == id )
					{
						return  typeList[i].name;
					}	
				}		
				return "";
			};
		
		for ( var i=0, h = typeList.length; i < h; i++ )
		{
			var category_id = typeList[ i ].control_category;
			
			if( typeof categoryList[ category_id ]  == 'undefined' )
			{
				categoryList[ category_id ] = { title : '', description : '', typeList : [] };
			}
			else if( typeof categoryList[ category_id ][ 'typeList' ]  == 'undefined' )
			{
				categoryList[ category_id ][ 'typeList' ] = [];
			}
			
			categoryList[ category_id ].typeList.push( i );
		}

		for ( var i in categoryList )
		{
			$("#tabs-1").append('<div style="clear:both;"></div><div>'+categoryList[ i ].title+'</div><hr />');
			if( typeof categoryList[ i ][ 'description' ] != 'undefined' && !/^\s*$/.test( categoryList[ i ][ 'description' ] ) )
			{
				$("#tabs-1").append('<div style="clear:both;"></div><div class="category-description">'+categoryList[ i ].description+'</div>');
			}
			
			for( var j = 0, k = categoryList[ i ].typeList.length; j < k; j++ )
			{
				var index = categoryList[ i ].typeList[ j ];
				$("#tabs-1").append('<div class="button '+((j!=0)?"n":"itemForm")+'  width40" id="'+typeList[ index ].id+'">'+typeList[ index ].name+'</div>');
			}
		}
		
		$("#tabs-1").append('<div class="clearer"></div>');
		$( ".button").button();
		
		// Create a items object
		var items = [],
			selected = -3;
			
		$.fbuilder[ 'editItem' ] = function( id ) 
			{		    
				$('#tabs').tabs("option", "active", 1);
				try 
				{ 
					$('#tabs-2').html( items[id].showAllSettings() ); 
				} catch (e) {}
				selected = id;
				items[id].editItemEvents();
			};
			
		$.fbuilder[ 'removeItem' ] = function( index ) 
			{
				items.splice(index,1);
				for ( var i=0, h = items.length; i<h; i++ )
				{
					items[i].index = i;
				}
				
				$('#tabs').tabs("option", "active", 0);
				$.fbuilder.reloadItems();
			};
			
		$.fbuilder[ 'duplicateItem' ] = function( index ) 
			{
				var n = 0;
				for ( var i=0, h = items.length; i<h; i++ )
				{
				   n1 = parseInt( items[i].name.replace( /fieldname/g,"" ) );
				   if (n1>n)
					   n = n1;
				}
				items.splice( index*1+1, 0, $.extend( true, {}, items[index], { name:"fieldname"+(n+1) } ) );
				for ( var i=index*1+1, h = items.length; i<h; i++ )
				{
					items[i].index = i;
				}	
				$('#tabs').tabs("option", "active", 0);
				$.fbuilder.reloadItems();
			}
		
		$.fbuilder[ 'editForm' ] = function() 
			{
				$('#tabs-3').html(theForm.showAllSettings());
				selected = -1;
				$("#fTitle").keyup(function()
				{
					theForm.title = $(this).val();
					$.fbuilder.reloadItems();
				});
				
				$("#fDescription").keyup(function()
				{
					theForm.description = $(this).val();
					$.fbuilder.reloadItems();
				});
				
				$("#fLayout").change(function()
				{
					theForm.formlayout = $(this).val();
					$.fbuilder.reloadItems();
				});
				
				$("#fTemplate").change(function()
				{
					theForm.formtemplate = $(this).val();
					var template 	= $.fbuilder.showSettings.formTemplateDic[ theForm.formtemplate ],
						thumbnail	= '',
						description = '';

					if( typeof template != 'undefined' )
					{
						if( typeof template[ 'thumbnail' ] != 'undefined' )
						{
							thumbnail = '<img src="' + template[ 'thumbnail' ] + '">';
						}
						if( typeof template[ 'description' ] != 'undefined' )
						{
							description = template[ 'description' ];
						}
					}
					$( '#fTemplateThumbnail' ).html( thumbnail );
					$( '#fTemplateDescription' ).html( description );
					$.fbuilder.reloadItems();
				});
			};
		
		$.fbuilder[ 'reloadItems' ] = function() 
			{
				for ( var i=0, h = $.fbuilder.showSettings.formlayoutList.length; i < h; i++ )
				{
					$("#fieldlist"+opt.identifier).removeClass( $.fbuilder.showSettings.formlayoutList[i].id );
				}	
				$("#fieldlist"+opt.identifier).addClass(theForm.formlayout);
				$("#formheader"+opt.identifier).html(theForm.display());
				$("#fieldlist"+opt.identifier).html("");
				if ( parseInt( selected ) == -1 )
				{
					$(".fform").addClass("ui-selected");
				}
				else
				{
					$(".fform").removeClass("ui-selected");
				}
				
				for ( var i=0, h = items.length; i < h; i++ )
				{
					items[i].index = i;
					$("#fieldlist"+opt.identifier).append(items[i].display());
					if ( i == selected )
					{
						$("#field"+opt.identifier+"-"+i).addClass("ui-selected");
					}	
					else
					{
						$("#field"+opt.identifier+"-"+i).removeClass("ui-selected");
					}	
					$("#field"+opt.identifier+"-"+i+" .remove").click(function()
						{
							$.fbuilder[ 'removeItem' ]($(this).parent().attr("id").replace("field"+opt.identifier+"-",""));
						});
						
					$("#field"+opt.identifier+"-"+i+" .copy").click(function()
						{
							$.fbuilder[ 'duplicateItem' ]($(this).parent().attr("id").replace("field"+opt.identifier+"-",""));
						});
						
				}
				if (items.length>0)
				{
				    $(".fields").mouseover(function() 
						{
							$(this).addClass("ui-over");
						})
						.mouseout(function()
						{
							$(this).removeClass("ui-over")
						})
						.click(function()
						{
							$.fbuilder[ 'editItem' ]($(this).attr("id").replace("field"+opt.identifier+"-",""));
							$(this).siblings().removeClass("ui-selected");
							$(this).addClass("ui-selected");
						});
					$(".field").focus(function()
						{
							$(this).blur();
						});	
				}
				
				$(".fform").mouseover(function() 
					{
						$(this).addClass("ui-over");
					})
					.mouseout(function()
					{
						$(this).removeClass("ui-over");
					})
					.click(function(){
						$('#tabs').tabs("option", "active", 2);
						$.fbuilder.editForm();
						$(this).siblings().removeClass("ui-selected");
						$(this).addClass("ui-selected");
					});
					
				ffunct.saveData("form_structure");
				
				//email list
				var str = "";
				for ( var i=0, h = items.length; i < h; i++ )
				{
					var item = items[ i ];
					if (item.ftype=="femail")
					{
						str += '<option value="'+item.name+'" '+((item.name == $('#cu_user_email_field').attr("def"))?"selected":"")+'>'+item.name+' ('+item.title+')'+'</option>';
					}
				}
				
				$('#cu_user_email_field').html(str);
				//field list for paypal request
				if (($('#request_cost').length > 0) && ($('#request_cost').is('select')))
				{
					var str = "";
					for (var i=0, h = items.length; i<h; i++)
					{
						var item = items[ i ];
						str += '<option value="'+item.name+'" '+((item.name == $('#request_cost').attr("def"))?"selected":"")+'>'+item.name+'('+(item.title)+')</option>';
					}	
					$('#request_cost').html(str);
				}
				
				//request amount list
				if ($('#paypal_price_field').length > 0)
				{
					var str = '<option value="" '+(('' == $('#paypal_price_field').attr("def"))?"selected":"")+'> ---- No ---- </option>';
					for (var i=0, h = items.length; i < h; i++)
					{
						var item = items[ i ];
						str += '<option value="'+item.name+'" '+((item.name == $('#paypal_price_field').attr("def"))?"selected":"")+'>'+(item.title)+'</option>';
					}		
					$('#paypal_price_field').html(str);
				}
			};
		
		var fform=function(){};
		$.extend(fform.prototype,
			{
				title:"Untitled Form",
				description:"This is my form. Please fill it out. It's awesome!",
				formlayout:"top_aligned",
				formtemplate:"",
				display:function()
				{
					return '<div class="fform" id="field"><div class="arrow ui-icon ui-icon-play "></div><h1>'+this.title+'</h1><span>'+this.description+'</span></div>';
				},
				
				showAllSettings:function()
				{
					var layout 	    = '',
						template    = '<option value="">Use default template</option>',
						thumbnail   = '',
						description = '',
						selected    = '';
						
					for ( var i = 0; i< $.fbuilder.showSettings.formlayoutList.length; i++ )
					{
						layout += '<option value="'+$.fbuilder.showSettings.formlayoutList[i].id+'" '+(($.fbuilder.showSettings.formlayoutList[i].id==this.formlayout)?"selected":"")+'>'+$.fbuilder.showSettings.formlayoutList[i].name+'</option>';
					}	

					for ( var i in $.fbuilder.showSettings.formTemplateDic )
					{
						selected = '';
						if( $.fbuilder.showSettings.formTemplateDic[i].prefix==this.formtemplate )
						{
							selected = 'SELECTED';
							if( typeof $.fbuilder.showSettings.formTemplateDic[i].thumbnail != 'undefined' )
							{
								thumbnail = '<img src="'+$.fbuilder.showSettings.formTemplateDic[i].thumbnail+'">'; 
							}
							
							if( typeof $.fbuilder.showSettings.formTemplateDic[i].description != 'undefined' )
							{
								description = $.fbuilder.showSettings.formTemplateDic[i].description; 
							}
						}
						
						template += '<option value="'+$.fbuilder.showSettings.formTemplateDic[i].prefix+'" ' + selected + '>'+$.fbuilder.showSettings.formTemplateDic[i].title+'</option>';
					}	
					
					return '<div><label>Form Name</label><input class="large" name="fTitle" id="fTitle" value="'+$.fbuilder.htmlEncode(this.title)+'" /></div><div><label>Description</label><textarea class="large" name="fDescription" id="fDescription">'+this.description+'</textarea></div><div><label>Label Placement</label><br /><select name="fLayout" id="fLayout" class="large">'+layout+'</select></div><div><label>Form Template</label><br /><select name="fTemplate" id="fTemplate" class="large">'+template+'</select></div><br /><div><span id="fTemplateThumbnail" style="float:left;padding-right:10px;">'+thumbnail+'</span><span  id="fTemplateDescription" style="float:left;">'+description+'</span></div>' ;
				}
			}
		);
		
		var theForm = new fform();
		$("#fieldlist"+opt.identifier).sortable(
			{
			    start: function(event, ui) 
				{
				   var start_pos = ui.item.index();
				   ui.item.data('start_pos', start_pos);
			    },
			    stop: function(event, ui) 
				{
				    var end_pos = parseInt(ui.item.index()),
						start_pos = parseInt(ui.item.data('start_pos')),
						tmp = items[start_pos];
						
				    if (end_pos > start_pos)
				    {
					    for (var i = start_pos; i<end_pos; i++)
						{
						   items[i] = items[i+1];
						}   
				   }
				   else
				   {
					    for (var i = start_pos; i > end_pos; i--)
					    {
						   items[i] = items[i-1];
						}   
				   }
				   items[end_pos] = tmp;
				   $.fbuilder.reloadItems();
			    }
			}   
		);
		
		$('#tabs').tabs(
			{
				activate: function(event, ui) 
					{
					   if ($(this).tabs( "option", "active" )!=1)
					   {
							$(".fields").removeClass("ui-selected");
							selected = -2;
							if ($(this).tabs( "option", "active" )==2)
							{
							   $(".fform").addClass("ui-selected");
							   $.fbuilder.editForm();
							}
							else
							{
							   $(".fform").removeClass("ui-selected");
							} 
					   }
					   else
					   {
							$(".fform").removeClass("ui-selected");
							if (selected < 0)
							{
							   $('#tabs-2').html('<b>No Field Selected</b><br />Please click on a field in the form preview on the right to change its properties.');
							}   
					   }
					}	   
			}		
		);
		
	    var ffunct = {
	        getItems: function() 
			{
			   return items;
		    },
		    addItem: function(id) 
			{
			    var obj = eval("new $.fbuilder.controls['"+id+"']();"),
					fBuild = this,
					n = 0;
			    
				obj.init();
			    
			    for (var i=0, h = items.length; i < h; i++)
			    {
		 		    n1 = parseInt(items[i].name.replace(/fieldname/g,""));
	 			    if (n1>n)
					{
					   n = n1;
					}   
 			    }
				obj.fBuild = fBuild;
			    $.extend(obj,{name:"fieldname"+(n+1)});
			    items[items.length] = obj;
			    $.fbuilder.reloadItems();
		    },
		    saveData:function(f)
			{
			   $("#"+f).val("["+ $.stringifyXX( items, false )+",["+ $.stringifyXX(theForm,false)+"]]");
		    },
		    loadData:function(form_structure, available_templates)
			{
		        var structure = $.parseJSON( $("#"+form_structure).val() ), // JSON data
					templates = ( typeof available_templates == 'undefined' ) ? null : $.parseJSON( $("#"+available_templates).val() ),
					fBuild = this;

			    if ( structure )
				{
					if (structure.length==2)
					{
						items = [];
						for (var i=0;i<structure[0].length;i++)
						{
						   var obj = eval("new $.fbuilder.controls['"+structure[0][i].ftype+"']();");
						   obj = $.extend( true, {}, obj, structure[0][i] );
						   obj.name = obj.name+opt.identifier;
						   obj.form_identifier = opt.identifier;
						   obj.fBuild = fBuild;
						   items[items.length] = obj;
						}
						theForm = new fform();
						theForm = $.extend(theForm,structure[1][0]);
						$.fbuilder.reloadItems();
					}
				}
				
				if( templates )
				{
					$.fbuilder.showSettings.formTemplateDic = templates;
				}
		    },
		    removeItem: $.fbuilder[ 'removeItem' ],
		    editItem:   $.fbuilder[ 'editItem' ]
	    }
	   
	    this.fBuild = ffunct;
	    return this;
	};

    $.fbuilder[ 'showSettings' ] = {
		sizeList:new Array({id:"small",name:"Small"},{id:"medium",name:"Medium"},{id:"large",name:"Large"}),
		layoutList:new Array({id:"one_column",name:"One Column"},{id:"two_column",name:"Two Column"},{id:"three_column",name:"Three Column"},{id:"side_by_side",name:"Side by Side"}),
		formlayoutList:new Array({id:"top_aligned",name:"Top Aligned"},{id:"left_aligned",name:"Left Aligned"},{id:"right_aligned",name:"Right Aligned"}),
		formTemplateDic: {}, // Form Template dictionary
		showTitle: function(f,v) 
		{
			var str = '<label>Field Label</label><textarea class="large" name="sTitle" id="sTitle">'+v+'</textarea>';
			if (v=="Page Break") str = "";
			return '<label>Field Type: '+$.fbuilder[ 'getNameByIdFromType' ](f)+'</label><br /><br />'+str;
		},
		showName: function(v1,v2) 
		{
			return '<div><label>Short label (optional) [<a class="helpfbuilder" text="The short label is used at title for the column when exporting the form data to CSV files.\n\nIf the short label is empty then, the field label will be used for the CSV file.">help?</a>] :</label><input class="large" name="sShortlabel" id="sShortlabel" value="'+v2+'" /></div>'+
				   '<div><label>Field tag for the message (optional):</label><input readonly="readonly" class="large" name="sNametag" id="sNametag" value="&lt;%'+v1+'%&gt;" />'+
				   '<input style="display:none" readonly="readonly" class="large" name="sName" id="sName" value="'+v1+'" /></div>';
		},
		showPredefined: function(v,c) 
		{
			return '<div><label>Predefined Value</label><textarea class="large" name="sPredefined" id="sPredefined">'+v+'</textarea><br /><input type="checkbox" name="sPredefinedClick" id="sPredefinedClick" '+((c)?"checked":"")+' value="1" > Hide predefined value on click.</div>';
		},
		showEqualTo: function(v,name) 
		{
			return '<div><label>Equal to [<a class="helpfbuilder" text="Use this field to create password confirmation field or email confirmation fields.\n\nSpecify this setting ONLY into the confirmation field, not in the original field.">help?</a>]</label><br /><select class="equalTo" name="sEqualTo" id="sEqualTo" dvalue="'+v+'" dname="'+name+'"></select></div>';
		},
		showRequired: function(v) 
		{
			return '<div><input type="checkbox" name="sRequired" id="sRequired" '+((v)?"checked":"")+'><label>Required</label></div>';
		},
		showSize: function(v) 
		{
			var str = "";
			for (var i=0;i<this.sizeList.length;i++)
			{
				str += '<option value="'+this.sizeList[i].id+'" '+((this.sizeList[i].id==v)?"selected":"")+'>'+this.sizeList[i].name+'</option>';
			}	
			return '<label>Field Size</label><br /><select name="sSize" id="sSize">'+str+'</select>';
		},
		showLayout: function(v) 
		{
			var str = "";
			for (var i=0;i<this.layoutList.length;i++)
			{
				str += '<option value="'+this.layoutList[i].id+'" '+((this.layoutList[i].id==v)?"selected":"")+'>'+this.layoutList[i].name+'</option>';
			}	
			return '<label>Field Layout</label><br /><select name="sLayout" id="sLayout">'+str+'</select>';
		},
		showUserhelp: function(v,c) 
		{
			return '<div><label>Instructions for User</label><textarea class="large" name="sUserhelp" id="sUserhelp">'+v+'</textarea><br /><input type="checkbox" name="sUserhelpTooltip" id="sUserhelpTooltip" '+((c)?"checked":"")+' value="1" > Show as floating tooltip.</div>';
		},
		showCsslayout: function(v) 
		{
			return '<label>Additional CSS Class</label><input class="large" name="sCsslayout" id="sCsslayout" value="'+v+'" />';
		}
	};
	
	$.fbuilder.controls[ 'ffields' ] = function(){};
	$.extend( $.fbuilder.controls[ 'ffields' ].prototype, 
		{
			form_identifier:"",
			name:"",
			shortlabel:"",
			index:-1,
			ftype:"",
			userhelp:"",
			userhelpTooltip:false,
			csslayout:"",
			init:function(){},
			editItemEvents:function()
			{
				$("#sTitle").bind("keyup", {obj: this}, function(e) 
					{
						var str = $(this).val();
						e.data.obj.title = str.replace(/\n/g,"<br />");
						$.fbuilder.reloadItems();
					});
					
				$("#sShortlabel").bind("keyup", {obj: this}, function(e) 
					{
						e.data.obj.shortlabel = $(this).val();
						$.fbuilder.reloadItems();
					});
					
				$("#sPredefined").bind("keyup", {obj: this}, function(e) 
					{
						e.data.obj.predefined = $(this).val();
						$.fbuilder.reloadItems();
					});
					
				$("#sPredefinedClick").bind("click", {obj: this}, function(e) 
					{
						e.data.obj.predefinedClick = $(this).is(':checked');
						$.fbuilder.reloadItems();
					});
					
				$("#sRequired").bind("click", {obj: this}, function(e) 
					{
						e.data.obj.required = $(this).is(':checked');
						$.fbuilder.reloadItems();
					});
					
				$("#sUserhelp").bind("keyup", {obj: this}, function(e) 
					{
						e.data.obj.userhelp = $(this).val();
						$.fbuilder.reloadItems();
					});
					
				$("#sUserhelpTooltip").bind("click", {obj: this}, function(e) 
					{
						e.data.obj.userhelpTooltip = $(this).is(':checked');
						$.fbuilder.reloadItems();
					});
					
				$("#sCsslayout").bind("keyup", {obj: this}, function(e) 
					{
						e.data.obj.csslayout = $(this).val();
						$.fbuilder.reloadItems();
					});
					
				$(".helpfbuilder").click(function()
					{
						alert($(this).attr("text"));
					});
			},
			
			showSpecialData:function()
			{
				if(typeof this.showSpecialDataInstance != 'undefined')
				{
					return this.showSpecialDataInstance();
				}	
				else
				{
					return "";
				}	
			},
			
			showEqualTo:function()
			{
				if(typeof this.equalTo != 'undefined')
				{
					return $.fbuilder.showSettings.showEqualTo(this.equalTo,this.name);
				}	
				else
				{
					return "";
				}	
			},
			
			showPredefined:function()
			{
				if(typeof this.predefined != 'undefined')
				{
					return $.fbuilder.showSettings.showPredefined(this.predefined,this.predefinedClick);
				}	
				else
				{
					return "";
				}	
			},
			
			showRequired:function()
			{
				if(typeof this.required != 'undefined')
				{
					return $.fbuilder.showSettings.showRequired(this.required);
				}	
				else
				{
					return "";
				}	
			},
			
			showSize:function()
			{
				if(typeof this.size != 'undefined')
				{
					return $.fbuilder.showSettings.showSize(this.size);
				}	
				else
				{
					return "";
				}	
			},
			
			showLayout:function()
			{
				if(typeof this.layout != 'undefined')
				{
					return $.fbuilder.showSettings.showLayout(this.layout);
				}	
				else
				{
					return "";
				}	
			},
			
			showRange:function()
			{
				if(typeof this.min != 'undefined')
				{
					return this.showRangeIntance();
				}	
				else
				{
					return "";
				}	
			},
			
			showFormat:function()
			{
				if(typeof this.dformat != 'undefined')
				{
					try 
					{
						return this.showFormatIntance();
					} catch(e) {return "";}
				}	
				else
				{
					return "";
				}	
			},
			
			showChoice:function()
			{
				if(typeof this.choices != 'undefined')
				{
					return this.showChoiceIntance();
				}	
				else
				{
					return "";
				}	
			},
			
			showUserhelp:function()
			{
				return ((this.ftype!="fPageBreak") ? $.fbuilder.showSettings.showUserhelp(this.userhelp,this.userhelpTooltip) : "");
			},
			
			showCsslayout:function()
			{
				return ((this.ftype!="fPageBreak") ? $.fbuilder.showSettings.showCsslayout(this.csslayout) : "");
			},
			
			showAllSettings:function()
			{
				return this.showTitle()+this.showName()+this.showSize()+this.showLayout()+this.showFormat()+this.showRange()+this.showRequired()+this.showSpecialData()+this.showEqualTo()+this.showPredefined()+this.showChoice()+this.showUserhelp()+this.showCsslayout();
			},
			
			showTitle:function()
			{
				return $.fbuilder.showSettings.showTitle(this.ftype,this.title);
			},
			
			showName:function()
			{
				return ((this.ftype!="fPageBreak") ? $.fbuilder.showSettings.showName(this.name,this.shortlabel) : "");
			},
			
			display:function()
			{
				return 'Not available yet';
			},
			
			show:function(){
				return 'Not available yet';
			}
		}
	);$.fbuilder.categoryList[1]={
		title : "Form Controls",
		description : "",
		typeList : []
	};
		$.fbuilder.typeList.push(
			{
				id:"fradio",
				name:"Radio Buttons",
				control_category:1
			}
		);
		$.fbuilder.controls[ 'fradio' ] = function(){};
		$.extend(
			$.fbuilder.controls[ 'fradio' ].prototype,
			$.fbuilder.controls[ 'ffields' ].prototype,
			{
				title:"Select a Choice",
				ftype:"fradio",
				layout:"one_column",
				required:false,
				choiceSelected:"",
				showDep:false,
				init:function()
					{
						this.choices = new Array("First Choice","Second Choice","Third Choice");
						this.choicesVal = new Array("First Choice","Second Choice","Third Choice");
						this.choicesDep = new Array(new Array(),new Array(),new Array());
					},
				display:function()
					{
						this.choicesVal = ((typeof(this.choicesVal) != "undefined" && this.choicesVal !== null)?this.choicesVal:this.choices.slice(0));
						var str = "";
						for (var i=0;i<this.choices.length;i++)
						{
							str += '<div class="'+this.layout+'"><input class="field" disabled="true" type="radio" i="'+i+'"  '+(( this.choices[i]+' - '+this.choicesVal[i]==this.choiceSelected)?"checked":"")+'/> '+$.fbuilder.htmlEncode(this.choices[i])+'</div>';
						}	
						return '<div class="fields" id="field'+this.form_identifier+'-'+this.index+'"><div class="arrow ui-icon ui-icon-play "></div><div title="Delete" class="remove ui-icon ui-icon-trash "></div><div title="Duplicate" class="copy ui-icon ui-icon-copy "></div><label>'+$.fbuilder.htmlEncode(this.title)+''+((this.required)?"*":"")+'</label><div class="dfield">'+str+'<span class="uh">'+$.fbuilder.htmlEncode(this.userhelp)+'</span></div><div class="clearer"></div></div>';
					},
				editItemEvents:function()
					{
						$(".choice_text").bind("keyup", {obj: this}, function(e) 
							{
								if (e.data.obj.choices[$(this).attr("i")] == e.data.obj.choicesVal[$(this).attr("i")])
								{
									$("#"+$(this).attr("id")+"V"+$(this).attr("i")).val($(this).val());
									e.data.obj.choicesVal[$(this).attr("i")]= $(this).val();
								}
								e.data.obj.choices[$(this).attr("i")]= $(this).val();
								$.fbuilder.reloadItems();
							});
						$(".choice_value").bind("keyup", {obj: this}, function(e) 
							{
								e.data.obj.choicesVal[$(this).attr("i")]= $(this).val();
								$.fbuilder.reloadItems();
							});
						$(".choice_radio").bind("click", {obj: this}, function(e) 
							{
								if ($(this).is(':checked'))
								{
									e.data.obj.choiceSelected = e.data.obj.choices[$(this).attr("i")] + ' - ' + e.data.obj.choicesVal[$(this).attr("i")];
								}	
								$.fbuilder.reloadItems();
							});
						$("#sLayout").bind("change", {obj: this}, function(e) 
							{
								e.data.obj.layout = $(this).val();
								$.fbuilder.reloadItems();
							});
						$(".choice_up").bind("click", {obj: this}, function(e) 
							{
								var i = $(this).attr("i")*1;
								if (i!=0)
								{
									e.data.obj.choices.splice(i-1, 0, e.data.obj.choices.splice(i, 1)[0]);
									e.data.obj.choicesVal.splice(i-1, 0, e.data.obj.choicesVal.splice(i, 1)[0]);
									e.data.obj.choicesDep.splice(i-1, 0, e.data.obj.choicesDep.splice(i, 1)[0]);
								}
								$.fbuilder.editItem(e.data.obj.index);
								$.fbuilder.reloadItems();
							});
						$(".choice_down").bind("click", {obj: this}, function(e) 
							{
								var i = $(this).attr("i")*1;
								var n = $(this).attr("n")*1;
								if (i!=n)
								{
									e.data.obj.choices.splice(i, 0, e.data.obj.choices.splice(i+1, 1)[0]);
									e.data.obj.choicesVal.splice(i, 0, e.data.obj.choicesVal.splice(i+1, 1)[0]);
									e.data.obj.choicesDep.splice(i, 0, e.data.obj.choicesDep.splice(i+1, 1)[0]);
								}
								$.fbuilder.editItem(e.data.obj.index);
								$.fbuilder.reloadItems();
							});
						$(".choice_removeDep").bind("click", {obj: this}, function(e) 
							{
								if (e.data.obj.choices.length==1)
								{
									e.data.obj.choicesDep[$(this).attr("i")][0]="";
								}	
								else
								{
									e.data.obj.choicesDep[$(this).attr("i")].splice($(this).attr("j"),1);
								}	
								$.fbuilder.editItem(e.data.obj.index);
								$.fbuilder.reloadItems();
							});
						$(".choice_addDep").bind("click", {obj: this}, function(e) 
							{
								e.data.obj.choicesDep[$(this).attr("i")].splice($(this).attr("j")*1+1,0,"");
								$.fbuilder.editItem(e.data.obj.index);
								$.fbuilder.reloadItems();
							});
						$(".choice_remove").bind("click", {obj: this}, function(e) 
							{
								var i = $(this).attr("i");
								
								if( e.data.obj.choices[ i ] + ' - ' + e.data.obj.choicesVal[ i ] == e.data.obj.choiceSelected )
								{
									e.data.obj.choiceSelected = "";
								}
								
								if (e.data.obj.choices.length==1)
								{
									e.data.obj.choices[0]="";
									e.data.obj.choicesVal[0]="";
									e.data.obj.choicesDep[0]=new Array();
								}
								else
								{
									e.data.obj.choices.splice(i,1);
									e.data.obj.choicesVal.splice(i,1);
									e.data.obj.choicesDep.splice(i,1);
								}
								$.fbuilder.editItem(e.data.obj.index);
								$.fbuilder.reloadItems();
							});
						$(".choice_add").bind("click", {obj: this}, function(e) 
							{
								var i = $(this).attr("i")*1+1;
								
								e.data.obj.choices.splice(i,0,"");
								e.data.obj.choicesVal.splice(i,0,"");
								e.data.obj.choicesDep.splice(i,0,new Array());
								$.fbuilder.editItem(e.data.obj.index);
								$.fbuilder.reloadItems();
							});
						$(".showHideDependencies").bind("click", {obj: this}, function(e) 
							{
								if (e.data.obj.showDep)
								{
									$(this).parent().removeClass("show");
									$(this).parent().addClass("hide");
									$(this).html("Show Dependencies");
									e.data.obj.showDep = false;
								}
								else
								{
									$(this).parent().addClass("show");
									$(this).parent().removeClass("hide");
									$(this).html("Hide Dependencies");
									e.data.obj.showDep = true;
								}
								return false;
							});
						$('.dependencies').bind("change", {obj: this}, function(e) 
							{
								e.data.obj.choicesDep[$(this).attr("i")][$(this).attr("j")] = $(this).val();
								$.fbuilder.reloadItems();
							});
						var items = this.fBuild.getItems();
						$('.dependencies').each(function()
							{
								var str = '<option value="" '+(("" == $(this).attr("dvalue"))?"selected":"")+'></option>';
								for (var i=0;i<items.length;i++)
								{
									if (items[i].name != $(this).attr("dname"))
									{
										str += '<option value="'+items[i].name+'" '+((items[i].name == $(this).attr("dvalue"))?"selected":"")+'>'+(items[i].name)+' (' + items[i].title + ') </option>';
									}
								}	
								$(this).html(str);
							});
						$.fbuilder.controls[ 'ffields' ].prototype.editItemEvents.call(this);
					},				
				showChoiceIntance: function() 
					{
						this.choicesVal = ((typeof(this.choicesVal) != "undefined" && this.choicesVal !== null)?this.choicesVal:this.choices.slice(0));
						var l = this.choices;
						var lv = this.choicesVal;
						if (!(typeof(this.choicesDep) != "undefined" && this.choicesDep !== null))
						{
							this.choicesDep = new Array();
							for (var i=0;i<l.length;i++)
							{
								this.choicesDep[i] = new Array();
							}	
						}
						var d = this.choicesDep;
						var str = "";
						for (var i=0;i<l.length;i++)
						{
							str += '<div class="choicesEdit"><input class="choice_radio" i="'+i+'" type="radio" '+((this.choiceSelected==l[i]+' - '+lv[i])?"checked":"")+' name="choice_radio" /><input class="choice_text" i="'+i+'" type="text" name="sChoice'+this.name+'" id="sChoice'+this.name+'" value="'+$.fbuilder.htmlEncode(l[i])+'"/><input class="choice_value" i="'+i+'" type="text" name="sChoice'+this.name+'V'+i+'" id="sChoice'+this.name+'V'+i+'" value="'+$.fbuilder.htmlEncode(lv[i])+'"/><a class="choice_down ui-icon ui-icon-arrowthick-1-s" i="'+i+'" n="'+(l.length-1)+'" title="Down"></a><a class="choice_up ui-icon ui-icon-arrowthick-1-n" i="'+i+'" title="Up"></a><a class="choice_add ui-icon ui-icon-circle-plus" i="'+i+'" title="Add another choice."></a><a class="choice_remove ui-icon ui-icon-circle-minus" i="'+i+'" title="Delete this choice."></a></div>';
							for (var j=0;j<d[i].length;j++)
							{
								str += '<div class="choicesEditDep">If selected show: <select class="dependencies" i="'+i+'" j="'+j+'" dname="'+this.name+'" dvalue="'+d[i][j]+'" ></select><a class="choice_addDep ui-icon ui-icon-circle-plus" i="'+i+'" j="'+j+'" title="Add another dependency."></a><a class="choice_removeDep ui-icon ui-icon-circle-minus" i="'+i+'" j="'+j+'" title="Delete this dependency."></a></div>';
							}	
							if (d[i].length==0)
							{
								str += '<div class="choicesEditDep">If selected show: <select class="dependencies" i="'+i+'" j="'+d[i].length+'" dname="'+this.name+'" dvalue="" ></select><a class="choice_addDep ui-icon ui-icon-circle-plus" i="'+i+'" j="'+d[i].length+'" title="Add another dependency."></a><a class="choice_removeDep ui-icon ui-icon-circle-minus" i="'+i+'" j="'+d[i].length+'" title="Delete this dependency."></a></div>';
							}	
						}
						return '<div class="choicesSet '+((this.showDep)?"show":"hide")+'"><label>Choices</label> <a class="helpfbuilder dep" text="Dependencies are used to show/hide other fields depending of the option selected in this field.">help?</a> <a href="" class="showHideDependencies">'+((this.showDep)?"Hide":"Show")+' Dependencies</a><div><div class="t">Text</div><div class="t">Value</div><div class="clearer"></div></div>'+str+'</div>';
					}
		});		$.fbuilder.typeList.push(
			{
				id:"fcurrency",
				name:"Currency",
				control_category:1
			}
		);
        $.fbuilder.controls[ 'fcurrency' ] = function(){};
		$.extend(
			$.fbuilder.controls[ 'fcurrency' ].prototype, 
			$.fbuilder.controls[ 'ffields' ].prototype,
			{
				title:"Currency",
				ftype:"fcurrency",
				predefined:"",
				predefinedClick:false,
				required:false,
				size:"small",
				readonly:false,
				currencySymbol:"$",
				currencyText:"USD",
				thousandSeparator:",",
				centSeparator:".",
				formatDynamically:false,
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
		});		$.fbuilder.typeList.push(
			{
				id:"fnumber",
				name:"Number",
				control_category:1
			}
		);
        $.fbuilder.controls[ 'fnumber' ] = function(){};
		$.extend(
			$.fbuilder.controls[ 'fnumber' ].prototype, 
			$.fbuilder.controls[ 'ffields' ].prototype,
			{
				title:"Number",
				ftype:"fnumber",
				predefined:"",
				predefinedClick:false,
				required:false,
				size:"small",
				thousandSeparator:"",
				decimalSymbol:".",
				min:"",
				max:"",
				dformat:"digits",
				formats:new Array("digits","number"),
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
		});	$.fbuilder.typeList.push(
		{
			id:"femail",
			name:"Email",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'femail'] = function(){};
	$.extend(
		$.fbuilder.controls[ 'femail' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Email",
			ftype:"femail",
			predefined:"",
			predefinedClick:false,
			required:false,
			size:"medium",
			equalTo:"",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fdate",
			name:"Date Time",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fdate' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fdate' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Date",
			ftype:"fdate",
			predefined:"",
			predefinedClick:false,
			size:"medium",
			required:false,
			dformat:"mm/dd/yyyy",
			showDropdown:false,
			dropdownRange:"-10:+10",
			
			minDate:"",
			maxDate:"",
			minHour:0,
			maxHour:23,
			minMinute:0,
			maxMinute:59,
			
			stepHour: 1,
			stepMinute: 1,
			
			showTimepicker: false,
				
			defaultDate:"",
			defaultTime:"",
			working_dates:[true,true,true,true,true,true,true],
			
			formats:new Array("mm/dd/yyyy","dd/mm/yyyy"),
			
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"ftextarea",
			name:"Text Area",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'ftextarea' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'ftextarea' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Untitled",
			ftype:"ftextarea",
			predefined:"",
			predefinedClick:false,
			required:false,
			size:"medium",
			minlength:"",
			maxlength:"",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fcheck",
			name:"Checkboxes",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fcheck' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fcheck' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Check All That Apply",
			ftype:"fcheck",
			layout:"one_column",
			required:false,
			showDep:false,
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"ftext",
			name:"Single Line Text",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'ftext' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'ftext' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Untitled",
			ftype:"ftext",
			predefined:"",
			predefinedClick:false,
			required:false,
			size:"medium",
			minlength:"",
			maxlength:"",
			equalTo:"",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fdropdown",
			name:"Dropdown",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fdropdown' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fdropdown' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Select a Choice",
			ftype:"fdropdown",
			size:"medium",
			required:false,
			choiceSelected:"",
			showDep:false,
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"ffile",
			name:"Upload File",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'ffile' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'ffile' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Untitled",
			ftype:"ffile",
			required:false,
			size:"medium",
			accept:"",
			upload_size:"",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fpassword",
			name:"Password",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fpassword' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fpassword' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Untitled",
			ftype:"fpassword",
			predefined:"",
			predefinedClick:false,
			required:false,
			size:"medium",
			minlength:"",
			maxlength:"",
			equalTo:"",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fPhone",
			name:"Phone field",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fPhone' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fPhone' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Phone",
			ftype:"fPhone",
			required:false,
			dformat:"### ### ####",
			predefined:"888 888 8888",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fCommentArea",
			name:"Instruct. Text",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fCommentArea' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'fCommentArea' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Comments here",
			ftype:"fCommentArea",
			userhelp:"A description of the section goes here.",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fhidden",
			name:"Hidden",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fhidden' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'fhidden' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Hidden",
			ftype:"fhidden",
			predefined:"",
			display:function()
				{
					return 'Available only in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">commercial versions</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fSectionBreak",
			name:"Section break",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fSectionBreak' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fSectionBreak' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Section Break",
			ftype:"fSectionBreak",
			userhelp:"A description of the section goes here.",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fPageBreak",
			name:"Page break",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fPageBreak' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fPageBreak' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Page Break",
			ftype:"fPageBreak",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});	$.fbuilder.typeList.push(
		{
			id:"fsummary",
			name:"Summary",
			control_category:1
		}
	);
	$.fbuilder.controls[ 'fsummary' ] = function(){};
	$.extend(
		$.fbuilder.controls[ 'fsummary' ].prototype,
		$.fbuilder.controls[ 'ffields' ].prototype,
		{
			title:"Summary",
			ftype:"fsummary",
			fields:"",
			titleClassname:"summary-field-title",
			valueClassname:"summary-field-value",
			display:function()
				{
					return 'Available in <a href="http://wordpress.dwbooster.com/forms/cp-polls#download">pro version</a>.';
				}
	});})(fbuilderjQuery);
});