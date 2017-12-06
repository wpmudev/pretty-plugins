/* global _wpPluginSettings, confirm */
window.wp = window.wp || {};

( function($) {

// Set up our namespace...
var plugins, l10n;
plugins = wp.plugins = wp.plugins || {};

// Store the plugin data and settings for organized and quick access
// plugins.data.settings, plugins.data.plugins, plugins.data.l10n
plugins.data = _wpPluginsSettings;
l10n = plugins.data.l10n;
categories = plugins.data.categories;

// Setup app structure
_.extend( plugins, { model: {}, view: {}, routes: {}, router: {}, template: wp.template });

plugins.model = Backbone.Model.extend({});

// Main view controller for plugins.php
// Unifies and renders all available views
plugins.view.Appearance = wp.Backbone.View.extend({

  el: '#wpbody-content .wrap .plugin-browser',

  window: $( window ),
  // Pagination instance
  page: 0,

  // Sets up a throttler for binding to 'scroll'
  initialize: function() {
    // Scroller checks how far the scroll position is
    _.bindAll( this, 'scroller' );

    // Bind to the scroll event and throttle
    // the results from this.scroller
    this.window.bind( 'scroll', _.throttle( this.scroller, 300 ) );
  },

  // Main render control
  render: function() {
    // Setup the main plugin view
    // with the current plugin collection
    this.view = new plugins.view.Plugins({
      collection: this.collection,
      parent: this
    });

    // Render categories form.
    this.categories();

    // Render search form.
    this.search();

    // Render and append
    this.view.render();
    this.$el.empty().append( this.view.el ).addClass('rendered');
    this.$el.append( '<br class="clear"/>' );
  },

  // Search input and view
  // for current plugin collection
  categories: function() {
    var view,
      self = this;

    // Don't render the categories if there is only one plugin
    if ( plugins.data.plugins.length === 1 ) {
      return;
    }

    view = new plugins.view.Categories({ collection: self.collection });
    view_wp_menu = new plugins.view.CategoriesWpMenu({ collection: self.collection });

    // Render and append before plugins list
    view.render();
    $('#wpbody .plugin-browser')
      .before( view.el );
  },

  // Search input and view
  // for current plugin collection
  search: function() {
    var view,
      self = this;

    // Don't render the search if there is only one plugin
    if ( plugins.data.plugins.length === 1 ) {
      return;
    }

    view = new plugins.view.Search({ collection: self.collection });

    view.render();
    $('.wp-filter')
      .append( '<div class="search-form"></div>' );
    $('.wp-filter .search-form')
      .append( view.el );
  },

  // Checks when the user gets close to the bottom
  // of the mage and triggers a plugin:scroll event
  scroller: function() {
    var self = this,
      bottom, threshold;

    bottom = this.window.scrollTop() + self.window.height();
    threshold = self.$el.offset().top + self.$el.outerHeight( false ) - self.window.height();
    threshold = Math.round( threshold * 0.9 );

    if ( bottom > threshold ) {
      this.trigger( 'plugin:scroll' );
    }
  }
});

// Set up the Collection for our plugin data
plugins.Collection = Backbone.Collection.extend({

  model: plugins.model,

  // Search terms
  terms: '',

  // Controls searching on the current plugin collection
  // and triggers an update event
  doSearch: function( value ) {
    //reset category to "all" on search
    $('.plugin-categories .plugin-category').removeClass('current');
    $('[data-category="all"]').addClass('current');

    // Don't do anything if we've already done this search
    // Useful because the Search handler fires multiple times per keystroke
    if ( this.terms === value ) {
      return;
    }

    // Updates terms with the value passed
    this.terms = value;

    // If we have terms, run a search...
    if ( this.terms.length > 0 ) {
      this.search( this.terms );
    }

    // If search is blank, show all plugins
    // Useful for resetting the views when you clean the input
    if ( this.terms === '' ) {
      this.reset( plugins.data.plugins );
    }

    // Trigger an 'update' event
    this.trigger( 'update' );
  },

  // Controls viewing plugins from category on the current plugin collection
  // and triggers an update event
  doCategory: function( value ) {  
    //Sets up class for active category button
    $('#plugin-search-input').val('');
    $('.plugin-categories .plugin-category').removeClass('current');
    $('[data-category="'+ value + '"]').addClass('current');
    $('#toplevel_page_pretty-plugins li').removeClass('current');

    // If we have terms, run a search...
    if ( value.length > 0 ) {
      this.category( value );

      if(value == 'all')
        $('#toplevel_page_pretty-plugins li a.current').parent().addClass('current');
      else
        $('#toplevel_page_pretty-plugins li a[href$="category\\='+value+'"]').parent().addClass('current');
    }

    // If search is blank, show all plugins
    // Useful for resetting the views when you clean the input
    if ( value === '' ) {
      this.reset( plugins.data.plugins );
    }

    // Trigger an 'update' event
    this.trigger( 'update' );
  },

  // Performs a search within the collection
  // @uses RegExp
  search: function( term ) {
    var match, results, haystack, name, description;

    // Start with a full collection
    this.reset( plugins.data.plugins, { silent: true } );

    // Escape the term string for RegExp meta characters
    term = term.replace( /[-\/\\^$*+?.()|[\]{}]/g, '\\$&' );

    // Consider spaces as word delimiters and match the whole string
    // so matching terms can be combined
    term = term.replace( / /g, ')(?=.*' );
    match = new RegExp( '^(?=.*' + term + ').+', 'i' );

    // Find results
    // _.filter and .test
    results = this.filter( function( data ) {
      name        = data.get( 'Name' ).replace( /(<([^>]+)>)/ig, '' );
      description = data.get( 'Description' ).replace( /(<([^>]+)>)/ig, '' );

      haystack = _.union( [ name, description ] );

      return match.test( haystack );
    });

    this.reset( results );
  },

  // Picks categories within the collection
  category: function( term ) {
    var results;

    // Start with a full collection
    this.reset( plugins.data.plugins, { silent: true } );

    // Find results
    // _.filter and .test
    results = this.filter( function( data ) {
      if(term == 'all' || term == 'active' || term == 'inactive') {
        if(term == 'all')
          return data;
        else if(term == 'active' && data.attributes.isActive == true)
          return data;
        else if(term == 'inactive' && data.attributes.isActive == false)
          return data;
      }
      else if($.inArray( term, data.attributes.Categories ) !== -1)
        return data;
    });

    this.reset( results );
  },

  // Paginates the collection with a helper method
  // that slices the collection
  paginate: function( instance ) {
    var collection = this;
    instance = instance || 0;

    // Plugins per instance are set at 15
    collection = _( collection.rest( 15 * instance ) );
    collection = _( collection.first( 15 ) );

    return collection;
  }
});

// This is the view that controls each plugin item
// that will be displayed on the screen
plugins.view.Plugin = wp.Backbone.View.extend({

  // Wrap plugin data on a div.plugin element
  className: 'plugin-card',

  events: {
    'click .show-more-button':  'showMore'
  },

  // Reflects which plugin view we have
  // 'grid' (default) or 'detail'
  state: 'grid',

  // The HTML template for each element to be rendered
  html: plugins.template( 'plugin' ),

  touchDrag: false,

  render: function() {
    var data = this.model.toJSON();

    // Render plugins using the html template
    this.$el.html( this.html( data ) ).attr({
      tabindex: 0,
      'aria-describedby' : data.id + '-action ' + data.id + '-name'
    });

    // Renders active plugin styles
    this.activePlugin();

    if ( this.model.get( 'displayAuthor' ) ) {
      this.$el.addClass( 'display-author' );
    }
  },

  // Adds a class to the currently active plugin
  // and to the overlay in detailed view mode
  activePlugin: function() {
    if ( this.model.get( 'active' ) ) {
      this.$el.addClass( 'active' );
    }
  },
  showMore: function( event ) {
    event.preventDefault();

    this.$el.find('.plugin-content').removeClass('plugin-show-more');
  },
});

// Controls the rendering of div.plugins,
// a wrapper that will hold all the plugin elements
plugins.view.Plugins = wp.Backbone.View.extend({

  className: 'plugins',
  $overlay: $( 'div.plugin-overlay' ),

  // Number to keep track of scroll position
  // while in plugin-overlay mode
  index: 0,

  // The plugin count element
  count: $( '.plugin-count' ),

  initialize: function( options ) {
    var self = this;

    // Set up parent
    this.parent = options.parent;

    // Set current view to [grid]
    this.setView( 'grid' );

    // Move the active plugin to the beginning of the collection
    self.currentPlugin();

    // When the collection is updated by user input...
    this.listenTo( self.collection, 'update', function() {
      self.parent.page = 0;
      self.currentPlugin();
      self.render( this );
    });

    this.listenTo( this.parent, 'plugin:scroll', function() {
      self.renderPlugins( self.parent.page );
    });

    // Bind keyboard events.
    $('body').on( 'keyup', function( event ) {
      if ( ! self.overlay ) {
        return;
      }

      // Pressing the right arrow key fires a plugin:next event
      if ( event.keyCode === 39 ) {
        self.overlay.nextPlugin();
      }

      // Pressing the left arrow key fires a plugin:previous event
      if ( event.keyCode === 37 ) {
        self.overlay.previousPlugin();
      }

      // Pressing the escape key fires a plugin:collapse event
      if ( event.keyCode === 27 ) {
        self.overlay.collapse( event );
      }
    });
  },

  // Manages rendering of plugin pages
  // and keeping plugin count in sync
  render: function() {
    // Clear the DOM, please
    this.$el.html( '' );

    // Generate the plugins
    // Using page instance
    this.renderPlugins( this.parent.page );

    // Display a live plugin count for the collection
    this.count.text( this.collection.length );
  },

  // Iterates through each instance of the collection
  // and renders each plugin module
  renderPlugins: function( page ) {
    var self = this;

    self.instance = self.collection.paginate( page );

    // If we have no more plugins bail
    if ( self.instance.length === 0 ) {
      return;
    }

    // Loop through the plugins and setup each plugin view
    self.instance.each( function( plugin ) {
      self.plugin = new plugins.view.Plugin({
        model: plugin
      });

      // Render the views...
      self.plugin.render();
      // and append them to div.plugins
      self.$el.append( self.plugin.el );
    });

    this.parent.page++;
  },

  // Grabs current plugin and puts it at the beginning of the collection
  currentPlugin: function() {
    var self = this,
      current;

    current = self.collection.findWhere({ active: true });

    // Move the active plugin to the beginning of the collection
    if ( current ) {
      self.collection.remove( current );
      self.collection.add( current, { at:0 } );
    }
  },

  // Sets current view
  setView: function( view ) {
    return view;
  },
});

// Search input view controller.
plugins.view.Search = wp.Backbone.View.extend({

  tagName: 'input',
  className: 'plugin-search',
  id: 'plugin-search-input',

  attributes: {
    placeholder: l10n.searchPlaceholder,
    type: 'search'
  },

  events: {
    'input':  'search',
    'keyup':  'search',
    'change': 'search',
    'search': 'search'
  },

  // Runs a search on the plugin collection.
  search: function( event ) {
    // Clear on escape.
    if ( event.type === 'keyup' && event.which === 27 ) {
      event.target.value = '';
    }

    this.collection.doSearch( event.target.value );

    // Update the URL hash
    if ( event.target.value ) {
      plugins.router.navigate( plugins.router.baseUrl( '&search=' + event.target.value ), { replace: true } );
    } else {
      plugins.router.navigate( plugins.router.baseUrl( '' ), { replace: true } );
    }
  }
});

// Categories input view controller.
plugins.view.Categories = wp.Backbone.View.extend({

  tagName: 'div',
  className: 'wp-filter plugin-categories',

  events: {
    'click a':  'categories'
  },

  render: function() {
    var el = $(this.el);
    el.append( $( '.filter-count' ) );

      el.append( $.parseHTML( '<span style="display:none;" class="plugin-categories-label">' + l10n.categories + '</span>' ) );
      el.append( '<ul class="filter-links"></ul>' );
      var el_ul = el.find('.filter-links');
    $.each(categories, function( index, value ) {
      var add_class = '';
      if(index == 'all')
        add_class = ' current';
      
      el_ul.append( $.parseHTML( '<li><a data-category="'+ index +'" class="plugin-category '+ add_class +'" href="#">' + value + '</a></li>' ) );
    });
  },

  // Runs a search on the plugin collection.
  categories: function( event ) {
    event.preventDefault();
    
    // Update the URL hash
    if ( event.target.dataset.category ) {
      this.collection.doCategory( event.target.dataset.category );
      plugins.router.navigate( plugins.router.baseUrl( '&category=' + event.target.dataset.category ), { replace: true } );
    }
  }
});

// Categories input view controller.
plugins.view.CategoriesWpMenu = wp.Backbone.View.extend({

  el: 'li.toplevel_page_pretty-plugins',

  events: {
    'click a':  'categories'
  },

  // Runs a search on the plugin collection.
  categories: function( event ) {
    event.preventDefault();

    // Update the URL hash
    if ( event.target.href ) {
      var category = this.getUrlParameter(event.target.href);
      if('category' in category)
        category = category['category'];
      else
        category = 'all';

      if ( category ) {
        this.collection.doCategory( category );
        plugins.router.navigate( plugins.router.baseUrl( '&category=' + category ), { replace: true } );
      }
    }
  },

  getUrlParameter: function(url){
    var vars = [], hash;
    var hashes = url.slice(url.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  }
});

// Sets up the routes events for relevant url queries
// Listens to [plugin] and [search] params
plugins.routes = Backbone.Router.extend({

  initialize: function() {
    this.routes = _.object([
    ]);
  },

  baseUrl: function( url ) {
    return plugins.data.settings.root + url;
  }
});

// Execute and setup the application
plugins.Run = {
  init: function() {
    // Initializes the blog's plugin library view
    // Create a new collection with data
    this.plugins = new plugins.Collection( plugins.data.plugins );

    // Set up the view
    this.view = new plugins.view.Appearance({
      collection: this.plugins
    });

    this.render();
  },

  render: function() {
    // Render results
    this.view.render();
    this.routes();

    // Set the initial search
    if ( 'undefined' !== typeof plugins.data.settings.search && '' !== plugins.data.settings.search ){
      $( '.plugin-search' ).val( plugins.data.settings.search );
      this.plugins.doSearch( plugins.data.settings.search );
    }

    // Set the initial category
    if ( 'undefined' !== typeof plugins.data.settings.category && '' !== plugins.data.settings.category ){
      this.plugins.doCategory( plugins.data.settings.category );
    }

    // Start the router if browser supports History API
    if ( window.history && window.history.pushState ) {
      // Calls the routes functionality
      Backbone.history.start({ pushState: true, silent: true });
    }
  },

  routes: function() {
    // Bind to our global thx object
    // so that the object is available to sub-views
    plugins.router = new plugins.routes();
  }
};

// Ready...
jQuery( document ).ready(

  // Bring on the plugins
  _.bind( plugins.Run.init, plugins.Run )

);

})( jQuery );

/*

jQuery(document).ready( function($) {
  // Integrate with WP submenu
  
  $('#toplevel_page_pretty-plugins li a').click(function(e) {
    e.preventDefault();

    var category = get_url_parameter($(this).attr('href'));
    if(typeof category['category'] === "undefined")
      category = 'all';
    else
      category = category['category'];
console.log(plugins);
    // Update the URL hash
    if ( category ) {
      _.bind( plugins.doCategory, category );
      //wp.plugins.view.Categories.collection.doCategory( category );
      wp.plugins.router.navigate( wp.plugins.router.baseUrl( '&category=' + category ), { replace: true } );
    }

    return false;
  });
});

// Align plugin browser thickbox
var tb_position;
jQuery(document).ready( function($) {
  tb_position = function() {
    var tbWindow = $('#TB_window'),
      width = $(window).width(),
      H = $(window).height(),
      W = ( 1040 < width ) ? 1040 : width,
      adminbar_height = 0;

    if ( $('body.admin-bar').length ) {
      adminbar_height = parseInt( jQuery('#wpadminbar').css('height'), 10 );
    }

    if ( tbWindow.size() ) {
      tbWindow.width( W - 50 ).height( H - 45 - adminbar_height );
      $('#TB_iframeContent').width( W - 50 ).height( H - 75 - adminbar_height );
      tbWindow.css({'margin-left': '-' + parseInt( ( ( W - 50 ) / 2 ), 10 ) + 'px'});
      if ( typeof document.body.style.maxWidth !== 'undefined' ) {
        tbWindow.css({'top': 20 + adminbar_height + 'px', 'margin-top': '0'});
      }
    }
  };

  $(window).resize(function(){ tb_position(); });
});
*/