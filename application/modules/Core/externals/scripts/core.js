import 'mootools';

let en4 = {};

window.en4 = en4;


/**
 * Core methods
 */
en4.core = {

  baseUrl: false,

  basePath: false,

  loader: false,

  environment: 'production',

  setBaseUrl: function (url) {
    this.baseUrl = url;
    var m = this.baseUrl.match(/^(.+?)index[.]php/i);
    this.basePath = (m ? m[1] : this.baseUrl);
  },

  subject: {
    type: '',
    id: 0,
    guid: ''
  },

  showError: function (text) {
    Smoothbox.close();
    Smoothbox.instance = new Smoothbox.Modal.String({
      bodyText: text
    });
  }

};


/**
 * Run Once scripts
 */
en4.core.runonce = {

  executing: false,

  fns: [],

  add: function (fn) {
    this.fns.push(fn);
  },

  trigger: function () {
    if (this.executing) return;
    this.executing = true;
    var fn;
    while ((fn = this.fns.shift())) {
      Function.attempt(function () {
        fn();
      });
    }
    this.fns = [];
    this.executing = false;
  }

};


/**
 * shutdown scripts
 */
en4.core.shutdown = {

  executing: false,

  fns: [],

  add: function (fn) {
    this.fns.push(fn);
  },

  trigger: function () {
    if (this.executing) return;
    this.executing = true;
    var fn;
    while ((fn = this.fns.shift())) {
      Function.attempt(function () {
        fn();
      });
    }
    this.fns = [];
    this.executing = false;
  }

};

$(window).on('load', function () {
  en4.core.runonce.trigger();
});

// This is experimental
$(document).ready(function () {
  en4.core.runonce.trigger();
});

$(window).on('unload', function () {
  en4.core.shutdown.trigger();
});


/**
 * Dynamic page loader
 */
en4.core.dloader = {

  loopId: false,

  currentHref: false,

  activeHref: false,

  xhr: false,

  frame: false,

  enabled: false,

  previous: false,

  hash: false,

  registered: false,

  setEnabled: function (flag) {
    this.enabled = (flag == true);
  },

  start: function (options) {
    if (this.frame || this.xhr) return this;

    this.activeHref = options.url;

    // Use an iframe for get requests
    if (typeof(options.conntype) && options.conntype == 'frame') {
      options = _.merge({
        data: {
          format: 'async',
          mode: 'frame'
        },
        styles: {
          'position': 'absolute',
          'top': '-200px',
          'left': '-200px',
          'height': '100px',
          'width': '100px'
        },
        events: {
          //load : this.handleLoad.bind(this)
        }
      }, options);

      if (typeof(options.url)) {
        options.src = options.url;
        delete options.url;
      }
      // Add format as query string
      if (typeof(options.data)) {
        var separator = (options.src.indexOf('?') > -1 ? '&' : '?');
        options.src += separator + $H(options.data).toQueryString();
        delete options.data;
      }
      this.frame = new IFrame(options);
      this.frame.inject(document.body);
    } else {
      options = _.merge({
        method: 'get',
        data: {
          'format': 'html',
          'mode': 'xhr'
        },
        onComplete: this.handleLoad.bind(this)
      }, options);
      this.xhr = new Request.HTML(options);
      this.xhr.send();
    }

    return this;
  },

  cancel: function () {
    if (this.frame) {
      this.frame.destroy();
      this.frame = false;
    }
    if (this.xhr) {
      this.xhr.cancel();
      this.xhr = false;
    }
    this.activeHref = false;
    return this;
  },

  attach: function (els) {
    var bind = this;

    if (!typeof(els)) {
      els = $('a');
    }

    // Attach to links
    //$$('a.ajaxable').each(function(__ind, element)
    els.each(function (element) {
      if (!this.shouldAttach(element)) {
        return;
      } else if (element.hasEvents()) {
        return;
      }

      element.addEvent('click', function (event) {
        if (!this.shouldAttach(element)) {
          return;
        }

        var events = element.getEvents('click');
        if (events && events.length > 1) {
          return;
        }


        // Remove host + basePath
        var basePath = window.location.protocol + '//' + window.location.hostname + en4.core.baseUrl;
        var newPath;
        if (element.href.indexOf(basePath) === 0) {
          // Cancel link click
          if (event) {
            event.stopPropagation();
            event.preventDefault();
          }

          // Start request
          newPath = element.href.substring(basePath.length);

          // Update url
          if (this.hasPushState()) {
            this.push(element.href);
          } else {
            this.push(newPath);
          }

          // Make request
          this.startRequest(newPath);
        }
      }.bind(this));
    }.bind(this));

    // Monitor location
    //$(window).on('unload', this.monitorAddress.bind(this));
    this.currentHref = window.location.href;

    if (!this.registered) {
      this.registered = true;
      if (this.hasPushState()) {
        window.addEventListener("popstate", function (e) {
          this.pop(e)
        }.bind(this));
      } else {
        this.loopId = this.monitor.periodical(200, this);
      }
    }
  },

  shouldAttach: function (element) {
    return (
      element.get('tag') == 'a' &&
      !element.onclick &&
      element.href &&
      !element.href.match(/^(javascript|[#])/) &&
      !element.hasClass('no-dloader') &&
      !element.hasClass('smoothbox')
    );
  },

  handleLoad: function (response1, response2, response3, response4) {
    var response;

    if (this.frame) {
      response = Function.attempt(function () {
        return response1;
      }, function () {
        return this.frame.contentWindow.document.documentElement.innerHTML;
      }.bind(this));
    } else if (this.xhr) {
      response = response3;
    }

    if (response) {
      // Shutdown previous scripts
      en4.core.shutdown.trigger();
      // Replace HTML
      $('global_content').innerHTML = response;
      // Evaluate scripts in content
      en4.core.request.evalScripts($('global_content'));
      // Attach dloader to a's in content
      this.attach($('global_content').getElements('a'));
      // Execute runonce
      en4.core.runonce.trigger();
    }

    this.cancel();
    this.activeHref = false;
  },

  handleRedirect: function (url) {
    this.push(url);
    this.startRequest(url);
  },

  startRequest: function (url) {

    var fullUrl = window.location.protocol + '//' + window.location.hostname + en4.core.baseUrl + url;
    //console.log(url, fullUrl);

    // Cancel current request if active
    if (this.activeHref) {
      // Ignore if equal
      if (this.activeHref == url) {
        return;
      }
      // Otherwise cancel an continue
      this.cancel();
    }

    //$('global_content').innerHTML = '<h1>Loading...</h1>';

    this.start({
      url: fullUrl,
      conntype: 'frame'
    });

  },


  // functions for history
  hasPushState: function () {
    //return false;
    return ('pushState' in window.history);
  },

  push: function (url, title, state) {
    if (this.previous == url) return;

    if (this.hasPushState()) {
      window.history.pushState(state || null, title || null, url);
      this.previous = url;
    } else {
      window.location.hash = url;
    }
  },

  replace: function (url, title, state) {
    if (this.hasPushState()) {
      window.history.replaceState(state || null, title || null, url);
    } else {
      this.hash = '#' + url;
      this.push(url);
    }
  },

  pop: function (event) {
    if (this.hasPushState()) {
      if (window.location.pathname.indexOf(en4.core.baseUrl) === 0) {
        this.onChange(window.location.pathname.substring(en4.core.baseUrl.length));
      } else {
        this.onChange(window.location.pathname);
      }
    } else {
      var hash = window.location.hash;
      if (this.hash == hash) {
        return;
      }

      this.hash = hash;
      this.onChange(hash.substr(1));
    }
  },

  onChange: function (url) {
    this.startRequest(url);
  },

  back: function () {
    window.history.back();
  },

  forward: function () {
    window.history.forward();
  },

  monitor: function () {
    if (this.hash != window.location.hash) {
      this.pop();
    }
  }
};

/**
 * Request pipeline
 */
en4.core.request = {

  activeRequests: [],

  isRequestActive: function () {
    return (this.activeRequests.length > 0);
  },

  send: function (req, options) {
    options = options || {};
    if (!typeof(options.force)) options.force = false;

    // If there are currently active requests, ignore
    if (this.activeRequests.length > 0 && !options.force) {
      return this;
    }
    this.activeRequests.push(req);

    // Process options
    if (!typeof(options.htmlJsonKey)) options.htmlJsonKey = 'body';
    if (typeof(options.element)) {
      options.updateHtmlElement = options.element;
      options.evalsScriptsElement = options.element;
    }

    // OnComplete
    var bind = this;
    req.addEvent('success', function (response, response2, response3, response4) {
      bind.activeRequests.erase(req);
      var htmlBody;
      var jsBody;
      //alert(typeof(response) + typeof(response2) + typeof(response3) + typeof(response4));

      // Get response
      if (typeof(response) == 'object') { // JSON response
        htmlBody = response[options.htmlJsonKey];
      } else if (typeof(response3) == 'string') { // HTML response
        htmlBody = response3;
        jsBody = response4;
      }

      // An error probably occurred
      if (!response && !response3 && typeof(options.updateHtmlElement)) {
        en4.core.showError('An error has occurred processing the request. The target may no longer exist.');
        return;
      }

      if (typeof(response) == 'object' && typeof(response.status) && response.status == false && typeof(response.error) === 'string') {
        en4.core.showError(response.error + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
        return;
      }

      if (typeof(response) == 'object' && typeof(response.status) && response.status == false /* && typeof(response.error) */) {
        en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
        return;
      }

      // Get scripts
      if (typeof(options.evalsScriptsElement) || typeof(options.evalsScripts)) {
        if (htmlBody) htmlBody.stripScripts(true);
        if (jsBody) eval(jsBody);
      }

      if (typeof(options.updateHtmlElement) && htmlBody) {
        if (typeof(options.updateHtmlMode) && options.updateHtmlMode == 'append') {
          Elements.from(htmlBody).inject($(options.updateHtmlElement));
        } else if (typeof(options.updateHtmlMode) && options.updateHtmlMode == 'prepend') {
          Elements.from(htmlBody).reverse().inject($(options.updateHtmlElement), 'top');
        } else if (typeof(options.updateHtmlMode) && options.updateHtmlMode == 'comments' && Elements.from(htmlBody) && Elements.from(htmlBody)[1] && Elements.from(htmlBody)[1].getElement('.comments')) {
          $(options.updateHtmlElement).getElement('.comments').destroy();
          $(options.updateHtmlElement).getElement('.feed_item_date').destroy();
          if (Elements.from(htmlBody)[1].getElement('.feed_item_date'))
            Elements.from(htmlBody)[1].getElement('.feed_item_date').inject($(options.updateHtmlElement.getElement('.feed_item_body')));
          Elements.from(htmlBody)[1].getElement('.comments').inject($(options.updateHtmlElement.getElement('.feed_item_body')));
        } else if (typeof(options.updateHtmlMode) && options.updateHtmlMode == 'comments2') {
          $(options.updateHtmlElement).empty();
          Elements.from(htmlBody)[0].getChildren().inject($(options.updateHtmlElement));
        } else {
          $(options.updateHtmlElement).empty();
          Elements.from(htmlBody).inject($(options.updateHtmlElement));
        }
        Smoothbox.bind($(options.updateHtmlElement));
      }

      if (!typeof(options.doRunOnce) || !options.doRunOnce) {
        en4.core.runonce.trigger();
      }
    });

    req.send();

    return this;
  },

  evalScripts: function (element) {
    element = $(element);
    if (!element) return this;
    element.getElements('script').each(function (script) {
      if (script.type != 'text/javascript') return;
      if (script.src) {
        Asset.javascript(script.src);
      }
      else if (script.innerHTML.trim()) {
        eval(script.innerHTML);
      }
    });

    return this;
  }

};

/**
 * Comments
 */
en4.core.comments = {

  loadComments: function (type, id, page) {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'core/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        page: page
      }
    }), {
      'element': $('comments')
    });
  },

  attachCreateComment: function (formElement) {
    var bind = this;
    formElement.addEvent('submit', function (event) {
      event.stop();
      var form_values = formElement.toQueryString();
      form_values += '&format=json';
      form_values += '&id=' + formElement.identity.value;
      en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'core/comment/create',
        data: form_values
      }), {
        'element': $('comments')
      });
      //bind.comment(formElement.type.value, formElement.identity.value, formElement.body.value);
    })
  },

  comment: function (formData) {
    en4.core.request.send(new Request.JSON({
      format: 'json',
      url: en4.core.baseUrl + 'core/comment/create',
      data: formData,
    }), {
      'element': $('comments')
    });
  },

  like: function (type, id, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'core/comment/like',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      }
    }), {
      'element': $('comments')
    });
  },

  unlike: function (type, id, comment_id) {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'core/comment/unlike',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      }
    }), {
      'element': $('comments')
    });
  },

  showLikes: function (type, id) {
    en4.core.request.send(new Request.HTML({
      url: en4.core.baseUrl + 'core/comment/list',
      data: {
        format: 'html',
        type: type,
        id: id,
        viewAllLikes: true
      }
    }), {
      'element': $('comments')
    });
  },

  deleteComment: function (type, id, comment_id) {
    if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
      return;
    }
    (new Request.JSON({
      url: en4.core.baseUrl + 'core/comment/delete',
      data: {
        format: 'json',
        type: type,
        id: id,
        comment_id: comment_id
      },
      onComplete: function () {
        if ($('comment-' + comment_id)) {
          $('comment-' + comment_id).destroy();
        }
        try {
          var commentCount = $('.comments_options span')[0];
          var m = commentCount.get('html').match(/\d+/);
          var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
          commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
        } catch (e) {
        }
      }
    })).send();
  }
};

en4.core.layout = {
  /**
   * Set layout column width
   */
  setColumnWidth: function (elementId, width) {
    var widgetContainer = $(elementId).getParent('.generic_layout_container');
    widgetContainer.getParent('.generic_layout_container').setStyle('width', width);
    widgetContainer.destroy();
  },
  setLeftPannelMenu: function (type) {
    var pannelElement = $(document).getElement('body').addClass('global_left_panel panel-collapsed');
    var button = $(document).getElement('.layout_core_menu_main .panel-toggle');
    var scrollBar;
    var headerButton = new Element('div', {
      'class': 'core_main_menu_toggle fa header-panel-toggle'
    }).inject(pannelElement.getElement('.layout_page_header .layout_main .generic_layout_container'), 'top');
    var navigationElement = pannelElement.getElement('.layout_core_menu_main .main_menu_navigation');
    var setContent = function () {
      if (type == 'horizontal' && headerButton.getStyle('display') == 'none') {
        pannelElement.removeClass('global_left_panel');
        navigationElement.setStyle('height', 'auto');
        navigationElement.addClass('horizontal_core_main_menu');
        return;
      }
      pannelElement.addClass('global_left_panel');
      navigationElement.setStyle('height', window.getSize().y - button.getCoordinates().height + 'px');
      navigationElement.removeClass('horizontal_core_main_menu');
    };
    pannelElement.getElements('.core_main_menu_toggle').addEvent('click', function () {
      pannelElement.toggleClass('panel-collapsed').toggleClass('panel-open');
      scrollBar.updateScrollBars();
    });
    $(window).on('resize', setContent);
    setContent();
    navigationElement.scrollbars({
      scrollBarSize: 10,
      fade: true,
      barOverContent: true
    });
    scrollBar = navigationElement.retrieve('scrollbars');
    scrollBar.element.getElement('.scrollbar-content-wrapper').setStyle('float', 'none');
    scrollBar.updateScrollBars();

    var menuTipElement = new Element('div', {
      'class': 'menu_core_main_tip'
    }).inject($(document.body));
    var hideMenuTip = function () {
      menuTipElement.hide()
    };

    navigationElement.getElements('li').addEvent('mouseover', function () {
      if (!this.getParent('.panel-collapsed') || this.getParent('.horizontal_core_main_menu')) {
        hideMenuTip();
        return;
      }
      if (this.getElement('.menu_core_main')) {
        menuTipElement.set('html', this.getElement('.menu_core_main').get('html')).setStyles({
          'top': this.getCoordinates().top + 8,
          'display': 'block'
        });
      }
    }).addEvent('mouseout', function () {
      hideMenuTip();
    });

    scrollBar.element.getElement('.scrollbar-content').addEvent('scroll', function () {
      hideMenuTip();
    });
  }
};

en4.core.languageAbstract = new Class({

  Implements: [Options, Events],

  name: 'language',

  options: {
    locale: 'en',
    defaultLocale: 'en'
  },

  data: {},

  initialize: function (options, data) {
    // b/c
    if (typeof(options) == 'object') {
      if (typeof(options.lang)) {
        this.addData(options.lang);
        delete options.lang;
      }
      if (typeof(options.data)) {
        this.addData(options.data);
        delete options.data;
      }
      this.setOptions(options);
    }
    if (typeof(data) == 'object') {
      this.setData(data);
    }
  },

  getName: function () {
    return this.name;
  },

  setLocale: function (locale) {
    this.options.locale = locale;
    return this;
  },

  getLocale: function () {
    return this.options.locale;
  },

  translate: function () {
    try {
      if (arguments.length < 1) {
        return '';
      }

      // Process arguments
      var locale = this.options.locale;
      var messageId = arguments[0];
      var options = new Array();
      if (arguments.length > 1) {
        for (var i = 1, l = arguments.length; i < l; i++) {
          options.push(arguments[i]);
        }
      }

      // Check plural
      var plural = false;
      var number = 1;
      if (typeof(messageId) == 'array') {
        if (messageId.length > 2) {
          number = messageId.pop();
          plural = messageId;
        }
        messageId = messageId[0];
      }

      // Get message
      var message;
      if (typeof(this.data[messageId])) {
        message = this.data[messageId];
      } else if (plural) {
        message = plural;
        locale = this.options.defaultLocale;
      } else {
        message = messageId;
      }

      // Get correct message from plural
      if (typeof(message) == 'array') {
        var rule = this.getPlural(locale, number);
        if (typeof(message[rule])) {
          message = message[rule];
        } else {
          message = message[0];
        }
      }

      if (options.length <= 0) {
        return message;
      }

      return message.vsprintf(options);
    } catch (e) {
      alert(e);
    }
  },

  setData: function (data) {
    if (typeof(data) != 'object' && typeof(data) != 'hash') {
      return this;
    }
    this.data = data;
    return this;
  },

  addData: function (data) {
    if (typeof(data) != 'object' && typeof(data) != 'hash') {
      return this;
    }
    this.data = _.merge(this.data, data);
    return this;
  },

  getData: function (data) {
    return this.data;
  },


  getPlural: function (locale, number) {

    if (typeof(locale) != 'string') {
      return 0;
    }

    if (locale == "pt_BR") {
      locale = "xbr";
    }

    if (locale.length > 3) {
      locale = locale.substring(0, locale.indexOf('_'));
    }

    switch (locale) {
      case 'bo':
      case 'dz':
      case 'id':
      case 'ja':
      case 'jv':
      case 'ka':
      case 'km':
      case 'kn':
      case 'ko':
      case 'ms':
      case 'th':
      case 'tr':
      case 'vi':
        return 0;
        break;

      case 'af':
      case 'az':
      case 'bn':
      case 'bg':
      case 'ca':
      case 'da':
      case 'de':
      case 'el':
      case 'en':
      case 'eo':
      case 'es':
      case 'et':
      case 'eu':
      case 'fa':
      case 'fi':
      case 'fo':
      case 'fur':
      case 'fy':
      case 'gl':
      case 'gu':
      case 'ha':
      case 'he':
      case 'hu':
      case 'is':
      case 'it':
      case 'ku':
      case 'lb':
      case 'ml':
      case 'mn':
      case 'mr':
      case 'nah':
      case 'nb':
      case 'ne':
      case 'nl':
      case 'nn':
      case 'no':
      case 'om':
      case 'or':
      case 'pa':
      case 'pap':
      case 'ps':
      case 'pt':
      case 'so':
      case 'sq':
      case 'sv':
      case 'sw':
      case 'ta':
      case 'te':
      case 'tk':
      case 'ur':
      case 'zh':
      case 'zu':
        return (number == 1) ? 0 : 1;
        break;

      case 'am':
      case 'bh':
      case 'fil':
      case 'fr':
      case 'gun':
      case 'hi':
      case 'ln':
      case 'mg':
      case 'nso':
      case 'xbr':
      case 'ti':
      case 'wa':
        return ((number == 0) || (number == 1)) ? 0 : 1;
        break;

      case 'be':
      case 'bs':
      case 'hr':
      case 'ru':
      case 'sr':
      case 'uk':
        return ((number % 10 == 1) && (number % 100 != 11)) ? 0 :
          (((number % 10 >= 2) && (number % 10 <= 4) && ((number % 100 < 10)
            || (number % 100 >= 20))) ? 1 : 2);

      case 'cs':
      case 'sk':
        return (number == 1) ? 0 : (((number >= 2) && (number <= 4)) ? 1 : 2);

      case 'ga':
        return (number == 1) ? 0 : ((number == 2) ? 1 : 2);

      case 'lt':
        return ((number % 10 == 1) && (number % 100 != 11)) ? 0 :
          (((number % 10 >= 2) && ((number % 100 < 10) ||
            (number % 100 >= 20))) ? 1 : 2);

      case 'sl':
        return (number % 100 == 1) ? 0 : ((number % 100 == 2) ? 1 :
          (((number % 100 == 3) || (number % 100 == 4)) ? 2 : 3));

      case 'mk':
        return (number % 10 == 1) ? 0 : 1;

      case 'mt':
        return (number == 1) ? 0 :
          (((number == 0) || ((number % 100 > 1) && (number % 100 < 11))) ? 1 :
            (((number % 100 > 10) && (number % 100 < 20)) ? 2 : 3));

      case 'lv':
        return (number == 0) ? 0 : (((number % 10 == 1) &&
          (number % 100 != 11)) ? 1 : 2);

      case 'pl':
        return (number == 1) ? 0 : (((number % 10 >= 2) && (number % 10 <= 4) &&
          ((number % 100 < 10) || (number % 100 > 29))) ? 1 : 2);

      case 'cy':
        return (number == 1) ? 0 : ((number == 2) ? 1 : (((number == 8) ||
          (number == 11)) ? 2 : 3));

      case 'ro':
        return (number == 1) ? 0 : (((number == 0) || ((number % 100 > 0) &&
          (number % 100 < 20))) ? 1 : 2);

      case 'ar':
        return (number == 0) ? 0 : ((number == 1) ? 1 : ((number == 2) ? 2 :
          (((number >= 3) && (number <= 10)) ? 3 : (((number >= 11) &&
            (number <= 99)) ? 4 : 5))));

      default:
        return 0;
    }
  }

});

en4.core.language = new en4.core.languageAbstract();

/**
 * ReCaptcha scripts
 */
en4.core.reCaptcha = {
  lodedJs: [],
  render: function () {
    $('.g-recaptcha').each(function ($el) {
      if ($el.retrieve('recaptcha-loaded', false)) {
        return;
      }
      $el.empty();
      grecaptcha.render($el, {
        sitekey: $el.get('data-sitekey'),
        theme: $el.get('data-theme'),
        type: $el.get('data-type'),
        tabindex: $el.get('data-tabindex'),
        size: $el.get('data-size'),
      });
      $el.store('recaptcha-loaded', true);
    });
  },
  loadJs: function (js) {
    if (this.lodedJs.indexOf(js) != -1) {
      return;
    }
    this.lodedJs.push(js);
    new Element('script', {'src': js, 'async': true, defer: true}).inject($(document.body));
  }
};

window.en4CoreReCaptcha = function () {
  en4.core.reCaptcha.render();
};

export {en4};
