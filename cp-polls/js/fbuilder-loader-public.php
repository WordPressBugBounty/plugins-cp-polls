<?php header('Content-Type: application/x-javascript; charset=UTF-8'); ?>
fbuilderjQuery = (typeof fbuilderjQuery != 'undefined' ) ? fbuilderjQuery : jQuery;
fbuilderjQuery(function(){
(function($) {
	// Namespace of fbuilder
	$.fbuilder = $.fbuilder || {};
	$.fbuilder[ 'objName' ] = 'fbuilderjQuery';	
	
<?php


	// Load Control files
    require 'fbuilder-pro-public.jquery.js';
    try {
        $d = dir( dirname( __FILE__ )."/fields-public" );
		$controls_files = array();
        while (false !== ($entry = $d->read())) {            
            if (strlen($entry) > 3 && strtolower(substr($entry,strlen($entry)-3)) == '.js')
                if ( file_exists( $d->path.'/'.$entry ) )
                    $controls_files[] = $d->path.'/'.$entry;
        }
		sort( $controls_files );
		foreach( $controls_files as $file )
		{
			require $file;
		}
    } catch (Exception $e) {
        // ignore the error
    }
?>
        var fcount = 1;
        var fnum = "_"+fcount;
        while (eval("typeof CP_Polls_fbuilder_config"+fnum+" != 'undefined'"))
        {
            try {
            var CP_Polls_fbuilder_config = eval("CP_Polls_fbuilder_config"+fnum);
            var f = $("#fbuilder"+fnum).fbuilder($.parseJSON(CP_Polls_fbuilder_config.obj));
			f.fBuild.loadData("form_structure"+fnum);
			$("#CP_Polls_pform"+fnum).validate({
                ignore:".ignore,.ignorepb",
			    errorElement: "div",
			    errorPlacement: function(e, element) 
					{
						//if (element.hasClass('group'))
							element = element.closest( '.dfield' )
						e.insertBefore(element);
						e.addClass('message'); // add a class to the wrapper
						e.css('position', 'absolute');
						e.css('left',0 );
						e.css('top',element.parent().outerHeight(true));
					}
     		});
     		} catch (e) {}
	    	fcount++;
	    	fnum = "_"+fcount;
	    }
})(fbuilderjQuery);
});