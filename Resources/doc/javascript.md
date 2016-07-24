Javascript enhancement
======================

You can enhance user experience with a bit of AJAX, making the tag inputact as a multiple select. You need
[Select2](http://select2.github.io/select2/).

Here is a sneak preview of what you'll get.

![tag](https://cloud.githubusercontent.com/assets/179866/7724813/3e1d5b50-fef5-11e4-83f1-e05615518548.png)

#### 1. Controller

Create an action like the following:

```php
    /**
     * @Route("/tags.json", name="tags", defaults={"_format": "json"})
     */
    public function tagsAction()
    {
        $tags = $this->getDoctrine()->getRepository('app:Tag')->findBy([], ['name' => 'ASC']);

        return $this->render('default/tags.json.twig', ['tags' => $tags]);
    }
```

#### 2. Template

Create a template like the following:


```jinja
[
    {%- for tag in tags -%}
        {"id": "{{ tag }}", "text": "{{ tag }}"}{% if not loop.last %},{% endif %}
    {%- endfor -%}
]
```

#### 3. Form

Your form needs to know the `tags` route. Here is an example:

```php
<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class TagsTextType extends AbstractType
{
    /**
     * @var RouterInterface $route
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'label' => 'Tags',
            'attr' => [
                'placeholder' => 'separate tags with comma',
                'data-ajax' => $this->router->generate('tags'),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}

```

#### 4. Javascript

Add this snippet to your Javascript:

```js
$(document).ready(function () {
    (function () {
        var $tagInput = $('input[name$="[tagsText]"]');
        function tags($input) {
            $input.attr('type', 'hidden').select2({
                tags: true,
                tokenSeparators: [","],
                createSearchChoice: function(term, data) {
                    if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                        return {
                            id: term,
                            text: term
                        };
                    }
                },
                multiple: true,
                ajax: {
                    url: $input.data('ajax'),
                    dataType: "json",
                    data: function (term, page) {
                        return {
                            q: term
                        };
                    },
                    results: function (data, page) {
                        return {
                            results: data
                        };
                    }
                },
                initSelection: function (element, callback) {
                    var data = [];
                    function splitVal(string, separator) {
                        var val, i, l;
                        if (string === null || string.length < 1) {
                            return [];
                        }
                        val = string.split(separator);
                        for (i = 0, l = val.length; i < l; i = i + 1) {
                            val[i] = $.trim(val[i]);
                        }
                        return val;
                    }
                    $(splitVal(element.val(), ",")).each(function () {
                        data.push({
                            id: this,
                            text: this
                        });
                    });
                    callback(data);
                }
            });
        }
        if ($tagInput.length > 0) {
            tags($tagInput);
        }
    }());
});
```

[← back to documentation index](index.md)
