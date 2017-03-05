KB.component('chat-widget', function (containerElement, options) {
    var widgetElement;
    var widgetState = localStorage.getItem('chatState') || 'minimized';
    var lastMessageId = options.lastMessageId;
    var nbUnread = 0;

    function setState(state) {
        widgetState = state;
        nbUnread = 0;
        localStorage.setItem('chatState', state);
    }

    function minimize() {
        setState('minimized');
        updateWidget();
    }

    function maximize() {
        setState('maximized');
        KB.http.get(options.showUrl).success(updateWidget);
    }

    function restore() {
        setState('normal');
        KB.http.get(options.showUrl).success(updateWidget);
    }

    function listen() {
        var formElement = KB.find('#chat-form');
        formElement.on('submit', onFormSubmit, false);
    }

    function refresh() {
        var url = options.checkUrl;

        if (widgetState === 'minimized') {
            url = options.pingUrl;
        }

        KB.http.get(url + "&lastMessageId=" + lastMessageId).success(function (response) {
            nbUnread = response.nbUnread;

            if (widgetState !== 'minimized') {
                lastMessageId = response.lastMessageId;
                updateMessages(response.messages);
            } else {
                updateWidget();
            }
        });
    }

    function onFormSubmit() {
        var formElement = KB.find('#chat-form').build();
        var url = formElement.getAttribute('action');

        if (url) {
            KB.http.postForm(url, formElement).success(function (response) {
                updateWidget(response);
                document.querySelector('#chat-form input[type="text"]').focus();
            });
        }
    }

    function updateMessages(html) {
        KB.find('#chat-widget-messages-container').html(html);
        scrollBottom();
    }

    function createMinimizedWidget() {
        var toolbar = KB.dom('div').addClass('chat-widget-toolbar');
        var title = options.defaultTitle;

        if (nbUnread > 0) {
            title = '(' + nbUnread + ') ' + title;
        }

        toolbar.add(KB.dom('a')
            .attr('href', '#')
            .html('<i class="fa fa-expand" aria-hidden="true"></i>')
            .click(restore)
            .build());

        return KB.dom('div')
            .addClass('chat-widget-minimized')
            .add(toolbar.build())
            .add(KB.dom('span').text(title).build())
            .build();
    }

    function createMaximizedWidget(html) {
        var toolbar = KB.dom('div').addClass('chat-widget-toolbar');

        toolbar.add(KB.dom('a')
            .attr('href', '#')
            .html('<i class="fa fa-window-minimize" aria-hidden="true"></i>')
            .click(minimize)
            .build());

        toolbar.add(KB.dom('a')
            .attr('href', '#')
            .html('<i class="fa fa-window-restore" aria-hidden="true"></i>')
            .click(restore)
            .build());

        var containerElement = KB.dom('div')
            .html(html)
            .build();

        return KB.dom('div')
            .addClass('chat-widget-maximized')
            .add(toolbar.build())
            .add(containerElement)
            .build();
    }

    function createNormalWidget(html) {
        var toolbar = KB.dom('div').addClass('chat-widget-toolbar');

        toolbar.add(KB.dom('a')
            .attr('href', '#')
            .html('<i class="fa fa-window-minimize" aria-hidden="true"></i>')
            .click(minimize)
            .build());

        toolbar.add(KB.dom('a')
            .attr('href', '#')
            .html('<i class="fa fa-window-maximize" aria-hidden="true"></i>')
            .click(maximize)
            .build());

        var containerElement = KB.dom('div')
            .html(html)
            .build();

        return KB.dom('div')
            .addClass('chat-widget-normal')
            .add(toolbar.build())
            .add(containerElement)
            .build();
    }

    function createWidget(html) {
        if (widgetState === 'minimized') {
            return createMinimizedWidget(html);
        } else if (widgetState === 'maximized') {
            return createMaximizedWidget(html);
        }

        return createNormalWidget(html);
    }

    function updateWidget(html) {
        var updatedWidgetElement = createWidget(html);

        KB.dom(widgetElement).replace(updatedWidgetElement);
        widgetElement = updatedWidgetElement;

        if (widgetState !== 'minimized') {
            listen();
            scrollBottom();
        }
    }

    function renderWidget(html) {
        widgetElement = createWidget(html);
        document.body.appendChild(widgetElement);

        if (widgetState !== 'minimized') {
            listen();
            scrollBottom();
        }

        setInterval(refresh, options.interval * 1000);
    }

    function scrollBottom() {
        document.querySelector('.chat-messages').scrollTop = document.querySelector('.chat-message:last-child').offsetTop;
    }

    this.render = function () {
        KB.http.get(options.showUrl).success(renderWidget);
    }
});
