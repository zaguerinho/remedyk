(function ($) {
    var name = 'infinitescroll';

    var defaults = {
        item: '.item',
        pagination: '.pagination',
        next: '.pagination .next a:first',
        wrapper: 'body', // selector for all elements
        bufferPx: 40,
        loadingText: '<div style="width: 100% text-align: center;"><div class="lds-eclipse"><div></div></div></div>',
        loadingTextWrapperClass: 'infinite-scroll-loading-text',
        pjax: {
            container: null
        },
        state: {
            isPaused: true,
            isLoadingNextPage: false
        },
        alwaysHidePagination: true,
        container: 'window'
    };
    var options = {};

    var object;
    var container;

    function isScrolledToBottom(elem, container) {
        var containerViewTop = container.scrollTop();
        var containerViewBottom = containerViewTop + container.height();

        var elemTop = elem.offset().top;
        var elemBottom = elemTop + elem.height();

        return (elemBottom - options.bufferPx <= containerViewBottom);
    }

    function isScrolledToBottomContainer(container) {
        var containerViewTop = container.scrollTop() + container.height();
        var height = 0;

        container.children().each(function (indx, element) {
            if ($(element).is(":visible")) {
                height += $(element).height();
            }
        });

        return (height <= containerViewTop);
    }

    function scroll(e) {
        if (
            (options.container == "window" && isScrolledToBottom(object, container)) ||
            (options.container == "container" && isScrolledToBottomContainer(object))
        ) {
            methods.retrieve();
        }
    }

    var methods = {
        init: function (params) {
            options = $.extend(true, {}, defaults, params);
            var data = $(this).data(name);

            if (data) {
                return this;
            } else {
                object = this;
                container = (options.container == "container")
                    ? object
                    : $(window);

                if (!options.state.isPaused) {
                    methods.start();
                }
                return this;
            }

        },
        bind: function () {
            container.bind('scroll.' + name, scroll);
        },
        unbind: function () {
            container.unbind('.' + name);
        },
        hidePagination: function () {
            $(options.pagination).hide();
            return this;
        },
        showPagination: function () {
            if (!options.alwaysHidePagination) {
                $(options.pagination).show();
            }
            return this;
        },
        start: function () {
            options.state.isPaused = false;
            methods.bind();
            methods.hidePagination();
            object.trigger('infinitescroll:afterStart');
            return this;
        },
        stop: function () {
            options.state.isPaused = true;
            methods.unbind();
            methods.showPagination();
            object.trigger('infinitescroll:afterStop');
            return this;
        },
        retrieve: function () {
            if (!options.state.isPaused && !options.state.isLoadingNextPage) {
                options.state.isLoadingNextPage = true;
                var link = $(options.next);
                if (link.length) {
                    var href = link.attr('href');
                    methods.showLoadingText();
                    $.ajax({
                        url: href,
                        beforeSend: function (xhr) {
                            if (options.pjax.container) {
                                xhr.setRequestHeader('X-PJAX', 'true');
                                xhr.setRequestHeader('X-PJAX-Container', '#' + options.pjax.container);
                            }
                        },
                        success: function (text) {
                            var html = $(text);
                            object.find(options.item).last().after(html.find(options.item).hide().fadeIn('slow'));
                            $(options.wrapper).find(options.pagination).html(html.find(options.pagination).html());
                            options.state.isLoadingNextPage = false;
                            methods.hideLoadingText();
                            object.trigger('infinitescroll:afterRetrieve');
                        }
                    });
                } else {
                    methods.stop();
                }
            }
        },
        getState: function (param) {
            return options.state[param];
        },
        showLoadingText: function () {
            object.append($('<div/>').addClass(options.loadingTextWrapperClass).html(options.loadingText));
        },
        hideLoadingText: function () {
            var target = object.find('.' + options.loadingTextWrapperClass);
            if (target.length) {
                target.slideUp(300, function () {
                    target.remove();
                });
            }
        }
    };
    $.fn.infinitescroll = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Метод "' + method + '" не найден');
        }
        return this;
    }
})(jQuery);
