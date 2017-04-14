Simple Javascript enhancement
======================

You can enhance user experience with a bit of AJAX, making the tag inputact as a multiple select. You need
[Select2](http://select2.github.io/select2/).

Here is a sneak preview of what you'll get.

![tag](https://cloud.githubusercontent.com/assets/179866/7724813/3e1d5b50-fef5-11e4-83f1-e05615518548.png)

#### 1. Template

Create a form_row like the following:
(src/AppBundle/Resources/views/post.html.twig)

```twig
...
{{ form_row(form.tagsText, {'attr': {'style': 'display:none !important'}}) }}
...
```

#### 2. Javascript

Add this snippet to your Javascript:
(src/AppBundle/Resources/views/post.html.twig)

```javascript
$(document).ready(function () {
        (function () {
            var $tagInput = $('input[name$="[tagsText]"]');
            $('<select id="tagsText" multiple="multiple"></select>').insertAfter('#post_tagsText');

            var selectTags = '#tagsText';
            var arrayTags = $tagInput.val().indexOf(",") > -1 ? $tagInput.val().split(',') : $tagInput.val().split(' ').filter(function(item){ return item.trim().length > 0 });

            function tags() {
                $.each(arrayTags, function (key, value) {
                    $(selectTags).append($("<option></option>")
                        .attr("value",value).attr("selected","selected")
                        .text(value));
                });
            }
            function  addTag($tag) {
                arrayTags.push($tag);
                $tagInput.val(arrayTags);
                console.log('tag:'+arrayTags);
            }
            function removeTag($tag) {
                arrayTags = jQuery.grep(arrayTags, function( a ) {
                    return a !== $tag;
                });
                $tagInput.val(arrayTags);
                console.log('tag:'+arrayTags);
            }

            $(selectTags).on("select2:select", function (e) { addTag(e.params.data.text); });
            $(selectTags).on("select2:unselect", function (e) { removeTag(e.params.data.text); });


            if ($tagInput.length > 0) {
                tags();
            }

            $(selectTags).select2({
                tags: true,
                tokenSeparators: [',', ' '],
                placeholder: "Enter Tag"
            });
        }());
    });
```

[← back to documentation index](index.md)
