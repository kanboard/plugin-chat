KB.component('chat-widget', function (containerElement, options) {
    var widgetElement;
    var widgetState = localStorage.getItem('chatState') || 'minimized';
    var lastMessageId = options.lastMessageId;
    var nbUnread = 0;
    var mentioned = false;
    var originalTitle = document.title;
    var messagesBuffer = [];
    var bufferPosition = -1;

    function onKeyDown(e) {
        var key = KB.utils.getKey(e);

        if (messagesBuffer.length > 0 && (key === 'ArrowUp' || key === 'ArrowDown')) {
            var buffer = messagesBuffer.slice().reverse();

            if (key === 'ArrowUp') {
                bufferPosition++;
            } else if (key === 'ArrowDown') {
                bufferPosition--;
            }

            if (bufferPosition >= buffer.length) {
                bufferPosition = 0;
            } else if (bufferPosition < 0) {
                bufferPosition = buffer.length - 1;
            }

            e.target.value = buffer[bufferPosition];
        }
    }

    function unsetUserMention() {
        if (mentioned) {
            mentioned = false;
            KB.http.get(options.ackUrl);
            KB.dom(getTextInputElement()).removeClass('chat-input-mentioned');
        }
    }

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
        if (widgetState === 'minimized') {
            unsetUserMention();
        }

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
            var isDifferentState = response.nbUnread !== nbUnread || response.mentioned !== mentioned;
            nbUnread = response.nbUnread;
            mentioned = response.mentioned;

            if (widgetState !== 'minimized') {
                lastMessageId = response.lastMessageId;
                updateMessages(response.messages);

                if (mentioned) {
                    KB.dom(getTextInputElement()).addClass('chat-input-mentioned');
                    setTimeout(unsetUserMention, 10000);
                }
            } else if (isDifferentState) {
                updateWidget();
            }
        });
    }

    function getTextInputElement() {
        return document.querySelector('#chat-form input[type="text"]');
    }

    function onFormSubmit() {
        var formElement = KB.find('#chat-form').build();
        var url = formElement.getAttribute('action');

        bufferPosition = -1;
        messagesBuffer.push(getTextInputElement().value);

        if (messagesBuffer.length > 5) {
            messagesBuffer = messagesBuffer.slice(-5);
        }

        if (url) {
            KB.http.postForm(url, formElement).success(function (response) {
                updateWidget(response);
                getTextInputElement().focus();
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

        var widget = KB.dom('div')
            .addClass('chat-widget-minimized')
            .add(toolbar.build())
            .add(KB.dom('a').attr('href', '#').click(restore).text(title).build());

        if (mentioned) {
            widget.addClass('chat-widget-mentioned');
        }

        return widget.build();
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
        updateWindowTitle();

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

            var textInputElement = getTextInputElement();
            textInputElement.onfocus = unsetUserMention;
            textInputElement.onkeydown = onKeyDown;
        }
    }

    function renderWidget(html) {
        widgetElement = createWidget(html);
        containerElement.appendChild(widgetElement);

        if (widgetState !== 'minimized') {
            listen();
            scrollBottom();

            var textInputElement = getTextInputElement();
            textInputElement.onfocus = unsetUserMention;
            textInputElement.onkeydown = onKeyDown;
        }

        setInterval(refresh, options.interval * 1000);
    }

    function scrollBottom() {
        var lastMessageElement = document.querySelector('.chat-message:last-child');

        if (lastMessageElement) {
            document.querySelector('.chat-messages').scrollTop = lastMessageElement.offsetTop;
        }
    }

    function updateWindowTitle() {
        if (nbUnread > 0) {
            if (mentioned) {
                document.title = '(' + nbUnread + '!) ' + originalTitle;
            } else {
                document.title = '(' + nbUnread + ') ' + originalTitle;
            }
        } else if (mentioned) {
            document.title = '(*) ' + originalTitle;
        } else if (document.title !== originalTitle) {
            document.title = originalTitle;
        }
    }

    this.render = function () {
        KB.http.get(options.showUrl).success(renderWidget);
    };
});
