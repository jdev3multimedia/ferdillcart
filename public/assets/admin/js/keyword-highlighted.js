"use strict";
$(document).ready(function() {
    var urlParams = new URLSearchParams(window.location.search);
    var keyword = urlParams.get('keyword');

    if (keyword) {
        highlightKeyword(keyword);
    }

    var highlightedElements = document.querySelectorAll('.highlighted-keyword');
    if (highlightedElements.length > 0) {
        var firstHighlightedElement = highlightedElements[0];
        var elementPosition = firstHighlightedElement.getBoundingClientRect().top;
        var windowHeight = window.innerHeight;
        var offsetPosition = elementPosition - (windowHeight / 2);

        window.scrollTo({
            top: window.pageYOffset + offsetPosition,
            behavior: 'smooth'
        });
    }
});

function highlightKeyword(keyword) {
    if (!keyword) return;

    var regex = new RegExp(keyword, 'gi');
    var textNodes = getTextNodes(document.body);

    textNodes.forEach(function(node, index) {
        var text = node.nodeValue;
        if (!regex.test(text)) return;
        var parts = text.split(/(\w+)/);
        var html = parts.map(function(part) {
            if (regex.test(part)) {
                var inner = part.replace(regex, function(match) {
                    return '<span class="highlighted-keyword">' + match + '</span>';
                });
                return '<span class="highlighted-word">' + inner + '</span>';
            }
            return part;
        }).join('');

        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        while (tempDiv.firstChild) {
            node.parentNode.insertBefore(tempDiv.firstChild, node);
        }
        node.parentNode.removeChild(node);
    });
}

function getTextNodes(node) {
    var textNodes = [];
    if (node.nodeType === Node.TEXT_NODE) {
        textNodes.push(node);
    } else {
        var children = node.childNodes;
        for (var i = 0; i < children.length; i++) {
            textNodes = textNodes.concat(getTextNodes(children[i]));
        }
    }
    return textNodes;
}
