// Set tooltips and load things up
$(document).ready(function() {
	$('.widget-box').sandboxWidget(); 
	
	// top right
	$('.tooltip-tr').tipsy({gravity:'sw'});
	// bottom left
	$('.tooltip-bl').tipsy({gravity:'ne'});
	$('.tooltip-t').tipsy({gravity:'s'});
	$('.tooltip').tipsy();
	
});

/**
 * Sandbox widgets.
 * 
 * Use:
 * $('#somediv').sandboxWidget({type:'links', title:'Links', help:'Some helpful links for you.'});
*/
(function( $ ) {
	var dataLoaded = {links:false,articles:false,screencasts:false};
	
	var methods = {
		init : function(options) {
			// Create some defaults, extending them with any options that were provided
			var settings = $.extend( {
				// maybe...
			}, options);
			
			// If the plugin hasn't been initialized yet
			if (!data) {
				// Do more setup stuff here
				$(this).data('sandboxWidget', {
					widgets : $(this),
					options : settings
				});
			}
			
			// Namespaced data
			var $this = $(this),
				data = $this.data('sandboxWidget');
			
			// Set the date range picker.
			// Initial values (the past month from today).
			var now = Math.floor(new Date().getTime() / 1000);
			var d = new Date();
			d.setMonth(d.getMonth()-1);
			var prevMonth = Math.floor(d.getTime() / 1000);

			var dateFrom = (typeof(dateFrom) != 'undefined') ? dateFrom:prevMonth;
			var dateTo = (typeof(dateTo) != 'undefined') ? dateTo:now;

			// Initial date range. NOTE: Some widgets may not be date sensitive.
			$('#date-filter-from').val(new Date(dateFrom * 1000).toString('MMMM d, yyyy'));
			$('#date-filter-to').val(new Date(dateTo * 1000).toString('MMMM d, yyyy'));

			var dates = $( "#date-filter-from, #date-filter-to" ).datepicker({
				dateFormat: 'MM dd, yy',
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 3,
				onSelect: function( selectedDate ) {
					var option = this.id == "date-filter-from" ? "minDate" : "maxDate",
						instance = $( this ).data( "datepicker" ),
						date = $.datepicker.parseDate(
							instance.settings.dateFormat ||
							$.datepicker._defaults.dateFormat,
							selectedDate, instance.settings );
					dates.not( this ).datepicker( "option", option, date );

					if(option == 'minDate') {
						selectedDateFrom = +new Date(selectedDate.toString()).setUTCHours(0,0,0,0) / 1000;
					}
					if(option == 'maxDate') {
						selectedDateTo = +new Date(selectedDate.toString()).setUTCHours(0,0,0,0) / 1000;
					}
				}
			});
			
			// Loop each widget and add the HTML necessary to the DOM
			if(typeof(data) == 'undefined' || typeof(data.widgets) == 'undefined') {
				return;
			}
			
			data.widgets.each(function(k, v){
				// console.dir(this);
				
				// Get the type of widget from the div's class value prefixed with: widget-
				var matchRegex = /widget\-(.*)/i;
				var matches = matchRegex.exec($(this).attr('class'));
				var type = false;
				if(typeof(matches) == 'object' && typeof(matches[1]) == 'string') {
					type = matches[1];
				}
				
				// Stick this on here for future use
				data.widgets[k].widgetType = type;
				
				// Get the widget settings from the div
				var title = $(this).attr('title');
				var tooltip = $(this).attr('rel');
				
				var widgetHtml = '';
					if(typeof(title) == 'string') {
						widgetHtml += '<span class="widget-label text-shadow">' + title + '</span>';
					}
					if(typeof(tooltip) == 'string') {
						widgetHtml += ' <a href="#" onclick="return false;" class="tooltip-t widget-label-tooltip" title="' + tooltip + '">[?]</a>';
					}
					
					// For the loading spinner/style/info
					widgetHtml += '<div class="widget-loading" style="display:none;"><img src="/img/12x12spinner.gif" /> Loading...</div>';
					
					// Wrap the content area in a div
					widgetHtml += '<div class="widget-content">';
					
					switch(type) {
						case 'links':
							// widgetHtml += '<div class="links-list"></div>';
							break;
					}
					
					widgetHtml += '</div>';
				$(this).html(widgetHtml);
				
				// Set the widget to be loading on init.
				$(this).sandboxWidget('loading', true);
				
				// Bind the date picker
				$(this).sandboxWidget('bindDatePicker');
			});
			
			$(this).sandboxWidget('getData');
			
			return this.each(function(){
				// Bind events
				$(window).bind('update.sandboxWidget', methods.update);
				// ... add more here ...
			});
			
			
		},
		// When the widget is destroyed, remove all the event bindings
		// $('#somediv').sandboxWidget('destroy'); would need to be called
		destroy : function( ) {
			return this.each(function(){
				// Unbinds only the specific widget (namespace)
				var $this = $(this),
					data = $this.data('sandboxWidget');
				
				$(window).unbind('.sandboxWidget');
				data.sandboxWidget.remove();
				$this.removeData('sandboxWidget');
			});
		},
		/**
		 * If all async calls have completed, this will bind and display the link button 
		 * to change the results based on date range chosen with the date picker.
		 * 
		*/
		updateDatePickerButton : function() {
			if($(this).sandboxWidget('allDataLoaded') == true) {
				$('#date-filter-update-date-range').animate({opacity: 1});
				
				$('#date-filter-update-date-range').bind('click', function() {
					var selectedDateFrom = +new Date($('#date-filter-from').datepicker('getDate').toString()).setUTCHours(0,0,0,0) / 1000;
					var selectedDateTo = +new Date($('#date-filter-to').datepicker('getDate').toString()).setUTCHours(0,0,0,0) / 1000;
					// console.dir(selectedDateFrom);
					// console.dir(selectedDateTo);
					$('.widget-box').sandboxWidget('getData', {dateFrom:selectedDateFrom, dateTo:selectedDateTo});
					$('.widget-box').sandboxWidget('loading', true);
				});
			} else {
				$('#date-filter-update-date-range').animate({opacity: 0.25});
				$('#date-filter-update-date-range').unbind('click');
			}
			
		},
		/**
		 * This function will set the loading state.
		 * If true then a spinner will fade in and content wil be hidden.
		 * If false, then the spinner will fade out and the content will be revealed.
		 * 
		 * @param state 
		*/ 
		loading : function(state) {
			var $this = $(this),
				data = $this.data('sandboxWidget');
			
			if(state == true) {
				$('.widget-content', this).hide();
				$('.widget-loading', this).show();
			} else {
				// Checks to see if all data has loaded, if so the date picker 
				// button will be displayed and enabled.
				$this.sandboxWidget('updateDatePickerButton');
				
				$('.widget-loading', this).hide();
				$('.widget-content', this).fadeIn('slow');
			}
		},
		/**
		 * There's multiple asynchronous calls.
		 * This will let us know when all data has been loaded.
		 * The getData method will set these properties to true or false.
		 * 
		 * @return boolean
		*/
		allDataLoaded : function() {
			for(i in dataLoaded) {
				if(dataLoaded[i] == false) {
					return false;
				}
			}
			return true;
		},
		/**
		 * Displays a "no data available" message in the widget.
		 * This may be called if the call to the any of the JSON actions failed
		 * or if there was no data available at all for the date range chosen.
		*/
		noData : function() {
			var $this = $(this),
				data = $this.data('sandboxWidget');
			
			$('.widget-content', this).html('<span class="widget-no-data">Sorry, no data available.</span>');
			// Show it.
			$(this).sandboxWidget('loading', false);
		},
		// Updates the widget with new data
		update : function(options) { 
			var $this = $(this),
				data = $this.data('sandboxWidget');
			
			if(typeof(options) == 'undefined') {
				console.warn('No options were passed when calling $("' + $this.selector + '").sandboxWidget("update")'); 
				return false;
			}
			
			// Any defaults?
			var settings = $.extend( {
			}, options);
			
			//$($this).append(' ' + settings.title);
			
			// Must have a widget type.
			if(typeof(settings.type) == 'undefined') {
				return false;
			}
			
			switch(settings.type) {
				case 'links':
					var widgetHtml = '<ul class="widget-links-list">';
					
					for(i in data.links) {
						widgetHtml += '<li><a href="' + data.links[i].link + '" target="_blank">' + data.links[i].title + '</a></li>';
					}
					
					widgetHtml += '</ul>';
					
					$('.widget-links .widget-content').html(widgetHtml);
					
					// If for some reason we had multiple widgets, they would all now display.
					$('.widget-links').sandboxWidget('loading', false);
					break;
				case 'articles':
					var widgetHtml = '<ul class="widget-articles-list">';
					
					for(i in data.articles) {
						widgetHtml += '<li><a href="' + data.articles[i].link + '" target="_blank">' + data.articles[i].title + '</a></li>';
					}
					
					widgetHtml += '</ul>';
					
					$('.widget-articles .widget-content').html(widgetHtml);
					
					// If for some reason we had multiple widgets, they would all now display.
					$('.widget-articles').sandboxWidget('loading', false);
					break;
				case 'screencasts':
					var widgetHtml = '<ul class="widget-screencasts-list">';
					
					for(i in data.screencasts) {
						widgetHtml += '<li><a href="' + data.screencasts[i].link + '" target="_blank">' + data.screencasts[i].title + '</a></li>';
					}
					
					widgetHtml += '</ul>';
					
					$('.widget-screencasts .widget-content').html(widgetHtml);
					
					// If for some reason we had multiple widgets, they would all now display.
					$('.widget-screencasts').sandboxWidget('loading', false);
					break;
			}
			
			
		},
		/**
		 * Sets the title and optional help tooltip
		*/
		setTitle : function(options) {
			var $this = $(this),
				data = $this.data('sandboxWidget');
			
			if(typeof(options) == 'undefined') {
				return false;
			}
			
			if(typeof(options.title) == 'string') {
				$($this + ' .widget-label').text(options.title);
			}
			
			if(typeof(options.tooltip) == 'string') {
				$($this + ' .widget-label-tooltip').text(options.tooltip);
				
				var tooltipGravity = 's';
				if(typeof(options.tooltipGravity) == 'string') {
					tooltipGravity = options.tooltipGravity;
				}
				
				$($this + ' .widget-label-tooltip').tipsy({html:true, gravity: tooltipGravity});
			}
			
		},
		getData : function(options) {
			var now = Math.floor(new Date().getTime() / 1000);
			var d = new Date();
			d.setMonth(d.getMonth()-1);
			var prevMonth = Math.floor(d.getTime() / 1000);
			
			// Defaults
			var settings = $.extend( {
				dateFrom : (typeof(options) != 'undefined' && typeof(options.dateFrom) != 'undefined') ? options.dateFrom:prevMonth,
				dateTo : (typeof(options) != 'undefined' && typeof(options.dateTo) != 'undefined') ? options.dateTo:now
			}, options);
			
			var $this = $(this),
				data = $this.data('sandboxWidget');
			
			// When getting data, we need to disable the date range update button.
			// To do that, we need to set the data loaded properties to false first.
			// Other methods can check these values using the "allDataLoaded" method.
			dataLoaded.links = false;
			dataLoaded.screencasts = false;
			dataLoaded.articles = false;
			$this.sandboxWidget('updateDatePickerButton');
			
			// console.dir('getting data...');
			
			// LINKS
			$.ajax({
				url: '/sandbox/links_list.json',
				data: { },
				type: 'POST',
				dataType: 'json',
				success: function(response) {
					// First, let everything know that this call has completed.
					dataLoaded.links = true;
					
					//console.dir(response);
					if(response.success == true) {
						// Update the sandboxWidget data.
						data.links = response.result;
						$(this).data('sandboxWidget', data);
						
						// Call the update method now that we have the data.
						$this.sandboxWidget('update', {type:'links'});
					}
					if(response.result == null || response.success == false) {
						$this.sandboxWidget('noData');
					}
				}
			});

			// SCREENCASTS
			$.ajax({
				url: '/sandbox/screencasts_list.json',
				data: { },
				type: 'POST',
				dataType: 'json',
				success: function(response) {
					// First, let everything know that this call has completed.
					dataLoaded.screencasts = true;
					
					//console.dir(response);
					if(response.success == true) {
						// Update the sandboxWidget data.
						data.screencasts = response.result;
						$(this).data('sandboxWidget', data);
						
						// Call the update method now that we have the data.
						$this.sandboxWidget('update', {type:'screencasts'});
					}
					if(response.result == null || response.success == false) {
						$this.sandboxWidget('noData');
					}
				}
			});
			
			// ARTICLES
			$.ajax({
				url: '/sandbox/articles_list.json',
				data: { },
				type: 'POST',
				dataType: 'json',
				success: function(response) {
					// First, let everything know that this call has completed.
					dataLoaded.articles = true;
					
					//console.dir(response);
					if(response.success == true) {
						// Update the sandboxWidget data.
						data.articles = response.result;
						$(this).data('sandboxWidget', data);
						
						// Call the update method now that we have the data.
						$this.sandboxWidget('update', {type:'articles'});
					}
					if(response.result == null || response.success == false) {
						$this.sandboxWidget('noData');
					}
				}
			});
			
		}
	};
	
	$.fn.sandboxWidget = function( method ) {
		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			// $.error('Method ' +  method + ' does not exist on jQuery.sandboxWidget');
			return false;
		}
	};
	
})( jQuery );