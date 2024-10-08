jQuery(function()
	{
		(function( blocks, element ) {
            var el = wp.element.createElement,
                source 		= blocks.source,
	            InspectorControls   = ('blockEditor' in wp) ? wp.blockEditor.InspectorControls : wp.editor.InspectorControls;
		    var category 	= {slug:'cp-polls', title : 'CP Polls'};

		    var _wp$components = wp.components,
                 SelectControl = _wp$components.SelectControl,
                 ServerSideRender = wp.serverSideRender;                
                
			/* Plugin Category */
			blocks.getCategories().push({slug: 'cppolls', title: 'CP Polls'}) ;

			
            /* ICONS */
	        const iconcppolls = el('img', { width: 20, height: 20, src:  "data:image/gif;base64,R0lGODlhFAARAOYAAO/n6f/8//38/eLi+/b2/ubp+ufr9env+uPt/NDi9t/r+Feo9/j5+sTf90ms/obB81Or8Vas7VOh2luo4nq660Cn7Eqj41Gm31it6FSg1HW348fc6jeq8kKj4Uqj2k6q5Umg1Uuj2UibzU+l2qTa/ajS7Tmm4juj3j+m30Om4UKi2Eqp31Ok0nC65GWixpnS9J7Y+qjS66zW77zj+jak3kGx6EKw50mm112y33LC6r/k+Mni7z6n10Gr3kWx4kml0YfN76nf96bX7bTR3lqmx6nU5TijzEeozUekxtbr80apyrPi8rXd6rri77zi78Pg6fv+/5HK27/k7+f5/uHv89/09167x8vw8o3Iytn+//D///H///P7+97/+ff6+OT06Pj7+P3/+/L+5Pz/9/r79v39+v39+/79+vv6+f/5+f////7+/vHx8QAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACwAAAAAFAARAAAHnYAMSEgMhYaHiIYODgwSEiA3kTc9PT5mZmiHi42PkpOVl5mGEBAMGRkhK6orJiY0YWFlhzY2pqe3uIZghrSJvogsLLa4xLeGCwsMExMeP84/R9HSR4YREQwXFx0p3Cko3+Ao1dfZ293h4IY1Nb/tDBgYw7nt8AwqKkYV+hXuODgMHz7wsEDQgjsiRAAKLGiwHUI1ECNK/GXFijtfgQAAOw==" } );            

			/* Form's shortcode */
			blocks.registerBlockType( 'cppolls/form-rendering', {
                title: 'CP Polls', 
                icon: iconcppolls,    
                category: 'cppolls',
				supports: {
					customClassName: false,
					className: false
				},
				attributes: {
			      	  formId: {
			            type: 'string'
		              },
			      	  instanceId: {
			            type: 'string'
		              }
			      },           
	            edit: function( { attributes, className, isSelected, setAttributes }  ) {             
                    const formOptions = cppolls_forms.forms;
                    if (!formOptions.length)
                        return el("div", null, 'Please create a payment form first.' );
                    var iId = attributes.instanceId;
                    if (!iId)
                    {                        
                        iId = formOptions[0].value+parseInt(Math.random()*100000);
                        setAttributes({instanceId: iId });
                    }
                    if (!attributes.formId)
                        setAttributes({formId: formOptions[0].value });
                    cppolls_renderForm(iId);
			    	var focus = isSelected;
					return [
						!!focus && el(
							InspectorControls,
							{
								key: 'cppolls_inspector'
							},
							[
								el(
									'span',
									{
										key: 'cppolls_inspector_help',
										style:{fontStyle: 'italic'}
									},
									'If you need help: '
								),
								el(
									'a',
									{
										key		: 'cppolls_inspector_help_link',
										href	: 'https://wordpress.dwbooster.com/contact-us',
										target	: '_blank'
									},
									'CLICK HERE'
								)
							]
						),
						el(SelectControl, {
                                value: attributes.formId,
                                options: formOptions,
                                onChange: function(evt){         
                                    setAttributes({formId: evt});
                                    iId = evt+parseInt(Math.random()*100000);
                                    setAttributes({instanceId: iId });
                                    cppolls_renderForm(iId);                                   
			    				},
                        }),
                        el(ServerSideRender, {
                             block: "cppolls/form-rendering",
                             attributes: attributes
                        })
					];
				},

				save: function( props ) {
					return null;
				}
			});

		} )(
			window.wp.blocks,
			window.wp.element
		);
	}
);
function cppolls_renderForm(id) {      
    if (jQuery("#form_structure"+id).length)
    {
        try
        {
            var cp_appbooking_fbuilder_myconfig = {"obj":"{\"pub\":true,\"identifier\":\"_"+id+"\",\"messages\": {}}"};
            var f = jQuery("#fbuilder_"+id).fbuilder(jQuery.parseJSON(cp_appbooking_fbuilder_myconfig.obj));
            f.fBuild.loadData("form_structure"+id);                     
        } catch (e) { setTimeout ('cppolls_renderForm('+id+')',250);}
    }
    else
    {
        setTimeout ('cppolls_renderForm('+id+')',50);
    }
}