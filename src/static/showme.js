
// i love you YUI
YUI().use('node','event','json','anim','io', function(Y) {
 
 	// i'm lazy
 	var $ = Y.one, $j = Y.JSON;
 
 	// load me up
 	Y.on("domready",function() { var o = new ShowMe(); });
 
 	// our function
	function ShowMe() { this.init(); }
 	
 	// show me
 	ShowMe.prototype = {
 	
 		// init
 		init : function() {

			// watch our form
			$('#hd form').on('submit',this.submit,this);
			
			// focus
			$('#hd form input').on('click',function(e) { e.target.select(); e.target.removeClass('off'); });
 		
 			// load
 			$('#bd').on('click',function(e){
 				if ( e.target.hasClass('load') ) {
 				
 					// set value
 					$('#hd form input').set('value', e.target.get('innerHTML') );
 					
 					// submit
 					this.submit(e);
 					
 				}
 				else if ( e.target.hasClass("raw") && e.target.get('tagName') == 'A' ) {
 					
 					// stop
 					e.halt();
 					
 					// open
 					$('pre.raw').removeClass('hide');
 					
 					// bye
 					e.target.remove();
 					
 				}
 			},this);
 		
 		
 		},
 		
 		// submit watcher
 		submit : function(e) {
 		
 			// stop
 			e.halt();
 			
 			// alreayd open
 			if ( !$("#leaders").hasClass('open') ) {
	 			
	 			// move aside our lists
	 			var leaders = new Y.Anim({
	 				'node': $('#leaders'),
	 				'from': {
	 					'width': '100%'
	 				},
	 				'to': {
	 					'width': '40%'
	 				},
	 				'duration': .5 			
	 			});


	 			
	 			// done
	 			leaders.on('start',function(){ Y.all('#leaders ul').setStyles({'width':'auto','text-align':'right'}); });
				leaders.on('end',function(){ leaders.get('node').addClass('open'); Y.all('#leaders ul').setStyles({'width':'95%'}); });
	 			
	 			// run
	 			leaders.run();
		 			 			
	 			// move aside our lists
	 			var results = new Y.Anim({
	 				'node': $('#results'),
	 				'from': {
	 					'width': '0'
	 				},
	 				'to': {
	 					'width': '60%'
	 				},
	 				'duration': .5 			
	 			});
	 			
	 			// end
	 			results.on('end',function(){ this.load(); },this);
 			
 				// run me
 				results.run();
	
				// stop here
				return;
	
			}
			
			// fade out
 			// move aside our lists
 			var results = new Y.Anim({
 				'node': $('#results'),
 				'to': {
 					'opacity': 0
 				},
 				'duration': .2			
 			});			 			
			
			results.on("end",function(){ this.load(); },this);
			
			// run
			results.run();
 		
 		},
 		
 		load : function() {
 		
 			// loading
 			var r = $("#results");
 			
 			// clear
 			r.set('innerHTML','&nbsp;').setStyle('opacity',1).addClass('loading');
 		
 			// load it 
 			var q = $('#hd form input').get('value');
 		
 			// send
 			Y.io("/query?xhr=true&q="+q,{
 				'method': 'get',
 				'on': {
 					'complete': function(id,o) {
 						
 						// get our j
 						var j = $j.parse(o.responseText);
 					
 						// if stat is good
 						if ( j.stat != true ) { alert("Couldn't finish request"); return }
 						
 						// set it 
 						$("#results").set('innerHTML',j.html).removeClass('loading').setStyle('opacity',1);
 					
 					}
 				} 			
 			}); 		
 		
 		}
 		
 	}
 
});